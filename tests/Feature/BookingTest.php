<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
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

    public function test_can_get_all_bookings(): void
    {
        Booking::factory(3)->create([
            'resource_id' => $this->resource->id,
            'user_id' => $this->user->id
        ]);

        $response = $this->getJson('/api/bookings');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'resource_id',
                        'user_id',
                        'start_time',
                        'end_time',
                        'status',
                        'notes',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'links',
                'meta'
            ]);
    }

    public function test_can_create_booking(): void
    {
        $bookingData = [
            'resource_id' => $this->resource->id,
            'user_id' => $this->user->id,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
            'notes' => 'Test booking'
        ];

        $response = $this->postJson('/api/bookings', $bookingData);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'resource_id' => $this->resource->id,
                    'user_id' => $this->user->id,
                    'status' => 'confirmed'
                ]
            ]);

        $this->assertDatabaseHas('bookings', [
            'resource_id' => $this->resource->id,
            'user_id' => $this->user->id,
            'status' => 'confirmed'
        ]);
    }

    public function test_cannot_create_overlapping_booking(): void
    {
        // Create an existing booking
        $startTime = now()->addDay();
        $endTime = $startTime->copy()->addHours(2);

        Booking::create([
            'resource_id' => $this->resource->id,
            'user_id' => $this->user->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'confirmed'
        ]);

        // Try to create an overlapping booking
        $bookingData = [
            'resource_id' => $this->resource->id,
            'user_id' => $this->user->id,
            'start_time' => $startTime->copy()->addHour(),
            'end_time' => $endTime->copy()->addHour(),
            'notes' => 'Overlapping booking'
        ];

        $response = $this->postJson('/api/bookings', $bookingData);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'The resource is not available for the selected time period.',
                'errors' => [
                    'resource_id' => [
                        'The resource is not available for the selected time period.'
                    ]
                ]
            ]);
    }

    public function test_can_cancel_booking(): void
    {
        $booking = Booking::factory()->create([
            'resource_id' => $this->resource->id,
            'user_id' => $this->user->id,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
            'status' => 'confirmed'
        ]);

        $response = $this->deleteJson("/api/bookings/{$booking->id}");

        $response->assertStatus(204);
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'cancelled'
        ]);
    }

    public function test_cannot_cancel_past_booking(): void
    {
        $booking = Booking::factory()->create([
            'resource_id' => $this->resource->id,
            'user_id' => $this->user->id,
            'start_time' => now()->subDay(),
            'end_time' => now()->subDay()->addHours(2),
            'status' => 'confirmed'
        ]);

        $response = $this->deleteJson("/api/bookings/{$booking->id}");

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'This booking cannot be cancelled'
            ]);
    }

    public function test_can_get_resource_bookings(): void
    {
        Booking::factory(3)->create([
            'resource_id' => $this->resource->id,
            'user_id' => $this->user->id
        ]);

        $response = $this->getJson("/api/resources/{$this->resource->id}/bookings");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'resource_id',
                        'user_id',
                        'start_time',
                        'end_time',
                        'status'
                    ]
                ]
            ]);
    }

    public function test_cannot_create_booking_with_invalid_data(): void
    {
        $response = $this->postJson('/api/bookings', [
            'resource_id' => '',
            'user_id' => '',
            'start_time' => '',
            'end_time' => ''
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['resource_id', 'user_id', 'start_time', 'end_time']);
    }
}
