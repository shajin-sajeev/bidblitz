@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-yellow-600 to-yellow-500 shadow-2xl">
        <div class="container mx-auto px-4 py-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-4xl font-bold text-black mb-2">⚡ Player Selection</h1>
                    <p class="text-black/80 text-lg">Choose your dream team for the auction</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="showAddPlayersModal()" class="bg-black hover:bg-gray-800 text-yellow-400 font-bold py-3 px-6 rounded-lg transition-all transform hover:scale-105 shadow-lg">
                        <i class="fas fa-users mr-2"></i>Add Selected Players
                    </button>
                    <a href="{{ route('auctions.show', $auction->id) }}" class="bg-white/20 hover:bg-white/30 text-black font-bold py-3 px-6 rounded-lg transition-all backdrop-blur-sm">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Auction
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Auction Info Card -->
    <div class="container mx-auto px-4 -mt-6">
        <div class="bg-gray-800/90 backdrop-blur-sm rounded-2xl shadow-2xl p-6 border border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-yellow-400 text-3xl font-bold">{{ $auction->name }}</div>
                    <div class="text-gray-400 text-sm mt-1">Auction Name</div>
                </div>
                <div class="text-center">
                    <div class="text-white text-2xl font-semibold">{{ $auction->start_date->format('M d, Y') }}</div>
                    <div class="text-gray-400 text-sm mt-1">Start Date</div>
                </div>
                <div class="text-center">
                    <div class="text-white text-2xl font-semibold">{{ $auction->end_date->format('M d, Y') }}</div>
                    <div class="text-gray-400 text-sm mt-1">End Date</div>
                </div>
                <div class="text-center">
                    <div class="text-yellow-400 text-2xl font-bold">{{ $auction->players()->count() }}/20</div>
                    <div class="text-gray-400 text-sm mt-1">Players Selected</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="container mx-auto px-4 mt-8">
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-700">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1 relative">
                    <input 
                        type="text" 
                        id="playerSearch" 
                        placeholder="🔍 Search players by name, username, email, or specialization..." 
                        class="w-full px-6 py-4 bg-gray-900/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:border-yellow-500 focus:ring-2 focus:ring-yellow-500/20 transition-all text-lg"
                    >
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                        <i class="fas fa-search text-yellow-400"></i>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button onclick="filterBySpecialization('all')" class="filter-btn px-4 py-2 bg-gray-700 hover:bg-yellow-500 hover:text-black text-white rounded-lg transition-all">
                        All
                    </button>
                    <button onclick="filterBySpecialization('Batsman')" class="filter-btn px-4 py-2 bg-gray-700 hover:bg-yellow-500 hover:text-black text-white rounded-lg transition-all">
                        🏏 Batsman
                    </button>
                    <button onclick="filterBySpecialization('Bowler')" class="filter-btn px-4 py-2 bg-gray-700 hover:bg-yellow-500 hover:text-black text-white rounded-lg transition-all">
                        🎯 Bowler
                    </button>
                    <button onclick="filterBySpecialization('All-rounder')" class="filter-btn px-4 py-2 bg-gray-700 hover:bg-yellow-500 hover:text-black text-white rounded-lg transition-all">
                        ⚡ All-rounder
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Selected Players Counter -->
    <div class="container mx-auto px-4 mt-6">
        <div class="bg-gradient-to-r from-yellow-600 to-yellow-500 rounded-xl p-4 shadow-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="bg-black/20 rounded-lg px-4 py-2">
                        <span class="text-black font-bold text-lg" id="selectedCount">0</span>
                        <span class="text-black/80 text-sm">players selected</span>
                    </div>
                    <div class="text-black/80 text-sm">Maximum 20 players allowed</div>
                </div>
                <button onclick="clearSelection()" class="bg-black/20 hover:bg-black/30 text-black px-4 py-2 rounded-lg transition-all">
                    <i class="fas fa-times mr-2"></i>Clear Selection
                </button>
            </div>
        </div>
    </div>

    <!-- Players Grid -->
    <div class="container mx-auto px-4 mt-8 pb-12">
        <div id="playersList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <!-- Players will be loaded here via JavaScript -->
        </div>
    </div>

    <!-- Pagination -->
    <div id="pagination" class="container mx-auto px-4 pb-12">
        <div class="flex justify-center">
            <!-- Pagination will be loaded here via JavaScript -->
        </div>
    </div>
</div>

