<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CarrierController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    public function index(): View
    {
        $carriers = Carrier::withCount(['shipments', 'shipments as delayed_count' => fn ($q) => $q->where('is_delayed', true)])
            ->orderBy('name')
            ->get();

        return view('carriers.index', compact('carriers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403, 'Only Administrators can register new carrier logistics partners.');
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:10', 'unique:carriers,code'],
            'name' => ['required', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'service_type' => ['required', 'string', 'max:100'],
            'website' => ['nullable', 'url', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $carrier = Carrier::create($validated);
        $this->auditService->log('CREATE_CARRIER', $carrier, null, $carrier->toArray(), $user);

        return back()->with('success', sprintf('Carrier "%s" registered successfully.', $carrier->name));
    }
}
