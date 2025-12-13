<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Organization
            $table->foreignId('brand_id')->constrained('brands')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('sub_category_id')->constrained('sub-categories')->onDelete('cascade');

            // Basic Details
            $table->text('name'); // Translatable (handled by Spatie)
            $table->string('slug')->unique();

            // Parent SKU (Style Code) - Nullable because real SKUs are on variants
            $table->string('sku')->nullable()->unique();

            $table->longText('description'); // Translatable
            $table->string('cover_image')->nullable();

            // Display Price (Min price among variants) - Not transaction price
            $table->decimal('price', 10, 2)->default(0);

            // Shared Specs (Inherited by variants unless overridden)
            $table->json('dimensions')->nullable();
            $table->json('specifications')->nullable();
            $table->text('warranty')->nullable();

            // Flags
            $table->boolean('is_bestseller')->default(false);
            $table->enum('status', ['active', 'inactive'])->default('inactive');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