<!-- Add Players Confirmation Modal -->
<div id="addPlayersModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-2xl max-w-md w-full shadow-2xl border border-gray-700">
            <div class="p-6">
                <div class="text-center">
                    <div class="w-20 h-20 bg-yellow-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-black text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-yellow-400 mb-2">Confirm Player Selection</h3>
                    <p class="text-gray-300 mb-6">Are you sure you want to add <span id="modalPlayerCount" class="text-yellow-400 font-bold">0</span> players to this auction?</p>
                    
                    <div class="flex gap-3">
                        <button onclick="hideAddPlayersModal()" class="flex-1 bg-gray-700 hover:bg-gray-600 text-white py-3 rounded-lg transition-all">
                            Cancel
                        </button>
                        <button onclick="confirmAddPlayers()" class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-3 rounded-lg transition-all">
                            Add Players
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
let currentPage = 1;
let searchQuery = '';
let selectedPlayers = new Set();
let currentFilter = 'all';
const auctionId = {{ $auction->id }};

// Load players on page load
document.addEventListener('DOMContentLoaded', function() {
    loadPlayers();
    loadAuctionPlayers();
});

// Search functionality
document.getElementById('playerSearch').addEventListener('input', function(e) {
    searchQuery = e.target.value;
    currentPage = 1;
    loadPlayers();
});

function filterBySpecialization(specialization) {
    currentFilter = specialization;
    currentPage = 1;
    
    // Update button styles
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('bg-yellow-500', 'text-black');
        btn.classList.add('bg-gray-700', 'text-white');
    });
    
    event.target.classList.remove('bg-gray-700', 'text-white');
    event.target.classList.add('bg-yellow-500', 'text-black');
    
    loadPlayers();
}

function loadPlayers() {
    let url = `/players/search?q=${encodeURIComponent(searchQuery)}&page=${currentPage}`;
    if (currentFilter !== 'all') {
        url += `&specialization=${currentFilter}`;
    }
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderPlayers(data.players.data);
                renderPagination(data.players);
            }
        })
        .catch(error => {
            console.error('Error loading players:', error);
            window.modalSystem.error('Error loading players');
        });
}

function loadAuctionPlayers() {
    fetch(`/auctions/${auctionId}/players`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                selectedPlayers = new Set(data.players.map(p => p.id));
                updateSelectedCount();
            }
        })
        .catch(error => {
            console.error('Error loading auction players:', error);
        });
}

