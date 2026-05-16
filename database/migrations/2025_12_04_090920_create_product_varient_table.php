<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');

            // Identity
            $table->text('variant_name'); // Translatable (e.g. "iPhone 15 - Red / 128GB")
            $table->string('sku')->unique(); // The Transactional SKU
            $table->string('slug')->unique();

            // Commerce
            $table->decimal('price', 10, 2);

            // Attributes (Color, Size, Material)
            $table->json('attributes')->nullable();

            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
