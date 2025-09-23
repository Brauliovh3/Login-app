<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProxyDniController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\ActaController;
use App\Http\Controllers\CargaPasajeroController;


// Ruta principal - redirige al login si no está autenticado
Route::get('/', function () {
    return \Illuminate\Support\Facades\Auth::check() ? redirect('/dashboard') : redirect('/login');
});

// Proxy seguro para consulta de DNI (usa PERUDEVS_KEY en .env)
Route::get('/api/proxy-dni', [ProxyDniController::class, 'consulta']);

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
    // Dashboard principal - vista unificada con secciones condicionadas por rol
    Route::get('/dashboard', [DashboardController::class, 'dashboardUnificado'])->name('dashboard');

    // Guardar acta (closure para evitar controlador en este flujo)
    Route::post('/actas', function (\Illuminate\Http\Request $request) {
        $data = $request->all();

        $validator = \Illuminate\Support\Facades\Validator::make($data, [
            'numero_acta' => 'required|string',
            'fecha_intervencion' => 'required|date',
            'hora_intervencion' => 'required',
            'tipo_agente' => 'required|string',
            'placa' => 'required|string',
            'razon_social' => 'required|string',
            'ruc_dni' => 'required|string',
            'descripcion_hechos' => 'required|string',
            'calificacion' => 'required|in:Leve,Grave,Muy Grave',
            'monto_multa' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $insert = [
                'numero_acta' => $data['numero_acta'],
                'codigo_ds' => $data['codigo_ds'] ?? '017-2009-MTC',
                'lugar_intervencion' => $data['lugar_intervencion'] ?? null,
                'fecha_intervencion' => $data['fecha_intervencion'],
                'hora_intervencion' => $data['hora_intervencion'],
                'inspector_responsable' => $data['inspector_responsable'] ?? null,
                'tipo_servicio' => $data['tipo_servicio'] ?? null,
                'tipo_agente' => $data['tipo_agente'],
                'placa' => $data['placa'],
                'placa_vehiculo' => $data['placa_vehiculo'] ?? null,
                'razon_social' => $data['razon_social'],
                'ruc_dni' => $data['ruc_dni'],
                'nombre_conductor' => $data['nombre_conductor'] ?? null,
                'licencia' => $data['licencia'] ?? $data['licencia_conductor'] ?? null,
                'clase_licencia' => $data['clase_licencia'] ?? null,
                'origen' => $data['origen'] ?? null,
                'destino' => $data['destino'] ?? null,
                'numero_personas' => isset($data['numero_personas']) ? (int)$data['numero_personas'] : null,
                'ubicacion' => $data['ubicacion'] ?? null,
                'conductor_id' => $data['conductor_id'] ?? null,
                'infraccion_id' => $data['infraccion_id'] ?? null,
                'inspector_id' => $data['inspector_id'] ?? null,
                'vehiculo_id' => $data['vehiculo_id'] ?? null,
                'descripcion_hechos' => $data['descripcion_hechos'],
                'medios_probatorios' => $data['medios_probatorios'] ?? null,
                'calificacion' => $data['calificacion'],
                'medida_administrativa' => $data['medida_administrativa'] ?? null,
                'sancion' => $data['sancion'] ?? null,
                'monto_multa' => isset($data['monto_multa']) ? (float)$data['monto_multa'] : null,
                'observaciones_intervenido' => $data['observaciones_intervenido'] ?? null,
                'observaciones_inspector' => $data['observaciones_inspector'] ?? null,
                'observaciones' => $data['observaciones'] ?? null,
                'estado' => $data['estado'] ?? 'pendiente',
                'user_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            \Illuminate\Support\Facades\DB::table('actas')->insert($insert);

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->back()->with('success', 'Acta guardada correctamente.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Log::error('Error guardando acta (closure): ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al guardar acta: ' . $e->getMessage());
        }
    })->name('actas.store');
    
    // Perfil y configuración del usuario
    Route::get('/perfil', [UserController::class, 'perfil'])->name('user.perfil');
    Route::put('/perfil', [UserController::class, 'updatePerfil'])->name('user.perfil.update');
    Route::get('/configuracion', [UserController::class, 'configuracion'])->name('user.configuracion');
    Route::put('/configuracion', [UserController::class, 'updateConfiguracion'])->name('user.configuracion.update');
    
    
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
            Route::post('/users/{user}/approve', [App\Http\Controllers\Admin\UserApprovalController::class, 'approve'])->name('users.approve');
            Route::post('/users/{user}/reject', [App\Http\Controllers\Admin\UserApprovalController::class, 'reject'])->name('users.reject');
            Route::get('/users/{user}/details', [App\Http\Controllers\Admin\UserApprovalController::class, 'show'])->name('users.details');
            
            // Rutas CRUD activas para conductores (mapeadas al controlador existente)
            Route::get('/conductores', [App\Http\Controllers\ConductorController::class, 'index'])->name('conductores.index');
            Route::post('/conductores', [App\Http\Controllers\ConductorController::class, 'store'])->name('conductores.store');
            Route::get('/conductores/{id}', [App\Http\Controllers\ConductorController::class, 'show'])->name('conductores.show');
            Route::put('/conductores/{id}', [App\Http\Controllers\ConductorController::class, 'update'])->name('conductores.update');
            Route::delete('/conductores/{id}', [App\Http\Controllers\ConductorController::class, 'destroy'])->name('conductores.destroy');
            Route::get('/conductores/search', [App\Http\Controllers\ConductorController::class, 'search'])->name('conductores.search');
            
            // Reinicio de AUTO_INCREMENT para la tabla actas (solo administradores)
            Route::post('/actas/reset-autoincrement', function (\Illuminate\Http\Request $request) {
                $force = filter_var($request->input('force', false), FILTER_VALIDATE_BOOLEAN);
                try {
                    $count = \DB::table('actas')->count();
                    if ($force) {
                        // Truncate (destructivo) y asegurarse de que la secuencia se reinicia
                        \DB::table('actas')->truncate();
                        return response()->json(['message' => 'Tabla actas truncada y AUTO_INCREMENT reseteado.']);
                    }

                    if ($count === 0) {
                        // Sólo ajustar AUTO_INCREMENT si la tabla está vacía
                        \DB::statement('ALTER TABLE actas AUTO_INCREMENT = 1');
                        return response()->json(['message' => 'AUTO_INCREMENT reseteado a 1.']);
                    }

                    return response()->json(['message' => 'La tabla no está vacía. Use force=true para truncar.', 'count' => $count], 400);
                } catch (\Exception $e) {
                    return response()->json(['message' => 'Error al reiniciar auto-increment: ' . $e->getMessage()], 500);
                }
            })->name('actas.reset-autoincrement');
        });
    });
});

