<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_us', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name', 100);
            $table->string('email', 255);
            $table->string('mobile', 20);
            $table->enum('subject', [
                'product_inquiry',
                'order_status',
                'return_replacement',
                'warranty_claim',
                'installation_support',
                'technical_issue',
                'bulk_purchase',
                'complaint',
                'other',
            ]);
            $table->string('order_number', 50)->nullable();
            $table->string('product_sku', 100)->nullable();
            $table->text('message');
            $table->json('images')->nullable();
            $table->json('attachments')->nullable();
            $table->string('ticket_number', 20)->nullable()->unique();
            $table->enum('status', [
                'open',
                'in_progress',
                'resolved',
                'closed',
            ])->default('open');
            $table->foreignId('admin_id')->nullable()->constrained('admin')->nullOnDelete();
            $table->text('admin_notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_us');
    }
};
