@extends('layouts.dashboard')

@section('title', 'Panel de Fiscalizador')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="fas fa-search text-success"></i> Panel de Fiscalizador</h1>
                <p class="text-muted">Bienvenido {{ auth()->user()->username }} - Supervisión y Control de Calidad</p>
            </div>
            <div>
                <button class="btn btn-success me-2"><i class="fas fa-check-double"></i> Nueva Auditoría</button>
                <button class="btn btn-outline-primary"><i class="fas fa-file-export"></i> Exportar Informe</button>
            </div>
        </div>
    </div>
</div>

<!-- Estadísticas de Fiscalización -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card bg-success text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $stats['pending_reviews'] ?? 12 }}</h4>
                        <p class="mb-0">Revisiones Pendientes</p>
                    </div>
                    <div>
                        <i class="fas fa-clock fa-3x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="#">Revisar Ahora</a>
                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-primary text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $stats['completed_reviews'] ?? 45 }}</h4>
                        <p class="mb-0">Revisiones Completadas</p>
                    </div>
                    <div>
                        <i class="fas fa-check-circle fa-3x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="#">Ver Historial</a>
                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-warning text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>8</h4>
                        <p class="mb-0">Casos Críticos</p>
                    </div>
                    <div>
                        <i class="fas fa-exclamation-triangle fa-3x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="#">Revisar Urgente</a>
                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-info text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>95%</h4>
                        <p class="mb-0">Eficiencia</p>
                    </div>
                    <div>
                        <i class="fas fa-chart-line fa-3x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="#">Ver Métricas</a>
                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Panel de Fiscalización -->
<div class="row">
    <div class="col-xl-8 col-lg-7">
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
    
    <!-- Panel de actividad -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-success"><i class="fas fa-bell"></i> Notificaciones</h6>
                <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-success">Ver Todas</a>
            </div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                @if($notifications->count() > 0)
                    @foreach($notifications as $notification)
                        <div class="media mb-3">
                            <div class="media-object mr-3">
                                @switch($notification->type)
                                    @case('success')
                                        <i class="fas fa-check-circle text-success fa-2x"></i>
                                        @break
                                    @case('error')
                                        <i class="fas fa-exclamation-circle text-danger fa-2x"></i>
                                        @break
                                    @case('warning')
                                        <i class="fas fa-exclamation-triangle text-warning fa-2x"></i>
                                        @break
                                    @default
                                        <i class="fas fa-info-circle text-info fa-2x"></i>
                                @endswitch
                            </div>
                            <div class="media-body">
                                <h6 class="mt-0">{{ $notification->title }}</h6>
                                <p class="text-muted small mb-1">{{ $notification->message }}</p>
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        @if(!$loop->last)<hr>@endif
                    @endforeach
                @else
                    <div class="text-center text-muted">
                        <i class="fas fa-bell-slash fa-3x mb-3"></i>
                        <p>No hay notificaciones</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Alertas de trabajo -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-warning"><i class="fas fa-exclamation-triangle"></i> Alertas de Trabajo</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-warning" role="alert">
                    <i class="fas fa-clock"></i> <strong>Recordatorio:</strong> Tienes 5 documentos pendientes de revisión con vencimiento hoy.
                </div>
                <div class="alert alert-info" role="alert">
                    <i class="fas fa-info-circle"></i> <strong>Información:</strong> Nueva auditoría programada para mañana.
                </div>
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check"></i> <strong>Completado:</strong> Informe mensual generado correctamente.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
