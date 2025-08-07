<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class UserApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Mostrar lista de usuarios pendientes de aprobación
     */
    public function index()
    {
        $pendingUsers = User::where('status', 'pending')->latest()->get();
        $rejectedUsers = User::where('status', 'rejected')->latest()->limit(10)->get();
        
        return view('admin.user-approval.index', compact('pendingUsers', 'rejectedUsers'));
    }

    /**
     * Aprobar un usuario
     */
    public function approve(Request $request, User $user)
    {
        if ($user->status !== 'pending') {
            return back()->with('error', 'Este usuario ya no está pendiente de aprobación.');
        }

        $user->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);

        // Crear una sola notificación para el usuario aprobado
        Notification::create([
            'title' => '¡Cuenta aprobada!',
            'message' => 'Tu cuenta ha sido aprobada por un administrador. Ya puedes iniciar sesión en el sistema.',
            'type' => 'success',
            'user_id' => $user->id,
        ]);

        return back()->with([
            'success' => "Usuario {$user->username} aprobado exitosamente.",
            'toast' => [
                'type' => 'success',
                'title' => '¡Usuario Aprobado!',
                'message' => "El usuario {$user->username} ha sido aprobado y puede acceder al sistema."
            ]
        ]);
    }

    /**
     * Rechazar un usuario
     */
    public function reject(Request $request, User $user)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        if ($user->status !== 'pending') {
            return back()->with('error', 'Este usuario ya no está pendiente de aprobación.');
        }

        $user->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'approved_by' => Auth::id(),
        ]);

        // Notificar al usuario rechazado
        Notification::create([
            'title' => 'Solicitud rechazada',
            'message' => "Tu solicitud de registro ha sido rechazada. Motivo: {$request->rejection_reason}",
            'type' => 'danger',
            'user_id' => $user->id,
        ]);

        // Notificar al administrador
        Notification::create([
            'title' => 'Usuario rechazado',
            'message' => "Has rechazado la cuenta de {$user->username} ({$user->email}).",
            'type' => 'info',
            'user_id' => Auth::id(),
        ]);

        return back()->with([
            'success' => "Usuario {$user->username} rechazado.",
            'toast' => [
                'type' => 'warning',
                'title' => 'Usuario Rechazado',
                'message' => "La solicitud de {$user->username} ha sido rechazada."
            ]
        ]);
    }

    /**
     * Ver detalles de un usuario pendiente
     */
    public function show(User $user)
    {
        if ($user->status === 'approved') {
            return redirect()->route('admin.users.index')->with('info', 'Este usuario ya está aprobado.');
        }

        return view('admin.user-approval.show', compact('user'));
    }
}
