<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')
                    ->paginate(10);

        return view('admin.users.index', compact('users'));
    }
    
    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }
    
    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', 'ends_with:strathmore.edu'],
            'role' => ['required', 'in:admin,user'],
        ]);
        
        // Generate a random password
        $password = $this->generateRandomPassword();
        
        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'role' => $request->role,
            'password_changed' => false,
        ]);
        
        // Send welcome email with credentials
        $this->sendWelcomeEmail($user, $password);
        
        // Trigger registered event for any necessary actions
        event(new Registered($user));
        
        return redirect()->route('admin.users.index')
            ->with('success', "User created successfully. Login credentials have been sent to {$user->email}");
    }
    
    /**
     * Show the user details.
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }
    
    /**
     * Show the form for editing the user.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }
    
    /**
     * Update the user in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id, 'ends_with:strathmore.edu'],
            'role' => ['required', 'in:admin,user'],
        ]);
        
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }
    
    /**
     * Remove the user from storage.
     */
    public function destroy(User $user)
    {
        // Don't allow deleting yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function toggleRole(User $user)
    {
        // Don't allow changing your own role
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $user->role = $user->role === 'admin' ? 'user' : 'admin';
        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', "User role has been updated successfully.");
    }
    
    /**
     * Generate a random password.
     */
    private function generateRandomPassword($length = 12)
    {
        // Create a password with at least one uppercase, one lowercase, one number, and one special character
        $uppercase = Str::upper(Str::random(1));
        $lowercase = Str::lower(Str::random(1));
        $number = rand(0, 9);
        $special = ['!', '@', '#', '$', '%', '^', '&', '*'][rand(0, 7)];
        
        // Generate the rest of the password
        $remaining = Str::random($length - 4);
        
        // Combine all parts and shuffle
        $password = str_shuffle($uppercase . $lowercase . $number . $special . $remaining);
        
        return $password;
    }
    
    /**
     * Send welcome email with login credentials.
     */
    private function sendWelcomeEmail(User $user, $password)
    {
        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $password,
            'login_url' => route('login')
        ];
        
        Mail::send('emails.welcome', $data, function($message) use ($user) {
            $message->to($user->email, $user->name)
                    ->subject('Welcome to Strathmore Equipment Management System');
        });
    }
    
    /**
     * Reset a user's password and send new credentials.
     */
    public function resetPassword(User $user)
    {
        // Generate a new random password
        $password = $this->generateRandomPassword();
        
        // Update the user's password
        $user->update([
            'password' => Hash::make($password),
            'password_changed' => false
        ]);
        
        // Send email with new credentials
        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $password,
            'login_url' => route('login')
        ];
        
        Mail::send('emails.password_reset', $data, function($message) use ($user) {
            $message->to($user->email, $user->name)
                    ->subject('Your Strathmore EMS Password Has Been Reset');
        });
        
        return redirect()->route('admin.users.index')
            ->with('success', "User password has been reset. New credentials have been sent to {$user->email}");
    }
} 