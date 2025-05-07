<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Rol;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RolesPermisosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear roles base
        $this->vendedorRol = Rol::create(['nombre' => 'vendedor', 'descripcion' => 'Vendedor']);
        $this->adminRol = Rol::create(['nombre' => 'admin', 'descripcion' => 'Administrador']);
    }

    public function test_solo_admin_puede_ver_roles()
    {
        // Usuario normal
        $usuario = User::factory()->create([
            'rol_id' => $this->vendedorRol->id
        ]);

        // Intento acceder a roles siendo usuario normal
        $response = $this->actingAs($usuario)->get('/roles');
        $response->assertStatus(403);

        // Admin
        $admin = User::factory()->create([
            'rol_id' => $this->adminRol->id
        ]);

        // Intento acceder a roles siendo admin
        $response = $this->actingAs($admin)->get('/roles');
        $response->assertStatus(200);
    }

    public function test_crear_rol()
    {
        $admin = User::factory()->create([
            'rol_id' => $this->adminRol->id
        ]);

        $response = $this->actingAs($admin)->post('/roles', [
            'nombre' => 'nuevo_rol',
            'descripcion' => 'Nuevo Rol de Prueba',
            'permisos' => []
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('roles', [
            'nombre' => 'nuevo_rol',
            'descripcion' => 'Nuevo Rol de Prueba'
        ]);
    }

    public function test_actualizar_rol()
    {
        $admin = User::factory()->create([
            'rol_id' => $this->adminRol->id
        ]);

        $rol = Rol::create([
            'nombre' => 'rol_viejo',
            'descripcion' => 'Descripción Vieja'
        ]);

        $response = $this->actingAs($admin)->put("/roles/{$rol->id}", [
            'nombre' => 'rol_actualizado',
            'descripcion' => 'Descripción Nueva',
            'permisos' => []
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('roles', [
            'id' => $rol->id,
            'nombre' => 'rol_actualizado',
            'descripcion' => 'Descripción Nueva'
        ]);
    }

    public function test_registro_de_actividad()
    {
        $admin = User::factory()->create([
            'rol_id' => $this->adminRol->id
        ]);

        $this->actingAs($admin)->post('/roles', [
            'nombre' => 'nuevo_rol',
            'descripcion' => 'Nuevo Rol de Prueba',
            'permisos' => []
        ]);

        $this->assertDatabaseHas('logs', [
            'usuario_id' => $admin->id,
            'accion' => 'crear_rol',
            'modelo' => 'Rol'
        ]);
    }
}
