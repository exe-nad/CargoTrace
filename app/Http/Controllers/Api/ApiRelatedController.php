<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Services\AuditService;
use App\Services\ExceptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ApiRelatedController extends Controller
{
    public function __construct(
        protected ExceptionService $exceptionService,
        protected AuditService $auditService
    ) {}

    // GET /api/carriers
    public function getCarriers(): JsonResponse
    {
        $carriers = Carrier::where('is_active', true)->get();
        return response()->json(['success' => true, 'data' => $carriers], 200);
    }

    // POST /api/carriers
    public function postCarriers(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Forbidden.'], 403);
        }

        try {
            $validated = $request->validate([
                'code' => ['required', 'string', 'unique:carriers,code'],
                'name' => ['required', 'string'],
                'contact_email' => ['nullable', 'email'],
                'phone' => ['nullable', 'string'],
                'service_type' => ['required', 'string'],
            ]);

            $carrier = Carrier::create($validated);
            $this->auditService->log('API_CREATE_CARRIER', $carrier, null, $carrier->toArray(), $user);

            return response()->json(['success' => true, 'message' => 'Carrier created.', 'data' => $carrier], 201);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $e->errors()], 422);
        }
    }

    // GET /api/locations
    public function getLocations(): JsonResponse
    {
        $locations = Location::all();
        return response()->json(['success' => true, 'data' => $locations], 200);
    }

    // POST /api/locations
    public function postLocations(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Forbidden.'], 403);
        }

        try {
            $validated = $request->validate([
                'code' => ['required', 'string', 'unique:locations,code'],
                'name' => ['required', 'string'],
                'type' => ['required', 'string'],
                'city' => ['required', 'string'],
                'country' => ['required', 'string'],
                'latitude' => ['required', 'numeric'],
                'longitude' => ['required', 'numeric'],
            ]);

            $loc = Location::create($validated);
            $this->auditService->log('API_CREATE_LOCATION', $loc, null, $loc->toArray(), $user);

            return response()->json(['success' => true, 'message' => 'Location created.', 'data' => $loc], 201);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $e->errors()], 422);
        }
    }

    // GET /api/shipments/{id}/exceptions
    public function getShipmentExceptions(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $shipment = Shipment::find($id);
        if (!$shipment) {
            return response()->json(['success' => false, 'message' => 'Shipment not found.'], 404);
        }

        if ($user->isCustomer() && $shipment->customer_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden.'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $shipment->exceptions()->with('resolver')->get(),
        ], 200);
    }

    // POST /api/shipments/{id}/exceptions
    public function postShipmentExceptions(Request $request, int $id): JsonResponse
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
                'exception_type' => ['required', 'in:CUSTOMS_HOLD,WEATHER_DELAY,PORT_CONGESTION,DAMAGED,MISROUTED,DOCUMENTATION_ISSUE'],
                'severity' => ['required', 'in:LOW,MEDIUM,HIGH,CRITICAL'],
                'description' => ['required', 'string'],
            ]);

            $exception = $this->exceptionService->createException($shipment, $validated, $user);

            return response()->json([
                'success' => true,
                'message' => 'Exception logged.',
                'data' => $exception,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $e->errors()], 422);
        }
    }

    // PATCH /api/exceptions/{id}
    public function patchException(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        if (!$user->canManageShipments()) {
            return response()->json(['success' => false, 'message' => 'Forbidden.'], 403);
        }

        $exception = ShipmentException::find($id);
        if (!$exception) {
            return response()->json(['success' => false, 'message' => 'Exception not found.'], 404);
        }

        try {
            $validated = $request->validate([
                'status' => ['required', 'in:OPEN,INVESTIGATING,RESOLVED'],
                'resolution_notes' => ['nullable', 'string'],
            ]);

            $updated = $this->exceptionService->updateStatus(
                $exception,
                $validated['status'],
                $validated['resolution_notes'] ?? null,
                $user
            );

            return response()->json([
                'success' => true,
                'message' => 'Exception status updated successfully.',
                'data' => $updated,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $e->errors()], 422);
        }
    }
}
