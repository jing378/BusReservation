<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'full_name', 'email', 'phone', 'username', 'password', 'role', 'user_type',
    'newsletter_subscription', 'is_active', 'reset_token', 'reset_token_expiry',
])]
#[Hidden(['password', 'remember_token', 'reset_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /** @var list<string> */
    public const USER_TYPES = ['Student', 'Regular', 'Senior Citizen/PWD'];

    /** @var list<string> */
    public const ROLES = ['admin', 'staff', 'customer'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'reset_token_expiry' => 'datetime',
            'newsletter_subscription' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * @return HasMany<Booking, $this>
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Two-letter initials used for the profile avatar.
     */
    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim((string) $this->full_name)) ?: [];

        if ($parts === [] || $parts[0] === '') {
            return '';
        }

        $first = mb_substr($parts[0], 0, 1, 'UTF-8');
        $last = mb_substr($parts[count($parts) - 1], 0, 1, 'UTF-8');

        return strtoupper($first.$last);
    }
}
