<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_occupancy_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('asset_id')
                ->constrained('assets')
                ->cascadeOnDelete();

            // Previous occupancy snapshot
            $table->foreignId('previous_department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();

            $table->string('previous_department_name', 150)->nullable();
            $table->foreignId('previous_sub_department_id')
                ->nullable()
                ->constrained('sub_departments')
                ->nullOnDelete();

            $table->string('previous_sub_department_name', 150)->nullable();
            $table->string('previous_occupant_name', 150)->nullable();
            $table->string('previous_location', 255)->nullable();

            // Present occupancy snapshot
            $table->foreignId('present_department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();

            $table->string('present_department_name', 150)->nullable();
            $table->foreignId('present_sub_department_id')
                ->nullable()
                ->constrained('sub_departments')
                ->nullOnDelete();

            $table->string('present_sub_department_name', 150)->nullable();
            $table->string('present_occupant_name', 150)->nullable();
            $table->string('present_location', 255)->nullable();

            $table->timestamp('effective_at')->nullable();

            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['asset_id', 'effective_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_occupancy_histories');
    }
};
