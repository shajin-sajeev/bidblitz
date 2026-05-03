<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PlayerPoolController extends Controller
{
    public function index(\App\Models\Auction $auction, Request $request)
    {
        if ($auction->created_by !== auth()->id()) {
            abort(403);
        }

        $search = $request->get('search');
        
        $players = \App\Models\Player::active()
            ->when($search, function($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('unique_username', 'like', "%{$search}%")
                      ->orWhere('specialization', 'like', "%{$search}%");
                });
            })
            ->with(['auctionPlayers' => function($query) use ($auction) {
                $query->where('auction_id', $auction->id);
            }])
            ->paginate(10);

        $pool = \App\Models\AuctionPlayer::where('auction_id', $auction->id)
            ->with('player')
            ->paginate(10, ['*'], 'pool_page');

        $activeTab = $request->get('tab', 'available');
        
        return view('auctions.pool', compact('auction', 'players', 'pool', 'search', 'activeTab'));
    }

    public function store(Request $request, \App\Models\Auction $auction)
    {
        if ($auction->created_by !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'player_id' => 'required|exists:players,id',
            'base_price' => 'required|numeric|min:1'
        ]);

        $maxAllowed = $auction->total_teams * $auction->max_players;
        $currentCount = \App\Models\AuctionPlayer::where('auction_id', $auction->id)->count();

        if ($currentCount >= $maxAllowed) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Max players limit reached for this auction.'
                ], 422);
            }
            return back()->withErrors(['player_id' => 'Max players limit reached for this auction.']);
        }

        // Check if player is already in the pool
        $existingPlayer = \App\Models\AuctionPlayer::where('auction_id', $auction->id)
            ->where('player_id', $request->player_id)
            ->first();
            
        if ($existingPlayer) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Player is already in the pool.'
                ], 422);
            }
            return back()->withErrors(['player_id' => 'Player is already in the pool.']);
        }

        $auctionPlayer = \App\Models\AuctionPlayer::create([
            'auction_id' => $auction->id,
            'player_id' => $request->player_id,
            'base_price' => $request->base_price,
            'status' => 'pending'
        ]);

        // Always return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            try {
                // Load the player data with relationships
                $auctionPlayer->load('player');
                
                // Generate the pool item HTML
                $poolItemHtml = view('partials.pool-item', [
                    'item' => $auctionPlayer
                ])->render();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Player added to pool successfully.',
                    'pool_item_html' => $poolItemHtml,
                    'pool_count' => \App\Models\AuctionPlayer::where('auction_id', $auction->id)->count(),
                    'player_id' => $auctionPlayer->player_id,
                    'pool_player_id' => $auctionPlayer->id,
                    'remove_url' => route('auctions.pool.remove', [$auction, $auctionPlayer])
                ]);
            } catch (\Exception $e) {
                // Log the error for debugging
                Log::error('Error rendering pool item: ' . $e->getMessage());
                
                // Return a simple HTML fallback
                $player = $auctionPlayer->player;
                $fallbackHtml = '<div class="player-card" style="padding: 1rem;">
                    <div class="flex items-center gap-3">
                        <div class="player-avatar" style="width: 40px; height: 40px; font-size: 0.9rem;">
                            <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                ' . substr($player->name, 0, 2) . '
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="font-semibold">' . $player->name . '</div>
                            <div class="text-sm text-gray-400">' . ($player->unique_username ?: strtolower(str_replace(' ', '', $player->name))) . '</div>
                            <div class="text-sm text-blue-400">' . $player->specialization . '</div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs px-2 py-1 rounded-full inline-block mt-1 bg-yellow-500/20 text-yellow-400">
                                Pending
                            </div>
                        </div>
                    </div>
                </div>';
                
                return response()->json([
                    'success' => true,
                    'message' => 'Player added to pool successfully.',
                    'pool_item_html' => $fallbackHtml,
                    'pool_count' => \App\Models\AuctionPlayer::where('auction_id', $auction->id)->count(),
                    'player_id' => $auctionPlayer->player_id,
                    'pool_player_id' => $auctionPlayer->id,
                    'remove_url' => route('auctions.pool.remove', [$auction, $auctionPlayer])
                ]);
            }
        }

        return back()->with('success', 'Player added to the pool.');
    }

    public function remove(Request $request, \App\Models\Auction $auction, \App\Models\AuctionPlayer $poolPlayer)
    {
        if ($auction->created_by !== auth()->id()) {
            abort(403);
        }

        if ($poolPlayer->auction_id !== $auction->id) {
            abort(404);
        }

        if (in_array($auction->status, ['active', 'live'], true)) {
            $message = 'Players cannot be removed after the auction is active.';

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 422);
            }

            return back()->withErrors(['pool_player' => $message]);
        }

        if ($poolPlayer->team_id || in_array($poolPlayer->status, ['sold'], true)) {
            $message = 'This player is already assigned and cannot be removed from the pool.';

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 422);
            }

            return back()->withErrors(['pool_player' => $message]);
        }

        $playerId = $poolPlayer->player_id;
        $playerName = $poolPlayer->player->name ?? 'Player';
        $poolPlayer->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$playerName} removed from the pool.",
                'player_id' => $playerId,
                'pool_count' => \App\Models\AuctionPlayer::where('auction_id', $auction->id)->count()
            ]);
        }

        return back()->with('success', "{$playerName} removed from the pool.");
    }

    public function search(\App\Models\Auction $auction, Request $request)
    {
        if ($auction->created_by !== auth()->id()) {
            abort(403);
        }

        $search = $request->get('search', '');
        
        $players = \App\Models\Player::active()
            ->when($search, function($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('unique_username', 'like', "%{$search}%")
                      ->orWhere('specialization', 'like', "%{$search}%");
                });
            })
            ->with(['auctionPlayers' => function($query) use ($auction) {
                $query->where('auction_id', $auction->id);
            }])
            ->limit(10)
            ->get();

        $html = '';
        foreach ($players as $player) {
            $html .= view('partials.player-card', compact('player', 'auction'))->render();
        }

        if ($players->isEmpty()) {
            $html = '<div class="text-center py-8">
                        <div class="text-gray-400 text-lg">🔍 No available players found.</div>
                        <div class="text-gray-500 text-sm mt-2">Try adjusting your search criteria</div>
                    </div>';
        }

        return response()->json([
            'html' => $html,
            'count' => $players->count()
        ]);
    }

    public function validatePool(Request $request, \App\Models\Auction $auction)
    {
        if ($auction->created_by !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to validate this auction.'
            ], 403);
        }

        // Get current pool count
        $currentPoolCount = \App\Models\AuctionPlayer::where('auction_id', $auction->id)->count();
        $minRequired = $auction->min_players * $auction->total_teams;
        $maxAllowed = $auction->max_players * $auction->total_teams;

        // Determine validation type and if auction can be created
        $validationType = null;
        $canCreate = false;

        if ($currentPoolCount < $minRequired) {
            $validationType = 'minimum_not_met';
            $canCreate = false;
        } elseif ($currentPoolCount === $minRequired) {
            $validationType = 'exact_minimum_met';
            $canCreate = true;
        } elseif ($currentPoolCount > $minRequired && $currentPoolCount < $maxAllowed) {
            $validationType = 'minimum_met_but_not_maximum';
            $canCreate = false; // NOT allowed - must be exactly minimum or maximum
        } elseif ($currentPoolCount === $maxAllowed) {
            $validationType = 'maximum_met';
            $canCreate = true;
        } elseif ($currentPoolCount > $maxAllowed) {
            $validationType = 'maximum_exceeded';
            $canCreate = false;
        }

        return response()->json([
            'success' => true,
            'currentPoolCount' => $currentPoolCount,
            'minRequired' => $minRequired,
            'maxAllowed' => $maxAllowed,
            'canCreate' => $canCreate,
            'validationType' => $validationType,
            'message' => $this->getValidationMessage($validationType, $currentPoolCount, $minRequired, $maxAllowed)
        ]);
    }

    private function getValidationMessage($validationType, $currentPoolCount, $minRequired, $maxAllowed)
    {
        switch ($validationType) {
            case 'minimum_not_met':
                $needed = $minRequired - $currentPoolCount;
                return "Minimum players not met! You need {$minRequired} players but only have {$currentPoolCount}. Add {$needed} more player" . ($needed > 1 ? 's' : '') . " to create the auction.";
            
            case 'exact_minimum_met':
                return "Perfect! You have exactly the minimum required players ({$currentPoolCount}). You can create the auction now.";
            
            case 'minimum_met_but_not_maximum':
                $neededToReachMax = $maxAllowed - $currentPoolCount;
                return "Minimum requirement met ({$currentPoolCount}/{$minRequired}), but auction creation requires exactly {$minRequired} or {$maxAllowed} players. Add {$neededToReachMax} more player" . ($neededToReachMax > 1 ? 's' : '') . " to reach maximum capacity, or remove " . ($currentPoolCount - $minRequired) . " player" . (($currentPoolCount - $minRequired) > 1 ? 's' : '') . " to reach minimum.";
            
            case 'maximum_met':
                return "Excellent! You have reached maximum capacity ({$currentPoolCount} players). Ready to create auction.";
            
            case 'maximum_exceeded':
                $excess = $currentPoolCount - $maxAllowed;
                return "Maximum players exceeded! You can only have {$maxAllowed} players but have {$currentPoolCount}. Remove {$excess} player" . ($excess > 1 ? 's' : '') . " to create the auction.";
            
            default:
                return "Validation status unknown.";
        }
    }

    public function activateAuction(Request $request, \App\Models\Auction $auction)
    {
        if ($auction->created_by !== auth()->id()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to activate this auction.'
                ], 403);
            }
            abort(403);
        }

        // Check if auction is already active
        if ($auction->status === 'active' || $auction->status === 'live') {
            return response()->json([
                'success' => false,
                'message' => 'This auction is already active.'
            ]);
        }

        // Get current pool count
        $currentPoolCount = \App\Models\AuctionPlayer::where('auction_id', $auction->id)->count();
        $minRequired = $auction->min_players * $auction->total_teams;
        $maxAllowed = $auction->max_players * $auction->total_teams;

        // Validate minimum players
        if ($currentPoolCount < $minRequired) {
            $needed = $minRequired - $currentPoolCount;
            return response()->json([
                'success' => false,
                'message' => "Minimum players not met! You need {$minRequired} players but only have {$currentPoolCount}. Add {$needed} more player" . ($needed > 1 ? 's' : '') . " to create the auction."
            ]);
        }

        // Validate maximum players
        if ($currentPoolCount > $maxAllowed) {
            $excess = $currentPoolCount - $maxAllowed;
            return response()->json([
                'success' => false,
                'message' => "Maximum players exceeded! You can only have {$maxAllowed} players but have {$currentPoolCount}. Remove {$excess} player" . ($excess > 1 ? 's' : '') . " to create the auction."
            ]);
        }

        // Update auction status to active
        try {
            $auction->update([
                'status' => 'active',
                'activated_at' => now()
            ]);

            // Log the activation
            Log::info('Auction activated', [
                'auction_id' => $auction->id,
                'user_id' => auth()->id(),
                'pool_count' => $currentPoolCount
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Auction activated successfully! Redirecting to live auction...',
                'redirect_url' => route('auctions.live', $auction)
            ]);

        } catch (\Exception $e) {
            Log::error('Error activating auction: ' . $e->getMessage(), [
                'auction_id' => $auction->id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error activating auction. Please try again.'
            ]);
        }
    }
}
