// ==================== VENTANILLA.JS ====================
// Funcionalidades específicas para el rol de ventanilla

console.log('🏢 Cargando módulo de Ventanilla...');

// ==================== VARIABLES GLOBALES ====================
let colaEsperaData = [];
let tramitesData = [];
let consultasData = [];

// ==================== INICIALIZACIÓN ====================
document.addEventListener('DOMContentLoaded', function() {
    if (window.dashboardUserRole === 'ventanilla') {
        console.log('✅ Inicializando funcionalidades de ventanilla');
        initVentanillaModule();
        
        // Sobrescribir las funciones del core con las versiones completas
        window.loadNuevaAtencion = loadNuevaAtencion;
        window.loadColaEspera = loadColaEspera;
        window.loadConsultas = loadConsultas;
        window.loadTramites = loadTramites;
        
        console.log('✅ Funciones de ventanilla registradas globalmente');
    }
});

function initVentanillaModule() {
    // Cargar datos iniciales
    loadDashboardStatsVentanilla();
    
    // Configurar actualizaciones automáticas
    setInterval(loadColaEsperaData, 30000); // Actualizar cola cada 30 segundos
    setInterval(loadNotificationsVentanilla, 60000); // Actualizar notificaciones cada minuto
}

// ==================== DASHBOARD STATS VENTANILLA ====================
function loadDashboardStatsVentanilla() {
    fetch('dashboard.php?api=dashboard-stats')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.stats) {
                updateVentanillaStats(data.stats);
            }
        })
        .catch(error => {
            console.error('Error cargando estadísticas de ventanilla:', error);
        });
}

function updateVentanillaStats(stats) {
    const statsContainer = document.getElementById('dashboardStats');
    if (!statsContainer) return;

    statsContainer.innerHTML = `
        <div class="col-md-3 mb-4">
            <div class="stats-card">
                <div class="stats-number">${stats.atenciones_hoy || 0}</div>
                <div class="stats-label">Atenciones Hoy</div>
                <div class="stats-trend">+${Math.floor(Math.random() * 10)}% vs ayer</div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="stats-card">
                <div class="stats-number">${stats.cola_espera || 0}</div>
                <div class="stats-label">En Cola de Espera</div>
                <div class="stats-trend">Tiempo promedio: ${stats.tiempo_promedio || 15} min</div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="stats-card">
                <div class="stats-number">${stats.tramites_completados || 0}</div>
                <div class="stats-label">Trámites Completados</div>
                <div class="stats-trend">Hoy</div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="stats-card">
                <div class="stats-number">98%</div>
                <div class="stats-label">Satisfacción Cliente</div>
                <div class="stats-trend">Promedio mensual</div>
            </div>
        </div>
    `;
}

// ==================== NUEVA ATENCIÓN ====================
function loadNuevaAtencion() {
    console.log('📝 Cargando formulario de nueva atención');
    
    const content = `
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5><i class="fas fa-plus-circle"></i> Nueva Atención al Cliente</h5>
                        </div>
                        <div class="card-body">
                            <form id="nuevaAtencionForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Tipo de Consulta *</label>
                                            <select class="form-select" name="tipo_consulta" required>
                                                <option value="">Seleccione...</option>
                                                <option value="consulta_acta">Consulta de Acta</option>
                                                <option value="consulta_vehiculo">Consulta de Vehículo</option>
                                                <option value="consulta_conductor">Consulta de Conductor</option>
                                                <option value="tramite_licencia">Trámite de Licencia</option>
                                                <option value="tramite_vehicular">Trámite Vehicular</option>
                                                <option value="reclamo">Reclamo</option>
                                                <option value="informacion_general">Información General</option>
                                                <option value="otros">Otros</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Documento del Cliente *</label>
                                            <input type="text" class="form-control" name="documento" placeholder="DNI/RUC" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Nombre Completo *</label>
                                            <input type="text" class="form-control" name="nombre" placeholder="Nombres y apellidos" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Teléfono</label>
                                            <input type="tel" class="form-control" name="telefono" placeholder="Número de contacto">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Descripción de la Consulta *</label>
                                    <textarea class="form-control" name="descripcion" rows="4" placeholder="Detalle la consulta o solicitud del cliente..." required></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Prioridad</label>
                                            <select class="form-select" name="prioridad">
                                                <option value="normal">Normal</option>
                                                <option value="alta">Alta</option>
                                                <option value="urgente">Urgente</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Canal de Atención</label>
                                            <select class="form-select" name="canal">
                                                <option value="presencial">Presencial</option>
                                                <option value="telefono">Teléfono</option>
                                                <option value="correo">Correo Electrónico</option>
                                                <option value="web">Página Web</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-secondary" onclick="loadSection('dashboard')">
                                        <i class="fas fa-arrow-left"></i> Volver
                                    </button>
                                    <div>
                                        <button type="button" class="btn btn-info me-2" onclick="agregarAColaEspera()">
                                            <i class="fas fa-hourglass-half"></i> Agregar a Cola
                                        </button>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save"></i> Registrar Atención
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    showContent('Nueva Atención', content);
    
    // Configurar el formulario
    document.getElementById('nuevaAtencionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        registrarNuevaAtencion();
    });
}

function registrarNuevaAtencion() {
    const form = document.getElementById('nuevaAtencionForm');
    const formData = new FormData(form);
    
    showLoading('Registrando atención...');
    
    fetch('dashboard.php?api=registrar-atencion', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showNotification('Atención registrada correctamente', 'success');
            form.reset();
            loadDashboardStatsVentanilla();
        } else {
            showNotification('Error: ' + data.message, 'error');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showNotification('Error al registrar atención', 'error');
    });
}

// ==================== COLA DE ESPERA ====================
function loadColaEspera() {
    console.log('⏳ Cargando cola de espera');
    
    const content = `
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                            <h5><i class="fas fa-hourglass-half"></i> Cola de Espera</h5>
                            <div>
                                <button class="btn btn-primary btn-sm me-2" onclick="agregarClienteCola()">
                                    <i class="fas fa-plus"></i> Agregar Cliente
                                </button>
                                <button class="btn btn-success btn-sm" onclick="loadColaEsperaData()">
                                    <i class="fas fa-sync"></i> Actualizar
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="colaEsperaContainer">
                                <div class="text-center p-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                    <p class="mt-2">Cargando cola de espera...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    showContent('Cola de Espera', content);
    loadColaEsperaData();
}

