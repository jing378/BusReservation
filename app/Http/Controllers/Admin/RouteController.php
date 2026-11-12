<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusRoute;
use App\Services\ActivityLogger;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RouteController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        if ($request->query('action') === 'get_route' && $request->integer('id') > 0) {
            $route = BusRoute::select('id', 'origin', 'destination', 'distance_km', 'base_price', 'status')->find($request->integer('id'));

            return response()->json($route ?? []);
        }

        return $this->render();
    }

    public function store(Request $request): View
    {
        return match ($request->input('action')) {
            'create' => $this->create($request),
            'update' => $this->update($request),
            'toggle_status' => $this->toggleStatus($request),
            'delete' => $this->delete($request),
            default => $this->render(),
        };
    }

    private function create(Request $request): View
    {
        [$attributes, $errors] = $this->validated($request);

        if (! $errors && BusRoute::where('origin', $attributes['origin'])->where('destination', $attributes['destination'])->exists()) {
            $errors['general'] = 'Information already existed.';
        }

        if ($errors) {
            return $this->render(errors: $errors);
        }

        try {
            BusRoute::create($attributes + ['status' => 'active']);
        } catch (QueryException $exception) {
            ActivityLogger::log('route_create_failed', ['error' => $exception->getMessage()]);

            return $this->render(errors: ['general' => 'Failed to add route.']);
        }

        ActivityLogger::log('route_create', ['by_user_id' => $request->user()->id, 'origin' => $attributes['origin'], 'destination' => $attributes['destination']]);

        return $this->render(ok: 'Route added successfully.');
    }

    private function update(Request $request): View
    {
        $routeId = $request->integer('route_id');
        [$attributes, $errors] = $this->validated($request);
        $attributes['status'] = in_array($request->input('status'), ['active', 'inactive'], true) ? $request->input('status') : 'active';

        if (! $errors && $routeId > 0 && BusRoute::where('origin', $attributes['origin'])
            ->where('destination', $attributes['destination'])->whereKeyNot($routeId)->exists()) {
            $errors['general'] = 'Information already existed.';
        }

        if ($errors || $routeId <= 0) {
            return $this->render(errors: $errors);
        }

        try {
            BusRoute::whereKey($routeId)->update($attributes);
        } catch (QueryException $exception) {
            ActivityLogger::log('route_update_failed', ['error' => $exception->getMessage(), 'route_id' => $routeId]);

            return $this->render(errors: ['general' => 'Failed to update route.']);
        }

        ActivityLogger::log('route_update', ['by_user_id' => $request->user()->id, 'route_id' => $routeId]);

        return $this->render(ok: 'Route updated successfully.');
    }

    private function toggleStatus(Request $request): View
    {
        $route = BusRoute::find($request->integer('route_id'));

        if (! $route) {
            return $this->render();
        }

        $route->update(['status' => $route->status === 'active' ? 'inactive' : 'active']);
        ActivityLogger::log('route_toggle', ['route_id' => $route->id, 'to' => $route->status]);

        return $this->render(ok: 'Route status updated.');
    }

    private function delete(Request $request): View
    {
        $route = BusRoute::find($request->integer('route_id'));

        if (! $route) {
            return $this->render();
        }

        if ($route->trips()->exists()) {
            return $this->render(errors: ['general' => 'Cannot delete route. It has associated trips.']);
        }

        try {
            $route->delete();
        } catch (QueryException $exception) {
            ActivityLogger::log('route_delete_failed', ['error' => $exception->getMessage(), 'route_id' => $route->id]);

            return $this->render(errors: ['general' => 'Failed to delete route.']);
        }

        ActivityLogger::log('route_delete', [
            'by_user_id' => $request->user()->id, 'route_id' => $route->id,
            'origin' => $route->origin, 'destination' => $route->destination,
        ]);

        return $this->render(ok: 'Route deleted successfully.');
    }

    /**
     * @return array{0: array{origin: string, destination: string, distance_km: float|null, base_price: float}, 1: array<string, string>}
     */
    private function validated(Request $request): array
    {
        $origin = trim((string) $request->input('origin'));
        $destination = trim((string) $request->input('destination'));
        $distance = trim((string) $request->input('distance_km'));
        $basePrice = trim((string) $request->input('base_price'));
        $errors = [];

        if ($origin === '') {
            $errors['origin'] = 'Origin is required.';
        } elseif (strlen($origin) < 2) {
            $errors['origin'] = 'Origin must be at least 2 characters.';
        }

        if ($destination === '') {
            $errors['destination'] = 'Destination is required.';
        } elseif (strlen($destination) < 2) {
            $errors['destination'] = 'Destination must be at least 2 characters.';
        }

        if (! is_numeric($basePrice) || (float) $basePrice < 0) {
            $errors['base_price'] = 'Base price must be a non-negative number.';
        }

        if ($distance !== '' && ! is_numeric($distance)) {
            $errors['distance_km'] = 'Distance must be a number.';
        }

        return [[
            'origin' => $origin,
            'destination' => $destination,
            'distance_km' => $distance === '' ? null : (float) $distance,
            'base_price' => (float) $basePrice,
        ], $errors];
    }

    /**
     * @param  array<string, string>  $errors
     */
    private function render(string $ok = '', array $errors = []): View
    {
        $routes = $this->rows('SELECT id, origin, destination, distance_km, base_price, status, created_at
            FROM routes ORDER BY status DESC, origin ASC, destination ASC');

        return view('admin.routes', ['routes' => $routes, 'ok' => $ok, 'formErrors' => $errors]);
    }
}
