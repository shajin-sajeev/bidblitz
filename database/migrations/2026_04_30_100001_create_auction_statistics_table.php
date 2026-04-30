<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('auction_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auction_id')->constrained()->onDelete('cascade');
            $table->integer('total_players_sold')->default(0);
            $table->integer('total_players_unsold')->default(0);
            $table->decimal('total_amount_spent', 12, 2)->default(0);
            $table->decimal('average_player_price', 10, 2)->default(0);
            $table->decimal('highest_bid', 10, 2)->default(0);
            $table->decimal('lowest_bid', 10, 2)->default(0);
            $table->integer('total_bids_placed')->default(0);
            $table->integer('unique_bidders')->default(0);
            $table->timestamp('auction_started_at')->nullable();
            $table->timestamp('auction_completed_at')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auction_statistics');
    }
};
