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
        Schema::table('admin', function (Blueprint $table) {
            $table->string('password_reset_token', 64)->nullable()->index();
            $table->timestamp('password_reset_expires_at')->nullable();
            $table->timestamp('password_reset_requested_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin', function (Blueprint $table) {
            $table->dropColumn([
                'password_reset_token',
                'password_reset_expires_at',
                'password_reset_requested_at',
            ]);
        });
    }
};
