<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ResetPasswordController extends Controller
{
    public function show(Request $request): Response
    {
        [$user, $message] = $this->resolveToken((string) $request->query('token', ''));

        return $this->render($request, $user, $message, 'error');
    }

    public function store(Request $request): Response
    {
        $token = (string) $request->query('token', $request->input('token', ''));
        [$user, $message] = $this->resolveToken($token);

        if (! $user) {
            return $this->render($request, null, $message, 'error');
        }

        $password = trim((string) $request->input('password'));
        $confirm = trim((string) $request->input('confirm'));

        $error = match (true) {
            strlen($password) < 8 => 'Password must be at least 8 characters long.',
            ! preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $password) => 'Password must contain at least one lowercase letter, one uppercase letter, and one number.',
            $confirm !== $password => 'Passwords do not match.',
            default => null,
        };

        if ($error) {
            return $this->render($request, $user, $error, 'error');
        }

        $user->forceFill(['password' => $password, 'reset_token' => null, 'reset_token_expiry' => null])->save();
        ActivityLogger::log('password_reset_completed', ['user_id' => $user->id]);

        return $this->render($request, $user, 'Password has been reset successfully! Redirecting to login...', 'success')
            ->header('Refresh', '2;url='.route('login'));
    }

    /**
     * @return array{0: User|null, 1: string}
     */
    private function resolveToken(string $token): array
    {
        if ($token === '') {
            return [null, 'No reset token provided.'];
        }

        $user = User::where('reset_token', $token)->first();

        if (! $user) {
            return [null, 'Invalid reset token. Please check the link and try again.'];
        }

        if (! $user->reset_token_expiry || $user->reset_token_expiry->isPast()) {
            return [null, 'Reset token has expired. Please request a new password reset.'];
        }

        return [$user, ''];
    }

    private function render(Request $request, ?User $user, string $message, string $messageType): Response
    {
        return response()->view('auth.reset-password', [
            'token' => (string) $request->query('token', $request->input('token', '')),
            'validToken' => $user !== null,
            'message' => $message,
            'messageType' => $messageType,
        ]);
    }
}
