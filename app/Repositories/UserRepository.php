<?php

namespace App\Repositories;

use App\Models\User;
use App\Interfaces\IUserRepository;

class UserRepository extends BaseRepository implements IUserRepository
{
    /**
     * UserRepository constructor.
     *
     * @param User $model
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * @inheritDoc
     */
    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * @inheritDoc
     */
    public function getByRol(int $rolId)
    {
        return $this->model->where('rol_id', $rolId)->get();
    }

    /**
     * @inheritDoc
     */
    public function getAllWithPermissions()
    {
        return $this->model->with(['rol', 'permisos'])->get();
    }
}
