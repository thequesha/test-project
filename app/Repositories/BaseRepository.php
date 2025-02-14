<?php

namespace App\Repositories;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(array $relations = [])
    {
        $query = $this->model->newQuery();
        $this->applyRelations($query, $relations);
        return $query->get();
    }

    public function findById(int $id, array $relations = [])
    {
        $query = $this->model->newQuery();
        $this->applyRelations($query, $relations);
        return $query->findOrFail($id);
    }

    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    public function update($model, array $attributes)
    {
        $model->update($attributes);
        return $model->fresh();
    }

    public function delete($model)
    {
        return $model->delete();
    }

    public function list(array $filters = [], array $relations = [])
    {
        $query = $this->model->newQuery();
        $this->applyFilters($query, $filters);
        $this->applyRelations($query, $relations);
        $this->applyOrdering($query);
        return $query->get();
    }

    protected function applyFilters($query, array $filters)
    {
        return $query;
    }

    protected function applyRelations($query, array $relations = [])
    {
        if (!empty($relations)) {
            $query->with($relations);
        }
        return $query;
    }

    protected function applyOrdering($query)
    {
        return $query;
    }
}
