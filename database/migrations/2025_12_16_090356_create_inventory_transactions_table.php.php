<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            // Link to the main inventory record
            $table->foreignId('inventory_id')->constrained()->onDelete('cascade');

            // Transaction Details
            $table->enum('type', ['inward', 'outward', 'transfer', 'adjustment']);
            $table->decimal('quantity', 18, 3);
            $table->decimal('rate', 18, 3)->default(0); // Cost price at time of entry
            $table->string('batch_no')->nullable(); // The shared batch number

            // Optional: User who did it
            $table->unsignedBigInteger('created_by')->nullable();

            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
