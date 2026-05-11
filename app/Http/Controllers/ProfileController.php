<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\AuctionParticipant;
use App\Models\AuctionPlayer;
use App\Models\Team;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function create()
    {
        return view('profile.create');
    }

    public function show()
    {
        $user = auth()->user();
        $user->load('playerProfile');
        
        // Get user statistics
        $stats = [
            'teams_owned' => Team::where('owner_id', $user->id)->count(),
            'auctions_created' => Auction::where('created_by', $user->id)->count(),
            'teams_joined' => AuctionParticipant::where('user_id', $user->id)->count(),
            'total_players' => AuctionPlayer::whereHas('team.owner', function($query) use ($user) {
                $query->where('id', $user->id);
            })->count()
        ];
        
        return view('profile.show', compact('user', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . auth()->id() . '|max:255',
            'player_role' => 'required|in:Batsman,Bowler,All-rounder,Wicket-keeper',
        ]);

        $user = auth()->user();
        $user->name = $request->name;
        $user->username = $request->username;
        // Default everyone to 'player' role internally to keep DB happy
        $user->role = 'player';
        $user->save();

        \App\Models\PlayerProfile::updateOrCreate(
            ['user_id' => $user->id],
            ['player_role' => $request->player_role]
        );

        return redirect()->route('dashboard')->with('success', 'Profile updated successfully!');
    }
}
