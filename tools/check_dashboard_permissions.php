<?php
// Este script verifica que el controlador de Dashboard está cargando correctamente los roles y permisos

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

echo "Verificando carga de permisos para el dashboard...\n\n";

// Obtener el usuario admin
$admin = User::where('email', 'admin@ventaplus.com')->first();
if (!$admin) {
    echo "Usuario admin no encontrado.\n";
    exit(1);
}

echo "Usuario: {$admin->name} (ID: {$admin->id})\n";
echo "Rol ID: {$admin->rol_id}\n";

// Cargar el rol con los permisos
$admin->load('rol.permisos');

// Verificar que el rol se cargó correctamente
if ($admin->relationLoaded('rol')) {
    echo "✓ Relación 'rol' cargada correctamente.\n";
    echo "Rol: {$admin->rol->nombre}\n";

    // Verificar que los permisos se cargaron correctamente
    if ($admin->rol->relationLoaded('permisos')) {
        echo "✓ Relación 'permisos' cargada correctamente.\n";
        echo "Permisos:\n";
        
        foreach ($admin->rol->permisos as $permiso) {
            echo "- {$permiso->nombre}\n";
        }

        // Verificar específicamente el permiso exportar_datos
        $tieneExportarDatos = $admin->rol->permisos->contains('nombre', 'exportar_datos');
        echo "\nTiene permiso 'exportar_datos': " . ($tieneExportarDatos ? "Sí" : "No") . "\n";
        
        // Usando el método tienePermiso (si existe)
        if (method_exists($admin, 'tienePermiso')) {
            echo "Verificación con método tienePermiso(): " . ($admin->tienePermiso('exportar_datos') ? "Sí" : "No") . "\n";
        }
    } else {
        echo "✗ Relación 'permisos' no cargada.\n";
    }
} else {
    echo "✗ Relación 'rol' no cargada.\n";
}

echo "\nVerificación completada.\n";
