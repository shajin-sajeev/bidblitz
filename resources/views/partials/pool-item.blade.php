@php
    $statusClass = match ($item->status) {
        'sold' => 'status-sold',
        'unsold' => 'status-unsold',
        default => 'status-pending',
    };
@endphp

<div class="pool-player-card" data-player-id="{{ $item->player_id }}" data-pool-player-id="{{ $item->id }}">
    <div class="pool-player-main">
        <div class="pool-player-avatar">
            @if($item->player->avatar)
                <img src="{{ $item->player->avatar }}" alt="{{ $item->player->name }}">
            @else
                {{ strtoupper(substr($item->player->name ?? $item->player->unique_username, 0, 2)) }}
            @endif
        </div>

        <div class="pool-player-details">
            <div class="pool-player-name-row">
                <h4>{{ $item->player->name }}</h4>
                <span class="pool-status-pill {{ $statusClass }}">{{ ucfirst($item->status) }}</span>
            </div>
            <div class="pool-player-username">{{ $item->player->unique_username ?: strtolower(str_replace(' ', '', $item->player->name)) }}</div>
            <div class="pool-player-meta">
                <span>{{ $item->player->specialization ?? 'All-rounder' }}</span>
                <span>{{ $item->player->experience_years ?? 0 }} yrs exp</span>
            </div>
        </div>
    </div>

    <div class="pool-player-actions">
        <div class="pool-price-panel">
            <span>Base Price</span>
            <strong>Rs. {{ number_format($item->base_price) }}</strong>
        </div>

        @if(!$item->team_id && !in_array($item->status, ['sold'], true))
            <button type="button"
                    class="remove-pool-player-btn"
                    data-remove-url="{{ route('auctions.pool.remove', [$item->auction_id, $item->id]) }}"
                    data-player-id="{{ $item->player_id }}"
                    data-base-price="{{ $item->base_price }}"
                    onclick="removePoolPlayer(this)">
                Remove
            </button>
        @endif
    </div>
</div>
