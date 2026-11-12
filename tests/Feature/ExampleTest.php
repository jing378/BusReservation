<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\BusRoute;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_renders_for_guests_with_an_empty_system(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Your journey starts')
            ->assertSee('No trips are scheduled right now.')
            ->assertSee(route('register'))
            ->assertSee(route('login'));
    }

    public function test_landing_page_lists_upcoming_trips_routes_and_seats_left(): void
    {
        $route = BusRoute::factory()->create(['origin' => 'Manila', 'destination' => 'Baguio', 'base_price' => 750]);
        $trip = Trip::factory()->for($route, 'route')->create(['price' => 750]);
        Booking::factory()->count(3)->for($trip)->create();
        Trip::factory()->arrived()->create(['status' => 'completed']);

        $this->get('/')
            ->assertOk()
            ->assertSee('Manila → Baguio')
            ->assertSee('₱750.00')
            ->assertSee('₱600.00 discounted')
            ->assertSee(($trip->bus->capacity - 3).' seats left')
            ->assertViewHas('departures', fn (array $departures) => count($departures) === 1);
    }

    public function test_landing_page_ships_data_for_the_interactive_widgets(): void
    {
        BusRoute::factory()->create(['origin' => 'Maasin City', 'destination' => 'Sogod', 'base_price' => 120]);

        $this->get('/')
            ->assertOk()
            ->assertSee('id="fareCalc"', false)
            ->assertSee('data-discount="20"', false)
            ->assertSee('id="rotator"', false)
            ->assertSee('id="routeSearch"', false)
            ->assertSee('data-search="maasin city sogod"', false)
            ->assertSee(asset('assets/js/landing.js'), false);
    }

    public function test_signed_in_users_get_a_dashboard_link(): void
    {
        $this->actingAs(User::factory()->create())->get('/')
            ->assertOk()
            ->assertSee(route('customer.dashboard'))
            ->assertDontSee('Create Free Account');

        $this->actingAs(User::factory()->admin()->create())->get('/')
            ->assertSee(route('admin.dashboard'));
    }
}
