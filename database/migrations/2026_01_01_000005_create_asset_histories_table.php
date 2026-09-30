<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('asset_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->string('action', 80);
            $table->longText('old_value')->nullable();
            $table->longText('new_value')->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['asset_id','created_at']);
        });
    }

    public function down(): void { Schema::dropIfExists('asset_histories'); }
};