function loadColaEsperaData() {
    fetch('dashboard.php?api=cola-espera')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                colaEsperaData = data.cola || [];
                renderColaEspera();
            } else {
                document.getElementById('colaEsperaContainer').innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Error al cargar la cola de espera
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('colaEsperaContainer').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle"></i> Error de conexión
                </div>
            `;
        });
}

function renderColaEspera() {
    const container = document.getElementById('colaEsperaContainer');
    if (!container) return;
    
    if (colaEsperaData.length === 0) {
        container.innerHTML = `
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> No hay clientes en cola de espera
            </div>
        `;
        return;
    }
    
    let html = `
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-success">
                    <strong>Total en cola:</strong> ${colaEsperaData.length} clientes
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Ticket</th>
                        <th>Cliente</th>
                        <th>Documento</th>
                        <th>Tipo Consulta</th>
                        <th>Hora Llegada</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    colaEsperaData.forEach(cliente => {
        const estadoBadge = getEstadoBadge(cliente.estado);
        const tiempoEspera = calcularTiempoEspera(cliente.hora_llegada);
        
        html += `
            <tr>
                <td><strong>${cliente.numero_ticket}</strong></td>
                <td>${cliente.nombre_cliente}</td>
                <td>${cliente.documento_cliente}</td>
                <td>${cliente.tipo_consulta}</td>
                <td>${formatDateTime(cliente.hora_llegada)}<br><small class="text-muted">${tiempoEspera}</small></td>
                <td>${estadoBadge}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-success" onclick="atenderCliente(${cliente.id})" title="Atender">
                            <i class="fas fa-user-check"></i>
                        </button>
                        <button class="btn btn-info" onclick="verDetalleCliente(${cliente.id})" title="Ver detalle">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-danger" onclick="removerDeCola(${cliente.id})" title="Remover">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    html += `
                </tbody>
            </table>
        </div>
    `;
    
    container.innerHTML = html;
}

function agregarClienteCola() {
    const modalContent = `
        <form id="agregarColaForm">
            <div class="mb-3">
                <label class="form-label">Nombre del Cliente *</label>
                <input type="text" class="form-control" name="nombre" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Documento *</label>
                <input type="text" class="form-control" name="documento" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Tipo de Consulta *</label>
                <select class="form-select" name="tipo_consulta" required>
                    <option value="">Seleccione...</option>
                    <option value="consulta_acta">Consulta de Acta</option>
                    <option value="consulta_vehiculo">Consulta de Vehículo</option>
                    <option value="consulta_conductor">Consulta de Conductor</option>
                    <option value="tramite_licencia">Trámite de Licencia</option>
                    <option value="tramite_vehicular">Trámite Vehicular</option>
                    <option value="otros">Otros</option>
                </select>
            </div>
        </form>
    `;
    
    showModal('Agregar Cliente a Cola', modalContent, [
        {
            text: 'Cancelar',
            class: 'btn-secondary',
            dismiss: true
        },
        {
            text: 'Agregar',
            class: 'btn-primary',
            onclick: 'submitAgregarCola()'
        }
    ]);
}

function submitAgregarCola() {
    const form = document.getElementById('agregarColaForm');
    const formData = new FormData(form);
    
    fetch('dashboard.php?api=cola-espera', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Cliente agregado a la cola. Ticket: ' + data.ticket, 'success');
            closeModal();
            loadColaEsperaData();
        } else {
            showNotification('Error: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al agregar cliente', 'error');
    });
}

// ==================== TRÁMITES DRTC ====================
function loadTramites() {
    console.log('📋 Cargando gestión de trámites');
    
    const content = `
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5><i class="fas fa-folder-open"></i> Nuevo Trámite DRTC</h5>
                        </div>
                        <div class="card-body">
                            <form id="nuevoTramiteForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Tipo de Trámite *</label>
                                            <select class="form-select" name="tipo_tramite" required>
                                                <option value="">Seleccione...</option>
                                                <option value="licencia_conducir">Licencia de Conducir</option>
                                                <option value="renovacion_licencia">Renovación de Licencia</option>
                                                <option value="duplicado_licencia">Duplicado de Licencia</option>
                                                <option value="tarjeta_propiedad">Tarjeta de Propiedad</option>
                                                <option value="cambio_propietario">Cambio de Propietario</option>
                                                <option value="placa_vehicular">Placa Vehicular</option>
                                                <option value="revision_tecnica">Revisión Técnica</option>
                                                <option value="certificado_no_adeudo">Certificado de No Adeudo</option>
                                                <option value="otros">Otros</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Documento del Solicitante *</label>
                                            <input type="text" class="form-control" name="documento" placeholder="DNI/RUC" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Nombre Completo *</label>
                                            <input type="text" class="form-control" name="nombre" placeholder="Nombres y apellidos" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Teléfono *</label>
                                            <input type="tel" class="form-control" name="telefono" placeholder="Número de contacto" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Observaciones</label>
                                    <textarea class="form-control" name="observaciones" rows="3" placeholder="Detalles adicionales del trámite..."></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Documentos Requeridos</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="docs[]" value="dni" id="doc_dni">
                                                <label class="form-check-label" for="doc_dni">DNI</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="docs[]" value="recibo_agua_luz" id="doc_recibo">
                                                <label class="form-check-label" for="doc_recibo">Recibo de Agua/Luz</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="docs[]" value="certificado_medico" id="doc_medico">
                                                <label class="form-check-label" for="doc_medico">Certificado Médico</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Estado Inicial</label>
                                            <select class="form-select" name="estado">
                                                <option value="pendiente">Pendiente</option>
                                                <option value="proceso">En Proceso</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-secondary" onclick="loadSection('dashboard')">
                                        <i class="fas fa-arrow-left"></i> Volver
                                    </button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save"></i> Registrar Trámite
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    showContent('Nuevo Trámite', content);
    
    // Configurar el formulario
    document.getElementById('nuevoTramiteForm').addEventListener('submit', function(e) {
        e.preventDefault();
        registrarNuevoTramite();
    });
}

function registrarNuevoTramite() {
    const form = document.getElementById('nuevoTramiteForm');
    const formData = new FormData(form);
    
    showLoading('Registrando trámite...');
    
    fetch('dashboard.php?api=tramites', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showNotification('Trámite registrado correctamente. Número: ' + data.numero, 'success');
            form.reset();
            loadDashboardStatsVentanilla();
        } else {
            showNotification('Error: ' + data.message, 'error');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showNotification('Error al registrar trámite', 'error');
    });
}

function tramitesPendientes() {
    console.log('📋 Cargando trámites pendientes');
    
    fetch('dashboard.php?api=tramites')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const tramitesPendientes = data.tramites.filter(t => t.estado === 'pendiente' || t.estado === 'proceso');
                mostrarTramitesPendientes(tramitesPendientes);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al cargar trámites pendientes', 'error');
        });
}

