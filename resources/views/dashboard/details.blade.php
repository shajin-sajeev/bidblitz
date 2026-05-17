@extends('layouts.app')

@section('content')
<div class="dashboard-detail-screen" style="--detail-accent: {{ $accent }};">
    <div class="dashboard-detail-hero glass-card">
        <div>
            <a href="{{ route('dashboard') }}" class="detail-back-link">Back to Dashboard</a>
            <h1>{{ $title }}</h1>
            <p>{{ $subtitle }}</p>
        </div>
        <div class="detail-count-pill">
            <span>{{ $items->total() }}</span>
            <small>Total</small>
        </div>
    </div>

    <div class="dashboard-detail-list">
        @forelse($items as $item)
            @if($type === 'my-teams')
                @php
                    $auction = $item->auction;
                    $spent = $item->players->sum(fn($auctionPlayer) => $auctionPlayer->sold_price ?? 0);
                    $budget = $item->budget ?? $auction?->budget ?? 0;
                @endphp
                <article class="detail-record-card glass-card">
                    <div class="detail-record-head">
                        <div>
                            <div class="detail-eyebrow">{{ $auction?->name ?? 'Auction unavailable' }}</div>
                            <h2>{{ $item->name }}</h2>
                            <p>{{ $auction?->sport ?? 'Sport not available' }}</p>
                        </div>
                        <span class="detail-status-pill">{{ $auction ? ucfirst($auction->status) : 'Team' }}</span>
                    </div>

                    <div class="detail-metric-grid">
                        <div><span>Players</span><strong>{{ $item->players->count() }}{{ $auction ? '/' . $auction->max_players : '' }}</strong></div>
                        <div><span>Budget</span><strong>Rs {{ number_format($budget, 2) }}</strong></div>
                        <div><span>Spent</span><strong>Rs {{ number_format($spent, 2) }}</strong></div>
                        <div><span>Remaining</span><strong>Rs {{ number_format($budget - $spent, 2) }}</strong></div>
                    </div>

                    <div class="detail-actions">
                        @if($auction)
                            <a href="{{ route('teams.show', $item) }}" class="btn btn-primary">View Details</a>
                            <a href="{{ route('auctions.show', $auction) }}" class="btn">Auction Details</a>
                            @if($auction->status === 'live')
                                <a href="{{ route('auctions.live', $auction) }}" class="btn btn-accent">Enter Live</a>
                            @endif
                        @endif
                    </div>
                </article>
            @else
                @php
                    $soldTotal = $item->relationLoaded('history') ? $item->history->where('action', 'player_sold')->sum('amount') : null;
                @endphp
                <article class="detail-record-card glass-card">
                    <div class="detail-record-head">
                        <div>
                            <div class="detail-eyebrow">{{ $item->sport }}</div>
                            <h2>{{ $item->name }}</h2>
                            <p>Created by {{ $item->creator->name ?? 'Unknown' }} on {{ $item->created_at->format('M d, Y') }}</p>
                        </div>
                        <span class="detail-status-pill">{{ ucfirst($item->status) }}</span>
                    </div>

                    <div class="detail-metric-grid">
                        <div><span>Teams</span><strong>{{ $item->teams->count() }}/{{ $item->total_teams }}</strong></div>
                        <div><span>Budget</span><strong>Rs {{ number_format($item->budget, 2) }}</strong></div>
                        <div><span>Players</span><strong>{{ $item->min_players }}-{{ $item->max_players }}</strong></div>
                        <div><span>{{ $soldTotal !== null ? 'Sold Total' : 'Min Bid' }}</span><strong>Rs {{ number_format($soldTotal ?? $item->min_amount, 2) }}</strong></div>
                    </div>

                    <div class="detail-actions">
                        <a href="{{ route('auctions.show', $item) }}" class="btn btn-primary">View Details</a>
                        @if($item->status === 'live')
                            <a href="{{ route('auctions.live', $item) }}" class="btn btn-accent">Enter Live</a>
                        @endif
                        @if($item->created_by === auth()->id() && !in_array($item->status, ['completed', 'live'], true))
                            <a href="{{ route('auctions.pool', $item) }}" class="btn">Manage</a>
                        @endif
                        @if($item->status === 'completed')
                            <a href="{{ route('auctions.statistics', $item) }}" class="btn">Statistics</a>
                        @endif
                    </div>
                </article>
            @endif
        @empty
            <div class="empty-detail-state glass-card">
                <div>No records found</div>
                <p>This section will show details when matching data is available.</p>
                <a href="{{ route('dashboard') }}" class="btn btn-primary">Back to Overview</a>
            </div>
        @endforelse
    </div>

    @if($items->hasPages())
        <div class="pagination-wrapper">
            <div class="pagination-info">
                Showing <span>{{ $items->firstItem() }}</span> to <span>{{ $items->lastItem() }}</span> of <span>{{ $items->total() }}</span> records
            </div>
            {{ $items->links() }}
        </div>
    @endif
