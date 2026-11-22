<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\TrackingEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ShipmentService
{
    public function __construct(
        protected EtaService $etaService,
        protected AuditService $auditService
    ) {}

    /**
     * Generate a unique sequential shipment number (e.g. SHP-2024-001284).
     */
    public function generateShipmentNumber(): string
    {
        $year = date('Y');
        $count = Shipment::count() + 1;
        do {
            $number = sprintf('SHP-%s-%06d', $year, $count++);
        } while (Shipment::where('shipment_number', $number)->exists());

        return $number;
    }

    /**
     * Create a new shipment with items and initial evaluation.
     */
    public function createShipment(array $data, ?User $actor = null): Shipment
    {
        return DB::transaction(function () use ($data, $actor) {
            $shipmentNumber = $data['shipment_number'] ?? $this->generateShipmentNumber();

            // Evaluate ETA / Delay status
            $delayEval = $this->etaService->evaluateDelay(
                $data['expected_arrival'],
                $data['estimated_arrival'],
                $data['group_scenario'] ?? 'Default'
            );

            $shipment = Shipment::create([
                'shipment_number' => $shipmentNumber,
                'customer_id' => $data['customer_id'],
                'carrier_id' => $data['carrier_id'],
                'origin_id' => $data['origin_id'],
                'destination_id' => $data['destination_id'],
                'current_location_id' => $data['origin_id'],
                'cargo_type' => $data['cargo_type'] ?? 'General Cargo',
                'total_weight' => $data['total_weight'] ?? 0.0,
                'total_volume' => $data['total_volume'] ?? 0.0,
                'expected_departure' => $data['expected_departure'],
                'actual_departure' => $data['actual_departure'] ?? null,
                'expected_arrival' => $data['expected_arrival'],
                'estimated_arrival' => $data['estimated_arrival'],
                'current_status' => 'CREATED',
                'is_delayed' => $delayEval['is_delayed'],
                'delay_hours' => $delayEval['delay_hours'],
                'delay_severity' => $delayEval['delay_severity'],
                'group_scenario' => $data['group_scenario'] ?? 'Default',
                'notes' => $data['notes'] ?? null,
            ]);

            // Create shipment items if provided
            if (!empty($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    ShipmentItem::create([
                        'shipment_id' => $shipment->id,
                        'item_name' => $item['item_name'],
                        'sku' => $item['sku'] ?? null,
                        'quantity' => $item['quantity'] ?? 1,
                        'weight' => $item['weight'] ?? 0,
                        'volume' => $item['volume'] ?? 0,
                        'package_type' => $item['package_type'] ?? 'Standard Box',
                        'hs_code' => $item['hs_code'] ?? null,
                    ]);
                }
            }

            // Create initial milestone tracking event
            TrackingEvent::create([
                'shipment_id' => $shipment->id,
                'location_id' => $shipment->origin_id,
                'event_type' => 'MILESTONE',
                'status' => 'CREATED',
                'location_name' => $shipment->origin?->name ?? 'Origin Hub',
                'description' => 'Shipment created and registered in the CargoTrace system.',
                'event_time' => now(),
                'created_by' => $actor?->id ?? auth()->id(),
            ]);

            // Notify customer
            if ($shipment->customer) {
                Notification::create([
                    'user_id' => $shipment->customer_id,
                    'title' => 'Shipment Booked: #' . $shipment->shipment_number,
                    'message' => 'Your shipment has been created and registered with carrier ' . ($shipment->carrier?->name ?? 'assigned carrier') . '.',
                    'type' => 'info',
                    'link' => route('tracking.show', $shipment->shipment_number),
                ]);
            }

            $this->auditService->log('CREATE_SHIPMENT', $shipment, null, $shipment->toArray(), $actor);

            return $shipment;
        });
    }

    /**
     * Transition shipment status with strict business rule validation.
     * Slide 6:
     * - Transisi status divalidasi di backend.
     * - Status terminal seperti DELIVERED tidak boleh kembali ke status awal.
     */
    public function transitionStatus(Shipment $shipment, string $targetStatus, ?User $actor = null): Shipment
    {
        $current = $shipment->current_status;

        if ($current === $targetStatus) {
            return $shipment; // No transition needed
        }

        // Terminal check
        if ($shipment->isTerminal()) {
            throw ValidationException::withMessages([
                'status' => 'Shipment is already DELIVERED (terminal status) and cannot transition backwards.',
            ]);
        }

        // State machine transition validation
        if (!$shipment->canTransitionTo($targetStatus)) {
            $allowed = implode(', ', Shipment::STATUS_FLOW[$current] ?? []);
            throw ValidationException::withMessages([
                'status' => sprintf(
                    'Invalid status transition from "%s" to "%s". Allowed next status: [%s].',
                    $current,
                    $targetStatus,
                    $allowed ?: 'None'
                ),
            ]);
        }

        $oldValues = $shipment->toArray();
        $updates = ['current_status' => $targetStatus];

        if ($targetStatus === 'PICKED_UP' && !$shipment->actual_departure) {
            $updates['actual_departure'] = now();
        }

        if ($targetStatus === 'DELIVERED') {
            $updates['actual_arrival'] = now();
            $updates['current_location_id'] = $shipment->destination_id;
        }

        $shipment->update($updates);

        // Notify customer of status change
        if ($shipment->customer) {
            Notification::create([
                'user_id' => $shipment->customer_id,
                'title' => sprintf('Status Update: %s', str_replace('_', ' ', $targetStatus)),
                'message' => sprintf('Shipment #%s is now %s.', $shipment->shipment_number, str_replace('_', ' ', $targetStatus)),
                'type' => $targetStatus === 'DELIVERED' ? 'success' : 'info',
                'link' => route('tracking.show', $shipment->shipment_number),
            ]);
        }

        $this->auditService->log('STATUS_TRANSITION', $shipment, $oldValues, $shipment->toArray(), $actor);

        return $shipment;
    }

    /**
     * Filtered, searched, sorted & paginated query for shipments.
     * Slide 10 & 13.
     */
    public function getShipmentsQuery(array $filters = [], ?User $user = null)
    {
        $query = Shipment::with(['customer', 'carrier', 'origin', 'destination', 'currentLocation', 'openExceptions']);

        // RBAC Filter: Customer can only view their own shipments
        if ($user && $user->isCustomer()) {
            $query->where('customer_id', $user->id);
        }

        // Search by shipment number or notes
        if (!empty($filters['search'])) {
            $search = '%' . trim($filters['search']) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('shipment_number', 'like', $search)
                  ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', $search)->orWhere('company_name', 'like', $search))
                  ->orWhereHas('carrier', fn ($cr) => $cr->where('name', 'like', $search));
            });
        }

        // Status filter
        if (!empty($filters['status'])) {
            $query->where('current_status', $filters['status']);
        }

        // Carrier filter
        if (!empty($filters['carrier_id'])) {
            $query->where('carrier_id', $filters['carrier_id']);
        }

        // Origin filter
        if (!empty($filters['origin_id'])) {
            $query->where('origin_id', $filters['origin_id']);
        }

        // Destination filter
        if (!empty($filters['destination_id'])) {
            $query->where('destination_id', $filters['destination_id']);
        }

        // Delay filter
        if (isset($filters['is_delayed']) && $filters['is_delayed'] !== '') {
            $query->where('is_delayed', (bool) $filters['is_delayed']);
        }

        // Date range filter
        if (!empty($filters['start_date'])) {
            $query->whereDate('expected_departure', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('expected_departure', '<=', $filters['end_date']);
        }

        // Sorting
        $sortField = $filters['sort_by'] ?? 'created_at';
        $sortDir = strtolower($filters['sort_order'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
        $allowedSorts = ['id', 'shipment_number', 'created_at', 'expected_departure', 'expected_arrival', 'estimated_arrival', 'current_status', 'delay_hours'];
        if (in_array($sortField, $allowedSorts, true)) {
            $query->orderBy($sortField, $sortDir);
        } else {
            $query->latest();
        }

        return $query;
    }

    /**
     * Paginated list of shipments.
     */
    public function getPaginatedShipments(array $filters = [], int $perPage = 15, ?User $user = null): LengthAwarePaginator
    {
        return $this->getShipmentsQuery($filters, $user)->paginate($perPage)->withQueryString();
    }
}
