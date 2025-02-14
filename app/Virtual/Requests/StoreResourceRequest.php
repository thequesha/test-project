<?php

namespace App\Virtual\Requests;

/**
 * @OA\Schema(
 *     title="Store Resource Request",
 *     description="Store Resource request body data",
 *     type="object",
 *     required={"name", "type"}
 * )
 */
class StoreResourceRequest
{
    /**
     * @OA\Property(
     *     title="name",
     *     description="Name of the resource",
     *     example="Meeting Room A"
     * )
     *
     * @var string
     */
    public $name;

    /**
     * @OA\Property(
     *     title="type",
     *     description="Type of the resource",
     *     example="room"
     * )
     *
     * @var string
     */
    public $type;

    /**
     * @OA\Property(
     *     title="description",
     *     description="Description of the resource",
     *     example="A spacious meeting room with projector"
     * )
     *
     * @var string
     */
    public $description;

    /**
     * @OA\Property(
     *     title="is_active",
     *     description="Whether the resource is active",
     *     example=true
     * )
     *
     * @var boolean
     */
    public $is_active;
}
