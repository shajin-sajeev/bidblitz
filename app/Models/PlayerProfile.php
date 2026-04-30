<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerProfile extends Model
{
    protected $fillable = [
        'user_id', 'player_role', 'matches', 'runs', 'wickets', 'strike_rate', 'economy'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
