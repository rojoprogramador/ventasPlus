<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Log;
use App\Models\Permiso;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RolController extends Controller
{
    public function index()
    {
        $roles = Rol::with('permisos')->get();
        $permisos = Permiso::all();
        
        return Inertia::render('Roles/Index', [
            'roles' => $roles,
            'permisos' => $permisos
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:roles,nombre',
            'descripcion' => 'required|string|max:255',
            'permisos' => 'required|array'
        ]);

        $rol = Rol::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion
        ]);

        $rol->permisos()->sync($request->permisos);

        // Registrar la acción
        Log::create([
            'usuario_id' => auth()->id(),
            'accion' => 'crear_rol',
            'descripcion' => 'Creó el rol ' . $request->nombre,
            'modelo' => 'Rol',
            'modelo_id' => $rol->id
        ]);

        return redirect()->back()->with('success', 'Rol creado exitosamente');
    }

    public function update(Request $request, Rol $rol)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:roles,nombre,' . $rol->id,
            'descripcion' => 'required|string|max:255',
            'permisos' => 'required|array'
        ]);

        $rol->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion
        ]);

        $rol->permisos()->sync($request->permisos);

        // Registrar la acción
        Log::create([
            'usuario_id' => auth()->id(),
            'accion' => 'actualizar_rol',
            'descripcion' => 'Actualizó el rol ' . $request->nombre,
            'modelo' => 'Rol',
            'modelo_id' => $rol->id
        ]);

        return redirect()->back()->with('success', 'Rol actualizado exitosamente');
    }
}
