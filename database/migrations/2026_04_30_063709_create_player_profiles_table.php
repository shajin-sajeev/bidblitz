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
        Schema::create('player_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('player_role', ['Batsman', 'Bowler', 'All-rounder', 'Wicket-keeper'])->default('Batsman');
            $table->integer('matches')->default(0);
            $table->integer('runs')->default(0);
            $table->integer('wickets')->default(0);
            $table->decimal('strike_rate', 5, 2)->default(0);
            $table->decimal('economy', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_profiles');
    }
};
