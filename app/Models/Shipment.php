<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Shipment extends Model
{
    use HasFactory;

    // Allowed status transitions as per Slide 6
    public const STATUS_FLOW = [
        'CREATED' => ['BOOKED'],
        'BOOKED' => ['PICKED_UP'],
        'PICKED_UP' => ['IN_TRANSIT'],
        'IN_TRANSIT' => ['DELIVERED'],
        'DELIVERED' => [], // Terminal status: cannot revert without admin procedure
    ];

    protected $fillable = [
        'shipment_number',
        'customer_id',
        'carrier_id',
        'origin_id',
        'destination_id',
        'current_location_id',
        'cargo_type',
        'total_weight',
        'total_volume',
        'expected_departure',
        'actual_departure',
        'expected_arrival',
        'estimated_arrival',
        'actual_arrival',
        'current_status',
        'is_delayed',
        'delay_hours',
        'delay_severity',
        'group_scenario',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'expected_departure' => 'datetime',
            'actual_departure' => 'datetime',
            'expected_arrival' => 'datetime',
            'estimated_arrival' => 'datetime',
            'actual_arrival' => 'datetime',
            'is_delayed' => 'boolean',
            'total_weight' => 'float',
            'total_volume' => 'float',
            'delay_hours' => 'float',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class);
    }

    public function origin(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'origin_id');
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'destination_id');
    }

    public function currentLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'current_location_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ShipmentItem::class);
    }

    public function trackingEvents(): HasMany
    {
        return $this->hasMany(TrackingEvent::class)->orderBy('event_time', 'asc');
    }

    public function latestTrackingEvent(): HasOne
    {
        return $this->hasOne(TrackingEvent::class)->latestOfMany('event_time');
    }

    public function exceptions(): HasMany
    {
        return $this->hasMany(ShipmentException::class)->orderBy('detected_at', 'desc');
    }

    public function openExceptions(): HasMany
    {
        return $this->hasMany(ShipmentException::class)->whereIn('status', ['OPEN', 'INVESTIGATING']);
    }

    public function canTransitionTo(string $targetStatus): bool
    {
        $current = $this->current_status;
        $allowed = self::STATUS_FLOW[$current] ?? [];
        return in_array($targetStatus, $allowed, true);
    }

    public function isTerminal(): bool
    {
        return $this->current_status === 'DELIVERED';
    }

    public function scopeForUser(Builder $query, User $user): Builder
    {
        if ($user->isCustomer()) {
            return $query->where('customer_id', $user->id);
        }
        return $query;
    }

    public function scopeInTransit(Builder $query): Builder
    {
        return $query->where('current_status', 'IN_TRANSIT');
    }

    public function scopeDelivered(Builder $query): Builder
    {
        return $query->where('current_status', 'DELIVERED');
    }

    public function scopeDelayed(Builder $query): Builder
    {
        return $query->where('is_delayed', true);
    }
}
