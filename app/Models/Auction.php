<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auction extends Model
{
    protected $fillable = [
        'name', 'sport', 'min_players', 'max_players', 
        'total_teams', 'budget', 'auction_pass', 'status', 'created_by'
    ];

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function teams() {
        return $this->hasMany(Team::class);
    }

    public function participants() {
        return $this->hasMany(AuctionParticipant::class);
    }

    public function history() {
        return $this->hasMany(AuctionHistory::class);
    }

    public function statistics() {
        return $this->hasOne(AuctionStatistics::class);
    }

    public function players() {
        return $this->hasManyThrough(AuctionPlayer::class, Team::class);
    }
}
