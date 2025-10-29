// ==================== VENTANILLA SIMPLE ====================
// Sistema simplificado y avanzado para ventanilla DRTC

console.log('🏢 Cargando Ventanilla Simple...');

// ==================== INICIALIZACIÓN ====================
document.addEventListener('DOMContentLoaded', function() {
    if (window.dashboardUserRole === 'ventanilla') {
        initVentanilla();
    }
});

function initVentanilla() {
    // Registrar funciones globales
    window.loadNuevaAtencion = nuevaAtencion;
    window.loadColaEspera = colaEspera;
    window.loadConsultas = consultasPublicas;
    window.loadTramites = nuevoTramite;
    window.tramitesPendientes = tramitesPendientes;
    window.historialTramites = historialTramites;
    window.loadConsultasActas = consultarActas;
    window.consultarVehiculos = consultarVehiculos;
    window.consultarConductores = consultarConductores;
    window.mostrarInfoDRTC = infoGeneral;
    
    // Cargar stats específicos
    loadVentanillaStats();
    
    console.log('✅ Ventanilla inicializada');
}

// ==================== ESTADÍSTICAS ====================
function loadVentanillaStats() {
    fetch('dashboard.php?api=dashboard-stats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateStats(data.stats);
            }
        })
        .catch(error => console.error('Error stats:', error));
}

function updateStats(stats) {
    const container = document.getElementById('dashboardStats');
    if (!container) return;
    
    container.innerHTML = `
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-number">${stats.atenciones_hoy || 0}</div>
                <div class="stats-label">Atenciones Hoy</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-number">${stats.cola_espera || 0}</div>
                <div class="stats-label">En Cola</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-number">${stats.tramites_completados || 0}</div>
                <div class="stats-label">Trámites Hoy</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-number">15 min</div>
                <div class="stats-label">Tiempo Promedio</div>
            </div>
        </div>
    `;
}

// ==================== NUEVA ATENCIÓN ====================
function nuevaAtencion() {
    showContent('Nueva Atención', `
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5><i class="fas fa-plus"></i> Registrar Atención</h5>
            </div>
            <div class="card-body">
                <form id="atencionForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo de Consulta</label>
                            <select class="form-select" name="tipo_consulta" required>
                                <option value="">Seleccione...</option>
                                <option value="consulta_acta">Consulta de Acta</option>
                                <option value="consulta_vehiculo">Consulta de Vehículo</option>
                                <option value="tramite_licencia">Trámite de Licencia</option>
                                <option value="tramite_vehicular">Trámite Vehicular</option>
                                <option value="reclamo">Reclamo</option>
                                <option value="otros">Otros</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Documento</label>
                            <input type="text" class="form-control" name="documento" placeholder="DNI/RUC" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre Completo</label>
                            <input type="text" class="form-control" name="nombre" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" name="telefono">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="3" required></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Registrar
                        </button>
                        <button type="button" class="btn btn-info" onclick="agregarACola()">
                            <i class="fas fa-clock"></i> Agregar a Cola
                        </button>
                    </div>
                </form>
            </div>
        </div>
    `);
    
    document.getElementById('atencionForm').onsubmit = function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('dashboard.php?api=registrar-atencion', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Atención registrada correctamente', 'success');
                this.reset();
                loadVentanillaStats();
            } else {
                showToast('Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showToast('Error de conexión', 'error');
        });
    };
}

// ==================== COLA DE ESPERA ====================
function colaEspera() {
    showContent('Cola de Espera', `
        <div class="card">
            <div class="card-header bg-warning text-dark d-flex justify-content-between">
                <h5><i class="fas fa-clock"></i> Cola de Espera</h5>
                <button class="btn btn-primary btn-sm" onclick="agregarClienteCola()">
                    <i class="fas fa-plus"></i> Agregar Cliente
                </button>
            </div>
            <div class="card-body">
                <div id="colaContainer">Cargando...</div>
            </div>
        </div>
    `);
    
    cargarCola();
}

