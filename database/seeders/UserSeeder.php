<?php

namespace Database\Seeders;

use App\Models\User;
use App\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@bookvault.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@bookvault.com',
                'password' => Hash::make('password'),
                'role' => UserRole::ADMIN,
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'member@bookvault.com'],
            [
                'name' => 'Test Member',
                'email' => 'member@bookvault.com',
                'password' => Hash::make('password'),
                'role' => UserRole::MEMBER,
                'email_verified_at' => now(),
            ]
        );
    }
}
