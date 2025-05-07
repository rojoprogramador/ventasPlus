<?php

namespace App\Traits;

trait VerificaPermisos
{
    /**
     * Verifica si el usuario tiene un permiso específico
     *
     * @param string $permiso
     * @return bool
     */
    public function tienePermiso(string $permiso): bool
    {
        return auth()->check() && 
               auth()->user()->rol && 
               auth()->user()->rol->permisos->contains('nombre', $permiso);
    }

    /**
     * Verifica si el usuario tiene alguno de los permisos especificados
     *
     * @param array $permisos
     * @return bool
     */
    public function tieneAlgunPermiso(array $permisos): bool
    {
        return auth()->check() && 
               auth()->user()->rol && 
               auth()->user()->rol->permisos
                    ->whereIn('nombre', $permisos)
                    ->isNotEmpty();
    }

    /**
     * Verifica si el usuario tiene todos los permisos especificados
     *
     * @param array $permisos
     * @return bool
     */
    public function tieneTodosLosPermisos(array $permisos): bool
    {
        if (!auth()->check() || !auth()->user()->rol) {
            return false;
        }

        $permisosUsuario = auth()->user()->rol->permisos->pluck('nombre')->toArray();
        return empty(array_diff($permisos, $permisosUsuario));
    }
}
