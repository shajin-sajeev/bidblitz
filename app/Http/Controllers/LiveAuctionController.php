<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LiveAuctionController extends Controller
{
    public function index(\App\Models\Auction $auction)
    {
        return view('auctions.live', compact('auction'));
    }

    public function start(\App\Models\Auction $auction)
    {
        if ($auction->created_by !== auth()->id()) abort(403);
        $auction->update(['status' => 'live']);
        broadcast(new \App\Events\AuctionStarted($auction->id))->toOthers();
        return response()->json(['success' => true]);
    }

    public function spin(\App\Models\Auction $auction)
    {
        if ($auction->created_by !== auth()->id()) abort(403);

        $pendingPlayer = \App\Models\AuctionPlayer::where('auction_id', $auction->id)
                                      ->where('status', 'pending')
                                      ->inRandomOrder()
                                      ->first();

        if (!$pendingPlayer) {
            return response()->json(['message' => 'No players left'], 404);
        }

        $playerData = [
            'id' => $pendingPlayer->player_id,
            'auction_player_id' => $pendingPlayer->id,
            'name' => $pendingPlayer->player->name ?? $pendingPlayer->player->username,
            'role' => $pendingPlayer->player->playerProfile->player_role ?? 'Unknown',
            'base_price' => $pendingPlayer->base_price,
            'matches' => $pendingPlayer->player->playerProfile->matches ?? 0,
            'runs' => $pendingPlayer->player->playerProfile->runs ?? 0,
            'wickets' => $pendingPlayer->player->playerProfile->wickets ?? 0,
        ];

        \Illuminate\Support\Facades\Redis::set("auction:{$auction->id}:current_player", json_encode($playerData));
        \Illuminate\Support\Facades\Redis::del("auction:{$auction->id}:highest_bid");

        broadcast(new \App\Events\PlayerSelected($auction->id, $playerData))->toOthers();

        return response()->json($playerData);
    }

    public function bid(Request $request, \App\Models\Auction $auction)
    {
        $request->validate(['amount' => 'required|numeric']);
        $user = auth()->user();

        // Check if user is owner of a team in this auction
        // Using team_owners table or team.owner_id
        $team = \App\Models\Team::where('auction_id', $auction->id)
            ->where(function($q) use ($user) {
                $q->where('owner_id', $user->id)
                  ->orWhereHas('teamOwners', function($q2) use ($user) {
                      $q2->where('user_id', $user->id);
                  });
            })->first();

        if (!$team) {
            return response()->json(['error' => 'You do not own a team in this auction.'], 403);
        }

        // Budget calculations
        $spent = \App\Models\Bid::where('auction_id', $auction->id)->where('team_id', $team->id)->sum('amount');
        $remainingBudget = $auction->budget - $spent;

        $playersBought = \App\Models\AuctionResult::where('auction_id', $auction->id)->where('team_id', $team->id)->count();
        $playersNeeded = max(0, $auction->min_players - $playersBought);

        $lowestBasePrice = \App\Models\AuctionPlayer::where('auction_id', $auction->id)
                                        ->where('status', 'pending')
                                        ->min('base_price') ?? 0;
        
        $maxBid = $remainingBudget;
        if ($playersNeeded > 1) {
            $maxBid = $remainingBudget - (($playersNeeded - 1) * $lowestBasePrice);
        }

        if ($request->amount > $maxBid) {
            return response()->json(['error' => 'Bid exceeds maximum allowed budget constraint.'], 400);
        }

        $currentBidData = \Illuminate\Support\Facades\Redis::get("auction:{$auction->id}:highest_bid");
        $currentAmount = $currentBidData ? json_decode($currentBidData)->amount : 0;
        
        if ($request->amount <= $currentAmount) {
            return response()->json(['error' => 'Bid must be higher than current highest bid.'], 400);
        }

        $currentPlayerData = json_decode(\Illuminate\Support\Facades\Redis::get("auction:{$auction->id}:current_player"));
        if (!$currentPlayerData) {
            return response()->json(['error' => 'No active player.'], 400);
        }

        if ($request->amount < $currentPlayerData->base_price) {
            return response()->json(['error' => 'Bid must be at least the base price.'], 400);
        }

        $bidData = [
            'team_id' => $team->id,
            'team_name' => $team->name,
            'amount' => $request->amount,
            'player_id' => $currentPlayerData->id,
            'timestamp' => now()->timestamp
        ];

        \Illuminate\Support\Facades\Redis::set("auction:{$auction->id}:highest_bid", json_encode($bidData));

        \App\Models\Bid::create([
            'auction_id' => $auction->id,
            'player_id' => $currentPlayerData->id,
            'team_id' => $team->id,
            'amount' => $request->amount
        ]);

        broadcast(new \App\Events\BidPlaced($auction->id, $bidData))->toOthers();

        return response()->json($bidData);
    }

    public function sell(\App\Models\Auction $auction)
    {
        if ($auction->created_by !== auth()->id()) abort(403);

        $currentBidData = json_decode(\Illuminate\Support\Facades\Redis::get("auction:{$auction->id}:highest_bid"));
        $currentPlayerData = json_decode(\Illuminate\Support\Facades\Redis::get("auction:{$auction->id}:current_player"));

        if (!$currentPlayerData || !$currentBidData) {
            return response()->json(['error' => 'No valid bid to sell.'], 400);
        }

        \App\Models\AuctionResult::create([
            'auction_id' => $auction->id,
            'team_id' => $currentBidData->team_id,
            'player_id' => $currentPlayerData->id,
            'price' => $currentBidData->amount
        ]);

        \App\Models\AuctionPlayer::where('id', $currentPlayerData->auction_player_id)
            ->update([
                'status' => 'sold',
                'sold_price' => $currentBidData->amount,
                'team_id' => $currentBidData->team_id
            ]);

        \Illuminate\Support\Facades\Redis::del("auction:{$auction->id}:current_player");
        \Illuminate\Support\Facades\Redis::del("auction:{$auction->id}:highest_bid");

        $msg = "{$currentPlayerData->name} SOLD to {$currentBidData->team_name} for ₹{$currentBidData->amount}";
        broadcast(new \App\Events\PlayerSold($auction->id, $msg))->toOthers();

        return response()->json(['success' => true, 'message' => $msg]);
    }

    public function unsold(\App\Models\Auction $auction)
    {
        if ($auction->created_by !== auth()->id()) abort(403);

        $currentPlayerData = json_decode(\Illuminate\Support\Facades\Redis::get("auction:{$auction->id}:current_player"));
        if (!$currentPlayerData) {
            return response()->json(['error' => 'No active player.'], 400);
        }

        \App\Models\AuctionPlayer::where('id', $currentPlayerData->auction_player_id)
            ->update(['status' => 'unsold']);

        \Illuminate\Support\Facades\Redis::del("auction:{$auction->id}:current_player");
        \Illuminate\Support\Facades\Redis::del("auction:{$auction->id}:highest_bid");

        $msg = "{$currentPlayerData->name} went UNSOLD.";
        broadcast(new \App\Events\PlayerUnsold($auction->id, $msg))->toOthers();

        return response()->json(['success' => true, 'message' => $msg]);
    }
}
