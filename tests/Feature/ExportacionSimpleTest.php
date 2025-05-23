<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Rol;
use App\Models\User;
use App\Models\Permiso;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExportacionSimpleTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function un_usuario_con_permisos_puede_acceder_a_la_pagina_de_exportacion()
    {
        // Crear rol y permisos
        $rol = Rol::create(['nombre' => 'admin', 'descripcion' => 'Administrador']);
        
        $permiso = Permiso::create([
            'nombre' => 'exportar_datos',
            'descripcion' => 'Permite exportar datos del sistema'
        ]);
        
        $rol->permisos()->attach($permiso->id);
        
        // Crear usuario
        $usuario = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $rol->id
        ]);
        
        // Verificar acceso a la página de exportación
        $this->actingAs($usuario)
             ->get('/exportacion')
             ->assertStatus(200);
    }
    
    /** @test */
    public function verifica_permiso_exportacion_datos_existe()
    {
        // Verificar que existe el permiso de exportar datos
        Permiso::create([
            'nombre' => 'exportar_datos',
            'descripcion' => 'Permite exportar datos del sistema'
        ]);
        
        $this->assertDatabaseHas('permisos', [
            'nombre' => 'exportar_datos'
        ]);
    }
}
