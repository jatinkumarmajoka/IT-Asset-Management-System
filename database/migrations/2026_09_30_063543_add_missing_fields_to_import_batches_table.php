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
        Schema::table('import_batches', function (Blueprint $table) {
            $table->string('original_filename')
                ->after('id');

            $table->string('stored_path')
                ->after('original_filename');

            $table->string('file_type', 20)
                ->after('stored_path');

            $table->string('status', 30)
                ->default('uploaded')
                ->after('file_type');

            $table->unsignedInteger('total_rows')
                ->default(0)
                ->after('status');

            $table->unsignedInteger('valid_rows')
                ->default(0)
                ->after('total_rows');

            $table->unsignedInteger('invalid_rows')
                ->default(0)
                ->after('valid_rows');

            $table->unsignedInteger('duplicate_rows')
                ->default(0)
                ->after('invalid_rows');

            $table->unsignedInteger('imported_rows')
                ->default(0)
                ->after('duplicate_rows');

            $table->json('error_summary')
                ->nullable()
                ->after('imported_rows');

            $table->foreignId('uploaded_by')
                ->nullable()
                ->after('error_summary')
                ->constrained('users')
                ->nullOnDelete();

            $table->index('status');
            $table->index('file_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('import_batches', function (Blueprint $table) {
            $table->dropForeign(['uploaded_by']);

            $table->dropIndex([
                'import_batches_status_index',
            ]);

            $table->dropIndex([
                'import_batches_file_type_index',
            ]);

            $table->dropColumn([
                'original_filename',
                'stored_path',
                'file_type',
                'status',
                'total_rows',
                'valid_rows',
                'invalid_rows',
                'duplicate_rows',
                'imported_rows',
                'error_summary',
                'uploaded_by',
            ]);
        });
    }
};