// Rutas web para ver/editar/imprimir actas (páginas que consumen la API existente)
Route::middleware(['auth', 'user.approved'])->group(function () {
    Route::get('/actas/{id}', function ($id) {
        return view('fiscalizador.actas.show', compact('id'));
    })->name('actas.show');

    Route::get('/actas/{id}/editar', function ($id) {
        return view('fiscalizador.actas.edit', compact('id'));
    })->name('actas.edit');

    Route::get('/actas/{id}/imprimir', function ($id) {
        return view('fiscalizador.actas.imprimir', compact('id'));
    })->name('actas.imprimir');
});

// Rutas para administradores y fiscalizadores (infracciones)
Route::middleware(['auth', 'user.approved', 'multirole:administrador,fiscalizador'])->group(function () {
            // Ruta simple para ver infracciones (controlador no presente en este branch)
            Route::get('/infracciones', function () {
                $infracciones = \DB::table('infracciones')
                    ->select('id', 'codigo_infraccion as codigo', 'descripcion', 'multa_soles', 'tipo_infraccion')
                    ->get();

                return view('infracciones.index', compact('infracciones'));
            })->name('infracciones.index');

            // CRUD de carga y pasajero
            Route::resource('carga-pasajero', CargaPasajeroController::class);
});



// Rutas específicas por rol con middleware de protección
Route::middleware(['auth', 'user.approved'])->group(function () {
    // Redirecciones de dashboards específicos al dashboard unificado
    Route::get('/admin/dashboard', function() {
        return redirect()->route('dashboard');
    })->middleware('role:administrador')->name('admin.dashboard');
    
    Route::get('/fiscalizador/dashboard', function() {
        return redirect()->route('dashboard');
    })->middleware('role:fiscalizador')->name('fiscalizador.dashboard');
    
    Route::get('/ventanilla/dashboard', function() {
        return redirect()->route('dashboard');
    })->middleware('role:ventanilla')->name('ventanilla.dashboard');
    
    Route::get('/inspector/dashboard', function() {
        return redirect()->route('dashboard');
    })->middleware('role:inspector')->name('inspector.dashboard');
});

