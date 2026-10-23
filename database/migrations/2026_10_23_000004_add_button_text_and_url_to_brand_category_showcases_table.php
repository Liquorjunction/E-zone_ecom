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
        Schema::table('brand_category_showcases', function (Blueprint $table) {
            $table->json('button_text')->nullable()->after('description');
            $table->string('button_url')->nullable()->after('button_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brand_category_showcases', function (Blueprint $table) {
            $table->dropColumn(['button_text', 'button_url']);
        });
    }
};
