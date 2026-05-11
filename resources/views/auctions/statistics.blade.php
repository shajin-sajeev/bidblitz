@extends('layouts.app')

@section('content')
<div class="auction-detail-layout">
    <!-- Sidebar -->
    <aside class="auction-sidebar">
        @include('partials.sidebar')
    </aside>

    <!-- Main Content -->
    <main class="auction-main-content">
        <!-- Statistics Header -->
        <div class="glass-card mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 style="margin: 0 0 1rem 0;">📊 Auction Statistics</h1>
                    <h2 style="margin: 0; color: var(--text-muted); font-size: 1.2rem;">{{ $auction->name }}</h2>
                    <div style="margin-top: 0.5rem; color: var(--text-muted); font-size: 0.9rem;">
                        <span style="background: {{ $auction->status === 'live' ? '#10b981' : ($auction->status === 'completed' ? '#ef4444' : ($auction->status === 'setup' ? '#3b82f6' : 'var(--primary)')) }}; padding: 4px 12px; border-radius: 12px; font-size: 0.8rem; color: white; font-weight: 600;">
                            {{ ucfirst($auction->status) }}
                        </span>
                    </div>
                </div>
                <div style="text-align: right;">
                    <a href="{{ route('auctions.show', $auction) }}" class="btn" style="background: rgba(255,255,255,0.1);">
                        🔙 Back to Details
                    </a>
                </div>
            </div>
        </div>

        <!-- Overview Statistics -->
        <div class="glass-card mb-6">
            <h2 style="margin-bottom: 1.5rem; color: var(--primary);">📈 Overview</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                <div style="text-align: center; padding: 1.5rem; border: var(--glass-border); border-radius: 12px; background: rgba(59, 130, 246, 0.05);">
                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">👥</div>
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 0.5rem;">{{ $auction->teams->count() }}/{{ $auction->total_teams }}</div>
                    <div style="font-size: 0.9rem; color: var(--text-muted);">Teams Joined</div>
                    <div style="margin-top: 0.5rem; font-size: 0.8rem;">
                        {{ round(($auction->teams->count() / $auction->total_teams) * 100, 1) }}% Complete
                    </div>
                </div>
                <div style="text-align: center; padding: 1.5rem; border: var(--glass-border); border-radius: 12px; background: rgba(16, 185, 129, 0.05);">
                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">👤</div>
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 0.5rem;">{{ $auction->participants->count() }}</div>
                    <div style="font-size: 0.9rem; color: var(--text-muted);">Participants</div>
                </div>
                <div style="text-align: center; padding: 1.5rem; border: var(--glass-border); border-radius: 12px; background: rgba(251, 191, 36, 0.05);">
                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🎯</div>
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 0.5rem;">{{ $playerStats['total_players'] ?? $auction->auctionPlayers->count() }}</div>
                    <div style="font-size: 0.9rem; color: var(--text-muted);">Total Players</div>
                </div>
                <div style="text-align: center; padding: 1.5rem; border: var(--glass-border); border-radius: 12px; background: rgba(239, 68, 68, 0.05);">
                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">💰</div>
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 0.5rem;">₹{{ number_format($auction->history->where('action', 'player_sold')->sum('amount'), 0) }}</div>
                    <div style="font-size: 0.9rem; color: var(--text-muted);">Total Spent</div>
                </div>
            </div>
        </div>

        <!-- Bidding Statistics -->
        <div class="glass-card mb-6">
            <h2 style="margin-bottom: 1.5rem; color: var(--primary);">💰 Bidding Activity</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                <div style="text-align: center; padding: 1.5rem; border: var(--glass-border); border-radius: 12px; background: rgba(147, 51, 234, 0.05);">
                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📊</div>
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 0.5rem;">{{ $bidStats['total_bids'] ?? 0 }}</div>
                    <div style="font-size: 0.9rem; color: var(--text-muted);">Total Bids</div>
                </div>
                <div style="text-align: center; padding: 1.5rem; border: var(--glass-border); border-radius: 12px; background: rgba(59, 130, 246, 0.05);">
                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">👥</div>
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 0.5rem;">{{ $bidStats['unique_bidders'] ?? 0 }}</div>
                    <div style="font-size: 0.9rem; color: var(--text-muted);">Active Bidders</div>
                </div>
                <div style="text-align: center; padding: 1.5rem; border: var(--glass-border); border-radius: 12px; background: rgba(16, 185, 129, 0.05);">
                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📈</div>
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 0.5rem;">₹{{ number_format($bidStats['average_bid_amount'] ?? 0, 2) }}</div>
                    <div style="font-size: 0.9rem; color: var(--text-muted);">Average Bid</div>
                </div>
                <div style="text-align: center; padding: 1.5rem; border: var(--glass-border); border-radius: 12px; background: rgba(251, 191, 36, 0.05);">
                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">⬆️</div>
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 0.5rem;">₹{{ number_format($bidStats['highest_bid'] ?? 0, 2) }}</div>
                    <div style="font-size: 0.9rem; color: var(--text-muted);">Highest Bid</div>
                </div>
            </div>
        </div>

        <!-- Player Statistics -->
        <div class="glass-card mb-6">
            <h2 style="margin-bottom: 1.5rem; color: var(--primary);">👤 Player Outcomes</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                <div style="text-align: center; padding: 1.5rem; border: var(--glass-border); border-radius: 12px; background: rgba(16, 185, 129, 0.05);">
                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">✅</div>
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 0.5rem;">{{ $playerStats['sold_players'] ?? 0 }}</div>
                    <div style="font-size: 0.9rem; color: var(--text-muted);">Sold Players</div>
                    @if($playerStats['sold_players'] > 0)
                        <div style="margin-top: 0.5rem; font-size: 0.8rem;">
                            {{ round(($playerStats['sold_players'] / ($playerStats['total_players'] ?? 1)) * 100, 1) }}% Success Rate
                        </div>
                    @endif
                </div>
                <div style="text-align: center; padding: 1.5rem; border: var(--glass-border); border-radius: 12px; background: rgba(239, 68, 68, 0.05);">
                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">❌</div>
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 0.5rem;">{{ $playerStats['unsold_players'] ?? 0 }}</div>
                    <div style="font-size: 0.9rem; color: var(--text-muted);">Unsold Players</div>
                    @if($playerStats['unsold_players'] > 0)
                        <div style="margin-top: 0.5rem; font-size: 0.8rem;">
                            {{ round(($playerStats['unsold_players'] / ($playerStats['total_players'] ?? 1)) * 100, 1) }}% Unsold
                        </div>
                    @endif
                </div>
                <div style="text-align: center; padding: 1.5rem; border: var(--glass-border); border-radius: 12px; background: rgba(251, 191, 36, 0.05);">
                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">💵</div>
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 0.5rem;">₹{{ number_format($playerStats['average_player_price'] ?? 0, 2) }}</div>
                    <div style="font-size: 0.9rem; color: var(--text-muted);">Average Price</div>
                </div>
                <div style="text-align: center; padding: 1.5rem; border: var(--glass-border); border-radius: 12px; background: rgba(147, 51, 234, 0.05);">
                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📊</div>
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 0.5rem;">{{ ($playerStats['total_players'] ?? 0) - (($playerStats['sold_players'] ?? 0) + ($playerStats['unsold_players'] ?? 0)) }}</div>
                    <div style="font-size: 0.9rem; color: var(--text-muted);">Pending Players</div>
                </div>
            </div>
        </div>

        <!-- Team Performance -->
        <div class="glass-card mb-6">
            <h2 style="margin-bottom: 1.5rem; color: var(--primary);">🏆 Team Performance</h2>
            <div style="display: grid; gap: 1.5rem;">
                @foreach($auction->teams as $team)
                    <div style="border: var(--glass-border); padding: 1.5rem; border-radius: 12px; background: {{ $team->owner_id === auth()->id() ? 'rgba(251, 191, 36, 0.1)' : 'rgba(255,255,255,0.02)' }};">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                            <div>
                                <h3 style="margin: 0; font-size: 1.1rem;">{{ $team->name }}</h3>
                                @if($team->owner)
                                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">
                                        👤 {{ $team->owner->name }}
                                    </div>
                                @else
                                    <div style="font-size: 0.85rem; color: #ef4444; margin-top: 0.25rem;">
                                        ❌ No owner assigned
                                    </div>
                                @endif
                            </div>
                            @if($team->owner_id === auth()->id())
                                <span style="background: var(--accent); color: #000; padding: 4px 12px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">
                                    YOURS
                                </span>
                            @endif
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 1rem; font-size: 0.9rem;">
                            <div>
                                <div style="color: var(--text-muted);">Players</div>
                                <div style="font-weight: 600;">{{ $team->players->count() }}/{{ $auction->max_players }}</div>
                            </div>
                            <div>
                                <div style="color: var(--text-muted);">Spent</div>
                                <div style="font-weight: 600;">₹{{ number_format($team->players->sum(function($auctionPlayer) { return $auctionPlayer->sold_price ?? 0; }), 2) }}</div>
                            </div>
                            <div>
                                <div style="color: var(--text-muted);">Remaining</div>
                                <div style="font-weight: 600; color: {{ ($team->budget ?? $auction->budget) - $team->players->sum(function($auctionPlayer) { return $auctionPlayer->sold_price ?? 0; }) > 0 ? '#10b981' : '#ef4444' }};">
                                    ₹{{ number_format(($team->budget ?? $auction->budget) - $team->players->sum(function($auctionPlayer) { return $auctionPlayer->sold_price ?? 0; }), 2) }}
                                </div>
                            </div>
                            <div>
                                <div style="color: var(--text-muted);">Avg Price</div>
                                <div style="font-weight: 600;">${{ $team->players->count() > 0 ? number_format($team->players->sum(function($auctionPlayer) { return $auctionPlayer->sold_price ?? 0; }) / $team->players->count(), 2) : '0.00' }}</div>
                            </div>
                        </div>
                        @if($team->players->count() > 0)
                            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.1);">
                                <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem;">Recent Purchases:</div>
                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                    @foreach($team->players->take(3) as $auctionPlayer)
                                        @if($auctionPlayer->player)
                                            <span style="background: rgba(255,255,255,0.1); padding: 2px 8px; border-radius: 12px; font-size: 0.8rem;">
                                                {{ $auctionPlayer->player->name }} (₹{{ number_format($auctionPlayer->sold_price ?? 0, 2) }} )
                                            </span>
                                        @endif
                                    @endforeach
                                    @if($team->players->count() > 3)
                                        <span style="background: rgba(255,255,255,0.1); padding: 2px 8px; border-radius: 12px; font-size: 0.8rem;">
                                            +{{ $team->players->count() - 3 }} more
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Activity Timeline -->
        @if($history && $history->count() > 0)
            <div class="glass-card mb-6">
                <h2 style="margin-bottom: 1.5rem; color: var(--primary);">📜 Activity Timeline</h2>
                <div style="display: grid; gap: 1rem;">
                    @foreach($history->take(20) as $activity)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: rgba(255,255,255,0.02); border-radius: 8px; border-left: 4px solid {{ $activity->action === 'player_sold' ? '#10b981' : ($activity->action === 'player_unsold' ? '#ef4444' : ($activity->action === 'bid_placed' ? '#3b82f6' : '#8b5cf6')) }};">
                            <div>
                                <div style="font-weight: 600; margin-bottom: 0.25rem;">
                                    @if($activity->action === 'player_sold')
                                        🎉 {{ $activity->player->name ?? 'Unknown Player' }} sold to {{ $activity->team->name ?? 'Unknown Team' }}
                                    @elseif($activity->action === 'player_unsold')
                                        ❌ {{ $activity->player->name ?? 'Unknown Player' }} remained unsold
                                    @elseif($activity->action === 'bid_placed')
                                        💰 {{ $activity->bidder->name ?? 'Someone' }} bid ₹{{ number_format($activity->amount, 2) }} for {{ $activity->player->name ?? 'Unknown Player' }}
                                    @else
                                        📋 {{ ucfirst(str_replace('_', ' ', $activity->action)) }}
                                    @endif
                                </div>
                                <div style="font-size: 0.85rem; color: var(--text-muted);">
                                    {{ $activity->action_at instanceof \Carbon\Carbon ? $activity->action_at->format('M d, Y H:i') : date('M d, Y H:i', strtotime($activity->action_at)) }}
                                </div>
                            </div>
                            @if($activity->action === 'player_sold')
                                <div style="text-align: right;">
                                    <div style="font-weight: 600; color: #10b981;">₹{{ number_format($activity->amount, 2) }}</div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted);">Final Price</div>
                                </div>
                            @elseif($activity->action === 'bid_placed')
                                <div style="text-align: right;">
                                    <div style="font-weight: 600; color: #3b82f6;">₹{{ number_format($activity->amount, 2) }}</div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted);">Bid Amount</div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                @if($history->count() > 20)
                    <div style="text-align: center; margin-top: 1.5rem;">
                        <div style="color: var(--text-muted); font-size: 0.9rem;">
                            Showing 20 of {{ $history->count() }} activities
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="glass-card">
            <div style="display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center;">
                <a href="{{ route('auctions.show', $auction) }}" class="btn btn-primary">
                    📊 Back to Auction Details
                </a>
                @if($auction->status === 'live')
                    <a href="{{ route('auctions.live', $auction) }}" class="btn btn-accent">
                        🔴 Enter Live Auction
                    </a>
                @endif
                <a href="{{ route('auctions.joined') }}" class="btn" style="background: rgba(255,255,255,0.1);">
                    🔙 Back to Joined Auctions
                </a>
            </div>
        </div>
    </main>
