<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        
        $userData = null;
        if ($user) {
            try {
                // Cargar el usuario con el rol y los permisos
                $user->load(['rol.permisos']);
                
                // Obtener los permisos del rol
                $permisos = $user->rol ? $user->rol->permisos->pluck('nombre')->toArray() : [];
                
                // Crear el array de usuario con los datos necesarios
                $userData = array_merge($user->toArray(), [
                    'rol' => $user->rol ? [
                        'id' => $user->rol->id,
                        'nombre' => $user->rol->nombre,
                        'descripcion' => $user->rol->descripcion,
                        'permisos' => $user->rol->permisos->map(function($permiso) {
                            return [
                                'id' => $permiso->id,
                                'nombre' => $permiso->nombre,
                                'descripcion' => $permiso->descripcion
                            ];
                        })
                    ] : null,
                    'permisos' => $permisos
                ]);
                
                // Registrar en el log para depuración
                Log::info('Datos del usuario compartidos con Inertia:', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'rol' => $user->rol ? $user->rol->nombre : 'Sin rol',
                    'permisos' => $permisos
                ]);
            } catch (\Exception $e) {
                Log::error('Error al cargar datos de usuario en Inertia:', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $userData,
            ],
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'error' => fn () => $request->session()->get('error'),
                'status' => fn () => $request->session()->get('status'),
            ],
            'errors' => fn () => $request->session()->get('errors') 
                ? $request->session()->get('errors')->getBag('default')->getMessages() 
                : (object) [],
            'ziggy' => function () use ($request) {
                return array_merge((new Ziggy)->toArray(), [
                    'location' => $request->url(),
                ]);
            },
        ]);
    }
}
