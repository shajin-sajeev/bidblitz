<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamOwner extends Model
{
    protected $fillable = [
        'team_id', 'user_id', 'is_active'
    ];

    public function team() {
        return $this->belongsTo(Team::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
