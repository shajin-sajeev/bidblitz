<div class="player-card" style="padding: 1rem;">
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
            <div class="text-green-400 font-bold text-lg">₹{{ number_format($item->base_price) }}</div>
            <div class="text-xs px-2 py-1 rounded-full inline-block mt-1 {{ $item->status === 'pending' ? 'bg-yellow-500/20 text-yellow-400' : ($item->status === 'sold' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400') }}">
                {{ ucfirst($item->status) }}
            </div>
        </div>
    </div>
</div>
