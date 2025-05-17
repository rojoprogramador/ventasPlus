<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ventas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cliente_id',
        'usuario_id',
        'caja_id',
        'codigo',
        'fecha',
        'subtotal',
        'descuento',
        'impuesto',
        'total',
        'estado',
        'tipo_pago',
        'observaciones',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha' => 'datetime',
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'impuesto' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Get the cliente that owns the venta.
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Get the usuario that owns the venta.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Get the caja that owns the venta.
     */
    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }

    /**
     * Get the detalles for the venta.
     */
    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class);
    }
}
