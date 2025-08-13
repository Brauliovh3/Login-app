@extends('layouts.dashboard')

@section('title', 'Gestión de Actas de Fiscalización')

@section('content')
<style>
    :root {
        --drtc-orange: #ff8c00;
        --drtc-dark-orange: #e67c00;
        --drtc-light-orange: #ffffff;
        --drtc-orange-bg: #fff4e6;
        --drtc-navy: #1e3a8a;
    }
    
    .bg-drtc-orange { background-color: var(--drtc-orange) !important; }
    .bg-drtc-dark { background-color: var(--drtc-dark-orange) !important; }
    .bg-drtc-light { background-color: var(--drtc-light-orange) !important; }
    .bg-drtc-soft { background-color: var(--drtc-orange-bg) !important; }
    .bg-drtc-navy { background-color: var(--drtc-navy) !important; }
    .text-drtc-orange { color: var(--drtc-orange) !important; }
    .text-drtc-navy { color: var(--drtc-navy) !important; }
    
    /* Fix para SweetAlert z-index */
    .swal-z-index-high {
        z-index: 999999 !important;
    }
    .swal2-container.swal-z-index-high {
        z-index: 999999 !important;
    }
    .border-drtc-orange { border-color: var(--drtc-orange) !important; }
    
    .action-btn {
        background: white;
        border: 2px solid #e0e0e0;
        border-radius: 15px;
        padding: 15px;
        text-align: center;
        transition: all 0.3s ease;
        height: 100px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        text-decoration: none;
        color: #333;
        margin-bottom: 15px;
    }
    
    .action-btn:hover {
        border-color: var(--drtc-orange);
        background: var(--drtc-orange-bg);
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(255, 140, 0, 0.2);
        color: var(--drtc-orange);
        text-decoration: none;
    }
    
    .action-btn i {
        font-size: 24px;
        margin-bottom: 5px;
        color: var(--drtc-orange);
    }
    
    .action-btn:hover i {
        color: var(--drtc-dark-orange);
    }
    
    /* Estilos mejorados para la búsqueda */
    #buscar_general {
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(255, 140, 0, 0.1);
    }
    
    #buscar_general:focus {
        border-color: var(--drtc-orange) !important;
        box-shadow: 0 0 0 0.2rem rgba(255, 140, 0, 0.25) !important;
        transform: translateY(-1px);
    }
    
    .search-highlight {
        background-color: #fff3cd;
        border-left: 4px solid #ffc107;
        padding: 10px;
        margin: 10px 0;
        border-radius: 5px;
    }
    
    .btn-group .btn {
        border: 2px solid;
    }
    
    .btn-primary {
        background-color: var(--drtc-orange);
        border-color: var(--drtc-orange);
    }
    
    .btn-primary:hover {
        background-color: var(--drtc-dark-orange);
        border-color: var(--drtc-dark-orange);
    }
    
    .loading-spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid #ffffff;
        border-radius: 50%;
        border-top-color: transparent;
        animation: spin 1s ease-in-out infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    .table tbody tr {
        transition: all 0.2s ease;
    }
    
    .table tbody tr:hover {
        background-color: rgba(255, 140, 0, 0.05);
        transform: scale(1.01);
    }
</style>

<div class="container-fluid">
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
                            <div class="action-btn" onclick="abrirModal('modal-consultas')">
                                <i class="fas fa-search"></i>
                                <strong>Consultas</strong>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                            <div class="action-btn" onclick="abrirModal('modal-eliminar-acta')">
                                <i class="fas fa-trash-alt"></i>
                                <strong>Eliminar Acta</strong>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                            <div class="action-btn" onclick="probarConexion()" style="background: linear-gradient(135deg, #28a745, #20c997);">
                                <i class="fas fa-bug"></i>
                                <strong>🔧 Debug POST</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Búsqueda simplificada -->
    <div class="card mb-4" style="border-color: #ff8c00;">
        <div class="card-header" style="background-color: #ff8c00; color: white;">
            <h5 class="mb-0">
                <i class="fas fa-search me-2"></i>Búsqueda de Actas
            </h5>
        </div>
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-5">
                    <label for="buscar_general" class="form-label fw-bold">
                        <i class="fas fa-search me-1"></i>Buscar por: Número de Acta, DNI, Licencia, Placa o Nombre
                    </label>
                    <input type="text" 
                           class="form-control form-control-lg" 
                           id="buscar_general" 
                           placeholder="Ej: DRTC-APU-2025-000001, 12345678, ABC-123, Juan Pérez..."
                           style="border: 2px solid #ff8c00;">
                </div>
                <div class="col-md-2">
                    <label for="filtro_estado" class="form-label">Estado</label>
                    <select class="form-select" id="filtro_estado">
                        <option value="">Todos</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="procesada">Procesada</option>
                        <option value="anulada">Anulada</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="filtro_fecha" class="form-label">Fecha</label>
                    <input type="date" class="form-select" id="filtro_fecha">
                </div>
                <div class="col-md-3">
                    <div class="btn-group w-100" role="group">
                        <button type="button" class="btn btn-primary" onclick="buscarActas()">
                            <i class="fas fa-search me-1"></i>Buscar
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="limpiarBusqueda()">
                            <i class="fas fa-times me-1"></i>Limpiar
                        </button>
                        <button type="button" class="btn btn-success" onclick="exportarActas()">
                            <i class="fas fa-download me-1"></i>Exportar
                        </button>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <div class="alert alert-info d-flex align-items-center" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <div>
                            <strong>Instrucciones de búsqueda:</strong> Escribe el término de búsqueda y haz clic en "Buscar" o presiona Enter. 
                            Puedes buscar por número de acta, DNI, licencia, placa o nombre.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas dinámicas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 id="count-pendientes">{{ $actas->where('estado', 'pendiente')->count() }}</h4>
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
                            <h4 id="count-procesadas">{{ $actas->where('estado', 'procesada')->count() }}</h4>
                            <p class="mb-0">Procesadas</p>
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
                            <h4 id="count-anuladas">{{ $actas->where('estado', 'anulada')->count() }}</h4>
                            <p class="mb-0">Anuladas</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-ban fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 id="count-total">{{ $actas->count() }}</h4>
                            <p class="mb-0">Total</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de actas -->
    <div class="card">
        <div class="card-header" style="background-color: #fff3e0; border-color: #ff8c00;">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0" style="color: #ff8c00;">
                    <i class="fas fa-list me-2"></i>Lista de Actas de Contra
                </h5>
                <div class="text-end">
                    <small class="text-muted">
                        <i class="fas fa-database me-1"></i>
                        Total en base de datos: <strong id="total-actas-db">{{ $actas->count() }}</strong>
                    </small>
                </div>
            </div>
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
                        @forelse($actas as $acta)
                        <tr>
                            <td><strong>{{ $acta->numero_acta }}</strong></td>
                            <td>{{ \Carbon\Carbon::parse($acta->fecha_intervencion . ' ' . $acta->hora_intervencion)->format('d/m/Y H:i') }}</td>
                            <td><span class="badge bg-dark">{{ $acta->placa }}</span></td>
                            <td>{{ $acta->nombre_conductor ?? $acta->razon_social }}</td>
                            <td>{{ $acta->descripcion_hechos ? \Str::limit($acta->descripcion_hechos, 30) : 'Sin descripción' }}</td>
                            <td><strong>{{ $acta->sancion ? 'S/ ' . number_format($acta->sancion, 2) : 'Sin sanción' }}</strong></td>
                            <td>{{ $acta->created_at ? $acta->created_at->addDays(15)->format('d/m/Y') : 'N/A' }}</td>
                            <td>
                                @switch($acta->estado)
                                    @case('procesada')
                                        <span class="badge bg-success">Procesada</span>
                                        @break
                                    @case('anulada')
                                        <span class="badge bg-danger">Anulada</span>
                                        @break
                                    @default
                                        <span class="badge bg-warning">Pendiente</span>
                                @endswitch
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" title="Ver detalle" onclick="verDetalleActa({{ $acta->id }})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @if($acta->estado !== 'anulada')
                                <button class="btn btn-sm btn-outline-success" title="Editar" onclick="editarActa({{ $acta->id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" title="Imprimir" onclick="imprimirActa({{ $acta->id }})">
                                    <i class="fas fa-print"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="Anular" onclick="anularActa({{ $acta->id }})">
                                    <i class="fas fa-ban"></i>
                                </button>
                                @else
                                <button class="btn btn-sm btn-outline-secondary" title="Acta anulada" disabled>
                                    <i class="fas fa-ban"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No hay actas registradas</h5>
                                    <p class="text-muted">Haz clic en "Nueva Acta" para crear la primera acta</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva Acta -->
