<?php

namespace App\Virtual\Models;

/**
 * @OA\Schema(
 *     schema="Resource",
 *     title="Resource",
 *     description="Resource model",
 *     @OA\Xml(name="Resource")
 * )
 */
class ResourceModel
{
    /**
     * @OA\Property(
     *     title="ID",
     *     description="ID of the resource",
     *     format="int64",
     *     example=1
     * )
     *
     * @var integer
     */
    private $id;

    /**
     * @OA\Property(
     *     title="Name",
     *     description="Name of the resource",
     *     example="Meeting Room A"
     * )
     *
     * @var string
     */
    private $name;

    /**
     * @OA\Property(
     *     title="Type",
     *     description="Type of the resource",
     *     example="room"
     * )
     *
     * @var string
     */
    private $type;

    /**
     * @OA\Property(
     *     title="Description",
     *     description="Description of the resource",
     *     example="A spacious meeting room with projector"
     * )
     *
     * @var string
     */
    private $description;

    /**
     * @OA\Property(
     *     title="Is Active",
     *     description="Whether the resource is active",
     *     example=true
     * )
     *
     * @var boolean
     */
    private $is_active;

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
}
