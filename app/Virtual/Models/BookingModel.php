<?php

namespace App\Virtual\Models;

/**
 * @OA\Schema(
 *     schema="Booking",
 *     title="Booking",
 *     description="Booking model",
 *     @OA\Xml(name="Booking")
 * )
 */
class BookingModel
{
    /**
     * @OA\Property(
     *     title="ID",
     *     description="ID of the booking",
     *     format="int64",
     *     example=1
     * )
     *
     * @var integer
     */
    private $id;

    /**
     * @OA\Property(
     *     title="Resource ID",
     *     description="ID of the resource being booked",
     *     format="int64",
     *     example=1
     * )
     *
     * @var integer
     */
    private $resource_id;

    /**
     * @OA\Property(
     *     title="User ID",
     *     description="ID of the user making the booking",
     *     format="int64",
     *     example=1
     * )
     *
     * @var integer
     */
    private $user_id;

    /**
     * @OA\Property(
     *     title="Start Time",
     *     description="Start time of the booking",
     *     example="2025-01-01 10:00:00",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    private $start_time;

    /**
     * @OA\Property(
     *     title="End Time",
     *     description="End time of the booking",
     *     example="2025-01-01 11:00:00",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    private $end_time;

    /**
     * @OA\Property(
     *     title="Status",
     *     description="Status of the booking",
     *     enum={"confirmed", "cancelled"},
     *     example="confirmed"
     * )
     *
     * @var string
     */
    private $status;

    /**
     * @OA\Property(
     *     title="Created at",
     *     description="Created at",
     *     example="2025-01-01T00:00:00.000000Z",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    private $created_at;

    /**
     * @OA\Property(
     *     title="Updated at",
     *     description="Updated at",
     *     example="2025-01-01T00:00:00.000000Z",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    private $updated_at;

    /**
     * @OA\Property(
     *     title="Resource",
     *     description="The resource associated with this booking",
     *     ref="#/components/schemas/Resource"
     * )
     *
     * @var \App\Virtual\Models\ResourceModel
     */
    private $resource;

    /**
     * @OA\Property(
     *     title="User",
     *     description="The user who made this booking",
     *     ref="#/components/schemas/User"
     * )
     *
     * @var \App\Virtual\Models\UserModel
     */
    private $user;
}
