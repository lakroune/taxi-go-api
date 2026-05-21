<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ride;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class RideController extends Controller
{
    /**
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'origin_lat' => 'required|numeric',
            'origin_lng' => 'required|numeric',
            'dest_lat' => 'required|numeric',
            'dest_lng' => 'required|numeric',
            'is_shared' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $distance = $this->calculateDistance(
            $request->origin_lat,
            $request->origin_lng,
            $request->dest_lat,
            $request->dest_lng
        );

        $base_fare = 5.00;
        $price_per_km = 3.00;
        $estimated_fare = $base_fare + ($distance * $price_per_km);

        if ($request->is_shared) {
            $estimated_fare = $estimated_fare * 0.70;
        }

        $ride = Ride::create([
            'passenger_id' => $request->user()->id,
            'origin_lat' => $request->origin_lat,
            'origin_lng' => $request->origin_lng,
            'dest_lat' => $request->dest_lat,
            'dest_lng' => $request->dest_lng,
            'status' => 'pending', // 
            'fare' => round($estimated_fare, 2),
            'distance' => round($distance, 2),
            'duration' => round($distance * 2),
            'is_shared' => $request->is_shared ?? false,
        ]);


        return response()->json([
            'message' => 'Ride request created successfully. Searching for drivers...',
            'ride' => $ride
        ], 201);
    }

    /**
     */
    public function history(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role === 'passenger') {
            $rides = Ride::where('passenger_id', $user->id)->orderBy('created_at', 'desc')->get();
        } else {
            $driver = Driver::where('user_id', $user->id)->first();
            $rides = $driver
                ? Ride::where('driver_id', $driver->id)->orderBy('created_at', 'desc')->get()
                : collect();
        }

        return response()->json([
            'rides' => $rides
        ]);
    }

    /**
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * 3. (Complete Ride & Cash Payment)
     */
    public function completeRide(Request $request, $id): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'driver') {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $driver = Driver::where('user_id', $user->id)->first();
        $ride = Ride::where('id', $id)->where('driver_id', $driver->id)->first();

        if (!$ride) {
            return response()->json(['message' => 'Ride not found or not assigned to you.'], 404);
        }

        if ($ride->status !== 'accepted') {
            return response()->json(['message' => 'Ride cannot be completed from current status: ' . $ride->status], 400);
        }

        $ride->update(['status' => 'completed']);

        \App\Models\Payment::create([
            'ride_id' => $ride->id,
            'method' => 'cash',
            'amount' => $ride->fare,
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $driver->update(['status' => 'available']);

        return response()->json([
            'message' => 'Ride completed successfully. Cash payment recorded.',
            'fare_to_collect' => $ride->fare . ' DH', // 
            'ride' => $ride
        ]);
    }
    /**
     *      getNearbyDrivers  
     * 
     */
    public function getNearbyDrivers(Request $request): JsonResponse
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'radius' => 'nullable|numeric|max:50', // 
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userLat = $request->lat;
        $userLng = $request->lng;
        $radius = $request->radius ?? 5; 

        /*
         *  
         */
        $haversineFormula = '(6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat))))';

        $drivers = \App\Models\Driver::where('status', 'available')
            ->select('drivers.*')
            ->selectRaw("{$haversineFormula} AS distance", [$userLat, $userLng, $userLat])
            ->whereRaw("{$haversineFormula} <= ?", [$userLat, $userLng, $userLat, $radius])
            ->orderBy('distance', 'asc')
            ->with('user:id,name,phone,avatar')
            ->get();

        return response()->json([
            'search_radius_km' => (float)$radius,
            'drivers_found_count' => $drivers->count(),
            'drivers' => $drivers
        ]);
    }
    /**
     */
    public function cancelRide(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $ride = Ride::find($id);

        if (!$ride) {
            return response()->json(['message' => 'Ride not found.'], 404);
        }

        $isPassenger = ($user->id === $ride->passenger_id);
        $isDriver = false;

        if ($ride->driver_id) {
            $driver = Driver::find($ride->driver_id);
            if ($driver && $driver->user_id === $user->id) {
                $isDriver = true;
            }
        }

        if (!$isPassenger && !$isDriver) {
            return response()->json(['message' => 'Unauthorized. You are not part of this ride.'], 403);
        }

        if (in_array($ride->status, ['completed', 'cancelled'])) {
            return response()->json(['message' => 'Cannot cancel a ride that is already ' . $ride->status], 400);
        }

        $ride->update(['status' => 'cancelled']);

        if ($ride->driver_id) {
            Driver::where('id', $ride->driver_id)->update(['status' => 'available']);
        }


        return response()->json([
            'message' => 'Ride cancelled successfully.',
            'ride_status' => 'cancelled'
        ]);
    }
}
