<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
     * Busca productos por nombre o código de barras
     */
    public function buscarProductos(Request $request)
    {
        $busqueda = $request->busqueda;
        
        $productos = Producto::where('estado', 'activo')
            ->where(function($query) use ($busqueda) {
                $query->where('nombre', 'LIKE', "%{$busqueda}%")
                    ->orWhere('codigo', 'LIKE', "%{$busqueda}%");
            })
            ->where('stock', '>', 0)
            ->get();
            
        return response()->json(['productos' => $productos]);
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
                    'precio' => $item['precio_venta'],
                    'subtotal' => $item['precio_venta'] * $item['cantidad']
                ]);
                
                // Actualizar inventario
                $producto->stock -= $item['cantidad'];
                $producto->save();
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'venta' => $venta,
                'cambio' => $request->tipo_pago === 'efectivo' ? $request->monto_recibido - $total : 0
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
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
}
