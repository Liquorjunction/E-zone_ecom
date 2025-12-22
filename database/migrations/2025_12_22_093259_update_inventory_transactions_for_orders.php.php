<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_transactions', function (Blueprint $table) {

            // Reference to source (order, return, adjustment)
            $table->string('reference_type')->nullable()->after('batch_no');
            $table->unsignedBigInteger('reference_id')->nullable()->after('reference_type');

            // Audit snapshot
            $table->decimal('before_quantity', 18, 3)->nullable()->after('reference_id');
            $table->decimal('after_quantity', 18, 3)->nullable()->after('before_quantity');

            // New signed quantity column (future-safe)
            $table->decimal('quantity_change', 18, 3)->nullable()->after('type');
        });

        // Expand enum safely (MySQL only)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE inventory_transactions
                MODIFY type ENUM(
                    'inward',
                    'outward',
                    'transfer',
                    'adjustment',
                    'reserve',
                    'release',
                    'sale',
                    'return',
                    'transfer_in',
                    'transfer_out'
                )
            ");
        }

        // Index for fast lookups (orders / returns)
        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->index(
                ['reference_type', 'reference_id'],
                'inv_txn_reference_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('inventory_transactions', function (Blueprint $table) {

            $table->dropIndex('inv_txn_reference_idx');

            $table->dropColumn([
                'reference_type',
                'reference_id',
                'before_quantity',
                'after_quantity',
                'quantity_change',
            ]);
        });

        // Revert enum (only if absolutely needed)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE inventory_transactions
                MODIFY type ENUM(
                    'inward',
                    'outward',
                    'transfer',
                    'adjustment'
                )
            ");
        }
    }
};
