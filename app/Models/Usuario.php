<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'usuarios';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'email',
        'password',
        'rol_id',
        'estado',
        'ultimo_acceso'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_creacion' => 'datetime',
        'ultimo_acceso' => 'datetime',
        'estado' => 'boolean',
    ];

    /**
     * Get the rol associated with the usuario.
     */
    public function rol()
    {
        return $this->belongsTo(Rol::class);
    }

    /**
     * Get the ventas for the usuario.
     */
    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    /**
     * Get the cotizaciones for the usuario.
     */
    public function cotizaciones()
    {
        return $this->hasMany(Cotizacion::class);
    }

    /**
     * Get the cajas for the usuario.
     */
    public function cajas()
    {
        return $this->hasMany(Caja::class);
    }

    /**
     * Get the movimientos_caja for the usuario.
     */
    public function movimientosCaja()
    {
        return $this->hasMany(MovimientoCaja::class);
    }
}
