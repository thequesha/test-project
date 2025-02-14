<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Resource;
use App\Models\User;
use App\Notifications\BookingCancelledNotification;
use App\Notifications\BookingCreatedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BookingNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $resource;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->resource = Resource::factory()->create(['is_active' => true]);
    }

    public function test_notification_is_sent_when_booking_is_created(): void
    {
        Notification::fake();

        $bookingData = [
            'resource_id' => $this->resource->id,
            'user_id' => $this->user->id,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
        ];

        $response = $this->postJson('/api/bookings', $bookingData);

        $response->assertStatus(201);

        Notification::assertSentTo(
            $this->user,
            BookingCreatedNotification::class
        );
    }

    public function test_notification_is_sent_when_booking_is_cancelled(): void
    {
        Notification::fake();

        $booking = Booking::factory()->create([
            'resource_id' => $this->resource->id,
            'user_id' => $this->user->id,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
            'status' => 'confirmed'
        ]);

        $response = $this->deleteJson("/api/bookings/{$booking->id}");

        $response->assertStatus(204);

        Notification::assertSentTo(
            $this->user,
            BookingCancelledNotification::class
        );
    }
}
