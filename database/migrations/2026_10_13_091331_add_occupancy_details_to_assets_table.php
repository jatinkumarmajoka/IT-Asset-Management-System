<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('previous_department_name')->nullable();
            $table->string('previous_location')->nullable();
            $table->string('previous_room_number')->nullable();
            $table->string('present_location')->nullable();
            $table->string('present_room_number')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn([
                'previous_department_name',
                'previous_location',
                'previous_room_number',
                'present_location',
                'present_room_number',
            ]);
        });
    }
};
