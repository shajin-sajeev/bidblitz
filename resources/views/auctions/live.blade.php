@extends('layouts.app')

@section('content')
@php
    $summary = [
        'total' => $auctionPlayers->count(),
        'pending' => $auctionPlayers->where('status', 'pending')->count(),
        'sold' => $auctionPlayers->where('status', 'sold')->count(),
        'unsold' => $auctionPlayers->where('status', 'unsold')->count(),
        'spent' => $auctionPlayers->where('status', 'sold')->sum('sold_price'),
    ];
@endphp

<style>
.live-shell {
    display: grid;
    gap: 1.25rem;
}

.live-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.live-status {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 0.35rem 0.75rem;
    background: rgba(16, 185, 129, 0.14);
    color: #34d399;
    border: 1px solid rgba(16, 185, 129, 0.25);
    font-size: 0.8rem;
    font-weight: 800;
    text-transform: uppercase;
}

.live-tabs {
    display: flex;
    gap: 0.5rem;
    border-bottom: 1px solid var(--border-color);
    flex-wrap: wrap;
}

.live-tab-button {
    border: 0;
    background: transparent;
    color: var(--text-muted);
    padding: 0.8rem 1rem;
    font-weight: 800;
    cursor: pointer;
    border-radius: 8px 8px 0 0;
}

.live-tab-button.active {
    color: #111827;
    background: linear-gradient(135deg, var(--primary), var(--accent));
}

.live-tab-panel {
    display: none;
}

.live-tab-panel.active {
    display: block;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
}

.summary-card,
.team-card,
.player-row,
.setup-row,
.assignment-panel {
    border: 1px solid var(--border-color);
    background: color-mix(in srgb, var(--card-bg) 94%, var(--primary) 6%);
    border-radius: 12px;
}

.summary-card {
    padding: 1rem;
}

.summary-card span {
    display: block;
    color: var(--text-muted);
    font-size: 0.78rem;
    font-weight: 800;
    text-transform: uppercase;
}

.summary-card strong {
    display: block;
    color: var(--text-main);
    font-size: 1.45rem;
    margin-top: 0.25rem;
}

.team-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1rem;
}

.team-card {
    padding: 1rem;
}

.team-card-header {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1rem;
}

.team-card h4 {
    margin: 0;
    color: var(--text-main);
}

.owner-name {
    color: var(--text-muted);
    font-size: 0.86rem;
    margin-top: 0.25rem;
}

.budget-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.65rem;
    margin-bottom: 1rem;
}

.budget-pill {
    border-radius: 10px;
    padding: 0.65rem;
    background: color-mix(in srgb, var(--form-bg) 88%, transparent);
    border: 1px solid var(--border-color);
}

.budget-pill span {
    display: block;
    color: var(--text-muted);
    font-size: 0.72rem;
    font-weight: 800;
    text-transform: uppercase;
}

.budget-pill strong {
    color: var(--text-main);
    display: block;
    margin-top: 0.15rem;
}

.roster-list {
    display: grid;
    gap: 0.45rem;
}

.roster-item {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    padding: 0.55rem 0.65rem;
    border-radius: 8px;
    background: color-mix(in srgb, var(--form-bg) 86%, transparent);
    color: var(--text-main);
    font-size: 0.86rem;
}

.assignment-panel {
    padding: 1rem;
    margin-top: 1rem;
}

.assignment-grid,
.setup-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
    gap: 1rem;
    align-items: end;
}

.player-board {
    display: grid;
    grid-template-columns: minmax(280px, 420px) 1fr;
    gap: 1rem;
}

.wheel-card {
    text-align: center;
}

