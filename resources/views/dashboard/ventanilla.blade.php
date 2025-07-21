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
    <div class="col-xl-12 col-lg-12">
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

</div>
@endsection
