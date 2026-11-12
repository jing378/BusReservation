<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BusLayoutService;
use App\Services\MaintenanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

/**
 * Admin overview. Revenue and booking counts include archived bookings so cleanup does not
 * erase history; live revenue applies the 20% student/senior/PWD discount to the trip price.
 */
class DashboardController extends Controller
{
    private const ACTIVE_OR_DONE = "('confirmed','pending','completed')";

    private const DISCOUNTED_PRICE = "CASE WHEN u.user_type IN ('Student', 'Senior Citizen/PWD') THEN t.price * 0.8 ELSE t.price END";

    public function __invoke(Request $request, MaintenanceService $maintenance, BusLayoutService $busLayouts): View
    {
        $maintenance->runScheduled();

        [$dayLabels, $bookingsData, $revenueData] = $this->monthlySeries();

        return view('admin.dashboard', [
            'username' => $request->user()->username,
            'full_name' => $request->user()->full_name ?: $request->user()->username,
            'countBookings' => (int) $this->value('SELECT COUNT(*) FROM bookings WHERE DATE(booked_at) = CURDATE()'),
            'totalBookings' => (int) $this->value('SELECT COUNT(*) FROM bookings') + (int) $this->value('SELECT COUNT(*) FROM bookings_archive'),
            'countRoutes' => (int) $this->value("SELECT COUNT(*) FROM routes WHERE status = 'active'"),
            'countBuses' => (int) $this->value("SELECT COUNT(*) FROM buses WHERE status = 'active'"),
            'countPassengersToday' => (int) $this->value("SELECT COUNT(*) FROM bookings WHERE status IN ('pending','confirmed') AND DATE(booked_at) = CURDATE()"),
            'cancellationsToday' => (int) $this->value("SELECT COUNT(*) FROM bookings WHERE status = 'cancelled' AND DATE(booked_at) = CURDATE()"),
            'countBusesWithTrips' => (int) $this->value("SELECT COUNT(DISTINCT b.id) FROM buses b JOIN trips t ON t.bus_id = b.id WHERE b.status = 'active' AND t.status = 'scheduled'"),
            'totalRevenue' => $this->revenue(),
            'monthlyRevenue' => $this->revenue(thisMonthOnly: true),
            'avgTicketToday' => $this->averageTicketToday(),
            'occupancyRateToday' => $this->occupancyRateToday(),
            'dayLabels' => $dayLabels,
            'bookingsData' => $bookingsData,
            'revenueData' => $revenueData,
            'statusBreakdown' => $this->statusBreakdown(),
            'recentBookings' => $this->rows("SELECT b.booking_code, b.passenger_name, b.amount, b.status, b.booked_at,
                    CONCAT(r.origin, ' → ', r.destination) AS route,
                    DATE_FORMAT(t.departure_time, '%Y-%m-%d %H:%i') AS departure
                FROM bookings b
                JOIN trips t ON t.id = b.trip_id
                JOIN routes r ON r.id = t.route_id
                ORDER BY b.booked_at DESC LIMIT 5"),
            'upcomingTrips' => $this->rows("SELECT t.id,
                    CONCAT(r.origin, ' → ', r.destination) AS route,
                    DATE_FORMAT(t.departure_time, '%Y-%m-%d %H:%i') AS departure,
                    b.model AS bus,
                    b.capacity,
                    t.status,
                    (SELECT COUNT(*) FROM bookings bk WHERE bk.trip_id = t.id AND bk.status = 'confirmed') AS booked
                FROM trips t
                JOIN routes r ON r.id = t.route_id
                JOIN buses b ON b.id = t.bus_id
                WHERE t.status != 'completed'
                ORDER BY t.departure_time ASC LIMIT 10"),
            'lowSeatAlerts' => $this->rows("SELECT t.id,
                    CONCAT(r.origin, ' → ', r.destination) AS route,
                    DATE_FORMAT(t.departure_time, '%Y-%m-%d %H:%i') AS departure,
                    (b.capacity - (SELECT COUNT(*) FROM bookings bk WHERE bk.trip_id = t.id AND bk.status = 'confirmed')) AS seats_left
                FROM trips t
                JOIN routes r ON r.id = t.route_id
                JOIN buses b ON b.id = t.bus_id
                WHERE t.status = 'scheduled' AND t.departure_time > NOW()
                HAVING seats_left <= 5 AND seats_left > 0
                ORDER BY t.departure_time ASC LIMIT 10"),
            'activeRoutes' => array_column($this->rows("SELECT CONCAT(origin, ' → ', destination) AS route FROM routes WHERE status = 'active' ORDER BY origin ASC"), 'route'),
            'busLayouts' => $busLayouts->forDashboard(withUnassignedBuses: true),
        ]);
    }

    private function revenue(bool $thisMonthOnly = false): float
    {
        $month = fn (string $column) => $thisMonthOnly
            ? " AND MONTH({$column}) = MONTH(CURRENT_DATE()) AND YEAR({$column}) = YEAR(CURRENT_DATE())"
            : '';

        $live = (float) $this->value('SELECT COALESCE(SUM('.self::DISCOUNTED_PRICE.'), 0)
            FROM bookings b
            JOIN trips t ON b.trip_id = t.id
            LEFT JOIN users u ON b.user_id = u.id
            WHERE b.status IN '.self::ACTIVE_OR_DONE.$month('b.booked_at'));

        $archived = (float) $this->value('SELECT COALESCE(SUM(amount), 0) FROM bookings_archive WHERE status IN '.self::ACTIVE_OR_DONE.$month('booked_at'));

        return $live + $archived;
    }

    private function averageTicketToday(): float
    {
        $totals = ['sum' => 0.0, 'count' => 0];

        foreach (['bookings', 'bookings_archive'] as $table) {
            $row = $this->rows("SELECT COALESCE(SUM(amount), 0) AS total, COUNT(*) AS cnt FROM {$table}
                WHERE status IN ".self::ACTIVE_OR_DONE.' AND DATE(booked_at) = CURDATE()')[0];
            $totals['sum'] += (float) $row['total'];
            $totals['count'] += (int) $row['cnt'];
        }

        return $totals['count'] > 0 ? $totals['sum'] / $totals['count'] : 0;
    }

    private function occupancyRateToday(): int
    {
        $row = $this->rows("SELECT COALESCE(SUM(seats_booked), 0) AS booked, COALESCE(SUM(capacity), 0) AS capacity
            FROM (
                SELECT b.capacity,
                       (SELECT COUNT(*) FROM bookings bk WHERE bk.trip_id = t.id AND bk.status = 'confirmed') AS seats_booked
                FROM trips t
                JOIN buses b ON b.id = t.bus_id
                WHERE DATE(t.departure_time) = CURDATE() AND t.status = 'scheduled'
            ) x")[0];

        return (int) $row['capacity'] > 0 ? (int) round($row['booked'] / $row['capacity'] * 100) : 0;
    }

    /**
     * Bookings and revenue per month for the last 12 months (months with data only).
     *
     * @return array{0: list<string>, 1: list<int>, 2: list<float>}
     */
    private function monthlySeries(): array
    {
        $window = 'booked_at >= DATE_SUB(CURDATE(), INTERVAL 11 MONTH)';
        $key = fn (array $row): string => $row['y'].'-'.str_pad((string) $row['m'], 2, '0', STR_PAD_LEFT);

        $counts = [];
        foreach ($this->rows("SELECT YEAR(booked_at) y, MONTH(booked_at) m, COUNT(*) cnt FROM bookings WHERE {$window} GROUP BY y, m
            UNION ALL
            SELECT YEAR(booked_at) y, MONTH(booked_at) m, COUNT(*) cnt FROM bookings_archive WHERE {$window} GROUP BY y, m") as $row) {
            $counts[$key($row)] = ($counts[$key($row)] ?? 0) + (int) $row['cnt'];
        }

        $revenue = [];
        foreach ($this->rows('SELECT YEAR(b.booked_at) y, MONTH(b.booked_at) m, COALESCE(SUM('.self::DISCOUNTED_PRICE.'), 0) amt
            FROM bookings b
            JOIN trips t ON b.trip_id = t.id
            LEFT JOIN users u ON b.user_id = u.id
            WHERE b.status IN '.self::ACTIVE_OR_DONE.' AND b.'.$window.'
            GROUP BY y, m
            UNION ALL
            SELECT YEAR(booked_at) y, MONTH(booked_at) m, COALESCE(SUM(amount), 0) amt FROM bookings_archive
            WHERE status IN '.self::ACTIVE_OR_DONE." AND {$window} GROUP BY y, m") as $row) {
            $revenue[$key($row)] = ($revenue[$key($row)] ?? 0) + (float) $row['amt'];
        }

        $months = array_keys($counts);
        sort($months);

        if ($months === []) {
            $months = [now()->format('Y-m')];
        }

        return [
            array_map(fn (string $month): string => Carbon::parse($month.'-01')->format('M Y'), $months),
            array_map(fn (string $month): int => $counts[$month] ?? 0, $months),
            array_map(fn (string $month): float => $revenue[$month] ?? 0.0, $months),
        ];
    }

    /**
     * @return array<string, int>
     */
    private function statusBreakdown(): array
    {
        $breakdown = ['confirmed' => 0, 'pending' => 0, 'cancelled' => 0, 'completed' => 0, 'refunded' => 0];
        $thisMonth = 'YEAR(booked_at) = YEAR(CURDATE()) AND MONTH(booked_at) = MONTH(CURDATE())';

        foreach ($this->rows("SELECT status, COUNT(*) c FROM bookings WHERE {$thisMonth} GROUP BY status
            UNION ALL
            SELECT status, COUNT(*) c FROM bookings_archive WHERE {$thisMonth} GROUP BY status") as $row) {
            $status = strtolower($row['status']);

            if (isset($breakdown[$status])) {
                $breakdown[$status] += (int) $row['c'];
            }
        }

        return $breakdown;
    }
}
