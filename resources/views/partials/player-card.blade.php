<div class="player-card" data-player-id="{{ $player->id }}">
    <div class="flex items-start gap-4">
        <div class="player-avatar">
            @if($player->avatar)
                <img src="{{ $player->avatar }}" alt="{{ $player->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
            @else
                {{ substr($player->name ?? $player->unique_username, 0, 2) }}
            @endif
        </div>
        
        <div class="player-info">
            <div class="player-name">{{ $player->name ?? 'Unknown Player' }}</div>
            <div class="player-username mt-1">{{ $player->unique_username ?: strtolower(str_replace(' ', '', $player->name)) }}</div>
            <div class="player-specialization mt-2">{{ $player->specialization ?? 'All-rounder' }}</div>
        </div>
        
        <div class="price-input-group">
            @php
                $isInPool = $player->auctionPlayers && $player->auctionPlayers->where('auction_id', $auction->id)->isNotEmpty();
                $poolPlayer = $isInPool ? $player->auctionPlayers->where('auction_id', $auction->id)->first() : null;
            @endphp
            
            @if($isInPool)
                <div class="flex gap-2 items-center">
                    <input type="number" value="{{ $poolPlayer->base_price }}" class="price-input" disabled style="opacity: 0.5;">
                    <button type="button" class="btn px-6 py-3 text-white font-semibold rounded-xl shadow-lg relative overflow-hidden" disabled style="background: linear-gradient(135deg, #10b981, #059669); opacity: 0.8;">
                        <span class="relative z-10">✓ Added</span>
                    </button>
                    <button type="button"
                            class="btn px-5 py-3 text-white font-semibold rounded-xl shadow-lg relative overflow-hidden"
                            style="background: linear-gradient(135deg, #ef4444, #dc2626);"
                            data-remove-url="{{ route('auctions.pool.remove', [$auction, $poolPlayer]) }}"
                            data-player-id="{{ $player->id }}"
                            data-base-price="{{ $poolPlayer->base_price }}"
                            onclick="removePoolPlayer(this)">
                        Remove
                    </button>
                </div>
            @else
                <form action="{{ route('auctions.pool.store', $auction) }}" method="POST" class="flex gap-2" onsubmit="event.preventDefault(); handleFormSubmit(this); return false;">
                    @csrf
                    <input type="hidden" name="player_id" value="{{ $player->id }}">
                    <input type="number" name="base_price" class="price-input" placeholder="Base Price" required min="1" step="1000">
                    <button type="submit" class="btn btn-accent px-6 py-3 text-white font-semibold rounded-xl shadow-lg transform transition-all duration-300 hover:scale-105 hover:shadow-xl relative overflow-hidden group" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                        <span class="relative z-10">Add to Pool</span>
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent opacity-0 group-hover:opacity-20 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-all duration-700"></div>
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
