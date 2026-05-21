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
     * 1. (Upload Avatar)
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048', // 
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // supprimer prev avatar
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        //store public/avatars
        $path = $request->file('avatar')->store('avatars', 'public');

        $user->update(['avatar' => $path]);

        return response()->json([
            'message' => 'Profile picture updated successfully.',
            'avatar_url' => asset('storage/' . $path) //
        ]);
    }

    /**
     * 2. (Upload Documents)
     */
    public function uploadDocuments(Request $request): JsonResponse
    {
        $user = $request->user();
        $driver = Driver::where('user_id', $user->id)->first();

        if (!$driver) {
            return response()->json(['message' => 'Driver profile not found. Please join as a driver first.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'license_file' => 'required|file|mimes:pdf,jpeg,png,jpg|max:5120',   
            'insurance_file' => 'required|file|mimes:pdf,jpeg,png,jpg|max:5120', // 
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $licensePath = $request->file('license_file')->store('documents/licenses', 'public');
        $insurancePath = $request->file('insurance_file')->store('documents/insurances', 'public');

        $driver->update([
            'license' => $licensePath,
        ]);

        return response()->json([
            'message' => 'Documents uploaded successfully. Waiting for review.',
            'license_url' => asset('storage/' . $licensePath),
            'insurance_url' => asset('storage/' . $insurancePath)
        ]);
    }
}