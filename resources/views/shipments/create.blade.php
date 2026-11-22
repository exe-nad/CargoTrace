@extends('layouts.app')

@section('title', 'Register New Shipment')
@section('header', 'Register Shipment')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="bg-white rounded-2xl p-8 border border-slate-100 shadow-sm">
        <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">Create & Dispatch New Shipment</h2>
                <p class="text-xs text-slate-400 mt-1">Register shipment manifest, carrier routing, and milestone expectations.</p>
            </div>
            <a href="{{ route('shipments.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                &larr; Back to shipments
            </a>
        </div>

        <form action="{{ route('shipments.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Section 1: Customer & Carrier -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1.5">Customer / Consignee *</label>
                    <select name="customer_id" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:bg-white outline-none">
                        <option value="">Select Customer Account</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->company_name ?? 'Individual' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1.5">Carrier Service *</label>
                    <select name="carrier_id" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:bg-white outline-none">
                        <option value="">Select Logistics Carrier</option>
                        @foreach($carriers as $carrier)
                            <option value="{{ $carrier->id }}" {{ old('carrier_id') == $carrier->id ? 'selected' : '' }}>
                                {{ $carrier->name }} ({{ $carrier->service_type }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Section 2: Routing Origin & Destination -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1.5">Origin Location *</label>
                    <select name="origin_id" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:bg-white outline-none">
                        <option value="">Select Departure Port / Hub</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('origin_id') == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }} ({{ $loc->city }}, {{ $loc->country }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1.5">Destination Location *</label>
                    <select name="destination_id" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:bg-white outline-none">
                        <option value="">Select Destination Gateway</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('destination_id') == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }} ({{ $loc->city }}, {{ $loc->country }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Section 3: Cargo Details -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1.5">Cargo Description *</label>
                    <input type="text" name="cargo_type" value="{{ old('cargo_type', 'Precision Electronics') }}" required
                        placeholder="e.g. Semiconductor Modules" class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:bg-white outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1.5">Total Weight (kg) *</label>
                    <input type="number" step="0.01" name="total_weight" value="{{ old('total_weight', '4500') }}" required
                        class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:bg-white outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1.5">Total Volume (cbm) *</label>
                    <input type="number" step="0.01" name="total_volume" value="{{ old('total_volume', '18.5') }}" required
                        class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:bg-white outline-none">
                </div>
            </div>

            <!-- Section 4: Schedule Dates -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1.5">Expected Departure *</label>
                    <input type="datetime-local" name="expected_departure" value="{{ old('expected_departure', now()->format('Y-m-d\TH:i')) }}" required
                        class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:bg-white outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1.5">Expected Arrival (SLA) *</label>
                    <input type="datetime-local" name="expected_arrival" value="{{ old('expected_arrival', now()->addDays(5)->format('Y-m-d\TH:i')) }}" required
                        class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:bg-white outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1.5">Estimated Arrival (ETA) *</label>
                    <input type="datetime-local" name="estimated_arrival" value="{{ old('estimated_arrival', now()->addDays(5)->format('Y-m-d\TH:i')) }}" required
                        class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:bg-white outline-none">
                </div>
            </div>

            <!-- Section 5: Group Scenario & Notes -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1.5">Group Evaluation Scenario (Slide 23)</label>
                    <select name="group_scenario" class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:bg-white outline-none">
                        <option value="Group A" {{ old('group_scenario') === 'Group A' ? 'selected' : '' }}>Group A (Threshold: 12 Hours, Southeast Asia &rarr; Europe)</option>
                        <option value="Group B" {{ old('group_scenario') === 'Group B' ? 'selected' : '' }}>Group B (Threshold: 18 Hours, Asia &rarr; North America)</option>
                        <option value="Group C" {{ old('group_scenario') === 'Group C' ? 'selected' : '' }}>Group C (Threshold: 24 Hours, Southeast Asia &rarr; Australia)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1.5">Special Handling Notes</label>
                    <input type="text" name="notes" value="{{ old('notes') }}" placeholder="e.g. Temperature-sensitive, fragile"
                        class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:bg-white outline-none">
                </div>
            </div>

            <!-- Section 6: Cargo Items Repeater -->
            <div class="pt-4 border-t border-slate-100">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-bold text-slate-800">Package & Item Manifest</h3>
                    <button type="button" id="addItemBtn" class="text-xs text-cyan-600 font-bold hover:underline flex items-center gap-1">
                        <i class="fa-solid fa-plus text-[10px]"></i> Add Another Item
                    </button>
                </div>

                <div id="itemsContainer" class="space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200 item-row">
                        <div>
                            <input type="text" name="items[0][item_name]" value="Primary Cargo Unit" placeholder="Item Name" required
                                class="w-full py-1.5 px-3 text-xs bg-white border border-slate-200 rounded-lg outline-none">
                        </div>
                        <div>
                            <input type="number" name="items[0][quantity]" value="100" placeholder="Qty" required
                                class="w-full py-1.5 px-3 text-xs bg-white border border-slate-200 rounded-lg outline-none">
                        </div>
                        <div>
                            <input type="number" step="0.01" name="items[0][weight]" value="4500" placeholder="Weight (kg)" required
                                class="w-full py-1.5 px-3 text-xs bg-white border border-slate-200 rounded-lg outline-none">
                        </div>
                        <div>
                            <select name="items[0][package_type]" class="w-full py-1.5 px-2 text-xs bg-white border border-slate-200 rounded-lg outline-none">
                                <option value="Standard Box">Standard Box</option>
                                <option value="Pallet">Pallet</option>
                                <option value="Container 40ft">Container 40ft</option>
                                <option value="Crate">Crate</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('shipments.index') }}" class="px-5 py-2.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 rounded-xl transition shadow-md">
                    Register & Generate Manifest
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    let itemIndex = 1;
    document.getElementById('addItemBtn').addEventListener('click', function () {
        const container = document.getElementById('itemsContainer');
        const row = document.createElement('div');
        row.className = 'grid grid-cols-1 sm:grid-cols-4 gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200 item-row';
        row.innerHTML = `
            <div>
                <input type="text" name="items[${itemIndex}][item_name]" placeholder="Item Name" required
                    class="w-full py-1.5 px-3 text-xs bg-white border border-slate-200 rounded-lg outline-none">
            </div>
            <div>
                <input type="number" name="items[${itemIndex}][quantity]" value="1" placeholder="Qty" required
                    class="w-full py-1.5 px-3 text-xs bg-white border border-slate-200 rounded-lg outline-none">
            </div>
            <div>
                <input type="number" step="0.01" name="items[${itemIndex}][weight]" value="10" placeholder="Weight (kg)" required
                    class="w-full py-1.5 px-3 text-xs bg-white border border-slate-200 rounded-lg outline-none">
            </div>
            <div class="flex items-center gap-2">
                <select name="items[${itemIndex}][package_type]" class="w-full py-1.5 px-2 text-xs bg-white border border-slate-200 rounded-lg outline-none">
                    <option value="Standard Box">Standard Box</option>
                    <option value="Pallet">Pallet</option>
                    <option value="Container 40ft">Container 40ft</option>
                    <option value="Crate">Crate</option>
                </select>
                <button type="button" onclick="this.closest('.item-row').remove()" class="text-rose-500 hover:text-rose-700 px-1"><i class="fa-solid fa-trash-can text-xs"></i></button>
            </div>
        `;
        container.appendChild(row);
        itemIndex++;
    });
</script>
@endsection
