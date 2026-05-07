<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\AuctionPlayer;
use App\Models\AuctionResult;
use App\Models\Bid;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LiveAuctionController extends Controller
{
    public function index(Auction $auction, Request $request)
    {

        $isOwner = $auction->created_by === auth()->id();
        $user = auth()->user();
        // Check if user is assigned to a team in this auction (either as owner or team member)
        $userTeam = Team::where('auction_id', $auction->id)
            ->where(function ($q) use ($user) {
                $q->where('owner_id', $user->id)
                    ->orWhereHas('teamOwners', function ($q2) use ($user) {
                        $q2->where('user_id', $user->id);
                    });
            })
            ->first();
        // Allow all authenticated users to view, but restrict participation
        $canParticipate = $isOwner || $userTeam;

        // Track viewers (authenticated only)
        $viewersKey = "auction:{$auction->id}:viewers";
        $viewers = \Cache::get($viewersKey, []);
        // Do not count the auction owner as a viewer
        if (!$isOwner) {
            $viewers[$user->id] = [
                'id' => $user->id,
                'name' => $user->name,
                'profile' => $user->profile_photo_url ?? null,
                'viewed_at' => now()->toDateTimeString(),
            ];
            \Cache::put($viewersKey, $viewers, now()->addMinutes(10));
        }
        // Exclude owner from viewer count
        $viewerCount = collect($viewers)->reject(fn($v) => $v['id'] == $auction->created_by)->count();

        if ($isOwner && blank($auction->auction_pass)) {
            $auction->forceFill(['auction_pass' => strtoupper(Str::random(6))])->save();
            $auction->refresh();
        }

        // Handle AJAX polling request for currentPlayer
        if ($request->ajax() && $request->get('ajax') === 'currentPlayer') {
            $currentPlayer = Cache::get($this->currentPlayerKey($auction));
            $highestBid = Cache::get($this->highestBidKey($auction));
            
            // If we have a cached player, verify their actual database status
            if ($currentPlayer && isset($currentPlayer['auction_player_id'])) {
                $auctionPlayer = AuctionPlayer::find($currentPlayer['auction_player_id']);
                
                if ($auctionPlayer) {
                    // Update the cached player data with actual database status
                    $currentPlayer['status'] = $auctionPlayer->status;
                    
                    // Pending players are new to the round; unsold players can return after the first pass.
                    if (!in_array($auctionPlayer->status, ['pending', 'unsold'], true)) {
                        Cache::forget($this->currentPlayerKey($auction));
                        $currentPlayer = null; // Don't return the player
                    }
                    
                    // Update cache with current status if still available for bidding.
                    if (in_array($auctionPlayer->status, ['pending', 'unsold'], true)) {
                        Cache::put($this->currentPlayerKey($auction), $currentPlayer, now()->addHours(6));
                    }
                } else {
                    // Auction player not found in database, clear cache
                    Cache::forget($this->currentPlayerKey($auction));
                    $currentPlayer = null;
                }
            }
            
            // Debug logging
            Log::info('AJAX currentPlayer request with DB verification', [
                'auction_id' => $auction->id,
                'user_id' => auth()->id(),
                'is_owner' => $auction->created_by === auth()->id(),
                'has_team' => $userTeam ? true : false,
                'team_name' => $userTeam ? $userTeam->name : null,
                'currentPlayer' => $currentPlayer,
                'highestBid' => $highestBid,
            ]);
            
            return response()->json([
                'currentPlayer' => $currentPlayer,
                'highestBid' => $highestBid,
                'userTeam' => $userTeam ? [
                    'id' => $userTeam->id,
                    'name' => $userTeam->name,
                    'is_owner' => $userTeam->owner_id === auth()->id()
                ] : null,
            ]);
        }

        $auction->load([
            'teams.ownerPlayer',
            'teams.players.player',
            'auctionPlayers.player',
        ]);

        $auctionPlayers = $auction->auctionPlayers()
            ->with(['player', 'team.ownerPlayer'])
            ->orderByRaw("FIELD(status, 'pending', 'sold', 'unsold')")
            ->orderBy('id')
            ->get();

        $poolPlayers = $auctionPlayers
            ->pluck('player')
            ->filter()
            ->sortBy('name')
            ->values();

        $pendingPlayers = $auctionPlayers->where('status', 'pending')->values();
        $teamSummaries = $this->teamSummaries($auction);
        $currentPlayer = Cache::get($this->currentPlayerKey($auction));
        $highestBid = Cache::get($this->highestBidKey($auction));

        $canStartLive = $auction->status === 'active' && $auction->canStartLive();
        $liveStartProgress = null;
        if ($isOwner && $auction->status === 'active') {
            $liveStartProgress = [
                'registered' => $auction->teams->filter(fn ($t) => $t->owner_player_id !== null)->count(),
                'joined' => $auction->teams->filter(fn ($t) => $t->owner_id !== null)->count(),
                'required' => (int) $auction->total_teams,
            ];
        }

        return view('auctions.live', compact(
            'auction',
            'isOwner',
            'auctionPlayers',
            'poolPlayers',
            'pendingPlayers',
            'teamSummaries',
            'currentPlayer',
            'highestBid',
            'canStartLive',
            'liveStartProgress',
            'canParticipate',
            'viewerCount'
        ));
    }

    public function start(Request $request, Auction $auction)
    {
        if ($auction->created_by !== auth()->id()) {
            abort(403);
        }

        if (!in_array($auction->status, ['active', 'live'], true)) {
            $message = 'Only active auctions can be started.';

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return back()->with('warning', $message);
        }

        $auction->loadMissing('teams');
        if ($auction->status === 'active' && ! $auction->canStartLive()) {
            $registered = $auction->teams->filter(fn ($t) => $t->owner_player_id !== null)->count();
            $joined = $auction->teams->filter(fn ($t) => $t->owner_id !== null)->count();
            $need = (int) $auction->total_teams;
            $message = "You cannot go live yet. Finish Team Setup for all {$need} teams (owner players assigned: {$registered}/{$need}) and ensure each team has joined with their team pass (teams claimed: {$joined}/{$need}).";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return back()->with('warning', $message);
        }

        if ($auction->status !== 'live') {
            $auction->update(['status' => 'live']);
            broadcast(new \App\Events\AuctionStarted($auction->id))->toOthers();
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'redirect_url' => route('auctions.live', $auction),
            ]);
        }

        return redirect()->route('auctions.live', $auction);
    }

    public function saveTeams(Request $request, Auction $auction)
    {
        if ($auction->created_by !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'teams' => 'required|array',
            'teams.*.id' => 'required|exists:teams,id',
            'teams.*.name' => 'required|string|max:255',
            'teams.*.owner_player_id' => 'nullable|exists:players,id',
        ]);

        foreach ($validated['teams'] as $teamData) {
            $team = Team::where('auction_id', $auction->id)->findOrFail($teamData['id']);
            $team->update([
                'name' => $teamData['name'],
                'owner_player_id' => $teamData['owner_player_id'] ?: null,
            ]);
        }

        return redirect()
            ->route('auctions.live', $auction)
            ->with('warning', 'Team setup updated.');
    }

    public function spin(Auction $auction)
    {
        if ($auction->created_by !== auth()->id()) {
            abort(403);
        }

        if ($auction->status !== 'live') {
            return response()->json(['message' => 'Start the auction before spinning players.'], 422);
        }

        $auctionPlayer = AuctionPlayer::where('auction_id', $auction->id)
            ->where('status', 'pending')
            ->with('player')
            ->inRandomOrder()
            ->first();

        if (!$auctionPlayer) {
            $auctionPlayer = AuctionPlayer::where('auction_id', $auction->id)
                ->where('status', 'unsold')
                ->with('player')
                ->inRandomOrder()
                ->first();
        }

        if (!$auctionPlayer || !$auctionPlayer->player) {
            return response()->json(['message' => 'No more players to sell.'], 404);
        }

        $player = $auctionPlayer->player;
        $playerData = [
            'id' => $player->id,
            'auction_player_id' => $auctionPlayer->id,
            'name' => $player->name,
            'role' => $player->specialization ?? 'All-rounder',
            'base_price' => (float) $auctionPlayer->base_price,
            'avatar' => $player->avatar,
            'status' => $auctionPlayer->status,
        ];

        Cache::put($this->currentPlayerKey($auction), $playerData, now()->addHours(6));
        Cache::forget($this->highestBidKey($auction));

        broadcast(new \App\Events\PlayerSelected($auction->id, $playerData))->toOthers();

        return response()->json($playerData);
    }

    public function assignPlayer(Request $request, Auction $auction)
    {
        if ($auction->created_by !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'auction_player_id' => 'required|exists:auction_players,id',
            'team_id' => 'required|exists:teams,id',
            'price' => 'required|numeric|min:0',
        ]);

        $auctionPlayer = AuctionPlayer::where('auction_id', $auction->id)
            ->with('player')
            ->findOrFail($validated['auction_player_id']);

        $team = Team::where('auction_id', $auction->id)->findOrFail($validated['team_id']);
        $price = (float) $validated['price'];

        if ($auction->status !== 'live') {
            return back()->with('warning', 'Start the auction before assigning players.');
        }

        if ($auctionPlayer->status === 'sold') {
            return back()->withErrors(['auction_player_id' => 'This player is already sold.']);
        }

        if ($price < (float) $auctionPlayer->base_price) {
            return back()->withErrors(['price' => 'Sold price cannot be lower than the player base price.']);
        }

        $teamPlayerCount = AuctionPlayer::where('auction_id', $auction->id)
            ->where('team_id', $team->id)
            ->where('status', 'sold')
            ->count();

        if ($teamPlayerCount >= $auction->max_players) {
            return back()->withErrors(['team_id' => 'This team already has the maximum number of players.']);
        }

        $maxBid = $this->maxBidForTeam($auction, $team);
        if ($price > $maxBid) {
            return back()->withErrors(['price' => "This sale exceeds the selected team's maximum bid amount of Rs. {$maxBid}."]);
        }

        DB::transaction(function () use ($auction, $auctionPlayer, $team, $price) {
            $auctionPlayer->update([
                'status' => 'sold',
                'sold_price' => $price,
                'team_id' => $team->id,
            ]);

            AuctionResult::updateOrCreate(
                [
                    'auction_id' => $auction->id,
                    'player_id' => $auctionPlayer->player_id,
                ],
                [
                    'team_id' => $team->id,
                    'price' => $price,
                ]
            );
        });

        Cache::forget($this->currentPlayerKey($auction));
        Cache::forget($this->highestBidKey($auction));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Player assigned.',
                'team_summaries' => $this->teamSummaries($auction),
            ]);
        }

        return redirect()->route('auctions.live', $auction);
    }

    public function bid(Request $request, Auction $auction)
    {
        $request->validate(['amount' => 'required|numeric']);
        $user = auth()->user();

        $team = Team::where('auction_id', $auction->id)
            ->where(function ($q) use ($user) {
                $q->where('owner_id', $user->id)
                    ->orWhereHas('teamOwners', function ($q2) use ($user) {
                        $q2->where('user_id', $user->id);
                    });
            })
            ->first();

        if (!$team) {
            return response()->json(['error' => 'You do not own a team in this auction.'], 403);
        }

        $maxBid = $this->maxBidForTeam($auction, $team);

        if ((float) $request->amount > $maxBid) {
            return response()->json(['error' => "Bid exceeds maximum allowed amount for your team. Maximum bid: Rs. {$maxBid}."], 400);
        }

        $currentBidData = Cache::get($this->highestBidKey($auction));
        $currentAmount = $currentBidData['amount'] ?? 0;

        if ((float) $request->amount <= (float) $currentAmount) {
            return response()->json(['error' => 'Bid must be higher than current highest bid.'], 400);
        }

        $currentPlayerData = Cache::get($this->currentPlayerKey($auction));
        if (!$currentPlayerData) {
            return response()->json(['error' => 'No active player.'], 400);
        }

        if ((float) $request->amount < (float) $currentPlayerData['base_price']) {
            return response()->json(['error' => 'Bid must be at least the base price.'], 400);
        }

        $bidData = [
            'team_id' => $team->id,
            'team_name' => $team->name,
            'amount' => (float) $request->amount,
            'player_id' => $currentPlayerData['id'],
            'timestamp' => now()->timestamp,
        ];

        Cache::put($this->highestBidKey($auction), $bidData, now()->addHours(6));

        Bid::create([
            'auction_id' => $auction->id,
            'player_id' => $currentPlayerData['id'],
            'team_id' => $team->id,
            'amount' => $request->amount,
        ]);

        broadcast(new \App\Events\BidPlaced($auction->id, $bidData))->toOthers();

        return response()->json($bidData);
    }

    public function sell(Auction $auction)
    {
        if ($auction->created_by !== auth()->id()) {
            abort(403);
        }

        $currentBidData = Cache::get($this->highestBidKey($auction));
        $currentPlayerData = Cache::get($this->currentPlayerKey($auction));

        if (!$currentPlayerData || !$currentBidData) {
            return response()->json(['error' => 'No valid bid to sell.'], 400);
        }

        $auctionPlayer = AuctionPlayer::findOrFail($currentPlayerData['auction_player_id']);
        $team = Team::findOrFail($currentBidData['team_id']);

        $basePrice = (float) $currentPlayerData['base_price'];
        $bidAmount = (float) $currentBidData['amount'];

        if ($bidAmount < $basePrice) {
            return response()->json([
                'error' => "Cannot sell player. Bid amount (Rs. {$bidAmount}) is less than the base price (Rs. {$basePrice})."
            ], 400);
        }

        $teamPlayerCount = AuctionPlayer::where('auction_id', $auction->id)
            ->where('team_id', $team->id)
            ->where('status', 'sold')
            ->count();

        if ($teamPlayerCount >= $auction->max_players) {
            return response()->json(['error' => 'This team already has the maximum number of players.'], 400);
        }

        $maxBid = $this->maxBidForTeam($auction, $team);
        if ($bidAmount > $maxBid) {
            return response()->json(['error' => "Cannot sell player. Bid exceeds this team's maximum allowed amount of Rs. {$maxBid}."], 400);
        }

        $auctionPlayer->update([
            'status' => 'sold',
            'sold_price' => $currentBidData['amount'],
            'team_id' => $team->id,
        ]);

        AuctionResult::updateOrCreate(
            [
                'auction_id' => $auction->id,
                'player_id' => $auctionPlayer->player_id,
            ],
            [
                'team_id' => $team->id,
                'price' => $currentBidData['amount'],
            ]
        );

        // Update cached player data with sold status and clear after delay
        $updatedPlayerData = $currentPlayerData;
        $updatedPlayerData['status'] = 'sold';
        $updatedPlayerData['sold_price'] = $currentBidData['amount'];
        $updatedPlayerData['team_name'] = $team->name;
        Cache::put($this->currentPlayerKey($auction), $updatedPlayerData, now()->addMinutes(2));
        
        // Clear highest bid immediately
        Cache::forget($this->highestBidKey($auction));
        
        // Clear current player cache after 10 seconds to allow polling to detect change
        dispatch(function () use ($auction) {
            sleep(10);
            Cache::forget($this->currentPlayerKey($auction));
        })->afterResponse();

        $msg = "{$currentPlayerData['name']} SOLD to {$team->name} for Rs. {$currentBidData['amount']}";

        broadcast(new \App\Events\PlayerSold($auction->id, $msg))->toOthers();

        return response()->json(['success' => true, 'message' => $msg]);
    }

    public function unsold(Auction $auction)
    {
        if ($auction->created_by !== auth()->id()) {
            abort(403);
        }

        $currentPlayerData = Cache::get($this->currentPlayerKey($auction));
        if (!$currentPlayerData) {
            return response()->json(['error' => 'No active player.'], 400);
        }

        AuctionPlayer::where('id', $currentPlayerData['auction_player_id'])
            ->where('auction_id', $auction->id)
            ->update(['status' => 'unsold']);

        // Update cached player data with unsold status and clear after delay
        $updatedPlayerData = $currentPlayerData;
        $updatedPlayerData['status'] = 'unsold';
        Cache::put($this->currentPlayerKey($auction), $updatedPlayerData, now()->addMinutes(2));
        
        // Clear highest bid immediately
        Cache::forget($this->highestBidKey($auction));
        
        // Clear current player cache after 10 seconds to allow polling to detect change
        dispatch(function () use ($auction) {
            sleep(10);
            Cache::forget($this->currentPlayerKey($auction));
        })->afterResponse();

        $msg = "{$currentPlayerData['name']} went UNSOLD.";
        broadcast(new \App\Events\PlayerUnsold($auction->id, $msg))->toOthers();

        return response()->json(['success' => true, 'message' => $msg]);
    }

    private function teamSummaries(Auction $auction)
    {
        return Team::where('auction_id', $auction->id)
            ->with(['ownerPlayer', 'players.player'])
            ->get()
            ->map(function (Team $team) use ($auction) {
                $soldPlayers = $team->players->where('status', 'sold')->values();
                $spent = (float) $soldPlayers->sum('sold_price');
                $remaining = max(0, (float) $auction->budget - $spent);

                return [
                    'id' => $team->id,
                    'name' => $team->name,
                    'owner' => $team->ownerPlayer?->name ?? 'Owner not assigned',
                    'spent' => $spent,
                    'remaining' => $remaining,
                    'players_count' => $soldPlayers->count(),
                    'max_bid' => $this->maxBidForTeam($auction, $team),
                    'players' => $soldPlayers->map(fn (AuctionPlayer $item) => [
                        'name' => $item->player?->name ?? 'Unknown Player',
                        'price' => (float) $item->sold_price,
                    ])->values(),
                ];
            })
            ->values();
    }

    private function maxBidForTeam(Auction $auction, Team $team): float
    {
        $soldCount = AuctionPlayer::where('auction_id', $auction->id)
            ->where('team_id', $team->id)
            ->where('status', 'sold')
            ->count();

        $spent = (float) AuctionPlayer::where('auction_id', $auction->id)
            ->where('team_id', $team->id)
            ->where('status', 'sold')
            ->sum('sold_price');

        if ($soldCount >= $auction->max_players) {
            return 0;
        }

        $remaining = max(0, (float) $auction->budget - $spent);
        $playersStillNeededAfterNext = max(0, $auction->max_players - $soldCount - 1);
        $reservedForRequiredPlayers = $playersStillNeededAfterNext * (float) $auction->min_amount;

        return max(0, $remaining - $reservedForRequiredPlayers);
    }

    private function currentPlayerKey(Auction $auction): string
    {
        return "auction:{$auction->id}:current_player";
    }

    private function highestBidKey(Auction $auction): string
    {
        return "auction:{$auction->id}:highest_bid";
    }
}
