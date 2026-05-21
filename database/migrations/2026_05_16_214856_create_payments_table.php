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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ride_id')->constrained('rides')->onDelete('cascade'); // 
            $table->enum('method', ['cash', 'mobile_pay', 'wallet'])->default('cash'); // طريقة الدفع 
            $table->decimal('amount', 8, 2); // المبلغ المدفوع 
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending'); // حالة الدفع 
            $table->string('transaction_ref')->nullable(); // مرجع المعاملة الرقمية إن وجد 
            $table->timestamp('paid_at')->nullable(); // تاريخ الدفع الفعلي 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
