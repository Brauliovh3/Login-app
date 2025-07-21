<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Notification;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Redirigir a la URL específica del rol
        switch ($user->role) {
            case 'administrador':
                return redirect()->route('admin.dashboard');
            case 'fiscalizador':
                return redirect()->route('fiscalizador.dashboard');
            case 'ventanilla':
                return redirect()->route('ventanilla.dashboard');
            default:
                abort(403, 'Rol no válido.');
        }
    }

    public function adminDashboard()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->take(5)->get();
        
        $stats = [
            'total_users' => User::count(),
            'admin_users' => User::where('role', 'administrador')->count(),
            'fiscalizador_users' => User::where('role', 'fiscalizador')->count(),
            'ventanilla_users' => User::where('role', 'ventanilla')->count(),
            'total_notifications' => \App\Models\Notification::count(),
            'unread_notifications' => $user->notifications()->where('read', false)->count(),
        ];
        
        return view('administrador.dashboard', compact('notifications', 'stats'));
    }

    public function fiscalizadorDashboard()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->take(5)->get();
        
        $stats = [
            'pending_reviews' => 12,
            'completed_reviews' => 45,
            'critical_cases' => 8,
            'efficiency_rate' => 95,
            'pending_audits' => 3,
            'monthly_reports' => 15
        ];
        
        return view('fiscalizador.dashboard', compact('notifications', 'stats'));
    }

    public function ventanillaDashboard()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->take(5)->get();
        
        $stats = [
            'pending_tasks' => 23,
            'completed_tasks' => 87,
            'clients_waiting' => 15,
            'satisfaction_rate' => 98,
            'documents_processed' => 156,
            'average_time' => 12
        ];
        
        return view('ventanilla.dashboard', compact('notifications', 'stats'));
    }
}
