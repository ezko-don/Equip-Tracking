<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Equipment;
use App\Models\Booking;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@strathmore.edu',
            'password' => Hash::make('Admin@123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create staff user
        \App\Models\User::create([
            'name' => 'Staff User',
            'email' => 'staff@strathmore.edu',
            'password' => Hash::make('12345678'),
            'role' => 'staff',
            'email_verified_at' => now(),
        ]);

        // Create regular user
        \App\Models\User::create([
            'name' => 'Regular User',
            'email' => 'user@strathmore.edu',
            'password' => Hash::make('User@123'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        // Create some basic categories
        $categories = [
            ['name' => 'Cameras', 'description' => 'Photography and video equipment', 'is_active' => true],
            ['name' => 'Audio Equipment', 'description' => 'Sound and recording equipment', 'is_active' => true],
            ['name' => 'Lighting', 'description' => 'Studio and location lighting', 'is_active' => true],
            ['name' => 'Computers', 'description' => 'Laptops and desktop computers', 'is_active' => true],
            ['name' => 'Accessories', 'description' => 'Various equipment accessories', 'is_active' => true],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }

        // Create some sample equipment
        $equipment = [
            [
                'name' => 'Canon EOS 5D Mark IV',
                'description' => 'Professional DSLR camera',
                'category_id' => 1,
                'status' => 'available',
                'condition' => 'good',
                'quantity' => 1,
                'is_active' => true,
                'slug' => 'canon-eos-5d-mark-iv',
            ],
            [
                'name' => 'Tripod',
                'description' => 'Professional camera tripod',
                'category_id' => 5,
                'status' => 'available',
                'condition' => 'good',
                'quantity' => 2,
                'is_active' => true,
                'slug' => 'tripod',
            ],
            [
                'name' => 'Microphone Set',
                'description' => 'Professional wireless microphone set',
                'category_id' => 2,
                'status' => 'available',
                'condition' => 'good',
                'quantity' => 3,
                'is_active' => true,
                'slug' => 'microphone-set',
            ],
            [
                'name' => 'Studio Lights',
                'description' => 'Professional studio lighting kit',
                'category_id' => 3,
                'status' => 'available',
                'condition' => 'good',
                'quantity' => 2,
                'is_active' => true,
                'slug' => 'studio-lights',
            ],
            [
                'name' => 'MacBook Pro',
                'description' => '16-inch MacBook Pro with M1 Pro',
                'category_id' => 4,
                'status' => 'available',
                'condition' => 'good',
                'quantity' => 1,
                'is_active' => true,
                'slug' => 'macbook-pro',
            ],
            [
                'name' => 'Camera Lens Kit',
                'description' => 'Professional camera lens kit',
                'category_id' => 5,
                'status' => 'available',
                'condition' => 'good',
                'quantity' => 1,
                'is_active' => true,
                'slug' => 'camera-lens-kit',
            ]
        ];

        foreach ($equipment as $item) {
            \App\Models\Equipment::create($item);
        }

        // Create some sample bookings
        $bookings = [
            [
                'user_id' => 3, // Regular User
                'equipment_id' => 1, // Canon EOS 5D Mark IV
                'start_time' => now()->addDays(1),
                'end_time' => now()->addDays(3),
                'event_name' => 'Photography Project',
                'location' => 'Studio Room 101',
                'status' => 'approved',
                'notes' => 'Need for a weekend photoshoot'
            ],
            [
                'user_id' => 2, // Staff User
                'equipment_id' => 3, // Microphone Set
                'start_time' => now()->addDays(4),
                'end_time' => now()->addDays(5),
                'event_name' => 'Recording Session',
                'location' => 'Audio Lab',
                'status' => 'pending',
                'notes' => 'Audio recording for department event'
            ],
            [
                'user_id' => 3, // Regular User
                'equipment_id' => 5, // MacBook Pro
                'start_time' => now()->addDays(2),
                'end_time' => now()->addDays(4),
                'event_name' => 'Video Editing Project',
                'location' => 'Media Lab',
                'status' => 'pending',
                'notes' => 'Final year project editing'
            ]
        ];

        foreach ($bookings as $booking) {
            \App\Models\Booking::create($booking);
        }
    }
}
