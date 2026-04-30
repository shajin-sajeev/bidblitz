<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Auction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PlayerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $players = Player::active()
            ->search($search)
            ->orderBy('name')
            ->paginate(10);

        return response()->json([
            'players' => $players,
            'search' => $search,
        ]);
    }

    public function create()
    {
        return view('players.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:players,email',
            'phone' => 'nullable|string|max:20',
            'specialization' => 'required|string|max:255',
            'experience_years' => 'required|integer|min:0|max:50',
            'base_price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        
        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
            $avatar->move(public_path('avatars'), $avatarName);
            $data['avatar'] = $avatarName;
        }

        $player = Player::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Player created successfully',
            'player' => $player
        ]);
    }

    public function show(Player $player)
    {
        return view('players.show', compact('player'));
    }

    public function edit(Player $player)
    {
        return view('players.edit', compact('player'));
    }

    public function update(Request $request, Player $player)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:players,email,' . $player->id,
            'phone' => 'nullable|string|max:20',
            'specialization' => 'required|string|max:255',
            'experience_years' => 'required|integer|min:0|max:50',
            'base_price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        
        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($player->avatar) {
                $oldAvatarPath = public_path('avatars/' . $player->avatar);
                if (file_exists($oldAvatarPath)) {
                    unlink($oldAvatarPath);
                }
            }
            
            $avatar = $request->file('avatar');
            $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
            $avatar->move(public_path('avatars'), $avatarName);
            $data['avatar'] = $avatarName;
        }

        $player->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Player updated successfully',
            'player' => $player
        ]);
    }

    public function destroy(Player $player)
    {
        // Delete avatar if exists
        if ($player->avatar) {
            $avatarPath = public_path('avatars/' . $player->avatar);
            if (file_exists($avatarPath)) {
                unlink($avatarPath);
            }
        }

        $player->delete();

        return response()->json([
            'success' => true,
            'message' => 'Player deleted successfully'
        ]);
    }

    public function search(Request $request)
    {
        $search = $request->get('q', '');
        $specialization = $request->get('specialization', 'all');
        $page = $request->get('page', 1);
        
        $query = Player::active();
        
        if ($search) {
            $query->search($search);
        }
        
        if ($specialization !== 'all') {
            $query->where('specialization', $specialization);
        }
        
        $players = $query->orderBy('name')
            ->paginate(10, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'players' => $players,
            'search' => $search,
            'specialization' => $specialization,
        ]);
    }

    public function addToAuction(Request $request, Auction $auction)
    {
        $validator = Validator::make($request->all(), [
            'player_ids' => 'required|array',
            'player_ids.*' => 'exists:players,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $playerIds = $request->input('player_ids');
        $addedCount = 0;

        foreach ($playerIds as $playerId) {
            // Check if player is already in this auction
            $existing = $auction->players()->where('player_id', $playerId)->first();
            
            if (!$existing) {
                $auction->players()->attach($playerId, [
                    'status' => 'available',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $addedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Added {$addedCount} players to auction",
            'added_count' => $addedCount
        ]);
    }

    public function showAuctionPlayers(Auction $auction)
    {
        return view('players.auction_players_v2', compact('auction'));
    }

    public function removeFromAuction(Request $request, Auction $auction, Player $player)
    {
        $auction->players()->detach($player->id);

        return response()->json([
            'success' => true,
            'message' => 'Player removed from auction'
        ]);
    }
}
