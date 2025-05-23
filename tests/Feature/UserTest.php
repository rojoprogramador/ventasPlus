<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Rol;
use App\Models\User;
use App\Models\Permiso;
use Tests\Helpers\TestHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Pruebas para la gestión de usuarios
 * 
 * Estas pruebas verifican:
 * - La visualización de usuarios (requiere permiso gestion_usuarios)
 * - La creación de usuarios (requiere permiso gestion_usuarios)
 * - La restricción de acceso basada en permisos
 */
class UserTest extends TestCase
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
        
        // Usar el helper para configurar el entorno de prueba
        $entorno = TestHelper::prepararEntornoPruebas();
        $this->rolAdmin = $entorno['rolAdmin'];
        $this->admin = $entorno['admin'];
    }

    /**
     * Prueba que un administrador puede ver la lista de usuarios
     * 
     * @test
     * @return void
     */
    public function test_admin_puede_ver_lista_de_usuarios()
    {
        // Actuar como admin y hacer la petición
        $response = $this->actingAs($this->admin)->get('/users');

        // Verificar que:
        // 1. La respuesta es exitosa (código 200)
        // 2. Se carga el componente correcto
        // 3. Se pasan los datos necesarios (usuarios y roles)
        $response->assertStatus(200);
        $response->assertInertia(fn ($assert) => $assert
            ->component('Users/Index')
            ->has('users')
            ->has('roles')
        );
    }

    /**
     * Prueba que un administrador puede crear un nuevo usuario
     * 
     * @test
     * @return void
     */
    public function test_admin_puede_crear_usuario()
    {
        // Obtener rol vendedor para el nuevo usuario
        $rolVendedor = Rol::firstOrCreate(
            ['nombre' => 'vendedor'],
            ['descripcion' => 'Vendedor']
        );

        // Datos del nuevo usuario a crear
        $nuevoUsuario = [
            'name' => 'Nuevo Vendedor',
            'email' => 'vendedor@test.com',
            'password' => 'Password123!', // Cumple con requisitos de seguridad comunes
            'password_confirmation' => 'Password123!',
            'rol_id' => $rolVendedor->id
        ];

        // Actuar como admin y crear el usuario
        $response = $this->actingAs($this->admin)->post('/users', $nuevoUsuario);

        // Verificar que:
        // 1. La respuesta redirecciona (éxito)
        // 2. El usuario existe en la base de datos con los datos correctos
        // 3. El rol está asignado correctamente
        $response->assertStatus(302); // Asegurarse que hay redirección
        $this->assertDatabaseHas('users', [
            'name' => 'Nuevo Vendedor',
            'email' => 'vendedor@test.com',
            'rol_id' => $rolVendedor->id
        ]);

        // Verificar que el password se hashó correctamente
        $usuarioCreado = User::where('email', 'vendedor@test.com')->first();
        $this->assertTrue(Hash::check('Password123!', $usuarioCreado->password));
    }

    /**
     * Prueba que un usuario sin permiso no puede ver la lista de usuarios
     * 
     * @test
     * @return void
     */
    public function test_usuario_sin_permiso_no_puede_ver_usuarios()
    {
        // Crear rol sin permisos y usuario vendedor
        $rolVendedor = Rol::create(['nombre' => 'vendedor', 'descripcion' => 'Vendedor']);
        $vendedor = User::create([
            'name' => 'Vendedor Test',
            'email' => 'vendedor@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $rolVendedor->id
        ]);

        // Actuar como vendedor e intentar ver usuarios
        $response = $this->actingAs($vendedor)->get('/users');

        // Verificar que se niega el acceso (código 403 Forbidden)
        $response->assertStatus(403);
    }
}
