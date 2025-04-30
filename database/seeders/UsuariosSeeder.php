<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Usuario::create([
            'nombre' => 'Administrador',
            'email' => 'admin@ventaplus.com',
            'password' => bcrypt('admin123'),
            'rol_id' => 1, // admin
            'estado' => 'activo'
        ]);

        \App\Models\Usuario::create([
            'nombre' => 'Vendedor',
            'email' => 'vendedor@ventaplus.com',
            'password' => bcrypt('vendedor123'),
            'rol_id' => 2, // vendedor
            'estado' => 'activo'
        ]);

        \App\Models\Usuario::create([
            'nombre' => 'Cajero',
            'email' => 'cajero@ventaplus.com',
            'password' => bcrypt('cajero123'),
            'rol_id' => 3, // cajero
            'estado' => 'activo'
        ]);
    }
}
