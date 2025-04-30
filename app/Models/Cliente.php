<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'clientes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'telefono',
        'email',
        'direccion',
        'identificacion',
        'tipo_identificacion',
        'estado',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_registro' => 'datetime',
        'estado' => 'boolean',
    ];

    /**
     * Get the ventas for the cliente.
     */
    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    /**
     * Get the cotizaciones for the cliente.
     */
    public function cotizaciones()
    {
        return $this->hasMany(Cotizacion::class);
    }
}
