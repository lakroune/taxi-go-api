<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'ride_id',
        'method',
        'amount',
        'status',
        'transaction_ref',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * Le trajet lié à ce paiement
     */
    public function ride(): BelongsTo
    {
        return $this->belongsTo(Ride::class, 'ride_id');
    }
}