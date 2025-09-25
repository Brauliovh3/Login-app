<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
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

    /**
     * API endpoint para obtener usuarios (para dashboard.php)
     */
    public function apiIndex(Request $request)
    {
        try {
            $query = User::query();
            
            // Búsqueda si se proporciona
            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('username', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('role', 'LIKE', "%{$search}%");
                });
            }
            
            $users = $query->orderBy('created_at', 'desc')
                          ->select('id', 'name', 'username', 'email', 'role', 'status', 'created_at')
                          ->get();
            
            return response()->json([
                'success' => true,
                'users' => $users
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener usuarios: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API endpoint para obtener usuarios pendientes de aprobación
     */
    public function apiPending(Request $request)
    {
        try {
            $users = User::where('status', 'pending')
                        ->orderBy('created_at', 'desc')
                        ->select('id', 'name', 'username', 'email', 'role', 'status', 'created_at')
                        ->get();
            
            return response()->json([
                'success' => true,
                'users' => $users
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener usuarios pendientes: ' . $e->getMessage()
            ], 500);
        }
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
            'username' => 'required|string|max:255|unique:usuarios',
            'email' => 'required|email|max:255|unique:usuarios',
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
            'username' => ['required', 'string', 'max:255', Rule::unique('usuarios')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('usuarios')->ignore($user->id)],
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

    // Cambiar contraseña de usuario
    public function changePassword(Request $request, $id)
    {
        if(Auth::user()->role !== 'administrador'){
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para cambiar contraseñas.'
                ], 403);
            }
            abort(403, 'No tienes permisos para cambiar contraseñas.');
        }
        
        $user = User::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed',
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

        $user->update([
            'password' => Hash::make($request->password)
        ]);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Contraseña actualizada exitosamente'
            ]);
        }
        
        return redirect()->route('users.index')->with('success', 'Contraseña actualizada correctamente');
    }

    // Bloquear/Desbloquear usuario
    public function toggleStatus(Request $request, $id)
    {
        if(Auth::user()->role !== 'administrador'){
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para cambiar el estado de usuarios.'
                ], 403);
            }
            abort(403, 'No tienes permisos para cambiar el estado de usuarios.');
        }
        
        $user = User::findOrFail($id);
        
        // Evitar que el usuario se bloquee a sí mismo
        if (Auth::user()->id == $user->id) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes cambiar tu propio estado'
                ], 403);
            }
            return redirect()->route('users.index')->with('error', 'No puedes cambiar tu propio estado');
        }
        
        $action = $request->input('action');
        $message = '';
        
        if ($action === 'block') {
            $user->update(['blocked_at' => now()]);
            $message = 'Usuario bloqueado exitosamente';
        } elseif ($action === 'unblock') {
            $user->update(['blocked_at' => null]);
            $message = 'Usuario desbloqueado exitosamente';
        } else {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acción no válida'
                ], 400);
            }
            return redirect()->route('users.index')->with('error', 'Acción no válida');
        }
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }
        
        return redirect()->route('users.index')->with('success', $message);
    }

    public function reject(Request $request, User $user)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $user->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'approved_at' => null,
            'approved_by' => null,
        ]);

        return back()->with('success', 'Usuario rechazado correctamente.');
    }

    // Métodos para perfil del usuario
    public function perfil()
    {
        $user = Auth::user();
        return view('user.perfil', compact('user'));
    }

    public function updatePerfil(Request $request)
    {
        $user = Auth::user();
        
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:usuarios,email,' . $user->id,
            'username' => 'required|string|max:255|unique:usuarios,username,' . $user->id,
        ];

        // Solo validar phone si la tabla tiene esa columna
        if (Schema::hasColumn('usuarios', 'phone')) {
            $rules['phone'] = 'nullable|string|max:20';
        }

        if ($request->filled('current_password')) {
            $rules['current_password'] = 'required';
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Verificar contraseña actual si se quiere cambiar
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
            }
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
        ];

        // Solo actualizar phone si la tabla tiene esa columna
        if (Schema::hasColumn('usuarios', 'phone')) {
            $data['phone'] = $request->phone;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    public function configuracion()
    {
        $user = Auth::user();
        return view('user.configuracion', compact('user'));
    }

    public function updateConfiguracion(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'notifications_enabled' => 'boolean',
            'email_notifications' => 'boolean',
            'theme' => 'in:light,dark,auto',
            'language' => 'in:es,en',
            'timezone' => 'string|max:50',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // Aquí puedes guardar las configuraciones
        // Por ahora las guardamos en la sesión como ejemplo
        session([
            'user_config' => [
                'notifications_enabled' => $request->boolean('notifications_enabled', true),
                'email_notifications' => $request->boolean('email_notifications', true),
                'theme' => $request->get('theme', 'light'),
                'language' => $request->get('language', 'es'),
                'timezone' => $request->get('timezone', 'America/Lima'),
            ]
        ]);

        return back()->with('success', 'Configuración actualizada correctamente.');
    }
}
