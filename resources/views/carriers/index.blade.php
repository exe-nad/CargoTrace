@extends('layouts.app')

@section('title', 'Logistics Carriers')
@section('header', 'Carriers Fleet & Performance')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-base font-bold text-slate-900">Registered Carrier Network</h3>
            <p class="text-xs text-slate-400">Integrated global freight and delivery partners</p>
        </div>

        @if(auth()->user() && auth()->user()->isAdmin())
            <button type="button" onclick="document.getElementById('carrierModal').classList.remove('hidden')"
                class="px-4 py-2 text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 rounded-xl transition shadow-sm flex items-center gap-1.5">
                <i class="fa-solid fa-plus text-xs"></i> Add Carrier Partner
            </button>
        @endif
    </div>

    <!-- Carriers Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($carriers as $c)
            @php
                $delayRate = $c->shipments_count > 0 ? round(($c->delayed_count / $c->shipments_count) * 100, 1) : 0;
            @endphp
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-slate-900 text-cyan-400 flex items-center justify-center font-black text-lg">
                            {{ $c->code }}
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase {{ $c->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $c->is_active ? 'Active Fleet' : 'Inactive' }}
                        </span>
                    </div>

                    <h4 class="text-lg font-bold text-slate-900">{{ $c->name }}</h4>
                    <span class="text-xs text-slate-400 block mb-4">{{ $c->service_type }}</span>

                    <div class="grid grid-cols-2 gap-3 py-3 border-y border-slate-100 text-xs mb-4">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Total Shipments</span>
                            <span class="text-base font-black text-slate-900">{{ $c->shipments_count }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Delay Incident Rate</span>
                            <span class="text-base font-black {{ $delayRate > 25 ? 'text-rose-600' : 'text-emerald-600' }}">
                                {{ $delayRate }}%
                            </span>
                        </div>
                    </div>
                </div>

                <div class="space-y-1 text-xs text-slate-500 pt-2">
                    <div class="flex items-center gap-2">
                        <i class="fa-regular fa-envelope text-slate-400 w-4"></i>
                        <span>{{ $c->contact_email ?? 'dispatch@' . strtolower(str_replace(' ', '', $c->name)) . '.com' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-phone text-slate-400 w-4"></i>
                        <span>{{ $c->phone ?? '+1 (800) 555-0199' }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>

<!-- Modal: Add Carrier -->
<div id="carrierModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <h3 class="text-base font-bold text-slate-900">Register Carrier Partner</h3>
            <button type="button" onclick="document.getElementById('carrierModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="{{ route('carriers.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Carrier Code *</label>
                <input type="text" name="code" required placeholder="e.g. EVER" maxlength="10"
                    class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-cyan-500">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Carrier Name *</label>
                <input type="text" name="name" required placeholder="e.g. Evergreen Marine Lines"
                    class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-cyan-500">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Service Type *</label>
                <input type="text" name="service_type" required placeholder="e.g. Ocean Container Lines"
                    class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-cyan-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Contact Email</label>
                    <input type="email" name="contact_email" placeholder="support@carrier.com"
                        class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Phone</label>
                    <input type="text" name="phone" placeholder="+1 800..."
                        class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl outline-none">
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="document.getElementById('carrierModal').classList.add('hidden')"
                    class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 rounded-xl">Cancel</button>
                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 rounded-xl transition">
                    Register Carrier
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
