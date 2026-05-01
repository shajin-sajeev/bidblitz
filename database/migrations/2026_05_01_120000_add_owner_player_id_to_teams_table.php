<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            if (!Schema::hasColumn('teams', 'owner_player_id')) {
                $table->foreignId('owner_player_id')
                    ->nullable()
                    ->after('owner_id')
                    ->constrained('players')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            if (Schema::hasColumn('teams', 'owner_player_id')) {
                $table->dropConstrainedForeignId('owner_player_id');
            }
        });
    }
};
