<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Ride;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class DriverController extends Controller
{
    /**
     * 1. Mettre à jour le statut et la position actuelle du conducteur
     */
    public function updateStatus(Request $request): JsonResponse
    {
        $user = $request->user();

        // Vérifier si l'utilisateur est bien un conducteur
        if ($user->role !== 'driver') {
            return response()->json(['message' => 'Unauthorized. Only drivers can update status.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:available,busy,offline',
            'lat' => 'required_if:status,available|numeric',
            'lng' => 'required_if:status,available|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Récupérer ou créer le profil conducteur
        $driver = Driver::updateOrCreate(
            ['user_id' => $user->id],
            [
                'status' => $request->status,
                'lat' => $request->lat,
                'lng' => $request->lng,
            ]
        );

        return response()->json([
            'message' => 'Driver status updated successfully.',
            'driver' => $driver
        ]);
    }

    /**
     * 2. Lister les trajets disponibles (en attente de chauffeur) à proximité
     * Note : Pour Asfi, on filtre les trajets "pending"
     */
    public function availableRides(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if ($user->role !== 'driver') {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // Récupérer les trajets en attente
        // Idéalement, ici on ferait un filtrage par rayon GPS (Bounding Box ou PostGIS)
        $rides = Ride::where('status', 'pending')
            ->with('passenger:id,name,phone,avatar') // Inclure les infos du passager pour le chauffeur
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['rides' => $rides]);
    }

    /**
     * 3. Accepter un trajet (Accept Ride Request)
     */
    public function acceptRide(Request $request, $id): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'driver') {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $driver = Driver::where('user_id', $user->id)->first();

        if (!$driver) {
            return response()->json(['message' => 'Driver profile not found.'], 404);
        }

        // Trouver le trajet
        $ride = Ride::find($id);

        if (!$ride) {
            return response()->json(['message' => 'Ride not found.'], 404);
        }

        // Vérifier si le trajet est toujours disponible
        if ($ride->status !== 'pending') {
            return response()->json(['message' => 'Ride has already been accepted or cancelled.'], 400);
        }

        // Mettre à jour le trajet
        $ride->update([
            'driver_id' => $driver->id,
            'status' => 'accepted'
        ]);

        // Mettre à jour le statut du chauffeur à "busy"
        $driver->update(['status' => 'busy']);

        // Ici, un événement WebSocket/FCM est normalement déclenché pour notifier le passager

        return response()->json([
            'message' => 'Ride accepted successfully. Drive safe!',
            'ride' => $ride->load('passenger:id,name,phone')
        ]);
    }

    public function joinAsDriver(Request $request): JsonResponse
    {
        $user = $request->user();

        // 1. التأكد أن المستخدم لم يقم بطلب مسبقاً
        $existingDriver = Driver::where('user_id', $user->id)->first();
        if ($existingDriver) {
            return response()->json([
                'message' => 'You have already submitted a driver request. Status: ' . $existingDriver->status
            ], 400);
        }

        // 2. التحقق من صحة الوثائق والبيانات
        $validator = Validator::make($request->all(), [
            'vehicle_type' => 'required|in:taxi,moto', // طاكسي صغير أو دراجة نارية
            'plate_number' => 'required|string|unique:drivers,plate_number', // رقم اللوحة
            'license' => 'required|string', // رقم رخصة السياقة
            'insurance_expiry' => 'required|date|after:today', // تاريخ انتهاء التأمين (خاص يكون مزال خدام)
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 3. إنشاء ملف السائق بحالة 'offline' مؤقتاً في انتظار تفعيل الـ Admin
        $driver = Driver::create([
            'user_id' => $user->id,
            'vehicle_type' => $request->vehicle_type,
            'plate_number' => $request->plate_number,
            'license' => $request->license,
            'insurance_expiry' => $request->insurance_expiry,
            'status' => 'offline', // كيبدا offline تال التفعيل
        ]);

        // (اختياري) هنا فالمستقبل غنزيدو الكود ديال رفع صور الوثائق (Carte Grise, Assurance)

        return response()->json([
            'message' => 'Your driver application has been submitted successfully. Waiting for admin approval.',
            'driver' => $driver
        ], 21);
    }

    /**
     * 2. تفعيل السائق من طرف الأدمن (Admin Approval)
     * هادي كيمشي ليها الأدمن ف لوحة التحكم باش يرجع الـ Role ديالو 'driver'
     */
    public function approveDriver($id): JsonResponse
    {
        // ملاحظة: هاد الـ Endpoint خاص يكون محمي بـ Middleware ديال Admin مستقبلاً
        $driver = Driver::find($id);

        if (!$driver) {
            return response()->json(['message' => 'Driver application not found.'], 404);
        }

        // تحديث دور المستخدم في جدول Users ليصبح driver رسميًا
        $user = User::find($driver->user_id);
        $user->update(['role' => 'driver']);

        // تحديث حالة السائق ليصبح جاهزاً للاستخدام
        $driver->update(['status' => 'offline']); 

        return response()->json([
            'message' => 'Driver approved successfully. The user is now an official TAXI GO driver.',
            'user' => $user,
            'driver' => $driver
        ]);
    }
}