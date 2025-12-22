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

// ==================== GESTIÓN DE ACTAS DEL FISCALIZADOR ====================
async function loadActas(event) {
    console.log('📋 Cargando gestión de actas...');
    
    // Determinar qué sección cargar
    let seccion = 'crear-acta';
    if (event && event.target) {
        seccion = event.target.getAttribute('data-section') || 
                  event.target.closest('a').getAttribute('data-section') || 
                  'crear-acta';
    }
    
    console.log('📋 Cargando sección:', seccion);
    
    switch(seccion) {
        case 'crear-acta':
            await loadCrearActa();
            break;
        case 'mis-actas':
            await loadMisActas();
            break;
        case 'buscar-conductor':
            await loadBuscarConductor();
            break;
        case 'buscar-vehiculo':
            await loadBuscarVehiculo();
            break;
        default:
            await loadCrearActa();
    }
}

async function loadMisActas() {
    console.log('📋 Cargando mis actas...');
    
    try {
        // Obtener el ID del fiscalizador actual
        const response = await fetch(`${window.location.origin}${window.location.pathname}?api=obtener_actas_fiscalizador`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                fiscalizador_id: window.dashboardUserId || null
            })
        });
        
        const result = await response.json();
        
        if (result.success && result.actas) {
            mostrarMisActasEnTabla(result.actas);
            actualizarEstadisticasFiscalizador(result.actas);
        } else {
            console.error('❌ Error al cargar mis actas:', result.message);
            mostrarErrorActas('No se pudieron cargar las actas');
        }
    } catch (error) {
        console.error('❌ Error al cargar mis actas:', error);
        mostrarErrorActas('Error de conexión al cargar las actas');
    }
}

// Función que conecta con fiscalizador-actas.js
function mostrarFormularioCrearActa() {
    console.log('📋 Conectando con fiscalizador-actas.js...');
    if (typeof window.showCrearActaModal === 'function') {
        window.showCrearActaModal();
    } else {
        console.warn('⚠️ Función showCrearActaModal no encontrada en fiscalizador-actas.js');
        // Fallback: mostrar mensaje
        const content = document.getElementById('contentContainer');
        if (content) {
            content.innerHTML = `
                <div class="container-fluid">
                    <div class="alert alert-warning">
                        <h4><i class="fas fa-exclamation-triangle me-2"></i>Función no disponible</h4>
                        <p>El módulo de creación de actas no se ha cargado correctamente.</p>
                        <p>Por favor, recarga la página e intenta nuevamente.</p>
                        <button class="btn btn-primary mt-2" onclick="location.reload()">
                            <i class="fas fa-refresh me-2"></i>Recargar página
                        </button>
                    </div>
                </div>
            `;
        }
    }
}

async function loadCrearActa() {
    console.log('📋 Cargando formulario crear acta...');
    // Esta función ya debe estar implementada en fiscalizador-actas.js
    mostrarFormularioCrearActa();
}

async function loadBuscarConductor() {
    console.log('📋 Cargando búsqueda de conductor...');
    await loadConductores();
}

async function loadBuscarVehiculo() {
    console.log('📋 Cargando búsqueda de vehículo...');
    await loadVehiculos();
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
    
    //Actualizar título del contenido principal
    const mainTitle = document.querySelector('#main-content h2');
    if (mainTitle) {
        mainTitle.innerHTML = '<i class="fas fa-exclamation-triangle text-warning"></i> Gestión de Infracciones';
    }
    
    //Cargar la sección correspondiente
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
            // Si no hay API, mostrar interfaz con datos de ejemplo
            const infraccionesEjemplo = [
                {
                    id: 1,
                    codigo: 'INF-001',
                    descripcion: 'Ejemplo de infracción',
                    gravedad: 'leve',
                    monto: 100.00,
                    created_at: '2025-09-30'
                }
            ];
            displayInfraccionesInterface(infraccionesEjemplo);
        }
    } catch (error) {
        console.error('❌ Error al cargar infracciones:', error);
        // Si hay error de conexión, mostrar interfaz con datos de ejemplo
        const infraccionesEjemplo = [
            {
                id: 1,
                codigo: 'INF-001',
                descripcion: 'Ejemplo de infracción',
                gravedad: 'leve',
                monto: 100.00,
                created_at: '2025-09-30'
            }
        ];
        displayInfraccionesInterface(infraccionesEjemplo);
    }
}

