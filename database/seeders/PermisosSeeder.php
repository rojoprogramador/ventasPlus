<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermisosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Permiso::create([
            'nombre' => 'ver_dashboard',
            'descripcion' => 'Ver el dashboard'
        ]);

        \App\Models\Permiso::create([
            'nombre' => 'gestion_usuarios',
            'descripcion' => 'Gestionar usuarios del sistema'
        ]);

        \App\Models\Permiso::create([
            'nombre' => 'gestion_roles',
            'descripcion' => 'Gestionar roles del sistema'
        ]);

        \App\Models\Permiso::create([
            'nombre' => 'gestion_permisos',
            'descripcion' => 'Gestionar permisos del sistema'
        ]);

        \App\Models\Permiso::create([
            'nombre' => 'gestion_clientes',
            'descripcion' => 'Gestionar clientes'
        ]);

        \App\Models\Permiso::create([
            'nombre' => 'gestion_productos',
            'descripcion' => 'Gestionar productos'
        ]);

        \App\Models\Permiso::create([
            'nombre' => 'gestion_ventas',
            'descripcion' => 'Gestionar ventas'
        ]);

        \App\Models\Permiso::create([
            'nombre' => 'aplicar_descuentos',
            'descripcion' => 'Aplicar descuentos en ventas'
        ]);

        \App\Models\Permiso::create([
            'nombre' => 'gestion_cotizaciones',
            'descripcion' => 'Gestionar cotizaciones'
        ]);

        \App\Models\Permiso::create([
            'nombre' => 'gestion_caja',
            'descripcion' => 'Gestionar caja'
        ]);
    }
}
