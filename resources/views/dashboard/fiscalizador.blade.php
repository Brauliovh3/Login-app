@extends('layouts.app')

@section('title', 'Panel de Fiscalizador - DRTC Apurímac')

@section('content')
<style>
    .nav-pills .nav-link {
        border-radius: 25px;
        margin: 0 2px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #007bff, #0056b3);
        box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
    }
    
    .card.bg-gradient {
        border: none;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .card.bg-gradient:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }
    
    .gap-2 {
        gap: 0.5rem;
    }
</style>

<div class="container-fluid">
    <!-- Header principal -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-search text-success"></i> 
                Panel de Fiscalizador DRTC
            </h1>
            <p class="text-muted">Bienvenido {{ auth()->user()->username }} - Control y Supervisión de Transporte Apurímac</p>
        </div>
        <div>
            <button class="btn btn-success shadow-sm">
                <i class="fas fa-clipboard-check fa-sm text-white-50"></i> Nueva Inspección
            </button>
        </div>
    </div>

    <!-- Estadísticas de control -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-gradient" style="background: linear-gradient(135deg, #ff8c00, #ff7700); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="h3 mb-0 font-weight-bold">{{ $stats['actas_registradas'] }}</div>
                            <div class="font-weight-bold text-uppercase">ACTAS REGISTRADAS</div>
                            <small class="opacity-75">Hoy</small>
                        </div>
                        <div>
                            <i class="fas fa-file-alt fa-3x" style="opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-gradient" style="background: linear-gradient(135deg, #28a745, #20c997); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="h3 mb-0 font-weight-bold">{{ $stats['procesadas'] }}</div>
                            <div class="font-weight-bold text-uppercase">PROCESADAS</div>
                            <small class="opacity-75">Completadas</small>
                        </div>
                        <div>
                            <i class="fas fa-check-circle fa-3x" style="opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-gradient" style="background: linear-gradient(135deg, #ffc107, #fd7e14); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="h3 mb-0 font-weight-bold">{{ $stats['pendientes'] }}</div>
                            <div class="font-weight-bold text-uppercase">PENDIENTES</div>
                            <small class="opacity-75">Por revisar</small>
                        </div>
                        <div>
                            <i class="fas fa-clock fa-3x" style="opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-gradient" style="background: linear-gradient(135deg, #dc3545, #e83e8c); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="h3 mb-0 font-weight-bold">{{ $stats['total_infracciones'] }}</div>
                            <div class="font-weight-bold text-uppercase">INFRACCIONES</div>
                            <small class="opacity-75">Detectadas</small>
                        </div>
                        <div>
                            <i class="fas fa-exclamation-triangle fa-3x" style="opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel principal de fiscalización -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clipboard-list"></i> Control de Fiscalización DRTC
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Tabs de navegación mejorados -->
                    <ul class="nav nav-pills nav-fill mb-4" id="fiscalizacionTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="actas-tab" data-bs-toggle="pill" data-bs-target="#actas" type="button" role="tab">
                                <i class="fas fa-file-alt"></i> Actas de Infracción
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="control-tab" data-bs-toggle="pill" data-bs-target="#control" type="button" role="tab">
                                <i class="fas fa-search"></i> Control Vehicular
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="reportes-tab" data-bs-toggle="pill" data-bs-target="#reportes" type="button" role="tab">
                                <i class="fas fa-chart-bar"></i> Reportes
                            </button>
                        </li>
                    </ul>

                    <!-- Contenido de tabs -->
                    <div class="tab-content" id="fiscalizacionTabsContent">
                        <!-- Tab Actas -->
                        <div class="tab-pane fade show active" id="actas" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body text-center">
                                            <div class="h4 text-warning mb-2">
                                                <i class="fas fa-plus-circle"></i>
                                            </div>
                                            <h6 class="font-weight-bold">Nueva Acta de Infracción</h6>
                                            <p class="text-muted small">Registrar nueva infracción detectada</p>
                                            <button class="btn btn-warning btn-sm">
                                                <i class="fas fa-plus"></i> Crear Acta
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body text-center">
                                            <div class="h4 text-info mb-2">
                                                <i class="fas fa-list"></i>
                                            </div>
                                            <h6 class="font-weight-bold">Revisar Actas Pendientes</h6>
                                            <p class="text-muted small">{{ $stats['pendientes'] }} actas por procesar</p>
                                            <button class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i> Revisar
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body text-center">
                                            <div class="h4 text-success mb-2">
                                                <i class="fas fa-check-double"></i>
                                            </div>
                                            <h6 class="font-weight-bold">Actas Procesadas</h6>
                                            <p class="text-muted small">{{ $stats['procesadas'] }} completadas hoy</p>
                                            <button class="btn btn-success btn-sm">
                                                <i class="fas fa-history"></i> Ver Historial
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body text-center">
                                            <div class="h4 text-danger mb-2">
                                                <i class="fas fa-exclamation-triangle"></i>
                                            </div>
                                            <h6 class="font-weight-bold">Infracciones Registradas</h6>
                                            <p class="text-muted small">{{ $stats['total_infracciones'] }} tipos en sistema</p>
                                            <button class="btn btn-danger btn-sm">
                                                <i class="fas fa-book"></i> Consultar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Control -->
                        <div class="tab-pane fade" id="control" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card border-left-primary shadow h-100 py-2">
                                        <div class="card-body">
                                            <h6 class="text-primary font-weight-bold mb-2">
                                                <i class="fas fa-bus"></i> Inspección Vehicular
                                            </h6>
                                            <p class="text-muted small mb-3">Control técnico y documentario de vehículos</p>
                                            <div class="small text-muted mb-2">{{ $stats['vehiculos_activos'] }} vehículos activos</div>
                                            <button class="btn btn-primary btn-sm">Iniciar Inspección</button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="card border-left-success shadow h-100 py-2">
                                        <div class="card-body">
                                            <h6 class="text-success font-weight-bold mb-2">
                                                <i class="fas fa-id-card"></i> Verificación de Licencias
                                            </h6>
                                            <p class="text-muted small mb-3">Control de habilitaciones de conductores</p>
                                            <div class="small text-muted mb-2">{{ $stats['conductores_vigentes'] }} licencias vigentes</div>
                                            <button class="btn btn-success btn-sm">Verificar Licencia</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Reportes -->
                        <div class="tab-pane fade" id="reportes" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body">
                                            <div class="h4 text-primary mb-3">
                                                <i class="fas fa-chart-line"></i>
                                            </div>
                                            <h5 class="font-weight-bold">Reportes de Fiscalización</h5>
                                            <p class="text-muted">Generar informes detallados de actividades de control</p>
                                            <div class="d-flex justify-content-center gap-2 mt-3">
                                                <button class="btn btn-primary btn-sm">
                                                    <i class="fas fa-file-pdf"></i> Generar PDF
                                                </button>
                                                <button class="btn btn-success btn-sm">
                                                    <i class="fas fa-file-excel"></i> Exportar Excel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de control de empresas -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-building"></i> Control de Empresas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="h4 mb-2 font-weight-bold text-primary">{{ $stats['empresas_registradas'] }}</div>
                        <p class="text-muted">Empresas de transporte registradas</p>
                    </div>
                    <hr>
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                        Vehículos en Mantenimiento
                    </div>
                    <div class="h6 mb-2 text-gray-800">{{ $stats['vehiculos_inspeccion'] ?? 0 }} unidades</div>
                    <div class="progress mb-3">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 25%"></div>
                    </div>
                    <button class="btn btn-sm btn-block btn-outline-primary">
                        <i class="fas fa-list"></i> Ver Lista Completa
                    </button>
                </div>
            </div>

            <!-- Notificaciones recientes -->
            @if($notifications && $notifications->count() > 0)
            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bell"></i> Notificaciones Recientes
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($notifications->take(3) as $notification)
                    <div class="media mb-3">
                        <div class="mr-3">
                            <div class="icon-circle bg-warning">
                                <i class="fas fa-exclamation-triangle text-white"></i>
                            </div>
                        </div>
                        <div class="media-body">
                            <div class="small text-gray-500">{{ $notification->created_at->diffForHumans() }}</div>
                            <strong>{{ $notification->title }}</strong>
                            <p class="mb-0 small">{{ Str::limit($notification->message, 50) }}</p>
                        </div>
                    </div>
                    @endforeach
                    <div class="text-center">
                        <a class="small" href="#">Ver todas las notificaciones</a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
                <a class="small text-white stretched-link" href="#">Ver Métricas</a>
                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Panel de Fiscalización -->
<div class="row">
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-success"><i class="fas fa-tasks"></i> Herramientas de Fiscalización</h6>
            </div>
            <div class="card-body">
                <!-- Tabs de navegación -->
                <ul class="nav nav-tabs" id="fiscalizadorTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="revision-tab" data-bs-toggle="tab" data-bs-target="#revision" type="button" role="tab">
                            <i class="fas fa-search"></i> Revisiones
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="auditoria-tab" data-bs-toggle="tab" data-bs-target="#auditoria" type="button" role="tab">
                            <i class="fas fa-clipboard-check"></i> Auditorías
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="informes-tab" data-bs-toggle="tab" data-bs-target="#informes" type="button" role="tab">
                            <i class="fas fa-file-alt"></i> Informes
                        </button>
                    </li>
                </ul>
                
                <!-- Contenido de tabs -->
                <div class="tab-content mt-3" id="fiscalizadorTabsContent">
                    <div class="tab-pane fade show active" id="revision" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card border-left-success shadow h-100 py-2">
                                    <div class="card-body">
                                        <h6 class="text-success font-weight-bold mb-2">
                                            <i class="fas fa-search"></i> Revisar Documentos
                                        </h6>
                                        <p class="text-muted small mb-3">Revisar y validar documentos enviados por usuarios de ventanilla</p>
                                        <button class="btn btn-success btn-sm">Iniciar Revisión</button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="card border-left-warning shadow h-100 py-2">
                                    <div class="card-body">
                                        <h6 class="text-warning font-weight-bold mb-2">
                                            <i class="fas fa-exclamation-circle"></i> Casos Pendientes
                                        </h6>
                                        <p class="text-muted small mb-3">Casos que requieren atención inmediata o seguimiento especial</p>
                                        <button class="btn btn-warning btn-sm">Ver Casos</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="auditoria" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card border-left-primary shadow h-100 py-2">
                                    <div class="card-body">
                                        <h6 class="text-primary font-weight-bold mb-2">
                                            <i class="fas fa-clipboard-list"></i> Nueva Auditoría
                                        </h6>
                                        <p class="text-muted small mb-3">Crear y programar nuevas auditorías del sistema</p>
                                        <button class="btn btn-primary btn-sm">Crear Auditoría</button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="card border-left-info shadow h-100 py-2">
                                    <div class="card-body">
                                        <h6 class="text-info font-weight-bold mb-2">
                                            <i class="fas fa-history"></i> Historial de Auditorías
                                        </h6>
                                        <p class="text-muted small mb-3">Consultar auditorías anteriores y sus resultados</p>
                                        <button class="btn btn-info btn-sm">Ver Historial</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="informes" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card border-left-secondary shadow h-100 py-2">
                                    <div class="card-body">
                                        <h6 class="text-secondary font-weight-bold mb-2">
                                            <i class="fas fa-chart-bar"></i> Generar Informe
                                        </h6>
                                        <p class="text-muted small mb-3">Crear informes personalizados con métricas específicas</p>
                                        <button class="btn btn-secondary btn-sm">Generar</button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="card border-left-dark shadow h-100 py-2">
                                    <div class="card-body">
                                        <h6 class="text-dark font-weight-bold mb-2">
                                            <i class="fas fa-download"></i> Exportar Datos
                                        </h6>
                                        <p class="text-muted small mb-3">Exportar datos en diferentes formatos (PDF, Excel, CSV)</p>
                                        <button class="btn btn-dark btn-sm">Exportar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
