<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    /**
     * تحديث الموقع الجغرافي للسائق (تستدعى كل 5 ثواني من تطبيق الموبايل)
     */
    public function updateLocation(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'driver') {
            return response()->json(['message' => 'Unauthorized. Drivers only.'], 403);
        }

        $driver = Driver::where('user_id', $user->id)->first();
        if (!$driver || $driver->status === 'offline') {
            return response()->json(['message' => 'Driver is offline or profile not found.'], 400);
        }

        $validator = Validator::make($request->all(), [
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'heading' => 'nullable|integer', // اتجاه السيارة (0-360 درجة) لرسمها بشكل صحيح في الخريطة
            'speed' => 'nullable|integer',   // السرعة الحالية
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 1. تحديث الموقع الحالي في جدول السائقين لسرعة البحث العادية
        $driver->update([
            'lat' => $request->lat,
            'lng' => $request->lng
        ]);

        // 2. تسجيل النقطة في جدول التاريخ (Locations Tracking) من أجل الـ Real-time والـ History
        $location = Location::create([
            'driver_id' => $driver->id,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'heading' => $request->heading ?? 0,
            'speed' => $request->speed ?? 0,
        ]);

        // ملاحظة: هنا يتم إطلاق Broadcast Event عبر WebSockets (Pusher/Reverb) 
        // لتحديث مكان السيارة على خريطة الراكب مباشرة بدون عمل Refresh.
        broadcast(new \App\Events\DriverLocationUpdated($location, $driver->id))->toOthers();

        return response()->json([
            'message' => 'Location tracked successfully.',
            'current_location' => $location
        ]);
    }
}