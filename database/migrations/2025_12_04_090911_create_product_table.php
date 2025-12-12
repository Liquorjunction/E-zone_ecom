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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained('brands')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('sub_category_id')->constrained('sub-categories')->onDelete('cascade');

            $table->text('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->longText('description');

            $table->string('cover_image')->nullable();

            // Pricing
            $table->decimal('price', 10, 2);

            // Specs
            $table->json('dimensions')->nullable();
            $table->json('specifications')->nullable();
            $table->text('warranty')->nullable();

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
        Schema::dropIfExists('products');
    }
};
