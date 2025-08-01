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
        title: 'Crear Nueva Acta',
        html: `
            <form id="actaForm" class="text-left">
                <div class="mb-3">
                    <label>Vehículo (Placa):</label>
                    <select id="vehiculo_id" class="form-control" required>
                        <option value="">Seleccionar vehículo...</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Conductor:</label>
                    <select id="conductor_id" class="form-control" required>
                        <option value="">Seleccionar conductor...</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Infracción:</label>
                    <select id="infraccion_id" class="form-control" required>
                        <option value="">Seleccionar infracción...</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Inspector:</label>
                    <select id="inspector_id" class="form-control" required>
                        <option value="">Seleccionar inspector...</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Ubicación:</label>
                    <input type="text" id="ubicacion" class="form-control" required placeholder="Ubicación de la infracción">
                </div>
                <div class="mb-3">
                    <label>Descripción:</label>
                    <textarea id="descripcion" class="form-control" required placeholder="Descripción detallada"></textarea>
                </div>
                <div class="mb-3">
                    <label>Monto de Multa (S/):</label>
                    <input type="number" id="monto_multa" class="form-control" required min="0" step="0.01">
                </div>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Crear Acta',
        cancelButtonText: 'Cancelar',
        width: 600,
        didOpen: () => {
            // Cargar datos para los selects
            cargarDatosActa();
        },
        preConfirm: () => {
            const form = document.getElementById('actaForm');
            const formData = new FormData(form);
            
            return fetch('/api/actas', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    vehiculo_id: document.getElementById('vehiculo_id').value,
                    conductor_id: document.getElementById('conductor_id').value,
                    infraccion_id: document.getElementById('infraccion_id').value,
                    inspector_id: document.getElementById('inspector_id').value,
                    ubicacion: document.getElementById('ubicacion').value,
                    descripcion: document.getElementById('descripcion').value,
                    monto_multa: document.getElementById('monto_multa').value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    return data;
                } else {
                    throw new Error(data.message || 'Error al crear el acta');
                }
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire('¡Éxito!', 'Acta creada exitosamente', 'success');
            // Recargar estadísticas
            setTimeout(() => location.reload(), 1500);
        }
    }).catch((error) => {
        Swal.fire('Error', error.message, 'error');
    });
}

function cargarDatosActa() {
    // Cargar vehículos
    fetch('/api/vehiculos-activos')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('vehiculo_id');
            data.vehiculos.forEach(vehiculo => {
                const option = document.createElement('option');
                option.value = vehiculo.id;
                option.textContent = `${vehiculo.placa} - ${vehiculo.modelo}`;
                select.appendChild(option);
            });
        });

    // Cargar conductores
    fetch('/api/conductores-vigentes')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('conductor_id');
            data.conductores.forEach(conductor => {
                const option = document.createElement('option');
                option.value = conductor.id;
                option.textContent = `${conductor.nombre} - ${conductor.licencia}`;
                select.appendChild(option);
            });
        });

    // Cargar infracciones
    fetch('/api/infracciones')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('infraccion_id');
            data.infracciones.forEach(infraccion => {
                const option = document.createElement('option');
                option.value = infraccion.id;
                option.textContent = `${infraccion.codigo} - ${infraccion.descripcion}`;
                select.appendChild(option);
            });
        });

    // Cargar inspectores
    fetch('/api/inspectores-activos')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('inspector_id');
            data.inspectores.forEach(inspector => {
                const option = document.createElement('option');
                option.value = inspector.id;
                option.textContent = inspector.nombre;
                select.appendChild(option);
            });
        });
}

function revisarPendientes() {
    fetch('/api/actas/pendientes')
        .then(response => response.json())
        .then(data => {
            if (data.actas.length === 0) {
                Swal.fire('Sin Pendientes', 'No hay actas pendientes de revisión', 'info');
                return;
            }

            let html = '<div class="table-responsive"><table class="table table-sm">';
            html += '<thead><tr><th>Número</th><th>Placa</th><th>Conductor</th><th>Infracción</th><th>Acciones</th></tr></thead><tbody>';
            
            data.actas.forEach(acta => {
                html += `<tr>
                    <td>${acta.numero_acta}</td>
                    <td>${acta.placa}</td>
                    <td>${acta.conductor_nombre}</td>
                    <td>${acta.infraccion_descripcion}</td>
                    <td>
                        <button class="btn btn-sm btn-success" onclick="procesarActa(${acta.id})">Procesar</button>
                        <button class="btn btn-sm btn-info" onclick="verDetalleActa(${acta.id})">Ver</button>
                    </td>
                </tr>`;
            });
            
            html += '</tbody></table></div>';

            Swal.fire({
                title: 'Actas Pendientes',
                html: html,
                width: 800,
                showCloseButton: true,
                showConfirmButton: false
            });
        });
}

function procesarActa(actaId) {
    Swal.fire({
        title: '¿Procesar Acta?',
        text: 'Esta acción marcará el acta como procesada',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, procesar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/api/actas/${actaId}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ estado: 'procesada' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('¡Procesada!', 'El acta ha sido procesada exitosamente', 'success');
                    setTimeout(() => location.reload(), 1500);
                }
            });
        }
    });
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
        title: 'Iniciar Inspección Vehicular',
        html: `
            <form id="inspeccionForm" class="text-left">
                <div class="mb-3">
                    <label>Placa del Vehículo:</label>
                    <input type="text" id="placa" class="form-control" required placeholder="ABC-123" style="text-transform: uppercase;">
                </div>
                <div class="mb-3">
                    <label>Inspector:</label>
                    <select id="inspector_id" class="form-control" required>
                        <option value="">Seleccionar inspector...</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Tipo de Inspección:</label>
                    <select id="tipo_inspeccion" class="form-control" required>
                        <option value="tecnica">Técnica</option>
                        <option value="documentos">Documentos</option>
                        <option value="completa">Completa</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Ubicación:</label>
                    <input type="text" id="ubicacion_inspeccion" class="form-control" required placeholder="Lugar de la inspección">
                </div>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Iniciar Inspección',
        cancelButtonText: 'Cancelar',
        width: 500,
        didOpen: () => {
            // Cargar inspectores
            cargarInspectores();
            
            // Auto completar placa
            document.getElementById('placa').addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        },
        preConfirm: () => {
            const placa = document.getElementById('placa').value;
            const inspector_id = document.getElementById('inspector_id').value;
            const tipo_inspeccion = document.getElementById('tipo_inspeccion').value;
            const ubicacion = document.getElementById('ubicacion_inspeccion').value;

            if (!placa || !inspector_id || !tipo_inspeccion || !ubicacion) {
                Swal.showValidationMessage('Todos los campos son obligatorios');
                return false;
            }

            return fetch('/api/inspeccion/iniciar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    placa: placa,
                    inspector_id: inspector_id,
                    tipo_inspeccion: tipo_inspeccion,
                    ubicacion: ubicacion
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    return data;
                } else {
                    throw new Error(data.message || 'Error al iniciar inspección');
                }
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Abrir formulario de inspección detallada
            abrirFormularioInspeccion(result.value.inspeccion_id, result.value.vehiculo);
        }
    }).catch((error) => {
        Swal.fire('Error', error.message, 'error');
    });
}

