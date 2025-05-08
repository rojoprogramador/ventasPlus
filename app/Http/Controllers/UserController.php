<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Permiso;
use App\Services\UserService;
use App\Traits\ApiResponse;
use App\Traits\VerificaPermisos;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Exceptions\Custom\BusinessException;

class UserController extends Controller
{
    use ApiResponse, VerificaPermisos;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * UserController constructor.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->middleware(function ($request, $next) {
            if (!$this->tienePermiso('gestion_usuarios')) {
                abort(403, 'No tienes permiso para gestionar usuarios.');
            }
            return $next($request);
        });
    }

    /**
     * Mostrar listado de usuarios
     */
    public function index()
    {
        try {
            return Inertia::render('Users/Index', [
                'users' => $this->userService->getAllWithPermissions(),
                'roles' => Rol::all(),
                'permisos' => Permiso::all()
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Crear un nuevo usuario
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'regex:/[a-z]/',      // debe contener al menos una letra minúscula
                    'regex:/[A-Z]/',      // debe contener al menos una letra mayúscula
                    'regex:/[0-9]/',      // debe contener al menos un número
                    'regex:/[@$!%*#?&]/', // debe contener al menos un caracter especial
                ],
                'rol_id' => 'required|exists:roles,id',
                'estado' => 'required|boolean'
            ]);

            $user = $this->userService->create($data);
            
            if ($request->has('permisos')) {
                $this->userService->updatePermissions($user->id, $request->permisos);
            }

            return redirect()->back()->with('success', 'Usuario creado exitosamente');
        } catch (BusinessException $e) {
            return redirect()->back()->withErrors($e->getErrors());
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Actualizar un usuario
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $id,
                'password' => $request->has('password') ? [
                    'required',
                    'string',
                    'min:8',
                    'regex:/[a-z]/',      // debe contener al menos una letra minúscula
                    'regex:/[A-Z]/',      // debe contener al menos una letra mayúscula
                    'regex:/[0-9]/',      // debe contener al menos un número
                    'regex:/[@$!%*#?&]/', // debe contener al menos un caracter especial
                ] : 'nullable',
                'rol_id' => 'required|exists:roles,id',
                'estado' => 'required|boolean'
            ]);

            $user = $this->userService->update($id, $data);
            
            if ($request->has('permisos')) {
                $this->userService->updatePermissions($id, $request->permisos);
            }

            return redirect()->back()->with('success', 'Usuario actualizado exitosamente');
        } catch (BusinessException $e) {
            return redirect()->back()->withErrors($e->getErrors());
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Eliminar un usuario
     */
    public function destroy($id)
    {
        try {
            $this->userService->delete($id);
            return redirect()->back()->with('success', 'Usuario eliminado exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}

