<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_variant_id')->constrained('product_variants')->onDelete('cascade');

            $table->enum('location_type', ['warehouse', 'store']);

            $table->foreignId('store_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('warehouse_id')->nullable()->constrained()->onDelete('cascade');

            $table->decimal('cost_price', 18, 3)->default(0);

            $table->decimal('quantity', 18, 3)->default(0);
            $table->string('batch_no')->nullable();

            $table->enum('status', ['active', 'inactive', 'depleted'])->default('active');
            $table->text('notes')->nullable();

            $table->softDeletes();
            $table->timestamps();

            // Index for fast lookups
            // Determine stock for a variant in a specific location
            $table->index(['product_id', 'product_variant_id', 'warehouse_id', 'store_id'], 'inv_variant_loc_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
