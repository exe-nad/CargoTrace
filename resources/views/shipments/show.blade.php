@extends('layouts.app')

@section('title', 'Shipment #' . $shipment->shipment_number)
@section('header', 'Shipment Details')

@section('content')
<div class="space-y-6">

    <!-- Header Card -->
    <div class="bg-navyink/90 rounded-2xl p-6 border border-steelblue/30 shadow-lg shadow-deepnavy/40 flex flex-col md:flex-row md:items-center md:justify-between gap-4 backdrop-blur-md">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-black text-cargogold-400 tracking-tight font-mono">{{ $shipment->shipment_number }}</h2>
                @if($shipment->is_delayed)
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-rose-500/20 text-rose-300 border border-rose-500/40 flex items-center gap-1.5">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        DELAYED (+{{ $shipment->delay_hours }}h - {{ $shipment->delay_severity }})
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-check"></i> On Schedule
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-400 mt-1">
                Registered on {{ $shipment->created_at->format('M d, Y H:i') }} &bull; Scenario: <span class="text-steelblue font-mono">{{ $shipment->group_scenario ?? 'Standard' }}</span>
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('tracking.show', $shipment->shipment_number) }}" class="px-4 py-2 text-xs font-bold text-white bg-oceanblue hover:bg-oceanblue/80 border border-steelblue/40 rounded-xl transition shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-map-location-dot text-cargogold-400"></i> Live Map Tracking
            </a>
            <a href="{{ route('shipments.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-300 bg-deepnavy hover:bg-slate-800 border border-steelblue/30 rounded-xl transition">
                &larr; Back
            </a>
        </div>
    </div>

    <!-- Status Lifecycle Progress Bar (Slide 6) -->
    <div class="bg-navyink/90 rounded-2xl p-6 border border-steelblue/30 shadow-lg shadow-deepnavy/40 backdrop-blur-md">
        <h4 class="text-xs font-bold uppercase tracking-wider text-steelblue mb-6">Shipment Lifecycle Progression</h4>

        @php
            $stages = ['CREATED', 'BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'DELIVERED'];
            $currentIndex = array_search($shipment->current_status, $stages);
            if ($currentIndex === false) $currentIndex = 0;
        @endphp

        <div class="relative flex items-center justify-between">
            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-deepnavy border border-steelblue/20 z-0"></div>
            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-gradient-to-r from-oceanblue to-cyan-400 z-0 transition-all duration-500"
                 style="width: {{ ($currentIndex / (count($stages) - 1)) * 100 }}%"></div>

            @foreach($stages as $idx => $st)
                @php
                    $isPassed = $idx <= $currentIndex;
                    $isCurrent = $idx === $currentIndex;
                @endphp
                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs border-2 transition-all duration-300
                        {{ $isPassed ? 'bg-oceanblue text-white border-cyan-400 shadow-md shadow-oceanblue/40' : 'bg-deepnavy text-slate-400 border-steelblue/40' }}
                        {{ $isCurrent ? 'ring-4 ring-cargogold-400/40 ring-offset-2 ring-offset-deepnavy' : '' }}">
                        @if($isPassed)
                            <i class="fa-solid fa-check text-xs"></i>
                        @else
                            {{ $idx + 1 }}
                        @endif
                    </div>
                    <span class="mt-2 text-xs font-bold tracking-tight text-center {{ $isPassed ? 'text-cyan-300' : 'text-slate-400' }}">
                        {{ str_replace('_', ' ', $st) }}
                    </span>
                </div>
            @endforeach
        </div>

        <!-- Next Transition Form for Operators / Admins -->
        @if(auth()->user() && auth()->user()->canManageShipments() && !$shipment->isTerminal())
            @php
                $nextAllowed = \App\Models\Shipment::STATUS_FLOW[$shipment->current_status] ?? [];
            @endphp
            @if(!empty($nextAllowed))
                <div class="mt-8 pt-5 border-t border-steelblue/20 flex flex-wrap items-center justify-between gap-4">
                    <span class="text-xs text-slate-300">
                        <i class="fa-solid fa-shield-halved text-cargogold-400 mr-1"></i>
                        Next authorized lifecycle transition:
                    </span>
                    <form action="{{ route('shipments.transition', $shipment->id) }}" method="POST" class="flex items-center gap-2">
                        @csrf
                        @foreach($nextAllowed as $nxt)
                            <button type="submit" name="status" value="{{ $nxt }}" class="px-4 py-2 text-xs font-bold text-white bg-deepnavy hover:bg-slate-800 border border-steelblue/40 rounded-xl transition shadow-sm flex items-center gap-1.5">
                                Advance to {{ str_replace('_', ' ', $nxt) }} &rarr;
                            </button>
                        @endforeach
                    </form>
                </div>
            @endif
        @endif
    </div>

    <!-- 2 Column Details: Overview & Route Info -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Column 1 & 2: Overview & Cargo Items -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Route & Entities -->
            <div class="bg-navyink/90 rounded-2xl p-6 border border-steelblue/30 shadow-lg shadow-deepnavy/40 space-y-4 backdrop-blur-md">
                <h3 class="text-sm font-bold text-white">Routing & Stakeholders</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-4 rounded-xl bg-deepnavy/90 border border-steelblue/30">
                        <span class="text-steelblue uppercase font-bold block mb-1">Customer / Consignee</span>
                        <div class="text-sm font-bold text-slate-100">{{ $shipment->customer?->company_name ?? $shipment->customer?->name }}</div>
                        <div class="text-slate-400 mt-0.5">{{ $shipment->customer?->email }}</div>
                        <div class="text-slate-400">{{ $shipment->customer?->phone ?? 'No contact phone' }}</div>
                    </div>

                    <div class="p-4 rounded-xl bg-deepnavy/90 border border-steelblue/30">
                        <span class="text-steelblue uppercase font-bold block mb-1">Assigned Carrier</span>
                        <div class="text-sm font-bold text-slate-100">{{ $shipment->carrier?->name }}</div>
                        <div class="text-slate-400 mt-0.5">Service: {{ $shipment->carrier?->service_type }}</div>
                        <div class="text-slate-400">Code: <span class="text-cargogold-400 font-mono">{{ $shipment->carrier?->code }}</span></div>
                    </div>

                    <div class="p-4 rounded-xl bg-deepnavy/90 border border-steelblue/30">
                        <span class="text-steelblue uppercase font-bold block mb-1">Origin Port / Hub</span>
                        <div class="text-sm font-bold text-slate-100">{{ $shipment->origin?->name }}</div>
                        <div class="text-slate-400">{{ $shipment->origin?->city }}, {{ $shipment->origin?->country }}</div>
                        <div class="text-steelblue text-[11px] font-mono mt-1">Coords: {{ $shipment->origin?->latitude }}, {{ $shipment->origin?->longitude }}</div>
                    </div>

                    <div class="p-4 rounded-xl bg-deepnavy/90 border border-steelblue/30">
                        <span class="text-steelblue uppercase font-bold block mb-1">Destination Gateway</span>
                        <div class="text-sm font-bold text-slate-100">{{ $shipment->destination?->name }}</div>
                        <div class="text-slate-400">{{ $shipment->destination?->city }}, {{ $shipment->destination?->country }}</div>
                        <div class="text-steelblue text-[11px] font-mono mt-1">Coords: {{ $shipment->destination?->latitude }}, {{ $shipment->destination?->longitude }}</div>
                    </div>
                </div>
            </div>

            <!-- Items Manifest Table -->
            <div class="bg-navyink/90 rounded-2xl border border-steelblue/30 shadow-lg shadow-deepnavy/40 overflow-hidden backdrop-blur-md">
                <div class="px-6 py-4 border-b border-steelblue/30 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-white">Cargo Items Manifest</h3>
                    <span class="text-xs text-steelblue">{{ $shipment->items->count() }} items registered</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-deepnavy/90 text-steelblue font-bold uppercase border-b border-steelblue/30">
                            <tr>
                                <th class="py-3 px-6">Item Description</th>
                                <th class="py-3 px-6">SKU / HS Code</th>
                                <th class="py-3 px-6">Package Type</th>
                                <th class="py-3 px-6 text-right">Quantity</th>
                                <th class="py-3 px-6 text-right">Weight (kg)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-navyink/80 text-slate-200">
                            @forelse($shipment->items as $item)
                                <tr class="hover:bg-oceanblue/20 transition-colors">
                                    <td class="py-3 px-6 font-semibold text-slate-100">{{ $item->item_name }}</td>
                                    <td class="py-3 px-6 text-cargogold-400 font-mono">{{ $item->sku ?? $item->hs_code ?? '-' }}</td>
                                    <td class="py-3 px-6 text-slate-300">{{ $item->package_type }}</td>
                                    <td class="py-3 px-6 text-right font-medium text-slate-100">{{ $item->quantity }}</td>
                                    <td class="py-3 px-6 text-right font-medium text-slate-100">{{ number_format($item->weight, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-400">Standard bulk consignment package.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Column 3: Schedule, ETA & Exception Flags -->
        <div class="space-y-6">

            <!-- Schedule Card -->
            <div class="bg-navyink/90 rounded-2xl p-6 border border-steelblue/30 shadow-lg shadow-deepnavy/40 space-y-4 backdrop-blur-md">
                <h3 class="text-sm font-bold text-white">Schedule & ETA Assessment</h3>

                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between py-2 border-b border-navyink/80">
                        <span class="text-slate-400">Expected Departure</span>
                        <span class="font-bold text-slate-200">{{ $shipment->expected_departure?->format('M d, Y H:i') }}</span>
                    </div>

                    <div class="flex items-center justify-between py-2 border-b border-navyink/80">
                        <span class="text-slate-400">Actual Departure</span>
                        <span class="font-bold text-slate-200">{{ $shipment->actual_departure ? $shipment->actual_departure->format('M d, Y H:i') : 'Pending departure' }}</span>
                    </div>

                    <div class="flex items-center justify-between py-2 border-b border-navyink/80">
                        <span class="text-slate-400">Expected Arrival (SLA)</span>
                        <span class="font-bold text-slate-200">{{ $shipment->expected_arrival?->format('M d, Y H:i') }}</span>
                    </div>

                    <div class="flex items-center justify-between py-2 border-b border-navyink/80">
                        <span class="text-slate-400">Estimated Arrival (ETA)</span>
                        <span class="font-bold {{ $shipment->is_delayed ? 'text-rose-400' : 'text-slate-200' }}">
                            {{ $shipment->estimated_arrival?->format('M d, Y H:i') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between py-2">
                        <span class="text-slate-400">Calculated Delay</span>
                        <span class="font-bold {{ $shipment->is_delayed ? 'text-rose-400' : 'text-emerald-400' }}">
                            {{ $shipment->delay_hours }} Hours ({{ $shipment->delay_severity }})
                        </span>
                    </div>
                </div>
            </div>

            <!-- Exceptions on this Shipment -->
            <div class="bg-navyink/90 rounded-2xl p-6 border border-steelblue/30 shadow-lg shadow-deepnavy/40 space-y-4 backdrop-blur-md">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-white">Exception Incidents</h3>
                    <span class="text-xs text-rose-400 font-bold">{{ $shipment->exceptions->count() }} flagged</span>
                </div>

                <div class="space-y-3">
                    @forelse($shipment->exceptions as $ex)
                        <div class="p-3.5 rounded-xl border {{ $ex->isOpen() ? 'bg-deepnavy/90 border-rose-500/40' : 'bg-deepnavy/90 border-steelblue/30' }} text-xs">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-slate-100">{{ str_replace('_', ' ', $ex->exception_type) }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                    {{ $ex->status === 'OPEN' ? 'bg-rose-500 text-white' : ($ex->status === 'INVESTIGATING' ? 'bg-amber-500 text-slate-900' : 'bg-emerald-600 text-white') }}">
                                    {{ $ex->status }}
                                </span>
                            </div>
                            <p class="text-slate-300 mt-1">{{ $ex->description }}</p>
                            <span class="text-[10px] text-slate-400 block mt-2">Detected: {{ $ex->detected_at?->diffForHumans() }}</span>
                        </div>
                    @empty
                        <div class="p-4 rounded-xl bg-deepnavy/90 border border-emerald-500/30 text-center text-xs text-emerald-300">
                            <i class="fa-solid fa-check-circle mr-1"></i> No active exceptions registered for this shipment.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
