<?php

namespace App\Services;

use App\Models\Descuento;
use App\Models\DetalleVenta;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;

class DescuentoService
{
    /**
     * Aplica un descuento a un producto en una venta.
     *
     * @param DetalleVenta $detalleVenta
     * @param string $tipo (porcentaje o fijo)
     * @param float $valor
     * @return Descuento
     * @throws \Exception
     */
    public function aplicarDescuento(DetalleVenta $detalleVenta, string $tipo, float $valor): Descuento
    {
        $producto = $detalleVenta->producto;
        
        // Validar si el producto permite descuentos
        if (!$producto->permite_descuentos) {
            throw new \Exception('Este producto no es elegible para descuentos.');
        }
        
        // Validar límites de descuento
        $limiteMaximo = $this->obtenerLimiteMaximoDescuento($detalleVenta);
        $descuentoFinal = $this->calcularDescuento($tipo, $valor, $detalleVenta->precio_unitario);
        
        if ($descuentoFinal > $limiteMaximo) {
            throw new \Exception('El descuento supera el límite máximo permitido.');
        }
        
        // Crear el registro de descuento
        $descuento = new Descuento([
            'tipo' => $tipo,
            'valor' => $valor,
            'limite' => $limiteMaximo,
            'aplicado_por' => Auth::id(),
            'detalle_venta_id' => $detalleVenta->id,
            'producto_id' => $producto->id
        ]);
        
        $descuento->save();
        
        // Actualizar el precio del detalle de venta
        $nuevoPrecio = $detalleVenta->precio_unitario - $descuentoFinal;
        $detalleVenta->update([
            'precio_unitario' => $nuevoPrecio,
            'subtotal' => $nuevoPrecio * $detalleVenta->cantidad
        ]);
        
        return $descuento;
    }
    
    /**
     * Calcula el valor del descuento.
     *
     * @param string $tipo (porcentaje o fijo)
     * @param float $valor
     * @param float $precioBase
     * @return float
     */
    private function calcularDescuento(string $tipo, float $valor, float $precioBase): float
    {
        if ($tipo === 'porcentaje') {
            return $precioBase * ($valor / 100);
        }
        return $valor;
    }
    
    /**
     * Obtiene el límite máximo de descuento permitido.
     *
     * @param DetalleVenta $detalleVenta
     * @return float
     */
    private function obtenerLimiteMaximoDescuento(DetalleVenta $detalleVenta): float
    {
        // Aquí implementar la lógica para obtener el límite máximo de descuento
        // Por ejemplo, podría ser un porcentaje del precio base
        return $detalleVenta->precio_unitario * 0.3; // 30% máximo por defecto
    }
    
    /**
     * Obtiene el historial de descuentos de una venta.
     *
     * @param int $ventaId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function obtenerHistorialDescuentos(int $ventaId)
    {
        return Descuento::whereHas('venta', function($query) use ($ventaId) {
            $query->where('id', $ventaId);
        })->with(['producto', 'aplicadoPor'])->get();
    }
}
