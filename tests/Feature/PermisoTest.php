<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Rol;
use App\Models\User;
use App\Models\Permiso;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Pruebas para el sistema de permisos
 * 
 * Estas pruebas verifican:
 * - La asignación correcta de permisos a roles
 * - La herencia de permisos de rol a usuario
 * - El funcionamiento del middleware de verificación de permisos
 */
class PermisoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Prueba que los permisos del rol administrador se asignan correctamente
     * 
     * @test
     * @return void
     */
    public function test_verifica_permisos_de_admin()
    {
        // Crear permisos necesarios para el rol admin
        $permisoRoles = Permiso::create(['nombre' => 'gestion_roles', 'descripcion' => 'Gestionar roles']);
        $permisoUsuarios = Permiso::create(['nombre' => 'gestion_usuarios', 'descripcion' => 'Gestionar usuarios']);

        // Crear rol admin y asignarle los permisos
        $rolAdmin = Rol::create(['nombre' => 'admin', 'descripcion' => 'Administrador']);
        $rolAdmin->permisos()->attach([
            $permisoRoles->id, 
            $permisoUsuarios->id
        ]);

        // Crear usuario admin con el rol
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $rolAdmin->id
        ]);

        // Verificar que:
        // 1. El rol admin tiene los permisos asignados
        // 2. El usuario hereda los permisos del rol correctamente
        $this->assertTrue($admin->rol->permisos->contains('nombre', 'gestion_roles'));
        $this->assertTrue($admin->rol->permisos->contains('nombre', 'gestion_usuarios'));
    }

    /**
     * Prueba que los permisos del rol vendedor se limitan correctamente
     * 
     * @test
     * @return void
     */
    public function test_verifica_permisos_de_vendedor()
    {
        // Crear permisos: uno que debería tener y otro que no
        $permisoVentas = Permiso::create(['nombre' => 'gestion_ventas', 'descripcion' => 'Gestionar ventas']);
        $permisoRoles = Permiso::create(['nombre' => 'gestion_roles', 'descripcion' => 'Gestionar roles']);

        // Crear rol vendedor y asignarle SOLO el permiso de ventas
        $rolVendedor = Rol::create(['nombre' => 'vendedor', 'descripcion' => 'Vendedor']);
        $rolVendedor->permisos()->attach($permisoVentas->id);

        // Crear usuario vendedor con el rol
        $vendedor = User::create([
            'name' => 'Vendedor Test',
            'email' => 'vendedor@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $rolVendedor->id
        ]);

        // Verificar que:
        // 1. El vendedor tiene el permiso de ventas
        // 2. El vendedor NO tiene el permiso de roles
        $this->assertTrue($vendedor->rol->permisos->contains('nombre', 'gestion_ventas'), 'El vendedor debe tener permiso de ventas');
        $this->assertFalse($vendedor->rol->permisos->contains('nombre', 'gestion_roles'), 'El vendedor no debe tener permiso de roles');
    }

    /**
     * Prueba que el middleware de verificación de permisos funciona correctamente
     * 
     * @test
     * @return void
     */
    public function test_middleware_verifica_permiso()
    {
        // Crear rol básico sin ningún permiso
        $rolBasico = Rol::create(['nombre' => 'basico', 'descripcion' => 'Usuario básico']);
        
        // Crear usuario con rol básico
        $usuario = User::create([
            'name' => 'Usuario Test',
            'email' => 'usuario@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $rolBasico->id
        ]);

        // Verificar que:
        // 1. No puede acceder a la gestión de roles
        // 2. No puede acceder a la gestión de usuarios
        // 3. Ambas rutas devuelven 403 Forbidden
        $response = $this->actingAs($usuario)->get('/roles');
        $response->assertStatus(403, 'Debería denegar acceso a roles');

        $response = $this->actingAs($usuario)->get('/users');
        $response->assertStatus(403, 'Debería denegar acceso a usuarios');
    }
}
