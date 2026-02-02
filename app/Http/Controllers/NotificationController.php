<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Afficher la page de notifications uniquement
    public function index() {
         return view('notifications.index');
    }

    // Récupérer les notifications en JSON pour le dropdown et la page
    public function getNotifications(Request $request) {
        $limit = $request->input('limit', 10);
        
        $notifications = \App\Models\Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
        
        return response()->json($notifications);
    }

    // Marquer comme lu via JavaScript Vanilla
    public function markAsRead($id) {
        try {
            $notification = Notification::findOrFail($id);
            
            \Log::info('Mark as read attempt', [
                'notification_id' => $id,
                'notification_user_id' => $notification->user_id,
                'auth_id' => Auth::id(),
                'is_read_before' => $notification->is_read
            ]);
            
            // Update the notification
            $notification->update(['is_read' => true]);
            
            \Log::info('Mark as read success', [
                'notification_id' => $id,
                'is_read_after' => $notification->fresh()->is_read
            ]);
            
            return response()->json(['success' => true, 'message' => 'Marked as read']);
        } catch (\Exception $e) {
            \Log::error('Mark as read error', [
                'error' => $e->getMessage(),
                'notification_id' => $id ?? 'N/A'
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}