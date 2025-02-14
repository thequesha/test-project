<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Resource $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->resource = Resource::factory()->create();
    }

    public function test_booking_belongs_to_resource(): void
    {
        $booking = Booking::factory()->create([
            'resource_id' => $this->resource->id,
            'user_id' => $this->user->id
        ]);

        $this->assertInstanceOf(Resource::class, $booking->resource);
        $this->assertEquals($this->resource->id, $booking->resource->id);
    }

    public function test_booking_belongs_to_user(): void
    {
        $booking = Booking::factory()->create([
            'resource_id' => $this->resource->id,
            'user_id' => $this->user->id
        ]);

        $this->assertInstanceOf(User::class, $booking->user);
        $this->assertEquals($this->user->id, $booking->user->id);
    }

    public function test_booking_can_be_cancelled_if_in_future(): void
    {
        $booking = Booking::factory()->create([
            'resource_id' => $this->resource->id,
            'user_id' => $this->user->id,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
            'status' => 'confirmed'
        ]);

        $this->assertTrue($booking->canBeCancelled());
    }

    public function test_booking_cannot_be_cancelled_if_in_past(): void
    {
        $booking = Booking::factory()->create([
            'resource_id' => $this->resource->id,
            'user_id' => $this->user->id,
            'start_time' => now()->subDay(),
            'end_time' => now()->subDay()->addHours(2),
            'status' => 'confirmed'
        ]);

        $this->assertFalse($booking->canBeCancelled());
    }

    public function test_booking_cannot_be_cancelled_if_already_cancelled(): void
    {
        $booking = Booking::factory()->create([
            'resource_id' => $this->resource->id,
            'user_id' => $this->user->id,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
            'status' => 'cancelled'
        ]);

        $this->assertFalse($booking->canBeCancelled());
    }

    public function test_active_scope_only_returns_confirmed_bookings(): void
    {
        // Create confirmed booking
        Booking::factory()->create([
            'resource_id' => $this->resource->id,
            'user_id' => $this->user->id,
            'status' => 'confirmed'
        ]);

        // Create cancelled booking
        Booking::factory()->create([
            'resource_id' => $this->resource->id,
            'user_id' => $this->user->id,
            'status' => 'cancelled'
        ]);

        $activeBookings = Booking::active()->get();

        $this->assertEquals(1, $activeBookings->count());
        $this->assertEquals('confirmed', $activeBookings->first()->status);
    }
}
