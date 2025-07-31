@extends('layouts.app')

@section('title', 'Panel de Ventanilla - DRTC Apurímac')

@section('content')
<div class="container-fluid">
    <!-- Header principal -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-desktop text-info"></i> 
                Panel de Ventanilla DRTC
            </h1>
            <p class="text-muted">Bienvenido {{ auth()->user()->username }} - Atención y Trámites Documentarios Apurímac</p>
        </div>
        <div>
            <button class="btn btn-info shadow-sm">
                <i class="fas fa-file-alt fa-sm text-white-50"></i> Nuevo Trámite
            </button>
        </div>
    </div>

    <!-- Estadísticas de ventanilla -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Empresas Registradas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['empresas_activas'] }}</div>
                            <div class="text-xs text-primary">En estado activo</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Vehículos en Sistema
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_vehiculos'] }}</div>
                            <div class="text-xs text-success">Total registrados</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bus fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Licencias Vigentes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['licencias_vigentes'] }}</div>
                            <div class="text-xs text-info">de {{ $stats['conductores_registrados'] }} conductores</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-id-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Trámites Pendientes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['solicitudes_pendientes'] }}</div>
                            <div class="text-xs text-warning">Por procesar</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel principal de trámites -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-alt"></i> Servicios de Ventanilla
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Registro de Empresas
                                            </div>
                                            <div class="text-gray-800">
                                                <small class="text-muted d-block mb-2">Alta y modificación de empresas</small>
                                                <button class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-building"></i> Gestionar
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-building fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Registro Vehicular
                                            </div>
                                            <div class="text-gray-800">
                                                <small class="text-muted d-block mb-2">Altas, bajas y modificaciones</small>
                                                <button class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-bus"></i> Registrar
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-bus fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Licencias de Conducir
                                            </div>
                                            <div class="text-gray-800">
                                                <small class="text-muted d-block mb-2">Expedición y renovación</small>
                                                <button class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-id-card"></i> Tramitar
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-id-card fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Certificaciones
                                            </div>
                                            <div class="text-gray-800">
                                                <small class="text-muted d-block mb-2">Emisión de certificados</small>
                                                <button class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-certificate"></i> Certificar
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-certificate fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de estado de licencias -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-id-badge"></i> Estado de Licencias
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <div class="text-center">
                            <div class="h5 mb-2 font-weight-bold text-success">{{ $stats['licencias_vigentes'] }}</div>
                            <p class="text-muted">Licencias Vigentes</p>
                        </div>
                        <hr>
                        <div class="text-center">
                            <div class="h6 mb-2 font-weight-bold text-danger">{{ $stats['licencias_vencidas'] }}</div>
                            <p class="text-muted">Licencias Vencidas</p>
                        </div>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Vigentes
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-danger"></i> Vencidas
                        </span>
                    </div>
                    <button class="btn btn-sm btn-block btn-outline-primary mt-3">
                        <i class="fas fa-list"></i> Ver Detalle Completo
                    </button>
                </div>
            </div>

            <!-- Accesos rápidos -->
            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt"></i> Accesos Rápidos
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-search text-primary"></i> Buscar Empresa
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-car text-success"></i> Consultar Vehículo
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-user text-info"></i> Verificar Conductor
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-print text-warning"></i> Imprimir Formatos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

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
