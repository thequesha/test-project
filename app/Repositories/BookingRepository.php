<?php

namespace App\Repositories;

use App\Models\Booking;
use App\Repositories\Interfaces\BookingRepositoryInterface;
use Carbon\Carbon;

class BookingRepository extends BaseRepository implements BookingRepositoryInterface
{
    protected $defaultRelations = ['resource', 'user'];
    protected $defaultOrderBy = ['start_time' => 'desc'];

    public function __construct(Booking $model)
    {
        parent::__construct($model);
    }

    public function getResourceBookings(int $resourceId, array $relations = [])
    {
        $query = $this->model->newQuery();
        $this->applyRelations($query, array_merge($this->defaultRelations, $relations));
        $this->applyOrdering($query);
        return $query->where('resource_id', $resourceId)->get();
    }

    public function getUserBookings(int $userId, array $relations = [])
    {
        $query = $this->model->newQuery();
        $this->applyRelations($query, array_merge($this->defaultRelations, $relations));
        $this->applyOrdering($query);
        return $query->where('user_id', $userId)->get();
    }

    public function getOverlappingBookings(int $resourceId, string $startTime, string $endTime, ?int $excludeBookingId = null)
    {
        $startTime = $startTime instanceof Carbon ? $startTime : Carbon::parse($startTime);
        $endTime = $endTime instanceof Carbon ? $endTime : Carbon::parse($endTime);

        $query = $this->model->newQuery()
            ->where('resource_id', $resourceId)
            ->where('status', 'confirmed')
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<=', $startTime)
                        ->where('end_time', '>', $startTime);
                })->orWhere(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)
                        ->where('end_time', '>=', $endTime);
                })->orWhere(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '>=', $startTime)
                        ->where('end_time', '<=', $endTime);
                });
            });

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return $query->get();
    }

    public function cancel(Booking $booking): bool
    {
        $result = $this->update($booking, ['status' => 'cancelled']);
        return $result !== null;
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
        if (isset($filters['resource_id'])) {
            $query->where('resource_id', $filters['resource_id']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return parent::applyFilters($query, $filters);
    }

    protected function applyRelations($query, array $relations = [])
    {
        $relations = array_merge($this->defaultRelations, $relations);
        return parent::applyRelations($query, $relations);
    }

    protected function applyOrdering($query)
    {
        foreach ($this->defaultOrderBy as $column => $direction) {
            $query->orderBy($column, $direction);
        }
        return $query;
    }
}
