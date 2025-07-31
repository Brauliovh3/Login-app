@push('styles')
<style>
/* Prevenir overflow horizontal - GLOBAL */
html, body {
    overflow-x: hidden !important;
    max-width: 100vw !important;
}

/* Contenedores principales */
.container-fluid, .container {
    max-width: 100% !important;
    overflow-x: hidden !important;
    padding-left: 15px !important;
    padding-right: 15px !important;
}

.row {
    margin-left: 0 !important;
    margin-right: 0 !important;
    max-width: 100% !important;
}

[class*="col-"] {
    padding-left: 7.5px !important;
    padding-right: 7.5px !important;
    max-width: 100% !important;
}

/* Cards responsive */
.card {
    margin-bottom: 1rem;
    word-wrap: break-word;
    overflow-wrap: break-word;
    max-width: 100%;
}

.card-body {
    padding: 1rem;
    overflow: hidden;
}

/* Botones responsive */
.btn {
    word-wrap: break-word;
    white-space: normal;
    max-width: 100%;
}

/* Navegación responsive */
.nav-pills .nav-link {
    border-radius: 25px;
    margin: 0 2px;
    font-weight: 600;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.nav-pills .nav-link.active {
    background: linear-gradient(135deg, #007bff, #0056b3);
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
}

/* Cards con gradientes */
.card.bg-gradient {
    border: none;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.card.bg-gradient:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}

/* Media queries para móviles */
@media (max-width: 768px) {
    .d-sm-flex {
        flex-direction: column !important;
        align-items: stretch !important;
    }
    
    .nav-pills {
        flex-direction: column !important;
    }
    
    .nav-pills .nav-item {
        margin-bottom: 5px !important;
        width: 100% !important;
    }
    
    .nav-pills .nav-link {
        text-align: center !important;
        margin: 0 !important;
    }
    
    .flex-shrink-0 {
        margin-top: 1rem !important;
    }
}

@media (max-width: 576px) {
    .container-fluid {
        padding-left: 10px !important;
        padding-right: 10px !important;
    }
    
    [class*="col-"] {
        padding-left: 5px !important;
        padding-right: 5px !important;
    }
}
</style>
@endpush

@extends('layouts.app')

@section('title', 'Panel de Fiscalizador - DRTC Apurímac')

@section('content')
<div class="container-fluid p-3">
    <!-- Header principal -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div class="flex-grow-1">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-search text-success"></i> 
                Panel de Fiscalizador DRTC
            </h1>
            <p class="text-muted">Bienvenido {{ auth()->user()->username }} - Control y Supervisión de Transporte Apurímac</p>
        </div>
        <div class="flex-shrink-0">
            <button class="btn btn-success shadow-sm" onclick="nuevaInspeccion()">
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
                                            <button class="btn btn-warning btn-sm" onclick="crearActa()">
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
                                            <button class="btn btn-info btn-sm" onclick="revisarPendientes()">
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
                                            <button class="btn btn-success btn-sm" onclick="verHistorial()">
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
                                            <button class="btn btn-danger btn-sm" onclick="consultarInfracciones()">
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
                                            <button class="btn btn-primary btn-sm" onclick="iniciarInspeccionVehicular()">Iniciar Inspección</button>
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
                                            <button class="btn btn-success btn-sm" onclick="verificarLicencia()">Verificar Licencia</button>
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
                                            <div class="d-flex justify-content-center gap-2 mt-3 flex-wrap">
                                                <button class="btn btn-primary btn-sm" onclick="generarReportePDF()">
                                                    <i class="fas fa-file-pdf"></i> Generar PDF
                                                </button>
                                                <button class="btn btn-success btn-sm" onclick="exportarExcel()">
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
                    <button class="btn btn-sm btn-block btn-outline-primary" onclick="verListaEmpresas()">
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Funciones del header
function nuevaInspeccion() {
    Swal.fire({
        title: 'Nueva Inspección',
        text: 'Iniciando proceso de nueva inspección...',
        icon: 'info',
        timer: 2000
    });
    // Aquí iría la lógica para crear una nueva inspección
}

function verHistorialCompleto() {
    Swal.fire({
        title: 'Historial Completo',
        text: 'Cargando historial completo de inspecciones...',
        icon: 'info',
        timer: 2000
    });
    // Aquí iría la lógica para mostrar el historial completo
}

// Funciones de la pestaña Actas
function crearActa() {
    Swal.fire({
        title: 'Crear Acta',
        text: 'Abriendo formulario para crear nueva acta...',
        icon: 'info',
        timer: 2000
    });
    // Aquí iría la lógica para crear un acta
}

function revisarPendientes() {
    Swal.fire({
        title: 'Actas Pendientes',
        text: 'Mostrando actas pendientes de revisión...',
        icon: 'warning',
        timer: 2000
    });
    // Aquí iría la lógica para revisar pendientes
}

function verHistorial() {
    Swal.fire({
        title: 'Historial de Actas',
        text: 'Cargando historial de actas...',
        icon: 'info',
        timer: 2000
    });
    // Aquí iría la lógica para ver historial
}

function consultarInfracciones() {
    Swal.fire({
        title: 'Consultar Infracciones',
        text: 'Abriendo consulta de infracciones...',
        icon: 'info',
        timer: 2000
    });
    // Aquí iría la lógica para consultar infracciones
}

// Funciones de la pestaña Control
function iniciarInspeccionVehicular() {
    Swal.fire({
        title: 'Inspección Vehicular',
        text: 'Iniciando proceso de inspección vehicular...',
        icon: 'info',
        timer: 2000
    });
    // Aquí iría la lógica para inspección vehicular
}

function verificarLicencia() {
    Swal.fire({
        title: 'Verificar Licencia',
        text: 'Abriendo verificador de licencias...',
        icon: 'info',
        timer: 2000
    });
    // Aquí iría la lógica para verificar licencias
}

// Funciones de la pestaña Reportes
function generarReportePDF() {
    Swal.fire({
        title: 'Generar Reporte PDF',
        text: 'Generando reporte en formato PDF...',
        icon: 'success',
        timer: 2000
    });
    // Aquí iría la lógica para generar PDF
}

function exportarExcel() {
    Swal.fire({
        title: 'Exportar a Excel',
        text: 'Exportando datos a Excel...',
        icon: 'success',
        timer: 2000
    });
    // Aquí iría la lógica para exportar Excel
}

// Función del panel lateral
function verListaEmpresas() {
    Swal.fire({
        title: 'Lista de Empresas',
        text: 'Cargando lista completa de empresas de transporte...',
        icon: 'info',
        timer: 2000
    });
    // Aquí iría la lógica para ver lista de empresas
}

// Funciones de Revisión
function iniciarRevision() {
    Swal.fire({
        title: 'Iniciar Revisión',
        text: 'Iniciando proceso de revisión de documentos...',
        icon: 'success',
        timer: 2000
    });
}

function verCasosPendientes() {
    Swal.fire({
        title: 'Casos Pendientes',
        text: 'Mostrando casos que requieren atención inmediata...',
        icon: 'warning',
        timer: 2000
    });
}

// Funciones de Auditoría
function crearAuditoria() {
    Swal.fire({
        title: 'Crear Auditoría',
        text: 'Abriendo formulario para nueva auditoría...',
        icon: 'info',
        timer: 2000
    });
}

function verHistorialAuditorias() {
    Swal.fire({
        title: 'Historial de Auditorías',
        text: 'Consultando auditorías anteriores...',
        icon: 'info',
        timer: 2000
    });
}

// Funciones de Informes
function generarInforme() {
    Swal.fire({
        title: 'Generar Informe',
        text: 'Creando informe personalizado...',
        icon: 'success',
        timer: 2000
    });
}

function exportarDatos() {
    Swal.fire({
        title: 'Exportar Datos',
        text: 'Exportando datos en formato seleccionado...',
        icon: 'success',
        timer: 2000
    });
}
</script>
@endpush
