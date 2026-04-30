<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'unique_username', 'email', 'phone', 'specialization', 
        'experience_years', 'base_price', 'description', 'avatar', 'is_active'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'experience_years' => 'integer',
        'is_active' => 'boolean',
        'profile' => 'array',
        'stats' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($player) {
            if (empty($player->unique_username)) {
                $player->unique_username = $player->generateUniqueUsername();
            }
        });
    }

    public function generateUniqueUsername()
    {
        $baseUsername = Str::lower(Str::slug($this->name));
        $username = $baseUsername;
        $counter = 1;

        while (static::where('unique_username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }

    public function auctionPlayers()
    {
        return $this->hasMany(AuctionPlayer::class);
    }

    public function auctions()
    {
        return $this->belongsToMany(Auction::class, 'auction_players')
                    ->withPivot(['team_id', 'sold_price', 'status'])
                    ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhere('unique_username', 'like', '%' . $search . '%')
              ->orWhere('email', 'like', '%' . $search . '%')
              ->orWhere('specialization', 'like', '%' . $search . '%');
        });
    }
}
