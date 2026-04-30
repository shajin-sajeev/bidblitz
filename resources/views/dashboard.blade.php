@extends('layouts.app')

@section('content')
<div style="display: flex; gap: 2rem; flex-wrap: wrap;">
    <!-- Sidebar -->
    <div style="width: 280px; flex-shrink: 0;">
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
    <div style="flex-grow: 1; min-width: 300px;">
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

        <!-- Recent Auctions -->
        <div class="glass-card mb-8">
            <h3>📊 Recent Auctions</h3>
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
                        @php
                            $recentAuctions = \App\Models\Auction::with('creator')
                                ->orderBy('created_at', 'desc')
                                ->limit(10)
                                ->get();
                        @endphp
                        @forelse($recentAuctions as $auction)
                            <tr style="border-bottom: 1px solid var(--border-color);">
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
                                        @endif
                                        <a href="{{ route('auctions.live', $auction) }}" class="btn btn-accent" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">View</a>
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
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-2 gap-8">
            <div class="glass-card">
                <h3>⚡ Quick Actions</h3>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <a href="{{ route('auctions.create') }}" class="btn btn-primary">
                        ➕ Create New Auction
                    </a>
                    <a href="{{ route('auctions.join') }}" class="btn btn-accent">
                        🔗 Join Existing Auction
                    </a>
                    <a href="{{ route('profile.create') }}" class="btn" style="background: rgba(255,255,255,0.1);">
                        ⚙️ Update Profile
                    </a>
                </div>
            </div>
            
            <div class="glass-card">
                <h3>📈 Activity Feed</h3>
                @php
                    $recentActivity = \App\Models\AuctionHistory::with(['auction', 'user'])
                        ->orderBy('action_at', 'desc')
                        ->limit(5)
                        ->get();
                @endphp
                @forelse($recentActivity as $activity)
                    <div style="padding: 0.75rem 0; border-bottom: 1px solid var(--border-color);">
                        <div style="font-size: 0.9rem;">
                            <strong>{{ $activity->auction->name ?? 'Unknown Auction' }}</strong>
                        </div>
                        <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">
                            {{ ucfirst(str_replace('_', ' ', $activity->action)) }} 
                            @if($activity->user) by {{ $activity->user->name ?? 'Unknown' }} @endif
                            <span style="float: right;">{{ $activity->action_at->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-muted">No recent activity.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
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
</style>
@endsection
