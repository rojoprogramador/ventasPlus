<?php

namespace Database\Seeders;

use App\Models\Rol;
use App\Models\Permiso;
use Illuminate\Database\Seeder;

class RolPermisoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener roles
        $admin = Rol::where('nombre', 'admin')->first();
        $vendedor = Rol::where('nombre', 'vendedor')->first();
        $cajero = Rol::where('nombre', 'cajero')->first();

        // Obtener permisos por nombre
        $permisos = [
            'ver_dashboard' => Permiso::where('nombre', 'ver_dashboard')->first()->id,
            'gestion_usuarios' => Permiso::where('nombre', 'gestion_usuarios')->first()->id,
            'gestion_roles' => Permiso::where('nombre', 'gestion_roles')->first()->id,
            'gestion_permisos' => Permiso::where('nombre', 'gestion_permisos')->first()->id,
            'gestion_clientes' => Permiso::where('nombre', 'gestion_clientes')->first()->id,
            'gestion_productos' => Permiso::where('nombre', 'gestion_productos')->first()->id,
            'gestion_ventas' => Permiso::where('nombre', 'gestion_ventas')->first()->id,
        ];

        // Asignar permisos al administrador (todos los permisos)
        $admin->permisos()->sync(array_values($permisos));

        // Asignar permisos al vendedor
        $vendedor->permisos()->sync([
            $permisos['ver_dashboard'],
            $permisos['gestion_clientes'],
            $permisos['gestion_productos'],
            $permisos['gestion_ventas']
        ]);

        // Asignar permisos al cajero
        $cajero->permisos()->sync([
            $permisos['ver_dashboard'],
            $permisos['gestion_ventas']
        ]);
    }
}
