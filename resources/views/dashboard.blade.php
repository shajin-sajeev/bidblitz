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
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    📊 Overview
                </a>
                <a href="{{ route('auctions.create') }}" class="nav-item {{ request()->routeIs('auctions.create') ? 'active' : '' }}">
                    ➕ Create Auction
                </a>
                <a href="{{ route('auctions.join') }}" class="nav-item {{ request()->routeIs('auctions.join') ? 'active' : '' }}">
                    🔗 Join Auction
                </a>
                <a href="{{ route('auctions.joined') }}" class="nav-item {{ request()->routeIs('auctions.joined') ? 'active' : '' }}">
                    📋 Joined Auctions
                </a>
                <a href="{{ route('auctions.history') }}" class="nav-item {{ request()->routeIs('auctions.history') ? 'active' : '' }}">
                    📜 Auction History
                </a>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">Teams</div>
                <a href="{{ route('teams.joined') }}" class="nav-item">
                    � Joined Teams
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="dashboard-main" style="flex: 1; min-width: 0; overflow: hidden;">
        <div class="dashboard-mobile-tabs" aria-label="Dashboard sections">
            <button type="button" class="dashboard-tab-button active" data-dashboard-tab="overview">Overview</button>
            <button type="button" class="dashboard-tab-button" data-dashboard-tab="auctions">Auctions</button>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-4 gap-4 mb-8 dashboard-tab-panel active" data-dashboard-panel="overview">
            <a href="{{ route('dashboard.created-auctions') }}" class="glass-card text-center dashboard-overview-card">
                <div style="font-size: 2rem; font-weight: bold; color: var(--primary);">{{ \App\Models\Auction::where('created_by', auth()->id())->count() }}</div>
                <div style="font-size: 0.85rem; color: var(--text-muted);">Created Auctions</div>
            </a>
            <a href="{{ route('dashboard.my-teams') }}" class="glass-card text-center dashboard-overview-card">
                <div style="font-size: 2rem; font-weight: bold; color: var(--accent);">{{ \App\Models\Team::where('owner_id', auth()->id())->count() }}</div>
                <div style="font-size: 0.85rem; color: var(--text-muted);">My Teams</div>
            </a>
            <a href="{{ route('dashboard.live-auctions') }}" class="glass-card text-center dashboard-overview-card">
                <div style="font-size: 2rem; font-weight: bold; color: #10b981;">{{ \App\Models\Auction::where('status', 'live')->count() }}</div>
                <div style="font-size: 0.85rem; color: var(--text-muted);">Live Auctions</div>
            </a>
            <a href="{{ route('dashboard.completed-auctions') }}" class="glass-card text-center dashboard-overview-card">
                <div style="font-size: 2rem; font-weight: bold; color: #ef4444;">{{ \App\Models\Auction::where('status', 'completed')->count() }}</div>
                <div style="font-size: 0.85rem; color: var(--text-muted);">Completed</div>
            </a>
        </div>

        <!-- All Auctions with Pagination -->
        <div class="glass-card dashboard-tab-panel" data-dashboard-panel="auctions">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3>📊 All Auctions</h3>
                <a href="{{ route('auctions.create') }}" class="btn btn-primary" style="padding: 0.5rem 1rem;">
                    ➕ Create Auction
                </a>
            </div>
            
            <div class="auction-table-wrap" style="overflow-x: auto;">
                <table class="auction-list-table" style="width: 100%; border-collapse: collapse;">
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
                                <td style="padding: 1rem;" data-label="Name">
                                    <div>
                                        <strong>{{ $auction->name }}</strong>
                                        <div style="font-size: 0.85rem; color: var(--text-muted);">by {{ $auction->creator->name ?? 'Unknown' }}</div>
                                    </div>
                                </td>
                                <td style="padding: 1rem;" data-label="Sport">{{ $auction->sport }}</td>
                                <td style="padding: 1rem;" data-label="Status">
                                    <span style="background: {{ $auction->status === 'live' ? '#10b981' : ($auction->status === 'completed' ? '#ef4444' : 'var(--primary)') }}; padding: 2px 8px; border-radius: 12px; font-size: 0.8rem; color: white;">
                                        {{ ucfirst($auction->status) }}
                                    </span>
                                </td>
                                <td style="padding: 1rem;" data-label="Teams">{{ $auction->total_teams }}</td>
                                <td style="padding: 1rem;" data-label="Budget">${{ number_format($auction->budget, 2) }}</td>
                                <td style="padding: 1rem;" data-label="Actions">
                                    <div class="auction-row-actions" style="display: flex; gap: 0.5rem;">
                                        @if($auction->created_by === auth()->id() && !in_array($auction->status, ['completed', 'live'], true))
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
                    {{ $auctions->links() }}
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

.dashboard-overview-card {
    display: block;
    text-decoration: none;
    color: inherit;
    transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
}

.dashboard-overview-card:hover {
    transform: translateY(-3px);
    border-color: rgba(251, 191, 36, 0.28);
    box-shadow: 0 16px 34px rgba(0, 0, 0, 0.18);
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

/* Pagination is styled globally in app.css/style.css for consistency. */

</style>

@endsection
