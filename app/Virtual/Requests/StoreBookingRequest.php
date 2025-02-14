<?php

namespace App\Virtual\Requests;

/**
 * @OA\Schema(
 *     title="Store Booking Request",
 *     description="Store Booking request body data",
 *     type="object",
 *     required={"resource_id", "user_id", "start_time", "end_time"}
 * )
 */
class StoreBookingRequest
{
    /**
     * @OA\Property(
     *     title="resource_id",
     *     description="ID of the resource to book",
     *     format="int64",
     *     example=1
     * )
     *
     * @var integer
     */
    public $resource_id;

    /**
     * @OA\Property(
     *     title="user_id",
     *     description="ID of the user making the booking",
     *     format="int64",
     *     example=1
     * )
     *
     * @var integer
     */
    public $user_id;

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
