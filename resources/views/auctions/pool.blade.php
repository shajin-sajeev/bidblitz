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
    pointer-events: none;
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
    pointer-events: none;
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
    z-index: 10;
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

/* Tab styles */
.tab-container {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid rgba(255,255,255,0.1);
}

.tab-button {
    padding: 0.75rem 1.5rem;
    background: transparent;
    border: none;
    color: rgba(255,255,255,0.7);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    border-radius: 8px 8px 0 0;
}

.tab-button:hover {
    color: rgba(255,255,255,0.9);
    background: rgba(255,255,255,0.05);
}

.tab-button.active {
    color: white;
    background: linear-gradient(135deg, #667eea, #764ba2);
}

.tab-button.active::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, #667eea, #764ba2);
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Modern Pagination Styles */
.pagination-wrapper {
    margin-top: 3rem;
    padding: 2rem 0;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
    border-radius: 20px;
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.75rem;
    margin: 0 auto;
    max-width: fit-content;
}

.pagination-items {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem;
    background: rgba(0, 0, 0, 0.2);
    border-radius: 100px;
    backdrop-filter: blur(10px);
}

.pagination .page-item {
    list-style: none;
    margin: 0;
}

.pagination .page-link {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 44px;
    height: 44px;
    padding: 0 16px;
    margin: 0;
    background: transparent;
    border: 2px solid transparent;
    border-radius: 50px;
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    font-weight: 500;
    font-size: 0.95rem;
    transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
    position: relative;
    overflow: hidden;
}

.pagination .page-link:hover {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-color: rgba(255, 255, 255, 0.2);
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-color: rgba(255, 255, 255, 0.3);
    color: white;
    transform: scale(1.1);
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
    font-weight: 600;
}

.pagination .page-item.disabled .page-link {
    background: transparent;
    border-color: transparent;
    color: rgba(255, 255, 255, 0.2);
    cursor: not-allowed;
    transform: none;
    opacity: 0.5;
}

.pagination .page-link::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 70%);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    transition: all 0.6s ease;
    z-index: 0;
}

.pagination .page-link:hover::before {
    width: 100%;
    height: 100%;
}

.pagination .page-link span {
    position: relative;
    z-index: 1;
}

/* Pagination arrows styling */
.pagination .page-link svg {
    width: 18px;
    height: 18px;
    transition: transform 0.3s ease;
}

.pagination .page-link:hover svg {
    transform: translateX(-2px);
}

.pagination .page-link:hover[rel="next"] svg {
    transform: translateX(2px);
}

/* Enhanced pagination info */
.pagination-info {
    text-align: center;
    margin-bottom: 1.5rem;
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.9rem;
    font-weight: 500;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
}

.pagination-info::before,
.pagination-info::after {
    content: '';
    flex: 1;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.3), transparent);
    max-width: 100px;
}

.pagination-info span {
    color: #667eea;
    font-weight: 600;
    font-size: 1rem;
    text-shadow: 0 0 20px rgba(102, 126, 234, 0.5);
}

/* Responsive design */
@media (max-width: 768px) {
    .pagination-items {
        padding: 0.25rem;
        gap: 0.25rem;
    }
    
    .pagination .page-link {
        min-width: 36px;
        height: 36px;
        padding: 0 12px;
        font-size: 0.85rem;
    }
    
    .pagination-info {
        font-size: 0.8rem;
    }
    
    .pagination-info::before,
    .pagination-info::after {
        max-width: 50px;
    }
}

/* Enhanced Modal Styles */
#notificationModal {
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}

#notificationModal.show {
    display: flex !important;
}

#notificationModal.show #modalContent {
    transform: scale(1) !important;
    opacity: 1 !important;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
}

#notificationModal.hide {
    display: none !important;
}

/* Modal Content Styling */
#modalContent {
    background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 15px 20px -5px rgba(0, 0, 0, 0.3);
    position: relative;
    overflow: hidden;
}