<div class="modal fade" id="nuevaActaModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #ff8c00; color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Nueva Acta de Contra
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="nuevaActaForm">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Datos del Vehículo y Conductor</h6>
                            <hr>
                            <div class="mb-3">
                                <label for="placa_vehiculo" class="form-label">Placa del Vehículo *</label>
                                <input type="text" class="form-control" id="placa_vehiculo" required>
                            </div>
                            <div class="mb-3">
                                <label for="dni_conductor" class="form-label">DNI del Conductor *</label>
                                <input type="text" class="form-control" id="dni_conductor" maxlength="8" required>
                            </div>
                            <div class="mb-3">
                                <label for="nombre_conductor" class="form-label">Nombre del Conductor</label>
                                <input type="text" class="form-control" id="nombre_conductor" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="licencia_conductor" class="form-label">N° Licencia</label>
                                <input type="text" class="form-control" id="licencia_conductor" readonly>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="text-primary">Datos de la Infracción</h6>
                            <hr>
                            <div class="mb-3">
                                <label for="fecha_infraccion" class="form-label">Fecha y Hora *</label>
                                <input type="datetime-local" class="form-control bg-light" id="fecha_infraccion" value="{{ now()->format('Y-m-d\TH:i') }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="lugar_infraccion" class="form-label">Lugar de la Infracción *</label>
                                <input type="text" class="form-control" id="lugar_infraccion" required>
                            </div>
                            <div class="mb-3">
                                <label for="infraccion_id" class="form-label">Tipo de Infracción *</label>
                                <select class="form-select" id="infraccion_id" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="1">G.01 - Exceso de velocidad (S/ 462.00)</option>
                                    <option value="2">L.02 - Documentos vencidos (S/ 231.00)</option>
                                    <option value="3">MG.03 - Transporte ilegal (S/ 4,620.00)</option>
                                    <option value="4">G.05 - No usar cinturón (S/ 462.00)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="monto_multa" class="form-label">Monto de la Multa</label>
                                <input type="number" class="form-control" id="monto_multa" step="0.01" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary">Observaciones y Evidencias</h6>
                            <hr>
                            <div class="mb-3">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="observaciones" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="evidencias" class="form-label">Evidencias (Fotos, Videos)</label>
                                <input type="file" class="form-control" id="evidencias" multiple accept="image/*,video/*">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarActa()">
                    <i class="fas fa-save me-2"></i>Generar Acta
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Función de prueba para verificar conectividad POST
function probarConexion() {
    console.log('🔧 Iniciando prueba de conexión...');
    
    Swal.fire({
        title: '🔧 Probando Conexión',
        text: 'Verificando rutas POST...',
        allowOutsideClick: false,
        customClass: {
            container: 'swal-z-index-high'
        },
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Probar la ruta de prueba temporal
    fetch('/fiscalizador/actas-test', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            test: 'debug',
            timestamp: new Date().toISOString()
        })
    })
    .then(response => {
        console.log('📡 Respuesta recibida:', response);
        console.log('📊 Status:', response.status);
        console.log('📝 Status Text:', response.statusText);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        return response.json();
    })
    .then(result => {
        console.log('✅ Resultado exitoso:', result);
        
        Swal.fire({
            icon: 'success',
            title: '✅ Conexión Exitosa',
            html: `
                <div class="text-start">
                    <p><strong>✅ Servidor:</strong> Respondiendo correctamente</p>
                    <p><strong>✅ Usuario:</strong> ${result.user}</p>
                    <p><strong>✅ Middleware:</strong> Funcionando</p>
                    <p><strong>✅ CSRF Token:</strong> Válido</p>
                    <p><strong>✅ Método POST:</strong> Permitido</p>
                </div>
            `,
            confirmButtonText: '🚀 ¡Probar Acta Real!',
            customClass: {
                container: 'swal-z-index-high'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                abrirModal('modal-nueva-acta');
            }
        });
    })
    .catch(error => {
        console.error('❌ Error en la prueba:', error);
        
        Swal.fire({
            icon: 'error',
            title: '❌ Error de Conexión',
            html: `
                <div class="text-start">
                    <p><strong>❌ Error:</strong> ${error.message}</p>
                    <p><strong>🔍 Debug Info:</strong></p>
                    <ul class="text-start">
                        <li>Verifica que estés logueado</li>
                        <li>Verifica el token CSRF</li>
                        <li>Revisa la consola del navegador</li>
                        <li>Verifica que el servidor esté funcionando</li>
                    </ul>
                </div>
            `,
            confirmButtonText: 'Entendido',
            customClass: {
                container: 'swal-z-index-high'
            }
        });
    });
}

function guardarActa() {
    // Validar formulario
    const form = document.getElementById('form-nueva-acta');
    const formData = new FormData(form);
    
    // Validaciones básicas - solo campos realmente esenciales
    const lugar = formData.get('lugar_intervencion')?.trim();
    const fecha = formData.get('fecha_intervencion');
    const hora = formData.get('hora_intervencion');
    const tipoServicio = formData.get('tipo_servicio');
    const tipoAgente = formData.get('tipo_agente');
    const rucDni = formData.get('ruc_dni')?.trim();
    const descripcionHechos = formData.get('descripcion_hechos')?.trim();
    
    // Lista de campos faltantes - solo los esenciales
    const camposFaltantes = [];
    
    if (!lugar) camposFaltantes.push('Lugar de Intervención');
    if (!fecha) camposFaltantes.push('Fecha de Intervención');
    if (!hora) camposFaltantes.push('Hora de Intervención');
    if (!tipoServicio) camposFaltantes.push('Tipo de Servicio');
    if (!tipoAgente) camposFaltantes.push('Tipo de Agente');
    if (!rucDni) camposFaltantes.push('RUC/DNI');
    if (!descripcionHechos) camposFaltantes.push('Descripción de Hechos');
    
    if (camposFaltantes.length > 0) {
        Swal.fire({
            icon: 'error',
            title: 'Campos Requeridos',
            html: `
                <div class="text-start">
                    <p><strong>Por favor complete los siguientes campos:</strong></p>
                    <ul class="text-danger">
                        ${camposFaltantes.map(campo => `<li>${campo}</li>`).join('')}
                    </ul>
                </div>
            `,
            confirmButtonColor: '#dc3545',
            customClass: {
                container: 'swal-z-index-high'
            }
        });
        return;
    }
    
    // Mostrar indicador de carga
    Swal.fire({
        title: 'Guardando Acta...',
        text: 'Por favor espere mientras se registra el acta en la base de datos',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        customClass: {
            container: 'swal-z-index-high'
        },
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Preparar datos para envío con valores por defecto
    const data = {
        numero_acta: formData.get('numero_acta') || document.getElementById('numero_acta_hidden')?.value,
        lugar_intervencion: lugar,
        fecha_intervencion: formData.get('fecha_intervencion') || new Date().toISOString().split('T')[0],
        hora_intervencion: formData.get('hora_intervencion') || new Date().toTimeString().split(' ')[0].substr(0,5),
        inspector_responsable: '{{ Auth::user()->name }}', // Siempre usar el usuario logueado
        tipo_servicio: formData.get('tipo_servicio') || 'Público Regular',
        tipo_agente: formData.get('tipo_agente') || 'Transportista',
        placa: formData.get('placa')?.trim()?.toUpperCase() || 'NO APLICA',
        razon_social: formData.get('razon_social')?.trim()?.toUpperCase() || 'PERSONA NATURAL',
        ruc_dni: rucDni,
        nombre_conductor: formData.get('nombre_conductor') || '',
        licencia: formData.get('licencia') || '',
        clase_licencia: formData.get('clase_licencia') || '',
        origen: formData.get('origen') || '',
        destino: formData.get('destino') || '',
        numero_personas: formData.get('numero_personas') || null,
        descripcion_hechos: descripcionHechos,
        medios_probatorios: formData.get('medios_probatorios') || 'Inspección visual, documentación revisada',
        calificacion: formData.get('calificacion') || 'Leve',
        medida_administrativa: formData.get('medida_administrativa') || '',
        sancion: formData.get('sancion') || '',
        observaciones_intervenido: formData.get('observaciones_intervenido') || '',
        observaciones_inspector: formData.get('observaciones_inspector') || ''
    };
    
    // Enviar datos al servidor
    console.log('Enviando datos:', data); // Debug
    fetch('/fiscalizador/actas', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        console.log('Respuesta del servidor:', response); // Debug
        return response.json();
    })
    .then(result => {
        console.log('Resultado procesado:', result); // Debug
        Swal.close();
        
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Acta Registrada!',
                html: `
                    <div class="text-center">
                        <h5 class="text-success">Acta N° ${result.numero_acta}</h5>
                        <p class="mb-2"><strong>Registrada exitosamente en la base de datos</strong></p>
                        <div class="border rounded p-3 bg-light">
                            <small class="text-muted">
                                <i class="fas fa-database me-1"></i>
                                ID: ${result.id || 'N/A'}<br>
                                <i class="fas fa-clock me-1"></i>
                                ${new Date().toLocaleString('es-PE')}
                            </small>
                        </div>
                    </div>
                `,
                confirmButtonColor: '#28a745',
                confirmButtonText: 'Aceptar',
                customClass: {
                    container: 'swal-z-index-high'
                }
            }).then(() => {
                // Cerrar modal y limpiar formulario
                cerrarModal('modal-nueva-acta');
                form.reset();
                
                // Recargar la página para mostrar la nueva acta
                window.location.reload();
            });
        } else {
            console.error('Error del servidor:', result); // Debug
            Swal.fire({
                icon: 'error',
                title: 'Error al Registrar',
                html: `
                    <p>${result.message || 'Ocurrió un error al guardar el acta en la base de datos'}</p>
                    <small class="text-muted">Detalles técnicos: ${JSON.stringify(result.errors || {})}</small>
                `,
                confirmButtonColor: '#dc3545',
                customClass: {
                    container: 'swal-z-index-high'
                }
            });
        }
    })
    .catch(error => {
        console.error('Error de conexión:', error); // Debug
        Swal.close();
        Swal.fire({
            icon: 'error',
            title: 'Error de Conexión',
            html: `
                <p>No se pudo conectar con el servidor.</p>
                <small class="text-muted">Error: ${error.message}</small>
            `,
            confirmButtonColor: '#dc3545',
            customClass: {
                container: 'swal-z-index-high'
            }
        });
    });
}

// Función para agregar botón de finalizar al modal
function agregarBotonFinalizar() {
    const modalFooter = document.querySelector('#modal-nueva-acta .d-flex.justify-content-between');
    if (modalFooter && !document.getElementById('btn-finalizar-acta')) {
        const btnFinalizar = document.createElement('button');
        btnFinalizar.id = 'btn-finalizar-acta';
        btnFinalizar.type = 'button';
        btnFinalizar.className = 'btn btn-success';
        btnFinalizar.innerHTML = '<i class="fas fa-check-double me-2"></i>Finalizar Registro';
        btnFinalizar.onclick = finalizarRegistroActa;
        
        modalFooter.appendChild(btnFinalizar);
    }
}

