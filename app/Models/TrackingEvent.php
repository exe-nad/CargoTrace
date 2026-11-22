<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackingEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'location_id',
        'event_type',
        'status',
        'latitude',
        'longitude',
        'location_name',
        'description',
        'event_time',
        'created_by',
        'is_exception',
    ];

    protected function casts(): array
    {
        return [
            'event_time' => 'datetime',
            'latitude' => 'float',
            'longitude' => 'float',
            'is_exception' => 'boolean',
        ];
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
