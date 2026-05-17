<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Auction;
use App\Models\AuctionHistory;
use App\Models\AuctionStatistics;
use App\Models\AuctionParticipant;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AuctionHistoryController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $currentUserId = auth()->id();
        $perPage = config('pagination.per_page');
        
        $userAuctions = Auction::with(['creator', 'teams', 'statistics'])
            ->where('created_by', $currentUserId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'created_page')
            ->withQueryString();

        $participatedAuctions = Auction::with(['creator', 'teams', 'statistics'])
            ->whereHas('participants', function($query) use ($currentUserId) {
                $query->where('user_id', $currentUserId);
            })
            ->where('created_by', '!=', $currentUserId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'participated_page')
            ->withQueryString();

        return view('auctions.history', compact('userAuctions', 'participatedAuctions'));
    }

    public function joined()
    {
        $currentUserId = auth()->id();
        
        $joinedAuctions = Auction::with(['creator', 'teams', 'participants', 'history', 'auctionPlayers'])
            ->whereHas('participants', function($query) use ($currentUserId) {
                $query->where('user_id', $currentUserId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(config('pagination.per_page'))
            ->withQueryString()
            ->through(function ($auction) use ($currentUserId) {
                // Add joined_at date from participant record
                $participant = $auction->participants->where('user_id', $currentUserId)->first();
                $auction->joined_at = $participant ? $participant->joined_at : null;
                return $auction;
            });

        return view('auctions.joined', compact('joinedAuctions'));
    }

    public function show(Auction $auction)
    {
        // Simple authorization check - user must be creator or participant
        $currentUserId = auth()->id();
        $isCreator = $auction->created_by === $currentUserId;
        $isParticipant = $auction->participants()->where('user_id', $currentUserId)->exists();
        
        if (!$isCreator && !$isParticipant) {
            abort(403, 'Unauthorized action.');
        }
        
        $auction->load([
            'creator', 
            'participants.user', 
            'statistics', 
            'auctionPlayers.player'
        ]);
        $teams = $auction->teams()
            ->with(['owner', 'players.player'])
            ->paginate(config('pagination.per_page'), ['*'], 'teams_page')
            ->withQueryString();
        $history = $auction->history()
            ->with(['player', 'team', 'bidder'])
            ->orderBy('action_at', 'desc')
            ->paginate(config('pagination.per_page'), ['*'], 'history_page')
            ->withQueryString();

        $totalBids = $auction->history()->where('action', 'bid_placed')->count();
        $totalSold = $auction->history()->where('action', 'player_sold')->count();
        $totalUnsold = $auction->history()->where('action', 'player_unsold')->count();

        return view('auctions.show', compact('auction', 'teams', 'history', 'totalBids', 'totalSold', 'totalUnsold'));
    }

    public function statistics(Auction $auction)
    {
        // Simple authorization check - user must be creator or participant
        $currentUserId = auth()->id();
        $isCreator = $auction->created_by === $currentUserId;
        $isParticipant = $auction->participants()->where('user_id', $currentUserId)->exists();
        
        if (!$isCreator && !$isParticipant) {
            abort(403, 'Unauthorized action.');
        }
        
        $auction->load(['teams.owner', 'teams.players.player']);
        $teams = $auction->teams()
            ->with(['owner', 'players.player'])
            ->paginate(config('pagination.per_page'), ['*'], 'teams_page')
            ->withQueryString();
        $stats = $auction->statistics;
        $allHistory = $auction->history()->with(['player', 'team', 'bidder'])->get();
        $history = $auction->history()
            ->with(['player', 'team', 'bidder'])
            ->orderBy('action_at', 'desc')
            ->paginate(config('pagination.per_page'), ['*'], 'history_page')
            ->withQueryString();

        // Calculate additional statistics
        $bidStats = [
            'total_bids' => $allHistory->where('action', 'bid_placed')->count(),
            'unique_bidders' => $allHistory->where('action', 'bid_placed')->pluck('bidder_id')->unique()->count(),
            'average_bid_amount' => $allHistory->where('action', 'bid_placed')->avg('amount') ?? 0,
            'highest_bid' => $allHistory->where('action', 'bid_placed')->max('amount') ?? 0,
            'lowest_bid' => $allHistory->where('action', 'bid_placed')->min('amount') ?? 0,
        ];

        $playerStats = [
            'total_players' => $allHistory->where('action', 'player_added')->count(),
            'sold_players' => $allHistory->where('action', 'player_sold')->count(),
            'unsold_players' => $allHistory->where('action', 'player_unsold')->count(),
            'average_player_price' => $allHistory->where('action', 'player_sold')->avg('amount') ?? 0,
        ];

        return view('auctions.statistics', compact('auction', 'teams', 'stats', 'bidStats', 'playerStats', 'history'));
    }

    public function leave(Auction $auction, Request $request)
    {
        $currentUserId = auth()->id();
        
        // Check if user is a participant (not creator)
        $isCreator = $auction->created_by === $currentUserId;
        if ($isCreator) {
            return response()->json([
                'success' => false,
                'message' => 'Auction creators cannot leave their own auction.'
            ]);
        }

        $participant = $auction->participants()->where('user_id', $currentUserId)->first();
        if (!$participant) {
            return response()->json([
                'success' => false,
                'message' => 'You are not a participant in this auction.'
            ]);
        }

        // Check if auction is in setup phase
        if ($auction->status !== 'setup') {
            return response()->json([
                'success' => false,
                'message' => 'You can only leave auctions during the setup phase.'
            ]);
        }

        try {
            // Remove user's team ownership if they have a team
            $userTeam = $auction->teams()->where('owner_id', $currentUserId)->first();
            if ($userTeam) {
                $userTeam->update(['owner_id' => null]);
            }

            // Remove participant record
            $participant->delete();

            return response()->json([
                'success' => true,
                'message' => 'You have successfully left the auction.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while leaving the auction. Please try again.'
            ]);
        }
    }
}
