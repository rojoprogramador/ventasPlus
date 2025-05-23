<?php

// Archivo para verificar la estructura de la base de datos
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// Verificar la estructura de la tabla de clientes
echo "=== Estructura de la tabla de clientes ===\n";
$columns = Schema::getColumnListing('clientes');
echo implode(', ', $columns);
echo "\n\n";

// Verificar la estructura de la tabla de usuarios
echo "=== Estructura de la tabla de usuarios ===\n";
$columns = Schema::getColumnListing('users');
echo implode(', ', $columns);
echo "\n\n";

// Verificar la estructura de la tabla de permisos
echo "=== Estructura de la tabla de permisos ===\n";
$columns = Schema::getColumnListing('permisos');
echo implode(', ', $columns);
echo "\n\n";

// Verificar la estructura de la tabla de roles
echo "=== Estructura de la tabla de roles ===\n";
$columns = Schema::getColumnListing('roles');
echo implode(', ', $columns);
echo "\n\n";

// Verificar los permisos disponibles
echo "=== Permisos disponibles ===\n";
$permisos = DB::table('permisos')->get();
foreach ($permisos as $permiso) {
    echo $permiso->nombre . ': ' . $permiso->descripcion . "\n";
}
