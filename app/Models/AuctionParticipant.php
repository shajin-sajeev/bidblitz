<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuctionParticipant extends Model
{
    protected $fillable = [
        'auction_id',
        'user_id',
        'team_id',
        'role',
        'joined_at'
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get all auctions the user has joined (not created)
     */
    public static function getJoinedAuctionsForUser($userId)
    {
        return self::with(['auction.creator', 'auction.teams', 'auction.statistics'])
            ->where('user_id', $userId)
            ->where('role', '!=', 'creator')
            ->orderBy('joined_at', 'desc')
            ->get()
            ->map(function ($participant) {
                return $participant->auction;
            });
    }

    /**
     * Check if user has joined a specific auction
     */
    public static function hasUserJoinedAuction($userId, $auctionId)
    {
        return self::where('user_id', $userId)
            ->where('auction_id', $auctionId)
            ->exists();
    }

    /**
     * Get user's role in a specific auction
     */
    public static function getUserRoleInAuction($userId, $auctionId)
    {
        return self::where('user_id', $userId)
            ->where('auction_id', $auctionId)
            ->value('role');
    }
}
