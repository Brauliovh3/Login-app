<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ActaController;

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
Route::get('/register/success', function () {
    return view('auth.register-success');
})->name('register.success');

// Rutas protegidas por autenticación y aprobación
Route::middleware(['auth', 'user.approved'])->group(function () {
    // Dashboard principal - redirige según el rol
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
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

// Rutas específicas por rol con middleware de protección
Route::middleware(['auth', 'user.approved', 'role:administrador'])->group(function () {
    // Dashboard de administrador
    Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
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
    Route::get('/fiscalizador/actas-contra', [ActaController::class, 'index'])->name('fiscalizador.actas-contra');
    
    // Rutas para Actas AJAX
    Route::post('/fiscalizador/actas', [ActaController::class, 'store'])->name('actas.store');
    Route::get('/fiscalizador/actas/{id}', [ActaController::class, 'show'])->name('actas.show');
    Route::put('/fiscalizador/actas/{id}', [ActaController::class, 'update'])->name('actas.update');
    Route::delete('/fiscalizador/actas/{id}', [ActaController::class, 'destroy'])->name('actas.destroy');
    Route::get('/fiscalizador/actas-consultas', [ActaController::class, 'consultas'])->name('actas.consultas');
    Route::get('/fiscalizador/actas-exportar', [ActaController::class, 'exportarExcel'])->name('actas.exportar');
    Route::get('/fiscalizador/actas-proximo-numero', [ActaController::class, 'proximoNumero'])->name('actas.proximo-numero');
    
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

// Rutas API para AJAX (Fiscalizador)
Route::middleware(['auth', 'user.approved', 'multirole:administrador,fiscalizador'])->prefix('api')->group(function () {
    // Rutas para Actas con seguimiento automático de tiempo
    Route::post('/actas', [ActaController::class, 'store']);
    Route::get('/actas/pendientes', [ActaController::class, 'getPendientes']);
    Route::put('/actas/{id}/status', [ActaController::class, 'updateStatus']);
    Route::post('/actas/{id}/finalizar', [ActaController::class, 'finalizarRegistro']);
    Route::post('/actas/{id}/progreso', [ActaController::class, 'guardarProgreso']);
});
