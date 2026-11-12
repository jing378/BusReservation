<?php

namespace App\Http\Controllers;

use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Public landing page with live routes, fares and upcoming departures.
 */
class HomeController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        return view('home', [
            'user' => $user,
            'dashboardUrl' => $user ? route($user->isAdmin() ? 'admin.dashboard' : 'customer.dashboard') : null,
            'bookUrl' => $user && ! $user->isAdmin() ? route('customer.bookings') : route('login'),
            'discountPercent' => BookingService::DISCOUNT_PERCENT,
            'stats' => [
                'routes' => (int) $this->value("SELECT COUNT(*) FROM routes WHERE status = 'active'"),
                'buses' => (int) $this->value("SELECT COUNT(*) FROM buses WHERE status = 'active'"),
                'trips' => (int) $this->value("SELECT COUNT(*) FROM trips WHERE status = 'scheduled' AND departure_time > NOW()"),
            ],
            'departures' => $this->rows("SELECT t.id, r.origin, r.destination, t.price, t.departure_time, t.arrival_time,
                    b.model AS bus_model, b.capacity,
                    b.capacity - (SELECT COUNT(*) FROM bookings bk WHERE bk.trip_id = t.id AND bk.status IN ('pending', 'confirmed')) AS seats_left
                FROM trips t
                JOIN routes r ON r.id = t.route_id
                JOIN buses b ON b.id = t.bus_id
                WHERE t.status = 'scheduled' AND r.status = 'active' AND t.departure_time > NOW()
                ORDER BY t.departure_time ASC
                LIMIT 6"),
            'routes' => $this->rows("SELECT origin, destination, distance_km, base_price
                FROM routes
                WHERE status = 'active'
                ORDER BY origin, destination
                LIMIT 60"),
        ]);
    }
}
