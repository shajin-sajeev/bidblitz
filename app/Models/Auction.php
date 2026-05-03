<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auction extends Model
{
    protected $fillable = [
        'name', 'sport', 'min_players', 'max_players', 
        'total_teams', 'budget', 'min_amount', 'auction_pass', 'status', 'created_by', 'activated_at'
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

    public function auctionPlayers() {
        return $this->hasMany(AuctionPlayer::class);
    }

    /**
     * Each team has a designated owner player from the auction pool (Team Setup).
     */
    public function teamsFullyRegistered(): bool
    {
        $this->loadMissing('teams');
        $teams = $this->teams;
        if ($teams->count() !== (int) $this->total_teams) {
            return false;
        }

        return $teams->every(fn ($team) => $team->owner_player_id !== null);
    }

    /**
     * Each team has been claimed by a user via team pass (Join Auction).
     */
    public function teamsFullyJoined(): bool
    {
        $this->loadMissing('teams');
        $teams = $this->teams;
        if ($teams->count() !== (int) $this->total_teams) {
            return false;
        }

        return $teams->every(fn ($team) => $team->owner_id !== null);
    }

    public function canStartLive(): bool
    {
        return $this->teamsFullyRegistered() && $this->teamsFullyJoined();
    }
}