function mostrarTramitesPendientes(tramites) {
    let html = `
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5><i class="fas fa-clock"></i> Trámites Pendientes (${tramites.length})</h5>
                        </div>
                        <div class="card-body">
    `;
    
    if (tramites.length === 0) {
        html += `
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> No hay trámites pendientes
            </div>
        `;
    } else {
        html += `
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Número</th>
                            <th>Tipo</th>
                            <th>Solicitante</th>
                            <th>Documento</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        tramites.forEach(tramite => {
            html += `
                <tr>
                    <td><strong>${tramite.numero_tramite}</strong></td>
                    <td>${tramite.tipo_tramite}</td>
                    <td>${tramite.nombre_solicitante}</td>
                    <td>${tramite.documento_solicitante}</td>
                    <td>${formatDateTime(tramite.fecha_registro)}</td>
                    <td>${getEstadoBadge(tramite.estado)}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-info" onclick="verDetalleTramite(${tramite.id})" title="Ver detalle">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-success" onclick="procesarTramite(${tramite.id})" title="Procesar">
                                <i class="fas fa-cog"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
        
        html += `
                    </tbody>
                </table>
            </div>
        `;
    }
    
    html += `
                </div>
            </div>
        </div>
    </div>
    `;
    
    showContent('Trámites Pendientes', html);
}

function historialTramites() {
    console.log('📚 Cargando historial de trámites');
    
    fetch('dashboard.php?api=tramites')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarHistorialTramites(data.tramites);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al cargar historial', 'error');
        });
}

