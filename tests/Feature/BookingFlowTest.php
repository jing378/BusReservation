<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Bus;
use App\Models\BusRoute;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_books_a_seat_and_gets_a_receipt(): void
    {
        $customer = User::factory()->create();
        $trip = Trip::factory()->create(['price' => 500]);

        $this->actingAs($customer)->post(route('customer.bookings.store'), [
            'trip_id' => $trip->id, 'seat_number' => 5, 'amount' => 500,
        ])->assertOk()->assertSee('Booking created: BK-')->assertSee('generateReceipt(', false);

        $booking = Booking::sole();
        $this->assertSame('confirmed', $booking->status);
        $this->assertSame($customer->full_name, $booking->passenger_name);
        $this->assertSame('paid', $booking->payments()->sole()->status);
    }

    public function test_discount_lowers_minimum_amount_for_students(): void
    {
        $student = User::factory()->student()->create();
        $regular = User::factory()->create();
        $trip = Trip::factory()->create(['price' => 500]);

        $this->actingAs($regular)->post(route('customer.bookings.store'), ['trip_id' => $trip->id, 'seat_number' => 1, 'amount' => 400])
            ->assertSee('Amount must be at least ₱500.00');

        $this->actingAs($student)->post(route('customer.bookings.store'), ['trip_id' => $trip->id, 'seat_number' => 1, 'amount' => 400])
            ->assertSee('Booking created');
    }

    public function test_a_taken_seat_cannot_be_booked_until_cancelled(): void
    {
        $customer = User::factory()->create();
        $trip = Trip::factory()->create();
        $existing = Booking::factory()->for($trip)->create(['seat_number' => 7]);

        $this->actingAs($customer)->post(route('customer.bookings.store'), ['trip_id' => $trip->id, 'seat_number' => 7, 'amount' => 500])
            ->assertSee('This seat is already booked.');

        $existing->update(['status' => 'cancelled']);

        $this->actingAs($customer)->post(route('customer.bookings.store'), ['trip_id' => $trip->id, 'seat_number' => 7, 'amount' => 500])
            ->assertSee('Booking created');
    }

    public function test_customer_can_cancel_only_their_own_booking(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $booking = Booking::factory()->for($owner)->create();
        $booking->payments()->create(['amount' => 500, 'method' => 'cash', 'status' => 'paid', 'paid_at' => now()]);

        $this->actingAs($other)->post(route('customer.bookings.store'), ['cancel_booking' => 1, 'booking_id' => $booking->id])
            ->assertSee('Booking not found or you do not have permission to cancel it.');
        $this->assertSame('confirmed', $booking->refresh()->status);

        $this->actingAs($owner)->post(route('customer.bookings.store'), ['cancel_booking' => 1, 'booking_id' => $booking->id])
            ->assertSee('has been cancelled. Refund has been processed.');
        $this->assertSame('cancelled', $booking->refresh()->status);
        $this->assertSame('refunded', $booking->payments()->sole()->status);
    }

    public function test_admin_books_for_a_customer(): void
    {
        $admin = User::factory()->admin()->create();
        $customer = User::factory()->create();
        $trip = Trip::factory()->create();

        $this->actingAs($admin)->post(route('admin.bookings.store'), [
            'trip_id' => $trip->id, 'seat_number' => 2, 'amount' => 500, 'user_id' => $customer->id,
        ])->assertSee('Booking created');

        $this->assertSame($customer->id, Booking::sole()->user_id);
    }

    public function test_customer_delete_all_only_removes_their_own_payments(): void
    {
        $customer = User::factory()->create();
        $mine = Booking::factory()->for($customer)->create();
        $theirs = Booking::factory()->create();
        $mine->payments()->create(['amount' => 500, 'method' => 'cash', 'status' => 'paid']);
        $theirs->payments()->create(['amount' => 500, 'method' => 'cash', 'status' => 'paid']);

        $this->actingAs($customer)->post(route('customer.transactions.store'), ['action' => 'delete_all_transactions'])
            ->assertRedirect(route('customer.transactions', ['deleted_all' => 1]));

        $this->assertSame(0, $mine->payments()->count());
        $this->assertSame(1, $theirs->payments()->count());
    }

    public function test_admin_manages_buses_routes_and_trips(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.buses.store'), ['action' => 'create', 'plate_number' => 'ABC-1234', 'model' => 'Hino', 'capacity' => 45])
            ->assertSee('Bus added successfully.');
        $this->actingAs($admin)->post(route('admin.buses.store'), ['action' => 'create', 'plate_number' => 'ABC-1234', 'capacity' => 45])
            ->assertSee('Information already existed.');

        $this->actingAs($admin)->post(route('admin.routes.store'), ['action' => 'create', 'origin' => 'Manila', 'destination' => 'Baguio', 'base_price' => 750])
            ->assertSee('Route added successfully.');

        $bus = Bus::sole();
        $route = BusRoute::sole();
        $departure = now()->addDay()->setTime(9, 0);

        $this->actingAs($admin)->post(route('admin.trips.store'), [
            'action' => 'create', 'route_id' => $route->id, 'bus_id' => $bus->id,
            'departure_time' => $departure->format('Y-m-d\TH:i'), 'arrival_time' => $departure->copy()->addHours(6)->format('Y-m-d\TH:i'),
        ])->assertSee('Trip added successfully with route price.');

        $this->assertEquals(750, Trip::sole()->price);

        $this->actingAs($admin)->post(route('admin.trips.store'), [
            'action' => 'create', 'route_id' => $route->id, 'bus_id' => $bus->id,
            'departure_time' => $departure->copy()->addHours(2)->format('Y-m-d\TH:i'),
        ])->assertSee('Scheduling conflict');

        $this->actingAs($admin)->post(route('admin.routes.store'), ['action' => 'delete', 'route_id' => $route->id])
            ->assertSee('Cannot delete route. It has associated trips.');
    }
}
