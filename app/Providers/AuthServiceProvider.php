<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Equipment;
use App\Models\Booking;
use App\Policies\BookingPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Booking::class => BookingPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define admin access
        Gate::define('access-admin', function (User $user) {
            return $user->isAdmin();
        });

        // Equipment management
        Gate::define('manage-equipment', function (User $user) {
            return $user->isAdmin();
        });

        // Category management
        Gate::define('manage-categories', function (User $user) {
            return $user->isAdmin();
        });

        // User management
        Gate::define('manage-users', function (User $user) {
            return $user->isAdmin();
        });

        // Booking management
        Gate::define('manage-bookings', function (User $user) {
            return $user->isAdmin();
        });

        // View reports
        Gate::define('view-reports', function (User $user) {
            return $user->isAdmin();
        });

        // Normal user permissions
        Gate::define('make-booking', function (User $user) {
            return $user->isNormalUser();
        });

        Gate::define('view-own-bookings', function (User $user) {
            return true; // Both admin and normal users can view their own bookings
        });

        Gate::define('cancel-booking', function (User $user, Booking $booking) {
            return $user->id === $booking->user_id || $user->isAdmin();
        });

        Gate::define('clear-all-bookings', function (User $user) {
            return true; // Allow users to clear their own bookings
        });
    }
} 