<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Rol;
use App\Models\Permiso;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $rol;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear rol y permisos necesarios
        $this->rol = Rol::create([
            'nombre' => 'admin',
            'descripcion' => 'Administrador del sistema'
        ]);

        $permiso = Permiso::create([
            'nombre' => 'gestion_usuarios',
            'descripcion' => 'Gestionar usuarios del sistema'
        ]);

        $this->rol->permisos()->attach($permiso->id);

        // Crear usuario administrador
        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $this->rol->id,
            'estado' => true
        ]);
    }

    /** @test */
    public function un_admin_puede_ver_la_lista_de_usuarios()
    {
        $response = $this->actingAs($this->admin)->get('/users');
        $response->assertStatus(200);
        $response->assertInertia(fn ($assert) => $assert
            ->component('Users/Index')
            ->has('users')
            ->has('roles')
            ->has('permisos')
        );
    }

    /** @test */
    public function un_admin_puede_crear_un_usuario()
    {
        $response = $this->actingAs($this->admin)->post('/users', [
            'name' => 'Nuevo Usuario',
            'email' => 'nuevo@test.com',
            'password' => 'Password1!',
            'rol_id' => $this->rol->id
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'nuevo@test.com'
        ]);
    }

    /** @test */
    public function un_admin_puede_actualizar_un_usuario()
    {
        $user = User::create([
            'name' => 'Usuario Test',
            'email' => 'test@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $this->rol->id,
            'estado' => true
        ]);

        $response = $this->actingAs($this->admin)->put("/users/{$user->id}", [
            'name' => 'Usuario Actualizado',
            'email' => 'test@test.com',
            'rol_id' => $this->rol->id,
            'estado' => true
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Usuario Actualizado'
        ]);
    }

    /** @test */
    public function un_admin_puede_gestionar_permisos_individuales()
    {
        $user = User::create([
            'name' => 'Usuario Test',
            'email' => 'test@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $this->rol->id,
            'estado' => true
        ]);

        $permiso = Permiso::create([
            'nombre' => 'permiso_test',
            'descripcion' => 'Permiso de prueba'
        ]);

        $response = $this->actingAs($this->admin)->put("/users/{$user->id}", [
            'name' => $user->name,
            'email' => $user->email,
            'rol_id' => $user->rol_id,
            'estado' => $user->estado,
            'permisos' => [
                ['id' => $permiso->id, 'habilitado' => true]
            ]
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('user_permiso', [
            'user_id' => $user->id,
            'permiso_id' => $permiso->id,
            'habilitado' => true
        ]);
    }
}
