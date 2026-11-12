<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the default administrator (username "admin", password "password"), as in the original schema.
     */
    public function run(): void
    {
        User::firstOrCreate(['username' => 'admin'], [
            'full_name' => 'System Administrator',
            'email' => 'admin@example.com',
            'phone' => '0000000000',
            'password' => 'password',
            'role' => 'admin',
            'user_type' => 'Regular',
            'is_active' => true,
        ]);
    }
}
