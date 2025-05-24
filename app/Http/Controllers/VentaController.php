<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Venta;
use App\Models\Caja;
use App\Models\DetalleVenta;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Inertia\Inertia;

class VentaController extends Controller
{
    /**
     * Muestra la vista principal de registro de ventas
     */
    public function index()
    {
        return Inertia::render('Ventas/Registro', [
            'productos' => [],
            'clientes' => Cliente::where('estado', 'activo')->get()
        ]);
    }
    
    /**
     * Muestra la lista de ventas realizadas
     *
     * @return \Inertia\Response
     */
    public function listar()
    {
        $ventas = Venta::with(['cliente', 'usuario'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);
        
        return Inertia::render('Ventas/Index', [
            'ventas' => $ventas
        ]);
    }

    /**
     * Busca productos por nombre o código de barras
     */    public function buscarProductos(Request $request)
    {
        $busqueda = $request->busqueda;
        
        $productos = Producto::where('estado', 'activo')
            ->where(function($query) use ($busqueda) {
                $query->whereRaw('LOWER(nombre) LIKE ?', ['%' . strtolower($busqueda) . '%'])
                    ->orWhereRaw('LOWER(codigo) LIKE ?', ['%' . strtolower($busqueda) . '%']);
            })
            ->where('stock', '>', 0)
            ->get();
            
        return response()->json(['productos' => $productos]);
    }

    /**
     * Guardar una nueva venta y actualizar el total de la caja abierta
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function guardarVenta(Request $request)
    {
        try {
            // Validar los datos recibidos
            $request->validate([
                'numero_venta' => 'required|string',
                'detalles' => 'required|array',
                'subtotal' => 'required|numeric|min:0',
                'descuentos' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0',
                'fecha' => 'required',
                'metodo_pago' => 'required|string|in:efectivo,tarjeta,transferencia'
            ]);
            
            // Verificar si hay una caja abierta
            $cajaAbierta = Caja::where('estado', 'abierta')->first();
            if (!$cajaAbierta) {
                return response()->json([
                    'error' => 'No hay una caja abierta. Debe abrir una caja antes de realizar ventas.'
                ], 400);
            }
            
            // Iniciar una transacción de base de datos
            DB::beginTransaction();
            
            // Crear la venta
            $venta = new Venta();
            $venta->usuario_id = Auth::id();
            $venta->caja_id = $cajaAbierta->id;
            $venta->codigo = $request->numero_venta;
            $venta->fecha = Carbon::now();
            $venta->subtotal = $request->subtotal;
            $venta->descuento = $request->descuentos;
            $venta->impuesto = 0; // Si se necesita calcular impuestos
            $venta->total = $request->total;
            $venta->tipo_pago = $request->metodo_pago;
            $venta->estado = 'completada';
            $venta->observaciones = $request->observaciones ?? null;
            $venta->save();
            
            // Guardar los detalles de la venta
            foreach ($request->detalles as $detalle) {
                $detalleVenta = new DetalleVenta();
                $detalleVenta->venta_id = $venta->id;
                $detalleVenta->producto_id = $detalle['producto_id'];
                $detalleVenta->cantidad = $detalle['cantidad'];
                $detalleVenta->precio_unitario = $detalle['precio_unitario'];
                $detalleVenta->descuento = $detalle['descuento'] ?? 0;
                $detalleVenta->subtotal = $detalle['subtotal'];
                $detalleVenta->save();
                
                // Actualizar stock del producto (opcional)
                $producto = Producto::find($detalle['producto_id']);
                if ($producto) {
                    $producto->stock -= $detalle['cantidad'];
                    $producto->save();
                }
            }
            
            // Actualizar el total de ventas en la caja
            $cajaAbierta->total_ventas += $venta->total;
            $cajaAbierta->save();
            
            // Confirmar la transacción
            DB::commit();
            
            // Retornar éxito con el ID de la venta
            return response()->json([
                'success' => true,
                'message' => 'Venta guardada correctamente',
                'venta_id' => $venta->id
            ], 200);
            
        } catch (\Exception $e) {
            // Si algo sale mal, revertir la transacción
            DB::rollBack();
            
            // Registrar el error
            Log::error('Error al guardar venta: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            // Retornar el error
            return response()->json([
                'error' => 'Error al guardar la venta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Procesa y guarda una venta
     */
    public function store(Request $request)
    {
        $request->validate([
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'tipo_pago' => 'required|in:efectivo,tarjeta,transferencia',
            'monto_recibido' => 'required_if:tipo_pago,efectivo|numeric|min:0'
        ], [
            'productos.required' => 'Debe agregar al menos un producto para registrar la venta',
            'productos.min' => 'Debe agregar al menos un producto para registrar la venta',
        ]);

        try {
            // Verificar si hay una caja abierta
            $cajaAbierta = Caja::where('estado', 'abierta')->first();
            if (!$cajaAbierta) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay una caja abierta. Debe abrir una caja antes de realizar ventas.'
                ], 400);
            }
            
            DB::beginTransaction();
            
            // Calcular totales
            $subtotal = 0;
            foreach ($request->productos as $producto) {
                $subtotal += $producto['precio_venta'] * $producto['cantidad'];
            }
            
            $impuesto = $subtotal * 0.16; // 16% de impuesto
            $total = $subtotal + $impuesto;
            
            // Crear la venta
            $venta = Venta::create([
                'usuario_id' => Auth::id(),
                'cliente_id' => $request->cliente_id ?? 1, // Cliente por defecto si no se especifica
                'caja_id' => $cajaAbierta->id,
                'codigo' => 'V-' . time(),
                'fecha' => now(),
                'subtotal' => $subtotal,
                'impuesto' => $impuesto,
                'total' => $total,
                'tipo_pago' => $request->tipo_pago,
                'estado' => 'completado',
                'observaciones' => $request->observaciones
            ]);
            
