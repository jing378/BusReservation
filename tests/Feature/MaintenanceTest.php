<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Trip;
use App\Services\MaintenanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MaintenanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_trips_and_bookings_follow_the_clock(): void
    {
        $departed = Trip::factory()->departed()->create();
        $arrived = Trip::factory()->arrived()->create();
        $booking = Booking::factory()->for($arrived)->create();

        // Bookings can't be inserted on completed trips, so the arrived trip starts out scheduled.
        app(MaintenanceService::class)->updateTripStatuses();
        app(MaintenanceService::class)->updateTripStatuses();
        app(MaintenanceService::class)->updateBookingStatuses();

        $this->assertSame('departed', $departed->refresh()->status);
        $this->assertSame('completed', $arrived->refresh()->status);
        $this->assertSame('completed', $booking->refresh()->status);
    }

    public function test_immediate_cleanup_archives_completed_trips(): void
    {
        $trip = Trip::factory()->arrived()->create();
        Booking::factory()->for($trip)->create();
        $trip->update(['status' => 'completed']);

        $result = app(MaintenanceService::class)->cleanupCompletedTrips(daysOld: 0);

        $this->assertSame(1, $result['trips_cleaned']);
        $this->assertSame(1, $result['bookings_archived']);
        $this->assertDatabaseMissing('trips', ['id' => $trip->id]);
        $this->assertSame(1, DB::table('bookings_archive')->count());
    }

    public function test_artisan_command_runs(): void
    {
        $this->artisan('bus:maintenance', ['--force' => true])->assertSuccessful();
    }
}
