<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusRoute;
use App\Models\Trip;
use App\Services\ActivityLogger;
use App\Services\BookingService;
use App\Services\MaintenanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class TripController extends Controller
{
    public function __construct(private MaintenanceService $maintenance, private BookingService $bookings) {}

    public function index(): View
    {
        $this->maintenance->runScheduled();

        return $this->render();
    }

    public function store(Request $request): View
    {
        $this->maintenance->runScheduled();

        return match ($request->input('action', 'create')) {
            'create' => $this->create($request),
            'delete' => $this->delete($request),
            default => $this->render(),
        };
    }

    private function create(Request $request): View
    {
        $routeId = trim((string) $request->input('route_id'));
        $busId = trim((string) $request->input('bus_id'));
        $departure = trim((string) $request->input('departure_time'));
        $arrival = trim((string) $request->input('arrival_time'));
        $errors = [];

        if (! ctype_digit($routeId)) {
            $errors['route_id'] = 'Route is required.';
        }

        if (! ctype_digit($busId)) {
            $errors['bus_id'] = 'Bus is required.';
        }

        if ($departure === '') {
            $errors['departure_time'] = 'Departure time is required.';
        }

        if (! $errors) {
            try {
                if (Carbon::parse($departure)->lessThanOrEqualTo(now())) {
                    $errors['departure_time'] = 'Departure time cannot be in the past.';
                }
            } catch (Throwable) {
                $errors['departure_time'] = 'Invalid departure time format.';
            }
        }

        if ($errors) {
            return $this->render(errors: $errors);
        }

        $departureAt = Carbon::parse($departure);
        $arrivalAt = $arrival !== '' ? Carbon::parse($arrival) : null;

        $duplicate = Trip::where('route_id', $routeId)
            ->where('bus_id', $busId)
            ->where('departure_time', $departureAt)
            ->whereIn('status', ['scheduled', 'departed'])
            ->exists();

        if ($duplicate) {
            return $this->render(errors: ['general' => 'This trip already exists. A trip with the same route, bus, and departure time is already scheduled.']);
        }

        if ($conflict = $this->findConflict((int) $busId, $departureAt, $arrivalAt ?? $departureAt->copy()->addHours(2))) {
            return $this->render(errors: ['general' => 'Scheduling conflict: This bus is already scheduled for a trip from '
                .e($conflict->origin).' to '.e($conflict->destination).' at '
                .Carbon::parse($conflict->departure_time)->format('Y-m-d H:i').'. Please choose a different time or bus.']);
        }

        $route = BusRoute::find($routeId);

        if (! $route) {
            return $this->render(errors: ['route_id' => 'Route is required.']);
        }

        try {
            Trip::create([
                'route_id' => $route->id,
                'bus_id' => (int) $busId,
                'departure_time' => $departureAt,
                'arrival_time' => $arrivalAt,
                'price' => $route->base_price,
                'status' => 'scheduled',
            ]);
        } catch (Throwable $exception) {
            ActivityLogger::log('trip_create_failed', ['error' => $exception->getMessage(), 'route_id' => (int) $routeId, 'bus_id' => (int) $busId]);

            return $this->render(errors: ['general' => 'An error occurred while creating the trip. Please try again.']);
        }

        ActivityLogger::log('trip_create', [
            'by_user_id' => $request->user()->id, 'route_id' => (int) $routeId, 'bus_id' => (int) $busId,
            'departure_time' => $departure, 'arrival_time' => $arrival ?: null,
        ]);

        return $this->render(ok: 'Trip added successfully with route price.');
    }

    /**
     * Find an active trip of this bus whose time window overlaps [departure, arrival].
     * Trips without an arrival time are assumed to take two hours.
     */
    private function findConflict(int $busId, Carbon $departure, Carbon $arrival): ?object
    {
        return DB::selectOne(
            "SELECT t.id, t.departure_time, r.origin, r.destination
             FROM trips t
             JOIN routes r ON r.id = t.route_id
             WHERE t.bus_id = ?
               AND t.status IN ('scheduled', 'departed')
               AND (
                   (? BETWEEN t.departure_time AND COALESCE(t.arrival_time, DATE_ADD(t.departure_time, INTERVAL 2 HOUR)))
                   OR (? BETWEEN t.departure_time AND COALESCE(t.arrival_time, DATE_ADD(t.departure_time, INTERVAL 2 HOUR)))
                   OR (t.departure_time BETWEEN ? AND ?)
               )
             LIMIT 1",
            [$busId, $departure, $arrival, $departure, $arrival]
        );
    }

    private function delete(Request $request): View
    {
        $tripId = $request->integer('trip_id');
        $trip = Trip::with(['route', 'bus'])->find($tripId);

        if (! $trip) {
            ActivityLogger::log('trip_delete_failed', ['trip_id' => $tripId, 'reason' => 'not_found']);

            return $this->render(errors: ['general' => 'Trip not found.']);
        }

        $isCompleted = $trip->status === 'completed';

        if (! $isCompleted && $trip->bookings()->where('status', '!=', 'cancelled')->exists()) {
            ActivityLogger::log('trip_delete_blocked', ['trip_id' => $tripId, 'reason' => 'has_active_bookings']);

            return $this->render(errors: ['general' => 'Cannot delete trip. It has active bookings.']);
        }

        try {
            // Remaining bookings (all of them for a completed trip, only cancelled ones otherwise)
            // are archived first so the foreign key allows the trip to be deleted.
            DB::transaction(function () use ($trip) {
                $this->bookings->archive('trip_id', [$trip->id]);
                $trip->bookings()->delete();
                $trip->delete();
            });
        } catch (Throwable $exception) {
            ActivityLogger::log('trip_delete_failed', ['error' => $exception->getMessage(), 'trip_id' => $tripId]);

            return $this->render(errors: ['general' => 'Failed to delete trip: '.$exception->getMessage()]);
        }

        ActivityLogger::log('trip_delete', [
            'by_user_id' => $request->user()->id, 'trip_id' => $tripId, 'status' => $trip->status,
            'origin' => $trip->route?->origin, 'destination' => $trip->route?->destination,
            'plate_number' => $trip->bus?->plate_number, 'departure_time' => $trip->departure_time?->format('Y-m-d H:i:s'),
        ]);

        return $this->render(ok: 'Trip deleted successfully.'.($isCompleted ? ' Bookings have been archived.' : ''));
    }

    /**
     * @param  array<string, string>  $errors
     */
    private function render(string $ok = '', array $errors = []): View
    {
        return view('admin.trips', [
            'routes' => $this->rows("SELECT id, CONCAT(origin, ' → ', destination) AS name FROM routes WHERE status = 'active' ORDER BY origin, destination"),
            'buses' => $this->rows("SELECT id, CONCAT(plate_number, ' (', capacity, ')') AS name FROM buses WHERE status IN ('active', 'maintenance') ORDER BY plate_number"),
            'trips' => $this->rows("SELECT t.id,
                    CONCAT(r.origin, ' → ', r.destination) AS route_name,
                    b.plate_number,
                    DATE_FORMAT(t.departure_time, '%Y-%m-%d %H:%i') AS dep_time,
                    DATE_FORMAT(t.arrival_time, '%Y-%m-%d %H:%i') AS arr_time,
                    r.base_price AS price,
                    t.status,
                    t.created_at
                FROM trips t
                JOIN routes r ON r.id = t.route_id
                JOIN buses b ON b.id = t.bus_id
                ORDER BY t.departure_time DESC LIMIT 50"),
            'ok' => $ok,
            'formErrors' => $errors,
        ]);
    }
}
