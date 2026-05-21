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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            // يربط السائق بحسابه في جدول المستخدمين
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // 
            $table->enum('vehicle_type', ['taxi', 'moto']); // نوع المركبة 
            $table->string('plate_number'); // رقم اللوحة 
            $table->string('license'); // رخصة السياقة 
            $table->date('insurance_expiry'); // تاريخ انتهاء التأمين [cite: 54, 96]
            $table->enum('status', ['available', 'busy', 'offline'])->default('offline'); // حالة السائق [cite: 36, 96]

            // آخر إحداثيات جغرافية مسجلة للسائق 
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
