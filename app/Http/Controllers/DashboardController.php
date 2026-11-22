<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use App\Models\Notification;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Services\ShipmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected ShipmentService $shipmentService
    ) {}

    public function index(Request $request): View
    {
        $user = Auth::user();

        // 1. Core Top Stat Cards (Filtered by customer if role is customer)
        $shipmentsQuery = Shipment::query()->forUser($user);

        $totalShipments = (clone $shipmentsQuery)->count();
        $inTransitCount = (clone $shipmentsQuery)->where('current_status', 'IN_TRANSIT')->count();
        $deliveredCount = (clone $shipmentsQuery)->where('current_status', 'DELIVERED')->count();
        $delayedCount = (clone $shipmentsQuery)->where('is_delayed', true)->count();

        $openExceptionsQuery = ShipmentException::whereIn('status', ['OPEN', 'INVESTIGATING']);
        if ($user && $user->isCustomer()) {
            $openExceptionsQuery->whereHas('shipment', fn ($q) => $q->where('customer_id', $user->id));
        }
        $openExceptionsCount = $openExceptionsQuery->count();

        // 2. Chart: Shipments by Status
        $statusCounts = [
            'in_transit' => $inTransitCount,
            'delivered' => $deliveredCount,
            'delayed' => $delayedCount,
            'open_exceptions' => $openExceptionsCount,
        ];

        // 3. Chart: Delay Rate by Carrier
        $carriers = Carrier::withCount(['shipments', 'shipments as delayed_shipments_count' => function ($q) use ($user) {
            $q->where('is_delayed', true);
            if ($user && $user->isCustomer()) {
                $q->where('customer_id', $user->id);
            }
        }])->get();

        $carrierDelayData = [];
        $totalDelayedAllCarriers = 0;
        foreach ($carriers as $carrier) {
            $delayed = $carrier->delayed_shipments_count;
            $carrierDelayData[] = [
                'name' => $carrier->name,
                'delayed_count' => $delayed,
                'total_shipments' => $carrier->shipments_count,
            ];
            $totalDelayedAllCarriers += $delayed;
        }

        // Calculate average delay rate percentage
        $averageDelayRate = $totalShipments > 0 ? round(($delayedCount / $totalShipments) * 100, 1) : 0.0;

        // 4. Map Preview Data (Top active in-transit shipments with coordinates)
        $mapShipments = (clone $shipmentsQuery)
            ->with(['origin', 'destination', 'currentLocation', 'latestTrackingEvent'])
            ->whereIn('current_status', ['IN_TRANSIT', 'PICKED_UP'])
            ->limit(10)
            ->get()
            ->map(function ($s) {
                return [
                    'number' => $s->shipment_number,
                    'status' => $s->current_status,
                    'is_delayed' => $s->is_delayed,
                    'origin' => [
                        'name' => $s->origin->name,
                        'city' => $s->origin->city,
                        'lat' => $s->origin->latitude,
                        'lng' => $s->origin->longitude,
                    ],
                    'destination' => [
                        'name' => $s->destination->name,
                        'city' => $s->destination->city,
                        'lat' => $s->destination->latitude,
                        'lng' => $s->destination->longitude,
                    ],
                    'current' => $s->currentLocation ? [
                        'name' => $s->currentLocation->name,
                        'lat' => $s->currentLocation->latitude,
                        'lng' => $s->currentLocation->longitude,
                    ] : null,
                ];
            });

        // 5. Recent Shipments Table (Slide 10 & Mockup)
        $filters = $request->only(['search', 'status', 'carrier_id', 'is_delayed']);
        $recentShipments = $this->shipmentService->getPaginatedShipments($filters, 7, $user);

        // 6. Header Notifications & Unread Count
        $notifications = $user ? $user->notifications()->latest()->limit(5)->get() : collect();
        $unreadNotificationCount = $user ? $user->notifications()->where('is_read', false)->count() : 0;

        return view('dashboard.index', compact(
            'totalShipments',
            'inTransitCount',
            'deliveredCount',
            'delayedCount',
            'openExceptionsCount',
            'statusCounts',
            'carrierDelayData',
            'averageDelayRate',
            'mapShipments',
            'recentShipments',
            'notifications',
            'unreadNotificationCount'
        ));
    }
}
