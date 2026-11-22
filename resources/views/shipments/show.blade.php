@extends('layouts.app')

@section('title', 'Shipment #' . $shipment->shipment_number)
@section('header', 'Shipment Details')

@section('content')
<div class="space-y-6">

    <!-- Header Card -->
    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">{{ $shipment->shipment_number }}</h2>
                @if($shipment->is_delayed)
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-rose-50 text-rose-700 border border-rose-200 flex items-center gap-1.5">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        DELAYED (+{{ $shipment->delay_hours }}h - {{ $shipment->delay_severity }})
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-check"></i> On Schedule
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-400 mt-1">
                Registered on {{ $shipment->created_at->format('M d, Y H:i') }} &bull; Scenario: {{ $shipment->group_scenario ?? 'Standard' }}
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('tracking.show', $shipment->shipment_number) }}" class="px-4 py-2 text-xs font-bold text-white bg-cyan-600 hover:bg-cyan-700 rounded-xl transition shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-map-location-dot"></i> Live Map Tracking
            </a>
            <a href="{{ route('shipments.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                &larr; Back
            </a>
        </div>
    </div>

    <!-- Status Lifecycle Progress Bar (Slide 6) -->
    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-6">Shipment Lifecycle Progression</h4>

        @php
            $stages = ['CREATED', 'BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'DELIVERED'];
            $currentIndex = array_search($shipment->current_status, $stages);
            if ($currentIndex === false) $currentIndex = 0;
        @endphp

        <div class="relative flex items-center justify-between">
            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-slate-100 z-0"></div>
            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-cyan-500 z-0 transition-all duration-500"
                 style="width: {{ ($currentIndex / (count($stages) - 1)) * 100 }}%"></div>

            @foreach($stages as $idx => $st)
                @php
                    $isPassed = $idx <= $currentIndex;
                    $isCurrent = $idx === $currentIndex;
                @endphp
                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs border-4 transition-all duration-300
                        {{ $isPassed ? 'bg-cyan-600 text-white border-cyan-100 shadow-md shadow-cyan-500/20' : 'bg-white text-slate-400 border-slate-200' }}
                        {{ $isCurrent ? 'ring-4 ring-cyan-500/30 ring-offset-2' : '' }}">
                        @if($isPassed)
                            <i class="fa-solid fa-check text-xs"></i>
                        @else
                            {{ $idx + 1 }}
                        @endif
                    </div>
                    <span class="mt-2 text-xs font-bold tracking-tight text-center {{ $isPassed ? 'text-cyan-700' : 'text-slate-400' }}">
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
                <div class="mt-8 pt-5 border-t border-slate-100 flex flex-wrap items-center justify-between gap-4">
                    <span class="text-xs text-slate-500">
                        <i class="fa-solid fa-shield-halved text-cyan-600 mr-1"></i>
                        Next authorized lifecycle transition:
                    </span>
                    <form action="{{ route('shipments.transition', $shipment->id) }}" method="POST" class="flex items-center gap-2">
                        @csrf
                        @foreach($nextAllowed as $nxt)
                            <button type="submit" name="status" value="{{ $nxt }}" class="px-4 py-2 text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 rounded-xl transition shadow-sm flex items-center gap-1.5">
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
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-900">Routing & Stakeholders</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 uppercase font-semibold block mb-1">Customer / Consignee</span>
                        <div class="text-sm font-bold text-slate-800">{{ $shipment->customer?->company_name ?? $shipment->customer?->name }}</div>
                        <div class="text-slate-500 mt-0.5">{{ $shipment->customer?->email }}</div>
                        <div class="text-slate-500">{{ $shipment->customer?->phone ?? 'No contact phone' }}</div>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 uppercase font-semibold block mb-1">Assigned Carrier</span>
                        <div class="text-sm font-bold text-slate-800">{{ $shipment->carrier?->name }}</div>
                        <div class="text-slate-500 mt-0.5">Service: {{ $shipment->carrier?->service_type }}</div>
                        <div class="text-slate-500">Code: {{ $shipment->carrier?->code }}</div>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 uppercase font-semibold block mb-1">Origin Port / Hub</span>
                        <div class="text-sm font-bold text-slate-800">{{ $shipment->origin?->name }}</div>
                        <div class="text-slate-500">{{ $shipment->origin?->city }}, {{ $shipment->origin?->country }}</div>
                        <div class="text-slate-400 text-[11px] font-mono mt-1">Coords: {{ $shipment->origin?->latitude }}, {{ $shipment->origin?->longitude }}</div>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 uppercase font-semibold block mb-1">Destination Gateway</span>
                        <div class="text-sm font-bold text-slate-800">{{ $shipment->destination?->name }}</div>
                        <div class="text-slate-500">{{ $shipment->destination?->city }}, {{ $shipment->destination?->country }}</div>
                        <div class="text-slate-400 text-[11px] font-mono mt-1">Coords: {{ $shipment->destination?->latitude }}, {{ $shipment->destination?->longitude }}</div>
                    </div>
                </div>
            </div>

            <!-- Items Manifest Table -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900">Cargo Items Manifest</h3>
                    <span class="text-xs text-slate-400">{{ $shipment->items->count() }} items registered</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-50 text-slate-400 font-semibold uppercase">
                            <tr>
                                <th class="py-3 px-6">Item Description</th>
                                <th class="py-3 px-6">SKU / HS Code</th>
                                <th class="py-3 px-6">Package Type</th>
                                <th class="py-3 px-6 text-right">Quantity</th>
                                <th class="py-3 px-6 text-right">Weight (kg)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($shipment->items as $item)
                                <tr>
                                    <td class="py-3 px-6 font-semibold text-slate-800">{{ $item->item_name }}</td>
                                    <td class="py-3 px-6 text-slate-500 font-mono">{{ $item->sku ?? $item->hs_code ?? '-' }}</td>
                                    <td class="py-3 px-6 text-slate-600">{{ $item->package_type }}</td>
                                    <td class="py-3 px-6 text-right font-medium text-slate-800">{{ $item->quantity }}</td>
                                    <td class="py-3 px-6 text-right font-medium text-slate-800">{{ number_format($item->weight, 2) }}</td>
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
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-900">Schedule & ETA Assessment</h3>

                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Expected Departure</span>
                        <span class="font-bold text-slate-800">{{ $shipment->expected_departure?->format('M d, Y H:i') }}</span>
                    </div>

                    <div class="flex items-center justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Actual Departure</span>
                        <span class="font-bold text-slate-800">{{ $shipment->actual_departure ? $shipment->actual_departure->format('M d, Y H:i') : 'Pending departure' }}</span>
                    </div>

                    <div class="flex items-center justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Expected Arrival (SLA)</span>
                        <span class="font-bold text-slate-800">{{ $shipment->expected_arrival?->format('M d, Y H:i') }}</span>
                    </div>

                    <div class="flex items-center justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Estimated Arrival (ETA)</span>
                        <span class="font-bold {{ $shipment->is_delayed ? 'text-rose-600' : 'text-slate-800' }}">
                            {{ $shipment->estimated_arrival?->format('M d, Y H:i') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between py-2">
                        <span class="text-slate-500">Calculated Delay</span>
                        <span class="font-bold {{ $shipment->is_delayed ? 'text-rose-600' : 'text-emerald-600' }}">
                            {{ $shipment->delay_hours }} Hours ({{ $shipment->delay_severity }})
                        </span>
                    </div>
                </div>
            </div>

            <!-- Exceptions on this Shipment -->
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900">Exception Incidents</h3>
                    <span class="text-xs text-rose-600 font-bold">{{ $shipment->exceptions->count() }} flagged</span>
                </div>

                <div class="space-y-3">
                    @forelse($shipment->exceptions as $ex)
                        <div class="p-3.5 rounded-xl border {{ $ex->isOpen() ? 'bg-rose-50/50 border-rose-200' : 'bg-slate-50 border-slate-200' }} text-xs">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-slate-900">{{ str_replace('_', ' ', $ex->exception_type) }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                    {{ $ex->status === 'OPEN' ? 'bg-rose-600 text-white' : ($ex->status === 'INVESTIGATING' ? 'bg-amber-500 text-white' : 'bg-emerald-600 text-white') }}">
                                    {{ $ex->status }}
                                </span>
                            </div>
                            <p class="text-slate-600 mt-1">{{ $ex->description }}</p>
                            <span class="text-[10px] text-slate-400 block mt-2">Detected: {{ $ex->detected_at?->diffForHumans() }}</span>
                        </div>
                    @empty
                        <div class="p-4 rounded-xl bg-emerald-50/50 border border-emerald-100 text-center text-xs text-emerald-700">
                            <i class="fa-solid fa-check-circle mr-1"></i> No active exceptions registered for this shipment.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
