<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Hero Slider
        Schema::table('hero_slider', function (Blueprint $table) {
            $table->text('title')->change();
            $table->text('subtitle')->nullable()->change();
            $table->text('button_text')->nullable()->change();
        });

        // Categories
        Schema::table('categories', function (Blueprint $table) {
            $table->text('category-name')->change();
        });

        // Sub Categories
        Schema::table('sub-categories', function (Blueprint $table) {
            $table->text('sub-category-name')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hero_slider', function (Blueprint $table) {
            $table->string('title')->change();
            $table->string('subtitle')->nullable()->change();
            $table->string('button_text')->nullable()->change();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('category-name')->change();
        });

        Schema::table('sub-categories', function (Blueprint $table) {
            $table->string('sub-category-name')->change();
        });
    }
};
