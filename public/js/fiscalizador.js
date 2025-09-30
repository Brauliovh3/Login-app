/**
 * SISTEMA DE GESTIÓN - MÓDULO FISCALIZADOR
 * Funcionalidades específicas para el rol fiscalizador
 */

console.log('📋 Cargando módulo fiscalizador...');

// Variable global para verificar que el usuario es fiscalizador
let isFiscalizador = false;

// Inicialización del módulo fiscalizador
document.addEventListener('DOMContentLoaded', function() {
    if (window.dashboardUserRole === 'fiscalizador') {
        isFiscalizador = true;
        console.log('✅ Módulo fiscalizador habilitado para:', window.dashboardUserName);
        initializeFiscalizadorModule();
    }
});

function initializeFiscalizadorModule() {
    console.log('🚀 Inicializando módulo fiscalizador...');
    
    // Cargar estadísticas del dashboard al inicio
    loadDashboardStatsFiscalizador();
    
    // Configurar eventos específicos del fiscalizador
    setupFiscalizadorEvents();
}

function setupFiscalizadorEvents() {
    // Configurar eventos específicos para fiscalizador
    console.log('⚙️ Configurando eventos del fiscalizador...');
}

// ==================== DASHBOARD STATS FISCALIZADOR ====================
async function loadDashboardStatsFiscalizador() {
    console.log('📊 Cargando estadísticas del fiscalizador...');
    
    try {
        const response = await fetch(`${window.location.origin}${window.location.pathname}?api=dashboard-stats`);
        const result = await response.json();
        
        if (result.success && result.stats) {
            updateDashboardStatsFiscalizador(result.stats);
        } else {
            console.error('❌ Error al cargar estadísticas:', result.message);
        }
    } catch (error) {
        console.error('❌ Error al cargar estadísticas del fiscalizador:', error);
    }
}

function updateDashboardStatsFiscalizador(stats) {
    console.log('📈 Actualizando estadísticas del fiscalizador:', stats);
    
    // Actualizar contadores específicos para fiscalizador
    if (document.getElementById('total-actas')) {
        document.getElementById('total-actas').textContent = stats.total_actas || 0;
    }
    
    if (document.getElementById('total-conductores')) {
        document.getElementById('total-conductores').textContent = stats.total_conductores || 0;
    }
    
    if (document.getElementById('total-vehiculos')) {
        document.getElementById('total-vehiculos').textContent = stats.total_vehiculos || 0;
    }
    
    if (document.getElementById('total-notifications')) {
        document.getElementById('total-notifications').textContent = stats.actas_pendientes || 0;
    }
    
    // Crear cards específicas para fiscalizador
    createFiscalizadorSpecificCards(stats);
}

