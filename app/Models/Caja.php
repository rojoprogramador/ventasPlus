<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cajas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'usuario_id',
        'fecha_apertura',
        'fecha_cierre',
        'monto_inicial',
        'monto_final',
        'total_ventas',
        'estado',
        'observaciones',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
        'monto_inicial' => 'decimal:2',
        'monto_final' => 'decimal:2',
        'total_ventas' => 'decimal:2',
    ];

    /**
     * Get the usuario that owns the caja.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Get the ventas for the caja.
     */
    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    /**
     * Get the movimientos for the caja.
     */
    public function movimientos()
    {
        return $this->hasMany(MovimientoCaja::class);
    }
}