function cargarInspectores() {
    fetch('/api/inspectores-activos')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('inspector_id');
            data.inspectores.forEach(inspector => {
                const option = document.createElement('option');
                option.value = inspector.id;
                option.textContent = inspector.nombre;
                select.appendChild(option);
            });
        });
}

function abrirFormularioInspeccion(inspeccionId, vehiculo) {
    Swal.fire({
        title: `Inspección - ${vehiculo.placa}`,
        html: `
            <div class="text-left">
                <h6>Información del Vehículo:</h6>
                <p><strong>Placa:</strong> ${vehiculo.placa}<br>
                <strong>Modelo:</strong> ${vehiculo.modelo}<br>
                <strong>Año:</strong> ${vehiculo.año}</p>
                
                <hr>
                
                <form id="inspeccionDetalleForm">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Documentos:</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="soat_vigente">
                                <label class="form-check-label">SOAT Vigente</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="revision_tecnica">
                                <label class="form-check-label">Revisión Técnica</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="tarjeta_propiedad">
                                <label class="form-check-label">Tarjeta de Propiedad</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Estado Técnico:</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="luces_funcionando">
                                <label class="form-check-label">Luces Funcionando</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="neumaticos_buen_estado">
                                <label class="form-check-label">Neumáticos Buen Estado</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="frenos_operativos">
                                <label class="form-check-label">Frenos Operativos</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <label>Observaciones:</label>
                        <textarea id="observaciones_inspeccion" class="form-control" rows="3" placeholder="Observaciones adicionales..."></textarea>
                    </div>
                    
                    <div class="mt-3">
                        <label>Resultado de la Inspección:</label>
                        <select id="resultado_inspeccion" class="form-control" required>
                            <option value="">Seleccionar resultado...</option>
                            <option value="aprobada">Aprobada</option>
                            <option value="observada">Observada</option>
                            <option value="rechazada">Rechazada</option>
                        </select>
                    </div>
                </form>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Finalizar Inspección',
        cancelButtonText: 'Cancelar',
        width: 700,
        preConfirm: () => {
            const resultado = document.getElementById('resultado_inspeccion').value;
            if (!resultado) {
                Swal.showValidationMessage('Debe seleccionar el resultado de la inspección');
                return false;
            }

            const detalles = {
                soat_vigente: document.getElementById('soat_vigente').checked,
                revision_tecnica: document.getElementById('revision_tecnica').checked,
                tarjeta_propiedad: document.getElementById('tarjeta_propiedad').checked,
                luces_funcionando: document.getElementById('luces_funcionando').checked,
                neumaticos_buen_estado: document.getElementById('neumaticos_buen_estado').checked,
                frenos_operativos: document.getElementById('frenos_operativos').checked,
                observaciones: document.getElementById('observaciones_inspeccion').value,
                resultado: resultado
            };

            return fetch('/api/inspeccion/registrar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    inspeccion_id: inspeccionId,
                    detalles: detalles
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    return data;
                } else {
                    throw new Error(data.message || 'Error al registrar inspección');
                }
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire('¡Éxito!', 'Inspección registrada exitosamente', 'success');
            setTimeout(() => location.reload(), 1500);
        }
    });
}

function verificarLicencia() {
    Swal.fire({
        title: 'Verificar Licencia de Conducir',
        html: `
            <form id="licenciaForm" class="text-left">
                <div class="mb-3">
                    <label>Número de Licencia:</label>
                    <input type="text" id="numero_licencia" class="form-control" required placeholder="Número de licencia">
                </div>
                <div class="mb-3">
                    <label>DNI del Conductor:</label>
                    <input type="text" id="dni_conductor" class="form-control" placeholder="DNI (opcional)">
                </div>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Verificar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const numero_licencia = document.getElementById('numero_licencia').value;
            const dni_conductor = document.getElementById('dni_conductor').value;

            if (!numero_licencia) {
                Swal.showValidationMessage('El número de licencia es obligatorio');
                return false;
            }

            return fetch('/api/verificar-licencia', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    numero_licencia: numero_licencia,
                    dni_conductor: dni_conductor
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    return data;
                } else {
                    throw new Error(data.message || 'Error al verificar licencia');
                }
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const licencia = result.value.licencia;
            const estado = licencia.vigente ? 'Vigente' : 'Vencida';
            const clase = licencia.vigente ? 'success' : 'error';
            
            Swal.fire({
                title: `Licencia ${estado}`,
                html: `
                    <div class="text-left">
                        <p><strong>Conductor:</strong> ${licencia.conductor_nombre}</p>
                        <p><strong>DNI:</strong> ${licencia.dni}</p>
                        <p><strong>Categoría:</strong> ${licencia.categoria}</p>
                        <p><strong>Fecha Vencimiento:</strong> ${licencia.fecha_vencimiento}</p>
                        <p><strong>Estado:</strong> <span class="badge bg-${licencia.vigente ? 'success' : 'danger'}">${estado}</span></p>
                    </div>
                `,
                icon: clase
            });
        }
    }).catch((error) => {
        Swal.fire('Error', error.message, 'error');
    });
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
