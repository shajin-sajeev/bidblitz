@extends('layouts.app')

@section('content')
<div style="max-width: 400px; margin: 4rem auto;">
    <div class="glass-card text-center">
        <h2>Create Account</h2>
        <p>Register your account. A unique username will be generated automatically.</p>

        <form action="{{ route('auth.register') }}" method="POST" class="mt-8 text-left">
            @csrf
            <div class="form-group">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Enter your full name" required>
                @error('name')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="phone" class="form-label">Mobile Number</label>
                <input type="text" name="phone" id="phone" class="form-control" placeholder="e.g. 9876543210" required>
                @error('phone')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Create a password" required>
                @error('password')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Re-enter your password" required>
            </div>
            
            <div class="form-group">
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i> 
                    A unique username will be automatically generated based on your name
                </small>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Register & Send OTP</button>
        </form>

        <div class="mt-4">
            <p class="text-muted">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
        </div>
    </div>
</div>
@endsection
