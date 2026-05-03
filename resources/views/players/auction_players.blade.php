@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-yellow-400">Add Players to Auction</h1>
        <div class="flex space-x-3">
            <button onclick="showAddPlayersModal()" class="bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-2 px-4 rounded transition-colors">
                <i class="fas fa-plus mr-2"></i>Add Selected Players
            </button>
            <a href="{{ route('auctions.show', $auction->id) }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back to Auction
            </a>
        </div>
    </div>

    <!-- Auction Info -->
    <div class="bg-gray-800 rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-yellow-400 mb-2">{{ $auction->name }}</h2>
        <p class="text-gray-300">{{ $auction->description }}</p>
        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <span class="text-gray-400">Start Date:</span>
                <span class="text-white ml-2">{{ $auction->start_date->format('M d, Y H:i') }}</span>
            </div>
            <div>
                <span class="text-gray-400">End Date:</span>
                <span class="text-white ml-2">{{ $auction->end_date->format('M d, Y H:i') }}</span>
            </div>
            <div>
                <span class="text-gray-400">Players Added:</span>
                <span class="text-white ml-2">{{ $auction->players()->count() }}/20</span>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="mb-6">
        <div class="relative">
            <input 
                type="text" 
                id="playerSearch" 
                placeholder="Search by name, username, email, or specialization..." 
                class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-yellow-500 transition-colors"
            >
            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                <i class="fas fa-search text-gray-400"></i>
            </div>
        </div>
    </div>

    <!-- Players List -->
    <div id="playersList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Players will be loaded here via JavaScript -->
    </div>

    <!-- Pagination -->
    <div id="pagination" class="mt-8 flex justify-center">
        <!-- Pagination will be loaded here via JavaScript -->
    </div>
</div>

@section('scripts')
<script>
let currentPage = 1;
let searchQuery = '';
let selectedPlayers = new Set();
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

function loadPlayers() {
    fetch(`/players/search?q=${encodeURIComponent(searchQuery)}&page=${currentPage}`)
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
            <div class="col-span-full text-center py-12">
                <i class="fas fa-users text-6xl text-gray-600 mb-4"></i>
                <p class="text-gray-400 text-lg">No players found</p>
            </div>
        `;
        return;
    }

    container.innerHTML = players.map(player => `
        <div class="bg-gray-800 rounded-lg p-6 hover:bg-gray-700 transition-colors ${selectedPlayers.has(player.id) ? 'ring-2 ring-yellow-500' : ''}">
            <div class="flex items-center mb-4">
                <div class="w-16 h-16 bg-yellow-500 rounded-full flex items-center justify-center text-black font-bold text-xl mr-4">
                    ${player.avatar ? `<img src="/avatars/${player.avatar}" alt="${player.name}" class="w-16 h-16 rounded-full object-cover">` : player.name.charAt(0).toUpperCase()}
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-yellow-400">${player.name}</h3>
                    <p class="text-sm text-gray-400">@${player.unique_username}</p>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="player-${player.id}" 
                           value="${player.id}" 
                           ${selectedPlayers.has(player.id) ? 'checked' : ''}
                           onchange="togglePlayerSelection(${player.id})"
                           class="w-5 h-5 text-yellow-500 bg-gray-700 border-gray-600 rounded focus:ring-yellow-500 focus:ring-2">
                </div>
            </div>
            
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-400">Email:</span>
                    <span class="text-white">${player.email}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Specialization:</span>
                    <span class="text-white">${player.specialization}</span>
                </div>
            </div>
            
            <div class="mt-4">
                <label for="player-${player.id}" class="cursor-pointer">
                    <div class="w-full py-2 px-3 rounded text-center transition-colors ${
                        selectedPlayers.has(player.id) 
                            ? 'bg-green-600 hover:bg-green-700 text-white' 
                            : 'bg-gray-700 hover:bg-gray-600 text-gray-300'
                    }">
                        ${selectedPlayers.has(player.id) ? '✓ Selected' : 'Select Player'}
                    </div>
                </label>
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
        html += `<button onclick="changePage(${pagination.current_page - 1})" class="px-3 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded transition-colors">
            <i class="fas fa-chevron-left"></i>
        </button>`;
    }
    
    // Page numbers
    for (let i = 1; i <= pagination.last_page; i++) {
        if (i === pagination.current_page) {
            html += `<button class="px-3 py-2 bg-yellow-500 text-black font-bold rounded">${i}</button>`;
        } else if (Math.abs(i - pagination.current_page) <= 2 || i === 1 || i === pagination.last_page) {
            html += `<button onclick="changePage(${i})" class="px-3 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded transition-colors">${i}</button>`;
        } else if (Math.abs(i - pagination.current_page) === 3) {
            html += '<span class="px-3 py-2 text-gray-400">...</span>';
        }
    }
    
    // Next button
    if (pagination.current_page < pagination.last_page) {
        html += `<button onclick="changePage(${pagination.current_page + 1})" class="px-3 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded transition-colors">
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
        selectedPlayers.add(playerId);
    }
    
    // Re-render to update visual state
    loadPlayers();
    updateSelectedCount();
}

function updateSelectedCount() {
    // Update UI with selected count if needed
    console.log('Selected players:', selectedPlayers.size);
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
    
    const confirmAdd = confirm(`Are you sure you want to add ${selectedPlayers.size} player(s) to this auction?`);
    
    if (confirmAdd) {
        addPlayersToAuction();
    }
}

function addPlayersToAuction() {
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
@endsection
@endsection