function createFiscalizadorSpecificCards(stats) {
    const dashboardContent = document.getElementById('dashboardContent');
    if (!dashboardContent) return;
    
    // Agregar cards específicas para fiscalizador
    const fiscalizadorCardsHTML = `
        <div class="col-md-3 mb-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title">Actas Procesadas</h5>
                            <h3>${stats.actas_procesadas || 0}</h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title">Actas Pendientes</h5>
                            <h3>${stats.actas_pendientes || 0}</h3>
                        </div>
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title">Total Multas</h5>
                            <h3>S/ ${parseFloat(stats.total_multas || 0).toFixed(2)}</h3>
                        </div>
                        <i class="fas fa-money-bill fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title">Mis Inspecciones</h5>
                            <h3>${stats.total_inspecciones || 0}</h3>
                        </div>
                        <i class="fas fa-clipboard-check fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Agregar las cards adicionales
    dashboardContent.insertAdjacentHTML('beforeend', fiscalizadorCardsHTML);
}

// ==================== GESTIÓN DE INFRACCIONES ====================
async function loadInfracciones(event) {
    console.log('📋 Cargando gestión de infracciones...');
    
    // Determinar qué sección cargar basado en el data-section
    let seccion = 'gestionar-infracciones'; // Por defecto
    if (event && event.target) {
        seccion = event.target.getAttribute('data-section') || 
                  event.target.closest('a').getAttribute('data-section') || 
                  'gestionar-infracciones';
    }
    
    console.log('📋 Cargando sección:', seccion);
    
    // Actualizar título del contenido principal
    const mainTitle = document.querySelector('#main-content h2');
    if (mainTitle) {
        mainTitle.innerHTML = '<i class="fas fa-exclamation-triangle text-warning"></i> Gestión de Infracciones';
    }
    
    // Cargar la sección correspondiente
    switch(seccion) {
        case 'gestionar-infracciones':
            await loadGestionarInfracciones();
            break;
        case 'nueva-infraccion':
            await loadNuevaInfraccion();
            break;
        case 'buscar-infracciones':
            await loadBuscarInfracciones();
            break;
        case 'estadisticas-infracciones':
            await loadEstadisticasInfracciones();
            break;
        default:
            await loadGestionarInfracciones();
    }
}

async function loadGestionarInfracciones() {
    console.log('📋 Cargando gestión de infracciones...');
    
    try {
        const response = await fetch(`${window.location.origin}${window.location.pathname}?api=infracciones`);
        const result = await response.json();
        
        if (result.success && result.infracciones) {
            displayInfraccionesInterface(result.infracciones);
        } else {
            console.error('❌ Error al cargar infracciones:', result.message);
            // Si no hay API, mostrar interfaz vacía
            displayInfraccionesInterface([]);
        }
    } catch (error) {
        console.error('❌ Error al cargar infracciones:', error);
        // Si hay error de conexión, mostrar interfaz vacía
        displayInfraccionesInterface([]);
    }
}

async function loadNuevaInfraccion() {
    console.log('📋 Cargando formulario nueva infracción...');
    
    const content = document.getElementById('dynamic-content');
    if (!content) return;
    
    const nuevaInfraccionHTML = `
        <div class="container-fluid">
            <div class="card shadow">
                <div class="card-header">
                    <h3 class="mb-0">
                        <i class="fas fa-plus-circle text-success"></i>
                        Nueva Infracción
                    </h3>
                </div>
                
                <div class="card-body">
                    <form id="formNuevaInfraccion">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="codigoNueva" class="form-label">Código de Infracción *</label>
                                    <input type="text" class="form-control" id="codigoNueva" required 
                                           placeholder="Ej: G.01, L.02, MG.01">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="gravedadNueva" class="form-label">Gravedad *</label>
                                    <select class="form-select" id="gravedadNueva" required>
                                        <option value="">Seleccionar gravedad...</option>
                                        <option value="leve">Leve</option>
                                        <option value="grave">Grave</option>
                                        <option value="muy_grave">Muy Grave</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descripcionNueva" class="form-label">Descripción *</label>
                            <textarea class="form-control" id="descripcionNueva" rows="3" required
                                      placeholder="Descripción detallada de la infracción..."></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="multaSolesNueva" class="form-label">Multa en Soles (S/) *</label>
                                    <input type="number" class="form-control" id="multaSolesNueva" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="multaUitNueva" class="form-label">Multa en UIT</label>
                                    <input type="number" class="form-control" id="multaUitNueva" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="puntosLicenciaNueva" class="form-label">Puntos de Licencia</label>
                                    <input type="number" class="form-control" id="puntosLicenciaNueva">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="baseLegalNueva" class="form-label">Base Legal</label>
                                    <input type="text" class="form-control" id="baseLegalNueva" 
                                           placeholder="Ej: Art. 318° Reglamento Nacional de Tránsito">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estadoNueva" class="form-label">Estado</label>
                                    <select class="form-select" id="estadoNueva">
                                        <option value="activo" selected>Activo</option>
                                        <option value="inactivo">Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="loadGestionarInfracciones()">
                                <i class="fas fa-arrow-left"></i> Volver a la Lista
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Infracción
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    `;
    
    content.innerHTML = nuevaInfraccionHTML;
    
    // Configurar evento del formulario
    document.getElementById('formNuevaInfraccion').addEventListener('submit', function(e) {
        e.preventDefault();
        guardarNuevaInfraccion();
    });
}

async function loadBuscarInfracciones() {
    console.log('📋 Cargando búsqueda de infracciones...');
    
    const content = document.getElementById('dynamic-content');
    if (!content) return;
    
    const buscarHTML = `
        <div class="container-fluid">
            <div class="card shadow">
                <div class="card-header">
                    <h3 class="mb-0">
                        <i class="fas fa-search text-info"></i>
                        Buscar Infracciones
                    </h3>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="buscarPorCodigo" class="form-label">Buscar por Código</label>
                                <input type="text" class="form-control" id="buscarPorCodigo" 
                                       placeholder="Ingrese código...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="buscarPorGravedad" class="form-label">Filtrar por Gravedad</label>
                                <select class="form-select" id="buscarPorGravedad">
                                    <option value="">Todas</option>
                                    <option value="leve">Leve</option>
                                    <option value="grave">Grave</option>
                                    <option value="muy_grave">Muy Grave</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" class="btn btn-primary w-100" onclick="ejecutarBusquedaInfracciones()">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div id="resultadosBusqueda">
                        <div class="text-center text-muted">
                            <i class="fas fa-search fa-3x mb-3"></i>
                            <p>Utilice los filtros de arriba para buscar infracciones</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    content.innerHTML = buscarHTML;
}

