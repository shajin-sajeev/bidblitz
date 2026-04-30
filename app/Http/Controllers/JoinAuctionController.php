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
            return redirect()->route('auctions.live', $auction)->with('success', 'Joined auction as a viewer/player.');
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

            return redirect()->route('dashboard')->with('success', "You have successfully claimed ownership of {$team->name}.");
        }

        return back()->withErrors(['passcode' => 'Invalid passcode. No auction or team found.']);
    }
}
