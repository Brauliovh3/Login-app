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
            'name' => $request->username, // Usa el username como nombre
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        Auth::login($user);

        // Crear notificación de bienvenida
        Notification::create([
            'title' => '¡Bienvenido!',
            'message' => 'Tu cuenta ha sido creada exitosamente. Rol asignado: ' . ucfirst($request->role),
            'type' => 'success',
            'user_id' => $user->id,
        ]);

        return redirect('dashboard');
    }
}