// Actualizar monto al seleccionar infracción
document.getElementById('infraccion_id').addEventListener('change', function() {
    const select = this;
    const montoInput = document.getElementById('monto_multa');
    
    if (select.value === '1') montoInput.value = '462.00';
    else if (select.value === '2') montoInput.value = '231.00';
    else if (select.value === '3') montoInput.value = '4620.00';
    else if (select.value === '4') montoInput.value = '462.00';
    else montoInput.value = '';
});
</script>

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
            <form id="form-nueva-acta" method="POST" action="{{ route('actas.store') }}">
                @csrf
                
                <!-- Campos automáticos ocultos -->
                <input type="hidden" id="fecha_inspeccion_hidden" name="fecha_inspeccion">
                <input type="hidden" id="hora_inicio_hidden" name="hora_inicio">
                <input type="hidden" name="inspector_principal" value="{{ Auth::user()->name }}">

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
                                    <span id="numero_acta_display" 
                                          class="fw-bold text-dark me-2" 
                                          style="font-size: 24px; color: #d32f2f; font-family: 'Courier New', monospace;">
                                        000001
                                    </span>
                                    <span class="fw-bold text-dark" style="font-size: 18px;">- {{ date('Y') }}</span>
                                    <!-- Campo oculto para enviar el número en el formulario -->
                                    <input type="hidden" id="numero_acta_hidden" name="numero_acta" value="">
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
                        
                        <!-- Información adicional del documento -->
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold me-2">Fecha:</span> 
                                    <div class="border-bottom border-dark px-3" style="min-width: 120px; text-align: center; font-weight: bold;">
                                        {{ now()->format('d/m/Y') }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold me-2">Hora:</span> 
                                    <div class="border-bottom border-dark px-3" style="min-width: 120px; text-align: center; font-weight: bold;">
                                        {{ now()->format('H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
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
                                        <input class="form-check-input" type="radio" name="tipo_agente" id="transportista" value="Transportista" checked>
                                        <label class="form-check-label fw-bold w-100" for="transportista">
                                            <i class="fas fa-truck me-2 text-warning"></i>TRANSPORTISTA
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check p-3 border border-warning rounded bg-white">
                                        <input class="form-check-input" type="radio" name="tipo_agente" id="operador_ruta" value="Operador de Ruta">
                                        <label class="form-check-label fw-bold w-100" for="operador_ruta">
                                            <i class="fas fa-route me-2 text-warning"></i>OPERADOR DE RUTA
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check p-3 border border-warning rounded bg-white">
                                        <input class="form-check-input" type="radio" name="tipo_agente" id="conductor" value="Conductor">
                                        <label class="form-check-label fw-bold w-100" for="conductor">
                                            <i class="fas fa-id-card me-2 text-warning"></i>CONDUCTOR
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Datos del operador/conductor -->
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold text-warning">RUC/DNI:</label>
                                <input type="text" class="form-control border-warning" name="ruc_dni" id="ruc_dni" placeholder="20123456789 o 12345678" maxlength="11" required>
                                <div class="form-text">
                                    <small class="text-muted">DNI: 8 dígitos | RUC: 11 dígitos</small>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-warning">Razón Social/Nombres y Apellidos: <small class="text-muted">(Opcional)</small></label>
                                <input type="text" class="form-control border-warning" name="razon_social" id="razon_social" placeholder="Se autocompletará con los datos de RENIEC/SUNAT o deje vacío">
                                <div id="loading-data" class="form-text text-info" style="display: none;">
                                    <i class="fas fa-spinner fa-spin"></i> Consultando datos...
                                </div>
                                <div class="form-text mt-1">
                                    <small class="text-muted">Datos obtenidos de APIs oficiales</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold text-warning">Placa del Vehículo:</label>
                                <input type="text" class="form-control border-warning" name="placa_1" placeholder="ABC-123" style="text-transform: uppercase;" required>
                            </div>
                        </div>
                        
                        <!-- Datos adicionales del conductor -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-warning">Nombre del Conductor:</label>
                                <input type="text" class="form-control border-warning" name="nombre_conductor_1" placeholder="Nombres y apellidos completos" required>
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
                    </div>
                </div>

                <!-- SECCIÓN 2: DATOS DE LA INTERVENCIÓN -->
                <div class="card mb-4 border-info">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-map-marker-alt me-2"></i>II. DATOS DE LA INTERVENCIÓN</h6>
                    </div>
                    <div class="card-body bg-light">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-info">Lugar de Intervención:</label>
                                <select class="form-select border-info" name="lugar_intervencion" required>
                                    <option value="">Seleccione provincia y distrito de Apurímac...</option>
                                    
                                    <!-- PROVINCIA DE ABANCAY -->
                                    <optgroup label="🏛️ PROVINCIA DE ABANCAY">
                                        <option value="Abancay - Abancay - Apurímac">Abancay (Capital)</option>
                                        <option value="Chacoche - Abancay - Apurímac">Chacoche</option>
                                        <option value="Circa - Abancay - Apurímac">Circa</option>
                                        <option value="Curahuasi - Abancay - Apurímac">Curahuasi</option>
                                        <option value="Huanipaca - Abancay - Apurímac">Huanipaca</option>
                                        <option value="Lambrama - Abancay - Apurímac">Lambrama</option>
                                        <option value="Pichirhua - Abancay - Apurímac">Pichirhua</option>
                                        <option value="San Pedro de Cachora - Abancay - Apurímac">San Pedro de Cachora</option>
                                        <option value="Tamburco - Abancay - Apurímac">Tamburco</option>
                                    </optgroup>
                                    
                                    <!-- PROVINCIA DE ANDAHUAYLAS -->
                                    <optgroup label="🌾 PROVINCIA DE ANDAHUAYLAS">
                                        <option value="Andahuaylas - Andahuaylas - Apurímac">Andahuaylas (Capital)</option>
                                        <option value="Andarapa - Andahuaylas - Apurímac">Andarapa</option>
                                        <option value="Chiara - Andahuaylas - Apurímac">Chiara</option>
                                        <option value="Huancarama - Andahuaylas - Apurímac">Huancarama</option>
                                        <option value="Huancaray - Andahuaylas - Apurímac">Huancaray</option>
                                        <option value="Huayana - Andahuaylas - Apurímac">Huayana</option>
                                        <option value="Kishuara - Andahuaylas - Apurímac">Kishuara</option>
                                        <option value="Pacobamba - Andahuaylas - Apurímac">Pacobamba</option>
                                        <option value="Pacucha - Andahuaylas - Apurímac">Pacucha</option>
                                        <option value="Pampachiri - Andahuaylas - Apurímac">Pampachiri</option>
                                        <option value="Pomacocha - Andahuaylas - Apurímac">Pomacocha</option>
                                        <option value="San Antonio de Cachi - Andahuaylas - Apurímac">San Antonio de Cachi</option>
                                        <option value="San Jerónimo - Andahuaylas - Apurímac">San Jerónimo</option>
                                        <option value="San Miguel de Chaccrampa - Andahuaylas - Apurímac">San Miguel de Chaccrampa</option>
                                        <option value="Santa María de Chicmo - Andahuaylas - Apurímac">Santa María de Chicmo</option>
                                        <option value="Talavera - Andahuaylas - Apurímac">Talavera</option>
                                        <option value="Tumay Huaraca - Andahuaylas - Apurímac">Tumay Huaraca</option>
                                        <option value="Turpo - Andahuaylas - Apurímac">Turpo</option>
                                        <option value="Kaquiabamba - Andahuaylas - Apurímac">Kaquiabamba</option>
                                        <option value="José María Arguedas - Andahuaylas - Apurímac">José María Arguedas</option>
                                    </optgroup>
                                    
                                    <!-- PROVINCIA DE ANTABAMBA -->
                                    <optgroup label="⛰️ PROVINCIA DE ANTABAMBA">
                                        <option value="Antabamba - Antabamba - Apurímac">Antabamba (Capital)</option>
                                        <option value="El Oro - Antabamba - Apurímac">El Oro</option>
                                        <option value="Huaquirca - Antabamba - Apurímac">Huaquirca</option>
                                        <option value="Juan Espinoza Medrano - Antabamba - Apurímac">Juan Espinoza Medrano</option>
                                        <option value="Oropesa - Antabamba - Apurímac">Oropesa</option>
                                        <option value="Pachaconas - Antabamba - Apurímac">Pachaconas</option>
                                        <option value="Sabaino - Antabamba - Apurímac">Sabaino</option>
                                    </optgroup>
                                    
                                    <!-- PROVINCIA DE AYMARAES -->
                                    <optgroup label="🏔️ PROVINCIA DE AYMARAES">
                                        <option value="Chalhuanca - Aymaraes - Apurímac">Chalhuanca (Capital)</option>
                                        <option value="Capaya - Aymaraes - Apurímac">Capaya</option>
                                        <option value="Caraybamba - Aymaraes - Apurímac">Caraybamba</option>
                                        <option value="Chalhuanca - Aymaraes - Apurímac">Chalhuanca</option>
                                        <option value="Chapimarca - Aymaraes - Apurímac">Chapimarca</option>
                                        <option value="Colcabamba - Aymaraes - Apurímac">Colcabamba</option>
                                        <option value="Cotaruse - Aymaraes - Apurímac">Cotaruse</option>
                                        <option value="Ihuayllo - Aymaraes - Apurímac">Ihuayllo</option>
                                        <option value="Justo Apu Sahuaraura - Aymaraes - Apurímac">Justo Apu Sahuaraura</option>
                                        <option value="Lucre - Aymaraes - Apurímac">Lucre</option>
                                        <option value="Pocohuanca - Aymaraes - Apurímac">Pocohuanca</option>
                                        <option value="San Juan de Chacña - Aymaraes - Apurímac">San Juan de Chacña</option>
                                        <option value="Sañayca - Aymaraes - Apurímac">Sañayca</option>
                                        <option value="Soraya - Aymaraes - Apurímac">Soraya</option>
                                        <option value="Tapairihua - Aymaraes - Apurímac">Tapairihua</option>
                                        <option value="Tintay - Aymaraes - Apurímac">Tintay</option>
                                        <option value="Toraya - Aymaraes - Apurímac">Toraya</option>
                                        <option value="Yanaca - Aymaraes - Apurímac">Yanaca</option>
                                    </optgroup>
                                    
                                    <!-- PROVINCIA DE COTABAMBAS -->
                                    <optgroup label="🌿 PROVINCIA DE COTABAMBAS">
                                        <option value="Tambobamba - Cotabambas - Apurímac">Tambobamba (Capital)</option>
                                        <option value="Cotabambas - Cotabambas - Apurímac">Cotabambas</option>
                                        <option value="Coyllurqui - Cotabambas - Apurímac">Coyllurqui</option>
                                        <option value="Haquira - Cotabambas - Apurímac">Haquira</option>
                                        <option value="Mara - Cotabambas - Apurímac">Mara</option>
                                        <option value="Challhuahuacho - Cotabambas - Apurímac">Challhuahuacho</option>
                                    </optgroup>
                                    
                                    <!-- PROVINCIA DE CHINCHEROS -->
                                    <optgroup label="🌺 PROVINCIA DE CHINCHEROS">
                                        <option value="Chincheros - Chincheros - Apurímac">Chincheros (Capital)</option>
                                        <option value="Anco Huallo - Chincheros - Apurímac">Anco Huallo</option>
                                        <option value="Cocharcas - Chincheros - Apurímac">Cocharcas</option>
                                        <option value="Huaccana - Chincheros - Apurímac">Huaccana</option>
                                        <option value="Ocobamba - Chincheros - Apurímac">Ocobamba</option>
                                        <option value="Ongoy - Chincheros - Apurímac">Ongoy</option>
                                        <option value="Uranmarca - Chincheros - Apurímac">Uranmarca</option>
                                        <option value="Ranracancha - Chincheros - Apurímac">Ranracancha</option>
                                        <option value="Rocchacc - Chincheros - Apurímac">Rocchacc</option>
                                        <option value="El Porvenir - Chincheros - Apurímac">El Porvenir</option>
                                        <option value="Los Chankas - Chincheros - Apurímac">Los Chankas</option>
                                    </optgroup>
                                    
                                    <!-- PROVINCIA DE GRAU -->
                                    <optgroup label="🎯 PROVINCIA DE GRAU">
                                        <option value="Chuquibambilla - Grau - Apurímac">Chuquibambilla (Capital)</option>
                                        <option value="Curasco - Grau - Apurímac">Curasco</option>
                                        <option value="Curpahuasi - Grau - Apurímac">Curpahuasi</option>
                                        <option value="Gamarra - Grau - Apurímac">Gamarra</option>
                                        <option value="Huayllati - Grau - Apurímac">Huayllati</option>
                                        <option value="Mamara - Grau - Apurímac">Mamara</option>
                                        <option value="Micaela Bastidas - Grau - Apurímac">Micaela Bastidas</option>
                                        <option value="Pataypampa - Grau - Apurímac">Pataypampa</option>
                                        <option value="Progreso - Grau - Apurímac">Progreso</option>
                                        <option value="San Antonio - Grau - Apurímac">San Antonio</option>
                                        <option value="Santa Rosa - Grau - Apurímac">Santa Rosa</option>
                                        <option value="Turpay - Grau - Apurímac">Turpay</option>
                                        <option value="Vilcabamba - Grau - Apurímac">Vilcabamba</option>
                                        <option value="Virundo - Grau - Apurímac">Virundo</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold text-info">Fecha:</label>
                                <input type="date" class="form-control border-info bg-light" name="fecha_intervencion" value="{{ date('Y-m-d') }}" readonly>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold text-info">Hora:</label>
                                <input type="time" class="form-control border-info bg-light" name="hora_intervencion" value="{{ date('H:i') }}" readonly>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-info">Inspector Responsable:</label>
                                <input type="text" class="form-control border-info" name="inspector_responsable" id="inspector_responsable" value="{{ Auth::user()->name }}" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-info">Tipo de Servicio:</label>
                                <select class="form-select border-info" name="tipo_servicio" required>
                                    <option value="">Seleccione tipo de servicio...</option>
                                    <option value="publico">Servicio Público</option>
                                    <option value="privado">Servicio Privado</option>
                                    <option value="turistico">Servicio Turístico</option>
                                    <option value="carga">Transporte de Carga</option>
                                    <option value="especial">Servicio Especial</option>
                                </select>
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
                    <button type="button" class="btn btn-success btn-lg me-3 px-5" onclick="guardarActa()">
                        <i class="fas fa-save me-2"></i>GUARDAR ACTA
                    </button>
                    <button type="reset" class="btn btn-secondary btn-lg px-5">
                        <i class="fas fa-undo me-2"></i>LIMPIAR FORMULARIO
                    </button>
                </div>
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
                
                <!-- Aquí iría el formulario de edición igual al de nueva acta pero con _edit en los nombres -->
                <p class="text-center text-muted">
                    <i class="fas fa-info-circle me-2"></i>
                    Formulario de edición se cargaría aquí con los datos del acta encontrada
                </p>
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
            <button class="close-modal" onclick="cerrarModal('modal-eliminar-acta')">
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
                            <button type="button" class="btn btn-danger d-block w-100 fw-bold" onclick="buscarActaEliminar()">
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
                                    <p class="mb-0 fs-5" id="eliminar-numero-acta">DRTC-APU-2024-001</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-group p-3 border border-danger rounded bg-white">
                                    <label class="form-label fw-bold text-danger">Fecha de Registro:</label>
                                    <p class="mb-0 fs-5" id="eliminar-fecha-acta">15/08/2024</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Motivo de eliminación -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-danger">Motivo de la Eliminación (Obligatorio):</label>
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
                                        <label class="form-label fw-bold">Código de Autorización:</label>
                                        <input type="password" class="form-control border-warning" id="codigo-autorizacion" placeholder="Ingrese código de supervisor" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Supervisor Autorizante:</label>
                                        <input type="text" class="form-control border-warning" id="supervisor-autorizante" placeholder="Nombre del supervisor" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Botones de confirmación -->
                        <div class="text-center">
                            <button type="button" class="btn btn-danger btn-lg me-3 px-5" onclick="confirmarEliminacion()">
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
    </div>
</div>

@endsection

<!-- MODAL: CONSULTAS Y REPORTES -->
<div class="floating-modal" id="modal-consultas">
    <div class="modal-content-wrapper">
        <div class="modal-header-custom" style="background: linear-gradient(135deg, #17a2b8, #138496);">
            <h4 class="mb-0 fw-bold">
                <i class="fas fa-search me-2"></i>
                CONSULTAS Y REPORTES DRTC
            </h4>
            <button class="close-modal" onclick="cerrarModal('modal-consultas')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body-custom">
            <!-- Formulario de filtros -->
            <div class="card mb-4 border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-filter me-2"></i>FILTROS DE BÚSQUEDA</h6>
                </div>
                <div class="card-body bg-light">
                    <form id="form-consultas">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-info">N° de Acta:</label>
                                <input type="text" class="form-control border-info" name="numero_acta" placeholder="DRTC-APU-2025-001">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-info">RUC/DNI:</label>
                                <input type="text" class="form-control border-info" name="ruc_dni" placeholder="20123456789">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-info">Placa del Vehículo: <small class="text-muted">(Opcional)</small></label>
                                <input type="text" class="form-control border-info" name="placa" placeholder="ABC-123 o deje vacío si no aplica" style="text-transform: uppercase;">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-info">Estado del Acta:</label>
                                <select class="form-select border-info" name="estado">
                                    <option value="">Todos los estados</option>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="procesada">Procesada</option>
                                    <option value="anulada">Anulada</option>
                                    <option value="pagada">Pagada</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-info">Fecha Desde:</label>
                                <input type="date" class="form-control border-info" name="fecha_desde">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-info">Fecha Hasta:</label>
                                <input type="date" class="form-control border-info" name="fecha_hasta">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-info">Inspector:</label>
                                <select class="form-select border-info" name="inspector">
                                    <option value="">Todos los inspectores</option>
                                    <option value="{{ Auth::user()->name }}">{{ Auth::user()->name }}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-info">Calificación:</label>
                                <select class="form-select border-info" name="calificacion">
                                    <option value="">Seleccione calificación...</option>
                                    <option value="Leve" selected>Leve</option>
                                    <option value="Grave">Grave</option>
                                    <option value="Muy Grave">Muy Grave</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Botones de acción -->
            <div class="text-center mb-4">
                <button type="button" class="btn btn-info btn-lg me-2 px-4" onclick="consultarActas()">
                    <i class="fas fa-search me-2"></i>CONSULTAR ACTAS
                </button>
                <button type="button" class="btn btn-success btn-lg me-2 px-4" onclick="exportarExcel()">
                    <i class="fas fa-file-excel me-2"></i>EXPORTAR EXCEL
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
                                    <th>OPERADOR/CONDUCTOR</th>
                                    <th>RUC/DNI</th>
                                    <th>PLACA</th>
                                    <th>DESCRIPCIÓN</th>
                                    <th>CALIFICACIÓN</th>
                                    <th>ESTADO</th>
                                    <th>INSPECTOR</th>
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
// FUNCIONES PARA APIs DE CONSULTA DNI/RUC
document.addEventListener('DOMContentLoaded', function() {
    // Prevenir envío tradicional del formulario
    const formNuevaActa = document.getElementById('form-nueva-acta');
    if (formNuevaActa) {
        formNuevaActa.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('🛑 Envío tradicional del formulario bloqueado - usando JavaScript');
            return false;
        });
    }
    
    // API para consulta de RUC/DNI único
    const rucDniInput = document.getElementById('ruc_dni');
    const razonSocialInput = document.getElementById('razon_social');
    const loadingData = document.getElementById('loading-data');
    
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
    
    // Función para consultar DNI en RENIEC (con API de Decolecta como principal)
    async function consultarDNI(dni) {
        try {
            loadingData.style.display = 'block';
            razonSocialInput.value = '';
            
            // Lista de APIs a probar en orden (API ultra-robusta como principal)
            const apis = [
                // API ULTRA-ROBUSTA PRINCIPAL - Garantiza JSON válido siempre
                {
                    url: `/api/api-dni-ultra.php?dni=${dni}`,
                    headers: {},
                    process: (data) => {
                        console.log('Respuesta API DNI Ultra:', data);
                        if (data && data.success && data.nombre_completo) {
                            // Almacenar datos adicionales para uso posterior
                            window.ultimaConsultaDNI = {
                                dni: data.dni,
                                nombre_completo: data.nombre_completo,
                                nombres: data.nombres,
                                apellido_paterno: data.apellido_paterno,
                                apellido_materno: data.apellido_materno,
                                fuente: data.fuente
                            };
                            return data.nombre_completo;
                        }
                        return null;
                    }
                },
                // API HÍBRIDA PRINCIPAL - APISPERU + Local como fallback
                {
                    url: `/api/api-dni-hibrido.php?dni=${dni}`,
                    headers: {},
                    process: (data) => {
                        console.log('Respuesta API DNI Híbrida:', data);
                        if (data && data.success && data.nombre_completo) {
                            // Almacenar datos adicionales para uso posterior
                            window.ultimaConsultaDNI = {
                                dni: data.dni,
                                nombre_completo: data.nombre_completo,
                                nombres: data.nombres,
                                apellido_paterno: data.apellido_paterno,
                                apellido_materno: data.apellido_materno,
                                departamento: data.departamento,
                                provincia: data.provincia,
                                distrito: data.distrito,
                                direccion: data.direccion,
                                fecha_nacimiento: data.fecha_nacimiento,
                                estado_civil: data.estado_civil,
                                fuente: data.fuente
                            };
                            return data.nombre_completo;
                        }
                        return null;
                    }
                },
                // API LOCAL PRINCIPAL - Siempre disponible
                {
                    url: `/api/api-dni-local.php?dni=${dni}`,
                    headers: {},
                    process: (data) => {
                        console.log('Respuesta API DNI Local:', data);
                        if (data && data.success && data.nombre_completo) {
                            // Almacenar datos adicionales para uso posterior
                            window.ultimaConsultaDNI = {
                                dni: data.dni,
                                nombre_completo: data.nombre_completo,
                                nombres: data.nombres,
                                apellido_paterno: data.apellido_paterno,
                                apellido_materno: data.apellido_materno,
                                departamento: data.departamento,
                                provincia: data.provincia,
                                distrito: data.distrito,
                                direccion: data.direccion,
                                fecha_nacimiento: data.fecha_nacimiento,
                                estado_civil: data.estado_civil,
                                fuente: data.fuente
                            };
                            return data.nombre_completo;
                        }
                        return null;
                    }
                },
                // API proxy PHP local (respaldo externo)
                {
                    url: `/api/test-api-dni.php?dni=${dni}`,
                    headers: {},
                    process: (data) => {
                        console.log('Respuesta API DNI Proxy:', data);
                        if (data && data.success && data.nombre_completo) {
                            return data.nombre_completo;
                        }
                        return null;
                    }
                },
                // API de APISPERU.com directa (sin token)
                {
                    url: `https://dniruc.apisperu.com/api/v1/dni/${dni}`,
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    process: (data) => {
                        console.log('Respuesta APISPERU DNI Directa:', data);
                        if (data && data.dni && data.nombres) {
                            const nombreCompleto = `${data.nombres} ${data.apellidoPaterno || ''} ${data.apellidoMaterno || ''}`.trim();
                            
                            // Almacenar datos adicionales
                            window.ultimaConsultaDNI = {
                                dni: data.dni,
                                nombre_completo: nombreCompleto,
                                nombres: data.nombres,
                                apellido_paterno: data.apellidoPaterno,
                                apellido_materno: data.apellidoMaterno,
                                cod_verifica: data.codVerifica,
                                fuente: 'APISPERU.com Directa'
                            };
                            
                            return nombreCompleto;
                        }
                        return null;
                    }
                },
                // API de Factiliza directa
                {
                    url: `https://api.factiliza.com/v1/dni/info/${dni}`,
                    headers: {
                        'Authorization': 'Bearer apis-token-1.aTSI1U7KEuT-6bbbCguH-4Y8TI6KS73N',
                        'Content-Type': 'application/json'
                    },
                    process: (data) => {
                        console.log('Respuesta Factiliza:', data);
                        if (data && data.success && data.data) {
                            const d = data.data;
                            // Usar el nombre_completo si está disponible, sino construirlo
                            let nombreCompleto = d.nombre_completo || '';
                            if (!nombreCompleto && d.nombres) {
                                nombreCompleto = `${d.nombres} ${d.apellido_paterno || ''} ${d.apellido_materno || ''}`.trim();
                            }
                            
                            // Almacenar datos adicionales para uso posterior
                            window.ultimaConsultaDNI = {
                                dni: d.numero,
                                nombre_completo: nombreCompleto,
                                nombres: d.nombres,
                                apellido_paterno: d.apellido_paterno,
                                apellido_materno: d.apellido_materno,
                                departamento: d.departamento,
                                provincia: d.provincia,
                                distrito: d.distrito,
                                direccion: d.direccion,
                                direccion_completa: d.direccion_completa,
                                ubigeo: d.ubigeo_reniec,
                                fecha_nacimiento: d.fecha_nacimiento,
                                sexo: d.sexo
                            };
                            
                            return nombreCompleto;
                        }
                        return null;
                    }
                },
                // API de Decolecta como respaldo
                {
                    url: `https://api.decolecta.com/v1/reniec/dni?numero=${dni}`,
                    headers: {
                        'Referer': 'https://apis.net.pe/consulta-dni-api',
                        'Authorization': 'Bearer apis-token-1.aTSI1U7KEuT-6bbbCguH-4Y8TI6KS73N'
                    },
                    process: (data) => {
                        console.log('Respuesta Decolecta:', data);
                        if (data && data.data && data.data.nombre_completo) {
                            return data.data.nombre_completo;
                        } else if (data && data.nombres) {
                            return `${data.nombres} ${data.apellido_paterno || ''} ${data.apellido_materno || ''}`.trim();
                        }
                        return null;
                    }
                },
                // API de APISPERU.com (sin token - gratis)
                {
                    url: `https://dniruc.apisperu.com/api/v1/dni/${dni}`,
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    process: (data) => {
                        console.log('Respuesta APISPERU DNI:', data);
                        if (data && data.dni && data.nombres) {
                            const nombreCompleto = `${data.nombres} ${data.apellidoPaterno || ''} ${data.apellidoMaterno || ''}`.trim();
                            
                            // Almacenar datos adicionales
                            window.ultimaConsultaDNI = {
                                dni: data.dni,
                                nombre_completo: nombreCompleto,
                                nombres: data.nombres,
                                apellido_paterno: data.apellidoPaterno,
                                apellido_materno: data.apellidoMaterno,
                                cod_verifica: data.codVerifica,
                                fuente: 'APISPERU.com'
                            };
                            
                            return nombreCompleto;
                        }
                        return null;
                    }
                },
                // API de respaldo con token (deprecada)
                {
                    url: `https://dniruc.apisperu.com/api/v1/dni/${dni}?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6InRlc3RAdGVzdC5jb20ifQ.bb2doqtI_pKcqT3TsCtm9-lFfwHJUkkrOkF_a1r7jW4`,
                    headers: {},
                    process: (data) => {
                        if (data && data.success && data.nombres) {
                            return `${data.nombres} ${data.apellidoPaterno || ''} ${data.apellidoMaterno || ''}`.trim();
                        }
                        return null;
                    }
                }
            ];
            
            let nombreCompleto = null;
            let apiUsada = '';
            
            // Intentar con cada API hasta encontrar una que funcione
            for (const api of apis) {
                try {
                    console.log(`Intentando API DNI: ${api.url}`);
                    
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
                    
                    nombreCompleto = api.process(data);
                    if (nombreCompleto) {
                        apiUsada = api.url.includes('api-dni-ultra.php') ? 'API Ultra-Robusta DRTC' :
                                  api.url.includes('api-dni-hibrido.php') ? 'API Híbrida APISPERU+Local' :
                                  api.url.includes('api/api-dni-local.php') ? 'Base de Datos Local RENIEC-DRTC' :
                                  api.url.includes('api/test-api-dni.php') ? 'API Proxy (Factiliza)' :
                                  api.url.includes('dniruc.apisperu.com') ? 'APISPERU.com (Oficial)' :
                                  api.url.includes('factiliza') ? 'Factiliza (Oficial)' :
                                  api.url.includes('decolecta') ? 'Decolecta (Respaldo)' : 
                                  'API Externa';
                        break;
                    }
                } catch (apiError) {
                    console.log(`Error con API ${api.url}:`, apiError);
                    continue;
                }
            }
            
            if (nombreCompleto) {
                razonSocialInput.value = nombreCompleto;
                razonSocialInput.style.backgroundColor = '#d4edda';
                razonSocialInput.style.borderColor = '#28a745';
                razonSocialInput.title = `Datos obtenidos de: ${apiUsada}`;
                
                // También completar el nombre del conductor si es persona natural
                const nombreConductorInput = document.querySelector('input[name="nombre_conductor_1"]');
                if (nombreConductorInput) {
                    nombreConductorInput.value = nombreCompleto;
                    nombreConductorInput.style.backgroundColor = '#e2f3ff';
                    nombreConductorInput.title = 'Autocompletado desde DNI del operador';
                }
                
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
                razonSocialInput.placeholder = 'DNI no encontrado - Ingrese el nombre manualmente';
                razonSocialInput.style.backgroundColor = '#fff3cd';
                razonSocialInput.style.borderColor = '#ffc107';
                razonSocialInput.focus();
                
                // Mostrar mensaje informativo
                const infoData = document.getElementById('info-data');
                if (infoData) {
                    infoData.innerHTML = '<i class="fas fa-exclamation-triangle text-warning me-1"></i>DNI no encontrado - Complete manualmente';
                    setTimeout(() => {
                        infoData.innerHTML = '<i class="fas fa-info-circle me-1"></i>RUC: 11 dígitos | DNI: 8 dígitos';
                    }, 5000);
                }
            }
        } catch (error) {
            console.error('Error general consultando DNI:', error);
            razonSocialInput.value = '';
            razonSocialInput.placeholder = 'Error de conexión - Ingrese el nombre manualmente';
            razonSocialInput.style.backgroundColor = '#fff3cd';
            razonSocialInput.style.borderColor = '#ffc107';
            razonSocialInput.focus();
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
                alert('Ingrese un DNI (8 dígitos) o RUC (11 dígitos) válido');
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

// FUNCIONES PARA MODALES FLOTANTES (código existente)
let tiempoInicioRegistro = null;
let actaIdEnProceso = null;
let autoguardadoInterval = null;

// Función para abrir modales
function abrirModal(modalId) {
    document.getElementById(modalId).style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    // Auto-llenar campos de fecha y hora en modal nueva acta
    if (modalId === 'modal-nueva-acta') {
        iniciarRegistroAutomatico();
        cargarProximoNumeroActa();
        
        // Asegurar que el inspector responsable esté lleno
        const inspectorField = document.getElementById('inspector_responsable');
        if (inspectorField) {
            inspectorField.value = '{{ Auth::user()->name }}';
        }
    }
}

// Función para cargar el próximo número de acta automáticamente
function cargarProximoNumeroActa() {
    fetch('/fiscalizador/actas-proximo-numero', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            const numeroDisplay = document.getElementById('numero_acta_display');
            const numeroHidden = document.getElementById('numero_acta_hidden');
            if (numeroDisplay && numeroHidden) {
                // Mostrar solo el número secuencial (ej: 000451)
                numeroDisplay.textContent = result.solo_numero;
                numeroDisplay.title = `Número completo: ${result.numero}`;
                // Guardar el número completo en el campo oculto para el formulario
                numeroHidden.value = result.numero;
            }
        }
    })
    .catch(error => {
        console.error('Error al cargar número de acta:', error);
        const numeroDisplay = document.getElementById('numero_acta_display');
        const numeroHidden = document.getElementById('numero_acta_hidden');
        if (numeroDisplay && numeroHidden) {
            numeroDisplay.textContent = '000001';
            numeroDisplay.title = 'Error al cargar número automático';
            numeroHidden.value = 'DRTC-APU-' + new Date().getFullYear() + '-000001';
        }
    });
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
function iniciarAutoguardado() {
    // Limpiar interval anterior si existe
    if (autoguardadoInterval) {
        clearInterval(autoguardadoInterval);
    }
    
    // Guardar progreso cada 30 segundos
    autoguardadoInterval = setInterval(() => {
        if (actaIdEnProceso) {
            guardarProgresoAutomatico();
        }
    }, 30000);
}

// Función para guardar progreso automáticamente
function guardarProgresoAutomatico() {
    if (!actaIdEnProceso) return;
    
    const formData = new FormData(document.getElementById('form-nueva-acta'));
    const data = Object.fromEntries(formData.entries());
    
    fetch(`/api/actas/${actaIdEnProceso}/progreso`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            mostrarAutoguardadoExitoso(result.hora_actualizacion);
        }
    })
    .catch(error => {
        console.error('Error en autoguardado:', error);
        mostrarErrorAutoguardado();
    });
}

// Función para mostrar autoguardado exitoso
function mostrarAutoguardadoExitoso(hora) {
    const status = document.getElementById('autoguardado-status');
    if (status) {
        status.innerHTML = `<i class="fas fa-check-circle text-success"></i> Autoguardado: ${hora}`;
        status.className = 'text-success ms-3';
    }
}

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
        alert('No hay un acta en proceso para finalizar');
        return;
    }
    
    fetch(`/api/actas/${actaIdEnProceso}/finalizar`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert(`Acta finalizada exitosamente.\nTiempo total: ${result.tiempo_total}\nHora de finalización: ${result.hora_finalizacion}`);
            limpiarRegistroTiempo();
            cerrarModal('modal-nueva-acta');
        }
    })
    .catch(error => {
        console.error('Error al finalizar:', error);
        alert('Error al finalizar el registro del acta');
    });
}

