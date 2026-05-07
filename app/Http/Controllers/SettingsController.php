<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function profile()
    {
        $user = Auth::user();
        return view('settings.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'specialization' => 'required|string|in:Batsman,Bowler,All-rounder,Wicket-keeper',
            'skills' => 'nullable|string|max:1000',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();
        $user->name = $request->name;

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $image = $request->file('profile_image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->store('profile_images', 'public');
            $user->profile_image = $imagePath;
        }

        $user->save();

        // Update player profile
        $playerProfile = \App\Models\Player::where('phone', $user->phone)->first();
        if ($playerProfile) {
            $playerProfile->specialization = $request->specialization;
            $playerProfile->description = $request->skills;
            $playerProfile->save();
        }

        // Check if this is an AJAX request (for quick upload)
        if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => 'Profile photo updated successfully!',
                'image_url' => $user->profile_image ? asset('storage/' . $user->profile_image) : null
            ]);
        }

        return redirect()->route('settings.profile')->with('success', 'Profile updated successfully!');
    }

    public function theme()
    {
        return view('settings.theme');
    }

    public function updateTheme(Request $request)
    {
        $request->validate([
            'theme' => 'required|in:dark,light',
        ]);

        $user = Auth::user();
        
        // Store theme preference in session or user preferences
        session(['theme' => $request->theme]);
        
        // If you want to persist it in the database, you might need to add a theme column to users table
        // For now, we'll use session storage
        
        return redirect()->route('settings.theme')->with('success', 'Theme updated successfully!');
    }
}
