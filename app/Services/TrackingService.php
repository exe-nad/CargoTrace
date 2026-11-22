<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Shipment;
use App\Models\TrackingEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TrackingService
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Add a chronological tracking event to a shipment.
     */
    public function addEvent(Shipment $shipment, array $data, ?User $actor = null): TrackingEvent
    {
        $eventTime = isset($data['event_time']) ? Carbon::parse($data['event_time']) : now();

        // Validate chronological order (Slide 7: "Validasi urutan waktu dan relasi shipment")
        $latestEvent = $shipment->trackingEvents()->latest('event_time')->first();
        if ($latestEvent && $eventTime->lessThan($latestEvent->event_time)) {
            throw ValidationException::withMessages([
                'event_time' => sprintf(
                    'Tracking event time (%s) cannot be earlier than previous event time (%s).',
                    $eventTime->toDateTimeString(),
                    $latestEvent->event_time->toDateTimeString()
                ),
            ]);
        }

        return DB::transaction(function () use ($shipment, $data, $eventTime, $actor) {
            $event = TrackingEvent::create([
                'shipment_id' => $shipment->id,
                'location_id' => $data['location_id'] ?? null,
                'event_type' => $data['event_type'] ?? 'CHECKPOINT',
                'status' => $data['status'] ?? $shipment->current_status,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'location_name' => $data['location_name'] ?? null,
                'description' => $data['description'],
                'event_time' => $eventTime,
                'created_by' => $actor?->id ?? auth()->id(),
                'is_exception' => (bool) ($data['is_exception'] ?? false),
            ]);

            // Update current location if provided
            if (!empty($data['location_id'])) {
                $shipment->update(['current_location_id' => $data['location_id']]);
            }

            // If an explicit new status is specified and different from current, validate and transition
            if (!empty($data['status']) && $data['status'] !== $shipment->current_status) {
                app(ShipmentService::class)->transitionStatus($shipment, $data['status'], $actor);
            }

            // Send notification to customer
            if ($shipment->customer) {
                Notification::create([
                    'user_id' => $shipment->customer_id,
                    'title' => 'Tracking Update: #' . $shipment->shipment_number,
                    'message' => $event->description,
                    'type' => $event->is_exception ? 'warning' : 'info',
                    'link' => route('tracking.show', $shipment->shipment_number),
                ]);
            }

            $this->auditService->log('ADD_TRACKING_EVENT', $event, null, $event->toArray(), $actor);

            return $event;
        });
    }
}
