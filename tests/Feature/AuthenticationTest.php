<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_log_in_with_username_or_email(): void
    {
        $user = User::factory()->create(['username' => 'juan', 'email' => 'juan@example.com']);

        $this->post(route('login.store'), ['username' => 'juan', 'password' => 'password'])
            ->assertOk()
            ->assertSee('Login successful! Redirecting...');
        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();

        $this->post(route('login.store'), ['username' => 'juan@example.com', 'password' => 'password'])->assertOk();
        $this->assertAuthenticatedAs($user);
    }

    public function test_logout_requires_post_and_shows_a_notice(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/logout')->assertMethodNotAllowed();
        $this->assertAuthenticatedAs($user);

        $this->actingAs($user)->followingRedirects()->post(route('logout'))
            ->assertOk()
            ->assertSee('You have been logged out.');
        $this->assertGuest();
    }

    public function test_logout_with_an_expired_session_goes_to_login(): void
    {
        $request = Request::create(route('logout'), 'POST');
        $request->setRouteResolver(fn () => app('router')->getRoutes()->match($request));

        $response = app(ExceptionHandler::class)->render($request, new TokenMismatchException);

        $this->assertTrue($response->isRedirect(route('login')));
    }

    public function test_pages_post_logout_through_the_confirm_modal(): void
    {
        $this->actingAs(User::factory()->admin()->create())->get(route('admin.dashboard'))
            ->assertSee('<form method="POST" action="'.route('logout').'"', false)
            ->assertSee('<a class="logout" href="#" role="button">', false);
    }

    public function test_login_and_register_have_a_back_link_to_the_landing_page(): void
    {
        $backLink = '<a href="'.route('home').'" class="back-link"';

        $this->get(route('login'))->assertOk()->assertSee($backLink, false);
        $this->get(route('register'))->assertOk()->assertSee($backLink, false);
    }

    public function test_wrong_password_and_disabled_accounts_are_rejected(): void
    {
        User::factory()->create(['username' => 'juan']);
        User::factory()->inactive()->create(['username' => 'pedro']);

        $this->post(route('login.store'), ['username' => 'juan', 'password' => 'wrong'])
            ->assertSee('Invalid username/email or password.');
        $this->post(route('login.store'), ['username' => 'pedro', 'password' => 'password'])
            ->assertSee('Account is disabled. Contact admin.');

        $this->assertGuest();
    }

    public function test_successful_login_redirects_by_role(): void
    {
        $admin = User::factory()->admin()->create(['username' => 'boss']);

        $this->post(route('login.store'), ['username' => 'boss', 'password' => 'password'])
            ->assertSee(route('admin.dashboard'), false);
    }

    public function test_registration_validates_and_creates_customer(): void
    {
        $this->post(route('register.store'), ['fullName' => 'Al'])
            ->assertOk()
            ->assertSee('Please enter your full name (minimum 3 characters required).');

        $this->post(route('register.store'), [
            'fullName' => 'Maria Clara',
            'email' => 'maria@example.com',
            'phone' => '09171234567',
            'username' => 'maria_c',
            'userType' => 'Student',
            'password' => 'Secret123',
            'confirm' => 'Secret123',
            'newsletter' => 'on',
            'terms' => 'on',
        ])->assertOk()->assertSee('Registration successful!');

        $user = User::where('username', 'maria_c')->firstOrFail();
        $this->assertSame('customer', $user->role);
        $this->assertSame('Student', $user->user_type);
    }

    public function test_password_reset_flow(): void
    {
        $user = User::factory()->create(['email' => 'reset@example.com']);

        $this->post(route('password.email'), ['email' => 'reset@example.com'])->assertSee('Reset Link:');
        $token = $user->refresh()->reset_token;
        $this->assertNotNull($token);

        $this->post(route('password.update', ['token' => $token]), ['password' => 'NewPass123', 'confirm' => 'NewPass123'])
            ->assertSee('Password has been reset successfully!')
            ->assertHeader('Refresh');

        $this->post(route('login.store'), ['username' => $user->username, 'password' => 'NewPass123']);
        $this->assertAuthenticatedAs($user);
    }
}
