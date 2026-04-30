<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Update existing users who don't have a name
        DB::table('users')
            ->whereNull('name')
            ->orWhere('name', '')
            ->update([
                'name' => DB::raw("CONCAT('User ', id)")
            ]);
    }

    public function down()
    {
        // No need to revert this migration
    }
};
