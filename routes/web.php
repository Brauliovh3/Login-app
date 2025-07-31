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
    Route::get('/admin/mantenimiento/fiscal', function () {
        return view('administrador.mantenimiento.fiscal');
    })->name('admin.mantenimiento.fiscal');
    
    Route::get('/admin/mantenimiento/conductor', function () {
        return view('administrador.mantenimiento.conductor');
    })->name('admin.mantenimiento.conductor');
    
    Route::get('/admin/mantenimiento/usuario', function () {
        return view('administrador.mantenimiento.usuario');
    })->name('admin.mantenimiento.usuario');
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
