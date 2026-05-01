<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PlayerPoolController extends Controller
{
    public function index(\App\Models\Auction $auction, Request $request)
    {
        if ($auction->created_by !== auth()->id()) {
            abort(403);
        }

        $search = $request->get('search');
        
        $players = \App\Models\User::where('role', 'player')
            ->when($search, function($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->whereDoesntHave('auctionPlayers', function($query) use ($auction) {
                $query->where('auction_id', $auction->id);
            })
            ->with('playerProfile')
            ->paginate(10);

        $pool = \App\Models\AuctionPlayer::where('auction_id', $auction->id)->with('player.playerProfile')->get();

        return view('auctions.pool', compact('auction', 'players', 'pool', 'search'));
    }

    public function store(Request $request, \App\Models\Auction $auction)
    {
        if ($auction->created_by !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'player_id' => 'required|exists:users,id',
            'base_price' => 'required|numeric|min:0'
        ]);

        $maxAllowed = $auction->total_teams * $auction->max_players;
        $currentCount = \App\Models\AuctionPlayer::where('auction_id', $auction->id)->count();

        if ($currentCount >= $maxAllowed) {
            return back()->withErrors(['player_id' => 'Max players limit reached for this auction.']);
        }

        \App\Models\AuctionPlayer::firstOrCreate([
            'auction_id' => $auction->id,
            'player_id' => $request->player_id
        ], [
            'base_price' => $request->base_price,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Player added to the pool.');
    }

    public function search(\App\Models\Auction $auction, Request $request)
    {
        if ($auction->created_by !== auth()->id()) {
            abort(403);
        }

        $search = $request->get('search', '');
        
        $players = \App\Models\User::where('role', 'player')
            ->when($search, function($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->whereDoesntHave('auctionPlayers', function($query) use ($auction) {
                $query->where('auction_id', $auction->id);
            })
            ->with('playerProfile')
            ->limit(10)
            ->get();

        $html = '';
        foreach ($players as $player) {
            $html .= view('partials.player-card', compact('player', 'auction'))->render();
        }

        if ($players->isEmpty()) {
            $html = '<div class="text-center py-8">
                        <div class="text-gray-400 text-lg">🔍 No available players found.</div>
                        <div class="text-gray-500 text-sm mt-2">Try adjusting your search criteria</div>
                    </div>';
        }

        return response()->json([
            'html' => $html,
            'count' => $players->count()
        ]);
    }
}
