<?php

namespace App\Services;

use App\Repositories\Interfaces\ResourceRepositoryInterface;
use App\Services\Contracts\ResourceServiceInterface;
use App\Models\Resource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResourceService implements ResourceServiceInterface
{
    protected $resourceRepository;

    public function __construct(ResourceRepositoryInterface $resourceRepository)
    {
        $this->resourceRepository = $resourceRepository;
    }

    public function list(array $filters = [], int $perPage = 10)
    {
        try {
            return $this->resourceRepository->list($filters);
        } catch (\Exception $e) {
            Log::error('Error fetching resources: ' . $e->getMessage());
            throw $e;
        }
    }

    public function findById($id, array $with = [])
    {
        try {
            return $this->resourceRepository->findById($id, $with);
        } catch (\Exception $e) {
            Log::error('Error finding resource: ' . $e->getMessage());
            throw $e;
        }
    }

    public function create(array $data)
    {
        try {
            $existingResource = $this->resourceRepository->findByName($data['name']);
            if ($existingResource) {
                throw new \Exception('A resource with this name already exists.');
            }

            DB::beginTransaction();
            $resource = $this->resourceRepository->create($data);
            DB::commit();

            return $resource;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating resource: ' . $e->getMessage());
            throw $e;
        }
    }

    public function update($resource, array $data)
    {
        try {
            if (isset($data['name']) && $data['name'] !== $resource->name) {
                $existingResource = $this->resourceRepository->findByName($data['name']);
                if ($existingResource) {
                    throw new \Exception('A resource with this name already exists.');
                }
            }

            DB::beginTransaction();
            $resource = $this->resourceRepository->update($resource, $data);
            DB::commit();

            return $resource;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating resource: ' . $e->getMessage());
            throw $e;
        }
    }

    public function delete($resource)
    {
        try {
            if ($this->resourceRepository->hasActiveBookings($resource)) {
                throw new \Exception('Cannot delete a resource with active bookings.');
            }

            DB::beginTransaction();
            $this->resourceRepository->delete($resource);
            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting resource: ' . $e->getMessage());
            throw $e;
        }
    }

    public function hasActiveBookings($resource): bool
    {
        return $this->resourceRepository->hasActiveBookings($resource);
    }

    public function isAvailable($resource, string $startTime, string $endTime, ?int $excludeBookingId = null): bool
    {
        // Ensure we have a Resource model instance
        if (!$resource instanceof Resource) {
            $resource = $this->resourceRepository->findById($resource);
        }
        
        return $this->resourceRepository->isAvailable($resource, $startTime, $endTime, $excludeBookingId);
    }

    public function getBookings($resource, int $perPage = 10)
    {
        try {
            $resource = $this->resourceRepository->findById($resource->id, ['bookings']);
            return $resource->bookings()
                ->orderBy('start_time', 'desc')
                ->paginate($perPage);
        } catch (\Exception $e) {
            Log::error('Error fetching resource bookings: ' . $e->getMessage());
            throw $e;
        }
    }
}
