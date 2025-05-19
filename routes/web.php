<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VentaController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard', [
        'auth' => [
            'user' => auth()->user()->load('rol')
        ]
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas de roles y usuarios (requieren permisos específicos)
    Route::middleware(['auth'])->group(function () {
        // Roles
        Route::middleware(['permiso:gestion_roles'])->group(function () {
            Route::get('/roles', [RolController::class, 'index'])->name('roles.index');
            Route::post('/roles', [RolController::class, 'store'])->name('roles.store');
            Route::put('/roles/{rol}', [RolController::class, 'update'])->name('roles.update');
        });

        // Usuarios
        Route::middleware(['permiso:gestion_usuarios'])->group(function () {
            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::post('/users', [UserController::class, 'store'])->name('users.store');
            Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        });
    });

    // Rutas para el registro de ventas
    Route::get('/ventas/registro', [VentaController::class, 'index'])->name('ventas.registro');
    Route::get('/ventas/buscar-productos', [VentaController::class, 'buscarProductos'])->name('ventas.buscar-productos');
    Route::post('/ventas', [VentaController::class, 'store'])->name('ventas.store');
    Route::get('/ventas/comprobante/{id}', [VentaController::class, 'comprobante'])->name('ventas.comprobante');
    Route::post('/ventas/cancelar', [VentaController::class, 'cancelar'])->name('ventas.cancelar');
});

require __DIR__.'/auth.php';