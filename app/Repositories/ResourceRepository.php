<?php

namespace App\Repositories;

use App\Models\Resource;
use App\Repositories\Interfaces\ResourceRepositoryInterface;
use Carbon\Carbon;

class ResourceRepository extends BaseRepository implements ResourceRepositoryInterface
{
    protected $defaultOrderBy = ['name' => 'asc'];

    public function __construct(Resource $model)
    {
        parent::__construct($model);
    }

    public function findByName(string $name)
    {
        return $this->model->where('name', $name)->first();
    }

    public function getActiveResources()
    {
        return $this->model->where('is_active', true)->get();
    }

    public function getResourcesByType(string $type)
    {
        return $this->model->where('type', $type)->get();
    }

    public function hasActiveBookings($resource): bool
    {
        return $resource->bookings()
            ->where('end_time', '>', Carbon::now())
            ->exists();
    }

    public function isAvailable($resource, string $startTime, string $endTime, ?int $excludeBookingId = null): bool
    {
        $query = $resource->bookings()
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '>=', $startTime)
                      ->where('start_time', '<', $endTime);
                })->orWhere(function ($q) use ($startTime, $endTime) {
                    $q->where('end_time', '>', $startTime)
                      ->where('end_time', '<=', $endTime);
                })->orWhere(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<=', $startTime)
                      ->where('end_time', '>=', $endTime);
                });
            });

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return !$query->exists();
    }

    public function list(array $filters = [], array $relations = [])
    {
        $query = $this->model->newQuery();
        $this->applyFilters($query, $filters);
        $this->applyRelations($query, $relations);
        $this->applyOrdering($query);
        return $query->paginate(10);
    }

    protected function applyFilters($query, array $filters)
    {
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['active_only']) && $filters['active_only'] === true) {
            $query->where('is_active', true);
        }

        // Remove our custom filters so parent doesn't try to apply them again
        $filters = array_diff_key($filters, array_flip(['type', 'active_only']));

        return parent::applyFilters($query, $filters);
    }

    protected function applyOrdering($query)
    {
        foreach ($this->defaultOrderBy as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query;
    }
}
