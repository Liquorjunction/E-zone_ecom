<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            // Keep as text, add unique index with key length for MySQL
            $table->text('question')->change();
        });
        // Add unique index with key length (MySQL only allows this way for TEXT columns)
        DB::statement('CREATE UNIQUE INDEX faqs_question_unique ON faqs (question(191))');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            $table->text('question')->change();
        });
        // Drop the unique index
        DB::statement('DROP INDEX faqs_question_unique ON faqs');
    }
};
