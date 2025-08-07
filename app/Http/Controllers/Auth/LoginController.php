<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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

        // Primero verificar si las credenciales son correctas
        $user = User::where($loginType, $request->login)->first();
        
        if ($user && Hash::check($request->password, $user->password)) {
            // Verificar el estado de aprobación solo si el campo existe
            if (isset($user->status)) {
                if ($user->status === 'pending') {
                    return back()->withErrors([
                        'login' => 'Tu cuenta está pendiente de aprobación por un administrador. Recibirás una notificación cuando sea aprobada.',
                    ])->onlyInput('login');
                }
                
                if ($user->status === 'rejected') {
                    return back()->withErrors([
                        'login' => 'Tu solicitud de registro fue rechazada. Contacta al administrador para más información.',
                    ])->onlyInput('login');
                }
            }
            
            // Si el usuario está aprobado o no tiene campo status (usuarios antiguos), proceder con el login
            if (Auth::attempt($credentials, $remember)) {
                $request->session()->regenerate();
                
                // Configurar la duración de la sesión basada en "Recordarme"
                if (!$remember) {
                    config(['session.expire_on_close' => true]);
                }

                return redirect()->intended('dashboard');
            }
        }

        return back()->withErrors([
            'login' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        // Crear notificación de cierre de sesión
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
