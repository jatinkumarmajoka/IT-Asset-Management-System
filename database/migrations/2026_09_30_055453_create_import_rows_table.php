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
        Schema::create('import_rows', function (Blueprint $table) {
            $table->id();

            $table->foreignId('import_batch_id')
                ->constrained('import_batches')
                ->cascadeOnDelete();

            $table->unsignedInteger('row_number');

            $table->json('raw_data');

            $table->json('normalized_data')
                ->nullable();

            $table->string('status', 30)
                ->default('pending');

            $table->json('errors')
                ->nullable();

            $table->foreignId('duplicate_of_asset_id')
                ->nullable()
                ->constrained('assets')
                ->nullOnDelete();

            $table->timestamps();

            $table->index([
                'import_batch_id',
                'row_number',
            ]);

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_rows');
    }
};
