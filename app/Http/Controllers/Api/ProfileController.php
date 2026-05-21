<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * 1. رفع وتحديث الصورة الشخصية (Upload Avatar)
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048', // الحد الأقصى 2 ميجا
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // حذف الصورة القديمة من السيرفر إذا كانت موجودة لتوفير المساحة
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // حفظ الصورة الجديدة في مجلد public/avatars
        $path = $request->file('avatar')->store('avatars', 'public');

        // تحديث مسار الصورة في قاعدة البيانات
        $user->update(['avatar' => $path]);

        return response()->json([
            'message' => 'Profile picture updated successfully.',
            'avatar_url' => asset('storage/' . $path) // رابط مباشر وصحيح للموبايل
        ]);
    }

    /**
     * 2. رفع وثائق السائق (Upload Driver Documents)
     */
    public function uploadDocuments(Request $request): JsonResponse
    {
        $user = $request->user();
        $driver = Driver::where('user_id', $user->id)->first();

        if (!$driver) {
            return response()->json(['message' => 'Driver profile not found. Please join as a driver first.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'license_file' => 'required|file|mimes:pdf,jpeg,png,jpg|max:5120',   // رخصة السياقة (حتى 5 ميجا)
            'insurance_file' => 'required|file|mimes:pdf,jpeg,png,jpg|max:5120', // وثيقة التأمين
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // حفظ الملفات في مجلدات خاصة ومأمنة داخل الـ Storage
        $licensePath = $request->file('license_file')->store('documents/licenses', 'public');
        $insurancePath = $request->file('insurance_file')->store('documents/insurances', 'public');

        // تحديث حقل الـ license في جدول السائقين ليحتفظ بالمسار الجديد
        $driver->update([
            'license' => $licensePath,
            // إذا قمت بإضافة حقل للتأمين مستقبلاً في الـ migration يمكنك حفظه هنا أيضاً
        ]);

        return response()->json([
            'message' => 'Documents uploaded successfully. Waiting for review.',
            'license_url' => asset('storage/' . $licensePath),
            'insurance_url' => asset('storage/' . $insurancePath)
        ]);
    }
}