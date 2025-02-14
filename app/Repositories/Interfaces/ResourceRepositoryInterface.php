<?php

namespace App\Repositories\Interfaces;

interface ResourceRepositoryInterface extends BaseRepositoryInterface
{
    public function findByName(string $name);
    public function getActiveResources();
    public function getResourcesByType(string $type);
    public function hasActiveBookings($resource): bool;
    public function isAvailable($resource, string $startTime, string $endTime, ?int $excludeBookingId = null): bool;
}
