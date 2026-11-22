<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill of Lading - {{ $shipment->shipment_number }} - CargoTrace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Space+Mono:wght@400;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'Space Mono', monospace; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 p-4 sm:p-8 text-slate-800">

    <div class="max-w-4xl mx-auto bg-white border border-slate-300 shadow-xl rounded-2xl p-8 sm:p-12 print:border-0 print:shadow-none print:p-0">

        <!-- Action Print Button Header -->
        <div class="no-print flex items-center justify-between pb-6 mb-6 border-b border-slate-200">
            <a href="{{ route('tracking.show', $shipment->shipment_number) }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 flex items-center gap-1">
                <i class="fa-solid fa-arrow-left"></i> Back to Tracking
            </a>
            <button onclick="window.print()" class="px-5 py-2.5 bg-slate-900 text-white rounded-xl text-xs font-bold shadow hover:bg-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Print Document / Save as PDF
            </button>
        </div>

        <!-- Official Header -->
        <div class="flex items-start justify-between border-b-2 border-slate-900 pb-6 mb-6">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <div class="w-8 h-8 rounded-lg bg-slate-900 text-cyan-400 flex items-center justify-center text-sm font-black">
                        <i class="fa-solid fa-ship"></i>
                    </div>
                    <span class="text-xl font-black tracking-tight text-slate-900">CargoTrace</span>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400 block">Global Multimodal Logistics Network</span>
                <span class="text-xs text-slate-500 mt-1 block">Verified Telemetry & Cargo Manifest Verification</span>
            </div>

            <div class="text-right">
                <span class="text-xs font-bold uppercase text-slate-400 block">Consignment Waybill No.</span>
                <span class="text-xl font-black font-mono text-slate-900 block">{{ $shipment->shipment_number }}</span>
                <span class="text-[10px] text-slate-500">Date Issued: {{ now()->format('Y-m-d') }}</span>
            </div>
        </div>

        <!-- Shipper & Carrier Info Grid -->
        <div class="grid grid-cols-2 gap-6 text-xs mb-6 border border-slate-200 rounded-xl p-5 bg-slate-50/50">
            <div>
                <span class="text-[10px] font-bold uppercase text-slate-400 block mb-1">Shipper / Consignee:</span>
                <p class="font-bold text-slate-900 text-sm">{{ $shipment->customer?->company_name ?? $shipment->customer?->name }}</p>
                <p class="text-slate-600 mt-0.5">{{ $shipment->customer?->email }}</p>
                <p class="text-slate-600">{{ $shipment->customer?->phone ?? 'Contact on file' }}</p>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase text-slate-400 block mb-1">Assigned Freight Carrier:</span>
                <p class="font-bold text-slate-900 text-sm">{{ $shipment->carrier?->name }}</p>
                <p class="text-slate-600 mt-0.5">Service: {{ $shipment->carrier?->service_type }}</p>
                <p class="text-slate-600">Carrier Code: {{ $shipment->carrier?->code }}</p>
            </div>
        </div>

        <!-- Port of Loading & Discharge -->
        <div class="grid grid-cols-2 gap-6 text-xs mb-6 border border-slate-200 rounded-xl p-5">
            <div>
                <span class="text-[10px] font-bold uppercase text-slate-400 block mb-1">Port / Gateway of Loading:</span>
                <p class="font-bold text-slate-900 text-sm">{{ $shipment->origin?->name }}</p>
                <p class="text-slate-600">{{ $shipment->origin?->city }}, {{ $shipment->origin?->country }}</p>
                <p class="font-mono text-[11px] text-slate-500 mt-1">Expected Dep: {{ $shipment->expected_departure?->format('M d, Y H:i') }}</p>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase text-slate-400 block mb-1">Port / Gateway of Discharge:</span>
                <p class="font-bold text-slate-900 text-sm">{{ $shipment->destination?->name }}</p>
                <p class="text-slate-600">{{ $shipment->destination?->city }}, {{ $shipment->destination?->country }}</p>
                <p class="font-mono text-[11px] text-slate-500 mt-1">Expected Arr: {{ $shipment->expected_arrival?->format('M d, Y H:i') }}</p>
            </div>
        </div>

        <!-- Manifest Items Table -->
        <div class="mb-6">
            <h4 class="text-xs font-bold uppercase text-slate-500 mb-2">Description of Packages & Goods</h4>
            <table class="w-full text-left text-xs border border-slate-200 border-collapse">
                <thead class="bg-slate-100 text-slate-600 uppercase font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-2.5 px-4">Item & Specification</th>
                        <th class="py-2.5 px-4 font-mono">SKU / HS Code</th>
                        <th class="py-2.5 px-4">Packaging</th>
                        <th class="py-2.5 px-4 text-right">Quantity</th>
                        <th class="py-2.5 px-4 text-right">Gross Weight</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($shipment->items as $item)
                        <tr>
                            <td class="py-3 px-4 font-semibold text-slate-900">{{ $item->item_name }}</td>
                            <td class="py-3 px-4 font-mono text-slate-600">{{ $item->sku ?? $item->hs_code ?? '8471.50' }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $item->package_type }}</td>
                            <td class="py-3 px-4 text-right font-bold">{{ $item->quantity }} units</td>
                            <td class="py-3 px-4 text-right font-mono">{{ number_format($item->weight, 2) }} kg</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="py-3 px-4 font-semibold text-slate-900">{{ $shipment->cargo_type }}</td>
                            <td class="py-3 px-4 font-mono text-slate-600">8471.50</td>
                            <td class="py-3 px-4 text-slate-600">Consolidated Container</td>
                            <td class="py-3 px-4 text-right font-bold">1 Consignment</td>
                            <td class="py-3 px-4 text-right font-mono">{{ number_format($shipment->total_weight, 2) }} kg</td>
                        </tr>
                    @endforelse
                    <tr class="bg-slate-50 font-bold border-t-2 border-slate-300">
                        <td colspan="3" class="py-2.5 px-4 uppercase text-slate-700">Total Consignment Declared:</td>
                        <td class="py-2.5 px-4 text-right">{{ $shipment->items->sum('quantity') ?: 1 }} items</td>
                        <td class="py-2.5 px-4 text-right font-mono text-cyan-800">{{ number_format($shipment->total_weight, 2) }} kg</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Checkpoints Summary -->
        <div class="mb-8 p-4 rounded-xl border border-slate-200 bg-slate-50/50 text-xs">
            <span class="text-[10px] font-bold uppercase text-slate-400 block mb-1">Telemetry Status & Verification</span>
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-slate-600">Current Lifecycle Status:</span>
                    <span class="font-bold text-slate-900 uppercase ml-1">{{ $shipment->current_status }}</span>
                </div>
                <div>
                    <span class="text-slate-600">Delay Assessment:</span>
                    @if($shipment->is_delayed)
                        <span class="font-bold text-rose-600 uppercase ml-1">+{{ $shipment->delay_hours }}h Delayed ({{ $shipment->delay_severity }})</span>
                    @else
                        <span class="font-bold text-emerald-600 uppercase ml-1">On Schedule</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Official Signatures -->
        <div class="grid grid-cols-2 gap-12 pt-8 border-t border-slate-200 text-xs mt-8">
            <div class="text-center">
                <div class="h-16 border-b border-dashed border-slate-400 mb-2 flex items-center justify-center">
                    <span class="text-cyan-700 font-mono text-[11px] uppercase font-bold tracking-wider">[ Authorized Carrier Officer Signature ]</span>
                </div>
                <span class="font-bold text-slate-800">For Carrier ({{ $shipment->carrier?->name }})</span>
                <span class="text-[10px] text-slate-400 block">Terminal Port Master Authorization</span>
            </div>

            <div class="text-center">
                <div class="h-16 border-b border-dashed border-slate-400 mb-2 flex items-center justify-center">
                    <span class="text-slate-500 font-mono text-[11px] uppercase tracking-wider">[ Shipper Acceptance & Verification ]</span>
                </div>
                <span class="font-bold text-slate-800">For Consignee ({{ $shipment->customer?->company_name ?? $shipment->customer?->name }})</span>
                <span class="text-[10px] text-slate-400 block">Cargo Receipt & Manifest Sign-Off</span>
            </div>
        </div>

    </div>

</body>
</html>
