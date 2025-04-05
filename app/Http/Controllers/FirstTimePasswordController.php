<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class FirstTimePasswordController extends Controller
{
    /**
     * Show the change password form.
     */
    public function showChangePasswordForm()
    {
        // If user has already changed their password, redirect to dashboard
        if (Auth::user()->password_changed) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.first-time-password');
    }

    /**
     * Handle a password change request.
     */
    public function changePassword(Request $request)
    {
        // If user has already changed their password, redirect to dashboard
        if (Auth::user()->password_changed) {
            return redirect()->route('dashboard');
        }

        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = Auth::user();
        
        // Update the user's password
        $user->password = Hash::make($request->password);
        $user->password_changed = true;
        $user->save();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Password changed successfully.');
    }
} 