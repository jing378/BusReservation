<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class UserController extends Controller
{
    private const PER_PAGE = 10;

    private const SORTABLE = ['id', 'full_name', 'email', 'username', 'role', 'is_active', 'created_at'];

    public function index(Request $request): View|JsonResponse|Response
    {
        if ($request->query('action') === 'get_user' && $request->integer('id') > 0) {
            $user = User::select('id', 'full_name', 'email', 'phone', 'username', 'role', 'user_type', 'is_active')
                ->find($request->integer('id'));

            return response()->json($user ? $user->toArray() + ['is_active' => (int) $user->is_active] : []);
        }

        if ($request->query('ajax') == '1') {
            return response()->view('admin.partials.users-table', $this->listing($request));
        }

        return $this->render($request);
    }

    public function store(Request $request): View
    {
        return match ($request->input('action')) {
            'create' => $this->create($request),
            'update' => $this->update($request),
            'delete' => $this->delete($request),
            default => $this->render($request),
        };
    }

    private function create(Request $request): View
    {
        [$attributes, $errors] = $this->validated($request, passwordRequired: true);

        if (! $errors) {
            $errors = $this->duplicateErrors($attributes['email'], $attributes['username']);
        }

        if ($errors) {
            return $this->render($request, errors: $errors);
        }

        try {
            User::create($attributes);
        } catch (QueryException $exception) {
            ActivityLogger::log('user_create_failed', ['by_user_id' => $request->user()->id, 'new_user' => $attributes['username'], 'error' => $exception->getMessage()]);

            return $this->render($request, errors: ['general' => 'Failed to create user.']);
        }

        ActivityLogger::log('user_create', ['by_user_id' => $request->user()->id, 'new_user' => $attributes['username'], 'role' => $attributes['role']]);

        return $this->render($request, ok: 'User created successfully.');
    }

    private function update(Request $request): View
    {
        $userId = $request->integer('user_id');
        [$attributes, $errors] = $this->validated($request, passwordRequired: false);
        $attributes['is_active'] = $request->integer('is_active', 1) === 1;

        if (! $errors) {
            $errors = $this->duplicateErrors($attributes['email'], $attributes['username'], $userId);
        }

        if ($errors) {
            return $this->render($request, errors: $errors);
        }

        $user = User::find($userId);

        try {
            $user?->update($attributes);
        } catch (QueryException $exception) {
            ActivityLogger::log('user_update_failed', ['by_user_id' => $request->user()->id, 'user_id' => $userId, 'error' => $exception->getMessage()]);

            return $this->render($request, errors: ['general' => 'Failed to update user.']);
        }

        if (! $user) {
            return $this->render($request, errors: ['general' => 'Failed to update user.']);
        }

        ActivityLogger::log('user_update', ['by_user_id' => $request->user()->id, 'user_id' => $userId]);

        return $this->render($request, ok: 'User updated successfully.');
    }

    private function delete(Request $request): View
    {
        $userId = $request->integer('user_id');

        if ($userId > 0) {
            User::whereKey($userId)->delete();
            ActivityLogger::log('user_delete', ['by_user_id' => $request->user()->id, 'user_id' => $userId]);

            return $this->render($request, ok: 'User deleted successfully.');
        }

        return $this->render($request);
    }

    /**
     * @return array{0: array<string, mixed>, 1: array<string, string>}
     */
    private function validated(Request $request, bool $passwordRequired): array
    {
        $input = fn (string $key, string $default = ''): string => trim((string) $request->input($key, $default));

        $fullName = $input('full_name');
        $email = $input('email');
        $username = $input('username');
        $password = $input('password');
        $confirm = $input('confirm_password');
        $role = $input('role', 'customer');
        $userType = $input('user_type', 'Regular');
        $errors = [];

        if ($fullName === '') {
            $errors['full_name'] = 'Full name is required.';
        } elseif (strlen($fullName) > 120) {
            $errors['full_name'] = 'Full name must be less than 120 characters.';
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Valid email address is required.';
        } elseif (strlen($email) > 160) {
            $errors['email'] = 'Email address must be less than 160 characters.';
        }

        if (strlen($username) < 3) {
            $errors['username'] = 'Username must be at least 3 characters long.';
        } elseif (strlen($username) > 60) {
            $errors['username'] = 'Username must be less than 60 characters.';
        } elseif (! preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors['username'] = 'Username can only contain letters, numbers, and underscores.';
        }

        if ($passwordRequired || $password !== '') {
            if (strlen($password) < 6) {
                $errors['password'] = 'Password must be at least 6 characters long.';
            } elseif (strlen($password) > 128) {
                $errors['password'] = 'Password must be less than 128 characters.';
            }

            if ($confirm === '') {
                $errors['confirm_password'] = 'Please confirm your password.';
            } elseif ($password !== $confirm) {
                $errors['confirm_password'] = 'Passwords do not match.';
            }
        }

        if (! in_array($role, User::ROLES, true)) {
            $errors['role'] = 'Invalid role selected.';
        }

        if (! in_array($userType, User::USER_TYPES, true)) {
            $errors['user_type'] = 'Invalid user type selected.';
        }

        $attributes = [
            'full_name' => $fullName,
            'email' => $email,
            'phone' => $input('phone'),
            'username' => $username,
            'role' => $role,
            'user_type' => $userType,
        ];

        if ($password !== '') {
            $attributes['password'] = $password;
        }

        return [$attributes, $errors];
    }

    /**
     * @return array<string, string>
     */
    private function duplicateErrors(string $email, string $username, ?int $ignoreId = null): array
    {
        $errors = [];
        $matches = User::where(fn ($query) => $query->where('email', $email)->orWhere('username', $username))
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->get(['email', 'username']);

        foreach ($matches as $match) {
            if ($match->email === $email) {
                $errors['email'] = 'Information already existed.';
            }

            if ($match->username === $username) {
                $errors['username'] = 'Information already existed.';
            }
        }

        return $errors;
    }

    /**
     * Search, sort and paginate users.
     *
     * @return array<string, mixed>
     */
    private function listing(Request $request): array
    {
        $search = trim((string) $request->query('search', ''));
        $sort = in_array($request->query('sort'), self::SORTABLE, true) ? $request->query('sort') : 'created_at';
        $order = strtoupper((string) $request->query('order', 'DESC')) === 'ASC' ? 'ASC' : 'DESC';
        $page = max(1, $request->integer('page', 1));
        $offset = ($page - 1) * self::PER_PAGE;

        $query = User::query()->when($search !== '', function ($query) use ($search) {
            $term = "%{$search}%";
            $query->where(fn ($q) => $q->where('full_name', 'like', $term)->orWhere('email', 'like', $term)
                ->orWhere('username', 'like', $term)->orWhere('phone', 'like', $term));
        });

        $total = (clone $query)->count();
        $users = $query->toBase()
            ->select('id', 'full_name', 'email', 'phone', 'username', 'role', 'user_type', 'is_active', 'created_at')
            ->orderBy($sort, $order)
            ->offset($offset)
            ->limit(self::PER_PAGE)
            ->get()
            ->map(fn (object $row): array => (array) $row)
            ->all();

        return [
            'users' => $users,
            'search' => $search,
            'sort' => $sort,
            'order' => $order,
            'page' => $page,
            'offset' => $offset,
            'total_users' => $total,
            'total_pages' => (int) ceil($total / self::PER_PAGE),
        ];
    }

    /**
     * @param  array<string, string>  $errors
     */
    private function render(Request $request, string $ok = '', array $errors = []): View
    {
        return view('admin.users', $this->listing($request) + ['ok' => $ok, 'formErrors' => $errors]);
    }
}
