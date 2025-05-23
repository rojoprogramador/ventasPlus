<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permiso;
use App\Models\Rol;

class ExportarDatosPermisoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear el permiso de exportar datos si no existe
        $permiso = Permiso::firstOrCreate(
            ['nombre' => 'exportar_datos'],
            ['descripcion' => 'Permite exportar datos del sistema para análisis']
        );

        // Obtener el rol admin
        $admin = Rol::where('nombre', 'admin')->first();
        
        // Si existe el rol admin, asignarle el permiso
        if ($admin) {
            // Verificar si ya tiene el permiso asignado para evitar duplicados
            if (!$admin->permisos()->where('permisos.id', $permiso->id)->exists()) {
                $admin->permisos()->attach($permiso->id);
                $this->command->info('Permiso exportar_datos asignado al rol admin');
            } else {
                $this->command->info('El rol admin ya tenía asignado el permiso exportar_datos');
            }
        } else {
            $this->command->error('No se encontró el rol admin');
        }
    }
}
