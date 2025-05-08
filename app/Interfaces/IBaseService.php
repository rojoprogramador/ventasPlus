<?php

namespace App\Interfaces;

interface IBaseService
{
    /**
     * Obtener todos los registros
     */
    public function getAll();

    /**
     * Obtener un registro por su ID
     */
    public function findById($id);

    /**
     * Crear un nuevo registro
     */
    public function create(array $data);

    /**
     * Actualizar un registro
     */
    public function update($id, array $data);

    /**
     * Eliminar un registro
     */
    public function delete($id);

    /**
     * Obtener registros con paginación
     */
    public function getPaginated($perPage = 10);
}
