@extends('layouts.app')

@section('title', 'Tracking #' . $shipment->shipment_number)
@section('header', 'Shipment Telemetry')

@section('content')
<div class="space-y-6">

    <!-- Header & Status Bar -->
    <div class="bg-navyink/90 rounded-2xl p-6 border border-steelblue/30 shadow-lg shadow-deepnavy/40 flex flex-col md:flex-row md:items-center md:justify-between gap-4 backdrop-blur-md">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-black text-cargogold-400 tracking-tight font-mono">{{ $shipment->shipment_number }}</h2>
                @if($shipment->is_delayed)
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-rose-500/20 text-rose-300 border border-rose-500/40 shadow-sm flex items-center gap-1.5">
                        <i class="fa-solid fa-triangle-exclamation"></i> DELAYED (+{{ $shipment->delay_hours }}h)
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-oceanblue/30 text-cyan-300 border border-oceanblue/50 shadow-sm flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-check"></i> {{ str_replace('_', ' ', $shipment->current_status) }}
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-400 mt-1">
                Carrier: <span class="font-semibold text-slate-200">{{ $shipment->carrier?->name }}</span> &bull;
                Consignee: <span class="font-semibold text-slate-200">{{ $shipment->customer?->company_name ?? $shipment->customer?->name }}</span>
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('tracking.print', $shipment->shipment_number) }}" target="_blank"
                class="px-4 py-2.5 text-xs font-bold text-white bg-oceanblue hover:bg-oceanblue/80 border border-steelblue/40 rounded-xl transition shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-print text-cargogold-400"></i> Print Waybill
            </a>
            @if(auth()->user() && auth()->user()->canManageShipments())
                <button type="button" onclick="document.getElementById('checkpointModal').classList.remove('hidden')"
                    class="px-4 py-2.5 text-xs font-bold text-white bg-deepnavy hover:bg-slate-900 border border-steelblue/40 rounded-xl transition shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-plus text-xs text-cargogold-400"></i> Log Checkpoint
                </button>
            @endif
            <a href="{{ route('shipments.show', $shipment->id) }}" class="px-4 py-2.5 text-xs font-semibold text-slate-200 bg-deepnavy/80 hover:bg-slate-800 border border-steelblue/30 rounded-xl transition">
                Shipment Details &rarr;
            </a>
        </div>
    </div>

    <!-- Map & Weather Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Map Section (Col span 8) -->
        <div class="lg:col-span-8 bg-navyink/90 rounded-2xl p-6 border border-steelblue/30 shadow-lg shadow-deepnavy/40 flex flex-col backdrop-blur-md">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-white">Multimodal Route & Checkpoint Trajectory</h3>
                    <p class="text-xs text-slate-400">OpenStreetMap & Leaflet.js live interactive geo-mapping</p>
                </div>
                <div class="flex items-center gap-2 text-xs font-medium text-slate-300">
                    <span class="inline-flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-blue-500"></span> Origin</span>
                    <span class="inline-flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-cyan-400"></span> Waypoint</span>
                    <span class="inline-flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-emerald-400"></span> Destination</span>
                </div>
            </div>

            <!-- Leaflet Container -->
            <div class="relative w-full h-[450px] rounded-2xl overflow-hidden border border-steelblue/40 shadow-inner z-10">
                <div id="trackingFullMap" class="w-full h-full"></div>
            </div>
        </div>

        <!-- Ambient Weather & Telemetry (Col span 4) -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Live Weather Widget via External Open-Meteo API (Slide 9) -->
            <div class="bg-gradient-to-br from-cyan-900 via-slate-900 to-slate-900 rounded-2xl p-6 text-white border border-slate-800 shadow-lg relative overflow-hidden">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs uppercase tracking-wider font-bold text-cyan-400 flex items-center gap-1.5">
                        <i class="fa-solid fa-cloud-sun"></i> Ambient Meteorological Data
                    </span>
                    <span class="text-[10px] text-slate-400 font-mono">Live API</span>
                </div>

                @if($weatherData)
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <span class="text-4xl font-black text-white">{{ $weatherData['temperature'] ?? '--' }}&deg;C</span>
                            <span class="text-xs text-cyan-200 block font-semibold mt-1">{{ $weatherData['weather_condition'] }}</span>
                        </div>
                        <div class="text-4xl text-cyan-400">
                            @if(str_contains(strtolower($weatherData['weather_condition']), 'rain'))
                                <i class="fa-solid fa-cloud-showers-heavy"></i>
                            @elseif(str_contains(strtolower($weatherData['weather_condition']), 'cloud'))
                                <i class="fa-solid fa-cloud-sun"></i>
                            @elseif(str_contains(strtolower($weatherData['weather_condition']), 'thunder'))
                                <i class="fa-solid fa-cloud-bolt text-amber-400"></i>
                            @else
                                <i class="fa-solid fa-sun text-amber-400"></i>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-3 border-t border-slate-800 text-xs text-slate-300">
                        <div>
                            <span class="text-slate-400 text-[10px] uppercase block">Wind Speed</span>
                            <span class="font-bold text-white">{{ $weatherData['wind_speed'] ?? '--' }} km/h</span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] uppercase block">Humidity</span>
                            <span class="font-bold text-white">{{ $weatherData['humidity'] ?? '--' }}%</span>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-800/80 text-[10px] text-slate-400">
                        <i class="fa-solid fa-network-wired text-cyan-400 mr-1"></i>
                        Source: {{ $weatherData['source'] }}
                        @if($weatherData['is_fallback'])
                            (Fallback safe)
                        @endif
                    </div>
                @else
                    <div class="py-6 text-center text-xs text-slate-400">
                        Weather telemetry pending coordinates sync.
                    </div>
                @endif
            </div>

            <!-- Route Coordinates Card -->
            <div class="bg-navyink/90 rounded-2xl p-6 border border-steelblue/30 shadow-lg shadow-deepnavy/40 space-y-4 backdrop-blur-md">
                <h4 class="text-xs font-bold uppercase tracking-wider text-steelblue flex items-center gap-2">
                    <i class="fa-solid fa-compass text-cargogold-400"></i> Route Waypoint Coordinates
                </h4>
                <div class="space-y-3 text-xs">
                    <div class="flex items-start justify-between pb-2.5 border-b border-navyink/80">
                        <div>
                            <span class="font-bold text-slate-200 block">Origin: {{ $shipment->origin?->city }}</span>
                            <span class="text-[11px] text-slate-400">{{ $shipment->origin?->name }}</span>
                        </div>
                        <span class="font-mono text-[11px] text-steelblue bg-deepnavy/90 border border-steelblue/30 px-2 py-0.5 rounded">
                            {{ $shipment->origin?->latitude }}, {{ $shipment->origin?->longitude }}
                        </span>
                    </div>

                    <div class="flex items-start justify-between pb-2.5 border-b border-navyink/80">
                        <div>
                            <span class="font-bold text-slate-200 block">Current Location</span>
                            <span class="text-[11px] text-slate-400">{{ $shipment->currentLocation?->name ?? 'En Route' }}</span>
                        </div>
                        <span class="font-mono text-[11px] text-cyan-300 bg-oceanblue/20 border border-oceanblue/40 px-2 py-0.5 rounded">
                            {{ $shipment->currentLocation?->latitude ?? '-' }}, {{ $shipment->currentLocation?->longitude ?? '-' }}
                        </span>
                    </div>

                    <div class="flex items-start justify-between">
                        <div>
                            <span class="font-bold text-slate-200 block">Destination: {{ $shipment->destination?->city }}</span>
                            <span class="text-[11px] text-slate-400">{{ $shipment->destination?->name }}</span>
                        </div>
                        <span class="font-mono text-[11px] text-steelblue bg-deepnavy/90 border border-steelblue/30 px-2 py-0.5 rounded">
                            {{ $shipment->destination?->latitude }}, {{ $shipment->destination?->longitude }}
                        </span>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Operational Incident & Resolution Playbook (Slide 8 & Exception Handling) -->
    @if($shipment->exceptions->where('is_resolved', false)->count() > 0 || $shipment->is_delayed)
        <div class="bg-gradient-to-r from-rose-900/90 via-slate-900 to-slate-900 rounded-2xl p-6 border border-rose-500/30 text-white shadow-xl">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-rose-500/20">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center text-lg border border-rose-500/40 animate-pulse">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-white tracking-wide">Operational Incident Alert & Resolution Playbook</h3>
                        <p class="text-xs text-rose-200/80">Active exceptions detected. Immediate operational intervention required according to SOP.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('exceptions.index', ['shipment_id' => $shipment->id]) }}"
                        class="px-3.5 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 rounded-xl transition shadow flex items-center gap-1.5">
                        <i class="fa-solid fa-screwdriver-wrench"></i> Manage Incident Tickets
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-5">
                @foreach($shipment->exceptions->where('is_resolved', false) as $exc)
                    <div class="bg-slate-950/60 rounded-xl p-4 border border-rose-500/20">
                        <div class="flex items-center justify-between mb-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider
                                {{ $exc->severity === 'CRITICAL' ? 'bg-rose-500 text-white' : ($exc->severity === 'HIGH' ? 'bg-amber-500 text-slate-900' : 'bg-slate-700 text-white') }}">
                                {{ $exc->severity }} &bull; {{ str_replace('_', ' ', $exc->exception_type) }}
                            </span>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $exc->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-slate-200 font-semibold mb-2">{{ $exc->description }}</p>

                        <!-- Standard Operating Procedure (SOP) based on exception type -->
                        <div class="mt-3 pt-2.5 border-t border-slate-800 text-[11px] space-y-1">
                            <span class="text-rose-400 font-bold block uppercase tracking-wider text-[10px]">
                                <i class="fa-solid fa-clipboard-check mr-1"></i> Resolution SOP:
                            </span>
                            @if($exc->exception_type === 'CUSTOMS_HOLD')
                                <p class="text-slate-300">1. Audit Harmonized System (HS) codes against customs declaration.<br>2. Submit verified Commercial Invoice to destination Customs Broker.<br>3. Request priority physical inspection clearance.</p>
                            @elseif($exc->exception_type === 'WEATHER_DELAY')
                                <p class="text-slate-300">1. Cross-reference Open-Meteo marine barometric telemetry.<br>2. Authorize secondary southern maritime detour or rail transfer.<br>3. Recalculate dynamic SLA arrival date (+24-48h).</p>
                            @elseif($exc->exception_type === 'PORT_CONGESTION')
                                <p class="text-slate-300">1. Reallocate container to Off-Dock Container Yard (ODCY).<br>2. Secure dedicated night-gate pickup appointment with terminal dispatch.<br>3. Inform consignee of revised offloading window.</p>
                            @elseif($exc->exception_type === 'DAMAGED')
                                <p class="text-slate-300">1. Freeze custody chain & order certified marine cargo survey.<br>2. Take photographic evidence of seal and container exterior.<br>3. File preliminary insurance claim within 72h window.</p>
                            @elseif($exc->exception_type === 'MISROUTED')
                                <p class="text-slate-300">1. Flag container in carrier EDI tracking network.<br>2. Coordinate emergency intermodal transfer back to primary route.<br>3. Issue expedited dispatch priority waiver.</p>
                            @else
                                <p class="text-slate-300">1. Verify Bill of Lading (B/L) against commercial invoice.<br>2. Correct consignee Tax/EORI registration number.<br>3. Re-upload amended manifest via carrier EDI portal.</p>
                            @endif
                        </div>
                    </div>
                @endforeach

                @if($shipment->exceptions->where('is_resolved', false)->isEmpty() && $shipment->is_delayed)
                    <div class="bg-slate-950/60 rounded-xl p-4 border border-amber-500/30 col-span-full">
                        <div class="flex items-center gap-2 text-amber-400 font-bold text-xs mb-1">
                            <i class="fa-solid fa-clock-rotate-left"></i> Transit Delay Alert (+{{ $shipment->delay_hours }} Hours)
                        </div>
                        <p class="text-xs text-slate-300">
                            The current shipment ETA has exceeded the baseline SLA threshold. Suggested action: Notify the consignee ({{ $shipment->customer?->name }}), verify carrier vessel telematics, and log an incident checkpoint if necessary.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Vertical Timeline Section (Slide 7: "Shipment Timeline, urutkan kronologis, bedakan event normal dan exception") -->
    <div class="bg-navyink/90 rounded-2xl p-8 border border-steelblue/30 shadow-lg shadow-deepnavy/40 backdrop-blur-md">
        <div class="flex items-center justify-between mb-8 pb-4 border-b border-steelblue/20">
            <div>
                <h3 class="text-lg font-bold text-white tracking-tight">Milestone & Tracking Event Timeline</h3>
                <p class="text-xs text-slate-400 mt-0.5">Chronologically sorted immutable event log</p>
            </div>
            <span class="text-xs font-semibold px-3 py-1 bg-deepnavy/90 border border-steelblue/30 text-steelblue rounded-full">
                {{ $shipment->trackingEvents->count() }} Recorded Checkpoints
            </span>
        </div>

        <div class="relative pl-6 space-y-8 before:absolute before:left-3 before:top-2 before:bottom-2 before:w-0.5 before:bg-steelblue/30">
            @forelse($shipment->trackingEvents as $index => $event)
                @php
                    $isException = $event->is_exception;
                    $dotColor = $isException ? 'bg-rose-500 ring-rose-900/60' : 'bg-oceanblue ring-oceanblue/40';
                @endphp
                <div class="relative flex items-start group">
                    <!-- Timeline Dot -->
                    <div class="absolute -left-[27px] top-1 w-4 h-4 rounded-full {{ $dotColor }} ring-4 ring-deepnavy shadow-sm flex items-center justify-center">
                    </div>

                    <!-- Event Content Card -->
                    <div class="flex-1 bg-deepnavy/90 hover:bg-deepnavy border border-steelblue/30 rounded-2xl p-5 transition shadow-sm">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 mb-2">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-sm text-slate-100">{{ $event->event_type }}</span>
                                @if($isException)
                                    <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/40">Exception Event</span>
                                @endif
                                @if($event->status)
                                    <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-full bg-navyink text-steelblue border border-steelblue/30">
                                        {{ str_replace('_', ' ', $event->status) }}
                                    </span>
                                @endif
                            </div>
                            <span class="text-xs font-mono text-slate-400">
                                <i class="fa-regular fa-clock mr-1 text-cargogold-400"></i>
                                {{ $event->event_time?->format('M d, Y &bull; H:i') }} ({{ $event->event_time?->diffForHumans() }})
                            </span>
                        </div>

                        <p class="text-xs text-slate-300 leading-relaxed">{{ $event->description }}</p>

                        <div class="mt-3 pt-2.5 border-t border-steelblue/20 flex flex-wrap items-center justify-between text-[11px] text-slate-400 gap-2">
                            <span>
                                <i class="fa-solid fa-location-dot text-oceanblue mr-1"></i>
                                {{ $event->location_name ?? ($event->location?->name ?? 'En Route Transit') }}
                            </span>
                            @if($event->creator)
                                <span class="text-slate-400">Recorded by: <span class="text-slate-300 font-medium">{{ $event->creator->name }}</span></span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-6 text-center text-xs text-slate-400">
                    No tracking events recorded yet.
                </div>
            @endforelse
        </div>
    </div>

