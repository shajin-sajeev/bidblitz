<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Search auctions via AJAX for Select2
     */
    public function searchAuctions(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $page = $request->get('page', 1);
        $perPage = 10;
        
        $auctions = Auction::with(['creator'])
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', '%' . $query . '%')
                  ->orWhere('sport', 'LIKE', '%' . $query . '%')
                  ->orWhere('status', 'LIKE', '%' . $query . '%');
            })
            ->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->map(function ($auction) {
                return [
                    'id' => $auction->id,
                    'name' => $auction->name,
                    'sport' => $auction->sport,
                    'status' => $auction->status,
                    'budget' => $auction->budget,
                    'total_teams' => $auction->total_teams,
                    'creator_name' => $auction->creator->name ?? 'Unknown',
                    'created_by' => $auction->created_by,
                ];
            });
        
        return response()->json($auctions);
    }
    
    /**
     * Get recent auctions for dashboard
     */
    public function recentAuctions(): JsonResponse
    {
        $recentAuctions = Auction::with(['creator'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($auction) {
                return [
                    'id' => $auction->id,
                    'name' => $auction->name,
                    'sport' => $auction->sport,
                    'status' => $auction->status,
                    'budget' => $auction->budget,
                    'total_teams' => $auction->total_teams,
                    'creator_name' => $auction->creator->name ?? 'Unknown',
                    'created_by' => $auction->created_by,
                ];
            });
        
        return response()->json($recentAuctions);
    }
    
    /**
     * Get all auctions with pagination for dashboard
     */
    public function getAllAuctions(Request $request)
    {
        $auctions = Auction::with(['creator'])
            ->orderBy('created_at', 'desc')
            ->paginate(5);
            
        return view('dashboard', compact('auctions'));
    }

    public function createdAuctions()
    {
        $items = Auction::with(['creator', 'teams'])
            ->where('created_by', auth()->id())
            ->latest()
            ->paginate(5);

        return view('dashboard.details', [
            'items' => $items,
            'type' => 'created-auctions',
            'title' => 'Created Auctions',
            'subtitle' => 'All auctions created from your account.',
            'accent' => 'var(--primary)',
        ]);
    }

    public function myTeams()
    {
        $items = Team::with(['auction', 'players.player'])
            ->where('owner_id', auth()->id())
            ->latest()
            ->paginate(5);

        return view('dashboard.details', [
            'items' => $items,
            'type' => 'my-teams',
            'title' => 'My Teams',
            'subtitle' => 'Teams you own across joined auctions.',
            'accent' => 'var(--accent)',
        ]);
    }

    public function liveAuctions()
    {
        $items = Auction::with(['creator', 'teams'])
            ->where('status', 'live')
            ->latest()
            ->paginate(5);

        return view('dashboard.details', [
            'items' => $items,
            'type' => 'live-auctions',
            'title' => 'Live Auctions',
            'subtitle' => 'Auctions currently running live.',
            'accent' => '#10b981',
        ]);
    }

    public function completedAuctions()
    {
        $items = Auction::with(['creator', 'teams', 'history'])
            ->where('status', 'completed')
            ->latest()
            ->paginate(5);

        return view('dashboard.details', [
            'items' => $items,
            'type' => 'completed-auctions',
            'title' => 'Completed Auctions',
            'subtitle' => 'Finished auctions and their summary details.',
            'accent' => '#ef4444',
        ]);
    }
}
