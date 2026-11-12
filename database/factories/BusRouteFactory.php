<?php

namespace Database\Factories;

use App\Models\BusRoute;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BusRoute>
 */
class BusRouteFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'origin' => fake()->unique()->city(),
            'destination' => fake()->city(),
            'distance_km' => fake()->randomFloat(2, 10, 500),
            'base_price' => 500,
            'status' => 'active',
        ];
    }
}
