<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuctionResult extends Model
{
    protected $fillable = [
        'auction_id', 'team_id', 'player_id', 'price'
    ];

    public function auction() {
        return $this->belongsTo(Auction::class);
    }

    public function player() {
        return $this->belongsTo(Player::class, 'player_id');
    }

    public function team() {
        return $this->belongsTo(Team::class);
    }
}
