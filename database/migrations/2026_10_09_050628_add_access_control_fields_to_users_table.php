<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('staff');
            $table->string('account_status')->default('pending');
            $table->json('permissions')->nullable();
            $table->timestamp('last_activity_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'account_status',
                'permissions',
                'last_activity_at',
            ]);
        });
    }
};
