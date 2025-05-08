<?php

namespace App\Repositories;

use App\Interfaces\IBaseRepository;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements IBaseRepository
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @inheritDoc
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * @inheritDoc
     */
    public function findById($id)
    {
        return $this->model->find($id);
    }

    /**
     * @inheritDoc
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * @inheritDoc
     */
    public function update($id, array $data)
    {
        $record = $this->findById($id);
        if ($record) {
            $record->update($data);
            return $record;
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    /**
     * @inheritDoc
     */
    public function getPaginated($perPage = 10)
    {
        return $this->model->paginate($perPage);
    }
}
