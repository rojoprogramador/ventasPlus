<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AsignarPermisoExportacion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permisos:asignar-exportacion';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asigna el permiso de exportación de datos al rol de administrador';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Verificar si existe el permiso
        $permiso = DB::table('permisos')->where('nombre', 'exportar_datos')->first();
        
        if (!$permiso) {
            // Crear el permiso si no existe
            $permisoId = DB::table('permisos')->insertGetId([
                'nombre' => 'exportar_datos',
                'descripcion' => 'Permite exportar datos del sistema para análisis',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            
            $this->info('Permiso "exportar_datos" creado correctamente.');
        } else {
            $permisoId = $permiso->id;
            $this->info('El permiso "exportar_datos" ya existe en la base de datos.');
        }
        
        // Obtener el ID del rol de administrador
        $adminRol = DB::table('roles')->where('nombre', 'admin')->first();
        
        if (!$adminRol) {
            $this->error('No se encontró el rol de administrador.');
            return 1;
        }
        
        $adminRolId = $adminRol->id;
        
        // Verificar las tablas pivot posibles
        $tablasPivot = ['permiso_rol', 'permisos_roles', 'rol_permiso', 'roles_permisos', 'permiso_role', 'role_permiso'];
        $tablaPivotEncontrada = null;
        
        foreach ($tablasPivot as $tabla) {
            try {
                if (Schema::hasTable($tabla)) {
                    $tablaPivotEncontrada = $tabla;
                    $this->info("Tabla pivot encontrada: {$tabla}");
                    break;
                }
            } catch (\Exception $e) {
                // Continuar con la siguiente tabla
            }
        }
        
        if (!$tablaPivotEncontrada) {
            // Mostrar todas las tablas disponibles
            $tablas = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
            $this->info("Tablas disponibles en la base de datos:");
            foreach ($tablas as $tabla) {
                $this->info("- " . $tabla->table_name);
            }
            
            $this->error('No se pudo encontrar la tabla pivot que relaciona roles y permisos.');
            return 1;
        }
        
        // Verificar las columnas de la tabla pivot
        $columnas = Schema::getColumnListing($tablaPivotEncontrada);
        $this->info("Columnas de la tabla {$tablaPivotEncontrada}:");
        foreach ($columnas as $columna) {
            $this->info("- " . $columna);
        }
        
        // Determinar los nombres de las columnas para rol_id y permiso_id
        $rolColumn = in_array('rol_id', $columnas) ? 'rol_id' : (in_array('role_id', $columnas) ? 'role_id' : null);
        $permisoColumn = in_array('permiso_id', $columnas) ? 'permiso_id' : (in_array('permission_id', $columnas) ? 'permission_id' : null);
        
        if (!$rolColumn || !$permisoColumn) {
            $this->error('No se pudieron determinar los nombres de las columnas para rol_id y permiso_id.');
            return 1;
        }
        
        // Verificar si la relación ya existe
        $relacionExistente = DB::table($tablaPivotEncontrada)
            ->where($rolColumn, $adminRolId)
            ->where($permisoColumn, $permisoId)
            ->first();
        
        if ($relacionExistente) {
            $this->info("La relación entre el permiso \"exportar_datos\" y el rol de administrador ya existe en la tabla {$tablaPivotEncontrada}.");
            return 0;
        }
        
        // Insertar la relación
        $data = [
            $rolColumn => $adminRolId,
            $permisoColumn => $permisoId
        ];
        
        // Añadir timestamps si la tabla los tiene
        if (in_array('created_at', $columnas)) {
            $data['created_at'] = Carbon::now();
        }
        
        if (in_array('updated_at', $columnas)) {
            $data['updated_at'] = Carbon::now();
        }
        
        DB::table($tablaPivotEncontrada)->insert($data);
        $this->info("Permiso \"exportar_datos\" asignado al rol de administrador en la tabla {$tablaPivotEncontrada}.");
        
        return 0;
    }
}
