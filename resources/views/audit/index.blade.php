@extends('layouts.app')

@section('title', 'System Audit Trail')
@section('header', 'Audit Logs')

@section('content')
<div class="space-y-6">

    <!-- Search & Filter -->
    <div class="bg-navyink/90 rounded-2xl p-6 border border-steelblue/30 shadow-lg shadow-deepnavy/40 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 backdrop-blur-md">
        <div>
            <h3 class="text-base font-bold text-white">Security & Operational Audit Trail</h3>
            <p class="text-xs text-slate-400">Read-only immutable log of system modifications and transactions</p>
        </div>

        <form action="{{ route('audit.index') }}" method="GET" class="flex items-center gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search action, user, IP..."
                class="py-2 px-3.5 text-xs bg-deepnavy/90 border border-steelblue/30 rounded-xl text-slate-100 placeholder-slate-400 outline-none focus:ring-2 focus:ring-oceanblue focus:border-steelblue">
            <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-gradient-to-r from-oceanblue to-cyan-600 hover:from-oceanblue/90 hover:to-cyan-500 rounded-xl transition shadow">Filter</button>
        </form>
    </div>

    <!-- Audit Logs Table -->
    <div class="bg-navyink/90 rounded-2xl border border-steelblue/30 shadow-xl shadow-deepnavy/50 overflow-hidden backdrop-blur-md">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-deepnavy/90 text-steelblue font-bold uppercase tracking-wider border-b border-steelblue/30">
                    <tr>
                        <th class="py-3.5 px-6">Timestamp</th>
                        <th class="py-3.5 px-6">Actor</th>
                        <th class="py-3.5 px-6">Action</th>
                        <th class="py-3.5 px-6">Target Entity</th>
                        <th class="py-3.5 px-6">IP Address</th>
                        <th class="py-3.5 px-6 text-right">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navyink/80 text-slate-200">
                    @forelse($logs as $log)
                        <tr class="hover:bg-oceanblue/20 transition">
                            <td class="py-3.5 px-6 font-mono text-slate-300">
                                {{ $log->created_at->format('Y-m-d H:i:s') }}
                            </td>
                            <td class="py-3.5 px-6 font-medium text-white">
                                {{ $log->user?->name ?? 'System / Anonymous' }}
                                @if($log->user)
                                    <span class="block text-[10px] text-steelblue font-mono">({{ $log->user->role }})</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-mono font-bold
                                    {{ str_contains($log->action, 'DELETE') ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40' : (str_contains($log->action, 'CREATE') ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : 'bg-oceanblue/30 text-cyan-300 border border-oceanblue/50') }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-slate-300 font-mono">
                                {{ $log->entity_type }} #{{ $log->entity_id }}
                            </td>
                            <td class="py-3.5 px-6 font-mono text-slate-400">
                                {{ $log->ip_address }}
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                @if($log->new_values)
                                    <span class="font-mono text-[10px] text-cargogold-400" title="{{ json_encode($log->new_values) }}">
                                        {{ count($log->new_values) }} attributes
                                    </span>
                                @else
                                    <span class="text-slate-500 text-[10px]">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                No audit events logged yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="px-6 py-4 bg-deepnavy/80 border-t border-steelblue/20 text-slate-400">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
