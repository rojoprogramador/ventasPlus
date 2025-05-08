<?php

namespace App\Services;

use App\Interfaces\IBaseRepository;
use App\Interfaces\IBaseService;
use Illuminate\Support\Facades\DB;
use Exception;

abstract class BaseService implements IBaseService
{
    /**
     * @var IBaseRepository
     */
    protected $repository;

    /**
     * BaseService constructor.
     *
     * @param IBaseRepository $repository
     */
    public function __construct(IBaseRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     */
    public function getAll()
    {
        try {
            return $this->repository->getAll();
        } catch (Exception $e) {
            throw new Exception("Error al obtener los registros: " . $e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function findById($id)
    {
        try {
            return $this->repository->findById($id);
        } catch (Exception $e) {
            throw new Exception("Error al obtener el registro: " . $e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function create(array $data)
    {
        try {
            DB::beginTransaction();
            $result = $this->repository->create($data);
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Error al crear el registro: " . $e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function update($id, array $data)
    {
        try {
            DB::beginTransaction();
            $result = $this->repository->update($id, $data);
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Error al actualizar el registro: " . $e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $result = $this->repository->delete($id);
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Error al eliminar el registro: " . $e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function getPaginated($perPage = 10)
    {
        try {
            return $this->repository->getPaginated($perPage);
        } catch (Exception $e) {
            throw new Exception("Error al obtener los registros paginados: " . $e->getMessage());
        }
    }
}
