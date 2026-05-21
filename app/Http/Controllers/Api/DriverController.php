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

        // 1. Vérifier si l'utilisateur a deja soumis une demande de chauffeur
        $existingDriver = Driver::where('user_id', $user->id)->first();
        if ($existingDriver) {
            return response()->json([
                'message' => 'You have already submitted a driver request. Status: ' . $existingDriver->status
            ], 400);
        }

        // 2. Vérifier les données du formulaire
        $validator = Validator::make($request->all(), [
            'vehicle_type' => 'required|in:taxi,moto', // type de voiture
            'plate_number' => 'required|string|unique:drivers,plate_number', // numero de plaque
            'license' => 'required|string', // permis
            'insurance_expiry' => 'required|date|after:today', // date d'expiration de l'assurance
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 3. Créer le profil de chauffeur
        $driver = Driver::create([
            'user_id' => $user->id,
            'vehicle_type' => $request->vehicle_type,
            'plate_number' => $request->plate_number,
            'license' => $request->license,
            'insurance_expiry' => $request->insurance_expiry,
            'status' => 'offline', // par défaut, le chauffeur est hors ligne
        ]);

        // 4. Envoyer une notification aux admins (Carte Grise, Assurance)

        return response()->json([
            'message' => 'Your driver application has been submitted successfully. Waiting for admin approval.',
            'driver' => $driver
        ], 21);
    }

    /** 
     * 4. Approuver le chauffeur (Approve Driver)
     * Note : Pour Asfi, on approuve le chauffeur directement et on met son statut à "offline"
     */
    public function approveDriver($id): JsonResponse
    {
        // Trouver le profil de chauffeur
        $driver = Driver::find($id);

        if (!$driver) {
            return response()->json(['message' => 'Driver application not found.'], 404);
        }

        // Mettre à jour le rôle de l'utilisateur
        $user = User::find($driver->user_id);
        $user->update(['role' => 'driver']);

        // Mettre à jour le statut du chauffeur
        $driver->update(['status' => 'offline']); 

        return response()->json([
            'message' => 'Driver approved successfully. The user is now an official TAXI GO driver.',
            'user' => $user,
            'driver' => $driver
        ]);
    }
}