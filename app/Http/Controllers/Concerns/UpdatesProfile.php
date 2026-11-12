<?php

namespace App\Http\Controllers\Concerns;

use App\Models\User;
use Illuminate\Http\Request;

/**
 * Profile editing shared by the admin settings page and the customer profile page.
 */
trait UpdatesProfile
{
    /**
     * @param  array<int|string, mixed>  $bindings
     * @return list<array<string, mixed>>
     */
    abstract protected function rows(string $sql, array $bindings = []): array;

    /**
     * Validate and save the submitted profile fields.
     *
     * @return array{0: array<string, string>, 1: string} [errors, success message]
     */
    protected function updateProfile(Request $request, User $user): array
    {
        $input = fn (string $key): string => trim((string) $request->input($key, ''));

        $fullName = $input('full_name');
        $email = $input('email');
        $phone = $input('phone');
        $username = $input('username');
        $userType = in_array($input('user_type'), User::USER_TYPES, true) ? $input('user_type') : $user->user_type;
        $password = $input('password');
        $confirm = $input('confirm_password');
        $errors = [];

        if ($fullName === '') {
            $errors['full_name'] = 'Full name is required.';
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Valid email required.';
        }

        if (strlen($username) < 3) {
            $errors['username'] = 'Username must be at least 3 characters.';
        }

        if ($phone !== '' && ! preg_match('/^[0-9\-\+ ]{7,15}$/', $phone)) {
            $errors['phone'] = 'Invalid phone number.';
        }

        if ($password !== '' || $confirm !== '') {
            if (strlen($password) < 6) {
                $errors['password'] = 'Password must be at least 6 characters.';
            }

            if ($password !== $confirm) {
                $errors['confirm_password'] = 'Passwords do not match.';
            }
        }

        if (! $errors && User::where('username', $username)->whereKeyNot($user->id)->exists()) {
            $errors['username'] = 'Information already existed.';
        }

        if (! $errors && User::where('email', $email)->whereKeyNot($user->id)->exists()) {
            $errors['email'] = 'Information already existed.';
        }

        if ($errors) {
            return [$errors, ''];
        }

        $attributes = [
            'full_name' => $fullName,
            'email' => $email,
            'phone' => $phone,
            'username' => $username,
            'user_type' => $userType,
        ];

        if ($password !== '') {
            $attributes['password'] = $password;
        }

        $user->update($attributes);

        return [[], 'Profile updated successfully.'];
    }

    /**
     * The profile row as the views expect it (raw column values).
     *
     * @return array<string, mixed>|null
     */
    protected function profileRow(User $user): ?array
    {
        return $this->rows('SELECT id, full_name, email, phone, username, role, user_type, is_active, created_at, updated_at
            FROM users WHERE id = ? LIMIT 1', [$user->id])[0] ?? null;
    }
}
