<?php

namespace App\Repositories\Contracts;

interface BaseRepositoryInterface
{
    /**
     * Get all records with optional filters
     */
    public function all(array $filters = [], array $relations = []);

    /**
     * Get paginated records
     */
    public function paginate(int $perPage = 10, array $filters = [], array $relations = []);

    /**
     * Find a record by ID
     */
    public function findById($id, array $relations = []);

    /**
     * Create a new record
     */
    public function create(array $data);

    /**
     * Update an existing record
     */
    public function update($model, array $data);

    /**
     * Delete a record
     */
    public function delete($model);

    /**
     * Get model query builder
     */
    public function query();
}
