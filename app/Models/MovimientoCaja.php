<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoCaja extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'movimientos_caja';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'caja_id',
        'usuario_id',
        'tipo',
        'monto',
        'descripcion',
        'fecha',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha' => 'datetime',
        'monto' => 'decimal:2',
    ];

    /**
     * Get the caja that owns the movimiento_caja.
     */
    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }

    /**
     * Get the usuario that owns the movimiento_caja.
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
