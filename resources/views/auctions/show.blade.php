@extends('layouts.app')

@section('content')
<div class="auction-detail-layout">
    <!-- Sidebar -->
    <aside class="auction-sidebar">
        @include('partials.sidebar')
    </aside>

    <!-- Main Content -->
    <main class="auction-main-content">
        <!-- Auction Header -->
        <div class="glass-card mb-4 joined-detail-hero">
            <div class="flex justify-between items-start joined-hero-layout">
                <div>
                    <h1 style="margin: 0 0 1rem 0;">{{ $auction->name }}</h1>
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1rem;">
                        <span style="background: {{ $auction->status === 'live' ? '#10b981' : ($auction->status === 'completed' ? '#ef4444' : ($auction->status === 'setup' ? '#3b82f6' : 'var(--primary)')) }}; padding: 6px 16px; border-radius: 20px; font-size: 0.9rem; color: white; font-weight: 600;">
                            {{ ucfirst($auction->status) }}
                        </span>
                        @if($auction->status === 'live')
                            <span style="background: #ef4444; padding: 6px 16px; border-radius: 20px; font-size: 0.9rem; color: white; font-weight: 600; animation: pulse 2s infinite;">
                                🔴 LIVE
                            </span>
                        @endif
                    </div>
                    <div style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">
                        <div>🏟️ <strong>Sport:</strong> {{ $auction->sport }}</div>
                        <div>👥 <strong>Teams:</strong> {{ $auction->teams->count() }}/{{ $auction->total_teams }}</div>
                        <div>💰 <strong>Budget:</strong> ₹{{ number_format($auction->budget, 2) }}</div>
                        <div>📊 <strong>Players per Team:</strong> {{ $auction->min_players }}-{{ $auction->max_players }}</div>
                        <div>💵 <strong>Minimum Bid:</strong> ₹{{ number_format($auction->min_amount, 2) }}</div>
                        <div>👤 <strong>Created by:</strong> {{ $auction->creator->name ?? 'Unknown' }}</div>
                        <div>📅 <strong>Created:</strong> {{ $auction->created_at->format('M d, Y H:i') }}</div>
                        @if($auction->activated_at)
                            <div>🚀 <strong>Activated:</strong> {{ $auction->activated_at instanceof \Carbon\Carbon ? $auction->activated_at->format('M d, Y H:i') : date('M d, Y H:i', strtotime($auction->activated_at)) }}</div>
                        @endif
                    </div>
                </div>
                <div class="joined-hero-actions" style="text-align: right;">
                    @if($auction->created_by === auth()->id())
                        <div style="margin-bottom: 0.5rem; font-size: 0.85rem; color: var(--text-muted);">
                            Auction Pass: <strong>{{ $auction->auction_pass }}</strong>
                        </div>
                    @endif
                    @if($auction->status === 'live')
                        <a href="{{ route('auctions.live', $auction) }}" class="btn btn-accent">
                            Enter Live Auction
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- User's Team Section -->
        <div class="glass-card mb-4 joined-team-card">
            <h2 style="margin-bottom: 1.5rem; color: var(--primary);">🏆 Your Team Details</h2>
            @php
                $userTeam = $auction->teams->where('owner_id', auth()->id())->first();
            @endphp
            @if($userTeam)
                <div class="joined-team-panel" style="border: var(--glass-border); padding: 2rem; border-radius: 12px; background: rgba(251, 191, 36, 0.05);">
                    <div class="joined-team-head" style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.5rem;">
                        <div>
                            <h3 style="margin: 0 0 0.5rem 0; font-size: 1.4rem;">{{ $userTeam->name }}</h3>
                            <div style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">
                                <div>💰 <strong>Budget:</strong> ₹{{ number_format($userTeam->budget ?? $auction->budget, 2) }}</div>
                                <div>👤 <strong>Team Pass:</strong> {{ $userTeam->team_pass }}</div>
                                <div>📅 <strong>Joined:</strong> {{ $userTeam->updated_at instanceof \Carbon\Carbon ? $userTeam->updated_at->format('M d, Y H:i') : date('M d, Y H:i', strtotime($userTeam->updated_at)) }}</div>
                            </div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 3rem; margin-bottom: 0.5rem;">🏆</div>
                            <span style="background: var(--accent); color: #000; padding: 6px 16px; border-radius: 16px; font-size: 0.9rem; font-weight: 600;">
                                YOUR TEAM
                            </span>
                        </div>
                    </div>

                    <!-- Team Players -->
                    <div>
                        <h4 style="margin-bottom: 1rem;">👥 Your Players ({{ $userTeam->players->count() }}/{{ $auction->max_players }})</h4>
                        @if($userTeam->players->count() > 0)
                            <div style="display: grid; gap: 1rem;">
                                @foreach($userTeam->players as $auctionPlayer)
                                    @if($auctionPlayer->player)
                                    <div class="joined-player-row" style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: rgba(255,255,255,0.05); border-radius: 8px; border: 1px solid rgba(255,255,255,0.1);">
                                        <div>
                                            <div style="font-weight: 600; margin-bottom: 0.25rem;">{{ $auctionPlayer->player->name }}</div>
                                            <div style="font-size: 0.85rem; color: var(--text-muted);">
                                                {{ $auctionPlayer->player->position ?? 'Unknown Position' }} • {{ $auctionPlayer->player->skill_level ?? 'Unknown Skill' }}
                                            </div>
                                        </div>
                                        <div style="text-align: right;">
                                            <div style="font-weight: 600; color: var(--accent);">₹{{ number_format($auctionPlayer->sold_price ?? $auctionPlayer->base_price, 2) }}</div>
                                            <div style="font-size: 0.8rem; color: var(--text-muted);">Purchased</div>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <div style="text-align: center; padding: 2rem; color: var(--text-muted); border: 1px dashed rgba(255,255,255,0.2); border-radius: 8px;">
                                <div style="font-size: 2rem; margin-bottom: 0.5rem;">📋</div>
                                <div>No players purchased yet</div>
                            </div>
                        @endif
                    </div>

                    <!-- Team Budget Summary -->
                    <div class="joined-budget-summary" style="margin-top: 1.5rem; padding: 1rem; background: rgba(255,255,255,0.05); border-radius: 8px;">
                        <div class="joined-budget-row" style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <div style="font-size: 0.9rem; color: var(--text-muted);">Total Spent</div>
                                <div style="font-weight: 600;">₹{{ $userTeam->players->count() > 0 ? number_format($userTeam->players->sum(function($auctionPlayer) { return $auctionPlayer->sold_price ?? 0; }) / $userTeam->players->count(), 2) : '0.00' }}</div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 0.9rem; color: var(--text-muted);">Remaining Budget</div>
                                <div style="font-size: 1.2rem; font-weight: 600; color: {{ ($userTeam->budget ?? $auction->budget) - $userTeam->players->sum(function($auctionPlayer) { return $auctionPlayer->sold_price ?? $auctionPlayer->base_price; }) > 0 ? '#10b981' : '#ef4444' }};">
                                    ₹{{ number_format(($userTeam->budget ?? $auction->budget) - $userTeam->players->sum(function($auctionPlayer) { return $auctionPlayer->sold_price ?? $auctionPlayer->base_price; }), 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div style="text-align: center; padding: 3rem; color: var(--text-muted); border: 1px dashed rgba(255,255,255,0.2); border-radius: 8px;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;"> </div>
                    <h3 style="margin-bottom: 0.5rem;">No Team Assigned</h3>
                    <p>You haven't been assigned a team in this auction yet. Contact the auction creator to get your team assignment.</p>
                </div>
            @endif
        </div>

        <!-- Auction Statistics -->
        <div class="glass-card mb-4 joined-stats-card">
            <h2 style="margin-bottom: 1.5rem; color: var(--primary);"> Auction Statistics</h2>
            <div class="joined-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                <div class="joined-stat-tile" style="text-align: center; padding: 1.5rem; border: var(--glass-border); border-radius: 12px; background: rgba(59, 130, 246, 0.05);">
                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;"> </div>
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 0.5rem;">{{ $auction->teams->count() }}/{{ $auction->total_teams }}</div>
                    <div style="font-size: 0.9rem; color: var(--text-muted);">Teams Joined</div>
                    <div style="margin-top: 0.5rem; font-size: 0.8rem;">
                        {{ round(($auction->teams->count() / $auction->total_teams) * 100, 1) }}% Complete
                    </div>
                </div>
                <div class="joined-stat-tile" style="text-align: center; padding: 1.5rem; border: var(--glass-border); border-radius: 12px; background: rgba(16, 185, 129, 0.05);">
                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;"> </div>
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 0.5rem;">{{ $auction->participants->count() }}</div>
                    <div style="font-size: 0.9rem; color: var(--text-muted);">Participants</div>
                </div>
                <div class="joined-stat-tile" style="text-align: center; padding: 1.5rem; border: var(--glass-border); border-radius: 12px; background: rgba(251, 191, 36, 0.05);">
                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;"> </div>
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 0.5rem;">{{ $auction->auctionPlayers->count() }}</div>
                    <div style="font-size: 0.9rem; color: var(--text-muted);">Total Players</div>
                </div>
                <div class="joined-stat-tile" style="text-align: center; padding: 1.5rem; border: var(--glass-border); border-radius: 12px; background: rgba(239, 68, 68, 0.05);">
                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;"> </div>
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 0.5rem;">₹{{ number_format($auction->history->where('action', 'player_sold')->sum('amount'), 0) }}</div>
                    <div style="font-size: 0.9rem; color: var(--text-muted);">Total Spent</div>
                </div>
            </div>
        </div>

        <!-- All Teams Overview -->
        <div class="glass-card mb-4 joined-all-teams-card">
            <h2 style="margin-bottom: 1.5rem; color: var(--primary);"> All Teams</h2>
            <div class="joined-teams-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                @foreach($teams as $team)
                    <div class="joined-team-overview-card" style="border: var(--glass-border); padding: 1.5rem; border-radius: 12px; background: {{ $team->owner_id === auth()->id() ? 'rgba(251, 191, 36, 0.1)' : 'rgba(255,255,255,0.02)' }};">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                            <div>
                                <h3 style="margin: 0; font-size: 1.1rem;">{{ $team->name }}</h3>
                                @if($team->owner)
                                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">
                                        {{ $team->owner->name }}
                                    </div>
                                @else
                                    <div style="font-size: 0.85rem; color: #ef4444; margin-top: 0.25rem;">
                                        No owner assigned
                                    </div>
                                @endif
                            </div>
                            @if($team->owner_id === auth()->id())
                                <span style="background: var(--accent); color: #000; padding: 4px 12px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">
                                    YOURS
                                </span>
                            @endif
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; font-size: 0.9rem;">
                            <div>
                                <div style="color: var(--text-muted);">Players</div>
                                <div style="font-weight: 600;">{{ $team->players->count() }}/{{ $auction->max_players }}</div>
                            </div>
                            <div>
                                <div style="color: var(--text-muted);">Budget</div>
                                <div style="font-weight: 600;">₹{{ number_format($team->budget ?? $auction->budget, 2) }}</div>
                            </div>
                        </div>
                        @if($team->players->count() > 0)
                            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.1);">
                                <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem;">Recent Players:</div>
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
            @if($teams->hasPages())
                <div class="pagination-wrapper">
                    <div class="pagination-info">
                        Showing <span>{{ $teams->firstItem() }}</span> to <span>{{ $teams->lastItem() }}</span> of <span>{{ $teams->total() }}</span> teams
                    </div>
                    {{ $teams->links() }}
                </div>
            @endif
        </div>

        <!-- Recent Activity -->
        @if($history->count() > 0)
            <div class="glass-card mb-4 joined-activity-card">
                <h2 style="margin-bottom: 1.5rem; color: var(--primary);"> Recent Activity</h2>
                <div style="display: grid; gap: 1rem;">
                    @foreach($history as $activity)
                        <div class="joined-activity-row" style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: rgba(255,255,255,0.02); border-radius: 8px; border-left: 4px solid {{ $activity->action === 'player_sold' ? '#10b981' : ($activity->action === 'player_unsold' ? '#ef4444' : '#3b82f6') }};">
                            <div>
                                <div style="font-weight: 600; margin-bottom: 0.25rem;">
                                    @if($activity->action === 'player_sold')
                                        {{ $activity->player->name ?? 'Unknown Player' }} sold to {{ $activity->team->name ?? 'Unknown Team' }}
                                    @elseif($activity->action === 'player_unsold')
                                        {{ $activity->player->name ?? 'Unknown Player' }} remained unsold
                                    @elseif($activity->action === 'bid_placed')
                                        {{ $activity->bidder->name ?? 'Someone' }} bid ₹{{ number_format($activity->amount, 2) }} for {{ $activity->player->name ?? 'Unknown Player' }}
                                    @else
                                        {{ ucfirst(str_replace('_', ' ', $activity->action)) }}
                                    @endif
                                </div>
                                <div style="font-size: 0.85rem; color: var(--text-muted);">
                                    {{ $activity->action_at instanceof \Carbon\Carbon ? $activity->action_at->format('M d, Y H:i') : date('M d, Y H:i', strtotime($activity->action_at)) }}
                                </div>
                            </div>
                            @if($activity->action === 'player_sold')
                                <div style="text-align: right;">
                                    <div style="font-weight: 600; color: #3b82f6;">₹{{ number_format($activity->amount, 2) }}</div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted);">Final Price</div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                @if($history->hasPages())
                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Showing <span>{{ $history->firstItem() }}</span> to <span>{{ $history->lastItem() }}</span> of <span>{{ $history->total() }}</span> activities
                        </div>
                        {{ $history->links() }}
                    </div>
                @endif
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="glass-card joined-actions-card">
            <div class="joined-actions-grid" style="display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center;">
                @if($auction->status === 'live')
                    <a href="{{ route('auctions.live', $auction) }}" class="btn btn-accent">
                        🔴 Enter Live Auction
                    </a>
                @endif
                                <a href="{{ route('auctions.statistics', $auction) }}" class="btn" style="background: rgba(255,255,255,0.1);">
                    📊 View Statistics
                </a>
                <a href="{{ route('auctions.joined') }}" class="btn" style="background: rgba(255,255,255,0.1);">
                    🔙 Back to Joined Auctions
                </a>
                @if($auction->status === 'setup' && $auction->created_by !== auth()->id())
                    <button onclick="leaveAuction({{ $auction->id }})" class="btn" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                        🚪 Leave Auction
                    </button>
                @endif
            </div>
        </div>
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
                window.location.href = '/auctions/joined';
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

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
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
        min-height: auto;
        width: 100%;
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

    .auction-main-content > .glass-card,
    .joined-detail-hero,
    .joined-team-card,
    .joined-stats-card,
    .joined-all-teams-card,
    .joined-activity-card,
    .joined-actions-card {
        margin-bottom: 0 !important;
        padding: 1rem !important;
        border-radius: 22px !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        box-shadow: 0 16px 34px rgba(0, 0, 0, 0.2);
    }

    .joined-detail-hero {
        overflow: hidden;
        background:
            linear-gradient(135deg, rgba(251, 191, 36, 0.18), rgba(14, 165, 233, 0.08) 52%, rgba(15, 23, 42, 0.78)),
            var(--card-bg) !important;
    }

    .joined-detail-hero::before {
        content: "";
        display: block;
        height: 4px;
        margin: -1rem -1rem 1rem;
        background: linear-gradient(90deg, var(--primary), rgba(14, 165, 233, 0.9));
    }

    .joined-hero-layout {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 1rem !important;
        align-items: stretch !important;
    }

    .joined-detail-hero h1 {
        font-size: clamp(1.65rem, 8vw, 2.2rem);
        line-height: 1.1;
        margin-bottom: 0.85rem !important;
        overflow-wrap: anywhere;
    }

    .joined-detail-hero h1 + div {
        gap: 0.5rem !important;
        margin-bottom: 0.85rem !important;
    }

    .joined-detail-hero h1 + div span {
        min-height: 34px;
        display: inline-flex;
        align-items: center;
        border-radius: 999px !important;
        padding: 0.45rem 0.85rem !important;
        font-size: 0.78rem !important;
    }

    .joined-detail-hero h1 + div + div {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.55rem;
        color: var(--text-main) !important;
        font-size: 0.86rem !important;
        line-height: 1.35 !important;
    }

    .joined-detail-hero h1 + div + div > div,
    .joined-team-panel > .joined-team-head > div:first-child > div,
    .joined-budget-summary,
    .joined-team-overview-card > div:nth-child(2) > div {
        padding: 0.72rem 0.8rem;
        border-radius: 15px;
        background: rgba(255, 255, 255, 0.07);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .joined-hero-actions {
        text-align: left !important;
        display: grid;
        gap: 0.75rem;
    }

    .joined-hero-actions .btn,
    .joined-actions-grid .btn,
    .joined-actions-grid button {
        width: 100%;
        min-height: 48px;
        border-radius: 16px !important;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        text-align: center;
        font-weight: 800;
    }

    .joined-team-card h2,
    .joined-stats-card h2,
    .joined-all-teams-card h2,
    .joined-activity-card h2 {
        margin-bottom: 1rem !important;
        font-size: 1.18rem;
        line-height: 1.2;
    }

    .joined-team-panel {
        padding: 0.9rem !important;
        border-radius: 18px !important;
        background: rgba(251, 191, 36, 0.08) !important;
    }

    .joined-team-head {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 1rem !important;
        margin-bottom: 1rem !important;
    }

    .joined-team-head > div:last-child {
        text-align: left !important;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.8rem;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .joined-player-row {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 0.75rem !important;
        align-items: start !important;
        padding: 0.9rem !important;
        border-radius: 16px !important;
    }

    .joined-player-row > div:last-child,
    .joined-activity-row > div:last-child {
        text-align: left !important;
    }

    .joined-budget-row {
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
        gap: 0.8rem !important;
        align-items: stretch !important;
    }

    .joined-budget-row > div {
        padding: 0.85rem;
        border-radius: 14px;
        background: rgba(0, 0, 0, 0.12);
    }

    .joined-budget-row > div:last-child {
        text-align: left !important;
    }

    .joined-stats-grid,
    .joined-teams-grid {
        grid-template-columns: 1fr !important;
        gap: 0.8rem !important;
    }

    .joined-stat-tile {
        min-height: 112px;
        padding: 1rem !important;
        border-radius: 18px !important;
        display: grid;
        align-content: center;
    }

    .joined-stat-tile > div:first-child {
        font-size: 1.45rem !important;
        margin-bottom: 0.25rem !important;
    }

    .joined-stat-tile > div:nth-child(2) {
        font-size: 1.55rem !important;
    }

    .joined-team-overview-card {
        padding: 1rem !important;
        border-radius: 18px !important;
    }

    .joined-team-overview-card > div:first-child {
        gap: 0.75rem;
    }

    .joined-team-overview-card h3 {
        font-size: 1rem !important;
        overflow-wrap: anywhere;
    }

    .joined-team-overview-card > div:nth-child(2) {
        grid-template-columns: 1fr 1fr !important;
        gap: 0.75rem !important;
    }

    .joined-activity-row {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 0.65rem !important;
        align-items: start !important;
        padding: 0.9rem !important;
        border-radius: 16px !important;
    }

    .joined-actions-card {
        position: sticky;
        bottom: 0.65rem;
        z-index: 5;
        backdrop-filter: blur(18px);
    }

    .joined-actions-grid {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 0.65rem !important;
    }
}

@media (max-width: 420px) {
    .joined-budget-row,
    .joined-team-overview-card > div:nth-child(2) {
        grid-template-columns: 1fr !important;
    }

    .auction-main-content > .glass-card,
    .joined-detail-hero,
    .joined-team-card,
    .joined-stats-card,
    .joined-all-teams-card,
    .joined-activity-card,
    .joined-actions-card {
        padding: 0.9rem !important;
        border-radius: 20px !important;
    }
}
</style>
@endsection