async function loadEstadisticasInfracciones() {
    console.log('📋 Cargando estadísticas de infracciones...');
    
    const content = document.getElementById('dynamic-content');
    if (!content) return;
    
    const estadisticasHTML = `
        <div class="container-fluid">
            <div class="card shadow">
                <div class="card-header">
                    <h3 class="mb-0">
                        <i class="fas fa-chart-bar text-primary"></i>
                        Estadísticas de Infracciones
                    </h3>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3>0</h3>
                                    <p>Total Infracciones</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3>0</h3>
                                    <p>Infracciones Leves</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h3>0</h3>
                                    <p>Infracciones Graves</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h3>0</h3>
                                    <p>Infracciones Muy Graves</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="text-center text-muted">
                        <i class="fas fa-chart-line fa-3x mb-3"></i>
                        <p>Aquí se mostrarán las estadísticas detalladas</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    content.innerHTML = estadisticasHTML;
}

function displayInfraccionesInterface(infracciones) {
    const content = document.getElementById('dynamic-content');
    if (!content) return;
    
    let infraccionesHTML = `
        <div class="container-fluid">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        Gestión de Infracciones
                    </h3>
                    <div>
                        <button type="button" class="btn btn-success me-2" onclick="loadNuevaInfraccion()">
                            <i class="fas fa-plus"></i> Nueva Infracción
                        </button>
                        <button type="button" class="btn btn-outline-primary" onclick="loadGestionarInfracciones()">
                            <i class="fas fa-sync"></i> Actualizar
                        </button>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="filtroGravedad" class="form-label">Filtrar por Gravedad:</label>
                            <select id="filtroGravedad" class="form-select" onchange="filtrarTablaInfracciones()">
                                <option value="">Todas las gravedades</option>
                                <option value="leve">Leve</option>
                                <option value="grave">Grave</option>
                                <option value="muy_grave">Muy Grave</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filtroEstado" class="form-label">Filtrar por Estado:</label>
                            <select id="filtroEstado" class="form-select" onchange="filtrarTablaInfracciones()">
                                <option value="">Todos los estados</option>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="busquedaInfraccion" class="form-label">Buscar:</label>
                            <input type="text" id="busquedaInfraccion" class="form-control" 
                                   placeholder="Buscar por código, descripción..." 
                                   onkeyup="filtrarTablaInfracciones()">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" onclick="limpiarFiltros()" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-eraser"></i> Limpiar
                            </button>
                        </div>
                    </div>
                    
                    <!-- Estadísticas rápidas -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center py-2">
                                    <h5 class="mb-0">${infracciones.length}</h5>
                                    <small>Total Infracciones</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center py-2">
                                    <h5 class="mb-0">${infracciones.filter(i => i.gravedad === 'leve').length}</h5>
                                    <small>Leves</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center py-2">
                                    <h5 class="mb-0">${infracciones.filter(i => i.gravedad === 'grave').length}</h5>
                                    <small>Graves</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center py-2">
                                    <h5 class="mb-0">${infracciones.filter(i => i.gravedad === 'muy_grave').length}</h5>
                                    <small>Muy Graves</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabla de infracciones -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tablaInfracciones">
                            <thead class="table-dark">
                                <tr>
                                    <th>Código</th>
                                    <th>Descripción</th>
                                    <th>Gravedad</th>
                                    <th>Multa (S/)</th>
                                    <th>UIT</th>
                                    <th>Puntos</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="infraccionesTableBody">
    `;
    
    // Agregar filas de infracciones
    if (infracciones.length > 0) {
        infracciones.forEach(infraccion => {
            const gravedadBadge = getGravedadBadge(infraccion.gravedad);
            const estadoBadge = (infraccion.estado === 'activo' || !infraccion.estado) ? 
                '<span class="badge bg-success">Activo</span>' : 
                '<span class="badge bg-secondary">Inactivo</span>';
                
            const codigo = infraccion.codigo_infraccion || infraccion.codigo || 'N/A';
            const descripcion = infraccion.descripcion || infraccion.tipo || 'Sin descripción';
            const multaSoles = infraccion.multa_soles || infraccion.sancion || '0.00';
            const multaUit = infraccion.multa_uit || '0.00';
            const puntos = infraccion.puntos_licencia || '0';
                
            infraccionesHTML += `
                <tr data-codigo="${codigo}" data-gravedad="${infraccion.gravedad}" data-estado="${infraccion.estado || 'activo'}">
                    <td><strong>${codigo}</strong></td>
                    <td class="text-truncate" style="max-width: 200px;" title="${descripcion}">${descripcion}</td>
                    <td>${gravedadBadge}</td>
                    <td>S/ ${multaSoles}</td>
                    <td>${multaUit}</td>
                    <td>${puntos}</td>
                    <td>${estadoBadge}</td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <button class="btn btn-outline-info" onclick="verInfraccion(${infraccion.id})" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-outline-warning" onclick="editarInfraccion(${infraccion.id})" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-outline-danger" onclick="confirmarEliminarInfraccion(${infraccion.id}, '${codigo}')" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    } else {
        infraccionesHTML += `
            <tr>
                <td colspan="8" class="text-center text-muted py-4">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                    <p class="mb-0">No se encontraron infracciones</p>
                    <button type="button" class="btn btn-primary mt-2" onclick="loadNuevaInfraccion()">
                        <i class="fas fa-plus"></i> Crear Primera Infracción
                    </button>
                </td>
            </tr>
        `;
    }
    
    infraccionesHTML += `
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Información de paginación -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Mostrando ${infracciones.length} infracciones
                        </div>
                        <div>
                            <button type="button" class="btn btn-outline-success" onclick="exportarInfracciones()">
                                <i class="fas fa-download"></i> Exportar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal para ver detalles de infracción -->
        <div class="modal fade" id="modalVerInfraccion" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-eye text-info"></i> 
                            Detalles de Infracción
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="modalVerInfraccionBody">
                        <!-- Contenido dinámico -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal para confirmar eliminación -->
        <div class="modal fade" id="modalEliminarInfraccion" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-trash"></i> 
                            Confirmar Eliminación
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Está seguro que desea eliminar la infracción?</p>
                        <div class="alert alert-warning">
                            <strong>Código:</strong> <span id="codigoEliminar"></span><br>
                            <small class="text-muted">Esta acción no se puede deshacer.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger" id="btnConfirmarEliminar">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    content.innerHTML = infraccionesHTML;
}

function getGravedadBadge(gravedad) {
    switch(gravedad) {
        case 'leve':
            return '<span class="badge bg-success">Leve</span>';
        case 'grave':
            return '<span class="badge bg-warning">Grave</span>';
        case 'muy_grave':
            return '<span class="badge bg-danger">Muy Grave</span>';
        default:
            return '<span class="badge bg-secondary">N/A</span>';
    }
}

function abrirModalCrearInfraccion() {
    document.getElementById('modalInfraccionTitle').textContent = 'Nueva Infracción';
    document.getElementById('formInfraccion').reset();
    const modal = new bootstrap.Modal(document.getElementById('modalInfraccion'));
    modal.show();
}

function verInfraccion(id) {
    console.log('Ver infracción:', id);
    // Implementar vista de detalles
}

function editarInfraccion(id) {
    console.log('Editar infracción:', id);
    // Implementar edición
}

function eliminarInfraccion(id) {
    console.log('Eliminar infracción:', id);
    // Implementar eliminación
}

function guardarInfraccion() {
    console.log('Guardar infracción');
    // Implementar guardado
}

function filtrarInfracciones() {
    console.log('Filtrar infracciones');
    // Implementar filtrado
}

function filtrarTablaInfracciones() {
    const gravedad = document.getElementById('filtroGravedad').value.toLowerCase();
    const estado = document.getElementById('filtroEstado').value.toLowerCase();
    const busqueda = document.getElementById('busquedaInfraccion').value.toLowerCase();
    
    const tabla = document.getElementById('tablaInfracciones');
    const filas = tabla.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    let filasVisibles = 0;
    
    for (let fila of filas) {
        if (fila.cells.length === 1) continue; // Saltar fila de "no hay datos"
        
        const codigoFila = fila.getAttribute('data-codigo') || '';
        const gravedadFila = fila.getAttribute('data-gravedad') || '';
        const estadoFila = fila.getAttribute('data-estado') || '';
        const textoFila = fila.textContent.toLowerCase();
        
        const coincideGravedad = !gravedad || gravedadFila.toLowerCase() === gravedad;
        const coincideEstado = !estado || estadoFila.toLowerCase() === estado;
        const coincideBusqueda = !busqueda || textoFila.includes(busqueda);
        
        if (coincideGravedad && coincideEstado && coincideBusqueda) {
            fila.style.display = '';
            filasVisibles++;
        } else {
            fila.style.display = 'none';
        }
    }
    
    // Actualizar contador
    const contador = document.querySelector('.text-muted');
    if (contador && contador.textContent.includes('Mostrando')) {
        contador.textContent = `Mostrando ${filasVisibles} infracciones`;
    }
}

function limpiarFiltros() {
    document.getElementById('filtroGravedad').value = '';
    document.getElementById('filtroEstado').value = '';
    document.getElementById('busquedaInfraccion').value = '';
    filtrarTablaInfracciones();
}

function verInfraccion(id) {
    console.log('👀 Viendo infracción:', id);
    
    // Simulamos obtener datos de la infracción
    const modalBody = document.getElementById('modalVerInfraccionBody');
    modalBody.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <strong>Código:</strong> G.01<br>
                <strong>Gravedad:</strong> <span class="badge bg-warning">Grave</span><br>
                <strong>Estado:</strong> <span class="badge bg-success">Activo</span><br>
            </div>
            <div class="col-md-6">
                <strong>Multa:</strong> S/ 420.00<br>
                <strong>UIT:</strong> 0.84<br>
                <strong>Puntos:</strong> 8<br>
            </div>
        </div>
        <hr>
        <div>
            <strong>Descripción:</strong><br>
            <p class="text-muted">Exceso de velocidad en zona urbana</p>
        </div>
        <div>
            <strong>Base Legal:</strong><br>
            <p class="text-muted">Art. 318° Reglamento Nacional de Tránsito</p>
        </div>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <small>ID de infracción: ${id}</small>
        </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('modalVerInfraccion'));
    modal.show();
}

function editarInfraccion(id) {
    console.log('✏️ Editando infracción:', id);
    
    // Por ahora redirigir a nueva infracción con datos precargados
    loadNuevaInfraccion();
    
    // Simular carga de datos
    setTimeout(() => {
        if (document.getElementById('codigoNueva')) {
            document.getElementById('codigoNueva').value = 'G.01';
            document.getElementById('gravedadNueva').value = 'grave';
            document.getElementById('descripcionNueva').value = 'Exceso de velocidad en zona urbana';
            document.getElementById('multaSolesNueva').value = '420.00';
            document.getElementById('multaUitNueva').value = '0.84';
            document.getElementById('puntosLicenciaNueva').value = '8';
            document.getElementById('baseLegalNueva').value = 'Art. 318° Reglamento Nacional de Tránsito';
            
            // Cambiar título
            const titulo = document.querySelector('#formNuevaInfraccion').closest('.card').querySelector('h3');
            if (titulo) {
                titulo.innerHTML = '<i class="fas fa-edit text-warning"></i> Editar Infracción';
            }
        }
    }, 100);
}

function confirmarEliminarInfraccion(id, codigo) {
    console.log('🗑️ Confirmar eliminación:', id, codigo);
    
    document.getElementById('codigoEliminar').textContent = codigo;
    
    // Configurar botón de confirmación
    const btnConfirmar = document.getElementById('btnConfirmarEliminar');
    btnConfirmar.onclick = function() {
        eliminarInfraccion(id);
    };
    
    const modal = new bootstrap.Modal(document.getElementById('modalEliminarInfraccion'));
    modal.show();
}

function eliminarInfraccion(id) {
    console.log('🗑️ Eliminando infracción:', id);
    
    // Simular eliminación
    alert(`Eliminando infracción ID: ${id}\n(Función por implementar)`);
    
    // Cerrar modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('modalEliminarInfraccion'));
    if (modal) modal.hide();
    
    // Recargar tabla
    setTimeout(() => {
        loadGestionarInfracciones();
    }, 500);
}

function exportarInfracciones() {
    console.log('📤 Exportando infracciones...');
    alert('Función de exportación - Por implementar');
}

function guardarNuevaInfraccion() {
    console.log('💾 Guardando nueva infracción...');
    
    const formData = {
        codigo_infraccion: document.getElementById('codigoNueva').value,
        gravedad: document.getElementById('gravedadNueva').value,
        descripcion: document.getElementById('descripcionNueva').value,
        multa_soles: document.getElementById('multaSolesNueva').value,
        multa_uit: document.getElementById('multaUitNueva').value || '0',
        puntos_licencia: document.getElementById('puntosLicenciaNueva').value || '0',
        base_legal: document.getElementById('baseLegalNueva').value,
        estado: document.getElementById('estadoNueva').value
    };
    
    console.log('Datos a guardar:', formData);
    
    // Validar campos requeridos
    if (!formData.codigo_infraccion || !formData.gravedad || !formData.descripcion || !formData.multa_soles) {
        alert('Por favor complete todos los campos obligatorios (*)');
        return;
    }
    
    // Simular guardado exitoso
    alert(`✅ Infracción "${formData.codigo_infraccion}" guardada exitosamente!\n\n(Función real por implementar)`);
    
    // Volver a la lista
    loadGestionarInfracciones();
}

function ejecutarBusquedaInfracciones() {
    console.log('🔍 Ejecutando búsqueda de infracciones...');
    
    const codigo = document.getElementById('buscarPorCodigo').value;
    const gravedad = document.getElementById('buscarPorGravedad').value;
    
    console.log('Búsqueda por:', { codigo, gravedad });
    
    // Aquí iría la lógica de búsqueda
    const resultados = document.getElementById('resultadosBusqueda');
    resultados.innerHTML = `
        <div class="text-center text-info">
            <i class="fas fa-search fa-2x mb-3"></i>
            <p>Buscando: Código="${codigo}", Gravedad="${gravedad}"</p>
            <p><em>Función de búsqueda - Por implementar</em></p>
        </div>
    `;
}

// ==================== EXPORTAR FUNCIONES ====================
// Hacer las funciones disponibles globalmente para el fiscalizador
window.loadDashboardStatsFiscalizador = loadDashboardStatsFiscalizador;
window.loadInfracciones = loadInfracciones;
window.loadGestionarInfracciones = loadGestionarInfracciones;
window.loadNuevaInfraccion = loadNuevaInfraccion;
window.loadBuscarInfracciones = loadBuscarInfracciones;
window.loadEstadisticasInfracciones = loadEstadisticasInfracciones;

console.log('✅ Módulo fiscalizador cargado completamente');