function mostrarHistorialTramites(tramites) {
    let html = `
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                            <h5><i class="fas fa-history"></i> Historial de Trámites</h5>
                            <div>
                                <input type="text" class="form-control form-control-sm d-inline-block" style="width: 200px;" placeholder="Buscar..." id="searchTramites">
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="tramitesTable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Número</th>
                                            <th>Tipo</th>
                                            <th>Solicitante</th>
                                            <th>Fecha</th>
                                            <th>Estado</th>
                                            <th>Registrado por</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
    `;
    
    tramites.forEach(tramite => {
        html += `
            <tr>
                <td><strong>${tramite.numero_tramite}</strong></td>
                <td>${tramite.tipo_tramite}</td>
                <td>${tramite.nombre_solicitante}</td>
                <td>${formatDateTime(tramite.fecha_registro)}</td>
                <td>${getEstadoBadge(tramite.estado)}</td>
                <td>${tramite.registrado_por}</td>
                <td>
                    <button class="btn btn-info btn-sm" onclick="verDetalleTramite(${tramite.id})" title="Ver detalle">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    
    html += `
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
    `;
    
    showContent('Historial de Trámites', html);
    
    // Configurar búsqueda
    document.getElementById('searchTramites').addEventListener('input', function() {
        filterTable('tramitesTable', this.value);
    });
}

// ==================== CONSULTAS PÚBLICAS ====================
function loadConsultas() {
    console.log('🔍 Cargando consultas públicas');
    
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
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100 border-primary">
                                        <div class="card-body text-center">
                                            <i class="fas fa-file-alt fa-3x text-primary mb-3"></i>
                                            <h5>Consultar Actas</h5>
                                            <p class="text-muted">Buscar actas de infracción por número, placa o documento</p>
                                            <button class="btn btn-primary" onclick="loadConsultasActas()">
                                                <i class="fas fa-search"></i> Consultar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100 border-success">
                                        <div class="card-body text-center">
                                            <i class="fas fa-car fa-3x text-success mb-3"></i>
                                            <h5>Consultar Vehículos</h5>
                                            <p class="text-muted">Información de vehículos registrados en el sistema</p>
                                            <button class="btn btn-success" onclick="consultarVehiculos()">
                                                <i class="fas fa-search"></i> Consultar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100 border-warning">
                                        <div class="card-body text-center">
                                            <i class="fas fa-id-card fa-3x text-warning mb-3"></i>
                                            <h5>Consultar Conductores</h5>
                                            <p class="text-muted">Información de licencias y conductores registrados</p>
                                            <button class="btn btn-warning" onclick="consultarConductores()">
                                                <i class="fas fa-search"></i> Consultar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-md-6 mb-4">
                                    <div class="card border-info">
                                        <div class="card-body text-center">
                                            <i class="fas fa-info-circle fa-3x text-info mb-3"></i>
                                            <h5>Información DRTC</h5>
                                            <p class="text-muted">Horarios, ubicación y servicios disponibles</p>
                                            <button class="btn btn-info" onclick="mostrarInfoDRTC()">
                                                <i class="fas fa-info"></i> Ver Información
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <div class="card border-secondary">
                                        <div class="card-body text-center">
                                            <i class="fas fa-phone fa-3x text-secondary mb-3"></i>
                                            <h5>Contacto y Reclamos</h5>
                                            <p class="text-muted">Canales de atención y presentación de reclamos</p>
                                            <button class="btn btn-secondary" onclick="mostrarContactoReclamos()">
                                                <i class="fas fa-phone"></i> Contactar
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
    
    showContent('Consultas Públicas', content);
}

function loadConsultasActas() {
    const modalContent = `
        <form id="consultaActasForm">
            <div class="mb-3">
                <label class="form-label">Tipo de Búsqueda</label>
                <select class="form-select" name="tipo_busqueda" id="tipoBusqueda" onchange="cambiarTipoBusqueda()">
                    <option value="numero_acta">Número de Acta</option>
                    <option value="placa">Placa del Vehículo</option>
                    <option value="documento">Documento del Conductor</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" id="labelBusqueda">Número de Acta</label>
                <input type="text" class="form-control" name="valor_busqueda" id="valorBusqueda" required>
            </div>
            <div id="resultadosConsulta" class="mt-3" style="display: none;">
                <!-- Resultados se mostrarán aquí -->
            </div>
        </form>
    `;
    
    showModal('Consultar Actas de Infracción', modalContent, [
        {
            text: 'Cerrar',
            class: 'btn-secondary',
            dismiss: true
        },
        {
            text: 'Buscar',
            class: 'btn-primary',
            onclick: 'buscarActas()'
        }
    ]);
}

function cambiarTipoBusqueda() {
    const tipo = document.getElementById('tipoBusqueda').value;
    const label = document.getElementById('labelBusqueda');
    const input = document.getElementById('valorBusqueda');
    
    switch(tipo) {
        case 'numero_acta':
            label.textContent = 'Número de Acta';
            input.placeholder = 'Ej: ACT-2024-0001';
            break;
        case 'placa':
            label.textContent = 'Placa del Vehículo';
            input.placeholder = 'Ej: ABC-123';
            break;
        case 'documento':
            label.textContent = 'Documento del Conductor';
            input.placeholder = 'DNI o RUC';
            break;
    }
    input.value = '';
}

function buscarActas() {
    const form = document.getElementById('consultaActasForm');
    const formData = new FormData(form);
    const resultadosDiv = document.getElementById('resultadosConsulta');
    
    resultadosDiv.innerHTML = `
        <div class="text-center p-3">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Buscando...</span>
            </div>
            <p class="mt-2">Buscando actas...</p>
        </div>
    `;
    resultadosDiv.style.display = 'block';
    
    const criterios = {
        tipo_busqueda: formData.get('tipo_busqueda'),
        valor_busqueda: formData.get('valor_busqueda')
    };
    
    fetch('dashboard.php?api=consultar-actas', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(criterios)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarResultadosActas(data.actas);
        } else {
            resultadosDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> ${data.message}
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        resultadosDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-times-circle"></i> Error de conexión
            </div>
        `;
    });
}

