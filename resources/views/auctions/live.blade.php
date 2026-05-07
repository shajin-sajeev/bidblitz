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

.budget-pill.warning {
    background: color-mix(in srgb, rgba(245, 158, 11, 0.16), transparent);
    border-color: rgba(245, 158, 11, 0.4);
}

.budget-pill.warning span {
    color: #d97706;
}

.budget-pill.warning strong {
    color: #d97706;
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

.auction-pass-owner-only {
    margin-top: 1rem;
    padding: 0.85rem 1rem;
    border-radius: 12px;
    border: 1px dashed color-mix(in srgb, var(--primary) 55%, transparent);
    background: color-mix(in srgb, var(--primary) 12%, transparent);
    max-width: 28rem;
}

.auction-pass-owner-only .auction-pass-label {
    display: block;
    color: var(--text-muted);
    font-size: 0.72rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-bottom: 0.5rem;
}

.auction-pass-row {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.5rem;
}

.auction-pass-code {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    letter-spacing: 0.15em;
    font-size: 1.15rem;
    font-weight: 800;
    color: var(--text-main);
    padding: 0.35rem 0.65rem;
    border-radius: 8px;
    background: color-mix(in srgb, var(--card-bg) 88%, var(--primary) 12%);
    border: 1px solid var(--border-color);
}

.auction-pass-hint {
    margin: 0.5rem 0 0;
    font-size: 0.75rem;
    color: var(--text-muted);
}

body.light-theme .auction-pass-owner-only {
    border-color: rgba(217, 119, 6, 0.35);
    background: rgba(251, 191, 36, 0.1);
}

body.light-theme .auction-pass-code {
    background: #fff;
    border-color: rgba(15, 23, 42, 0.12);
}

/* Select2 — live auction (assign panel + team setup); dropdownCssClass: select2-live-dropdown */
#live-auction-tabs-card .select2-live-scope .select2-container {
    display: block;
    width: 100% !important;
}

#live-auction-tabs-card .select2-live-scope .select2-container--default .select2-selection--single {
    height: auto;
    min-height: 3rem;
    padding: 0.5rem 2.5rem 0.5rem 0.85rem;
    border-radius: 10px;
    border: 2px solid var(--select-border-strong, rgba(251, 191, 36, 0.55));
    background-color: var(--select-surface, rgba(24, 24, 35, 0.95));
    box-shadow: var(--select-shadow, 0 6px 22px rgba(0, 0, 0, 0.35));
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

#live-auction-tabs-card .select2-live-scope .select2-container--default .select2-selection--single .select2-selection__rendered {
    padding-left: 0;
    line-height: 1.45;
    color: var(--text-main);
    font-weight: 600;
}

#live-auction-tabs-card .select2-live-scope .select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: var(--text-muted);
    font-weight: 600;
}

#live-auction-tabs-card .select2-live-scope .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: calc(3rem - 4px);
    right: 0.65rem;
}

#live-auction-tabs-card .select2-live-scope .select2-container--default.select2-container--focus .select2-selection--single,
#live-auction-tabs-card .select2-live-scope .select2-container--default.select2-container--open .select2-selection--single {
    border-color: var(--primary);
    box-shadow:
        0 0 0 4px rgba(251, 191, 36, 0.28),
        0 10px 28px rgba(0, 0, 0, 0.35);
}

body.light-theme #live-auction-tabs-card .select2-live-scope .select2-container--default.select2-container--focus .select2-selection--single,
body.light-theme #live-auction-tabs-card .select2-live-scope .select2-container--default.select2-container--open .select2-selection--single {
    box-shadow:
        0 0 0 4px rgba(251, 191, 36, 0.35),
        0 10px 30px rgba(15, 23, 42, 0.12);
}

#live-auction-tabs-card .select2-live-scope .select2-container--default .select2-selection--single .select2-selection__arrow b {
    border-color: var(--text-main) transparent transparent transparent !important;
    margin-top: -3px;
}

.select2-dropdown.select2-live-dropdown {
    border: 2px solid var(--select-border-strong, rgba(251, 191, 36, 0.55));
    border-radius: 10px;
    background: var(--select-surface, rgba(24, 24, 35, 0.95));
    box-shadow: var(--select-shadow, 0 10px 36px rgba(0, 0, 0, 0.45));
    overflow: hidden;
    z-index: 3000 !important;
}

#live-auction-tabs-card .select2-live-scope .select2-container--open {
    z-index: 2900 !important;
}

.select2-live-dropdown .select2-search--dropdown {
    padding: 0.5rem;
    background: color-mix(in srgb, var(--card-bg) 90%, transparent);
    border-bottom: 1px solid var(--border-color);
}

.select2-live-dropdown .select2-search--dropdown .select2-search__field {
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 0.5rem 0.65rem;
    background: var(--form-bg);
    color: var(--text-main);
    font-weight: 500;
}

.select2-live-dropdown .select2-results__option {
    padding: 0.55rem 0.85rem;
    font-weight: 600;
    color: var(--text-main);
}

.select2-live-dropdown .select2-results__option--highlighted.select2-results__option--selectable {
    background: rgba(251, 191, 36, 0.22) !important;
    color: var(--text-main) !important;
}

