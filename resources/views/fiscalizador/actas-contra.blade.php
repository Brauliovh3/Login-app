@extends('layouts.dashboard')

@section('title', 'Gestión de Actas de Fiscalización')

@section('content')
@php
use Illuminate\Support\Facades\DB;
// Calcular próximo sufijo numérico para mostrar en el encabezado (reinicio anual)
try {
    $year = date('Y');
    $prefix = 'DRTC-APU-' . $year . '-';

    // Intentar obtener AUTO_INCREMENT y calcular sufijo, con fallbacks
    $countActas = DB::table('actas')->count();
    if ($countActas === 0) {
        $next = 1;
    } else {
        try {
            $tbl = DB::selectOne("SELECT AUTO_INCREMENT as next_id FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'actas'", [env('DB_DATABASE')]);
            $nextId = ($tbl && isset($tbl->next_id) && is_numeric($tbl->next_id)) ? (int)$tbl->next_id : null;
            if ($nextId !== null) {
                $sufijo = max(1, $nextId - 1);
                $next = $sufijo;
            } else {
                $res = DB::selectOne("SELECT MAX(CAST(SUBSTRING_INDEX(numero_acta, '-', -1) AS UNSIGNED)) as max_suf FROM `actas` WHERE numero_acta LIKE ?", [$prefix . '%']);
                $max = ($res && isset($res->max_suf) && is_numeric($res->max_suf)) ? (int)$res->max_suf : null;
                $next = ($max === null) ? 1 : ($max + 1);
            }
        } catch (\Exception $inner) {
            $res = DB::selectOne("SELECT MAX(CAST(SUBSTRING_INDEX(numero_acta, '-', -1) AS UNSIGNED)) as max_suf FROM `actas` WHERE numero_acta LIKE ?", [$prefix . '%']);
            $max = ($res && isset($res->max_suf) && is_numeric($res->max_suf)) ? (int)$res->max_suf : null;
            $next = ($max === null) ? 1 : ($max + 1);
        }
    }
    $proximo_sufijo = str_pad($next, 6, '0', STR_PAD_LEFT);
} catch (\Exception $e) {
    $proximo_sufijo = str_pad(1, 6, '0', STR_PAD_LEFT);
}
@endphp

    {{-- Mostrar paneles por rol en una sola vista (administrador / ventanilla / fiscalizador) --}}
    @php
        // Asegurar que la variable $role esté definida para evitar errors en la vista.
        // Preferir la variable inyectada; si no existe, intentar determinar desde el usuario autenticado.
        $role = $role ?? null;
        if (!$role) {
            if (Auth::check()) {
                $user = Auth::user();
                // Campos comunes donde puede almacenarse el rol
                if (isset($user->role) && $user->role) {
                    $role = $user->role;
                } elseif (isset($user->rol) && $user->rol) {
                    $role = $user->rol;
                } elseif (method_exists($user, 'getRoleNames')) {
                    $names = $user->getRoleNames();
                    $role = count($names) ? $names[0] : 'fiscalizador';
                } elseif (property_exists($user, 'roles') && is_iterable($user->roles)) {
                    // Intentar obtener el primer rol de una colección/array
                    try {
                        $first = is_array($user->roles) ? ($user->roles[0]->name ?? $user->roles[0]->nombre ?? null) : (count($user->roles) ? ($user->roles[0]->name ?? $user->roles[0]->nombre ?? null) : null);
                        $role = $first ?: 'fiscalizador';
                    } catch (\Throwable $e) {
                        $role = 'fiscalizador';
                    }
                } else {
                    $role = 'fiscalizador';
                }
            } else {
                $role = 'guest';
            }
        }
    @endphp

    @if($role === 'administrador')
        <!-- Panel Administrador (minimal, enlaces rápidos) -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-drtc-navy text-white shadow-lg border-0">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="drtc-logo"><i class="fas fa-user-shield"></i></div>
                            </div>
                            <div class="col">
                                <h2 class="mb-1 fw-bold">PANEL DE ADMINISTRACIÓN</h2>
                                <div class="d-flex align-items-center text-warning">
                                    <i class="fas fa-user-shield me-2"></i>
                                    <span class="me-3">Administrador: {{ Auth::user()->name }}</span>
                                    <i class="fas fa-calendar me-2"></i>
                                    <span class="me-3">{{ date('d/m/Y') }}</span>
                                </div>
                            </div>
                            <div class="col-auto text-end">
                                <a href="{{ route('users.index') }}" class="btn btn-outline-light">Gestionar Usuarios</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-user-plus fa-3x text-drtc-orange mb-3"></i>
                        <h5 class="card-title">Gestionar Usuarios</h5>
                        <a href="{{ route('users.index') }}" class="btn btn-primary">Gestionar</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-cog fa-3x text-success mb-3"></i>
                        <h5 class="card-title">Configuración</h5>
                        <a href="#" class="btn btn-success">Configurar</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-hashtag fa-3x text-secondary mb-3"></i>
                        <h5 class="card-title">Numeración Actas</h5>
                        <button id="btn-reset-actas" class="btn btn-outline-secondary btn-sm">Reiniciar números</button>
                    </div>
                </div>
            </div>
        </div>

    @elseif($role === 'ventanilla')
        <!-- Panel Ventanilla -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-drtc-light text-dark shadow-lg border-0">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="drtc-logo"><i class="fas fa-window-maximize"></i></div>
                            </div>
                            <div class="col">
                                <h2 class="mb-1 fw-bold text-drtc-navy">SISTEMA DE VENTANILLA</h2>
                                <div class="d-flex align-items-center text-drtc-navy">
                                    <i class="fas fa-user-tie me-2"></i>
                                    <span class="me-3">Operador: {{ Auth::user()->name }}</span>
                                    <i class="fas fa-calendar me-2"></i>
                                    <span class="me-3">{{ date('d/m/Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-drtc-orange">
                    <div class="card-body text-center">
                        <h6>Atenciones Hoy</h6>
                        <h3>{{ $stats['atenciones_hoy'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body text-center">
                        <h6>En Cola</h6>
                        <h3>{{ $stats['cola_espera'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body text-center">
                        <h6>Completados</h6>
                        <h3>{{ $stats['tramites_completados'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body text-center">
                        <h6>Tiempo Promedio</h6>
                        <h3>{{ $stats['tiempo_promedio'] ?? 15 }}m</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <a href="{{ route('inspecciones.create') }}" class="btn btn-warning w-100">Nueva Inspección</a>
            </div>
            <div class="col-md-6">
                <a href="{{ route('fiscalizador.actas-contra') }}" class="btn btn-success w-100">Ver Actas</a>
            </div>
        </div>

    @else
        <!-- Default: Fiscalizador (vista original) -->
        <!-- Header principal -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0" style="background: linear-gradient(135deg, var(--drtc-orange), var(--drtc-dark-orange)); border-radius: 20px;">
                    <div class="card-body py-4">
                        <div class="row align-items-center">
                            <div class="col">
                                <h1 class="mb-2 fw-bold text-white">
                                    <i class="fas fa-file-contract me-3"></i>
                                    Gestión de Actas de Fiscalización DRTC
                                </h1>
                                <p class="mb-0 fs-5 text-white opacity-75">
                                    <i class="fas fa-user me-2"></i>Inspector: <strong>{{ Auth::user()->name }}</strong>
                                    <span class="ms-3">
                                        <i class="fas fa-calendar-alt me-2"></i>{{ date('d/m/Y') }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de acción principales -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0" style="border-radius: 20px;">
                    <div class="card-header bg-drtc-orange text-white">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-tasks me-2"></i>Acciones de Fiscalización</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                <div class="action-btn" onclick="abrirModal('modal-nueva-acta')">
                                    <i class="fas fa-plus-circle"></i>
                                    <strong>Nueva Acta</strong>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                <div class="action-btn" onclick="abrirModal('modal-editar-acta')">
                                    <i class="fas fa-edit"></i>
                                    <strong>Editar Acta</strong>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                <div class="action-btn" onclick="abrirConsultasDirecto()">
                                    <i class="fas fa-search"></i>
                                    <strong>Consultas</strong>
                                </div>
                                <script>
                                // Función para abrir modal de consultas (sin alert)
                                function abrirConsultasDirecto() {
                                    console.log('Abriendo modal de consultas...');
                                    
                                    var modal = document.getElementById('modal-consultas');
                                    
                                    if (modal) {
                                        // Aplicar estilos forzados
                                        modal.style.cssText = 'display: flex !important; position: fixed !important; top: 0 !important; left: 0 !important; width: 100% !important; height: 100% !important; background-color: rgba(0, 0, 0, 0.5) !important; z-index: 99999 !important; justify-content: center !important; align-items: center !important; visibility: visible !important; opacity: 1 !important;';
                                        
                                        // Forzar propiedades adicionales
                                        modal.style.setProperty('display', 'flex', 'important');
                                        modal.style.setProperty('visibility', 'visible', 'important');
                                        modal.style.setProperty('opacity', 1, 'important');
                                        modal.style.setProperty('pointer-events', 'auto', 'important');
                                        
                                        // Asegurar que elementos padre no estén ocultos
                                        var elemento = modal;
                                        while (elemento && elemento !== document.body) {
                                            if (elemento.style.display === 'none') {
                                                elemento.style.setProperty('display', 'block', 'important');
                                            }
                                            if (elemento.style.visibility === 'hidden') {
                                                elemento.style.setProperty('visibility', 'visible', 'important');
                                            }
                                            elemento = elemento.parentNode;
                                        }
                                        
                                        // Remover clases que puedan estar ocultando
                                        modal.classList.remove('d-none', 'hidden', 'invisible');
                                        modal.classList.add('d-flex');
                                        
                                        document.body.style.overflow = 'hidden';
                                        
                                        // Verificar si el modal original necesita ser restaurado
                                        setTimeout(function() {
                                            if (modal.offsetWidth === 0 || modal.offsetHeight === 0) {
                                                // Mover al body si está en contenedor problemático
                                                if (modal.parentNode !== document.body) {
                                                    document.body.appendChild(modal);
                                                }
                                                
                                                // Inyectar CSS forzado
                                                var estilosForzados = document.createElement('style');
                                                estilosForzados.innerHTML = '#modal-consultas { display: flex !important; position: fixed !important; top: 0 !important; left: 0 !important; width: 100vw !important; height: 100vh !important; background: rgba(0,0,0,0.5) !important; z-index: 999999 !important; visibility: visible !important; opacity: 1 !important; }';
                                                document.head.appendChild(estilosForzados);
                                            }
                                        }, 50);
                                        
                                        // Cargar estadísticas si la función existe
                                        if (typeof cargarEstadisticasReales === 'function') {
                                            try {
                                                cargarEstadisticasReales();
                                            } catch (e) {
                                                console.log('Error cargando estadísticas:', e);
                                            }
                                        }
                                        
                                    } else {
                                        console.error('Modal no encontrado');
                                    }
                                }
                                </script>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                <div class="action-btn" onclick="abrirEliminarDirecto()">
                                    <i class="fas fa-trash-alt"></i>
                                    <strong>Eliminar Acta</strong>
                                </div>
                                <script>
                                // Función para abrir modal de eliminar (sin alert)
                                function abrirEliminarDirecto() {
                                    console.log('Abriendo modal eliminar...');
                                    
                                    var modal = document.getElementById('modal-eliminar-acta');
                                    
                                    if (modal) {
                                        modal.style.cssText = 'display: flex !important; position: fixed !important; top: 0 !important; left: 0 !important; width: 100% !important; height: 100% !important; background-color: rgba(0, 0, 0, 0.5) !important; z-index: 99999 !important; justify-content: center !important; align-items: center !important; visibility: visible !important; opacity: 1 !important;';
                                        
                                        modal.style.setProperty('display', 'flex', 'important');
                                        modal.style.setProperty('visibility', 'visible', 'important');
                                        modal.style.setProperty('opacity', 1, 'important');
                                        
                                        modal.classList.remove('d-none', 'hidden', 'invisible');
                                        modal.classList.add('d-flex');
                                        
                                        document.body.style.overflow = 'hidden';
                                        
                                        console.log('Modal eliminar abierto');
                                    } else {
                                        console.error('Modal eliminar no encontrado');
                                    }
                                }
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros de búsqueda -->
        <div class="card mb-4" style="border-color: #ff8c00;">
            <div class="card-header" style="background-color: #ff8c00; color: white;">
                <h5 class="mb-0">
                    <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <label for="filtro_numero" class="form-label">N° de Acta</label>
                        <input type="text" class="form-control" id="filtro_numero" placeholder="ACT-2025-001">
                    </div>
                    <div class="col-md-3">
                        <label for="filtro_placa" class="form-label">Placa</label>
                        <input type="text" class="form-control" id="filtro_placa" placeholder="ABC-123">
                    </div>
                    <div class="col-md-3">
                        <label for="filtro_fecha" class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="filtro_fecha">
                    </div>
                    <div class="col-md-3">
                        <label for="filtro_estado" class="form-label">Estado</label>
                        <select class="form-select" id="filtro_estado">
                            <option value="">Todos</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="pagada">Pagada</option>
                            <option value="anulada">Anulada</option>
                            <option value="en_cobranza">En Cobranza</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button class="btn btn-primary me-2">
                            <i class="fas fa-search me-2"></i>Buscar
                        </button>
                        <button class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas rápidas (valores reales desde la BD) -->
        @php
            try {
                $totalActas = DB::table('actas')->count();
                $pendientes = DB::table('actas')->where('estado', 'pendiente')->count();
                $pagadas = DB::table('actas')->where('estado', 'pagada')->count();
                $anuladas = DB::table('actas')->where('estado', 'anulada')->count();
                $enCobranza = DB::table('actas')
                    ->where(function($q){
                        $q->where('estado', 'en_cobranza')
                          ->orWhere('estado', 'en cobranza')
                          ->orWhere('estado', 'encobranza');
                    })->count();
            } catch (\Exception $e) {
                $totalActas = 0; $pendientes = 0; $pagadas = 0; $enCobranza = 0; $anuladas = 0;
            }
        @endphp

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 id="dashboard-actas-pendientes">{{ $pendientes }}</h4>
                                <p class="mb-0">Pendientes</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 id="dashboard-actas-pagadas">{{ $pagadas }}</h4>
                                <p class="mb-0">Pagadas</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 id="dashboard-actas-en-cobranza">{{ $enCobranza }}</h4>
                                <p class="mb-0">En Cobranza</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-exclamation-triangle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-secondary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 id="dashboard-actas-anuladas">{{ $anuladas }}</h4>
                                <p class="mb-0">Anuladas</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-times-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de actas -->
        <div class="card">
            <div class="card-header" style="background-color: #fff3e0; border-color: #ff8c00;">
                <h5 class="mb-0" style="color: #ff8c00;">
                    <i class="fas fa-list me-2"></i>Lista de Actas de Contra
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead style="background-color: #ff8c00; color: white;">
                            <tr>
                                <th>N° Acta</th>
                                <th>Fecha/Hora</th>
                                <th>Placa</th>
                                <th>Conductor</th>
                                <th>Infracción</th>
                                <th>Monto</th>
                                <th>Vencimiento</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                try {
                                    $recentActas = DB::table('actas')->orderBy('created_at', 'desc')->limit(10)->get();
                                } catch (\Exception $e) {
                                    $recentActas = collect();
                                    logger()->error('Error obteniendo actas para la vista: ' . $e->getMessage());
                                }
                            @endphp

                            @if($recentActas->isEmpty())
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="fas fa-info-circle me-2"></i>
                                        No hay actas para mostrar en este momento
                                    </td>
                                </tr>
                            @else
                                @foreach($recentActas as $acta)
                                    @php
                                        $fecha = $acta->fecha_infraccion ?? $acta->fecha_intervencion ?? ($acta->created_at ?? null);
                                        try {
                                            $fechaForm = $fecha ? \Carbon\Carbon::parse($fecha)->format('d/m/Y') : 'N/A';
                                        } catch (\Exception $ex) {
                                            $fechaForm = 'N/A';
                                        }
                                        $hora = $acta->hora_infraccion ?? $acta->hora_intervencion ?? '';
                                        $empresa = $acta->razon_social ?? $acta->nombre_conductor ?? 'N/A';
                                        $documento = $acta->ruc_dni ?? 'N/A';
                                        $placa = $acta->placa_vehiculo ?? $acta->placa ?? 'N/A';
                                        $ubicacion = $acta->ubicacion ?? $acta->lugar_intervencion ?? 'N/A';
                                        $monto = number_format((float)($acta->monto_multa ?? 0), 2, '.', ',');
                                    @endphp
                                    <tr>
                                        <td class="fw-bold">{{ $acta->numero_acta ?? 'N/A' }}</td>
                                        <td>{{ $fechaForm }} {{ $hora ? ' ' . $hora : '' }}</td>
                                        <td><span class="badge bg-dark">{{ $placa }}</span></td>
                                        <td>{{ $empresa }}</td>
                                        <td>{{ isset($acta->codigo_infraccion) && $acta->codigo_infraccion ? $acta->codigo_infraccion . ' - ' : '' }}{{ \Illuminate\Support\Str::limit($acta->descripcion ?? ($acta->descripcion_hechos ?? ''), 40) }}</td>
                                        <td>{{ $ubicacion != 'N/A' ? (Str::limit($ubicacion, 30) . (strlen($ubicacion) > 30 ? '...' : '')) : 'N/A' }}</td>
                                        <td><strong>S/ {{ $monto }}</strong></td>
                                        <td>
                                            @if(($acta->estado ?? '') === 'pendiente')
                                                <span class="badge bg-warning">Pendiente</span>
                                            @elseif(($acta->estado ?? '') === 'pagada')
                                                <span class="badge bg-success">Pagada</span>
                                            @elseif(($acta->estado ?? '') === 'anulada')
                                                <span class="badge bg-secondary">Anulada</span>
                                            @elseif(($acta->estado ?? '') === 'en_cobranza' || ($acta->estado ?? '') === 'en cobranza' || ($acta->estado ?? '') === 'en_cobranza')
                                                <span class="badge bg-danger">En Cobranza</span>
                                            @else
                                                <span class="badge bg-info">{{ $acta->estado ?? 'Sin estado' }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="/actas/{{ $acta->id }}" class="btn btn-sm btn-outline-primary" title="Ver detalle" onclick="event.stopPropagation(); window.location.href=this.href; return false;"><i class="fas fa-eye"></i></a>
                                            <a href="/actas/{{ $acta->id }}/editar" class="btn btn-sm btn-outline-success" title="Editar" onclick="event.stopPropagation(); window.location.href=this.href; return false;"><i class="fas fa-edit"></i></a>
                                            <a href="/actas/{{ $acta->id }}/imprimir" class="btn btn-sm btn-outline-info" title="Imprimir" onclick="event.stopPropagation(); window.location.href=this.href; return false;"><i class="fas fa-print"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @endif
</div>

<!-- (Removed duplicate Bootstrap modal 'nuevaActaModal' to avoid duplicate/unnamed inputs.) -->
<script>
// Función para mostrar notificaciones flotantes modernas
function mostrarNotificacion(mensaje, tipo = 'info', duracion = 4000) {
    // Crear el elemento de notificación
    const notificacion = document.createElement('div');
    notificacion.className = `notificacion-flotante ${tipo}`;
    
    // Determinar el ícono según el tipo
    let icono = '';
    let colorFondo = '';
    let colorTexto = '#ffffff';
    
    switch(tipo) {
        case 'success':
            icono = '✓';
            colorFondo = '#10b981';
            break;
        case 'error':
            icono = '✕';
            colorFondo = '#ef4444';
            break;
        case 'warning':
            icono = '⚠';
            colorFondo = '#f59e0b';
            break;
        case 'info':
        default:
            icono = 'ℹ';
            colorFondo = '#3b82f6';
            break;
    }
    
    notificacion.innerHTML = `
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 18px; font-weight: bold;">${icono}</span>
            <span style="flex: 1;">${mensaje}</span>
            <button onclick="this.parentElement.parentElement.remove()" 
                    style="background: none; border: none; color: white; font-size: 20px; cursor: pointer; padding: 0; margin-left: 10px;">×</button>
        </div>
    `;
    
    // Estilos de la notificación
    notificacion.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${colorFondo};
        color: ${colorTexto};
        padding: 16px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10000;
        max-width: 400px;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        font-size: 14px;
        line-height: 1.4;
        transform: translateX(100%);
        transition: transform 0.3s ease-in-out;
        border-left: 4px solid rgba(255,255,255,0.3);
    `;
    
    // Agregar al DOM
    document.body.appendChild(notificacion);
    
    // Animar entrada
    setTimeout(() => {
        notificacion.style.transform = 'translateX(0)';
    }, 10);
    
    // Auto-remover después de la duración especificada
    setTimeout(() => {
        notificacion.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notificacion.parentElement) {
                notificacion.remove();
            }
        }, 300);
    }, duracion);
    
    return notificacion;
}

function submitActa(event) {
    event.preventDefault(); // Prevenir el envío normal del formulario
    
    console.log('🚀 Iniciando envío de acta...');
    
    // Obtener el formulario
    const form = document.getElementById('form-nueva-acta');
    if (!form) {
        console.error('❌ Formulario no encontrado');
        mostrarNotificacion('Error: Formulario no encontrado', 'error');
        return false;
    }
    
    // Recopilar datos del formulario
    const formData = new FormData(form);
    
    // Validar campos obligatorios (origen/destino son opcionales ahora)
    const camposObligatorios = {
        'placa_1': 'Placa del vehículo',
        'apellidos_nombres': 'Apellidos y Nombres (completo)',
        'licencia_conductor_1': 'Licencia del conductor',
        'ruc_dni': 'RUC/DNI',
        'lugar_intervencion': 'Lugar de intervención',
        'tipo_servicio': 'Tipo de servicio',
        'descripcion_hechos': 'Descripción de los hechos'
    };
    
    const camposFaltantes = [];
    for (const [campo, nombre] of Object.entries(camposObligatorios)) {
        const valor = formData.get(campo);
        if (!valor || valor.trim() === '') {
            camposFaltantes.push(nombre);
        }
    }
    
    if (camposFaltantes.length > 0) {
        console.log('❌ Campos faltantes:', camposFaltantes);
        mostrarNotificacion('Por favor complete todos los campos obligatorios:\n• ' + camposFaltantes.join('\n• '), 'warning');
        return false;
    }
    
    // Obtener lugar completo (combinar select + dirección específica)
    const lugarSelect = formData.get('lugar_intervencion');
    const direccionEspecifica = formData.get('direccion_especifica');
    let lugarCompleto = lugarSelect;
    if (direccionEspecifica && direccionEspecifica.trim() !== '') {
        lugarCompleto += ' - ' + direccionEspecifica.trim();
    }
    
    // Preparar datos para envío (mapear a los campos que espera el backend)
    // Aceptar nombres alternativos de campos (compatibilidad con distintos formularios)
    const placaValor = formData.get('placa_1') || formData.get('placa_vehiculo') || formData.get('placa') || '';
    // Combinar campo 'apellidos_nombres' con 'nombre_conductor_1' si se proporciona
    // Ahora solo usamos 'apellidos_nombres' como nombre completo del conductor. 'nombre_conductor_1' se mantiene hidden solo para compatibilidad.
    const apellidosNombres = formData.get('apellidos_nombres') || '';
    const nombreConductorValor = apellidosNombres.trim();
    const licenciaValor = formData.get('licencia_conductor_1') || formData.get('licencia_conductor') || formData.get('licencia') || '';
    const documentoValor = formData.get('ruc_dni') || formData.get('dni_conductor') || formData.get('dni') || '';
    // Prenombres (campo hidden usado como compatibilidad). Puede ser llenado por la búsqueda DNI.
    const nombreConductorRaw = formData.get('nombre_conductor_1') || '';

    // Incluir explícitamente campos que backend espera. Usar cadena vacía cuando el campo existe
    // para que no sea omitido en el envío y así llene columnas que ahora aparecen NULL.
    const datosParaEnvio = {
        // variantes de placa (clientes/BD diferentes)
        placa_1: placaValor,
        placa: placaValor,
        placa_vehiculo: placaValor,

    // conductor / licencia
    nombre_conductor_1: nombreConductorRaw,
    // Normalized full name for backend
    nombre_conductor: nombreConductorValor,
        licencia_conductor_1: licenciaValor,

        // datos fiscales
        razon_social: formData.get('razon_social') || '',
        ruc_dni: documentoValor,

        // ubicación
        lugar_intervencion: lugarCompleto,
        origen_viaje: formData.get('origen_viaje') || '',
        destino_viaje: formData.get('destino_viaje') || '',

        // servicio / infracción
        tipo_servicio: formData.get('tipo_servicio') || '',
        descripcion_hechos: formData.get('descripcion_hechos') || formData.get('descripcion') || '',

        // inspector / responsables
        inspector_responsable: formData.get('inspector_responsable') || formData.get('inspector') || formData.get('inspector_principal') || document.querySelector('input[name="inspector_principal"]') && document.querySelector('input[name="inspector_principal"]').value || '{{ Auth::user()->name }}',
        inspector: formData.get('inspector') || formData.get('inspector_principal') || document.querySelector('input[name="inspector_principal"]') && document.querySelector('input[name="inspector_principal"]').value || '{{ Auth::user()->name }}',
        inspector_principal: formData.get('inspector_principal') || '{{ Auth::user()->name }}',

        tipo_agente: formData.get('tipo_agente') || (document.getElementById('tipo_agente_hidden') && document.getElementById('tipo_agente_hidden').value) || '',
        clase_licencia: formData.get('clase_categoria') || (document.getElementById('clase_licencia_hidden') && document.getElementById('clase_licencia_hidden').value) || '',

        // Campos adicionales opcionales
        fecha_intervencion: formData.get('fecha_intervencion') || '',
        hora_intervencion: formData.get('hora_intervencion') || '',
        clase_categoria: formData.get('clase_categoria') || '',
        tipo_infraccion: formData.get('tipo_infraccion') || '',
        codigo_infraccion: formData.get('codigo_infraccion') || '',
        gravedad: formData.get('gravedad') || ''
    };
    
        // Actualizar hidden fallbacks en el DOM para compatibilidad servidor
        try {
            const placaHidden = document.getElementById('placa_hidden');
            const placaVehHidden = document.getElementById('placa_vehiculo_hidden');
            const inspectorRespHidden = document.getElementById('inspector_responsable_hidden');
            if (placaHidden) placaHidden.value = placaValor || '';
            if (placaVehHidden) placaVehHidden.value = placaValor || '';
            if (inspectorRespHidden) inspectorRespHidden.value = datosParaEnvio.inspector || datosParaEnvio.inspector_principal || '';
        } catch (e) {
            console.warn('No se pudieron actualizar hidden fallbacks:', e);
        }

    // No enviar numero_acta desde el cliente: el servidor lo generará de forma única
    
    console.log('📤 Datos a enviar:', datosParaEnvio);
    
    // Obtener botón de envío para mostrar estado de carga (usar id del botón)
    const submitBtn = document.getElementById('btn-guardar-acta');
    const originalText = submitBtn ? submitBtn.innerHTML : '';

    if (submitBtn) {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando Acta...';
        submitBtn.disabled = true;
    }

    // Obtener token CSRF
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.error('❌ Token CSRF no encontrado');
        mostrarNotificacion('Error: Token CSRF no encontrado', 'error');
        if (submitBtn) {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
        return false;
    }

    // Siempre usar FormData para enviar todos los campos tal como lo haría un form normal
    const inputFiles = document.getElementById('evidencias');
    const fd = new FormData(form);

    // Añadir/forzar campos normalizados para asegurar que no queden NULL en BD
    for (const key in datosParaEnvio) {
        if (datosParaEnvio.hasOwnProperty(key)) {
            const val = datosParaEnvio[key] === null || datosParaEnvio[key] === undefined ? '' : datosParaEnvio[key];
            fd.set(key, val);
        }
    }

    // Asegurarse explícitamente de copiar los campos principales que el backend puede leer
    const explicitFields = ['placa_1','licencia_conductor_1','tipo_servicio','inspector','inspector_responsable','razon_social','ruc_dni','placa','placa_vehiculo'];
    explicitFields.forEach(k => {
        try {
            const el = form.querySelector('[name="' + k + '"]');
            if (el) {
                const v = (el.value !== undefined && el.value !== null) ? el.value : '';
                fd.set(k, v);
            }
        } catch (e) {
            // ignore
        }
    });

    // Debug: mostrar todas las entradas de FormData en consola antes de enviar
    try {
        console.log('🔎 FormData entries to be sent:');
        for (const pair of fd.entries()) {
            console.log(pair[0] + ':', pair[1]);
        }
    } catch (e) {
        console.warn('No se pudo listar FormData entries:', e);
    }

    // Si hay archivos seleccionados, FormData ya los contiene si el input file tiene name; añadir si es necesario
    if (inputFiles && inputFiles.files && inputFiles.files.length > 0) {
        for (let i = 0; i < inputFiles.files.length; i++) {
            fd.append('evidencias[]', inputFiles.files[i]);
        }
    }

    let fetchOptions = {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
            'Accept': 'application/json'
        },
        body: fd
    };

    // Enviar datos al servidor
    fetch('/api/actas', fetchOptions)
    .then(async response => {
        console.log('📡 Respuesta del servidor:', response.status);
        const text = await response.text();
        let json = null;
        try { json = JSON.parse(text); } catch(e) { json = null; }
        if (!response.ok) {
            const serverMsg = (json && json.message) ? json.message : text;
            throw new Error(`HTTP ${response.status}: ${serverMsg}`);
        }
        return json;
    })
    .then(result => {
        console.log('✅ Resultado:', result);
        
        // Restaurar botón
        if (submitBtn) {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
        
    if (result.success) {
            // Mostrar notificación de éxito
            // Preferir mostrar el numero_acta oficial; el ID de la BD es interno
            let mensajeExito = `🎉 ¡Acta ${result.numero_acta} registrada exitosamente!\n` +
                               `📅 Fecha: ${result.hora_registro}\n` +
                               `📍 Lugar: ${lugarCompleto}`;
            if (result.sufijo_padded) {
                mensajeExito += `\n🔢 Sufijo: ${result.sufijo_padded}`;
            }
            mostrarNotificacion(mensajeExito, 'success', 8000);
            
            // Mostrar notificación flotante (ya ejecutada). Restaurar comportamiento: limpiar formulario,
            // cerrar modal y recargar la tabla de actas, pero NO redirigir a otra página.
            console.log('✅ Acta creada exitosamente con ID:', result.acta_id);

            // NO limpiar el formulario automáticamente: mantener datos para impresión/descarga
            // Guardar identificadores retornados por el servidor para prevenir duplicados
            try {
                window.__lastSavedActa = {
                    id: result.acta_id || null,
                    numero_acta: result.numero_acta || (document.getElementById('numero_acta_hidden') && document.getElementById('numero_acta_hidden').value) || null
                };
            } catch (e) {
                console.warn('No se pudo almacenar metadatos del acta:', e);
            }

            // Actualizar el sufijo mostrado para el siguiente acta (incremento inmediato)
            try {
                const span = document.getElementById('proximo_sufijo_span');
                const hidden = document.getElementById('numero_acta_hidden');
                if (span && hidden) {
                    const current = span.textContent.trim();
                    const n = parseInt(current, 10);
                    if (!isNaN(n)) {
                        const next = n + 1;
                        const nextStr = String(next).padStart(6, '0');
                        span.textContent = nextStr;
                        const year = new Date().getFullYear();
                        hidden.value = `DRTC-APU-${year}-${nextStr}`;
                    }
                }
            } catch (e) {
                console.warn('No se pudo actualizar el sufijo en el DOM:', e);
            }
            // Mantener modal abierto para impresión/descarga. Habilitar botones de imprimir/descargar.
            try {
                const btnPdf = document.getElementById('btn-descargar-pdf');
                const btnPrint = document.getElementById('btn-imprimir-acta');
                const btnClear = document.getElementById('btn-limpiar-acta');
                if (btnPdf) btnPdf.style.display = 'inline-block';
                if (btnPrint) btnPrint.style.display = 'inline-block';
                if (btnClear) btnClear.style.display = 'none';

                // Deshabilitar el botón de guardar para evitar reenvíos
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Acta Guardada';
                }
            } catch (e) {
                console.warn('No se pudo ajustar botones post-guardado:', e);
            }
            
        } else {
            console.error('❌ Error del servidor:', result);
            mostrarNotificacion(
                '❌ Error al registrar el acta:\n' + (result.message || result.error || 'Error desconocido'),
                'error'
            );
        }
    })
    .catch(error => {
        console.error('❌ Error en la petición:', error);
        
        // Restaurar botón
        if (submitBtn) {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
        
        mostrarNotificacion(
            '❌ Error al enviar el acta:\n' + error.message + '\n\nVerifique el servidor o consulte logs.',
            'error',
            8000
        );
    });
    
    return false; // Prevenir envío del formulario
}

// Función para agregar botón de finalizar al modal
function agregarBotonFinalizar() {
    const modalFooter = document.querySelector('#modal-nueva-acta .d-flex.justify-content-between');
    if (modalFooter && !document.getElementById('btn-finalizar-acta')) {
        // Hidden fallbacks para nombres de columnas alternativos: crear inputs ocultos si no existen
        const form = document.getElementById('form-nueva-acta');
        if (form) {
            if (!document.getElementById('placa_hidden')) {
                const placaHidden = document.createElement('input');
                placaHidden.type = 'hidden';
                placaHidden.id = 'placa_hidden';
                placaHidden.name = 'placa';
                placaHidden.value = '';
                form.appendChild(placaHidden);
            }
            if (!document.getElementById('placa_vehiculo_hidden')) {
                const placaVehHidden = document.createElement('input');
                placaVehHidden.type = 'hidden';
                placaVehHidden.id = 'placa_vehiculo_hidden';
                placaVehHidden.name = 'placa_vehiculo';
                placaVehHidden.value = '';
                form.appendChild(placaVehHidden);
            }
            if (!document.getElementById('inspector_responsable_hidden')) {
                const inspectorHidden = document.createElement('input');
                inspectorHidden.type = 'hidden';
                inspectorHidden.id = 'inspector_responsable_hidden';
                inspectorHidden.name = 'inspector_responsable';
                inspectorHidden.value = '{{ Auth::user()->name }}';
                form.appendChild(inspectorHidden);
            }
        }
        const btnFinalizar = document.createElement('button');
        btnFinalizar.id = 'btn-finalizar-acta';
        btnFinalizar.type = 'button';
        btnFinalizar.className = 'btn btn-success';
        btnFinalizar.innerHTML = '<i class="fas fa-check-double me-2"></i>Finalizar Registro';
        btnFinalizar.onclick = finalizarRegistroActa;
        
        modalFooter.appendChild(btnFinalizar);
    }
}

// Actualizar monto al seleccionar infracción (adjuntar solo si existen los elementos)
const _infraccionEl = document.getElementById('infraccion_id');
const _montoEl = document.getElementById('monto_multa');
if (_infraccionEl && _montoEl) {
    _infraccionEl.addEventListener('change', function() {
        const select = this;
        const montoInput = _montoEl;
        if (select.value === '1') montoInput.value = '462.00';
        else if (select.value === '2') montoInput.value = '231.00';
        else if (select.value === '3') montoInput.value = '4620.00';
        else if (select.value === '4') montoInput.value = '462.00';
        else montoInput.value = '';
    });
}
</script>
@include('partials.export-actas-scripts')

<!-- MODALES FLOTANTES -->

<!-- MODAL: NUEVA ACTA -->
<div class="floating-modal" id="modal-nueva-acta">
    <div class="modal-content-wrapper">
        <div class="modal-header-custom">
            <h4 class="mb-0 fw-bold">
                <i class="fas fa-plus-circle me-2"></i>
                REGISTRO DE NUEVA ACTA DE FISCALIZACIÓN DRTC
            </h4>
            <button class="close-modal" onclick="cerrarModal('modal-nueva-acta')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body-custom">
                <form id="form-nueva-acta" action="/api/actas" method="POST">
                @csrf
                
                <!-- Campos automáticos ocultos -->
                <input type="hidden" id="fecha_inspeccion_hidden" name="fecha_inspeccion">
                <input type="hidden" id="hora_inicio_hidden" name="hora_inicio">
                <input type="hidden" name="inspector_principal" value="{{ Auth::user()->name }}">
                <!-- Añadir campos que el backend espera y a veces no llegan desde el formulario -->
                <input type="hidden" id="inspector_hidden" name="inspector" value="{{ Auth::user()->name }}">
                <input type="hidden" id="tipo_agente_hidden" name="tipo_agente" value="transportista">
                <input type="hidden" id="clase_licencia_hidden" name="clase_licencia" value="">

                <!-- CABEZAL OFICIAL DEL ACTA -->
                <div class="card mb-4 border-3 border-dark" style="background: #ffffff;">
                    <div class="card-body py-2">
                        <!-- Fila superior con cuadros según la imagen oficial -->
                        <div class="row g-0 mb-2">
                            <!-- Logo/Escudo del Perú (izquierdo) -->
                            <div class="col-1 d-flex align-items-center justify-content-center">
                                <div class="text-center p-1" style="border: 2px solid #000; background: #ffffff; border-radius: 10px; width: 60px; height: 60px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                    <img src="{{ asset('images/escudo_peru.png') }}" alt="Escudo del Perú" style="max-width: 45px; max-height: 45px; object-fit: contain;">
                                </div>
                            </div>
                            
                            <!-- Cuadros centrales -->
                            <div class="col-10">
                                <div class="row g-0">
                                    <div class="col-2">
                                        <div class="p-2 text-center" style="background-color: #dc143c; color: white; border: 2px solid #000; font-weight: bold; font-size: 16px; min-height: 60px; display: flex; align-items: center; justify-content: center;">
                                            PERÚ
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="p-2 text-center" style="background-color: #ffffff; color: #000; border: 2px solid #000; border-left: none; font-weight: bold; font-size: 14px; min-height: 60px; display: flex; align-items: center; justify-content: center; line-height: 1.2;">
                                            GOBIERNO REGIONAL<br>DE APURÍMAC
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-2 text-center" style="background-color: #dc143c; color: white; border: 2px solid #000; border-left: none; font-weight: bold; font-size: 13px; min-height: 60px; display: flex; align-items: center; justify-content: center; line-height: 1.2;">
                                            DIRECCIÓN REGIONAL DE<br>TRANSPORTES Y COMUNICACIONES
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="p-2 text-center" style="background-color: #ffffff; color: #000; border: 2px solid #000; border-left: none; font-weight: bold; font-size: 12px; min-height: 60px; display: flex; align-items: center; justify-content: center; line-height: 1.1;">
                                            DIRECCIÓN DE CIRCULACIÓN<br>TERRESTRE OF. FISCALIZACIÓN
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Logo Regional (derecho) -->
                            <div class="col-1 d-flex align-items-center justify-content-center">
                                <div class="text-center p-1" style="background: #ffffff; border: 2px solid #000; border-radius: 50%; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                    <img src="{{ asset('images/logo-gobierno.png') }}" alt="Logo Gobierno Regional" style="max-width: 50px; max-height: 50px; object-fit: contain;">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sección del número de acta centrada -->
                        <div class="row mt-3">
                            <div class="col-12 text-center">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <h3 class="mb-0 fw-bold text-dark me-3">ACTA DE CONTROL</h3>
                                    <span class="me-2 fw-bold text-dark" style="font-size: 18px;">Nº</span>
                                    <span id="proximo_sufijo_span" class="me-2 d-inline-block" style="width: 120px; text-align: center; font-weight: bold; font-size: 18px;">{{ $proximo_sufijo }}</span>
                                    <span class="fw-bold text-dark" style="font-size: 18px;">- {{ date('Y') }}</span>
                                    {{-- Hidden con el numero_acta completo para envío al backend --}}
                                    <input type="hidden" id="numero_acta_hidden" name="numero_acta" value="{{ 'DRTC-APU-' . date('Y') . '-' . $proximo_sufijo }}">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Información del decreto -->
                        <div class="row mt-2">
                            <div class="col-12 text-center">
                                <div class="d-inline-block p-2" style="border: 2px solid #000; background-color: #ffffff;">
                                    <div class="fw-bold text-dark mb-1">D.S. Nº 017-2009-MTC</div>
                                    <div style="font-size: 12px; color: #000;">Código de infracciones y/o incumplimiento</div>
                                    <div style="font-size: 12px; color: #000; font-weight: bold;">Tipo infractor</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Información adicional del documento (fecha/hora superior removida por requerimiento) -->
                    </div>
                </div>

                <!-- SECCIÓN 1: INFORMACIÓN DEL OPERADOR/CONDUCTOR -->
                <div class="card mb-4 border-warning">
                    <div class="card-header" style="background: linear-gradient(135deg, var(--drtc-orange), var(--drtc-dark-orange)); color: white;">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-user-tie me-2"></i>I. DATOS DEL OPERADOR/CONDUCTOR</h6>
                    </div>
                    <div class="card-body bg-light">
                        <!-- Tipo de Agente Infractor -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-warning">Tipo de Agente Infractor:</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check p-3 border border-warning rounded bg-white">
                                        <input class="form-check-input" type="radio" name="tipo_agente" id="transportista" value="transportista">
                                        <label class="form-check-label fw-bold w-100" for="transportista">
                                            <i class="fas fa-truck me-2 text-warning"></i>TRANSPORTISTA
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check p-3 border border-warning rounded bg-white">
                                        <input class="form-check-input" type="radio" name="tipo_agente" id="operador_ruta" value="operador_ruta">
                                        <label class="form-check-label fw-bold w-100" for="operador_ruta">
                                            <i class="fas fa-route me-2 text-warning"></i>OPERADOR DE RUTA
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check p-3 border border-warning rounded bg-white">
                                        <input class="form-check-input" type="radio" name="tipo_agente" id="conductor" value="conductor">
                                        <label class="form-check-label fw-bold w-100" for="conductor">
                                            <i class="fas fa-id-card me-2 text-warning"></i>CONDUCTOR
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Datos del operador/conductor -->
                        <div class="row">
                            <div class="col-md-2 mb-3">
                                <label class="form-label fw-bold text-warning">RUC/DNI:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control border-warning" name="ruc_dni" id="ruc_dni" placeholder="20123456789 o 123" maxlength="11" required>
                                    <button type="button" class="btn btn-outline-secondary" id="btn-probar-ruc-dni">
                                        <i class="fas fa-search"></i> Probar
                                    </button>
                                </div>
                                <div class="form-text">
                                    <small class="text-muted">DNI: 8 dígitos | RUC: 11 dígitos</small>
                                </div>
                            </div>
                            <div class="col-md-7 mb-3">
                                <label class="form-label fw-bold text-warning">Razón Social: <small class="text-muted">(Opcional)</small></label>
                                <div class="input-group">
                                    <input type="text" class="form-control border-warning" name="razon_social" id="razon_social" placeholder="Razón social (empresa) - opcional">
                                    <a href="/ver-consultas.html" target="_blank" class="btn btn-outline-secondary">
                                        <i class="fas fa-database me-1"></i>Ver Consultas
                                    </a>
                                </div>
                                <div id="loading-data" class="form-text text-info" style="display: none;">
                                    <i class="fas fa-spinner fa-spin"></i> Consultando datos...
                                </div>
                                <div class="form-text mt-1">
                                    <small class="text-muted">Datos de empresa (SUNAT) o nombre del operador si aplica</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold text-warning">Placa del Vehículo:</label>
                                <input type="text" class="form-control border-warning" name="placa_1" placeholder="ABC-123" style="text-transform: uppercase;" required>
                            </div>
                        </div>
                        
                        <!-- Segunda línea: Nombres, Licencia y Clase/Categoría -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-warning">Apellidos y Nombres (completo):</label>
                                <input type="text" class="form-control border-warning" name="apellidos_nombres" id="apellidos_nombres" placeholder="Apellido paterno Apellido materno Nombres" required>
                                <div class="form-text"><small class="text-muted">Opcional: si completa aquí, se usará como nombre completo del conductor.</small></div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold text-warning">N° Licencia de Conducir:</label>
                                <input type="text" class="form-control border-warning" name="licencia_conductor_1" placeholder="N° Licencia" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold text-warning">Clase y Categoría:</label>
                                <select class="form-select border-warning" name="clase_categoria" required>
                                    <option value="">Seleccione...</option>
                                    <option value="A-I">A-I (Motocicletas hasta 125cc)</option>
                                    <option value="A-IIa">A-IIa (Motocicletas de 126cc a 200cc)</option>
                                    <option value="A-IIb">A-IIb (Motocicletas mayor a 200cc)</option>
                                    <option value="A-IIIa">A-IIIa (Vehículos menores)</option>
                                    <option value="A-IIIb">A-IIIb (Automóviles, camionetas)</option>
                                    <option value="A-IIIc">A-IIIc (Buses, camiones)</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Campo hidden para compatibilidad con servidor -->
                        <div style="display: none;">
                            <input type="hidden" class="form-control border-warning" name="nombre_conductor_1" placeholder="Nombres (prenombres)">
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 2: DATOS DE LA INTERVENCIÓN -->
                <div class="card mb-4 border-info">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-map-marker-alt me-2"></i>II. DATOS DE LA INTERVENCIÓN</h6>
                    </div>
                    <div class="card-body bg-light">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-bold text-info">Lugar de Intervención - Región Apurímac:</label>
                                <select class="form-select border-info" name="lugar_intervencion" id="lugar-select" required onchange="actualizarLugarCompleto()">
                                    <option value="">Seleccione el lugar de intervención...</option>
                                    
                                    <!-- PROVINCIA ABANCAY -->
                                    <optgroup label="PROVINCIA ABANCAY">
                                        <option value="Abancay, Provincia Abancay">Distrito Abancay</option>
                                        <option value="Chikay, Provincia Abancay">Distrito Chikay</option>
                                        <option value="Curahuasi, Provincia Abancay">Distrito Curahuasi</option>
                                        <option value="Huanipaca, Provincia Abancay">Distrito Huanipaca</option>
                                        <option value="Lambrama, Provincia Abancay">Distrito Lambrama</option>
                                        <option value="Pichirhua, Provincia Abancay">Distrito Pichirhua</option>
                                        <option value="San Pedro de Cachora, Provincia Abancay">Distrito San Pedro de Cachora</option>
                                        <option value="Tamburco, Provincia Abancay">Distrito Tamburco</option>
                                    </optgroup>
                                    
                                    <!-- PROVINCIA ANDAHUAYLAS -->
                                    <optgroup label="PROVINCIA ANDAHUAYLAS">
                                        <option value="Andahuaylas, Provincia Andahuaylas">Distrito Andahuaylas</option>
                                        <option value="Andarapa, Provincia Andahuaylas">Distrito Andarapa</option>
                                        <option value="Chiara, Provincia Andahuaylas">Distrito Chiara</option>
                                        <option value="Huancarama, Provincia Andahuaylas">Distrito Huancarama</option>
                                        <option value="Huancaray, Provincia Andahuaylas">Distrito Huancaray</option>
                                        <option value="Huayana, Provincia Andahuaylas">Distrito Huayana</option>
                                        <option value="Kishuara, Provincia Andahuaylas">Distrito Kishuara</option>
                                        <option value="Pacobamba, Provincia Andahuaylas">Distrito Pacobamba</option>
                                        <option value="Pacucha, Provincia Andahuaylas">Distrito Pacucha</option>
                                        <option value="Pampachiri, Provincia Andahuaylas">Distrito Pampachiri</option>
                                        <option value="Pomacocha, Provincia Andahuaylas">Distrito Pomacocha</option>
                                        <option value="San Antonio de Cachi, Provincia Andahuaylas">Distrito San Antonio de Cachi</option>
                                        <option value="San Jerónimo, Provincia Andahuaylas">Distrito San Jerónimo</option>
                                        <option value="San Miguel de Chaccrampa, Provincia Andahuaylas">Distrito San Miguel de Chaccrampa</option>
                                        <option value="Santa María de Chicmo, Provincia Andahuaylas">Distrito Santa María de Chicmo</option>
                                        <option value="Talavera, Provincia Andahuaylas">Distrito Talavera</option>
                                        <option value="Tumay Huaraca, Provincia Andahuaylas">Distrito Tumay Huaraca</option>
                                        <option value="Turpo, Provincia Andahuaylas">Distrito Turpo</option>
                                        <option value="Kaquiabamba, Provincia Andahuaylas">Distrito Kaquiabamba</option>
                                    </optgroup>
                                    
                                    <!-- PROVINCIA ANTABAMBA -->
                                    <optgroup label="PROVINCIA ANTABAMBA">
                                        <option value="Antabamba, Provincia Antabamba">Distrito Antabamba</option>
                                        <option value="El Oro, Provincia Antabamba">Distrito El Oro</option>
                                        <option value="Huaquirca, Provincia Antabamba">Distrito Huaquirca</option>
                                        <option value="Juan Espinoza Medrano, Provincia Antabamba">Distrito Juan Espinoza Medrano</option>
                                        <option value="Oropesa, Provincia Antabamba">Distrito Oropesa</option>
                                        <option value="Pachaconas, Provincia Antabamba">Distrito Pachaconas</option>
                                        <option value="Sabaino, Provincia Antabamba">Distrito Sabaino</option>
                                    </optgroup>
                                    
                                    <!-- PROVINCIA AYMARAES -->
                                    <optgroup label="PROVINCIA AYMARAES">
                                        <option value="Chalhuanca, Provincia Aymaraes">Distrito Chalhuanca</option>
                                        <option value="Capaya, Provincia Aymaraes">Distrito Capaya</option>
                                        <option value="Caraybamba, Provincia Aymaraes">Distrito Caraybamba</option>
                                        <option value="Chapimarca, Provincia Aymaraes">Distrito Chapimarca</option>
                                        <option value="Colcabamba, Provincia Aymaraes">Distrito Colcabamba</option>
                                        <option value="Cotaruse, Provincia Aymaraes">Distrito Cotaruse</option>
                                        <option value="Huayllo, Provincia Aymaraes">Distrito Huayllo</option>
                                        <option value="Justo Apu Sahuaraura, Provincia Aymaraes">Distrito Justo Apu Sahuaraura</option>
                                        <option value="Lucre, Provincia Aymaraes">Distrito Lucre</option>
                                        <option value="Pocohuanca, Provincia Aymaraes">Distrito Pocohuanca</option>
                                        <option value="San Juan de Chacña, Provincia Aymaraes">Distrito San Juan de Chacña</option>
                                        <option value="Sañayca, Provincia Aymaraes">Distrito Sañayca</option>
                                        <option value="Soraya, Provincia Aymaraes">Distrito Soraya</option>
                                        <option value="Tapairihua, Provincia Aymaraes">Distrito Tapairihua</option>
                                        <option value="Tintay, Provincia Aymaraes">Distrito Tintay</option>
                                        <option value="Toraya, Provincia Aymaraes">Distrito Toraya</option>
                                        <option value="Yanaca, Provincia Aymaraes">Distrito Yanaca</option>
                                    </optgroup>
                                    
                                    <!-- PROVINCIA COTABAMBAS -->
                                    <optgroup label="PROVINCIA COTABAMBAS">
                                        <option value="Tambobamba, Provincia Cotabambas">Distrito Tambobamba</option>
                                        <option value="Cotabambas, Provincia Cotabambas">Distrito Cotabambas</option>
                                        <option value="Coyllurqui, Provincia Cotabambas">Distrito Coyllurqui</option>
                                        <option value="Haquira, Provincia Cotabambas">Distrito Haquira</option>
                                        <option value="Mara, Provincia Cotabambas">Distrito Mara</option>
                                        <option value="Challhuahuacho, Provincia Cotabambas">Distrito Challhuahuacho</option>
                                    </optgroup>
                                    
                                    <!-- PROVINCIA CHINCHEROS -->
                                    <optgroup label="PROVINCIA CHINCHEROS">
                                        <option value="Chincheros, Provincia Chincheros">Distrito Chincheros</option>
                                        <option value="Anco Huallo, Provincia Chincheros">Distrito Anco Huallo</option>
                                        <option value="Cocharcas, Provincia Chincheros">Distrito Cocharcas</option>
                                        <option value="Huaccana, Provincia Chincheros">Distrito Huaccana</option>
                                        <option value="Ocobamba, Provincia Chincheros">Distrito Ocobamba</option>
                                        <option value="Ongoy, Provincia Chincheros">Distrito Ongoy</option>
                                        <option value="Uranmarca, Provincia Chincheros">Distrito Uranmarca</option>
                                        <option value="Ranracancha, Provincia Chincheros">Distrito Ranracancha</option>
                                    </optgroup>
                                    
                                    <!-- PROVINCIA GRAU -->
                                    <optgroup label="PROVINCIA GRAU">
                                        <option value="Chuquibambilla, Provincia Grau">Distrito Chuquibambilla</option>
                                        <option value="Curasco, Provincia Grau">Distrito Curasco</option>
                                        <option value="Curpahuasi, Provincia Grau">Distrito Curpahuasi</option>
                                        <option value="Huayllati, Provincia Grau">Distrito Huayllati</option>
                                        <option value="Mamara, Provincia Grau">Distrito Mamara</option>
                                        <option value="Micaela Bastidas, Provincia Grau">Distrito Micaela Bastidas</option>
                                        <option value="Pataypampa, Provincia Grau">Distrito Pataypampa</option>
                                        <option value="Progreso, Provincia Grau">Distrito Progreso</option>
                                        <option value="San Antonio, Provincia Grau">Distrito San Antonio</option>
                                        <option value="Santa Rosa, Provincia Grau">Distrito Santa Rosa</option>
                                        <option value="Turpay, Provincia Grau">Distrito Turpay</option>
                                        <option value="Vilcabamba, Provincia Grau">Distrito Vilcabamba</option>
                                        <option value="Virundo, Provincia Grau">Distrito Virundo</option>
                                        <option value="Mariscal Gamarra, Provincia Grau">Distrito Mariscal Gamarra</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold text-secondary">Dirección específica:</label>
                                <input type="text" class="form-control border-info" name="direccion_especifica" placeholder="Av/Jr/Calle, número, referencia" onchange="actualizarLugarCompleto()">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold text-info">Fecha:</label>
                                <input type="date" class="form-control border-info bg-light" name="fecha_intervencion" value="{{ date('Y-m-d') }}" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold text-info">Hora:</label>
                                <input type="time" class="form-control border-info bg-light" name="hora_intervencion" value="{{ date('H:i') }}" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold text-info">Ubicación completa:</label>
                                <input type="text" class="form-control border-info bg-light" id="ubicacion-completa" readonly placeholder="Se generará automáticamente" style="font-size: 12px;">
                            </div>
                        </div>
                        
                        <!-- Origen y Destino removidos según requerimiento -->
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-bold text-info">Tipo de Servicio:</label>
                                <select class="form-select border-info" name="tipo_servicio" required>
                                    <option value="">Seleccione tipo de servicio...</option>
                                    <option value="Interprovincial">Interprovincial</option>
                                    <option value="Interdistrital">Interdistrital</option>
                                    <option value="Urbano">Urbano</option>
                                    <option value="Turístico">Turístico</option>
                                    <option value="Carga">Transporte de Carga</option>
                                    <option value="Especial">Servicio Especial</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold text-info">Inspector Responsable:</label>
                                <input type="text" class="form-control border-info" name="inspector" value="{{ Auth::user()->name }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 3: DESCRIPCIÓN DE LA INFRACCIÓN -->
                <div class="card mb-4 border-danger">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>III. DESCRIPCIÓN DE LA INFRACCIÓN</h6>
                    </div>
                    <div class="card-body bg-light">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-danger">Tipo de Infracción:</label>
                            <select class="form-select border-danger" name="tipo_infraccion" required>
                                <option value="">Seleccione el tipo de infracción...</option>
                                <option value="documentaria">Infracción Documentaria</option>
                                <option value="administrativa">Infracción Administrativa</option>
                                <option value="tecnica">Infracción Técnica</option>
                                <option value="operacional">Infracción Operacional</option>
                                <option value="seguridad">Infracción de Seguridad</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-danger">Descripción Detallada de los Hechos:</label>
                            <textarea class="form-control border-danger" name="descripcion_hechos" rows="4" placeholder="Describa detalladamente la infracción detectada..." required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-danger">Código de Infracción:</label>
                                <input type="text" class="form-control border-danger" name="codigo_infraccion" placeholder="Ej: INF-001">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-danger">Gravedad:</label>
                                <select class="form-select border-danger" name="gravedad" required>
                                    <option value="">Seleccione gravedad...</option>
                                    <option value="leve">Leve</option>
                                    <option value="grave">Grave</option>
                                    <option value="muy_grave">Muy Grave</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Botones de acción -->
                <div class="text-center mt-4">
                    <button type="button" class="btn btn-success btn-lg me-2 px-4" id="btn-guardar-acta" onclick="submitActa(event)">
                        <i class="fas fa-save me-2"></i>GUARDAR ACTA
                    </button>

                    <!-- Descargar PDF -->
                    <button type="button" class="btn btn-outline-primary btn-lg me-2 px-4" id="btn-descargar-pdf" onclick="descargarPDF()" title="Descargar acta en PDF" style="display:none;">
                            <i class="fas fa-file-pdf me-2"></i>DESCARGAR PDF
                        </button>

                        <!-- Imprimir -->
                        <button type="button" class="btn btn-outline-info btn-lg me-3 px-4" id="btn-imprimir-acta" onclick="imprimirActa()" title="Imprimir acta" style="display:none;">
                            <i class="fas fa-print me-2"></i>IMPRIMIR
                        </button>

                        <!-- Botón limpiar: oculto hasta que se haya impreso/exportado -->
                        <button type="button" class="btn btn-secondary btn-lg px-5" id="btn-limpiar-acta" style="display:none;" onclick="limpiarDespuesDeExport()">
                            <i class="fas fa-undo me-2"></i>LIMPIAR FORMULARIO
                        </button>
                </div>

                <!-- Script: imprimir y descargar PDF -->
                <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
                <script>
                    /**
                     * buildPrintableActaHTML: genera una plantilla HTML (cliente) con estilo fijo
                     * a partir de los valores actuales del formulario. Esta plantilla se usa
                     * tanto para imprimir como para exportar a PDF.
                     */
                    function buildPrintableActaHTML(data) {
                        // Nota: mantener estilos inline para evitar dependencias externas
                        var css = `
                            @page { size: A4; margin: 8mm; }
                            body { font-family: Arial, Helvetica, sans-serif; color: #000; margin: 0; padding: 0; }
                            .acta-container { width: 210mm; max-width: 100%; margin: 0 auto; padding: 4mm; box-sizing: border-box; }
                            .header { display: flex; align-items: center; gap: 6px; margin-bottom: 8px; }
                            .logo { width: 55px; height: 55px; border: 2px solid #000; display: flex; align-items: center; justify-content: center; }
                            .header-center { flex: 1; text-align: center; }
                            .acta-title { font-size: 18px; font-weight: 700; margin: 0; }
                            .acta-meta { margin-top: 4px; font-weight: 700; display: flex; align-items: center; gap: 6px; justify-content: center; }
                            .numero-box { display: inline-block; min-width: 85px; max-width: 130px; padding: 4px 6px; border: 2px solid #000; text-align: center; font-weight: 700; background: #fff; }
                            .section { margin-top: 6px; border: 1px solid #000; padding: 6px; }
                            .row { display: flex; gap: 6px; margin-bottom: 2px; }
                            .col { flex: 1; }
                            .label { font-weight: 700; font-size: 11px; margin-bottom: 2px; }
                            .value { font-size: 12px; border-bottom: 1px dashed #000; padding: 2px 1px; min-height: 16px; }
                            .big-value { font-size: 13px; font-weight: 700; }
                            .signatures { display: flex; justify-content: space-between; margin-top: 12px; }
                            .sig-box { width: 45%; text-align: center; border-top: 1px solid #000; padding-top: 4px; }
                        `;

                        // Seguridad: normalizar undefined
                        data = data || {};

                        var html = `<!doctype html><html><head><meta charset="utf-8"><title>Acta - Imprimir</title><style>${css}</style></head><body>`;
                        html += `<div class="acta-container">`;
                        // Header
                        html += `<div class="header">`;
                        html += `<div class="logo"><img src="${data.escudo || '/images/escudo_peru.png'}" style="max-width:48px; max-height:48px;"/></div>`;
                        html += `<div class="header-center">`;
                        html += `<div class="acta-title">ACTA DE CONTROL</div>`;
                        html += `<div class="acta-meta">Nº ${data.numero_acta || data.numero || ''} - ${data.anio || new Date().getFullYear()}</div>`;
                        html += `</div>`;
                        html += `<div class="logo"><img src="${data.logo || '/images/logo-gobierno.png'}" style="max-width:48px; max-height:48px;"/></div>`;
                        html += `</div>`;

                        // Nota: se elimina fecha/hora en el header impreso por requerimiento

                        // Secciones principales
                        html += `<div class="section">`;
                        html += `<div class="label">I. DATOS DEL OPERADOR/CONDUCTOR</div>`;
                        html += `<div style="margin-top:4px;">`;
                        html += `<div class="row"><div class="col"><div class="label">RUC/DNI</div><div class="value">${data.ruc_dni || ''}</div></div>`;
                        html += `<div class="col"><div class="label">Razón social / Nombres</div><div class="value">${data.razon_social || ''}</div></div>`;
                        html += `<div class="col"><div class="label">Placa</div><div class="value">${data.placa || ''}</div></div></div>`;
                        html += `<div class="row"><div class="col"><div class="label">Conductor</div><div class="value">${data.nombre_conductor || ''}</div></div>`;
                        html += `<div class="col"><div class="label">N° Licencia</div><div class="value">${data.licencia || ''}</div></div>`;
                        html += `<div class="col"><div class="label">Clase/Categoría</div><div class="value">${data.clase_categoria || ''}</div></div></div>`;
                        html += `</div></div>`;

                        html += `<div class="section">`;
                        html += `<div class="label">II. DATOS DE LA INTERVENCIÓN</div>`;
                        html += `<div style="margin-top:4px;">`;
                        html += `<div class="label">Lugar</div><div class="value">${data.lugar_intervencion || ''}${data.direccion_especifica ? ' - ' + data.direccion_especifica : ''}</div>`;
                        html += `<div style="margin-top:3px;" class="label">Tipo de Servicio</div><div class="value">${data.tipo_servicio || ''}</div>`;
                        html += `</div></div>`;

                        html += `<div class="section">`;
                        html += `<div class="label">III. DESCRIPCIÓN DE LA INFRACCIÓN</div>`;
                        html += `<div style="margin-top:4px;"><div class="label">Tipo / Código / Gravedad</div><div class="value">${data.tipo_infraccion || ''} ${data.codigo_infraccion ? ' / ' + data.codigo_infraccion : ''} ${data.gravedad ? ' / ' + data.gravedad : ''}</div></div>`;
                        html += `<div style="margin-top:4px;"><div class="label">Descripción detallada</div><div class="value" style="min-height:60px;">${(data.descripcion_hechos || '').replace(/\n/g, '<br/>')}</div></div>`;
                        html += `</div>`;

                        // Multa / monto
                        html += `<div style="margin-top:6px; display:flex; gap:8px;"><div style="flex:1;"><div class="label">Monto de la multa</div><div class="value big-value">${data.monto_multa ? ('S/ ' + Number(data.monto_multa).toFixed(2)) : ''}</div></div>`;
                        html += `<div style="width:200px;"><div class="label">Vencimiento</div><div class="value">${data.vencimiento || ''}</div></div></div>`;

                        // Firmas: no mostrar nombre del inspector en la exportación (se deja el espacio para firma)
                        html += `<div class="signatures">`;
                        html += `<div class="sig-box">Firma del Inspector<br/><div style="margin-top:8px; font-weight:700;">&nbsp;</div></div>`;
                        html += `<div class="sig-box">Firma del Operador / Conductor<br/><div style="margin-top:8px; font-weight:700;">${data.nombre_conductor || ''}</div></div>`;
                        html += `</div>`;

                        html += `</div>`; // container
                        html += `</body></html>`;
                        return html;
                    }

                    /**
                     * imprimirActa: toma los valores actuales del formulario, genera la
                     * plantilla imprimible y abre una ventana para imprimir.
                     */
                    function imprimirActa() {
                        var form = document.getElementById('form-nueva-acta');
                        if (!form) { alert('Formulario no encontrado para imprimir.'); return; }

                        // Mapear valores del formulario
                        var fd = new FormData(form);
                        var data = {
                            numero_acta: fd.get('numero_acta') || document.getElementById('numero_acta_hidden') && document.getElementById('numero_acta_hidden').value,
                            anio: new Date().getFullYear(),
                            fecha: fd.get('fecha_intervencion') || fd.get('fecha_inspeccion_hidden') || document.querySelector('input[name="fecha_intervencion"]') && document.querySelector('input[name="fecha_intervencion"]').value || '',
                            hora: fd.get('hora_intervencion') || fd.get('hora_inicio_hidden') || document.querySelector('input[name="hora_intervencion"]') && document.querySelector('input[name="hora_intervencion"]').value || '',
                            ruc_dni: fd.get('ruc_dni') || '',
                            razon_social: fd.get('razon_social') || '',
                            placa: fd.get('placa_1') || fd.get('placa') || '',
                            // Preferir campo 'apellidos_nombres' si existe
                            nombre_conductor: (fd.get('apellidos_nombres') && fd.get('apellidos_nombres').trim() !== '') ? fd.get('apellidos_nombres').trim() : (fd.get('nombre_conductor') || ''),
                            licencia: fd.get('licencia_conductor_1') || fd.get('licencia_conductor') || '',
                            clase_categoria: fd.get('clase_categoria') || '',
                            lugar_intervencion: fd.get('lugar_intervencion') || '',
                            direccion_especifica: fd.get('direccion_especifica') || '',
                            tipo_servicio: fd.get('tipo_servicio') || '',
                            tipo_infraccion: fd.get('tipo_infraccion') || '',
                            codigo_infraccion: fd.get('codigo_infraccion') || '',
                            gravedad: fd.get('gravedad') || '',
                            descripcion_hechos: fd.get('descripcion_hechos') || fd.get('descripcion') || '',
                            monto_multa: fd.get('monto_multa') || '',
                            vencimiento: fd.get('vencimiento') || '' ,
                            inspector: fd.get('inspector') || document.querySelector('input[name="inspector"]') && document.querySelector('input[name="inspector"]').value || ''
                        };

                        var html = buildPrintableActaHTML(data);
                        var printWindow = window.open('', '_blank');
                        printWindow.document.write(html);
                        printWindow.document.close();
                        printWindow.focus();
                        setTimeout(function(){
                            printWindow.print();
                            // Mostrar botón limpiar después de imprimir
                            try { document.getElementById('btn-limpiar-acta').style.display = 'inline-block'; } catch(e){}
                        }, 600);
                    }

                    /**
                     * descargarPDF: genera el mismo HTML imprimible y lo convierte a PDF usando html2pdf.
                     */
                    function descargarPDF() {
                        var form = document.getElementById('form-nueva-acta');
                        if (!form) { alert('Formulario no encontrado para exportar.'); return; }

                        var fd = new FormData(form);
                        var data = {
                            numero_acta: fd.get('numero_acta') || document.getElementById('numero_acta_hidden') && document.getElementById('numero_acta_hidden').value,
                            anio: new Date().getFullYear(),
                            fecha: fd.get('fecha_intervencion') || fd.get('fecha_inspeccion_hidden') || '',
                            hora: fd.get('hora_intervencion') || fd.get('hora_inicio_hidden') || '',
                            ruc_dni: fd.get('ruc_dni') || '',
                            razon_social: fd.get('razon_social') || '',
                            placa: fd.get('placa_1') || fd.get('placa') || '',
                            nombre_conductor: (fd.get('apellidos_nombres') && fd.get('apellidos_nombres').trim() !== '') ? fd.get('apellidos_nombres').trim() : (fd.get('nombre_conductor') || ''),
                            licencia: fd.get('licencia_conductor_1') || fd.get('licencia_conductor') || '',
                            clase_categoria: fd.get('clase_categoria') || '',
                            lugar_intervencion: fd.get('lugar_intervencion') || '',
                            direccion_especifica: fd.get('direccion_especifica') || '',
                            tipo_servicio: fd.get('tipo_servicio') || '',
                            tipo_infraccion: fd.get('tipo_infraccion') || '',
                            codigo_infraccion: fd.get('codigo_infraccion') || '',
                            gravedad: fd.get('gravedad') || '',
                            descripcion_hechos: fd.get('descripcion_hechos') || fd.get('descripcion') || '',
                            monto_multa: fd.get('monto_multa') || '',
                            vencimiento: fd.get('vencimiento') || '' ,
                            inspector: fd.get('inspector') || ''
                        };

                        var filename = 'acta-' + (data.numero_acta || new Date().toISOString().slice(0,19).replace(/[:T]/g,'-')) + '.pdf';
                        var opt = {
                            margin:       10,
                            filename:     filename,
                            image:        { type: 'jpeg', quality: 0.98 },
                            html2canvas:  { scale: 2, useCORS: true },
                            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
                        };

                        // Generar el HTML y extraer el body dentro de un contenedor para html2pdf
                        var printableHTML = buildPrintableActaHTML(data);
                        var wrapper = document.createElement('div');
                        wrapper.innerHTML = printableHTML;

                        // html2pdf espera un elemento DOM
                            // html2pdf devuelve una Promise
                            return html2pdf().set(opt).from(wrapper).save().then(() => {
                                try { document.getElementById('btn-limpiar-acta').style.display = 'inline-block'; } catch(e){}
                            });
                    }
                </script>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: EDITAR ACTA -->
<div class="floating-modal" id="modal-editar-acta">
    <div class="modal-content-wrapper">
        <div class="modal-header-custom">
            <h4 class="mb-0 fw-bold">
                <i class="fas fa-edit me-2"></i>
                EDITAR ACTA DE FISCALIZACIÓN EXISTENTE
            </h4>
            <button class="close-modal" onclick="cerrarModal('modal-editar-acta')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body-custom">
            <!-- Buscador de Acta -->
            <div class="card mb-4 border-warning">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-search me-2"></i>BUSCAR ACTA PARA EDITAR</h6>
                </div>
                <div class="card-body bg-light">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold text-warning">Criterio de Búsqueda:</label>
                            <input type="text" class="form-control border-warning" id="buscar-editar" placeholder="Ingrese N° de Acta, RUC/DNI o Placa del vehículo">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold text-warning">Acción:</label>
                            <button type="button" class="btn btn-warning d-block w-100 fw-bold" onclick="buscarActaEditar()">
                                <i class="fas fa-search me-2"></i>BUSCAR ACTA
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Resultado de la búsqueda y formulario de edición -->
            <div id="resultado-editar" style="display: none;">
                <div class="alert alert-warning border-warning">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <i class="fas fa-info-circle fa-2x"></i>
                        </div>
                        <div class="col">
                            <h5 class="mb-1">ACTA ENCONTRADA</h5>
                            <strong>Editando Acta N°:</strong> <span id="acta-numero-editar" class="text-danger"></span>
                        </div>
                    </div>
                </div>
                
                <!-- Formulario de Edición de Acta -->
                <div id="formulario-edicion" style="display: none;">
                    <form id="form-editar-acta" class="needs-validation" novalidate>
                        <input type="hidden" id="acta-id-editar">
                        
                        <!-- Datos Básicos del Acta -->
                        <div class="section-header mb-4">
                            <h5 class="mb-0 fw-bold text-primary">
                                <i class="fas fa-file-alt me-2"></i>DATOS BÁSICOS DEL ACTA
                            </h5>
                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Número de Acta</label>
                                <input type="text" class="form-control form-control-lg" id="edit-numero-acta" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Fecha</label>
                                <input type="date" class="form-control form-control-lg" id="edit-fecha" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Hora</label>
                                <input type="time" class="form-control form-control-lg" id="edit-hora" required>
                            </div>
                        </div>
                        
                        <!-- Datos de la Empresa -->
                        <div class="section-header mb-4">
                            <h5 class="mb-0 fw-bold text-success">
                                <i class="fas fa-building me-2"></i>DATOS DE LA EMPRESA
                            </h5>
                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Empresa / Operador</label>
                                <input type="text" class="form-control form-control-lg" id="edit-empresa" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">RUC</label>
                                <input type="text" class="form-control form-control-lg" id="edit-ruc-empresa" 
                                       pattern="[0-9]{11}" maxlength="11">
                            </div>
                        </div>
                        
                        <!-- Datos del Conductor -->
                        <div class="section-header mb-4">
                            <h5 class="mb-0 fw-bold text-info">
                                <i class="fas fa-user me-2"></i>DATOS DEL CONDUCTOR
                            </h5>
                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nombre del Conductor</label>
                                <input type="text" class="form-control form-control-lg" id="edit-conductor" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">DNI</label>
                                <input type="text" class="form-control form-control-lg" id="edit-documento" 
                                       pattern="[0-9]{8}" maxlength="8" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Licencia</label>
                                <input type="text" class="form-control form-control-lg" id="edit-licencia">
                            </div>
                        </div>
                        
                        <!-- Datos del Vehículo -->
                        <div class="section-header mb-4">
                            <h5 class="mb-0 fw-bold text-warning">
                                <i class="fas fa-car me-2"></i>DATOS DEL VEHÍCULO
                            </h5>
                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Placa</label>
                                <input type="text" class="form-control form-control-lg" id="edit-placa" 
                                       style="text-transform: uppercase;" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tipo de Servicio</label>
                                <select class="form-select form-select-lg" id="edit-tipo-servicio" required>
                                    <option value="">Seleccione...</option>
                                    <option value="TRANSPORTE PUBLICO REGULAR">TRANSPORTE PÚBLICO REGULAR</option>
                                    <option value="TRANSPORTE PUBLICO ESPECIAL">TRANSPORTE PÚBLICO ESPECIAL</option>
                                    <option value="TRANSPORTE TURISMO">TRANSPORTE TURISMO</option>
                                    <option value="TRANSPORTE ESCOLAR">TRANSPORTE ESCOLAR</option>
                                    <option value="TRANSPORTE CARGA">TRANSPORTE DE CARGA</option>
                                    <option value="TAXI">TAXI</option>
                                    <option value="REMISSE">REMISSE</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Origen</label>
                                <input type="text" class="form-control form-control-lg" id="edit-origen-viaje">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Destino</label>
                                <input type="text" class="form-control form-control-lg" id="edit-destino-viaje">
                            </div>
                        </div>
                        
                        <!-- Ubicación y Detalles -->
                        <div class="section-header mb-4">
                            <h5 class="mb-0 fw-bold text-danger">
                                <i class="fas fa-map-marker-alt me-2"></i>UBICACIÓN Y DETALLES DE LA INFRACCIÓN
                            </h5>
                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Lugar de la Intervención</label>
                                <input type="text" class="form-control form-control-lg" id="edit-ubicacion" required>
                            </div>
                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Descripción de los Hechos</label>
                                <textarea class="form-control" rows="4" id="edit-descripcion-hechos" 
                                        placeholder="Describa detalladamente los hechos que motivaron la intervención..." required></textarea>
                            </div>
                        </div>
                        
                        <!-- Datos de la Sanción -->
                        <div class="section-header mb-4">
                            <h5 class="mb-0 fw-bold text-secondary">
                                <i class="fas fa-gavel me-2"></i>DATOS DE LA SANCIÓN
                            </h5>
                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Monto de la Multa (S/)</label>
                                <input type="number" class="form-control form-control-lg" id="edit-monto-multa" 
                                       min="0" step="0.01" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Estado del Acta</label>
                                <select class="form-select form-select-lg" id="edit-estado" required>
                                    <option value="pendiente">PENDIENTE</option>
                                    <option value="pagada">PAGADA</option>
                                    <option value="anulada">ANULADA</option>
                                    <option value="en_proceso">EN PROCESO</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Inspector Responsable</label>
                                <input type="text" class="form-control form-control-lg" id="edit-inspector-responsable" required>
                            </div>
                        </div>
                        
                        <!-- Botones de Acción -->
                        <div class="row g-3 mt-4">
                            <div class="col-md-6">
                                <button type="button" class="btn btn-secondary btn-lg w-100" onclick="cancelarEdicion()">
                                    <i class="fas fa-times me-2"></i>CANCELAR
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-success btn-lg w-100">
                                    <i class="fas fa-save me-2"></i>GUARDAR CAMBIOS
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- MODAL: ELIMINAR ACTA -->
<div class="floating-modal" id="modal-eliminar-acta">
    <div class="modal-content-wrapper">
        <div class="modal-header-custom" style="background: linear-gradient(135deg, #dc3545, #c82333);">
            <h4 class="mb-0 fw-bold">
                <i class="fas fa-trash-alt me-2"></i>
                ELIMINAR ACTA DEL SISTEMA
            </h4>
            <button class="close-modal" onclick="cerrarEliminarDirecto()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body-custom">
            <!-- Advertencia crítica -->
            <div class="alert alert-danger text-center mb-4 border-danger" style="background: #f8d7da;">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-3x text-danger"></i>
                    </div>
                    <div class="col">
                        <h4 class="mb-2 text-danger">⚠️ ADVERTENCIA CRÍTICA</h4>
                        <p class="mb-1 fw-bold">Esta acción eliminará permanentemente el acta del sistema</p>
                        <p class="mb-0 text-muted">Esta operación es IRREVERSIBLE y requiere autorización</p>
                    </div>
                </div>
            </div>
            
            <!-- Buscador de Acta -->
            <div class="card mb-4 border-danger">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-search me-2"></i>BUSCAR ACTA PARA ELIMINAR</h6>
                </div>
                <div class="card-body bg-light">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold text-danger">Criterio de Búsqueda:</label>
                            <input type="text" class="form-control border-danger" id="buscar-eliminar" placeholder="Ingrese N° de Acta, RUC/DNI o Placa del vehículo">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold text-danger">Acción:</label>
                            <button type="button" id="btn-buscar-eliminar" class="btn btn-danger d-block w-100 fw-bold" onclick="buscarActaEliminar()">
                                <i class="fas fa-search me-2"></i>BUSCAR ACTA
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Resultado de la búsqueda -->
            <div id="resultado-eliminar" style="display: none;">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-file-alt me-2"></i>ACTA ENCONTRADA</h6>
                    </div>
                    <div class="card-body bg-light">
                        <!-- Información del acta -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="info-group p-3 border border-danger rounded bg-white">
                                    <label class="form-label fw-bold text-danger">N° de Acta:</label>
                                    <p class="mb-0 fs-5" id="eliminar-numero-acta"></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-group p-3 border border-danger rounded bg-white">
                                    <label class="form-label fw-bold text-danger">Fecha de Registro:</label>
                                    <p class="mb-0 fs-5" id="eliminar-fecha-acta"></p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Motivo de eliminación -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-danger">Motivo de la Eliminación (Obligatorion):</label>
                            <select class="form-select border-danger mb-3" id="motivo-eliminacion" required>
                                <option value="">Seleccione el motivo...</option>
                                <option value="error_registro">Error en el registro</option>
                                <option value="duplicado">Acta duplicada</option>
                                <option value="datos_incorrectos">Datos incorrectos</option>
                                <option value="solicitud_operador">Solicitud del operador</option>
                                <option value="revision_superior">Revisión de superior</option>
                                <option value="otro">Otro motivo</option>
                            </select>
                            <textarea class="form-control border-danger" id="observaciones-eliminacion" rows="3" placeholder="Observaciones adicionales sobre la eliminación..."></textarea>
                        </div>
                        
                        <!-- Autorización -->
                        <div class="card border-warning mb-4" style="background: #fff3cd;">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0 fw-bold"><i class="fas fa-key me-2"></i>AUTORIZACIÓN REQUERIDA</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Supervisor Autorizante:</label>
                                        <input type="text" class="form-control border-warning" id="supervisor-autorizante" placeholder="Nombre del supervisor" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Botones de confirmación -->
                        <div class="text-center">
                            <button type="button" id="btn-confirmar-eliminacion" class="btn btn-danger btn-lg me-3 px-5" onclick="confirmarEliminacion()">
                                <i class="fas fa-trash me-2"></i>CONFIRMAR ELIMINACIÓN
                            </button>
                            <button type="button" class="btn btn-secondary btn-lg px-5" onclick="cancelarEliminacion()">
                                <i class="fas fa-times me-2"></i>CANCELAR OPERACIÓN
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<!-- MODAL: CONSULTAS Y REPORTES -->
<div class="floating-modal" id="modal-consultas">
    <div class="modal-content-wrapper">
        <div class="modal-header-custom" style="background: linear-gradient(135deg, #17a2b8, #138496);">
            <h4 class="mb-0 fw-bold">
                <i class="fas fa-search me-2"></i>
                CONSULTAS Y REPORTES DRTC
            </h4>
            <button class="close-modal" onclick="cerrarConsultasDirecto(); console.log('Botón X clickeado');">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body-custom">
            <!-- Formulario de filtros -->
            <div class="card mb-4 border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-search me-2"></i>CONSULTA RÁPIDA POR DOCUMENTO</h6>
                </div>
                <div class="card-body bg-light">
                    <!-- Consulta simple por DNI/RUC -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-primary">Consulta rápida por DNI o RUC:</label>
                            <div class="input-group">
                                <input type="text" class="form-control border-primary" id="documento-rapido" placeholder="Ingrese DNI (8 dígitos) o RUC (11 dígitos)" maxlength="11">
                                <button class="btn btn-primary" type="button" onclick="consultarPorDocumento()">
                                    <i class="fas fa-search me-1"></i>BUSCAR
                                </button>
                            </div>
                            <small class="text-muted">Busca todas las actas registradas para este documento</small>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Consulta instantánea:</strong> Ingrese el DNI o RUC y obtenga todos los registros de actas asociados con datos reales de la base de datos.
                            </div>
                        </div>
                    </div>
                    
                    <!-- Separador -->
                    <hr class="my-4">
                    
                    <!-- Filtros avanzados -->
                    <div class="mb-3">
                        <button class="btn btn-outline-secondary" type="button" onclick="toggleFiltrosAvanzados()">
                            <i class="fas fa-filter me-1"></i>Mostrar filtros avanzados
                        </button>
                    </div>
                    
                    <div id="filtros-avanzados" style="display: none;">
                        <h6 class="text-secondary mb-3"><i class="fas fa-sliders-h me-2"></i>Filtros Avanzados</h6>
                        <form id="form-consultas">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-bold text-info">N° de Acta:</label>
                                    <input type="text" class="form-control border-info" name="numero_acta" placeholder="DRTC-APU-2025-001" value="{{ $proximo_numero_acta ?? '' }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold text-info">Placa del Vehículo:</label>
                                    <input type="text" class="form-control border-info" name="placa" placeholder="ABC-123" style="text-transform: uppercase;">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold text-info">Estado del Acta:</label>
                                    <select class="form-select border-info" name="estado">
                                        <option value="">Todos los estados</option>
                                        <option value="pendiente">Registrada</option>
                                        <option value="procesada">Procesada</option>
                                        <option value="pendiente">Pendiente</option>
                                        <option value="anulada">Anulada</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold text-info">Fecha Desde:</label>
                                    <input type="date" class="form-control border-info" name="fecha_desde">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-bold text-info">Fecha Hasta:</label>
                                    <input type="date" class="form-control border-info" name="fecha_hasta">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-info">RUC/DNI (filtro avanzado):</label>
                                    <input type="text" class="form-control border-info" name="ruc_dni" placeholder="20123456789 o 12345678">
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-info w-100" onclick="ejecutarConsulta()" style="margin-top: 25px;">
                                        <i class="fas fa-search me-2"></i>CONSULTAR
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Botones de acción -->
            <div class="text-center mb-4">
                <button type="button" class="btn btn-info btn-lg me-2 px-4" onclick="ejecutarConsulta()">
                    <i class="fas fa-search me-2"></i>CONSULTAR ACTAS
                </button>
                <button type="button" class="btn btn-success btn-lg me-2 px-4" onclick="exportarExcel()">
                    <i class="fas fa-file-excel me-2"></i>EXPORTAR EXCEL
                </button>
                <button type="button" class="btn btn-danger btn-lg me-2 px-4" onclick="exportarPDF()">
                    <i class="fas fa-file-pdf me-2"></i>EXPORTAR PDF
                </button>
                <button type="button" class="btn btn-warning btn-lg px-4" onclick="generarReporte()">
                    <i class="fas fa-chart-bar me-2"></i>REPORTE ESTADÍSTICO
                </button>
            </div>
            
            <!-- Resumen de resultados -->
            <div id="resumen-consulta" class="card border-info mb-4" style="display: none;">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-info-circle me-2"></i>RESUMEN DE RESULTADOS</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="p-3 border border-info rounded bg-white">
                                <h4 class="text-info mb-1" id="total-actas">0</h4>
                                <small class="text-muted">Total de Actas</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 border border-success rounded bg-white">
                                <h4 class="text-success mb-1" id="actas-procesadas-modal">0</h4>
                                <small class="text-muted">Procesadas</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 border border-warning rounded bg-white">
                                <h4 class="text-warning mb-1" id="actas-pendientes-modal">0</h4>
                                <small class="text-muted">Pendientes</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 border border-danger rounded bg-white">
                                <h4 class="text-danger mb-1" id="actas-anuladas-modal">0</h4>
                                <small class="text-muted">Anuladas</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabla de resultados -->
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-table me-2"></i>RESULTADOS DE LA CONSULTA</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0" id="tabla-resultados">
                            <thead class="bg-info text-white">
                                <tr>
                                    <th class="py-3">N° ACTA</th>
                                    <th>FECHA</th>
                                    <th>EMPRESA/CONDUCTOR</th>
                                    <th>RUC/DNI</th>
                                    <th>PLACA</th>
                                    <th>UBICACIÓN</th>
                                    <th>MONTO</th>
                                    <th>ESTADO</th>
                                    <th>INSPECTOR</th>
                                    <th>ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-resultados">
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="fas fa-search me-2"></i>
                                        Use los filtros y haga clic en "Consultar Actas" para ver los resultados
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// FUNCIONES GLOBALES PARA MODALES
function cerrarConsultasDirecto() {
    console.log('Cerrando modal de consultas...');
    var modal = document.getElementById('modal-consultas');
    if (modal) {
        // Método 1: Estilos CSS agresivos
        modal.style.cssText = 'display: none !important; visibility: hidden !important; opacity: 0 !important; z-index: -9999 !important;';
        
        // Método 2: Propiedades individuales
        modal.style.setProperty('display', 'none', 'important');
        modal.style.setProperty('visibility', 'hidden', 'important');
        modal.style.setProperty('opacity', '0', 'important');
        modal.style.setProperty('pointer-events', 'none', 'important');
        
        // Método 3: Clases
        modal.classList.remove('d-flex', 'show');
        modal.classList.add('d-none');
        
        // Método 4: Remover del DOM temporalmente y volver a agregar
        var padre = modal.parentNode;
        var siguienteHermano = modal.nextSibling;
        padre.removeChild(modal);
        modal.style.display = 'none';
        padre.insertBefore(modal, siguienteHermano);
        
        // Método 5: Inyectar CSS específico para ocultar
        var estiloOcultar = document.getElementById('estilo-ocultar-modal');
        if (!estiloOcultar) {
            estiloOcultar = document.createElement('style');
            estiloOcultar.id = 'estilo-ocultar-modal';
            estiloOcultar.innerHTML = '#modal-consultas { display: none !important; visibility: hidden !important; opacity: 0 !important; z-index: -9999 !important; }';
            document.head.appendChild(estiloOcultar);
        }
        
        document.body.style.overflow = '';
        console.log('Modal de consultas cerrado');
        
        // Verificación final
        setTimeout(function() {
            console.log('Verificación post-cierre:');
            console.log('- Display:', modal.style.display);
            console.log('- Visibility:', modal.style.visibility);
            console.log('- Opacity:', modal.style.opacity);
            console.log('- Visible:', modal.offsetWidth > 0 && modal.offsetHeight > 0);
        }, 100);
        
    } else {
        console.error('Modal consultas no encontrado para cerrar');
    }
}

function cerrarEliminarDirecto() {
    console.log('Cerrando modal de eliminar...');
    var modal = document.getElementById('modal-eliminar-acta');
    if (modal) {
        modal.style.display = 'none';
        modal.style.zIndex = '';
        modal.classList.remove('d-flex');
        document.body.style.overflow = '';
        console.log('Modal eliminar cerrado');
    } else {
        console.error('Modal eliminar no encontrado para cerrar');
    }
}

// Función universal para cerrar cualquier modal
function cerrarModalUniversal(modalId) {
    console.log('Cerrando modal:', modalId);
    var modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        modal.style.zIndex = '';
        modal.classList.remove('d-flex');
        document.body.style.overflow = '';
        console.log('Modal cerrado:', modalId);
    }
}

// Hacer funciones disponibles globalmente
window.cerrarConsultasDirecto = cerrarConsultasDirecto;
window.cerrarEliminarDirecto = cerrarEliminarDirecto;
window.cerrarModalUniversal = cerrarModalUniversal;

// Función de emergencia para cerrar todos los modales
window.cerrarTodosLosModalesEmergencia = function() {
    console.log('EMERGENCIA: Cerrando todos los modales');
    const modales = document.querySelectorAll('.floating-modal');
    modales.forEach(function(modal) {
        modal.style.cssText = 'display: none !important; visibility: hidden !important; opacity: 0 !important; z-index: -9999 !important;';
        modal.classList.remove('d-flex', 'show');
        modal.classList.add('d-none');
    });
    
    // Inyectar CSS ultra-agresivo
    var estiloEmergencia = document.createElement('style');
    estiloEmergencia.innerHTML = '.floating-modal { display: none !important; visibility: hidden !important; opacity: 0 !important; z-index: -9999 !important; }';
    document.head.appendChild(estiloEmergencia);
    
    document.body.style.overflow = '';
    console.log('Todos los modales cerrados FORZADAMENTE');
};

// Función específica para ocultar modal de consultas brutalmente
window.ocultarModalConsultasBrutal = function() {
    console.log('MÉTODO BRUTAL: Ocultando modal consultas');
    
    // Método 1: CSS directo
    var modal = document.getElementById('modal-consultas');
    if (modal) {
        modal.remove(); // Eliminar completamente del DOM
        console.log('Modal eliminado del DOM');
    }
    
    // Método 2: CSS global
    var estiloBrutal = document.createElement('style');
    estiloBrutal.innerHTML = '#modal-consultas, .floating-modal { display: none !important; visibility: hidden !important; opacity: 0 !important; z-index: -9999 !important; height: 0 !important; width: 0 !important; }';
    document.head.appendChild(estiloBrutal);
    
    document.body.style.overflow = '';
    console.log('Modal ocultado brutalmente');
};

// Función para testear el botón de cerrar específico
window.testearBotonCerrar = function() {
    const modalConsultas = document.getElementById('modal-consultas');
    if (modalConsultas) {
        const boton = modalConsultas.querySelector('.close-modal');
        if (boton) {
            console.log('Botón encontrado:', boton);
            console.log('Onclick:', boton.getAttribute('onclick'));
            boton.click();
        } else {
            console.error('Botón close-modal no encontrado');
        }
    } else {
        console.error('Modal consultas no encontrado');
    }
};

// FUNCIONES PARA APIs DE CONSULTA DNI/RUC
document.addEventListener('DOMContentLoaded', function() {
    // API para consulta de RUC/DNI único
    const rucDniInput = document.getElementById('ruc_dni');
    const razonSocialInput = document.getElementById('razon_social');
    const loadingData = document.getElementById('loading-data');
    
    // Conectar el botón "Probar" con la funcionalidad de consulta
    const btnProbarRucDni = document.getElementById('btn-probar-ruc-dni');
    if (btnProbarRucDni) {
        btnProbarRucDni.addEventListener('click', function() {
            const valor = rucDniInput.value.trim();
            if (valor.length === 8) {
                consultarDNI(valor);
            } else if (valor.length === 11) {
                consultarRUC(valor);
            } else {
                mostrarNotificacion('Ingrese un DNI (8 dígitos) o RUC (11 dígitos) válido', 'warning');
            }
        });
    }
    
    // Función para consultar RUC en SUNAT (con API de Decolecta mejorada)
    async function consultarRUC(ruc) {
        try {
            loadingData.style.display = 'block';
            razonSocialInput.value = '';
            
            // Lista de APIs a probar en orden (API ultra-robusta como principal)
            const apis = [
                // API ULTRA-ROBUSTA PRINCIPAL - Garantiza JSON válido siempre
                {
                    url: `/api/api-ruc-ultra.php?ruc=${ruc}`,
                    headers: {},
                    process: (data) => {
                        console.log('Respuesta API RUC Ultra:', data);
                        if (data && data.success && data.razon_social) {
                            return {
                                razonSocial: data.razon_social,
                                direccion: data.direccion || null,
                                estado: data.estado || null,
                                departamento: data.departamento || null,
                                fuente: data.fuente || 'API Ultra'
                            };
                        }
                        return null;
                    }
                },
                // API HÍBRIDA PRINCIPAL - APISPERU + Local como fallback
                {
                    url: `/api/api-ruc-hibrido.php?ruc=${ruc}`,
                    headers: {},
                    process: (data) => {
                        console.log('Respuesta API RUC Híbrida:', data);
                        if (data && data.success && data.razon_social) {
                            return {
                                razonSocial: data.razon_social,
                                direccion: data.direccion || null,
                                estado: data.estado || null,
                                departamento: data.departamento || null,
                                fuente: data.fuente || 'API Híbrida'
                            };
                        }
                        return null;
                    }
                },
                // API LOCAL PRINCIPAL - Siempre disponible
                {
                    url: `/api/api-ruc-local.php?ruc=${ruc}`,
                    headers: {},
                    process: (data) => {
                        console.log('Respuesta API RUC Local:', data);
                        if (data && data.success && data.razon_social) {
                            return {
                                razonSocial: data.razon_social,
                                direccion: data.direccion || null,
                                estado: data.estado || null
                            };
                        }
                        return null;
                    }
                },
                // API proxy PHP local para RUC (respaldo externo)
                {
                    url: `/api/test-api-ruc.php?ruc=${ruc}`,
                    headers: {},
                    process: (data) => {
                        console.log('Respuesta API RUC Proxy:', data);
                        if (data && data.success && data.razon_social) {
                            return {
                                razonSocial: data.razon_social,
                                direccion: data.direccion || null,
                                estado: data.estado || null
                            };
                        }
                        return null;
                    }
                },
                // API de APISPERU.com directa para RUC (sin token)
                {
                    url: `https://dniruc.apisperu.com/api/v1/ruc/${ruc}`,
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    process: (data) => {
                        console.log('Respuesta APISPERU RUC Directa:', data);
                        if (data && data.ruc && data.razonSocial) {
                            return {
                                razonSocial: data.razonSocial,
                                nombreComercial: data.nombreComercial,
                                direccion: data.direccion || null,
                                departamento: data.departamento,
                                provincia: data.provincia,
                                distrito: data.distrito,
                                estado: data.estado || null,
                                condicion: data.condicion || null,
                                capital: data.capital,
                                ubigeo: data.ubigeo,
                                telefonos: data.telefonos
                            };
                        }
                        return null;
                    }
                },
                // API de Decolecta SUNAT directa
                {
                    url: `https://api.decolecta.com/v1/sunat/ruc?numero=${ruc}`,
                    headers: {
                        'Referer': 'http://apis.net.pe/api-ruc',
                        'Authorization': 'Bearer apis-token-1.aTSI1U7KEuT-6bbbCguH-4Y8TI6KS73N'
                    },
                    process: (data) => {
                        console.log('Respuesta Decolecta SUNAT:', data);
                        if (data && data.data && data.data.razon_social) {
                            return {
                                razonSocial: data.data.razon_social,
                                direccion: data.data.direccion || null,
                                estado: data.data.estado || null
                            };
                        } else if (data && data.razon_social) {
                            return {
                                razonSocial: data.razon_social,
                                direccion: data.direccion || null,
                                estado: data.estado || null
                            };
                        }
                        return null;
                    }
                },
                // APIs de respaldo
                {
                    url: `https://api.apis.net.pe/v1/ruc?numero=${ruc}`,
                    headers: {},
                    process: (data) => {
                        if (data && data.razonSocial) {
                            return {
                                razonSocial: data.razonSocial,
                                direccion: data.direccion || null,
                                estado: null
                            };
                        }
                        return null;
                    }
                },
                // API de APISPERU.com para RUC (sin token - gratis)
                {
                    url: `https://dniruc.apisperu.com/api/v1/ruc/${ruc}`,
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    process: (data) => {
                        console.log('Respuesta APISPERU RUC:', data);
                        if (data && data.ruc && data.razonSocial) {
                            return {
                                razonSocial: data.razonSocial,
                                nombreComercial: data.nombreComercial,
                                direccion: data.direccion || null,
                                departamento: data.departamento,
                                provincia: data.provincia,
                                distrito: data.distrito,
                                estado: data.estado || null,
                                condicion: data.condicion || null,
                                capital: data.capital,
                                ubigeo: data.ubigeo,
                                telefonos: data.telefonos
                            };
                        }
                        return null;
                    }
                },
                // API de respaldo con token (deprecada)
                {
                    url: `https://dniruc.apisperu.com/api/v1/ruc/${ruc}?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6InRlc3RAdGVzdC5jb20ifQ.bb2doqtI_pKcqT3TsCtm9-lFfwHJUkkrOkF_a1r7jW4`,
                    headers: {},
                    process: (data) => {
                        if (data && data.success && data.razonSocial) {
                            return {
                                razonSocial: data.razonSocial,
                                direccion: data.direccion || null,
                                estado: data.estado || null
                            };
                        }
                        return null;
                    }
                }
            ];
            
            let datosEmpresa = null;
            let apiUsada = '';
            
            // Intentar con cada API hasta encontrar una que funcione
            for (const api of apis) {
                try {
                    console.log(`Intentando API RUC: ${api.url}`);
                    
                    // Configurar headers según la API
                    const fetchOptions = {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            ...api.headers
                        }
                    };
                    
                    const response = await fetch(api.url, fetchOptions);
                    const data = await response.json();
                    
                    console.log(`Respuesta de ${api.url}:`, data);
                    
                    datosEmpresa = api.process(data);
                    if (datosEmpresa && datosEmpresa.razonSocial) {
                        apiUsada = api.url.includes('api-ruc-ultra.php') ? 'API Ultra-Robusta DRTC' :
                                  api.url.includes('api-ruc-hibrido.php') ? 'API Híbrida APISPERU+Local' :
                                  api.url.includes('api/api-ruc-local.php') ? 'Base de Datos Local SUNAT-DRTC' :
                                  api.url.includes('api/test-api-ruc.php') ? 'API Proxy (Decolecta SUNAT)' :
                                  api.url.includes('dniruc.apisperu.com') ? 'APISPERU.com (Oficial)' :
                                  api.url.includes('decolecta') ? 'Decolecta SUNAT (Oficial)' : 
                                  api.url.includes('apis.net.pe') ? 'APIs.net.pe' : 
                                  'API Externa';
                        break;
                    }
                } catch (apiError) {
                    console.log(`Error con API RUC ${api.url}:`, apiError);
                    continue;
                }
            }
            
            if (datosEmpresa && datosEmpresa.razonSocial) {
                razonSocialInput.value = datosEmpresa.razonSocial;
                razonSocialInput.style.backgroundColor = '#d4edda';
                razonSocialInput.style.borderColor = '#28a745';
                
                // Construir tooltip con información adicional
                let tooltip = `Datos obtenidos de: ${apiUsada}`;
                if (datosEmpresa.direccion) {
                    tooltip += `\nDirección: ${datosEmpresa.direccion}`;
                }
                if (datosEmpresa.estado) {
                    tooltip += `\nEstado: ${datosEmpresa.estado}`;
                }
                razonSocialInput.title = tooltip;
                
                // Mostrar éxito en el info
                const infoData = document.getElementById('info-data');
                if (infoData) {
                    infoData.innerHTML = `<i class="fas fa-check-circle text-success me-1"></i>Datos obtenidos de ${apiUsada}`;
                    setTimeout(() => {
                        infoData.innerHTML = '<i class="fas fa-info-circle me-1"></i>RUC: 11 dígitos | DNI: 8 dígitos';
                    }, 3000);
                }
            } else {
                // Si ninguna API funcionó, permitir ingreso manual
                razonSocialInput.value = '';
                razonSocialInput.placeholder = 'RUC no encontrado - Ingrese la razón social manualmente';
                razonSocialInput.style.backgroundColor = '#fff3cd';
                razonSocialInput.style.borderColor = '#ffc107';
                razonSocialInput.focus();
                
                // Mostrar mensaje informativo
                const infoData = document.getElementById('info-data');
                if (infoData) {
                    infoData.innerHTML = '<i class="fas fa-exclamation-triangle text-warning me-1"></i>RUC no encontrado - Complete manualmente';
                    setTimeout(() => {
                        infoData.innerHTML = '<i class="fas fa-info-circle me-1"></i>RUC: 11 dígitos | DNI: 8 dígitos';
                    }, 5000);
                }
            }
        } catch (error) {
            console.error('Error general consultando RUC:', error);
            razonSocialInput.value = '';
            razonSocialInput.placeholder = 'Error de conexión - Ingrese la razón social manualmente';
            razonSocialInput.style.backgroundColor = '#fff3cd';
            razonSocialInput.style.borderColor = '#ffc107';
            razonSocialInput.focus();
        } finally {
            loadingData.style.display = 'none';
        }
    }
    
    // Función para consultar DNI usando el proxy local (PeruDevs). Autocompleta Apellidos y Nombres.
    async function consultarDNI(dni) {
        try {
            if (!dni || !/^[0-9]{8}$/.test(dni)) {
                mostrarNotificacion('DNI inválido. Debe ser 8 dígitos.', 'warning');
                return;
            }

            loadingData.style.display = 'block';

            // Elementos objetivo
            const apellidosInput = document.getElementById('apellidos_nombres');
            const nombreInput = document.querySelector('input[name="nombre_conductor_1"]'); // hidden fallback

            if (apellidosInput) apellidosInput.value = '';
            if (nombreInput) nombreInput.value = '';

            const url = `/api/proxy-dni?dni=${dni}`;
            const res = await fetch(url, { method: 'GET', headers: { 'Accept': 'application/json' } });

            if (!res.ok) {
                throw new Error('API DNI responded with status ' + res.status);
            }

            const json = await res.json();

            // PeruDevs responde con { estado, mensaje, resultado: { id, nombres, apellido_paterno, apellido_materno, nombre_completo } }
            const payload = (json && (json.resultado || json.data)) ? (json.resultado || json.data) : json || {};

            const nombreCompleto = (payload.nombre_completo || '').trim();
            const nombres = (payload.nombres || payload.nombres || '').trim();
            const apellidoP = (payload.apellido_paterno || payload.apellidoPaterno || '').trim();
            const apellidoM = (payload.apellido_materno || payload.apellidoMaterno || '').trim();

            const combined = (nombreCompleto && nombreCompleto.length > 0) ? nombreCompleto : [apellidoP, apellidoM, nombres].filter(Boolean).join(' ').trim();

            if (combined) {
                if (apellidosInput) {
                    apellidosInput.value = combined;
                    apellidosInput.style.backgroundColor = '#d4edda';
                    apellidosInput.style.borderColor = '#28a745';
                }

                if (nombreInput) {
                    // Guardar prenombres en el campo hidden por compatibilidad (intentar extraer prenombres)
                    const prenombres = nombres || '';
                    nombreInput.value = prenombres;
                    nombreInput.style.backgroundColor = '#d4edda';
                    nombreInput.style.borderColor = '#28a745';
                }

                mostrarNotificacion('Datos del DNI cargados automáticamente', 'success');
            } else {
                mostrarNotificacion('No se encontraron datos con el DNI proporcionado; complete manualmente.', 'warning');
                if (razonSocialInput) {
                    razonSocialInput.placeholder = 'Ingrese el nombre completo manualmente';
                    razonSocialInput.style.backgroundColor = '#fff3cd';
                    razonSocialInput.style.borderColor = '#ffc107';
                }
            }
        } catch (err) {
            console.error('Error consultando DNI (proxy):', err);
            mostrarNotificacion('No fue posible obtener datos del DNI desde la API (fallback a ingreso manual).', 'error');
            if (razonSocialInput) {
                razonSocialInput.placeholder = 'Ingrese el nombre completo manualmente';
                razonSocialInput.style.backgroundColor = '#fff3cd';
                razonSocialInput.style.borderColor = '#ffc107';
            }
        } finally {
            loadingData.style.display = 'none';
        }
    }
    
    // Event listener para RUC/DNI único
    rucDniInput.addEventListener('blur', function() {
        const valor = this.value.trim();
        
        // Limpiar estilos previos
        razonSocialInput.style.backgroundColor = '';
        razonSocialInput.style.borderColor = '';
        razonSocialInput.title = '';
        razonSocialInput.placeholder = 'Se completará automáticamente al ingresar RUC/DNI';
        
        if (valor.length === 8 && /^\d{8}$/.test(valor)) {
            // Es un DNI
            console.log(`Consultando DNI: ${valor}`);
            consultarDNI(valor);
        } else if (valor.length === 11 && /^\d{11}$/.test(valor)) {
            // Es un RUC
            console.log(`Consultando RUC: ${valor}`);
            consultarRUC(valor);
        } else if (valor.length > 0) {
            razonSocialInput.value = '';
            razonSocialInput.placeholder = 'Formato inválido - DNI: 8 dígitos, RUC: 11 dígitos';
            razonSocialInput.style.backgroundColor = '#f8d7da';
            razonSocialInput.style.borderColor = '#dc3545';
        }
    });
    
    // Función para probar APIs manualmente (botón de prueba)
    function crearBotonPrueba() {
        const btnPrueba = document.createElement('button');
        btnPrueba.type = 'button';
        btnPrueba.className = 'btn btn-sm btn-outline-secondary ms-2';
        btnPrueba.innerHTML = '<i class="fas fa-search me-1"></i>Probar';
        btnPrueba.title = 'Probar consulta manualmente';
        
        btnPrueba.onclick = function() {
            const valor = rucDniInput.value.trim();
            if (valor.length === 8) {
                consultarDNI(valor);
            } else if (valor.length === 11) {
                consultarRUC(valor);
            } else {
                mostrarNotificacion('Ingrese un DNI (8 dígitos) o RUC (11 dígitos) válido', 'warning');
            }
        };
        
        // Agregar el botón al lado del campo RUC/DNI
        const container = rucDniInput.parentNode;
        const inputGroup = document.createElement('div');
        inputGroup.className = 'input-group';
        
        // Mover el input al grupo
        container.removeChild(rucDniInput);
        inputGroup.appendChild(rucDniInput);
        
        // Agregar el botón
        const appendDiv = document.createElement('div');
        appendDiv.className = 'input-group-append';
        appendDiv.appendChild(btnPrueba);
        inputGroup.appendChild(appendDiv);
        
        // Agregar el grupo al contenedor
        const label = container.querySelector('label');
        container.insertBefore(inputGroup, label.nextSibling);
    }
    
    // Crear el botón de prueba
    setTimeout(crearBotonPrueba, 100);
    
    // Validación en tiempo real para RUC/DNI
    rucDniInput.addEventListener('input', function() {
        const valor = this.value;
        
        // Solo permitir números
        this.value = valor.replace(/[^0-9]/g, '');
        
        // Validar longitud
        if (this.value.length > 11) {
            this.value = this.value.substring(0, 11);
        }
        
        // Indicar el tipo de documento en tiempo real
        const length = this.value.length;
        if (length <= 8) {
            this.placeholder = 'DNI: 12345678';
            this.style.borderColor = length === 8 ? '#28a745' : '#ffc107';
        } else {
            this.placeholder = 'RUC: 20123456789';
            this.style.borderColor = length === 11 ? '#28a745' : '#ffc107';
        }
    });
});

// Listeners auxiliares para diagnosticar clicks en modal eliminar
(function(){
    document.addEventListener('DOMContentLoaded', function(){
        const bBuscar = document.getElementById('btn-buscar-eliminar');
        const bConfirm = document.getElementById('btn-confirmar-eliminacion');
        if (bBuscar) {
            bBuscar.addEventListener('click', function(e){
                console.log('Click detectado: btn-buscar-eliminar');
                try {
                    const fn = window.__buscarActaEliminarReal || window.buscarActaEliminar;
                    if (typeof fn === 'function') fn(); else console.warn('buscarActaEliminar no está definido');
                } catch(err){ console.error('Error ejecutar buscarActaEliminar():', err); }
            });
        }
        if (bConfirm) {
            bConfirm.addEventListener('click', function(e){
                console.log('Click detectado: btn-confirmar-eliminacion');
                try {
                    const fn = window.__confirmarEliminacionReal || window.confirmarEliminacion;
                    if (typeof fn === 'function') fn(); else console.warn('confirmarEliminacion no está definido');
                } catch(err){ console.error('Error ejecutar confirmarEliminacion():', err); }
            });
        }
    });
})();

// Limpiar formulario luego de exportar o imprimir, evitando duplicados
function limpiarDespuesDeExport() {
    try {
        // Si tenemos metadatos de la última acta guardada, validar que exista en BD (opcionalmente pedir al servidor)
        const meta = window.__lastSavedActa || null;
        if (meta && meta.id) {
            // Evitar reenvío: pedir confirmación al usuario
            if (!confirm('¿Desea limpiar el formulario ahora? Se conservará el registro guardado.')) return;
        }

        // Limpiar el formulario
        const form = document.getElementById('form-nueva-acta');
        if (form) form.reset();

        // Reset UI: ocultar botones de imprimir/descarga/limpiar y habilitar guardar
        try {
            const btnPdf = document.getElementById('btn-descargar-pdf');
            const btnPrint = document.getElementById('btn-imprimir-acta');
            const btnClear = document.getElementById('btn-limpiar-acta');
            const submitBtn = document.getElementById('btn-guardar-acta');
            if (btnPdf) btnPdf.style.display = 'none';
            if (btnPrint) btnPrint.style.display = 'none';
            if (btnClear) btnClear.style.display = 'none';
            if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>GUARDAR ACTA'; }
        } catch (e) { console.warn('No se pudo resetear UI:', e); }

        // Intentar recargar tabla de actas si existe la función
        try { if (typeof cargarActas === 'function') cargarActas(); } catch (e) { }

        // Borrar metadatos locales
        try { delete window.__lastSavedActa; } catch(e){}
    } catch (e) {
        console.warn('Error limpiando después de export:', e);
    }
}

// FUNCIONES PARA MODALES FLOTANTES (código existente)
let tiempoInicioRegistro = null;
let actaIdEnProceso = null;
let autoguardadoInterval = null;

// Función para abrir modales con z-index dinámico
function abrirModal(modalId) {
    console.log('abrirModal llamado con:', modalId);
    
    const modal = document.getElementById(modalId);
    if (!modal) {
        console.error('Modal no encontrado:', modalId);
        return;
    }
    
    console.log('Modal encontrado:', modal);
    
    // Incrementar z-index base para cada nuevo modal
    if (!window.modalZIndex) window.modalZIndex = 9999;
    window.modalZIndex += 10;
    
    // Dar boost especial al modal de consultas
    if (modalId === 'modal-consultas') {
        window.modalZIndex += 20;
        console.log('Boost aplicado para modal-consultas');
    }
    
    console.log('Z-index aplicado:', window.modalZIndex);
    
    // Limpiar estilos previos
    modal.style.display = '';
    modal.style.zIndex = '';
    modal.classList.remove('show');
    
    // Aplicar estilos para mostrar (método que funciona)
    modal.style.display = 'flex';
    modal.style.zIndex = window.modalZIndex;
    modal.style.position = 'fixed';
    modal.style.top = '0';
    modal.style.left = '0';
    modal.style.width = '100%';
    modal.style.height = '100%';
    modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    
    document.body.style.overflow = 'hidden';
    
    console.log('Modal debería estar visible ahora');
    
    // Auto-llenar campos de fecha y hora en modal nueva acta
    if (modalId === 'modal-nueva-acta') {
        iniciarRegistroAutomatico();
    }
    
    // Cargar estadísticas reales en modal de consultas
    if (modalId === 'modal-consultas') {
        console.log('Cargando estadísticas para modal-consultas');
        cargarEstadisticasReales();
    }
}

// Función para cerrar modales
function cerrarModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    
    // Ocultar modal
    modal.style.display = 'none';
    modal.style.zIndex = '';
    modal.classList.remove('show');
    
    // Verificar si hay otros modales abiertos
    const modalesAbiertos = document.querySelectorAll('.floating-modal');
    let hayModalAbierto = false;
    
    for (let i = 0; i < modalesAbiertos.length; i++) {
        if (modalesAbiertos[i].style.display === 'flex') {
            hayModalAbierto = true;
            break;
        }
    }
    
    // Si no hay modales abiertos, restaurar scroll
    if (!hayModalAbierto) {
        document.body.style.overflow = '';
        // Resetear contador si no hay modales abiertos
        window.modalZIndex = 9999;
    }
}

// Función para iniciar el registro automático de tiempo
function iniciarRegistroAutomatico() {
    tiempoInicioRegistro = new Date();
    const ahora = tiempoInicioRegistro;
    
    // Llenar campos automáticos
    document.getElementById('fecha_inspeccion_hidden').value = ahora.toISOString().split('T')[0];
    document.getElementById('hora_inicio_hidden').value = ahora.toTimeString().slice(0, 5);
    
    // Mostrar información de tiempo en el formulario
    mostrarTiempoEnFormulario();
    
    // Iniciar autoguardado cada 30 segundos
    iniciarAutoguardado();
    
    console.log('Registro iniciado a las:', ahora.toLocaleTimeString());
}

// Función para mostrar el tiempo en el formulario
function mostrarTiempoEnFormulario() {
    // Crear elementos para mostrar el tiempo si no existen
    if (!document.getElementById('tiempo-registro-info')) {
        const tiempoInfo = document.createElement('div');
        tiempoInfo.id = 'tiempo-registro-info';
        tiempoInfo.className = 'alert alert-info d-flex align-items-center mb-3';
        tiempoInfo.innerHTML = `
            <i class="fas fa-clock me-2"></i>
            <div>
                <strong>Registro iniciado:</strong> <span id="hora-inicio-display">${tiempoInicioRegistro.toLocaleTimeString()}</span> |
                <strong>Tiempo transcurrido:</strong> <span id="tiempo-transcurrido">00:00:00</span>
                <span id="autoguardado-status" class="text-success ms-3"><i class="fas fa-check-circle"></i> Autoguardado activo</span>
            </div>
        `;
        
        // Insertar después del header del modal
        const modalBody = document.querySelector('#modal-nueva-acta .modal-body-custom');
        modalBody.insertBefore(tiempoInfo, modalBody.firstChild);
    }
    
    // Actualizar tiempo transcurrido cada segundo
    setInterval(actualizarTiempoTranscurrido, 1000);
}

// Función para actualizar el tiempo transcurrido
function actualizarTiempoTranscurrido() {
    if (!tiempoInicioRegistro) return;
    
    const ahora = new Date();
    const diferencia = ahora - tiempoInicioRegistro;
    
    const horas = Math.floor(diferencia / 3600000);
    const minutos = Math.floor((diferencia % 3600000) / 60000);
    const segundos = Math.floor((diferencia % 60000) / 1000);
    
    const tiempoFormateado = `${horas.toString().padStart(2, '0')}:${minutos.toString().padStart(2, '0')}:${segundos.toString().padStart(2, '0')}`;
    
    const elemento = document.getElementById('tiempo-transcurrido');
    if (elemento) {
        elemento.textContent = tiempoFormateado;
    }
}

// Función para iniciar autoguardado


// Función para mostrar error de autoguardado
function mostrarErrorAutoguardado() {
    const status = document.getElementById('autoguardado-status');
    if (status) {
        status.innerHTML = `<i class="fas fa-exclamation-triangle text-warning"></i> Error en autoguardado`;
        status.className = 'text-warning ms-3';
    }
}

// Función para finalizar registro
function finalizarRegistroActa() {
    if (!actaIdEnProceso) {
        mostrarNotificacion('No hay un acta en proceso para finalizar', 'warning');
        return;
    }
    
    fetch(`/api/actas/${actaIdEnProceso}/finalizar`, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            mostrarNotificacion(`Acta finalizada exitosamente.\nTiempo total: ${result.tiempo_total}\nHora de finalización: ${result.hora_finalizacion}`, 'success', 6000);
            limpiarRegistroTiempo();
            function exportarPDF() {
                // use shared helper: if single row selected it will still export the visible table
                exportTableToPDF('#tabla-resultados', `Actas_Export_${new Date().toISOString().slice(0,10)}.pdf`);
            }
                document.getElementById('edit-conductor').value = acta.conductor || '';
                document.getElementById('edit-documento').value = acta.documento || '';
                document.getElementById('edit-licencia').value = acta.licencia || '';
                document.getElementById('edit-placa').value = acta.placa || '';
                document.getElementById('edit-tipo-servicio').value = acta.tipo_servicio || '';
                document.getElementById('edit-origen-viaje').value = acta.origen_viaje || '';
                document.getElementById('edit-destino-viaje').value = acta.destino_viaje || '';
                document.getElementById('edit-ubicacion').value = acta.ubicacion || acta.lugar_intervencion || '';
                document.getElementById('edit-descripcion-hechos').value = acta.descripcion_hechos || '';
                document.getElementById('edit-monto-multa').value = acta.monto_multa || '0';
                document.getElementById('edit-estado').value = acta.estado || 'pendiente';
                document.getElementById('edit-inspector-responsable').value = acta.inspector_responsable || '';
                
                mostrarNotificacion(`Acta encontrada: ${acta.numero_acta}. Ya puede editar los campos.`, 'success');
                console.log('Acta encontrada:', acta);
                
            } else {
                document.getElementById('resultado-editar').style.display = 'none';
                document.getElementById('formulario-edicion').style.display = 'none';
                mostrarNotificacion(data.message || 'No se encontró ninguna acta con el criterio especificado', 'error');
            }
        })
        .catch(error => {
            console.error('Error al buscar acta:', error);
            document.getElementById('resultado-editar').style.display = 'none';
            document.getElementById('formulario-edicion').style.display = 'none';
            mostrarNotificacion('Error al buscar el acta. Por favor intente nuevamente.', 'error');
        });
}

// Modal Eliminar Acta
function buscarActaEliminar() {
    const criterioEL = document.getElementById('buscar-eliminar');
    const criterio = criterioEL ? criterioEL.value.trim() : '';

    if (!criterio) {
        mostrarNotificacion('Por favor ingrese un criterio de búsqueda', 'warning');
        if (criterioEL) criterioEL.focus();
        return;
    }

    const btn = document.querySelector('button[onclick="buscarActaEliminar()"]');
    const originalText = btn ? btn.innerHTML : null;
    if (btn) { btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>BUSCANDO...'; btn.disabled = true; }

    const headers = { 'Accept': 'application/json' };
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (csrfMeta) headers['X-CSRF-TOKEN'] = csrfMeta.getAttribute('content');

    fetch(`/api/actas/buscar?criterio=${encodeURIComponent(criterio)}`, {
        method: 'GET',
        credentials: 'same-origin',
        headers
    })
    .then(async res => {
        const txt = await res.text();
        let json = null;
        try { json = JSON.parse(txt); } catch (e) { json = null; }
        if (!res.ok) {
            const msg = (json && json.message) ? json.message : `HTTP ${res.status}`;
            throw new Error(msg);
        }
        return json;
    })
    .then(result => {
        if (!result || !result.success || !result.acta) {
            mostrarNotificacion(result && result.message ? result.message : 'Acta no encontrada', 'info');
            return;
        }

        const acta = result.acta;
        // Guardar id global y dataset
        actaIdEnProceso = acta.id;
        const resultadoEL = document.getElementById('resultado-eliminar');
        if (resultadoEL) resultadoEL.dataset.actaId = acta.id;

        // Rellenar UI
        const numeroEL = document.getElementById('eliminar-numero-acta');
        const fechaEL = document.getElementById('eliminar-fecha-acta');

        if (numeroEL) numeroEL.textContent = acta.numero_acta || acta.numero || 'N/A';
        if (fechaEL) fechaEL.textContent = acta.fecha_registro || acta.fecha || (acta.created_at ? new Date(acta.created_at).toLocaleDateString('es-PE') : 'N/A');

        document.getElementById('resultado-eliminar').style.display = 'block';
        mostrarNotificacion('Acta encontrada', 'success', 2000);
    })
    .catch(err => {
        console.error('Error buscando acta:', err);
        mostrarNotificacion('Error al buscar acta: ' + (err.message || 'Error de conexión'), 'error', 6000);
    })
    .finally(() => {
        if (btn) { btn.innerHTML = originalText; btn.disabled = false; }
    });
}

function confirmarEliminacion() {
       const resultadoEL = document.getElementById('resultado-eliminar');
    if (!resultadoEL || resultadoEL.style.display === 'none') {
        mostrarNotificacion('Primero busque y seleccione el acta a eliminar.', 'warning');
        return;
    }

    const motivoEL = document.getElementById('motivo-eliminacion');
    const obsEL = document.getElementById('observaciones-eliminacion');
    const supervisorEL = document.getElementById('supervisor-autorizante');
    console.log('motivoEL:', motivoEL, 'obsEL:', obsEL, 'supervisorEL:', supervisorEL);

    if (!motivoEL || !obsEL || !supervisorEL) {
        mostrarNotificacion('Error interno: No se encontraron todos los campos requeridos en el formulario de eliminación.', 'error');
        return;
    }

    const motivo = motivoEL.value;
    const observaciones = obsEL.value;
    const supervisor = supervisorEL.value;

    if (!motivo || !supervisor) {
        mostrarNotificacion('Todos los campos son obligatorios para la eliminacion', 'warning');
        return;
    }

    const actaId = actaIdEnProceso || resultadoEL.dataset.actaId;
    if (!actaId) {
        mostrarNotificacion('ID de acta no encontrado. Primero busque la acta a eliminar.', 'warning');
        return;
    }

    if (!confirm('¿Esta seguro de que quiere eliminar esta acta? Esta accion es IRREVERSIBLE')){
        return;
    }

    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrf = csrfMeta ? csrfMeta.getAttribute('content') : '';

    mostrarNotificacion('Eliminando acta...', 'info', 2000);

    console.log('Eliminación -> actaId:', actaId, 'motivo:', motivo, 'supervisor:', supervisor);

    fetch(`/api/actas/${actaId}`, {
        method: 'DELETE',
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf,
            'Content-Type': 'application/json'
        }
    , body: JSON.stringify({ motivo: motivo, observaciones: observaciones, supervisor: supervisor })
    })
    .then(async res => {
        const txt = await res.text();
        let json = null;
        try { json = JSON.parse(txt); } catch (e) { json = null; }
        if (!res.ok) {
            const msg = (json && json.message) ? json.message : `HTTP ${res.status}`;
            throw new Error(msg);
        }
        return json;
    })
    .then(result => {
        mostrarNotificacion(result && result.message ? result.message : 'Acta eliminada exitosamente', 'success');
        cancelarEliminacion();
        if (typeof cargarActas === 'function') setTimeout(cargarActas, 700);
    })
    .catch(err => {
        console.error('Error eliminando acta:', err);
        mostrarNotificacion('Error al eliminar: ' + (err.message || 'Error desconocido'), 'error', 6000);
    });
}

// Mantener referencias a las implementaciones reales en caso de que el layout las sobreescriba
if (typeof buscarActaEliminar === 'function') window.__buscarActaEliminarReal = buscarActaEliminar;
if (typeof confirmarEliminacion === 'function') window.__confirmarEliminacionReal = confirmarEliminacion;

// Función para cancelar edición de acta
function cancelarEdicion() {
    // Ocultar formulario y resultado
    document.getElementById('formulario-edicion').style.display = 'none';
    document.getElementById('resultado-editar').style.display = 'none';
    
    // Limpiar formulario
    document.getElementById('form-editar-acta').reset();
    document.getElementById('buscar-editar').value = '';
    
    mostrarNotificacion('Edición cancelada', 'info');
}

// Función para manejar el envío del formulario de edición
document.addEventListener('DOMContentLoaded', function() {
    const formEditarActa = document.getElementById('form-editar-acta');
    if (formEditarActa) {
        formEditarActa.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const actaId = document.getElementById('acta-id-editar').value;
            if (!actaId) {
                mostrarNotificacion('Error: ID de acta no encontrado', 'error');
                return;
            }
            
            // Recopilar datos del formulario
            const formData = {
                numero_acta: document.getElementById('edit-numero-acta').value,
                fecha: document.getElementById('edit-fecha').value,
                hora: document.getElementById('edit-hora').value,
                empresa: document.getElementById('edit-empresa').value,
                conductor: document.getElementById('edit-conductor').value,
                documento: document.getElementById('edit-documento').value,
                licencia: document.getElementById('edit-licencia').value,
                placa: document.getElementById('edit-placa').value,
                tipo_servicio: document.getElementById('edit-tipo-servicio').value,
                origen_viaje: document.getElementById('edit-origen-viaje').value,
                destino_viaje: document.getElementById('edit-destino-viaje').value,
                ubicacion: document.getElementById('edit-ubicacion').value,
                descripcion_hechos: document.getElementById('edit-descripcion-hechos').value,
                monto_multa: document.getElementById('edit-monto-multa').value,
                estado: document.getElementById('edit-estado').value,
                inspector_responsable: document.getElementById('edit-inspector-responsable').value
            };
            
            // Enviar datos mediante AJAX
            const submitButton = formEditarActa.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>GUARDANDO...';
            submitButton.disabled = true;
            
            fetch(`/actualizar-acta/${actaId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarNotificacion('Acta actualizada exitosamente', 'success');
                    
                    // Cerrar modal después de un breve delay
                    setTimeout(() => {
                        cerrarModal('modal-editar-acta');
                        
                        // Actualizar la tabla si existe
                        if (typeof actualizarTablaActas === 'function') {
                            actualizarTablaActas();
                        }
                    }, 1500);
                    
                } else {
                    mostrarNotificacion(data.message || 'Error al actualizar el acta', 'error');
                }
            })
            .catch(error => {
                console.error('Error al actualizar acta:', error);
                mostrarNotificacion('Error al actualizar el acta. Por favor intente nuevamente.', 'error');
            })
            .finally(() => {
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            });
        });
    }
});

// Función para consulta rápida por documento
async function consultarPorDocumento() {
    const documentoInput = document.getElementById('documento-rapido');
    const documento = documentoInput.value.trim();
    
    if (!documento) {
        mostrarNotificacion('Por favor ingrese un DNI o RUC', 'warning');
        documentoInput.focus();
        return;
    }
    
    // Validar formato básico
    if (documento.length !== 8 && documento.length !== 11) {
        mostrarNotificacion('El DNI debe tener 8 dígitos y el RUC 11 dígitos', 'warning');
        documentoInput.focus();
        return;
    }
    
    // Mostrar indicador de carga
    const btn = document.querySelector('button[onclick="consultarPorDocumento()"]');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>BUSCANDO...';
    btn.disabled = true;
    
    try {
        const response = await fetch(`/api/consultar-actas/${documento}`, {
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            mostrarResultadosConsulta(result);
            
            // Mostrar mensaje de resultado
            if (result.total > 0) {
                mostrarNotificacion(`Se encontraron ${result.total} acta(s) para el documento ${documento}`, 'success');
            } else {
                mostrarNotificacion(`No se encontraron actas para el documento ${documento}`, 'info');
            }
        } else {
            mostrarNotificacion('Error al consultar: ' + result.message, 'error');
        }
        
    } catch (error) {
        console.error('Error:', error);
        mostrarNotificacion('Error de conexión al consultar las actas', 'error');
    } finally {
        // Restaurar botón
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
}

// Función para mostrar/ocultar filtros avanzados
function toggleFiltrosAvanzados() {
    const filtros = document.getElementById('filtros-avanzados');
    const btn = document.querySelector('button[onclick="toggleFiltrosAvanzados()"]');
    
    if (filtros.style.display === 'none') {
        filtros.style.display = 'block';
        btn.innerHTML = '<i class="fas fa-filter me-1"></i>Ocultar filtros avanzados';
    } else {
        filtros.style.display = 'none';
        btn.innerHTML = '<i class="fas fa-filter me-1"></i>Mostrar filtros avanzados';
    }
}

// Modal Consultas con datos reales
async function ejecutarConsulta() {
    const form = document.getElementById('form-consultas');
    const formData = new FormData(form);
    
    // Convertir a objeto para enviar como query parameters
    const params = new URLSearchParams();
    for (let [key, value] of formData.entries()) {
        if (value.trim()) {
            params.append(key, value);
        }
    }
    
    try {
        const response = await fetch(`/api/consultar-actas?${params.toString()}`, {
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            mostrarResultadosConsulta(result);
        } else {
            mostrarNotificacion('Error al consultar: ' + result.message, 'error');
        }
        
    } catch (error) {
        console.error('Error:', error);
        mostrarNotificacion('Error de conexión al consultar las actas', 'error');
    }
}

// Función para mostrar los resultados de la consulta
function mostrarResultadosConsulta(result) {
    // Mostrar resumen
    document.getElementById('resumen-consulta').style.display = 'block';
    
    // Actualizar estadísticas reales
    const total = result.total || 0;
    let procesadas = 0, pendientes = 0, anuladas = 0, registradas = 0;
    
    if (result.actas) {
        result.actas.forEach(acta => {
            switch(acta.estado) {
                case 'procesada': procesadas++; break;
                case 'pendiente': pendientes++; break;
                case 'anulada': anuladas++; break;
                case 'registrada': registradas++; break;
            }
        });
    }
    
    document.getElementById('total-actas').textContent = total;
    document.getElementById('actas-procesadas-modal').textContent = procesadas;
    document.getElementById('actas-pendientes-modal').textContent = pendientes + registradas;
    document.getElementById('actas-anuladas-modal').textContent = anuladas;
    
    // Actualizar tabla con datos reales
    const tbody = document.getElementById('tbody-resultados');
    
    if (total === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center text-muted py-4">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    No se encontraron actas con los criterios especificados
                </td>
            </tr>
        `;
        return;
    }
    
    let tableHTML = '';
    result.actas.forEach(acta => {
        const estadoBadge = getEstadoBadge(acta.estado);
        const fechaFormateada = new Date(acta.fecha).toLocaleDateString('es-PE');
        
        tableHTML += `
            <tr data-acta-id="${acta.id || ''}">
                <td class="fw-bold">${acta.numero_acta}</td>
                <td>${fechaFormateada}</td>
                <td>${acta.empresa || acta.conductor || 'N/A'}</td>
                <td>${acta.documento || 'N/A'}</td>
                <td class="fw-bold">${acta.placa || 'N/A'}</td>
                <td>${(acta.ubicacion || acta.lugar_intervencion) ? ( (acta.ubicacion || acta.lugar_intervencion).substring(0, 30) + '...' ) : 'N/A'}</td>
                <td><span class="badge bg-info">S/ ${acta.monto_multa || '0.00'}</span></td>
                <td>${estadoBadge}</td>
                <td>{{ Auth::user()->name }}</td>
                <td>
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-outline-primary" type="button" onclick="verActa(${acta.id || 'null'})">Ver</button>
                        <button class="btn btn-sm btn-outline-success" type="button" onclick="imprimirActa(${acta.id || 'null'})">Imprimir</button>
                        <button class="btn btn-sm btn-outline-danger" type="button" onclick="descargarActaPDF(${acta.id || 'null'})">PDF</button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = tableHTML;
}

// Función auxiliar para obtener badge de estado
function getEstadoBadge(estado) {
    switch(estado) {
        case 'procesada': return '<span class="badge bg-success">Procesada</span>';
        case 'pendiente': return '<span class="badge bg-warning">Pendiente</span>';
        case 'registrada': return '<span class="badge bg-info">Registrada</span>';
        case 'anulada': return '<span class="badge bg-danger">Anulada</span>';
        default: return '<span class="badge bg-secondary">Sin estado</span>';
    }
}

// Función auxiliar para extraer documento de la descripción
function extractDocumento(acta) {
    // Si tenemos el campo descripción, extraer el RUC/DNI
    if (acta.descripcion) {
        const match = acta.descripcion.match(/RUC\/DNI:\s*(\d+)/);
        return match ? match[1] : null;
    }
    return null;
}

// Función para cargar estadísticas reales desde la base de datos
async function cargarEstadisticasReales() {
    try {
        const response = await fetch('/api/estadisticas-actas', {
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const result = await response.json();
        
        if (result.success && result.estadisticas) {
            // Actualizar los números del resumen
            document.getElementById('total-actas').textContent = result.estadisticas.total;
            document.getElementById('actas-procesadas-modal').textContent = result.estadisticas.procesadas;
            document.getElementById('actas-pendientes-modal').textContent = result.estadisticas.pendientes;
            document.getElementById('actas-anuladas-modal').textContent = result.estadisticas.anuladas;
            
            // Mostrar el resumen si hay datos
            if (result.estadisticas.total > 0) {
                document.getElementById('resumen-consulta').style.display = 'block';
                
                // Si hay actas recientes, llenar la tabla con algunas
                if (result.actas_recientes && result.actas_recientes.length > 0) {
                    const tbody = document.getElementById('tbody-resultados');
                    let tableHTML = '';
                    
                    result.actas_recientes.forEach(acta => {
                        const estadoBadge = getEstadoBadge(acta.estado);
                        const fechaFormateada = acta.fecha ? new Date(acta.fecha).toLocaleDateString('es-PE') : 'N/A';
                        
                        tableHTML += `
                            <tr>
                                <td class="fw-bold">${acta.numero_acta || 'N/A'}</td>
                                <td>${fechaFormateada}</td>
                                <td>N/A</td>
                                <td>N/A</td>
                                <td class="fw-bold">${acta.placa || 'N/A'}</td>
                                <td>${(acta.ubicacion || acta.lugar_intervencion) ? ( (acta.ubicacion || acta.lugar_intervencion).substring(0, 30) + '...' ) : 'N/A'}</td>
                                <td><span class="badge bg-info">S/ ${acta.monto_multa || '0.00'}</span></td>
                                <td>${estadoBadge}</td>
                                <td>{{ Auth::user()->name }}</td>
                            </tr>
                        `;
                    });
                    
                    tbody.innerHTML = tableHTML;
                } else {
                    document.getElementById('tbody-resultados').innerHTML = `
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="fas fa-info-circle me-2"></i>
                                Use la consulta rápida por documento o los filtros avanzados para ver resultados específicos
                            </td>
                        </tr>
                    `;
                }
            }
        } else {
            console.error('Error al cargar estadísticas:', result.message);
        }
        
    } catch (error) {
        console.error('Error de conexión al cargar estadísticas:', error);
    }
}

function exportarExcel() {
    // Exportar los resultados actuales de la tabla #tabla-resultados en formato CSV (Excel)
    try {
        const tbody = document.getElementById('tbody-resultados');
        if (!tbody) throw new Error('Tabla de resultados no encontrada');

        // Recolectar filas visibles con datos
        const rows = Array.from(tbody.querySelectorAll('tr'))
            .filter(r => r.querySelectorAll('td').length >= 6);

        if (rows.length === 0) {
            mostrarNotificacion('No hay resultados visibles para exportar. Realice una consulta primero.', 'warning');
            return;
        }

        // Cabecera CSV
        const headers = ['N° ACTA','FECHA','EMPRESA/CONDUCTOR','RUC/DNI','PLACA','UBICACIÓN','MONTO','ESTADO','INSPECTOR'];
        const data = [headers];

        rows.forEach(row => {
            const cells = Array.from(row.querySelectorAll('td'));
            // Mapear columnas esperadas (intenta tolerar posiciones distintas)
            const numero = cells[0] ? cells[0].textContent.trim() : '';
            const fecha = cells[1] ? cells[1].textContent.trim() : '';
            const empresa = cells[2] ? cells[2].textContent.trim() : '';
            const ruc = cells[3] ? cells[3].textContent.trim() : '';
            const placa = cells[4] ? cells[4].textContent.trim() : '';
            const ubic = cells[5] ? cells[5].textContent.trim() : '';
            const montoRaw = cells[6] ? cells[6].textContent.replace(/[S\/$,\s]/g,'').trim() : '0';
            const estado = cells[7] ? cells[7].textContent.trim() : '';
            const inspector = cells[8] ? cells[8].textContent.trim() : '';

            data.push([numero, fecha, empresa, ruc, placa, ubic, montoRaw, estado, inspector]);
        });

        // Convertir a CSV y descargar
        const csvContent = data.map(row => row.map(cell => '"' + (String(cell || '').replace(/"/g,'""')) + '"').join(',')).join('\n');
        const blob = new Blob(['\ufeff' + csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `Actas_Consulta_${new Date().toISOString().slice(0,10)}.csv`;
        link.click();

        mostrarNotificacion(`✅ Exportación a CSV iniciada (${rows.length} filas)`, 'success', 4000);
    } catch (e) {
        console.error('Error exportando a Excel:', e);
        mostrarNotificacion('Error al exportar a Excel: ' + e.message, 'error', 6000);
    }
}

function exportarPDF() {
    // Generar PDF/HTML de los resultados actuales (descarga por fila o consolidado)
    try {
        const tbody = document.getElementById('tbody-resultados');
        if (!tbody) throw new Error('Tabla de resultados no encontrada');

        const rows = Array.from(tbody.querySelectorAll('tr'))
            .filter(r => r.querySelectorAll('td').length >= 6);

        if (rows.length === 0) {
            mostrarNotificacion('No hay resultados para exportar. Realice una consulta primero.', 'warning');
            return;
        }

        // Si solo hay una fila, generar PDF/HTML para esa acta; si hay varias, generar un listado consolidado en HTML
        if (rows.length === 1) {
            const cells = Array.from(rows[0].querySelectorAll('td'));
            const acta = {
                numero_acta: cells[0] ? cells[0].textContent.trim() : '',
                fecha: cells[1] ? cells[1].textContent.trim() : '',
                empresa: cells[2] ? cells[2].textContent.trim() : '',
                ruc_dni: cells[3] ? cells[3].textContent.trim() : '',
                placa: cells[4] ? cells[4].textContent.trim() : '',
                ubicacion: cells[5] ? cells[5].textContent.trim() : '',
                monto_multa: cells[6] ? cells[6].textContent.replace(/[S\/$,\s]/g,'').trim() : '',
                estado: cells[7] ? cells[7].textContent.trim() : '',
                inspector: cells[8] ? cells[8].textContent.trim() : '{{ Auth::user()->name ?? "Inspector DRTC" }}'
            };

            const html = buildPrintableActaHTML({
                numero_acta: acta.numero_acta,
                anio: new Date().getFullYear(),
                fecha: acta.fecha,
                hora: '',
                ruc_dni: acta.ruc_dni,
                razon_social: acta.empresa,
                placa: acta.placa,
                nombre_conductor: '',
                licencia: '',
                clase_categoria: '',
                lugar_intervencion: acta.ubicacion,
                tipo_servicio: '',
                descripcion_hechos: '',
                monto_multa: acta.monto_multa,
                vencimiento: '',
                inspector: acta.inspector
            });

            const element = document.createElement('a');
            element.href = 'data:text/html;charset=utf-8,' + encodeURIComponent(html);
            const filename = `Acta_${(acta.numero_acta || 'acta')}_${new Date().toISOString().slice(0,10)}.html`;
            element.download = filename;
            element.click();
            mostrarNotificacion('✅ Exportación HTML iniciada para 1 acta', 'success', 4000);
            return;
        }

        // Consolidado: generar tabla HTML con todas las filas
        let tableHtml = '<!doctype html><html><head><meta charset="utf-8"><title>Actas - Exportado</title><style>body{font-family:Arial}table{border-collapse:collapse;width:100%}td,th{border:1px solid #333;padding:6px}</style></head><body>';
        tableHtml += '<h3>Exportación de Actas - ' + new Date().toLocaleDateString('es-PE') + '</h3>';
        tableHtml += '<table><thead><tr>';
        ['N° ACTA','FECHA','EMPRESA/CONDUCTOR','RUC/DNI','PLACA','UBICACIÓN','MONTO','ESTADO','INSPECTOR'].forEach(h=>{tableHtml += '<th>'+h+'</th>'});
        tableHtml += '</tr></thead><tbody>';

        rows.forEach(r => {
            const cells = Array.from(r.querySelectorAll('td'));
            tableHtml += '<tr>';
            for (let i=0;i<9;i++) {
                tableHtml += '<td>' + (cells[i] ? cells[i].textContent.trim() : '') + '</td>';
            }
            tableHtml += '</tr>';
        });

        tableHtml += '</tbody></table></body></html>';

        const link = document.createElement('a');
        link.href = 'data:text/html;charset=utf-8,' + encodeURIComponent(tableHtml);
        link.download = `Actas_Export_${new Date().toISOString().slice(0,10)}.html`;
        link.click();
        mostrarNotificacion(`✅ Exportación HTML iniciada (${rows.length} filas)`, 'success', 4000);

        } catch (e) {
            console.error('Error exportando PDF/HTML:', e);
            mostrarNotificacion('Error al exportar PDF/HTML: ' + e.message, 'error', 6000);
        }
    }

// Helper: convertir una fila de la tabla a objeto acta (intenta tolerar distintas estructuras)
function parseActaRow(row) {
    const cells = Array.from(row.querySelectorAll('td'));
    return {
        numero_acta: cells[0] ? cells[0].textContent.trim() : '',
        fecha: cells[1] ? cells[1].textContent.trim() : '',
        empresa: cells[2] ? cells[2].textContent.trim() : '',
        ruc_dni: cells[3] ? cells[3].textContent.trim() : '',
        placa: cells[4] ? cells[4].textContent.trim() : '',
        ubicacion: cells[5] ? cells[5].textContent.trim() : '',
        monto_multa: cells[6] ? cells[6].textContent.replace(/[S\/$,\s]/g,'').trim() : '',
        estado: cells[7] ? cells[7].textContent.trim() : '',
        inspector: cells[8] ? cells[8].textContent.trim() : ''
    };
}

// Attach click handler to tbody rows to preview acta in printable format
document.addEventListener('click', function(e){
    const tr = e.target.closest && e.target.closest('#tbody-resultados tr');
    if (!tr) return;
    // Ignore clicks on action buttons
    if (e.target.closest('a,button')) return;
    try {
        const acta = parseActaRow(tr);
        if (acta && acta.numero_acta) {
            const html = buildPrintableActaHTML({
                numero_acta: acta.numero_acta,
                anio: new Date().getFullYear(),
                fecha: acta.fecha,
                hora: '',
                ruc_dni: acta.ruc_dni,
                razon_social: acta.empresa,
                placa: acta.placa,
                nombre_conductor: '',
                licencia: '',
                clase_categoria: '',
                lugar_intervencion: acta.ubicacion,
                tipo_servicio: '',
                descripcion_hechos: '',
                monto_multa: acta.monto_multa,
                vencimiento: '',
                inspector: acta.inspector
            });
            const w = window.open('', '_blank');
            w.document.write(html);
            w.document.close();
        }
    } catch (err) {
        // noop
    }
});

// Helpers to view/print/download a single acta by id
async function fetchActaById(id) {
    if (!id) throw new Error('ID de acta inválido');
    try {
        const res = await fetch(`/api/actas/${id}`, { credentials: 'same-origin', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') } });
        if (!res.ok) {
            const txt = await res.text();
            let j = null; try { j = JSON.parse(txt); } catch(_) { }
            throw new Error((j && j.message) ? j.message : `HTTP ${res.status}`);
        }
        const json = await res.json();
        if (!json.success || !json.acta) throw new Error(json.message || 'Acta no encontrada');
        return json.acta;
    } catch (err) {
        console.error('fetchActaById error:', err);
        throw err;
    }
}

async function verActa(id) {
    try {
        const acta = await fetchActaById(id);
        const html = buildPrintableActaHTML({
            numero_acta: acta.numero_acta || acta.numero || '',
            anio: new Date().getFullYear(),
            fecha: acta.fecha_infraccion || acta.fecha || acta.created_at || '',
            hora: acta.hora_infraccion || '',
            ruc_dni: acta.ruc_dni || '',
            razon_social: acta.razon_social || acta.empresa || '',
            placa: acta.placa_vehiculo || acta.placa || '',
            nombre_conductor: acta.nombre_conductor || acta.conductor || '',
            licencia: acta.licencia || '',
            clase_categoria: acta.clase_categoria || '',
            lugar_intervencion: acta.lugar_intervencion || acta.ubicacion || '',
            tipo_servicio: acta.tipo_servicio || '',
            descripcion_hechos: acta.descripcion_hechos || acta.descripcion || '',
            monto_multa: acta.monto_multa || '',
            vencimiento: acta.vencimiento || '',
            inspector: acta.inspector_responsable || acta.inspector || '{{ Auth::user()->name }}'
        });

        const w = window.open('', '_blank');
        w.document.write(html);
        w.document.close();
    } catch (err) {
        mostrarNotificacion('No se pudo cargar la acta: ' + (err.message || ''), 'error');
    }
}

async function imprimirActa(id) {
    try {
        const acta = await fetchActaById(id);
        const html = buildPrintableActaHTML({
            numero_acta: acta.numero_acta || acta.numero || '',
            anio: new Date().getFullYear(),
            fecha: acta.fecha_infraccion || acta.fecha || acta.created_at || '',
            hora: acta.hora_infraccion || '',
            ruc_dni: acta.ruc_dni || '',
            razon_social: acta.razon_social || acta.empresa || '',
            placa: acta.placa_vehiculo || acta.placa || '',
            nombre_conductor: acta.nombre_conductor || acta.conductor || '',
            licencia: acta.licencia || '',
            clase_categoria: acta.clase_categoria || '',
            lugar_intervencion: acta.lugar_intervencion || acta.ubicacion || '',
            tipo_servicio: acta.tipo_servicio || '',
            descripcion_hechos: acta.descripcion_hechos || acta.descripcion || '',
            monto_multa: acta.monto_multa || '',
            vencimiento: acta.vencimiento || '',
            inspector: acta.inspector_responsable || acta.inspector || '{{ Auth::user()->name }}'
        });

        const w = window.open('', '_blank');
        w.document.write(html);
        w.document.close();
        w.focus();
        w.print();
    } catch (err) {
        mostrarNotificacion('No se pudo imprimir la acta: ' + (err.message || ''), 'error');
    }
}

async function descargarActaPDF(id) {
    try {
        const acta = await fetchActaById(id);
        const html = buildPrintableActaHTML({
            numero_acta: acta.numero_acta || acta.numero || '',
            anio: new Date().getFullYear(),
            fecha: acta.fecha_infraccion || acta.fecha || acta.created_at || '',
            hora: acta.hora_infraccion || '',
            ruc_dni: acta.ruc_dni || '',
            razon_social: acta.razon_social || acta.empresa || '',
            placa: acta.placa_vehiculo || acta.placa || '',
            nombre_conductor: acta.nombre_conductor || acta.conductor || '',
            licencia: acta.licencia || '',
            clase_categoria: acta.clase_categoria || '',
            lugar_intervencion: acta.lugar_intervencion || acta.ubicacion || '',
            tipo_servicio: acta.tipo_servicio || '',
            descripcion_hechos: acta.descripcion_hechos || acta.descripcion || '',
            monto_multa: acta.monto_multa || '',
            vencimiento: acta.vencimiento || '',
            inspector: acta.inspector_responsable || acta.inspector || '{{ Auth::user()->name }}'
        });

        // Use html2pdf if available
        if (window.html2pdf) {
            const wrapper = document.createElement('div'); wrapper.innerHTML = html;
            await html2pdf().set({ margin: 10, filename: `Acta_${acta.numero_acta || 'acta'}.pdf`, html2canvas: { scale: 2 }, jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' } }).from(wrapper).save();
            mostrarNotificacion('PDF generado y descargado', 'success');
            return;
        }

        // Fallback: download HTML file
        const a = document.createElement('a');
        a.href = 'data:text/html;charset=utf-8,' + encodeURIComponent(html);
        a.download = `Acta_${acta.numero_acta || 'acta'}.html`;
        a.click();
        mostrarNotificacion('Exportación HTML iniciada (fallback)', 'info');
    } catch (err) {
        mostrarNotificacion('No se pudo descargar la acta: ' + (err.message || ''), 'error');
    }
}

function generarReporte() {
    mostrarNotificacion('Generando reporte estadístico...', 'info', 3000);
    console.log('Generando reporte estadístico');
}

// ESCAPE KEY para cerrar modales
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modalesAbiertos = document.querySelectorAll('.floating-modal[style*="flex"]');
        modalesAbiertos.forEach(modal => {
            cerrarModal(modal.id);
        });
    }
});

// Función para actualizar la ubicación completa
function actualizarLugarCompleto() {
    const lugarSelect = document.getElementById('lugar-select');
    const direccionInput = document.querySelector('input[name="direccion_especifica"]');
    const ubicacionCompleta = document.getElementById('ubicacion-completa');
    
    if (!lugarSelect || !ubicacionCompleta) return;
    
    const lugarSeleccionado = lugarSelect.value; // "Distrito, Provincia"
    const direccion = direccionInput ? direccionInput.value.trim() : '';
    
    let ubicacion = '';
    
    if (direccion) {
        ubicacion += direccion + ', ';
    }
    
    if (lugarSeleccionado) {
        ubicacion += lugarSeleccionado + ', Región Apurímac';
    } else {
        ubicacion = direccion || 'Seleccione ubicación';
    }
    
    ubicacionCompleta.value = ubicacion;
}

// Event listeners para actualizar ubicación
document.addEventListener('DOMContentLoaded', function() {
    const direccionInput = document.querySelector('input[name="direccion_especifica"]');
    
    if (direccionInput) {
        direccionInput.addEventListener('input', actualizarLugarCompleto);
    }
    
    // Verificar si hay un modal que abrir desde URL
    const urlParams = new URLSearchParams(window.location.search);
    const modalToOpen = urlParams.get('modal');
    
    if (modalToOpen) {
        setTimeout(() => {
            // Cerrar cualquier modal abierto primero
            const modalesAbiertos = document.querySelectorAll('.floating-modal.show');
            modalesAbiertos.forEach(modal => {
                modal.classList.remove('show');
            });
            
            // Abrir el modal solicitado con z-index alto
            const modal = document.getElementById(modalToOpen);
            if (modal) {
                modal.style.zIndex = '10500'; // Z-index muy alto para modales desde URL
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
        }, 300);
    }
});

// Wrapper helpers that UI buttons call
function exportarExcel() { exportTableToCSV('#tabla-resultados', `Actas_Consulta_${new Date().toISOString().slice(0,10)}.csv`); }
function exportarPDF() { exportTableToPDF('#tabla-resultados', `Actas_Export_${new Date().toISOString().slice(0,10)}.pdf`); }
</script>

<style>
/* CSS para modales flotantes */
.floating-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    justify-content: center;
    align-items: center;
}

.floating-modal.show {
    display: flex !important;
}
</style>

<script>
// Sistema de gestión de modales mejorado
let modalZIndexBase = 10000;
let modalesAbiertos = [];

function cerrarTodosLosModales() {
    console.log('Cerrando todos los modales');
    const modales = ['modal-consultas', 'modal-eliminar-acta', 'modal-nueva-acta', 'modal-editar-acta'];
    modales.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
            modal.style.zIndex = '';
        }
    });
    modalesAbiertos = [];
    document.body.style.overflow = '';
}

function mostrarModalConsultasSimple() {
    console.log('Abriendo modal de consultas');
    
    // Cerrar otros modales primero
    cerrarTodosLosModales();
    
    const modal = document.getElementById('modal-consultas');
    if (modal) {
        modalZIndexBase += 10;
        modal.style.display = 'flex';
        modal.style.zIndex = modalZIndexBase;
        modal.style.position = 'fixed';
        modal.style.top = '0';
        modal.style.left = '0';
        modal.style.width = '100%';
        modal.style.height = '100%';
        modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
        modal.style.justifyContent = 'center';
        modal.style.alignItems = 'center';
        
        document.body.style.overflow = 'hidden';
        modalesAbiertos.push('modal-consultas');
        
        console.log('Modal de consultas abierto con z-index:', modalZIndexBase);
        
        // Cargar estadísticas si la función existe
        if (typeof cargarEstadisticasReales === 'function') {
            try {
                cargarEstadisticasReales();
            } catch (e) {
                console.log('Error cargando estadísticas:', e);
            }
        }
    } else {
        console.error('Modal modal-consultas no encontrado');
    }
}

function cerrarModalConsultasSimple() {
    console.log('Cerrando modal de consultas');
    const modal = document.getElementById('modal-consultas');
    if (modal) {
        modal.style.display = 'none';
        modal.style.zIndex = '';
        
        // Remover de la lista de modales abiertos
        modalesAbiertos = modalesAbiertos.filter(id => id !== 'modal-consultas');
        
        // Si no hay más modales abiertos, restaurar scroll
        if (modalesAbiertos.length === 0) {
            document.body.style.overflow = '';
        }
    }
}

function mostrarModalEliminarSimple() {
    console.log('Abriendo modal eliminar');
    
    // No cerrar otros modales automáticamente para testing
    
    const modal = document.getElementById('modal-eliminar-acta');
    if (modal) {
        modalZIndexBase += 10;
        modal.style.display = 'flex';
        modal.style.zIndex = modalZIndexBase;
        modal.style.position = 'fixed';
        modal.style.top = '0';
        modal.style.left = '0';
        modal.style.width = '100%';
        modal.style.height = '100%';
        modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
        modal.style.justifyContent = 'center';
        modal.style.alignItems = 'center';
        
        document.body.style.overflow = 'hidden';
        modalesAbiertos.push('modal-eliminar-acta');
        
        console.log('Modal eliminar abierto con z-index:', modalZIndexBase);
    } else {
        console.error('Modal modal-eliminar-acta no encontrado');
    }
}

function cerrarModalEliminarSimple() {
    console.log('Cerrando modal eliminar');
    const modal = document.getElementById('modal-eliminar-acta');
    if (modal) {
        modal.style.display = 'none';
        modal.style.zIndex = '';
        
        // Remover de la lista de modales abiertos
        modalesAbiertos = modalesAbiertos.filter(id => id !== 'modal-eliminar-acta');
        
        // Si no hay más modales abiertos, restaurar scroll
        if (modalesAbiertos.length === 0) {
            document.body.style.overflow = '';
        }
    }
}

// Función de emergencia para debugging
function debugModales() {
    console.log('=== DEBUG MODALES ===');
    console.log('Modales abiertos:', modalesAbiertos);
    console.log('Z-index base:', modalZIndexBase);
    
    const modalConsultas = document.getElementById('modal-consultas');
    const modalEliminar = document.getElementById('modal-eliminar-acta');
    
    console.log('Modal consultas existe:', !!modalConsultas);
    if (modalConsultas) {
        console.log('Consultas display:', modalConsultas.style.display);
        console.log('Consultas z-index:', modalConsultas.style.zIndex);
    }
    
    console.log('Modal eliminar existe:', !!modalEliminar);
    if (modalEliminar) {
        console.log('Eliminar display:', modalEliminar.style.display);
        console.log('Eliminar z-index:', modalEliminar.style.zIndex);
    }
}

// Hacer función disponible globalmente para testing
window.debugModales = debugModales;
window.cerrarTodosLosModales = cerrarTodosLosModales;

// Event listeners para cerrar modales al hacer clic en el overlay
document.addEventListener('DOMContentLoaded', function() {
    // Función para manejar clicks en overlay
    function manejarClickOverlay(event) {
        if (event.target.classList.contains('floating-modal')) {
            const modalId = event.target.id;
            if (modalId === 'modal-consultas') {
                cerrarConsultasDirecto();
            } else if (modalId === 'modal-eliminar-acta') {
                cerrarEliminarDirecto();
            }
        }
    }
    
    // Agregar event listeners a los modales
    const modalConsultas = document.getElementById('modal-consultas');
    const modalEliminar = document.getElementById('modal-eliminar-acta');
    
    if (modalConsultas) {
        modalConsultas.addEventListener('click', manejarClickOverlay);
        
        // Event listener directo para el botón de cerrar
        const btnCerrarConsultas = modalConsultas.querySelector('.close-modal');
        if (btnCerrarConsultas) {
            btnCerrarConsultas.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Botón cerrar consultas clickeado (event listener)');
                cerrarConsultasDirecto();
            });
        }
    }
    
    if (modalEliminar) {
        modalEliminar.addEventListener('click', manejarClickOverlay);
        
        // Event listener directo para el botón de cerrar
        const btnCerrarEliminar = modalEliminar.querySelector('.close-modal');
        if (btnCerrarEliminar) {
            btnCerrarEliminar.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Botón cerrar eliminar clickeado (event listener)');
                cerrarEliminarDirecto();
            });
        }
    }
    
    console.log('Event listeners de modales configurados');
    
    // Event listener para cerrar modales con tecla Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            // Buscar cualquier modal abierto y cerrarlo
            const modalesAbiertos = document.querySelectorAll('.floating-modal');
            modalesAbiertos.forEach(function(modal) {
                if (modal.style.display === 'flex') {
                    console.log('Cerrando modal con Escape:', modal.id);
                    modal.style.display = 'none';
                    modal.style.zIndex = '';
                    modal.classList.remove('d-flex');
                    document.body.style.overflow = '';
                }
            });
        }
    });
});
</script>

@endsection
