@extends('layouts.app')

@section('title', 'Operational Analytics & Reports')
@section('header', 'Reports & Logistics KPIs')

@section('content')
<div class="space-y-6">

    <!-- KPI Summary Cards (Slide 23 Group A, B, C KPIs) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <!-- KPI 1: On-Time Delivery Rate (Group A KPI) -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Group A KPI: On-Time Delivery</span>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-slate-900">{{ $onTimeDeliveryRate }}%</span>
                <span class="text-xs font-semibold text-emerald-600 flex items-center gap-1">
                    <i class="fa-solid fa-arrow-trend-up"></i> High Reliability
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-2">Percentage of total shipments meeting or arriving ahead of expected SLA arrival.</p>
        </div>

        <!-- KPI 2: Average Delay Duration (Group B KPI) -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Group B KPI: Avg Delay Duration</span>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-amber-600">{{ round($avgDelayHours, 1) }}h</span>
                <span class="text-xs font-semibold text-slate-500">per delayed unit</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">Average transit delay variance between expected arrival and adjusted ETA.</p>
        </div>

        <!-- KPI 3: Exception Resolution Time (Group C KPI) -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Group C KPI: Exception Resolution</span>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-cyan-600">{{ $avgResolutionTimeHours }}h</span>
                <span class="text-xs font-semibold text-slate-500">mean time to resolve</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">Average duration from exception detection to operational status sign-off.</p>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Chart 1: Exception Root Causes Breakdown -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900 mb-1">Exception Root Cause Distribution</h3>
                <p class="text-xs text-slate-400 mb-4">Breakdown by operational disruption category</p>
            </div>

            <div class="relative h-64 w-full">
                <canvas id="exceptionsChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Carrier Fleet Volume & Reliability -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900 mb-1">Carrier Reliability Matrix</h3>
                <p class="text-xs text-slate-400 mb-4">Shipment volume vs delay incident count</p>
            </div>

            <div class="relative h-64 w-full">
                <canvas id="carrierReliabilityChart"></canvas>
            </div>
        </div>

    </div>

    <!-- Carrier Performance Table -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Partner Carrier Performance Table</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-50 text-slate-400 uppercase font-bold">
                    <tr>
                        <th class="py-3 px-6">Carrier Partner</th>
                        <th class="py-3 px-6 text-center">Assigned Consignments</th>
                        <th class="py-3 px-6 text-center">Delayed Consignments</th>
                        <th class="py-3 px-6 text-right">Delay Rate (%)</th>
                        <th class="py-3 px-6 text-right">Reliability Rating</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($carriersPerformance as $cp)
                        <tr>
                            <td class="py-3.5 px-6 font-bold text-slate-900">{{ $cp['name'] }}</td>
                            <td class="py-3.5 px-6 text-center text-slate-700 font-semibold">{{ $cp['total'] }}</td>
                            <td class="py-3.5 px-6 text-center text-rose-600 font-bold">{{ $cp['delayed'] }}</td>
                            <td class="py-3.5 px-6 text-right font-black {{ $cp['delay_rate'] > 20 ? 'text-rose-600' : 'text-emerald-600' }}">
                                {{ $cp['delay_rate'] }}%
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                @if($cp['delay_rate'] < 15)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-100 text-emerald-800">Tier A+</span>
                                @elseif($cp['delay_rate'] < 30)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-cyan-100 text-cyan-800">Tier A</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-100 text-amber-800">Review</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Exception Root Causes
        const ctxEx = document.getElementById('exceptionsChart').getContext('2d');
        const exLabels = {!! json_encode(array_keys($exceptionsByType)) !!}.map(l => l.replace(/_/g, ' '));
        const exValues = {!! json_encode(array_values($exceptionsByType)) !!};

        new Chart(ctxEx, {
            type: 'bar',
            data: {
                labels: exLabels,
                datasets: [{
                    label: 'Incidents',
                    data: exValues,
                    backgroundColor: '#0891b2',
                    borderRadius: 6
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } },
                    y: { grid: { display: false }, ticks: { font: { size: 10 } } }
                }
            }
        });

        // 2. Carrier Reliability Chart
        const ctxCarrier = document.getElementById('carrierReliabilityChart').getContext('2d');
        const cLabels = {!! json_encode($carriersPerformance->pluck('name')) !!};
        const cTotals = {!! json_encode($carriersPerformance->pluck('total')) !!};
        const cDelayed = {!! json_encode($carriersPerformance->pluck('delayed')) !!};

        new Chart(ctxCarrier, {
            type: 'bar',
            data: {
                labels: cLabels,
                datasets: [
                    { label: 'Total Shipments', data: cTotals, backgroundColor: '#0f172a', borderRadius: 4 },
                    { label: 'Delayed Units', data: cDelayed, backgroundColor: '#f43f5e', borderRadius: 4 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }
                }
            }
        });
    });
</script>
@endpush
