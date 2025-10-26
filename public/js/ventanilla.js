/**
 * SISTEMA DE GESTIÓN - MÓDULO VENTANILLA DRTC
 * Panel completo para ventanilla con consultas de actas y funcionalidades DRTC
 */

console.log('🏢 Cargando módulo ventanilla DRTC...');

// Variables globales
let isVentanilla = false;
let actasData = [];
let currentPage = 1;
const itemsPerPage = 10;

// Inicialización del módulo ventanilla
document.addEventListener('DOMContentLoaded', function() {
    if (window.dashboardUserRole === 'ventanilla') {
        isVentanilla = true;
        console.log('✅ Módulo ventanilla DRTC habilitado para:', window.dashboardUserName);
        initializeVentanillaModule();
    }
});

function initializeVentanillaModule() {
    console.log('🚀 Inicializando módulo ventanilla DRTC...');
    loadDashboardStatsVentanilla();
}

// ==================== DASHBOARD STATS VENTANILLA ====================
async function loadDashboardStatsVentanilla() {
    try {
        const response = await fetch(`${window.location.origin}${window.location.pathname}?api=dashboard-stats`);
        const result = await response.json();
        
        if (result.success && result.stats) {
            updateDashboardStatsVentanilla(result.stats);
        }
    } catch (error) {
        console.error('❌ Error al cargar estadísticas de ventanilla:', error);
    }
}

function updateDashboardStatsVentanilla(stats) {
    if (document.getElementById('total-actas')) {
        document.getElementById('total-actas').textContent = stats.atenciones_hoy || 0;
    }
    if (document.getElementById('total-conductores')) {
        document.getElementById('total-conductores').textContent = stats.tramites_completados || 0;
    }
    if (document.getElementById('total-vehiculos')) {
        document.getElementById('total-vehiculos').textContent = stats.cola_espera || 0;
    }
    if (document.getElementById('total-notifications')) {
        document.getElementById('total-notifications').textContent = stats.tiempo_promedio || 15;
    }
}

// ==================== PANEL PRINCIPAL VENTANILLA ====================
function loadNuevaAtencion() {
    console.log('🆕 Cargando panel de nueva atención...');
    
    const content = `
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5><i class="fas fa-user-plus"></i> Nueva Atención al Cliente</h5>
                        </div>
                        <div class="card-body">
                            <form id="nuevaAtencionForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Tipo de Consulta</label>
                                            <select class="form-select" id="tipoConsulta" required>
                                                <option value="">Seleccionar...</option>
                                                <option value="consulta_acta">Consulta de Acta</option>
                                                <option value="estado_tramite">Estado de Trámite</option>
                                                <option value="informacion_general">Información General</option>
                                                <option value="reclamo">Reclamo</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Documento de Identidad</label>
                                            <input type="text" class="form-control" id="documentoCliente" placeholder="DNI/RUC">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Nombres y Apellidos</label>
                                            <input type="text" class="form-control" id="nombreCliente" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Teléfono</label>
                                            <input type="tel" class="form-control" id="telefonoCliente">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Descripción de la Consulta</label>
                                    <textarea class="form-control" id="descripcionConsulta" rows="3" required></textarea>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Registrar Atención
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="loadConsultasActas()">
                                        <i class="fas fa-search"></i> Consultar Actas
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    showContentInSection(content, 'Nueva Atención');
    
    // Event listener para el formulario
    document.getElementById('nuevaAtencionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        registrarNuevaAtencion();
    });
}

function registrarNuevaAtencion() {
    const formData = {
        tipo_consulta: document.getElementById('tipoConsulta').value,
        documento: document.getElementById('documentoCliente').value,
        nombre: document.getElementById('nombreCliente').value,
        telefono: document.getElementById('telefonoCliente').value,
        descripcion: document.getElementById('descripcionConsulta').value,
        fecha_atencion: new Date().toISOString(),
        atendido_por: window.dashboardUserName
    };
    
    // Simular registro exitoso
    showToast('Atención registrada correctamente', 'success');
    document.getElementById('nuevaAtencionForm').reset();
}

// ==================== CONSULTAS DE ACTAS ====================
function loadConsultasActas() {
    console.log('🔍 Cargando panel de consultas de actas...');
    
    const content = `
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5><i class="fas fa-search"></i> Consulta de Actas DRTC</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">Buscar por:</label>
                                    <select class="form-select" id="tipoBusqueda">
                                        <option value="documento">Documento (DNI/RUC)</option>
                                        <option value="placa">Placa de Vehículo</option>
                                        <option value="numero_acta">Número de Acta</option>
                                        <option value="conductor">Nombre del Conductor</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Término de búsqueda:</label>
                                    <input type="text" class="form-control" id="terminoBusqueda" placeholder="Ingrese el término a buscar">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <button class="btn btn-primary d-block w-100" onclick="buscarActas()">
                                        <i class="fas fa-search"></i> Buscar
                                    </button>
                                </div>
                            </div>
                            
                            <div id="resultadosActas">
                                <div class="text-center text-muted">
                                    <i class="fas fa-search fa-3x mb-3"></i>
                                    <p>Ingrese un término de búsqueda para consultar actas</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    showContentInSection(content, 'Consulta de Actas');
    
    // Event listener para búsqueda con Enter
    document.getElementById('terminoBusqueda').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            buscarActas();
        }
    });
}

