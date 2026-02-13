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
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('status_changed_at')->nullable()->after('status');
            $table->unsignedBigInteger('status_changed_by')->nullable()->after('status_changed_at');
            $table->foreign('status_changed_by')->references('id')->on('admin')->onDelete('set null');
            $table->unsignedBigInteger('cancelled_by')->nullable()->after('cancel_reason');
            $table->timestamp('cancelled_at')->nullable()->after('cancelled_by');          
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['status_changed_by']);
            $table->dropColumn(['status_changed_at', 'status_changed_by', 'cancelled_by', 'cancelled_at']);
        });
    }
};
