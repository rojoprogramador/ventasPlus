<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class VerificarPermisosUsuario extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:verificar-permisos {email} {permiso}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica si un usuario tiene un permiso específico';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->argument('email');
        $permisoBuscado = $this->argument('permiso');
        
        // Buscar el usuario por email
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("Usuario con email {$email} no encontrado.");
            return 1;
        }
        
        $this->info("Usuario encontrado: {$user->name} (ID: {$user->id})");
        $this->info("Rol: {$user->rol->nombre} (ID: {$user->rol->id})");
        
        // Verificar el permiso directamente en la base de datos
        $tienePermiso = DB::table('rol_permiso')
            ->join('permisos', 'rol_permiso.permiso_id', '=', 'permisos.id')
            ->where('rol_permiso.rol_id', $user->rol_id)
            ->where('permisos.nombre', $permisoBuscado)
            ->exists();
            
        $this->info("\nVerificación en la base de datos:");
        $this->info("¿Tiene el permiso '{$permisoBuscado}'? " . ($tienePermiso ? 'Sí' : 'No'));
        
        // Mostrar todos los permisos del rol
        $permisos = DB::table('rol_permiso')
            ->join('permisos', 'rol_permiso.permiso_id', '=', 'permisos.id')
            ->where('rol_permiso.rol_id', $user->rol_id)
            ->select('permisos.id', 'permisos.nombre', 'permisos.descripcion')
            ->get();
            
        $this->info("\nPermisos asignados al rol '{$user->rol->nombre}':");
        
        if ($permisos->isEmpty()) {
            $this->warn("No se encontraron permisos asignados a este rol.");
        } else {
            $this->table(
                ['ID', 'Nombre', 'Descripción'],
                $permisos->toArray()
            );
        }
        
        return 0;
    }
}
