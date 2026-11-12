<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Keeps trip/booking statuses in sync with the clock and archives old data.
 *
 * Trips move scheduled → departed → completed; confirmed bookings on completed trips
 * become completed. Once a day, completed trips older than 24 hours and cancelled
 * bookings are archived and removed.
 */
class MaintenanceService
{
    private const LAST_RUN_KEY = 'maintenance:last_run';

    private const INTERVAL_SECONDS = 86400;

    public function __construct(private BookingService $bookings) {}

    /**
     * @return array{success: bool, message: string, trips_to_departed: int, trips_to_completed: int}
     */
    public function updateTripStatuses(): array
    {
        try {
            [$departed, $completed] = DB::transaction(function (): array {
                $departed = DB::table('trips')
                    ->where('status', 'scheduled')
                    ->where('departure_time', '<', now())
                    ->update(['status' => 'departed', 'updated_at' => now()]);

                $completed = DB::table('trips')
                    ->where('status', 'departed')
                    ->whereNotNull('arrival_time')
                    ->where('arrival_time', '<', now())
                    ->update(['status' => 'completed', 'updated_at' => now()]);

                return [$departed, $completed];
            });
        } catch (Throwable $exception) {
            report($exception);

            return ['success' => false, 'message' => 'Status update failed: '.$exception->getMessage(), 'trips_to_departed' => 0, 'trips_to_completed' => 0];
        }

        return [
            'success' => true,
            'message' => "Status updates complete: {$departed} departed, {$completed} completed",
            'trips_to_departed' => $departed,
            'trips_to_completed' => $completed,
        ];
    }

    /**
     * @return array{success: bool, message: string, bookings_completed: int}
     */
    public function updateBookingStatuses(): array
    {
        $count = DB::table('bookings')
            ->where('status', 'confirmed')
            ->whereIn('trip_id', DB::table('trips')->select('id')->where('status', 'completed'))
            ->update(['status' => 'completed']);

        return [
            'success' => true,
            'message' => "Booking statuses updated: {$count} bookings marked as completed",
            'bookings_completed' => $count,
        ];
    }

    /**
     * Archive and delete completed trips (and their bookings) older than $daysOld days.
     * Pass 0 to clean every completed trip immediately.
     *
     * @return array{success: bool, message: string, trips_cleaned: int, bookings_archived: int}
     */
    public function cleanupCompletedTrips(int $daysOld = 1): array
    {
        $hoursOld = max(0, $daysOld * 24);

        try {
            [$tripsCleaned, $archived] = DB::transaction(function () use ($hoursOld): array {
                $cutoff = now()->subHours($hoursOld);

                $tripIds = DB::table('trips')
                    ->where('status', 'completed')
                    ->when($hoursOld > 0, function ($query) use ($cutoff) {
                        $query->where(function ($query) use ($cutoff) {
                            $query->where(fn ($q) => $q->whereNotNull('arrival_time')->where('arrival_time', '<', $cutoff))
                                ->orWhere(fn ($q) => $q->whereNull('arrival_time')->where('departure_time', '<', $cutoff->copy()->subHours(2)));
                        });
                    })
                    ->limit(100)
                    ->pluck('id')
                    ->all();

                if ($tripIds === []) {
                    return [0, 0];
                }

                $archived = $this->bookings->archive('trip_id', $tripIds);
                DB::table('bookings')->whereIn('trip_id', $tripIds)->delete();
                $tripsCleaned = DB::table('trips')->whereIn('id', $tripIds)->where('status', 'completed')->delete();

                return [$tripsCleaned, $archived];
            });
        } catch (Throwable $exception) {
            report($exception);

            return ['success' => false, 'message' => 'Cleanup failed: '.$exception->getMessage(), 'trips_cleaned' => 0, 'bookings_archived' => 0];
        }

        $message = "Cleanup complete: {$tripsCleaned} trips removed";

        if ($archived > 0) {
            $message .= ", {$archived} bookings archived";
        }

        return ['success' => true, 'message' => $message, 'trips_cleaned' => $tripsCleaned, 'bookings_archived' => $archived];
    }

    /**
     * @return array{success: bool, message: string, bookings_cleaned: int}
     */
    public function cleanupCancelledBookings(): array
    {
        try {
            $cleaned = DB::transaction(function (): int {
                $ids = DB::table('bookings')->where('status', 'cancelled')->pluck('id')->all();

                if ($ids === []) {
                    return 0;
                }

                $this->bookings->archive('id', $ids);

                return DB::table('bookings')->whereIn('id', $ids)->where('status', 'cancelled')->delete();
            });
        } catch (Throwable $exception) {
            report($exception);

            return ['success' => false, 'message' => 'Cancelled bookings cleanup failed: '.$exception->getMessage(), 'bookings_cleaned' => 0];
        }

        return ['success' => true, 'message' => "Cancelled bookings cleanup complete: {$cleaned} bookings removed", 'bookings_cleaned' => $cleaned];
    }

    /**
     * Status updates run on every call; archiving runs at most once per 24 hours unless forced.
     *
     * @return array<string, mixed>
     */
    public function runScheduled(bool $force = false): array
    {
        $this->updateTripStatuses();
        $bookingResult = $this->updateBookingStatuses();

        $lastRun = Cache::get(self::LAST_RUN_KEY);

        if (! $force && $lastRun !== null && time() - (int) $lastRun <= self::INTERVAL_SECONDS) {
            return $bookingResult;
        }

        $cleanupResult = $this->cleanupCompletedTrips(1);
        $this->cleanupCancelledBookings();
        Cache::forever(self::LAST_RUN_KEY, time());

        return $cleanupResult;
    }

    /**
     * @return array<string, int|string>
     */
    public function stats(): array
    {
        $tripCounts = DB::table('trips')->selectRaw('status, COUNT(*) as count')->groupBy('status')->pluck('count', 'status');

        $stats = [
            'scheduled_trips' => (int) ($tripCounts['scheduled'] ?? 0),
            'departed_trips' => (int) ($tripCounts['departed'] ?? 0),
            'completed_trips' => (int) ($tripCounts['completed'] ?? 0),
            'cancelled_trips' => (int) ($tripCounts['cancelled'] ?? 0),
            'completed_awaiting_cleanup' => (int) ($tripCounts['completed'] ?? 0),
            'completed_bookings' => DB::table('bookings')->where('status', 'completed')->count(),
            'cancelled_bookings' => DB::table('bookings')->where('status', 'cancelled')->count(),
            'total_bookings' => DB::table('bookings')->count(),
            'archived_bookings' => DB::table('bookings_archive')->count(),
            'last_maintenance_run' => 'Never',
            'next_maintenance_run' => 'Unknown',
        ];

        $lastRun = Cache::get(self::LAST_RUN_KEY);

        if ($lastRun !== null) {
            $stats['last_maintenance_run'] = Carbon::createFromTimestamp((int) $lastRun, config('app.timezone'))->format('Y-m-d H:i:s');
            $nextRun = (int) $lastRun + self::INTERVAL_SECONDS;

            $stats['next_maintenance_run'] = $nextRun > time()
                ? Carbon::createFromTimestamp($nextRun, config('app.timezone'))->format('Y-m-d H:i:s')
                : 'Ready to run (overdue by '.intdiv(time() - $nextRun, 3600).' hours)';
        }

        return $stats;
    }
}