#modalContent::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.1), transparent, rgba(118, 75, 162, 0.1));
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% {
        left: -100%;
    }
    100% {
        left: 100%;
    }
}

/* Modal Header */
#modalIcon {
    background: linear-gradient(135deg, #667eea, #764ba2);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 6px 12px rgba(102, 126, 234, 0.4);
}

#modalTitle {
    background: linear-gradient(135deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    text-fill-color: transparent;
    margin-bottom: 0.5rem;
    position: relative;
}

#modalTitle::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
    border-radius: 1px;
}

/* Modal Message */
#modalMessage {
    color: #e2e8f0;
    line-height: 1.6;
    font-size: 1rem;
}

/* Modal Buttons */
#notificationModal button {
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2);
    position: relative;
    overflow: hidden;
}

#notificationModal button::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 70%);
    transition: all 0.6s ease;
    transform: translate(-50%, -50%);
    z-index: 0;
}

#notificationModal button:hover::before {
    width: 300%;
    height: 300%;
}

#notificationModal button:active {
    transform: scale(0.95);
}

#notificationModal button:first-of-type {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border: 1px solid rgba(102, 126, 234, 0.3);
}

#notificationModal button:last-of-type {
    background: rgba(75, 85, 99, 0.2);
    color: #e2e8f0;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

#notificationModal button:last-of-type:hover {
    background: rgba(75, 85, 99, 0.3);
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

