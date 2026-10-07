<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('asset_id')
                ->constrained('assets')
                ->cascadeOnDelete();

            /*
             * Supported document types:
             *
             * asset_image
             * bill_invoice
             */
            $table->string('document_type', 30);

            $table->string('original_name', 255);
            $table->string('stored_path', 500);

            $table->string('mime_type', 100);
            $table->unsignedBigInteger('file_size')->default(0);

            $table->foreignId('uploaded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
             * Once locked_at is populated, the original uploader
             * cannot replace/delete the document.
             *
             * Super Admin override will be implemented through
             * authorization in the document workflow.
             */
            $table->timestamp('locked_at')->nullable();

            $table->timestamps();

            /*
             * One official image and one official bill/invoice
             * per asset.
             */
            $table->unique(
                ['asset_id', 'document_type'],
                'asset_documents_asset_type_unique'
            );

            $table->index('document_type');
            $table->index('uploaded_by');
            $table->index('locked_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_documents');
    }
};