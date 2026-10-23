<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_departments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();

            $table->string('name', 150);
            $table->string('code', 50)->nullable()->unique();

            $table->enum('status', ['active', 'inactive'])
                ->default('active');

            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['department_id', 'status']);
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_departments');
    }
};
