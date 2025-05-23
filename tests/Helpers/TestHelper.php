<?php

namespace Tests\Helpers;

use App\Models\Rol;
use App\Models\User;
use App\Models\Permiso;

class TestHelper 
{
    /**
     * Crea y prepara un entorno de prueba con roles y permisos
     * 
     * @return array Contiene las claves 'admin', 'rolAdmin', etc.
     */
    public static function prepararEntornoPruebas()
    {
        // Crear rol admin
        $rolAdmin = Rol::firstOrCreate(
            ['nombre' => 'admin'],
            ['descripcion' => 'Administrador']
        );
        
        // Crear rol vendedor
        $rolVendedor = Rol::firstOrCreate(
            ['nombre' => 'vendedor'],
            ['descripcion' => 'Vendedor']
        );

        // Crear permisos comunes
        $permisoUsuarios = Permiso::firstOrCreate(
            ['nombre' => 'gestion_usuarios'],
            ['descripcion' => 'Gestionar usuarios']
        );
        
        // Asociar permiso con rol admin
        $rolAdmin->permisos()->syncWithoutDetaching([$permisoUsuarios->id]);
        
        // Crear usuario admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin Test',
                'password' => bcrypt('password'),
                'rol_id' => $rolAdmin->id,
                'estado' => true
            ]
        );

        return [
            'admin' => $admin,
            'rolAdmin' => $rolAdmin,
            'rolVendedor' => $rolVendedor,
            'permisoUsuarios' => $permisoUsuarios
        ];
    }
}