function mostrarResultadosActas(resultados) {
    const resultadosDiv = document.getElementById('resultadosConsulta');
    
    if (resultados.length === 0) {
        resultadosDiv.innerHTML = `
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> No se encontraron actas con los criterios especificados
            </div>
        `;
        return;
    }
    
    let html = `
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> Se encontraron ${resultados.length} acta(s)
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Placa</th>
                        <th>Conductor</th>
                        <th>Fecha</th>
                        <th>Lugar</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    resultados.forEach(acta => {
        html += `
            <tr>
                <td><strong>${acta.numero_acta || 'N/A'}</strong></td>
                <td>${acta.placa_vehiculo || acta.placa || 'N/A'}</td>
                <td>${acta.nombre_conductor || 'N/A'}</td>
                <td>${formatDateTime(acta.fecha_intervencion)}</td>
                <td>${acta.lugar_intervencion || 'N/A'}</td>
                <td><span class="badge bg-warning">Pendiente</span></td>
            </tr>
        `;
    });
    
    html += `
                </tbody>
            </table>
        </div>
    `;
    
    resultadosDiv.innerHTML = html;
}

function consultarVehiculos() {
    const modalContent = `
        <form id="consultaVehiculosForm">
            <div class="mb-3">
                <label class="form-label">Placa del Vehículo</label>
                <input type="text" class="form-control" name="placa" placeholder="Ej: ABC-123" required>
            </div>
            <div id="resultadosVehiculo" class="mt-3" style="display: none;">
                <!-- Resultados se mostrarán aquí -->
            </div>
        </form>
    `;
    
    showModal('Consultar Vehículo', modalContent, [
        {
            text: 'Cerrar',
            class: 'btn-secondary',
            dismiss: true
        },
        {
            text: 'Buscar',
            class: 'btn-success',
            onclick: 'buscarVehiculo()'
        }
    ]);
}

function buscarVehiculo() {
    const placa = document.querySelector('[name="placa"]').value;
    const resultadosDiv = document.getElementById('resultadosVehiculo');
    
    resultadosDiv.innerHTML = `
        <div class="text-center p-3">
            <div class="spinner-border text-success" role="status">
                <span class="visually-hidden">Buscando...</span>
            </div>
            <p class="mt-2">Consultando vehículo...</p>
        </div>
    `;
    resultadosDiv.style.display = 'block';
    
    fetch('dashboard.php?api=consultar-vehiculo', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ placa: placa })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.vehiculo) {
                mostrarResultadoVehiculo(data.vehiculo, data.actas);
            } else {
                resultadosDiv.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> No se encontró vehículo con la placa: ${placa}
                    </div>
                `;
            }
        } else {
            resultadosDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> ${data.message}
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        resultadosDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-times-circle"></i> Error de conexión
            </div>
        `;
    });
}

function mostrarResultadoVehiculo(vehiculo, actas = []) {
    const resultadosDiv = document.getElementById('resultadosVehiculo');
    
    let html = `
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> Vehículo encontrado
        </div>
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Información del Vehículo</h6>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Placa:</strong> ${vehiculo.placa || 'N/A'}</p>
                        <p><strong>Marca:</strong> ${vehiculo.marca || 'N/A'}</p>
                        <p><strong>Modelo:</strong> ${vehiculo.modelo || 'N/A'}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Año:</strong> ${vehiculo.año || vehiculo.ano || 'N/A'}</p>
                        <p><strong>Color:</strong> ${vehiculo.color || 'N/A'}</p>
                        <p><strong>Estado:</strong> <span class="badge bg-success">${vehiculo.estado || 'Vigente'}</span></p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Mostrar actas relacionadas si existen
    if (actas && actas.length > 0) {
        html += `
            <div class="card mt-3">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="fas fa-file-alt"></i> Actas Relacionadas (${actas.length})</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Fecha</th>
                                    <th>Conductor</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
        `;
        
        actas.forEach(acta => {
            html += `
                <tr>
                    <td>${acta.numero_acta || 'N/A'}</td>
                    <td>${formatDateTime(acta.fecha_intervencion)}</td>
                    <td>${acta.nombre_conductor || 'N/A'}</td>
                    <td><span class="badge bg-warning">Pendiente</span></td>
                </tr>
            `;
        });
        
        html += `
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;
    }
    
    resultadosDiv.innerHTML = html;
}

function consultarConductores() {
    const modalContent = `
        <form id="consultaConductoresForm">
            <div class="mb-3">
                <label class="form-label">Tipo de Búsqueda</label>
                <select class="form-select" name="tipo_busqueda" onchange="cambiarTipoBusquedaConductor()">
                    <option value="dni">DNI</option>
                    <option value="licencia">Número de Licencia</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" id="labelBusquedaConductor">DNI del Conductor</label>
                <input type="text" class="form-control" name="valor_busqueda" id="valorBusquedaConductor" required>
            </div>
            <div id="resultadosConductor" class="mt-3" style="display: none;">
                <!-- Resultados se mostrarán aquí -->
            </div>
        </form>
    `;
    
    showModal('Consultar Conductor', modalContent, [
        {
            text: 'Cerrar',
            class: 'btn-secondary',
            dismiss: true
        },
        {
            text: 'Buscar',
            class: 'btn-warning',
            onclick: 'buscarConductor()'
        }
    ]);
}

function cambiarTipoBusquedaConductor() {
    const tipo = document.querySelector('[name="tipo_busqueda"]').value;
    const label = document.getElementById('labelBusquedaConductor');
    const input = document.getElementById('valorBusquedaConductor');
    
    if (tipo === 'dni') {
        label.textContent = 'DNI del Conductor';
        input.placeholder = 'Ej: 12345678';
    } else {
        label.textContent = 'Número de Licencia';
        input.placeholder = 'Ej: L12345678';
    }
    input.value = '';
}

function buscarConductor() {
    const form = document.getElementById('consultaConductoresForm');
    const formData = new FormData(form);
    const resultadosDiv = document.getElementById('resultadosConductor');
    
    resultadosDiv.innerHTML = `
        <div class="text-center p-3">
            <div class="spinner-border text-warning" role="status">
                <span class="visually-hidden">Buscando...</span>
            </div>
            <p class="mt-2">Consultando conductor...</p>
        </div>
    `;
    resultadosDiv.style.display = 'block';
    
    const criterios = {
        tipo_busqueda: formData.get('tipo_busqueda'),
        valor_busqueda: formData.get('valor_busqueda')
    };
    
    fetch('dashboard.php?api=consultar-conductor', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(criterios)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.conductores && data.conductores.length > 0) {
                mostrarResultadosConductores(data.conductores, data.actas_relacionadas);
            } else {
                resultadosDiv.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> No se encontraron conductores con los criterios especificados
                    </div>
                `;
            }
        } else {
            resultadosDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> ${data.message}
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        resultadosDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-times-circle"></i> Error de conexión
            </div>
        `;
    });
}

function mostrarResultadosConductores(conductores, actasRelacionadas = {}) {
    const resultadosDiv = document.getElementById('resultadosConductor');
    
    let html = `
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> Se encontraron ${conductores.length} conductor(es)
        </div>
    `;
    
    conductores.forEach((conductor, index) => {
        html += `
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="card-title">Conductor ${index + 1}</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>DNI:</strong> ${conductor.dni || 'N/A'}</p>
                            <p><strong>Nombres:</strong> ${conductor.nombres || 'N/A'}</p>
                            <p><strong>Apellidos:</strong> ${conductor.apellidos || 'N/A'}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Licencia:</strong> ${conductor.numero_licencia || 'N/A'}</p>
                            <p><strong>Categoría:</strong> ${conductor.clase_categoria || 'N/A'}</p>
                            <p><strong>Vencimiento:</strong> ${conductor.fecha_vencimiento || 'N/A'}</p>
                            <p><strong>Estado:</strong> <span class="badge bg-success">${conductor.estado_licencia || 'Vigente'}</span></p>
                        </div>
                    </div>
        `;
        
        // Mostrar actas relacionadas si existen
        const actas = actasRelacionadas[conductor.id] || [];
        if (actas.length > 0) {
            html += `
                    <hr>
                    <h6><i class="fas fa-file-alt"></i> Actas Relacionadas (${actas.length})</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Fecha</th>
                                    <th>Placa</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
            `;
            
            actas.forEach(acta => {
                html += `
                    <tr>
                        <td>${acta.numero_acta || 'N/A'}</td>
                        <td>${formatDateTime(acta.fecha_intervencion)}</td>
                        <td>${acta.placa_vehiculo || acta.placa || 'N/A'}</td>
                        <td><span class="badge bg-warning">Pendiente</span></td>
                    </tr>
                `;
            });
            
            html += `
                            </tbody>
                        </table>
                    </div>
            `;
        }
        
        html += `
                </div>
            </div>
        `;
    });
    
    resultadosDiv.innerHTML = html;
}

function mostrarInfoDRTC() {
    const content = `
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5><i class="fas fa-info-circle"></i> Información DRTC</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card mb-4">
                                        <div class="card-header bg-primary text-white">
                                            <h6><i class="fas fa-clock"></i> Horarios de Atención</h6>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Lunes a Viernes:</strong> 8:00 AM - 5:00 PM</p>
                                            <p><strong>Sábados:</strong> 8:00 AM - 12:00 PM</p>
                                            <p><strong>Domingos:</strong> Cerrado</p>
                                            <hr>
                                            <p class="text-muted"><small>Horario de almuerzo: 12:00 PM - 1:00 PM</small></p>
                                        </div>
                                    </div>
                                    
                                    <div class="card mb-4">
                                        <div class="card-header bg-success text-white">
                                            <h6><i class="fas fa-map-marker-alt"></i> Ubicación</h6>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Dirección:</strong> Av. Principal 123, Centro</p>
                                            <p><strong>Distrito:</strong> Cercado</p>
                                            <p><strong>Provincia:</strong> Lima</p>
                                            <p><strong>Referencia:</strong> Frente al Banco de la Nación</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card mb-4">
                                        <div class="card-header bg-warning text-dark">
                                            <h6><i class="fas fa-phone"></i> Contacto</h6>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Teléfono:</strong> (01) 123-4567</p>
                                            <p><strong>Email:</strong> info@drtc.gob.pe</p>
                                            <p><strong>WhatsApp:</strong> +51 987 654 321</p>
                                            <p><strong>Web:</strong> www.drtc.gob.pe</p>
                                        </div>
                                    </div>
                                    
                                    <div class="card mb-4">
                                        <div class="card-header bg-secondary text-white">
                                            <h6><i class="fas fa-cogs"></i> Servicios Disponibles</h6>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-unstyled">
                                                <li><i class="fas fa-check text-success"></i> Licencias de Conducir</li>
                                                <li><i class="fas fa-check text-success"></i> Tarjetas de Propiedad</li>
                                                <li><i class="fas fa-check text-success"></i> Revisiones Técnicas</li>
                                                <li><i class="fas fa-check text-success"></i> Certificados</li>
                                                <li><i class="fas fa-check text-success"></i> Consultas en línea</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <h6><i class="fas fa-info-circle"></i> Información Importante</h6>
                                        <ul class="mb-0">
                                            <li>Traer documentos originales y copias</li>
                                            <li>Los pagos se realizan en caja</li>
                                            <li>Respetar el orden de llegada</li>
                                            <li>Usar mascarilla es obligatorio</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    showContent('Información DRTC', content);
}