// Función para limpiar el registro de tiempo
function limpiarRegistroTiempo() {
    tiempoInicioRegistro = null;
    actaIdEnProceso = null;
    if (autoguardadoInterval) {
        clearInterval(autoguardadoInterval);
        autoguardadoInterval = null;
    }
    
    // Limpiar elementos de tiempo del DOM
    const tiempoInfo = document.getElementById('tiempo-registro-info');
    if (tiempoInfo) {
        tiempoInfo.remove();
    }
}

// Función para cerrar modales
function cerrarModal(modalId) {
    // Si es el modal de nueva acta y hay un registro en proceso, preguntar si desea finalizar
    if (modalId === 'modal-nueva-acta' && actaIdEnProceso) {
        const confirmar = confirm('¿Desea finalizar el registro del acta antes de cerrar?\n\nSi cierra sin finalizar, el acta quedará como borrador y podrá continuar más tarde.');
        
        if (confirmar) {
            finalizarRegistroActa();
            return; // La función de finalizar se encargará de cerrar el modal
        } else {
            // Guardar como borrador
            guardarProgresoAutomatico();
        }
    }
    
    document.getElementById(modalId).style.display = 'none';
    document.body.style.overflow = 'auto';
    
    // Limpiar tiempo si es nueva acta
    if (modalId === 'modal-nueva-acta') {
        limpiarRegistroTiempo();
    }
}

