<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'code' => strtoupper(fake()->unique()->bothify('??')),
            'opening_time' => fake()->dateTimeBetween('today 06:00:00', 'today 12:00:00')->format('H:i:s'),
            'closing_time' => fake()->dateTimeBetween('today 12:00:00', 'today 15:00:00')->format('H:i:s'),
            'max_queue_per_day' => fake()->numberBetween(50, 200),
            'is_active' => fake()->boolean(80),
        ];
    }
}
