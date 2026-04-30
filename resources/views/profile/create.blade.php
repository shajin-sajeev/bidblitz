@extends('layouts.app')

@section('content')
<div class="glass-card" style="max-width: 600px; margin: 2rem auto;">
    <h2>Complete Your Profile</h2>
    <p>Please provide your details before accessing the dashboard.</p>

    <form action="{{ route('profile.store') }}" method="POST" class="mt-8">
        @csrf
        
        <div class="form-group">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', auth()->user()->name) }}" required>
        </div>

        <div class="form-group">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" id="username" class="form-control" value="{{ old('username', auth()->user()->username) }}" required>
        </div>

        <div class="form-group">
            <label for="player_role" class="form-label">Player Skill</label>
            <select name="player_role" id="player_role" class="form-control">
                <option value="Batsman">Batsman</option>
                <option value="Bowler">Bowler</option>
                <option value="All-rounder">All-rounder</option>
                <option value="Wicket-keeper">Wicket-keeper</option>
            </select>
        </div>
        <p style="font-size: 0.85rem; color: var(--text-muted);">Note: You can update your exact stats (Matches, Runs, etc.) from your profile page later.</p>
        
        <div class="mt-8 text-center">
            <button type="submit" class="btn btn-primary btn-block">Save Profile</button>
        </div>
    </form>
</div>
@endsection
