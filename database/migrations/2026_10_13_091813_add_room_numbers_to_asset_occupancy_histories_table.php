<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asset_occupancy_histories', function (Blueprint $table) {
            $table->string('previous_room_number', 100)->nullable();
            $table->string('present_room_number', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('asset_occupancy_histories', function (Blueprint $table) {
            $table->dropColumn([
                'previous_room_number',
                'present_room_number',
            ]);
        });
    }
};
