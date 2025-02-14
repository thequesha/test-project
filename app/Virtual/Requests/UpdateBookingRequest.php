<?php

namespace App\Virtual\Requests;

/**
 * @OA\Schema(
 *     schema="UpdateBookingRequest",
 *     title="Update Booking Request",
 *     description="Update Booking request body data",
 *     type="object"
 * )
 */
class UpdateBookingRequest
{
    /**
     * @OA\Property(
     *     title="start_time",
     *     description="Start time of the booking",
     *     example="2025-01-01 10:00:00",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var string
     */
    public $start_time;

    /**
     * @OA\Property(
     *     title="end_time",
     *     description="End time of the booking",
     *     example="2025-01-01 11:00:00",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var string
     */
    public $end_time;
}
