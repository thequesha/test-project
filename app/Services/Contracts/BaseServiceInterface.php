<?php

namespace App\Services\Contracts;

interface BaseServiceInterface
{
    /**
     * Get paginated list of items
     */
    public function list(array $filters = [], int $perPage = 10);

    /**
     * Create a new item
     */
    public function create(array $data);

    /**
     * Update an existing item
     */
    public function update($model, array $data);

    /**
     * Delete an item
     */
    public function delete($model);

    /**
     * Find an item by ID
     */
    public function findById($id);
}
