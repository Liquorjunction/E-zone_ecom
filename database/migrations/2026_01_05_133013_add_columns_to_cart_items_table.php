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
        Schema::table('cart_items', function (Blueprint $table) {
            $table->unsignedBigInteger('discount_id')->nullable()->index()->after('unit_price');
            $table->foreign('discount_id')->references('id')->on('discounts')->onDelete('set null')->after('unit_price');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('discount_id');
            $table->decimal('final_price', 10, 2)->default(0)->after('discount_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['discount_id']);
            $table->dropColumn(['discount_id', 'discount_amount', 'final_price']);
        });
    }
};
