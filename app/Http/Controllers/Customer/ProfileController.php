<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Concerns\UpdatesProfile;
use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    use UpdatesProfile;

    public function index(Request $request): View
    {
        return $this->render($request);
    }

    public function store(Request $request): View
    {
        if ($request->input('action') !== 'update_profile') {
            return $this->render($request);
        }

        $user = $request->user();
        [$errors, $ok] = $this->updateProfile($request, $user);

        if ($ok) {
            ActivityLogger::log('customer_profile_update', [
                'by_user_id' => $user->id,
                'username' => $user->username,
                'user_type' => $user->user_type,
                'password_changed' => trim((string) $request->input('password')) !== '',
            ]);
        }

        return $this->render($request, $errors, $ok);
    }

    /**
     * @param  array<string, string>  $errors
     */
    private function render(Request $request, array $errors = [], string $ok = ''): View
    {
        $user = $request->user()->refresh();

        return view('customer.profile', [
            'profile' => $this->profileRow($user),
            'initials' => $user->initials(),
            'profileErrors' => $errors,
            'profileOk' => $ok,
        ]);
    }
}