.spin-wheel {
    width: 260px;
    height: 260px;
    margin: 0 auto 1rem;
    border-radius: 50%;
    display: grid;
    place-items: center;
    background:
        conic-gradient(from 0deg, #fbbf24, #14b8a6, #6366f1, #f43f5e, #fbbf24);
    padding: 12px;
    box-shadow: 0 18px 50px rgba(0, 0, 0, 0.2);
}

.spin-wheel.is-spinning {
    animation: spinWheel 1s cubic-bezier(0.2, 0.8, 0.2, 1);
}

.wheel-center {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    display: grid;
    place-items: center;
    padding: 1.25rem;
    background: var(--card-bg);
    color: var(--text-main);
    font-weight: 900;
    text-align: center;
}

@keyframes spinWheel {
    to { transform: rotate(1080deg); }
}

.player-list {
    display: grid;
    gap: 0.75rem;
    max-height: 560px;
    overflow-y: auto;
    padding-right: 0.35rem;
}

.player-row {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 1rem;
    padding: 0.85rem;
    align-items: center;
}

.player-row h4 {
    margin: 0;
}

.player-meta {
    color: var(--text-muted);
    font-size: 0.84rem;
}

.status-pill {
    border-radius: 999px;
    padding: 0.32rem 0.7rem;
    font-size: 0.72rem;
    font-weight: 900;
    text-transform: uppercase;
}

.status-pending { background: rgba(245, 158, 11, 0.16); color: #d97706; }
.status-sold { background: rgba(16, 185, 129, 0.16); color: #059669; }
.status-unsold { background: rgba(239, 68, 68, 0.16); color: #dc2626; }

.setup-row {
    padding: 1rem;
    margin-bottom: 1rem;
}

body.light-theme .summary-card,
body.light-theme .team-card,
body.light-theme .player-row,
body.light-theme .setup-row,
body.light-theme .assignment-panel {
    background: #ffffff;
    border-color: rgba(15, 23, 42, 0.12);
}

@media (max-width: 850px) {
    .live-header,
    .team-card-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .player-board {
        grid-template-columns: 1fr;
    }

    .budget-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="live-shell">
    <div class="glass-card live-header">
        <div>
            <h2 style="margin-bottom: 0.35rem;">Live Auction: {{ $auction->name }}</h2>
            <p style="margin: 0;">Manage teams, spin players, assign purchases, and track every budget in one place.</p>
        </div>
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
            <span class="live-status">{{ ucfirst($auction->status) }}</span>
            @if($isOwner && $auction->status === 'active')
                <form method="POST" action="{{ route('auctions.start', $auction) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">Start Auction</button>
                </form>
            @endif
            @if($isOwner && $auction->status === 'pending')
                <a href="{{ route('auctions.pool', $auction) }}" class="btn btn-accent">Manage Pool</a>
            @endif
        </div>
    </div>

    <div class="glass-card">
        <div class="live-tabs">
            <button type="button" class="live-tab-button active" data-tab="live-summary">Live Summary</button>
            <button type="button" class="live-tab-button" data-tab="players-wheel">Players & Wheel</button>
            @if($isOwner)
                <button type="button" class="live-tab-button" data-tab="team-setup">Team Setup</button>
            @endif
        </div>

        <div id="live-summary" class="live-tab-panel active" style="padding-top: 1.25rem;">
            <div class="summary-grid mb-8">
                <div class="summary-card"><span>Total Players</span><strong>{{ $summary['total'] }}</strong></div>
                <div class="summary-card"><span>Pending</span><strong>{{ $summary['pending'] }}</strong></div>
                <div class="summary-card"><span>Sold</span><strong>{{ $summary['sold'] }}</strong></div>
                <div class="summary-card"><span>Unsold</span><strong>{{ $summary['unsold'] }}</strong></div>
                <div class="summary-card"><span>Total Spent</span><strong>Rs. {{ number_format($summary['spent']) }}</strong></div>
                <div class="summary-card"><span>Budget / Team</span><strong>Rs. {{ number_format($auction->budget) }}</strong></div>
            </div>

            @if($isOwner)
                <div class="assignment-panel mb-8">
                    <h3>Assign Purchased Player</h3>
                    <form method="POST" action="{{ route('auctions.live.assign', $auction) }}" class="assignment-grid">
                        @csrf
                        <div>
                            <label class="form-label">Player</label>
                            <select name="auction_player_id" id="assign-player-select" class="form-control" required>
                                @foreach($auctionPlayers->where('status', 'pending') as $item)
                                    <option value="{{ $item->id }}" data-base-price="{{ $item->base_price }}">{{ $item->player->name ?? 'Unknown Player' }} - Rs. {{ number_format($item->base_price) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Team Owner / Team</label>
                            <select name="team_id" class="form-control" required>
                                @foreach($auction->teams as $team)
                                    <option value="{{ $team->id }}">{{ $team->name }} - {{ $team->ownerPlayer->name ?? 'Owner not assigned' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Sold Price</label>
                            <input type="number" name="price" id="assign-price" class="form-control" min="0" step="1" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Team</button>
                    </form>
                </div>
            @endif

            <div class="team-grid">
                @foreach($teamSummaries as $team)
                    <div class="team-card">
                        <div class="team-card-header">
                            <div>
                                <h4>{{ $team['name'] }}</h4>
                                <div class="owner-name">{{ $team['owner'] }}</div>
                            </div>
                            <span class="status-pill status-sold">{{ $team['players_count'] }} Players</span>
                        </div>
                        <div class="budget-grid">
                            <div class="budget-pill"><span>Spent</span><strong>Rs. {{ number_format($team['spent']) }}</strong></div>
                            <div class="budget-pill"><span>Remaining</span><strong>Rs. {{ number_format($team['remaining']) }}</strong></div>
                            <div class="budget-pill"><span>Max Bid</span><strong>Rs. {{ number_format($team['max_bid']) }}</strong></div>
                        </div>
                        <div class="roster-list">
                            @forelse($team['players'] as $player)
                                <div class="roster-item">
                                    <span>{{ $player['name'] }}</span>
                                    <strong>Rs. {{ number_format($player['price']) }}</strong>
                                </div>
                            @empty
                                <div class="roster-item"><span>No players purchased yet.</span><strong>-</strong></div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div id="players-wheel" class="live-tab-panel" style="padding-top: 1.25rem;">
            <div class="player-board">
                <div class="glass-card wheel-card" style="box-shadow: none;">
                    <div id="spin-wheel" class="spin-wheel">
                        <div class="wheel-center">
                            <div>
                                <div style="color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; font-weight: 800;">Selected Player</div>
                                <div id="selected-player-name" style="font-size: 1.35rem; margin-top: 0.35rem;">
                                    {{ $currentPlayer['name'] ?? 'Spin to choose' }}
                                </div>
                                <div id="selected-player-price" style="color: var(--primary); margin-top: 0.25rem;">
                                    @if($currentPlayer) Rs. {{ number_format($currentPlayer['base_price']) }} @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($isOwner)
                        <button type="button" class="btn btn-primary" onclick="spinPlayer()" {{ $auction->status !== 'live' ? 'disabled' : '' }}>Spin Random Player</button>
                    @endif
                    @if($auction->status !== 'live')
                        <p style="margin-top: 1rem;">Start the auction to enable spinning.</p>
                    @endif
                </div>

                <div>
                    <h3>Complete Player List</h3>
                    <div class="player-list">
                        @foreach($auctionPlayers as $item)
                            <div class="player-row" data-auction-player-id="{{ $item->id }}">
                                <div>
                                    <h4>{{ $item->player->name ?? 'Unknown Player' }}</h4>
                                    <div class="player-meta">
                                        {{ $item->player->specialization ?? 'All-rounder' }} |
                                        Base Rs. {{ number_format($item->base_price) }}
                                        @if($item->team)
                                            | {{ $item->team->name }} for Rs. {{ number_format($item->sold_price) }}
                                        @endif
                                    </div>
                                </div>
                                <span class="status-pill status-{{ $item->status }}">{{ ucfirst($item->status) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        @if($isOwner)
            <div id="team-setup" class="live-tab-panel" style="padding-top: 1.25rem;">
                <h3>Participating Teams</h3>
                <p>Set the team name and choose the team owner from the auction player list.</p>
                <form method="POST" action="{{ route('auctions.live.teams.save', $auction) }}">
                    @csrf
                    @foreach($auction->teams as $index => $team)
                        <div class="setup-row">
                            <div class="setup-grid">
                                <input type="hidden" name="teams[{{ $index }}][id]" value="{{ $team->id }}">
                                <div>
                                    <label class="form-label">Team Name</label>
                                    <input type="text" name="teams[{{ $index }}][name]" class="form-control" value="{{ $team->name }}" required>
                                </div>
                                <div>
                                    <label class="form-label">Team Owner</label>
                                    <select name="teams[{{ $index }}][owner_player_id]" class="form-control">
                                        <option value="">Choose owner</option>
                                        @foreach($poolPlayers as $player)
                                            <option value="{{ $player->id }}" @selected($team->owner_player_id === $player->id)>
                                                {{ $player->name }} ({{ $player->specialization ?? 'Player' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <button type="submit" class="btn btn-primary">Save Teams</button>
                </form>
            </div>
        @endif
    </div>
</div>

<script>
const auctionId = {{ $auction->id }};

document.querySelectorAll('.live-tab-button').forEach(button => {
    button.addEventListener('click', () => {
        document.querySelectorAll('.live-tab-button').forEach(item => item.classList.remove('active'));
        document.querySelectorAll('.live-tab-panel').forEach(item => item.classList.remove('active'));
        button.classList.add('active');
        document.getElementById(button.dataset.tab).classList.add('active');
    });
});

const assignPlayerSelect = document.getElementById('assign-player-select');
const assignPrice = document.getElementById('assign-price');

function syncAssignPrice() {
    if (!assignPlayerSelect || !assignPrice) return;
    const option = assignPlayerSelect.selectedOptions[0];
    assignPrice.value = option ? option.dataset.basePrice || '' : '';
}

if (assignPlayerSelect) {
    assignPlayerSelect.addEventListener('change', syncAssignPrice);
    syncAssignPrice();
}

function spinPlayer() {
    const wheel = document.getElementById('spin-wheel');
    wheel.classList.remove('is-spinning');
    void wheel.offsetWidth;
    wheel.classList.add('is-spinning');

    fetch(`/auctions/${auctionId}/spin`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(async response => {
        const data = await response.json();
        if (!response.ok) {
            throw new Error(data.message || 'Unable to spin player.');
        }
        return data;
    })
    .then(player => {
        setTimeout(() => {
            document.getElementById('selected-player-name').innerText = player.name;
            document.getElementById('selected-player-price').innerText = `Rs. ${Number(player.base_price).toLocaleString()}`;
            if (assignPlayerSelect) {
                assignPlayerSelect.value = player.auction_player_id;
                syncAssignPrice();
            }
        }, 650);
    })
    .catch(error => {
        alert(error.message);
    });
}
</script>
@endsection
