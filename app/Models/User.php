<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'avatar',
        'rating',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'rating' => 'decimal:2',
        ];
    }

    /**
     * Relation avec le profil Conducteur (si le rôle est 'driver')
     */
    public function driverProfile(): HasOne
    {
        return $this->hasOne(Driver::class, 'user_id');
    }

    /**
     * Les trajets demandés par cet utilisateur en tant que passager
     */
    public function ridesAsPassenger(): HasMany
    {
        return $this->hasMany(Ride::class, 'passenger_id');
    }

    /**
     * Les avis donnés par cet utilisateur
     */
    public function ratingsGiven(): HasMany
    {
        return $this->hasMany(Rating::class, 'rater_id');
    }

    /**
     * Les avis reçus par cet utilisateur
     */
    public function ratingsReceived(): HasMany
    {
        return $this->hasMany(Rating::class, 'ratee_id');
    }
}