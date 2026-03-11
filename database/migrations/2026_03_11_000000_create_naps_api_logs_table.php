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
        Schema::create('naps_api_logs', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->nullable()->index();
            $table->longText('api_response');
            $table->string('api_type')->nullable(); // token, authorization, refund, status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('naps_api_logs');
    }
};

