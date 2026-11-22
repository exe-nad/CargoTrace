<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class ExceptionService
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Create and record a new shipment exception.
     */
    public function createException(Shipment $shipment, array $data, ?User $actor = null): ShipmentException
    {
        $validTypes = [
            'CUSTOMS_HOLD',
            'WEATHER_DELAY',
            'PORT_CONGESTION',
            'DAMAGED',
            'MISROUTED',
            'DOCUMENTATION_ISSUE',
        ];

        if (!in_array($data['exception_type'], $validTypes, true)) {
            throw ValidationException::withMessages([
                'exception_type' => 'Invalid exception type provided.',
            ]);
        }

        $exception = ShipmentException::create([
            'shipment_id' => $shipment->id,
            'exception_type' => $data['exception_type'],
            'severity' => $data['severity'] ?? 'MEDIUM',
            'status' => 'OPEN',
            'description' => $data['description'],
            'detected_at' => $data['detected_at'] ?? now(),
        ]);

        // Automatically mark shipment as delayed if critical or high
        if (in_array($exception->severity, ['HIGH', 'CRITICAL']) && !$shipment->is_delayed) {
            $shipment->update([
                'is_delayed' => true,
                'delay_severity' => $exception->severity === 'CRITICAL' ? 'HIGH' : $exception->severity,
            ]);
        }

        // Notify customer
        if ($shipment->customer) {
            Notification::create([
                'user_id' => $shipment->customer_id,
                'title' => 'Shipment Alert: ' . str_replace('_', ' ', $exception->exception_type),
                'message' => sprintf(
                    'An exception was flagged for shipment #%s: %s',
                    $shipment->shipment_number,
                    $exception->description
                ),
                'type' => 'danger',
                'link' => route('tracking.show', $shipment->shipment_number),
            ]);
        }

        $this->auditService->log('CREATE_EXCEPTION', $exception, null, $exception->toArray(), $actor);

        return $exception;
    }

    /**
     * Update exception status (e.g. OPEN -> INVESTIGATING -> RESOLVED).
     */
    public function updateStatus(
        ShipmentException $exception,
        string $newStatus,
        ?string $resolutionNotes = null,
        ?User $actor = null
    ): ShipmentException {
        $validStatuses = ['OPEN', 'INVESTIGATING', 'RESOLVED'];

        if (!in_array($newStatus, $validStatuses, true)) {
            throw ValidationException::withMessages([
                'status' => 'Invalid exception status.',
            ]);
        }

        $oldValues = $exception->toArray();
        $updates = ['status' => $newStatus];

        if ($newStatus === 'RESOLVED') {
            $updates['resolved_at'] = now();
            $updates['resolved_by'] = $actor?->id ?? auth()->id();
            if ($resolutionNotes) {
                $updates['resolution_notes'] = $resolutionNotes;
            }

            // Check if there are other open exceptions on the shipment
            $hasRemainingOpen = ShipmentException::where('shipment_id', $exception->shipment_id)
                ->where('id', '!=', $exception->id)
                ->whereIn('status', ['OPEN', 'INVESTIGATING'])
                ->exists();

            if (!$hasRemainingOpen && $exception->shipment->is_delayed) {
                // If no remaining open exceptions and estimated_arrival <= expected_arrival, clear delay
                if ($exception->shipment->estimated_arrival <= $exception->shipment->expected_arrival) {
                    $exception->shipment->update([
                        'is_delayed' => false,
                        'delay_severity' => 'NONE',
                    ]);
                }
            }

            // Notify customer of resolution
            if ($exception->shipment->customer) {
                Notification::create([
                    'user_id' => $exception->shipment->customer_id,
                    'title' => 'Exception Resolved: ' . str_replace('_', ' ', $exception->exception_type),
                    'message' => sprintf(
                        'Exception for shipment #%s has been resolved. Note: %s',
                        $exception->shipment->shipment_number,
                        $resolutionNotes ?? 'Resolved by operations team.'
                    ),
                    'type' => 'success',
                    'link' => route('tracking.show', $exception->shipment->shipment_number),
                ]);
            }
        }

        $exception->update($updates);

        $this->auditService->log('UPDATE_EXCEPTION_STATUS', $exception, $oldValues, $exception->toArray(), $actor);

        return $exception;
    }
}
