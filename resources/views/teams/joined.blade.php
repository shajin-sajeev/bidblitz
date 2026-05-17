@extends('layouts.app')

@section('content')
<div style="display: flex; gap: 2rem; flex-wrap: wrap;">
    <!-- Sidebar -->
    <div style="width: 280px; flex-shrink: 0;">
        @include('partials.sidebar')
    </div>

    <!-- Main Content -->
    <div style="flex-grow: 1; min-width: 300px;">
        <div class="glass-card mb-8">
            <h2>👥 My Teams</h2>
            <p>Manage your auction teams and view player rosters.</p>
        </div>

        <!-- My Owned Teams -->
        <div class="glass-card mb-8">
            <h3>🏆 Teams I Own</h3>
            @forelse($userTeams as $team)
                <div style="border: var(--glass-border); padding: 1.5rem; margin-bottom: 1rem; border-radius: 12px;">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h4 style="margin: 0;">{{ $team->name }}</h4>
                            <div style="color: var(--text-muted); font-size: 0.9rem;">
                                🏟️ {{ $team->auction->name }} • {{ $team->auction->sport }}
                            </div>
                            <div style="color: var(--text-muted); font-size: 0.85rem;">
                                Budget: ${{ number_format($team->auction->budget, 2) }}
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <span style="background: var(--accent); color: #000; padding: 6px 16px; border-radius: 20px; font-size: 0.9rem; font-weight: 600;">
                                OWNER
                            </span>
                        </div>
                    </div>

                    <!-- Team Stats -->
                    <div class="grid grid-cols-4 gap-4 mb-4">
                        <div style="text-align: center;">
                            <div style="font-size: 1.2rem; font-weight: bold; color: var(--primary);">{{ $team->players->count() }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">Players</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 1.2rem; font-weight: bold; color: var(--accent);">${{ number_format($team->players->sum('sold_price') ?? 0, 0) }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">Spent</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 1.2rem; font-weight: bold; color: #10b981;">${{ number_format($team->auction->budget - ($team->players->sum('sold_price') ?? 0), 0) }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">Remaining</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 1.2rem; font-weight: bold; color: #f59e0b;">{{ $team->auction->min_players }}-{{ $team->auction->max_players }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">Required</div>
                        </div>
                    </div>

                    <!-- Players Preview -->
                    @if($team->players->count() > 0)
                        <div style="margin-bottom: 1rem;">
                            <h5 style="margin-bottom: 0.5rem; color: var(--primary);">👤 Players</h5>
                            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                                @foreach($team->players->take(5) as $player)
                                    <div style="background: rgba(251, 191, 36, 0.1); padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.8rem;">
                                        {{ $player->player->name ?? 'Unknown' }}
                                        @if($player->sold_price)
                                            <span style="color: var(--text-muted);">(${{ number_format($player->sold_price, 0) }})</span>
                                        @endif
                                    </div>
                                @endforeach
                                @if($team->players->count() > 5)
                                    <div style="color: var(--text-muted); font-size: 0.8rem; padding: 0.25rem 0.75rem;">
                                        +{{ $team->players->count() - 5 }} more
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ route('teams.show', $team) }}" class="btn btn-primary" style="font-size: 0.85rem; padding: 0.5rem 1rem;">📊 View Team</a>
                        @if($team->auction->status === 'live')
                            <a href="{{ route('auctions.live', $team->auction) }}" class="btn btn-accent" style="font-size: 0.85rem; padding: 0.5rem 1rem;">🔴 Enter Auction</a>
                        @endif
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 2rem; color: var(--text-muted);">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">🏆</div>
                    <h4>No Teams Owned</h4>
                    <p>You don't own any teams yet. Join an auction to create or claim a team!</p>
                    <a href="{{ route('auctions.join') }}" class="btn btn-primary" style="margin-top: 1rem;">🔗 Join Auction</a>
                </div>
            @endforelse

            @if($userTeams->hasPages())
                <div class="pagination-wrapper">
                    <div class="pagination-info">
                        Showing <span>{{ $userTeams->firstItem() }}</span> to <span>{{ $userTeams->lastItem() }}</span> of <span>{{ $userTeams->total() }}</span> teams
                    </div>
                    {{ $userTeams->links() }}
                </div>
            @endif
        </div>

        <!-- Participated Auctions -->
        @if($participatedAuctions->count() > 0)
            <div class="glass-card">
                <h3>🎯 Auctions I Participate In</h3>
                @foreach($participatedAuctions as $auction)
                    <div style="border: var(--glass-border); padding: 1rem; margin-bottom: 1rem; border-radius: 8px;">
                        <div class="flex justify-between items-center">
                            <div>
                                <strong>{{ $auction->name }}</strong>
                                <div style="font-size: 0.85rem; color: var(--text-muted);">
                                    {{ $auction->sport }} • {{ $auction->teams->count() }} teams
                                </div>
                            </div>
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="{{ route('teams.auction', $auction) }}" class="btn btn-primary" style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">View Teams</a>
                                @if($auction->status === 'live')
                                    <a href="{{ route('auctions.live', $auction) }}" class="btn btn-accent" style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">Live</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
                @if($participatedAuctions->hasPages())
                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Showing <span>{{ $participatedAuctions->firstItem() }}</span> to <span>{{ $participatedAuctions->lastItem() }}</span> of <span>{{ $participatedAuctions->total() }}</span> auctions
                        </div>
                        {{ $participatedAuctions->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
