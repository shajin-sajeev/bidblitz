<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('unique_username')->unique();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('specialization');
            $table->integer('experience_years')->default(0);
            $table->decimal('base_price', 10, 2)->default(0.00);
            $table->text('description')->nullable();
            $table->string('avatar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('players');
    }
};
