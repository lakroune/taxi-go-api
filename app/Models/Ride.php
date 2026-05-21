<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ride extends Model
{
    use HasFactory;

    protected $fillable = [
        'passenger_id',
        'driver_id',
        'origin_lat',
        'origin_lng',
        'dest_lat',
        'dest_lng',
        'status',
        'fare',
        'distance',
        'duration',
        'is_shared',
    ];

    protected function casts(): array
    {
        return [
            'origin_lat' => 'decimal:8',
            'origin_lng' => 'decimal:8',
            'dest_lat' => 'decimal:8',
            'dest_lng' => 'decimal:8',
            'fare' => 'decimal:2',
            'distance' => 'double',
            'duration' => 'integer',
            'is_shared' => 'boolean',
        ];
    }

    /**
     * Le passager qui a commandé le trajet
     */
    public function passenger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'passenger_id');
    }

    /**
     * Le conducteur qui a accepté le trajet
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    /**
     * Le paiement associé à ce trajet
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'ride_id');
    }

    /**
     * Les avis/notes laissés pour ce trajet (Avis du passager et du chauffeur)
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class, 'ride_id');
    }
}