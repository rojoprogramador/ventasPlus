<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
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
     * Primero verifica permisos en la sesión, luego individuales y finalmente los del rol.
     */
    public function tienePermiso($nombrePermiso)
    {
        // Depuración: Verificar si el permiso solicitado es 'exportar_datos'
        $esExportarDatos = ($nombrePermiso === 'exportar_datos');
        
        if ($esExportarDatos) {            Log::info('Verificando permiso exportar_datos para el usuario: ' . $this->email);
            Log::info('ID de usuario: ' . $this->id);
            Log::info('Rol ID: ' . $this->rol_id);
            Log::info('Rol cargado: ' . ($this->relationLoaded('rol') ? 'Sí' : 'No'));
        }

        // 1. Verificar permisos en la sesión
        if (session()->has('user_permisos')) {
            $permisosSesion = session('user_permisos', []);
            
            if ($esExportarDatos) {                Log::info('Permisos en sesión:', $permisosSesion);
                Log::info('Buscando exportar_datos en sesión: ' . (in_array('exportar_datos', $permisosSesion) ? 'Encontrado' : 'No encontrado'));
            }
            
            if (in_array($nombrePermiso, $permisosSesion)) {                if ($esExportarDatos) {
                    Log::info('Permiso exportar_datos encontrado en la sesión');
                }
                return true;
            }        } elseif ($esExportarDatos) {
            Log::warning('No se encontraron permisos en la sesión para el usuario: ' . $this->email);
        }

        // 2. Verificar permisos individuales
        $permisoIndividual = $this->permisos()
            ->where('nombre', $nombrePermiso)
            ->wherePivot('habilitado', true)
            ->first();

        if ($permisoIndividual) {            if ($esExportarDatos) {
                Log::info('Permiso exportar_datos encontrado en permisos individuales');
            }
            return true;        } elseif ($esExportarDatos) {
            Log::info('No se encontró el permiso exportar_datos en permisos individuales');
        }

        // 3. Verificar permisos del rol
        if ($this->rol) {            if ($esExportarDatos) {
                Log::info('Rol del usuario: ' . $this->rol->nombre);
            }
            
            // Intentar cargar los permisos si no están cargados
            if (!$this->rol->relationLoaded('permisos')) {
                $this->rol->load('permisos');                if ($esExportarDatos) {
                    Log::info('Permisos cargados manualmente para el rol');
                }
            }
            
            if ($this->rol->relationLoaded('permisos')) {
                $tienePermiso = $this->rol->permisos->contains('nombre', $nombrePermiso);                if ($esExportarDatos) {
                    Log::info('Permisos del rol:', $this->rol->permisos->pluck('nombre')->toArray());
                    Log::info('¿Tiene el permiso exportar_datos? ' . ($tienePermiso ? 'Sí' : 'No'));
                }
                return $tienePermiso;            } elseif ($esExportarDatos) {
                Log::warning('No se pudo cargar la relación de permisos para el rol');
            }
            
            // Último recurso: consulta directa a la base de datos
            $tienePermiso = $this->rol->permisos()->where('nombre', $nombrePermiso)->exists();            if ($esExportarDatos) {
                Log::info('Resultado de consulta directa a la base de datos: ' . ($tienePermiso ? 'Sí' : 'No'));
            }
            return $tienePermiso;        } elseif ($esExportarDatos) {
            Log::warning('El usuario no tiene un rol asignado');
        }        if ($esExportarDatos) {
            Log::warning('No se pudo verificar el permiso exportar_datos para el usuario: ' . $this->email);
        }
        
        return false;
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
