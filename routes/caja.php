<?php

use App\Http\Controllers\CajaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas para la gestión de caja
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    // Grupo de rutas para la gestión de caja - Accesible para administradores o usuarios con permiso específico
    Route::group(['namespace' => 'App\Http\Controllers'], function () {
        Route::get('/caja', [CajaController::class, 'index'])->name('caja.index');
        Route::get('/caja/create', [CajaController::class, 'create'])->name('caja.create');
        Route::post('/caja', [CajaController::class, 'store'])->name('caja.store');
        Route::get('/caja/{id}/edit', [CajaController::class, 'edit'])->name('caja.edit');
        Route::put('/caja/{id}', [CajaController::class, 'update'])->name('caja.update');
        Route::get('/caja/{id}', [CajaController::class, 'show'])->name('caja.show');
        Route::post('/caja/movimiento', [CajaController::class, 'registrarMovimiento'])->name('caja.movimiento');
        
        // Ruta de prueba para diagnóstico
        Route::get('/caja/test', function() {
            return Inertia\Inertia::render('Caja/AperturaTest');
        })->name('caja.test');
    });
    
    // Rutas que requieren permisos de administrador
    Route::middleware(['permiso:administrar_sistema'])->group(function () {
        Route::post('/caja/{id}/reabrir', [CajaController::class, 'reabrir'])->name('caja.reabrir');
    });
});
