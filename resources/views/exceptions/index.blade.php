@extends('layouts.app')

@section('title', 'Operational Exceptions')
@section('header', 'Exceptions Management')

@section('content')
<div class="space-y-6">

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Open Incidents</p>
                <h3 class="text-2xl font-black text-rose-600 mt-0.5">{{ $totalOpen }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-magnifying-glass text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Under Investigation</p>
                <h3 class="text-2xl font-black text-amber-600 mt-0.5">{{ $totalInvestigating }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-circle-check text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Resolved Exceptions</p>
                <h3 class="text-2xl font-black text-emerald-600 mt-0.5">{{ $totalResolved }}</h3>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
        <form action="{{ route('exceptions.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-500 mb-1.5">Exception Type</label>
                <select name="type" class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-cyan-500">
                    <option value="">All Types</option>
                    @foreach(['CUSTOMS_HOLD', 'WEATHER_DELAY', 'PORT_CONGESTION', 'DAMAGED', 'MISROUTED', 'DOCUMENTATION_ISSUE'] as $t)
                        <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ str_replace('_', ' ', $t) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-500 mb-1.5">Severity</label>
                <select name="severity" class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-cyan-500">
                    <option value="">All Severities</option>
                    @foreach(['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'] as $s)
                        <option value="{{ $s }}" {{ request('severity') === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-500 mb-1.5">Resolution Status</label>
                <select name="status" class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-cyan-500">
                    <option value="">All Statuses</option>
                    @foreach(['OPEN', 'INVESTIGATING', 'RESOLVED'] as $st)
                        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 rounded-xl transition shadow-sm">
                    Filter
                </button>
                <a href="{{ route('exceptions.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 rounded-xl">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Exceptions Table -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 uppercase tracking-wider text-slate-400 font-bold">
                        <th class="py-3.5 px-6">Shipment</th>
                        <th class="py-3.5 px-6">Exception Type</th>
                        <th class="py-3.5 px-6">Severity</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6">Detected At</th>
                        <th class="py-3.5 px-6">Description</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($exceptions as $ex)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-4 px-6 font-semibold">
                                <a href="{{ route('shipments.show', $ex->shipment_id) }}" class="text-cyan-600 hover:underline">
                                    {{ $ex->shipment?->shipment_number }}
                                </a>
                                <span class="text-[10px] text-slate-400 block">{{ $ex->shipment?->carrier?->name }}</span>
                            </td>
                            <td class="py-4 px-6 font-bold text-slate-800">
                                {{ str_replace('_', ' ', $ex->exception_type) }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                    {{ $ex->severity === 'CRITICAL' ? 'bg-red-600 text-white' : ($ex->severity === 'HIGH' ? 'bg-rose-50 text-rose-700 border border-rose-200' : ($ex->severity === 'MEDIUM' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-600')) }}">
                                    {{ $ex->severity }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase
                                    {{ $ex->status === 'OPEN' ? 'bg-rose-100 text-rose-800' : ($ex->status === 'INVESTIGATING' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800') }}">
                                    {{ $ex->status }}
                                </span>
                            </td>
                            <td class="py-4 px-6 font-mono text-slate-500">
                                {{ $ex->detected_at?->format('Y-m-d H:i') }}
                                <span class="block text-[10px] text-slate-400">{{ $ex->detected_at?->diffForHumans() }}</span>
                            </td>
                            <td class="py-4 px-6 text-slate-600 max-w-xs truncate" title="{{ $ex->description }}">
                                {{ $ex->description }}
                                @if($ex->resolution_notes)
                                    <span class="block text-[11px] text-emerald-600 font-medium mt-0.5">Note: {{ $ex->resolution_notes }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                @if(auth()->user() && auth()->user()->canManageShipments() && $ex->isOpen())
                                    <button type="button" onclick="openResolveModal({{ $ex->id }}, '{{ $ex->status }}')"
                                        class="px-3 py-1.5 text-[11px] font-bold text-cyan-700 bg-cyan-50 hover:bg-cyan-100 rounded-lg transition border border-cyan-200">
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
            <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100">
                {{ $exceptions->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal: Update Exception Status -->
<div id="resolveModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <h3 class="text-base font-bold text-slate-900">Update Exception Resolution</h3>
            <button type="button" onclick="document.getElementById('resolveModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="resolveForm" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Target Status</label>
                <select name="status" id="modalStatusSelect" required class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-cyan-500">
                    <option value="INVESTIGATING">INVESTIGATING</option>
                    <option value="RESOLVED">RESOLVED</option>
                    <option value="OPEN">OPEN</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Resolution Notes</label>
                <textarea name="resolution_notes" rows="3" placeholder="Provide root cause & actions taken..."
                    class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-cyan-500"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="document.getElementById('resolveModal').classList.add('hidden')"
                    class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 rounded-xl">Cancel</button>
                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 rounded-xl transition">
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
