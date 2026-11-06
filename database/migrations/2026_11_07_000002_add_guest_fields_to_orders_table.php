<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('client_type', 20)->default('registered')->after('user_id')->index();
            $table->foreignId('guest_user_id')->nullable()->after('client_type')->constrained('guest_users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['guest_user_id']);
            $table->dropColumn(['guest_user_id', 'client_type']);
        });
    }
};
