@extends('layouts.app')

@section('title', 'Panel de Ventanilla')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="fas fa-desktop text-info"></i> Panel de Ventanilla</h1>
                <p class="text-muted">Bienvenido {{ auth()->user()->username }} - Atención al Cliente y Trámites</p>
            </div>
            <div>
                <button class="btn btn-info me-2"><i class="fas fa-plus"></i> Nuevo Trámite</button>
                <button class="btn btn-outline-success"><i class="fas fa-print"></i> Imprimir Tickets</button>
            </div>
        </div>
    </div>
</div>

<!-- Estadísticas de Ventanilla -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card bg-info text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $stats['pending_tasks'] ?? 23 }}</h4>
                        <p class="mb-0">Trámites Pendientes</p>
                    </div>
                    <div>
                        <i class="fas fa-clock fa-3x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="#">Atender Ahora</a>
                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-success text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $stats['completed_tasks'] ?? 87 }}</h4>
                        <p class="mb-0">Trámites Completados</p>
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
                        <h4>15</h4>
                        <p class="mb-0">Clientes en Espera</p>
                    </div>
                    <div>
                        <i class="fas fa-users fa-3x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="#">Gestionar Cola</a>
                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-primary text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>98%</h4>
                        <p class="mb-0">Satisfacción</p>
                    </div>
                    <div>
                        <i class="fas fa-smile fa-3x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="#">Ver Evaluaciones</a>
                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Panel de Operaciones -->
