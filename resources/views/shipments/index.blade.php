@extends('layouts.app')

@section('title', 'Shipments Directory')
@section('header', 'Shipments')

@section('content')
<div class="space-y-6">

    <!-- Top Action & Search Bar -->
    <div class="bg-navyink/90 rounded-2xl p-6 border border-steelblue/30 shadow-lg shadow-deepnavy/40 backdrop-blur-md">
        <form action="{{ route('shipments.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Keyword Search -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-bold uppercase text-steelblue mb-1.5">Search</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Shipment No, Customer, Carrier..."
                        class="w-full pl-9 pr-4 py-2 text-sm bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-oceanblue focus:border-steelblue outline-none">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-steelblue text-xs"></i>
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-xs font-bold uppercase text-steelblue mb-1.5">Status</label>
                <select name="status" class="w-full py-2 px-3 text-sm bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 focus:ring-2 focus:ring-oceanblue focus:border-steelblue outline-none">
                    <option value="" class="bg-navyink text-slate-300">All Statuses</option>
                    @foreach(['CREATED', 'BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'DELIVERED'] as $st)
                        <option value="{{ $st }}" {{ ($filters['status'] ?? '') === $st ? 'selected' : '' }} class="bg-navyink text-slate-200">{{ str_replace('_', ' ', $st) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Carrier Filter -->
            <div>
                <label class="block text-xs font-bold uppercase text-steelblue mb-1.5">Carrier</label>
                <select name="carrier_id" class="w-full py-2 px-3 text-sm bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 focus:ring-2 focus:ring-oceanblue focus:border-steelblue outline-none">
                    <option value="" class="bg-navyink text-slate-300">All Carriers</option>
                    @foreach($carriers as $carrier)
                        <option value="{{ $carrier->id }}" {{ ($filters['carrier_id'] ?? '') == $carrier->id ? 'selected' : '' }} class="bg-navyink text-slate-200">{{ $carrier->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Delay Filter -->
            <div>
                <label class="block text-xs font-bold uppercase text-steelblue mb-1.5">Delay Status</label>
                <select name="is_delayed" class="w-full py-2 px-3 text-sm bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 focus:ring-2 focus:ring-oceanblue focus:border-steelblue outline-none">
                    <option value="" class="bg-navyink text-slate-300">All Schedules</option>
                    <option value="1" {{ ($filters['is_delayed'] ?? '') === '1' ? 'selected' : '' }} class="bg-navyink text-slate-200">Delayed Only</option>
                    <option value="0" {{ ($filters['is_delayed'] ?? '') === '0' ? 'selected' : '' }} class="bg-navyink text-slate-200">On Schedule</option>
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="lg:col-span-5 flex items-center justify-between pt-3 border-t border-steelblue/20 mt-2">
                <div class="flex items-center gap-3">
                    <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-gradient-to-r from-oceanblue to-cyan-600 hover:from-oceanblue/90 hover:to-cyan-500 rounded-xl transition shadow-sm flex items-center gap-2">
                        <i class="fa-solid fa-filter text-xs"></i> Apply Filters
                    </button>
                    <a href="{{ route('shipments.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-300 hover:text-white bg-deepnavy hover:bg-slate-800 border border-steelblue/30 rounded-xl transition">
                        Reset
                    </a>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('shipments.export', request()->all()) }}" class="px-4 py-2 text-xs font-semibold text-slate-200 bg-deepnavy hover:bg-slate-800 border border-steelblue/30 rounded-xl transition flex items-center gap-1.5">
                        <i class="fa-solid fa-file-csv text-cargogold-400"></i> Export CSV
                    </a>
                    @if(auth()->user() && auth()->user()->canManageShipments())
                        <a href="{{ route('shipments.create') }}" class="px-4 py-2 text-xs font-extrabold text-deepnavy bg-gradient-to-r from-cargogold-400 to-amber-400 hover:from-cargogold-300 hover:to-amber-300 rounded-xl transition flex items-center gap-1.5 shadow-md shadow-cargogold-500/20">
                            <i class="fa-solid fa-plus text-xs"></i> Register Shipment
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Shipments Table -->
    <div class="bg-navyink/90 rounded-2xl border border-steelblue/30 shadow-xl shadow-deepnavy/50 overflow-hidden backdrop-blur-md">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-deepnavy/90 border-b border-steelblue/30 text-[11px] font-bold uppercase tracking-wider text-steelblue">
                        <th class="py-3.5 px-6">Shipment No.</th>
                        <th class="py-3.5 px-6">Customer</th>
                        <th class="py-3.5 px-6">Carrier</th>
                        <th class="py-3.5 px-6">Route (Origin &rarr; Dest)</th>
                        <th class="py-3.5 px-6">Cargo / Weight</th>
                        <th class="py-3.5 px-6">ETA</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navyink/80 text-sm">
                    @forelse($shipments as $shipment)
                        <tr class="hover:bg-oceanblue/20 transition-colors duration-100 text-slate-200">
                            <td class="py-4 px-6 font-semibold">
                                <a href="{{ route('shipments.show', $shipment->id) }}" class="text-cargogold-400 hover:text-cargogold-300 hover:underline font-mono">
                                    {{ $shipment->shipment_number }}
                                </a>
                                @if($shipment->group_scenario)
                                    <span class="block text-[10px] text-steelblue font-mono">{{ $shipment->group_scenario }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-slate-200 font-medium">
                                {{ $shipment->customer?->company_name ?? $shipment->customer?->name }}
                            </td>
                            <td class="py-4 px-6 text-slate-300">
                                {{ $shipment->carrier?->name }}
                            </td>
                            <td class="py-4 px-6 text-slate-300">
                                <div class="text-xs font-semibold text-slate-200">{{ $shipment->origin?->city }}</div>
                                <div class="text-[11px] text-slate-400">&darr; {{ $shipment->destination?->city }}</div>
                            </td>
                            <td class="py-4 px-6 text-slate-300 text-xs">
                                <span class="font-medium text-slate-200">{{ $shipment->cargo_type }}</span>
                                <span class="text-slate-400 block">{{ number_format($shipment->total_weight) }} kg ({{ $shipment->total_volume }} cbm)</span>
                            </td>
                            <td class="py-4 px-6 text-xs font-mono">
                                <span class="text-slate-200 font-medium">{{ $shipment->estimated_arrival?->format('Y-m-d') }}</span>
                                @if($shipment->is_delayed)
                                    <span class="text-rose-400 block font-bold text-[10px]">+{{ $shipment->delay_hours }}h Delay ({{ $shipment->delay_severity }})</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($shipment->is_delayed)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase bg-rose-500/20 text-rose-300 border border-rose-500/40">
                                        DELAYED
                                    </span>
                                @elseif($shipment->current_status === 'DELIVERED')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
                                        DELIVERED
                                    </span>
                                @elseif($shipment->current_status === 'IN_TRANSIT')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase bg-oceanblue/30 text-cyan-300 border border-oceanblue/50">
                                        IN TRANSIT
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase bg-deepnavy text-slate-300 border border-steelblue/30">
                                        {{ str_replace('_', ' ', $shipment->current_status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <a href="{{ route('tracking.show', $shipment->shipment_number) }}" title="Live Tracking" class="p-2 text-steelblue hover:text-cargogold-400 transition">
                                    <i class="fa-solid fa-map-location-dot"></i>
                                </a>
                                <a href="{{ route('shipments.show', $shipment->id) }}" title="Details" class="p-2 text-slate-400 hover:text-white transition">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400 text-sm">
                                <i class="fa-solid fa-box-open text-3xl mb-2 text-steelblue block"></i>
                                No shipments found for the specified filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($shipments->hasPages())
            <div class="px-6 py-4 bg-deepnavy/80 border-t border-steelblue/20 flex items-center justify-between text-slate-300">
                <span class="text-xs text-slate-400">
                    Showing {{ $shipments->firstItem() }} to {{ $shipments->lastItem() }} of {{ $shipments->total() }} shipments
                </span>
                {{ $shipments->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
