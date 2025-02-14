<?php

namespace App\Repositories\Contracts;

use App\Models\Booking;

interface BookingRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get bookings for a specific resource
     */
    public function getResourceBookings(int $resourceId, array $relations = []);

    /**
     * Get bookings for a specific user
     */
    public function getUserBookings(int $userId, array $relations = []);

    /**
     * Get overlapping bookings for a resource
     */
    public function getOverlappingBookings(int $resourceId, string $startTime, string $endTime, ?int $excludeBookingId = null);

    /**
     * Cancel a booking
     */
    public function cancel(Booking $booking): bool;
}
