<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Location extends Model
{
    use HasFactory;

    // On désactive la gestion par défaut de created_at et updated_at car gérée par la DB
    public $timestamps = false;

    protected $fillable = [
        'driver_id',
        'lat',
        'lng',
        'heading',
        'speed',
    ];

    protected function casts(): array
    {
        return [
            'lat' => 'decimal:8',
            'lng' => 'decimal:8',
            'heading' => 'integer',
            'speed' => 'integer',
        ];
    }

    /**
     * Le conducteur concerné par cette coordonnée GPS
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
}