<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        // التحقق من صحة البيانات (الإيميل والباسورد)
        $request->authenticate();

        // جلب المستخدم الحالي
        $user = $request->user();

        // إنشاء الـ Token الخاص بـ Sanctum (الذي سيعوض الـ JWT المذكور في الدفتر) [cite: 54, 85]
        $token = $user->createToken('taxi-go-token')->plainTextToken;

        // إرجاع البيانات للموبايل مع الـ Token
        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role, // مهم لمعرفة واجهة الراكب من السائق 
            ]
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): JsonResponse
    {
        // حذف الـ Token الحالي عند تسجيل الخروج [cite: 54]
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