function cargarCola() {
    fetch('dashboard.php?api=cola-espera')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('colaContainer');
            if (data.success && data.cola.length > 0) {
                container.innerHTML = `
                    <div class="alert alert-info mb-3">
                        <strong>Total en cola:</strong> ${data.total} cliente(s) esperando
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Ticket</th>
                                    <th>Cliente</th>
                                    <th>Documento</th>
                                    <th>Tipo Consulta</th>
                                    <th>Tiempo Espera</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.cola.map(cliente => {
                                    const estadoBadge = cliente.estado === 'esperando' ? 
                                        '<span class="badge bg-warning">Esperando</span>' : 
                                        '<span class="badge bg-info">Atendiendo</span>';
                                    const prioridadClass = cliente.prioridad === 'urgente' ? 'table-danger' : 
                                                          cliente.prioridad === 'alta' ? 'table-warning' : '';
                                    return `
                                        <tr class="${prioridadClass}">
                                            <td><strong>${cliente.numero_ticket}</strong></td>
                                            <td>${cliente.nombre_cliente}</td>
                                            <td>${cliente.documento_cliente}</td>
                                            <td>${cliente.tipo_consulta}</td>
                                            <td>
                                                <span class="text-${cliente.tiempo_espera_min > 30 ? 'danger' : 'muted'}">
                                                    ${cliente.tiempo_espera_texto}
                                                </span>
                                            </td>
                                            <td>${estadoBadge}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-success" onclick="atenderCliente(${cliente.id})" title="Atender">
                                                        <i class="fas fa-user-check"></i>
                                                    </button>
                                                    <button class="btn btn-info" onclick="verDetalleCliente(${cliente.id})" title="Ver detalle">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-danger" onclick="cancelarCliente(${cliente.id})" title="Cancelar">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    `;
                                }).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
            } else {
                container.innerHTML = `
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-hourglass-half fa-3x mb-3"></i>
                        <h5>No hay clientes en cola</h5>
                        <p>La cola de espera está vacía</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('colaContainer').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> Error al cargar cola de espera
                </div>
            `;
        });
}

// ==================== CONSULTAS PÚBLICAS ====================
function consultasPublicas() {
    showContent('Consultas Públicas', `
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-file-alt fa-3x text-primary mb-3"></i>
                        <h5>Consultar Actas</h5>
                        <p class="text-muted">Buscar actas de infracción</p>
                        <button class="btn btn-primary" onclick="consultarActas()">Consultar</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-car fa-3x text-success mb-3"></i>
                        <h5>Consultar Vehículos</h5>
                        <p class="text-muted">Información vehicular</p>
                        <button class="btn btn-success" onclick="consultarVehiculos()">Consultar</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-id-card fa-3x text-warning mb-3"></i>
                        <h5>Consultar Conductores</h5>
                        <p class="text-muted">Licencias y conductores</p>
                        <button class="btn btn-warning" onclick="consultarConductores()">Consultar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-info-circle fa-2x text-info mb-3"></i>
                        <h5>Información DRTC</h5>
                        <button class="btn btn-info" onclick="infoGeneral()">Ver Info</button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-phone fa-2x text-secondary mb-3"></i>
                        <h5>Contacto</h5>
                        <p class="mb-0">Tel: (01) 123-4567<br>Email: info@drtc.gob.pe</p>
                    </div>
                </div>
            </div>
        </div>
    `);
}

// ==================== TRÁMITES ====================
function nuevoTramite() {
    showContent('Nuevo Trámite', `
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5><i class="fas fa-folder-plus"></i> Registrar Trámite</h5>
            </div>
            <div class="card-body">
                <form id="tramiteForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo de Trámite</label>
                            <select class="form-select" name="tipo_tramite" required>
                                <option value="">Seleccione...</option>
                                <option value="licencia_conducir">Licencia de Conducir</option>
                                <option value="renovacion_licencia">Renovación de Licencia</option>
                                <option value="tarjeta_propiedad">Tarjeta de Propiedad</option>
                                <option value="cambio_propietario">Cambio de Propietario</option>
                                <option value="revision_tecnica">Revisión Técnica</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Documento</label>
                            <input type="text" class="form-control" name="documento" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="nombre" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" name="telefono" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea class="form-control" name="observaciones" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Registrar Trámite
                    </button>
                </form>
            </div>
        </div>
    `);
    
    document.getElementById('tramiteForm').onsubmit = function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('dashboard.php?api=tramites', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Trámite registrado: ' + data.numero, 'success');
                this.reset();
            } else {
                showToast('Error: ' + data.message, 'error');
            }
        });
    };
}

