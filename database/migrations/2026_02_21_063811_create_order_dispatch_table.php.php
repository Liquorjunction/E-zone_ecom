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
        Schema::create('order_dispatch', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('inventory_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->unique(['order_id', 'order_item_id', 'inventory_id']);
            $table->string('status')->default('pending');
            $table->enum('dispatch_method', ['courier', 'pickup'])->nullable();
            $table->unsignedBigInteger('dispatch_from_store_id')->nullable();
            $table->unsignedBigInteger('dispatch_from_warehouse_id')->nullable();
            $table->foreign('dispatch_from_store_id')->references('id')->on('stores')->onDelete('set null');
            $table->foreign('dispatch_from_warehouse_id')->references('id')->on('warehouses')->onDelete('set null');
            $table->timestamp('dispatched_at')->nullable();
            $table->unsignedBigInteger('dispatched_by')->nullable();
            $table->foreign('dispatched_by')->references('id')->on('admin')->onDelete('set null');
            $table->text('remarks')->nullable();
            $table->softDeletes();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_dispatch');
    }
};
