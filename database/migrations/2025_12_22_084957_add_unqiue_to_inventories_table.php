<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventories', function (Blueprint $table) {

            // Unique inventory per variant per location
            $table->unique(
                ['product_variant_id', 'warehouse_id', 'store_id'],
                'inv_variant_unique_loc'
            );

            // Explicit index names (IMPORTANT)
            $table->index('product_variant_id', 'inv_product_variant_idx');
            $table->index('warehouse_id', 'inv_warehouse_idx');
            $table->index('store_id', 'inv_store_idx');
        });

        // CHECK constraint (MySQL 8.0.16+)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE inventories
                ADD CONSTRAINT chk_inventory_location
                CHECK (
                    (warehouse_id IS NOT NULL AND store_id IS NULL)
                    OR
                    (warehouse_id IS NULL AND store_id IS NOT NULL)
                )
            ");
        }
    }

    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {

            $table->dropUnique('inv_variant_unique_loc');

            $table->dropIndex('inv_product_variant_idx');
            $table->dropIndex('inv_warehouse_idx');
            $table->dropIndex('inv_store_idx');
        });

        // Drop CHECK safely
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE inventories
                DROP CHECK chk_inventory_location
            ");
        }
    }
};
