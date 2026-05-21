<?php

namespace App\Listeners;

use Laravel\Reverb\Events\ConnectionClosed;
use App\Models\Driver;
use Illuminate\Support\Facades\Log;

class HandleDriverDisconnect
{
    /**
     * معالجة انقطاع الاتصال المفاجئ
     */
    public function handle(object $event): void
    {
        // جلب المستخدم المرتبط بالاتصال الذي أُغلق
        $user = $event->connection->user; 

        if ($user && $user->role === 'driver') {
            $driver = Driver::where('user_id', $user->id)->first();
            
            if ($driver && $driver->status === 'available') {
                // تحويل حالة السائق إلى offline تلقائياً لحماية الخريطة من السيارات الوهمية
                $driver->update(['status' => 'offline']);
                
                Log::info("Driver ID {$driver->id} went offline automatically due to connection loss.");
            }
        }
    }
}