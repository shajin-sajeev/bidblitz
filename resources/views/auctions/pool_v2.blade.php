@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-purple-600 to-blue-600 shadow-2xl">
        <div class="container mx-auto px-4 py-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-4xl font-bold text-white mb-2">🏏 Player Pool Management</h1>
                    <p class="text-white/80 text-lg">Build your auction player pool with strategic pricing</p>
                </div>
                <a href="{{ route('dashboard') }}" class="bg-white/20 hover:bg-white/30 text-white font-bold py-3 px-6 rounded-lg transition-all backdrop-blur-sm">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Auction Info Card -->
    <div class="container mx-auto px-4 -mt-6">
        <div class="bg-gray-800/90 backdrop-blur-sm rounded-2xl shadow-2xl p-6 border border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-purple-400 text-3xl font-bold">{{ $auction->name }}</div>
                    <div class="text-gray-400 text-sm mt-1">Auction Name</div>
                </div>
                <div class="text-center">
                    <div class="text-white text-2xl font-semibold">{{ $auction->start_date ? $auction->start_date->format('M d, Y') : 'Not Set' }}</div>
                    <div class="text-gray-400 text-sm mt-1">Start Date</div>
                </div>
                <div class="text-center">
                    <div class="text-white text-2xl font-semibold">{{ $auction->end_date ? $auction->end_date->format('M d, Y') : 'Not Set' }}</div>
                    <div class="text-gray-400 text-sm mt-1">End Date</div>
                </div>
                <div class="text-center">
                    <div class="text-blue-400 text-2xl font-bold">{{ $poolPlayers->count() }}</div>
                    <div class="text-gray-400 text-sm mt-1">Players in Pool</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="container mx-auto px-4 mt-8">
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-700">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1 relative">
                    <form action="{{ route('auctions.pool', $auction) }}" method="GET" class="flex gap-4">
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ $search }}"
                            placeholder="🔍 Search players by name, username, or specialization..." 
                            class="flex-1 px-6 py-4 bg-gray-900/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all text-lg"
                        >
                        <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-4 px-8 rounded-xl transition-all transform hover:scale-105 shadow-lg">
                            <i class="fas fa-search mr-2"></i>Search
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="container mx-auto px-4 mt-8 pb-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Available Players Section -->
            <div class="lg:col-span-2">
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-700">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-purple-400">📋 Available Players</h2>
                        <div class="text-gray-400 text-sm">{{ $players->count() }} players found</div>
                    </div>
                    
                    <div class="space-y-4 max-h-[600px] overflow-y-auto pr-2">
                        @forelse($players as $player)
                            <div class="bg-gray-900/50 rounded-xl border border-gray-700 hover:border-purple-500/50 transition-all duration-300 overflow-hidden">
                                <div class="p-4">
                                    <div class="flex items-start gap-4">
                                        <!-- Player Avatar -->
                                        <div class="flex-shrink-0">
                                            <div class="w-16 h-16 bg-gradient-to-br from-purple-600 to-blue-600 rounded-full overflow-hidden border-2 border-purple-500">
                                                @if($player->avatar)
                                                    <img src="/avatars/{{ $player->avatar }}" alt="{{ $player->name }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center">
                                                        <span class="text-white font-bold text-xl">{{ $player->name ? substr($player->name, 0, 1) : '?' }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <!-- Player Info -->
                                        <div class="flex-1">
                                            <div class="flex items-start justify-between mb-3">
                                                <div>
                                                    <h3 class="text-lg font-bold text-white mb-1">{{ $player->name ?? 'Unknown Player' }}</h3>
                                                    <p class="text-gray-400 text-sm">@{{ $player->username ?? $player->unique_username ?? 'no-username' }}</p>
                                                </div>
                                                <div class="bg-purple-600/20 px-3 py-1 rounded-full">
                                                    <span class="text-purple-400 text-xs font-semibold">{{ $player->specialization ?? 'Player' }}</span>
                                                </div>
                                            </div>
                                            
                                            <!-- Player Stats -->
                                            <div class="grid grid-cols-2 gap-3 mb-4">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-gray-400 text-sm">📧</span>
                                                    <span class="text-white text-sm">{{ $player->email ?? 'No email' }}</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-gray-400 text-sm">📱</span>
                                                    <span class="text-white text-sm">{{ $player->phone ?? 'No phone' }}</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-gray-400 text-sm">⏱️</span>
                                                    <span class="text-white text-sm">{{ $player->experience_years ?? 0 }} years</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-gray-400 text-sm">💰</span>
                                                    <span class="text-green-400 text-sm font-semibold">₹{{ number_format($player->base_price ?? 0, 2) }}</span>
                                                </div>
                                            </div>
                                            
                                            <!-- Add to Pool Form -->
                                            <form action="{{ route('auctions.pool.store', $auction) }}" method="POST" class="flex gap-3 items-end">
                                                @csrf
                                                <input type="hidden" name="player_id" value="{{ $player->id }}">
                                                <div class="flex-1">
                                                    <label class="text-gray-400 text-xs block mb-1">Base Price (₹)</label>
                                                    <input type="number" name="base_price" 
                                                           class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-purple-500 transition-all" 
                                                           placeholder="Enter base price" 
                                                           required 
                                                           min="0" 
                                                           step="0.01"
                                                           value="{{ $player->base_price ?? 1000 }}">
                                                </div>
                                                <button type="submit" class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white font-bold py-2 px-6 rounded-lg transition-all transform hover:scale-105 shadow-lg">
                                                    <i class="fas fa-plus mr-2"></i>Add to Pool
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <div class="w-24 h-24 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-search text-gray-500 text-3xl"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-400 mb-2">No Players Found</h3>
                                <p class="text-gray-500">Try adjusting your search criteria</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <!-- Pool Players Section -->
            <div class="lg:col-span-1">
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-700 sticky top-4">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-blue-400">🎯 Current Pool</h2>
                        <div class="bg-blue-600/20 px-3 py-1 rounded-full">
                            <span class="text-blue-400 text-xs font-semibold">{{ $poolPlayers->count() }} players</span>
                        </div>
                    </div>
                    
                    <div class="space-y-3 max-h-[500px] overflow-y-auto">
                        @forelse($poolPlayers as $poolPlayer)
                            <div class="bg-gray-900/50 rounded-lg border border-gray-700 p-3 hover:border-blue-500/50 transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-full overflow-hidden flex-shrink-0">
                                        @if($poolPlayer->player->avatar)
                                            <img src="/avatars/{{ $poolPlayer->player->avatar }}" alt="{{ $poolPlayer->player->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <span class="text-white font-bold text-xs">{{ $poolPlayer->player->name ? substr($poolPlayer->player->name, 0, 1) : '?' }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-white font-semibold text-sm truncate">{{ $poolPlayer->player->name }}</h4>
                                        <p class="text-gray-400 text-xs">{{ $poolPlayer->player->specialization }}</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-green-400 font-bold text-sm">₹{{ number_format($poolPlayer->base_price, 2) }}</div>
                                        <form action="{{ route('auctions.pool.remove', [$auction, $poolPlayer->id]) }}" method="POST" class="mt-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-300 text-xs">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <div class="w-16 h-16 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-users text-gray-500 text-xl"></i>
                                </div>
                                <h4 class="text-gray-400 font-semibold mb-1">No Players in Pool</h4>
                                <p class="text-gray-500 text-sm">Add players from the available list</p>
                            </div>
                        @endforelse
                    </div>
                    
                    @if($poolPlayers->count() > 0)
                        <div class="mt-6 pt-6 border-t border-gray-700">
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-gray-400 text-sm">Total Pool Value:</span>
                                <span class="text-green-400 font-bold text-lg">₹{{ number_format($poolPlayers->sum('base_price'), 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-gray-400 text-sm">Average Price:</span>
                                <span class="text-blue-400 font-bold">₹{{ number_format($poolPlayers->avg('base_price'), 2) }}</span>
                            </div>
                            <button class="w-full bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 text-white font-bold py-3 rounded-lg transition-all transform hover:scale-105 shadow-lg">
                                <i class="fas fa-check mr-2"></i>Finalize Pool
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
// Add smooth scrolling and animations
document.addEventListener('DOMContentLoaded', function() {
    // Animate cards on scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.bg-gray-900\\/50').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'all 0.6s ease-out';
        observer.observe(el);
    });
});
</script>
@endsection
@endsection
