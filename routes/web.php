<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\InfraccionController;
use App\Http\Controllers\InspeccionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ActaController;
use App\Http\Controllers\InspeccionVehicularController;

// Ruta principal - redirige al login si no está autenticado
Route::get('/', function () {
    return \Illuminate\Support\Facades\Auth::check() ? redirect('/dashboard') : redirect('/login');
});

// Rutas de autenticación
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    // Dashboard principal - redirige según el rol
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Notificaciones
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    
    // Información de sesión
    Route::get('/session-info', function () {
        return view('auth.session-info');
    })->name('session.info');
    
    // Gestión de usuarios (solo para administradores)
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::put('users/{id}/change-password', [UserController::class, 'changePassword'])->name('users.change-password');
        Route::put('users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    });
});

// Rutas para administradores y fiscalizadores (infracciones)
Route::middleware(['auth', 'multirole:administrador,fiscalizador'])->group(function () {
    Route::resource('infracciones', InfraccionController::class);
});

// Rutas para inspecciones (administrador, fiscalizador, ventanilla)
Route::middleware(['auth', 'multirole:administrador,fiscalizador,ventanilla'])->group(function () {
    Route::resource('inspecciones', InspeccionController::class);
});

// Rutas específicas por rol con middleware de protección
Route::middleware(['auth', 'role:administrador'])->group(function () {
    // Dashboard de administrador
    Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');

    // Mantenimientos
    // Rutas para Inspector/Fiscal
    Route::get('/admin/mantenimiento/fiscal', [App\Http\Controllers\InspectorController::class, 'index'])->name('admin.mantenimiento.fiscal');
    Route::post('/admin/inspectores', [App\Http\Controllers\InspectorController::class, 'store'])->name('inspectores.store');
    Route::get('/admin/inspectores/{id}', [App\Http\Controllers\InspectorController::class, 'show'])->name('inspectores.show');
    Route::put('/admin/inspectores/{id}', [App\Http\Controllers\InspectorController::class, 'update'])->name('inspectores.update');
    Route::delete('/admin/inspectores/{id}', [App\Http\Controllers\InspectorController::class, 'destroy'])->name('inspectores.destroy');
    Route::post('/admin/inspectores/{id}/toggle-status', [App\Http\Controllers\InspectorController::class, 'toggleStatus'])->name('inspectores.toggle-status');
    Route::get('/admin/inspectores/search', [App\Http\Controllers\InspectorController::class, 'search'])->name('inspectores.search');
    
    // Rutas para Conductor
    Route::get('/admin/mantenimiento/conductor', [App\Http\Controllers\ConductorController::class, 'index'])->name('admin.mantenimiento.conductor');
    Route::post('/admin/conductores', [App\Http\Controllers\ConductorController::class, 'store'])->name('conductores.store');
    Route::get('/admin/conductores/{id}', [App\Http\Controllers\ConductorController::class, 'show'])->name('conductores.show');
    Route::put('/admin/conductores/{id}', [App\Http\Controllers\ConductorController::class, 'update'])->name('conductores.update');
    Route::delete('/admin/conductores/{id}', [App\Http\Controllers\ConductorController::class, 'destroy'])->name('conductores.destroy');
    Route::post('/admin/conductores/{id}/toggle-status', [App\Http\Controllers\ConductorController::class, 'toggleStatus'])->name('conductores.toggle-status');
    Route::get('/admin/conductores/search', [App\Http\Controllers\ConductorController::class, 'search'])->name('conductores.search');
    
    // Rutas para Usuario
    Route::get('/admin/mantenimiento/usuario', [App\Http\Controllers\UserManagementController::class, 'index'])->name('admin.mantenimiento.usuario');
    Route::post('/admin/users', [App\Http\Controllers\UserManagementController::class, 'store'])->name('users.store');
    Route::get('/admin/users/{id}', [App\Http\Controllers\UserManagementController::class, 'show'])->name('users.show');
    Route::put('/admin/users/{id}', [App\Http\Controllers\UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/admin/users/{id}', [App\Http\Controllers\UserManagementController::class, 'destroy'])->name('users.destroy');
    Route::post('/admin/users/{id}/change-password', [App\Http\Controllers\UserManagementController::class, 'changePassword'])->name('users.change-password');
    Route::post('/admin/users/{id}/toggle-status', [App\Http\Controllers\UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::get('/admin/users/search', [App\Http\Controllers\UserManagementController::class, 'search'])->name('users.search');
});

Route::middleware(['auth', 'role:fiscalizador'])->group(function () {
    // Dashboard de fiscalizador
    Route::get('/fiscalizador/dashboard', [DashboardController::class, 'fiscalizadorDashboard'])->name('fiscalizador.dashboard');
    
    // Inspecciones
    Route::get('/fiscalizador/inspecciones', function () {
        return view('fiscalizador.inspecciones');
    })->name('fiscalizador.inspecciones');
    
    // Calendario
    Route::get('/fiscalizador/calendario', function () {
        return view('fiscalizador.calendario');
    })->name('fiscalizador.calendario');
    
    // Gestión de Actas
    Route::get('/fiscalizador/actas-contra', function () {
        return view('fiscalizador.actas-contra');
    })->name('fiscalizador.actas-contra');
    
    Route::get('/fiscalizador/carga-paga', function () {
        return view('fiscalizador.carga-paga');
    })->name('fiscalizador.carga-paga');
    
    Route::get('/fiscalizador/empresas', function () {
        return view('fiscalizador.empresas');
    })->name('fiscalizador.empresas');
    
    // Consultas y Reportes
    Route::get('/fiscalizador/consultas', function () {
        return view('fiscalizador.consultas');
    })->name('fiscalizador.consultas');
    
    Route::get('/fiscalizador/reportes', function () {
        return view('fiscalizador.reportes');
    })->name('fiscalizador.reportes');
});

Route::middleware(['auth', 'role:ventanilla'])->group(function () {
    // Dashboard de ventanilla
    Route::get('/ventanilla/dashboard', [DashboardController::class, 'ventanillaDashboard'])->name('ventanilla.dashboard');
    Route::get('/ventanilla/nueva-atencion', function () {
        return view('ventanilla.nueva-atencion');
    })->name('ventanilla.nueva-atencion');
    Route::get('/ventanilla/tramites', function () {
        return view('ventanilla.tramites');
    })->name('ventanilla.tramites');
    Route::get('/ventanilla/consultar', function () {
        return view('ventanilla.consultar');
    })->name('ventanilla.consultar');
    Route::get('/ventanilla/cola-espera', function () {
        return view('ventanilla.cola-espera');
    })->name('ventanilla.cola-espera');
});

// Rutas API para AJAX (Fiscalizador)
Route::middleware(['auth', 'multirole:administrador,fiscalizador'])->prefix('api')->group(function () {
    // Rutas para Actas
    Route::post('/actas', [ActaController::class, 'store']);
    Route::get('/actas/pendientes', [ActaController::class, 'getPendientes']);
    Route::put('/actas/{id}/status', [ActaController::class, 'updateStatus']);
    
    // Rutas para datos de formularios
    Route::get('/vehiculos-activos', function () {
        $vehiculos = \App\Models\Vehiculo::where('estado', 'activo')
            ->select('id', 'placa', 'marca', 'modelo', 'año')
            ->get();
        return response()->json(['vehiculos' => $vehiculos]);
    });
    Route::get('/conductores-vigentes', function () {
        $conductores = \App\Models\Conductor::where('estado', 'activo')
            ->where('estado_licencia', 'vigente')
            ->select('id', 'nombres', 'apellidos', 'numero_licencia', 'dni')
            ->get()
            ->map(function($conductor) {
                $conductor->nombre = $conductor->nombres . ' ' . $conductor->apellidos;
                $conductor->licencia = $conductor->numero_licencia;
                return $conductor;
            });
        return response()->json(['conductores' => $conductores]);
    });
    Route::get('/infracciones', function () {
        $infracciones = \App\Models\Infraccion::where('estado', 'activo')
            ->select('id', 'codigo_infraccion as codigo', 'descripcion', 'multa_soles')
            ->get();
        return response()->json(['infracciones' => $infracciones]);
    });
    Route::get('/inspectores-activos', function () {
        $inspectores = \App\Models\Inspector::where('estado', 'activo')
            ->select('id', 'nombres', 'apellidos', 'codigo_inspector')
            ->get()
            ->map(function($inspector) {
                $inspector->nombre = $inspector->nombres . ' ' . $inspector->apellidos;
                return $inspector;
            });
        return response()->json(['inspectores' => $inspectores]);
    });
    
    // Rutas para Inspección Vehicular
    Route::post('/inspeccion/iniciar', [InspeccionVehicularController::class, 'iniciar']);
    Route::post('/inspeccion/registrar', [InspeccionVehicularController::class, 'registrarInspeccion']);
    Route::post('/verificar-licencia', [InspeccionVehicularController::class, 'verificarLicencia']);
});
