<?php

use App\Models\User;
use App\Models\Rol;
use App\Providers\RouteServiceProvider;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    // Crear un rol antes de crear el usuario
    $rol = Rol::firstOrCreate(
        ['nombre' => 'admin'],
        ['descripcion' => 'Administrador']
    );
    
    $user = User::factory()->create([
        'password' => bcrypt('password'),
        'rol_id' => $rol->id
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(RouteServiceProvider::HOME);
});

test('users can not authenticate with invalid password', function () {
    // Crear un rol antes de crear el usuario
    $rol = Rol::firstOrCreate(
        ['nombre' => 'admin'],
        ['descripcion' => 'Administrador']
    );
    
    $user = User::factory()->create([
        'rol_id' => $rol->id
    ]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});
