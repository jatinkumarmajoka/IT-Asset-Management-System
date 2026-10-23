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
        Schema::create('asset_transfers', function (Blueprint $table) {
            $table->id();

            /*
             * Asset being transferred.
             */
            $table->foreignId('asset_id')
                ->constrained('assets')
                ->cascadeOnDelete();

            /*
             * Department before the transfer.
             */
            $table->foreignId('from_department_id')
                ->constrained('departments')
                ->restrictOnDelete();

            /*
             * Department after the transfer.
             */
            $table->foreignId('to_department_id')
                ->constrained('departments')
                ->restrictOnDelete();

            /*
             * Occupant before the transfer.
             *
             * Nullable because an asset can belong to a
             * department/section without an employee.
             */
            $table->foreignId('from_occupant_id')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();

            /*
             * Occupant after the transfer.
             *
             * Nullable for the same reason.
             */
            $table->foreignId('to_occupant_id')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();

            /*
             * Date on which the transfer actually happened.
             */
            $table->date('transfer_date');

            /*
             * Why the asset was transferred.
             */
            $table->string('reason', 500)->nullable();

            /*
             * Additional information.
             */
            $table->text('remarks')->nullable();

            /*
             * Transfer lifecycle.
             *
             * Initially we will use:
             * completed
             *
             * Later we can support:
             * pending
             * cancelled
             */
            $table->string('status', 30)->default('completed');

            /*
             * Administrator/user who performed the transfer.
             */
            $table->foreignId('performed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            /*
             * Indexes for common queries.
             */
            $table->index(['asset_id', 'transfer_date']);
            $table->index('from_department_id');
            $table->index('to_department_id');
            $table->index('transfer_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_transfers');
    }
};