async function loadNuevaInfraccion() {
    console.log('📋 Cargando formulario nueva infracción...');
    
    const content = document.getElementById('contentContainer');
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
    
    const content = document.getElementById('contentContainer');
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
    
    const content = document.getElementById('contentContainer');
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
    const content = document.getElementById('contentContainer');
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

// ==================== GESTIÓN DE INSPECCIONES ====================
async function loadInspecciones(event) {
    console.log('📋 [DEBUG] loadInspecciones iniciado');

    // Prevenir comportamiento por defecto y propagación
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    // Determinar qué sección de inspecciones cargar
    let seccion = 'todas';
    if (event && event.target) {
        seccion = event.target.getAttribute('data-section') ||
                  event.target.closest('a').getAttribute('data-section') ||
                  'todas';
    }

    console.log('📋 [DEBUG] Cargando sección:', seccion);

    const content = document.getElementById('contentContainer');
    if (!content) {
        console.error('📋 [ERROR] contentContainer no encontrado');
        return;
    }

    // Determinar título según sección
    let titulo = 'Gestión de Inspecciones';
    let subtitulo = 'Todas las inspecciones';
    switch(seccion) {
        case 'mis-inspecciones':
            titulo = 'Mis Inspecciones';
            subtitulo = 'Inspecciones realizadas por mí';
            break;
        case 'inspecciones-pendientes':
            titulo = 'Inspecciones Pendientes';
            subtitulo = 'Inspecciones programadas pendientes';
            break;
        case 'nueva-inspeccion':
            titulo = 'Nueva Inspección';
            subtitulo = 'Registrar nueva inspección';
            break;
    }

    const inspeccionesHTML = `
        <div class="container-fluid">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">
                            <i class="fas fa-clipboard-check text-primary"></i>
                            ${titulo}
                        </h3>
                        <small class="text-muted">${subtitulo}</small>
                    </div>
                    <div>
                        <button type="button" class="btn btn-success me-2" onclick="nuevaInspeccion()">
                            <i class="fas fa-plus"></i> Nueva Inspección
                        </button>
                        <button type="button" class="btn btn-outline-primary" onclick="cargarInspecciones()">
                            <i class="fas fa-sync"></i> Actualizar
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="filtroFechaInspeccion" class="form-label">Fecha:</label>
                            <input type="date" id="filtroFechaInspeccion" class="form-control" onchange="filtrarInspecciones()">
                        </div>
                        <div class="col-md-3">
                            <label for="filtroEstadoInspeccion" class="form-label">Estado:</label>
                            <select id="filtroEstadoInspeccion" class="form-control" onchange="filtrarInspecciones()">
                                <option value="">Todos</option>
                                <option value="completada">Completada</option>
                                <option value="en_progreso">En Progreso</option>
                                <option value="pendiente">Pendiente</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="busquedaInspeccion" class="form-label">Buscar:</label>
                            <input type="text" id="busquedaInspeccion" class="form-control"
                                   placeholder="Placa, conductor..."
                                   onkeyup="filtrarInspecciones()">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" onclick="limpiarFiltrosInspecciones()" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-eraser"></i> Limpiar
                            </button>
                        </div>
                    </div>

                    <!-- Estadísticas rápidas -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center py-2">
                                    <h5 class="mb-0" id="total-inspecciones">0</h5>
                                    <small>Total Inspecciones</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center py-2">
                                    <h5 class="mb-0" id="inspecciones-completadas">0</h5>
                                    <small>Completadas</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center py-2">
                                    <h5 class="mb-0" id="inspecciones-progreso">0</h5>
                                    <small>En Progreso</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center py-2">
                                    <h5 class="mb-0" id="inspecciones-pendientes">0</h5>
                                    <small>Pendientes</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de inspecciones -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tablaInspecciones">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Placa</th>
                                    <th>Conductor</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th>Resultado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="inspeccionesTableBody">
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                        <p class="mt-2 text-muted">Cargando inspecciones...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    `;

    content.innerHTML = inspeccionesHTML;

    // Asegurar que el submenu se mantenga abierto si se accedió desde un sub-item
    if (seccion !== 'todas') {
        const submenu = document.getElementById('submenu-inspecciones');
        if (submenu) {
            submenu.style.setProperty('display', 'block', 'important');
            submenu.classList.add('show');
            console.log('📋 [DEBUG] Submenu forzado a mantenerse abierto');
        }
    }

    // Pequeño delay para asegurar que el submenu esté completamente renderizado
    await new Promise(resolve => setTimeout(resolve, 100));

    console.log('📋 [DEBUG] HTML insertado, iniciando carga de datos');

    // Cargar datos
    await cargarInspecciones(seccion);

    console.log('📋 [DEBUG] Carga de inspecciones completada');
}

async function cargarInspecciones(seccion = 'todas') {
    console.log('📋 Cargando lista de inspecciones...', seccion);

    const tbody = document.getElementById('inspeccionesTableBody');
    if (!tbody) return;

    try {
        let url = `${window.location.origin}${window.location.pathname}?api=inspecciones`;
        if (seccion !== 'todas') {
            url += `&seccion=${seccion}`;
        }

        const response = await fetch(url);
        const result = await response.json();

        if (result.success && result.inspecciones) {
            console.log('📋 [DEBUG] Inspecciones cargadas:', result.inspecciones.length);

            // Filtrar inspecciones según la sección
            let inspeccionesFiltradas = result.inspecciones;

            if (seccion === 'mis-inspecciones') {
                inspeccionesFiltradas = result.inspecciones.filter(i => i.inspector_id == window.dashboardUserId);
                console.log('📋 [DEBUG] Inspecciones filtradas para usuario:', inspeccionesFiltradas.length);
            } else if (seccion === 'inspecciones-pendientes') {
                inspeccionesFiltradas = result.inspecciones.filter(i => i.estado === 'pendiente');
                console.log('📋 [DEBUG] Inspecciones pendientes filtradas:', inspeccionesFiltradas.length);
            }

            mostrarInspeccionesEnTabla(inspeccionesFiltradas);
            actualizarEstadisticasInspecciones(inspeccionesFiltradas);
            console.log('📋 [DEBUG] Tabla de inspecciones actualizada');
        } else {
            console.error('📋 [ERROR] Respuesta inválida de la API:', result);
            mostrarErrorInspecciones('Error en la respuesta del servidor');
        }
        } else {
            mostrarErrorInspecciones('No se pudieron cargar las inspecciones');
        }
    } catch (error) {
        console.error('❌ Error al cargar inspecciones:', error);
        mostrarErrorInspecciones('Error de conexión al cargar las inspecciones');
    }
}

