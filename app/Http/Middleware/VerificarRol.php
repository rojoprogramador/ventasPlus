<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerificarRol
{
    public function handle(Request $request, Closure $next, string $rol)
    {
        $user = $request->user();
        
        if (!$user || !$user->rol || $user->rol->nombre !== $rol) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }

        return $next($request);
    }
}
