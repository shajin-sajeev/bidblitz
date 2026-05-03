<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

        $auction = \App\Models\Auction::where('auction_pass', $passcode)->first();
        if ($auction) {
            // Find the next available team in this auction
            $team = $auction->teams()->whereNull('owner_id')->first();
            if (!$team) {
                return back()->withErrors(['passcode' => 'All teams for this auction have already been claimed.']);
            }

            $team->update(['owner_id' => auth()->id()]);
            \App\Models\TeamOwner::firstOrCreate([
                'team_id' => $team->id,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('auctions.live', $auction)->with('success', "You have successfully joined the auction as {$team->name}.");
        }

        $team = \App\Models\Team::where('team_pass', $passcode)->first();
        if ($team) {
            if ($team->owner_id && $team->owner_id !== auth()->id()) {
                return back()->withErrors(['passcode' => 'This team has already been claimed by another owner.']);
            }

            $team->update(['owner_id' => auth()->id()]);
            \App\Models\TeamOwner::firstOrCreate([
                'team_id' => $team->id,
                'user_id' => auth()->id()
            ]);

            // Redirect to the live auction page for the corresponding auction
            $auction = $team->auction;
            return redirect()->route('auctions.live', $auction)->with('success', "You have successfully joined the auction as {$team->name}.");
        }

        return back()->withErrors(['passcode' => 'Invalid passcode. No auction or team found.']);
    }
}
