<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class LoginController extends Controller
{
    private const REMEMBER_COOKIE = 'demo_username';

    public function show(): View
    {
        return view('auth.login', ['message' => '', 'messageType' => 'success']);
    }

    /**
     * Log in by username or email. On success the page shows a message and redirects by role via JavaScript.
     */
    public function store(Request $request): View
    {
        $login = trim((string) $request->input('username'));
        $password = trim((string) $request->input('password'));

        if ($login === '' || $password === '') {
            ActivityLogger::log('login_missing_fields', ['attempted_username' => $login]);

            return $this->fail('Please enter both username/email and password.');
        }

        $user = User::where('username', $login)->orWhere('email', $login)->first();

        if (! $user) {
            ActivityLogger::log('login_failed_no_user', ['attempted_username' => $login, 'reason' => 'user_not_found']);

            return $this->fail('Invalid username/email or password.');
        }

        if (! $user->is_active) {
            ActivityLogger::log('login_disabled_account', ['attempted_username' => $login, 'user_id' => $user->id]);

            return $this->fail('Account is disabled. Contact admin.');
        }

        if (! Hash::check($password, $user->password)) {
            ActivityLogger::log('login_failed', ['attempted_username' => $login, 'reason' => 'invalid_password']);

            return $this->fail('Invalid username/email or password.');
        }

        $remember = $request->boolean('remember');
        Auth::login($user, $remember);
        $request->session()->regenerate();

        Cookie::queue($remember
            ? Cookie::make(self::REMEMBER_COOKIE, $user->username, 60 * 24 * 7)
            : Cookie::forget(self::REMEMBER_COOKIE));

        ActivityLogger::log('login_success', ['action' => 'LOGIN', 'username' => $user->username, 'role' => $user->role]);

        return view('auth.login', ['message' => 'Login successful! Redirecting...', 'messageType' => 'success']);
    }

    /**
     * POST-only logout. The "remember me" username cookie is kept so the login form stays prefilled.
     */
    public function destroy(Request $request): RedirectResponse
    {
        ActivityLogger::log('logout', ['action' => 'LOGOUT']);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('logged_out', true);
    }

    private function fail(string $message): View
    {
        return view('auth.login', ['message' => $message, 'messageType' => 'error']);
    }
}
