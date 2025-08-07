<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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

        // Redirigir directamente al login con mensaje de éxito
        return redirect()->route('login')->with([
            'status' => 'Registro exitoso. Tu solicitud está pendiente de aprobación por un administrador. Te notificaremos por email cuando sea aprobada.',
            'toast' => [
                'type' => 'success',
                'title' => '¡Registro Exitoso!',
                'message' => 'Tu solicitud ha sido enviada. Espera la aprobación del administrador para poder iniciar sesión.',
                'duration' => 8000
            ]
        ]);
    }
}
