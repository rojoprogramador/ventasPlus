<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'rol_id',
        'estado'
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
        'email_verified_at' => 'datetime',
        'estado' => 'boolean',
    ];

    /**
     * Get the rol associated with the user.
     */
    public function rol()
    {
        return $this->belongsTo(Rol::class);
    }

    /**
     * Get the permisos individuales for the user.
     */
    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'user_permiso')
                    ->withPivot('habilitado')
                    ->withTimestamps();
    }

    /**
     * Verifica si el usuario tiene un permiso específico.
     * Primero verifica permisos individuales, luego los del rol.
     */
    public function tienePermiso($nombrePermiso)
    {
        // Verificar permisos individuales primero
        $permisoIndividual = $this->permisos()
            ->where('nombre', $nombrePermiso)
            ->wherePivot('habilitado', true)
            ->first();

        if ($permisoIndividual) {
            return true;
        }

        // Si no tiene permiso individual, verificar permisos del rol
        return $this->rol->permisos->contains('nombre', $nombrePermiso);
    }

    /**
     * Asigna un permiso individual al usuario.
     */
    public function asignarPermiso($permisoId, $habilitado = true)
    {
        return $this->permisos()->syncWithoutDetaching([
            $permisoId => ['habilitado' => $habilitado]
        ]);
    }

    /**
     * Revoca un permiso individual del usuario.
     */
    public function revocarPermiso($permisoId)
    {
        return $this->permisos()->detach($permisoId);
    }

}