<div class="row">
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-tools"></i> Herramientas de Ventanilla</h6>
            </div>
            <div class="card-body">
                <!-- Tabs de navegación -->
                <ul class="nav nav-tabs" id="ventanillaTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tramites-tab" data-bs-toggle="tab" data-bs-target="#tramites" type="button" role="tab">
                            <i class="fas fa-file-alt"></i> Trámites
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="clientes-tab" data-bs-toggle="tab" data-bs-target="#clientes" type="button" role="tab">
                            <i class="fas fa-users"></i> Clientes
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="documentos-tab" data-bs-toggle="tab" data-bs-target="#documentos" type="button" role="tab">
                            <i class="fas fa-folder"></i> Documentos
                        </button>
                    </li>
                </ul>
                
                <!-- Contenido de tabs -->
                <div class="tab-content mt-3" id="ventanillaTabsContent">
                    <div class="tab-pane fade show active" id="tramites" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card border-left-info shadow h-100 py-2">
                                    <div class="card-body">
                                        <h6 class="text-info font-weight-bold mb-2">
                                            <i class="fas fa-plus-circle"></i> Iniciar Nuevo Trámite
                                        </h6>
                                        <p class="text-muted small mb-3">Crear un nuevo trámite para un cliente</p>
                                        <button class="btn btn-info btn-sm">Nuevo Trámite</button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="card border-left-success shadow h-100 py-2">
                                    <div class="card-body">
                                        <h6 class="text-success font-weight-bold mb-2">
                                            <i class="fas fa-search"></i> Buscar Trámite
                                        </h6>
                                        <p class="text-muted small mb-3">Buscar y gestionar trámites existentes</p>
                                        <button class="btn btn-success btn-sm">Buscar</button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card border-left-warning shadow h-100 py-2">
                                    <div class="card-body">
                                        <h6 class="text-warning font-weight-bold mb-2">
                                            <i class="fas fa-clock"></i> Trámites Urgentes
                                        </h6>
                                        <p class="text-muted small mb-3">Trámites que requieren atención inmediata</p>
                                        <button class="btn btn-warning btn-sm">Ver Urgentes</button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card border-left-primary shadow h-100 py-2">
                                    <div class="card-body">
                                        <h6 class="text-primary font-weight-bold mb-2">
                                            <i class="fas fa-check-double"></i> Finalizar Trámite
                                        </h6>
                                        <p class="text-muted small mb-3">Completar y cerrar trámites en proceso</p>
                                        <button class="btn btn-primary btn-sm">Finalizar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="clientes" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card border-left-primary shadow h-100 py-2">
                                    <div class="card-body">
                                        <h6 class="text-primary font-weight-bold mb-2">
                                            <i class="fas fa-user-plus"></i> Registrar Cliente
                                        </h6>
                                        <p class="text-muted small mb-3">Agregar un nuevo cliente al sistema</p>
                                        <button class="btn btn-primary btn-sm">Registrar</button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="card border-left-info shadow h-100 py-2">
                                    <div class="card-body">
                                        <h6 class="text-info font-weight-bold mb-2">
                                            <i class="fas fa-address-book"></i> Consultar Cliente
                                        </h6>
                                        <p class="text-muted small mb-3">Buscar información de clientes registrados</p>
                                        <button class="btn btn-info btn-sm">Consultar</button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card border-left-warning shadow h-100 py-2">
                                    <div class="card-body">
                                        <h6 class="text-warning font-weight-bold mb-2">
                                            <i class="fas fa-user-edit"></i> Actualizar Datos
                                        </h6>
                                        <p class="text-muted small mb-3">Modificar información de clientes existentes</p>
                                        <button class="btn btn-warning btn-sm">Actualizar</button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card border-left-success shadow h-100 py-2">
                                    <div class="card-body">
                                        <h6 class="text-success font-weight-bold mb-2">
                                            <i class="fas fa-history"></i> Historial Cliente
                                        </h6>
                                        <p class="text-muted small mb-3">Ver historial de trámites de un cliente</p>
                                        <button class="btn btn-success btn-sm">Ver Historial</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="documentos" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card border-left-secondary shadow h-100 py-2">
                                    <div class="card-body">
                                        <h6 class="text-secondary font-weight-bold mb-2">
                                            <i class="fas fa-upload"></i> Subir Documentos
                                        </h6>
                                        <p class="text-muted small mb-3">Digitalizar y subir documentos del cliente</p>
                                        <button class="btn btn-secondary btn-sm">Subir</button>
                                    </div>
                                </div>
                            </div>
                        
                            <div class="col-md-6 mb-3">
                                <div class="card border-left-dark shadow h-100 py-2">
                                    <div class="card-body">
                                        <h6 class="text-dark font-weight-bold mb-2">
                                            <i class="fas fa-print"></i> Imprimir Documentos
                                        </h6>
                                        <p class="text-muted small mb-3">Generar e imprimir documentos oficiales</p>
                                        <button class="btn btn-dark btn-sm">Imprimir</button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card border-left-info shadow h-100 py-2">
                                    <div class="card-body">
                                        <h6 class="text-info font-weight-bold mb-2">
                                            <i class="fas fa-archive"></i> Archivar Documentos
                                        </h6>
                                        <p class="text-muted small mb-3">Organizar y archivar documentos procesados</p>
                                        <button class="btn btn-info btn-sm">Archivar</button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card border-left-success shadow h-100 py-2">
                                    <div class="card-body">
                                        <h6 class="text-success font-weight-bold mb-2">
                                            <i class="fas fa-share"></i> Enviar a Fiscalización
                                        </h6>
                                        <p class="text-muted small mb-3">Enviar documentos para revisión y aprobación</p>
                                        <button class="btn btn-success btn-sm">Enviar</button>
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
                <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-bell"></i> Notificaciones</h6>
                <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-info">Ver Todas</a>
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

        <!-- Cola de atención -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-warning"><i class="fas fa-users"></i> Cola de Atención</h6>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Cliente #001</strong><br>
                            <small class="text-muted">Trámite: Renovación de licencia</small>
                        </div>
                        <span class="badge bg-warning rounded-pill">Espera: 15 min</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Cliente #002</strong><br>
                            <small class="text-muted">Trámite: Cambio de domicilio</small>
                        </div>
                        <span class="badge bg-info rounded-pill">Espera: 8 min</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Cliente #003</strong><br>
                            <small class="text-muted">Trámite: Consulta general</small>
                        </div>
                        <span class="badge bg-success rounded-pill">Espera: 3 min</span>
                    </div>
                </div>
                <div class="mt-3">
                    <button class="btn btn-primary btn-sm me-2">Llamar Siguiente</button>
                    <button class="btn btn-outline-secondary btn-sm">Ver Cola Completa</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
