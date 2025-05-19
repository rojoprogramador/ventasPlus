<?php

namespace App\Http\Controllers;

use App\Models\Descuento;
use App\Services\DescuentoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DescuentoController extends Controller
{
    protected $descuentoService;

    public function __construct(DescuentoService $descuentoService)
    {
        $this->descuentoService = $descuentoService;
    }

    /**
     * Aplica un descuento a un producto en una venta.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function aplicar(Request $request)
    {
        $request->validate([
            'detalle_venta_id' => 'required|exists:detalle_venta,id',
            'tipo' => 'required|in:porcentaje,fijo',
            'valor' => 'required|numeric|min:0',
        ]);

        try {
            $detalleVenta = \App\Models\DetalleVenta::findOrFail($request->detalle_venta_id);
            $descuento = $this->descuentoService->aplicarDescuento(
                $detalleVenta,
                $request->tipo,
                $request->valor
            );

            return response()->json([
                'success' => true,
                'descuento' => $descuento,
                'mensaje' => 'Descuento aplicado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Obtiene el historial de descuentos de una venta.
     *
     * @param int $ventaId
     * @return \Illuminate\Http\JsonResponse
     */
    public function historial($ventaId)
    {
        try {
            $descuentos = $this->descuentoService->obtenerHistorialDescuentos($ventaId);
            return response()->json([
                'success' => true,
                'descuentos' => $descuentos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
