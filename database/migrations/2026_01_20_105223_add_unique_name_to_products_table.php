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
        Schema::table('products', function (Blueprint $table) {
            // Check if unique constraint already exists
            $indexExists = collect(DB::select("SHOW INDEXES FROM products WHERE Column_name = 'name' AND Non_unique = 0"))->isNotEmpty();

            if (!$indexExists) {
                $table->string('name')->unique()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('name')->change();
        });
    }
};
