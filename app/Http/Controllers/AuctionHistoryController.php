<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Auction;
use App\Models\AuctionHistory;
use App\Models\AuctionStatistics;
use App\Models\AuctionParticipant;

class AuctionHistoryController extends Controller
{
    public function index()
    {
        $userAuctions = Auction::with(['creator', 'teams', 'statistics'])
            ->where('created_by', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        $participatedAuctions = Auction::with(['creator', 'teams', 'statistics'])
            ->whereHas('participants', function($query) {
                $query->where('user_id', auth()->id());
            })
            ->where('created_by', '!=', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('auctions.history', compact('userAuctions', 'participatedAuctions'));
    }

    public function joined()
    {
        $joinedAuctions = Auction::with(['creator', 'teams', 'participants'])
            ->whereHas('participants', function($query) {
                $query->where('user_id', auth()->id());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('auctions.joined', compact('joinedAuctions'));
    }

    public function show(Auction $auction)
    {
        $this->authorize('view', $auction);
        
        $auction->load(['creator', 'teams.owner', 'participants.user', 'statistics', 'history' => function($query) {
            $query->with(['player', 'team', 'bidder'])->orderBy('action_at', 'desc');
        }]);

        $totalBids = $auction->history()->where('action', 'bid_placed')->count();
        $totalSold = $auction->history()->where('action', 'player_sold')->count();
        $totalUnsold = $auction->history()->where('action', 'player_unsold')->count();

        return view('auctions.show', compact('auction', 'totalBids', 'totalSold', 'totalUnsold'));
    }

    public function statistics(Auction $auction)
    {
        $this->authorize('view', $auction);
        
        $stats = $auction->statistics;
        $history = $auction->history()->with(['player', 'team', 'bidder'])->get();

        // Calculate additional statistics
        $bidStats = [
            'total_bids' => $history->where('action', 'bid_placed')->count(),
            'unique_bidders' => $history->where('action', 'bid_placed')->pluck('bidder_id')->unique()->count(),
            'average_bid_amount' => $history->where('action', 'bid_placed')->avg('amount') ?? 0,
            'highest_bid' => $history->where('action', 'bid_placed')->max('amount') ?? 0,
            'lowest_bid' => $history->where('action', 'bid_placed')->min('amount') ?? 0,
        ];

        $playerStats = [
            'total_players' => $history->where('action', 'player_added')->count(),
            'sold_players' => $history->where('action', 'player_sold')->count(),
            'unsold_players' => $history->where('action', 'player_unsold')->count(),
            'average_player_price' => $history->where('action', 'player_sold')->avg('amount') ?? 0,
        ];

        return view('auctions.statistics', compact('auction', 'stats', 'bidStats', 'playerStats', 'history'));
    }
}
