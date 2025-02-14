<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_get_all_resources(): void
    {
        Resource::factory(3)->create();

        $response = $this->getJson('/api/resources');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'type',
                        'description',
                        'is_active',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'links',
                'meta'
            ]);
    }

    public function test_can_create_resource(): void
    {
        $resourceData = [
            'name' => 'Conference Room A',
            'type' => 'room',
            'description' => 'A large conference room',
            'is_active' => true
        ];

        $response = $this->postJson('/api/resources', $resourceData);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'name' => 'Conference Room A',
                    'type' => 'room',
                    'is_active' => true
                ]
            ]);

        $this->assertDatabaseHas('resources', [
            'name' => 'Conference Room A',
            'type' => 'room',
            'is_active' => true
        ]);
    }

    public function test_can_get_single_resource(): void
    {
        $resource = Resource::factory()->create();

        $response = $this->getJson("/api/resources/{$resource->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $resource->id,
                    'name' => $resource->name,
                    'type' => $resource->type
                ]
            ]);
    }

    public function test_can_update_resource(): void
    {
        $resource = Resource::factory()->create();

        $updateData = [
            'name' => 'Updated Name',
            'type' => 'room',
            'description' => 'Updated description'
        ];

        $response = $this->putJson("/api/resources/{$resource->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $resource->id,
                    'name' => 'Updated Name',
                    'type' => 'room',
                    'description' => 'Updated description'
                ]
            ]);

        $this->assertDatabaseHas('resources', [
            'id' => $resource->id,
            'name' => 'Updated Name',
            'type' => 'room',
            'description' => 'Updated description'
        ]);
    }

    public function test_cannot_update_resource_with_invalid_data(): void
    {
        $resource = Resource::factory()->create();

        $response = $this->putJson("/api/resources/{$resource->id}", [
            'name' => '',
            'type' => 'invalid_type'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'type']);
    }

    public function test_can_delete_resource(): void
    {
        $resource = Resource::factory()->create();

        $response = $this->deleteJson("/api/resources/{$resource->id}");

        $response->assertStatus(204);
        $this->assertSoftDeleted('resources', ['id' => $resource->id]);
    }

    public function test_cannot_delete_resource_with_active_bookings(): void
    {
        $resource = Resource::factory()->create();
        $user = User::factory()->create();

        // Create an active booking
        Booking::factory()->create([
            'resource_id' => $resource->id,
            'user_id' => $user->id,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
            'status' => 'confirmed'
        ]);

        $response = $this->deleteJson("/api/resources/{$resource->id}");

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Cannot delete a resource with active bookings.'
            ]);
    }

    public function test_cannot_create_resource_with_duplicate_name(): void
    {
        $existingResource = Resource::factory()->create(['name' => 'Test Resource']);

        $resourceData = [
            'name' => 'Test Resource',
            'type' => 'room',
            'description' => 'Another resource with same name'
        ];

        $response = $this->postJson('/api/resources', $resourceData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_can_filter_resources_by_type(): void
    {
        Resource::factory()->create(['type' => 'room']);
        Resource::factory()->create(['type' => 'equipment']);
        Resource::factory()->create(['type' => 'vehicle']);

        $response = $this->getJson('/api/resources?type=room');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJson([
                'data' => [
                    [
                        'type' => 'room'
                    ]
                ]
            ]);
    }

    public function test_can_filter_active_resources(): void
    {
        Resource::factory()->create(['is_active' => true]);
        Resource::factory()->create(['is_active' => false]);

        $response = $this->getJson('/api/resources?active_only=true');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJson([
                'data' => [
                    [
                        'is_active' => true
                    ]
                ]
            ]);
    }

    public function test_cannot_create_resource_with_invalid_data(): void
    {
        $response = $this->postJson('/api/resources', [
            'name' => '',
            'type' => ''
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'type']);
    }
}
