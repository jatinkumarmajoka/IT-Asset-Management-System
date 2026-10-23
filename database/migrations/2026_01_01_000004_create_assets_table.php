<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code', 100)->unique();

            $table->foreignId('department_id')->constrained()->restrictOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();

            $table->string('item_name', 255);
            $table->string('make', 150)->nullable();
            $table->string('model', 150)->nullable();

            $table->unsignedInteger('issued_quantity')->default(1);
            $table->unsignedInteger('physically_verified_quantity')->default(0);

            $table->foreignId('present_occupant_id')
                ->nullable()->constrained('employees')->nullOnDelete();

            $table->string('asset_serial_number', 150)->nullable()->unique();
            $table->string('barcode_number', 150)->nullable()->unique();

            $table->date('date_of_issue')->nullable();

            $table->string('asset_status', 50)->default('Active');
            $table->string('condition', 50)->default('Good');
            $table->string('location', 255)->nullable();
            $table->text('remarks')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['department_id', 'asset_status']);
            $table->index(['category_id', 'asset_status']);
            $table->index(['item_name', 'make', 'model']);
            $table->index('date_of_issue');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