</div>

<!-- Modal: Add Tracking Checkpoint (Slide 7) -->
<div id="checkpointModal" class="hidden fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-navyink/95 rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-steelblue/40 text-slate-100">
        <div class="flex items-center justify-between pb-3 border-b border-steelblue/30 mb-4">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-location-crosshairs text-cargogold-400"></i> Record Tracking Checkpoint
            </h3>
            <button type="button" onclick="document.getElementById('checkpointModal').classList.add('hidden')" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="{{ route('tracking.event.store', $shipment->id) }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase text-steelblue mb-1">Select Registered Location</label>
                <select name="location_id" class="w-full py-2.5 px-3 text-xs bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 outline-none focus:ring-2 focus:ring-oceanblue">
                    <option value="" class="bg-navyink text-slate-300">Or enter custom location coordinates below</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" class="bg-navyink text-slate-200">{{ $loc->name }} ({{ $loc->city }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold uppercase text-steelblue mb-1">Event Type *</label>
                    <select name="event_type" required class="w-full py-2.5 px-3 text-xs bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 outline-none focus:ring-2 focus:ring-oceanblue">
                        <option value="CHECKPOINT" class="bg-navyink text-slate-200">CHECKPOINT</option>
                        <option value="DEPARTURE" class="bg-navyink text-slate-200">DEPARTURE</option>
                        <option value="ARRIVAL" class="bg-navyink text-slate-200">ARRIVAL</option>
                        <option value="CUSTOMS_INSPECTION" class="bg-navyink text-slate-200">CUSTOMS INSPECTION</option>
                        <option value="EXCEPTION" class="bg-navyink text-slate-200">EXCEPTION TRIGGER</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-steelblue mb-1">Lifecycle Status</label>
                    <select name="status" class="w-full py-2.5 px-3 text-xs bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 outline-none focus:ring-2 focus:ring-oceanblue">
                        <option value="" class="bg-navyink text-slate-300">Keep current ({{ $shipment->current_status }})</option>
                        @foreach(\App\Models\Shipment::STATUS_FLOW[$shipment->current_status] ?? [] as $st)
                            <option value="{{ $st }}" class="bg-navyink text-slate-200">Advance to {{ str_replace('_', ' ', $st) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold uppercase text-steelblue mb-1">Custom Latitude</label>
                    <input type="number" step="any" name="latitude" placeholder="e.g. 34.0522"
                        class="w-full py-2 px-3 text-xs bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 placeholder-slate-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-steelblue mb-1">Custom Longitude</label>
                    <input type="number" step="any" name="longitude" placeholder="e.g. -118.2437"
                        class="w-full py-2 px-3 text-xs bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 placeholder-slate-500 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-steelblue mb-1">Event Timestamp *</label>
                <input type="datetime-local" name="event_time" value="{{ now()->format('Y-m-d\TH:i') }}" required
                    class="w-full py-2 px-3 text-xs bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-steelblue mb-1">Checkpoint Description *</label>
                <textarea name="description" rows="2" required placeholder="Vessel reached intermediate waterway checkpoint..."
                    class="w-full py-2 px-3 text-xs bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 placeholder-slate-500 outline-none"></textarea>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_exception" value="1" id="is_ex_check" class="rounded text-oceanblue bg-deepnavy border-steelblue/40">
                <label for="is_ex_check" class="text-xs text-slate-300 font-semibold">Flag as an operational exception checkpoint</label>
            </div>

            <div class="pt-3 border-t border-steelblue/30 flex items-center justify-end gap-2">
                <button type="button" onclick="document.getElementById('checkpointModal').classList.add('hidden')"
                    class="px-4 py-2 text-xs font-semibold text-slate-300 bg-deepnavy hover:bg-slate-800 border border-steelblue/30 rounded-xl">Cancel</button>
                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-gradient-to-r from-oceanblue to-cyan-600 hover:from-oceanblue/90 hover:to-cyan-500 rounded-xl transition shadow">
                    Save Checkpoint
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const waypoints = {!! json_encode($waypoints) !!};

        if (waypoints.length === 0) return;

        // Initialize Leaflet map
        const map = L.map('trackingFullMap').setView([waypoints[0].lat, waypoints[0].lng], 4);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const latLngs = [];

        waypoints.forEach((wp, i) => {
            latLngs.push([wp.lat, wp.lng]);

            let markerColor = '#0284c7'; // cyan
            if (wp.type === 'ORIGIN') markerColor = '#2563eb'; // blue
            if (wp.type === 'DESTINATION') markerColor = '#10b981'; // green
            if (wp.type === 'EXCEPTION') markerColor = '#f43f5e'; // red

            const circleMarker = L.circleMarker([wp.lat, wp.lng], {
                radius: i === 0 || i === waypoints.length - 1 ? 8 : 6,
                fillColor: markerColor,
                color: '#ffffff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.9
            }).addTo(map);

            circleMarker.bindPopup(`
                <div style="font-family: sans-serif; font-size: 12px; line-height: 1.4;">
                    <b style="color: ${markerColor}">${wp.label}</b><br>
                    <span>${wp.city || ''}</span><br>
                    <span style="color: #64748b; font-size: 11px;">Time: ${wp.time || 'N/A'}</span>
                    ${wp.desc ? `<p style="margin-top: 4px; font-size: 11px;">${wp.desc}</p>` : ''}
                </div>
            `);
        });

        // Draw polyline connecting all waypoints
        const polyline = L.polyline(latLngs, {
            color: '#0891b2',
            weight: 3.5,
            opacity: 0.85,
            dashArray: '5, 8'
        }).addTo(map);

        // Highlight current/latest active position with pulsating beacon
        if (waypoints.length > 1) {
            const currentWp = waypoints[waypoints.length - 2] || waypoints[waypoints.length - 1];
            L.circle([currentWp.lat, currentWp.lng], {
                radius: 45000,
                color: '#06b6d4',
                fillColor: '#22d3ee',
                fillOpacity: 0.25,
                weight: 1.5
            }).addTo(map);
        }

        // Fit map bounds to show all waypoints
        map.fitBounds(polyline.getBounds(), { padding: [40, 40] });
    });
</script>
@endpush
