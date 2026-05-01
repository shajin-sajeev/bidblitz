<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('auctions', function (Blueprint $table) {
            // Add activated_at column
            $table->timestamp('activated_at')->nullable()->after('status');
        });

        // Update the status enum to include 'active'
        DB::statement("ALTER TABLE auctions MODIFY COLUMN status ENUM('pending', 'active', 'live', 'completed') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auctions', function (Blueprint $table) {
            $table->dropColumn('activated_at');
        });

        // Revert the status enum
        DB::statement("ALTER TABLE auctions MODIFY COLUMN status ENUM('pending', 'live', 'completed') DEFAULT 'pending'");
    }
};
