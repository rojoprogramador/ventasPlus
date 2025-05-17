<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarPermiso
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permiso
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $permiso): Response
    {
        $user = $request->user();

        // Permitir acceso automático a administradores
        if ($user && $user->rol && $user->rol->nombre === 'admin') {
            return $next($request);
        }

        // Para otros usuarios, verificar el permiso específico
        if (!$user || !$user->rol || !$user->rol->permisos->contains('nombre', $permiso)) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        return $next($request);
    }
}
