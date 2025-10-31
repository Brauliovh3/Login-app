/**
 * Dashboard de Infracciones - Visualización Avanzada
 * Maneja la visualización detallada de infracciones sin estadísticas básicas
 */

// Función principal para cargar la gestión de infracciones
function loadGestionarInfracciones() {
    showLoading();
    
    fetch('dashboard.php?api=infracciones')
        .then(response => response.json())
        .then(data => {
            hideLoading();
            
            if (data.success) {
                mostrarDashboardInfracciones(data);
            } else {
                showAlert('Error al cargar infracciones: ' + (data.message || 'Error desconocido'), 'error');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            showAlert('Error de conexión al cargar infracciones', 'error');
        });
}

// Mostrar dashboard completo de infracciones
function mostrarDashboardInfracciones(data) {
    const { infracciones, estadisticas, mas_frecuentes } = data;
    
    const content = `
        <div class="infracciones-dashboard">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-exclamation-triangle text-warning"></i> Gestión de Infracciones</h2>
                <div class="btn-group">
                    <button class="btn btn-primary" onclick="mostrarCrearInfraccion()">
                        <i class="fas fa-plus"></i> Nueva Infracción
                    </button>
                    <button class="btn btn-info" onclick="exportarInfracciones()">
                        <i class="fas fa-download"></i> Exportar
                    </button>
                </div>
            </div>

            <!-- Panel de Información Detallada -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-gradient-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información del Sistema de Infracciones</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6 class="text-primary">Marco Legal</h6>
                                    <p class="mb-3">
                                        Las infracciones están basadas en el <strong>Reglamento Nacional de Administración de Transportes (RENAT)</strong>, 
                                        establecido por el D.S. N° 017-2009-MTC y sus modificatorias. Este sistema permite la gestión 
                                        integral de infracciones de transporte terrestre.
                                    </p>
                                    
                                    <h6 class="text-primary">Clasificación por Gravedad</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="badge bg-danger me-2">Muy Grave</span>
                                                <span>${estadisticas.muy_graves || 0} infracciones</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="badge bg-warning me-2">Grave</span>
                                                <span>${estadisticas.graves || 0} infracciones</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="badge bg-info me-2">Leve</span>
                                                <span>${estadisticas.leves || 0} infracciones</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-primary">Resumen del Catálogo</h6>
                                    <div class="bg-light p-3 rounded">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Total de Infracciones:</span>
                                            <strong class="text-primary">${estadisticas.total_infracciones || 0}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Activas:</span>
                                            <span class="text-success">${estadisticas.activas || 0}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Inactivas:</span>
                                            <span class="text-muted">${estadisticas.inactivas || 0}</span>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Pecuniarias:</span>
                                            <span>${estadisticas.pecuniarias || 0}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>No Pecuniarias:</span>
                                            <span>${estadisticas.no_pecuniarias || 0}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Infracciones Más Frecuentes -->
            ${mas_frecuentes && mas_frecuentes.length > 0 ? `
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-gradient-warning text-dark">
                            <h5 class="mb-0"><i class="fas fa-chart-line"></i> Infracciones Más Frecuentes</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                ${mas_frecuentes.map((infraccion, index) => `
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-start">
                                            <span class="badge bg-primary me-3 mt-1">${index + 1}</span>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">${infraccion.codigo_infraccion}</h6>
                                                <p class="mb-1 text-muted small">${infraccion.descripcion}</p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge bg-${getGravedadColor(infraccion.gravedad)}">${infraccion.gravedad}</span>
                                                    <small class="text-muted">${infraccion.frecuencia || 0} casos</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            ` : ''}

            <!-- Filtros y Búsqueda -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Buscar por código o descripción</label>
                                    <input type="text" class="form-control" id="buscarInfraccion" 
                                           placeholder="Ej: F.1 o transporte público">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Gravedad</label>
                                    <select class="form-select" id="filtroGravedad">
                                        <option value="">Todas</option>
                                        <option value="Muy grave">Muy Grave</option>
                                        <option value="Grave">Grave</option>
                                        <option value="Leve">Leve</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Aplica sobre</label>
                                    <select class="form-select" id="filtroAplicaSobre">
                                        <option value="">Todos</option>
                                        <option value="Transportista">Transportista</option>
                                        <option value="Conductor">Conductor</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Estado</label>
                                    <select class="form-select" id="filtroEstado">
                                        <option value="">Todos</option>
                                        <option value="activo">Activo</option>
                                        <option value="inactivo">Inactivo</option>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">&nbsp;</label>
                                    <button class="btn btn-primary w-100" onclick="aplicarFiltrosInfracciones()">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Infracciones -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Catálogo de Infracciones (${infracciones.length} registros)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="tablaInfracciones">
                            <thead class="table-dark">
                                <tr>
                                    <th width="80">Código</th>
                                    <th width="120">Aplica Sobre</th>
                                    <th>Descripción</th>
                                    <th width="100">Gravedad</th>
                                    <th width="120">Sanción</th>
                                    <th width="150">Medida Preventiva</th>
                                    <th width="80">Estado</th>
                                    <th width="120">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${infracciones.map(infraccion => `
                                    <tr data-codigo="${infraccion.codigo_infraccion}" 
                                        data-gravedad="${infraccion.gravedad}" 
                                        data-aplica="${infraccion.aplica_sobre}"
                                        data-estado="${infraccion.estado}">
                                        <td>
                                            <span class="badge bg-dark">${infraccion.codigo_infraccion}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-${infraccion.aplica_sobre === 'Conductor' ? 'info' : 'warning'} text-dark">
                                                ${infraccion.aplica_sobre}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 300px;" title="${infraccion.descripcion}">
                                                ${infraccion.descripcion}
                                            </div>
                                            <small class="text-muted">${infraccion.reglamento} - ${infraccion.norma_modificatoria}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-${getGravedadColor(infraccion.gravedad)}">
                                                ${infraccion.gravedad}
                                            </span>
                                        </td>
                                        <td>
                                            <strong>${infraccion.sancion}</strong>
                                            ${infraccion.monto_base_uit > 0 ? `<br><small class="text-success">S/ ${infraccion.monto_base_uit.toFixed(2)}</small>` : ''}
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                ${infraccion.medida_preventiva || 'Ninguna'}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge bg-${infraccion.estado === 'activo' ? 'success' : 'secondary'}">
                                                ${infraccion.estado}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-info" onclick="verDetalleInfraccion('${infraccion.id}')" 
                                                        title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-warning" onclick="editarInfraccion('${infraccion.id}')" 
                                                        title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    loadSection('infracciones', content);
    
    // Configurar eventos de filtrado
    setupInfraccionesFiltros();
}

// Configurar filtros de infracciones
function setupInfraccionesFiltros() {
    const buscarInput = document.getElementById('buscarInfraccion');
    const filtroGravedad = document.getElementById('filtroGravedad');
    const filtroAplicaSobre = document.getElementById('filtroAplicaSobre');
    const filtroEstado = document.getElementById('filtroEstado');
    
    if (buscarInput) {
        buscarInput.addEventListener('input', debounce(aplicarFiltrosInfracciones, 300));
    }
    
    [filtroGravedad, filtroAplicaSobre, filtroEstado].forEach(filtro => {
        if (filtro) {
            filtro.addEventListener('change', aplicarFiltrosInfracciones);
        }
    });
}

// Aplicar filtros a la tabla de infracciones
function aplicarFiltrosInfracciones() {
    const buscar = document.getElementById('buscarInfraccion')?.value.toLowerCase() || '';
    const gravedad = document.getElementById('filtroGravedad')?.value || '';
    const aplicaSobre = document.getElementById('filtroAplicaSobre')?.value || '';
    const estado = document.getElementById('filtroEstado')?.value || '';
    
    const filas = document.querySelectorAll('#tablaInfracciones tbody tr');
    let visibles = 0;
    
    filas.forEach(fila => {
        const codigo = fila.dataset.codigo.toLowerCase();
        const descripcion = fila.cells[2].textContent.toLowerCase();
        const filaGravedad = fila.dataset.gravedad;
        const filaAplica = fila.dataset.aplica;
        const filaEstado = fila.dataset.estado;
        
        const coincideBusqueda = !buscar || codigo.includes(buscar) || descripcion.includes(buscar);
        const coincideGravedad = !gravedad || filaGravedad === gravedad;
        const coincideAplica = !aplicaSobre || filaAplica === aplicaSobre;
        const coincideEstado = !estado || filaEstado === estado;
        
        const mostrar = coincideBusqueda && coincideGravedad && coincideAplica && coincideEstado;
        
        fila.style.display = mostrar ? '' : 'none';
        if (mostrar) visibles++;
    });
    
    // Actualizar contador
    const header = document.querySelector('.card-header h5');
    if (header) {
        header.innerHTML = `<i class="fas fa-list"></i> Catálogo de Infracciones (${visibles} registros mostrados)`;
    }
}

// Ver detalle completo de una infracción
function verDetalleInfraccion(id) {
    showLoading();
    
    fetch(`dashboard.php?api=infracciones&id=${id}`)
        .then(response => response.json())
        .then(data => {
            hideLoading();
            
            if (data.success && data.infraccion) {
                mostrarModalDetalleInfraccion(data.infraccion);
            } else {
                showAlert('Error al cargar el detalle de la infracción', 'error');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            showAlert('Error de conexión', 'error');
        });
}

// Mostrar modal con detalle completo de infracción
function mostrarModalDetalleInfraccion(infraccion) {
    const modalContent = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-primary">Información Básica</h6>
                <table class="table table-sm">
                    <tr><td><strong>Código:</strong></td><td><span class="badge bg-dark">${infraccion.codigo_infraccion}</span></td></tr>
                    <tr><td><strong>Aplica sobre:</strong></td><td>${infraccion.aplica_sobre}</td></tr>
                    <tr><td><strong>Gravedad:</strong></td><td><span class="badge bg-${getGravedadColor(infraccion.gravedad)}">${infraccion.gravedad}</span></td></tr>
                    <tr><td><strong>Estado:</strong></td><td><span class="badge bg-${infraccion.estado === 'activo' ? 'success' : 'secondary'}">${infraccion.estado}</span></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="text-primary">Marco Legal</h6>
                <table class="table table-sm">
                    <tr><td><strong>Reglamento:</strong></td><td>${infraccion.reglamento}</td></tr>
                    <tr><td><strong>Norma:</strong></td><td>${infraccion.norma_modificatoria}</td></tr>
                    <tr><td><strong>Tipo:</strong></td><td>${infraccion.tipo}</td></tr>
                    <tr><td><strong>Clase de Pago:</strong></td><td>${infraccion.clase_pago}</td></tr>
                </table>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-12">
                <h6 class="text-primary">Descripción de la Infracción</h6>
                <div class="alert alert-light">
                    ${infraccion.descripcion}
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-primary">Sanción</h6>
                <div class="alert alert-warning">
                    <strong>${infraccion.sancion}</strong>
                    ${infraccion.monto_base_uit > 0 ? `<br><small>Equivalente a S/ ${infraccion.monto_base_uit.toFixed(2)}</small>` : ''}
                </div>
            </div>
            <div class="col-md-6">
                <h6 class="text-primary">Medida Preventiva</h6>
                <div class="alert alert-info">
                    ${infraccion.medida_preventiva || 'No aplica medida preventiva específica'}
                </div>
            </div>
        </div>
        
        ${infraccion.otros_responsables_otros_beneficios ? `
        <div class="row">
            <div class="col-12">
                <h6 class="text-primary">Otros Responsables y Beneficios</h6>
                <div class="alert alert-secondary">
                    ${infraccion.otros_responsables_otros_beneficios}
                </div>
            </div>
        </div>
        ` : ''}
    `;
    
    showModal(`Detalle de Infracción ${infraccion.codigo_infraccion}`, modalContent);
}

// Obtener color según gravedad
function getGravedadColor(gravedad) {
    switch (gravedad) {
        case 'Muy grave': return 'danger';
        case 'Grave': return 'warning';
        case 'Leve': return 'info';
        default: return 'secondary';
    }
}

// Exportar infracciones
function exportarInfracciones() {
    showAlert('Función de exportación en desarrollo', 'info');
}

// Función debounce para optimizar búsquedas
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Hacer funciones disponibles globalmente
window.loadGestionarInfracciones = loadGestionarInfracciones;
window.verDetalleInfraccion = verDetalleInfraccion;
window.aplicarFiltrosInfracciones = aplicarFiltrosInfracciones;
window.exportarInfracciones = exportarInfracciones;