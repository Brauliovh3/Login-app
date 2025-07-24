<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;

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
});

// Rutas específicas por rol con middleware de protección
Route::middleware(['auth', 'role:administrador'])->group(function () {
    // Dashboard de administrador
    Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');

    // Rutas de CRUD DE USUARIOS
    Route::resource('users', \App\Http\Controllers\UserController::class);

    Route::get('/admin/usuarios', function () {
        return view('administrador.usuarios');
    })->name('admin.usuarios');
    Route::get('/admin/reportes', function () {
        return view('administrador.reportes');
    })->name('admin.reportes');
    Route::get('/admin/configuracion', function () {
        return view('administrador.configuracion');
    })->name('admin.configuracion');
});

Route::middleware(['auth', 'role:fiscalizador'])->group(function () {
    // Dashboard de fiscalizador
    Route::get('/fiscalizador/dashboard', [DashboardController::class, 'fiscalizadorDashboard'])->name('fiscalizador.dashboard');
    Route::get('/fiscalizador/inspecciones', function () {
        return view('fiscalizador.inspecciones');
    })->name('fiscalizador.inspecciones');
    Route::get('/fiscalizador/nueva-inspeccion', function () {
        return view('fiscalizador.nueva-inspeccion');
    })->name('fiscalizador.nueva-inspeccion');
    Route::get('/fiscalizador/reportes', function () {
        return view('fiscalizador.reportes');
    })->name('fiscalizador.reportes');
    Route::get('/fiscalizador/calendario', function () {
        return view('fiscalizador.calendario');
    })->name('fiscalizador.calendario');
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