function tramitesPendientes() {
    fetch('dashboard.php?api=tramites')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const pendientes = data.tramites.filter(t => t.estado === 'pendiente');
                mostrarTramites('Trámites Pendientes', pendientes);
            }
        });
}

function historialTramites() {
    fetch('dashboard.php?api=tramites')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarTramites('Historial de Trámites', data.tramites);
            }
        });
}

function mostrarTramites(titulo, tramites) {
    // Si hay stats, mostrarlas
    let statsHtml = '';
    if (tramites.length > 0 && tramites[0].stats) {
        const stats = tramites[0].stats;
        statsHtml = `
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h4>${stats.total || 0}</h4>
                            <small>Total Trámites</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h4>${stats.pendientes || 0}</h4>
                            <small>Pendientes</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h4>${stats.en_proceso || 0}</h4>
                            <small>En Proceso</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h4>${stats.completados || 0}</h4>
                            <small>Completados</small>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    showContent(titulo, `
        ${statsHtml}
        <div class="card">
            <div class="card-body">
                ${tramites.length > 0 ? `
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Número</th>
                                    <th>Tipo</th>
                                    <th>Solicitante</th>
                                    <th>Teléfono</th>
                                    <th>Fecha Registro</th>
                                    <th>Días Transcurridos</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${tramites.map(t => {
                                    const estadoBadge = {
                                        'pendiente': '<span class="badge bg-warning">Pendiente</span>',
                                        'proceso': '<span class="badge bg-info">En Proceso</span>',
                                        'completado': '<span class="badge bg-success">Completado</span>',
                                        'rechazado': '<span class="badge bg-danger">Rechazado</span>'
                                    }[t.estado] || '<span class="badge bg-secondary">' + t.estado + '</span>';
                                    
                                    const diasClass = t.dias_transcurridos > 10 ? 'text-danger' : 
                                                     t.dias_transcurridos > 5 ? 'text-warning' : 'text-muted';
                                    
                                    return `
                                        <tr>
                                            <td><strong>${t.numero_tramite}</strong></td>
                                            <td>${t.tipo_tramite}</td>
                                            <td>${t.nombre_solicitante}</td>
                                            <td>${t.telefono_solicitante || 'N/A'}</td>
                                            <td>${formatDate(t.fecha_registro)}</td>
                                            <td><span class="${diasClass}">${t.dias_transcurridos} días</span></td>
                                            <td>${estadoBadge}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-info" onclick="verDetalleTramite(${t.id})" title="Ver detalle">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    ${t.estado === 'pendiente' ? `
                                                        <button class="btn btn-success" onclick="procesarTramite(${t.id})" title="Procesar">
                                                            <i class="fas fa-cog"></i>
                                                        </button>
                                                    ` : ''}
                                                </div>
                                            </td>
                                        </tr>
                                    `;
                                }).join('')}
                            </tbody>
                        </table>
                    </div>
                ` : `
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-folder-open fa-3x mb-3"></i>
                        <h5>No hay trámites</h5>
                        <p>No se encontraron trámites para mostrar</p>
                    </div>
                `}
            </div>
        </div>
    `);
}

// ==================== CONSULTAS ESPECÍFICAS ====================
function consultarActas() {
    showModal('Consultar Actas', `
        <form id="consultaForm">
            <div class="mb-3">
                <label class="form-label">Buscar por</label>
                <select class="form-select" name="tipo" id="tipoConsulta">
                    <option value="numero_acta">Número de Acta</option>
                    <option value="placa">Placa</option>
                    <option value="documento">Documento</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Valor</label>
                <input type="text" class="form-control" name="valor" required>
            </div>
            <div id="resultados"></div>
        </form>
    `, [
        { text: 'Buscar', class: 'btn-primary', onclick: 'buscarActas()' },
        { text: 'Cerrar', class: 'btn-secondary', dismiss: true }
    ]);
}

