<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Venta;
use App\Models\MovimientoCaja;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Carbon\Carbon;

class CajaController extends Controller
{
    /**
     * Mostrar la pantalla de cierre de caja
     */
    public function index()
    {
        $cajaAbierta = Caja::where('estado', 'abierta')
            ->with('usuario')
            ->first();
            
        $cajasCerradas = Caja::where('estado', 'cerrada')
            ->with('usuario')
            ->orderBy('fecha_cierre', 'desc')
            ->limit(10)
            ->get();
        
        return Inertia::render('Caja/Index', [
            'cajaAbierta' => $cajaAbierta,
            'cajasCerradas' => $cajasCerradas,
            'usuario' => User::with('rol')->find(Auth::id()),
            'fechaActual' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Mostrar el formulario de apertura de caja
     */
    public function create()
    {
        try {
            // Verificar si hay alguna caja abierta
            $cajaAbierta = Caja::where('estado', 'abierta')->first();
            
            if ($cajaAbierta) {
                return redirect()->route('caja.index')
                    ->with('error', 'Ya existe una caja abierta. Debe cerrarla antes de abrir una nueva.');
            }
            
            // Obtener usuario sin cargar rol para evitar posibles errores
            $usuario = Auth::user();
            
            return Inertia::render('Caja/Apertura', [
                'usuario' => [
                    'id' => $usuario->id,
                    'name' => $usuario->name,
                    'email' => $usuario->email,
                ],
                'fechaActual' => Carbon::now()->format('Y-m-d H:i:s'),
                'errors' => session('errors') ? session('errors')->getBag('default')->getMessages() : (object)[]
            ]);
              } catch (\Exception $e) {
            // Registrar el error para diagnóstico
            Log::error('Error en la página de apertura de caja: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            // Redireccionar a la página principal
            return redirect()->route('caja.index')
                ->with('error', 'Ocurrió un error al cargar la página de apertura de caja. Por favor, intente nuevamente.');
        }
    }
    
    /**
     * Abrir una nueva caja
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'monto_inicial' => 'required|numeric|min:0',
                'observaciones' => 'nullable|string|max:255',
            ]);
            
            // Verificar si hay alguna caja abierta
            $cajaAbierta = Caja::where('estado', 'abierta')->first();
            
            if ($cajaAbierta) {
                return redirect()->back()
                    ->with('error', 'Ya existe una caja abierta. Debe cerrarla antes de abrir una nueva.');
            }
            
            // Obtener el usuario autenticado
            $usuario = Auth::user();
            if (!$usuario) {
                return redirect()->back()
                    ->with('error', 'No se pudo identificar al usuario. Por favor, inicie sesión nuevamente.');
            }
            
            // Crear la nueva caja
            $caja = new Caja();
            $caja->usuario_id = $usuario->id;
            $caja->fecha_apertura = Carbon::now();
            $caja->monto_inicial = $request->monto_inicial;
            $caja->monto_final = 0;
            $caja->total_ventas = 0;
            $caja->estado = 'abierta';
            $caja->observaciones = $request->observaciones;
            $caja->save();
            
            // Registrar el movimiento de apertura
            $movimiento = new MovimientoCaja();
            $movimiento->caja_id = $caja->id;
            $movimiento->usuario_id = $usuario->id;
            $movimiento->tipo = 'apertura';
            $movimiento->monto = $request->monto_inicial;
            $movimiento->descripcion = 'Apertura de caja';
            $movimiento->fecha = Carbon::now();
            $movimiento->save();
            
            return redirect()->route('caja.index')
                ->with('success', 'Caja abierta correctamente.');
                  } catch (\Exception $e) {
            // Log del error
            Log::error('Error al abrir caja: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return redirect()->back()
                ->with('error', 'Ocurrió un error al abrir la caja. Por favor, inténtelo de nuevo.');
        }
    }
    
    /**
     * Mostrar formulario de cierre de caja
     */
    public function edit($id)
    {
        $caja = Caja::findOrFail($id);
        
        // Verificar que la caja esté abierta
        if ($caja->estado !== 'abierta') {
            return redirect()->route('caja.index')
                ->with('error', 'La caja ya está cerrada.');
        }
        
        // Obtener ventas del día para esta caja
        $ventas = Venta::where('caja_id', $caja->id)
            ->where('estado', 'completada')
            ->with('detalles', 'detalles.producto')
            ->get();
        
        // Calcular totales por método de pago
        $totalEfectivo = $ventas->where('metodo_pago', 'efectivo')->sum('total');
        $totalTarjeta = $ventas->where('metodo_pago', 'tarjeta')->sum('total');
        $totalTransferencia = $ventas->where('metodo_pago', 'transferencia')->sum('total');
        $totalVentas = $totalEfectivo + $totalTarjeta + $totalTransferencia;
        
        // Obtener movimientos de caja (entrada/salida de efectivo)
        $movimientos = MovimientoCaja::where('caja_id', $caja->id)
            ->where('tipo', '!=', 'apertura')
            ->orderBy('fecha', 'desc')
            ->get();
        
        $totalEntradas = $movimientos->where('tipo', 'entrada')->sum('monto');
        $totalSalidas = $movimientos->where('tipo', 'salida')->sum('monto');
        
        // Calcular saldo esperado en caja
        $saldoEsperado = $caja->monto_inicial + $totalEfectivo + $totalEntradas - $totalSalidas;
        
        return Inertia::render('Caja/Cierre', [
            'caja' => $caja,
            'ventas' => $ventas,
            'totalEfectivo' => $totalEfectivo,
            'totalTarjeta' => $totalTarjeta,
            'totalTransferencia' => $totalTransferencia,
            'totalVentas' => $totalVentas,
            'totalEntradas' => $totalEntradas,
            'totalSalidas' => $totalSalidas,
            'saldoEsperado' => $saldoEsperado,
            'usuario' => User::with('rol')->find(Auth::id()),
            'fechaActual' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Realizar el cierre de caja
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'monto_final' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string|max:255',
            'justificacion_diferencia' => 'required_if:hay_diferencia,true|nullable|string|max:255',
        ]);
        
        $caja = Caja::findOrFail($id);
        
        // Verificar que la caja esté abierta
        if ($caja->estado !== 'abierta') {
            return redirect()->back()
                ->with('error', 'La caja ya está cerrada.');
        }
        
        // Obtener ventas del día para esta caja
        $ventas = Venta::where('caja_id', $caja->id)
            ->where('estado', 'completada')
            ->get();
        
        // Calcular totales por método de pago
        $totalEfectivo = $ventas->where('metodo_pago', 'efectivo')->sum('total');
        $totalTarjeta = $ventas->where('metodo_pago', 'tarjeta')->sum('total');
        $totalTransferencia = $ventas->where('metodo_pago', 'transferencia')->sum('total');
        $totalVentas = $totalEfectivo + $totalTarjeta + $totalTransferencia;
        
        // Obtener movimientos de caja (entrada/salida de efectivo)
        $movimientos = MovimientoCaja::where('caja_id', $caja->id)
            ->where('tipo', '!=', 'apertura')
            ->get();
        
        $totalEntradas = $movimientos->where('tipo', 'entrada')->sum('monto');
        $totalSalidas = $movimientos->where('tipo', 'salida')->sum('monto');
        
        // Calcular saldo esperado en caja
        $saldoEsperado = $caja->monto_inicial + $totalEfectivo + $totalEntradas - $totalSalidas;
        
        // Calcular diferencia
        $diferencia = $request->monto_final - $saldoEsperado;
        
        // Actualizar la caja
        $caja->update([
            'fecha_cierre' => Carbon::now(),
            'monto_final' => $request->monto_final,
            'total_ventas' => $totalVentas,
            'estado' => 'cerrada',
            'observaciones' => $request->observaciones,
        ]);
        
        // Si hay diferencia, registrarla como un movimiento
        if ($diferencia != 0) {
            $tipo = $diferencia > 0 ? 'sobrante' : 'faltante';
            $monto = abs($diferencia);
            
            MovimientoCaja::create([
                'caja_id' => $caja->id,
                'usuario_id' => Auth::id(),
                'tipo' => $tipo,
                'monto' => $monto,
                'descripcion' => "Diferencia en cierre de caja: {$request->justificacion_diferencia}",
                'fecha' => Carbon::now(),
            ]);
        }
        
        // Registrar el movimiento de cierre
        MovimientoCaja::create([
            'caja_id' => $caja->id,
            'usuario_id' => Auth::id(),
            'tipo' => 'cierre',
            'monto' => $request->monto_final,
            'descripcion' => 'Cierre de caja',
            'fecha' => Carbon::now(),
        ]);
        
        return redirect()->route('caja.show', $caja->id)
            ->with('success', 'Caja cerrada correctamente.');
    }
    
    /**
     * Mostrar el reporte de cierre de caja
     */
    public function show($id)
    {
        $caja = Caja::with('usuario', 'movimientos')->findOrFail($id);
        
        // Obtener ventas para esta caja
        $ventas = Venta::where('caja_id', $caja->id)
            ->where('estado', 'completada')
            ->with('detalles', 'detalles.producto')
            ->get();
        
        // Calcular totales por método de pago
        $totalEfectivo = $ventas->where('metodo_pago', 'efectivo')->sum('total');
        $totalTarjeta = $ventas->where('metodo_pago', 'tarjeta')->sum('total');
        $totalTransferencia = $ventas->where('metodo_pago', 'transferencia')->sum('total');
        
        // Obtener movimientos de caja (entrada/salida de efectivo)
        $movimientos = $caja->movimientos()
            ->where('tipo', '!=', 'apertura')
            ->where('tipo', '!=', 'cierre')
            ->orderBy('fecha', 'desc')
            ->get();
        
        $totalEntradas = $movimientos->where('tipo', 'entrada')->sum('monto');
        $totalSalidas = $movimientos->where('tipo', 'salida')->sum('monto');
        
        // Calcular saldo esperado en caja
        $saldoEsperado = $caja->monto_inicial + $totalEfectivo + $totalEntradas - $totalSalidas;
        
        // Calcular diferencia
        $diferencia = $caja->monto_final - $saldoEsperado;
        
        return Inertia::render('Caja/Reporte', [
            'caja' => $caja,
            'ventas' => $ventas,
            'movimientos' => $movimientos,
            'totalEfectivo' => $totalEfectivo,
            'totalTarjeta' => $totalTarjeta,
            'totalTransferencia' => $totalTransferencia,
            'totalVentas' => $caja->total_ventas,
            'totalEntradas' => $totalEntradas,
            'totalSalidas' => $totalSalidas,
            'saldoEsperado' => $saldoEsperado,
            'diferencia' => $diferencia,
            'usuario' => User::with('rol')->find(Auth::id()),
        ]);
    }
    
    /**
     * Reabrir una caja cerrada (solo administradores)
     */
    public function reabrir($id)
    {
        $caja = Caja::findOrFail($id);
        
        // Verificar que la caja esté cerrada
        if ($caja->estado !== 'cerrada') {
            return redirect()->back()
                ->with('error', 'La caja ya está abierta.');
        }
        
        // Registrar el movimiento de reapertura
        MovimientoCaja::create([
            'caja_id' => $caja->id,
            'usuario_id' => Auth::id(),
            'tipo' => 'reapertura',
            'monto' => 0,
            'descripcion' => 'Reapertura de caja por administrador',
            'fecha' => Carbon::now(),
        ]);
        
        // Actualizar la caja
        $caja->update([
            'fecha_cierre' => null,
            'estado' => 'abierta',
            'observaciones' => $caja->observaciones . ' - Reabierta por ' . Auth::user()->name . ' el ' . Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        
        return redirect()->route('caja.index')
            ->with('success', 'Caja reabierta correctamente.');
    }
    
    /**
     * Registrar una entrada o salida de efectivo
     */
    public function registrarMovimiento(Request $request)
    {
        $request->validate([
            'caja_id' => 'required|exists:cajas,id',
            'tipo' => 'required|in:entrada,salida',
            'monto' => 'required|numeric|min:0.01',
            'descripcion' => 'required|string|max:255',
        ]);
        
        $caja = Caja::findOrFail($request->caja_id);
        
        // Verificar que la caja esté abierta
        if ($caja->estado !== 'abierta') {
            return redirect()->back()
                ->with('error', 'No se puede registrar movimientos en una caja cerrada.');
        }
        
        // Registrar el movimiento
        MovimientoCaja::create([
            'caja_id' => $request->caja_id,
            'usuario_id' => Auth::id(),
            'tipo' => $request->tipo,
            'monto' => $request->monto,
            'descripcion' => $request->descripcion,
            'fecha' => Carbon::now(),
        ]);
        
        return redirect()->back()
            ->with('success', 'Movimiento registrado correctamente.');
    }
}
