@extends('layouts.app')

@section('title', 'Global Shipment Tracking')
@section('header', 'Live Tracking')

@section('content')
<div class="space-y-6">

    <!-- Search Hero Box -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 rounded-3xl p-8 sm:p-12 text-white shadow-xl relative overflow-hidden">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 mb-4">
                <i class="fa-solid fa-satellite text-xs"></i> Global Telemetry & Status Visibility
            </span>
            <h2 class="text-3xl font-black tracking-tight text-white mb-2">Track Global Consignment</h2>
            <p class="text-slate-400 text-sm mb-6">Enter any shipment identification number to view full multimodal trajectory, waypoint status, weather conditions, and exception alerts.</p>

            <form action="{{ route('tracking.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <input type="text" name="number" required placeholder="e.g. SHP-2024-001284"
                        class="w-full pl-12 pr-4 py-3.5 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:bg-white/20 text-sm font-mono transition">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-4 text-cyan-400"></i>
                </div>
                <button type="submit" class="px-8 py-3.5 bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-sm rounded-2xl transition shadow-lg shadow-cyan-500/25 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-location-crosshairs"></i> Track Shipment
                </button>
            </form>
        </div>

        <div class="absolute -right-12 -bottom-12 opacity-10 text-white pointer-events-none hidden lg:block">
            <i class="fa-solid fa-earth-americas text-[280px]"></i>
        </div>
    </div>

    <!-- Active In-Transit Highlights -->
    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Active High-Priority Consignments</h3>
                <p class="text-xs text-slate-400">Currently in-transit across global routes</p>
            </div>
            <a href="{{ route('shipments.index', ['status' => 'IN_TRANSIT']) }}" class="text-xs text-cyan-600 font-bold hover:underline">
                View all in-transit &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($activeShipments as $s)
                <div class="p-5 rounded-2xl border border-slate-100 bg-slate-50/50 hover:bg-white hover:border-cyan-200 hover:shadow-md transition group">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-mono text-xs font-bold text-cyan-600 group-hover:underline">
                            {{ $s->shipment_number }}
                        </span>
                        @if($s->is_delayed)
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-rose-100 text-rose-700">Delayed</span>
                        @else
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-cyan-100 text-cyan-700">In Transit</span>
                        @endif
                    </div>

                    <div class="text-xs font-bold text-slate-800 mb-1">
                        {{ $s->origin?->city }} &rarr; {{ $s->destination?->city }}
                    </div>

                    <div class="text-[11px] text-slate-500 flex items-center justify-between mt-3 pt-3 border-t border-slate-200/60">
                        <span>Carrier: {{ $s->carrier?->name }}</span>
                        <a href="{{ route('tracking.show', $s->shipment_number) }}" class="text-cyan-600 font-bold hover:underline flex items-center gap-1">
                            Track <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 py-8 text-center text-xs text-slate-400">
                    No active in-transit shipments at this moment.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
