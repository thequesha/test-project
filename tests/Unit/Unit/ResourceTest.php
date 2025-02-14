<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Resource $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->resource = Resource::factory()->create();
    }

    public function test_resource_has_bookings_relationship(): void
    {
        $booking = Booking::factory()->create([
            'resource_id' => $this->resource->id,
            'user_id' => $this->user->id
        ]);

        $this->assertTrue($this->resource->bookings->contains($booking));
        $this->assertInstanceOf(Booking::class, $this->resource->bookings->first());
    }

    public function test_resource_is_available_when_no_bookings(): void
    {
        $startTime = now()->addDay();
        $endTime = now()->addDay()->addHours(2);

        $this->assertTrue($this->resource->isAvailable($startTime, $endTime));
    }

    public function test_resource_is_not_available_with_overlapping_booking(): void
    {
        $startTime = now()->addDay();
        $endTime = now()->addDay()->addHours(2);

        // Create an overlapping booking
        Booking::factory()->create([
            'resource_id' => $this->resource->id,
            'user_id' => $this->user->id,
            'start_time' => $startTime->copy()->addHour(),
            'end_time' => $endTime->copy()->addHour(),
            'status' => 'confirmed'
        ]);

        $this->assertFalse($this->resource->isAvailable($startTime, $endTime));
    }

    public function test_resource_is_available_with_cancelled_booking(): void
    {
        $startTime = now()->addDay();
        $endTime = now()->addDay()->addHours(2);

        // Create a cancelled booking
        Booking::factory()->create([
            'resource_id' => $this->resource->id,
            'user_id' => $this->user->id,
            'start_time' => $startTime->copy()->addHour(),
            'end_time' => $endTime->copy()->addHour(),
            'status' => 'cancelled'
        ]);

        $this->assertTrue($this->resource->isAvailable($startTime, $endTime));
    }

    public function test_resource_is_available_with_excluded_booking(): void
    {
        $startTime = now()->addDay();
        $endTime = now()->addDay()->addHours(2);

        // Create a booking
        $booking = Booking::factory()->create([
            'resource_id' => $this->resource->id,
            'user_id' => $this->user->id,
            'start_time' => $startTime->copy()->addHour(),
            'end_time' => $endTime->copy()->addHour(),
            'status' => 'confirmed'
        ]);

        $this->assertTrue($this->resource->isAvailable($startTime, $endTime, $booking->id));
    }
}
