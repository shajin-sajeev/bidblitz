@extends('layouts.app')

@section('content')
<div class="glass-card" style="max-width: 800px; margin: 0 auto;">
    <h2>Create New Auction</h2>
    <p>Setup your auction parameters below.</p>

    <form action="{{ route('auctions.store') }}" method="POST" class="mt-8">
        @csrf
        
        <div class="grid grid-cols-2">
            <div class="form-group">
                <label for="name" class="form-label">Auction Name</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="e.g. IPL 2026 Mega Auction" required>
            </div>
            
            <div class="form-group">
                <label for="sport" class="form-label">Sport</label>
                <select name="sport" id="sport" class="form-control" required>
                    <option value="" disabled selected>Select a Sport</option>
                    <option value="Cricket">Cricket</option>
                    <option value="Football">Football</option>
                    <option value="Kabaddi">Kabaddi</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="min_players" class="form-label">Min Players per Team</label>
                <input type="number" name="min_players" id="min_players" class="form-control" value="11" required min="1">
            </div>
            
            <div class="form-group">
                <label for="max_players" class="form-label">Max Players per Team</label>
                <input type="number" name="max_players" id="max_players" class="form-control" value="15" required min="1">
            </div>
            
            <div class="form-group">
                <label for="total_teams" class="form-label">Total Number of Teams</label>
                <input type="number" name="total_teams" id="total_teams" class="form-control" value="8" required min="2">
            </div>
            
            <div class="form-group">
                <label for="budget" class="form-label">Total Budget per Team (₹)</label>
                <input type="number" name="budget" id="budget" class="form-control" value="100000000" step="any" required>
            </div>
        </div>
        
        <div class="mt-4" style="text-align: right;">
            <a href="{{ route('dashboard') }}" class="btn" style="color: var(--text-muted); margin-right: 1rem;">Cancel</a>
            <button type="submit" class="btn btn-primary">Create Auction & Generate Passes</button>
        </div>
    </form>
</div>
@endsection
