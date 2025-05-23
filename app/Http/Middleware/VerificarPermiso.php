<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerificarPermiso
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permiso
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $permiso): Response
    {
        try {
            $user = $request->user();
            
            // Verificar si el usuario está autenticado
            if (!$user) {
                Log::warning('Acceso no autorizado: Usuario no autenticado intentando acceder a ruta protegida', [
                    'ruta' => $request->path(),
                    'permiso_requerido' => $permiso,
                    'ip' => $request->ip()
                ]);
                
                // Redireccionar al login
                return redirect()->route('login');
            }
            
            // Siempre cargar explícitamente la relación de rol y permisos para evitar problemas
            $user->load('rol.permisos');
            
            // Verificar que el usuario tiene un rol asignado
            if (!$user->rol) {
                Log::warning('Usuario sin rol intentando acceder a ruta protegida', [
                    'usuario_id' => $user->id,
                    'usuario_email' => $user->email,
                    'ruta' => $request->path(),
                    'permiso_requerido' => $permiso
                ]);
                
                return redirect()->route('dashboard')->with('error', 'No tienes un rol asignado. Contacta al administrador.');
            }
            
            // Permitir acceso automático a administradores
            if ($user->rol && $user->rol->nombre === 'admin') {
                return $next($request);
            }

            // Para otros usuarios, verificar el permiso específico
            if (!$user->rol || !$user->rol->permisos->contains('nombre', $permiso)) {
                Log::warning('Acceso denegado: Usuario sin los permisos necesarios', [
                    'usuario_id' => $user->id,
                    'usuario_email' => $user->email,
                    'rol' => $user->rol ? $user->rol->nombre : 'sin rol',
                    'permiso_requerido' => $permiso,
                    'permisos_usuario' => $user->rol ? $user->rol->permisos->pluck('nombre') : []
                ]);
                
                abort(403, 'No tienes permiso para realizar esta acción.');
            }

            return $next($request);
        } catch (\Exception $e) {
            Log::error('Error en middleware VerificarPermiso:', [
                'mensaje' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // En caso de error, redirigir al login para renovar sesión
            return redirect()->route('login')->with('error', 'Se ha producido un error de sesión. Por favor, inicie sesión nuevamente.');
        }
    }
}
