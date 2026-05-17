@extends('layouts.app')

@section('content')
<div class="team-detail-screen">
    <section class="team-detail-hero glass-card">
        <div>
            <a href="{{ route('dashboard.my-teams') }}" class="team-back-link">Back to My Teams</a>
            <p class="team-eyebrow">{{ $team->auction->name }} / {{ $team->auction->sport }}</p>
            <h1>{{ $team->name }}</h1>
            <div class="team-status-row">
                <span>{{ ucfirst($team->auction->status) }}</span>
                <span>{{ $playersCount }}/{{ $team->auction->max_players }} Players</span>
            </div>
        </div>
        <div class="team-hero-actions">
            <a href="{{ route('auctions.show', $team->auction) }}" class="btn btn-primary">Auction Details</a>
            @if($team->auction->status === 'live')
                <a href="{{ route('auctions.live', $team->auction) }}" class="btn btn-accent">Enter Live</a>
            @endif
        </div>
    </section>

    <section class="team-metrics-grid">
        <div class="team-metric-card glass-card"><span>Total Budget</span><strong>Rs {{ number_format($team->auction->budget, 2) }}</strong></div>
        <div class="team-metric-card glass-card"><span>Total Spent</span><strong>Rs {{ number_format($totalSpent, 2) }}</strong></div>
        <div class="team-metric-card glass-card"><span>Remaining</span><strong class="{{ $remainingBudget >= 0 ? 'positive' : 'negative' }}">Rs {{ number_format($remainingBudget, 2) }}</strong></div>
        <div class="team-metric-card glass-card"><span>Team Pass</span><strong>{{ $team->team_pass }}</strong></div>
    </section>

    <section class="team-roster-card glass-card">
        <div class="team-section-head">
            <div>
                <h2>Team Players</h2>
                <p>Complete roster and purchase details for this team.</p>
            </div>
            <span>{{ $playersCount }} total</span>
        </div>

        @if($players->count() > 0)
            <div class="team-roster-list">
                @foreach($players as $auctionPlayer)
                    @php
                        $player = $auctionPlayer->player;
                        $price = $auctionPlayer->sold_price ?? $auctionPlayer->base_price ?? 0;
                    @endphp
                    <article class="team-player-card">
                        <div class="player-avatar">{{ strtoupper(substr($player->name ?? 'P', 0, 1)) }}</div>
                        <div class="player-main">
                            <h3>{{ $player->name ?? 'Unknown Player' }}</h3>
                            <p>{{ $player->specialization ?? $player->position ?? 'Player' }}</p>
                        </div>
                        <div class="player-price">
                            <strong>Rs {{ number_format($price, 2) }}</strong>
                            <span>{{ ucfirst($auctionPlayer->status ?? 'Purchased') }}</span>
                        </div>
                    </article>
                @endforeach
            </div>
            @if($players->hasPages())
                <div class="pagination-wrapper">
                    <div class="pagination-info">
                        Showing <span>{{ $players->firstItem() }}</span> to <span>{{ $players->lastItem() }}</span> of <span>{{ $players->total() }}</span> players
                    </div>
                    {{ $players->links() }}
                </div>
            @endif
        @else
            <div class="team-empty-state">
                <h3>No Players Yet</h3>
                <p>Players purchased during the auction will appear here.</p>
            </div>
        @endif
    </section>

    <section class="team-info-grid">
        <div class="team-info-card glass-card">
            <h2>Auction Info</h2>
            <div class="team-info-list">
                <div><span>Created by</span><strong>{{ $team->auction->creator->name ?? 'Unknown' }}</strong></div>
                <div><span>Teams</span><strong>{{ $team->auction->teams->count() }}/{{ $team->auction->total_teams }}</strong></div>
                <div><span>Players Required</span><strong>{{ $team->auction->min_players }}-{{ $team->auction->max_players }}</strong></div>
                <div><span>Minimum Bid</span><strong>Rs {{ number_format($team->auction->min_amount, 2) }}</strong></div>
            </div>
        </div>

        <div class="team-info-card glass-card">
            <h2>Owner Info</h2>
            <div class="team-info-list">
                <div><span>Owner</span><strong>{{ $team->owner->name ?? 'You' }}</strong></div>
                <div><span>Joined</span><strong>{{ $team->updated_at->format('M d, Y') }}</strong></div>
                <div><span>Team ID</span><strong>#{{ $team->id }}</strong></div>
                <div><span>Auction ID</span><strong>#{{ $team->auction->id }}</strong></div>
            </div>
        </div>
    </section>
</div>

<style>
.team-detail-screen {
    max-width: 1120px;
    margin: 0 auto;
    display: grid;
    gap: 1rem;
}

