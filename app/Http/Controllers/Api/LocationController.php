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
     * mettre à jour la localisation du conducteur 5 secondes par seconde
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
            'heading' => 'nullable|integer', //direction
            'speed' => 'nullable|integer',   //speed
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 1. mettre à jour la localisation du conducteur
        $driver->update([
            'lat' => $request->lat,
            'lng' => $request->lng
        ]);

        // 2. créer une nouvelle location
        $location = Location::create([
            'driver_id' => $driver->id,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'heading' => $request->heading ?? 0,
            'speed' => $request->speed ?? 0,
        ]);
        //        $location = Location::create($request->all());
        // 3. broadcaster la nouvelle location
        broadcast(new \App\Events\DriverLocationUpdated($location, $driver->id))->toOthers();

        return response()->json([
            'message' => 'Location tracked successfully.',
            'current_location' => $location
        ]);
    }
}
