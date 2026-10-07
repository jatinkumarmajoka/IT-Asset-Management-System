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
        Schema::table('asset_transfers', function (Blueprint $table) {
            $table->date('received_date')
                ->nullable()
                ->after('transfer_date');

            $table->string('reference_number', 50)
                ->nullable()
                ->unique()
                ->after('remarks');

            $table->foreignId('initiated_by')
                ->nullable()
                ->after('status')
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('approved_by')
                ->nullable()
                ->after('initiated_by')
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('completed_by')
                ->nullable()
                ->after('approved_by')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_transfers', function (Blueprint $table) {
            $table->dropForeign(['initiated_by']);
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['completed_by']);

            $table->dropColumn([
                'received_date',
                'reference_number',
                'initiated_by',
                'approved_by',
                'completed_by',
            ]);
        });
    }
};