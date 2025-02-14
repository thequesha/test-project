<?php

namespace App\Repositories\Contracts;

use App\Models\Resource;

interface ResourceRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get active resources
     */
    public function getActive(array $relations = []);

    /**
     * Get resources by type
     */
    public function getByType(string $type, array $relations = []);

    /**
     * Check if resource has active bookings
     */
    public function hasActiveBookings(Resource $resource): bool;

    /**
     * Check if resource is available for given time period
     */
    public function isAvailable(Resource $resource, string $startTime, string $endTime, ?int $excludeBookingId = null): bool;
}
