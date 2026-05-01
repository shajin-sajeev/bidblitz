<div class="player-card pool-player-card" data-player-id="{{ $item->player_id }}" data-pool-player-id="{{ $item->id }}" style="padding: 1rem;">
    <div class="flex items-center gap-3">
        <div class="player-avatar" style="width: 40px; height: 40px; font-size: 0.9rem;">
            @if($item->player->avatar)
                <img src="{{ $item->player->avatar }}" alt="{{ $item->player->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
            @else
                {{ substr($item->player->name ?? $item->player->unique_username, 0, 2) }}
            @endif
        </div>

        <div class="flex-1">
            <div class="font-semibold">{{ $item->player->name }}</div>
            <div class="text-sm text-gray-400">{{ $item->player->unique_username ?: strtolower(str_replace(' ', '', $item->player->name)) }}</div>
            <div class="text-sm text-blue-400">{{ $item->player->specialization }}</div>
        </div>

        <div class="text-right">
            <div class="text-green-400 font-bold text-lg">Rs. {{ number_format($item->base_price) }}</div>
            <div class="text-xs px-2 py-1 rounded-full inline-block mt-1 {{ $item->status === 'pending' ? 'bg-yellow-500/20 text-yellow-400' : ($item->status === 'sold' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400') }}">
                {{ ucfirst($item->status) }}
            </div>
            @if(!$item->team_id && !in_array($item->status, ['sold'], true))
                <button type="button"
                        class="btn remove-pool-player-btn mt-2 px-3 py-2 text-white font-semibold rounded-md shadow"
                        style="background: linear-gradient(135deg, #ef4444, #dc2626); font-size: 0.8rem;"
                        data-remove-url="{{ route('auctions.pool.remove', [$item->auction_id, $item->id]) }}"
                        data-player-id="{{ $item->player_id }}"
                        data-base-price="{{ $item->base_price }}"
                        onclick="removePoolPlayer(this)">
                    Remove
                </button>
            @endif
        </div>
    </div>
</div>
