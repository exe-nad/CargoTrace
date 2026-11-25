<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Display the authenticated user's profile and operational stats.
     */
    public function show(): View
    {
        $user = Auth::user();

        // Calculate role-specific telemetry & activity metrics
        $stats = [];
        if ($user->isAdmin()) {
            $stats = [
                'primary_label' => 'Total Pengguna Sistem',
                'primary_value' => User::count(),
                'primary_icon'  => 'fa-solid fa-users',
                'sec_label'     => 'Kargo Terpantau',
                'sec_value'     => Shipment::count(),
                'sec_icon'      => 'fa-solid fa-boxes-stacked',
                'tert_label'    => 'Insiden Terbuka',
                'tert_value'    => ShipmentException::whereIn('status', ['OPEN', 'INVESTIGATING'])->count(),
                'tert_icon'     => 'fa-solid fa-triangle-exclamation',
                'audit_label'   => 'Total Audit Logs',
                'audit_value'   => AuditLog::count(),
                'audit_icon'    => 'fa-solid fa-clipboard-check',
            ];
        } elseif ($user->isOperator()) {
            $stats = [
                'primary_label' => 'Kargo Aktif Bergerak',
                'primary_value' => Shipment::whereIn('current_status', ['BOOKED', 'PICKED_UP', 'IN_TRANSIT'])->count(),
                'primary_icon'  => 'fa-solid fa-truck-fast',
                'sec_label'     => 'Kargo Tertunda',
                'sec_value'     => Shipment::where('is_delayed', true)->count(),
                'sec_icon'      => 'fa-solid fa-clock-rotate-left',
                'tert_label'    => 'Tiket Insiden Aktif',
                'tert_value'    => ShipmentException::whereIn('status', ['OPEN', 'INVESTIGATING'])->count(),
                'tert_icon'     => 'fa-solid fa-triangle-exclamation',
                'audit_label'   => 'Insiden Selesai',
                'audit_value'   => ShipmentException::where('status', 'RESOLVED')->count(),
                'audit_icon'    => 'fa-solid fa-circle-check',
            ];
        } else {
            // Customer / Shipper
            $myShipments = Shipment::where('customer_id', $user->id);
            $totalCount = (clone $myShipments)->count();
            $inTransitCount = (clone $myShipments)->where('current_status', 'IN_TRANSIT')->count();
            $deliveredCount = (clone $myShipments)->where('current_status', 'DELIVERED')->count();
            $delayedCount = (clone $myShipments)->where('is_delayed', true)->count();

            // Fallback for demo customer if no shipments linked yet
            if ($totalCount === 0) {
                $totalCount = Shipment::count();
                $inTransitCount = Shipment::where('current_status', 'IN_TRANSIT')->count();
                $deliveredCount = Shipment::where('current_status', 'DELIVERED')->count();
                $delayedCount = Shipment::where('is_delayed', true)->count();
            }

            $stats = [
                'primary_label' => 'Total Pesanan Kargo',
                'primary_value' => $totalCount,
                'primary_icon'  => 'fa-solid fa-box-open',
                'sec_label'     => 'Sedang Dalam Perjalanan',
                'sec_value'     => $inTransitCount,
                'sec_icon'      => 'fa-solid fa-plane-departure',
                'tert_label'    => 'Kargo Terkirim Sukses',
                'tert_value'    => $deliveredCount,
                'tert_icon'     => 'fa-solid fa-circle-check',
                'audit_label'   => 'Kargo Delay / Tertahan',
                'audit_value'   => $delayedCount,
                'audit_icon'    => 'fa-solid fa-triangle-exclamation',
            ];
        }

        // Recent audit trail for this user or system
        $recentActivities = AuditLog::where('user_id', $user->id)
            ->latest()
            ->take(6)
            ->get();

        if ($recentActivities->isEmpty()) {
            $recentActivities = AuditLog::latest()->take(6)->get();
        }

        return view('profile.show', compact('user', 'stats', 'recentActivities'));
    }

    /**
     * Update the user's personal information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone'        => ['nullable', 'string', 'max:50'],
            'company_name' => ['nullable', 'string', 'max:255'],
        ]);

        $oldValues = [
            'name'         => $user->name,
            'email'        => $user->email,
            'phone'        => $user->phone,
            'company_name' => $user->company_name,
        ];

        $user->update($validated);

        $this->auditService->log('UPDATE_PROFILE', $user, $oldValues, $validated, $user);

        return back()->with('success', 'Biodata profil Anda berhasil diperbarui.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->auditService->log('UPDATE_PASSWORD', $user, null, null, $user);

        return back()->with('success', 'Kata sandi akun Anda berhasil diperbarui dengan aman.');
    }
}
