<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invitation_template_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('price');
            $table->string('status')->default('pending');
            $table->string('payment_method')->default('manual');
            $table->string('payment_proof_path')->nullable();
            $table->timestamp('paid_at')->nullable();
            // confirmed_by / created_by: nullOnDelete supaya menghapus admin tak
            // menghapus order. created_by disiapkan untuk jalur mitra (7D+).
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
