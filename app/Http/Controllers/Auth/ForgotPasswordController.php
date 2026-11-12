<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Issues a one-hour reset token. There is no mail setup, so (as in the original app)
 * the reset link is shown on the page.
 */
class ForgotPasswordController extends Controller
{
    public function show(): View
    {
        return view('auth.forgot-password', ['message' => '', 'messageType' => 'info']);
    }

    public function store(Request $request): View
    {
        $email = trim((string) $request->input('email'));

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->render('Please enter a valid email address.', 'error');
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            ActivityLogger::log('password_reset_failed', ['email' => $email, 'reason' => 'user_not_found']);

            return $this->render('No account found with this email address. Please check and try again, or register a new account.', 'error');
        }

        $token = bin2hex(random_bytes(32));
        $user->forceFill(['reset_token' => $token, 'reset_token_expiry' => now()->addHour()])->save();

        ActivityLogger::log('password_reset_requested', ['user_id' => $user->id, 'username' => $user->username, 'email' => $email]);

        $resetLink = e(route('password.reset', ['token' => $token]));

        return $this->render(
            'Password reset instructions have been sent to your email. If this is a real application, please check your inbox.'
            .'<br><br><div style="background:#2a3441;padding:10px;border-radius:8px;margin-top:10px;font-size:12px;color:#94a3b8;">Reset Link: '
            .'<a href="'.$resetLink.'" style="color:#3b82f6;word-break:break-all;">'.$resetLink.'</a></div>',
            'success'
        );
    }

    private function render(string $message, string $messageType): View
    {
        return view('auth.forgot-password', ['message' => $message, 'messageType' => $messageType]);
    }
}
