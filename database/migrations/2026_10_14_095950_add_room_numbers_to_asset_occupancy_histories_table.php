
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asset_occupancy_histories', function (Blueprint $table) {
            if (! Schema::hasColumn('asset_occupancy_histories', 'previous_room_number')) {
                $table->string('previous_room_number')->nullable();
            }

            if (! Schema::hasColumn('asset_occupancy_histories', 'present_room_number')) {
                $table->string('present_room_number')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('asset_occupancy_histories', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('asset_occupancy_histories', 'previous_room_number')) {
                $columns[] = 'previous_room_number';
            }

            if (Schema::hasColumn('asset_occupancy_histories', 'present_room_number')) {
                $columns[] = 'present_room_number';
            }

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
