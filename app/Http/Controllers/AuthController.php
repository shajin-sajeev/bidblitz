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
            'role' => 'player'
        ]);
        
        // Generate and send OTP for verification
        $user->otp = '123456'; // Mock OTP for Phase 1
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        // Create corresponding player entry with the same username
        \App\Models\Player::create([
            'name' => $user->name,
            'unique_username' => $user->username,
            'email' => $user->username . '@player.com', // Generate a placeholder email
            'phone' => $user->phone,
            'specialization' => 'All-rounder', // Default specialization
            'experience_years' => 0, // Default experience
            'base_price' => 1000.00, // Default base price
            'description' => 'Player profile created during registration',
            'avatar' => 'https://picsum.photos/seed/' . $user->username . '/400/400.jpg',
            'is_active' => true,
        ]);

        session(['auth_phone' => $user->phone, 'registration_mode' => true]);

        return redirect()->route('auth.verify.show')->with('success', 'Registration successful! OTP sent for verification. (Mock: 123456)');
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

        // Check if user is verified, if not send OTP
        if (!$user->is_verified) {
            // Generate and send OTP
            $user->otp = '123456'; // Mock OTP for Phase 1
            $user->otp_expires_at = now()->addMinutes(10);
            $user->save();

            session(['auth_phone' => $user->phone, 'registration_mode' => false]);

            return redirect()->route('auth.verify.show')->with('info', 'Account not verified. OTP sent for verification. (Mock: 123456)');
        }

        // User is verified, log them in directly
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function showVerifyForm()
    {
        if (!session('auth_phone')) {
            return redirect()->route('login');
        }
        return view('auth.verify');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6'
        ]);

        $phone = session('auth_phone');
        if (!$phone) {
            return redirect()->route('login')->withErrors(['phone' => 'Session expired. Please try again.']);
        }

        $user = \App\Models\User::where('phone', $phone)->first();

        if (!$user || $user->otp !== $request->otp || $user->otp_expires_at < now()) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
        }

        // Mark user as verified and clear OTP
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->is_verified = true;
        $user->verified_at = now();
        $user->save();

        Auth::login($user);
        session()->forget(['auth_phone', 'registration_mode']);

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
