<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BookingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Booking $booking): bool
    {
        return $user->isAdmin() || $user->id === $booking->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Any authenticated user can create bookings
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Booking $booking): bool
    {
        return $user->isAdmin() || ($user->id === $booking->user_id && $booking->isPending());
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Booking $booking): bool
    {
        // Allow admins to delete any booking
        if ($user->isAdmin()) {
            return true;
        }
        
        // Allow users to delete their own bookings if they are pending, rejected, or cancelled
        return $user->id === $booking->user_id && 
               in_array($booking->status, ['pending', 'rejected', 'cancelled']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Booking $booking): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Booking $booking): bool
    {
        return false;
    }

    public function approve(User $user, Booking $booking): bool
    {
        return $user->isAdmin();
    }

    public function reject(User $user, Booking $booking): bool
    {
        return $user->isAdmin();
    }

    public function complete(User $user, Booking $booking): bool
    {
        return $user->isAdmin() || $user->id === $booking->user_id;
    }

    public function cancel(User $user, Booking $booking): bool
    {
        return $user->isAdmin() || $user->id === $booking->user_id;
    }

    /**
     * Determine whether the user can return equipment.
     */
    public function return(User $user, Booking $booking): bool
    {
        // Allow admins to mark any equipment as returned
        if ($user->isAdmin()) {
            return true;
        }
        
        // Allow users to return their own approved bookings regardless of time
        return $user->id === $booking->user_id && $booking->isApproved();
    }

    /**
     * Determine whether the user can clear all their bookings.
     */
    public function clearAll(User $user): bool
    {
        return true; // Users can clear their own bookings
    }
}
