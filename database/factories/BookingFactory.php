<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'trip_id' => Trip::factory(),
            'passenger_name' => fake()->name(),
            'passenger_phone' => '09'.fake()->numerify('#########'),
            'seat_number' => fake()->unique()->numberBetween(1, 40),
            'status' => 'confirmed',
            'amount' => 500,
            'booking_code' => 'BK-'.fake()->unique()->numerify('##########-####'),
        ];
    }
}
