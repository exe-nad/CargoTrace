<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Unauthorized. Only Administrators can view the CargoTrace audit log.');
        }

        $query = AuditLog::with('user');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('search')) {
            $search = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', $search)
                  ->orWhere('entity_type', 'like', $search)
                  ->orWhere('ip_address', 'like', $search)
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $search)->orWhere('email', 'like', $search));
            });
        }

        $logs = $query->latest('created_at')->paginate(20)->withQueryString();

        return view('audit.index', compact('logs'));
    }
}
