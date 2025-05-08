<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Rol;
use App\Models\Log;
use App\Models\Permiso;
use App\Traits\VerificaPermisos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    use VerificaPermisos;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!$this->tienePermiso('gestion_usuarios')) {
                abort(403, 'No tienes permiso para gestionar usuarios.');
            }
            return $next($request);
        });
    }
    public function index()
    {
        return Inertia::render('Users/Index', [
            'users' => User::with(['rol', 'permisos'])->get(),
            'roles' => Rol::all(),
            'permisos' => Permiso::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'rol_id' => 'required|exists:roles,id'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol_id' => $request->rol_id,
            'estado' => true
        ]);

        Log::create([
            'usuario_id' => auth()->id(),
            'accion' => 'crear_usuario',
            'descripcion' => 'Creó el usuario ' . $request->name,
            'modelo' => 'User',
            'modelo_id' => $user->id
        ]);

        return redirect()->back()->with('success', 'Usuario creado exitosamente');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'rol_id' => 'required|exists:roles,id',
            'estado' => 'required|boolean',
            'permisos' => 'array',
            'permisos.*.id' => 'exists:permisos,id',
            'permisos.*.habilitado' => 'boolean'
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'rol_id' => $request->rol_id,
            'estado' => $request->estado
        ]);

        if ($request->password) {
            $request->validate([
                'password' => [
                    'string',
                    'min:8',
                    'regex:/[a-z]/',      // debe contener al menos una letra minúscula
                    'regex:/[A-Z]/',      // debe contener al menos una letra mayúscula
                    'regex:/[0-9]/',      // debe contener al menos un número
                    'regex:/[@$!%*#?&]/', // debe contener al menos un caracter especial
                ],
            ]);
            $user->update(['password' => Hash::make($request->password)]);
        }

        // Actualizar permisos individuales
        if ($request->has('permisos')) {
            $permisosSync = [];
            foreach ($request->permisos as $permiso) {
                $permisosSync[$permiso['id']] = ['habilitado' => $permiso['habilitado']];
            }
            $user->permisos()->sync($permisosSync);
        }

        Log::create([
            'usuario_id' => auth()->id(),
            'accion' => 'actualizar_usuario',
            'descripcion' => 'Actualizó el usuario ' . $request->name . ' y sus permisos',
            'modelo' => 'User',
            'modelo_id' => $user->id,
            'detalles' => json_encode([
                'permisos_modificados' => $request->has('permisos') ? $request->permisos : [],
                'cambios_usuario' => $user->getChanges()
            ])
        ]);

        return redirect()->back()->with('success', 'Usuario y permisos actualizados exitosamente');
    }
}