/* Enhanced Modal Animation */
@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: scale(0.8) translateY(-30px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

@keyframes modalSlideOut {
    from {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
    to {
        opacity: 0;
        transform: scale(0.8) translateY(-30px);
    }
}

.modal-fade-in {
    animation: modalSlideIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.modal-fade-out {
    animation: modalSlideOut 0.3s cubic-bezier(0.4, 0, 1, 1);
}
</style>
<div class="glass-card mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">Manage Player Pool: {{ $auction->name }}</h2>
            <p class="text-gray-400 mt-1">Add players to the auction and set their base prices.</p>
        </div>
        <div class="flex gap-3">
            <button onclick="createAuction()" class="btn bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold px-6 py-3 rounded-xl shadow-lg transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
                <i class="fas fa-rocket mr-2"></i>Create Auction
            </button>
            <a href="{{ route('dashboard') }}" class="btn bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800">Back to Dashboard</a>
        </div>
    </div>
</div>

<div class="glass-card">
    <!-- Tab Navigation -->
    <div class="tab-container">
        <button class="tab-button {{ $activeTab === 'available' ? 'active' : '' }}" onclick="switchTabImmediate('available')">
            <i class="fas fa-users mr-2"></i>Available Players
        </button>
        <button class="tab-button {{ $activeTab === 'pool' ? 'active' : '' }}" onclick="switchTabImmediate('pool')">
            <i class="fas fa-list mr-2"></i>Current Pool ({{ $pool->total() }})
        </button>
    </div>

    <!-- Tab 1: Available Players -->
    <div id="available-tab" class="tab-content {{ $activeTab === 'available' ? 'active' : '' }}">
        <h3 class="mb-4">Available Players</h3>
        <div class="mb-4">
            <input type="text" id="search-input" class="form-control" placeholder="Search by name, username, or specialization" value="{{ $search }}">
        </div>

        <div id="players-container" style="max-height: 600px; overflow-y: auto; padding-right: 0.5rem;">
            @forelse($players as $player)
                <div class="player-card">
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
                            <div class="player-experience mt-1">{{ $player->experience_years ?? 0 }} years experience</div>
                            <div class="player-base-price mt-1">Base: ₹{{ number_format($player->base_price ?? 0, 2) }}</div>
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
                                </div>
                            @else
                                <form action="{{ route('auctions.pool.store', $auction) }}" method="POST" class="flex gap-2 pool-form">
                                    @csrf
                                    <input type="hidden" name="player_id" value="{{ $player->id }}">
                                    <input type="number" name="base_price" class="price-input" placeholder="Base Price" required min="1" >
                                    <button type="submit" class="btn btn-accent px-6 py-3 text-white font-semibold rounded-xl shadow-lg transform transition-all duration-300 hover:scale-105 hover:shadow-xl relative overflow-hidden group" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                                        <span class="relative z-10">Add to Pool</span>
                                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent opacity-0 group-hover:opacity-20 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-all duration-700"></div>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <div class="text-gray-400 text-lg">🔍 No available players found.</div>
                    <div class="text-gray-500 text-sm mt-2">Try adjusting your search criteria</div>
                </div>
            @endforelse
            
            <div class="pagination-wrapper">
                <div class="pagination-info">
                    Showing <span>{{ $players->firstItem() }}</span> to <span>{{ $players->lastItem() }}</span> of <span>{{ $players->total() }}</span> players
                </div>
                {{ $players->links('pagination::custom') }}
            </div>
        </div>
    </div>

    <!-- Tab 2: Current Pool -->
    <div id="pool-tab" class="tab-content {{ $activeTab === 'pool' ? 'active' : '' }}">
        <div class="pool-header">
            <h3 class="text-xl font-bold">Current Pool</h3>
            <div class="pool-stats">
                <div class="pool-stat">
                    <div class="pool-stat-value" id="pool-count">{{ $pool->total() }}</div>
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
        
        <div id="pool-container" style="max-height: 500px; overflow-y: auto; margin-top: 1rem;">
            @forelse($pool as $item)
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
                            <div class="text-sm text-gray-400">{{ $item->player->unique_username }}</div>
                            <div class="text-sm text-blue-400">{{ $item->player->specialization }}</div>
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
            
            @if($pool->hasPages())
                <div class="pagination-wrapper">
                    <div class="pagination-info">
                        Showing <span>{{ $pool->firstItem() }}</span> to <span>{{ $pool->lastItem() }}</span> of <span>{{ $pool->total() }}</span> players
                    </div>
                    {{ $pool->links('pagination::custom') }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// Create Auction function with enhanced dynamic validation
function createAuction() {
    // Disable the create button to prevent multiple clicks
    const createButton = event.target;
    const originalText = createButton.innerHTML;
    createButton.disabled = true;
    createButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Validating...';
    
    // Get current pool count via AJAX for real-time validation
    fetch(`{{ route('auctions.pool.validate', $auction) }}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Validation response:', data);
        
        const { minRequired, maxAllowed, currentPoolCount, canCreate, validationType } = data;
        
        // Restore button state for validation errors
        if (!canCreate) {
            createButton.disabled = false;
            createButton.innerHTML = originalText;
        }
        
        // Handle different validation scenarios
        if (validationType === 'minimum_not_met') {
            const needed = minRequired - currentPoolCount;
            showNotification(
                `⚠️ Minimum players not met! You need ${minRequired} players but only have ${currentPoolCount}. Add ${needed} more player${needed > 1 ? 's' : ''} to create the auction.`, 
                'error'
            );
            return;
        }
        
        if (validationType === 'minimum_met_but_not_maximum') {
            const neededToReachMax = maxAllowed - currentPoolCount;
            const excessOverMin = currentPoolCount - minRequired;
            showNotification(
                `❌ Auction creation requires exactly ${minRequired} or ${maxAllowed} players. You have ${currentPoolCount}. Add ${neededToReachMax} more player${neededToReachMax > 1 ? 's' : ''} to reach maximum, or remove ${excessOverMin} player${excessOverMin > 1 ? 's' : ''} to reach minimum.`, 
                'error'
            );
            return;
        }
        
        if (validationType === 'maximum_exceeded') {
            const excess = currentPoolCount - maxAllowed;
            showNotification(
                `❌ Maximum players exceeded! You can only have ${maxAllowed} players but have ${currentPoolCount}. Remove ${excess} player${excess > 1 ? 's' : ''} to create the auction.`, 
                'error'
            );
            return;
        }
        
        if (validationType === 'exact_minimum_met') {
            showNotification(
                `Perfect! You have exactly the minimum required players (${currentPoolCount}). Proceeding with auction creation...`, 
                'success',
                'Minimum Requirement Met',
                () => proceedWithAuctionActivation(createButton, originalText),
                'Create Auction'
            );
            return;
        }
        
        if (validationType === 'maximum_met') {
            showNotification(
                `Excellent! You have reached maximum capacity (${currentPoolCount} players). Proceeding with auction creation...`, 
                'success',
                'Maximum Capacity Reached',
                () => proceedWithAuctionActivation(createButton, originalText),
                'Create Auction'
            );
            return;
        }
        
        // Default case - proceed with confirmation modal
        const confirmMessage = `Are you ready to create the auction?<br><br>
            <strong>Pool:</strong> {{ $auction->name }}<br>
            <strong>Players:</strong> ${currentPoolCount}<br>
            <strong>Teams:</strong> {{ $auction->total_teams }}<br><br>
            Once created, the auction will be live for team managers to join.`;
        
        showNotification(
            confirmMessage,
            'info',
            'Create Auction Confirmation',
            () => proceedWithAuctionActivation(createButton, originalText),
            'Create Auction'
        );
    })
    .catch(error => {
        console.error('Validation error:', error);
        showNotification('Error validating auction pool. Please try again.', 'error');
        // Restore button state
        createButton.disabled = false;
        createButton.innerHTML = originalText;
    });
}

// Separate function to handle auction activation
function proceedWithAuctionActivation(createButton, originalText) {
    createButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';
    
    // Make AJAX request to activate the auction
    fetch(`{{ route('auctions.activate', $auction) }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            confirm: true
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification(
                'Auction activated successfully! You will be redirected to the live auction page...', 
                'success',
                'Auction Created Successfully',
                () => {
                    // Redirect to the live auction page
                    window.location.href = data.redirect_url || `{{ route('auctions.live', $auction) }}`;
                },
                'Go to Live Auction'
            );
        } else {
            showNotification(data.message || 'Error activating auction. Please try again.', 'error');
            // Restore button state
            createButton.disabled = false;
            createButton.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Activate auction error:', error);
        showNotification('Error activating auction. Please try again.', 'error');
        // Restore button state
        createButton.disabled = false;
        createButton.innerHTML = originalText;
    });
}

// Function to update validation status dynamically
function updateValidationStatus() {
    fetch(`{{ route('auctions.pool.validate', $auction) }}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        updateCreateButtonState(data);
    })
    .catch(error => {
        console.error('Update validation status error:', error);
    });
}

// Function to update create button state based on validation
function updateCreateButtonState(validationData) {
    const createButton = document.querySelector('[onclick="createAuction()"]');
    if (!createButton) return;
    
    const { canCreate, validationType, currentPoolCount, minRequired, maxAllowed } = validationData;
    
    // Update button appearance and tooltip based on validation
    if (canCreate) {
        createButton.disabled = false;
        createButton.classList.remove('opacity-50', 'cursor-not-allowed');
        
        if (validationType === 'exact_minimum_met') {
            createButton.innerHTML = '<i class="fas fa-rocket mr-2"></i>Create Auction (Minimum Met)';
            createButton.title = `Minimum requirement met (${currentPoolCount}/${minRequired})`;
        } else if (validationType === 'maximum_met') {
            createButton.innerHTML = '<i class="fas fa-rocket mr-2"></i>Create Auction (Full Capacity)';
            createButton.title = `Maximum capacity reached (${currentPoolCount}/${maxAllowed})`;
        } else {
            createButton.innerHTML = '<i class="fas fa-rocket mr-2"></i>Create Auction';
            createButton.title = `Ready to create auction (${currentPoolCount} players)`;
        }
    } else {
        createButton.disabled = true;
        createButton.classList.add('opacity-50', 'cursor-not-allowed');
        
        if (validationType === 'minimum_not_met') {
            createButton.innerHTML = '<i class="fas fa-lock mr-2"></i>Create Auction (Need More Players)';
            createButton.title = `Need ${minRequired - currentPoolCount} more players`;
        } else if (validationType === 'minimum_met_but_not_maximum') {
            createButton.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Create Auction (Invalid Count)';
            createButton.title = `Must have exactly ${minRequired} or ${maxAllowed} players`;
        } else if (validationType === 'maximum_exceeded') {
            createButton.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Create Auction (Too Many Players)';
            createButton.title = `Remove ${currentPoolCount - maxAllowed} players`;
        } else {
            createButton.innerHTML = '<i class="fas fa-lock mr-2"></i>Create Auction';
            createButton.title = 'Requirements not met';
        }
    }
}

let searchTimeout;
const searchInput = document.getElementById('search-input');
const playersContainer = document.getElementById('players-container');

// Tab switching function
function switchTab(tabName) {
    // Remove active class from all tabs and buttons
    document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    
    // Add active class to selected tab and button
    if (tabName === 'available') {
        document.querySelector('.tab-button:first-child').classList.add('active');
        document.getElementById('available-tab').classList.add('active');
    } else {
        document.querySelector('.tab-button:last-child').classList.add('active');
        document.getElementById('pool-tab').classList.add('active');
    }
    
    // Update URL parameter without page reload
    const url = new URL(window.location);
    url.searchParams.set('tab', tabName);
    window.history.replaceState({}, '', url);
}

// Function to check URL parameter and set active tab on page load
function setActiveTabFromUrl() {
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab') || 'available'; // Default to available tab
    
    console.log('Setting active tab from URL:', activeTab);
    
    // Set the active tab based on URL parameter
    switchTab(activeTab);
}

// Modify pagination links to include tab parameter
function addTabParameterToPaginationLinks() {
    const urlParams = new URLSearchParams(window.location.search);
    const currentTab = urlParams.get('tab') || 'available';
    
    console.log('Adding tab parameter to links, current tab:', currentTab);
    
    // Get all pagination links
    const paginationLinks = document.querySelectorAll('.pagination .page-link');
    
    paginationLinks.forEach(link => {
        if (link.href) {
            const url = new URL(link.href);
            url.searchParams.set('tab', currentTab);
            link.href = url.toString();
            console.log('Updated link:', link.href);
        }
    });
}

// Enhanced tab switching with immediate visual feedback
function switchTabImmediate(tabName) {
    console.log('Switching to tab:', tabName);
    
    // Remove active class from all tabs and buttons
    document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    
    // Add active class to selected tab and button
    if (tabName === 'available') {
        document.querySelector('.tab-button:first-child').classList.add('active');
        document.getElementById('available-tab').classList.add('active');
    } else {
        document.querySelector('.tab-button:last-child').classList.add('active');
        document.getElementById('pool-tab').classList.add('active');
    }
    
    // Update URL parameter without page reload
    const url = new URL(window.location);
    url.searchParams.set('tab', tabName);
    window.history.replaceState({}, '', url);
    
    // Re-apply tab parameter to pagination links
    setTimeout(() => {
        addTabParameterToPaginationLinks();
    }, 50);
}

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

// Handle form submission
function handleFormSubmit(form) {
    event.preventDefault();
    event.stopPropagation();
    
    const submitButton = form.querySelector('button[type="submit"]');
    const originalButtonText = submitButton.innerHTML;
    
    // Disable button and show loading state
    submitButton.disabled = true;
    submitButton.innerHTML = '<span class="relative z-10">Adding...</span>';
    
    const formData = new FormData(form);
    
    // Debug: Log form data
    console.log('Submitting form:', form.action);
    console.log('Form data:', Object.fromEntries(formData));
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Add the new item to the pool
            const poolContainer = document.getElementById('pool-container');
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = data.pool_item_html;
            
            // Remove empty state message if it exists
            const emptyState = poolContainer.querySelector('.text-center.py-8');
            if (emptyState) {
                emptyState.remove();
            }
            
            poolContainer.insertAdjacentHTML('afterbegin', tempDiv.innerHTML);
            
            // Update pool count in tab button
            const poolTabButton = document.querySelector('.tab-button:last-child');
            const currentCount = parseInt(poolTabButton.textContent.match(/\d+/)[0]) || 0;
            const newCount = currentCount + 1;
            poolTabButton.innerHTML = `<i class="fas fa-list mr-2"></i>Current Pool (${newCount})`;
            
            // Update pool count in stats
            const poolCountElement = document.getElementById('pool-count');
            if (poolCountElement) {
                const currentStatCount = parseInt(poolCountElement.textContent) || 0;
                poolCountElement.textContent = currentStatCount + 1;
            }
            
            // Show success message
            showNotification(data.message, 'success');
            
            // Mark the button as added and disable it
            const playerCard = form.closest('.player-card');
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="relative z-10">✓ Added</span>';
            submitButton.style.background = 'linear-gradient(135deg, #10b981, #059669)';
            
            // Disable the input field
            const priceInput = form.querySelector('input[name="base_price"]');
            priceInput.disabled = true;
            priceInput.style.opacity = '0.5';
            
            // Update the player card style to show it's added
            playerCard.style.opacity = '0.8';
            playerCard.style.border = '1px solid rgba(16, 185, 129, 0.2)';
            
            // Refresh pagination links to include tab parameter
            setTimeout(() => {
                addTabParameterToPaginationLinks();
            }, 100);
            
            // Update validation status after adding player
            setTimeout(() => {
                updateValidationStatus();
            }, 200);
            
        } else {
            showNotification(data.message || 'Error adding player to pool', 'error');
            // Restore button state
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
        }
    })
    .catch(error => {
        console.error('Submit error:', error);
        showNotification('Error adding player to pool', 'error');
        // Restore button state
        submitButton.disabled = false;
        submitButton.innerHTML = originalButtonText;
    });
    
    return false;
}

// Modal-based Notification System
let modalActionCallback = null;

function showNotificationModal(title, message, type = 'info', actionCallback = null, actionText = 'Action') {
    const modal = document.getElementById('notificationModal');
    const modalIcon = document.getElementById('modalIcon');
    const modalTitle = document.getElementById('modalTitle');
    const modalMessage = document.getElementById('modalMessage');
    const modalActionBtn = document.getElementById('modalActionBtn');
    const modalContent = document.getElementById('modalContent');
    
    // Set icon and colors based on type
    let icon, iconColor, titleColor;
    switch (type) {
        case 'success':
            icon = '✅';
            iconColor = 'text-green-500';
            titleColor = 'text-green-400';
            break;
        case 'error':
            icon = '❌';
            iconColor = 'text-red-500';
            titleColor = 'text-red-400';
            break;
        case 'warning':
            icon = '⚠️';
            iconColor = 'text-yellow-500';
            titleColor = 'text-yellow-400';
            break;
        case 'info':
        default:
            icon = 'ℹ️';
            iconColor = 'text-blue-500';
            titleColor = 'text-blue-400';
            break;
    }
    
    // Set modal content
    modalIcon.innerHTML = icon;
    modalIcon.className = `text-3xl mr-3 ${iconColor}`;
    modalTitle.innerHTML = title;
    modalTitle.className = `text-xl font-bold ${titleColor}`;
    modalMessage.innerHTML = message;
    
    // Handle action button
    if (actionCallback) {
        const modalActionText = document.getElementById('modalActionText');
        if (modalActionText) {
            modalActionText.innerHTML = actionText;
        }
        modalActionBtn.classList.remove('hidden');
        modalActionCallback = actionCallback;
    } else {
        modalActionBtn.classList.add('hidden');
        modalActionCallback = null;
    }
    
    // Show modal with animation
    modal.classList.remove('hide');
    modal.classList.add('show');
    modalContent.classList.add('modal-fade-in');
    
    // Remove animation class after animation completes
    setTimeout(() => {
        modalContent.classList.remove('modal-fade-in');
    }, 300);
}

function closeNotificationModal() {
    const modal = document.getElementById('notificationModal');
    const modalContent = document.getElementById('modalContent');
    
    // Add fade out animation
    modalContent.classList.add('modal-fade-out');
    
    setTimeout(() => {
        modal.classList.remove('show');
        modal.classList.add('hide');
        modalContent.classList.remove('modal-fade-out');
        modalActionCallback = null;
    }, 300);
}

function handleModalAction() {
    if (modalActionCallback) {
        modalActionCallback();
    }
    closeNotificationModal();
}

// Enhanced Notification function (now uses modal)
function showNotification(message, type = 'info', title = null, actionCallback = null, actionText = 'Action') {
    // Set default title based on type if not provided
    if (!title) {
        switch (type) {
            case 'success':
                title = 'Success';
                break;
            case 'error':
                title = 'Error';
                break;
            case 'warning':
                title = 'Warning';
                break;
            case 'info':
            default:
                title = 'Information';
                break;
        }
    }
    
    showNotificationModal(title, message, type, actionCallback, actionText);
}

// Add event listeners for pool forms
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Initializing...');
    
    const poolForms = document.querySelectorAll('.pool-form');
    poolForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            handleFormSubmit(this);
        });
    });
    
    // Set active tab based on URL parameter
    setActiveTabFromUrl();
    
    // Add tab parameter to pagination links with delay to ensure DOM is ready
    setTimeout(() => {
        addTabParameterToPaginationLinks();
    }, 100);
    
    // Update initial validation status
    setTimeout(() => {
        updateValidationStatus();
    }, 150);
    
    // Additional attempt to ensure tab state is correct
    setTimeout(() => {
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab') || 'available';
        console.log('Final tab check - setting to:', activeTab);
        switchTabImmediate(activeTab);
    }, 200);
});

// Also run on window load as backup
window.addEventListener('load', function() {
    console.log('Window Loaded - Backup initialization...');
    setTimeout(() => {
        setActiveTabFromUrl();
        addTabParameterToPaginationLinks();
    }, 100);
});

// Keyboard support for modal
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modal = document.getElementById('notificationModal');
        if (modal.classList.contains('show')) {
            closeNotificationModal();
        }
    }
});
</script>
<!-- Small Notification Modal -->
<div id="notificationModal" class="fixed inset-0 bg-black bg-opacity-60 hidden items-center justify-center z-50 backdrop-blur-sm">
    <div class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 rounded-lg p-0 max-w-xs w-full mx-4 transform transition-all duration-500 scale-95 opacity-0 shadow-lg border border-gray-700" id="modalContent">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-3 border-b border-gray-700">
            <div class="flex items-center">
                <div id="modalIcon" class="text-lg mr-2"></div>
                <h3 id="modalTitle" class="text-base font-bold text-white"></h3>
            </div>
            <button onclick="closeNotificationModal()" class="text-gray-400 hover:text-white transition-colors duration-200 p-1 rounded hover:bg-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 20 20">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6L6 6"/>
                </svg>
            </button>
        </div>
        
        <!-- Modal Body -->
        <div class="p-3">
            <p id="modalMessage" class="text-gray-300 text-center text-xs"></p>
        </div>
        
        <!-- Modal Footer -->
        <div class="flex justify-end gap-2 p-3 border-t border-gray-700">
            <button onclick="closeNotificationModal()" class="px-3 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-md transition-all duration-200 transform hover:scale-105 text-xs shadow">
                <span class="flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 20 20">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6L9 9l1 1 1 1 1"/>
                    </svg>
                    Cancel
                </span>
            </button>
            <button id="modalActionBtn" onclick="handleModalAction()" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-md transition-all duration-200 transform hover:scale-105 text-xs shadow min-w-[80px] hidden">
                <span class="flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 20 20">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 2-2 2"/>
                    </svg>
                    <span id="modalActionText">Action</span>
                </span>
            </button>
        </div>
    </div>
</div>
@endsection
