<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin users
        User::create([
            'name' => 'System Admin',
            'email' => 'admin@strathmore.edu',
            'password' => Hash::make('Admin@123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Equipment Manager',
            'email' => 'manager@strathmore.edu',
            'password' => Hash::make('Manager@123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create a regular user
        User::create([
            'name' => 'Regular User',
            'email' => 'user@strathmore.edu',
            'password' => Hash::make('User@123'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);
    }
}
