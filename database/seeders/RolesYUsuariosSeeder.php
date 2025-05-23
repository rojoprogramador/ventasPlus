<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permiso;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RolesYUsuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Log::info('Iniciando RolesYUsuariosSeeder');
        
        // Comenzar una transacción para asegurar la integridad de los datos
        DB::beginTransaction();
        
        try {
            // Crear los roles si no existen
            $roles = ['admin', 'cajero', 'vendedor'];
            $rolesCreados = [];
            
            foreach ($roles as $rolNombre) {
                $rol = Rol::firstOrCreate(
                    ['nombre' => $rolNombre],
                    ['descripcion' => ucfirst($rolNombre)]
                );
                $rolesCreados[$rolNombre] = $rol;
                Log::info("Rol {$rolNombre} creado o verificado (ID: {$rol->id})");
            }

            // Asignación de permisos a roles
            // 1. Administrador - todos los permisos
            $todosLosPermisos = Permiso::all();
            $rolesCreados['admin']->permisos()->sync($todosLosPermisos->pluck('id')->toArray());
            Log::info("Asignados " . $todosLosPermisos->count() . " permisos al rol admin");

            // 2. Cajero - permisos relacionados con caja y ventas
            $permisosCajero = Permiso::whereIn('nombre', [
                'acceso_caja', 'gestionar_caja', 'ver_ventas', 'crear_ventas',
                'gestionar_clientes', 'ver_productos'
            ])->get();
            $rolesCreados['cajero']->permisos()->sync($permisosCajero->pluck('id')->toArray());
            Log::info("Asignados " . $permisosCajero->count() . " permisos al rol cajero");

            // 3. Vendedor - permisos relacionados con ventas y productos
            $permisosVendedor = Permiso::whereIn('nombre', [
                'ver_productos', 'crear_ventas', 'ver_ventas',
                'gestionar_clientes'
            ])->get();
            $rolesCreados['vendedor']->permisos()->sync($permisosVendedor->pluck('id')->toArray());
            Log::info("Asignados " . $permisosVendedor->count() . " permisos al rol vendedor");

            // Crear usuarios de prueba para cada rol
            $usuariosTest = [
                'admin' => [
                    'name' => 'Administrador',
                    'email' => 'admin@test.com',
                    'password' => 'password',
                    'rol_id' => $rolesCreados['admin']->id
                ],
                'cajero' => [
                    'name' => 'Cajero',
                    'email' => 'cajero@test.com',
                    'password' => 'password',
                    'rol_id' => $rolesCreados['cajero']->id
                ],
                'vendedor' => [
                    'name' => 'Vendedor',
                    'email' => 'vendedor@test.com',
                    'password' => 'password',
                    'rol_id' => $rolesCreados['vendedor']->id
                ]
            ];

            foreach ($usuariosTest as $key => $userData) {
                $user = User::updateOrCreate(
                    ['email' => $userData['email']],
                    [
                        'name' => $userData['name'],
                        'password' => Hash::make($userData['password']),
                        'rol_id' => $userData['rol_id']
                    ]
                );
                Log::info("Usuario {$key} creado o actualizado (ID: {$user->id})");
            }

            // Confirmar transacción
            DB::commit();
            Log::info('RolesYUsuariosSeeder completado con éxito');
            
        } catch (\Exception $e) {
            // Revertir transacción en caso de error
            DB::rollBack();
            Log::error('Error en RolesYUsuariosSeeder: ' . $e->getMessage());
            throw $e;
        }
    }
}
