<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuctionStatistics extends Model
{
    protected $fillable = [
        'auction_id',
        'total_players_sold',
        'total_players_unsold',
        'total_amount_spent',
        'average_player_price',
        'highest_bid',
        'lowest_bid',
        'total_bids_placed',
        'unique_bidders',
        'auction_started_at',
        'auction_completed_at',
        'duration_minutes'
    ];

    protected $casts = [
        'total_players_sold' => 'integer',
        'total_players_unsold' => 'integer',
        'total_amount_spent' => 'decimal:2',
        'average_player_price' => 'decimal:2',
        'highest_bid' => 'decimal:2',
        'lowest_bid' => 'decimal:2',
        'total_bids_placed' => 'integer',
        'unique_bidders' => 'integer',
        'duration_minutes' => 'integer',
        'auction_started_at' => 'datetime',
        'auction_completed_at' => 'datetime',
    ];

    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }
}