.select2-live-dropdown .select2-results__option--selected {
    background: rgba(251, 191, 36, 0.12) !important;
}
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" crossorigin="anonymous">

<div class="live-shell">
    <div class="glass-card live-header">
        <div>
            <h2 style="margin-bottom: 0.35rem;">Live Auction: {{ $auction->name }}</h2>
            <p style="margin: 0;">Manage teams, spin players, assign purchases, and track every budget in one place.</p>
            @if(!$isOwner)
                @php
                    $userTeam = \App\Models\Team::where('auction_id', $auction->id)
                        ->where(function ($q) {
                            $q->where('owner_id', auth()->id())
                                ->orWhereHas('teamOwners', function ($q2) {
                                    $q2->where('user_id', auth()->id());
                                });
                        })
                        ->first();
                @endphp
                @if($userTeam)
                    <div style="margin-top: 0.5rem; padding: 0.5rem 1rem; background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); border-radius: 8px; display: inline-block;">
                        <span style="color: #22c55e; font-weight: 600; font-size: 0.85rem;">You are assigned to: {{ $userTeam->name }}</span>
                    </div>
                @endif
            @endif
            @if($isOwner && filled($auction->auction_pass))
                <div class="auction-pass-owner-only">
                    <span class="auction-pass-label">Auction pass key — visible only to you</span>
                    <div class="auction-pass-row">
                        <span id="auction-pass-value" class="auction-pass-code">{{ $auction->auction_pass }}</span>
                        <button type="button" class="btn btn-accent" style="font-size: 0.82rem; padding: 0.4rem 0.85rem;" onclick="copyAuctionPassKey()" aria-label="Copy auction pass key">Copy</button>
                    </div>
                    <p class="auction-pass-hint">Others join this auction from Join Auction using this code (team managers use their team pass).</p>
                </div>
            @endif
            
                    </div>
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
            <span class="live-status">{{ ucfirst($auction->status) }}</span>
            <span class="live-status">Viewers: {{ $viewerCount }}</span>
            @if($isOwner && $auction->status === 'active')
                @if($canStartLive)
                    <form method="POST" action="{{ route('auctions.start', $auction) }}">
                        @csrf
                        <button type="submit" class="btn btn-primary">Start Auction</button>
                    </form>
                @else
                    <div style="max-width: 22rem; text-align: left;">
                        <div style="font-size: 0.78rem; font-weight: 800; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.35rem;">Start locked</div>
                        <p style="margin: 0 0 0.5rem 0; font-size: 0.88rem; line-height: 1.45; color: var(--text-main);">
                            Go live only after every team has an owner player in <strong>Team Setup</strong> and every team manager has joined with their <strong>team pass</strong>.
                        </p>
                        @if($liveStartProgress)
                            <div style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5;">
                                <div>Team setup: <strong style="color: var(--primary);">{{ $liveStartProgress['registered'] }}/{{ $liveStartProgress['required'] }}</strong> owner players assigned</div>
                                <div>Teams joined: <strong style="color: var(--primary);">{{ $liveStartProgress['joined'] }}/{{ $liveStartProgress['required'] }}</strong> claimed</div>
                            </div>
                        @endif
                    </div>
                @endif
            @endif
            @if($isOwner && $auction->status === 'live')
                <button type="button" id="end-auction-btn" class="btn" onclick="endAuction()" style="background: rgba(220, 38, 38, 0.14); color: #dc2626; border: 1px solid rgba(220, 38, 38, 0.35); display: none; padding: 0.4rem 0.85rem; font-size: 0.82rem; font-weight: 600;">
                    <i class="fas fa-stop-circle mr-2"></i>End Auction
                </button>
            @endif
            @if($isOwner && $auction->status === 'pending')
                <a href="{{ route('auctions.pool', $auction) }}" class="btn btn-accent">Manage Pool</a>
            @endif
        </div>
    </div>

    @if(!$canParticipate)
        <div class="alert alert-info" style="margin:1rem 0;">You are viewing the auction as a <b>spectator</b>. You cannot participate in bidding or team actions.</div>
    @endif
    <div class="glass-card" id="live-auction-tabs-card">
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
                    <form method="POST" action="{{ route('auctions.live.assign', $auction) }}" class="assignment-grid select2-live-scope" id="assign-player-form">
                        @csrf
                        <div>
                            <label class="form-label">Player</label>
                            <select name="auction_player_id" id="assign-player-select" class="js-select2-assign-player" style="width:100%;max-width:100%;" required>
                                <option value=""></option>
                                @php
                                    $assignablePlayers = $auctionPlayers->whereIn('status', ['pending', 'unsold']);
                                @endphp
                                @foreach($assignablePlayers as $item)
                                    <option value="{{ $item->id }}" data-base-price="{{ $item->base_price }}">
                                        {{ $item->player->name ?? 'Unknown Player' }} - Rs. {{ number_format($item->base_price) }}
                                        @if($item->status === 'unsold') 
                                            (Unsold) 
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Team Owner / Team</label>
                            <select name="team_id" id="assign-team-select" class="js-select2-assign-team" style="width:100%;max-width:100%;" required>
                                <option value=""></option>
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
                            <span class="status-pill status-sold">{{ $team['players_count'] + ($team['players_left'] ?? 0) }} Players</span>
                        </div>
                        <div class="budget-grid">
                            <div class="budget-pill"><span>Spent</span><strong>Rs. {{ number_format($team['spent']) }}</strong></div>
                            <div class="budget-pill"><span>Remaining</span><strong>Rs. {{ number_format($team['remaining']) }}</strong></div>
                            <div class="budget-pill"><span>Max Bid</span><strong>Rs. {{ number_format($team['max_bid']) }}</strong></div>
                            <div class="budget-pill {{ ($team['players_left'] ?? ($auction->max_players - $team['players_count'])) <= 2 ? 'warning' : '' }}">
                            <span>Players Left</span><strong>{{ $team['players_left'] ?? ($auction->max_players - $team['players_count']) }}</strong>
                        </div>
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
            <div class="player-board" id="player-board-main">
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
                        <div style="display: flex; gap: 0.65rem; justify-content: center; flex-wrap: wrap;">
                            <button type="button" class="btn btn-primary" onclick="spinPlayer()" {{ $auction->status !== 'live' ? 'disabled' : '' }}>Spin Random Player</button>
                            <button type="button" class="btn" onclick="markCurrentPlayerUnsold()" {{ !$currentPlayer || $auction->status !== 'live' ? 'disabled' : '' }} style="background: rgba(239, 68, 68, 0.14); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.35);">Unsold</button>
                        </div>
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
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <div class="player-avatar" style="width: 48px; height: 48px;">
                                        @if($item->player && $item->player->avatar)
                                            <img src="{{ $item->player->avatar }}" alt="{{ $item->player->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                        @else
                                            <span style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; font-weight: bold; font-size: 1.1rem; background: #e0e7ef; color: #667eea; border-radius: 50%;">
                                                {{ strtoupper(substr($item->player->name ?? $item->player->unique_username, 0, 2)) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 style="margin: 0;">{{ $item->player->name ?? 'Unknown Player' }}</h4>
                                        <div class="player-meta">
                                            {{ $item->player->specialization ?? 'All-rounder' }} |
                                            Base Rs. {{ number_format($item->base_price) }}
                                            @if($item->team)
                                                | {{ $item->team->name }} for Rs. {{ number_format($item->sold_price) }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <span class="status-pill status-{{ $item->status }}">{{ ucfirst($item->status) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <script>
            // Spin player functionality
            window.spinPlayer = function() {
                console.log('spinPlayer function called');
                var spinWheel = document.getElementById('spin-wheel');
                var spinButton = event.target || document.querySelector('button[onclick="spinPlayer()"]');
                
                console.log('spinWheel:', spinWheel);
                console.log('spinButton:', spinButton);
                
                if (!spinWheel) {
                    console.error('Spin wheel not found');
                    return;
                }
                
                // Disable button during spin
                if (spinButton) {
                    spinButton.disabled = true;
                    spinButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Spinning...';
                }
                
                // Add spinning animation
                spinWheel.classList.remove('is-spinning');
                void spinWheel.offsetWidth; // Force reflow
                spinWheel.classList.add('is-spinning');
                
                console.log('Starting fetch to: {{ route('auctions.spin', $auction) }}');
                
                fetch('{{ route('auctions.spin', $auction) }}', { 
                    method: 'POST', 
                    headers: { 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    } 
                })
                .then(async response => {
                    console.log('Fetch response:', response);
                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        const message = response.status === 404
                            ? (data.message || 'No more players to sell.')
                            : (data.message || 'Failed to spin player');
                        throw new Error(message);
                    }
                    return data;
                })
                .then(data => {
                    console.log('Received data:', data);
                    // Wait for spinning animation to complete
                    setTimeout(() => {
                        spinWheel.classList.remove('is-spinning');
                        
                        if (data && data.id) {
                            console.log('Player selected:', data);
                            // Update wheel display
                            document.getElementById('selected-player-name').innerText = data.name;
                            document.getElementById('selected-player-price').innerText = `Rs. ${Number(data.base_price).toLocaleString()}`;
                            setAuctionActionButtons({ hasPlayer: true, hasBid: false });
                            
                            // Update assign player select if available
                            const $ap = window.jQuery && window.jQuery('#assign-player-select');
                            if ($ap && $ap.length) {
                                $ap.val(String(data.auction_player_id)).trigger('change');
                            }
                            syncAssignPrice();
                        } else {
                            console.log('No player selected in response');
                            showAuctionNotice('No more players to sell.');
                            showNoPlayersToSellState();
                            return;
                        }
                        
                        // Restore button
                        if (spinButton) {
                            spinButton.disabled = false;
                            spinButton.innerHTML = 'Spin Random Player';
                        }
                    }, 1000); // Match spinning animation duration
                })
                .catch(error => {
                    console.error('Spin error:', error);
                    const message = error.message || 'Error spinning player. Please try again.';
                    const noPlayersLeft = message.toLowerCase().includes('no players')
                        || message.toLowerCase().includes('no more players');
                    if (noPlayersLeft) {
                        showNoPlayersToSellState();
                    }
                    showAuctionNotice(message);
                    spinWheel.classList.remove('is-spinning');
                    if (spinButton) {
                        spinButton.disabled = noPlayersLeft;
                        spinButton.innerHTML = noPlayersLeft ? 'No More Players' : 'Spin Random Player';
                    }
                });
            };

            function showAuctionNotice(message) {
                if (window.modalSystem && typeof window.modalSystem.info === 'function') {
                    window.modalSystem.info(message);
                } else {
                    alert(message);
                }
            }

            function showNoPlayersToSellState() {
                const selectedPlayerName = document.getElementById('selected-player-name');
                const selectedPlayerPrice = document.getElementById('selected-player-price');
                const spinButton = document.querySelector('button[onclick="spinPlayer()"]');

                if (selectedPlayerName) {
                    selectedPlayerName.innerText = 'No more players to sell';
                }
                if (selectedPlayerPrice) {
                    selectedPlayerPrice.innerText = '';
                }
                if (spinButton) {
                    spinButton.disabled = true;
                    spinButton.innerHTML = 'No More Players';
                }
                setAuctionActionButtons({ hasPlayer: false, hasBid: false });
            }

            function setAuctionActionButtons({ hasPlayer, hasBid }) {
                const sellButton = document.querySelector('button[onclick="sellCurrentPlayer()"]');
                const unsoldButton = document.querySelector('button[onclick="markCurrentPlayerUnsold()"]');

                if (sellButton) {
                    sellButton.disabled = !hasPlayer || !hasBid;
                }
                if (unsoldButton) {
                    unsoldButton.disabled = !hasPlayer;
                }
            }

            @if($isOwner)
            function syncCurrentAuctionState() {
                fetch(window.location.href + '?ajax=currentPlayer', {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.ok ? response.json() : null)
                .then(data => {
                    if (!data) return;

                    setAuctionActionButtons({
                        hasPlayer: Boolean(data.currentPlayer),
                        hasBid: Boolean(data.highestBid)
                    });
                })
                .catch(error => console.error('Current auction state sync failed:', error));
            }

            @if($auction->status === 'live')
            syncCurrentAuctionState();
            setInterval(syncCurrentAuctionState, 2500);
            @endif
            @endif

            function postAuctionAction(url, fallbackMessage) {
                return fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(async response => {
                    const data = await response.json().catch(() => ({}));
                    if (!response.ok || data.error) {
                        throw new Error(data.error || data.message || fallbackMessage);
                    }
                    return data;
                });
            }

            window.sellCurrentPlayer = function() {
                postAuctionAction('{{ route('auctions.sell', $auction) }}', 'Unable to sell player.')
                    .then(data => {
                        if (window.modalSystem && typeof window.modalSystem.info === 'function') {
                            window.modalSystem.info(data.message || 'Player sold.');
                        }
                        setAuctionActionButtons({ hasPlayer: false, hasBid: false });
                        setTimeout(() => window.location.reload(), 800);
                    })
                    .catch(error => {
                        if (window.modalSystem && typeof window.modalSystem.error === 'function') {
                            window.modalSystem.error(error.message);
                        } else {
                            alert(error.message);
                        }
                    });
            };

            window.markCurrentPlayerUnsold = function() {
                const unsoldButton = event.target || document.querySelector('button[onclick="markCurrentPlayerUnsold()"]');
                
                // Disable button and show loading
                if (unsoldButton) {
                    unsoldButton.disabled = true;
                    unsoldButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Marking Unsold...';
                }
                
                postAuctionAction('{{ route('auctions.unsold', $auction) }}', 'Unable to mark player unsold.')
                    .then(data => {
                        // Update UI instantly without page reload
                        updatePlayerStatusToUnsold();
                        updateUnsoldPlayersCount();
                        removePlayerFromAssignSelect();
                        
                        if (window.modalSystem && typeof window.modalSystem.info === 'function') {
                            window.modalSystem.info(data.message || 'Player marked unsold.');
                        }
                        setAuctionActionButtons({ hasPlayer: false, hasBid: false });
                        
                        // Clear wheel display
                        document.getElementById('selected-player-name').innerText = 'Spin to choose';
                        document.getElementById('selected-player-price').innerText = '';
                        
                        // Restore button
                        if (unsoldButton) {
                            unsoldButton.disabled = false;
                            unsoldButton.innerHTML = 'Unsold';
                        }
                        
                        // Show unsold notification briefly
                        showUnsoldNotification();
                    })
                    .catch(error => {
                        if (window.modalSystem && typeof window.modalSystem.error === 'function') {
                            window.modalSystem.error(error.message);
                        } else {
                            alert(error.message);
                        }
                        
                        // Restore button on error
                        if (unsoldButton) {
                            unsoldButton.disabled = false;
                            unsoldButton.innerHTML = 'Unsold';
                        }
                    });
            };

            // Unsold Players Management Functions
            window.toggleUnsoldList = function() {
                const unsoldList = document.getElementById('unsold-players-list');
                if (unsoldList) {
                    const isHidden = unsoldList.style.display === 'none';
                    unsoldList.style.display = isHidden ? 'block' : 'none';
                    
                    // Update button text
                    const toggleBtn = event.target || document.querySelector('button[onclick="toggleUnsoldList()"]');
                    if (toggleBtn) {
                        const icon = isHidden ? 'fa-eye-slash' : 'fa-eye';
                        toggleBtn.innerHTML = `<i class="fas ${icon} mr-2"></i>${isHidden ? 'Hide' : 'View'} Unsold List`;
                    }
                }
            };

            window.spinUnsoldPlayers = function() {
                console.log('spinUnsoldPlayers function called');
                
                // Check if there are pending players
                fetch(window.location.href + '?ajax=currentPlayer', {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Check if there are any pending players
                    return fetch('{{ route('auctions.check-pending', $auction) }}', {
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                })
                .then(response => response.json())
                .then(data => {
                    if (data.hasPending) {
                        if (window.modalSystem && typeof window.modalSystem.warning === 'function') {
                            window.modalSystem.warning('Please complete all pending players before spinning unsold players.');
                        } else {
                            alert('Please complete all pending players before spinning unsold players.');
                        }
                        return;
                    }
                    
                    if (!data.hasUnsold) {
                        if (window.modalSystem && typeof window.modalSystem.info === 'function') {
                            window.modalSystem.info('No unsold players available for spinning.');
                        } else {
                            alert('No unsold players available for spinning.');
                        }
                        return;
                    }
                    
                    // Show confirmation dialog
                    if (window.modalSystem && typeof window.modalSystem.confirm === 'function') {
                        window.modalSystem.confirm(
                            `There are ${data.unsoldCount} unsold players. Do you want to start spinning them?`,
                            () => performUnsoldSpin(),
                            null,
                            'Spin Unsold Players'
                        );
                    } else if (confirm(`There are ${data.unsoldCount} unsold players. Do you want to start spinning them?`)) {
                        performUnsoldSpin();
                    }
                })
                .catch(error => {
                    console.error('Error checking player status:', error);
                    if (window.modalSystem && typeof window.modalSystem.error === 'function') {
                        window.modalSystem.error('Error checking player status. Please try again.');
                    } else {
                        alert('Error checking player status. Please try again.');
                    }
                });
            };

            function performUnsoldSpin() {
                const spinButton = document.getElementById('spin-unsold-btn');
                const spinWheel = document.getElementById('spin-wheel');
                
                // Disable button and show loading
                if (spinButton) {
                    spinButton.disabled = true;
                    spinButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Spinning Unsold...';
                }
                
                // Add spinning animation to wheel
                if (spinWheel) {
                    spinWheel.classList.remove('is-spinning');
                    void spinWheel.offsetWidth; // Force reflow
                    spinWheel.classList.add('is-spinning');
                }
                
                // Call the spin unsold endpoint
                fetch('{{ route('auctions.spin.unsold', $auction) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(async response => {
                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        throw new Error(data.message || 'Failed to spin unsold player');
                    }
                    return data;
                })
                .then(data => {
                    console.log('Unsold player selected:', data);
                    
                    // Wait for spinning animation to complete
                    setTimeout(() => {
                        if (spinWheel) {
                            spinWheel.classList.remove('is-spinning');
                        }
                        
                        if (data && data.id) {
                            // Update wheel display
                            document.getElementById('selected-player-name').innerText = data.name;
                            document.getElementById('selected-player-price').innerText = `Rs. ${Number(data.base_price).toLocaleString()}`;
                            setAuctionActionButtons({ hasPlayer: true, hasBid: false });
                            
                            // Update assign player select if available
                            const $ap = window.jQuery && window.jQuery('#assign-player-select');
                            if ($ap && $ap.length) {
                                $ap.val(String(data.auction_player_id)).trigger('change');
                            }
                            syncAssignPrice();
                            
                            // Show success message
                            if (window.modalSystem && typeof window.modalSystem.success === 'function') {
                                window.modalSystem.success(`${data.name} (unsold) is now up for bidding!`);
                            }
                        } else {
                            showAuctionNotice('No more unsold players to spin.');
                            showNoPlayersToSellState();
                        }
                        
                        // Restore button
                        if (spinButton) {
                            spinButton.disabled = false;
                            spinButton.innerHTML = '<i class="fas fa-redo mr-2"></i>Spin Unsold Players';
                        }
                        
                        // Reload page to update unsold players list
                        setTimeout(() => window.location.reload(), 2000);
                    }, 1000); // Match spinning animation duration
                })
                .catch(error => {
                    console.error('Unsold spin error:', error);
                    const message = error.message || 'Error spinning unsold player. Please try again.';
                    
                    if (spinWheel) {
                        spinWheel.classList.remove('is-spinning');
                    }
                    
                    if (spinButton) {
                        spinButton.disabled = false;
                        spinButton.innerHTML = '<i class="fas fa-redo mr-2"></i>Spin Unsold Players';
                    }
                    
                    showAuctionNotice(message);
                });
            }

            // Helper Functions for Instant UI Updates
            function updatePlayerStatusToUnsold() {
                // Get current player data from cache or wheel display
                const currentPlayerName = document.getElementById('selected-player-name')?.innerText;
                const currentPlayerPrice = document.getElementById('selected-player-price')?.innerText;
                
                if (currentPlayerName && currentPlayerName !== 'Spin to choose') {
                    // Find the player in the complete player list and update status
                    const playerRows = document.querySelectorAll('.player-row');
                    playerRows.forEach(row => {
                        const playerNameElement = row.querySelector('h4');
                        if (playerNameElement && playerNameElement.innerText.trim() === currentPlayerName.trim()) {
                            const statusPill = row.querySelector('.status-pill');
                            if (statusPill) {
                                // Remove existing status classes
                                statusPill.className = 'status-pill status-unsold';
                                statusPill.innerText = 'Unsold';
                            }
                            
                            // Add visual indication that this player was just marked unsold
                            row.style.backgroundColor = 'rgba(239, 68, 68, 0.1)';
                            setTimeout(() => {
                                row.style.backgroundColor = '';
                            }, 2000);
                        }
                    });
                }
            }

            function updateUnsoldPlayersCount() {
                // Update unsold players count in the management section
                const unsoldCountElement = document.querySelector('[data-unsold-count]');
                if (unsoldCountElement) {
                    const currentCount = parseInt(unsoldCountElement.innerText) || 0;
                    unsoldCountElement.innerText = currentCount + 1;
                }
                
                // Update the unsold players list if it's visible
                refreshUnsoldPlayersList();
                
                // Update the main summary if visible
                updateMainSummary();
            }

            function removePlayerFromAssignSelect() {
                // Remove the player from the assign player select dropdown only if they were sold, not unsold
                const assignSelect = document.getElementById('assign-player-select');
                if (assignSelect) {
                    const currentPlayerName = document.getElementById('selected-player-name')?.innerText;
                    if (currentPlayerName && currentPlayerName !== 'Spin to choose') {
                        const options = assignSelect.querySelectorAll('option');
                        options.forEach(option => {
                            // Only remove if it's the exact player match and doesn't contain "(Unsold)"
                            if (option.innerText.includes(currentPlayerName.trim()) && !option.innerText.includes('(Unsold)')) {
                                option.remove();
                            }
                        });
                        
                        // Reset the select if it was showing this player
                        if (assignSelect.value && assignSelect.options[assignSelect.selectedIndex]?.innerText.includes(currentPlayerName.trim()) && !assignSelect.options[assignSelect.selectedIndex]?.innerText.includes('(Unsold)')) {
                            assignSelect.value = '';
                            syncAssignPrice();
                        }
                    }
                }
            }

            function refreshUnsoldPlayersList() {
                // Refresh the unsold players list via AJAX
                fetch(window.location.href + '?ajax=unsoldList', {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.unsoldPlayers && data.unsoldPlayers.length > 0) {
                        updateUnsoldListDisplay(data.unsoldPlayers);
                    }
                    // Also update the assign dropdown to reflect current unsold players
                    updateAssignDropdown();
                })
                .catch(error => {
                    console.log('Failed to refresh unsold list:', error);
                });
            }

            function updateAssignDropdown() {
                // Update the assign player dropdown to include current unsold players
                fetch(window.location.href + '?ajax=assignablePlayers', {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.assignablePlayers) {
                        const assignSelect = document.getElementById('assign-player-select');
                        if (assignSelect) {
                            // Clear existing options (except the empty one)
                            while (assignSelect.options.length > 1) {
                                assignSelect.remove(1);
                            }
                            
                            // Add updated assignable players
                            data.assignablePlayers.forEach(player => {
                                const option = document.createElement('option');
                                option.value = player.id;
                                option.setAttribute('data-base-price', player.base_price);
                                option.textContent = `${player.name} - Rs. ${Number(player.base_price).toLocaleString()}${player.status === 'unsold' ? ' (Unsold)' : ''}`;
                                assignSelect.appendChild(option);
                            });
                            
                            // Re-initialize Select2 if it exists
                            if (window.jQuery && window.jQuery.fn.select2) {
                                window.jQuery(assignSelect).trigger('change');
                            }
                        }
                    }
                })
                .catch(error => {
                    console.log('Failed to update assign dropdown:', error);
                });
            }

            function updateUnsoldListDisplay(unsoldPlayers) {
                const unsoldListContainer = document.getElementById('unsold-players-list');
                if (unsoldListContainer) {
                    const playerList = unsoldListContainer.querySelector('.player-list');
                    if (playerList) {
                        // Clear existing list
                        playerList.innerHTML = '';
                        
                        // Add updated unsold players
                        unsoldPlayers.forEach(player => {
                            const playerRow = createUnsoldPlayerRow(player);
                            playerList.appendChild(playerRow);
                        });
                        
                        // Update the heading count
                        const heading = unsoldListContainer.querySelector('h4');
                        if (heading) {
                            heading.innerText = `Unsold Players (${unsoldPlayers.length})`;
                        }
                    }
                }
            }

            function createUnsoldPlayerRow(player) {
                const div = document.createElement('div');
                div.className = 'player-row';
                div.style.opacity = '0.8';
                div.setAttribute('data-auction-player-id', player.id);
                
                div.innerHTML = `
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div class="player-avatar" style="width: 40px; height: 40px;">
                            ${player.avatar ? 
                                `<img src="${player.avatar}" alt="${player.name}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">` :
                                `<span style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; font-weight: bold; font-size: 0.9rem; background: #e0e7ef; color: #667eea; border-radius: 50%;">
                                    ${player.name ? player.name.substring(0, 2).toUpperCase() : 'PL'}
                                </span>`
                            }
                        </div>
                        <div>
                            <h4 style="margin: 0; font-size: 0.9rem;">${player.name || 'Unknown Player'}</h4>
                            <div class="player-meta" style="font-size: 0.75rem;">
                                ${player.specialization || 'All-rounder'} | Base Rs. ${Number(player.base_price).toLocaleString()}
                            </div>
                        </div>
                    </div>
                    <span class="status-pill status-unsold" style="font-size: 0.65rem;">Unsold</span>
                `;
                
                return div;
            }

            function updateMainSummary() {
                // Update the main summary counts via AJAX
                fetch(window.location.href + '?ajax=summary', {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.summary) {
                        // Update summary cards
                        const summaryCards = document.querySelectorAll('.summary-card strong');
                        if (summaryCards.length >= 4) {
                            summaryCards[1].innerText = data.summary.pending; // Pending count
                            summaryCards[2].innerText = data.summary.sold;   // Sold count
                            summaryCards[3].innerText = data.summary.unsold; // Unsold count
                        }
                    }
                })
                .catch(error => {
                    console.log('Failed to update summary:', error);
                });
            }

            function showUnsoldNotification() {
                // Show a brief notification that player was marked unsold
                const notification = document.createElement('div');
                notification.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: rgba(239, 68, 68, 0.9);
                    color: white;
                    padding: 1rem 1.5rem;
                    border-radius: 8px;
                    font-weight: 600;
                    z-index: 9999;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                    animation: slideInRight 0.3s ease-out;
                `;
                notification.innerHTML = '<i class="fas fa-times-circle mr-2"></i>Player marked as unsold';
                
                document.body.appendChild(notification);
                
                // Remove after 2 seconds
                setTimeout(() => {
                    notification.style.animation = 'slideOutRight 0.3s ease-in';
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 300);
                }, 2000);
            }

            // Add CSS animations for notifications
            const style = document.createElement('style');
            style.textContent = `
                @keyframes slideInRight {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
                
                @keyframes slideOutRight {
                    from {
                        transform: translateX(0);
                        opacity: 1;
                    }
                    to {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);

            // End Auction functionality
            window.endAuction = function() {
                console.log('endAuction function called');
                
                // Show confirmation dialog using modal system
                if (window.modalSystem && typeof window.modalSystem.show === 'function') {
                    window.modalSystem.show('info', 
                        'Are you sure you want to end this auction? This action cannot be undone.',
                        'End Auction',
                        {
                            actionCallback: () => performEndAuction(),
                            actionText: 'End Auction'
                        }
                    );
                } else {
                    // Fallback to browser confirm if modal system not available
                    if (confirm('Are you sure you want to end this auction? This action cannot be undone.')) {
                        performEndAuction();
                    }
                }
            };

            function performEndAuction() {
                const endButton = document.getElementById('end-auction-btn');
                
                // Disable button and show loading
                if (endButton) {
                    endButton.disabled = true;
                    endButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Ending...';
                }
                
                fetch('{{ route('auctions.end', $auction) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(async response => {
                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        throw new Error(data.message || 'Failed to end auction');
                    }
                    return data;
                })
                .then(data => {
                    console.log('Auction ended successfully:', data);
                    
                    if (window.modalSystem && typeof window.modalSystem.success === 'function') {
                        window.modalSystem.success(data.message || 'Auction ended successfully!');
                    }
                    
                    // Redirect to dashboard after a short delay
                    setTimeout(() => {
                        window.location.href = data.redirect_url || '{{ route('dashboard') }}';
                    }, 1500);
                })
                .catch(error => {
                    console.error('End auction error:', error);
                    const message = error.message || 'Error ending auction. Please try again.';
                    
                    // Show error using modal system
                    if (window.modalSystem && typeof window.modalSystem.error === 'function') {
                        window.modalSystem.error(message);
                    } else {
                        // Fallback to browser alert if modal system not available
                        alert(message);
                    }
                    
                    // Restore button
                    if (endButton) {
                        endButton.disabled = false;
                        endButton.innerHTML = '<i class="fas fa-stop-circle mr-2"></i>End Auction';
                    }
                });
            }

            // Check if auction can be ended and show/hide button accordingly
            function checkEndAuctionStatus() {
                fetch('{{ route('auctions.check-end-status', $auction) }}', {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const endButton = document.getElementById('end-auction-btn');
                    if (endButton) {
                        if (data.can_end) {
                            endButton.style.display = 'inline-block';
                            console.log('Auction can be ended - showing End Auction button');
                        } else {
                            endButton.style.display = 'none';
                            console.log('Auction cannot be ended yet - hiding End Auction button');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error checking end auction status:', error);
                });
            }

            // Check end auction status periodically and on page load
            @if($isOwner && $auction->status === 'live')
            document.addEventListener('DOMContentLoaded', function() {
                checkEndAuctionStatus();
                // Check every 10 seconds
                setInterval(checkEndAuctionStatus, 10000);
            });
            @endif

            </script>
        </div>
        @if($isOwner)
            <div id="team-setup" class="live-tab-panel" style="padding-top: 1.25rem;">
                <h3>Participating Teams</h3>
                <p>Set the team name and choose the team owner from the auction player list.</p>
                <form method="POST" action="{{ route('auctions.live.teams.save', $auction) }}" id="team-setup-form" class="select2-live-scope">
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
                                    <select name="teams[{{ $index }}][owner_player_id]" class="js-select2-team-owner" style="width:100%;max-width:100%;">
                                        <option value="">Choose owner</option>
                                        @foreach($allPlayers as $player)
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

<style>
</style>


<script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js" crossorigin="anonymous"></script>

<script>
const auctionId = {{ $auction->id }};

function initTeamOwnerSelect2() {
    if (typeof window.jQuery === 'undefined' || typeof window.jQuery.fn.select2 !== 'function') {
        return;
    }
    const $ = window.jQuery;
    const $dropdownParent = $('#live-auction-tabs-card');
    const $card = $dropdownParent.length ? $dropdownParent : $('body');

    $('#team-setup-form .js-select2-team-owner').each(function () {
        const $el = $(this);
        if ($el.data('select2')) {
            return;
        }
        $el.select2({
            width: '100%',
            placeholder: 'Choose owner',
            allowClear: true,
            dropdownParent: $card,
            dropdownCssClass: 'select2-live-dropdown'
        });
    });
}

/** Select2 must run after the Team Setup panel is display:block (hidden tabs → width 0). */
function scheduleTeamOwnerSelect2() {
    requestAnimationFrame(() => {
        initTeamOwnerSelect2();
        setTimeout(initTeamOwnerSelect2, 60);
        setTimeout(initTeamOwnerSelect2, 200);
    });
}

function initAssignPanelSelect2() {
    if (typeof window.jQuery === 'undefined' || typeof window.jQuery.fn.select2 !== 'function') {
        return;
    }
    const $ = window.jQuery;
    const $card = $('#live-auction-tabs-card').length ? $('#live-auction-tabs-card') : $('body');

    const $player = $('#assign-player-select');
    if ($player.length && !$player.data('select2')) {
        $player.select2({
            width: '100%',
            placeholder: 'Choose player',
            allowClear: true,
            dropdownParent: $card,
            dropdownCssClass: 'select2-live-dropdown'
        });
    }

    const $team = $('#assign-team-select');
    if ($team.length && !$team.data('select2')) {
        $team.select2({
            width: '100%',
            placeholder: 'Choose team / owner',
            allowClear: true,
            dropdownParent: $card,
            dropdownCssClass: 'select2-live-dropdown'
        });
    }

    bindAssignPanelSelectEvents();
    syncAssignPrice();
}

function scheduleAssignPanelSelect2() {
    requestAnimationFrame(() => {
        initAssignPanelSelect2();
        setTimeout(initAssignPanelSelect2, 60);
        setTimeout(initAssignPanelSelect2, 200);
    });
}

document.querySelectorAll('.live-tab-button').forEach(button => {
    button.addEventListener('click', () => {
        document.querySelectorAll('.live-tab-button').forEach(item => item.classList.remove('active'));
        document.querySelectorAll('.live-tab-panel').forEach(item => item.classList.remove('active'));
        button.classList.add('active');
        const tabId = button.getAttribute('data-tab');
        const panel = tabId ? document.getElementById(tabId) : null;
        if (panel) {
            panel.classList.add('active');
        }
        if (tabId === 'live-summary') {
            scheduleAssignPanelSelect2();
        }
        if (tabId === 'team-setup') {
            scheduleTeamOwnerSelect2();
        }
    });
});

window.jQuery(function () {
    if (document.getElementById('team-setup')?.classList.contains('active')) {
        scheduleTeamOwnerSelect2();
    }
    if (document.getElementById('live-summary')?.classList.contains('active')) {
        scheduleAssignPanelSelect2();
    }
});

function syncAssignPrice() {
    const priceInput = document.getElementById('assign-price');
    const sel = document.getElementById('assign-player-select');
    if (!priceInput || !sel) return;
    const opt = sel.selectedOptions[0];
    if (!opt || !opt.value) {
        priceInput.value = '';
        return;
    }
    priceInput.value = opt.getAttribute('data-base-price') || '';
}

function bindAssignPanelSelectEvents() {
    if (typeof window.jQuery === 'undefined') return;
    const $ = window.jQuery;
    const $p = $('#assign-player-select');
    if (!$p.length) return;
    $p.off('.assignPanel').on('select2:select.assignPanel select2:clear.assignPanel change.assignPanel', syncAssignPrice);
}


function copyAuctionPassKey() {
    const el = document.getElementById('auction-pass-value');
    if (!el) return;
    const text = el.innerText.trim();
    navigator.clipboard.writeText(text).then(() => {
        if (window.modalSystem && typeof window.modalSystem.success === 'function') {
            window.modalSystem.success('Pass key copied.');
        }
    }).catch(() => {
        prompt('Copy this pass key:', text);
    });
}
</script>
@endsection
