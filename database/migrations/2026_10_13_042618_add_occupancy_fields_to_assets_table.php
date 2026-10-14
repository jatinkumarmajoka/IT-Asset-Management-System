
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            if (!Schema::hasColumn('assets', 'present_sub_department_id')) {
                $table->foreignId('present_sub_department_id')
                    ->nullable()
                    ->constrained('sub_departments')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('assets', 'present_occupant_name')) {
                $table->string('present_occupant_name', 150)->nullable();
            }
        });

        // Add the foreign key separately if the column already existed
        // but its foreign-key constraint was not created.
        $hasForeignKey = collect(
            \Illuminate\Support\Facades\DB::select(
                "SELECT CONSTRAINT_NAME
                 FROM information_schema.KEY_COLUMN_USAGE
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = 'assets'
                   AND COLUMN_NAME = 'present_sub_department_id'
                   AND REFERENCED_TABLE_NAME = 'sub_departments'"
            )
        )->isNotEmpty();

        if (!$hasForeignKey) {
            Schema::table('assets', function (Blueprint $table) {
                $table->foreign('present_sub_department_id')
                    ->references('id')
                    ->on('sub_departments')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            // Intentionally do not drop these columns automatically.
            // They may contain existing occupancy data.
        });
    }
};
