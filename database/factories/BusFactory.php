<?php

namespace Database\Factories;

use App\Models\Bus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bus>
 */
class BusFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'plate_number' => strtoupper(fake()->unique()->bothify('???-####')),
            'model' => fake()->randomElement(['Hino RK8', 'Volvo B11R', 'Yutong ZK6122', 'Daewoo BH117']),
            'capacity' => 40,
            'status' => 'active',
        ];
    }
}