            // Registrar los detalles de la venta y actualizar inventario
            foreach ($request->productos as $item) {
                $producto = Producto::findOrFail($item['id']);
                
                // Verificar stock disponible
                if ($producto->stock < $item['cantidad']) {
                    throw new \Exception("Stock insuficiente para {$producto->nombre}");
                }
                
                // Crear detalle
                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_venta'],
                    'subtotal' => $item['precio_venta'] * $item['cantidad']
                ]);
                
                // Actualizar inventario
                $producto->stock -= $item['cantidad'];
                $producto->save();
            }
            
            // Actualizar el total de ventas en la caja
            $cajaAbierta->total_ventas += $total;
            $cajaAbierta->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'venta' => $venta,
                'cambio' => $request->tipo_pago === 'efectivo' ? $request->monto_recibido - $total : 0
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar venta: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
    
    /**
     * Generar un comprobante de venta en formato PDF
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function generarComprobante(Request $request)
    {
        try {
            // Validar los datos recibidos - Hacemos la validación menos estricta para adaptarnos a la solicitud AJAX
            $request->validate([
                'numero_venta' => 'required|string',
                'detalles' => 'required|array',
                'subtotal' => 'required|numeric',
                'descuentos' => 'required|numeric',
                'total' => 'required|numeric',
                'fecha' => 'required',
                'cajero' => 'required|string',
            ]);
            
            // En una implementación real, aquí se obtendría la venta de la base de datos
            // $venta = Venta::findOrFail($request->venta_id);
            
            // Para esta demostración, usamos los datos del request
            $datosVenta = $request->all();
            
            // Generar el PDF
            $pdf = PDF::loadView('comprobantes.venta', [
                'venta' => $datosVenta,
                'fecha_formateada' => now()->format('d/m/Y H:i:s'),
            ]);
            
            // Nombre del archivo
            $nombreArchivo = 'comprobante_' . $datosVenta['numero_venta'] . '.pdf';
            
            // Retornar el PDF como un stream binario
            return $pdf->stream($nombreArchivo);
        } catch (\Exception $e) {
            // Log el error
            Log::error('Error al generar comprobante: ' . $e->getMessage());
            
            // Retornar el error
            return response()->json(['error' => 'Error al generar comprobante: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Enviar comprobante por correo electrónico
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function enviarComprobantePorEmail(Request $request)
    {
        // Validar los datos recibidos - Hacemos la validación menos estricta
        $request->validate([
            'email' => 'required|email',
            'numero_venta' => 'required|string',
            'detalles' => 'required|array',
            'subtotal' => 'required|numeric',
            'descuentos' => 'required|numeric',
            'total' => 'required|numeric',
            'fecha' => 'required',
            'cajero' => 'required|string',
        ]);
        
        try {
            // Generar el PDF
            $pdf = PDF::loadView('comprobantes.venta', [
                'venta' => $request->all(),
                'fecha_formateada' => now()->format('d/m/Y H:i:s'),
            ]);
            
            // Nombre del archivo
            $nombreArchivo = 'comprobante_' . $request->numero_venta . '.pdf';
            
            // Crear directorio si no existe
            $pdfPath = storage_path('app/public/comprobantes/' . $nombreArchivo);
            $dir = dirname($pdfPath);
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
            $pdf->save($pdfPath);
            
            // Enviar el correo con el PDF adjunto
            Mail::send('emails.comprobante', ['venta' => $request->all()], function ($message) use ($request, $pdfPath, $nombreArchivo) {
                $message->to($request->email)
                    ->subject('Comprobante de compra - VentasPlus')
                    ->attach($pdfPath, [
                        'as' => $nombreArchivo,
                        'mime' => 'application/pdf',
                    ]);
            });
            
            // Eliminar el archivo temporal
            @unlink($pdfPath);
            
            return response()->json(['message' => 'Comprobante enviado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al enviar el comprobante: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Reimprimir un comprobante de venta existente
     *
     * @param string $ventaId
     * @return \Illuminate\Http\Response
     */
    public function reimprimirComprobante($ventaId)
    {
        // En una implementación real, aquí se obtendría la venta de la base de datos
        // $venta = Venta::findOrFail($ventaId);
        
        // Para esta demostración, usamos datos de ejemplo
        $datosVenta = [
            'venta_id' => $ventaId,
            'fecha' => now()->toISOString(),
            'cajero' => 'Usuario Ejemplo',
            'detalles' => [],
            'subtotal' => 0,
            'descuentos' => 0,
            'total' => 0,
        ];
        
        // Generar el PDF
        $pdf = PDF::loadView('comprobantes.venta', [
            'venta' => $datosVenta,
            'fecha_formateada' => now()->format('d/m/Y H:i:s'),
            'es_reimpresion' => true,
        ]);
        
        // Nombre del archivo
        $nombreArchivo = 'comprobante_reimpresion_' . $ventaId . '.pdf';
        
        // Retornar el PDF para descarga
        return $pdf->download($nombreArchivo);
    }
    
    /**
     * Generar y mostrar el comprobante de venta
     */
    public function comprobante($id)
    {
        $venta = Venta::with(['detalles.producto', 'cliente', 'usuario'])->findOrFail($id);
        
        return Inertia::render('Ventas/Comprobante', [
            'venta' => $venta
        ]);
    }
    
    /**
     * Cancelar una venta en proceso
     */
    public function cancelar(Request $request)
    {
        // Esta función se maneja directamente en el frontend
        // ya que la venta aún no se ha guardado en la base de datos
        return response()->json(['success' => true]);
    }
    
    public function __invoke()
    {
        return inertia('Ventas/Venta');
    }
}
