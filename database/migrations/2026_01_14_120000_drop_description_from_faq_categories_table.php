<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('faq_categories', function (Blueprint $table) {
            if (Schema::hasColumn('faq_categories', 'description')) {
                $table->dropColumn('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('faq_categories', function (Blueprint $table) {
            if (! Schema::hasColumn('faq_categories', 'description')) {
                $table->json('description')->nullable();
            }
        });
    }
};
