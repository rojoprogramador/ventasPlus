<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            $request->authenticate();
            
            $request->session()->regenerate();

            // Cargar los permisos del usuario en la sesión
            $user = $request->user();
            if ($user) {
                // Forzar la carga de la relación con el rol y sus permisos
                $user->load(['rol.permisos']);
                
                if ($user->rol) {
                    $permisos = $user->rol->permisos->pluck('nombre')->toArray();
                    
                    // Almacenar en la sesión
                    session([
                        'user_permissions' => $permisos,
                        'user_rol' => $user->rol->nombre,
                        'user_rol_nombre' => $user->rol->nombre
                    ]);
                    
                    // Registrar en el log
                    Log::info('Usuario autenticado con éxito:', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'rol' => $user->rol->nombre,
                        'permisos' => $permisos
                    ]);
                    
                    // También asegurarse de que el rol y los permisos estén disponibles en la respuesta
                    $user->rol_nombre = $user->rol->nombre;
                    $user->permisos = $permisos;
                } else {
                    Log::warning('El usuario no tiene un rol asignado: ' . $user->email);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error durante la autenticación:', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
