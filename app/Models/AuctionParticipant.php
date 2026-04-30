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
}
