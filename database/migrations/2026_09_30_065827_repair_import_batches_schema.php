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
            if (! Schema::hasColumn('import_batches', 'original_filename')) {
                $table->string('original_filename')->after('id');
            }

            if (! Schema::hasColumn('import_batches', 'stored_path')) {
                $table->string('stored_path')->after('original_filename');
            }

            if (! Schema::hasColumn('import_batches', 'file_type')) {
                $table->string('file_type', 20)->after('stored_path');
            }

            if (! Schema::hasColumn('import_batches', 'status')) {
                $table->string('status', 30)
                    ->default('uploaded')
                    ->after('file_type');
            }

            if (! Schema::hasColumn('import_batches', 'total_rows')) {
                $table->unsignedInteger('total_rows')
                    ->default(0)
                    ->after('status');
            }

            if (! Schema::hasColumn('import_batches', 'valid_rows')) {
                $table->unsignedInteger('valid_rows')
                    ->default(0)
                    ->after('total_rows');
            }

            if (! Schema::hasColumn('import_batches', 'invalid_rows')) {
                $table->unsignedInteger('invalid_rows')
                    ->default(0)
                    ->after('valid_rows');
            }

            if (! Schema::hasColumn('import_batches', 'duplicate_rows')) {
                $table->unsignedInteger('duplicate_rows')
                    ->default(0)
                    ->after('invalid_rows');
            }

            if (! Schema::hasColumn('import_batches', 'imported_rows')) {
                $table->unsignedInteger('imported_rows')
                    ->default(0)
                    ->after('duplicate_rows');
            }

            if (! Schema::hasColumn('import_batches', 'error_summary')) {
                $table->json('error_summary')
                    ->nullable()
                    ->after('imported_rows');
            }

            if (! Schema::hasColumn('import_batches', 'uploaded_by')) {
                $table->foreignId('uploaded_by')
                    ->nullable()
                    ->after('error_summary')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });

        // Indexes already created in create_import_batches_table migration
        // Only create if they don't exist (SQLite doesn't support IF NOT EXISTS for indexes)
        try {
            Schema::table('import_batches', function (Blueprint $table) {
                $table->index('status');
                $table->index('file_type');
            });
        } catch (Exception $e) {
            // Index already exists, ignore
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('import_batches', function (Blueprint $table) {
            if (Schema::hasColumn('import_batches', 'uploaded_by')) {
                $table->dropForeign(['uploaded_by']);
            }

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
