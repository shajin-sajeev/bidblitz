<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Auction;
use App\Models\Team;
use App\Models\TeamOwner;
use App\Models\AuctionParticipant;

class JoinAuctionController extends Controller
{
    public function create()
    {
        return view('auctions.join');
    }

    public function store(Request $request)
    {
        $request->validate([
            'passcode' => 'required|string|min:4'
        ]);

        $passcode = $request->passcode;
        $currentUserId = auth()->id();

        $auction = Auction::where('auction_pass', $passcode)->first();
        if ($auction) {
            // Check if user is already a participant
            $existingParticipant = AuctionParticipant::where('auction_id', $auction->id)
                ->where('user_id', $currentUserId)
                ->first();
            
            if ($existingParticipant) {
                return back()->withErrors(['passcode' => 'You have already joined this auction.']);
            }

            // Find the next available team in this auction
            $team = $auction->teams()->whereNull('owner_id')->first();
            if (!$team) {
                return back()->withErrors(['passcode' => 'All teams for this auction have already been claimed.']);
            }

            // Update team ownership
            $team->update(['owner_id' => $currentUserId]);
            
            // Create team owner record
            TeamOwner::firstOrCreate([
                'team_id' => $team->id,
                'user_id' => $currentUserId
            ]);

            // Create auction participant record
            AuctionParticipant::create([
                'auction_id' => $auction->id,
                'user_id' => $currentUserId,
                'team_id' => $team->id,
                'role' => 'participant',
                'joined_at' => now()
            ]);

            return redirect()->route('auctions.live', $auction)->with('success', "You have successfully joined the auction as {$team->name}.");
        }

        $team = Team::where('team_pass', $passcode)->first();
        if ($team) {
            if ($team->owner_id && $team->owner_id !== $currentUserId) {
                return back()->withErrors(['passcode' => 'This team has already been claimed by another owner.']);
            }

            // Check if user is already a participant
            $existingParticipant = AuctionParticipant::where('auction_id', $team->auction_id)
                ->where('user_id', $currentUserId)
                ->first();
            
            if ($existingParticipant) {
                return back()->withErrors(['passcode' => 'You have already joined this auction.']);
            }

            // Update team ownership
            $team->update(['owner_id' => $currentUserId]);
            
            // Create team owner record
            TeamOwner::firstOrCreate([
                'team_id' => $team->id,
                'user_id' => $currentUserId
            ]);

            // Create auction participant record
            AuctionParticipant::create([
                'auction_id' => $team->auction_id,
                'user_id' => $currentUserId,
                'team_id' => $team->id,
                'role' => 'participant',
                'joined_at' => now()
            ]);

            // Redirect to the live auction page for the corresponding auction
            $auction = $team->auction;
            return redirect()->route('auctions.live', $auction)->with('success', "You have successfully joined the auction as {$team->name}.");
        }

        return back()->withErrors(['passcode' => 'Invalid passcode. No auction or team found.']);
    }
}
