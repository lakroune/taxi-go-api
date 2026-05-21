<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vehicle_type',
        'plate_number',
        'license',
        'insurance_expiry',
        'status',
        'lat',
        'lng',
    ];

    protected function casts(): array
    {
        return [
            'insurance_expiry' => 'date',
            'lat' => 'decimal:8',
            'lng' => 'decimal:8',
        ];
    }

    /**
     * Retourne le compte utilisateur lié à ce conducteur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relation avec l'historique de suivi en temps réel (High Frequency Tracking)
     */
    public function locations(): HasMany
    {
        return $this->hasMany(Location::class, 'driver_id');
    }

    /**
     * La position actuelle ou dernière position connue du conducteur
     */
    public function currentNotificationLocation(): HasOne
    {
        return $this->hasOne(Location::class, 'driver_id')->latestOfMany();
    }

    /**
     * Les trajets effectués par ce conducteur
     */
    public function rides(): HasMany
    {
        return $this->hasMany(Ride::class, 'driver_id');
    }
}