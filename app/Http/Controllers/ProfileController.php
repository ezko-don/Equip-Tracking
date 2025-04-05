<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Equipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile page.
     */
    public function index(Request $request): View
    {
        return view('profile.index', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        // Get available equipment for task management
        $equipment = Equipment::where('status', 'available')->get();
        
        return view('profile.edit', [
            'user' => $request->user(),
            'equipment' => $equipment,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . Auth::id()],
        ]);

        $user = $request->user();
        $user->fill($request->only(['name', 'email']));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.index')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Update the user's profile photo.
     */
    public function updatePhoto(Request $request)
    {
        try {
            \Log::info('Profile photo upload started');
            
            $request->validate([
                'photo' => ['required', 'image', 'mimes:jpeg,png,gif,webp,bmp,tiff', 'max:5120'], // 5MB max
            ]);

            if (!$request->hasFile('photo') || !$request->file('photo')->isValid()) {
                throw new \Exception('Invalid file upload');
            }

            $user = auth()->user();
            
            \Log::info('Uploading photo for user: ' . $user->id);

            // Ensure the storage directory exists
            $storage = Storage::disk('public');
            if (!$storage->exists('profile-photos')) {
                $storage->makeDirectory('profile-photos');
                \Log::info('Created profile-photos directory');
            }

            // Delete old photo if it exists
            if ($user->profile_photo_path && $storage->exists($user->profile_photo_path)) {
                $storage->delete($user->profile_photo_path);
                \Log::info('Deleted old profile photo: ' . $user->profile_photo_path);
            }

            // Store new photo with a unique name
            $file = $request->file('photo');
            $extension = $file->getClientOriginalExtension();
            $fileName = 'profile-' . $user->id . '-' . time() . '.' . $extension;
            
            $path = $file->storeAs('profile-photos', $fileName, 'public');
            
            if (!$path) {
                throw new \Exception('Failed to store the uploaded file.');
            }
            
            \Log::info('New photo stored at: ' . $path);

            $user->update([
                'profile_photo_path' => $path
            ]);

            \Log::info('Profile photo updated successfully');

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Profile photo updated successfully',
                    'path' => Storage::disk('public')->url($path)
                ]);
            }

            return redirect()->route('profile.index')
                ->with('status', 'profile-photo-updated');
        } catch (\Exception $e) {
            \Log::error('Profile photo upload failed: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Failed to upload photo: ' . $e->getMessage()
                ], 422);
            }

            return redirect()->route('profile.index')
                ->withErrors(['photo' => 'Failed to upload photo: ' . $e->getMessage()]);
        }
    }

    public function destroyPhoto()
    {
        try {
            $user = auth()->user();

            // Delete both photo fields if they exist
            if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            
            $user->update([
                'profile_photo_path' => null,
                'profile_photo' => null
            ]);

            return redirect()->route('profile.index')
                ->with('status', 'profile-photo-removed');
        } catch (\Exception $e) {
            return redirect()->route('profile.index')
                ->withErrors(['photo' => 'Failed to remove photo: ' . $e->getMessage()]);
        }
    }
} 