async function buscarActas() {
    const tipoBusqueda = document.getElementById('tipoBusqueda').value;
    const termino = document.getElementById('terminoBusqueda').value.trim();
    
    if (!termino) {
        showToast('Por favor ingrese un término de búsqueda', 'warning');
        return;
    }
    
    const resultadosDiv = document.getElementById('resultadosActas');
    resultadosDiv.innerHTML = '<div class="text-center"><div class="spinner-border"></div><p>Buscando actas...</p></div>';
    
    try {
        const response = await fetch(`${window.location.origin}${window.location.pathname}?api=actas`);
        const result = await response.json();
        
        if (result.success && result.actas) {
            const actasFiltradas = filtrarActas(result.actas, tipoBusqueda, termino);
            mostrarResultadosActas(actasFiltradas);
        } else {
            resultadosDiv.innerHTML = '<div class="alert alert-warning">No se encontraron actas</div>';
        }
    } catch (error) {
        console.error('Error al buscar actas:', error);
        resultadosDiv.innerHTML = '<div class="alert alert-danger">Error al realizar la búsqueda</div>';
    }
}

function filtrarActas(actas, tipo, termino) {
    return actas.filter(acta => {
        switch (tipo) {
            case 'documento':
                return acta.ruc_dni && acta.ruc_dni.toLowerCase().includes(termino.toLowerCase());
            case 'placa':
                return acta.placa && acta.placa.toLowerCase().includes(termino.toLowerCase());
            case 'numero_acta':
                return acta.numero_acta && acta.numero_acta.toLowerCase().includes(termino.toLowerCase());
            case 'conductor':
                return acta.nombre_conductor && acta.nombre_conductor.toLowerCase().includes(termino.toLowerCase());
            default:
                return false;
        }
    });
}

