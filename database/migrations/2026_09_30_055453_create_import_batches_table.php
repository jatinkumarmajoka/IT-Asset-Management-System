<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('import_batches', function (Blueprint $table) {
            $table->id();

            $table->string('original_filename');
            $table->string('stored_path');

            $table->string('file_type', 20);

            $table->string('status', 30)
                ->default('uploaded');

            $table->unsignedInteger('total_rows')
                ->default(0);

            $table->unsignedInteger('valid_rows')
                ->default(0);

            $table->unsignedInteger('invalid_rows')
                ->default(0);

            $table->unsignedInteger('duplicate_rows')
                ->default(0);

            $table->unsignedInteger('imported_rows')
                ->default(0);

            $table->json('error_summary')
                ->nullable();

            $table->foreignId('uploaded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('status');
            $table->index('file_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_batches');
    }
};