function renderPlayers(players) {
    const container = document.getElementById('playersList');
    
    if (players.length === 0) {
        container.innerHTML = `
            <div class="col-span-full text-center py-16">
                <div class="w-24 h-24 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-search text-gray-500 text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-400 mb-2">No Players Found</h3>
                <p class="text-gray-500">Try adjusting your search or filters</p>
            </div>
        `;
        return;
    }

    container.innerHTML = players.map(player => `
        <div class="player-card ${selectedPlayers.has(player.id) ? 'selected' : ''}" data-player-id="${player.id}">
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl overflow-hidden border border-gray-700 hover:border-yellow-500/50 transition-all duration-300 hover:transform hover:scale-105 hover:shadow-2xl">
                <!-- Player Header -->
                <div class="relative">
                    <div class="h-32 bg-gradient-to-br from-yellow-600 to-yellow-500 relative">
                        <div class="absolute -bottom-12 left-4">
                            <div class="w-24 h-24 bg-gray-900 rounded-full border-4 border-yellow-500 overflow-hidden shadow-xl">
                                ${player.avatar ? 
                                    `<img src="/avatars/${player.avatar}" alt="${player.name}" class="w-full h-full object-cover">` : 
                                    `<div class="w-full h-full bg-gradient-to-br from-yellow-600 to-yellow-500 flex items-center justify-center">
                                        <span class="text-black font-bold text-2xl">${player.name.charAt(0).toUpperCase()}</span>
                                    </div>`
                                }
                            </div>
                        </div>
                        <div class="absolute top-3 right-3">
                            <div class="bg-black/20 backdrop-blur-sm rounded-full p-2">
                                <input type="checkbox" 
                                       id="player-${player.id}" 
                                       value="${player.id}" 
                                       ${selectedPlayers.has(player.id) ? 'checked' : ''}
                                       onchange="togglePlayerSelection(${player.id})"
                                       class="w-5 h-5 text-yellow-500 bg-gray-700 border-gray-600 rounded focus:ring-yellow-500 focus:ring-2 cursor-pointer">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Player Info -->
                <div class="pt-14 px-4 pb-4">
                    <div class="text-center mb-4">
                        <h3 class="text-xl font-bold text-yellow-400 mb-1">${player.name}</h3>
                        <p class="text-gray-400 text-sm">@${player.unique_username}</p>
                    </div>
                    
                    <!-- Player Stats -->
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 text-sm">📧 Email</span>
                            <span class="text-white text-sm">${player.email}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 text-sm">📱 Phone</span>
                            <span class="text-white text-sm">${player.phone}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 text-sm">🎯 Role</span>
                            <span class="bg-yellow-500/20 text-yellow-400 px-2 py-1 rounded-full text-xs font-semibold">${player.specialization}</span>
                        </div>
                    </div>
                    
                    <!-- Action Button -->
                    <div class="mt-4">
                        <label for="player-${player.id}" class="block cursor-pointer">
                            <div class="w-full py-3 rounded-lg text-center font-semibold transition-all ${
                                selectedPlayers.has(player.id) 
                                    ? 'bg-green-600 hover:bg-green-700 text-white' 
                                    : 'bg-gray-700 hover:bg-yellow-500 hover:text-black text-gray-300'
                            }">
                                ${selectedPlayers.has(player.id) ? '✓ Selected' : 'Select Player'}
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

function renderPagination(pagination) {
    const container = document.getElementById('pagination');
    
    if (pagination.last_page <= 1) {
        container.innerHTML = '';
        return;
    }

    let html = '<div class="flex space-x-2">';
    
    // Previous button
    if (pagination.current_page > 1) {
        html += `<button onclick="changePage(${pagination.current_page - 1})" class="px-4 py-2 bg-gray-700 hover:bg-yellow-500 hover:text-black text-white rounded-lg transition-all">
            <i class="fas fa-chevron-left"></i>
        </button>`;
    }
    
    // Page numbers
    for (let i = 1; i <= pagination.last_page; i++) {
        if (i === pagination.current_page) {
            html += `<button class="px-4 py-2 bg-yellow-500 text-black font-bold rounded-lg">${i}</button>`;
        } else if (Math.abs(i - pagination.current_page) <= 2 || i === 1 || i === pagination.last_page) {
            html += `<button onclick="changePage(${i})" class="px-4 py-2 bg-gray-700 hover:bg-yellow-500 hover:text-black text-white rounded-lg transition-all">${i}</button>`;
        } else if (Math.abs(i - pagination.current_page) === 3) {
            html += '<span class="px-4 py-2 text-gray-400">...</span>';
        }
    }
    
    // Next button
    if (pagination.current_page < pagination.last_page) {
        html += `<button onclick="changePage(${pagination.current_page + 1})" class="px-4 py-2 bg-gray-700 hover:bg-yellow-500 hover:text-black text-white rounded-lg transition-all">
            <i class="fas fa-chevron-right"></i>
        </button>`;
    }
    
    html += '</div>';
    container.innerHTML = html;
}

function changePage(page) {
    currentPage = page;
    loadPlayers();
}

function togglePlayerSelection(playerId) {
    if (selectedPlayers.has(playerId)) {
        selectedPlayers.delete(playerId);
    } else {
        if (selectedPlayers.size >= 20) {
            window.modalSystem.error('Maximum 20 players can be selected');
            document.getElementById(`player-${playerId}`).checked = false;
            return;
        }
        selectedPlayers.add(playerId);
    }
    
    // Update visual state
    const card = document.querySelector(`[data-player-id="${playerId}"]`);
    if (card) {
        if (selectedPlayers.has(playerId)) {
            card.classList.add('selected');
        } else {
            card.classList.remove('selected');
        }
    }
    
    updateSelectedCount();
}

function updateSelectedCount() {
    document.getElementById('selectedCount').textContent = selectedPlayers.size;
}

function clearSelection() {
    selectedPlayers.clear();
    document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
    document.querySelectorAll('.player-card').forEach(card => card.classList.remove('selected'));
    updateSelectedCount();
}

function showAddPlayersModal() {
    if (selectedPlayers.size === 0) {
        window.modalSystem.error('Please select at least one player to add to the auction');
        return;
    }
    
    if (selectedPlayers.size > 20) {
        window.modalSystem.error('You can only add up to 20 players to an auction');
        return;
    }
    
    document.getElementById('modalPlayerCount').textContent = selectedPlayers.size;
    document.getElementById('addPlayersModal').classList.remove('hidden');
}

function hideAddPlayersModal() {
    document.getElementById('addPlayersModal').classList.add('hidden');
}

function confirmAddPlayers() {
    const playerIds = Array.from(selectedPlayers);
    
    fetch(`/auctions/${auctionId}/players`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            player_ids: playerIds
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.modalSystem.success(data.message);
            hideAddPlayersModal();
            selectedPlayers.clear();
            loadPlayers();
            loadAuctionPlayers();
        } else {
            window.modalSystem.error(data.message || 'Error adding players to auction');
        }
    })
    .catch(error => {
        console.error('Error adding players to auction:', error);
        window.modalSystem.error('Error adding players to auction');
    });
}
</script>

<style>
.player-card.selected {
    transform: scale(1.02);
}

.player-card.selected .bg-gray-800\/50 {
    border-color: #10b981;
    box-shadow: 0 0 20px rgba(16, 185, 129, 0.3);
}

.filter-btn.active {
    background-color: #eab308 !important;
    color: #000 !important;
}
</style>
@endsection
@endsection
