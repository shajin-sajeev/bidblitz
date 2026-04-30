<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuctionHistory extends Model
{
    protected $fillable = [
        'auction_id',
        'player_id',
        'team_id',
        'bidder_id',
        'action',
        'amount',
        'description',
        'metadata',
        'action_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'action_at' => 'datetime',
    ];

    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function bidder()
    {
        return $this->belongsTo(User::class, 'bidder_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'bidder_id');
    }
}
