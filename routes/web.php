<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
// use App\Http\Controllers\NotificationController; // CONTROLADOR ELIMINADO
use App\Http\Controllers\InfraccionController;
use App\Http\Controllers\InspeccionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ActaController;
use App\Http\Controllers\InspeccionVehicularController;

// Ruta principal - redirige al login si no está autenticado
Route::get('/', function () {
    return \Illuminate\Support\Facades\Auth::check() ? redirect('/dashboard') : redirect('/login');
});

// Ruta de prueba para el formulario de actas
Route::get('/test-formulario', function () {
    return view('test-formulario-funcional');
})->name('test.formulario');

// Rutas de autenticación
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/register/success', function () {
    return view('auth.register-success');
})->name('register.success');

// Rutas protegidas por autenticación y aprobación
Route::middleware(['auth', 'user.approved'])->group(function () {
    // Dashboard principal - redirige según el rol
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Notificaciones - SISTEMA ELIMINADO
    // Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    // Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    // Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    // Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    // Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    
    // Información de sesión
    Route::get('/session-info', function () {
        return view('auth.session-info');
    })->name('session.info');
    
    // Ruta temporal para evitar errores de notificaciones
    Route::get('/notifications/unread-count', function () {
        return response()->json(['count' => 0, 'latest' => null]);
    })->name('notifications.unread-count');
    
    // Gestión de usuarios (solo para administradores)
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::put('users/{id}/change-password', [UserController::class, 'changePassword'])->name('users.change-password');
        Route::put('users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        
        // Gestión de aprobación de usuarios
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/users/approval', [App\Http\Controllers\Admin\UserApprovalController::class, 'index'])->name('users.approval');
            Route::post('/users/{user}/approve', [App\Http\Controllers\Admin\UserApprovalController::class, 'approve'])->name('users.approve');
            Route::post('/users/{user}/reject', [App\Http\Controllers\Admin\UserApprovalController::class, 'reject'])->name('users.reject');
            Route::get('/users/{user}/details', [App\Http\Controllers\Admin\UserApprovalController::class, 'show'])->name('users.details');
        });
    });
});

// Rutas para administradores y fiscalizadores (infracciones)
Route::middleware(['auth', 'user.approved', 'multirole:administrador,fiscalizador'])->group(function () {
    Route::resource('infracciones', InfraccionController::class);
});

// Rutas para inspecciones (administrador, fiscalizador, ventanilla)
Route::middleware(['auth', 'user.approved', 'multirole:administrador,fiscalizador,ventanilla'])->group(function () {
    Route::resource('inspecciones', InspeccionController::class);
});

// Rutas específicas por rol con middleware de protección
Route::middleware(['auth', 'user.approved', 'role:administrador'])->group(function () {
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

    // Administración de actas: reiniciar AUTO_INCREMENT (solo administradores)
    Route::post('/admin/actas/reset-autoincrement', [App\Http\Controllers\Admin\ActasMaintenanceController::class, 'resetAutoIncrement'])->name('admin.actas.reset-autoincrement');
});

