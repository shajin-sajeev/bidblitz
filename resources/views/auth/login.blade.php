@extends('layouts.app')

@section('content')
<div style="max-width: 400px; margin: 4rem auto;">
    <div class="glass-card text-center">
        <h2>Welcome Back</h2>
        <p>Sign in with your username or phone number and password.</p>

        <form action="{{ route('auth.login') }}" method="POST" class="mt-8 text-left">
            @csrf
            <div class="form-group">
                <label for="login" class="form-label">Username or Phone Number</label>
                <input type="text" name="login" id="login" class="form-control" placeholder="Enter username or phone number" required>
                @error('login')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
                @error('password')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i> 
                    You can login using either your username or phone number
                </small>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
        </form>

        <div class="mt-4">
            <p class="text-muted">Don't have an account? <a href="{{ route('auth.register') }}">Register</a></p>
        </div>
    </div>
</div>
@endsection
