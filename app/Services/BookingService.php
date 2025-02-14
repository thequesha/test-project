<?php

namespace App\Services;

use App\Events\BookingCancelled;
use App\Events\BookingCreated;
use App\Models\Booking;
use App\Repositories\Interfaces\BookingRepositoryInterface;
use App\Repositories\Interfaces\ResourceRepositoryInterface;
use App\Services\Contracts\BookingServiceInterface;
use Illuminate\Support\Facades\DB;

class BookingService implements BookingServiceInterface
{
    protected $bookingRepository;
    protected $resourceRepository;

    public function __construct(
        BookingRepositoryInterface $bookingRepository,
        ResourceRepositoryInterface $resourceRepository
    ) {
        $this->bookingRepository = $bookingRepository;
        $this->resourceRepository = $resourceRepository;
    }

    public function list(array $filters = [], int $perPage = 10)
    {
        return $this->bookingRepository->list($filters);
    }

    public function findById($id, array $with = [])
    {
        return $this->bookingRepository->findById($id, $with);
    }

    public function create(array $data)
    {
        $resource = $this->resourceRepository->findById($data['resource_id']);
        
        if (!$resource->is_active) {
            throw new \Exception('Cannot book an inactive resource.');
        }

        $overlappingBookings = $this->bookingRepository->getOverlappingBookings(
            $data['resource_id'],
            $data['start_time'],
            $data['end_time']
        );

        if ($overlappingBookings->isNotEmpty()) {
            throw new \Exception('The resource is not available during the selected time period.');
        }

        return DB::transaction(function () use ($data) {
            $booking = $this->bookingRepository->create(array_merge($data, ['status' => 'confirmed']));
            event(new BookingCreated($booking));
            return $booking;
        });
    }

    public function update($booking, array $data)
    {
        if ($booking->status === 'cancelled') {
            throw new \Exception('Cannot update a cancelled booking.');
        }

        if (isset($data['start_time']) || isset($data['end_time'])) {
            $overlappingBookings = $this->bookingRepository->getOverlappingBookings(
                $booking->resource_id,
                $data['start_time'] ?? $booking->start_time,
                $data['end_time'] ?? $booking->end_time,
                $booking->id
            );

            if ($overlappingBookings->isNotEmpty()) {
                throw new \Exception('The resource is not available during the selected time period.');
            }
        }

        return DB::transaction(function () use ($booking, $data) {
            return $this->bookingRepository->update($booking, $data);
        });
    }

    public function cancel(Booking $booking): bool
    {
        if ($booking->status === 'cancelled') {
            throw new \Exception('Booking is already cancelled.');
        }

        return DB::transaction(function () use ($booking) {
            $cancelled = $this->bookingRepository->cancel($booking);
            if ($cancelled) {
                event(new BookingCancelled($booking));
            }
            return $cancelled;
        });
    }

    public function delete($booking)
    {
        return DB::transaction(function () use ($booking) {
            return $this->bookingRepository->delete($booking);
        });
    }

    public function getResourceBookings(int $resourceId, int $perPage = 10)
    {
        return $this->bookingRepository->getResourceBookings($resourceId, ['user']);
    }

    public function getUserBookings(int $userId, int $perPage = 10)
    {
        return $this->bookingRepository->getUserBookings($userId, ['resource']);
    }

    public function canBeCancelled(Booking $booking): bool
    {
        if ($booking->status === 'cancelled') {
            return false;
        }

        return now()->lt($booking->start_time);
    }
}
