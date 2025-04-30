<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConfiguracionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Configuracion::create([
            'nombre_empresa' => 'VentaPlus',
            'ruc' => '12345678901',
            'direccion' => 'Calle Principal 123',
            'telefono' => '123456789',
            'email' => 'info@ventaplus.com',
            'impuesto' => 18
        ]);
    }
}
