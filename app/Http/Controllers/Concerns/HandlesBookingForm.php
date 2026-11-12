<?php

namespace App\Http\Controllers\Concerns;

use App\Services\BookingService;

/**
 * Shared by the admin and customer booking pages: bookable trips, amount validation and receipts.
 *
 * @property BookingService $bookings
 */
trait HandlesBookingForm
{
    /**
     * @param  array<int|string, mixed>  $bindings
     * @return list<array<string, mixed>>
     */
    abstract protected function rows(string $sql, array $bindings = []): array;

    /**
     * Scheduled trips on active routes, in the shape the booking form expects.
     *
     * @return list<array<string, mixed>>
     */
    protected function bookableTrips(): array
    {
        return $this->rows("SELECT
                t.id,
                b.plate_number,
                b.model AS bus_name,
                b.capacity,
                CONCAT(r.origin, ' → ', r.destination, ' (', DATE_FORMAT(t.departure_time, '%Y-%m-%d %H:%i'), ')') AS label,
                CONCAT(r.origin, ' → ', r.destination) AS route_name,
                r.base_price,
                t.price AS trip_price,
                DATE_FORMAT(t.departure_time, '%Y-%m-%d %H:%i') AS departure_time,
                DATE_FORMAT(t.arrival_time, '%Y-%m-%d %H:%i') AS arrival_time
            FROM trips t
            JOIN routes r ON r.id = t.route_id
            JOIN buses b ON b.id = t.bus_id
            WHERE t.status = 'scheduled' AND r.status = 'active'
            ORDER BY t.departure_time ASC");
    }

    /**
     * Validate trip, seat and amount fields.
     *
     * @param  list<array<string, mixed>>  $trips
     * @return array<string, string>
     */
    protected function validateBookingFields(string $tripId, string $seatNumber, string $amount, array $trips, string $userType): array
    {
        $errors = [];

        if (! ctype_digit($tripId)) {
            $errors['trip_id'] = 'Trip is required.';
        }

        if (! ctype_digit($seatNumber)) {
            $errors['seat_number'] = 'Please select a seat from the seat layout.';
        } elseif (ctype_digit($tripId) && $this->bookings->isSeatTaken((int) $tripId, (int) $seatNumber)) {
            $errors['seat_number'] = 'This seat is already booked. Please select another seat.';
        }

        if (! is_numeric($amount)) {
            $errors['amount'] = 'Amount must be numeric.';
        }

        $trip = $this->findTrip($trips, $tripId);

        if (! $errors && $trip) {
            $discount = $this->bookings->calculateDiscount((float) ($trip['trip_price'] ?? $trip['base_price']), $userType);

            if ((float) $amount < $discount['discounted_price']) {
                $errors['amount'] = 'Amount must be at least ₱'.number_format($discount['discounted_price'], 2)
                    .($discount['discount_percent'] > 0 ? ' (with '.$discount['discount_percent'].'% discount)' : '');
            }
        }

        return $errors;
    }

    /**
     * @param  list<array<string, mixed>>  $trips
     * @return array<string, mixed>|null
     */
    protected function findTrip(array $trips, string $tripId): ?array
    {
        foreach ($trips as $trip) {
            if ((string) $trip['id'] === $tripId) {
                return $trip;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $trip
     * @return array<string, mixed>
     */
    protected function receipt(string $bookingCode, array $trip, string $passengerName, string $seatNumber, string $amount, string $userType): array
    {
        $discount = $this->bookings->calculateDiscount((float) ($trip['trip_price'] ?? $trip['base_price']), $userType);

        return [
            'booking_code' => $bookingCode,
            'route_name' => $trip['route_name'],
            'dep_time' => $trip['departure_time'],
            'passenger_name' => $passengerName,
            'seat_number' => $seatNumber,
            'amount' => $amount,
            'user_type' => $userType,
            'original_price' => $discount['original_price'],
            'discount_percent' => $discount['discount_percent'],
            'discount_amount' => $discount['discount_amount'],
        ];
    }

    /**
     * Most recent bookings, optionally limited to one user.
     *
     * @return list<array<string, mixed>>
     */
    protected function recentBookings(?int $userId): array
    {
        $where = $userId !== null ? 'WHERE b.user_id = ?' : '';

        return $this->rows("SELECT b.id, b.booking_code, b.passenger_name, b.seat_number, b.amount, b.status, b.booked_at,
                CONCAT(r.origin, ' → ', r.destination) AS route_name,
                DATE_FORMAT(t.departure_time, '%Y-%m-%d %H:%i') AS dep_time
            FROM bookings b
            JOIN trips t ON t.id = b.trip_id
            JOIN routes r ON r.id = t.route_id
            {$where}
            ORDER BY b.booked_at DESC LIMIT 20", $userId !== null ? [$userId] : []);
    }
}
