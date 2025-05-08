<?php

namespace App\Services;

use App\Interfaces\IUserRepository;
use App\Exceptions\Custom\BusinessException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserService extends BaseService
{
    /**
     * @var IUserRepository
     */
    protected $repository;

    /**
     * UserService constructor.
     *
     * @param IUserRepository $repository
     */
    public function __construct(IUserRepository $repository)
    {
        parent::__construct($repository);
        $this->repository = $repository;
    }

    /**
     * Crear un nuevo usuario
     *
     * @param array $data
     * @return mixed
     * @throws BusinessException
     */
    public function create(array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
            'rol_id' => 'required|exists:roles,id'
        ]);

        if ($validator->fails()) {
            throw new BusinessException('Error de validación', $validator->errors()->toArray());
        }

        $data['password'] = Hash::make($data['password']);
        
        return parent::create($data);
    }

    /**
     * Actualizar un usuario
     *
     * @param int $id
     * @param array $data
     * @return mixed
     * @throws BusinessException
     */
    public function update($id, array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
            'rol_id' => 'sometimes|required|exists:roles,id'
        ]);

        if ($validator->fails()) {
            throw new BusinessException('Error de validación', $validator->errors()->toArray());
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return parent::update($id, $data);
    }

    /**
     * Obtener usuarios con sus permisos
     *
     * @return mixed
     */
    public function getAllWithPermissions()
    {
        return $this->repository->getAllWithPermissions();
    }

    /**
     * Actualizar permisos de un usuario
     *
     * @param int $userId
     * @param array $permisos
     * @return mixed
     */
    public function updatePermissions(int $userId, array $permisos)
    {
        $user = $this->findById($userId);
        if (!$user) {
            throw new BusinessException('Usuario no encontrado');
        }

        $permisosFormateados = collect($permisos)->mapWithKeys(function ($permiso) {
            return [$permiso['id'] => ['habilitado' => $permiso['habilitado']]];
        })->toArray();

        $user->permisos()->sync($permisosFormateados);

        return $user->load('permisos');
    }
}