function mostrarResultadosActas(actas) {
    const resultadosDiv = document.getElementById('resultadosActas');
    
    if (actas.length === 0) {
        resultadosDiv.innerHTML = '<div class="alert alert-info">No se encontraron actas con los criterios especificados</div>';
        return;
    }
    
    let html = `
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>N° Acta</th>
                        <th>Fecha</th>
                        <th>Placa</th>
                        <th>Conductor</th>
                        <th>Documento</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    actas.forEach(acta => {
        html += `
            <tr>
                <td><strong>${acta.numero_acta || 'N/A'}</strong></td>
                <td>${formatearFecha(acta.fecha_acta || acta.created_at)}</td>
                <td><span class="badge bg-primary">${acta.placa || 'N/A'}</span></td>
                <td>${acta.nombre_conductor || 'N/A'}</td>
                <td>${acta.ruc_dni || 'N/A'}</td>
                <td><span class="badge bg-warning">${acta.estado || 'Pendiente'}</span></td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="verDetalleActa(${acta.id})">
                        <i class="fas fa-eye"></i> Ver
                    </button>
                    <button class="btn btn-sm btn-success" onclick="imprimirActa(${acta.id})">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
                </td>
            </tr>
        `;
    });
    
    html += `
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            <p class="text-muted">Se encontraron ${actas.length} acta(s)</p>
        </div>
    `;
    
    resultadosDiv.innerHTML = html;
}

// ==================== MODALES Y DETALLES ====================
function verDetalleActa(actaId) {
    console.log('👁️ Viendo detalle de acta:', actaId);
    
    const modalContent = `
        <div class="row">
            <div class="col-md-6">
                <h6>Información del Acta</h6>
                <table class="table table-sm">
                    <tr><td><strong>N° Acta:</strong></td><td>ACT-2025-${String(actaId).padStart(4, '0')}</td></tr>
                    <tr><td><strong>Fecha:</strong></td><td>${new Date().toLocaleDateString()}</td></tr>
                    <tr><td><strong>Estado:</strong></td><td><span class="badge bg-warning">Pendiente</span></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Información del Vehículo</h6>
                <table class="table table-sm">
                    <tr><td><strong>Placa:</strong></td><td>ABC-123</td></tr>
                    <tr><td><strong>Conductor:</strong></td><td>Juan Pérez</td></tr>
                    <tr><td><strong>Documento:</strong></td><td>12345678</td></tr>
                </table>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <h6>Infracciones</h6>
                <div class="alert alert-warning">
                    <strong>F.1:</strong> Prestar servicio de transporte sin autorización
                </div>
            </div>
        </div>
    `;
    
    showModal('Detalle del Acta', modalContent, [
        { text: 'Imprimir', class: 'btn-primary', onclick: `imprimirActa(${actaId})` },
        { text: 'Cerrar', class: 'btn-secondary', dismiss: true }
    ]);
}

function imprimirActa(actaId) {
    console.log('🖨️ Imprimiendo acta:', actaId);
    showToast('Generando documento para impresión...', 'info');
    
    // Simular impresión
    setTimeout(() => {
        showToast('Documento enviado a la impresora', 'success');
    }, 2000);
}

// ==================== COLA DE ESPERA ====================
function loadColaEspera() {
    console.log('⏳ Cargando cola de espera...');
    
    const content = `
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5><i class="fas fa-hourglass-half"></i> Cola de Espera - Atención al Público</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <button class="btn btn-success me-2" onclick="llamarSiguiente()">
                                        <i class="fas fa-arrow-right"></i> Llamar Siguiente
                                    </button>
                                    <button class="btn btn-info me-2" onclick="agregarCola()">
                                        <i class="fas fa-plus"></i> Agregar a Cola
                                    </button>
                                    <button class="btn btn-secondary" onclick="actualizarCola()">
                                        <i class="fas fa-sync"></i> Actualizar
                                    </button>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="badge bg-primary fs-6">En espera: <span id="totalEspera">3</span></div>
                                </div>
                            </div>
                            
                            <div id="colaEsperaList">
                                <div class="list-group">
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Ticket #001 - María García</h6>
                                            <p class="mb-1">Consulta de acta - DNI: 12345678</p>
                                            <small>Hora: 09:15 AM</small>
                                        </div>
                                        <span class="badge bg-success rounded-pill">Siguiente</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Ticket #002 - Carlos López</h6>
                                            <p class="mb-1">Estado de trámite - RUC: 20123456789</p>
                                            <small>Hora: 09:30 AM</small>
                                        </div>
                                        <span class="badge bg-warning rounded-pill">Esperando</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Ticket #003 - Ana Rodríguez</h6>
                                            <p class="mb-1">Información general</p>
                                            <small>Hora: 09:45 AM</small>
                                        </div>
                                        <span class="badge bg-secondary rounded-pill">Esperando</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    showContentInSection(content, 'Cola de Espera');
}

function llamarSiguiente() {
    showToast('Llamando al siguiente cliente...', 'info');
    // Lógica para llamar al siguiente
}

function agregarCola() {
    const modalContent = `
        <form id="agregarColaForm">
            <div class="mb-3">
                <label class="form-label">Nombre del Cliente</label>
                <input type="text" class="form-control" id="nombreCola" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Documento</label>
                <input type="text" class="form-control" id="documentoCola" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Tipo de Consulta</label>
                <select class="form-select" id="tipoConsultaCola" required>
                    <option value="">Seleccionar...</option>
                    <option value="consulta_acta">Consulta de Acta</option>
                    <option value="estado_tramite">Estado de Trámite</option>
                    <option value="informacion_general">Información General</option>
                </select>
            </div>
        </form>
    `;
    
    showModal('Agregar Cliente a Cola', modalContent, [
        { text: 'Agregar', class: 'btn-primary', onclick: 'procesarAgregarCola()' },
        { text: 'Cancelar', class: 'btn-secondary', dismiss: true }
    ]);
}

function procesarAgregarCola() {
    showToast('Cliente agregado a la cola de espera', 'success');
    closeModal();
    actualizarCola();
}

function actualizarCola() {
    showToast('Cola de espera actualizada', 'info');
}

