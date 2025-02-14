<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = now()->addHours(fake()->numberBetween(1, 720)); // Between now and 30 days
        $endTime = $startTime->copy()->addHours(fake()->numberBetween(1, 4)); // 1-4 hours duration

        return [
            'resource_id' => Resource::factory(),
            'user_id' => User::factory(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'confirmed',
            'notes' => fake()->sentence(),
        ];
    }

    /**
     * Indicate that the booking is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }

    /**
     * Indicate that the booking is in the past.
     */
    public function past(): static
    {
        return $this->state(function (array $attributes) {
            $startTime = now()->subHours(fake()->numberBetween(1, 720)); // Between 30 days ago and yesterday
            $endTime = $startTime->copy()->addHours(fake()->numberBetween(1, 4)); // 1-4 hours duration

            return [
                'start_time' => $startTime,
                'end_time' => $endTime,
            ];
        });
    }

    /**
     * Indicate that the booking is in the future.
     */
    public function future(): static
    {
        return $this->state(function (array $attributes) {
            $startTime = now()->addHours(fake()->numberBetween(24, 720)); // Between tomorrow and 30 days from now
            $endTime = $startTime->copy()->addHours(fake()->numberBetween(1, 4)); // 1-4 hours duration

            return [
                'start_time' => $startTime,
                'end_time' => $endTime,
            ];
        });
    }
}
