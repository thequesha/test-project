<?php

namespace App\Repositories\Interfaces;

use App\Models\Booking;

interface BookingRepositoryInterface extends BaseRepositoryInterface
{
    public function getResourceBookings(int $resourceId, array $relations = []);
    public function getUserBookings(int $userId, array $relations = []);
    public function getOverlappingBookings(int $resourceId, string $startTime, string $endTime, ?int $excludeBookingId = null);
    public function cancel(Booking $booking): bool;
}
