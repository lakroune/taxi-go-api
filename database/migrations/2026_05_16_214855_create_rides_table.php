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
        Schema::create('rides', function (Blueprint $table) {
            $table->id(); // 
            $table->foreignId('passenger_id')->constrained('users')->onDelete('cascade'); // 
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->onDelete('set null'); // يكون Null حتى يقبل السائق الطلب 

            // الإحداثيات الجغرافية (الانطلاق والوصول) 
            $table->decimal('origin_lat', 10, 8);
            $table->decimal('origin_lng', 11, 8);
            $table->decimal('dest_lat', 10, 8);
            $table->decimal('dest_lng', 11, 8);

            // حالة الرحلة 
            $table->enum('status', ['pending', 'accepted', 'ongoing', 'completed', 'cancelled'])->default('pending');

            $table->decimal('fare', 8, 2)->nullable(); // التكلفة التقريبية أو النهائية 
            $table->double('distance')->nullable(); // المسافة بالكيلومتر 
            $table->integer('duration')->nullable(); // المدة المتوقعة بالدقائق 
            $table->boolean('is_shared')->default(false); // ميزة "الكورس مشركة" الخاصة بآسفي [cite: 38, 40]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rides');
    }
};
