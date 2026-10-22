
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
        // SQLite doesn't support information_schema, so we try to add the FK and catch the error if it exists
        try {
            Schema::table('assets', function (Blueprint $table) {
                $table->foreign('present_sub_department_id')
                    ->references('id')
                    ->on('sub_departments')
                    ->nullOnDelete();
            });
        } catch (\Exception $e) {
            // Foreign key already exists or column doesn't exist, ignore
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
