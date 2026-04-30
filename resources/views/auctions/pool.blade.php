@extends('layouts.app')

@section('content')
<div class="glass-card mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h2>Manage Player Pool: {{ $auction->name }}</h2>
            <p>Add players to the auction and set their base prices.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn" style="border: var(--glass-border);">Back to Dashboard</a>
    </div>
</div>

<div class="grid grid-cols-2">
    <!-- Left Column: Search & Add Players -->
    <div class="glass-card">
        <h3>Available Players</h3>
        <form action="{{ route('auctions.pool', $auction) }}" method="GET" class="mb-4 flex gap-4">
            <input type="text" name="search" class="form-control" placeholder="Search by name or username" value="{{ $search }}">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <div style="max-height: 500px; overflow-y: auto;">
            @forelse($players as $player)
                <div style="border: var(--glass-border); padding: 1rem; margin-bottom: 1rem; border-radius: 8px;">
                    <div class="flex justify-between items-center mb-2">
                        <strong>{{ $player->name ?? 'Unknown' }} ({{ $player->username }})</strong>
                        <span style="background: var(--primary); padding: 2px 8px; border-radius: 12px; font-size: 0.8rem;">
                            {{ $player->playerProfile->player_role ?? 'Player' }}
                        </span>
                    </div>
                    <form action="{{ route('auctions.pool.store', $auction) }}" method="POST" class="flex gap-4 items-center">
                        @csrf
                        <input type="hidden" name="player_id" value="{{ $player->id }}">
                        <input type="number" name="base_price" class="form-control" placeholder="Base Price" required min="0" style="padding: 0.5rem;">
                        <button type="submit" class="btn btn-accent" style="padding: 0.5rem 1rem;">Add</button>
                    </form>
                </div>
            @empty
                <p>No available players found.</p>
            @endforelse
            
            <div class="mt-4">
                {{ $players->links() }}
            </div>
        </div>
    </div>

    <!-- Right Column: Current Pool -->
    <div class="glass-card">
        <h3>Current Pool ({{ $pool->count() }} Players)</h3>
        <p>Min required: {{ $auction->min_players * $auction->total_teams }} | Max allowed: {{ $auction->max_players * $auction->total_teams }}</p>
        
        <div style="max-height: 500px; overflow-y: auto; margin-top: 1rem;">
            @forelse($pool as $item)
                <div style="border: var(--glass-border); padding: 0.75rem; margin-bottom: 0.5rem; border-radius: 8px; display: flex; justify-content: space-between;">
                    <div>
                        <strong>{{ $item->player->name ?? $item->player->username }}</strong>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $item->player->playerProfile->player_role ?? 'Player' }}</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="color: var(--accent);">₹{{ number_format($item->base_price) }}</div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">{{ ucfirst($item->status) }}</div>
                    </div>
                </div>
            @empty
                <p>No players added to the pool yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
