<?php

namespace App\Interfaces;

interface IUserRepository extends IBaseRepository
{
    /**
     * Buscar usuario por email
     *
     * @param string $email
     * @return mixed
     */
    public function findByEmail(string $email);

    /**
     * Obtener usuarios por rol
     *
     * @param int $rolId
     * @return mixed
     */
    public function getByRol(int $rolId);

    /**
     * Obtener usuarios con sus permisos
     *
     * @return mixed
     */
    public function getAllWithPermissions();
}
