<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Rol;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UsersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear roles base
        $this->vendedorRol = Rol::create(['nombre' => 'vendedor', 'descripcion' => 'Vendedor']);
        $this->adminRol = Rol::create(['nombre' => 'admin', 'descripcion' => 'Administrador']);
    }

    public function test_solo_admin_puede_ver_usuarios()
    {
        // Usuario normal
        $usuario = User::factory()->create([
            'rol_id' => $this->vendedorRol->id
        ]);

        // Intento acceder a usuarios siendo usuario normal
        $response = $this->actingAs($usuario)->get('/users');
        $response->assertStatus(403);

        // Admin
        $admin = User::factory()->create([
            'rol_id' => $this->adminRol->id
        ]);

        // Intento acceder a usuarios siendo admin
        $response = $this->actingAs($admin)->get('/users');
        $response->assertStatus(200);
    }

    public function test_crear_usuario()
    {
        $admin = User::factory()->create([
            'rol_id' => $this->adminRol->id
        ]);

        $response = $this->actingAs($admin)->post('/users', [
            'name' => 'Nuevo Usuario',
            'email' => 'nuevo@ventaplus.com',
            'password' => 'password123',
            'rol_id' => $rolVendedor->id
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'name' => 'Nuevo Usuario',
            'email' => 'nuevo@ventaplus.com',
            'rol_id' => $rolVendedor->id
        ]);
    }

    public function test_desactivar_usuario()
    {
        $admin = User::factory()->create([
            'rol_id' => Rol::create(['nombre' => 'admin', 'descripcion' => 'Administrador'])->id
        ]);

        $usuario = User::factory()->create([
            'estado' => true
        ]);

        $response = $this->actingAs($admin)->put("/users/{$usuario->id}", [
            'name' => $usuario->name,
            'email' => $usuario->email,
            'rol_id' => $usuario->rol_id,
            'estado' => false
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $usuario->id,
            'estado' => false
        ]);
    }

    public function test_registro_de_actividad_usuarios()
    {
        $admin = User::factory()->create([
            'rol_id' => $this->adminRol->id
        ]);

        $this->actingAs($admin)->post('/users', [
            'name' => 'Nuevo Usuario',
            'email' => 'nuevo@ventaplus.com',
            'password' => 'password123',
            'rol_id' => $rolVendedor->id
        ]);

        $this->assertDatabaseHas('logs', [
            'usuario_id' => $admin->id,
            'accion' => 'crear_usuario',
            'modelo' => 'User'
        ]);
    }
}
