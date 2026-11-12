<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\UpdatesProfile;
use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use App\Services\MaintenanceService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    use UpdatesProfile;

    public function __construct(private MaintenanceService $maintenance) {}

    public function index(Request $request): View
    {
        return $this->render($request);
    }

    public function store(Request $request): View
    {
        return match ($request->input('action')) {
            'cleanup' => $this->cleanup($request),
            'update_profile' => $this->saveProfile($request),
            default => $this->render($request),
        };
    }

    /**
     * Archive every completed trip and cancelled booking right now.
     */
    private function cleanup(Request $request): View
    {
        $result = $this->maintenance->cleanupCompletedTrips(daysOld: 0);
        $this->maintenance->cleanupCancelledBookings();

        ActivityLogger::log('data_cleanup', ['trips_cleaned' => $result['trips_cleaned'], 'bookings_archived' => $result['bookings_archived']]);

        return $this->render($request, cleanupResult: $result);
    }

    private function saveProfile(Request $request): View
    {
        [$errors, $ok] = $this->updateProfile($request, $request->user());

        return $this->render($request, errors: $errors, ok: $ok);
    }

    /**
     * @param  array<string, string>  $errors
     * @param  array<string, mixed>|null  $cleanupResult
     */
    private function render(Request $request, array $errors = [], string $ok = '', ?array $cleanupResult = null): View
    {
        $user = $request->user()->refresh();

        return view('admin.settings', [
            'username' => $user->username,
            'profile' => $this->profileRow($user),
            'initials' => $user->initials(),
            'profileErrors' => $errors,
            'profileOk' => $ok,
            'cleanupResult' => $cleanupResult,
            'cleanupStats' => $this->maintenance->stats(),
        ]);
    }
}
