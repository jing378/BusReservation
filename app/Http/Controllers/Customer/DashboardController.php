<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\BusLayoutService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, BusLayoutService $busLayouts): View
    {
        $user = $request->user();

        return view('customer.dashboard', [
            'username' => $user->username,
            'full_name' => $user->full_name ?: $user->username,
            'busLayouts' => $busLayouts->forDashboard(withUnassignedBuses: false),
            'trips' => $this->rows("SELECT t.id,
                    CONCAT(r.origin, ' → ', r.destination) AS route_name,
                    DATE_FORMAT(t.departure_time, '%Y-%m-%d %H:%i') AS dep_time,
                    t.status,
                    b.model AS bus,
                    (SELECT COUNT(*) FROM bookings WHERE trip_id = t.id AND status = 'confirmed') AS booked_seats,
                    b.capacity AS total_seats,
                    t.price
                FROM trips t
                JOIN routes r ON r.id = t.route_id
                JOIN buses b ON b.id = t.bus_id
                WHERE t.status != 'completed'
                ORDER BY t.departure_time DESC LIMIT 20"),
            'activeRoutes' => array_column($this->rows("SELECT CONCAT(origin, ' → ', destination) AS route FROM routes WHERE status = 'active' ORDER BY origin ASC"), 'route'),
            'popularRoutes' => $this->rows("SELECT r.id,
                    CONCAT(r.origin, ' → ', r.destination) AS route,
                    COUNT(b.id) AS total_bookings,
                    COALESCE(SUM(b.amount), 0) AS revenue
                FROM routes r
                LEFT JOIN trips t ON t.route_id = r.id
                LEFT JOIN bookings b ON b.trip_id = t.id AND b.status = 'confirmed'
                WHERE r.status = 'active'
                GROUP BY r.id, r.origin, r.destination
                ORDER BY total_bookings DESC
                LIMIT 5"),
            'bookings' => $this->rows("SELECT b.booking_code, CONCAT(r.origin, ' → ', r.destination) AS route_name,
                    DATE_FORMAT(t.departure_time, '%Y-%m-%d %H:%i') AS dep_time,
                    b.seat_number, b.amount, b.status, b.booked_at
                FROM bookings b
                JOIN trips t ON t.id = b.trip_id
                JOIN routes r ON r.id = t.route_id
                WHERE b.user_id = ?
                ORDER BY b.booked_at DESC LIMIT 10", [$user->id]),
            'countBusesWithTrips' => (int) $this->value("SELECT COUNT(DISTINCT b.id) FROM buses b JOIN trips t ON t.bus_id = b.id
                WHERE b.status = 'active' AND t.status = 'scheduled'"),
            'stats' => [
                'availableTrips' => (int) $this->value("SELECT COUNT(*) FROM trips WHERE status = 'scheduled' AND departure_time > NOW()"),
                'myUpcoming' => (int) $this->value("SELECT COUNT(*) FROM bookings b JOIN trips t ON t.id = b.trip_id
                    WHERE b.user_id = ? AND b.status IN ('pending', 'confirmed') AND t.departure_time > NOW()", [$user->id]),
                'myBookings' => (int) $this->value('SELECT COUNT(*) FROM bookings WHERE user_id = ?', [$user->id])
                    + (int) $this->value('SELECT COUNT(*) FROM bookings_archive WHERE user_id = ?', [$user->id]),
                'totalSpent' => (float) $this->value("SELECT COALESCE(SUM(p.amount), 0) FROM payments p JOIN bookings b ON b.id = p.booking_id
                    WHERE b.user_id = ? AND p.status = 'paid'", [$user->id]),
            ],
        ]);
    }
}
