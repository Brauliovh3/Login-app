<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Notification;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => ['required'],
            'password' => ['required'],
        ]);

        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        $credentials = [
            $loginType => $request->login,
            'password' => $request->password,
        ];

        // Verificar si se marcó "Recordarme"
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            // Configurar la duración de la sesión basada en "Recordarme"
            if (!$remember) {
                // Si no se marcó "Recordarme", la sesión expira al cerrar el navegador
                config(['session.expire_on_close' => true]);
            }
            
            // Crear notificación de inicio de sesión
            Notification::create([
                'title' => 'Inicio de sesión exitoso',
                'message' => $remember 
                    ? 'Has iniciado sesión correctamente. Tu sesión se mantendrá activa.' 
                    : 'Has iniciado sesión correctamente. La sesión expirará al cerrar el navegador.',
                'type' => 'success',
                'user_id' => Auth::id(),
            ]);

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'login' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        // Crear notificación de cierre de sesión
        if (Auth::check()) {
            Notification::create([
                'title' => 'Sesión cerrada',
                'message' => 'Has cerrado sesión correctamente.',
                'type' => 'info',
                'user_id' => Auth::id(),
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
