<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Throwable;

/**
 * JSON-lines activity log read by the admin "Logs" page.
 */
class ActivityLogger
{
    public static function path(): string
    {
        return config('logging.activity_log');
    }

    /**
     * Append an event with request and user context. Never throws.
     *
     * @param  array<string, mixed>  $data
     */
    public static function log(string $event, array $data = []): void
    {
        try {
            $user = Auth::user();
            $request = request();

            $entry = array_merge([
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'event' => $event,
                'user_id' => $user?->id,
                'username' => $user?->username,
                'role' => $user?->role,
                'ip_address' => $request?->ip() ?? '-',
                'user_agent' => $request?->userAgent() ?? '-',
                'method' => $request?->method() ?? 'UNKNOWN',
            ], $data);

            File::ensureDirectoryExists(dirname(self::path()));
            file_put_contents(self::path(), json_encode($entry, JSON_UNESCAPED_SLASHES).PHP_EOL, FILE_APPEND | LOCK_EX);
        } catch (Throwable) {
            // Logging must never break the request.
        }
    }

    /**
     * Read all entries, newest first.
     *
     * @return list<array<string, mixed>>
     */
    public static function entries(): array
    {
        if (! is_file(self::path())) {
            return [];
        }

        $lines = array_reverse(file(self::path(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: []);
        $entries = [];

        foreach ($lines as $line) {
            $decoded = json_decode($line, true);

            if (is_array($decoded)) {
                $entries[] = $decoded;
            }
        }

        return $entries;
    }

    public static function clear(): void
    {
        if (is_file(self::path())) {
            file_put_contents(self::path(), '');
        }
    }

    public static function displayName(string $event): string
    {
        return match ($event) {
            'login_success' => '✓ Login Successful',
            'login_failed' => '✗ Login Failed',
            'login_failed_no_user' => '✗ Login Failed (No User)',
            'login_disabled_account' => '✗ Login Disabled Account',
            'login_missing_fields' => '✗ Login Missing Fields',
            'login_db_error' => '✗ Login Database Error',
            'logout' => '⊗ Logout',
            'user_register' => '+ User Registered',
            'user_register_failed' => '✗ Registration Failed',
            'user_register_validation_failed' => '✗ Registration Validation Failed',
            'user_create' => '+ User Created',
            'user_create_failed' => '✗ User Creation Failed',
            'user_update' => '✎ User Updated',
            'user_update_failed' => '✗ User Update Failed',
            'user_delete' => '✗ User Deleted',
            'user_delete_failed' => '✗ User Delete Failed',
            'booking_create' => '+ Booking Created',
            'booking_create_failed' => '✗ Booking Creation Failed',
            'booking_cancel' => '✗ Booking Cancelled',
            'booking_cancel_failed' => '✗ Booking Cancel Failed',
            'route_create' => '+ Route Created',
            'route_create_failed' => '✗ Route Creation Failed',
            'route_update' => '✎ Route Updated',
            'route_update_failed' => '✗ Route Update Failed',
            'route_delete' => '✗ Route Deleted',
            'route_delete_failed' => '✗ Route Delete Failed',
            'trip_create' => '+ Trip Created',
            'trip_create_failed' => '✗ Trip Creation Failed',
            'trip_update' => '✎ Trip Updated',
            'trip_update_failed' => '✗ Trip Update Failed',
            'trip_delete' => '✗ Trip Deleted',
            'trip_delete_failed' => '✗ Trip Delete Failed',
            'bus_create' => '+ Bus Created',
            'bus_create_failed' => '✗ Bus Creation Failed',
            'bus_update' => '✎ Bus Updated',
            'bus_update_failed' => '✗ Bus Update Failed',
            'bus_delete' => '✗ Bus Deleted',
            'bus_delete_failed' => '✗ Bus Delete Failed',
            'data_cleanup' => '🧹 Data Cleanup',
            default => e($event),
        };
    }

    /**
     * @return 'error'|'success'|'info'
     */
    public static function severity(string $event): string
    {
        if (str_contains($event, 'failed') || str_contains($event, 'error')) {
            return 'error';
        }

        return str_contains($event, 'success') ? 'success' : 'info';
    }
}
