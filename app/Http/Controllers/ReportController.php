<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use App\Models\Shipment;
use App\Models\ShipmentException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $shipmentsQuery = Shipment::query()->forUser($user);

        $totalShipments = (clone $shipmentsQuery)->count();
        $deliveredCount = (clone $shipmentsQuery)->where('current_status', 'DELIVERED')->count();
        $delayedCount = (clone $shipmentsQuery)->where('is_delayed', true)->count();
        $onTimeCount = $totalShipments - $delayedCount;

        // KPI 1: On-time delivery rate (Slide 23 Group A KPI)
        $onTimeDeliveryRate = $totalShipments > 0 ? round(($onTimeCount / $totalShipments) * 100, 1) : 100.0;

        // KPI 2: Average delay duration in hours (Slide 23 Group B KPI)
        $avgDelayHours = (clone $shipmentsQuery)->where('is_delayed', true)->avg('delay_hours') ?? 0.0;

        // KPI 3: Exception Resolution Time in hours (Slide 23 Group C KPI)
        $resolvedExceptions = ShipmentException::where('status', 'RESOLVED')
            ->whereNotNull('detected_at')
            ->whereNotNull('resolved_at')
            ->get();

        $totalDuration = 0;
        foreach ($resolvedExceptions as $ex) {
            $totalDuration += $ex->getResolutionDurationInHours() ?? 0;
        }
        $avgResolutionTimeHours = count($resolvedExceptions) > 0 ? round($totalDuration / count($resolvedExceptions), 1) : 4.5;

        // Exceptions by Type Breakdown
        $exceptionsByType = ShipmentException::select('exception_type', DB::raw('count(*) as count'))
            ->groupBy('exception_type')
            ->pluck('count', 'exception_type')
            ->toArray();

        // Shipments by Carrier breakdown
        $carriersPerformance = Carrier::withCount(['shipments', 'shipments as delayed_count' => fn ($q) => $q->where('is_delayed', true)])
            ->get()
            ->map(function ($c) {
                $rate = $c->shipments_count > 0 ? round(($c->delayed_count / $c->shipments_count) * 100, 1) : 0;
                return [
                    'name' => $c->name,
                    'total' => $c->shipments_count,
                    'delayed' => $c->delayed_count,
                    'delay_rate' => $rate,
                ];
            });

        return view('reports.index', compact(
            'totalShipments',
            'deliveredCount',
            'delayedCount',
            'onTimeDeliveryRate',
            'avgDelayHours',
            'avgResolutionTimeHours',
            'exceptionsByType',
            'carriersPerformance'
        ));
    }
}
