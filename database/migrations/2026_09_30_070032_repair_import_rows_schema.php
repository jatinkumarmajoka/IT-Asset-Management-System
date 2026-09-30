<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('import_rows', function (Blueprint $table) {
            $table->foreignId('import_batch_id')
                ->after('id')
                ->constrained('import_batches')
                ->cascadeOnDelete();

            $table->unsignedInteger('row_number')
                ->after('import_batch_id');

            $table->json('raw_data')
                ->after('row_number');

            $table->json('normalized_data')
                ->nullable()
                ->after('raw_data');

            $table->string('status', 30)
                ->default('pending')
                ->after('normalized_data');

            $table->json('errors')
                ->nullable()
                ->after('status');

            $table->foreignId('duplicate_of_asset_id')
                ->nullable()
                ->after('errors')
                ->constrained('assets')
                ->nullOnDelete();

            $table->index(['import_batch_id', 'status']);
            $table->index('row_number');
        });
    }

    public function down(): void
    {
        Schema::table('import_rows', function (Blueprint $table) {
            $table->dropForeign(['import_batch_id']);
            $table->dropForeign(['duplicate_of_asset_id']);

            $table->dropColumn([
                'import_batch_id',
                'row_number',
                'raw_data',
                'normalized_data',
                'status',
                'errors',
                'duplicate_of_asset_id',
            ]);
        });
    }
};