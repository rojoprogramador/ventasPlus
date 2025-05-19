<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClientesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Crear clientes de ejemplo
        $clientes = [
            [
                'nombre' => 'Cliente General',
                'documento' => '9999999999',
                'tipo_documento' => 'CC',
                'email' => 'cliente@general.com',
                'telefono' => '3001234567',
                'direccion' => 'Dirección por defecto',
                'estado' => 'activo'
            ],
            [
                'nombre' => 'Juan Pérez',
                'documento' => '1234567890',
                'tipo_documento' => 'CC',
                'email' => 'juan.perez@ejemplo.com',
                'telefono' => '3141234567',
                'direccion' => 'Calle 123 # 45-67',
                'estado' => 'activo'
            ],
            [
                'nombre' => 'María López',
                'documento' => '0987654321',
                'tipo_documento' => 'CC',
                'email' => 'maria.lopez@ejemplo.com',
                'telefono' => '3159876543',
                'direccion' => 'Av. Principal # 89-12',
                'estado' => 'activo'
            ],
            [
                'nombre' => 'Empresa XYZ',
                'documento' => '900123456',
                'tipo_documento' => 'NIT',
                'email' => 'contacto@empresaxyz.com',
                'telefono' => '6011234567',
                'direccion' => 'Carrera 34 # 56-78 Oficina 301',
                'estado' => 'activo'
            ],
            [
                'nombre' => 'Pedro Rodríguez',
                'documento' => '5678901234',
                'tipo_documento' => 'CC',
                'email' => 'pedro.rodriguez@ejemplo.com',
                'telefono' => '3001234567',
                'direccion' => 'Calle 45 # 67-89',
                'estado' => 'inactivo'
            ]
        ];

        foreach ($clientes as $cliente) {
            Cliente::create($cliente);
        }
    }
}