function buscarActas() {
    const form = document.getElementById('consultaForm');
    const formData = new FormData(form);
    
    fetch('dashboard.php?api=consultar-actas', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            tipo_busqueda: formData.get('tipo'),
            valor_busqueda: formData.get('valor')
        })
    })
    .then(response => response.json())
    .then(data => {
        const resultados = document.getElementById('resultados');
        if (data.success && data.actas.length > 0) {
            resultados.innerHTML = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Encontradas ${data.actas.length} acta(s)
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Número</th>
                                <th>Placa</th>
                                <th>Conductor</th>
                                <th>Documento</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.actas.map(a => {
                                const estadoBadge = {
                                    'Pendiente': '<span class="badge bg-warning">Pendiente</span>',
                                    'Procesada': '<span class="badge bg-success">Procesada</span>',
                                    'Anulada': '<span class="badge bg-danger">Anulada</span>',
                                    'Pagada': '<span class="badge bg-info">Pagada</span>'
                                }[a.estado_texto] || '<span class="badge bg-secondary">Pendiente</span>';
                                
                                return `
                                    <tr>
                                        <td><strong>${a.numero_acta || 'N/A'}</strong></td>
                                        <td><span class="badge bg-primary">${a.placa_vehiculo || 'N/A'}</span></td>
                                        <td>${a.nombre_conductor || 'N/A'}</td>
                                        <td>${a.ruc_dni || 'N/A'}</td>
                                        <td>${formatDate(a.fecha_intervencion)}</td>
                                        <td>${estadoBadge}</td>
                                        <td>${a.monto_multa ? 'S/ ' + parseFloat(a.monto_multa).toFixed(2) : 'N/A'}</td>
                                    </tr>
                                `;
                            }).join('')}
                        </tbody>
                    </table>
                </div>
            `;
        } else {
            resultados.innerHTML = '<div class="alert alert-warning">No se encontraron resultados</div>';
        }
    });
}

function consultarVehiculos() {
    showModal('Consultar Vehículo', `
        <form id="vehiculoForm">
            <div class="mb-3">
                <label class="form-label">Placa</label>
                <input type="text" class="form-control" name="placa" required>
            </div>
            <div id="resultadosVehiculo"></div>
        </form>
    `, [
        { text: 'Buscar', class: 'btn-success', onclick: 'buscarVehiculo()' },
        { text: 'Cerrar', class: 'btn-secondary', dismiss: true }
    ]);
}

function buscarVehiculo() {
    const placa = document.querySelector('[name="placa"]').value;
    
    fetch('dashboard.php?api=consultar-vehiculo', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ placa })
    })
    .then(response => response.json())
    .then(data => {
        const resultados = document.getElementById('resultadosVehiculo');
        if (data.success && data.vehiculo) {
            const v = data.vehiculo;
            let html = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Vehículo encontrado
                </div>
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Información del Vehículo</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Placa:</strong> <span class="badge bg-primary">${v.placa}</span></p>
                                <p><strong>Marca:</strong> ${v.marca || 'N/A'}</p>
                                <p><strong>Modelo:</strong> ${v.modelo || 'N/A'}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Año:</strong> ${v.ano || v.año || 'N/A'}</p>
                                <p><strong>Color:</strong> ${v.color || 'N/A'}</p>
                                <p><strong>Estado:</strong> <span class="badge bg-success">${v.estado || 'Vigente'}</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Mostrar actas relacionadas si existen
            if (data.actas && data.actas.length > 0) {
                html += `
                    <div class="card mt-3">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="fas fa-file-alt"></i> Actas Relacionadas (${data.actas.length})</h6>
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
                                        ${data.actas.map(acta => `
                                            <tr>
                                                <td><strong>${acta.numero_acta || 'N/A'}</strong></td>
                                                <td>${formatDate(acta.fecha_intervencion)}</td>
                                                <td>${acta.nombre_conductor || 'N/A'}</td>
                                                <td><span class="badge bg-warning">${acta.estado_texto || 'Pendiente'}</span></td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            resultados.innerHTML = html;
        } else {
            resultados.innerHTML = '<div class="alert alert-warning">Vehículo no encontrado</div>';
        }
    });
}

function consultarConductores() {
    showModal('Consultar Conductor', `
        <form id="conductorForm">
            <div class="mb-3">
                <label class="form-label">Buscar por</label>
                <select class="form-select" name="tipo">
                    <option value="dni">DNI</option>
                    <option value="licencia">Licencia</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Valor</label>
                <input type="text" class="form-control" name="valor" required>
            </div>
            <div id="resultadosConductor"></div>
        </form>
    `, [
        { text: 'Buscar', class: 'btn-warning', onclick: 'buscarConductor()' },
        { text: 'Cerrar', class: 'btn-secondary', dismiss: true }
    ]);
}

function buscarConductor() {
    const form = document.getElementById('conductorForm');
    const formData = new FormData(form);
    
    fetch('dashboard.php?api=consultar-conductor', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            tipo_busqueda: formData.get('tipo'),
            valor_busqueda: formData.get('valor')
        })
    })
    .then(response => response.json())
    .then(data => {
        const resultados = document.getElementById('resultadosConductor');
        if (data.success && data.conductores.length > 0) {
            let html = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Se encontraron ${data.conductores.length} conductor(es)
                </div>
            `;
            
            data.conductores.forEach((c, index) => {
                html += `
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="card-title">Conductor ${index + 1}</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>DNI:</strong> ${c.dni || 'N/A'}</p>
                                    <p><strong>Nombres:</strong> ${c.nombres || 'N/A'}</p>
                                    <p><strong>Apellidos:</strong> ${c.apellidos || 'N/A'}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Licencia:</strong> ${c.numero_licencia || 'N/A'}</p>
                                    <p><strong>Categoría:</strong> ${c.clase_categoria || 'N/A'}</p>
                                    <p><strong>Estado:</strong> <span class="badge bg-success">${c.estado_licencia || 'Vigente'}</span></p>
                                </div>
                            </div>
                `;
                
                // Mostrar actas relacionadas si existen
                const actas = data.actas_relacionadas && data.actas_relacionadas[c.id || c.dni] || [];
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
                                        ${actas.map(acta => `
                                            <tr>
                                                <td><strong>${acta.numero_acta || 'N/A'}</strong></td>
                                                <td>${formatDate(acta.fecha_intervencion)}</td>
                                                <td><span class="badge bg-primary">${acta.placa_vehiculo || 'N/A'}</span></td>
                                                <td><span class="badge bg-warning">${acta.estado_texto || 'Pendiente'}</span></td>
                                            </tr>
                                        `).join('')}
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
            
            resultados.innerHTML = html;
        } else {
            resultados.innerHTML = '<div class="alert alert-warning">Conductor no encontrado</div>';
        }
    });
}

function infoGeneral() {
    showContent('Información DRTC', `
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h6><i class="fas fa-clock"></i> Horarios</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Lunes a Viernes:</strong> 8:00 AM - 5:00 PM</p>
                        <p><strong>Sábados:</strong> 8:00 AM - 12:00 PM</p>
                        <p><strong>Domingos:</strong> Cerrado</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h6><i class="fas fa-map-marker-alt"></i> Ubicación</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Dirección:</strong> Av. Principal 123</p>
                        <p><strong>Distrito:</strong> Cercado</p>
                        <p><strong>Teléfono:</strong> (01) 123-4567</p>
                    </div>
                </div>
            </div>
        </div>
    `);
}

// ==================== FUNCIONES AUXILIARES ====================
function showContent(title, content) {
    const container = document.getElementById('contentContainer');
    if (container) {
        container.innerHTML = `
            <div class="content-section active">
                <h2>${title}</h2>
                ${content}
            </div>
        `;
    }
    hideAllSections();
}

function hideAllSections() {
    const dashboard = document.getElementById('dashboard-section');
    if (dashboard) dashboard.style.display = 'none';
}

function showModal(title, content, buttons = []) {
    const modal = document.getElementById('generalModal');
    if (!modal) return;
    
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalBody').innerHTML = content;
    
    const footer = document.getElementById('modalFooter');
    footer.innerHTML = buttons.map(btn => 
        `<button type="button" class="btn ${btn.class}" 
         ${btn.dismiss ? 'data-bs-dismiss="modal"' : ''} 
         ${btn.onclick ? `onclick="${btn.onclick}"` : ''}>${btn.text}</button>`
    ).join('');
    
    new bootstrap.Modal(modal).show();
}

function showToast(message, type = 'info') {
    const toastHtml = `
        <div class="toast" role="alert">
            <div class="toast-body bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} text-white">
                ${message}
            </div>
        </div>
    `;
    
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
    }
    
    container.insertAdjacentHTML('beforeend', toastHtml);
    const toast = new bootstrap.Toast(container.lastElementChild);
    toast.show();
    
    setTimeout(() => container.lastElementChild?.remove(), 5000);
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('es-PE');
}

function formatTime(dateString) {
    return new Date(dateString).toLocaleTimeString('es-PE', { 
        hour: '2-digit', 
        minute: '2-digit' 
    });
}

function agregarACola() {
    const form = document.getElementById('atencionForm');
    const formData = new FormData(form);
    
    fetch('dashboard.php?api=cola-espera', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Cliente agregado a cola: ' + data.ticket, 'success');
        }
    });
}

