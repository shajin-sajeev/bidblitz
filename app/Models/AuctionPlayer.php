<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuctionPlayer extends Model
{
    protected $fillable = [
        'auction_id', 'player_id', 'base_price', 'status', 'sold_price', 'team_id'
    ];

    public function auction() {
        return $this->belongsTo(Auction::class);
    }

    public function player() {
        return $this->belongsTo(Player::class);
    }

    public function team() {
        return $this->belongsTo(Team::class);
    }
}
