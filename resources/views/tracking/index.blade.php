@extends('layouts.app')

@section('title', 'Global Shipment Tracking')
@section('header', 'Live Tracking')

@section('content')
<div class="space-y-6">

    <!-- Search Hero Box (Matches User Palette: Deep Navy, Navy Ink, Ocean Blue, Steel Blue, Gold) -->
    <div class="bg-gradient-to-r from-navyink via-slate-900 to-deepnavy rounded-3xl p-8 sm:p-12 text-white shadow-xl shadow-deepnavy/50 border border-steelblue/40 relative overflow-hidden">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-oceanblue/50 text-cyan-300 border border-steelblue/50 mb-4">
                <i class="fa-solid fa-satellite text-xs text-cargogold-400"></i> Global Telemetry & Status Visibility
            </span>
            <h2 class="text-3xl font-black tracking-tight text-white mb-2">Track Global Consignment</h2>
            <p class="text-slate-300 text-sm mb-6">Enter any shipment identification number to view full multimodal trajectory, waypoint status, weather conditions, and exception alerts.</p>

            <form action="{{ route('tracking.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <input type="text" name="number" required placeholder="e.g. SHP-2024-001284"
                        class="w-full pl-12 pr-4 py-3.5 bg-deepnavy/90 backdrop-blur-md border border-steelblue/40 rounded-2xl text-white placeholder-slate-400 focus:outline-none focus:border-cargogold-400 focus:ring-2 focus:ring-cargogold-500/30 text-sm font-mono transition">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-4 text-cargogold-400"></i>
                </div>
                <button type="submit" class="px-8 py-3.5 bg-gradient-to-r from-cargogold-600 to-amber-500 hover:brightness-110 text-deepnavy font-black text-sm uppercase tracking-wider rounded-2xl transition shadow-lg shadow-cargogold-600/30 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-location-crosshairs text-base"></i> Track Shipment
                </button>
            </form>
        </div>

        <div class="absolute -right-12 -bottom-12 opacity-10 text-white pointer-events-none hidden lg:block">
            <i class="fa-solid fa-earth-americas text-[280px]"></i>
        </div>
    </div>

    <!-- Active In-Transit Highlights (Aligned to Ocean & Navy Palette) -->
    <div class="bg-navyink/90 rounded-2xl p-6 border border-steelblue/30 shadow-xl shadow-deepnavy/50">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-white tracking-wide">Active High-Priority Consignments</h3>
                <p class="text-xs text-duskblue">Currently in-transit across global routes</p>
            </div>
            <a href="{{ route('shipments.index', ['status' => 'IN_TRANSIT']) }}" class="text-xs text-cargogold-400 font-bold hover:text-cargogold-300 hover:underline">
                View all in-transit &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($activeShipments as $s)
                <div class="p-5 rounded-2xl border border-steelblue/30 bg-deepnavy/80 hover:bg-oceanblue/25 hover:border-cargogold-500/50 hover:shadow-lg transition group">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-mono text-xs font-bold text-cargogold-400 group-hover:underline">
                            {{ $s->shipment_number }}
                        </span>
                        @if($s->is_delayed)
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-rose-950/70 text-rose-300 border border-rose-500/30">Delayed</span>
                        @else
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-oceanblue/60 text-cyan-300 border border-cyan-500/30">In Transit</span>
                        @endif
                    </div>

                    <div class="text-xs font-bold text-slate-100 mb-1">
                        {{ $s->origin?->city }} &rarr; {{ $s->destination?->city }}
                    </div>

                    <div class="text-[11px] text-slate-400 flex items-center justify-between mt-3 pt-3 border-t border-navyink">
                        <span>Carrier: <b class="text-slate-300">{{ $s->carrier?->name }}</b></span>
                        <a href="{{ route('tracking.show', $s->shipment_number) }}" class="text-cyan-400 font-bold hover:text-cyan-300 hover:underline flex items-center gap-1 font-mono">
                            Track <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 py-8 text-center text-xs text-slate-400 font-mono">
                    No active in-transit shipments at this moment.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