function agregarClienteCola() {
    showModal('Agregar a Cola', `
        <form id="colaForm">
            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" class="form-control" name="nombre" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Documento</label>
                <input type="text" class="form-control" name="documento" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Tipo de Consulta</label>
                <select class="form-select" name="tipo_consulta" required>
                    <option value="consulta_acta">Consulta de Acta</option>
                    <option value="tramite_licencia">Trámite de Licencia</option>
                    <option value="otros">Otros</option>
                </select>
            </div>
        </form>
    `, [
        { text: 'Agregar', class: 'btn-primary', onclick: 'submitCola()' },
        { text: 'Cancelar', class: 'btn-secondary', dismiss: true }
    ]);
}

function submitCola() {
    const form = document.getElementById('colaForm');
    const formData = new FormData(form);
    
    fetch('dashboard.php?api=cola-espera', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Cliente agregado: ' + data.ticket, 'success');
            bootstrap.Modal.getInstance(document.getElementById('generalModal')).hide();
            cargarCola();
        }
    });
}

function atenderCliente(id) {
    if (confirm('¿Iniciar atención a este cliente?')) {
        fetch('dashboard.php?api=atender-cliente', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                cargarCola();
                loadVentanillaStats();
            } else {
                showToast('Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error de conexión', 'error');
        });
    }
}

