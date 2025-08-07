<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->update(['read_status' => true]);
        
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Auth::user()->notifications()->update(['read_status' => true]);
        
        return redirect()->back()->with('success', 'Todas las notificaciones han sido marcadas como leídas');
    }

    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();
        
        return redirect()->back()->with('success', 'Notificación eliminada');
    }

    public function getUnreadCount()
    {
        $user = Auth::user();
        $count = $user->notifications()->where('read_status', false)->count();
        $latest = $user->notifications()
            ->where('read_status', false)
            ->orderBy('created_at', 'desc')
            ->first();
        
        return response()->json([
            'count' => $count,
            'latest' => $latest
        ]);
    }
}
