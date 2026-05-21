<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\Ride;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class RatingController extends Controller
{
    /**
     * تقييم رحلة (Submit Rating)
     * الراكب يقيم السائق أو السائق يقيم الراكب مورا ما تسالي الرحلة
     */
    public function store(Request $request): JsonResponse
    {
        $auth_user = $request->user();

        // 1. التحقق من صحة البيانات (التقييم من 1 لـ 5 نجوم)
        $validator = Validator::make($request->all(), [
            'ride_id' => 'required|exists:rides,id',
            'score' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 2. جلب الرحلة والتأكد أنها مكتملة
        $ride = Ride::find($request->ride_id);
        if ($ride->status !== 'completed') {
            return response()->json(['message' => 'You can only rate completed rides.'], 400);
        }

        // 3. تحديد من هو الشخص اللي كايقيم (Rater) ومن اللي كايتقيم (Ratee)
        // إذا كان صاحب الحساب راكب، غيقيم السائق.. والعكس صحيح
        if ($auth_user->id === $ride->passenger_id) {
            
            // الراكب يقيم السائق (خاصنا نجيبو الـ user_id ديال السائق)
            $ride->load('driver');
            if (!$ride->driver) {
                return response()->json(['message' => 'No driver assigned to this ride.'], 404);
            }
            $ratee_id = $ride->driver->user_id;

        } elseif ($auth_user->role === 'driver' && $ride->driver && $auth_user->id === $ride->driver->user_id) {
            
            // السائق يقيم الراكب
            $ratee_id = $ride->passenger_id;

        } else {
            return response()->json(['message' => 'Unauthorized. You are not part of this ride.'], 403);
        }

        // 4. التأكد أن المستخدم ما صيفطش التقييم مسبقاً لنفس الرحلة (حماية الداتا)
        $existingRating = Rating::where('ride_id', $ride->id)
                                ->where('rater_id', $auth_user->id)
                                ->first();
        if ($existingRating) {
            return response()->json(['message' => 'You have already rated this ride.'], 400);
        }

        // 5. حفظ التقييم في قاعدة البيانات
        $rating = Rating::create([
            'ride_id' => $ride->id,
            'rater_id' => $auth_user->id,
            'ratee_id' => $ratee_id,
            'score' => $request->score,
            'comment' => $request->comment,
        ]);

        // 6. تحديث معدل النجوم (Average Rating) تلقائياً فـ جدول الـ Users للشخص المقيم
        $averageScore = Rating::where('ratee_id', $ratee_id)->avg('score');
        User::where('id', $ratee_id)->update([
            'rating' => round($averageScore, 2)
        ]);

        return response()->json([
            'message' => 'Rating submitted successfully. Profiles updated.',
            'rating' => $rating
        ], 201);
    }
}