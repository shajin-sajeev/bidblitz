@extends('layouts.app')

@section('content')
<div class="dashboard-container" style="display: flex; gap: 2rem; align-items: flex-start;">
    <!-- Sidebar -->
    <div class="dashboard-sidebar" style="width: 280px; flex-shrink: 0;">
        <!-- User Profile Card -->
        <div class="glass-card profile-card mb-4">
            <div class="profile-header">
                @if(auth()->user()->profile_image)
                    <div class="profile-avatar-container">
                        <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="Profile" class="profile-avatar">
                    </div>
                @else
                    <div class="profile-avatar-container">
                        <div class="profile-avatar profile-avatar-default">
                            👤
                        </div>
                    </div>
                @endif
            </div>
            
            <div class="profile-info">
                <h3 class="profile-name">{{ auth()->user()->name ?? 'User ' . auth()->user()->phone }}</h3>
                <div class="profile-role">{{ auth()->user()->playerProfile->player_role ?? 'Player' }}</div>
                <div class="profile-stats">
                    <div class="stat-item">
                        <span class="stat-value">{{ \App\Models\Team::where('owner_id', auth()->id())->count() }}</span>
                        <span class="stat-label">Teams</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">{{ \App\Models\Auction::where('created_by', auth()->id())->count() }}</span>
                        <span class="stat-label">Auctions</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <div class="glass-card">
            <h5 style="margin-bottom: 1rem; color: var(--primary);">🏠 Dashboard</h5>
            
            <div style="margin-bottom: 1.5rem;">
                <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">Auctions</div>
                <a href="{{ route('dashboard') }}" class="nav-item active">
                    📊 Overview
                </a>
                <a href="{{ route('auctions.create') }}" class="nav-item">
                    ➕ Create Auction
                </a>
                <a href="{{ route('auctions.join') }}" class="nav-item">
                    🔗 Join Auction
                </a>
                <a href="{{ route('auctions.joined') }}" class="nav-item">
                    📋 Joined Auctions
                </a>
                <a href="{{ route('auctions.history') }}" class="nav-item">
                    📜 Auction History
                </a>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">Teams</div>
                <a href="{{ route('teams.joined') }}" class="nav-item">
                    � Joined Teams
                </a>
            </div>

            <div>
                <a href="{{ route('profile.create') }}" class="nav-item">
                    ⚙️ Profile Settings
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="dashboard-main" style="flex: 1; min-width: 0; overflow: hidden;">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-4 gap-4 mb-8">
            <div class="glass-card text-center">
                <div style="font-size: 2rem; font-weight: bold; color: var(--primary);">{{ \App\Models\Auction::where('created_by', auth()->id())->count() }}</div>
                <div style="font-size: 0.85rem; color: var(--text-muted);">Created Auctions</div>
            </div>
            <div class="glass-card text-center">
                <div style="font-size: 2rem; font-weight: bold; color: var(--accent);">{{ \App\Models\Team::where('owner_id', auth()->id())->count() }}</div>
                <div style="font-size: 0.85rem; color: var(--text-muted);">My Teams</div>
            </div>
            <div class="glass-card text-center">
                <div style="font-size: 2rem; font-weight: bold; color: #10b981;">{{ \App\Models\Auction::where('status', 'live')->count() }}</div>
                <div style="font-size: 0.85rem; color: var(--text-muted);">Live Auctions</div>
            </div>
            <div class="glass-card text-center">
                <div style="font-size: 2rem; font-weight: bold; color: #ef4444;">{{ \App\Models\Auction::where('status', 'completed')->count() }}</div>
                <div style="font-size: 0.85rem; color: var(--text-muted);">Completed</div>
            </div>
        </div>

        <!-- All Auctions with Pagination -->
        <div class="glass-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3>📊 All Auctions</h3>
                <a href="{{ route('auctions.create') }}" class="btn btn-primary" style="padding: 0.5rem 1rem;">
                    ➕ Create Auction
                </a>
            </div>
            
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <th style="text-align: left; padding: 1rem; font-weight: 600;">Name</th>
                            <th style="text-align: left; padding: 1rem; font-weight: 600;">Sport</th>
                            <th style="text-align: left; padding: 1rem; font-weight: 600;">Status</th>
                            <th style="text-align: left; padding: 1rem; font-weight: 600;">Teams</th>
                            <th style="text-align: left; padding: 1rem; font-weight: 600;">Budget</th>
                            <th style="text-align: left; padding: 1rem; font-weight: 600;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($auctions as $auction)
                            <tr style="border-bottom: 1px solid var(--border-color);" data-auction-id="{{ $auction->id }}">
                                <td style="padding: 1rem;">
                                    <div>
                                        <strong>{{ $auction->name }}</strong>
                                        <div style="font-size: 0.85rem; color: var(--text-muted);">by {{ $auction->creator->name ?? 'Unknown' }}</div>
                                    </div>
                                </td>
                                <td style="padding: 1rem;">{{ $auction->sport }}</td>
                                <td style="padding: 1rem;">
                                    <span style="background: {{ $auction->status === 'live' ? '#10b981' : ($auction->status === 'completed' ? '#ef4444' : 'var(--primary)') }}; padding: 2px 8px; border-radius: 12px; font-size: 0.8rem; color: white;">
                                        {{ ucfirst($auction->status) }}
                                    </span>
                                </td>
                                <td style="padding: 1rem;">{{ $auction->total_teams }}</td>
                                <td style="padding: 1rem;">${{ number_format($auction->budget, 2) }}</td>
                                <td style="padding: 1rem;">
                                    <div style="display: flex; gap: 0.5rem;">
                                        @if($auction->created_by === auth()->id())
                                            <a href="{{ route('auctions.pool', $auction) }}" class="btn" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">Manage</a>
                                            @if($auction->status === 'active')
                                                @if($auction->canStartLive())
                                                    <form method="POST" action="{{ route('auctions.start', $auction) }}" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-primary" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">Start Auction</button>
                                                    </form>
                                                @else
                                                    <span style="font-size: 0.72rem; color: var(--text-muted);" title="Complete Team Setup and have all teams join before starting.">Start locked</span>
                                                @endif
                                            @endif
                                        @endif
                                        <a href="{{ route('auctions.live', $auction) }}" class="btn btn-accent" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">{{ $auction->status === 'live' ? 'Live' : 'View' }}</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 2rem; text-align: center; color: var(--text-muted);">
                                    No auctions found. Create your first auction to get started!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($auctions->hasPages())
                <div class="pagination-wrapper">
                    <div class="pagination-info">
                        Showing <span>{{ $auctions->firstItem() }}</span> to <span>{{ $auctions->lastItem() }}</span> of <span>{{ $auctions->total() }}</span> auctions
                    </div>
                    {{ $auctions->links('pagination::custom') }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
/* Dashboard Layout Fixes */
@media (max-width: 768px) {
    .dashboard-container {
        flex-direction: column !important;
    }
    
    .dashboard-sidebar {
        width: 100% !important;
        margin-bottom: 2rem;
    }
    
    .dashboard-main {
        width: 100% !important;
    }
    
    .grid.grid-cols-4 {
        grid-template-columns: repeat(2, 1fr) !important;
    }
    
    .grid.grid-cols-2 {
        grid-template-columns: 1fr !important;
    }
}

@media (max-width: 480px) {
    .grid.grid-cols-4 {
        grid-template-columns: 1fr !important;
    }
}

.nav-item {
    display: block;
    padding: 0.75rem 1rem;
    margin-bottom: 0.25rem;
    color: var(--text-main);
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.nav-item:hover {
    background: rgba(251, 191, 36, 0.1);
    color: var(--primary);
}

.nav-item.active {
    background: rgba(251, 191, 36, 0.2);
    color: var(--primary);
    font-weight: 600;
}

/* Ensure proper table scrolling on mobile */
@media (max-width: 640px) {
    .glass-card table {
        font-size: 0.85rem;
    }
    
    .glass-card th,
    .glass-card td {
        padding: 0.5rem !important;
    }
    
    .btn {
        font-size: 0.7rem !important;
        padding: 0.2rem 0.4rem !important;
    }
}

/* Modern Pagination Styles */
.pagination-wrapper {
    margin-top: 3rem;
    padding: 2rem 0;
    background: color-mix(in srgb, var(--card-bg) 92%, var(--primary) 8%);
    border-radius: 20px;
    backdrop-filter: blur(20px);
    border: 1px solid var(--border-color);
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
    background: color-mix(in srgb, var(--card-bg) 90%, #000000 10%);
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
    color: var(--text-muted);
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
    color: color-mix(in srgb, var(--text-muted) 50%, transparent);
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
    color: var(--text-muted);
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

@endsection
