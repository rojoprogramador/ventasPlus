<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Descuento extends Model
{
    protected $table = 'descuentos';
    
    protected $fillable = [
        'nombre',
        'tipo', // porcentaje o fijo
        'valor',
        'limite',
        'descripcion',
        'aplicado_por',
        'producto_id',
        'venta_id',
        'detalle_venta_id'
    ];
    
    protected $casts = [
        'valor' => 'decimal:2',
        'limite' => 'decimal:2'
    ];
    
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
    
    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
    
    public function detalleVenta()
    {
        return $this->belongsTo(DetalleVenta::class);
    }
    
    public function aplicadoPor()
    {
        return $this->belongsTo(User::class, 'aplicado_por');
    }
}