Route::middleware(['auth', 'user.approved', 'role:fiscalizador'])->group(function () {
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

Route::middleware(['auth', 'user.approved', 'role:ventanilla'])->group(function () {
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

Route::middleware(['auth', 'user.approved', 'role:inspector'])->group(function () {
    // Dashboard de inspector
    Route::get('/inspector/dashboard', [DashboardController::class, 'inspectorDashboard'])->name('inspector.dashboard');
    
    // Nueva inspección
    Route::get('/inspector/nueva-inspeccion', [DashboardController::class, 'inspectorNuevaInspeccion'])->name('inspector.nueva-inspeccion');
    Route::post('/inspector/nueva-inspeccion', [DashboardController::class, 'inspectorNuevaInspeccionStore'])->name('inspector.nueva-inspeccion.store');
    
    // Gestión de inspecciones
    Route::get('/inspector/inspecciones', [DashboardController::class, 'inspectorInspecciones'])->name('inspector.inspecciones');
    
    // Gestión de vehículos
    Route::get('/inspector/vehiculos', [DashboardController::class, 'inspectorVehiculos'])->name('inspector.vehiculos');
    
    // Reportes
    Route::get('/inspector/reportes', [DashboardController::class, 'inspectorReportes'])->name('inspector.reportes');
});

// Superadmin hidden panel - access only by users with role 'superadmin'
Route::middleware(['auth', 'user.approved', 'role:superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/super', [App\Http\Controllers\Admin\SuperAdminController::class, 'index'])->name('super.index');
    Route::post('/super/cache-clear', [App\Http\Controllers\Admin\SuperAdminController::class, 'cacheClear'])->name('super.cache-clear');
    Route::post('/super/config-cache', [App\Http\Controllers\Admin\SuperAdminController::class, 'configCache'])->name('super.config-cache');
    Route::post('/super/reset-actas', [App\Http\Controllers\Admin\SuperAdminController::class, 'resetActas'])->name('super.reset-actas');
    Route::get('/super/app-info', [App\Http\Controllers\Admin\SuperAdminController::class, 'appInfo'])->name('super.app-info');
    Route::post('/super/run-command', [App\Http\Controllers\Admin\SuperAdminController::class, 'runCommand'])->name('super.run-command');
    Route::get('/super/stats', [App\Http\Controllers\Admin\SuperAdminController::class, 'stats'])->name('super.stats');
    Route::get('/super/users', [App\Http\Controllers\Admin\SuperAdminController::class, 'usersList'])->name('super.users');
    Route::post('/super/users/{id}/toggle-status', [App\Http\Controllers\Admin\SuperAdminController::class, 'toggleUserStatus'])->name('super.users.toggle-status');
    Route::post('/super/users/{id}/approve', [App\Http\Controllers\Admin\SuperAdminController::class, 'approveUser'])->name('super.users.approve');
    // Temporary debug endpoint to inspect authenticated user while troubleshooting
    Route::get('/super/debug-user', function(){ return response()->json(['user' => auth()->user()]); })->name('super.debug-user');
});

// Auth-only debug route (no role check) to inspect authenticated user in session
Route::middleware(['auth', 'user.approved'])->get('/admin/super/debug-auth', function () {
    return response()->json(['user' => auth()->user()]);
})->name('super.debug-auth');


// Rutas API para AJAX (Fiscalizador)
Route::middleware(['auth', 'user.approved', 'multirole:administrador,fiscalizador'])->prefix('api')->group(function () {
    // Rutas para Actas con seguimiento automático de tiempo
    Route::post('/actas', [ActaController::class, 'store']);
    Route::get('/actas/{id}', [ActaController::class, 'show']);
    Route::get('/actas/pendientes', [ActaController::class, 'getPendientes']);
    Route::put('/actas/{id}/status', [ActaController::class, 'updateStatus']);
    Route::post('/actas/{id}/finalizar', [ActaController::class, 'finalizarRegistro']);
    Route::post('/actas/{id}/progreso', [ActaController::class, 'guardarProgreso']);
    
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
        $infracciones = \DB::table('infracciones')
            ->select('id', 'codigo_infraccion as codigo', 'descripcion', 'multa_soles', 'tipo_infraccion')
            ->get();
        return response()->json(['infracciones' => $infracciones]);
    });
    Route::get('/inspectores-activos', function () {
        $inspectores = \DB::table('inspectores')
            ->where('estado', 'activo')
            ->select('id', 'nombres', 'apellidos', 'codigo_inspector')
            ->get()
            ->map(function($inspector) {
                $inspector->nombre = $inspector->nombres . ' ' . $inspector->apellidos;
                return $inspector;
            });
        return response()->json(['inspectores' => $inspectores]);
    });
    
    // Nueva ruta para consulta simple por DNI/RUC
    Route::get('/consultar-actas/{documento}', [ActaController::class, 'consultarPorDocumento']);
    Route::get('/consultar-actas', [ActaController::class, 'consultarActas']);
    
    // Rutas para Inspección Vehicular
    Route::post('/inspeccion/iniciar', [InspeccionVehicularController::class, 'iniciar']);
    Route::post('/inspeccion/registrar', [InspeccionVehicularController::class, 'registrarInspeccion']);
    Route::post('/verificar-licencia', [InspeccionVehicularController::class, 'verificarLicencia']);
});

// Ruta de prueba temporal para depuración (sin autenticación)
Route::post('/api/test-actas', [ActaController::class, 'store'])->name('test.actas');
Route::get('/api/test-consulta/{documento}', [ActaController::class, 'consultarPorDocumento'])->name('test.consulta');
Route::get('/api/csrf-token', function() {
    return response()->json(['token' => csrf_token()]);
});
