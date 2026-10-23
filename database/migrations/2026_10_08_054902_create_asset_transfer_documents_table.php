<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_transfer_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('asset_transfer_id')
                ->constrained('asset_transfers')
                ->cascadeOnDelete();

            $table->string('document_type', 30);

            $table->string('original_name', 255);

            $table->string('stored_path', 500);

            $table->string('mime_type', 100);

            $table->unsignedBigInteger('file_size')->default(0);

            $table->foreignId('uploaded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            /*
             * One transfer = one transfer image.
             */
            $table->unique(
                ['asset_transfer_id', 'document_type'],
                'asset_transfer_documents_transfer_type_unique'
            );

            $table->index('document_type');
            $table->index('uploaded_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_transfer_documents');
    }
};