// ==================== CONSULTAS PÚBLICAS ====================
function loadConsultas() {
    console.log('❓ Cargando consultas públicas...');
    
    const content = `
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5><i class="fas fa-question-circle"></i> Consultas Públicas DRTC</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card border-primary">
                                        <div class="card-header bg-primary text-white">
                                            <h6><i class="fas fa-file-alt"></i> Consulta de Actas</h6>
                                        </div>
                                        <div class="card-body">
                                            <p>Consulte el estado de sus actas de infracción</p>
                                            <button class="btn btn-primary" onclick="loadConsultasActas()">
                                                <i class="fas fa-search"></i> Consultar Actas
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-info">
                                        <div class="card-header bg-info text-white">
                                            <h6><i class="fas fa-car"></i> Consulta de Vehículos</h6>
                                        </div>
                                        <div class="card-body">
                                            <p>Verifique el estado de registro de vehículos</p>
                                            <button class="btn btn-info" onclick="consultarVehiculos()">
                                                <i class="fas fa-search"></i> Consultar Vehículos
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="card border-warning">
                                        <div class="card-header bg-warning text-dark">
                                            <h6><i class="fas fa-id-card"></i> Consulta de Conductores</h6>
                                        </div>
                                        <div class="card-body">
                                            <p>Consulte información de conductores registrados</p>
                                            <button class="btn btn-warning" onclick="consultarConductores()">
                                                <i class="fas fa-search"></i> Consultar Conductores
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-success">
                                        <div class="card-header bg-success text-white">
                                            <h6><i class="fas fa-building"></i> Información DRTC</h6>
                                        </div>
                                        <div class="card-body">
                                            <p>Información general y procedimientos</p>
                                            <button class="btn btn-success" onclick="mostrarInfoDRTC()">
                                                <i class="fas fa-info-circle"></i> Ver Información
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
    `;
    
    showContentInSection(content, 'Consultas Públicas');
}

function consultarVehiculos() {
    showToast('Módulo de consulta de vehículos en desarrollo', 'info');
}

function consultarConductores() {
    showToast('Módulo de consulta de conductores en desarrollo', 'info');
}

function mostrarInfoDRTC() {
    const modalContent = `
        <div class="text-center mb-3">
            <i class="fas fa-building fa-3x text-primary"></i>
            <h4 class="mt-2">Dirección Regional de Transportes y Comunicaciones</h4>
        </div>
        <div class="row">
            <div class="col-md-6">
                <h6>Horarios de Atención:</h6>
                <ul>
                    <li>Lunes a Viernes: 8:00 AM - 4:30 PM</li>
                    <li>Sábados: 8:00 AM - 12:00 PM</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6>Servicios:</h6>
                <ul>
                    <li>Consulta de actas</li>
                    <li>Trámites vehiculares</li>
                    <li>Información general</li>
                </ul>
            </div>
        </div>
    `;
    
    showModal('Información DRTC', modalContent);
}

