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
        // Add missing fields to orders table
        Schema::table('orders', function (Blueprint $table) {
            // Discount and coupon fields
            if (!Schema::hasColumn('orders', 'subtotal')) {
                $table->decimal('subtotal', 12, 2)->default(0)->after('total')->comment('Price before discounts and coupons');
            }
            if (!Schema::hasColumn('orders', 'discount_amount')) {
                $table->decimal('discount_amount', 12, 2)->default(0)->after('subtotal')->comment('Total item-level discounts');
            }
            if (!Schema::hasColumn('orders', 'coupon_id')) {
                $table->unsignedBigInteger('coupon_id')->nullable()->after('discount_amount')->comment('Applied coupon ID');
            }
            if (!Schema::hasColumn('orders', 'coupon_code')) {
                $table->string('coupon_code')->nullable()->after('coupon_id')->comment('Applied coupon code for reference');
            }
            if (!Schema::hasColumn('orders', 'coupon_discount_amount')) {
                $table->decimal('coupon_discount_amount', 12, 2)->default(0)->after('coupon_code')->comment('Coupon discount amount');
            }

            // Shipping and tax fields
            if (!Schema::hasColumn('orders', 'shipping_method')) {
                $table->string('shipping_method')->nullable()->after('coupon_discount_amount')->comment('e.g., standard, express');
            }
            if (!Schema::hasColumn('orders', 'shipping_cost')) {
                $table->decimal('shipping_cost', 12, 2)->default(0)->after('shipping_method')->comment('Shipping fee');
            }
            if (!Schema::hasColumn('orders', 'tax_amount')) {
                $table->decimal('tax_amount', 12, 2)->default(0)->after('shipping_cost')->comment('Tax calculated on final amount');
            }
            if (!Schema::hasColumn('orders', 'tax_percentage')) {
                $table->decimal('tax_percentage', 5, 2)->default(8.00)->after('tax_amount')->comment('Tax rate used (e.g., 8%)');
            }

            // Email for reference
            if (!Schema::hasColumn('orders', 'email')) {
                $table->string('email')->nullable()->after('phone_no')->comment('Customer email');
            }

            // Payment tracking
            if (!Schema::hasColumn('orders', 'transaction_id')) {
                $table->string('transaction_id')->nullable()->unique()->after('payment_method')->comment('Payment gateway transaction ID');
            }
        });

        // Add missing fields to order_items table
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'unit_price_original')) {
                $table->decimal('unit_price_original', 12, 2)->nullable()->after('unit_price')->comment('Original unit price before discount');
            }
            if (!Schema::hasColumn('order_items', 'discount_amount')) {
                $table->decimal('discount_amount', 12, 2)->default(0)->after('unit_price_original')->comment('Per-unit discount amount');
            }
            if (!Schema::hasColumn('order_items', 'line_subtotal')) {
                $table->decimal('line_subtotal', 12, 2)->nullable()->after('total_price')->comment('quantity * unit_price_original');
            }
            if (!Schema::hasColumn('order_items', 'line_discount')) {
                $table->decimal('line_discount', 12, 2)->default(0)->after('line_subtotal')->comment('Total discount for this line item');
            }
            if (!Schema::hasColumn('order_items', 'line_total')) {
                $table->decimal('line_total', 12, 2)->nullable()->after('line_discount')->comment('Final line total after discount: (unit_price_original - discount_amount) * quantity');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = ['subtotal', 'discount_amount', 'coupon_id', 'coupon_code', 'coupon_discount_amount', 'shipping_method', 'shipping_cost', 'tax_amount', 'tax_percentage', 'email', 'transaction_id'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            $columns = ['unit_price_original', 'discount_amount', 'line_subtotal', 'line_discount', 'line_total'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('order_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

