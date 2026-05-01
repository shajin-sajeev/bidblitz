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
                @include('partials.pool-item', ['item' => $item])
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

    // Handler to reset button when any modal is closed
    function resetCreateButtonOnModalClose() {
        createButton.disabled = false;
        createButton.innerHTML = originalText;
        document.removeEventListener('modalClosed', resetCreateButtonOnModalClose);
    }
    document.addEventListener('modalClosed', resetCreateButtonOnModalClose);

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
            document.removeEventListener('modalClosed', resetCreateButtonOnModalClose);
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
            proceedWithAuctionActivation(createButton, originalText);
            return;
        }

        if (validationType === 'maximum_met') {
            proceedWithAuctionActivation(createButton, originalText);
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
        document.removeEventListener('modalClosed', resetCreateButtonOnModalClose);
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
            window.location.href = data.redirect_url || `{{ route('auctions.live', $auction) }}`;
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

function updatePoolCount(count) {
    const poolTabButton = document.querySelector('.tab-button:last-child');
    const poolCountElement = document.getElementById('pool-count');

    if (poolTabButton) {
        poolTabButton.innerHTML = `<i class="fas fa-list mr-2"></i>Current Pool (${count})`;
    }

    if (poolCountElement) {
        poolCountElement.textContent = count;
    }
}

function buildAddToPoolForm(playerId, basePrice = '') {
    return `
        <form action="{{ route('auctions.pool.store', $auction) }}" method="POST" class="flex gap-2 pool-form" onsubmit="event.preventDefault(); handleFormSubmit(this); return false;">
            @csrf
            <input type="hidden" name="player_id" value="${playerId}">
            <input type="number" name="base_price" class="price-input" placeholder="Base Price" required min="1" value="${basePrice || ''}">
            <button type="submit" class="btn btn-accent px-6 py-3 text-white font-semibold rounded-xl shadow-lg transform transition-all duration-300 hover:scale-105 hover:shadow-xl relative overflow-hidden group" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                <span class="relative z-10">Add to Pool</span>
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent opacity-0 group-hover:opacity-20 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-all duration-700"></div>
            </button>
        </form>
    `;
}

function buildAddedControls(playerId, basePrice, removeUrl) {
    return `
        <div class="flex gap-2 items-center">
            <input type="number" value="${basePrice}" class="price-input" disabled style="opacity: 0.5;">
            <button type="button" class="btn px-6 py-3 text-white font-semibold rounded-xl shadow-lg relative overflow-hidden" disabled style="background: linear-gradient(135deg, #10b981, #059669); opacity: 0.8;">
                <span class="relative z-10">✓ Added</span>
            </button>
            <button type="button"
                    class="btn px-5 py-3 text-white font-semibold rounded-xl shadow-lg relative overflow-hidden"
                    style="background: linear-gradient(135deg, #ef4444, #dc2626);"
                    data-remove-url="${removeUrl}"
                    data-player-id="${playerId}"
                    data-base-price="${basePrice}"
                    onclick="removePoolPlayer(this)">
                Remove
            </button>
        </div>
    `;
}

function syncAvailablePlayerToAdded(playerId, basePrice, removeUrl) {
    const playerCard = document.querySelector(`#players-container .player-card[data-player-id="${playerId}"]`);
    const controls = playerCard ? playerCard.querySelector('.price-input-group') : null;

    if (!controls) return;

    controls.innerHTML = buildAddedControls(playerId, basePrice, removeUrl);
    playerCard.style.opacity = '0.8';
    playerCard.style.border = '1px solid rgba(16, 185, 129, 0.2)';
}

function syncAvailablePlayerToRemoved(playerId, basePrice = '') {
    const playerCard = document.querySelector(`#players-container .player-card[data-player-id="${playerId}"]`);
    const controls = playerCard ? playerCard.querySelector('.price-input-group') : null;

    if (!controls) return;

    controls.innerHTML = buildAddToPoolForm(playerId, basePrice);
    playerCard.style.opacity = '';
    playerCard.style.border = '';
}

function showPoolEmptyStateIfNeeded() {
    const poolContainer = document.getElementById('pool-container');
    if (!poolContainer || poolContainer.querySelector('.pool-player-card')) return;

    poolContainer.insertAdjacentHTML('afterbegin', `
        <div class="text-center py-8">
            <div class="text-gray-400 text-lg">No players added to the pool yet.</div>
            <div class="text-gray-500 text-sm mt-2">Start adding players from the available list</div>
        </div>
    `);
}

function removePoolPlayer(button) {
    const removeUrl = button.dataset.removeUrl;
    const playerId = button.dataset.playerId;
    const basePrice = button.dataset.basePrice || '';
    const originalText = button.innerHTML;

    button.disabled = true;
    button.innerHTML = 'Removing...';

    fetch(removeUrl, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(async response => {
        let data;
        try {
            data = await response.json();
        } catch (e) {
            data = {};
        }

        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Error removing player from pool');
        }

        document.querySelectorAll(`.pool-player-card[data-player-id="${playerId}"]`).forEach(card => card.remove());

        const currentCount = parseInt(document.getElementById('pool-count')?.textContent || '0', 10);
        updatePoolCount(Number.isInteger(data.pool_count) ? data.pool_count : Math.max(currentCount - 1, 0));
        syncAvailablePlayerToRemoved(playerId, basePrice);
        showPoolEmptyStateIfNeeded();

        setTimeout(() => {
            updateValidationStatus();
        }, 200);
    })
    .catch(error => {
        console.error('Remove error:', error);
        showNotification(error.message || 'Error removing player from pool', 'error');
        button.disabled = false;
        button.innerHTML = originalText;
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
    .then(async response => {
        let data;
        try {
            data = await response.json();
        } catch (e) {
            data = {};
        }
        if (response.ok && data.success) {
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
            const currentCount = parseInt(document.getElementById('pool-count')?.textContent || '0', 10);
            updatePoolCount(Number.isInteger(data.pool_count) ? data.pool_count : currentCount + 1);
            const priceInput = form.querySelector('input[name="base_price"]');
            syncAvailablePlayerToAdded(data.player_id, priceInput.value, data.remove_url);
            // Refresh pagination links to include tab parameter
            setTimeout(() => {
                addTabParameterToPaginationLinks();
            }, 100);
            // Update validation status after adding player
            setTimeout(() => {
                updateValidationStatus();
            }, 200);
        } else {
            // Show error from server if available (e.g., max limit)
            let errorMsg = data.message || 'Error adding player to pool';
            // If 422, try to parse error from response
            if (!response.ok && response.status === 422 && response.headers.get('content-type')?.includes('application/json')) {
                if (data.message) {
                    errorMsg = data.message;
                } else if (data.errors) {
                    errorMsg = Object.values(data.errors).flat().join(' ');
                }
            }
            showNotification(errorMsg, 'error');
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

</script>
@endsection
