<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\PlayerProfile;
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
        $user->load('playerProfile');

        $player = Player::where(function ($query) use ($user) {
            if ($user->phone) {
                $query->where('phone', $user->phone);
            }

            if ($user->username) {
                $query->orWhere('unique_username', $user->username);
            }
        })->first();

        return view('settings.profile', compact('user', 'player'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        if (($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') && $request->hasFile('profile_image')) {
            $request->validate([
                'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $imagePath = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $imagePath;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile photo updated successfully!',
                'image_url' => asset('storage/' . $imagePath),
            ]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'specialization' => 'required|string|in:Batsman,Bowler,All-rounder,Wicket-keeper',
            'skills' => 'nullable|string|max:1000',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user->name = $request->name;

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $image = $request->file('profile_image');
            $imagePath = $image->store('profile_images', 'public');
            $user->profile_image = $imagePath;
        }

        $user->save();

        $player = Player::where(function ($query) use ($user) {
            if ($user->phone) {
                $query->where('phone', $user->phone);
            }

            if ($user->username) {
                $query->orWhere('unique_username', $user->username);
            }
        })->first();

        if (!$player) {
            $player = new Player();
            $player->unique_username = $user->username;
            $player->email = 'player' . $user->id . '@player.local';
            $player->phone = $user->phone;
            $player->avatar = $user->profile_image ? asset('storage/' . $user->profile_image) : null;
            $player->is_active = true;
        }

        $player->name = $user->name;
        $player->phone = $user->phone;
        $player->specialization = $request->specialization;
        $player->description = $request->skills;
        if ($user->profile_image) {
            $player->avatar = asset('storage/' . $user->profile_image);
        }
        $player->save();

        PlayerProfile::updateOrCreate(
            ['user_id' => $user->id],
            ['player_role' => $request->specialization]
        );

        // Check if this is an AJAX request (for quick upload)
        if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => 'Profile photo updated successfully!',
                'image_url' => $user->profile_image ? asset('storage/' . $user->profile_image) : null
            ]);
        }

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully!');
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
