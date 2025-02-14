<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResourceRequest;
use App\Http\Requests\UpdateResourceRequest;
use App\Http\Resources\ResourceResource;
use App\Models\Resource;
use App\Services\Contracts\ResourceServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @OA\Tag(
 *     name="Resources",
 *     description="API Endpoints for Resource Management"
 * )
 */
class ResourceController extends Controller
{
    protected $resourceService;

    public function __construct(ResourceServiceInterface $resourceService)
    {
        $this->resourceService = $resourceService;
    }

    /**
     * @OA\Get(
     *     path="/api/resources",
     *     summary="List all resources",
     *     tags={"Resources"},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filter resources by type",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="active_only",
     *         in="query",
     *         description="Filter only active resources",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of resources",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Resource")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $filters = [
                'type' => $request->type,
                'active_only' => $request->boolean('active_only'),
            ];

            $resources = $this->resourceService->list($filters);
            return ResourceResource::collection($resources);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching resources.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/resources",
     *     summary="Create a new resource",
     *     tags={"Resources"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreResourceRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Resource created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Resource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(StoreResourceRequest $request)
    {
        try {
            $resource = $this->resourceService->create($request->validated());
            return new ResourceResource($resource);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/resources/{id}",
     *     summary="Get a specific resource",
     *     tags={"Resources"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Resource details",
     *         @OA\JsonContent(ref="#/components/schemas/Resource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found"
     *     )
     * )
     */
    public function show(Resource $resource)
    {
        try {
            $resource = $this->resourceService->findById($resource->id);
            return new ResourceResource($resource);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Resource not found.'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/resources/{id}",
     *     summary="Update a resource",
     *     tags={"Resources"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateResourceRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Resource updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Resource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(UpdateResourceRequest $request, Resource $resource)
    {
        try {
            $resource = $this->resourceService->update($resource, $request->validated());
            return new ResourceResource($resource);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/resources/{id}",
     *     summary="Delete a resource",
     *     tags={"Resources"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Resource deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found"
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Resource cannot be deleted due to active bookings"
     *     )
     * )
     */
    public function destroy(Resource $resource)
    {
        try {
            $this->resourceService->delete($resource);
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
