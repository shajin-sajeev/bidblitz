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
                        <div style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.25rem;">
                            📅 Joined on {{ $auction->joined_at ? $auction->joined_at->format('M d, Y') : 'Unknown' }}
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <span style="background: {{ $auction->status === 'live' ? '#10b981' : ($auction->status === 'completed' ? '#ef4444' : ($auction->status === 'setup' ? '#3b82f6' : 'var(--primary)')) }}; padding: 6px 16px; border-radius: 20px; font-size: 0.9rem; color: white; font-weight: 600; display: inline-block;">
                            {{ ucfirst($auction->status) }}
                        </span>
                        @if($auction->created_by === auth()->id())
                            <div style="margin-top: 0.5rem; font-size: 0.85rem; color: var(--text-muted);">
                                Auction pass: <strong>{{ $auction->auction_pass }}</strong>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- User's Team in this Auction -->
                <div style="margin-bottom: 1.5rem;">
                    <h4 style="margin-bottom: 1rem; color: var(--primary);">🏆 Your Team</h4>
                    @php
                        $userTeam = $auction->teams->where('owner_id', auth()->id())->first();
                    @endphp
                    @if($userTeam)
                        <div style="border: var(--glass-border); padding: 1.5rem; border-radius: 8px; background: rgba(251, 191, 36, 0.05);">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <div style="font-size: 1.2rem; font-weight: 600; margin-bottom: 0.5rem;">{{ $userTeam->name }}</div>
                                    <div style="color: var(--text-muted); font-size: 0.9rem;">
                                        💰 <strong>Budget:</strong> ₹{{ number_format($auction->budget, 2) }}
                                    </div>
                                    <div style="color: var(--text-muted); font-size: 0.9rem;">
                                        👥 Players: {{ $userTeam->players->count() }}/{{ $auction->max_players }}
                                    </div>
                                </div>
                                <div style="text-align: center;">
                                    <div style="font-size: 2rem;">🏆</div>
                                    <span style="background: var(--accent); color: #000; padding: 4px 12px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; display: inline-block;">
                                        YOUR TEAM
                                    </span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div style="border: var(--glass-border); padding: 1rem; border-radius: 8px; text-align: center; color: var(--text-muted);">
                            <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">❓</div>
                            <div>No team assigned yet. Contact the auction creator.</div>
                        </div>
                    @endif
                </div>

                <!-- Auction Progress -->
                <div style="margin-bottom: 1.5rem;">
                    <h4 style="margin-bottom: 1rem; color: var(--primary);">� Auction Progress</h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
                        <div style="text-align: center; padding: 1rem; border: var(--glass-border); border-radius: 8px;">
                            <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">👥</div>
                            <div style="font-weight: 600;">{{ $auction->teams->count() }}/{{ $auction->total_teams }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">Teams Joined</div>
                        </div>
                        <div style="text-align: center; padding: 1rem; border: var(--glass-border); border-radius: 8px;">
                            <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">👤</div>
                            <div style="font-weight: 600;">{{ $auction->participants->count() }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">Participants</div>
                        </div>
                        <div style="text-align: center; padding: 1rem; border: var(--glass-border); border-radius: 8px;">
                            <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">🎯</div>
                            <div style="font-weight: 600;">{{ $auction->auctionPlayers->count() }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">Total Players</div>
                        </div>
                        @if($auction->status === 'completed' || $auction->status === 'live')
                            <div style="text-align: center; padding: 1rem; border: var(--glass-border); border-radius: 8px;">
                                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">💰</div>
                                <div style="font-weight: 600;">${{ number_format($auction->history->where('action', 'player_sold')->sum('amount'), 0) }}</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">Total Spent</div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    @if($auction->status === 'live')
                        <a href="{{ route('auctions.live', $auction) }}" class="btn btn-accent">
                            🔴 Enter Live Auction
                        </a>
                    @endif
                    <a href="{{ route('auctions.show', $auction) }}" class="btn btn-primary">
                        📊 View Details
                    </a>
                                        @if($auction->status === 'setup' && $auction->created_by !== auth()->id())
                        <button onclick="leaveAuction({{ $auction->id }})" class="btn" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                            🚪 Leave Auction
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="glass-card" style="text-align: center; padding: 4rem;">
                <div style="font-size: 4rem; margin-bottom: 1rem;">📋</div>
                <h3>No Joined Auctions</h3>
                <p style="color: var(--text-muted); margin-bottom: 2rem;">You haven't joined any auctions yet. Find and join an auction to get started!</p>
                <div style="display: flex; gap: 1rem; justify-content: center;">
                    <a href="{{ route('auctions.join') }}" class="btn btn-primary">🔗 Join Auction</a>
                    <a href="{{ route('dashboard') }}" class="btn" style="background: rgba(255,255,255,0.1);">🏠 Browse Auctions</a>
                </div>
            </div>
        @endforelse

        @if($joinedAuctions->hasPages())
            <div class="pagination-wrapper">
                <div class="pagination-info">
                    Showing <span>{{ $joinedAuctions->firstItem() }}</span> to <span>{{ $joinedAuctions->lastItem() }}</span> of <span>{{ $joinedAuctions->total() }}</span> auctions
                </div>
                {{ $joinedAuctions->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function leaveAuction(auctionId) {
    if (confirm('Are you sure you want to leave this auction? You will lose access to your team and any progress made.')) {
        fetch(`/auctions/${auctionId}/leave`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Failed to leave auction');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
}
</script>
@endsection
