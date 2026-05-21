<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Driver;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


/*
 * تحويل القناة إلى Presence Channel لمراقبة دخول وخروج السائق والراكب
 */
Broadcast::channel('tracking.{driverId}', function ($user, $driverId) {
    
    $isPassenger = \App\Models\Ride::where('driver_id', $driverId)
        ->where('passenger_id', $user->id)
        ->whereIn('status', ['pending', 'accepted'])
        ->exists();

    $driver = Driver::where('user_id', $user->id)->first();
    $isCurrentDriver = ($driver && $driver->id == $driverId);

    // إذا كان المستخدم مخولاً بالدخول، نعيد مصفوفة ببياناته الأساسية للـ Presence
    if ($isCurrentDriver || $isPassenger) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role
        ];
    }

    return false;
});
