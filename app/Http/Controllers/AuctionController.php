<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\Team;
use App\Models\AuctionParticipant;
use Illuminate\Http\Request;

class AuctionController extends Controller
{
    public function create()
    {
        return view('auctions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sport' => 'required|string|max:50',
            'min_players' => 'required|integer|min:1',
            'max_players' => 'required|integer|min:1|gte:min_players',
            'total_teams' => 'required|integer|min:2',
            'budget' => 'required|numeric|min:0',
            'min_amount' => 'required|numeric|min:0',
        ]);

        $maxBasePrice = (float) $request->budget / (int) $request->max_players;
        if ((float) $request->min_amount > $maxBasePrice) {
            return back()
                ->withInput()
                ->withErrors([
                    'min_amount' => "Minimum amount is too high for this budget. With {$request->max_players} players and a team budget of Rs. {$request->budget}, the maximum affordable base price is Rs. " . number_format($maxBasePrice, 2) . '.',
                ]);
        }

        $currentUserId = auth()->id();
        
        $auction = Auction::create([
            'name' => $request->name,
            'sport' => $request->sport,
            'min_players' => $request->min_players,
            'max_players' => $request->max_players,
            'total_teams' => $request->total_teams,
            'budget' => $request->budget,
            'min_amount' => $request->min_amount,
            'auction_pass' => strtoupper(\Illuminate\Support\Str::random(6)),
            'status' => 'pending',
            'created_by' => $currentUserId,
        ]);

        // Create the teams
        for ($i = 1; $i <= $request->total_teams; $i++) {
            Team::create([
                'auction_id' => $auction->id,
                'name' => "Team $i",
                'team_pass' => strtoupper(\Illuminate\Support\Str::random(6)),
            ]);
        }

        // Add the auction creator as a participant
        AuctionParticipant::create([
            'auction_id' => $auction->id,
            'user_id' => $currentUserId,
            'role' => 'creator',
            'joined_at' => now()
        ]);

        return redirect()->route('auctions.pool', $auction)->with('success', 'Auction created successfully! Now, add players to your auction pool.');
    }
}
