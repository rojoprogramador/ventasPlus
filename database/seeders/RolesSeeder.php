<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Rol::create([
            'nombre' => 'admin',
            'descripcion' => 'Administrador del sistema'
        ]);

        \App\Models\Rol::create([
            'nombre' => 'vendedor',
            'descripcion' => 'Vendedor de la tienda'
        ]);

        \App\Models\Rol::create([
            'nombre' => 'cajero',
            'descripcion' => 'Cajero de la tienda'
        ]);
    }
}
