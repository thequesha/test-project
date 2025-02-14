<?php

namespace App\Repositories\Interfaces;

interface BaseRepositoryInterface
{
    public function all(array $relations = []);
    public function findById(int $id, array $relations = []);
    public function create(array $attributes);
    public function update($model, array $attributes);
    public function delete($model);
    public function list(array $filters = [], array $relations = []);
}
