<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\User;
use App\Services\ShipmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ShipmentController extends Controller
{
    public function __construct(
        protected ShipmentService $shipmentService
    ) {}

    public function index(Request $request): View
    {
        $user = Auth::user();
        $filters = $request->only([
            'search', 'status', 'carrier_id', 'origin_id', 'destination_id',
            'is_delayed', 'start_date', 'end_date', 'sort_by', 'sort_order'
        ]);

        $shipments = $this->shipmentService->getPaginatedShipments($filters, 15, $user);
        $carriers = Carrier::where('is_active', true)->orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('shipments.index', compact('shipments', 'carriers', 'locations', 'filters'));
    }

    public function create(): View|RedirectResponse
    {
        $user = Auth::user();
        if (!$user->canManageShipments()) {
            abort(403, 'Unauthorized. Only Administrators and Logistics Operators can register new shipments.');
        }

        $customers = User::where('role', 'customer')->orderBy('name')->get();
        $carriers = Carrier::where('is_active', true)->orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('shipments.create', compact('customers', 'carriers', 'locations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (!$user->canManageShipments()) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'customer_id' => ['required', 'exists:users,id'],
            'carrier_id' => ['required', 'exists:carriers,id'],
            'origin_id' => ['required', 'exists:locations,id', 'different:destination_id'],
            'destination_id' => ['required', 'exists:locations,id'],
            'cargo_type' => ['required', 'string', 'max:100'],
            'total_weight' => ['required', 'numeric', 'min:0.01'],
            'total_volume' => ['required', 'numeric', 'min:0.01'],
            'expected_departure' => ['required', 'date'],
            'expected_arrival' => ['required', 'date', 'after:expected_departure'],
            'estimated_arrival' => ['required', 'date', 'after:expected_departure'],
            'group_scenario' => ['nullable', 'string'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['nullable', 'array'],
            'items.*.item_name' => ['required', 'string'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.weight' => ['required', 'numeric', 'min:0'],
            'items.*.package_type' => ['required', 'string'],
        ]);

        $shipment = $this->shipmentService->createShipment($validated, $user);

        return redirect()->route('shipments.show', $shipment->id)
            ->with('success', sprintf('Shipment #%s created successfully!', $shipment->shipment_number));
    }

    public function show(int $id): View
    {
        $user = Auth::user();
        $shipment = Shipment::with([
            'customer', 'carrier', 'origin', 'destination', 'currentLocation',
            'items', 'trackingEvents.location', 'trackingEvents.creator',
            'exceptions.resolver'
        ])->findOrFail($id);

        // RBAC: Customer can only view own shipment
        if ($user->isCustomer() && $shipment->customer_id !== $user->id) {
            abort(403, 'Access denied. You can only view your own company shipments.');
        }

        $locations = Location::orderBy('name')->get();

        return view('shipments.show', compact('shipment', 'locations'));
    }

    public function transition(Request $request, int $id): RedirectResponse
    {
        $user = Auth::user();
        if (!$user->canManageShipments()) {
            abort(403, 'Only Operators and Administrators can change shipment lifecycle status.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:CREATED,BOOKED,PICKED_UP,IN_TRANSIT,DELIVERED'],
        ]);

        $shipment = Shipment::findOrFail($id);
        $this->shipmentService->transitionStatus($shipment, $validated['status'], $user);

        return back()->with('success', sprintf('Shipment status updated to %s.', str_replace('_', ' ', $validated['status'])));
    }

    public function export(Request $request): StreamedResponse
    {
        $user = Auth::user();
        $filters = $request->only(['search', 'status', 'carrier_id', 'origin_id', 'destination_id', 'is_delayed']);
        $shipments = $this->shipmentService->getShipmentsQuery($filters, $user)->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="cargotrace_shipments_' . date('Y-m-d_His') . '.csv"',
        ];

        $callback = function () use ($shipments) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Shipment Number', 'Customer', 'Carrier', 'Origin', 'Destination',
                'Status', 'Cargo Type', 'Weight (kg)', 'Volume (cbm)',
                'Expected Departure', 'Expected Arrival', 'Estimated Arrival',
                'Is Delayed', 'Delay Hours', 'Delay Severity'
            ]);

            foreach ($shipments as $s) {
                fputcsv($handle, [
                    $s->shipment_number,
                    $s->customer?->name,
                    $s->carrier?->name,
                    $s->origin?->name,
                    $s->destination?->name,
                    $s->current_status,
                    $s->cargo_type,
                    $s->total_weight,
                    $s->total_volume,
                    $s->expected_departure?->toDateTimeString(),
                    $s->expected_arrival?->toDateTimeString(),
                    $s->estimated_arrival?->toDateTimeString(),
                    $s->is_delayed ? 'YES' : 'NO',
                    $s->delay_hours,
                    $s->delay_severity,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
