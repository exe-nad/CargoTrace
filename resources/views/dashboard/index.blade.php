@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Top Summary Stat Cards (5 Cards matching User Palette: Deep Navy, Navy Ink, Ocean Blue, Steel Blue, Gold) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
        <!-- Card 1: Total Shipments -->
        <div class="bg-gradient-to-br from-navyink to-slate-900 rounded-2xl p-5 border border-steelblue/30 shadow-xl shadow-deepnavy/40 flex items-center space-x-4 hover:border-cargogold-500/50 transition">
            <div class="w-12 h-12 rounded-xl bg-oceanblue/40 border border-oceanblue/60 text-cyan-400 flex items-center justify-center flex-shrink-0 text-xl shadow-inner">
                <i class="fa-solid fa-box"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-steelblue font-mono">Total Shipments</p>
                <h3 class="text-2xl font-black text-white mt-0.5 font-mono">{{ $totalShipments }}</h3>
            </div>
        </div>

        <!-- Card 2: In Transit -->
        <div class="bg-gradient-to-br from-navyink to-slate-900 rounded-2xl p-5 border border-steelblue/30 shadow-xl shadow-deepnavy/40 flex items-center space-x-4 hover:border-cargogold-500/50 transition">
            <div class="w-12 h-12 rounded-xl bg-steelblue/30 border border-steelblue/50 text-cyan-300 flex items-center justify-center flex-shrink-0 text-xl shadow-inner">
                <i class="fa-solid fa-truck-moving"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-steelblue font-mono">In Transit</p>
                <h3 class="text-2xl font-black text-cyan-400 mt-0.5 font-mono">{{ $inTransitCount }}</h3>
            </div>
        </div>

        <!-- Card 3: Delivered -->
        <div class="bg-gradient-to-br from-navyink to-slate-900 rounded-2xl p-5 border border-steelblue/30 shadow-xl shadow-deepnavy/40 flex items-center space-x-4 hover:border-cargogold-500/50 transition">
            <div class="w-12 h-12 rounded-xl bg-emerald-950/60 border border-emerald-500/40 text-emerald-400 flex items-center justify-center flex-shrink-0 text-xl shadow-inner">
                <i class="fa-solid fa-truck-fast"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-steelblue font-mono">Delivered</p>
                <h3 class="text-2xl font-black text-emerald-400 mt-0.5 font-mono">{{ $deliveredCount }}</h3>
            </div>
        </div>

        <!-- Card 4: Delayed -->
        <div class="bg-gradient-to-br from-navyink to-slate-900 rounded-2xl p-5 border border-steelblue/30 shadow-xl shadow-deepnavy/40 flex items-center space-x-4 hover:border-cargogold-500/50 transition">
            <div class="w-12 h-12 rounded-xl bg-amber-950/60 border border-cargogold-500/40 text-cargogold-400 flex items-center justify-center flex-shrink-0 text-xl shadow-inner">
                <i class="fa-regular fa-clock"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-cargogold-400 font-mono">Delayed</p>
                <h3 class="text-2xl font-black text-cargogold-400 mt-0.5 font-mono">{{ $delayedCount }}</h3>
            </div>
        </div>

        <!-- Card 5: Open Exceptions -->
        <div class="bg-gradient-to-br from-navyink to-slate-900 rounded-2xl p-5 border border-rose-500/30 shadow-xl shadow-deepnavy/40 flex items-center space-x-4 hover:border-rose-500/60 transition">
            <div class="w-12 h-12 rounded-xl bg-rose-950/60 border border-rose-500/40 text-rose-400 flex items-center justify-center flex-shrink-0 text-xl shadow-inner">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-rose-400 font-mono">Open Exceptions</p>
                <h3 class="text-2xl font-black text-rose-400 mt-0.5 font-mono">{{ $openExceptionsCount }}</h3>
            </div>
        </div>
    </div>

    <!-- Middle Section: 3 Cards (Status Bar Chart, Delay Doughnut, Shipment Map) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Card 1: Shipments by Status (Col span 4) -->
        <div class="lg:col-span-4 bg-navyink/90 rounded-2xl p-6 border border-steelblue/30 shadow-xl shadow-deepnavy/50 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1">
                    <h3 class="text-base font-bold text-white tracking-wide">Shipments by Status</h3>
                </div>
                <p class="text-xs text-duskblue mb-4">Overview of shipments by operational status</p>
            </div>

            <!-- Legend Pills -->
            <div class="flex flex-wrap items-center gap-2 mb-3 text-[11px] font-medium text-slate-300">
                <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-cyan-500"></span> In Transit ({{ $inTransitCount }})</span>
                <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-emerald-500"></span> Delivered ({{ $deliveredCount }})</span>
                <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-amber-500"></span> Delayed ({{ $delayedCount }})</span>
                <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-rose-500"></span> Exceptions ({{ $openExceptionsCount }})</span>
            </div>

            <div class="relative h-52 w-full">
                <canvas id="shipmentsStatusChart"></canvas>
            </div>

            <div class="mt-4 pt-3 border-t border-navyink flex items-center justify-between">
                <span class="text-xs text-duskblue font-mono">Total Cataloged</span>
                <span class="px-2.5 py-1 bg-deepnavy text-cyan-400 font-bold text-xs rounded-lg border border-steelblue/30 font-mono">
                    {{ $totalShipments }} Total Shipments
                </span>
            </div>
        </div>

        <!-- Card 2: Delay Rate by Carrier (Col span 4) -->
        <div class="lg:col-span-4 bg-navyink/90 rounded-2xl p-6 border border-steelblue/30 shadow-xl shadow-deepnavy/50 flex flex-col justify-between">
            <div>
                <h3 class="text-base font-bold text-white tracking-wide">Delay Rate by Carrier</h3>
                <p class="text-xs text-duskblue mb-2">Distribution of delay rates across carriers</p>
            </div>

            <div class="relative flex items-center justify-center my-auto py-2">
                <div class="w-48 h-48 relative">
                    <canvas id="delayRateCarrierChart"></canvas>
                    <!-- Centered metric text -->
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-2xl font-black text-white font-mono tracking-tight">{{ $averageDelayRate }}%</span>
                        <span class="text-[10px] uppercase font-bold text-steelblue font-mono">Avg Delay Rate</span>
                    </div>
                </div>
            </div>

            <div class="space-y-2 pt-2 border-t border-navyink">
                @foreach(array_slice($carrierDelayData, 0, 3) as $index => $c)
                    @php
                        $colorClass = $index === 0 ? 'bg-cyan-500' : ($index === 1 ? 'bg-cargogold-500' : 'bg-steelblue');
                        $rate = $delayedCount > 0 ? round(($c['delayed_count'] / $delayedCount) * 100) : 0;
                    @endphp
                    <div class="flex items-center justify-between text-xs">
                        <span class="flex items-center gap-2 text-slate-300">
                            <span class="w-2.5 h-2.5 rounded-sm {{ $colorClass }}"></span>
                            {{ $c['name'] }}
                        </span>
                        <span class="font-bold text-white font-mono">{{ $rate }}%</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Card 3: Shipment Map (Col span 4) -->
        <div class="lg:col-span-4 bg-navyink/90 rounded-2xl p-6 border border-steelblue/30 shadow-xl shadow-deepnavy/50 flex flex-col">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-base font-bold text-white tracking-wide">Shipment Map</h3>
                <a href="{{ route('tracking.index') }}" class="text-xs text-cyan-400 hover:text-cyan-300 font-semibold flex items-center gap-1 transition">
                    Expand <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                </a>
            </div>
            <p class="text-xs text-duskblue mb-3">Real-time shipment routes and active GPS vectors</p>

            <div class="relative flex-1 min-h-[220px] rounded-xl overflow-hidden border border-steelblue/30 z-10">
                <div id="dashboardMiniMap" class="w-full h-full min-h-[220px]"></div>
            </div>
        </div>

    </div>

    <!-- Bottom Section: Recent Shipments (Matching Mockup Table) -->
    <div class="bg-navyink/90 rounded-2xl border border-steelblue/30 shadow-xl shadow-deepnavy/50 overflow-hidden">
        <!-- Table Header & Controls -->
        <div class="px-6 py-5 border-b border-navyink flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-white tracking-tight">Recent Shipments</h3>
                <p class="text-xs text-duskblue mt-0.5">Live overview of all recorded consignments</p>
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ route('shipments.index') }}" class="px-3.5 py-2 text-xs font-semibold text-slate-200 bg-deepnavy/90 hover:bg-oceanblue/40 border border-steelblue/30 rounded-xl transition flex items-center gap-2">
                    <i class="fa-solid fa-filter text-steelblue"></i>
                    Filter
                </a>
                <a href="{{ route('shipments.export') }}" class="px-3.5 py-2 text-xs font-bold text-deepnavy bg-gradient-to-r from-cargogold-600 to-amber-500 hover:brightness-110 rounded-xl transition flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-download text-xs"></i>
                    Export
                </a>
            </div>
        </div>

        <!-- Table Responsive -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-deepnavy/90 border-b border-navyink text-[11px] font-bold uppercase tracking-wider text-steelblue">
                        <th class="py-3.5 px-6">Shipment No.</th>
                        <th class="py-3.5 px-6">Customer</th>
                        <th class="py-3.5 px-6">Carrier</th>
                        <th class="py-3.5 px-6">Origin</th>
                        <th class="py-3.5 px-6">Destination</th>
                        <th class="py-3.5 px-6">ETA</th>
                        <th class="py-3.5 px-6 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navyink/80 text-sm text-slate-200">
                    @forelse($recentShipments as $shipment)
                        <tr class="hover:bg-oceanblue/20 transition-colors duration-100 group">
                            <td class="py-4 px-6 font-semibold font-mono">
                                <a href="{{ route('tracking.show', $shipment->shipment_number) }}" class="text-cargogold-400 hover:text-cargogold-300 hover:underline flex items-center gap-1.5">
                                    {{ $shipment->shipment_number }}
                                </a>
                            </td>
                            <td class="py-4 px-6 text-slate-200 font-medium">
                                {{ $shipment->customer?->company_name ?? $shipment->customer?->name }}
                            </td>
                            <td class="py-4 px-6 text-slate-300">
                                {{ $shipment->carrier?->name }}
                            </td>
                            <td class="py-4 px-6 text-slate-300">
                                {{ $shipment->origin?->city }}, {{ $shipment->origin?->state ?? $shipment->origin?->country }}
                            </td>
                            <td class="py-4 px-6 text-slate-300">
                                {{ $shipment->destination?->city }}, {{ $shipment->destination?->state ?? $shipment->destination?->country }}
                            </td>
                            <td class="py-4 px-6 text-slate-400 text-xs font-mono">
                                {{ $shipment->estimated_arrival?->format('Y-m-d') }}
                            </td>
                            <td class="py-4 px-6 text-right">
                                @if($shipment->is_delayed)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider bg-rose-600 text-white shadow-sm">
                                        DELAYED
                                    </span>
                                @elseif($shipment->current_status === 'DELIVERED')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider bg-emerald-600 text-white shadow-sm">
                                        DELIVERED
                                    </span>
                                @elseif($shipment->current_status === 'IN_TRANSIT')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider bg-cyan-700 text-white shadow-sm">
                                        IN TRANSIT
                                    </span>
                                @elseif($shipment->current_status === 'PICKED_UP')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider bg-blue-600 text-white shadow-sm">
                                        PICKED UP
                                    </span>
                                @elseif($shipment->current_status === 'BOOKED')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider bg-slate-700 text-white shadow-sm">
                                        BOOKED
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider bg-slate-600 text-white">
                                        {{ $shipment->current_status }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400 text-sm font-mono">
                                No shipments found matching the query.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        @if($recentShipments->hasPages())
            <div class="px-6 py-4 bg-deepnavy/90 border-t border-navyink flex items-center justify-between text-xs text-slate-400">
                <span>
                    Showing {{ $recentShipments->firstItem() }} to {{ $recentShipments->lastItem() }} of {{ $recentShipments->total() }} shipments
                </span>
                {{ $recentShipments->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Chart: Shipments by Status (Bar Chart)
        const ctxStatus = document.getElementById('shipmentsStatusChart').getContext('2d');
        new Chart(ctxStatus, {
            type: 'bar',
            data: {
                labels: ['In Transit', 'Delivered', 'Delayed', 'Exceptions'],
                datasets: [{
                    data: [
                        {{ $statusCounts['in_transit'] }},
                        {{ $statusCounts['delivered'] }},
                        {{ $statusCounts['delayed'] }},
                        {{ $statusCounts['open_exceptions'] }}
                    ],
                    backgroundColor: [
                        '#06b6d4', // cyan
                        '#10b981', // emerald
                        '#f59e0b', // amber
                        '#f43f5e'  // rose
                    ],
                    borderRadius: 6,
                    barThickness: 28,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#94a3b8', font: { size: 10 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(58, 124, 165, 0.15)' },
                        ticks: { color: '#94a3b8', font: { size: 10 } }
                    }
                }
            }
        });

        // 2. Chart: Delay Rate by Carrier (Doughnut Chart)
        const ctxCarrier = document.getElementById('delayRateCarrierChart').getContext('2d');
        @php
            $carrierLabels = array_map(fn($c) => $c['name'], array_slice($carrierDelayData, 0, 3));
            $carrierValues = array_map(fn($c) => $c['delayed_count'], array_slice($carrierDelayData, 0, 3));
        @endphp
        new Chart(ctxCarrier, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($carrierLabels) !!},
                datasets: [{
                    data: {!! json_encode($carrierValues) !!},
                    backgroundColor: [
                        '#06b6d4', // cyan
                        '#d4af37', // gold
                        '#4682b4'  // steel blue
                    ],
                    borderWidth: 0,
                    cutout: '72%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                }
            }
        });

        // 3. Mini Leaflet Map Preview
        const map = L.map('dashboardMiniMap', {
            zoomControl: false,
            attributionControl: false
        }).setView([37.0902, -95.7129], 3);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
        }).addTo(map);

        L.control.zoom({ position: 'bottomright' }).addTo(map);

        // Plot preview markers A, B, C matching screenshot
        const pinA = L.marker([33.74318, -118.26725]).addTo(map).bindPopup("<b>Point A: Los Angeles Hub</b><br>Status: Dispatched");
        const pinB = L.marker([32.77666, -96.79699]).addTo(map).bindPopup("<b>Point B: Dallas Interchange</b><br>Status: Checkpoint");
        const pinC = L.marker([41.87811, -87.62980]).addTo(map).bindPopup("<b>Point C: Chicago Destination</b><br>Status: Expected Inbound");

        // Route polyline connecting A -> B -> C
        const routeCoords = [
            [33.74318, -118.26725],
            [35.0, -110.0],
            [32.77666, -96.79699],
            [38.0, -92.0],
            [41.87811, -87.62980]
        ];
        L.polyline(routeCoords, {
            color: '#0284c7',
            weight: 3.5,
            opacity: 0.85,
            dashArray: '6, 6'
        }).addTo(map);
    });
</script>
@endpush
