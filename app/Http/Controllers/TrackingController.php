<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Shipment;
use App\Services\ExternalApiService;
use App\Services\TrackingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TrackingController extends Controller
{
    public function __construct(
        protected TrackingService $trackingService,
        protected ExternalApiService $externalApiService
    ) {}

    public function index(Request $request): View
    {
        $user = Auth::user();
        $queryNumber = $request->input('number');

        $activeShipments = Shipment::query()->forUser($user)
            ->whereIn('current_status', ['PICKED_UP', 'IN_TRANSIT'])
            ->with(['origin', 'destination', 'carrier'])
            ->latest()
            ->limit(10)
            ->get();

        if ($queryNumber) {
            return $this->show($queryNumber);
        }

        return view('tracking.index', compact('activeShipments'));
    }

    public function show(string $number): View|RedirectResponse
    {
        $user = Auth::user();

        $shipment = Shipment::with([
            'customer', 'carrier', 'origin', 'destination', 'currentLocation',
            'trackingEvents.location', 'trackingEvents.creator',
            'exceptions'
        ])->where('shipment_number', trim($number))->first();

        if (!$shipment) {
            return redirect()->route('tracking.index')
                ->with('error', sprintf('Shipment with number "%s" was not found in CargoTrace registry.', $number));
        }

        // RBAC check: Customer cannot track another customer's shipment
        if ($user && $user->isCustomer() && $shipment->customer_id !== $user->id) {
            abort(403, 'Unauthorized access to this shipment.');
        }

        // Fetch Live Weather for the current location or origin via ExternalApiService (Slide 9)
        $targetLocation = $shipment->currentLocation ?? $shipment->origin;
        $weatherData = $targetLocation ? $this->externalApiService->getWeather(
            (float) $targetLocation->latitude,
            (float) $targetLocation->longitude
        ) : null;

        // Build Map Waypoints array for Leaflet.js
        $waypoints = [];

        // 1. Origin
        $waypoints[] = [
            'type' => 'ORIGIN',
            'label' => 'Origin: ' . $shipment->origin->name,
            'lat' => (float) $shipment->origin->latitude,
            'lng' => (float) $shipment->origin->longitude,
            'city' => $shipment->origin->city,
            'time' => $shipment->expected_departure?->format('M d, Y H:i'),
        ];

        // 2. Tracking intermediate events
        foreach ($shipment->trackingEvents as $event) {
            if ($event->latitude && $event->longitude) {
                $waypoints[] = [
                    'type' => $event->is_exception ? 'EXCEPTION' : 'WAYPOINT',
                    'label' => $event->location_name ?? $event->description,
                    'lat' => (float) $event->latitude,
                    'lng' => (float) $event->longitude,
                    'city' => $event->location_name ?? 'En Route',
                    'time' => $event->event_time?->format('M d, Y H:i'),
                    'status' => $event->status,
                    'desc' => $event->description,
                ];
            }
        }

        // 3. Destination
        $waypoints[] = [
            'type' => 'DESTINATION',
            'label' => 'Destination: ' . $shipment->destination->name,
            'lat' => (float) $shipment->destination->latitude,
            'lng' => (float) $shipment->destination->longitude,
            'city' => $shipment->destination->city,
            'time' => $shipment->estimated_arrival?->format('M d, Y H:i'),
        ];

        $locations = Location::orderBy('name')->get();

        return view('tracking.show', compact('shipment', 'weatherData', 'waypoints', 'locations'));
    }

    public function printWaybill(string $number): View|RedirectResponse
    {
        $user = Auth::user();
        $shipment = Shipment::with([
            'customer', 'carrier', 'origin', 'destination', 'currentLocation',
            'items', 'trackingEvents', 'exceptions'
        ])->where('shipment_number', trim($number))->firstOrFail();

        if ($user && $user->isCustomer() && $shipment->customer_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        return view('tracking.print', compact('shipment'));
    }

    public function storeEvent(Request $request, int $id): RedirectResponse
    {
        $user = Auth::user();
        if (!$user || !$user->canManageShipments()) {
            abort(403, 'Unauthorized.');
        }

        $shipment = Shipment::findOrFail($id);

        $validated = $request->validate([
            'location_id' => ['nullable', 'exists:locations,id'],
            'location_name' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'event_type' => ['required', 'string'],
            'status' => ['nullable', 'in:CREATED,BOOKED,PICKED_UP,IN_TRANSIT,DELIVERED'],
            'description' => ['required', 'string', 'max:500'],
            'event_time' => ['required', 'date'],
            'is_exception' => ['nullable', 'boolean'],
        ]);

        // Auto-fill coordinates from location if selected
        if (!empty($validated['location_id']) && empty($validated['latitude'])) {
            $loc = Location::find($validated['location_id']);
            if ($loc) {
                $validated['latitude'] = $loc->latitude;
                $validated['longitude'] = $loc->longitude;
                $validated['location_name'] = $loc->name;
            }
        }

        $this->trackingService->addEvent($shipment, $validated, $user);

        return back()->with('success', 'New tracking checkpoint logged successfully!');
    }
}
