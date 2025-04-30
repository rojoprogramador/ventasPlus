<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'configuracion';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre_empresa',
        'direccion',
        'telefono',
        'email',
        'rfc',
        'sitio_web',
        'logo',
        'moneda',
        'porcentaje_impuesto',
        'formato_factura',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'porcentaje_impuesto' => 'decimal:2',
    ];
}
