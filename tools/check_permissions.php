<?php

// Este script verifica los permisos asignados al rol admin
// Ejecutar desde la raíz del proyecto con: php tools/check_permissions.php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Rol;
use App\Models\User;
use App\Models\Permiso;

echo "Verificando permisos de exportación de datos...\n";

// Comprobar si existe el permiso
$exportar_datos = Permiso::where('nombre', 'exportar_datos')->first();
if ($exportar_datos) {
    echo "✓ El permiso 'exportar_datos' existe con ID: {$exportar_datos->id}\n";
} else {
    echo "✗ El permiso 'exportar_datos' NO existe\n";
    // Crear el permiso si no existe
    $exportar_datos = Permiso::create([
        'nombre' => 'exportar_datos',
        'descripcion' => 'Permite exportar datos del sistema para análisis'
    ]);
    echo "  → Permiso 'exportar_datos' creado con ID: {$exportar_datos->id}\n";
}

// Comprobar si el rol admin tiene el permiso
$admin_role = Rol::where('nombre', 'admin')->first();
if ($admin_role) {
    echo "✓ El rol 'admin' existe con ID: {$admin_role->id}\n";
    
    $has_permission = $admin_role->permisos()->where('nombre', 'exportar_datos')->exists();
    if ($has_permission) {
        echo "✓ El rol 'admin' tiene el permiso 'exportar_datos'\n";
    } else {
        echo "✗ El rol 'admin' NO tiene el permiso 'exportar_datos'\n";
        // Asignar permiso al rol admin
        if ($exportar_datos) {
            $admin_role->permisos()->attach($exportar_datos->id);
            echo "  → Permiso 'exportar_datos' asignado al rol 'admin'\n";
        }
    }
} else {
    echo "✗ El rol 'admin' NO existe\n";
}

// Comprobar si el usuario admin tiene el rol admin
$admin_user = User::where('email', 'admin@ventaplus.com')->first();
if ($admin_user) {
    echo "✓ El usuario 'admin@ventaplus.com' existe con ID: {$admin_user->id}\n";
    
    if ($admin_user->rol_id == $admin_role->id) {
        echo "✓ El usuario 'admin@ventaplus.com' tiene el rol 'admin'\n";
        
        // Verificar permisos del usuario
        echo "\nPermisos del usuario 'admin@ventaplus.com':\n";
        if ($admin_user->rol->permisos->count() > 0) {
            foreach ($admin_user->rol->permisos as $permiso) {
                echo "- {$permiso->nombre}: {$permiso->descripcion}\n";
            }
        } else {
            echo "El usuario no tiene permisos asignados a través de su rol\n";
        }
    } else {
        echo "✗ El usuario 'admin@ventaplus.com' NO tiene el rol 'admin'\n";
    }
} else {
    echo "✗ El usuario 'admin@ventaplus.com' NO existe\n";
}

echo "\nVerificación completada.\n";
