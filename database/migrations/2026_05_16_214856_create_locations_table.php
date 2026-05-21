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
       Schema::create('locations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('driver_id')->constrained('drivers')->onDelete('cascade'); // 
    $table->decimal('lat', 10, 8); // 
    $table->decimal('lng', 11, 8); // 
    $table->integer('heading')->nullable(); // زاوية دوران المركبة (مهمة لتحريك السيارة على الخريطة بسلاسة) 
    $table->integer('speed')->nullable(); // سرعة المركبة 
    $table->timestamp('updated_at')->useCurrent()->onUpdate(DB::raw('CURRENT_TIMESTAMP')); // تحديث تلقائي عالي التردد 
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