// Rutas funcionales específicas para fiscalizador
Route::middleware(['auth', 'user.approved', 'role:fiscalizador'])->group(function () {
    Route::get('/fiscalizador/actas-contra', function() {
        return view('fiscalizador.actas-contra');
    })->name('fiscalizador.actas-contra');
    
    Route::get('/fiscalizador/calendario', function() {
        return view('fiscalizador.calendario');
    })->name('fiscalizador.calendario');
    
    Route::get('/fiscalizador/inspecciones', function() {
        return view('fiscalizador.inspecciones');
    })->name('fiscalizador.inspecciones');
    
    Route::get('/fiscalizador/carga-paga', function() {
        return view('fiscalizador.carga-paga');
    })->name('fiscalizador.carga-paga');
    
    Route::get('/fiscalizador/empresas', function() {
        return view('fiscalizador.empresas');
    })->name('fiscalizador.empresas');
    
    Route::get('/fiscalizador/consultas', function() {
        return view('fiscalizador.consultas');
    })->name('fiscalizador.consultas');
    
    Route::get('/fiscalizador/reportes', function() {
        return view('fiscalizador.reportes');
    })->name('fiscalizador.reportes');
});

// Rutas específicas para administrador
Route::middleware(['auth', 'user.approved', 'role:administrador'])->group(function () {
    
    Route::get('/dashboard/admin/{module}', function($module) {
        // Mapeo de módulos seguros
        $moduleMap = [
            'usr' => 'gestionar-usuarios',      // usuarios -> usr
            'app' => 'aprobar-usuarios',        // aprobación -> app  
            'inf' => 'infracciones',            // infracciones -> inf
            'mnt-c' => 'mantenimiento-conductores', // mantenimiento conductores -> mnt-c
            'mnt-i' => 'mantenimiento-inspectores', // mantenimiento inspectores -> mnt-i
        ];
        
        // Verificar si el módulo existe
        if (!isset($moduleMap[$module])) {
            abort(404);
        }
        
        $realModule = $moduleMap[$module];
        
        return app('App\Http\Controllers\Admin\ModuleController')->handleModule($realModule);
    })->name('admin.module');
    
    // Ruta con token completamente encriptado
    Route::get('/dashboard/panel/{token}', function($token) {
        $module = \App\Helpers\ModuleTokenHelper::decodeToken($token);
        
        if (!$module) {
            abort(404);
        }
        
        return app('App\Http\Controllers\Admin\ModuleController')->handleModule($module);
    })->name('admin.secure-module');
    
    // Mantener las rutas originales para compatibilidad (redirigen a las nuevas)
    Route::get('/admin/gestionar-usuarios', function() {
        return redirect()->route('admin.module', ['module' => 'usr']);
    })->name('admin.gestionar-usuarios');
    
    Route::get('/admin/aprobar-usuarios', function() {
        return redirect()->route('admin.module', ['module' => 'app']);
    })->name('admin.aprobar-usuarios');
    
    Route::get('/admin/infracciones', function() {
        return redirect()->route('admin.module', ['module' => 'inf']);
    })->name('admin.infracciones');
    
    Route::get('/admin/mantenimiento-conductores', function() {
        return redirect()->route('admin.module', ['module' => 'mnt-c']);
    })->name('admin.mantenimiento-conductores');
    
    Route::get('/admin/mantenimiento-inspectores', function() {
        return redirect()->route('admin.module', ['module' => 'mnt-i']);
    })->name('admin.mantenimiento-inspectores');
});

