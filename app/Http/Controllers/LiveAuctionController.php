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

class LiveAuctionController extends Controller
{
    public function index(Auction $auction)
    {
        $isOwner = $auction->created_by === auth()->id();

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

        return view('auctions.live', compact(
            'auction',
            'isOwner',
            'auctionPlayers',
            'poolPlayers',
            'pendingPlayers',
            'teamSummaries',
            'currentPlayer',
            'highestBid'
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

        $pendingPlayer = AuctionPlayer::where('auction_id', $auction->id)
            ->where('status', 'pending')
            ->with('player')
            ->inRandomOrder()
            ->first();

        if (!$pendingPlayer || !$pendingPlayer->player) {
            return response()->json(['message' => 'No players left to spin.'], 404);
        }

        $player = $pendingPlayer->player;
        $playerData = [
            'id' => $player->id,
            'auction_player_id' => $pendingPlayer->id,
            'name' => $player->name,
            'role' => $player->specialization ?? 'All-rounder',
            'base_price' => (float) $pendingPlayer->base_price,
            'experience_years' => $player->experience_years ?? 0,
            'avatar' => $player->avatar,
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

        $spent = (float) AuctionPlayer::where('auction_id', $auction->id)
            ->where('team_id', $team->id)
            ->where('status', 'sold')
            ->sum('sold_price');

        if ($spent + $price > (float) $auction->budget) {
            return back()->withErrors(['price' => 'This sale exceeds the selected team budget.']);
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
            return response()->json(['error' => 'Bid exceeds maximum allowed budget constraint.'], 400);
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

        Cache::forget($this->currentPlayerKey($auction));
        Cache::forget($this->highestBidKey($auction));

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

        Cache::forget($this->currentPlayerKey($auction));
        Cache::forget($this->highestBidKey($auction));

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

        if ($soldCount >= $auction->max_players) {
            return 0;
        }

        $spent = (float) AuctionPlayer::where('auction_id', $auction->id)
            ->where('team_id', $team->id)
            ->where('status', 'sold')
            ->sum('sold_price');

        $remaining = max(0, (float) $auction->budget - $spent);
        $playersStillNeededAfterNext = max(0, $auction->min_players - $soldCount - 1);
        $lowestBasePrice = (float) (AuctionPlayer::where('auction_id', $auction->id)
            ->where('status', 'pending')
            ->min('base_price') ?? 0);

        return max(0, $remaining - ($playersStillNeededAfterNext * $lowestBasePrice));
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
