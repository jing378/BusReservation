<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function show(): View
    {
        return view('auth.register', ['formErrors' => [], 'success' => '']);
    }

    public function store(Request $request): View
    {
        $input = fn (string $key): string => trim((string) $request->input($key, ''));

        $fullName = $input('fullName');
        $email = $input('email');
        $phone = $input('phone');
        $username = $input('username');
        $userType = $input('userType');
        $password = $input('password');
        $confirm = $input('confirm');

        $errors = [];

        if ($fullName === '' || strlen($fullName) < 3) {
            $errors['fullName'] = 'Please enter your full name (minimum 3 characters required).';
        } elseif (strlen($fullName) > 120) {
            $errors['fullName'] = 'Full name must be less than 120 characters.';
        }

        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        } elseif (strlen($email) > 160) {
            $errors['email'] = 'Email address must be less than 160 characters.';
        }

        if (! preg_match('/^[0-9]{11}$/', $phone)) {
            $errors['phone'] = 'Please enter a valid 11-digit phone number.';
        }

        if ($username === '' || strlen($username) < 4) {
            $errors['username'] = 'Username must be at least 4 characters long.';
        } elseif (strlen($username) > 60) {
            $errors['username'] = 'Username must be less than 60 characters.';
        } elseif (! preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors['username'] = 'Username can only contain letters, numbers, and underscores.';
        }

        if (! in_array($userType, User::USER_TYPES, true)) {
            $errors['userType'] = 'Please select a valid user type.';
        }

        if ($password === '' || strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters long.';
        } elseif (strlen($password) > 128) {
            $errors['password'] = 'Password must be less than 128 characters.';
        } elseif (! preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $password)) {
            $errors['password'] = 'Password must contain at least one lowercase letter, one uppercase letter, and one number.';
        }

        if ($confirm !== $password) {
            $errors['confirm'] = 'Passwords do not match.';
        }

        if (! $request->has('newsletter')) {
            $errors['newsletter'] = 'Please subscribe to our newsletter to continue.';
        }

        if (! $request->has('terms')) {
            $errors['terms'] = 'You must accept the terms and conditions to continue.';
        }

        if ($errors) {
            ActivityLogger::log('user_register_validation_failed', [
                'action' => 'REGISTER', 'target_type' => 'user', 'attempted_username' => $username,
                'validation_errors' => array_keys($errors), 'status' => 'validation_failed',
                'email' => $email, 'phone' => $phone,
            ]);
        }

        if (! $errors && User::where('username', $username)->exists()) {
            $errors['username'] = 'Information already existed.';
        }

        if (! $errors && User::where('email', $email)->exists()) {
            $errors['email'] = 'Information already existed.';
        }

        if ($errors) {
            return view('auth.register', ['formErrors' => $errors, 'success' => '']);
        }

        User::create([
            'full_name' => $fullName,
            'email' => $email,
            'phone' => $phone,
            'username' => $username,
            'password' => $password,
            'user_type' => $userType,
            'newsletter_subscription' => true,
        ]);

        ActivityLogger::log('user_register', [
            'action' => 'REGISTER', 'target_type' => 'user', 'new_user' => $username, 'user_type' => $userType,
            'full_name' => $fullName, 'email' => $email, 'phone' => $phone,
        ]);

        // Clear the form fields, like the original page did.
        $request->replace([]);

        return view('auth.register', [
            'formErrors' => [],
            'success' => 'Registration successful! You can now sign in with your credentials.',
        ]);
    }
}
