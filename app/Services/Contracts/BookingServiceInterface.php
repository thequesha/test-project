<?php

namespace App\Services\Contracts;

use App\Models\Booking;

interface BookingServiceInterface extends BaseServiceInterface
{
    /**
     * Cancel a booking
     */
    public function cancel(Booking $booking): bool;

    /**
     * Check if a booking can be cancelled
     */
    public function canBeCancelled(Booking $booking): bool;

    /**
     * Get all bookings for a specific resource
     */
    public function getResourceBookings(int $resourceId, int $perPage = 10);

    /**
     * Get all bookings for a specific user
     */
    public function getUserBookings(int $userId, int $perPage = 10);
}