function mostrarContactoReclamos() {
    const modalContent = `
        <div class="row">
            <div class="col-md-6">
                <h6><i class="fas fa-phone"></i> Canales de Atención</h6>
                <ul class="list-unstyled">
                    <li><i class="fas fa-phone text-primary"></i> Teléfono: (01) 123-4567</li>
                    <li><i class="fas fa-envelope text-success"></i> Email: reclamos@drtc.gob.pe</li>
                    <li><i class="fas fa-map-marker-alt text-danger"></i> Presencial: Av. Principal 123</li>
                    <li><i class="fas fa-globe text-info"></i> Web: www.drtc.gob.pe/reclamos</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6><i class="fas fa-clock"></i> Horarios</h6>
                <p><strong>Atención telefónica:</strong><br>Lunes a Viernes: 8:00 AM - 6:00 PM</p>
                <p><strong>Atención presencial:</strong><br>Lunes a Viernes: 8:00 AM - 5:00 PM<br>Sábados: 8:00 AM - 12:00 PM</p>
            </div>
        </div>
        <hr>
        <div class="alert alert-warning">
            <h6><i class="fas fa-exclamation-triangle"></i> Para presentar un reclamo necesita:</h6>
            <ul class="mb-0">
                <li>Documento de identidad</li>
                <li>Número de acta o trámite</li>
                <li>Descripción detallada del problema</li>
                <li>Documentos de sustento (si los tiene)</li>
            </ul>
        </div>
    `;
    
    showModal('Contacto y Reclamos', modalContent, [
        {
            text: 'Cerrar',
            class: 'btn-secondary',
            dismiss: true
        }
    ]);
}

// ==================== FUNCIONES AUXILIARES ====================
function getEstadoBadge(estado) {
    const badges = {
        'pendiente': '<span class="badge bg-warning">Pendiente</span>',
        'proceso': '<span class="badge bg-info">En Proceso</span>',
        'completado': '<span class="badge bg-success">Completado</span>',
        'rechazado': '<span class="badge bg-danger">Rechazado</span>',
        'esperando': '<span class="badge bg-warning">Esperando</span>',
        'atendiendo': '<span class="badge bg-info">Atendiendo</span>',
        'atendido': '<span class="badge bg-success">Atendido</span>'
    };
    
    return badges[estado] || `<span class="badge bg-secondary">${estado}</span>`;
}

function calcularTiempoEspera(horaLlegada) {
    const ahora = new Date();
    const llegada = new Date(horaLlegada);
    const diferencia = ahora - llegada;
    
    const minutos = Math.floor(diferencia / (1000 * 60));
    const horas = Math.floor(minutos / 60);
    
    if (horas > 0) {
        return `${horas}h ${minutos % 60}m esperando`;
    } else {
        return `${minutos}m esperando`;
    }
}

function formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('es-PE', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function filterTable(tableId, searchTerm) {
    const table = document.getElementById(tableId);
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const text = row.textContent.toLowerCase();
        
        if (text.includes(searchTerm.toLowerCase())) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
}

function atenderCliente(clienteId) {
    showNotification('Cliente siendo atendido', 'info');
    // Aquí iría la lógica para marcar como "atendiendo"
    loadColaEsperaData();
}

function verDetalleCliente(clienteId) {
    const cliente = colaEsperaData.find(c => c.id === clienteId);
    if (!cliente) return;
    
    const modalContent = `
        <div class="row">
            <div class="col-md-6">
                <p><strong>Ticket:</strong> ${cliente.numero_ticket}</p>
                <p><strong>Cliente:</strong> ${cliente.nombre_cliente}</p>
                <p><strong>Documento:</strong> ${cliente.documento_cliente}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Tipo Consulta:</strong> ${cliente.tipo_consulta}</p>
                <p><strong>Hora Llegada:</strong> ${formatDateTime(cliente.hora_llegada)}</p>
                <p><strong>Estado:</strong> ${getEstadoBadge(cliente.estado)}</p>
            </div>
        </div>
    `;
    
    showModal('Detalle del Cliente', modalContent);
}

function removerDeCola(clienteId) {
    if (confirm('¿Está seguro de remover este cliente de la cola?')) {
        showNotification('Cliente removido de la cola', 'warning');
        loadColaEsperaData();
    }
}

function verDetalleTramite(tramiteId) {
    showNotification('Cargando detalle del trámite...', 'info');
    // Aquí iría la lógica para mostrar el detalle completo
}

function procesarTramite(tramiteId) {
    showNotification('Procesando trámite...', 'info');
    // Aquí iría la lógica para procesar el trámite
}

function loadNotificationsVentanilla() {
    // Cargar notificaciones específicas para ventanilla
    fetch('dashboard.php?api=notifications')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.notifications) {
                updateNotificationBadge(data.notifications.length);
            }
        })
        .catch(error => {
            console.error('Error cargando notificaciones:', error);
        });
}

function agregarAColaEspera() {
    const form = document.getElementById('nuevaAtencionForm');
    const formData = new FormData(form);
    
    // Convertir a formato para cola de espera
    const colaData = new FormData();
    colaData.append('nombre', formData.get('nombre'));
    colaData.append('documento', formData.get('documento'));
    colaData.append('tipo_consulta', formData.get('tipo_consulta'));
    
    fetch('dashboard.php?api=cola-espera', {
        method: 'POST',
        body: colaData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Cliente agregado a la cola. Ticket: ' + data.ticket, 'success');
            loadDashboardStatsVentanilla();
        } else {
            showNotification('Error: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al agregar a cola', 'error');
    });
}

// ==================== FUNCIONES AUXILIARES ADICIONALES ====================

// Función para mostrar contenido en el contenedor principal
function showContent(title, content) {
    const contentContainer = document.getElementById('contentContainer');
    if (contentContainer) {
        contentContainer.innerHTML = `
            <div class="content-section active">
                <h2>${title}</h2>
                ${content}
            </div>
        `;
    }
}

// Función para mostrar modal
function showModal(title, content, buttons = []) {
    const modal = document.getElementById('generalModal');
    if (!modal) {
        console.error('Modal no encontrado');
        return;
    }
    
    // Actualizar título
    const modalTitle = document.getElementById('modalTitle');
    if (modalTitle) modalTitle.textContent = title;
    
    // Actualizar contenido
    const modalBody = document.getElementById('modalBody');
    if (modalBody) modalBody.innerHTML = content;
    
    // Actualizar botones
    const modalFooter = document.getElementById('modalFooter');
    if (modalFooter && buttons.length > 0) {
        let buttonsHtml = '';
        buttons.forEach(button => {
            const dismissAttr = button.dismiss ? 'data-bs-dismiss="modal"' : '';
            const onclickAttr = button.onclick ? `onclick="${button.onclick}"` : '';
            buttonsHtml += `<button type="button" class="btn ${button.class}" ${dismissAttr} ${onclickAttr}>${button.text}</button>`;
        });
        modalFooter.innerHTML = buttonsHtml;
    }
    
    // Mostrar modal
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
}

// Función para cerrar modal
function closeModal() {
    const modal = document.getElementById('generalModal');
    if (modal) {
        const bootstrapModal = bootstrap.Modal.getInstance(modal);
        if (bootstrapModal) {
            bootstrapModal.hide();
        }
    }
}

// Función para mostrar loading
function showLoading(message = 'Cargando...') {
    const loading = document.getElementById('loading');
    if (loading) {
        loading.innerHTML = `
            <div class="text-center p-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2">${message}</p>
            </div>
        `;
        loading.style.display = 'block';
    }
}

// Función para ocultar loading
function hideLoading() {
    const loading = document.getElementById('loading');
    if (loading) {
        loading.style.display = 'none';
    }
}

