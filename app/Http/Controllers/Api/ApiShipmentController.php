<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Services\AuditService;
use App\Services\ShipmentService;
use App\Services\TrackingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ApiShipmentController extends Controller
{
    public function __construct(
        protected ShipmentService $shipmentService,
        protected TrackingService $trackingService,
        protected AuditService $auditService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $filters = $request->only(['search', 'status', 'carrier_id', 'origin_id', 'destination_id', 'is_delayed', 'sort_by', 'sort_order']);
        $perPage = min((int) ($request->input('per_page', 15)), 100);

        $shipments = $this->shipmentService->getPaginatedShipments($filters, $perPage, $user);

        return response()->json([
            'success' => true,
            'data' => $shipments->items(),
            'meta' => [
                'current_page' => $shipments->currentPage(),
                'last_page' => $shipments->lastPage(),
                'per_page' => $shipments->perPage(),
                'total' => $shipments->total(),
            ],
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user->canManageShipments()) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. Only Operators and Administrators can register shipments.',
            ], 403);
        }

        try {
            $validated = $request->validate([
                'customer_id' => ['required', 'exists:users,id'],
                'carrier_id' => ['required', 'exists:carriers,id'],
                'origin_id' => ['required', 'exists:locations,id'],
                'destination_id' => ['required', 'exists:locations,id', 'different:origin_id'],
                'cargo_type' => ['required', 'string', 'max:100'],
                'total_weight' => ['required', 'numeric', 'min:0.01'],
                'total_volume' => ['required', 'numeric', 'min:0.01'],
                'expected_departure' => ['required', 'date'],
                'expected_arrival' => ['required', 'date', 'after:expected_departure'],
                'estimated_arrival' => ['required', 'date', 'after:expected_departure'],
                'group_scenario' => ['nullable', 'string'],
                'notes' => ['nullable', 'string'],
                'items' => ['nullable', 'array'],
            ]);

            $shipment = $this->shipmentService->createShipment($validated, $user);

            return response()->json([
                'success' => true,
                'message' => 'Shipment created successfully.',
                'data' => $shipment->load(['customer', 'carrier', 'origin', 'destination', 'items']),
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function show(Request $request, int|string $id): JsonResponse
    {
        $user = $request->user();
        $shipment = Shipment::with([
            'customer', 'carrier', 'origin', 'destination', 'currentLocation',
            'items', 'trackingEvents', 'exceptions'
        ])->where('id', $id)->orWhere('shipment_number', $id)->first();

        if (!$shipment) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment not found.',
            ], 404);
        }

        if ($user->isCustomer() && $shipment->customer_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. You do not have permission to view this shipment.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $shipment,
        ], 200);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        if (!$user->canManageShipments()) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden.',
            ], 403);
        }

        $shipment = Shipment::find($id);
        if (!$shipment) {
            return response()->json(['success' => false, 'message' => 'Shipment not found.'], 404);
        }

        try {
            $validated = $request->validate([
                'status' => ['nullable', 'in:CREATED,BOOKED,PICKED_UP,IN_TRANSIT,DELIVERED'],
                'carrier_id' => ['nullable', 'exists:carriers,id'],
                'cargo_type' => ['nullable', 'string', 'max:100'],
                'total_weight' => ['nullable', 'numeric'],
                'total_volume' => ['nullable', 'numeric'],
                'notes' => ['nullable', 'string'],
            ]);

            if (!empty($validated['status']) && $validated['status'] !== $shipment->current_status) {
                $this->shipmentService->transitionStatus($shipment, $validated['status'], $user);
                unset($validated['status']);
            }

            if (!empty($validated)) {
                $old = $shipment->toArray();
                $shipment->update($validated);
                $this->auditService->log('UPDATE_SHIPMENT', $shipment, $old, $shipment->toArray(), $user);
            }

            return response()->json([
                'success' => true,
                'message' => 'Shipment updated successfully.',
                'data' => $shipment->fresh(['customer', 'carrier', 'origin', 'destination']),
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        if (!$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. Only Administrators can delete shipments.',
            ], 403);
        }

        $shipment = Shipment::find($id);
        if (!$shipment) {
            return response()->json(['success' => false, 'message' => 'Shipment not found.'], 404);
        }

        $this->auditService->log('DELETE_SHIPMENT', $shipment, $shipment->toArray(), null, $user);
        $shipment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Shipment deleted successfully.',
        ], 200);
    }

    public function getTracking(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $shipment = Shipment::with(['trackingEvents.location'])->find($id);

        if (!$shipment) {
            return response()->json(['success' => false, 'message' => 'Shipment not found.'], 404);
        }

        if ($user->isCustomer() && $shipment->customer_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden.'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'shipment_number' => $shipment->shipment_number,
                'current_status' => $shipment->current_status,
                'tracking_events' => $shipment->trackingEvents,
            ],
        ], 200);
    }

    public function postTracking(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        if (!$user->canManageShipments()) {
            return response()->json(['success' => false, 'message' => 'Forbidden.'], 403);
        }

        $shipment = Shipment::find($id);
        if (!$shipment) {
            return response()->json(['success' => false, 'message' => 'Shipment not found.'], 404);
        }

        try {
            $validated = $request->validate([
                'location_id' => ['nullable', 'exists:locations,id'],
                'location_name' => ['nullable', 'string'],
                'event_type' => ['required', 'string'],
                'status' => ['nullable', 'in:CREATED,BOOKED,PICKED_UP,IN_TRANSIT,DELIVERED'],
                'latitude' => ['nullable', 'numeric'],
                'longitude' => ['nullable', 'numeric'],
                'description' => ['required', 'string'],
                'event_time' => ['nullable', 'date'],
                'is_exception' => ['nullable', 'boolean'],
            ]);

            $event = $this->trackingService->addEvent($shipment, $validated, $user);

            return response()->json([
                'success' => true,
                'message' => 'Tracking event registered successfully.',
                'data' => $event,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        }
    }
}
