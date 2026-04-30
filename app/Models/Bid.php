<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    protected $fillable = [
        'auction_id', 'player_id', 'team_id', 'amount'
    ];

    public function auction() {
        return $this->belongsTo(Auction::class);
    }

    public function player() {
        return $this->belongsTo(User::class, 'player_id');
    }

    public function team() {
        return $this->belongsTo(Team::class);
    }
}