// Función para mostrar notificaciones
function showNotification(message, type = 'info', duration = 5000) {
    // Crear el toast
    const toastId = 'toast-' + Date.now();
    const toastHtml = `
        <div id="${toastId}" class="toast custom-toast toast-${type}" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-body d-flex align-items-center">
                <i class="fas fa-${getNotificationIcon(type)} toast-icon"></i>
                <div class="flex-grow-1">${message}</div>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    // Agregar al contenedor
    let toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toastContainer';
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    // Mostrar el toast
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, { delay: duration });
    toast.show();
    
    // Remover el elemento después de que se oculte
    toastElement.addEventListener('hidden.bs.toast', function() {
        toastElement.remove();
    });
}

// Función para obtener icono de notificación
function getNotificationIcon(type) {
    const icons = {
        'success': 'check-circle',
        'error': 'exclamation-triangle',
        'warning': 'exclamation-triangle',
        'info': 'info-circle'
    };
    return icons[type] || 'info-circle';
}

// Función para actualizar badge de notificaciones
function updateNotificationBadge(count) {
    const badge = document.getElementById('notificationCount');
    if (badge) {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'inline' : 'none';
    }
}

// Funciones específicas que pueden faltar
function tramitesPendientes() {
    console.log('📋 Cargando trámites pendientes');
    
    fetch('dashboard.php?api=tramites')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const tramitesPendientes = data.tramites.filter(t => t.estado === 'pendiente' || t.estado === 'proceso');
                mostrarTramitesPendientes(tramitesPendientes);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al cargar trámites pendientes', 'error');
        });
}

function historialTramites() {
    console.log('📋 Cargando historial de trámites');
    
    fetch('dashboard.php?api=tramites')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarHistorialTramites(data.tramites);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al cargar historial', 'error');
        });
}

function loadConsultasActas() {
    const modalContent = `
        <form id="consultaActasForm">
            <div class="mb-3">
                <label class="form-label">Tipo de Búsqueda</label>
                <select class="form-select" name="tipo_busqueda" id="tipoBusqueda" onchange="cambiarTipoBusqueda()">
                    <option value="numero_acta">Número de Acta</option>
                    <option value="placa">Placa del Vehículo</option>
                    <option value="documento">Documento del Conductor</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" id="labelBusqueda">Número de Acta</label>
                <input type="text" class="form-control" name="valor_busqueda" id="valorBusqueda" required>
            </div>
            <div id="resultadosConsulta" class="mt-3" style="display: none;">
                <!-- Resultados se mostrarán aquí -->
            </div>
        </form>
    `;
    
    showModal('Consultar Actas de Infracción', modalContent, [
        {
            text: 'Cerrar',
            class: 'btn-secondary',
            dismiss: true
        },
        {
            text: 'Buscar',
            class: 'btn-primary',
            onclick: 'buscarActas()'
        }
    ]);
}

function consultarVehiculos() {
    const modalContent = `
        <form id="consultaVehiculosForm">
            <div class="mb-3">
                <label class="form-label">Placa del Vehículo</label>
                <input type="text" class="form-control" name="placa" placeholder="Ej: ABC-123" required>
            </div>
            <div id="resultadosVehiculo" class="mt-3" style="display: none;">
                <!-- Resultados se mostrarán aquí -->
            </div>
        </form>
    `;
    
    showModal('Consultar Vehículo', modalContent, [
        {
            text: 'Cerrar',
            class: 'btn-secondary',
            dismiss: true
        },
        {
            text: 'Buscar',
            class: 'btn-success',
            onclick: 'buscarVehiculo()'
        }
    ]);
}

function consultarConductores() {
    const modalContent = `
        <form id="consultaConductoresForm">
            <div class="mb-3">
                <label class="form-label">Tipo de Búsqueda</label>
                <select class="form-select" name="tipo_busqueda" onchange="cambiarTipoBusquedaConductor()">
                    <option value="dni">DNI</option>
                    <option value="licencia">Número de Licencia</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" id="labelBusquedaConductor">DNI del Conductor</label>
                <input type="text" class="form-control" name="valor_busqueda" id="valorBusquedaConductor" required>
            </div>
            <div id="resultadosConductor" class="mt-3" style="display: none;">
                <!-- Resultados se mostrarán aquí -->
            </div>
        </form>
    `;
    
    showModal('Consultar Conductor', modalContent, [
        {
            text: 'Cerrar',
            class: 'btn-secondary',
            dismiss: true
        },
        {
            text: 'Buscar',
            class: 'btn-warning',
            onclick: 'buscarConductor()'
        }
    ]);
}

function mostrarInfoDRTC() {
    const content = `
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5><i class="fas fa-info-circle"></i> Información DRTC</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card mb-4">
                                        <div class="card-header bg-primary text-white">
                                            <h6><i class="fas fa-clock"></i> Horarios de Atención</h6>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Lunes a Viernes:</strong> 8:00 AM - 5:00 PM</p>
                                            <p><strong>Sábados:</strong> 8:00 AM - 12:00 PM</p>
                                            <p><strong>Domingos:</strong> Cerrado</p>
                                            <hr>
                                            <p class="text-muted"><small>Horario de almuerzo: 12:00 PM - 1:00 PM</small></p>
                                        </div>
                                    </div>
                                    
                                    <div class="card mb-4">
                                        <div class="card-header bg-success text-white">
                                            <h6><i class="fas fa-map-marker-alt"></i> Ubicación</h6>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Dirección:</strong> Av. Principal 123, Centro</p>
                                            <p><strong>Distrito:</strong> Cercado</p>
                                            <p><strong>Provincia:</strong> Lima</p>
                                            <p><strong>Referencia:</strong> Frente al Banco de la Nación</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card mb-4">
                                        <div class="card-header bg-warning text-dark">
                                            <h6><i class="fas fa-phone"></i> Contacto</h6>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Teléfono:</strong> (01) 123-4567</p>
                                            <p><strong>Email:</strong> info@drtc.gob.pe</p>
                                            <p><strong>WhatsApp:</strong> +51 987 654 321</p>
                                            <p><strong>Web:</strong> www.drtc.gob.pe</p>
                                        </div>
                                    </div>
                                    
                                    <div class="card mb-4">
                                        <div class="card-header bg-secondary text-white">
                                            <h6><i class="fas fa-cogs"></i> Servicios Disponibles</h6>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-unstyled">
                                                <li><i class="fas fa-check text-success"></i> Licencias de Conducir</li>
                                                <li><i class="fas fa-check text-success"></i> Tarjetas de Propiedad</li>
                                                <li><i class="fas fa-check text-success"></i> Revisiones Técnicas</li>
                                                <li><i class="fas fa-check text-success"></i> Certificados</li>
                                                <li><i class="fas fa-check text-success"></i> Consultas en línea</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <h6><i class="fas fa-info-circle"></i> Información Importante</h6>
                                        <ul class="mb-0">
                                            <li>Traer documentos originales y copias</li>
                                            <li>Los pagos se realizan en caja</li>
                                            <li>Respetar el orden de llegada</li>
                                            <li>Usar mascarilla es obligatorio</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    showContent('Información DRTC', content);
}

console.log('✅ Módulo de Ventanilla cargado completamente');