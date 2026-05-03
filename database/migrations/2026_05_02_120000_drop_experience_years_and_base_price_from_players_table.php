<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn(['experience_years', 'base_price']);
        });
    }

    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->integer('experience_years')->default(0)->after('specialization');
            $table->decimal('base_price', 10, 2)->default(0)->after('experience_years');
        });
    }
};
