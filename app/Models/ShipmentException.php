<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentException extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'exception_type',
        'severity',
        'status',
        'description',
        'detected_at',
        'resolved_at',
        'resolved_by',
        'resolution_notes',
    ];

    protected function casts(): array
    {
        return [
            'detected_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function isOpen(): bool
    {
        return in_array($this->status, ['OPEN', 'INVESTIGATING']);
    }

    public function isResolved(): bool
    {
        return $this->status === 'RESOLVED';
    }

    public function getResolutionDurationInHours(): ?float
    {
        if (!$this->resolved_at || !$this->detected_at) {
            return null;
        }
        return round($this->detected_at->diffInMinutes($this->resolved_at) / 60, 2);
    }
}
