<div class="player-card">
    <div class="flex items-start gap-4">
        <div class="player-avatar">
            @if($player->profile_image)
                <img src="{{ asset('storage/' . $player->profile_image) }}" alt="{{ $player->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
            @else
                {{ substr($player->name ?? $player->username, 0, 2) }}
            @endif
        </div>
        
        <div class="player-info">
            <div class="player-name">{{ $player->name ?? 'Unknown Player' }}</div>
            @if($player->playerProfile)
                <div class="player-role mt-2">{{ $player->playerProfile->player_role }}</div>
            @else
                <div class="player-role mt-2">Player</div>
            @endif
        </div>
        
        <div class="price-input-group">
            <form action="{{ route('auctions.pool.store', $auction) }}" method="POST" class="flex gap-2">
                @csrf
                <input type="hidden" name="player_id" value="{{ $player->id }}">
                <input type="number" name="base_price" class="price-input" placeholder="Base Price" required min="0" step="1000">
                <button type="submit" class="btn btn-accent px-6 py-3 text-white font-semibold rounded-xl shadow-lg transform transition-all duration-300 hover:scale-105 hover:shadow-xl relative overflow-hidden group" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                    <span class="relative z-10">Add to Pool</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent opacity-0 group-hover:opacity-20 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-all duration-700"></div>
                </button>
            </form>
        </div>
    </div>
</div>
