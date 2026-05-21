<?php

use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\RideController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 1. Public Authentication Routes (Guest)
|--------------------------------------------------------------------------
| Routes accessibles sans token, regroupées sous le préfixe 'auth'
*/
Route::middleware('guest')->prefix('auth')->group(function () {
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store']);
    Route::post('/reset-password', [NewPasswordController::class, 'store']);
});

/*
|--------------------------------------------------------------------------
| 2. Protected Routes (Authenticated via Sanctum)
|--------------------------------------------------------------------------
| Toutes les routes ci-dessous nécessitent un Bearer Token valide
*/
Route::middleware(['auth:sanctum'])->group(function () {

    /*
    |----------------------------------------------------------------------
    | User & Profile Management
    |----------------------------------------------------------------------
    */
    // Récupérer le profil de l'utilisateur connecté
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Mettre à jour la photo de profil (Avatar)
    Route::post('/user/avatar', [ProfileController::class, 'uploadAvatar']);

    /*
    |----------------------------------------------------------------------
    | Passenger & Ride Lifecycle Routes
    |----------------------------------------------------------------------
    */
    // Récupérer les chauffeurs disponibles à proximité (Geofencing)
    Route::get('/drivers/nearby', [RideController::class, 'getNearbyDrivers']);
    
    // Créer une nouvelle demande de trajet (Course Mcharka ou Standard)
    Route::post('/rides/request', [RideController::class, 'store']);
    
    // Récupérer l'historique des trajets de l'utilisateur
    Route::get('/rides/history', [RideController::class, 'history']);
    
    // Annuler un trajet (Action disponible pour le passager ou le chauffeur)
    Route::post('/rides/{id}/cancel', [RideController::class, 'cancelRide']);
    
    // Soumettre une note et un commentaire après la course (Système bidirectionnel)
    Route::post('/rides/rate', [RatingController::class, 'store']);

    /*
    |----------------------------------------------------------------------
    | Driver Specific Routes
    |----------------------------------------------------------------------
    */
    // Soumettre une demande pour devenir chauffeur (Inscription initiale)
    Route::post('/driver/join', [DriverController::class, 'joinAsDriver']);
    
    // Uploader les documents requis du véhicule (Permis, Assurance)
    Route::post('/driver/documents', [ProfileController::class, 'uploadDocuments']);
    
    // Mettre à jour le statut du chauffeur (available, busy, offline)
    Route::post('/driver/status', [DriverController::class, 'updateStatus']);
    
    // Consulter les demandes de trajets "pending" à proximité
    Route::get('/driver/available-rides', [DriverController::class, 'availableRides']);
    
    // Accepter une demande de trajet spécifique par un chauffeur
    Route::post('/driver/rides/{id}/accept', [DriverController::class, 'acceptRide']);
    
    // Finaliser la course et encaisser le paiement en espèces (Cash)
    Route::post('/rides/{id}/complete', [RideController::class, 'completeRide']);
    
    // Envoyer les coordonnées GPS en temps réel (Haute fréquence pour WebSockets)
    Route::post('/driver/track-location', [LocationController::class, 'updateLocation']);

    /*
    |----------------------------------------------------------------------
    | Internal Backoffice / Admin Simulation
    |----------------------------------------------------------------------
    */
    // Approuver le profil d'un chauffeur (À sécuriser par un middleware Admin plus tard)
    Route::post('/admin/driver/{id}/approve', [DriverController::class, 'approveDriver']);

    /*
    |----------------------------------------------------------------------
    | Email Verification & Logout
    |----------------------------------------------------------------------
    */
    // Correction ici : Changement du nom pour éviter les doublons lors de l'optimisation
    Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('api.verification.verify');

    // Renvoyer la notification de vérification d'email
    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1');

    // Déconnexion de l'utilisateur (Révocation du token Sanctum actuel)
    Route::post('/auth/logout', [AuthenticatedSessionController::class, 'destroy']);
});