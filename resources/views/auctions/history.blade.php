@extends('layouts.app')

@section('content')
<div style="display: flex; gap: 2rem; flex-wrap: wrap;">
    <!-- Sidebar -->
    <div style="width: 280px; flex-shrink: 0;">
        @include('partials.sidebar')
    </div>

    <!-- Main Content -->
    <div style="flex-grow: 1; min-width: 300px;">
        <div class="glass-card mb-8">
            <h2>📜 Auction History</h2>
            <p>View all your past and present auctions.</p>
        </div>

        <!-- My Created Auctions -->
        <div class="glass-card mb-8">
            <h3>🏆 Auctions I Created</h3>
            @forelse($userAuctions as $auction)
                <div style="border: var(--glass-border); padding: 1.5rem; margin-bottom: 1rem; border-radius: 12px;">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h4 style="margin: 0;">{{ $auction->name }}</h4>
                            <div style="color: var(--text-muted); font-size: 0.9rem;">
                                {{ $auction->sport }} • {{ $auction->total_teams }} teams • ${{ number_format($auction->budget, 2) }} budget
                            </div>
                        </div>
                        <span style="background: {{ $auction->status === 'live' ? '#10b981' : ($auction->status === 'completed' ? '#ef4444' : 'var(--primary)') }}; padding: 4px 12px; border-radius: 16px; font-size: 0.85rem; color: white; font-weight: 600;">
                            {{ ucfirst($auction->status) }}
                        </span>
                    </div>

                    @if($auction->statistics)
                        <div class="grid grid-cols-4 gap-4 mb-4">
                            <div style="text-align: center;">
                                <div style="font-size: 1.2rem; font-weight: bold; color: var(--primary);">{{ $auction->statistics->total_players_sold }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">Sold</div>
                            </div>
                            <div style="text-align: center;">
                                <div style="font-size: 1.2rem; font-weight: bold; color: var(--accent);">{{ $auction->statistics->total_bids_placed }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">Bids</div>
                            </div>
                            <div style="text-align: center;">
                                <div style="font-size: 1.2rem; font-weight: bold; color: #10b981;">${{ number_format($auction->statistics->total_amount_spent, 0) }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">Spent</div>
                            </div>
                            <div style="text-align: center;">
                                <div style="font-size: 1.2rem; font-weight: bold; color: #f59e0b;">${{ number_format($auction->statistics->average_player_price, 0) }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">Avg Price</div>
                            </div>
                        </div>
                    @endif

                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ route('auctions.show', $auction) }}" class="btn btn-primary" style="font-size: 0.85rem; padding: 0.5rem 1rem;">📊 View Details</a>
                        <a href="{{ route('auctions.statistics', $auction) }}" class="btn btn-accent" style="font-size: 0.85rem; padding: 0.5rem 1rem;">📈 Statistics</a>
                        @if($auction->status === 'pending')
                            <a href="{{ route('auctions.pool', $auction) }}" class="btn" style="background: rgba(255,255,255,0.1); font-size: 0.85rem; padding: 0.5rem 1rem;">⚙️ Manage</a>
                        @endif
                        @if($auction->status === 'active')
                            @if($auction->canStartLive())
                                <form method="POST" action="{{ route('auctions.start', $auction) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary" style="font-size: 0.85rem; padding: 0.5rem 1rem;">Start Auction</button>
                                </form>
                            @else
                                <span class="btn" style="font-size: 0.85rem; padding: 0.5rem 1rem; opacity: 0.65; cursor: help;" title="Assign every team’s owner player (Team Setup) and ensure each team has joined before starting.">Start locked</span>
                            @endif
                        @endif
                        @if(in_array($auction->status, ['active', 'live'], true))
                            <a href="{{ route('auctions.live', $auction) }}" class="btn btn-accent" style="font-size: 0.85rem; padding: 0.5rem 1rem;">Live Room</a>
                        @endif
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 3rem; color: var(--text-muted);">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">🏆</div>
                    <h4>No Auctions Created</h4>
                    <p>You haven't created any auctions yet. Start by creating your first auction!</p>
                    <a href="{{ route('auctions.create') }}" class="btn btn-primary" style="margin-top: 1rem;">➕ Create Auction</a>
                </div>
            @endforelse
        </div>

        <!-- Participated Auctions -->
        @if($participatedAuctions->count() > 0)
            <div class="glass-card">
                <h3>🎯 Auctions I Participated In</h3>
                @foreach($participatedAuctions as $auction)
                    <div style="border: var(--glass-border); padding: 1.5rem; margin-bottom: 1rem; border-radius: 12px;">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <h4 style="margin: 0;">{{ $auction->name }}</h4>
                                <div style="color: var(--text-muted); font-size: 0.9rem;">
                                    Created by {{ $auction->creator->name ?? 'Unknown' }} • {{ $auction->sport }}
                                </div>
                            </div>
                            <span style="background: {{ $auction->status === 'live' ? '#10b981' : ($auction->status === 'completed' ? '#ef4444' : 'var(--primary)') }}; padding: 4px 12px; border-radius: 16px; font-size: 0.85rem; color: white; font-weight: 600;">
                                {{ ucfirst($auction->status) }}
                            </span>
                        </div>

                        <div style="display: flex; gap: 0.5rem;">
                            <a href="{{ route('auctions.show', $auction) }}" class="btn btn-primary" style="font-size: 0.85rem; padding: 0.5rem 1rem;">📊 View Details</a>
                            @if($auction->status === 'live')
                                <a href="{{ route('auctions.live', $auction) }}" class="btn btn-accent" style="font-size: 0.85rem; padding: 0.5rem 1rem;">🔴 Enter Live</a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
