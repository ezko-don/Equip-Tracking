<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateNewAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'New Admin',
            'email' => 'newadmin@strathmore.edu',
            'password' => Hash::make('NewAdmin@123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }
} 