function verDetalleCliente(id) {
    // Buscar el cliente en los datos cargados
    fetch(`dashboard.php?api=cola-espera`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const cliente = data.cola.find(c => c.id == id);
                if (cliente) {
                    showModal('Detalle del Cliente', `
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Ticket:</strong> ${cliente.numero_ticket}</p>
                                <p><strong>Nombre:</strong> ${cliente.nombre_cliente}</p>
                                <p><strong>Documento:</strong> ${cliente.documento_cliente}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Tipo Consulta:</strong> ${cliente.tipo_consulta}</p>
                                <p><strong>Hora Llegada:</strong> ${formatTime(cliente.hora_llegada)}</p>
                                <p><strong>Tiempo Espera:</strong> ${cliente.tiempo_espera_texto}</p>
                                <p><strong>Estado:</strong> <span class="badge bg-warning">${cliente.estado}</span></p>
                            </div>
                        </div>
                    `, [
                        { text: 'Atender Ahora', class: 'btn-success', onclick: `atenderCliente(${id}); bootstrap.Modal.getInstance(document.getElementById('generalModal')).hide();` },
                        { text: 'Cerrar', class: 'btn-secondary', dismiss: true }
                    ]);
                }
            }
        });
}

function cancelarCliente(id) {
    if (confirm('¿Está seguro de cancelar la atención de este cliente?')) {
        fetch('dashboard.php?api=cancelar-cliente', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'warning');
                cargarCola();
                loadVentanillaStats();
            } else {
                showToast('Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error de conexión', 'error');
        });
    }
}

