<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Services\ActivityLogger;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        if ($request->query('action') === 'get_bus' && $request->integer('id') > 0) {
            $bus = Bus::select('id', 'plate_number', 'model', 'capacity', 'status')->find($request->integer('id'));

            return response()->json($bus ?? []);
        }

        return $this->render();
    }

    public function store(Request $request): View
    {
        return match ($request->input('action', 'create')) {
            'create' => $this->create($request),
            'update' => $this->update($request),
            'delete' => $this->delete($request),
            'toggle_status' => $this->toggleStatus($request),
            default => $this->render(),
        };
    }

    private function create(Request $request): View
    {
        [$plate, $model, $capacity, $errors] = $this->validated($request);

        if (! $errors && Bus::where('plate_number', $plate)->exists()) {
            $errors['plate_number'] = 'Information already existed.';
        }

        if ($errors) {
            return $this->render(errors: $errors);
        }

        try {
            Bus::create(['plate_number' => $plate, 'model' => $model, 'capacity' => $capacity, 'status' => 'active']);
        } catch (QueryException $exception) {
            ActivityLogger::log('bus_create_failed', ['error' => $exception->getMessage(), 'plate_number' => $plate]);

            return $this->render(errors: $this->duplicateOr($exception, 'Failed to add bus.'));
        }

        ActivityLogger::log('bus_create', ['by_user_id' => $request->user()->id, 'plate_number' => $plate, 'model' => $model, 'capacity' => $capacity]);

        return $this->render(ok: 'Bus added successfully.');
    }

    private function update(Request $request): View
    {
        $busId = $request->integer('bus_id');
        [$plate, $model, $capacity, $errors] = $this->validated($request);
        $status = in_array($request->input('status'), Bus::STATUSES, true) ? $request->input('status') : 'active';

        if ($busId <= 0) {
            $errors['general'] = 'Invalid bus ID provided.';
        }

        if (! $errors && Bus::where('plate_number', $plate)->whereKeyNot($busId)->exists()) {
            $errors['plate_number'] = 'Information already existed.';
        }

        if ($errors) {
            return $this->render(errors: $errors);
        }

        try {
            Bus::whereKey($busId)->update(['plate_number' => $plate, 'model' => $model, 'capacity' => $capacity, 'status' => $status]);
        } catch (QueryException $exception) {
            ActivityLogger::log('bus_update_failed', ['error' => $exception->getMessage(), 'bus_id' => $busId]);

            return $this->render(errors: $this->duplicateOr($exception, 'Failed to update bus.'));
        }

        ActivityLogger::log('bus_update', ['by_user_id' => $request->user()->id, 'bus_id' => $busId, 'plate_number' => $plate, 'status' => $status]);

        return $this->render(ok: 'Bus updated successfully.');
    }

    private function delete(Request $request): View
    {
        $bus = Bus::find($request->integer('bus_id'));

        if (! $bus) {
            return $this->render();
        }

        try {
            $bus->delete();
        } catch (QueryException $exception) {
            ActivityLogger::log('bus_delete_failed', ['error' => $exception->getMessage(), 'bus_id' => $bus->id]);

            return $this->render(errors: ['general' => 'Failed to delete bus. It may still have trips assigned.']);
        }

        ActivityLogger::log('bus_delete', ['by_user_id' => $request->user()->id, 'bus_id' => $bus->id, 'plate_number' => $bus->plate_number, 'model' => $bus->model]);

        return $this->render(ok: 'Bus deleted.');
    }

    private function toggleStatus(Request $request): View
    {
        $bus = Bus::find($request->integer('bus_id'));

        if (! $bus) {
            return $this->render();
        }

        $from = $bus->status;
        $bus->update(['status' => $from === 'active' ? 'inactive' : 'active']);
        ActivityLogger::log('bus_toggle_status', ['by_user_id' => $request->user()->id, 'bus_id' => $bus->id, 'from' => $from, 'to' => $bus->status]);

        return $this->render(ok: 'Bus status updated.');
    }

    /**
     * @return array{0: string, 1: string, 2: int, 3: array<string, string>}
     */
    private function validated(Request $request): array
    {
        $plate = trim((string) $request->input('plate_number'));
        $model = trim((string) $request->input('model'));
        $capacity = trim((string) $request->input('capacity'));
        $errors = [];

        if ($plate === '') {
            $errors['plate_number'] = 'Plate number is required.';
        } elseif (strlen($plate) < 3) {
            $errors['plate_number'] = 'Plate number must be at least 3 characters.';
        }

        if (! ctype_digit($capacity) || (int) $capacity < 1) {
            $errors['capacity'] = 'Capacity must be a positive integer.';
        }

        return [$plate, $model, (int) $capacity, $errors];
    }

    /**
     * @return array<string, string>
     */
    private function duplicateOr(QueryException $exception, string $fallback): array
    {
        return ($exception->errorInfo[1] ?? null) === 1062
            ? ['plate_number' => 'Information already existed.']
            : ['general' => $fallback];
    }

    /**
     * @param  array<string, string>  $errors
     */
    private function render(string $ok = '', array $errors = []): View
    {
        $buses = $this->rows('SELECT id, plate_number, model, capacity, status, created_at FROM buses ORDER BY created_at DESC');

        return view('admin.buses', ['buses' => $buses, 'ok' => $ok, 'formErrors' => $errors]);
    }
}
