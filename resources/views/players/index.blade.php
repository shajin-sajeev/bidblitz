@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-yellow-400">Player Management</h1>
        <button onclick="showCreatePlayerModal()" class="bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-2 px-4 rounded transition-colors">
            <i class="fas fa-plus mr-2"></i>Add New Player
        </button>
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

<!-- Create Player Modal -->
<div id="createPlayerModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold text-yellow-400">Add New Player</h2>
                    <button onclick="hideCreatePlayerModal()" class="text-gray-400 hover:text-white">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form id="createPlayerForm" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Name *</label>
                            <input type="text" name="name" required class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:border-yellow-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Email *</label>
                            <input type="email" name="email" required class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:border-yellow-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Phone</label>
                            <input type="text" name="phone" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:border-yellow-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Specialization *</label>
                            <input type="text" name="specialization" required class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:border-yellow-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Experience Years *</label>
                            <input type="number" name="experience_years" min="0" max="50" required class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:border-yellow-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Base Price *</label>
                            <input type="number" name="base_price" step="0.01" min="0" required class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:border-yellow-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Description</label>
                        <textarea name="description" rows="3" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:border-yellow-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Avatar</label>
                        <input type="file" name="avatar" accept="image/*" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:border-yellow-500">
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="hideCreatePlayerModal()" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-black font-bold rounded-md transition-colors">
                            Create Player
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
let currentPage = 1;
let searchQuery = '';

// Load players on page load
document.addEventListener('DOMContentLoaded', function() {
    loadPlayers();
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
        <div class="bg-gray-800 rounded-lg p-6 hover:bg-gray-700 transition-colors">
            <div class="flex items-center mb-4">
                <div class="w-16 h-16 bg-yellow-500 rounded-full flex items-center justify-center text-black font-bold text-xl mr-4">
                    ${player.avatar ? `<img src="/avatars/${player.avatar}" alt="${player.name}" class="w-16 h-16 rounded-full object-cover">` : player.name.charAt(0).toUpperCase()}
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-yellow-400">${player.name}</h3>
                    <p class="text-sm text-gray-400">@${player.unique_username}</p>
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
                <div class="flex justify-between">
                    <span class="text-gray-400">Experience:</span>
                    <span class="text-white">${player.experience_years} years</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Base Price:</span>
                    <span class="text-yellow-400 font-semibold">₹${player.base_price}</span>
                </div>
            </div>
            
            <div class="mt-4 flex space-x-2">
                <button onclick="editPlayer(${player.id})" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 px-3 rounded transition-colors text-sm">
                    <i class="fas fa-edit mr-1"></i>Edit
                </button>
                <button onclick="deletePlayer(${player.id})" class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2 px-3 rounded transition-colors text-sm">
                    <i class="fas fa-trash mr-1"></i>Delete
                </button>
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

function showCreatePlayerModal() {
    document.getElementById('createPlayerModal').classList.remove('hidden');
}

function hideCreatePlayerModal() {
    document.getElementById('createPlayerModal').classList.add('hidden');
    document.getElementById('createPlayerForm').reset();
}

// Create player form submission
document.getElementById('createPlayerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/players', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.modalSystem.success(data.message);
            hideCreatePlayerModal();
            loadPlayers();
        } else {
            if (data.errors) {
                let errorMessages = Object.values(data.errors).flat().join('\n');
                window.modalSystem.error(errorMessages);
            } else {
                window.modalSystem.error('Error creating player');
            }
        }
    })
    .catch(error => {
        console.error('Error creating player:', error);
        window.modalSystem.error('Error creating player');
    });
});

function editPlayer(playerId) {
    // Implement edit functionality
    window.modalSystem.info('Edit functionality coming soon');
}

function deletePlayer(playerId) {
    if (confirm('Are you sure you want to delete this player?')) {
        fetch(`/players/${playerId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.modalSystem.success(data.message);
                loadPlayers();
            } else {
                window.modalSystem.error('Error deleting player');
            }
        })
        .catch(error => {
            console.error('Error deleting player:', error);
            window.modalSystem.error('Error deleting player');
        });
    }
}
</script>
@endsection
@endsection
