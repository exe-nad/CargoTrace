@extends('layouts.app')

@section('title', 'System Audit Trail')
@section('header', 'Audit Logs')

@section('content')
<div class="space-y-6">

    <!-- Search & Filter -->
    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-slate-900">Security & Operational Audit Trail</h3>
            <p class="text-xs text-slate-400">Read-only immutable log of system modifications and transactions</p>
        </div>

        <form action="{{ route('audit.index') }}" method="GET" class="flex items-center gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search action, user, IP..."
                class="py-2 px-3.5 text-xs bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-cyan-500">
            <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-slate-900 rounded-xl">Filter</button>
        </form>
    </div>

    <!-- Audit Logs Table -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-slate-50 text-slate-400 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-6">Timestamp</th>
                        <th class="py-3.5 px-6">Actor</th>
                        <th class="py-3.5 px-6">Action</th>
                        <th class="py-3.5 px-6">Target Entity</th>
                        <th class="py-3.5 px-6">IP Address</th>
                        <th class="py-3.5 px-6 text-right">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3.5 px-6 font-mono text-slate-500">
                                {{ $log->created_at->format('Y-m-d H:i:s') }}
                            </td>
                            <td class="py-3.5 px-6 font-medium text-slate-800">
                                {{ $log->user?->name ?? 'System / Anonymous' }}
                                @if($log->user)
                                    <span class="block text-[10px] text-slate-400">({{ $log->user->role }})</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-mono font-bold
                                    {{ str_contains($log->action, 'DELETE') ? 'bg-rose-50 text-rose-700' : (str_contains($log->action, 'CREATE') ? 'bg-emerald-50 text-emerald-700' : 'bg-cyan-50 text-cyan-700') }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-slate-600">
                                {{ $log->entity_type }} #{{ $log->entity_id }}
                            </td>
                            <td class="py-3.5 px-6 font-mono text-slate-400">
                                {{ $log->ip_address }}
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                @if($log->new_values)
                                    <span class="font-mono text-[10px] text-slate-400" title="{{ json_encode($log->new_values) }}">
                                        {{ count($log->new_values) }} attributes
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[10px]">-</span>
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
            <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
