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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');

            $table->text('variant_name'); // e.g., "Red / Large"
            $table->string('sku')->unique();
            $table->string('slug')->unique(); // Optional for variants, but okay to keep

            $table->decimal('price', 10, 2); // Variant specific price

            // Stores {"Color": "Red", "Size": "L"} to allow filtering
            $table->json('attributes')->nullable();

            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
