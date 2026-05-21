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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ride_id')->constrained('rides')->onDelete('cascade'); // 
            $table->foreignId('rater_id')->constrained('users')->onDelete('cascade'); // الشخص الذي قام بالتقييم 
            $table->foreignId('ratee_id')->constrained('users')->onDelete('cascade'); // الشخص الذي تم تقييمه 
            $table->tinyInteger('score'); // التقييم من 1 إلى 5 نجوم [cite: 73, 96]
            $table->text('comment')->nullable(); // تعليق اختياري [cite: 74, 96]
            $table->timestamps(); // 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
