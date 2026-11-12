<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Active buses with their scheduled trip and booked seats, for the dashboard seat maps.
 */
class BusLayoutService
{
    /**
     * @param  bool  $withUnassignedBuses  Include active buses that have no scheduled trip (admin view).
     * @return list<array<string, mixed>>
     */
    public function forDashboard(bool $withUnassignedBuses): array
    {
        $join = $withUnassignedBuses ? 'LEFT JOIN' : 'JOIN';

        $buses = array_map(fn (object $row): array => (array) $row, DB::select("SELECT
                b.id,
                b.plate_number,
                b.model AS bus_name,
                b.capacity,
                b.status AS bus_status,
                t.id AS trip_id,
                CONCAT(COALESCE(r.origin, 'No'), ' → ', COALESCE(r.destination, 'Route')) AS route_name,
                DATE_FORMAT(t.departure_time, '%Y-%m-%d %H:%i') AS departure_time,
                t.status AS trip_status,
                r.status AS route_status
            FROM buses b
            {$join} trips t ON t.bus_id = b.id AND t.status = 'scheduled'
            {$join} routes r ON r.id = t.route_id
            WHERE b.status = 'active'
            ORDER BY t.departure_time ASC, b.created_at DESC"));

        $tripIds = array_values(array_filter(array_column($buses, 'trip_id')));

        $bookedSeats = $tripIds === [] ? collect() : DB::table('bookings')
            ->whereIn('trip_id', $tripIds)
            ->where('status', 'confirmed')
            ->get(['trip_id', 'seat_number'])
            ->groupBy('trip_id');

        foreach ($buses as &$bus) {
            $bus['booked_seats'] = $bus['trip_id']
                ? $bookedSeats->get($bus['trip_id'], collect())->pluck('seat_number')->map(fn ($seat) => (string) $seat)->all()
                : [];
        }

        return $buses;
    }
}
