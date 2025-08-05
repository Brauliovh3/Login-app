<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Notification;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:administrador,fiscalizador,ventanilla,inspector'],
        ]);

        $user = User::create([
            'name' => $request->username,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => 'pending', // Estado pendiente por defecto
        ]);

        // No iniciar sesión automáticamente
        // Auth::login($user);

        // Crear notificación para el usuario recién registrado
        Notification::create([
            'title' => 'Solicitud de registro enviada',
            'message' => 'Tu solicitud de registro ha sido enviada. Un administrador revisará tu cuenta y te notificará cuando sea aprobada.',
            'type' => 'info',
            'user_id' => $user->id,
        ]);

        // Notificar a todos los administradores
        $admins = User::where('role', 'administrador')->where('status', 'approved')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'title' => 'Nueva solicitud de registro',
                'message' => "Usuario '{$user->username}' ({$user->email}) solicita acceso como {$user->role}. Revisa y aprueba la solicitud en el panel de administración.",
                'type' => 'warning',
                'user_id' => $admin->id,
            ]);
        }

        // Redirigir con mensaje de éxito
        return redirect()->route('login')->with('status', 'Registro exitoso. Tu solicitud está pendiente de aprobación por un administrador. Te notificaremos por email cuando sea aprobada.');
    }
}
