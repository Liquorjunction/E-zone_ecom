<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->unsignedBigInteger('coupon_id')->nullable()->after('session_id');
            $table->string('coupon_code')->nullable()->after('coupon_id');
            $table->decimal('coupon_discount_amount', 10, 2)->default(0)->after('coupon_code');

            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropColumn(['coupon_id', 'coupon_code', 'coupon_discount_amount']);
        });
    }
};