function mostrarInspeccionesEnTabla(inspecciones) {
    const tbody = document.getElementById('inspeccionesTableBody');

    if (!inspecciones || inspecciones.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    <i class="fas fa-clipboard-list text-muted" style="font-size: 3rem;"></i>
                    <p class="mt-2 text-muted">No hay inspecciones registradas</p>
                    <button class="btn btn-primary" onclick="nuevaInspeccion()">
                        <i class="fas fa-plus"></i> Crear Primera Inspección
                    </button>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = inspecciones.map(inspeccion => {
        const estadoBadge = getEstadoInspeccionBadge(inspeccion.estado);
        const resultadoTexto = inspeccion.resultado || 'Pendiente';

        return `
        <tr>
            <td><strong>${inspeccion.id}</strong></td>
            <td>${formatearFechaInspeccion(inspeccion.fecha_inspeccion)}</td>
            <td><span class="badge bg-dark">${inspeccion.placa || 'N/A'}</span></td>
            <td>${inspeccion.conductor_nombre || 'N/A'}</td>
            <td>${inspeccion.tipo_inspeccion || 'General'}</td>
            <td>${estadoBadge}</td>
            <td>${resultadoTexto}</td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" onclick="verInspeccion(${inspeccion.id})" title="Ver">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-success" onclick="editarInspeccion(${inspeccion.id})" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-info" onclick="imprimirInspeccion(${inspeccion.id})" title="Imprimir">
                        <i class="fas fa-print"></i>
                    </button>
                    <button class="btn btn-outline-danger" onclick="eliminarInspeccion(${inspeccion.id})" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
        `;
    }).join('');
}

function mostrarErrorInspecciones(mensaje) {
    const tbody = document.getElementById('inspeccionesTableBody');
    tbody.innerHTML = `
        <tr>
            <td colspan="8" class="text-center py-4 text-danger">
                <i class="fas fa-exclamation-triangle" style="font-size: 3rem;"></i>
                <p class="mt-2">${mensaje}</p>
                <button class="btn btn-outline-primary" onclick="cargarInspecciones()">
                    <i class="fas fa-refresh"></i> Reintentar
                </button>
            </td>
        </tr>
    `;
}

function actualizarEstadisticasInspecciones(inspecciones) {
    const total = inspecciones.length;
    const completadas = inspecciones.filter(i => i.estado === 'completada').length;
    const enProgreso = inspecciones.filter(i => i.estado === 'en_progreso').length;
    const pendientes = inspecciones.filter(i => i.estado === 'pendiente').length;

    document.getElementById('total-inspecciones').textContent = total;
    document.getElementById('inspecciones-completadas').textContent = completadas;
    document.getElementById('inspecciones-progreso').textContent = enProgreso;
    document.getElementById('inspecciones-pendientes').textContent = pendientes;
}

function getEstadoInspeccionBadge(estado) {
    switch(estado) {
        case 'completada': return '<span class="badge bg-success">Completada</span>';
        case 'en_progreso': return '<span class="badge bg-warning text-dark">En Progreso</span>';
        case 'pendiente': return '<span class="badge bg-danger">Pendiente</span>';
        default: return '<span class="badge bg-secondary">N/A</span>';
    }
}

function formatearFechaInspeccion(fecha) {
    if (!fecha) return 'N/A';
    try {
        return new Date(fecha).toLocaleDateString('es-ES');
    } catch {
        return fecha;
    }
}

function nuevaInspeccion() {
    alert('🚧 Nueva Inspección - Funcionalidad en desarrollo');
}

function verInspeccion(id) {
    alert(`🚧 Ver Inspección ${id} - Funcionalidad en desarrollo`);
}

function editarInspeccion(id) {
    alert(`🚧 Editar Inspección ${id} - Funcionalidad en desarrollo`);
}

function imprimirInspeccion(id) {
    alert(`🚧 Imprimir Inspección ${id} - Funcionalidad en desarrollo`);
}

function eliminarInspeccion(id) {
    if (confirm(`¿Está seguro de eliminar la inspección ${id}?`)) {
        alert(`🚧 Eliminar Inspección ${id} - Funcionalidad en desarrollo`);
    }
}

function filtrarInspecciones() {
    console.log('🔍 Filtrando inspecciones...');
    // Implementar filtrado local
}

function limpiarFiltrosInspecciones() {
    document.getElementById('filtroFechaInspeccion').value = '';
    document.getElementById('filtroEstadoInspeccion').value = '';
    document.getElementById('busquedaInspeccion').value = '';
    filtrarInspecciones();
}

// ==================== BÚSQUEDA DE CONDUCTORES ====================
async function loadConductores() {
    console.log('👤 Cargando búsqueda de conductores...');

    const content = document.getElementById('contentContainer');
    if (!content) return;

    const conductoresHTML = `
        <div class="container-fluid">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">
                        <i class="fas fa-user-search text-primary"></i>
                        Búsqueda de Conductores
                    </h3>
                    <button type="button" class="btn btn-success" onclick="nuevoConductor()">
                        <i class="fas fa-plus"></i> Nuevo Conductor
                    </button>
                </div>

                <div class="card-body">
                    <!-- Formulario de búsqueda -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label for="busquedaDniConductor" class="form-label">DNI</label>
                            <input type="text" class="form-control" id="busquedaDniConductor" placeholder="12345678">
                        </div>
                        <div class="col-md-4">
                            <label for="busquedaNombreConductor" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="busquedaNombreConductor" placeholder="Nombre del conductor">
                        </div>
                        <div class="col-md-4">
                            <label for="busquedaLicenciaConductor" class="form-label">N° Licencia</label>
                            <input type="text" class="form-control" id="busquedaLicenciaConductor" placeholder="A-IIIa-123456">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-primary btn-lg w-100" onclick="buscarConductores()">
                                <i class="fas fa-search me-2"></i>Buscar Conductores
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-outline-secondary btn-lg w-100" onclick="limpiarBusquedaConductores()">
                                <i class="fas fa-broom me-2"></i>Limpiar Búsqueda
                            </button>
                        </div>
                    </div>

                    <!-- Resultados -->
                    <div id="resultadosConductores">
                        <div class="text-center py-5">
                            <i class="fas fa-user-search fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Realice una búsqueda para ver los resultados</h5>
                            <p class="text-muted">Ingrese DNI, nombre o número de licencia</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    content.innerHTML = conductoresHTML;
}

async function buscarConductores() {
    const dni = document.getElementById('busquedaDniConductor').value.trim();
    const nombre = document.getElementById('busquedaNombreConductor').value.trim();
    const licencia = document.getElementById('busquedaLicenciaConductor').value.trim();

    if (!dni && !nombre && !licencia) {
        alert('Ingrese al menos un criterio de búsqueda');
        return;
    }

    const resultadosDiv = document.getElementById('resultadosConductores');
    resultadosDiv.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Buscando...</span>
            </div>
            <p class="mt-2">Buscando conductores...</p>
        </div>
    `;

    try {
        const params = new URLSearchParams();
        if (dni) params.append('dni', dni);
        if (nombre) params.append('nombre', nombre);
        if (licencia) params.append('licencia', licencia);

        const response = await fetch(`${window.location.origin}${window.location.pathname}?api=buscar-conductores&${params}`);
        const result = await response.json();

        if (result.success && result.conductores && result.conductores.length > 0) {
            mostrarConductoresEnTabla(result.conductores);
        } else {
            resultadosDiv.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-user-times fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No se encontraron conductores</h5>
                    <p class="text-muted">Intente con otros criterios de búsqueda</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error al buscar conductores:', error);
        resultadosDiv.innerHTML = `
            <div class="text-center py-5 text-danger">
                <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                <h5>Error en la búsqueda</h5>
                <p>Intente nuevamente más tarde</p>
            </div>
        `;
    }
}

function mostrarConductoresEnTabla(conductores) {
    const resultadosDiv = document.getElementById('resultadosConductores');

    const tablaHTML = `
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>DNI</th>
                        <th>Nombre Completo</th>
                        <th>Licencia</th>
                        <th>Clase</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    ${conductores.map(conductor => `
                        <tr>
                            <td>${conductor.dni || 'N/A'}</td>
                            <td>${conductor.nombre_completo || 'N/A'}</td>
                            <td>${conductor.numero_licencia || 'N/A'}</td>
                            <td>${conductor.clase_licencia || 'N/A'}</td>
                            <td><span class="badge bg-success">Activo</span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="verConductor(${conductor.id})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-success" onclick="editarConductor(${conductor.id})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-info" onclick="verHistorialConductor(${conductor.id})">
                                        <i class="fas fa-history"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;

    resultadosDiv.innerHTML = tablaHTML;
}

function limpiarBusquedaConductores() {
    document.getElementById('busquedaDniConductor').value = '';
    document.getElementById('busquedaNombreConductor').value = '';
    document.getElementById('busquedaLicenciaConductor').value = '';

    document.getElementById('resultadosConductores').innerHTML = `
        <div class="text-center py-5">
            <i class="fas fa-user-search fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Realice una búsqueda para ver los resultados</h5>
            <p class="text-muted">Ingrese DNI, nombre o número de licencia</p>
        </div>
    `;
}

function nuevoConductor() {
    alert('🚧 Nuevo Conductor - Funcionalidad en desarrollo');
}

function verConductor(id) {
    alert(`🚧 Ver Conductor ${id} - Funcionalidad en desarrollo`);
}

function editarConductor(id) {
    alert(`🚧 Editar Conductor ${id} - Funcionalidad en desarrollo`);
}

function verHistorialConductor(id) {
    alert(`🚧 Historial Conductor ${id} - Funcionalidad en desarrollo`);
}

// ==================== BÚSQUEDA DE VEHÍCULOS ====================
async function loadVehiculos() {
    console.log('🚗 Cargando búsqueda de vehículos...');

    const content = document.getElementById('contentContainer');
    if (!content) return;

    const vehiculosHTML = `
        <div class="container-fluid">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">
                        <i class="fas fa-car text-success"></i>
                        Búsqueda de Vehículos
                    </h3>
                    <button type="button" class="btn btn-success" onclick="nuevoVehiculo()">
                        <i class="fas fa-plus"></i> Nuevo Vehículo
                    </button>
                </div>

                <div class="card-body">
                    <!-- Formulario de búsqueda -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label for="busquedaPlacaVehiculo" class="form-label">Placa</label>
                            <input type="text" class="form-control" id="busquedaPlacaVehiculo" placeholder="ABC-123" style="text-transform: uppercase;">
                        </div>
                        <div class="col-md-4">
                            <label for="busquedaMarcaVehiculo" class="form-label">Marca</label>
                            <input type="text" class="form-control" id="busquedaMarcaVehiculo" placeholder="Toyota, Hyundai, etc.">
                        </div>
                        <div class="col-md-4">
                            <label for="busquedaModeloVehiculo" class="form-label">Modelo</label>
                            <input type="text" class="form-control" id="busquedaModeloVehiculo" placeholder="Corolla, Tucson, etc.">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-primary btn-lg w-100" onclick="buscarVehiculos()">
                                <i class="fas fa-search me-2"></i>Buscar Vehículos
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-outline-secondary btn-lg w-100" onclick="limpiarBusquedaVehiculos()">
                                <i class="fas fa-broom me-2"></i>Limpiar Búsqueda
                            </button>
                        </div>
                    </div>

                    <!-- Resultados -->
                    <div id="resultadosVehiculos">
                        <div class="text-center py-5">
                            <i class="fas fa-car fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Realice una búsqueda para ver los resultados</h5>
                            <p class="text-muted">Ingrese placa, marca o modelo</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    content.innerHTML = vehiculosHTML;
}

async function buscarVehiculos() {
    const placa = document.getElementById('busquedaPlacaVehiculo').value.trim().toUpperCase();
    const marca = document.getElementById('busquedaMarcaVehiculo').value.trim();
    const modelo = document.getElementById('busquedaModeloVehiculo').value.trim();

    if (!placa && !marca && !modelo) {
        alert('Ingrese al menos un criterio de búsqueda');
        return;
    }

    const resultadosDiv = document.getElementById('resultadosVehiculos');
    resultadosDiv.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Buscando...</span>
            </div>
            <p class="mt-2">Buscando vehículos...</p>
        </div>
    `;

    try {
        const params = new URLSearchParams();
        if (placa) params.append('placa', placa);
        if (marca) params.append('marca', marca);
        if (modelo) params.append('modelo', modelo);

        const response = await fetch(`${window.location.origin}${window.location.pathname}?api=buscar-vehiculos&${params}`);
        const result = await response.json();

        if (result.success && result.vehiculos && result.vehiculos.length > 0) {
            mostrarVehiculosEnTabla(result.vehiculos);
        } else {
            resultadosDiv.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-car-slash fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No se encontraron vehículos</h5>
                    <p class="text-muted">Intente con otros criterios de búsqueda</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error al buscar vehículos:', error);
        resultadosDiv.innerHTML = `
            <div class="text-center py-5 text-danger">
                <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                <h5>Error en la búsqueda</h5>
                <p>Intente nuevamente más tarde</p>
            </div>
        `;
    }
}

function mostrarVehiculosEnTabla(vehiculos) {
    const resultadosDiv = document.getElementById('resultadosVehiculos');

    const tablaHTML = `
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Placa</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Año</th>
                        <th>Propietario</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    ${vehiculos.map(vehiculo => `
                        <tr>
                            <td><span class="badge bg-dark">${vehiculo.placa || 'N/A'}</span></td>
                            <td>${vehiculo.marca || 'N/A'}</td>
                            <td>${vehiculo.modelo || 'N/A'}</td>
                            <td>${vehiculo.anio || 'N/A'}</td>
                            <td>${vehiculo.propietario || 'N/A'}</td>
                            <td><span class="badge bg-success">Activo</span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="verVehiculo(${vehiculo.id})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-success" onclick="editarVehiculo(${vehiculo.id})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-info" onclick="verHistorialVehiculo(${vehiculo.id})">
                                        <i class="fas fa-history"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;

    resultadosDiv.innerHTML = tablaHTML;
}

function limpiarBusquedaVehiculos() {
    document.getElementById('busquedaPlacaVehiculo').value = '';
    document.getElementById('busquedaMarcaVehiculo').value = '';
    document.getElementById('busquedaModeloVehiculo').value = '';

    document.getElementById('resultadosVehiculos').innerHTML = `
        <div class="text-center py-5">
            <i class="fas fa-car fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Realice una búsqueda para ver los resultados</h5>
            <p class="text-muted">Ingrese placa, marca o modelo</p>
        </div>
    `;
}

function nuevoVehiculo() {
    alert('🚧 Nuevo Vehículo - Funcionalidad en desarrollo');
}

function verVehiculo(id) {
    alert(`🚧 Ver Vehículo ${id} - Funcionalidad en desarrollo`);
}

function editarVehiculo(id) {
    alert(`🚧 Editar Vehículo ${id} - Funcionalidad en desarrollo`);
}

function verHistorialVehiculo(id) {
    alert(`🚧 Historial Vehículo ${id} - Funcionalidad en desarrollo`);
}

// ==================== REPORTES ====================
async function loadSection(seccion) {
    console.log('📊 Cargando sección:', seccion);

    const content = document.getElementById('contentContainer');
    if (!content) return;

    switch(seccion) {
        case 'reportes':
            await loadReportes();
            break;
        default:
            content.innerHTML = `
                <div class="container-fluid">
                    <div class="text-center py-5">
                        <i class="fas fa-cog fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">Sección en desarrollo</h4>
                        <p class="text-muted">Esta funcionalidad estará disponible próximamente</p>
                    </div>
                </div>
            `;
    }
}

async function loadReportes() {
    console.log('📊 Cargando reportes...');

    const content = document.getElementById('contentContainer');

    const reportesHTML = `
        <div class="container-fluid">
            <div class="card shadow">
                <div class="card-header">
                    <h3 class="mb-0">
                        <i class="fas fa-chart-bar text-info"></i>
                        Reportes y Estadísticas
                    </h3>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Opciones de reportes -->
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-file-alt me-2"></i>Reporte de Actas
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">Generar reportes detallados de actas por período, fiscalizador o estado.</p>
                                    <button class="btn btn-primary w-100" onclick="generarReporteActas()">
                                        <i class="fas fa-download me-2"></i>Generar Reporte
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <div class="card h-100 border-success">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-chart-pie me-2"></i>Estadísticas
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">Ver estadísticas generales de infracciones, multas y rendimiento.</p>
                                    <button class="btn btn-success w-100" onclick="verEstadisticas()">
                                        <i class="fas fa-chart-line me-2"></i>Ver Estadísticas
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <div class="card h-100 border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0">
                                        <i class="fas fa-calendar-check me-2"></i>Reporte Mensual
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">Reportes consolidados mensuales para análisis y control.</p>
                                    <button class="btn btn-warning w-100" onclick="generarReporteMensual()">
                                        <i class="fas fa-calendar me-2"></i>Reporte Mensual
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Área de resultados -->
                    <div id="reportesResultados" class="mt-4" style="display: none;">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Resultado del Reporte</h5>
                            </div>
                            <div class="card-body">
                                <div id="reportesContenido">
                                    <!-- Aquí se mostrarán los resultados -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    content.innerHTML = reportesHTML;
}

function generarReporteActas() {
    alert('🚧 Generar Reporte de Actas - Funcionalidad en desarrollo');
}

function verEstadisticas() {
    alert('🚧 Ver Estadísticas - Funcionalidad en desarrollo');
}

function generarReporteMensual() {
    alert('🚧 Generar Reporte Mensual - Funcionalidad en desarrollo');
}

// ==================== CALENDARIO ====================
async function loadCalendario() {
    console.log('📅 Cargando calendario...');

    const content = document.getElementById('contentContainer');
    if (!content) return;

    const calendarioHTML = `
        <div class="container-fluid">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">
                        <i class="fas fa-calendar-alt text-danger"></i>
                        Calendario de Actividades
                    </h3>
                    <div>
                        <button class="btn btn-primary me-2" onclick="nuevaActividad()">
                            <i class="fas fa-plus"></i> Nueva Actividad
                        </button>
                        <button class="btn btn-outline-secondary" onclick="cambiarVistaCalendario('mes')">
                            <i class="fas fa-calendar"></i> Mes
                        </button>
                        <button class="btn btn-outline-secondary" onclick="cambiarVistaCalendario('semana')">
                            <i class="fas fa-calendar-week"></i> Semana
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Calendario -->
                    <div class="row">
                        <div class="col-md-8">
                            <div id="calendarioContainer" class="border rounded p-3">
                                <div class="text-center py-5">
                                    <i class="fas fa-calendar-alt fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">Calendario Interactivo</h5>
                                    <p class="text-muted">Vista del calendario con actividades programadas</p>
                                    <small class="text-muted">Funcionalidad en desarrollo</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Próximas actividades -->
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-clock text-warning me-2"></i>
                                        Próximas Actividades
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="list-group list-group-flush">
                                        <div class="list-group-item">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">Inspección programada</h6>
                                                <small>Hoy 10:00</small>
                                            </div>
                                            <p class="mb-1">Revisión de zona urbana norte</p>
                                            <small class="text-muted">Prioridad: Alta</small>
                                        </div>

                                        <div class="list-group-item">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">Reunión de coordinación</h6>
                                                <small>Mañana 09:00</small>
                                            </div>
                                            <p class="mb-1">Coordinación con inspectores</p>
                                            <small class="text-muted">Prioridad: Media</small>
                                        </div>

                                        <div class="list-group-item">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">Capacitación</h6>
                                                <small>15 Dic 14:00</small>
                                            </div>
                                            <p class="mb-1">Actualización normativa de tránsito</p>
                                            <small class="text-muted">Prioridad: Baja</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Leyenda -->
                            <div class="card mt-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Leyenda</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="badge bg-danger me-2">&nbsp;</div>
                                        <small>Inspecciones</small>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="badge bg-warning me-2">&nbsp;</div>
                                        <small>Reuniones</small>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="badge bg-info me-2">&nbsp;</div>
                                        <small>Capacitaciones</small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="badge bg-success me-2">&nbsp;</div>
                                        <small>Otros eventos</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    content.innerHTML = calendarioHTML;
}

function nuevaActividad() {
    alert('🚧 Nueva Actividad - Funcionalidad en desarrollo');
}

function cambiarVistaCalendario(vista) {
    alert(`🚧 Cambiar vista a ${vista} - Funcionalidad en desarrollo`);
}

// ==================== EXPORTAR FUNCIONES ====================
// Hacer las funciones disponibles globalmente para el fiscalizador
window.loadDashboardStatsFiscalizador = loadDashboardStatsFiscalizador;
window.loadActas = loadActas;
window.loadMisActas = loadMisActas;
window.loadCrearActa = loadCrearActa;
window.loadBuscarConductor = loadBuscarConductor;
window.loadBuscarVehiculo = loadBuscarVehiculo;
window.loadConductores = loadConductores;
window.loadVehiculos = loadVehiculos;
window.loadInspecciones = loadInspecciones;
window.loadSection = loadSection;
window.loadCalendario = loadCalendario;
window.mostrarFormularioCrearActa = mostrarFormularioCrearActa;
window.loadInfracciones = loadInfracciones;
window.loadGestionarInfracciones = loadGestionarInfracciones;
window.loadNuevaInfraccion = loadNuevaInfraccion;
window.loadBuscarInfracciones = loadBuscarInfracciones;
window.loadEstadisticasInfracciones = loadEstadisticasInfracciones;

console.log('✅ Módulo fiscalizador cargado completamente');