.team-detail-hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.5rem;
    padding: 1.5rem;
    border-radius: 24px;
    background: linear-gradient(135deg, rgba(251, 191, 36, 0.18), rgba(14, 165, 233, 0.08)), var(--card-bg);
}

.team-back-link {
    color: var(--primary);
    font-size: 0.85rem;
    font-weight: 800;
    text-decoration: none;
}

.team-eyebrow {
    margin: 0.85rem 0 0.45rem;
    color: var(--primary);
    font-size: 0.8rem;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

.team-detail-hero h1 {
    margin: 0;
    font-size: clamp(2.1rem, 5vw, 4rem);
    line-height: 1;
    font-weight: 900;
    overflow-wrap: anywhere;
}

.team-status-row {
    display: flex;
    flex-wrap: wrap;
    gap: 0.65rem;
    margin-top: 1rem;
}

.team-status-row span {
    padding: 0.45rem 0.8rem;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
    font-size: 0.82rem;
    font-weight: 800;
}

.team-hero-actions {
    display: flex;
    gap: 0.7rem;
    flex-wrap: wrap;
    justify-content: flex-end;
}

.team-metrics-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 1rem;
}

.team-metric-card,
.team-roster-card,
.team-info-card {
    padding: 1.2rem;
    border-radius: 20px;
}

.team-metric-card span,
.team-info-list span,
.player-price span {
    display: block;
    color: var(--text-muted);
    font-size: 0.8rem;
    font-weight: 800;
    margin-bottom: 0.35rem;
}

.team-metric-card strong {
    font-size: 1.35rem;
    overflow-wrap: anywhere;
}

.positive { color: #10b981; }
.negative { color: #ef4444; }

.team-section-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1rem;
}

.team-section-head h2,
.team-info-card h2 {
    margin: 0;
    color: var(--primary);
    font-size: 1.25rem;
}

.team-section-head p {
    margin: 0.35rem 0 0;
    color: var(--text-muted);
}

.team-section-head > span {
    flex: 0 0 auto;
    padding: 0.45rem 0.8rem;
    border-radius: 999px;
    background: rgba(251, 191, 36, 0.12);
    color: var(--primary);
    font-weight: 900;
}

.team-roster-list {
    display: grid;
    gap: 0.8rem;
}

.team-player-card {
    display: grid;
    grid-template-columns: auto minmax(0, 1fr) auto;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.055);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.player-avatar {
    width: 46px;
    height: 46px;
    border-radius: 16px;
    display: grid;
    place-items: center;
    background: var(--primary);
    color: #111827;
    font-weight: 900;
}

.player-main h3 {
    margin: 0;
    font-size: 1rem;
    font-weight: 900;
}

.player-main p {
    margin: 0.25rem 0 0;
    color: var(--text-muted);
    font-size: 0.88rem;
}

.player-price {
    text-align: right;
}

.player-price strong {
    color: var(--accent);
}

.team-info-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem;
}

.team-info-list {
    display: grid;
    gap: 0.7rem;
    margin-top: 1rem;
}

.team-info-list > div {
    padding: 0.85rem;
    border-radius: 15px;
    background: rgba(255, 255, 255, 0.055);
    border: 1px solid rgba(255, 255, 255, 0.09);
}

.team-empty-state {
    padding: 2rem;
    text-align: center;
    border-radius: 18px;
    border: 1px dashed rgba(255, 255, 255, 0.16);
}

.team-empty-state p {
    color: var(--text-muted);
}

@media (max-width: 768px) {
    .team-detail-screen {
        gap: 0.85rem;
    }

    .team-detail-hero {
        display: grid;
        grid-template-columns: 1fr;
        padding: 1.1rem;
        border-radius: 22px;
    }

    .team-detail-hero h1 {
        font-size: clamp(1.75rem, 9vw, 2.45rem);
        line-height: 1.08;
    }

    .team-hero-actions {
        display: grid;
        grid-template-columns: 1fr;
        justify-content: stretch;
    }

    .team-hero-actions .btn {
        width: 100%;
        min-height: 48px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
    }

    .team-metrics-grid,
    .team-info-grid {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }

    .team-metric-card,
    .team-roster-card,
    .team-info-card {
        padding: 1rem;
        border-radius: 20px;
    }

    .team-section-head {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.7rem;
    }

    .team-section-head > span {
        justify-self: start;
    }

    .team-player-card {
        grid-template-columns: auto minmax(0, 1fr);
        align-items: start;
        border-radius: 17px;
        padding: 0.9rem;
    }

    .player-price {
        grid-column: 1 / -1;
        text-align: left;
        padding: 0.75rem;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.06);
    }
}
</style>
@endsection