</div>

<style>
.auction-detail-layout {
    display: flex;
    gap: 2rem;
    align-items: flex-start;
    width: 100%;
    min-height: calc(100vh - 80px);
}

.auction-sidebar {
    width: 280px;
    flex-shrink: 0;
    position: sticky;
    top: 20px;
}

.auction-main-content {
    flex: 1;
    min-width: 0;
    max-width: calc(100% - 320px);
}

/* Responsive adjustments */
@media (max-width: 1024px) {
    .auction-detail-layout {
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .auction-sidebar {
        width: 100%;
        position: static;
    }
    
    .auction-main-content {
        max-width: 100%;
    }
}

@media (max-width: 768px) {
    .auction-detail-layout {
        display: block;
        width: 100%;
        min-height: auto;
    }

    .auction-sidebar {
        display: none;
    }

    .auction-main-content {
        width: 100%;
        max-width: 100%;
        display: grid;
        gap: 0.9rem;
    }

    .auction-main-content > .glass-card {
        margin-bottom: 0 !important;
        padding: 1rem !important;
        border-radius: 22px !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        box-shadow: 0 16px 34px rgba(0, 0, 0, 0.2);
    }

    .auction-main-content > .glass-card:first-child {
        overflow: hidden;
        background:
            linear-gradient(135deg, rgba(251, 191, 36, 0.18), rgba(14, 165, 233, 0.08) 52%, rgba(15, 23, 42, 0.78)),
            var(--card-bg) !important;
    }

    .auction-main-content > .glass-card:first-child::before {
        content: "";
        display: block;
        height: 4px;
        margin: -1rem -1rem 1rem;
        background: linear-gradient(90deg, var(--primary), rgba(14, 165, 233, 0.9));
    }

    .auction-main-content > .glass-card:first-child > .flex {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 1rem !important;
        align-items: stretch !important;
    }

    .auction-main-content > .glass-card:first-child h1 {
        margin-bottom: 0.55rem !important;
        font-size: clamp(1.65rem, 8vw, 2.2rem);
        line-height: 1.1;
        overflow-wrap: anywhere;
    }

    .auction-main-content > .glass-card:first-child h2 {
        font-size: 0.98rem !important;
        line-height: 1.35;
        overflow-wrap: anywhere;
    }

    .auction-main-content > .glass-card:first-child > .flex > div:last-child {
        text-align: left !important;
    }

    .auction-main-content > .glass-card:first-child .btn,
    .auction-main-content > .glass-card:last-child .btn {
        width: 100%;
        min-height: 48px;
        border-radius: 16px !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        font-weight: 800;
    }

    .auction-main-content > .glass-card h2 {
        margin-bottom: 1rem !important;
        font-size: 1.18rem;
        line-height: 1.2;
    }

    .auction-main-content > .glass-card > div[style*="grid-template-columns"] {
        grid-template-columns: 1fr !important;
        gap: 0.8rem !important;
    }

    .auction-main-content > .glass-card > div[style*="grid-template-columns"] > div {
        min-height: 112px;
        padding: 1rem !important;
        border-radius: 18px !important;
        display: grid;
        align-content: center;
        border-color: rgba(255, 255, 255, 0.12) !important;
        background-color: rgba(255, 255, 255, 0.045) !important;
    }

    .auction-main-content > .glass-card > div[style*="grid-template-columns"] > div > div:first-child {
        font-size: 1.45rem !important;
        margin-bottom: 0.25rem !important;
    }

    .auction-main-content > .glass-card > div[style*="grid-template-columns"] > div > div:nth-child(2) {
        font-size: 1.5rem !important;
        line-height: 1.15;
        overflow-wrap: anywhere;
    }

    .auction-main-content > .glass-card > div[style*="display: grid"] > div[style*="border: var(--glass-border)"] {
        padding: 1rem !important;
        border-radius: 18px !important;
        background-color: rgba(255, 255, 255, 0.045) !important;
    }

    .auction-main-content > .glass-card > div[style*="display: grid"] > div[style*="border: var(--glass-border)"] > div:first-child {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 0.65rem !important;
        margin-bottom: 0.9rem !important;
    }

    .auction-main-content > .glass-card > div[style*="display: grid"] > div[style*="border: var(--glass-border)"] h3 {
        font-size: 1.05rem !important;
        overflow-wrap: anywhere;
    }

    .auction-main-content > .glass-card > div[style*="display: grid"] > div[style*="border: var(--glass-border)"] > div:nth-child(2) {
        grid-template-columns: 1fr 1fr !important;
        gap: 0.75rem !important;
    }

    .auction-main-content > .glass-card > div[style*="display: grid"] > div[style*="border: var(--glass-border)"] > div:nth-child(2) > div {
        padding: 0.78rem;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .auction-main-content > .glass-card > div[style*="display: grid"] > div[style*="border-left"] {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 0.65rem !important;
        align-items: start !important;
        padding: 0.9rem !important;
        border-radius: 16px !important;
        background: rgba(255, 255, 255, 0.045) !important;
    }

    .auction-main-content > .glass-card > div[style*="display: grid"] > div[style*="border-left"] > div:last-child {
        text-align: left !important;
    }

    .auction-main-content > .glass-card:last-child {
        position: sticky;
        bottom: 0.65rem;
        z-index: 5;
        backdrop-filter: blur(18px);
    }

    .auction-main-content > .glass-card:last-child > div {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 0.65rem !important;
    }
}

@media (max-width: 420px) {
    .auction-main-content > .glass-card {
        padding: 0.9rem !important;
        border-radius: 20px !important;
    }

    .auction-main-content > .glass-card:first-child::before {
        margin: -0.9rem -0.9rem 0.9rem;
    }

    .auction-main-content > .glass-card > div[style*="display: grid"] > div[style*="border: var(--glass-border)"] > div:nth-child(2) {
        grid-template-columns: 1fr !important;
    }
}
</style>
@endsection
