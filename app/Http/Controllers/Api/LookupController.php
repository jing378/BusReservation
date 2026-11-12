<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Bus;
use App\Models\BusRoute;
use App\Models\Trip;
use App\Models\User;
use App\Services\WeatherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Small JSON endpoints called by the pages' JavaScript (duplicate checks, seat maps, search).
 */
class LookupController extends Controller
{
    public function weather(Request $request, WeatherService $weather): JsonResponse
    {
        if ($request->hasAny(['latitude', 'longitude'])) {
            $validated = $request->validate([
                'latitude' => ['required', 'numeric', 'between:-90,90'],
                'longitude' => ['required', 'numeric', 'between:-180,180'],
            ]);

            return response()->json($weather->forCoordinates(
                (float) $validated['latitude'],
                (float) $validated['longitude'],
            ));
        }

        $city = trim((string) $request->query('city', ''));

        if ($city === '') {
            return response()->json(['success' => false, 'error' => 'City parameter is required']);
        }

        return response()->json($weather->forCity($city));
    }

    public function checkSeats(Request $request): JsonResponse
    {
        $tripId = (string) $request->query('trip_id', '');

        if (! ctype_digit($tripId)) {
            return response()->json(['error' => 'Invalid trip ID'], 400);
        }

        return response()->json(Booking::where('trip_id', (int) $tripId)
            ->whereIn('status', Booking::ACTIVE_STATUSES)
            ->pluck('seat_number')
            ->map(fn ($seat) => (int) $seat)
            ->values());
    }

    public function checkDuplicateBus(Request $request): JsonResponse
    {
        $plate = trim((string) $request->query('plate_number', ''));

        $isDuplicate = $plate !== '' && Bus::where('plate_number', $plate)
            ->when($request->integer('bus_id') > 0, fn ($query) => $query->whereKeyNot($request->integer('bus_id')))
            ->exists();

        return response()->json(['isDuplicate' => $isDuplicate]);
    }

    public function checkDuplicateRoute(Request $request): JsonResponse
    {
        $origin = trim((string) $request->query('origin', ''));
        $destination = trim((string) $request->query('destination', ''));

        $isDuplicate = $origin !== '' && $destination !== '' && BusRoute::where('origin', $origin)
            ->where('destination', $destination)
            ->when($request->integer('route_id') > 0, fn ($query) => $query->whereKeyNot($request->integer('route_id')))
            ->exists();

        return response()->json(['isDuplicate' => $isDuplicate]);
    }

    public function checkDuplicateTrip(Request $request): JsonResponse
    {
        $routeId = $request->integer('route_id');
        $busId = $request->integer('bus_id');
        $departure = trim((string) $request->query('departure_time', ''));

        $isDuplicate = $routeId > 0 && $busId > 0 && $departure !== '' && Trip::where('route_id', $routeId)
            ->where('bus_id', $busId)
            ->where('departure_time', $departure)
            ->whereIn('status', ['scheduled', 'departed'])
            ->exists();

        return response()->json(['isDuplicate' => $isDuplicate]);
    }

    public function checkDuplicateUser(Request $request): JsonResponse
    {
        $ignoreId = $request->integer('user_id');
        $taken = fn (string $column, string $value): bool => User::where($column, $value)
            ->when($ignoreId > 0, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists();

        $email = trim((string) $request->query('email', ''));
        $username = trim((string) $request->query('username', ''));
        $phone = trim((string) $request->query('phone', ''));

        $duplicates = array_filter([
            'email' => $email !== '' && $taken('email', $email),
            'username' => $username !== '' && $taken('username', $username),
            'phone' => preg_match('/^[0-9]{11}$/', $phone) === 1 && $taken('phone', $phone),
        ]);

        return response()->json(['isDuplicate' => $duplicates !== [], 'duplicates' => (object) $duplicates]);
    }

    public function checkRouteTrips(Request $request): JsonResponse
    {
        $routeId = $request->integer('route_id');

        if ($routeId <= 0) {
            return response()->json(['hasTrips' => false, 'tripCount' => 0, 'error' => 'Invalid route ID']);
        }

        $count = Trip::where('route_id', $routeId)->count();

        return response()->json(['hasTrips' => $count > 0, 'tripCount' => $count]);
    }

    public function checkTripBookings(Request $request): JsonResponse
    {
        $tripId = $request->integer('trip_id');

        if ($tripId <= 0) {
            return response()->json(['hasBookings' => false, 'error' => 'Invalid trip ID']);
        }

        return response()->json([
            'hasBookings' => Booking::where('trip_id', $tripId)->whereIn('status', Booking::ACTIVE_STATUSES)->exists(),
        ]);
    }

    /**
     * Search registered customers plus past walk-in passenger names.
     */
    public function fetchUsers(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('search', ''));

        if ($search === '') {
            return response()->json([]);
        }

        $term = "%{$search}%";

        $users = User::where('full_name', 'like', $term)->orWhere('email', 'like', $term)
            ->get(['id', 'full_name', 'email', 'phone', 'user_type'])
            ->map(fn (User $user): array => [
                'id' => $user->id, 'name' => $user->full_name, 'email' => $user->email,
                'phone' => $user->phone, 'user_type' => $user->user_type,
            ]);

        $walkIns = Booking::query()
            ->where('passenger_name', 'like', $term)
            ->whereNotIn('passenger_name', User::select('full_name'))
            ->orderBy('passenger_name')
            ->distinct()
            ->get(['passenger_name', 'passenger_phone'])
            ->map(fn (Booking $booking): array => [
                'name' => $booking->passenger_name, 'phone' => $booking->passenger_phone, 'user_type' => 'Regular',
            ]);

        return response()->json($users->concat($walkIns)->unique('name')->values());
    }
}