// Cerrar modal al hacer clic fuera
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('floating-modal')) {
        cerrarModal(e.target.id);
    }
});

// FUNCIONES ESPECÍFICAS PARA CADA MODAL

// Modal Editar Acta
function buscarActaEditar() {
    const criterio = document.getElementById('buscar-editar').value.trim();
    if (!criterio) {
        alert('Por favor ingrese un criterio de búsqueda');
        return;
    }
    
    // Simulación de búsqueda
    document.getElementById('acta-numero-editar').textContent = 'DRTC-APU-2024-001';
    document.getElementById('resultado-editar').style.display = 'block';
    
    // Aquí se haría la llamada AJAX real para buscar el acta
    console.log('Buscando acta con criterio:', criterio);
}

// Modal Eliminar Acta
function buscarActaEliminar() {
    const criterio = document.getElementById('buscar-eliminar').value.trim();
    if (!criterio) {
        alert('Por favor ingrese un criterio de búsqueda');
        return;
    }
    
    // Simulación de búsqueda
    document.getElementById('eliminar-numero-acta').textContent = 'DRTC-APU-2024-001';
    document.getElementById('eliminar-fecha-acta').textContent = '15/08/2024';
    document.getElementById('resultado-eliminar').style.display = 'block';
    
    // Aquí se haría la llamada AJAX real para buscar el acta
    console.log('Buscando acta para eliminar con criterio:', criterio);
}

