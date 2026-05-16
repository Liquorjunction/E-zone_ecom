<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE inventory_transactions MODIFY COLUMN type ENUM(
            'inward',
            'outward',
            'transfer',
            'adjustment',
            'update',
            'added',
            'removed',
            'reserve',
            'release',
            'return',
            'sale',
            'dispatch',
            'restock',
            'reserve_consume'
        ) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE inventory_transactions MODIFY COLUMN type ENUM(
            'inward',
            'outward',
            'transfer',
            'adjustment',
            'update',
            'added',
            'removed',
            'reserve',
            'release',
            'return',
            'sale',
            'dispatch',
            'restock'
        ) NOT NULL");
    }
};
