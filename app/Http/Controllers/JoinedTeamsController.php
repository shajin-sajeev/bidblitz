<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Team;
use App\Models\Auction;
use App\Models\AuctionPlayer;
use App\Models\AuctionParticipant;

class JoinedTeamsController extends Controller
{
    public function index()
    {
        $userTeams = Team::with(['auction.creator', 'players', 'owner'])
            ->where('owner_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        $participatedAuctions = AuctionParticipant::with(['auction.creator', 'auction.teams'])
            ->where('user_id', Auth::id())
            ->where('role', 'participant')
            ->get()
            ->pluck('auction')
            ->unique('id');

        return view('teams.joined', compact('userTeams', 'participatedAuctions'));
    }

    public function show(Team $team)
    {
        if (!Auth::check() || $team->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $team->load(['auction.creator', 'auction.teams', 'players.player', 'owner', 'auction.players']);

        $totalSpent = $team->players()->sum('sold_price') ?? 0;
        $remainingBudget = $team->auction->budget - $totalSpent;
        $playersCount = $team->players()->count();

        return view('teams.show', compact('team', 'totalSpent', 'remainingBudget', 'playersCount'));
    }

    public function auctionTeams(Auction $auction)
    {
        $isParticipant = AuctionParticipant::where('auction_id', $auction->id)
            ->where('user_id', Auth::id())
            ->exists();

        if (!$isParticipant && $auction->created_by !== Auth::id()) {
            abort(403, 'You are not a participant in this auction.');
        }

        $teams = $auction->teams()->with(['owner', 'players'])->get();

        return view('teams.auction-teams', compact('auction', 'teams'));
    }
}
