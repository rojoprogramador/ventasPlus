<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerificarPermisosRol extends Command
{
    protected $signature = 'rol:verificar-permisos {rol}';
    protected $description = 'Verifica los permisos asignados a un rol';

    public function handle()
    {
        $rolNombre = $this->argument('rol');
        
        // Obtener el rol con sus permisos
        $rol = DB::table('roles')
            ->leftJoin('rol_permiso', 'roles.id', '=', 'rol_permiso.rol_id')
            ->leftJoin('permisos', 'rol_permiso.permiso_id', '=', 'permisos.id')
            ->where('roles.nombre', $rolNombre)
            ->select(
                'roles.id as rol_id',
                'roles.nombre as rol_nombre',
                'permisos.id as permiso_id',
                'permisos.nombre as permiso_nombre',
                'permisos.descripcion as permiso_descripcion'
            )
            ->get();
            
        if ($rol->isEmpty()) {
            $this->error("No se encontró el rol '{$rolNombre}'");
            return 1;
        }
        
        $this->info("Permisos del rol: {$rol->first()->rol_nombre}");
        $this->info("===================================");
        
        if ($rol->first()->permiso_id === null) {
            $this->warn("Este rol no tiene permisos asignados.");
            return 0;
        }
        
        $headers = ['ID Permiso', 'Nombre', 'Descripción'];
        $rows = $rol->map(function($item) {
            return [
                $item->permiso_id,
                $item->permiso_nombre,
                $item->permiso_descripcion
            ];
        });
        
        $this->table($headers, $rows);
        
        // Verificar específicamente el permiso 'exportar_datos'
        $tieneExportarDatos = $rol->contains('permiso_nombre', 'exportar_datos');
        $this->info("\n¿Tiene el permiso 'exportar_datos'? " . ($tieneExportarDatos ? 'Sí' : 'No'));
        
        return 0;
    }
}
