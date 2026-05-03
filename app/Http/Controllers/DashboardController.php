<?php

namespace App\Http\Controllers;

use App\Models\Auction;
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
            ->paginate(10);
            
        return view('dashboard', compact('auctions'));
    }
}
