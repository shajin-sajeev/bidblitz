<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'auction_id', 'name', 'logo', 'team_pass', 'owner_id', 'owner_player_id'
    ];

    public function auction() {
        return $this->belongsTo(Auction::class);
    }

    public function owner() {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function ownerPlayer() {
        return $this->belongsTo(Player::class, 'owner_player_id');
    }

    public function teamOwners() {
        return $this->hasMany(TeamOwner::class);
    }

    public function players() {
        return $this->hasMany(AuctionPlayer::class);
    }
}