function verDetalleTramite(id) {
    fetch(`dashboard.php?api=detalle-tramite&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.tramite) {
                const t = data.tramite;
                const estadoBadge = {
                    'pendiente': '<span class="badge bg-warning">Pendiente</span>',
                    'proceso': '<span class="badge bg-info">En Proceso</span>',
                    'completado': '<span class="badge bg-success">Completado</span>',
                    'rechazado': '<span class="badge bg-danger">Rechazado</span>'
                }[t.estado] || '<span class="badge bg-secondary">' + t.estado + '</span>';
                
                showModal('Detalle del Trámite', `
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Número:</strong> ${t.numero_tramite}</p>
                            <p><strong>Tipo:</strong> ${t.tipo_tramite}</p>
                            <p><strong>Solicitante:</strong> ${t.nombre_solicitante}</p>
                            <p><strong>Documento:</strong> ${t.documento_solicitante}</p>
                            <p><strong>Teléfono:</strong> ${t.telefono_solicitante}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Estado:</strong> ${estadoBadge}</p>
                            <p><strong>Fecha Registro:</strong> ${formatDate(t.fecha_registro)}</p>
                            <p><strong>Días Transcurridos:</strong> <span class="${t.dias_transcurridos > 10 ? 'text-danger' : 'text-muted'}">${t.dias_transcurridos} días</span></p>
                            ${t.fecha_estimada_finalizacion ? `<p><strong>Fecha Estimada:</strong> ${formatDate(t.fecha_estimada_finalizacion)}</p>` : ''}
                            ${t.dias_restantes !== null ? `<p><strong>Días Restantes:</strong> <span class="${t.dias_restantes < 0 ? 'text-danger' : 'text-success'}">${t.dias_restantes} días</span></p>` : ''}
                            <p><strong>Registrado por:</strong> ${t.registrado_por}</p>
                            ${t.procesado_por ? `<p><strong>Procesado por:</strong> ${t.procesado_por}</p>` : ''}
                        </div>
                    </div>
                    ${t.observaciones ? `
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <p><strong>Observaciones:</strong></p>
                                <div class="alert alert-light">${t.observaciones}</div>
                            </div>
                        </div>
                    ` : ''}
                `, [
                    { text: 'Cerrar', class: 'btn-secondary', dismiss: true },
                    ...(t.estado === 'pendiente' ? [{
                        text: 'Procesar',
                        class: 'btn-success',
                        onclick: `procesarTramite(${id}); bootstrap.Modal.getInstance(document.getElementById('generalModal')).hide();`
                    }] : [])
                ]);
            } else {
                showToast('Error al cargar detalle del trámite', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error de conexión', 'error');
        });
}

function procesarTramite(id) {
    if (confirm('¿Marcar este trámite como en proceso?')) {
        fetch('dashboard.php?api=procesar-tramite', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                // Recargar la lista actual
                if (typeof tramitesPendientes === 'function') {
                    tramitesPendientes();
                }
            } else {
                showToast('Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error de conexión', 'error');
        });
    }
}

// ==================== TOGGLE SUBMENU ====================
function toggleSubmenu(menuId, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    const submenu = document.getElementById('submenu-' + menuId);
    const toggle = event ? event.target.closest('.sidebar-toggle') : null;
    
    if (!submenu) {
        console.error('Submenu no encontrado:', 'submenu-' + menuId);
        return;
    }
    
    // Cerrar otros submenús
    document.querySelectorAll('.sidebar-submenu').forEach(menu => {
        if (menu !== submenu) {
            menu.classList.remove('show');
            menu.style.display = 'none';
        }
    });
    
    // Remover clase expanded de otros toggles
    document.querySelectorAll('.sidebar-toggle').forEach(t => {
        if (t !== toggle) {
            t.classList.remove('expanded');
        }
    });
    
    // Toggle del submenu actual
    if (submenu.classList.contains('show')) {
        submenu.classList.remove('show');
        submenu.style.display = 'none';
        if (toggle) toggle.classList.remove('expanded');
    } else {
        submenu.classList.add('show');
        submenu.style.display = 'block';
        if (toggle) toggle.classList.add('expanded');
    }
}

// Exponer la función globalmente
window.toggleSubmenu = toggleSubmenu;

console.log('✅ Ventanilla Simple cargada');