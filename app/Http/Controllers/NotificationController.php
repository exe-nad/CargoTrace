<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function markAsRead(Request $request, ?int $id = null): JsonResponse|RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        if ($id) {
            $notification = $user->notifications()->findOrFail($id);
            $notification->markAsRead();
        } else {
            $user->notifications()->where('is_read', false)->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notifications updated.');
    }
}
