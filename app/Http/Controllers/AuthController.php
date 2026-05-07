<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'required|string|max:20|unique:users'
        ]);

        $user = \App\Models\User::create([
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => 'player',
            'is_verified' => true, // Auto-verify since no OTP
            'verified_at' => now(),
        ]);

        // Create corresponding player entry with the same username
        \App\Models\Player::create([
            'name' => $user->name,
            'unique_username' => $user->username,
            'email' => $user->username . '@player.com', // Generate a placeholder email
            'phone' => $user->phone,
            'specialization' => 'All-rounder', // Default specialization
            'description' => 'Player profile created during registration',
            'avatar' => 'https://picsum.photos/seed/' . $user->username . '/400/400.jpg',
            'is_active' => true,
        ]);

        // Auto-login the user after successful registration
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Registration successful! You are now logged in.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string'
        ]);

        // Try to find user by username or phone number
        $user = \App\Models\User::where('username', $credentials['login'])
            ->orWhere('phone', $credentials['login'])
            ->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'login' => 'The provided credentials do not match our records.'
            ])->onlyInput('login');
        }

        // All users are now auto-verified, log them in directly
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    
    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    }
}
