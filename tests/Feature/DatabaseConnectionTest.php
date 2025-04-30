<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DatabaseConnectionTest extends TestCase
{
    public function test_database_connection()
    {
        try {
            DB::connection()->getPdo();
            $this->assertTrue(true, 'Database connection successful');
        } catch (\Exception $e) {
            $this->fail('Database connection failed: ' . $e->getMessage());
        }
    }

    public function test_migrations_exist()
    {
        $tables = [
            'roles',
            'permisos',
            'usuarios',
            'clientes',
            'productos',
            'categorias',
            'cajas',
            'ventas',
            'detalle_venta',
            'movimientos_caja',
            'cotizaciones',
            'detalle_cotizacion',
            'configuracion'
        ];

        foreach ($tables as $table) {
            $this->assertTrue(
                DB::getSchemaBuilder()->hasTable($table),
                "Table {$table} does not exist"
            );
        }
    }
}
