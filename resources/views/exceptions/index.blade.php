@extends('layouts.app')

@section('title', 'Operational Exceptions')
@section('header', 'Exceptions Management')

@section('content')
<div class="space-y-6">

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-navyink/90 rounded-2xl p-5 border border-steelblue/30 shadow-lg shadow-deepnavy/40 flex items-center space-x-4 backdrop-blur-md">
            <div class="w-12 h-12 rounded-xl bg-rose-500/20 text-rose-400 border border-rose-500/40 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-steelblue">Open Incidents</p>
                <h3 class="text-2xl font-black text-rose-400 mt-0.5">{{ $totalOpen }}</h3>
            </div>
        </div>

        <div class="bg-navyink/90 rounded-2xl p-5 border border-steelblue/30 shadow-lg shadow-deepnavy/40 flex items-center space-x-4 backdrop-blur-md">
            <div class="w-12 h-12 rounded-xl bg-amber-500/20 text-amber-400 border border-amber-500/40 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-magnifying-glass text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-steelblue">Under Investigation</p>
                <h3 class="text-2xl font-black text-amber-400 mt-0.5">{{ $totalInvestigating }}</h3>
            </div>
        </div>

        <div class="bg-navyink/90 rounded-2xl p-5 border border-steelblue/30 shadow-lg shadow-deepnavy/40 flex items-center space-x-4 backdrop-blur-md">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-circle-check text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-steelblue">Resolved Exceptions</p>
                <h3 class="text-2xl font-black text-emerald-400 mt-0.5">{{ $totalResolved }}</h3>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-navyink/90 rounded-2xl p-6 border border-steelblue/30 shadow-lg shadow-deepnavy/40 backdrop-blur-md">
        <form action="{{ route('exceptions.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-bold uppercase text-steelblue mb-1.5">Exception Type</label>
                <select name="type" class="w-full py-2 px-3 text-xs bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 outline-none focus:ring-2 focus:ring-oceanblue focus:border-steelblue">
                    <option value="" class="bg-navyink text-slate-300">All Types</option>
                    @foreach(['CUSTOMS_HOLD', 'WEATHER_DELAY', 'PORT_CONGESTION', 'DAMAGED', 'MISROUTED', 'DOCUMENTATION_ISSUE'] as $t)
                        <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }} class="bg-navyink text-slate-200">{{ str_replace('_', ' ', $t) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-steelblue mb-1.5">Severity</label>
                <select name="severity" class="w-full py-2 px-3 text-xs bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 outline-none focus:ring-2 focus:ring-oceanblue focus:border-steelblue">
                    <option value="" class="bg-navyink text-slate-300">All Severities</option>
                    @foreach(['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'] as $s)
                        <option value="{{ $s }}" {{ request('severity') === $s ? 'selected' : '' }} class="bg-navyink text-slate-200">{{ $s }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-steelblue mb-1.5">Resolution Status</label>
                <select name="status" class="w-full py-2 px-3 text-xs bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 outline-none focus:ring-2 focus:ring-oceanblue focus:border-steelblue">
                    <option value="" class="bg-navyink text-slate-300">All Statuses</option>
                    @foreach(['OPEN', 'INVESTIGATING', 'RESOLVED'] as $st)
                        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }} class="bg-navyink text-slate-200">{{ $st }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-gradient-to-r from-oceanblue to-cyan-600 hover:from-oceanblue/90 hover:to-cyan-500 rounded-xl transition shadow-sm">
                    Filter
                </button>
                <a href="{{ route('exceptions.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-300 bg-deepnavy hover:bg-slate-800 border border-steelblue/30 rounded-xl">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Exceptions Table -->
    <div class="bg-navyink/90 rounded-2xl border border-steelblue/30 shadow-xl shadow-deepnavy/50 overflow-hidden backdrop-blur-md">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-deepnavy/90 border-b border-steelblue/30 uppercase tracking-wider text-steelblue font-bold">
                        <th class="py-3.5 px-6">Shipment</th>
                        <th class="py-3.5 px-6">Exception Type</th>
                        <th class="py-3.5 px-6">Severity</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6">Detected At</th>
                        <th class="py-3.5 px-6">Description</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navyink/80 text-slate-200">
                    @forelse($exceptions as $ex)
                        <tr class="hover:bg-oceanblue/20 transition">
                            <td class="py-4 px-6 font-semibold">
                                <a href="{{ route('shipments.show', $ex->shipment_id) }}" class="text-cargogold-400 font-mono hover:underline">
                                    {{ $ex->shipment?->shipment_number }}
                                </a>
                                <span class="text-[10px] text-steelblue block">{{ $ex->shipment?->carrier?->name }}</span>
                            </td>
                            <td class="py-4 px-6 font-bold text-slate-100">
                                {{ str_replace('_', ' ', $ex->exception_type) }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                    {{ $ex->severity === 'CRITICAL' ? 'bg-red-600 text-white' : ($ex->severity === 'HIGH' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40' : ($ex->severity === 'MEDIUM' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : 'bg-deepnavy text-slate-300 border border-steelblue/30')) }}">
                                    {{ $ex->severity }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase
                                    {{ $ex->status === 'OPEN' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40' : ($ex->status === 'INVESTIGATING' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40') }}">
                                    {{ $ex->status }}
                                </span>
                            </td>
                            <td class="py-4 px-6 font-mono text-slate-300">
                                {{ $ex->detected_at?->format('Y-m-d H:i') }}
                                <span class="block text-[10px] text-slate-400">{{ $ex->detected_at?->diffForHumans() }}</span>
                            </td>
                            <td class="py-4 px-6 text-slate-300 max-w-xs truncate" title="{{ $ex->description }}">
                                {{ $ex->description }}
                                @if($ex->resolution_notes)
                                    <span class="block text-[11px] text-emerald-400 font-medium mt-0.5">Note: {{ $ex->resolution_notes }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                @if(auth()->user() && auth()->user()->canManageShipments() && $ex->isOpen())
                                    <button type="button" onclick="openResolveModal({{ $ex->id }}, '{{ $ex->status }}')"
                                        class="px-3 py-1.5 text-[11px] font-bold text-cyan-300 bg-oceanblue/30 hover:bg-oceanblue/50 rounded-lg transition border border-oceanblue/50">
                                        Update Status
                                    </button>
                                @else
                                    <span class="text-slate-400 text-xs">Closed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400">
                                No exceptions found matching criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($exceptions->hasPages())
            <div class="px-6 py-4 bg-deepnavy/80 border-t border-steelblue/20 text-slate-400">
                {{ $exceptions->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal: Update Exception Status -->
<div id="resolveModal" class="hidden fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-navyink/95 rounded-3xl max-w-md w-full p-6 shadow-2xl border border-steelblue/40 text-slate-100">
        <div class="flex items-center justify-between pb-3 border-b border-steelblue/30 mb-4">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-wrench text-cargogold-400"></i> Update Exception Resolution
            </h3>
            <button type="button" onclick="document.getElementById('resolveModal').classList.add('hidden')" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="resolveForm" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase text-steelblue mb-1">Target Status</label>
                <select name="status" id="modalStatusSelect" required class="w-full py-2.5 px-3 text-xs bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 outline-none focus:ring-2 focus:ring-oceanblue">
                    <option value="INVESTIGATING" class="bg-navyink">INVESTIGATING</option>
                    <option value="RESOLVED" class="bg-navyink">RESOLVED</option>
                    <option value="OPEN" class="bg-navyink">OPEN</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-steelblue mb-1">Resolution Notes</label>
                <textarea name="resolution_notes" rows="3" placeholder="Provide root cause & actions taken..."
                    class="w-full py-2.5 px-3 text-xs bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 placeholder-slate-500 outline-none focus:ring-2 focus:ring-oceanblue"></textarea>
            </div>

            <div class="pt-3 border-t border-steelblue/30 flex items-center justify-end gap-2">
                <button type="button" onclick="document.getElementById('resolveModal').classList.add('hidden')"
                    class="px-4 py-2 text-xs font-semibold text-slate-300 bg-deepnavy hover:bg-slate-800 border border-steelblue/30 rounded-xl">Cancel</button>
                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-gradient-to-r from-oceanblue to-cyan-600 hover:from-oceanblue/90 hover:to-cyan-500 rounded-xl transition shadow">
                    Save Resolution
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openResolveModal(id, currentStatus) {
        const form = document.getElementById('resolveForm');
        form.action = `/exceptions/${id}/status`;
        document.getElementById('modalStatusSelect').value = currentStatus === 'OPEN' ? 'INVESTIGATING' : 'RESOLVED';
        document.getElementById('resolveModal').classList.remove('hidden');
    }
</script>
@endsection
