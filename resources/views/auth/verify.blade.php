@extends('layouts.app')

@section('content')
<div style="max-width: 400px; margin: 4rem auto;">
    <div class="glass-card text-center">
        <h2>Verify OTP</h2>
        <p>Enter the 6-digit code sent to your mobile number.</p>

        <form action="{{ route('auth.verify') }}" method="POST" class="mt-8 text-left">
            @csrf
            <div class="form-group">
                <label for="otp" class="form-label">6-Digit OTP</label>
                <input type="text" name="otp" id="otp" class="form-control" placeholder="123456" maxlength="6" required style="text-align: center; font-size: 1.5rem; letter-spacing: 0.5rem;">
            </div>
            
            <button type="submit" class="btn btn-accent btn-block">Verify & Login</button>
        </form>
    </div>
</div>
@endsection
