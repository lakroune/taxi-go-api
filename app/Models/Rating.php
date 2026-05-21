<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'ride_id',
        'rater_id',
        'ratee_id',
        'score',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
        ];
    }

    /**
     * Le trajet évalué
     */
    public function ride(): BelongsTo
    {
        return $this->belongsTo(Ride::class, 'ride_id');
    }

    /**
     * L'utilisateur qui évalue (Rater)
     */
    public function rater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rater_id');
    }

    /**
     * L'utilisateur qui reçoit la note (Ratee)
     */
    public function ratee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ratee_id');
    }
}