// ==================== TRÁMITES ====================
function loadTramites() {
    console.log('📁 Cargando gestión de trámites...');
    
    const content = `
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            <h5><i class="fas fa-folder-open"></i> Gestión de Trámites DRTC</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <button class="btn btn-primary w-100 mb-2" onclick="nuevoTramite()">
                                        <i class="fas fa-plus"></i> Nuevo Trámite
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-warning w-100 mb-2" onclick="tramitesPendientes()">
                                        <i class="fas fa-clock"></i> Pendientes
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-success w-100 mb-2" onclick="tramitesCompletados()">
                                        <i class="fas fa-check"></i> Completados
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-info w-100 mb-2" onclick="historialTramites()">
                                        <i class="fas fa-history"></i> Historial
                                    </button>
                                </div>
                            </div>
                            
                            <div id="tramitesContent">
                                <div class="text-center text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3"></i>
                                    <p>Seleccione una opción para gestionar trámites</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    showContentInSection(content, 'Gestión de Trámites');
}

function nuevoTramite() {
    const modalContent = `
        <form id="nuevoTramiteForm">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Tipo de Trámite</label>
                        <select class="form-select" id="tipoTramite" required>
                            <option value="">Seleccionar...</option>
                            <option value="licencia_conducir">Licencia de Conducir</option>
                            <option value="tarjeta_propiedad">Tarjeta de Propiedad</option>
                            <option value="revision_tecnica">Revisión Técnica</option>
                            <option value="permiso_circulacion">Permiso de Circulación</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Documento del Solicitante</label>
                        <input type="text" class="form-control" id="documentoTramite" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nombres y Apellidos</label>
                        <input type="text" class="form-control" id="nombreTramite" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="tel" class="form-control" id="telefonoTramite">
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Observaciones</label>
                <textarea class="form-control" id="observacionesTramite" rows="3"></textarea>
            </div>
        </form>
    `;
    
    showModal('Nuevo Trámite', modalContent, [
        { text: 'Registrar', class: 'btn-primary', onclick: 'procesarNuevoTramite()' },
        { text: 'Cancelar', class: 'btn-secondary', dismiss: true }
    ]);
}

function procesarNuevoTramite() {
    showToast('Trámite registrado correctamente', 'success');
    closeModal();
}

function tramitesPendientes() {
    document.getElementById('tramitesContent').innerHTML = `
        <h6>Trámites Pendientes</h6>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>N° Trámite</th>
                        <th>Tipo</th>
                        <th>Solicitante</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>TR-001</td>
                        <td>Licencia de Conducir</td>
                        <td>Juan Pérez</td>
                        <td>15/01/2025</td>
                        <td><span class="badge bg-warning">Pendiente</span></td>
                        <td>
                            <button class="btn btn-sm btn-success">Procesar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    `;
}

function tramitesCompletados() {
    document.getElementById('tramitesContent').innerHTML = `
        <h6>Trámites Completados</h6>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> No hay trámites completados hoy
        </div>
    `;
}

function historialTramites() {
    document.getElementById('tramitesContent').innerHTML = `
        <h6>Historial de Trámites</h6>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Historial disponible próximamente
        </div>
    `;
}

// ==================== UTILIDADES ====================
function showContentInSection(content, title) {
    // Ocultar todas las secciones
    document.querySelectorAll('.content-section').forEach(section => {
        section.classList.remove('active');
    });
    
    // Crear o actualizar sección
    let section = document.getElementById('ventanilla-section');
    if (!section) {
        section = document.createElement('div');
        section.id = 'ventanilla-section';
        section.className = 'content-section';
        document.getElementById('contentContainer').appendChild(section);
    }
    
    section.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-user-tie"></i> ${title}</h2>
        </div>
        ${content}
    `;
    
    section.classList.add('active');
}

function formatearFecha(fecha) {
    if (!fecha) return 'N/A';
    return new Date(fecha).toLocaleDateString('es-PE');
}

function showModal(title, content, buttons = []) {
    const modal = document.getElementById('generalModal');
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalBody').innerHTML = content;
    
    const footer = document.getElementById('modalFooter');
    footer.innerHTML = '';
    
    if (buttons.length === 0) {
        buttons = [{ text: 'Cerrar', class: 'btn-secondary', dismiss: true }];
    }
    
    buttons.forEach(btn => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = `btn ${btn.class}`;
        button.textContent = btn.text;
        
        if (btn.dismiss) {
            button.setAttribute('data-bs-dismiss', 'modal');
        }
        if (btn.onclick) {
            button.setAttribute('onclick', btn.onclick);
        }
        
        footer.appendChild(button);
    });
    
    new bootstrap.Modal(modal).show();
}

function closeModal() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('generalModal'));
    if (modal) modal.hide();
}

function showToast(message, type = 'info') {
    if (typeof window.showToast === 'function') {
        window.showToast(message, type);
    } else {
        alert(message);
    }
}

// ==================== EXPORTAR FUNCIONES ====================
window.loadNuevaAtencion = loadNuevaAtencion;
window.loadColaEspera = loadColaEspera;
window.loadConsultas = loadConsultas;
window.loadTramites = loadTramites;
window.loadConsultasActas = loadConsultasActas;
window.buscarActas = buscarActas;
window.verDetalleActa = verDetalleActa;
window.imprimirActa = imprimirActa;
window.llamarSiguiente = llamarSiguiente;
window.agregarCola = agregarCola;
window.procesarAgregarCola = procesarAgregarCola;
window.actualizarCola = actualizarCola;
window.consultarVehiculos = consultarVehiculos;
window.consultarConductores = consultarConductores;
window.mostrarInfoDRTC = mostrarInfoDRTC;
window.nuevoTramite = nuevoTramite;
window.procesarNuevoTramite = procesarNuevoTramite;
window.tramitesPendientes = tramitesPendientes;
window.tramitesCompletados = tramitesCompletados;
window.historialTramites = historialTramites;

console.log('✅ Módulo ventanilla DRTC cargado completamente');