</div>

<style>
.dashboard-detail-screen {
    max-width: 1120px;
    margin: 0 auto;
    display: grid;
    gap: 1rem;
}

.dashboard-detail-hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.5rem;
    padding: 1.5rem;
    border-radius: 22px;
    background: linear-gradient(135deg, rgba(251, 191, 36, 0.16), rgba(14, 165, 233, 0.07)), var(--card-bg);
}

.detail-back-link {
    color: var(--detail-accent);
    font-size: 0.85rem;
    font-weight: 800;
    text-decoration: none;
}

.dashboard-detail-hero h1 {
    margin: 0.45rem 0 0.35rem;
    font-size: clamp(2rem, 4vw, 3.2rem);
    line-height: 1;
    font-weight: 900;
}

.dashboard-detail-hero p {
    margin: 0;
    color: var(--text-muted);
}

.detail-count-pill {
    min-width: 112px;
    min-height: 86px;
    border-radius: 20px;
    display: grid;
    place-items: center;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
}

.detail-count-pill span {
    color: var(--detail-accent);
    font-size: 2rem;
    font-weight: 900;
    line-height: 1;
}

.detail-count-pill small {
    color: var(--text-muted);
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

.dashboard-detail-list {
    display: grid;
    gap: 1rem;
}

.detail-record-card {
    padding: 1.25rem;
    border-radius: 20px;
}

.detail-record-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1rem;
}

.detail-eyebrow {
    color: var(--detail-accent);
    font-size: 0.78rem;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 0.35rem;
}

.detail-record-head h2 {
    margin: 0;
    font-size: 1.35rem;
    font-weight: 900;
}

.detail-record-head p {
    margin: 0.35rem 0 0;
    color: var(--text-muted);
    font-size: 0.9rem;
}

.detail-status-pill {
    flex: 0 0 auto;
    padding: 0.45rem 0.8rem;
    border-radius: 999px;
    background: rgba(251, 191, 36, 0.12);
    color: var(--detail-accent);
    border: 1px solid rgba(251, 191, 36, 0.2);
    font-size: 0.78rem;
    font-weight: 900;
}

.detail-metric-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.75rem;
}

.detail-metric-grid > div {
    padding: 0.85rem;
    border-radius: 15px;
    background: rgba(255, 255, 255, 0.055);
    border: 1px solid rgba(255, 255, 255, 0.09);
}

.detail-metric-grid span {
    display: block;
    color: var(--text-muted);
    font-size: 0.78rem;
    font-weight: 800;
    margin-bottom: 0.3rem;
}

.detail-metric-grid strong {
    color: var(--text-main);
    font-size: 1rem;
    overflow-wrap: anywhere;
}

.detail-actions {
    display: flex;
    gap: 0.65rem;
    flex-wrap: wrap;
    margin-top: 1rem;
}

.empty-detail-state {
    padding: 2rem;
    text-align: center;
    border-radius: 20px;
}

.empty-detail-state > div {
    font-size: 1.4rem;
    font-weight: 900;
}

.empty-detail-state p {
    color: var(--text-muted);
}

@media (max-width: 768px) {
    .dashboard-detail-screen {
        gap: 0.85rem;
    }

    .dashboard-detail-hero {
        display: grid;
        grid-template-columns: 1fr;
        padding: 1.1rem;
        border-radius: 22px;
    }

    .dashboard-detail-hero h1 {
        font-size: clamp(1.7rem, 8vw, 2.25rem);
        line-height: 1.08;
    }

    .detail-count-pill {
        width: 100%;
        min-height: 70px;
        display: flex;
        justify-content: space-between;
        padding: 1rem;
    }

    .detail-record-card {
        padding: 1rem;
        border-radius: 20px;
    }

    .detail-record-head {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }

    .detail-status-pill {
        justify-self: start;
    }

    .detail-metric-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.65rem;
    }

    .detail-actions {
        display: grid;
        grid-template-columns: 1fr;
    }

    .detail-actions .btn {
        width: 100%;
        min-height: 46px;
        border-radius: 15px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
    }
}

@media (max-width: 420px) {
    .detail-metric-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection
