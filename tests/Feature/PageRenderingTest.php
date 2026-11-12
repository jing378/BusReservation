<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PageRenderingTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->customer = User::factory()->create();

        $trip = Trip::factory()->create();
        $booking = Booking::factory()->for($trip)->for($this->customer)->create(['seat_number' => 3]);
        Payment::create(['booking_id' => $booking->id, 'amount' => 500, 'method' => 'cash', 'status' => 'paid', 'paid_at' => now()]);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function adminPages(): array
    {
        return collect(['dashboard', 'bookings', 'trips', 'routes', 'buses', 'users', 'logs', 'transactions', 'settings'])
            ->mapWithKeys(fn (string $page) => [$page => ['admin.'.$page]])
            ->all();
    }

    /**
     * @return array<string, array{string}>
     */
    public static function customerPages(): array
    {
        return collect(['dashboard', 'bookings', 'transactions', 'profile'])
            ->mapWithKeys(fn (string $page) => [$page => ['customer.'.$page]])
            ->all();
    }

    #[DataProvider('adminPages')]
    public function test_admin_pages_render(string $route): void
    {
        $this->actingAs($this->admin)->get(route($route))
            ->assertOk()
            ->assertSee('name="csrf-token"', false)
            ->assertDontSee('.php"', false);
    }

    #[DataProvider('customerPages')]
    public function test_customer_pages_render(string $route): void
    {
        $this->actingAs($this->customer)->get(route($route))
            ->assertOk()
            ->assertDontSee('.php"', false);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function signedInPages(): array
    {
        return array_merge(
            array_combine(array_map(fn ($key) => 'admin '.$key, array_keys(self::adminPages())), self::adminPages()),
            array_combine(array_map(fn ($key) => 'customer '.$key, array_keys(self::customerPages())), self::customerPages()),
        );
    }

    #[DataProvider('signedInPages')]
    public function test_signed_in_pages_load_the_mobile_layout(string $route): void
    {
        $user = str_starts_with($route, 'admin.') ? $this->admin : $this->customer;

        $this->actingAs($user)->get(route($route))
            ->assertOk()
            ->assertSee(asset('assets/css/mobile.css'), false)
            ->assertSee(asset('assets/js/mobile-responsive.js'), false);
    }

    public function test_sidebar_is_grouped_and_highlights_the_current_page(): void
    {
        $this->actingAs($this->admin)->get(route('admin.trips'))
            ->assertSeeInOrder(['Overview', 'Operations', 'Finance', 'Administration'])
            ->assertSee('<a href="'.route('admin.trips').'" class="active" title="Trips"', false)
            ->assertSee('aria-current="page"', false)
            ->assertSee($this->admin->full_name)
            ->assertDontSee('Notepad');

        $this->actingAs($this->customer)->get(route('customer.transactions'))
            ->assertSeeInOrder(['Overview', 'My Travel', 'Account'])
            ->assertSee('<a href="'.route('customer.transactions').'" class="active"', false)
            ->assertDontSee(route('admin.dashboard'));
    }

    public function test_admin_dashboard_shows_key_figures_and_upcoming_trips(): void
    {
        $this->actingAs($this->admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSeeInOrder(["Today's Bookings", 'Revenue This Month', 'Occupancy Today', 'Fleet In Service'])
            ->assertSeeInOrder(['Bookings &amp; Revenue', 'Weather Conditions', 'System Alerts', 'Available Buses', 'Today &amp; Upcoming Trips', 'Recent Bookings'], false)
            ->assertSee('class="seat-fill-bar"', false)
            ->assertSee("label: 'Revenue (₱)'", false);
    }

    public function test_customer_dashboard_uses_the_shared_grid_with_personal_figures(): void
    {
        // setUp gives this customer one confirmed booking on an upcoming trip, with a paid ₱500 payment.
        $this->actingAs($this->customer)->get(route('customer.dashboard'))
            ->assertOk()
            ->assertSee(asset('assets/css/dashboard-layout.css'), false)
            ->assertSeeInOrder(['Trips Available', 'My Upcoming Rides', 'My Bookings', 'Total Spent'])
            ->assertSee('₱500.00')
            ->assertSeeInOrder(['Available Trips', 'Weather Conditions', 'Popular Routes', 'Buses with Scheduled Trips', 'My Recent Bookings'])
            ->assertDontSee('<div class="main-layout"', false);
    }

    public function test_notepad_has_been_removed(): void
    {
        $this->actingAs($this->admin)->get('/admin/notepad')->assertNotFound();
    }

    public function test_booking_form_trip_ids_reach_javascript_as_strings(): void
    {
        // The seat picker matches trips with `trip.id === select.value`, so ids must be strings.
        $trip = Trip::first();

        $this->actingAs($this->customer)->get(route('customer.bookings'))
            ->assertOk()
            ->assertSee('"id":"'.$trip->id.'"', false);
    }

    #[DataProvider('adminPages')]
    public function test_customers_cannot_open_admin_pages(string $route): void
    {
        $this->actingAs($this->customer)->get(route($route))->assertRedirect(route('customer.dashboard'));
    }

    public function test_guests_are_sent_to_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('customer.dashboard'))->assertRedirect(route('login'));
    }

    public function test_auth_pages_render(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Sign In');
        $this->get(route('register'))->assertOk();
        $this->get(route('password.request'))->assertOk();
        $this->get(route('password.reset'))->assertOk()->assertSee('No reset token provided.');
    }

    public function test_dashboard_seat_map_marks_booked_seats(): void
    {
        $this->actingAs($this->customer)->get(route('customer.dashboard'))
            ->assertOk()
            ->assertSee('class="seat occupied" data-seat="3"', false);
    }

    public function test_users_table_ajax_partial_and_lookup_json(): void
    {
        $this->actingAs($this->admin)->get(route('admin.users', ['ajax' => 1, 'search' => $this->customer->username]))
            ->assertOk()
            ->assertSee($this->customer->full_name)
            ->assertDontSee('<html', false);

        $this->actingAs($this->admin)->get(route('admin.users', ['action' => 'get_user', 'id' => $this->customer->id]))
            ->assertOk()
            ->assertJson(['username' => $this->customer->username, 'is_active' => 1]);
    }

    public function test_lookup_endpoints(): void
    {
        $trip = Trip::first();

        $this->actingAs($this->admin)->getJson(route('api.check-seats', ['trip_id' => $trip->id]))->assertExactJson([3]);
        $this->actingAs($this->admin)->getJson(route('api.check-duplicate-bus', ['plate_number' => $trip->bus->plate_number]))
            ->assertExactJson(['isDuplicate' => true]);
        $this->actingAs($this->admin)->getJson(route('api.check-duplicate-user', ['email' => $this->customer->email]))
            ->assertJson(['isDuplicate' => true, 'duplicates' => ['email' => true]]);
        $this->actingAs($this->admin)->getJson(route('api.check-trip-bookings', ['trip_id' => $trip->id]))
            ->assertExactJson(['hasBookings' => true]);
        $this->actingAs($this->customer)->getJson(route('api.check-duplicate-bus', ['plate_number' => 'X']))->assertForbidden();
    }
}
