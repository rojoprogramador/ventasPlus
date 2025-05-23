<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class ExportarDatosPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Verificar si el permiso ya existe
        $permisoExistente = DB::table('permisos')->where('nombre', 'exportar_datos')->first();
        
        if (!$permisoExistente) {
            // Insertar el nuevo permiso
            DB::table('permisos')->insert([
                'nombre' => 'exportar_datos',
                'descripcion' => 'Permite exportar datos del sistema para análisis',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            
            $this->command->info('Permiso "exportar_datos" creado correctamente.');
            
            // Obtener el ID del permiso recién creado
            $permisoId = DB::table('permisos')->where('nombre', 'exportar_datos')->value('id');
            
            // Obtener el ID del rol de administrador
            $adminRolId = DB::table('roles')->where('nombre', 'admin')->value('id');
            
            if ($adminRolId) {
                // Asignar el permiso al rol de administrador
                // Verificar primero cómo se llama la tabla pivot
                $tablasPivot = ['permiso_rol', 'permisos_roles', 'rol_permiso', 'roles_permisos'];
                $tablaPivotEncontrada = null;
                
                foreach ($tablasPivot as $tabla) {
                    try {
                        if (Schema::hasTable($tabla)) {
                            $tablaPivotEncontrada = $tabla;
                            break;
                        }
                    } catch (\Exception $e) {
                        // Continuar con la siguiente tabla
                    }
                }
                
                if ($tablaPivotEncontrada) {
                    // Verificar si la relación ya existe
                    $relacionExistente = DB::table($tablaPivotEncontrada)
                        ->where('rol_id', $adminRolId)
                        ->where('permiso_id', $permisoId)
                        ->first();
                    
                    if (!$relacionExistente) {
                        // Insertar la relación
                        $data = [
                            'rol_id' => $adminRolId,
                            'permiso_id' => $permisoId
                        ];
                        
                        // Añadir timestamps si la tabla los tiene
                        if (Schema::hasColumn($tablaPivotEncontrada, 'created_at')) {
                            $data['created_at'] = Carbon::now();
                        }
                        
                        if (Schema::hasColumn($tablaPivotEncontrada, 'updated_at')) {
                            $data['updated_at'] = Carbon::now();
                        }
                        
                        DB::table($tablaPivotEncontrada)->insert($data);
                        $this->command->info("Permiso \"exportar_datos\" asignado al rol de administrador en la tabla {$tablaPivotEncontrada}.");
                    } else {
                        $this->command->info("La relación entre el permiso \"exportar_datos\" y el rol de administrador ya existe.");
                    }
                } else {
                    $this->command->error("No se pudo encontrar la tabla pivot que relaciona roles y permisos.");
                }
            }
        } else {
            $this->command->info('El permiso "exportar_datos" ya existe en la base de datos.');
        }
    }
}
