<?php

use App\Providers\RouteServiceProvider;
use App\Models\Rol;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $rol = Rol::create(['nombre' => 'vendedor', 'descripcion' => 'Vendedor']);
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'rol_id' => $rol->id,
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(RouteServiceProvider::HOME);
});