// Rutas específicas para ventanilla
Route::middleware(['auth', 'user.approved', 'role:ventanilla'])->group(function () {
    Route::get('/ventanilla/nueva-atencion', function() {
        return view('ventanilla.nueva-atencion');
    })->name('ventanilla.nueva-atencion');
    
    Route::get('/ventanilla/tramites', function() {
        return view('ventanilla.tramites');
    })->name('ventanilla.tramites');
    
    Route::get('/ventanilla/consultar', function() {
        return view('ventanilla.consultar');
    })->name('ventanilla.consultar');
    
    Route::get('/ventanilla/cola-espera', function() {
        return view('ventanilla.cola-espera');
    })->name('ventanilla.cola-espera');

    Route::get('/ventanilla/inspecciones/create', function() {
        return view('ventanilla.inspecciones.create');
    })->name('inspecciones.create');

    Route::get('/ventanilla/inspecciones', function() {
        return view('ventanilla,inspecciones.index');
    })->name('inspecciones.index');

});

// Rutas específicas para inspector
Route::middleware(['auth', 'user.approved', 'role:inspector'])->group(function () {
    Route::get('/inspector/generar-acta-inspector', function() {
        return view('inspector.generar-acta-inspector');
    })->name('inspector.generar-acta-inspector');
    
    Route::get('/inspector/inspecciones', function() {
        return view('inspector.inspecciones');
    })->name('inspector.inspecciones');
});

// Rutas adicionales de redirección para compatibilidad
Route::middleware(['auth', 'user.approved'])->group(function () {
    // Redirecciones de administrador
    Route::get('/admin/users/approval', function() {
        return redirect('/dashboard?module=aprobar-usuarios');
    })->name('admin.users.approval');
    
    Route::get('/admin/mantenimiento/conductor', function() {
        return redirect('/dashboard?module=mantenimiento-conductores');
    })->name('admin.mantenimiento.conductor');
    
    Route::get('/admin/mantenimiento/fiscal', function() {
        return redirect('/dashboard?module=mantenimiento-inspectores');
    })->name('admin.mantenimiento.fiscal');
});

