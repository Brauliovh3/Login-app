<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Creando funciones basicas para el CRUD de usuarios 
class UserController extends Controller
{

    public function index()
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }


    public function create()
    {
        if (Auth::user()->role !== 'administrador'){
            abort(403, 'No tienes permisos para crear usuarios.');
        }
        return view('users.create');
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:administrador,ventanilla,fiscalizador',
        ]);

        $validated['password']= bcrypt($validated['password']);
        User::create($validated);
        
        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
    }
    
    public function edit($id)
    {
        if(Auth::user()->role !== 'administrador'){
            abort(403, 'No tienes permisos para editar usuarios.');
        }
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }


    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:administrador,ventanilla,fiscalizador',

        ]);
        if ($request->filled('password')) {
            $validated['password'] = bcrypt($request->password);
    }
        $user->update($validated);
        return redirect()->route('users.index')->with('success', 'Usuario actualizado corectamente');
    }


    public function destroy($id)
    {
        if(Auth::user()->role !== 'administrador'){
            abort(403, 'No tienes permisos para eliminar usuarios.');
        }
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario eliminado correctamente');
    }
}
