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
            @if($isOwner && $auction->status === 'pending')
                <a href="{{ route('auctions.pool', $auction) }}" class="btn btn-accent">Manage Pool</a>
            @endif
        </div>
    </div>

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
                                @foreach($auctionPlayers->where('status', 'pending') as $item)
                                    <option value="{{ $item->id }}" data-base-price="{{ $item->base_price }}">{{ $item->player->name ?? 'Unknown Player' }} - Rs. {{ number_format($item->base_price) }}</option>
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
            // Show IPL-style auction player card
            function showSelectedPlayerModal(player) {
                console.log('showSelectedPlayerModal called with:', player);
                const modal = document.getElementById('selected-player-modal');
                
                console.log('Modal element:', modal);
                
                if (!modal) {
                    console.error('Modal not found!');
                    return;
                }
                
                // Store modal state in localStorage
                localStorage.setItem('auctionModalOpen', 'true');
                localStorage.setItem('currentPlayerId', player.id || player.auction_player_id);
                localStorage.setItem('currentPlayerStatus', player.status || 'pending');
                // Store complete player data for restoration
                localStorage.setItem('currentPlayerData', JSON.stringify(player));
                
                // Reset status sections
                document.getElementById('sold-status').style.display = 'none';
                document.getElementById('sold-price-section').style.display = 'none';
                
                // Show modal
                modal.style.display = 'block';
                console.log('Modal display set to block');
                
                // Avatar (larger for IPL style)
                var avatarDiv = document.getElementById('selected-player-avatar');
                if (player.avatar) {
                    avatarDiv.innerHTML = '<img src="' + player.avatar + '" alt="' + player.name + '" style="width:100%;height:100%;object-fit:cover;">';
                } else {
                    avatarDiv.innerHTML = '<span>' + (player.name ? player.name.substring(0,2).toUpperCase() : '?') + '</span>';
                }
                
                // Name and role (uppercase for IPL style)
                document.getElementById('selected-player-modal-name').textContent = (player.name || 'UNKNOWN PLAYER').toUpperCase();
                document.getElementById('selected-player-modal-role').textContent = (player.specialization || 'ALL-ROUNDER').toUpperCase();
                
                // Base Price with Indian Rupee symbol
                const price = player.base_price ? player.base_price.toLocaleString('en-IN') : '0';
                document.getElementById('selected-player-modal-price').textContent = '₹' + price;
                
                // Start polling for player status changes
                startPollingForPlayerStatus();
            }

            // Hide modal with animation
            function hideSelectedPlayerModal(clearLocalStorage = true) {
                const modal = document.getElementById('selected-player-modal');
                
                // Clear localStorage only when specified (e.g., when status changes)
                if (clearLocalStorage) {
                    localStorage.removeItem('auctionModalOpen');
                    localStorage.removeItem('currentPlayerId');
                    localStorage.removeItem('currentPlayerStatus');
                    localStorage.removeItem('currentPlayerData');
                }
                
                // Stop polling when modal is hidden
                if (pollInterval) {
                    clearInterval(pollInterval);
                    pollInterval = null;
                }
                
                if (modal) {
                    // Add hiding animation
                    modal.classList.add('modal-hiding');
                    
                    setTimeout(() => {
                        modal.style.display = 'none';
                        modal.classList.remove('modal-hiding');
                    }, 300);
                }
            }

            // Enhanced spinPlayer with better transition to modal
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
                .then(response => {
                    console.log('Fetch response:', response);
                    if (!response.ok) {
                        throw new Error('Failed to spin player');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Received data:', data);
                    // Wait for spinning animation to complete
                    setTimeout(() => {
                        spinWheel.classList.remove('is-spinning');
                        
                        if (data && data.id) {
                            console.log('Showing modal for player:', data);
                            // Update wheel display
                            document.getElementById('selected-player-name').innerText = data.name;
                            document.getElementById('selected-player-price').innerText = `Rs. ${Number(data.base_price).toLocaleString()}`;
                            
                            // Show the enhanced modal
                            showSelectedPlayerModal(data);
                            
                            // Update assign player select if available
                            const $ap = window.jQuery && window.jQuery('#assign-player-select');
                            if ($ap && $ap.length) {
                                $ap.val(String(data.auction_player_id)).trigger('change');
                            }
                            syncAssignPrice();
                        } else {
                            console.log('No player selected in response');
                            alert('No player selected.');
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
                    alert('Error spinning player. Please try again.');
                    spinWheel.classList.remove('is-spinning');
                    if (spinButton) {
                        spinButton.disabled = false;
                        spinButton.innerHTML = 'Spin Random Player';
                    }
                });
            };

            // Enhanced polling for player selection and status changes
            let pollInterval = null;
            let currentModalPlayer = null;
            
            function startPollingForPlayerStatus() {
                // Clear any existing interval
                if (pollInterval) {
                    clearInterval(pollInterval);
                }
                
                // Start polling every 1.5 seconds for real-time updates
                pollInterval = setInterval(function() {
                    console.log('Polling for current player...');
                    fetch(window.location.href + '?ajax=currentPlayer', {
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        console.log('Polling response status:', response.status);
                        if (!response.ok) {
                            throw new Error('Failed to check player status');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Polling response data:', data);
                        const modal = document.getElementById('selected-player-modal');
                        
                        // Log user team information for debugging
                        if (data.userTeam) {
                            console.log('User team info:', data.userTeam);
                        } else {
                            console.log('User is not assigned to any team');
                        }
                        
                        // Check if wheel was spun and database confirms player has pending status
                        if (data.currentPlayer) {
                            console.log('Database confirms wheel was spun, current player:', data.currentPlayer.name);
                            // Get stored status from localStorage
                            const storedStatus = localStorage.getItem('currentPlayerStatus');
                            const storedPlayerId = localStorage.getItem('currentPlayerId');
                            const modalOpen = localStorage.getItem('auctionModalOpen');
                            
                            // Show modal for ALL users when database confirms player has pending status
                            if (data.currentPlayer.status === 'pending' && modal.style.display !== 'block') {
                                console.log('Database confirms player has pending status, showing modal for ALL users');
                                showSelectedPlayerModal(data.currentPlayer);
                                
                                // Update localStorage for this user
                                localStorage.setItem('auctionModalOpen', 'true');
                                localStorage.setItem('currentPlayerId', data.currentPlayer.id);
                                localStorage.setItem('currentPlayerStatus', 'pending');
                                localStorage.setItem('currentPlayerData', JSON.stringify(data.currentPlayer));
                            }
                            
                            // Check if player status changed from pending to sold/unsold
                            if (storedStatus === 'pending' && data.currentPlayer.status !== 'pending') {
                                console.log('Player status changed from pending to:', data.currentPlayer.status);
                                // Player was sold or unsold - show notification and close modal
                                showStatusChangeNotification(data.currentPlayer);
                                // Stop polling - modal will be closed by the notification function
                                clearInterval(pollInterval);
                                pollInterval = null;
                            } else if (data.currentPlayer.status === 'pending') {
                                // Update stored status if still pending
                                localStorage.setItem('currentPlayerStatus', 'pending');
                            }
                        } else {
                            console.log('Database shows no current player with pending status - wheel not spun or player status changed');
                            // This means no player is currently selected with pending status in database
                            // Don't immediately close modal, check if we had a pending player recently
                            const storedStatus = localStorage.getItem('currentPlayerStatus');
                            const modalOpen = localStorage.getItem('auctionModalOpen');
                            
                            if (storedStatus === 'pending' && modalOpen === 'true') {
                                console.log('No current player data, but we had a pending player - waiting to see if status changed...');
                                // Wait a bit and check again before closing
                                setTimeout(() => {
                                    fetch(window.location.href + '?ajax=currentPlayer', {
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json'
                                        }
                                    })
                                    .then(response => response.json())
                                    .then(recheckData => {
                                        if (!recheckData.currentPlayer) {
                                            console.log('Confirmed no current player, closing modal');
                                            hideSelectedPlayerModal(true); // Clear localStorage since no current player
                                        } else if (recheckData.currentPlayer.status !== 'pending') {
                                            console.log('Player status changed to:', recheckData.currentPlayer.status);
                                            showStatusChangeNotification(recheckData.currentPlayer);
                                            // Stop polling - modal will be closed by the notification function
                                            clearInterval(pollInterval);
                                            pollInterval = null;
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error rechecking player status:', error);
                                    });
                                }, 2000); // Wait 2 seconds before rechecking
                            } else {
                                // No stored pending status, ensure modal is closed
                                if (modal.style.display === 'block') {
                                    hideSelectedPlayerModal(true); // Clear localStorage since no pending status
                                }
                                clearInterval(pollInterval);
                                pollInterval = null;
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error checking player status:', error);
                        // Don't stop polling on network errors, but log them
                    });
                }, 1500);
            }
            
            // Check and restore modal state on page load
            function checkAndRestoreModalState() {
                console.log('Checking modal state on page load...');
                
                // First check if we have localStorage indicating modal should be open
                const storedModalOpen = localStorage.getItem('auctionModalOpen');
                const storedPlayerId = localStorage.getItem('currentPlayerId');
                const storedStatus = localStorage.getItem('currentPlayerStatus');
                const storedPlayerData = localStorage.getItem('currentPlayerData');
                
                console.log('Stored state - modalOpen:', storedModalOpen, 'playerId:', storedPlayerId, 'status:', storedStatus);
                
                // Only show modal if we have stored state AND it was created after a spin (not just page load)
                // The key difference: we only show modal immediately if we previously had a spun player
                if (storedModalOpen === 'true' && storedStatus === 'pending' && storedPlayerId && storedPlayerData) {
                    console.log('Found stored pending state from previous spin, showing modal immediately');
                    let storedPlayer;
                    try {
                        // Use complete stored player data (only available after spin)
                        storedPlayer = JSON.parse(storedPlayerData);
                        // Additional validation: ensure this looks like real spun player data
                        if (storedPlayer && storedPlayer.name && storedPlayer.name !== 'Loading...') {
                            console.log('Valid stored player data found, showing modal');
                            showSelectedPlayerModal(storedPlayer);
                        } else {
                            console.log('Stored player data incomplete, waiting for server check');
                        }
                    } catch (e) {
                        console.log('Error parsing stored player data, waiting for server check');
                    }
                }
                
                // Always check if there's a current player with pending status, regardless of localStorage
                fetch(window.location.href + '?ajax=currentPlayer', {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    console.log('AJAX response status:', response.status);
                    if (!response.ok) {
                        throw new Error('AJAX response not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Immediate check on page load - data:', data);
                    
                    // Log user team information for debugging
                    if (data.userTeam) {
                        console.log('User is assigned to team:', data.userTeam.name);
                    } else {
                        console.log('User is not assigned to any team - should not see modal');
                    }
                    
                    // Show modal ONLY if there's a current player (wheel was spun) with pending status from database
                    if (data.currentPlayer && data.currentPlayer.status === 'pending') {
                        console.log('Database confirms player has pending status, showing modal on page load');
                        showSelectedPlayerModal(data.currentPlayer); // This will update the modal with real data
                        
                        // Update localStorage to reflect current state
                        localStorage.setItem('auctionModalOpen', 'true');
                        localStorage.setItem('currentPlayerId', data.currentPlayer.id);
                        localStorage.setItem('currentPlayerStatus', 'pending');
                        localStorage.setItem('currentPlayerData', JSON.stringify(data.currentPlayer));
                    } else {
                        console.log('Database shows no player with pending status, data:', data);
                        // If we had shown modal from stored state, hide it now since DB confirms no pending player
                        if (storedModalOpen === 'true' && storedStatus === 'pending') {
                            console.log('Database confirms no pending player, but we had stored state - hiding modal');
                            hideSelectedPlayerModal(true); // Clear localStorage since database confirms no pending player
                        } else {
                            console.log('No stored pending state, ensuring modal is closed');
                            // Ensure modal is closed and clear localStorage
                            hideSelectedPlayerModal(true); // Clear localStorage since no pending player
                        }
                    }
                })
                .catch(error => {
                    console.error('Error checking modal state on load:', error);
                    // If we had stored state, preserve the modal and retry
                    if (storedModalOpen === 'true' && storedStatus === 'pending') {
                        console.log('AJAX failed but had stored pending state - preserving modal and retrying');
                        // Don't hide the modal, just retry the check
                        setTimeout(() => {
                            checkAndRestoreModalState();
                        }, 3000);
                    } else {
                        console.log('AJAX failed and no stored pending state - ensuring modal closed');
                        hideSelectedPlayerModal(true);
                    }
                });
            }

            // Start polling on page load to check for existing selected player
            document.addEventListener('DOMContentLoaded', function() {
                checkAndRestoreModalState();
                startPollingForPlayerStatus();
            });
            
            // Update modal when player is sold
            function updateModalForSoldPlayer(player) {
                // Show sold status
                document.getElementById('sold-status').style.display = 'block';
                document.getElementById('sold-price-section').style.display = 'block';
                
                // Update sold price
                const soldPrice = player.sold_price ? player.sold_price.toLocaleString('en-IN') : '0';
                document.getElementById('selected-player-sold-price').textContent = '₹' + soldPrice;
                
                // Update team info
                const teamName = player.team_name || 'UNKNOWN TEAM';
                document.getElementById('team-name').textContent = teamName.toUpperCase();
                
                // Update team logo (use first letter of team name)
                const teamLogo = document.getElementById('team-logo');
                teamLogo.textContent = teamName.charAt(0).toUpperCase();
                
                // Add celebration effect
                const modal = document.getElementById('selected-player-modal');
                modal.style.borderColor = '#2ecc71';
                modal.style.boxShadow = '0 20px 60px rgba(46,204,113,0.5)';
                
                // Auto-hide after 3 seconds
                setTimeout(() => {
                    hideSelectedPlayerModal(true); // Clear localStorage since player was sold
                }, 3000);
            }
            
            // Show notification when player status changes
            function showStatusChangeNotification(player) {
                if (player.status === 'sold') {
                    // Update the modal instead of showing notification
                    updateModalForSoldPlayer(player);
                } else if (player.status === 'unsold') {
                    // Show unsold notification
                    const notification = document.createElement('div');
                    notification.style.cssText = `
                        position: fixed;
                        top: 20px;
                        right: 20px;
                        background: linear-gradient(135deg, #1a1a2e, #16213e);
                        border: 2px solid #e94560;
                        padding: 1rem 1.5rem;
                        border-radius: 8px;
                        box-shadow: 0 10px 30px rgba(233,69,96,0.3);
                        z-index: 10000;
                        animation: slideInRight 0.5s ease;
                        max-width: 350px;
                        color: white;
                    `;
                    
                    notification.innerHTML = `
                        <div style="display: flex; align-items: center; gap: 0.8rem;">
                            <div style="width: 8px; height: 8px; background: #e94560; border-radius: 50%; animation: pulse 1s ease-in-out infinite;"></div>
                            <div>
                                <div style="font-weight: 700; color: #ffffff; margin-bottom: 0.2rem;">${player.name.toUpperCase()}</div>
                                <div style="font-size: 0.85rem; color: #f39c12;">MARKED AS UNSOLD</div>
                            </div>
                        </div>
                    `;
                    
                    document.body.appendChild(notification);
                    
                    // Remove notification after 3 seconds
                    setTimeout(() => {
                        notification.style.animation = 'slideOutRight 0.5s ease forwards';
                        setTimeout(() => {
                            if (notification.parentNode) {
                                notification.parentNode.removeChild(notification);
                            }
                        }, 500);
                    }, 3000);
                    
                    // Hide modal after showing notification
                    setTimeout(() => {
                        hideSelectedPlayerModal(true); // Clear localStorage since player was unsold
                    }, 1000);
                }
            }
            
            // Add slide animations for notifications
            const styleSheet = document.createElement('style');
            styleSheet.textContent = `
                @keyframes slideInRight {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes slideOutRight {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(100%); opacity: 0; }
                }
            `;
            document.head.appendChild(styleSheet);
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

<style>
@keyframes slideUp {
    0% { 
        transform: translate(-50%, -40%) scale(0.9); 
        opacity: 0; 
        filter: blur(10px);
    }
    100% { 
        transform: translate(-50%, -50%) scale(1); 
        opacity: 1; 
        filter: blur(0px);
    }
}

@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.6; transform: scale(1.2); }
}

@keyframes slideOut {
    0% { opacity: 1; transform: translate(-50%, -50%) scale(1); }
    100% { opacity: 0; transform: translate(-50%, -60%) scale(0.8); }
}

@keyframes glow {
    0%, 100% { box-shadow: 0 10px 30px rgba(233,69,96,0.3); }
    50% { box-shadow: 0 15px 40px rgba(233,69,96,0.5); }
}

.modal-hiding {
    animation: slideOut 0.4s ease-out forwards;
}

/* Avatar glow effect */
#selected-player-avatar {
    animation: glow 2s ease-in-out infinite;
}

/* Card border glow */
#selected-player-modal {
    animation: slideUp 0.5s ease-out, glow 3s ease-in-out infinite;
}
</style>

<!-- IPL Style Auction Player Card - Available to ALL users -->
<div id="selected-player-modal" style="display:none; position:fixed; z-index:9999; top:50%; left:50%; transform:translate(-50%, -50%); background: linear-gradient(135deg, #1a1a2e, #16213e); border-radius: 12px; box-shadow: 0 20px 60px rgba(0,0,0,0.5); padding: 0; width: 380px; animation: slideUp 0.5s ease-out; border: 2px solid #e94560;">
                
                <!-- Auction Header -->
                <div style="background: linear-gradient(135deg, #e94560, #0f3460); padding: 15px; border-radius: 12px 12px 0 0; text-align:center; border-bottom: 3px solid #f39c12;">
                    <div style="font-size: 0.7rem; color: #ffffff; text-transform: uppercase; font-weight: 800; letter-spacing: 2px; margin-bottom: 5px;">{{ $auction->name ?? 'AUCTION' }}</div>
                    <div style="font-size: 0.9rem; color: #f39c12; text-transform: uppercase; font-weight: 700; letter-spacing: 1px;">LIVE AUCTION</div>
                </div>
                
                <!-- Player Photo Section -->
                <div style="padding: 20px; text-align:center; background: linear-gradient(to bottom, #0f3460, #1a1a2e);">
                    <div id="selected-player-avatar" style="width: 150px; height: 180px; margin: 0 auto 15px auto; border-radius: 8px; overflow: hidden; background: linear-gradient(135deg, #2d3561, #0f3460); display: flex; align-items: center; justify-content: center; font-size: 3rem; color: #e94560; font-weight: 900; border: 3px solid #f39c12; box-shadow: 0 10px 30px rgba(233,69,96,0.3);">
                        <!-- Player photo will be inserted here -->
                    </div>
                    
                    <!-- Player Name -->
                    <div id="selected-player-modal-name" style="font-size: 1.8rem; font-weight: 900; color: #ffffff; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 1px; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);"></div>
                    
                    <!-- Player Role -->
                    <div id="selected-player-modal-role" style="font-size: 1rem; color: #f39c12; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 20px;"></div>
                </div>
                
                <!-- Price Section -->
                <div style="padding: 0 20px;">
                    <!-- Base Price -->
                    <div style="background: rgba(243,156,18,0.1); border: 1px solid #f39c12; border-radius: 8px; padding: 12px; margin-bottom: 15px;">
                        <div style="font-size: 0.8rem; color: #f39c12; text-transform: uppercase; font-weight: 700; letter-spacing: 1px; margin-bottom: 5px;">Base Price</div>
                        <div id="selected-player-modal-price" style="font-size: 1.5rem; font-weight: 900; color: #ffffff;">₹0</div>
                    </div>
                    
                    <!-- Sold Price (Initially Hidden) -->
                    <div id="sold-price-section" style="background: rgba(46,204,113,0.1); border: 1px solid #2ecc71; border-radius: 8px; padding: 12px; margin-bottom: 15px; display: none;">
                        <div style="font-size: 0.8rem; color: #2ecc71; text-transform: uppercase; font-weight: 700; letter-spacing: 1px; margin-bottom: 5px;">Sold Price</div>
                        <div id="selected-player-sold-price" style="font-size: 1.5rem; font-weight: 900; color: #ffffff;">₹0</div>
                    </div>
                </div>
                
                <!-- Status Section -->
                <div style="padding: 0 20px 20px 20px;">
                    <!-- Sold Status (Initially Hidden) -->
                    <div id="sold-status" style="background: rgba(46,204,113,0.1); border: 1px solid #2ecc71; border-radius: 8px; padding: 15px; display: none;">
                        <div style="text-align:center; margin-bottom: 10px;">
                            <div style="font-size: 0.8rem; color: #2ecc71; text-transform: uppercase; font-weight: 700; letter-spacing: 1px;">SOLD TO</div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div id="team-logo" style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #2ecc71, #27ae60); display: flex; align-items: center; justify-content: center; color: white; font-weight: 900; font-size: 1.2rem;">
                                <!-- Team logo will be inserted here -->
                            </div>
                            <div id="team-name" style="flex: 1; font-size: 1.1rem; font-weight: 700; color: #ffffff; text-transform: uppercase; letter-spacing: 1px;">
                                <!-- Team name will be inserted here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
