<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Services\ExceptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ExceptionController extends Controller
{
    public function __construct(
        protected ExceptionService $exceptionService
    ) {}

    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = ShipmentException::with(['shipment.carrier', 'shipment.customer', 'resolver']);

        if ($user && $user->isCustomer()) {
            $query->whereHas('shipment', fn ($q) => $q->where('customer_id', $user->id));
        }

        if ($request->filled('type')) {
            $query->where('exception_type', $request->type);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $exceptions = $query->latest('detected_at')->paginate(15)->withQueryString();

        // Metrics for exceptions page
        $totalOpen = (clone $query)->where('status', 'OPEN')->count();
        $totalInvestigating = (clone $query)->where('status', 'INVESTIGATING')->count();
        $totalResolved = (clone $query)->where('status', 'RESOLVED')->count();

        return view('exceptions.index', compact('exceptions', 'totalOpen', 'totalInvestigating', 'totalResolved'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (!$user->canManageShipments()) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'shipment_id' => ['required', 'exists:shipments,id'],
            'exception_type' => ['required', 'in:CUSTOMS_HOLD,WEATHER_DELAY,PORT_CONGESTION,DAMAGED,MISROUTED,DOCUMENTATION_ISSUE'],
            'severity' => ['required', 'in:LOW,MEDIUM,HIGH,CRITICAL'],
            'description' => ['required', 'string', 'max:1000'],
            'detected_at' => ['nullable', 'date'],
        ]);

        $shipment = Shipment::findOrFail($validated['shipment_id']);
        $this->exceptionService->createException($shipment, $validated, $user);

        return back()->with('success', 'Exception logged and priority alert registered.');
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $user = Auth::user();
        if (!$user->canManageShipments()) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:OPEN,INVESTIGATING,RESOLVED'],
            'resolution_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $exception = ShipmentException::findOrFail($id);
        $this->exceptionService->updateStatus(
            $exception,
            $validated['status'],
            $validated['resolution_notes'] ?? null,
            $user
        );

        return back()->with('success', sprintf('Exception #%d status updated to %s.', $exception->id, $validated['status']));
    }
}
