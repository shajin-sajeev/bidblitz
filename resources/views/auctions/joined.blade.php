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
            <h2>📋 Joined Auctions</h2>
            <p>Auctions where you are participating as a team owner.</p>
        </div>

        @forelse($joinedAuctions as $auction)
            <div class="glass-card mb-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 style="margin: 0;">{{ $auction->name }}</h3>
                        <div style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.5rem;">
                            🏟️ {{ $auction->sport }} • 👥 {{ $auction->total_teams }} teams • 💰 ${{ number_format($auction->budget, 2) }} budget
                        </div>
                        <div style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.25rem;">
                            Created by {{ $auction->creator->name ?? 'Unknown' }} • {{ $auction->created_at->format('M d, Y') }}
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <span style="background: {{ $auction->status === 'live' ? '#10b981' : ($auction->status === 'completed' ? '#ef4444' : 'var(--primary)') }}; padding: 6px 16px; border-radius: 20px; font-size: 0.9rem; color: white; font-weight: 600; display: inline-block;">
                            {{ ucfirst($auction->status) }}
                        </span>
                        @if($auction->created_by === auth()->id())
                            <div style="margin-top: 0.5rem; font-size: 0.85rem; color: var(--text-muted);">
                                Auction pass: <strong>{{ $auction->auction_pass }}</strong>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Teams in this Auction -->
                <div style="margin-bottom: 1.5rem;">
                    <h4 style="margin-bottom: 1rem; color: var(--primary);">🏆 Teams in Auction</h4>
                    <div class="grid grid-cols-3 gap-4">
                        @foreach($auction->teams as $team)
                            <div style="border: var(--glass-border); padding: 1rem; border-radius: 8px; text-align: center;">
                                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">🏆</div>
                                <div style="font-weight: 600; margin-bottom: 0.25rem;">{{ $team->name }}</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">
                                    {{ $team->owner->name ?? 'Unknown Owner' }}
                                </div>
                                @if($team->owner_id === auth()->id())
                                    <span style="background: var(--accent); color: #000; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600; margin-top: 0.5rem; display: inline-block;">
                                        YOUR TEAM
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Participants -->
                <div style="margin-bottom: 1.5rem;">
                    <h4 style="margin-bottom: 1rem; color: var(--primary);">👥 Participants</h4>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                        @foreach($auction->participants as $participant)
                            <div style="background: rgba(251, 191, 36, 0.1); padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.85rem;">
                                @if($participant->user->name)
                                    {{ $participant->user->name }}
                                @else
                                    User {{ $participant->user->phone }}
                                @endif
                                <span style="color: var(--text-muted); font-size: 0.75rem;">({{ ucfirst($participant->role) }})</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Action Buttons -->
                <div style="display: flex; gap: 1rem;">
                    @if($auction->status === 'live')
                        <a href="{{ route('auctions.live', $auction) }}" class="btn btn-accent">
                            🔴 Enter Live Auction
                        </a>
                    @endif
                    <a href="{{ route('auctions.show', $auction) }}" class="btn btn-primary">
                        📊 View Details
                    </a>
                    <a href="{{ route('teams.auction', $auction) }}" class="btn" style="background: rgba(255,255,255,0.1);">
                        👥 View All Teams
                    </a>
                </div>
            </div>
        @empty
            <div class="glass-card" style="text-align: center; padding: 4rem;">
                <div style="font-size: 4rem; margin-bottom: 1rem;">📋</div>
                <h3>No Joined Auctions</h3>
                <p style="color: var(--text-muted); margin-bottom: 2rem;">You haven't joined any auctions yet. Find and join an auction to get started!</p>
                <a href="{{ route('auctions.join') }}" class="btn btn-primary">🔗 Join Auction</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
