<?php

namespace Database\Factories;

use App\Models\Bus;
use App\Models\BusRoute;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Trip>
 */
class TripFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $departure = now()->addDays(2)->setTime(8, 0);

        return [
            'route_id' => BusRoute::factory(),
            'bus_id' => Bus::factory(),
            'departure_time' => $departure,
            'arrival_time' => $departure->copy()->addHours(4),
            'price' => 500,
            'status' => 'scheduled',
        ];
    }

    public function departed(): static
    {
        return $this->state(fn (array $attributes) => [
            'departure_time' => now()->subHour(),
            'arrival_time' => now()->addHours(3),
        ]);
    }

    public function arrived(): static
    {
        return $this->state(fn (array $attributes) => [
            'departure_time' => now()->subHours(5),
            'arrival_time' => now()->subHour(),
        ]);
    }
}
