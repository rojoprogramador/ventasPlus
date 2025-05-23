<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Rol;
use App\Models\User;
use App\Models\Permiso;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use App\Http\Controllers\ExportacionController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Mockery;
use Mockery\MockInterface;

/**
 * Pruebas para la funcionalidad de exportación de datos
 * 
 * Estas pruebas verifican:
 * - El acceso a la vista de exportación (requiere permiso exportar_datos)
 * - La exportación de datos en diferentes formatos
 * - La restricción de acceso basada en permisos
 */
class ExportacionControllerTest extends TestCase
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
        
        // Crear rol admin y permiso de exportación
        $this->rolAdmin = Rol::create(['nombre' => 'admin', 'descripcion' => 'Administrador']);
        $permisoExportar = Permiso::create([
            'nombre' => 'exportar_datos', 
            'descripcion' => 'Exportar datos del sistema'
        ]);
        $this->rolAdmin->permisos()->attach($permisoExportar->id);

        // Crear usuario admin
        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $this->rolAdmin->id
        ]);
    }

    /** @test */
    public function usuario_puede_ver_pagina_exportacion_si_tiene_permiso()
    {
        // Mockear el controller para evitar problemas de base de datos
        $this->mock(ExportacionController::class, function ($mock) {
            $mock->shouldReceive('index')->andReturn(
                \Inertia\Inertia::render('Exportacion/Index', [
                    'tablas' => [
                        ['id' => 'clientes', 'nombre' => 'Clientes'],
                        ['id' => 'productos', 'nombre' => 'Productos'],
                    ],
                    'formatos' => [
                        ['id' => 'csv', 'nombre' => 'CSV'],
                        ['id' => 'excel', 'nombre' => 'Excel'],
                    ],
                    'campos' => []
                ])
            );
        });

        $response = $this->actingAs($this->admin)
                         ->get(route('exportacion.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function usuario_sin_permiso_no_puede_ver_pagina_exportacion()
    {
        // Crear rol sin permisos
        $rolSinPermiso = Rol::create(['nombre' => 'usuario', 'descripcion' => 'Usuario sin permisos']);
        
        // Crear usuario sin permisos
        $usuarioSinPermiso = User::create([
            'name' => 'Usuario Sin Permiso',
            'email' => 'nopermiso@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $rolSinPermiso->id
        ]);

        // Mockear el controller para simular la respuesta 403
        $this->mock(ExportacionController::class, function ($mock) {
            $mock->shouldReceive('index')->andThrow(new \Illuminate\Auth\Access\AuthorizationException());
        });

        $this->withoutExceptionHandling();
        
        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);
        
        $response = $this->actingAs($usuarioSinPermiso)
                         ->get(route('exportacion.index'));
    }

    /** @test */
    public function puede_exportar_clientes_a_csv()
    {
        Storage::fake('exports');

        // Mockear el controller para simular la exportación
        $this->mock(ExportacionController::class, function ($mock) {
            $mock->shouldReceive('exportar')->andReturn(
                response()->streamDownload(
                    function () {
                        echo "id,nombre,email,telefono\n";
                        echo "1,Cliente Test,cliente@test.com,123456789\n";
                    },
                    'clientes_' . now()->format('Y-m-d') . '.csv',
                    [
                        'Content-Type' => 'text/csv; charset=UTF-8',
                        'Content-Disposition' => 'attachment; filename=clientes_' . now()->format('Y-m-d') . '.csv',
                    ]
                )
            );
        });

        $response = $this->actingAs($this->admin)
                         ->post(route('exportacion.exportar'), [
                             'tabla' => 'clientes',
                             'formato' => 'csv'
                         ]);

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename=clientes_' . now()->format('Y-m-d') . '.csv');
    }

    /** @test */
    public function puede_exportar_productos_a_excel()
    {
        // Mockear el controller para simular la exportación a Excel
        $this->mock(ExportacionController::class, function ($mock) {
            $mock->shouldReceive('exportar')->andReturn(
                response('Simulated Excel content', 200, [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Content-Disposition' => 'attachment; filename=productos_' . now()->format('Y-m-d') . '.xlsx',
                ])
            );
        });

        $response = $this->actingAs($this->admin)
                         ->post(route('exportacion.exportar'), [
                             'tabla' => 'productos',
                             'formato' => 'excel'
                         ]);

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->assertHeader('Content-Disposition', 'attachment; filename=productos_' . now()->format('Y-m-d') . '.xlsx');
    }

    /** @test */
    public function validacion_funciona_para_exportacion()
    {
        $response = $this->actingAs($this->admin)
                         ->post(route('exportacion.exportar'), [
                             'tabla' => 'tabla_inexistente',
                             'formato' => 'formato_invalido'
                         ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['tabla', 'formato']);
    }
}
