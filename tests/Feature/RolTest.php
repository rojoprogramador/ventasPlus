<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Rol;
use App\Models\User;
use App\Models\Permiso;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Pruebas para la gestión de roles
 * 
 * Estas pruebas verifican:
 * - La visualización de roles (requiere permiso gestion_roles)
 * - La creación de roles (requiere permiso gestion_roles)
 * - La restricción de acceso basada en permisos
 */
class RolTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Rol El rol de administrador para las pruebas
     */
    private $rolAdmin;

    /**
     * @var User El usuario administrador para las pruebas
     */
    private $admin;

    /**
     * Configuración inicial para cada prueba
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear rol admin y permiso de gestión de roles
        $this->rolAdmin = Rol::create(['nombre' => 'admin', 'descripcion' => 'Administrador']);
        $permisoRoles = Permiso::create(['nombre' => 'gestion_roles', 'descripcion' => 'Gestionar roles']);
        $this->rolAdmin->permisos()->attach($permisoRoles->id);

        // Crear usuario admin
        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $this->rolAdmin->id
        ]);
    }

    /**
     * Prueba que un administrador puede ver la lista de roles
     * 
     * @test
     * @return void
     */
    public function test_admin_puede_ver_lista_de_roles()
    {
        // Actuar como admin y hacer la petición
        $response = $this->actingAs($this->admin)->get('/roles');

        // Verificar que:
        // 1. La respuesta es exitosa (código 200)
        // 2. Se carga el componente correcto
        // 3. Se pasan los datos necesarios (roles y permisos)
        $response->assertStatus(200);
        $response->assertInertia(fn ($assert) => $assert
            ->component('Roles/Index')
            ->has('roles')
            ->has('permisos')
        );
    }

    /**
     * Prueba que un usuario sin permiso no puede ver roles
     * 
     * @test
     * @return void
     */
    public function test_usuario_sin_permiso_no_puede_ver_roles()
    {
        // Crear rol sin permisos y usuario vendedor
        $rolVendedor = Rol::create(['nombre' => 'vendedor', 'descripcion' => 'Vendedor']);
        $vendedor = User::create([
            'name' => 'Vendedor Test',
            'email' => 'vendedor@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $rolVendedor->id
        ]);

        // Actuar como vendedor e intentar ver roles
        $response = $this->actingAs($vendedor)->get('/roles');

        // Verificar que se niega el acceso (código 403 Forbidden)
        $response->assertStatus(403);
    }

    /**
     * Prueba que un administrador puede crear un nuevo rol
     * 
     * @test
     * @return void
     */
    public function test_admin_puede_crear_rol()
    {
        // Crear permisos para asignar al nuevo rol
        $permisoVentas = Permiso::create(['nombre' => 'gestion_ventas', 'descripcion' => 'Gestionar ventas']);
        $permisoClientes = Permiso::create(['nombre' => 'gestion_clientes', 'descripcion' => 'Gestionar clientes']);

        // Datos del nuevo rol a crear
        $nuevoRol = [
            'nombre' => 'supervisor',
            'descripcion' => 'Supervisor de ventas',
            'permisos' => [$permisoVentas->id, $permisoClientes->id]
        ];

        // Actuar como admin y crear el rol
        $response = $this->actingAs($this->admin)->post('/roles', $nuevoRol);

        // Verificar que:
        // 1. La respuesta redirecciona (éxito)
        // 2. El rol existe en la base de datos
        // 3. Los permisos están asignados correctamente
        $response->assertRedirect();
        $this->assertDatabaseHas('roles', [
            'nombre' => 'supervisor',
            'descripcion' => 'Supervisor de ventas'
        ]);
        
        // Verificar que los permisos se asignaron
        $rolCreado = Rol::where('nombre', 'supervisor')->first();
        $this->assertTrue($rolCreado->permisos->contains($permisoVentas));
        $this->assertTrue($rolCreado->permisos->contains($permisoClientes));
    }
}