function confirmarEliminacion() {
    const motivo = document.getElementById('motivo-eliminacion').value;
    const codigo = document.getElementById('codigo-autorizacion').value;
    const supervisor = document.getElementById('supervisor-autorizante').value;
    
    if (!motivo || !codigo || !supervisor) {
        alert('Todos los campos son obligatorios para la eliminación');
        return;
    }
    
    if (confirm('¿Está seguro de que desea eliminar esta acta? Esta acción es IRREVERSIBLE.')) {
        // Aquí se haría la llamada AJAX para eliminar
        alert('Acta eliminada exitosamente');
        cerrarModal('modal-eliminar-acta');
    }
}

function cancelarEliminacion() {
    document.getElementById('resultado-eliminar').style.display = 'none';
    document.getElementById('buscar-eliminar').value = '';
}

// Función para cargar actas desde la base de datos
function cargarActas() {
    fetch('/fiscalizador/actas-consultas', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            actualizarTablaActas(result.actas);
            actualizarEstadisticas(result.estadisticas);
        }
    })
    .catch(error => {
        console.error('Error al cargar actas:', error);
    });
}

// Función para consultar actas con filtros
function consultarActas() {
    const formData = new FormData(document.getElementById('form-consultas'));
    const params = new URLSearchParams();
    
    for (let [key, value] of formData.entries()) {
        if (value) {
            params.append(key, value);
        }
    }
    
    Swal.fire({
        title: 'Consultando...',
        text: 'Buscando actas en la base de datos',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch('/fiscalizador/actas-consultas?' + params.toString(), {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(result => {
        Swal.close();
        
        if (result.success) {
            actualizarTablaActas(result.actas);
            actualizarEstadisticas(result.estadisticas);
            
            // Mostrar resumen
            document.getElementById('resumen-consulta').style.display = 'block';
            
            Swal.fire({
                icon: 'success',
                title: 'Consulta Realizada',
                text: `Se encontraron ${result.total} actas que coinciden con los filtros.`,
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error en la Consulta',
                text: result.message || 'Error al consultar las actas'
            });
        }
    })
    .catch(error => {
        Swal.close();
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de Conexión',
            text: 'No se pudo realizar la consulta'
        });
    });
}

// Función para exportar a Excel
function exportarExcel() {
    const formData = new FormData(document.getElementById('form-consultas'));
    const params = new URLSearchParams();
    
    for (let [key, value] of formData.entries()) {
        if (value) {
            params.append(key, value);
        }
    }
    
    Swal.fire({
        title: 'Generando Excel...',
        text: 'Preparando archivo para descarga',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch('/fiscalizador/actas-exportar?' + params.toString(), {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(result => {
        Swal.close();
        
        if (result.success) {
            // Crear y descargar archivo CSV/Excel
            const csvContent = convertirACSV(result.actas);
            const blob = new Blob(['\ufeff' + csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = result.filename || 'Actas_DRTC.csv';
            link.click();
            
            Swal.fire({
                icon: 'success',
                title: 'Excel Generado',
                text: `Se descargó el archivo con ${result.actas.length} actas`,
                timer: 3000,
                showConfirmButton: false
            });
        }
    })
    .catch(error => {
        Swal.close();
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error al Exportar',
            text: 'No se pudo generar el archivo Excel'
        });
    });
}

// Función para convertir datos a CSV
function convertirACSV(actas) {
    const headers = [
        'N° ACTA', 'FECHA', 'LUGAR', 'OPERADOR/CONDUCTOR', 'RUC/DNI', 'PLACA', 
        'DESCRIPCIÓN', 'CALIFICACIÓN', 'ESTADO', 'INSPECTOR'
    ];
    
    const rows = actas.map(acta => [
        acta.numero_acta,
        new Date(acta.fecha_intervencion).toLocaleDateString('es-PE'),
        acta.lugar_intervencion,
        acta.razon_social,
        acta.ruc_dni,
        acta.placa,
        acta.descripcion_hechos.replace(/[",\n\r]/g, ' '),
        acta.calificacion,
        getEstadoText(acta.estado),
        acta.inspector_responsable
    ]);
    
    const csvContent = [headers, ...rows]
        .map(row => row.map(cell => `"${cell || ''}"`).join(','))
        .join('\n');
    
    return csvContent;
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

// Funciones auxiliares para la tabla y estados
function actualizarTablaActas(actas) {
    const tbody = document.getElementById('tbody-resultados');
    if (!tbody) return;
    
    if (actas.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center text-muted py-4">
                    <i class="fas fa-search me-2"></i>
                    No se encontraron actas con los filtros aplicados
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = actas.map(acta => `
        <tr>
            <td class="fw-bold">${acta.numero_acta}</td>
            <td>${new Date(acta.fecha_intervencion).toLocaleDateString('es-PE')}</td>
            <td>${acta.razon_social}</td>
            <td>${acta.ruc_dni}</td>
            <td>${acta.placa}</td>
            <td>${acta.descripcion_hechos.substring(0, 50)}...</td>
            <td><span class="badge bg-${getBadgeColor(acta.calificacion)}">${acta.calificacion}</span></td>
            <td><span class="badge bg-${getEstadoColor(acta.estado)}">${getEstadoText(acta.estado)}</span></td>
            <td>${acta.inspector_responsable}</td>
        </tr>
    `).join('');
}

function actualizarEstadisticas(stats) {
    if (document.getElementById('total-actas')) {
        document.getElementById('total-actas').textContent = stats.total_actas || 0;
        document.getElementById('actas-procesadas-modal').textContent = stats.procesadas || 0;
        document.getElementById('actas-pendientes-modal').textContent = stats.pendientes || 0;
        document.getElementById('actas-anuladas-modal').textContent = stats.anuladas || 0;
    }
}

function getBadgeColor(calificacion) {
    switch(calificacion) {
        case 'Leve': return 'warning';
        case 'Grave': return 'danger';
        case 'Muy Grave': return 'dark';
        default: return 'secondary';
    }
}

function getEstadoColor(estado) {
    switch(estado) {
        case 'pendiente': return 'warning';
        case 'procesada': return 'success';
        case 'anulada': return 'danger';
        case 'pagada': return 'info';
        default: return 'secondary';
    }
}

function getEstadoText(estado) {
    switch(estado) {
        case 'pendiente': return 'Pendiente';
        case 'procesada': return 'Procesada';
        case 'anulada': return 'Anulada';
        case 'pagada': return 'Pagada';
        default: return estado;
    }
}

// Funciones para gestión de actas en la tabla
function verDetalleActa(id) {
    fetch(`/fiscalizador/actas/${id}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.acta) {
            // Mostrar modal con detalles del acta
            mostrarDetalleActa(data.acta);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Error al cargar los detalles del acta', 'error');
    });
}

function editarActa(id) {
    fetch(`/fiscalizador/actas/${id}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.acta) {
            // Llenar el formulario de edición con los datos del acta
            llenarFormularioEdicion(data.acta);
            abrirModal('modal-nueva-acta');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Error al cargar los datos del acta', 'error');
    });
}

function imprimirActa(id) {
    Swal.fire({
        title: 'Imprimir Acta',
        text: '¿Deseas generar el PDF del acta?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ff8c00',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, generar PDF',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Aquí implementarías la generación del PDF
            window.open(`/fiscalizador/actas/${id}/pdf`, '_blank');
        }
    });
}

function anularActa(id) {
    Swal.fire({
        title: '¿Anular esta acta?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, anular',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/fiscalizador/actas/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Anulada', data.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message || 'Error al anular el acta', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Error al anular el acta', 'error');
            });
        }
    });
}

function mostrarDetalleActa(acta) {
    const detalleHtml = `
        <div class="row">
            <div class="col-md-6">
                <p><strong>Número:</strong> ${acta.numero_acta}</p>
                <p><strong>Fecha:</strong> ${new Date(acta.fecha_intervencion).toLocaleDateString('es-PE')}</p>
                <p><strong>Hora:</strong> ${acta.hora_intervencion}</p>
                <p><strong>Lugar:</strong> ${acta.lugar_intervencion}</p>
                <p><strong>Inspector:</strong> ${acta.inspector_responsable}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Placa:</strong> ${acta.placa}</p>
                <p><strong>Conductor:</strong> ${acta.nombre_conductor || 'N/A'}</p>
                <p><strong>Razón Social:</strong> ${acta.razon_social}</p>
                <p><strong>RUC/DNI:</strong> ${acta.ruc_dni}</p>
                <p><strong>Estado:</strong> <span class="badge bg-${getEstadoColor(acta.estado)}">${getEstadoText(acta.estado)}</span></p>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <p><strong>Descripción de los hechos:</strong></p>
                <p>${acta.descripcion_hechos || 'Sin descripción'}</p>
                <p><strong>Calificación:</strong> ${acta.calificacion || 'No especificada'}</p>
                <p><strong>Sanción:</strong> ${acta.sancion ? 'S/ ' + parseFloat(acta.sancion).toFixed(2) : 'Sin sanción'}</p>
                ${acta.observaciones_inspector ? `<p><strong>Observaciones:</strong> ${acta.observaciones_inspector}</p>` : ''}
            </div>
        </div>
    `;

    Swal.fire({
        title: 'Detalle del Acta',
        html: detalleHtml,
        width: '800px',
        showConfirmButton: true,
        confirmButtonText: 'Cerrar',
        confirmButtonColor: '#ff8c00'
    });
}

function llenarFormularioEdicion(acta) {
    // Llenar todos los campos del formulario con los datos del acta
    document.getElementById('lugar_intervencion').value = acta.lugar_intervencion || '';
    document.getElementById('fecha_intervencion').value = acta.fecha_intervencion || '';
    document.getElementById('hora_intervencion').value = acta.hora_intervencion || '';
    document.getElementById('inspector_responsable').value = acta.inspector_responsable || '';
    document.getElementById('tipo_servicio').value = acta.tipo_servicio || '';
    document.getElementById('tipo_agente').value = acta.tipo_agente || '';
    document.getElementById('placa').value = acta.placa || '';
    document.getElementById('razon_social').value = acta.razon_social || '';
    document.getElementById('ruc_dni').value = acta.ruc_dni || '';
    document.getElementById('nombre_conductor').value = acta.nombre_conductor || '';
    document.getElementById('licencia').value = acta.licencia || '';
    document.getElementById('clase_licencia').value = acta.clase_licencia || '';
    document.getElementById('origen').value = acta.origen || '';
    document.getElementById('destino').value = acta.destino || '';
    document.getElementById('numero_personas').value = acta.numero_personas || '';
    document.getElementById('descripcion_hechos').value = acta.descripcion_hechos || '';
    document.getElementById('medios_probatorios').value = acta.medios_probatorios || '';
    document.getElementById('calificacion').value = acta.calificacion || '';
    document.getElementById('medida_administrativa').value = acta.medida_administrativa || '';
    document.getElementById('sancion').value = acta.sancion || '';
    document.getElementById('observaciones_intervenido').value = acta.observaciones_intervenido || '';
    document.getElementById('observaciones_inspector').value = acta.observaciones_inspector || '';
    
    // Mostrar el número del acta en modo edición
    const numeroDisplay = document.getElementById('numero_acta_display');
    const numeroHidden = document.getElementById('numero_acta_hidden');
    if (numeroDisplay && numeroHidden) {
        const soloNumero = acta.numero_acta.split('-')[3];
        numeroDisplay.textContent = soloNumero;
        numeroHidden.value = acta.numero_acta;
    }
    
    // Cambiar el título del modal y botón para indicar edición
    document.querySelector('#modal-nueva-acta .modal-title').textContent = 'Editar Acta de Control';
    document.querySelector('#modal-nueva-acta .btn-primary').textContent = 'Actualizar Acta';
    
    // Guardar el ID del acta para la actualización
    document.getElementById('acta-form').setAttribute('data-acta-id', acta.id);
}

// Funciones simplificadas para búsqueda
function buscarActas() {
    const buscar = document.getElementById('buscar_general').value;
    const estado = document.getElementById('filtro_estado').value;
    const fecha = document.getElementById('filtro_fecha').value;

    // Mostrar indicador de carga
    mostrarCargando(true);

    // Crear objeto con parámetros de búsqueda
    const params = new URLSearchParams();
    if (buscar.trim()) params.append('buscar', buscar.trim());
    if (estado) params.append('estado', estado);
    if (fecha) params.append('fecha', fecha);

    // Realizar búsqueda AJAX
    fetch(`/fiscalizador/actas-consultas?${params.toString()}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            actualizarTablaActas(data.actas);
            actualizarEstadisticas(data.estadisticas);
            mostrarResultadosBusqueda(data.actas.length, buscar);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Error al realizar la búsqueda', 'error');
    })
    .finally(() => {
        mostrarCargando(false);
    });
}

function mostrarCargando(mostrar) {
    const btnBuscar = document.querySelector('button[onclick="buscarActas()"]');
    if (mostrar) {
        btnBuscar.innerHTML = '<span class="loading-spinner me-1"></span>Buscando...';
        btnBuscar.disabled = true;
    } else {
        btnBuscar.innerHTML = '<i class="fas fa-search me-1"></i>Buscar';
        btnBuscar.disabled = false;
    }
}

function limpiarBusqueda() {
    document.getElementById('buscar_general').value = '';
    document.getElementById('filtro_estado').value = '';
    document.getElementById('filtro_fecha').value = '';
    
    // Recargar la página para mostrar todas las actas
    window.location.reload();
}

function exportarActas() {
    const buscar = document.getElementById('buscar_general').value;
    const estado = document.getElementById('filtro_estado').value;
    const fecha = document.getElementById('filtro_fecha').value;

    // Crear objeto con parámetros de búsqueda
    const params = new URLSearchParams();
    if (buscar.trim()) params.append('buscar', buscar.trim());
    if (estado) params.append('estado', estado);
    if (fecha) params.append('fecha', fecha);

    // Descargar archivo Excel
    fetch(`/fiscalizador/actas-exportar?${params.toString()}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Crear y descargar archivo CSV
            descargarCSV(data.actas, data.filename);
            Swal.fire({
                title: 'Exportación exitosa',
                text: `Se exportaron ${data.actas.length} actas`,
                icon: 'success',
                timer: 2000
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Error al exportar el archivo', 'error');
    });
}

function mostrarResultadosBusqueda(total, termino) {
    const alertContainer = document.querySelector('.alert-info');
    if (termino && termino.trim()) {
        alertContainer.innerHTML = `
            <i class="fas fa-search me-2"></i>
            <div>
                <strong>Resultados de búsqueda:</strong> Se encontraron ${total} acta(s) para "${termino}".
                <button class="btn btn-sm btn-outline-primary ms-2" onclick="limpiarBusqueda()">
                    <i class="fas fa-times me-1"></i>Ver todas
                </button>
            </div>
        `;
        alertContainer.className = 'alert alert-success d-flex align-items-center';
    } else {
        alertContainer.innerHTML = `
            <i class="fas fa-info-circle me-2"></i>
            <div>
                <strong>Instrucciones de búsqueda:</strong> Escribe el término de búsqueda y haz clic en "Buscar" o presiona Enter. 
                Puedes buscar por número de acta, DNI, licencia, placa o nombre.
            </div>
        `;
        alertContainer.className = 'alert alert-info d-flex align-items-center';
    }
}

function actualizarTablaActas(actas) {
    const tbody = document.querySelector('table tbody');
    
    if (actas.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-5">
                    <div class="d-flex flex-column align-items-center">
                        <i class="fas fa-search fa-4x text-muted mb-3" style="opacity: 0.3;"></i>
                        <h4 class="text-muted mb-2">No se encontraron resultados</h4>
                        <p class="text-muted mb-3">Intenta con otros términos de búsqueda o verifica la información.</p>
                        <div class="text-muted small">
                            <strong>Puedes buscar por:</strong><br>
                            • Número de acta (ej: DRTC-APU-2025-000001)<br>
                            • DNI/RUC (ej: 12345678)<br>
                            • Licencia de conducir<br>
                            • Placa del vehículo (ej: ABC-123)<br>
                            • Nombre del conductor o razón social
                        </div>
                        <button class="btn btn-outline-primary mt-3" onclick="limpiarBusqueda()">
                            <i class="fas fa-list me-1"></i>Ver todas las actas
                        </button>
                    </div>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = actas.map(acta => `
        <tr>
            <td><strong>${acta.numero_acta}</strong></td>
            <td>${formatearFechaHora(acta.fecha_intervencion, acta.hora_intervencion)}</td>
            <td><span class="badge bg-dark">${acta.placa}</span></td>
            <td>${acta.nombre_conductor || acta.razon_social}</td>
            <td>${acta.descripcion_hechos ? acta.descripcion_hechos.substring(0, 30) + '...' : 'Sin descripción'}</td>
            <td><strong>${acta.sancion ? 'S/ ' + parseFloat(acta.sancion).toFixed(2) : 'Sin sanción'}</strong></td>
            <td>${acta.created_at ? new Date(new Date(acta.created_at).getTime() + 15*24*60*60*1000).toLocaleDateString('es-PE') : 'N/A'}</td>
            <td><span class="badge bg-${getEstadoColor(acta.estado)}">${getEstadoText(acta.estado)}</span></td>
            <td>
                <button class="btn btn-sm btn-outline-primary" title="Ver detalle" onclick="verDetalleActa(${acta.id})">
                    <i class="fas fa-eye"></i>
                </button>
                ${acta.estado !== 'anulada' ? `
                    <button class="btn btn-sm btn-outline-success" title="Editar" onclick="editarActa(${acta.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-info" title="Imprimir" onclick="imprimirActa(${acta.id})">
                        <i class="fas fa-print"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" title="Anular" onclick="anularActa(${acta.id})">
                        <i class="fas fa-ban"></i>
                    </button>
                ` : `
                    <button class="btn btn-sm btn-outline-secondary" title="Acta anulada" disabled>
                        <i class="fas fa-ban"></i>
                    </button>
                `}
            </td>
        </tr>
    `).join('');
}

function actualizarEstadisticas(stats) {
    document.getElementById('count-pendientes').textContent = stats.pendientes || 0;
    document.getElementById('count-procesadas').textContent = stats.procesadas || 0;
    document.getElementById('count-anuladas').textContent = stats.anuladas || 0;
    document.getElementById('count-total').textContent = stats.total_actas || 0;
}

function formatearFechaHora(fecha, hora) {
    try {
        const fechaObj = new Date(fecha + ' ' + hora);
        return fechaObj.toLocaleDateString('es-PE') + ' ' + fechaObj.toLocaleTimeString('es-PE', {hour: '2-digit', minute: '2-digit'});
    } catch {
        return fecha + ' ' + hora;
    }
}

function descargarCSV(actas, filename) {
    const headers = ['Número Acta', 'Fecha/Hora', 'Placa', 'Conductor', 'Razón Social', 'RUC/DNI', 'Inspector', 'Estado', 'Sanción'];
    const csvContent = [
        headers.join(','),
        ...actas.map(acta => [
            acta.numero_acta,
            formatearFechaHora(acta.fecha_intervencion, acta.hora_intervencion),
            acta.placa,
            acta.nombre_conductor || '',
            acta.razon_social || '',
            acta.ruc_dni || '',
            acta.inspector_responsable || '',
            acta.estado,
            acta.sancion || ''
        ].join(','))
    ].join('\n');

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename.replace('.xlsx', '.csv');
    link.click();
}

// Event listeners para búsqueda manual solamente
document.addEventListener('DOMContentLoaded', function() {
    const buscarGeneral = document.getElementById('buscar_general');
    if (buscarGeneral) {
        // Solo búsqueda al presionar Enter (sin búsqueda automática)
        buscarGeneral.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                buscarActas();
            }
        });
    }
    
    const filtroEstado = document.getElementById('filtro_estado');
    if (filtroEstado) {
        // Solo cambiar cuando seleccione una opción, no búsqueda automática
        filtroEstado.addEventListener('change', function() {
            // Opcional: puedes comentar esta línea si no quieres que busque al cambiar estado
            // buscarActas();
        });
    }
    
    const filtroFecha = document.getElementById('filtro_fecha');
    if (filtroFecha) {
        // Solo cambiar cuando seleccione una fecha, no búsqueda automática
        filtroFecha.addEventListener('change', function() {
            // Opcional: puedes comentar esta línea si no quieres que busque al cambiar fecha
            // buscarActas();
        });
    }
    
    // Enfocar automáticamente el campo de búsqueda
    if (buscarGeneral) {
        buscarGeneral.focus();
    }
});
</script>
