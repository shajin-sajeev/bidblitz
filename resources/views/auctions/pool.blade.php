@extends('layouts.app')

@section('content')
<style>
.player-card {
    background: linear-gradient(145deg, rgba(255,255,255,0.12), rgba(255,255,255,0.04));
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 20px;
    padding: 1.5rem;
    margin-bottom: 1.25rem;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    backdrop-filter: blur(10px);
}

.player-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
    border-radius: 20px 20px 0 0;
}

.player-card::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.player-card:hover {
    transform: translateY(-4px) scale(1.02);
    box-shadow: 0 20px 40px rgba(102, 126, 234, 0.2), 0 10px 20px rgba(0,0,0,0.1);
    border-color: rgba(102, 126, 234, 0.3);
}

.player-card:hover::after {
    opacity: 1;
}

.player-avatar {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid transparent;
    background: linear-gradient(white, white) padding-box,
                linear-gradient(135deg, #667eea, #764ba2) border-box;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 1.3rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
}

.player-avatar img {
    transition: transform 0.3s ease;
}

.player-card:hover .player-avatar img {
    transform: scale(1.1);
}

.player-info {
    flex: 1;
    margin-left: 1.25rem;
}

.player-name {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    background: linear-gradient(135deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.player-role {
    display: inline-block;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 0.4rem 1rem;
    border-radius: 25px;
    font-size: 0.85rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    position: relative;
    overflow: hidden;
}

.player-role::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s ease;
}

.player-card:hover .player-role::before {
    left: 100%;
}

.price-input-group {
    display: flex;
    align-items: center;
    gap: 1rem;
    position: relative;
}

.price-input {
    width: 140px;
    padding: 0.75rem 1rem;
    border: 2px solid rgba(102, 126, 234, 0.2);
    border-radius: 12px;
    background: rgba(255,255,255,0.08);
    color: var(--text-primary);
    font-weight: 600;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.price-input:focus {
    outline: none;
    border-color: #667eea;
    background: rgba(255,255,255,0.12);
    box-shadow: 0 0 20px rgba(102, 126, 234, 0.3);
    transform: translateY(-2px);
}

.price-input::placeholder {
    color: rgba(255,255,255,0.5);
}

.pool-header {
    background: linear-gradient(135deg, var(--primary), var(--accent));
    color: white;
    padding: 1rem;
    border-radius: 12px;
    margin-bottom: 1rem;
}

.pool-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-top: 0.5rem;
}

.pool-stat {
    text-align: center;
}

.pool-stat-value {
    font-size: 1.5rem;
    font-weight: bold;
}

.pool-stat-label {
    font-size: 0.8rem;
    opacity: 0.9;
}
</style>
<div class="glass-card mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">Manage Player Pool: {{ $auction->name }}</h2>
            <p class="text-gray-400 mt-1">Add players to the auction and set their base prices.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800">Back to Dashboard</a>
    </div>
</div>

<div class="grid grid-cols-2">
    <!-- Left Column: Search & Add Players -->
    <div class="glass-card">
        <h3>Available Players</h3>
        <div class="mb-4">
            <input type="text" id="search-input" class="form-control" placeholder="Search by name or username" value="{{ $search }}">
        </div>

        <div id="players-container" style="max-height: 600px; overflow-y: auto; padding-right: 0.5rem;">
            @forelse($players as $player)
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
            @empty
                <div class="text-center py-8">
                    <div class="text-gray-400 text-lg">🔍 No available players found.</div>
                    <div class="text-gray-500 text-sm mt-2">Try adjusting your search criteria</div>
                </div>
            @endforelse
            
            <div class="mt-4">
                {{ $players->links() }}
            </div>
        </div>
    </div>

    <!-- Right Column: Current Pool -->
    <div class="glass-card">
        <div class="pool-header">
            <h3 class="text-xl font-bold">Current Pool</h3>
            <div class="pool-stats">
                <div class="pool-stat">
                    <div class="pool-stat-value">{{ $pool->count() }}</div>
                    <div class="pool-stat-label">Players</div>
                </div>
                <div class="pool-stat">
                    <div class="pool-stat-value">{{ $auction->min_players * $auction->total_teams }}</div>
                    <div class="pool-stat-label">Min Required</div>
                </div>
                <div class="pool-stat">
                    <div class="pool-stat-value">{{ $auction->max_players * $auction->total_teams }}</div>
                    <div class="pool-stat-label">Max Allowed</div>
                </div>
            </div>
        </div>
        
        <div style="max-height: 500px; overflow-y: auto; margin-top: 1rem;">
            @forelse($pool as $item)
                <div class="player-card" style="padding: 1rem;">
                    <div class="flex items-center gap-3">
                        <div class="player-avatar" style="width: 40px; height: 40px; font-size: 0.9rem;">
                            @if($item->player->profile_image)
                                <img src="{{ asset('storage/' . $item->player->profile_image) }}" alt="{{ $item->player->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                            @else
                                {{ substr($item->player->name ?? $item->player->username, 0, 2) }}
                            @endif
                        </div>
                        
                        <div class="flex-1">
                            <div class="font-semibold">{{ $item->player->name ?? $item->player->username }}</div>
                            @if($item->player->playerProfile)
                                <div class="text-sm text-gray-400">{{ $item->player->playerProfile->player_role }}</div>
                            @else
                                <div class="text-sm text-gray-400">Player</div>
                            @endif
                        </div>
                        
                        <div class="text-right">
                            <div class="text-green-400 font-bold text-lg">₹{{ number_format($item->base_price) }}</div>
                            <div class="text-xs px-2 py-1 rounded-full inline-block mt-1 {{ $item->status === 'available' ? 'bg-green-500/20 text-green-400' : 'bg-yellow-500/20 text-yellow-400' }}">
                                {{ ucfirst($item->status) }}
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <div class="text-gray-400 text-lg">👥 No players added to the pool yet.</div>
                    <div class="text-gray-500 text-sm mt-2">Start adding players from the available list</div>
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
let searchTimeout;
const searchInput = document.getElementById('search-input');
const playersContainer = document.getElementById('players-container');

function performSearch(query) {
    fetch(`{{ route('auctions.pool.search', $auction) }}?search=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            playersContainer.innerHTML = data.html;
        })
        .catch(error => {
            console.error('Search error:', error);
        });
}

searchInput.addEventListener('keyup', function(e) {
    clearTimeout(searchTimeout);
    const query = e.target.value.trim();
    
    if (query.length === 0) {
        window.location.href = '{{ route('auctions.pool', $auction) }}';
        return;
    }
    
    searchTimeout = setTimeout(() => {
        performSearch(query);
    }, 500);
});


document.addEventListener('submit', function(e) {
    if (e.target.closest('.player-card')) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                form.closest('.player-card').remove();
                
                location.reload();
            }
        })
        .catch(error => {
            console.error('Submit error:', error);
        });
    }
});
</script>
@endsection
