<?php

namespace App\Services\Contracts;

use App\Models\Resource;

interface ResourceServiceInterface extends BaseServiceInterface
{
    /**
     * Check if a resource has active bookings
     */
    public function hasActiveBookings(Resource $resource): bool;

    /**
     * Check if a resource is available for a given time period
     */
    public function isAvailable(Resource $resource, string $startTime, string $endTime, ?int $excludeBookingId = null): bool;

    /**
     * Get all bookings for a resource
     */
    public function getBookings(Resource $resource, int $perPage = 10);
}
