<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

// Controlador completo para el CRUD de usuarios con soporte para modales
class UserController extends Controller
{

    public function index(Request $request)
    {
        $query = User::query();
        
        // Búsqueda
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('username', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('role', 'LIKE', "%{$search}%");
            });
        }
        
        // Filtro por rol
        if ($request->has('role_filter') && $request->role_filter != '') {
            $query->where('role', $request->role_filter);
        }
        
        $users = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Si es una petición AJAX, devolver solo la tabla
        if ($request->ajax()) {
            return view('users.partials.table', compact('users'))->render();
        }
        
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:administrador,ventanilla,fiscalizador,inspector',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Usuario creado exitosamente',
                'user' => $user
            ]);
        }
        
        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
    }
    
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }
    
    public function edit($id)
    {
        if(Auth::user()->role !== 'administrador'){
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para editar usuarios.'
                ], 403);
            }
            abort(403, 'No tienes permisos para editar usuarios.');
        }
        
        $user = User::findOrFail($id);
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'user' => $user
            ]);
        }
        
        return view('users.edit', compact('user'));
    }


    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:administrador,ventanilla,fiscalizador,inspector',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'role' => $request->role,
        ];

        // Solo actualizar la contraseña si se proporciona
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado exitosamente',
                'user' => $user
            ]);
        }
        
        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente');
    }


    public function destroy($id)
    {
        if(Auth::user()->role !== 'administrador'){
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para eliminar usuarios.'
                ], 403);
            }
            abort(403, 'No tienes permisos para eliminar usuarios.');
        }
        
        $user = User::findOrFail($id);
        
        // Evitar que el usuario se elimine a sí mismo
        if (Auth::user()->id == $user->id) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes eliminar tu propia cuenta'
                ], 403);
            }
            return redirect()->route('users.index')->with('error', 'No puedes eliminar tu propia cuenta');
        }
        
        $user->delete();
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado exitosamente'
            ]);
        }
        
        return redirect()->route('users.index')->with('success', 'Usuario eliminado correctamente');
    }
}