// Superadmin hidden panel - access only by users with role 'superadmin'
Route::middleware(['auth', 'user.approved', 'role:superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/super', [App\Http\Controllers\Admin\SuperAdminController::class, 'index'])->name('super.index');
    Route::post('/super/cache-clear', [App\Http\Controllers\Admin\SuperAdminController::class, 'cacheClear'])->name('super.cache-clear');
    Route::post('/super/config-cache', [App\Http\Controllers\Admin\SuperAdminController::class, 'configCache'])->name('super.config-cache');
    Route::post('/super/reset-actas', [App\Http\Controllers\Admin\SuperAdminController::class, 'resetActas'])->name('super.reset-actas');
    Route::post('/super/reset-auto-increment', [App\Http\Controllers\Admin\SuperAdminController::class, 'resetAutoIncrement'])->name('super.reset-auto-increment');
    Route::get('/super/app-info', [App\Http\Controllers\Admin\SuperAdminController::class, 'appInfo'])->name('super.app-info');
    Route::post('/super/run-command', [App\Http\Controllers\Admin\SuperAdminController::class, 'runCommand'])->name('super.run-command');
    Route::get('/super/stats', [App\Http\Controllers\Admin\SuperAdminController::class, 'stats'])->name('super.stats');
    Route::get('/super/users', [App\Http\Controllers\Admin\SuperAdminController::class, 'usersList'])->name('super.users');
    Route::post('/super/users/{id}/toggle-status', [App\Http\Controllers\Admin\SuperAdminController::class, 'toggleUserStatus'])->name('super.users.toggle-status');
    Route::post('/super/users/{id}/approve', [App\Http\Controllers\Admin\SuperAdminController::class, 'approveUser'])->name('super.users.approve');
    Route::delete('/super/users/{id}', [App\Http\Controllers\Admin\SuperAdminController::class, 'deleteUser'])->name('super.users.delete');
    Route::post('/super/users/{id}/role', [App\Http\Controllers\Admin\SuperAdminController::class, 'updateUserRole'])->name('super.users.role');
    Route::get('/super/actas', [App\Http\Controllers\Admin\SuperAdminController::class, 'actasManagement'])->name('super.actas');
    Route::delete('/super/actas/{id}', [App\Http\Controllers\Admin\SuperAdminController::class, 'deleteActa'])->name('super.actas.delete');
    Route::post('/super/actas/{id}/status', [App\Http\Controllers\Admin\SuperAdminController::class, 'updateActaStatus'])->name('super.actas.status');
    Route::post('/super/system', [App\Http\Controllers\Admin\SuperAdminController::class, 'systemMaintenance'])->name('super.system');
    Route::post('/super/database', [App\Http\Controllers\Admin\SuperAdminController::class, 'databaseMaintenance'])->name('super.database');
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
    // Buscar acta por criterio (GET) debe registrarse antes que la ruta con parámetro {id}
    Route::get('/actas/buscar', [ActaController::class, 'buscar']);
    Route::get('/actas/{id}', [ActaController::class, 'show']);
    Route::get('/actas/pendientes', [ActaController::class, 'getPendientes']);
    Route::put('/actas/{id}/status', [ActaController::class, 'updateStatus']);
    Route::post('/actas/{id}/finalizar', [ActaController::class, 'finalizarRegistro']);
    Route::post('/actas/{id}/progreso', [ActaController::class, 'guardarProgreso']);
    //Eliminar acta por ID (DELETE)
    Route::delete('/actas/{id}', [ActaController::class, 'destroy']);

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
    Route::get('/estadisticas-actas', [ActaController::class, 'obtenerEstadisticas']);
    Route::get('/buscar-acta-editar/{criterio}', [ActaController::class, 'buscarActaParaEditar']);
    Route::put('/actualizar-acta/{id}', [ActaController::class, 'actualizarActa']);
    
    // Rutas para Inspección Vehicular (comentadas temporalmente porque el controlador no existe)
    /*
    Route::post('/inspeccion/iniciar', [InspeccionVehicularController::class, 'iniciar']);
    Route::post('/inspeccion/registrar', [InspeccionVehicularController::class, 'registrarInspeccion']);
    Route::post('/verificar-licencia', [InspeccionVehicularController::class, 'verificarLicencia']);
    */
});

// Ruta de prueba temporal para depuración (sin autenticación)
Route::post('/api/test-actas', [ActaController::class, 'store'])->name('test.actas');
Route::get('/api/test-consulta/{documento}', [ActaController::class, 'consultarPorDocumento'])->name('test.consulta');
Route::get('/api/csrf-token', function() {
    return response()->json(['token' => csrf_token()]);
});

// RUTA DE DEPURACIÓN: permite DELETE sin middleware (temporal, BORRAR tras pruebas)
Route::delete('/debug/api/actas/{id}', [ActaController::class, 'destroy'])->name('debug.actas.destroy');
