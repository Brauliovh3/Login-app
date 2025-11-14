/**
 * ================================
 * GESTIÓN DE ACTAS - FISCALIZADOR
 * Sistema de Gestión - JavaScript
 * ================================
 */

// Variable global para almacenar actas
let todasLasActas = [];

// ================================
// FUNCIONES HELPER - USUARIO
// ================================

// Función para obtener datos del usuario actual
function getCurrentUserData() {
    // Obtener datos del usuario desde variables globales del dashboard
    if (typeof window.dashboardUserName !== 'undefined' && typeof window.dashboardUserRole !== 'undefined') {
        return {
            id: window.dashboardUserId || 1, // Usar ID real del usuario
            name: window.dashboardUserName,
            role: window.dashboardUserRole,
            username: window.dashboardUserName
        };
    }
    
    // Fallback si no hay datos globales
    console.warn('No se encontraron datos del usuario en variables globales');
    return {
        id: 1,
        name: 'Fiscalizador',
        role: 'fiscalizador',
        username: 'fiscal'
    };
}

// ================================
// FUNCIONES HELPER - CONEXIÓN
// ================================

// Función helper para fetch con timeout automático
async function fetchWithTimeout(url, options = {}, timeout = 10000) {
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), timeout);
    
    try {
        const response = await fetch(url, {
            ...options,
            signal: controller.signal
        });
        clearTimeout(timeoutId);
        return response;
    } catch (error) {
        clearTimeout(timeoutId);
        if (error.name === 'AbortError') {
            throw new Error('La conexión tardó demasiado tiempo. Verifique su conexión a internet.');
        }
        throw error;
    }
}

// ================================
// FUNCIONES HELPER - MODALES
// ================================

// Función para validar que un elemento DOM exista
function validarElemento(elementId, funcionNombre = '') {
    const elemento = document.getElementById(elementId);
    if (!elemento) {
        const mensaje = `Error: Elemento '${elementId}' no encontrado${funcionNombre ? ` en ${funcionNombre}` : ''}`;
        console.error('❌', mensaje);
        mostrarErrorActas(mensaje + '. Por favor, recarga la página.');
        return null;
    }
    return elemento;
}

// Función para limpiar y cerrar todos los modales
function limpiarTodosLosModales() {
    console.log('🧹 Limpiando todos los modales...');
    
    // Lista de IDs de modales utilizados
    const modalIds = ['generalModal', 'verActaModal', 'editarActaModal'];
    
    modalIds.forEach(modalId => {
        const modalElement = document.getElementById(modalId);
        if (modalElement) {
            // Obtener instancia del modal si existe
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (modalInstance) {
                modalInstance.hide();
            }
            
            // Remover clases y atributos que puedan quedar
            modalElement.classList.remove('show');
            modalElement.style.display = 'none';
            modalElement.removeAttribute('aria-modal');
            modalElement.setAttribute('aria-hidden', 'true');
        }
    });
    
    // Remover backdrops que puedan haber quedado
    const backdrops = document.querySelectorAll('.modal-backdrop');
    backdrops.forEach(backdrop => backdrop.remove());
    
    // Restablecer el body
    document.body.classList.remove('modal-open');
    document.body.style.removeProperty('overflow');
    document.body.style.removeProperty('padding-right');
    
    console.log('✅ Modales limpiados correctamente');
}

// Función específica para cancelar acciones
function cancelarAccion(mostrarMensaje = false) {
    console.log('❌ Cancelando acción... mostrarMensaje:', mostrarMensaje);
    limpiarTodosLosModales();
    
    // Solo mostrar mensaje si es una cancelación explícita del usuario
    if (mostrarMensaje) {
        mostrarInfoActas('Acción cancelada por el usuario');
    }
}

// ================================
// FUNCIONES PRINCIPALES - ACTAS
// ================================

// Función principal llamada desde el menú
function loadActas(event) {
    console.log('🔄 Cargando sistema de gestión de actas...');
    
    // Obtener la sección específica del data-section
    const clickedElement = event?.target?.closest('a');
    const section = clickedElement?.getAttribute('data-section') || 'actas-contra';
    
    console.log('🎯 Sección solicitada:', section);
    
    switch(section) {
        case 'crear-acta':
            loadCrearActa();
            break;
        case 'mis-actas':
            loadMisActas();
            break;
        case 'actas-contra':
        default:
            loadGestionActas();
            // Cargar las actas automáticamente
            setTimeout(() => {
                cargarActasDesdeAPI();
            }, 500);
            break;
    }
}

// Función específica para crear acta
function loadCrearActa() {
    console.log('📝 Cargando formulario de nueva acta...');
    loadGestionActas();
    // Mostrar mensaje y abrir modal después de cargar la interfaz
    setTimeout(() => {
        try {
            showCrearActaModal();
        } catch (error) {
            console.error('Error al abrir modal:', error);
            mostrarNotificacion('Función de crear acta cargada. Click en "Nueva Acta" para continuar.', 'info');
        }
    }, 500);
}

// Función específica para mis actas (filtradas por usuario)
function loadMisActas() {
    console.log('🛒 Cargando historial de mis actas...');
    
    const contentContainer = validarElemento('contentContainer', 'loadMisActas');
    if (!contentContainer) return;
    
    // Generar el HTML del historial de actas del fiscalizador
    generarHTMLHistorialActas();
    
    // Cargar solo las actas del usuario actual
    setTimeout(() => {
        cargarMisActasDesdeAPI();
    }, 500);
}

function loadGestionActas() {
    console.log('📋 Cargando gestión completa de actas...');
    
    const contentContainer = validarElemento('contentContainer', 'loadGestionActas');
    if (!contentContainer) return;
    
    // Generar el HTML de la interfaz de gestión completa
    generarHTMLGestionActas();
    
    // Cargar todas las actas automáticamente
    setTimeout(() => {
        cargarActasDesdeAPI();
    }, 500);
}

// Función para generar el HTML del historial de actas del fiscalizador
function generarHTMLHistorialActas() {
    const contentContainer = document.getElementById('contentContainer');
    if (!contentContainer) {
        console.error('❌ contentContainer no encontrado en generarHTMLHistorialActas');
        return;
    }
    
    contentContainer.innerHTML = `
        <div class="content-section active">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-history"></i> Mi Historial de Actas</h2>
                <div class="d-flex gap-2">
                    <button class="btn btn-success" onclick="exportarMisActas('excel')">
                        <i class="fas fa-file-excel"></i> Exportar Mi Historial
                    </button>
                    <button class="btn btn-info" onclick="imprimirMisActas()">
                        <i class="fas fa-print"></i> Imprimir Mi Historial
                    </button>
                    <button class="btn btn-outline-secondary" onclick="cargarMisActasDesdeAPI()">
                        <i class="fas fa-sync-alt"></i> Actualizar
                    </button>
                </div>
            </div>
            
            <!-- Estadísticas del Fiscalizador -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="text-primary" id="totalActasFisca">0</h5>
                            <small class="text-muted">Total Actas</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="text-warning" id="actasPendientesFisca">0</h5>
                            <small class="text-muted">Pendientes</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="text-success" id="actasPagadasFisca">0</h5>
                            <small class="text-muted">Pagadas</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="text-danger" id="actasAnuladasFisca">0</h5>
                            <small class="text-muted">Anuladas</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Filtros del Historial -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">Buscar en Mi Historial</label>
                            <input type="text" class="form-control" id="searchMisActas" placeholder="Número, placa, conductor..." onkeyup="filtrarMisActas()">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Estado</label>
                            <select class="form-select" id="filterEstadoMisActas" onchange="filtrarMisActas()">
                                <option value="">Todos</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="pagada">Pagada</option>
                                <option value="anulada">Anulada</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Fecha Desde</label>
                            <input type="date" class="form-control" id="filterFechaDesdeMisActas" onchange="filtrarMisActas()">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Fecha Hasta</label>
                            <input type="date" class="form-control" id="filterFechaHastaMisActas" onchange="filtrarMisActas()">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button class="btn btn-secondary w-100" onclick="limpiarFiltrosMisActas()">
                                <i class="fas fa-times"></i> Limpiar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla del Historial -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="misActasTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>Número</th>
                                    <th>Fecha</th>
                                    <th>Placa</th>
                                    <th>Conductor</th>
                                    <th>Estado</th>
                                    <th>Código Inf.</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="misActasTableBody">
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <div class="spinner-border" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                        <p class="mt-2">Cargando mi historial...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Función para generar el HTML de gestión de actas
function generarHTMLGestionActas() {
    const contentContainer = document.getElementById('contentContainer');
    if (!contentContainer) {
        console.error('❌ contentContainer no encontrado en generarHTMLGestionActas');
        return;
    }
    
    contentContainer.innerHTML = `
        <div class="content-section active">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-file-alt"></i> Gestión de Actas</h2>
                <div class="d-flex gap-2">
                    <button class="btn btn-success" onclick="exportarActas('excel')">
                        <i class="fas fa-file-excel"></i> Excel
                    </button>
                    <button class="btn btn-info" onclick="exportarActas('pdf')">
                        <i class="fas fa-file-pdf"></i> PDF
                    </button>

                    <button class="btn btn-warning" onclick="imprimirActas()">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
                    <button class="btn btn-primary" onclick="showCrearActaModal()">
                        <i class="fas fa-plus"></i> Nueva Acta
                    </button>
                    <button class="btn btn-outline-secondary" onclick="cargarActasDesdeAPI()">
                        <i class="fas fa-refresh"></i> Actualizar
                    </button>
                </div>
            </div>
            
            <!-- Filtros y Búsqueda -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Buscar Acta</label>
                            <input type="text" class="form-control" id="searchActas" placeholder="Número, placa, conductor..." onkeyup="filtrarActas()">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Estado</label>
                            <select class="form-select" id="filterEstado" onchange="filtrarActas()">
                                <option value="">Todos</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="pagada">Pagada</option>
                                <option value="anulada">Anulada</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Fecha Desde</label>
                            <input type="date" class="form-control" id="filterFechaDesde" onchange="filtrarActas()">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Fecha Hasta</label>
                            <input type="date" class="form-control" id="filterFechaHasta" onchange="filtrarActas()">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button class="btn btn-secondary w-100" onclick="limpiarFiltros()">
                                <i class="fas fa-times"></i> Limpiar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Actas -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="actasTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>Número</th>
                                    <th>Placa</th>
                                    <th>Conductor</th>
                                    <th>RUC/DNI</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="actasTableBody">
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <div class="spinner-border" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                        <p class="mt-2">Cargando actas...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// ================================
// CARGAR ACTAS DESDE API
// ================================

async function cargarActasDesdeAPI() {
    // Mostrar indicador de carga
    const tbody = document.getElementById('actasTableBody');
    if (tbody) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Actualizando...</span>
                    </div>
                    <p class="mt-2">Actualizando actas...</p>
                </td>
            </tr>
        `;
    }
    
    const tryUrls = ['/dashboard.php?api=actas-raw'];
    let data = null;
    try {
        // Intento 1: actas por rol; si falla o viene vacío, intentar actas-admin
        for (let i = 0; i < tryUrls.length; i++) {
            const response = await fetchWithTimeout(tryUrls[i], {
                method: 'GET',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            });

            const text = await response.text();
            let parsed;
            try { parsed = JSON.parse(text); } catch (err) {
                console.error('❌ Respuesta no JSON al listar actas:', text);
                continue;
            }
            console.log('📦 Respuesta actas desde', tryUrls[i], parsed);
            if (parsed && parsed.success && Array.isArray(parsed.actas) && parsed.actas.length >= 0) {
                data = parsed;
                break;
            }
        }

        if (!data) {
            throw { status: 500, text: 'Sin datos válidos desde endpoints de actas' };
        }
        
        if (data.success) {
            if (typeof data.db !== 'undefined' || typeof data.count !== 'undefined') {
                const dbName = data.db || 'desconocida';
                const total = typeof data.count !== 'undefined' ? data.count : (Array.isArray(data.actas) ? data.actas.length : 0);
                mostrarInfoActas(`BD: ${dbName} · Registros en actas: ${total}`);
            }
            // Si el backend envía conteo y DB, úsalos para diagnosticar
            if (typeof data.count !== 'undefined' && data.count > 0 && (!Array.isArray(data.actas) || data.actas.length === 0)) {
                console.warn('⚠️ El backend reporta filas pero no se recibieron actas. DB:', data.db, 'count:', data.count);
                mostrarInfoActas(`Conexión a BD "${data.db || 'desconocida'}". Registros en actas: ${data.count}. Recibidos: 0`);
            }
            todasLasActas = Array.isArray(data.actas) ? data.actas : [];
            mostrarActas(todasLasActas);
            if (todasLasActas.length > 0) {
                mostrarExitoActas(`${todasLasActas.length} actas cargadas correctamente`);
            } else {
                mostrarInfoActas('No se encontraron actas en el sistema');
            }
        } else {
            mostrarErrorActas('Error al cargar actas: ' + (data.message || 'Error desconocido'));
        }
    } catch (error) {
        if (error.text) {
            mostrarErrorActas('Respuesta inesperada del servidor');
        } else {
            mostrarErrorActas('Error de conexión: ' + error.message);
        }
    }
}

// Función para cargar solo las actas del usuario actual
async function cargarMisActasDesdeAPI() {
    const usuario = getCurrentUserData();
    const tbody = document.getElementById('misActasTableBody');
    
    if (tbody) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando mi historial...</p>
                </td>
            </tr>
        `;
    }
    
    try {
        const response = await fetchWithTimeout('/dashboard.php?api=actas', {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json'
            }
        });

        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (err) {
            console.error('❌ Respuesta no JSON al listar MIS actas:', text);
            throw { status: response.status, text };
        }
        console.log('📦 Respuesta MIS actas:', data);
        
        if (data.success) {
            const todasActas = data.actas || [];
            // Filtrar actas del fiscalizador actual por múltiples criterios
            const userName = window.dashboardUserName;
            const userId = window.dashboardUserId;
            
            const misActas = todasActas.filter(acta => {
                return acta.inspector_responsable === userName || 
                       acta.inspector === userName ||
                       acta.user_name === userName ||
                       acta.fiscalizador_id == userId ||
                       acta.user_id == userId;
            });
            
            window.misActasFiscalizador = misActas;
            mostrarMisActasEnTabla(misActas);
            actualizarEstadisticasFiscalizador(misActas);
            
            if (misActas.length > 0) {
                mostrarExitoActas(`Mi historial: ${misActas.length} actas encontradas`);
            } else {
                mostrarInfoActas('No tienes actas registradas aún');
            }
        } else {
            mostrarErrorActas('Error al cargar mis actas: ' + (data.message || 'Error desconocido'));
        }
    } catch (error) {
        if (error.text) {
            mostrarErrorActas('Respuesta inesperada del servidor');
        } else {
            mostrarErrorActas('Error de conexión: ' + error.message);
        }
    }
}

function mostrarActas(actas) {
    console.log('📋 Iniciando mostrarActas con', actas?.length || 0, 'actas');
    
    // Validar datos de entrada
    if (!actas || !Array.isArray(actas)) {
        console.error('❌ Datos de actas inválidos:', actas);
        mostrarErrorActas('Error: Datos de actas inválidos. Intenta recargar las actas.');
        return;
    }
    
    // Normalizar estado antes de renderizar (acepta numérico o textos alternos)
    const normalizarEstado = (valor, estadoTexto) => {
        if (typeof valor === 'number') {
            switch (valor) {
                case 0: return 'pendiente';
                case 1: return 'procesada';
                case 2: return 'anulada';
                case 3: return 'pagada';
                default: return 'pendiente';
            }
        }
        if (typeof valor === 'string' && valor.trim() !== '') {
            return valor.toLowerCase();
        }
        if (estadoTexto && typeof estadoTexto === 'string') {
            return estadoTexto.toLowerCase();
        }
        return 'pendiente';
    };
    actas.forEach(a => { a.estado = normalizarEstado(a.estado, a.estado_texto); });

    // Función para verificar y crear la tabla si es necesario
    const verificarYCrearTabla = () => {
        let tbody = document.getElementById('actasTableBody');
        
        if (!tbody) {
            console.log('⚠️ actasTableBody no encontrado, verificando contentContainer...');
            
            // Verificar que contentContainer existe
            const contentContainer = document.getElementById('contentContainer');
            if (!contentContainer) {
                console.error('❌ contentContainer no encontrado');
                mostrarErrorActas('Error crítico: Contenedor principal no encontrado. Recarga la página.');
                return null;
            }
            
            // Verificar si ya hay contenido en contentContainer
            const existingContent = contentContainer.querySelector('.content-section');
            if (!existingContent) {
                console.log('🔄 Regenerando contenido de gestión de actas...');
                generarHTMLGestionActas();
                
                // Intentar encontrar la tabla nuevamente
                tbody = document.getElementById('actasTableBody');
            }
        }
        
        return tbody;
    };
    
    // Función para generar solo el HTML de la tabla
    const generarHTMLGestionActas = () => {
        const contentContainer = document.getElementById('contentContainer');
        contentContainer.innerHTML = `
            <div class="content-section active">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-file-alt"></i> Gestión de Actas</h2>
                    <div class="d-flex gap-2">
                        <button class="btn btn-success" onclick="exportarActas('excel')">
                            <i class="fas fa-file-excel"></i> Excel
                        </button>
                        <button class="btn btn-info" onclick="exportarActas('pdf')">
                            <i class="fas fa-file-pdf"></i> PDF
                        </button>
                        <button class="btn btn-secondary" onclick="exportarActas('pdf-detallado')">
                            <i class="fas fa-file-pdf"></i> PDF Detallado
                        </button>
                        <button class="btn btn-warning" onclick="imprimirActas()">
                            <i class="fas fa-print"></i> Imprimir
                        </button>
                        <button class="btn btn-primary" onclick="showCrearActaModal()">
                            <i class="fas fa-plus"></i> Nueva Acta
                        </button>
                        <button class="btn btn-outline-secondary" onclick="cargarActasDesdeAPI()">
                            <i class="fas fa-refresh"></i> Actualizar
                        </button>
                    </div>
                </div>
                
                <!-- Filtros y Búsqueda -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Buscar Acta</label>
                                <input type="text" class="form-control" id="searchActas" placeholder="Número, placa, conductor..." onkeyup="filtrarActas()">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Estado</label>
                                <select class="form-select" id="filterEstado" onchange="filtrarActas()">
                                    <option value="">Todos</option>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="pagada">Pagada</option>
                                    <option value="anulada">Anulada</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Fecha Desde</label>
                                <input type="date" class="form-control" id="filterFechaDesde" onchange="filtrarActas()">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Fecha Hasta</label>
                                <input type="date" class="form-control" id="filterFechaHasta" onchange="filtrarActas()">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <button class="btn btn-secondary w-100" onclick="limpiarFiltros()">
                                    <i class="fas fa-times"></i> Limpiar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Actas -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="actasTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Número</th>
                                        <th>Placa</th>
                                        <th>Conductor</th>
                                        <th>RUC/DNI</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="actasTableBody">
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <div class="spinner-border" role="status">
                                                <span class="visually-hidden">Cargando...</span>
                                            </div>
                                            <p class="mt-2">Cargando actas...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        `;
    };
    
    // Intentar obtener o crear la tabla
    let tbody = verificarYCrearTabla();
    
    // Si aún no existe, usar un observador de mutación
    if (!tbody) {
        console.log('🔍 Usando observador de DOM para detectar tabla...');
        
        const observer = new MutationObserver((mutations) => {
            tbody = document.getElementById('actasTableBody');
            if (tbody) {
                console.log('✅ Tabla detectada por observador');
                observer.disconnect();
                renderizarActasEnTabla(tbody, actas);
            }
        });
        
        // Observar cambios en contentContainer
        const contentContainer = document.getElementById('contentContainer');
        if (contentContainer) {
            observer.observe(contentContainer, { childList: true, subtree: true });
            
            // Timeout de seguridad para el observador
            setTimeout(() => {
                observer.disconnect();
                const finalTbody = document.getElementById('actasTableBody');
                if (!finalTbody) {
                    console.error('❌ Timeout - tabla no creada, intentando solución de emergencia...');
                    // Solución de emergencia: forzar regeneración
                    try {
                        if (window.generarHTMLGestionActas) {
                            window.generarHTMLGestionActas();
                            setTimeout(() => {
                                const emergencyTbody = document.getElementById('actasTableBody');
                                if (emergencyTbody) {
                                    console.log('✅ Tabla creada con solución de emergencia');
                                    renderizarActasEnTabla(emergencyTbody, actas);
                                } else {
                                    mostrarErrorActas('Error persistente al crear tabla. Intenta hacer clic en "Actualizar".');
                                }
                            }, 200);
                        } else {
                            mostrarErrorActas('Error: Función de generación no disponible. Recarga la página.');
                        }
                    } catch (error) {
                        console.error('Error en solución de emergencia:', error);
                        mostrarErrorActas('Error crítico. Por favor, recarga la página.');
                    }
                } else {
                    renderizarActasEnTabla(finalTbody, actas);
                }
            }, 2000);
        }
        
        return;
    }
    
    // Si la tabla existe, renderizar inmediatamente
    renderizarActasEnTabla(tbody, actas);
}

// Función separada para renderizar las actas en la tabla
function renderizarActasEnTabla(tbody, actas) {
    console.log('🎨 Renderizando', actas.length, 'actas en la tabla');
    
    if (actas.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center">
                    <i class="fas fa-file-alt text-muted" style="font-size: 2rem;"></i>
                    <p class="mt-2 text-muted">No se encontraron actas</p>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = actas.map(acta => {
        // Construir nombre del conductor
        let conductor = 'N/A';
        
        // Prioridad: nombre_conductor completo, luego apellidos + nombres separados
        if (acta.nombre_conductor && acta.nombre_conductor.trim() !== '') {
            conductor = acta.nombre_conductor.trim();
        } else if (acta.conductor_nombre && acta.conductor_nombre.trim() !== '') {
            conductor = acta.conductor_nombre.trim();
        } else {
            // Intentar construir desde apellidos y nombres separados
            const apellidos = acta.apellidos_conductor || acta.apellidos || '';
            const nombres = acta.nombres_conductor || acta.nombres || '';
            
            if (apellidos && nombres) {
                conductor = `${apellidos.trim()}, ${nombres.trim()}`;
            } else if (apellidos) {
                conductor = apellidos.trim();
            } else if (nombres) {
                conductor = nombres.trim();
            }
        }
        
        return `
        <tr>
            <td>
                <strong>${acta.numero_acta || 'N/A'}</strong>
            </td>
            <td>
                <span class="badge bg-info">${acta.placa || acta.placa_vehiculo || 'N/A'}</span>
            </td>
            <td>
                ${conductor}
            </td>
            <td>
                <small class="text-muted">${acta.ruc_dni || 'N/A'}</small>
            </td>
            <td>
                <span class="badge ${getEstadoBadgeClass(acta.estado)}">${getEstadoDisplayName(acta.estado)}</span>
            </td>
            <td>
                <small class="text-muted">
                    ${acta.fecha_acta ? formatDate(acta.fecha_acta) : (acta.created_at ? formatDate(acta.created_at) : (acta.fecha_intervencion ? formatDate(acta.fecha_intervencion) : ''))}
                </small>
            </td>
            <td class="text-center">
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-primary" onclick="verActa(${acta.id})" title="Ver">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-success" onclick="editarActa(${acta.id})" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-info" onclick="imprimirActa(${acta.id})" title="Imprimir">
                        <i class="fas fa-print"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="exportarActaPDF(${acta.id})" title="Exportar PDF">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="anularActa(${acta.id}, '${acta.numero_acta}')" title="Anular Acta">
                        <i class="fas fa-ban"></i>
                    </button>
                </div>
            </td>
        </tr>
    `;
    }).join('');
}

// ================================
// FUNCIONES DE MODAL - ACTAS
// ================================

function showCrearActaModal() {
    console.log('🆕 Abriendo modal para crear nueva acta...');
    
    const modalTitle = validarElemento('modalTitle', 'showCrearActaModal');
    const modalBody = validarElemento('modalBody', 'showCrearActaModal');
    const modalFooter = validarElemento('modalFooter', 'showCrearActaModal');
    
    if (!modalTitle || !modalBody || !modalFooter) return;
    
    // Configurar título del modal (el botón X ya existe en el header del modal por defecto)
    modalTitle.innerHTML = `
        <i class="fas fa-plus-circle me-2"></i> Crear Nueva Acta
    `;
    
    // Configurar contenido del modal (más amplio horizontalmente)
    modalBody.innerHTML = `
        <style>
            #generalModal .modal-dialog { max-width: 1400px; margin: 1rem auto; width: 95%; }
            #generalModal .modal-content { min-height: 600px; }
            @media (min-width: 1200px) {
                #generalModal .modal-dialog { max-width: 1300px; }
            }
        </style>
        <form id="formCrearActa" class="row g-4">
            
            <!-- Datos del Operador/Empresa -->
            <div class="col-12">
                <h6 class="text-primary border-bottom pb-2">
                    <i class="fas fa-building"></i> Datos del Operador/Empresa
                </h6>
            </div>
            
            <div class="col-md-6">
                <label class="form-label">RUC/DNI *</label>
                <input type="text" class="form-control" name="ruc_dni" id="ruc_dni" required 
                       placeholder="11 dígitos para RUC, 8 para DNI">
            </div>
            
            <div class="col-md-6">
                <label class="form-label">Razón Social / Nombre</label>
                <input type="text" class="form-control" name="razon_social" id="razon_social" 
                       placeholder="Nombre o razón social">
            </div>
            
            <!-- Datos del Vehículo -->
            <div class="col-12 mt-4">
                <h6 class="text-warning border-bottom pb-2">
                    <i class="fas fa-car"></i> Datos del Vehículo
                </h6>
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Placa del Vehículo *</label>
                <input type="text" class="form-control" name="placa" id="placa" required 
                       placeholder="ABC-123" style="text-transform: uppercase;">
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Tipo de Agente *</label>
                <select class="form-select" name="tipo_agente" id="tipo_agente" required>
                    <option value="">Seleccione...</option>
                    <option value="Transportista">Transportista</option>
                    <option value="Operador de Ruta">Operador de Ruta</option>
                    <option value="Conductor" selected>Conductor</option>
                    <option value="Inspector">Inspector</option>
                </select>
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Tipo de Servicio *</label>
                <select class="form-select" name="tipo_servicio" id="tipo_servicio" required>
                    <option value="">Seleccione...</option>
                    <option value="Interprovincial">Interprovincial</option>
                    <option value="Interdistrital">Interdistrital</option>
                    <option value="Urbano">Urbano</option>
                    <option value="Turístico">Turístico</option>
                    <option value="Carga">Transporte de Carga</option>
                    <option value="Especial">Servicio Especial</option>
                </select>
            </div>
            
            <!-- Datos del Conductor -->
            <div class="col-12 mt-4">
                <h6 class="text-success border-bottom pb-2">
                    <i class="fas fa-user"></i> Datos del Conductor
                </h6>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Apellidos del Conductor *</label>
                <input type="text" class="form-control" name="apellidos_conductor" id="apellidos_conductor" required
                       placeholder="Ej: García López">
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Nombres del Conductor *</label>
                <input type="text" class="form-control" name="nombres_conductor" id="nombres_conductor" required
                       placeholder="Ej: Juan Carlos">
            </div>
            
            <div class="col-md-4">
                <label class="form-label">N° Licencia</label>
                <input type="text" class="form-control" name="licencia_conductor" id="licencia_conductor" 
                       placeholder="Ej: A12345678">
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Provincia *</label>
                <select class="form-select" name="provincia" id="provincia" required onchange="cargarDistritos()">
                    <option value="">Seleccione provincia...</option>
                    <option value="Abancay">Abancay</option>
                    <option value="Andahuaylas">Andahuaylas</option>
                    <option value="Antabamba">Antabamba</option>
                    <option value="Aymaraes">Aymaraes</option>
                    <option value="Cotabambas">Cotabambas</option>
                    <option value="Chincheros">Chincheros</option>
                    <option value="Grau">Grau</option>
                </select>
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Distrito *</label>
                <select class="form-select" name="distrito" id="distrito" required>
                    <option value="">Primero seleccione provincia</option>
                </select>
            </div>
            
            <!-- Datos de la Intervención -->
            <div class="col-12 mt-4">
                <h6 class="text-danger border-bottom pb-2">
                    <i class="fas fa-map-marker-alt"></i> Datos de la Intervención
                </h6>
            </div>

            <div class="col-md-12">
                <label class="form-label">Lugar de Intervención *</label>
                <input type="text" class="form-control" name="lugar_intervencion" id="lugar_intervencion" required
                       placeholder="Ej: Av. Núñez - Abancay, Apurímac">
            </div>
            
            <!-- Campos ocultos automáticos -->
            <input type="hidden" name="fecha_intervencion" id="fecha_intervencion" 
                   value="${new Date().toISOString().split('T')[0]}">
            <input type="hidden" name="hora_intervencion" id="hora_intervencion">
            <input type="hidden" name="inspector_responsable" id="inspector_responsable" 
                   value="${window.dashboardUserName || ''}">
            
            <!-- Código de Infracción -->
            <div class="col-12 mt-4">
                <h6 class="text-danger border-bottom pb-2">
                    <i class="fas fa-gavel"></i> Infracción Detectada
                </h6>
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Código Base *</label>
                <select class="form-select" name="codigo_base" id="codigo_base" required>
                    <option value="">Seleccione código...</option>
                </select>
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Subcategorías *</label>
                <div id="subcategoria-container" class="border rounded p-2" style="min-height: 100px; max-height: 200px; overflow-y: auto; background-color: #f8f9fa;">
                    <small class="text-muted">Primero seleccione código base</small>
                </div>
                <input type="hidden" name="codigo_infraccion" id="codigo_infraccion">
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Gravedad</label>
                <div class="mt-2">
                    <span id="badge_gravedad" class="badge bg-secondary">Sin seleccionar</span>
                </div>
            </div>
            
            <div class="col-12">
                <label class="form-label">Descripción de la Infracción</label>
                <textarea class="form-control" name="descripcion_infraccion" id="descripcion_infraccion" 
                          rows="3" readonly style="background-color: #f8f9fa;"></textarea>
            </div>
        </form>
    `;
    
    // Configurar botones del modal centrados (cerrar está en header por defecto de Bootstrap)
    modalFooter.innerHTML = `
        <style>
            #botonesAccion { display: none !important; }
            #botonesAccion.activo { display: flex !important; }
        </style>
        <div class="d-flex justify-content-center align-items-center w-100 flex-column gap-3">
            <div id="botonesAccion" class="d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-secondary" onclick="limpiarFormularioActa()">
                    <i class="fas fa-eraser"></i> Limpiar
                </button>
                <button type="button" class="btn btn-success" onclick="exportarActaActual('excel')">
                    <i class="fas fa-file-excel"></i> Exportar Excel
                </button>
                <button type="button" class="btn btn-info" onclick="exportarActaActual('pdf')">
                    <i class="fas fa-file-pdf"></i> Exportar PDF
                </button>
                <button type="button" class="btn btn-primary" onclick="guardarNuevaActa()">
                    <i class="fas fa-save"></i> Guardar Acta
                </button>
            </div>
            <small id="estadoValidacion" class="text-warning text-center fw-bold">
                <i class="fas fa-exclamation-circle"></i> Complete los 8 campos obligatorios para ver las opciones
            </small>
        </div>
    `;
    
    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('generalModal'));
    modal.show();

    // Agregar event listener para limpiar backdrop cuando se cierre el modal
    document.getElementById('generalModal').addEventListener('hidden.bs.modal', function() {
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(backdrop => backdrop.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    });

    // Configurar captura automática de timestamp cuando el usuario comience a escribir
    setTimeout(() => {
        configurarTimestampAutomatico();
        configurarValidacionDinamica();
        cargarCodigosInfracciones();
        console.log('🎯 Modal de acta configurado completamente');
    }, 500);
    
    // Función helper para debug - permite verificar manualmente
    window.debugValidarBotones = function() {
        const form = document.getElementById('formCrearActa');
        const botones = document.getElementById('botonesAccion');
        console.log('🔍 Estado del formulario:', !!form);
        console.log('🔍 Estado de botones:', !!botones);
        if (form) {
            const formData = new FormData(form);
            const campos = ['ruc_dni', 'placa', 'tipo_agente', 'tipo_servicio', 'nombre_conductor', 'lugar_intervencion', 'descripcion_hechos'];
            campos.forEach(campo => {
                const valor = formData.get(campo);
                console.log(`📝 ${campo}: "${valor}"`);
            });
        }
    };
}

// ================================
// CARGAR CÓDIGOS DE INFRACCIONES
// ================================

let todosLosCodigos = []; // Variable global para almacenar todos los códigos

async function cargarCodigosInfracciones() {
    console.log('📋 Cargando códigos de infracciones...');
    
    try {
        const response = await fetch('/dashboard.php?api=codigos-infracciones', {
            method: 'GET',
            credentials: 'same-origin'
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success && data.codigos) {
            todosLosCodigos = data.codigos;
            
            // Obtener códigos base únicos (sin subcategorías)
            const codigosBase = {};
            todosLosCodigos.forEach(item => {
                const base = item.codigo.split('-')[0]; // F.1, F.4, I.1, etc.
                if (!codigosBase[base]) {
                    codigosBase[base] = {
                        codigo: base,
                        gravedad: item.gravedad,
                        descripcion: item.descripcion
                    };
                }
            });
            
            // Poblar primer select (códigos base)
            const selectBase = document.getElementById('codigo_base');
            if (!selectBase) {
                console.error('❌ Select base no encontrado');
                return;
            }
            
            selectBase.innerHTML = '<option value="">Seleccione código...</option>';
            
            Object.keys(codigosBase).sort().forEach(base => {
                const option = document.createElement('option');
                option.value = base;
                option.textContent = `${base} - ${codigosBase[base].gravedad}`;
                option.dataset.gravedad = codigosBase[base].gravedad;
                selectBase.appendChild(option);
            });
            
            console.log(`✅ ${Object.keys(codigosBase).length} códigos base cargados`);
            
            // Configurar listeners
            selectBase.addEventListener('change', onCodigoBaseChange);
            
            // El listener para checkboxes se configura dinámicamente en onCodigoBaseChange
            
        } else {
            console.error('❌ Error al cargar códigos:', data.message);
            mostrarErrorActas('No se pudieron cargar los códigos de infracciones');
        }
        
    } catch (error) {
        console.error('💥 Error al cargar códigos:', error);
        mostrarErrorActas('Error de conexión al cargar códigos');
    }
}

function onCodigoBaseChange(event) {
    const codigoBase = event.target.value;
    const containerSubcat = document.getElementById('subcategoria-container');
    const badgeGravedad = document.getElementById('badge_gravedad');
    const textareaDesc = document.getElementById('descripcion_infraccion');
    const hiddenCodigo = document.getElementById('codigo_infraccion');
    
    // Reset
    containerSubcat.innerHTML = '<small class="text-muted">Seleccione subcategorías...</small>';
    textareaDesc.value = '';
    hiddenCodigo.value = '';
    badgeGravedad.textContent = 'Sin seleccionar';
    badgeGravedad.className = 'badge bg-secondary';
    
    if (!codigoBase) {
        containerSubcat.innerHTML = '<small class="text-muted">Primero seleccione código base</small>';
        return;
    }
    
    // Actualizar badge de gravedad
    const selectedOption = event.target.options[event.target.selectedIndex];
    const gravedad = selectedOption.dataset.gravedad;
    if (gravedad) {
        badgeGravedad.textContent = gravedad;
        const gravedadClass = gravedad === 'Muy grave' ? 'bg-danger' : (gravedad === 'Grave' ? 'bg-warning' : 'bg-info');
        badgeGravedad.className = `badge ${gravedadClass}`;
    }
    
    // Buscar subcategorías para este código
    const subcategorias = todosLosCodigos.filter(c => c.codigo.startsWith(codigoBase + '-') || c.codigo === codigoBase);
    
    if (subcategorias.length === 0) {
        console.log('⚠️ No hay subcategorías para', codigoBase);
        containerSubcat.innerHTML = '<small class="text-muted">No hay subcategorías disponibles</small>';
        return;
    }
    
    if (subcategorias.length === 1 && subcategorias[0].codigo === codigoBase) {
        // Solo hay código base, sin subcategorías
        containerSubcat.innerHTML = `
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="general" id="subcat_general" 
                       data-codigo="${codigoBase}" data-descripcion="${subcategorias[0].descripcion}" 
                       onchange="onSubcategoriaCheckboxChange()">
                <label class="form-check-label" for="subcat_general">
                    General - ${subcategorias[0].descripcion.substring(0, 40)}...
                </label>
            </div>
        `;
        console.log('✅ Código sin subcategorías:', codigoBase);
    } else {
        // Hay subcategorías - crear checkboxes
        let checkboxesHTML = '';
        subcategorias.forEach((sub, index) => {
            if (sub.codigo !== codigoBase) { // Excluir el código base si viene solo
                const subcategoria = sub.codigo.split('-')[1] || 'general';
                const checkboxId = `subcat_${subcategoria}_${index}`;
                checkboxesHTML += `
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" value="${subcategoria}" id="${checkboxId}" 
                               data-codigo="${sub.codigo}" data-descripcion="${sub.descripcion}" 
                               onchange="onSubcategoriaCheckboxChange()">
                        <label class="form-check-label" for="${checkboxId}" style="font-size: 0.9rem;">
                            <strong>${subcategoria})</strong> ${sub.descripcion.substring(0, 60)}...
                        </label>
                    </div>
                `;
            }
        });
        containerSubcat.innerHTML = checkboxesHTML;
        console.log(`✅ ${subcategorias.length} subcategorías cargadas para ${codigoBase}`);
    }
}

function onSubcategoriaCheckboxChange() {
    const checkboxes = document.querySelectorAll('#subcategoria-container input[type="checkbox"]:checked');
    const textareaDesc = document.getElementById('descripcion_infraccion');
    const hiddenCodigo = document.getElementById('codigo_infraccion');
    
    if (checkboxes.length === 0) {
        textareaDesc.value = '';
        hiddenCodigo.value = '';
        return;
    }
    
    // Recopilar códigos y descripciones seleccionados
    const codigosSeleccionados = [];
    const descripcionesSeleccionadas = [];
    
    checkboxes.forEach(checkbox => {
        const codigo = checkbox.dataset.codigo;
        const descripcion = checkbox.dataset.descripcion;
        
        if (codigo) codigosSeleccionados.push(codigo);
        if (descripcion) descripcionesSeleccionadas.push(descripcion);
    });
    
    // Actualizar campos
    hiddenCodigo.value = codigosSeleccionados.join(', ');
    textareaDesc.value = descripcionesSeleccionadas.join('\n\n');
    
    console.log('✅ Códigos seleccionados:', codigosSeleccionados);
}

function configurarValidacionDinamica() {
    const camposRequeridos = ['ruc_dni', 'placa', 'tipo_agente', 'tipo_servicio', 'apellidos_conductor', 'nombres_conductor', 'lugar_intervencion', 'provincia', 'distrito', 'codigo_base', 'subcategoria'];
    const botonesAccion = document.getElementById('botonesAccion');
    
    // Función para restringir DNI/RUC a solo números (máx 11 dígitos)
    function restringirDNI(event) {
        const input = event.target;
        const key = event.key;

        // En keypress, prevenir teclas no numéricas
        if (event.type === 'keypress' && !/\d/.test(key) && key.length === 1) {
            event.preventDefault();
            return;
        }

        // En input, limpiar no dígitos y limitar longitud
        if (event.type === 'input') {
            input.value = input.value.replace(/\D/g, '').slice(0, 11);

            // Opcional: feedback visual si excede 8 dígitos (para DNI)
            if (input.value.length > 8) {
                input.classList.add('is-warning');
                input.title = 'Para DNI: máximo 8 dígitos. Para RUC: 11 dígitos.';
            } else {
                input.classList.remove('is-warning');
                input.title = '';
            }
        }
    }

    // Función para restringir código de infracción (letras, números, guiones)
    function restringirCodigoInfraccion(event) {
        const input = event.target;
        const key = event.key;

        // En keypress, permitir letras, números y guiones
        if (event.type === 'keypress' && !/[A-Za-z0-9\-]/.test(key) && key.length === 1) {
            event.preventDefault();
            return;
        }

        // En input, formatear: mayúsculas, solo caracteres permitidos, limitar longitud
        if (event.type === 'input') {
            input.value = input.value.toUpperCase().replace(/[^A-Z0-9\-]/g, '').slice(0, 20);

            // Feedback si no sigue el formato esperado
            if (input.value.length > 0 && !/^[A-Z0-9\-]+$/.test(input.value)) {
                input.classList.add('is-invalid');
                input.title = 'Solo letras, números y guiones. Ej: ART-001, INF-123';
            } else {
                input.classList.remove('is-invalid');
                input.title = 'Código de infracción (ej: ART-001, INF-123)';
            }
        }
    }
    
    // Función para restringir licencia (1 letra mayúscula + números, máx 9 chars)
    function restringirLicencia(event) {
        const input = event.target;
        const key = event.key;
        const currentValue = input.value;
        
        // En keypress, validar según posición
        if (event.type === 'keypress' && key.length === 1) {
            if (currentValue.length === 0) {
                // Primera posición: solo A-Z
                if (!/[A-Z]/.test(key)) {
                    event.preventDefault();
                    return;
                }
            } else {
                // Resto: solo números
                if (!/\d/.test(key)) {
                    event.preventDefault();
                    return;
                }
            }
        }
        
        // En input, formatear: primera letra mayúscula, resto números, limitar longitud
        if (event.type === 'input') {
            let valorLimpio = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            
            // Asegurar que solo la primera sea letra, resto números
            if (valorLimpio.length > 0) {
                const primeraLetra = valorLimpio[0].match(/[A-Z]/) ? valorLimpio[0] : '';
                const restoNumeros = valorLimpio.slice(1).replace(/[^0-9]/g, '');
                valorLimpio = primeraLetra + restoNumeros;
            }
            
            input.value = valorLimpio.slice(0, 9);
            
            // Feedback si no sigue el formato
            if (input.value.length > 0 && !/^[A-Z]\d*$/.test(input.value)) {
                input.classList.add('is-invalid');
                input.title = 'Formato: 1 letra mayúscula (A-Z) seguida de números (ej: A12345678)';
            } else {
                input.classList.remove('is-invalid');
                input.title = 'Licencia peruana: 1 letra + hasta 8 números';
            }
        }
    }
    
    function validarYMostrarBotones() {
        const form = document.getElementById('formCrearActa');
        const estadoValidacion = document.getElementById('estadoValidacion');
        
        if (!form) {
            console.warn('❌ Formulario no encontrado');
            return;
        }
        
        const formData = new FormData(form);
        let camposCompletos = 0;
        let camposTotal = camposRequeridos.length;
        
        camposRequeridos.forEach(campo => {
            const elemento = form.querySelector(`[name="${campo}"]`);
            let valido = false;
            
            if (campo === 'subcategoria') {
                // Validación especial para checkboxes de subcategoría - DEBE tener al menos una seleccionada
                const checkboxes = document.querySelectorAll('#subcategoria-container input[type="checkbox"]:checked');
                valido = checkboxes.length > 0;
                if (!valido) {
                    console.log('⚠️ Validación fallida: No hay subcategorías seleccionadas');
                }
            } else {
                const valor = formData.get(campo)?.trim();
                
                // Validación especial para selects
                if (elemento && elemento.tagName === 'SELECT') {
                    valido = valor && valor !== '' && valor !== 'Seleccione...' && valor !== 'seleccione';
                } else {
                    // Validación normal para inputs y textareas
                    valido = valor && valor.length > 0;
                }
            }
            
            if (valido) {
                camposCompletos++;
            }
        });
        
        console.log(`📋 Validación: ${camposCompletos}/${camposTotal} campos completos`);
        
        // Actualizar mensaje de estado con información específica
        if (estadoValidacion) {
            if (camposCompletos === camposTotal) {
                estadoValidacion.innerHTML = '<i class="fas fa-check-circle text-success"></i> Formulario completo - Opciones disponibles';
                estadoValidacion.className = 'text-success';
            } else {
                const faltantes = camposTotal - camposCompletos;
                const camposFaltantes = [];
                
                camposRequeridos.forEach(campo => {
                    let valido = false;
                    
                    if (campo === 'subcategoria') {
                        const checkboxes = document.querySelectorAll('#subcategoria-container input[type="checkbox"]:checked');
                        valido = checkboxes.length > 0;
                        if (!valido) camposFaltantes.push('Seleccionar al menos una subcategoría');
                    } else {
                        const elemento = form.querySelector(`[name="${campo}"]`);
                        const valor = formData.get(campo)?.trim();
                        
                        if (elemento && elemento.tagName === 'SELECT') {
                            valido = valor && valor !== '' && valor !== 'Seleccione...' && valor !== 'seleccione';
                        } else {
                            valido = valor && valor.length > 0;
                        }
                        
                        if (!valido) {
                            const label = elemento ? (elemento.closest('.col-md-6, .col-md-4, .col-md-3, .col-12')?.querySelector('label')?.textContent?.replace('*', '')?.trim() || campo) : campo;
                            camposFaltantes.push(label);
                        }
                    }
                });
                
                if (faltantes === 1) {
                    estadoValidacion.innerHTML = `<i class="fas fa-info-circle text-warning"></i> Falta: ${camposFaltantes[0]}`;
                } else {
                    estadoValidacion.innerHTML = `<i class="fas fa-list-check text-warning"></i> Faltan ${faltantes} campos`;
                }
                estadoValidacion.className = 'text-warning';
            }
        }
        
        // Mostrar botones cuando todos los campos estén completos
        if (camposCompletos === camposTotal) {
            if (botonesAccion) {
                botonesAccion.classList.add('activo');
                console.log('✅ Botones de acción mostrados');
            }
        } else {
            if (botonesAccion) {
                botonesAccion.classList.remove('activo');
                console.log(`⏳ Botones ocultos - Faltan ${camposTotal - camposCompletos} campos`);
            }
        }
    }
    
    // Agregar event listeners a todos los campos del formulario
    const todosLosCampos = document.querySelectorAll('#formCrearActa input, #formCrearActa select, #formCrearActa textarea');
    console.log(`🔍 Configurando validación en ${todosLosCampos.length} campos`);

    todosLosCampos.forEach(campo => {
        campo.addEventListener('input', validarYMostrarBotones);
        campo.addEventListener('change', validarYMostrarBotones);
        campo.addEventListener('keyup', validarYMostrarBotones);
        campo.addEventListener('blur', validarYMostrarBotones);
    });

    // Configurar el listener para actualizar la descripción del código de infracción (ahora input con datalist)
    const codigoInfraccionInput = document.getElementById('codigo_infraccion');
    const descripcionDiv = document.getElementById('descripcionInfraccion');

    console.log('🔍 Configurando listener de código infracción:', !!codigoInfraccionInput, !!descripcionDiv);

    if (codigoInfraccionInput && descripcionDiv) {
        const descripciones = {
            'F.4-a': 'Negarse a entregar información o documentación al ser requerido.',
            'F.4-b': 'Brindar información falsa intencionalmente durante fiscalización.',
            'F.4-c': 'Actos de simulación o suplantación para evadir controles.',
            'F.5-a': 'Contratar transportista no autorizado.',
            'F.5-b': 'Usar vía pública como lugar habitual de carga/descarga.',
            'F.5-c': 'Exigir autorización especial para cargas sobredimensionadas sin verificar.',
            'F.6-a': 'Negarse a entregar documentación como conductor.',
            'F.6-b': 'Proporcionar información falsa como conductor.',
            'F.6-c': 'Maniobras evasivas para evitar fiscalización.',
            'F.6-d': 'Simulación o suplantación como conductor.',
            'I.1-a': 'No portar manifiesto de usuarios en transporte de personas.',
            'I.1-b': 'No portar hoja de ruta.',
            'I.1-c': 'No portar guía de remisión en mercancías.',
            'I.1-d': 'No portar documento de habilitación del vehículo.',
            'I.1-e': 'No portar certificado de Inspección Técnica Vehicular.',
            'I.1-f': 'No portar certificado de seguro CAT.',
            'I.2-a': 'No exhibir modalidad del servicio y razón social en vehículo.',
            'I.2-b': 'No mostrar tarifas y ruta en transporte provincial.'
        };

        const actualizarDescripcion = function() {
            const codigoSeleccionado = this.value.toUpperCase().trim();
            console.log('📝 Código seleccionado:', codigoSeleccionado);
            if (codigoSeleccionado && descripciones[codigoSeleccionado]) {
                descripcionDiv.textContent = descripciones[codigoSeleccionado];
                descripcionDiv.classList.remove('text-muted');
                descripcionDiv.classList.add('text-dark');
                console.log('✅ Descripción actualizada:', descripciones[codigoSeleccionado]);
            } else {
                descripcionDiv.textContent = 'Escribe o selecciona un código válido para ver la descripción';
                descripcionDiv.classList.remove('text-dark');
                descripcionDiv.classList.add('text-muted');
                console.log('⚠️ Código no válido o vacío');
            }
        };

        // Bind to both 'input' (for typing) and 'change' (for selection)
        codigoInfraccionInput.addEventListener('input', actualizarDescripcion);
        codigoInfraccionInput.addEventListener('change', actualizarDescripcion);
        // Also on focus to reset if needed
        codigoInfraccionInput.addEventListener('focus', function() {
            if (!this.value.trim()) {
                actualizarDescripcion.call(this);
            }
            console.log('🔍 Input de código enfocado - Datalist debería aparecer al escribir');
        });

        // Restrict input to uppercase and valid characters
        codigoInfraccionInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9.\-]*/g, '');
        });

        console.log('✅ Event listeners agregados al input de código infracción');
    } else {
        console.error('❌ No se encontraron los elementos del código infracción');
    }
    
    // Listeners específicos para restricciones de campos
    const dniInput = document.getElementById('ruc_dni');
    const licenciaInput = document.getElementById('licencia_conductor');

    if (dniInput) {
        dniInput.addEventListener('keypress', restringirDNI);
        dniInput.addEventListener('input', restringirDNI);
    }

    if (licenciaInput) {
        licenciaInput.addEventListener('keypress', restringirLicencia);
        licenciaInput.addEventListener('input', restringirLicencia);
    }

    // The codigoInfraccionInput restrictions are now handled in the listener above (toUpperCase and regex filter)
    
    // Validación inicial después de un pequeño delay
    setTimeout(() => {
        validarYMostrarBotones();
    }, 500);
    
    // Validación periódica para asegurar funcionamiento
    const validacionInterval = setInterval(() => {
        if (!document.getElementById('formCrearActa')) {
            clearInterval(validacionInterval);
            return;
        }
        validarYMostrarBotones();
    }, 2000);

    // Ensure datalist is compatible and working
    if (codigoInfraccionInput) {
        // Test if datalist works by logging on focus
        codigoInfraccionInput.addEventListener('focus', function() {
            console.log('🔍 Input de código enfocado - Datalist debería aparecer al escribir');
        });
    }
}

// Función para exportar acta actual (antes de guardar)
function exportarActaActual(formato) {
    console.log(`📄 Exportando acta actual en formato: ${formato}`);
    
    const form = document.getElementById('formCrearActa');
    if (!form) {
        mostrarErrorActas('Error: Formulario no encontrado');
        return;
    }
    
    const formData = new FormData(form);
    
    // Crear objeto de acta temporal
    const actaTemp = {
        numero_acta: 'TEMPORAL-' + new Date().getTime(),
        placa: formData.get('placa') || 'N/A',
        nombre_conductor: formData.get('nombre_conductor') || 'N/A',
        ruc_dni: formData.get('ruc_dni') || 'N/A',
        razon_social: formData.get('razon_social') || 'N/A',
        estado: 'borrador',
        fecha_intervencion: formData.get('fecha_intervencion') || new Date().toISOString().split('T')[0],
        hora_intervencion: formData.get('hora_intervencion') || new Date().toTimeString().slice(0,5),
        lugar_intervencion: formData.get('lugar_intervencion') || 'N/A',
        tipo_servicio: formData.get('tipo_servicio') || 'N/A',
        tipo_agente: formData.get('tipo_agente') || 'N/A',
        licencia_conductor: formData.get('licencia_conductor') || 'N/A',
        codigo_infraccion: formData.get('codigo_infraccion') || 'N/A',
        inspector_responsable: formData.get('inspector_responsable') || 'N/A',
        created_at: new Date().toISOString()
    };
    
    try {
        if (formato === 'excel') {
            exportarActaTemporal(actaTemp, 'excel');
        } else if (formato === 'pdf') {
            exportarActaTemporal(actaTemp, 'pdf');
        }
    } catch (error) {
        console.error('Error al exportar acta actual:', error);
        mostrarErrorActas('Error al exportar: ' + error.message);
    }
}

function exportarActaTemporal(acta, formato) {
    if (formato === 'excel') {
        const datosExcel = [{
            'Número Acta': acta.numero_acta,
            'Placa': acta.placa,
            'Conductor': acta.nombre_conductor,
            'RUC/DNI': acta.ruc_dni,
            'Razón Social': acta.razon_social,
            'Estado': acta.estado,
            'Fecha Intervención': acta.fecha_intervencion,
            'Hora Intervención': acta.hora_intervencion,
            'Lugar': acta.lugar_intervencion,
            'Tipo Servicio': acta.tipo_servicio,
            'Tipo Agente': acta.tipo_agente,
            'Licencia': acta.licencia_conductor,
            'Descripción': acta.descripcion_hechos,
            'Inspector': acta.inspector_responsable
        }];
        
        const csv = convertirACSV(datosExcel);
        descargarArchivo(csv, `acta_borrador_${new Date().getTime()}.csv`, 'text/csv');
        mostrarExitoActas('Borrador exportado a Excel exitosamente');
        
    } else if (formato === 'pdf') {
        const contenidoPDF = generarHTMLActaIndividual(acta);
        const ventanaImpresion = window.open('', '_blank');
        
        if (!ventanaImpresion) {
            mostrarErrorActas('No se pudo abrir la ventana de impresión');
            return;
        }
        
        ventanaImpresion.document.write(contenidoPDF);
        ventanaImpresion.document.close();
        
        ventanaImpresion.onload = function() {
            ventanaImpresion.print();
        };
        
        mostrarInfoActas('Abriendo vista previa del borrador...');
    }
}

// Función para configurar el timestamp automático
function configurarTimestampAutomatico() {
    const horaIntervencionInput = document.getElementById('hora_intervencion');
    
    // Configurar hora actual en campo readonly (hora de intervención)
    const ahora = new Date();
    const horaFormateada = ahora.toTimeString().slice(0,5); // HH:MM
    
    if (horaIntervencionInput) {
        horaIntervencionInput.value = horaFormateada;
        console.log('⏰ Hora de intervención configurada:', horaFormateada);
    }
}

// Función para cargar distritos según la provincia seleccionada
function cargarDistritos() {
    const provinciaSelect = document.getElementById('provincia');
    const distritoSelect = document.getElementById('distrito');
    
    if (!provinciaSelect || !distritoSelect) return;
    
    const provincia = provinciaSelect.value;
    
    // Limpiar distritos
    distritoSelect.innerHTML = '<option value="">Seleccione distrito...</option>';
    
    const distritosPorProvincia = {
        'Abancay': ['Abancay', 'Chacoche', 'Circa', 'Curahuasi', 'Huanipaca', 'Lambrama', 'Pichirhua', 'San Pedro de Cachora', 'Tamburco'],
        'Andahuaylas': ['Andahuaylas', 'Andarapa', 'Chiara', 'Huancarama', 'Huancaray', 'Huayana', 'Kishuara', 'Pacobamba', 'Pacucha', 'Pampachiri', 'Pomacocha', 'San Antonio de Cachi', 'San Jerónimo', 'San Miguel de Chaccrampa', 'Santa María de Chicmo', 'Talavera', 'Tumay Huaraca', 'Turpo', 'Kaquiabamba', 'José María Arguedas'],
        'Antabamba': ['Antabamba', 'El Oro', 'Huaquirca', 'Juan Espinoza Medrano', 'Oropesa', 'Pachaconas', 'Sabaino'],
        'Aymaraes': ['Chalhuanca', 'Capaya', 'Caraybamba', 'Chapimarca', 'Colcabamba', 'Cotaruse', 'Ihuayllo', 'Justo Apu Sahuaraura', 'Lucre', 'Pocohuanca', 'San Juan de Chacña', 'Sañayca', 'Soraya', 'Tapairihua', 'Tintay', 'Toraya', 'Yanaca'],
        'Cotabambas': ['Tambobamba', 'Cotabambas', 'Coyllurqui', 'Haquira', 'Mara', 'Challhuahuacho'],
        'Chincheros': ['Chincheros', 'Anco Huallo', 'Cocharcas', 'Huaccana', 'Ocobamba', 'Ongoy', 'Uranmarca', 'Ranracancha', 'Rocchacc', 'El Porvenir', 'Los Chankas'],
        'Grau': ['Chuquibambilla', 'Curpahuasi', 'Gamarra', 'Huayllati', 'Mamara', 'Micaela Bastidas', 'Pataypampa', 'Progreso', 'San Antonio', 'Santa Rosa', 'Turpay', 'Vilcabamba', 'Virundo', 'Curasco']
    };
    
    if (provincia && distritosPorProvincia[provincia]) {
        distritosPorProvincia[provincia].forEach(distrito => {
            const option = document.createElement('option');
            option.value = distrito;
            option.textContent = distrito;
            distritoSelect.appendChild(option);
        });
        distritoSelect.disabled = false;
    } else {
        distritoSelect.disabled = true;
        distritoSelect.innerHTML = '<option value="">Primero seleccione provincia</option>';
    }
}

async function guardarNuevaActa() {
    console.log('💾 Guardando nueva acta...');
    
    const form = document.getElementById('formCrearActa');
    const formData = new FormData(form);
    
    // Validar campos requeridos
    const camposRequeridos = ['ruc_dni', 'placa', 'tipo_agente', 'tipo_servicio', 'apellidos_conductor', 'nombres_conductor', 'lugar_intervencion', 'provincia', 'distrito'];
    const camposFaltantes = [];

    camposRequeridos.forEach(campo => {
        if (!formData.get(campo)?.trim()) {
            camposFaltantes.push(campo);
        }
    });
    
    // Validación especial para subcategorías (checkboxes)
    const checkboxesSeleccionados = document.querySelectorAll('#subcategoria-container input[type="checkbox"]:checked');
    if (checkboxesSeleccionados.length === 0) {
        camposFaltantes.push('subcategorias');
    }
    
    // Validación especial para código base
    const codigoBase = formData.get('codigo_base')?.trim();
    if (!codigoBase) {
        camposFaltantes.push('codigo_base');
    }
    
    if (camposFaltantes.length > 0) {
        mostrarErrorActas('Por favor complete todos los campos obligatorios: código base y al menos una subcategoría');
        return;
    }
    
    // Convertir FormData a objeto JSON
    const actaData = {};
    for (let [key, value] of formData.entries()) {
        actaData[key] = value;
    }
    
    // Agregar campos adicionales para la base de datos
    actaData.placa_vehiculo = actaData.placa; // Usar la misma placa
    actaData.tipo_infraccion = actaData.codigo_infraccion; // Mapear código de infracción
    
    // Combinar apellidos y nombres para el campo nombre_conductor
    if (actaData.apellidos_conductor && actaData.nombres_conductor) {
        actaData.nombre_conductor = `${actaData.apellidos_conductor}, ${actaData.nombres_conductor}`;
    } else if (actaData.apellidos_conductor) {
        actaData.nombre_conductor = actaData.apellidos_conductor;
    } else if (actaData.nombres_conductor) {
        actaData.nombre_conductor = actaData.nombres_conductor;
    }
    
    // Agregar año actual para generación de número de acta
    actaData.anio_acta = new Date().getFullYear();

    // Log para debugging
    console.log('📋 Datos a enviar:', actaData);
    
    try {
    const response = await fetchWithTimeout('/dashboard.php?api=guardar_acta', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(actaData)
    });

        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (err) {
            console.error('Respuesta no es JSON válido:', text);
            throw { status: response.status, text };
        }

        if (data.success) {
            mostrarExitoActas(`Acta ${data.numero_acta || 'nueva'} creada exitosamente`);

            // Cerrar modal usando la función de limpieza
            limpiarTodosLosModales();

            // Recargar la lista de actas
            cargarActasDesdeAPI();
        } else {
            // Handle validation errors
            if (data.errors) {
                let errorMsg = 'Errores de validación:\n';
                for (let field in data.errors) {
                    errorMsg += `- ${field}: ${data.errors[field].join(', ')}\n`;
                }
                mostrarErrorActas(errorMsg);
            } else {
                mostrarErrorActas('Error al crear acta: ' + (data.message || 'Error desconocido'));
            }
        }
    } catch (error) {
        console.error('Error al guardar acta:', error);
        if (error.text) {
            mostrarErrorActas('Respuesta inesperada del servidor. Revise los datos e intente nuevamente.');
        } else {
            mostrarErrorActas('Error de conexión: ' + error.message);
        }
    }
}

// ================================
// FUNCIONES DE ACCIÓN - ACTAS
// ================================

async function verActa(actaId) {
    try {
        const response = await fetchWithTimeout(`${window.location.origin}${window.location.pathname}?api=acta-details&id=${actaId}`, {
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json'
            }
        });

        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (err) {
            throw { status: response.status, text };
        }

        if (!data.acta) {
            mostrarErrorActas('Acta no encontrada');
            return;
        }

        const acta = data.acta;
        const modalHtml = `
            <div class="modal fade" id="verActaModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Ver Acta - ${acta.numero_acta || 'N/A'}</h5>
                            <button type="button" class="btn-close" onclick="cancelarAccion()"></button>
                        </div>
                        <div class="modal-body">
                            <div class="card p-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Número de Acta:</strong> ${acta.numero_acta || 'N/A'}</p>
                                        <p><strong>Placa:</strong> ${acta.placa || acta.placa_vehiculo || 'N/A'}</p>
                                        <p><strong>Conductor:</strong> ${acta.conductor_nombre || acta.nombre_conductor || 'N/A'}</p>
                                        <p><strong>RUC/DNI:</strong> ${acta.ruc_dni || 'N/A'}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Estado:</strong> <span class="badge ${getEstadoBadgeClass(acta.estado)}">${getEstadoDisplayName(acta.estado)}</span></p>
                                        <p><strong>Fecha:</strong> ${acta.fecha_acta ? formatDate(acta.fecha_acta) : formatDate(acta.created_at)}</p>
                                        <p><strong>Monto:</strong> S/ ${acta.monto || '0.00'}</p>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <strong>Descripción de Hechos:</strong>
                                    <div class="bg-light p-3 rounded mt-2">
                                        <pre style="white-space: pre-wrap; margin: 0;">${acta.descripcion || acta.descripcion_hechos || 'Sin descripción'}</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="cancelarAccion()">Cerrar</button>
                            <button type="button" class="btn btn-info" onclick="imprimirActa(${acta.id})">
                                <i class="fas fa-print"></i> Imprimir
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="exportarActaPDF(${acta.id}); limpiarTodosLosModales();">
                                <i class="fas fa-file-pdf"></i> Exportar PDF
                            </button>
                            <button type="button" class="btn btn-primary" onclick="editarActa(${acta.id}); limpiarTodosLosModales();">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modal = new bootstrap.Modal(document.getElementById('verActaModal'));
        modal.show();

        document.getElementById('verActaModal').addEventListener('hidden.bs.modal', function() {
            this.remove();
        });

    } catch (error) {
        if (error.text) {
            mostrarErrorActas('Respuesta inesperada del servidor');
        } else {
            mostrarErrorActas('Error al cargar acta: ' + error.message);
        }
    }
}

async function editarActa(actaId) {
    try {
        const response = await fetchWithTimeout(`${window.location.origin}${window.location.pathname}?api=acta-details&id=${actaId}`, {
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json'
            }
        });

        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (err) {
            throw { status: response.status, text };
        }

        if (!data.acta) {
            mostrarErrorActas('Acta no encontrada');
            return;
        }

        const acta = data.acta;
        const modalHtml = `
            <div class="modal fade" id="editarActaModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar Acta - ${acta.numero_acta || 'N/A'}</h5>
                            <button type="button" class="btn-close" onclick="cancelarAccion()"></button>
                        </div>
                        <div class="modal-body">
                            <form id="formEditarActa">
                                <input type="hidden" id="editActaId" value="${acta.id}">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Número de Acta</label>
                                            <input type="text" class="form-control" id="editNumeroActa" value="${acta.numero_acta || ''}" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Placa</label>
                                            <input type="text" class="form-control" id="editPlaca" value="${acta.placa || acta.placa_vehiculo || ''}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Conductor</label>
                                            <input type="text" class="form-control" id="editConductor" value="${acta.conductor_nombre || acta.nombre_conductor || ''}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">RUC/DNI</label>
                                            <input type="text" class="form-control" id="editRucDni" value="${acta.ruc_dni || ''}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Estado</label>
                                            <select class="form-select" id="editEstado" required>
                                                <option value="pendiente" ${acta.estado === 'pendiente' ? 'selected' : ''}>Pendiente</option>
                                                <option value="pagada" ${acta.estado === 'pagada' ? 'selected' : ''}>Pagada</option>
                                                <option value="anulada" ${acta.estado === 'anulada' ? 'selected' : ''}>Anulada</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Monto</label>
                                            <input type="number" step="0.01" class="form-control" id="editMonto" value="${acta.monto || ''}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Descripción de Hechos</label>
                                    <textarea class="form-control" id="editDescripcion" rows="4" required>${acta.descripcion || acta.descripcion_hechos || ''}</textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="cancelarAccion(true)">Cancelar</button>
                            <button type="button" class="btn btn-primary" onclick="guardarEdicionActa()">Guardar Cambios</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modal = new bootstrap.Modal(document.getElementById('editarActaModal'));
        modal.show();

        document.getElementById('editarActaModal').addEventListener('hidden.bs.modal', function() {
            this.remove();
        });

    } catch (error) {
        if (error.text) {
            mostrarErrorActas('Respuesta inesperada del servidor');
        } else {
            mostrarErrorActas('Error al cargar acta: ' + error.message);
        }
    }
}

async function guardarEdicionActa() {
    const actaId = document.getElementById('editActaId').value;
    const formData = {
        placa: document.getElementById('editPlaca').value,
        conductor_nombre: document.getElementById('editConductor').value,
        ruc_dni: document.getElementById('editRucDni').value,
        estado: document.getElementById('editEstado').value,
        monto: document.getElementById('editMonto').value,
        descripcion: document.getElementById('editDescripcion').value
    };

    try {
        const response = await fetchWithTimeout(`${window.location.origin}${window.location.pathname}?api=update-acta`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({...formData, acta_id: actaId})
        });

        const data = await response.json();

        if (data.success) {
            mostrarExitoActas('Acta actualizada correctamente');
            limpiarTodosLosModales();
            cargarActasDesdeAPI(); // Recargar la lista
        } else {
            mostrarErrorActas('Error al actualizar acta: ' + (data.message || 'Error desconocido'));
        }
    } catch (error) {
        mostrarErrorActas('Error de conexión: ' + error.message);
    }
}

async function imprimirActa(actaId) {
    try {
        const response = await fetchWithTimeout(`${window.location.origin}${window.location.pathname}?api=acta-details&id=${actaId}`, {
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json'
            }
        });

        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (err) {
            throw { status: response.status, text };
        }

        if (!data.acta) {
            mostrarErrorActas('Acta no encontrada');
            return;
        }

        const acta = data.acta;
        const aniActual = new Date().getFullYear();
        
        const printContent = `
            <div style="padding: 15px; font-family: Arial, sans-serif; font-size: 9pt; max-width: 800px; margin: 0 auto;">
                <!-- Encabezado con logos -->
                <table style="width: 100%; margin-bottom: 10px; border-collapse: collapse;">
                    <tr>
                        <td style="width: 15%; text-align: left; vertical-align: top;">
                            <img src=""data:image/png;base64,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" style="width: 60px; height: auto;" />
                        </td>
                        <td style="width: 70%; text-align: center; vertical-align: middle;">
                            <div style="font-size: 7pt; line-height: 1.2;">
                                <strong>PERÚ</strong><br>
                                <strong>GOBIERNO REGIONAL</strong><br>
                                <strong>DE APURÍMAC</strong><br>
                                <strong>DIRECCIÓN REGIONAL DE</strong><br>
                                <strong>TRANSPORTES Y COMUNICACIONES</strong><br>
                                <strong>DIRECCIÓN DE CIRCULACIÓN</strong><br>
                                <strong>TERRESTRE Y SEGURIDAD VIAL</strong>
                            </div>
                        </td>
                        <td style="width: 15%; text-align: right; vertical-align: top;">
                            <img src=""data:image/png;base64,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" style="width: 60px; height: auto;" />
                        </td>
                    </tr>
                </table>

                <!-- Título del acta -->
                <div style="text-align: center; margin: 10px 0;">
                    <h3 style="margin: 5px 0; font-size: 11pt;">ACTA DE CONTROL N° ${acta.numero_acta || '000000'} -${aniActual}</h3>
                    <p style="margin: 3px 0; font-size: 9pt;"><strong>D.S. N° 017-2009-MTC</strong></p>
                    <p style="margin: 3px 0; font-size: 8pt;">Código de infracciones y/o incumplimiento<br>Tipo infractor</p>
                </div>

                <!-- Texto introductorio -->
                <p style="font-size: 7pt; text-align: justify; margin: 10px 0;">
                    Quienes suscriben la presente acta nos identificamos como Inspectores acreditados de la DRTC AP, informamos el objeto y el 
                    contenido de la acción de fiscalización, cumpliendo de acuerdo a lo señalado en la normativa vigente:
                </p>

                <!-- Tabla principal de datos -->
                <table style="width: 100%; border-collapse: collapse; font-size: 8pt; margin-bottom: 10px;">
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px; width: 25%;"><strong>Agente Infractor:</strong></td>
                        <td style="border: 1px solid #000; padding: 3px; width: 25%;">☐ Transportista</td>
                        <td style="border: 1px solid #000; padding: 3px; width: 25%;">☐ Operador de Ruta</td>
                        <td style="border: 1px solid #000; padding: 3px; width: 25%;">☑ Conductor</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Placa:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.placa || acta.placa_vehiculo || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Razón Social/Nombre:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.razon_social || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>RUC /DNI:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.ruc_dni || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Fecha y Hora Inicio:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.fecha_intervencion || ''} ${acta.hora_intervencion || ''}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Fecha y Hora de fin:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Nombre de Conductor:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.nombre_conductor || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>N° Licencia DNI del conductor:</strong></td>
                        <td style="border: 1px solid #000; padding: 3px;">N°: ${acta.licencia || 'N/A'}</td>
                        <td colspan="2" style="border: 1px solid #000; padding: 3px;">Clase y Categoría:</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Dirección:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>N° Km. De la red Vial Nacional Prov. /Dpto.</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.lugar_intervencion || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Origen del viaje (Depto./Prov./Distrito)</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Destino Viaje: (Depto./Prov./Distrito)</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Tipo de Servicio que presta:</strong></td>
                        <td style="border: 1px solid #000; padding: 3px;">☐ Personas</td>
                        <td style="border: 1px solid #000; padding: 3px;">☐ mercancía</td>
                        <td style="border: 1px solid #000; padding: 3px;">☐ mixto</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Inspector:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.inspector_responsable || 'N/A'}</td>
                    </tr>
                </table>

                <!-- Descripción de hechos -->
                <div style="border: 1px solid #000; padding: 5px; margin-bottom: 10px;">
                    <p style="margin: 0; font-size: 8pt;"><strong>Descripción de los hechos:</strong></p>
                    <p style="margin: 5px 0; font-size: 8pt; min-height: 60px;">${acta.descripcion_infraccion || ''}</p>
                </div>

                <!-- Medidas y calificación -->
                <table style="width: 100%; border-collapse: collapse; font-size: 8pt; margin-bottom: 10px;">
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px; width: 50%;"><strong>Medios probatorios:</strong></td>
                        <td style="border: 1px solid #000; padding: 3px; width: 50%;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Calificación de la Infracción:</strong></td>
                        <td style="border: 1px solid #000; padding: 3px;">${acta.codigo_infraccion || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Medida(s) Administrativa(s):</strong></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Sanción:</strong></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Observaciones del intervenido:</strong></td>
                        <td style="border: 1px solid #000; padding: 3px; min-height: 40px;"></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border: 1px solid #000; padding: 3px; min-height: 40px;"><strong>Observaciones del inspector:</strong></td>
                    </tr>
                </table>

                <!-- Texto legal -->
                <p style="font-size: 6pt; text-align: justify; margin: 10px 0;">
                    La medida administrativa impuesta deberá ser cumplida estrictamente, bajo apercibimiento expreso de ser denunciado 
                    penalmente por desobediencia o resistencia a la autoridad, ante su incumplimiento.
                </p>

                <!-- Firmas -->
                <table style="width: 100%; margin-top: 20px; font-size: 8pt;">
                    <tr>
                        <td style="width: 33%; text-align: center; vertical-align: bottom;">
                            <div style="border-top: 1px solid #000; padding-top: 3px; margin: 0 10px;">
                                <p style="margin: 2px 0;"><strong>Firma del Intervenido</strong></p>
                                <p style="margin: 2px 0;">Nom Ap.:</p>
                                <p style="margin: 2px 0;">DNI:</p>
                            </div>
                        </td>
                        <td style="width: 33%; text-align: center; vertical-align: bottom;">
                            <div style="border-top: 1px solid #000; padding-top: 3px; margin: 0 10px;">
                                <p style="margin: 2px 0;"><strong>Firma del Representante PNP</strong></p>
                                <p style="margin: 2px 0;">Nom Ap.:</p>
                                <p style="margin: 2px 0;">CIP:</p>
                            </div>
                        </td>
                        <td style="width: 33%; text-align: center; vertical-align: bottom;">
                            <div style="border-top: 1px solid #000; padding-top: 3px; margin: 0 10px;">
                                <p style="margin: 2px 0;"><strong>Firma del Inspector</strong></p>
                                <p style="margin: 2px 0;">Nombre Ap.:</p>
                                <p style="margin: 2px 0;">DNI:</p>
                            </div>
                        </td>
                    </tr>
                </table>

                <!-- Texto final -->
                <p style="font-size: 6pt; text-align: justify; margin-top: 15px;">
                    De conceder la presentación de algún descargo puede realizarlo en la sede de la DRTC. As. (h) Para lo cual dispone de cinco (5) días 
                    hábiles, a partir de la imposición del presente informe de control o del certificado de presente documento de acuerdo a lo dispuesto en el Reglamento del Procedimiento 
                    Administrativo Sancionador Especial de la Dirección General Caminos y Servicios de Transporte y tránsito terrestre, y sus servicios complementarios, 
                    aprobado mediante Decreto Supremo N° 009-2004 MTC, tal como si de acuerdo a la Ley N° 27867 Ley Orgánica de Gobiernos Regionales y su Reglamento de Organización y Funciones, aprobado mediante
                    Ordenanza Regional N°...
                </p>
            </div>
        `;

        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Imprimir Acta - ${acta.numero_acta}</title>
                    <style>
                        body { margin: 0; padding: 0; }
                        @media print {
                            body { margin: 0; }
                        }
                    </style>
                </head>
                <body>
                    ${printContent}
                </body>
            </html>
        `);
        printWindow.document.close();
        
        setTimeout(() => {
            printWindow.print();
        }, 500);

        mostrarExitoActas('Impresión iniciada');

    } catch (error) {
        if (error.text) {
            mostrarErrorActas('Respuesta inesperada del servidor');
        } else {
            mostrarErrorActas('Error al generar impresión: ' + error.message);
        }
    }
}

async function exportarActaPDF(actaId) {
    console.log('📄 Exportando PDF para acta ID:', actaId);
    try {
        const response = await fetchWithTimeout(`${window.location.origin}${window.location.pathname}?api=acta-details&id=${actaId}`, {
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json'
            }
        });

        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (err) {
            throw { status: response.status, text };
        }

        if (!data.acta) {
            mostrarErrorActas('Acta no encontrada');
            return;
        }

        const acta = data.acta;
        const aniActual = new Date().getFullYear();
        
        // Verificar que jsPDF esté disponible
        if (typeof window.jspdf === 'undefined') {
            mostrarErrorActas('Error: Librería jsPDF no disponible. Recargue la página.');
            return;
        }
        
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        
        // Generar PDF con formato oficial usando jsPDF
        
        const printContent = `
            <div style="padding: 15px; font-family: Arial, sans-serif; font-size: 9pt; max-width: 800px; margin: 0 auto;">
                <!-- Encabezado con logos -->
                <table style="width: 100%; margin-bottom: 10px; border-collapse: collapse;">
                    <tr>
                        <td style="width: 15%; text-align: left; vertical-align: top;">
                            <img src=""data:image/png;base64,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" style="width: 60px; height: auto;" />
                        </td>
                        <td style="width: 70%; text-align: center; vertical-align: middle;">
                            <div style="font-size: 7pt; line-height: 1.2;">
                                <strong>PERÚ</strong><br>
                                <strong>GOBIERNO REGIONAL</strong><br>
                                <strong>DE APURÍMAC</strong><br>
                                <strong>DIRECCIÓN REGIONAL DE</strong><br>
                                <strong>TRANSPORTES Y COMUNICACIONES</strong><br>
                                <strong>DIRECCIÓN DE CIRCULACIÓN</strong><br>
                                <strong>TERRESTRE Y SEGURIDAD VIAL</strong>
                            </div>
                        </td>
                        <td style="width: 15%; text-align: right; vertical-align: top;">
                            <img src=""data:image/png;base64,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" style="width: 60px; height: auto;" />
                        </td>
                    </tr>
                </table>

                <!-- Título del acta -->
                <div style="text-align: center; margin: 10px 0;">
                    <h3 style="margin: 5px 0; font-size: 11pt;">ACTA DE CONTROL N° ${acta.numero_acta || '000000'} -${aniActual}</h3>
                    <p style="margin: 3px 0; font-size: 9pt;"><strong>D.S. N° 017-2009-MTC</strong></p>
                    <p style="margin: 3px 0; font-size: 8pt;">Código de infracciones y/o incumplimiento<br>Tipo infractor</p>
                </div>

                <!-- Texto introductorio -->
                <p style="font-size: 7pt; text-align: justify; margin: 10px 0;">
                    Quienes suscriben la presente acta nos identificamos como Inspectores acreditados de la DRTC AP, informamos el objeto y el 
                    contenido de la acción de fiscalización, cumpliendo de acuerdo a lo señalado en la normativa vigente:
                </p>

                <!-- Tabla principal de datos -->
                <table style="width: 100%; border-collapse: collapse; font-size: 8pt; margin-bottom: 10px;">
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px; width: 25%;"><strong>Agente Infractor:</strong></td>
                        <td style="border: 1px solid #000; padding: 3px; width: 25%;">☐ Transportista</td>
                        <td style="border: 1px solid #000; padding: 3px; width: 25%;">☐ Operador de Ruta</td>
                        <td style="border: 1px solid #000; padding: 3px; width: 25%;">☑ Conductor</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Placa:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.placa || acta.placa_vehiculo || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Razón Social/Nombre:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.razon_social || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>RUC /DNI:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.ruc_dni || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Fecha y Hora Inicio:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.fecha_intervencion || ''} ${acta.hora_intervencion || ''}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Fecha y Hora de fin:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Nombre de Conductor:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.nombre_conductor || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>N° Licencia DNI del conductor:</strong></td>
                        <td style="border: 1px solid #000; padding: 3px;">N°: ${acta.licencia || 'N/A'}</td>
                        <td colspan="2" style="border: 1px solid #000; padding: 3px;">Clase y Categoría:</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Dirección:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>N° Km. De la red Vial Nacional Prov. /Dpto.</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.lugar_intervencion || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Origen del viaje (Depto./Prov./Distrito)</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Destino Viaje: (Depto./Prov./Distrito)</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Tipo de Servicio que presta:</strong></td>
                        <td style="border: 1px solid #000; padding: 3px;">☐ Personas</td>
                        <td style="border: 1px solid #000; padding: 3px;">☐ mercancía</td>
                        <td style="border: 1px solid #000; padding: 3px;">☐ mixto</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Inspector:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.inspector_responsable || 'N/A'}</td>
                    </tr>
                </table>

                <!-- Descripción de hechos -->
                <div style="border: 1px solid #000; padding: 5px; margin-bottom: 10px;">
                    <p style="margin: 0; font-size: 8pt;"><strong>Descripción de los hechos:</strong></p>
                    <p style="margin: 5px 0; font-size: 8pt; min-height: 60px;">${acta.descripcion_infraccion || ''}</p>
                </div>

                <!-- Medidas y calificación -->
                <table style="width: 100%; border-collapse: collapse; font-size: 8pt; margin-bottom: 10px;">
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px; width: 50%;"><strong>Medios probatorios:</strong></td>
                        <td style="border: 1px solid #000; padding: 3px; width: 50%;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Calificación de la Infracción:</strong></td>
                        <td style="border: 1px solid #000; padding: 3px;">${acta.codigo_infraccion || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Medida(s) Administrativa(s):</strong></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Sanción:</strong></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Observaciones del intervenido:</strong></td>
                        <td style="border: 1px solid #000; padding: 3px; min-height: 40px;"></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border: 1px solid #000; padding: 3px; min-height: 40px;"><strong>Observaciones del inspector:</strong></td>
                    </tr>
                </table>

                <!-- Texto legal -->
                <p style="font-size: 6pt; text-align: justify; margin: 10px 0;">
                    La medida administrativa impuesta deberá ser cumplida estrictamente, bajo apercibimiento expreso de ser denunciado 
                    penalmente por desobediencia o resistencia a la autoridad, ante su incumplimiento.
                </p>

                <!-- Firmas -->
                <table style="width: 100%; margin-top: 20px; font-size: 8pt;">
                    <tr>
                        <td style="width: 33%; text-align: center; vertical-align: bottom;">
                            <div style="border-top: 1px solid #000; padding-top: 3px; margin: 0 10px;">
                                <p style="margin: 2px 0;"><strong>Firma del Intervenido</strong></p>
                                <p style="margin: 2px 0;">Nom Ap.:</p>
                                <p style="margin: 2px 0;">DNI:</p>
                            </div>
                        </td>
                        <td style="width: 33%; text-align: center; vertical-align: bottom;">
                            <div style="border-top: 1px solid #000; padding-top: 3px; margin: 0 10px;">
                                <p style="margin: 2px 0;"><strong>Firma del Representante PNP</strong></p>
                                <p style="margin: 2px 0;">Nom Ap.:</p>
                                <p style="margin: 2px 0;">CIP:</p>
                            </div>
                        </td>
                        <td style="width: 33%; text-align: center; vertical-align: bottom;">
                            <div style="border-top: 1px solid #000; padding-top: 3px; margin: 0 10px;">
                                <p style="margin: 2px 0;"><strong>Firma del Inspector</strong></p>
                                <p style="margin: 2px 0;">Nombre Ap.:</p>
                                <p style="margin: 2px 0;">DNI:</p>
                            </div>
                        </td>
                    </tr>
                </table>

                <!-- Texto final -->
                <p style="font-size: 6pt; text-align: justify; margin-top: 15px;">
                    De conceder la presentación de algún descargo puede realizarlo en la sede de la DRTC. As. (h) Para lo cual dispone de cinco (5) días 
                    hábiles, a partir de la imposición del presente informe de control o del certificado de presente documento de acuerdo a lo dispuesto en el Reglamento del Procedimiento 
                    Administrativo Sancionador Especial de la Dirección General Caminos y Servicios de Transporte y tránsito terrestre, y sus servicios complementarios, 
                    aprobado mediante Decreto Supremo N° 009-2004 MTC, tal como si de acuerdo a la Ley N° 27867 Ley Orgánica de Gobiernos Regionales y su Reglamento de Organización y Funciones, aprobado mediante
                    Ordenanza Regional N°...
                </p>
            </div>
        `;

        // Encabezado
        doc.setFontSize(6);
        doc.setFont('helvetica', 'bold');
        doc.text('PERÚ', 105, 10, { align: 'center' });
        doc.text('GOBIERNO REGIONAL', 105, 13, { align: 'center' });
        doc.text('DE APURÍMAC', 105, 16, { align: 'center' });
        doc.text('DIRECCIÓN REGIONAL DE', 105, 19, { align: 'center' });
        doc.text('TRANSPORTES Y COMUNICACIONES', 105, 22, { align: 'center' });
        doc.text('DIRECCIÓN DE CIRCULACIÓN', 105, 25, { align: 'center' });
        doc.text('TERRESTRE Y SEGURIDAD VIAL', 105, 28, { align: 'center' });
        
        // Título
        doc.setFontSize(11);
        doc.text(`ACTA DE CONTROL N° ${acta.numero_acta || '000000'} -${aniActual}`, 105, 36, { align: 'center' });
        doc.setFontSize(9);
        doc.setFont('helvetica', 'normal');
        doc.text('D.S. N° 017-2009-MTC', 105, 41, { align: 'center' });
        doc.setFontSize(7);
        doc.text('Código de infracciones y/o incumplimiento', 105, 45, { align: 'center' });
        doc.text('Tipo infractor', 105, 48, { align: 'center' });
        
        // Texto intro
        doc.setFontSize(6);
        const intro = doc.splitTextToSize('Quienes suscriben la presente acta nos identificamos como Inspectores acreditados de la DRTC AP, informamos el objeto y el contenido de la acción de fiscalización, cumpliendo de acuerdo a lo señalado en la normativa vigente:', 170);
        doc.text(intro, 20, 53);
        
        let y = 60;
        doc.setFontSize(7);
        
        const cell = (x, y, w, h, text, bold = false) => {
            doc.rect(x, y, w, h);
            doc.setFont('helvetica', bold ? 'bold' : 'normal');
            doc.text(text, x + 2, y + 4);
        };
        
        // Tabla
        cell(20, y, 42.5, 6, 'Agente Infractor:', true);
        cell(62.5, y, 42.5, 6, '☐ Transportista');
        cell(105, y, 42.5, 6, '☐ Operador de Ruta');
        cell(147.5, y, 42.5, 6, '☑ Conductor');
        y += 6;
        
        cell(20, y, 42.5, 6, 'Placa:', true);
        cell(62.5, y, 127.5, 6, acta.placa || acta.placa_vehiculo || 'N/A');
        y += 6;
        
        cell(20, y, 42.5, 6, 'Razón Social/Nombre:', true);
        cell(62.5, y, 127.5, 6, acta.razon_social || 'N/A');
        y += 6;
        
        cell(20, y, 42.5, 6, 'RUC /DNI:', true);
        cell(62.5, y, 127.5, 6, acta.ruc_dni || 'N/A');
        y += 6;
        
        cell(20, y, 42.5, 6, 'Fecha y Hora Inicio:', true);
        cell(62.5, y, 127.5, 6, `${acta.fecha_intervencion || ''} ${acta.hora_intervencion || ''}`);
        y += 6;
        
        cell(20, y, 42.5, 6, 'Fecha y Hora de fin:', true);
        cell(62.5, y, 127.5, 6, '');
        y += 6;
        
        cell(20, y, 42.5, 6, 'Nombre de Conductor:', true);
        cell(62.5, y, 127.5, 6, acta.nombre_conductor || 'N/A');
        y += 6;
        
        cell(20, y, 42.5, 6, 'N° Licencia:', true);
        cell(62.5, y, 63.75, 6, `N°: ${acta.licencia_conductor || acta.licencia || 'N/A'}`);
        cell(126.25, y, 63.75, 6, 'Clase y Categoría:');
        y += 6;
        
        cell(20, y, 42.5, 6, 'Dirección:', true);
        cell(62.5, y, 127.5, 6, '');
        y += 6;
        
        cell(20, y, 42.5, 6, 'N° Km. red Vial:', true);
        cell(62.5, y, 127.5, 6, acta.lugar_intervencion || 'N/A');
        y += 6;
        
        cell(20, y, 42.5, 6, 'Origen del viaje:', true);
        cell(62.5, y, 127.5, 6, '');
        y += 6;
        
        cell(20, y, 42.5, 6, 'Destino Viaje:', true);
        cell(62.5, y, 127.5, 6, '');
        y += 6;
        
        cell(20, y, 42.5, 6, 'Tipo de Servicio:', true);
        cell(62.5, y, 42.5, 6, '☐ Personas');
        cell(105, y, 42.5, 6, '☐ mercancía');
        cell(147.5, y, 42.5, 6, '☐ mixto');
        y += 6;
        
        cell(20, y, 42.5, 6, 'Inspector:', true);
        cell(62.5, y, 127.5, 6, acta.inspector_responsable || 'N/A');
        y += 8;
        
        // Descripción
        doc.rect(20, y, 170, 20);
        doc.setFont('helvetica', 'bold');
        doc.text('Descripción de los hechos:', 22, y + 4);
        doc.setFont('helvetica', 'normal');
        const desc = doc.splitTextToSize(acta.descripcion_infraccion || acta.descripcion_hechos || '', 166);
        doc.text(desc, 22, y + 9);
        y += 22;
        
        cell(20, y, 85, 6, 'Medios probatorios:', true);
        cell(105, y, 85, 6, '');
        y += 6;
        
        cell(20, y, 85, 6, 'Calificación Infracción:', true);
        cell(105, y, 85, 6, acta.codigo_infraccion || 'N/A');
        y += 6;
        
        cell(20, y, 85, 6, 'Medida(s) Administrativa(s):', true);
        cell(105, y, 85, 6, '');
        y += 6;
        
        cell(20, y, 85, 6, 'Sanción:', true);
        cell(105, y, 85, 6, '');
        y += 6;
        
        cell(20, y, 85, 10, 'Observaciones intervenido:', true);
        cell(105, y, 85, 10, '');
        y += 10;
        
        doc.rect(20, y, 170, 10);
        doc.setFont('helvetica', 'bold');
        doc.text('Observaciones del inspector:', 22, y + 4);
        y += 12;
        
        // Texto legal
        doc.setFontSize(5);
        doc.setFont('helvetica', 'normal');
        const legal = doc.splitTextToSize('La medida administrativa impuesta deberá ser cumplida estrictamente, bajo apercibimiento expreso de ser denunciado penalmente por desobediencia o resistencia a la autoridad, ante su incumplimiento.', 170);
        doc.text(legal, 20, y);
        y += 8;
        
        // Firmas
        doc.setFontSize(7);
        doc.line(25, y + 10, 60, y + 10);
        doc.line(85, y + 10, 120, y + 10);
        doc.line(145, y + 10, 180, y + 10);
        doc.setFont('helvetica', 'bold');
        doc.text('Firma del Intervenido', 42.5, y + 13, { align: 'center' });
        doc.text('Firma Rep. PNP', 102.5, y + 13, { align: 'center' });
        doc.text('Firma del Inspector', 162.5, y + 13, { align: 'center' });
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(6);
        doc.text('Nom Ap.:', 25, y + 16);
        doc.text('DNI:', 25, y + 19);
        doc.text('Nom Ap.:', 85, y + 16);
        doc.text('CIP:', 85, y + 19);
        doc.text('Nombre Ap.:', 145, y + 16);
        doc.text('DNI:', 145, y + 19);
        y += 22;
        
        // Texto final
        doc.setFontSize(5);
        const textoFinal = doc.splitTextToSize('De conceder la presentación de algún descargo puede realizarlo en la sede de la DRTC. As. (h) Para lo cual dispone de cinco (5) días hábiles, a partir de la imposición del presente informe de control o del certificado de presente documento de acuerdo a lo dispuesto en el Reglamento del Procedimiento Administrativo Sancionador Especial de la Dirección General Caminos y Servicios de Transporte y tránsito terrestre, y sus servicios complementarios, aprobado mediante Decreto Supremo N° 009-2004 MTC, tal como si de acuerdo a la Ley N° 27867 Ley Orgánica de Gobiernos Regionales y su Reglamento de Organización y Funciones, aprobado mediante Ordenanza Regional N°...', 170);
        doc.text(textoFinal, 20, y);
        
        // Descargar PDF
        doc.save(`Acta_${acta.numero_acta || actaId}.pdf`);
        
        mostrarExitoActas('PDF descargado exitosamente');

    } catch (error) {
        if (error.text) {
            mostrarErrorActas('Respuesta inesperada del servidor');
        } else {
            mostrarErrorActas('Error al generar PDF: ' + error.message);
        }
    }
}

async function anularActa(actaId, numeroActa) {
    // Crear modal para solicitar motivo de anulación
    const modal = document.getElementById('generalModal');
    const modalTitle = modal.querySelector('.modal-title');
    const modalBody = modal.querySelector('.modal-body');
    const modalFooter = modal.querySelector('.modal-footer');
    
    modalTitle.innerHTML = `<i class="fas fa-ban text-danger"></i> Anular Acta: ${numeroActa}`;
    modalBody.innerHTML = `
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> 
            <strong>Atención:</strong> Esta acción cambiará el estado del acta a "Anulado" y no podrá ser revertida.
        </div>
        <form id="formAnularActa">
            <div class="mb-3">
                <label for="motivo_anulacion" class="form-label">Motivo de Anulación *</label>
                <textarea class="form-control" id="motivo_anulacion" name="motivo_anulacion" rows="4" 
                          required placeholder="Ingrese el motivo por el cual se anula esta acta..."></textarea>
                <small class="text-muted">Mínimo 10 caracteres</small>
            </div>
        </form>
    `;
    
    modalFooter.innerHTML = `
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" id="btnConfirmarAnulacion">
            <i class="fas fa-ban"></i> Confirmar Anulación
        </button>
    `;
    
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    
    // Listener para confirmar anulación
    document.getElementById('btnConfirmarAnulacion').addEventListener('click', async function() {
        const motivo = document.getElementById('motivo_anulacion').value.trim();
        
        if (!motivo || motivo.length < 10) {
            mostrarErrorActas('El motivo debe tener al menos 10 caracteres');
            return;
        }
        
        try {
            const response = await fetchWithTimeout(`${window.location.origin}${window.location.pathname}?api=anular-acta`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    acta_id: actaId,
                    motivo_anulacion: motivo
                })
            });

            const data = await response.json();

            if (data.success) {
                mostrarExitoActas('Acta anulada correctamente');
                bsModal.hide();
                
                // Actualizar el estado visualmente de inmediato
                actualizarEstadoActaEnTabla(actaId, 'anulada');
                
                // Recargar la lista completa
                cargarActasDesdeAPI();
            } else {
                mostrarErrorActas('Error al anular acta: ' + (data.message || 'Error desconocido'));
            }
        } catch (error) {
            mostrarErrorActas('Error de conexión: ' + error.message);
        }
    });
}

// ================================
// FUNCIONES DE FILTRADO - ACTAS
// ================================

function filtrarActas() {
    const search = document.getElementById('searchActas').value.toLowerCase();
    const estadoFilter = document.getElementById('filterEstado').value;
    const fechaDesde = document.getElementById('filterFechaDesde').value;
    const fechaHasta = document.getElementById('filterFechaHasta').value;

    let actasFiltradas = todasLasActas.filter(acta => {
        const matchSearch = !search || 
            (acta.numero_acta && acta.numero_acta.toLowerCase().includes(search)) ||
            (acta.placa && acta.placa.toLowerCase().includes(search)) ||
            (acta.placa_vehiculo && acta.placa_vehiculo.toLowerCase().includes(search)) ||
            (acta.conductor_nombre && acta.conductor_nombre.toLowerCase().includes(search)) ||
            (acta.nombre_conductor && acta.nombre_conductor.toLowerCase().includes(search)) ||
            (acta.ruc_dni && acta.ruc_dni.includes(search));
        
        const matchEstado = !estadoFilter || acta.estado === estadoFilter;
        
        let matchFecha = true;
        if (fechaDesde || fechaHasta) {
            const actaFecha = new Date(acta.fecha_acta || acta.created_at);
            if (fechaDesde) {
                matchFecha = matchFecha && actaFecha >= new Date(fechaDesde);
            }
            if (fechaHasta) {
                matchFecha = matchFecha && actaFecha <= new Date(fechaHasta + 'T23:59:59');
            }
        }

        return matchSearch && matchEstado && matchFecha;
    });

    mostrarActas(actasFiltradas);
}

function limpiarFiltros() {
    const searchInput = document.getElementById('searchActas');
    const estadoSelect = document.getElementById('filterEstado');
    const fechaDesde = document.getElementById('filterFechaDesde');
    const fechaHasta = document.getElementById('filterFechaHasta');
    
    if (searchInput) searchInput.value = '';
    if (estadoSelect) estadoSelect.value = '';
    if (fechaDesde) fechaDesde.value = '';
    if (fechaHasta) fechaHasta.value = '';
    
    mostrarActas(todasLasActas);
    mostrarInfoActas('Filtros limpiados');
}

function limpiarFiltrosActas() {
    limpiarFiltros();
}

// ================================
// FUNCIONES AUXILIARES - ACTAS
// ================================

// Función para actualizar el estado de un acta en la tabla sin recargar
function actualizarEstadoActaEnTabla(actaId, nuevoEstado) {
    // Buscar la fila del acta en la tabla
    const tabla = document.getElementById('actasTable');
    if (!tabla) return;
    
    const filas = tabla.querySelectorAll('tbody tr');
    filas.forEach(fila => {
        const botones = fila.querySelectorAll('button[onclick*="anularActa(' + actaId + '"]');
        if (botones.length > 0) {
            // Encontramos la fila correcta, actualizar el badge de estado
            const estadoBadge = fila.querySelector('.badge');
            if (estadoBadge && estadoBadge.textContent.toLowerCase().includes('pendiente')) {
                estadoBadge.className = `badge ${getEstadoBadgeClass(nuevoEstado)}`;
                estadoBadge.textContent = getEstadoDisplayName(nuevoEstado);
                
                // Actualizar también en el array global
                const actaIndex = todasLasActas.findIndex(a => a.id == actaId);
                if (actaIndex !== -1) {
                    todasLasActas[actaIndex].estado = nuevoEstado;
                }
                
                console.log(`✅ Estado actualizado visualmente para acta ${actaId}: ${nuevoEstado}`);
            }
        }
    });
}

function getEstadoBadgeClass(estado) {
    const estadoNormalizado = estado ? estado.toLowerCase() : '';
    switch (estadoNormalizado) {
        case 'pendiente': return 'bg-warning';
        case 'aprobado': return 'bg-success';
        case 'anulado': return 'bg-danger';
        // Compatibilidad con estados anteriores
        case 'pagada': return 'bg-success';
        case 'anulada': return 'bg-danger';
        default: return 'bg-secondary';
    }
}

function getEstadoDisplayName(estado) {
    const estadoNormalizado = estado ? estado.toLowerCase() : '';
    switch (estadoNormalizado) {
        case 'pendiente': return 'Pendiente';
        case 'aprobado': return 'Aprobado';
        case 'anulado': return 'Anulado';
        // Compatibilidad con estados anteriores
        case 'pagada': return 'Pagada';
        case 'anulada': return 'Anulada';
        default: return estado || 'N/A';
    }
}

function mostrarExitoActas(mensaje) {
    mostrarNotificacion(mensaje, 'success');
}

function mostrarErrorActas(mensaje) {
    mostrarNotificacion(mensaje, 'error');
}

function mostrarInfoActas(mensaje) {
    mostrarNotificacion(mensaje, 'info');
}

function mostrarNotificacion(mensaje, tipo = 'info') {
    // Crear contenedor de notificaciones si no existe
    let container = document.getElementById('notificaciones-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'notificaciones-container';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
        `;
        document.body.appendChild(container);
    }
    
    // Limpiar notificaciones anteriores para evitar duplicados
    container.innerHTML = '';
    
    // Crear notificación
    const notificacion = document.createElement('div');
    const iconos = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-triangle', 
        warning: 'fas fa-exclamation-circle',
        info: 'fas fa-info-circle'
    };
    
    const colores = {
        success: 'alert-success',
        error: 'alert-danger',
        warning: 'alert-warning', 
        info: 'alert-info'
    };
    
    notificacion.className = `alert ${colores[tipo]} alert-dismissible fade show shadow-sm`;
    notificacion.innerHTML = `
        <i class="${iconos[tipo]} me-2"></i>
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Agregar al contenedor
    container.appendChild(notificacion);
    
    // Auto-remover después de 4 segundos
    setTimeout(() => {
        if (notificacion.parentNode) {
            notificacion.remove();
        }
    }, 4000);
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// ================================
// FUNCIONES DE EXPORTACIÓN E IMPRESIÓN
// ================================

function exportarActas(formato) {
    console.log(`📄 Exportando actas en formato: ${formato}`);

    if (!todasLasActas || todasLasActas.length === 0) {
        mostrarErrorActas('No hay actas para exportar');
        return;
    }

    try {
        if (formato === 'excel') {
            exportarExcel();
        } else if (formato === 'pdf') {
            exportarPDF();
        } else if (formato === 'pdf-detallado') {
            exportarPDFDetallado();
        }
    } catch (error) {
        console.error('Error al exportar:', error);
        mostrarErrorActas('Error al exportar las actas: ' + error.message);
    }
}

function exportarExcel() {
    // Preparar datos para Excel con formato mejorado
    const datosExcel = todasLasActas.map(acta => {
        // Formatear fecha de manera más legible
        const fechaIntervencion = acta.fecha_intervencion ? 
            new Date(acta.fecha_intervencion).toLocaleDateString('es-PE', {
                year: 'numeric',
                month: '2-digit', 
                day: '2-digit'
            }) : '';
        
        // Formatear fecha de registro
        const fechaRegistro = acta.created_at ? 
            new Date(acta.created_at).toLocaleDateString('es-PE', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            }) : '';
        
        return {
            'N° Acta': acta.numero_acta || 'Sin número',
            'Placa': acta.placa || acta.placa_vehiculo || 'Sin placa',
            'Conductor': acta.nombre_conductor || 'Sin especificar',
            'RUC/DNI': acta.ruc_dni || 'Sin documento',
            'Razón Social': acta.razon_social || 'Sin razón social',
            'Licencia': acta.licencia || 'Sin licencia',
            'Tipo Agente': acta.tipo_agente || 'Sin especificar',
            'Tipo Servicio': acta.tipo_servicio || 'Sin especificar',
            'Código Infracción': acta.codigo_infraccion || 'Sin código',
            'Descripción Infracción': (acta.descripcion_infraccion || 'Sin descripción').replace(/[\n\r]/g, ' ').substring(0, 100),
            'Lugar Intervención': acta.lugar_intervencion || 'Sin especificar',
            'Fecha Intervención': fechaIntervencion,
            'Hora Intervención': acta.hora_intervencion || 'Sin hora',
            'Inspector': acta.inspector_responsable || 'Sin asignar',
            'Estado': getEstadoDisplayName(acta.estado),
            'Motivo Anulación': acta.motivo_anulacion || '',
            'Fecha Registro': fechaRegistro
        };
    });
    
    // Crear CSV mejorado con BOM para UTF-8
    const csv = '\uFEFF' + convertirACSVMejorado(datosExcel);
    const fechaExport = new Date().toISOString().slice(0,10);
    descargarArchivo(csv, `Reporte_Actas_${fechaExport}.csv`, 'text/csv;charset=utf-8');
    
    mostrarExitoActas(`✅ Exportadas ${datosExcel.length} actas a Excel con formato mejorado`);
}

function exportarPDF() {
    // Verificar si html2pdf está disponible, si no, cargarlo dinámicamente
    if (typeof html2pdf === 'undefined') {
        console.log('📚 Cargando html2pdf.js...');
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';
        script.onload = () => {
            console.log('✅ html2pdf.js cargado, generando PDF...');
            generarPDFDescarga();
        };
        script.onerror = () => {
            mostrarErrorActas('Error al cargar la librería de PDF. Intente nuevamente.');
        };
        document.head.appendChild(script);
    } else {
        generarPDFDescarga();
    }
}

function generarPDFDescarga() {
    try {
        // Generar el contenido HTML para el PDF
        const contenidoHTML = generarHTMLParaImpresion();

        // Configuración para html2pdf
        const opciones = {
            margin: 0.5,
            filename: `reporte_actas_${new Date().toISOString().slice(0,10)}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: {
                scale: 2,
                useCORS: true,
                letterRendering: true
            },
            jsPDF: {
                unit: 'in',
                format: 'a4',
                orientation: 'landscape'
            }
        };

        // Crear elemento temporal con el contenido
        const elementoTemporal = document.createElement('div');
        elementoTemporal.innerHTML = contenidoHTML;
        elementoTemporal.style.position = 'absolute';
        elementoTemporal.style.left = '-9999px';
        elementoTemporal.style.top = '-9999px';
        document.body.appendChild(elementoTemporal);

        // Generar y descargar el PDF
        html2pdf()
            .set(opciones)
            .from(elementoTemporal)
            .save()
            .then(() => {
                console.log('✅ PDF generado y descargado exitosamente');
                mostrarExitoActas('PDF descargado exitosamente');
                // Limpiar elemento temporal
                document.body.removeChild(elementoTemporal);
            })
            .catch(error => {
                console.error('❌ Error al generar PDF:', error);
                mostrarErrorActas('Error al generar el PDF: ' + error.message);
                document.body.removeChild(elementoTemporal);
            });

    } catch (error) {
        console.error('❌ Error en generarPDFDescarga:', error);
        mostrarErrorActas('Error al preparar el PDF: ' + error.message);
    }
}

function exportarPDFDetallado() {
    // Verificar si html2pdf está disponible, si no, cargarlo dinámicamente
    if (typeof html2pdf === 'undefined') {
        console.log('📚 Cargando html2pdf.js...');
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';
        script.onload = () => {
            console.log('✅ html2pdf.js cargado, generando PDF detallado...');
            generarPDFDetalladoDescarga();
        };
        script.onerror = () => {
            mostrarErrorActas('Error al cargar la librería de PDF. Intente nuevamente.');
        };
        document.head.appendChild(script);
    } else {
        generarPDFDetalladoDescarga();
    }
}

function generarPDFDetalladoDescarga() {
    try {
        // Generar el contenido HTML detallado para el PDF
        const contenidoHTML = generarHTMLParaImpresionDetallada();

        // Configuración para html2pdf con orientación portrait para más detalles
        const opciones = {
            margin: 0.3,
            filename: `reporte_actas_detallado_${new Date().toISOString().slice(0,10)}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: {
                scale: 2,
                useCORS: true,
                letterRendering: true
            },
            jsPDF: {
                unit: 'in',
                format: 'a4',
                orientation: 'portrait'
            }
        };

        // Crear elemento temporal con el contenido
        const elementoTemporal = document.createElement('div');
        elementoTemporal.innerHTML = contenidoHTML;
        elementoTemporal.style.position = 'absolute';
        elementoTemporal.style.left = '-9999px';
        elementoTemporal.style.top = '-9999px';
        document.body.appendChild(elementoTemporal);

        // Generar y descargar el PDF
        html2pdf()
            .set(opciones)
            .from(elementoTemporal)
            .save()
            .then(() => {
                console.log('✅ PDF detallado generado y descargado exitosamente');
                mostrarExitoActas('PDF detallado descargado exitosamente');
                // Limpiar elemento temporal
                document.body.removeChild(elementoTemporal);
            })
            .catch(error => {
                console.error('❌ Error al generar PDF detallado:', error);
                mostrarErrorActas('Error al generar el PDF detallado: ' + error.message);
                document.body.removeChild(elementoTemporal);
            });

    } catch (error) {
        console.error('❌ Error en generarPDFDetalladoDescarga:', error);
        mostrarErrorActas('Error al preparar el PDF detallado: ' + error.message);
    }
}

function imprimirActas() {
    console.log('🖨️ Preparando impresión de actas...');

    if (!todasLasActas || todasLasActas.length === 0) {
        mostrarErrorActas('No hay actas para imprimir');
        return;
    }

    // Abrir ventana de impresión con formato de impresión (no PDF)
    const ventanaImpresion = window.open('', '_blank');

    if (!ventanaImpresion) {
        mostrarErrorActas('No se pudo abrir la ventana de impresión. Verifique que no esté bloqueada por el navegador.');
        return;
    }

    const contenidoImpresion = generarHTMLParaImpresion();

    ventanaImpresion.document.write(contenidoImpresion);
    ventanaImpresion.document.close();

    // Esperar a que se cargue completamente antes de imprimir
    ventanaImpresion.onload = function() {
        ventanaImpresion.print();
    };

    mostrarInfoActas('Abriendo vista previa de impresión...');
}

function convertirACSV(datos) {
    if (!datos || datos.length === 0) return '';
    
    // Obtener cabeceras
    const cabeceras = Object.keys(datos[0]);
    const csvContent = [
        cabeceras.join(','), // Cabeceras
        ...datos.map(fila => cabeceras.map(campo => {
            const valor = fila[campo] || '';
            // Escapar comillas y envolver en comillas si contiene comas
            return valor.toString().includes(',') ? `"${valor.replace(/"/g, '""')}"` : valor;
        }).join(','))
    ].join('\n');
    
    return csvContent;
}

function convertirACSVMejorado(datos) {
    if (!datos || datos.length === 0) return '';
    
    // Obtener cabeceras
    const cabeceras = Object.keys(datos[0]);
    
    // Función para escapar y formatear valores
    const formatearValor = (valor) => {
        if (valor === null || valor === undefined) {
            return '""';
        }
        
        const valorStr = valor.toString().trim();
        
        // Si contiene comas, saltos de línea o comillas, envolver en comillas
        if (valorStr.includes(',') || valorStr.includes('"') || valorStr.includes('\n') || valorStr.includes('\r')) {
            return `"${valorStr.replace(/"/g, '""')}"`;
        }
        
        return valorStr;
    };
    
    // Crear contenido CSV
    const csvContent = [
        // Cabeceras con formato
        cabeceras.map(cabecera => `"${cabecera}"`).join(','),
        // Datos formateados
        ...datos.map(fila => 
            cabeceras.map(campo => formatearValor(fila[campo])).join(',')
        )
    ].join('\n');
    
    return csvContent;
}

function descargarArchivo(contenido, nombreArchivo, tipoMIME) {
    try {
        const blob = new Blob([contenido], { type: tipoMIME });
        const url = window.URL.createObjectURL(blob);
        
        const enlaceDescarga = document.createElement('a');
        enlaceDescarga.href = url;
        enlaceDescarga.download = nombreArchivo;
        enlaceDescarga.style.display = 'none';
        
        document.body.appendChild(enlaceDescarga);
        enlaceDescarga.click();
        document.body.removeChild(enlaceDescarga);
        
        // Limpiar URL después de un breve delay
        setTimeout(() => {
            window.URL.revokeObjectURL(url);
        }, 100);
        
        console.log(`✅ Archivo descargado: ${nombreArchivo}`);
    } catch (error) {
        console.error('❌ Error al descargar archivo:', error);
        mostrarErrorActas('Error al descargar el archivo: ' + error.message);
    }
}

function generarHTMLParaImpresion() {
    const fechaActual = new Date().toLocaleDateString('es-PE', {year: 'numeric', month: '2-digit', day: '2-digit'});
    
    return `
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Reporte de Actas de Fiscalización</title>
        <style>
            * { box-sizing: border-box; }
            body { font-family: Arial, sans-serif; margin: 0; padding: 15px; font-size: 9pt; }
            .header-logos { 
                width: 100%; 
                margin-bottom: 15px; 
                display: table;
                border-bottom: 2px solid #000;
                padding-bottom: 10px;
            }
            .logo-left, .logo-center, .logo-right { 
                display: table-cell; 
                vertical-align: middle;
            }
            .logo-left { width: 15%; text-align: left; }
            .logo-center { width: 70%; text-align: center; }
            .logo-right { width: 15%; text-align: right; }
            .logo-left img, .logo-right img { 
                width: 50px; 
                height: auto; 
            }
            .logo-center div { 
                font-size: 7pt; 
                line-height: 1.3; 
                font-weight: bold; 
            }
            .title { 
                text-align: center; 
                margin: 15px 0; 
            }
            .title h1 { 
                margin: 5px 0; 
                font-size: 14pt; 
                color: #000; 
            }
            .title p { 
                margin: 3px 0; 
                font-size: 9pt; 
                color: #666; 
            }
            table { 
                width: 100%; 
                border-collapse: collapse; 
                margin: 10px 0; 
                font-size: 8pt;
            }
            th, td { 
                border: 1px solid #000; 
                padding: 5px; 
                text-align: left; 
            }
            th { 
                background-color: #e0e0e0; 
                font-weight: bold; 
                text-align: center;
            }
            .numero { 
                font-weight: bold; 
                color: #0066cc; 
            }
            .estado-pendiente { 
                background-color: #fff3cd; 
            }
            .estado-aprobado { 
                background-color: #d1ecf1; 
            }
            .estado-anulado { 
                background-color: #f8d7da; 
            }
            .footer { 
                margin-top: 20px; 
                text-align: center; 
                font-size: 7pt; 
                color: #666; 
                border-top: 1px solid #ccc;
                padding-top: 10px;
            }
            @media print { 
                body { margin: 0; padding: 10px; } 
                @page { margin: 1cm; }
            }
        </style>
    </head>
    <body>
        <!-- Encabezado con logos -->
        <div class="header-logos">
            <div class="logo-left">
                <img src=""data:image/png;base64,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" alt="Escudo Perú" />
            </div>
            <div class="logo-center">
                <div>PERÚ</div>
                <div>GOBIERNO REGIONAL DE APURÍMAC</div>
                <div>DIRECCIÓN REGIONAL DE TRANSPORTES Y COMUNICACIONES</div>
                <div>DIRECCIÓN DE CIRCULACIÓN TERRESTRE Y SEGURIDAD VIAL</div>
            </div>
            <div class="logo-right">
                <img src=""data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAS0AAAEtCAYAAABd4zbuAAAKN2lDQ1BzUkdCIElFQzYxOTY2LTIuMQAAeJydlndUU9kWh8+9N71QkhCKlNBraFICSA29SJEuKjEJEErAkAAiNkRUcERRkaYIMijggKNDkbEiioUBUbHrBBlE1HFwFBuWSWStGd+8ee/Nm98f935rn73P3Wfvfda6AJD8gwXCTFgJgAyhWBTh58WIjYtnYAcBDPAAA2wA4HCzs0IW+EYCmQJ82IxsmRP4F726DiD5+yrTP4zBAP+flLlZIjEAUJiM5/L42VwZF8k4PVecJbdPyZi2NE3OMErOIlmCMlaTc/IsW3z2mWUPOfMyhDwZy3PO4mXw5Nwn4405Er6MkWAZF+cI+LkyviZjg3RJhkDGb+SxGXxONgAoktwu5nNTZGwtY5IoMoIt43kA4EjJX/DSL1jMzxPLD8XOzFouEiSniBkmXFOGjZMTi+HPz03ni8XMMA43jSPiMdiZGVkc4XIAZs/8WRR5bRmyIjvYODk4MG0tbb4o1H9d/JuS93aWXoR/7hlEH/jD9ld+mQ0AsKZltdn6h21pFQBd6wFQu/2HzWAvAIqyvnUOfXEeunxeUsTiLGcrq9zcXEsBn2spL+jv+p8Of0NffM9Svt3v5WF485M4knQxQ143bmZ6pkTEyM7icPkM5p+H+B8H/nUeFhH8JL6IL5RFRMumTCBMlrVbyBOIBZlChkD4n5r4D8P+pNm5lona+BHQllgCpSEaQH4eACgqESAJe2Qr0O99C8ZHA/nNi9GZmJ37z4L+fVe4TP7IFiR/jmNHRDK4ElHO7Jr8WgI0IABFQAPqQBvoAxPABLbAEbgAD+ADAkEoiARxYDHgghSQAUQgFxSAtaAYlIKtYCeoBnWgETSDNnAYdIFj4DQ4By6By2AE3AFSMA6egCnwCsxAEISFyBAVUod0IEPIHLKFWJAb5AMFQxFQHJQIJUNCSAIVQOugUqgcqobqoWboW+godBq6AA1Dt6BRaBL6FXoHIzAJpsFasBFsBbNgTzgIjoQXwcnwMjgfLoK3wJVwA3wQ7oRPw5fgEVgKP4GnEYAQETqiizARFsJGQpF4JAkRIauQEqQCaUDakB6kH7mKSJGnyFsUBkVFMVBMlAvKHxWF4qKWoVahNqOqUQdQnag+1FXUKGoK9RFNRmuizdHO6AB0LDoZnYsuRlegm9Ad6LPoEfQ4+hUGg6FjjDGOGH9MHCYVswKzGbMb0445hRnGjGGmsVisOtYc64oNxXKwYmwxtgp7EHsSewU7jn2DI+J0cLY4X1w8TogrxFXgWnAncFdwE7gZvBLeEO+MD8Xz8MvxZfhGfA9+CD+OnyEoE4wJroRIQiphLaGS0EY4S7hLeEEkEvWITsRwooC4hlhJPEQ8TxwlviVRSGYkNimBJCFtIe0nnSLdIr0gk8lGZA9yPFlM3kJuJp8h3ye/UaAqWCoEKPAUVivUKHQqXFF4pohXNFT0VFysmK9YoXhEcUjxqRJeyUiJrcRRWqVUo3RU6YbStDJV2UY5VDlDebNyi/IF5UcULMWI4kPhUYoo+yhnKGNUhKpPZVO51HXURupZ6jgNQzOmBdBSaaW0b2iDtCkVioqdSrRKnkqNynEVKR2hG9ED6On0Mvph+nX6O1UtVU9Vvuom1TbVK6qv1eaoeajx1UrU2tVG1N6pM9R91NPUt6l3qd/TQGmYaYRr5Grs0Tir8XQObY7LHO6ckjmH59zWhDXNNCM0V2ju0xzQnNbS1vLTytKq0jqj9VSbru2hnaq9Q/uE9qQOVcdNR6CzQ+ekzmOGCsOTkc6oZPQxpnQ1df11Jbr1uoO6M3rGelF6hXrtevf0Cfos/ST9Hfq9+lMGOgYhBgUGrQa3DfGGLMMUw12G/YavjYyNYow2GHUZPTJWMw4wzjduNb5rQjZxN1lm0mByzRRjyjJNM91tetkMNrM3SzGrMRsyh80dzAXmu82HLdAWThZCiwaLG0wS05OZw2xljlrSLYMtCy27LJ9ZGVjFW22z6rf6aG1vnW7daH3HhmITaFNo02Pzq62ZLde2xvbaXPJc37mr53bPfW5nbse322N3055qH2K/wb7X/oODo4PIoc1h0tHAMdGx1vEGi8YKY21mnXdCO3k5rXY65vTW2cFZ7HzY+RcXpkuaS4vLo3nG8/jzGueNueq5clzrXaVuDLdEt71uUnddd457g/sDD30PnkeTx4SnqWeq50HPZ17WXiKvDq/XbGf2SvYpb8Tbz7vEe9CH4hPlU+1z31fPN9m31XfKz95vhd8pf7R/kP82/xsBWgHcgOaAqUDHwJWBfUGkoAVB1UEPgs2CRcE9IXBIYMj2kLvzDecL53eFgtCA0O2h98KMw5aFfR+OCQ8Lrwl/GGETURDRv4C6YMmClgWvIr0iyyLvRJlESaJ6oxWjE6Kbo1/HeMeUx0hjrWJXxl6K04gTxHXHY+Oj45vipxf6LNy5cDzBPqE44foi40V5iy4s1licvvj4EsUlnCVHEtGJMYktie85oZwGzvTSgKW1S6e4bO4u7hOeB28Hb5Lvyi/nTyS5JpUnPUp2Td6ePJninlKR8lTAFlQLnqf6p9alvk4LTduf9ik9Jr09A5eRmHFUSBGmCfsytTPzMoezzLOKs6TLnJftXDYlChI1ZUPZi7K7xTTZz9SAxESyXjKa45ZTk/MmNzr3SJ5ynjBvYLnZ8k3LJ/J9879egVrBXdFboFuwtmB0pefK+lXQqqWrelfrry5aPb7Gb82BtYS1aWt/KLQuLC98uS5mXU+RVtGaorH1futbixWKRcU3NrhsqNuI2ijYOLhp7qaqTR9LeCUXS61LK0rfb+ZuvviVzVeVX33akrRlsMyhbM9WzFbh1uvb3LcdKFcuzy8f2x6yvXMHY0fJjpc7l+y8UGFXUbeLsEuyS1oZXNldZVC1tep9dUr1SI1XTXutZu2m2te7ebuv7PHY01anVVda926vYO/Ner/6zgajhop9mH05+x42Rjf2f836urlJo6m06cN+4X7pgYgDfc2Ozc0tmi1lrXCrpHXyYMLBy994f9Pdxmyrb6e3lx4ChySHHn+b+O31w0GHe4+wjrR9Z/hdbQe1o6QT6lzeOdWV0iXtjusePhp4tLfHpafje8vv9x/TPVZzXOV42QnCiaITn07mn5w+lXXq6enk02O9S3rvnIk9c60vvG/wbNDZ8+d8z53p9+w/ed71/LELzheOXmRd7LrkcKlzwH6g4wf7HzoGHQY7hxyHui87Xe4Znjd84or7ldNXva+euxZw7dLI/JHh61HXb95IuCG9ybv56Fb6ree3c27P3FlzF3235J7SvYr7mvcbfjT9sV3qID0+6j068GDBgztj3LEnP2X/9H686CH5YcWEzkTzI9tHxyZ9Jy8/Xvh4/EnWk5mnxT8r/1z7zOTZd794/DIwFTs1/lz0/NOvm1+ov9j/0u5l73TY9P1XGa9mXpe8UX9z4C3rbf+7mHcTM7nvse8rP5h+6PkY9PHup4xPn34D94Tz+49wZioAAAAJcEhZcwAACxIAAAsSAdLdfvwAACAASURBVHic7F0HfBTH1Z+yu9fUG0JICDBCICFdF7glbnFsx46/xHbsOMUt7h1XwLYiU9zj3ntc4l7iiiu4xUYdIboxoleBunR3u/O9t3fCQjpJp4KRYP8/Dt1tndmd+c97b968JwkhiAEDBgwMF0j7ugAGDBgw0BcYpGXAgIFhBYO0DBgwMKxgkJYBAwaGFQzSMmDAwLCCQVoGDBgYVjBIy4ABA8MKBmkZMGBgWMEgLQMGDAwrGKRlwICBYQWDtAwYMDCsYJCWAQMGhhUM0jJgwMCwgkFaBgwYGFYwSMuAAQPDCgZpGTBgYFjBIC0DBgwMKxikZcCAgWEFg7QMGDAwrGCQlgEDBoYVDNIyYMDAsIJBWgYMGBhWMEjLgAEDwwoGaRkwYGBYwSAtAwYMDCsYpGXAgIFhBYO0DBgwMKxgkJaBXsEYk9yT3MnQWmIZV82axlUmfE0Nfv+2JUuWNOzr8hk4sGCQloFuAWRFvfn5E7xO90mU0cMEIQdRISVwTnyCmDZGKaayAlfBm9t3bf9+9erVrfu6vAYODBikZSAs8vLyFK/TCWTFr4CfBfAxU9xBg/vhzxj4HEw4OTY5MfFZl8v1WFlZ2a69URa4dpzM2CVAo6OIED9pjHxRWlpaoQH2xv0MDG0YpLUfAiWkiRMnRkVLUlxAls2wqam8vHwL9HE1wvMlj8t1AaXsVvgZR3ZTVRfg9gmU0H8qnI/Kzs6euXz58vpBqsZuKFyaDn+QPGX4qJzQlgKXZ1WB1/uVJkQZDQSWEFmuU1W1FVC/bNmyRqirGOxyGBgaMEhrPwNISKlut3sqI+RYKsShMmVjiSClrjzXtbC7NJJreByOIxllc+FrdIS3NQF/XRIbHbsaCO9BIIxAvysQBkA/AUZ1wgK+ohz+KkCXbkaom1HgTVkJAENtB65dJ1mjFoKE+KV3kndB8dLi7YNZDgNDAwZp7Ufw5Hsm2cyW6UBSx0GnTiZ0ty43RZLZr0kEpAWq2CiQmpDgOhKWBtdcCsRQTAU5lDCSFeZUkO/IpW67vRi+fzMI1dmNVl/rY1bF/Aeox6RuDpGgpqmwPxX+egjlp3Eb+QDI+wZQI7cNZlkM7HsYpLWfAG1QZrP5BPh6BglKJR1RB9LK2t6uoRve3W64Bp2yxw5B1mkaOVfUi+Ukyj+aU+V5uIeDdFUbxzAunwfEt3gw7VuLFi1aN8VTUAo3C0daLfBBtdcWKg9+UuD/v8ogmI0bN+4SY5Jg/4JBWsMIQCqoGtFw6ld1dbXqcTq3Ecab4WfsHjsFKWnxtfy3t+vb7fZkIugx0OH3PJ+K+QvLiheGyrDY7XDfyDl9Cn5mdLoEB+HuGJlSDxz3+SDblZLCbAsIoRUKIdbDo7kFfk/ssE+GepycGJf4EXx/fRDLYWAfwyCtIQ6UfnJzc0dYTaZfF7g8btjkn+J2f7KrsfH75cuXt7Ufh0b2KXl5n6hm83Qu6KnQYY/afREqKJJaBPcaCaQzufN2TYiaDvcRIEktZILPo5SeQ9DOtCfS4EKHZGVlfQ3f28ggAMm6wO3JCbOrRm0jbzerLVuirdYjKWUTO+2Phcd3KJz/pjHTuP/AIK0hDOhsitfhPgvkl/OpIGOAiBJgs0YJ/0t8TMxH7pycWeXLlm1ql2h+qKraDOc86c5zf8lk+gYQUG7oUiO9kyenwd91Pd5PY4lwj8zOSh8jxNLxN6p+QJwfE8pPgp8jOl8GPoeDqvogGSTScjqd40gYSUugBKm17Kyvr1ejrVH+MKcioSaOGTNGgb+9qojw7FiBy3WYT9PqKyoqquG5hrumgX0Mg7SGKDIzMy1ep3s2EM9lJDhb1g6uEwuhF8m2qD96XK5HCpzON0sqK5eh2ogf6HsrC5zu++DYhwml0GHpKCqZppIeSAslOo/TMwKubQuzt4staUdd3WdJ8QmrSFfSQsHOA1IYXmdnf+reGaBzHk5IFztdAFjrf0uXLt2Vn58/Eh7P6DCnCnh+TWvWrPFFch+3230hnHGXwpnkdXu+9rq8DwdE4LvKyspthgvF0IFBWkMUiYmJIwijqA4qPRyWwii7iUj0926n+3lvnved4qritbqq6HKVUCYthWPseDnBaMG4cePe68EoDf1etXbV9vQ94wGxq1atqmvfhN+negu+ha+Hhjk+VpIklArXR17j7sEo9ZKuBdsMQucKrGuBqwClwy5qLRGkWVCxNFLVENRqnIHEPmGCh3EM53QqJ9J7oA6/Cyr6Z6Bi7xh4bQwMFAZpDVEIIbbB0F5GhTg4KC11C3iH1MUZyRJmchJIB8/t2LXjzbi4uDUSEYsYoUhaEkgcBQkJCenwfVV3FwLpqLvOHQ/Xw+t8tWcZtS8pZdeHuxSomgm91TES2O32eIuiZJOg2vnzvQlp0jTWAEQclxSfdCHpOimA5LnDr6qfRXov1Sf+zU0Ur9UuPUbBRU6Hh3eUzWorA9Xx3pKKivmG2rhvYZDWEEV5eXlTdnb29Ghz9EtcppcB6ZxOOtmWOiEapIOjQTo4PCUx8R+aEI/Db3OH/V4QVfJBDfwxnKqD0sgUjwedMZG4WKfdcRJhR8O533SUWlr9/h8siglnMru0owAZHP9SWZazBaFpnX0r4HcWl+gHKQlJGvxAn7LOkliLIOLBioqKJZHeq1ltbo4mts7PBp/FCPjvOMKlw70uzw9er/dh0tz8PVlq3V6sFRsE9gvDIK0hjNDsYKnD4bjZLCvx8B0N350JpSOwb6N3+hGgUh3RaZ9FEHYiqDnvw/ewNh6N0p1ckB26Y+qeMBEqpubn56Mxf7fKV1VV1VDg9mwkXe1JGud8UFQpkHJQykoJswufQ0yYBUYqsE4NyGKv+TZseLQvtiiryXoY/ulmN94pCgaPo+EZHU6s1vnERb+Y6vX+pLaS71Etj/Q+BgYGg7SGARYtWrTB4/S8Shk5lIb3V4oIjIgTFEVBH6ywXuKgku4QlK6Be3QmLQC1mzh3gbS1oQMR4N/NpDNpCbJN1dTa/pazHegwa1PMWVDwmD6ctiGgqde3tLR8smTjxqZITwI10wwS6m8J6TIRoUJ9qgUVmynRJwQsIXX9WKCxo4mgDdxEnoPfV/ehjAYGAIO09jJwVi4jI8OcmJho8vv9TFVV/7Jly1r6sj4PSSInJ+f9GJvtfCCPI/tdGEpTJJVix3wx7O5ddCOJE5VwnId09XZPoYyfCkSC/lcdZwU7z+ohk6EjasSE0R2AYJOAsNDA3ln1awTV72EgjLEg+ZxMdOlyN6I5pSNbW1v7pLYlJCQ4oMoHh7lXudAClzS2ta2OMpsnU0qvJZShD5yVBGdy46Asnr7WzUD/YZDWXgR08KgCu/0IwuWjQb2yS0yyMSpqPC7P5y6X6/2ysrINkV4Lg+15Xd6nOSf9Jy2kLYmdmZ2d/XpHx9R2FK8qrivwFHxOBRBBVxURSewUE5M/BCJ+FYkUVM1RwFATOtFbqxDqhxUVFY0DKGeosHQkCU4k7AEgxQ0NTU1zbJzHc7PFCZs6roWMxxnV5ISEjVDOdyKZOdSlrISk33a6DoqePkHJsz+UlRWHtiwYP358RVJcwg2w85r2CRKQwDb3u5IG+gyDtPYSnE7nWJvJcjG06LOIbpOhhAUDUhXA3xNlJh3pdrunlZaWboz0mhrVPuGEI9GN6rC53dM9jK9CWOTabLZ8+Fscbqdf9X9hkuQK+HoM6SptWZnEigqcHpvX610Pqttfwvh1LSIa/2agkR50vzGXBwmry6wgSDblSOJwTFOB290QJnLOCCC8U9zoAEtIc2/3Ail4FNHIH7qs2RR0FW1perX9J9RZRlcPeLf3wTM6GzaNxO2gP37d5woa6DcM0hpkoFe11+FwQ6OeAz/RBmIOc5gF1Jo/yoRvB2nsxqqqqoikErj0LuhJ76FjaYfNb2tq4AnK2N8pZaeSoKrUXfwrRLLM+TFwrZJwRury8vKtU9zue+Fav+7G1QJtTHdxQVtAeuxsX6sHRnlFSGJ5JPXpCahSg1R6DlSlCxlTLRitAqWoqV5v2POhYnkkjOoaFhr5HdQpt8tmqj21sLp6B75Tt9N9Lmfkd0Bc0+EZLZvqLUDpCklrPfP75/WhagYGCIO0BgnYsEFdSvE63CdRTm+HTb35KcmgepxuM5v/B6f+JxI1BqSygMfheItx6c9k96Jo0VzX1PTDypUrv/Q6nU9Rxi+HjUfAJ7Gby1iooL+ePHky2rXCesj/UFr6UYHHM5cReg3pGlMLCTEe/o/fgxtBlYLfL2yt3f7o6tWr++0GgOsMPXZ7/oikETPh52HhjgGqXdR+LEha3RATtalqoCfy1uHOzk6Wo2Onka59AQQ6sdk7fnys2+E+ijNaGNqcCf8tg79lcI8YoYp/7WppWRNh9QwMAgzSGgSAumDzOJ3HU8pPBQnqd0R3StSBksxKEiSQLiRC0VMdCMThcHwAP3sN5YKS0RS7/UfCednPBnmaFR0dPQr2off7ApDcqoAIT4Lt58JvDDFj6nIhSnKt3IT2m26X9bS0tT0I6q0CxHpBBDOW9XDcawFNLRpoGBggkXgmSRjP6w/dHUOJqrtsZGVlSQItX2GPEU2c8x7dHVAFLXB5zoGDR4XZDcIrv0PExx9Fib4yAd09lpK2Nt05t8Xnm6soykv1TfWl4eyDBvYeDNIaIDweT6aJy4XQ8I8jQU/q3X5UQpB5KtFuYoRlwcZbwgSxwx6HfkgY0jii+FO7Wls3xCsmtEf9igTtWLnw3yTogLj2UICqWQvfX3DmOL+TTez3cIfLg2sV98BIjdFcOO6r7mxPeB273X43dMxKTiiGO3aELZAQVSolD2j19e9C7x1wwL1WSdJsQRGuW380yqTzoOxfjxkzpltJShN0R1NzY4/SKww2k4BsT6Xd94MMIKx/tP+A91lRunixHvGisrJyNfxZ3WNlDOwVGKQ1AEx1OsdKXHpM99cJZwinwgqNfmlJaUm52+3eCerWi12kFkEUWVV7chjdAziqT/F4vqGU/YUEDfIx0L9PB9X0E/iu28ZCquZK6Nj3AfG8oHB5Btz3/0ISBb5zjHvlmjhxInrYd5sCDDrmTrjGG3DcRzaz7WBGyZFAguNCu9fCnb7asHnz5xs3bmwdrNAv1dXVO70Ox52U85iQCwKqwXs8Wyj7EaDepi1evHh9cmJSS7jrQH1be5K0UDqG53IO7UrGWI8NcGIz7MO6BtVPnEnU1Ef7O8GAUh2QrMnn8zGbzaaCOu8zFmH3DwZpDQAakycB24TzI9IBhHUY1cgJ0DjfgEb7SYHb/S5sPa/jMdBqVzUGAnXhzu8ObYFAuUlWNtLQLCJ04uMtsoy+QvP3KF8wkcVWUBmvN5lMb3HB/hoir2Q4x6yqaq82nxAZIbF9EvrsVYQ6ckV2dvaZcdHRv6eEYXSK8STowBqLuiE8tU/a2tq24rFT3d6lhNEu1ngkI4ui4GDybrj7SJKEjrp/Il2N9Vs1NXCeCPDV3ERew3WdoSt+WVJR8X1f6wODSWK0xeLwuFzjQaxOJ4Kacd0k/F47xeUq27Zr1zIjsmrfYJDWACCYWC0IXUKDamE44sK46Vc4HI7voYOtL3AVvMg4QVtTR7Kw2kwmF0piQohVkYQpXrRo0Uavy7Uc9KR2J9BoxqU7oAM/AjLbB8XFeyZ0AFUPbUBfuVyuRQrhH4Kc9Suiiq9iVsaElVKGAkJZfV4cN27cG4m2xBQq01SNadFMZWqLv3VZux0J6vs2PPi/kc4zppSkMsJvhPPndSaF9PR066jUtOvhmC7hbAQR7wM5fYkSVYG3YHNIBN4kiHZfpNmMEEC6ptio2OOiLLbzMAIFC9rETFjKYEFZM+FseXJi4jxoHw9XVFQMSkSMAwEGaQ0ApaWlKwomTTrDT2mCZLGcB5LVxYR0WnJCidsEagg04jtjbbFdroGGe8qlIzEuDHw2THF579u8Y+urNTU13RKKPtXvKUAjuiA/d1YvSBwPwY8ckKxmhXOjQEIEie+/GRkZ89atW+cf7Kw5ewMhwlkb+nTFJvKJSNXuAYnsLHgcsbvdNFCdI3TVmjVrdHJD9cw9zh2jxWqjgLAuRRUzzNUaiao+jM/Fm5s7kltsY+HpNoDs97DYTL8Kc3wX4H1gcJgYFxNzI7yLEwnOtIZ3QUGPeie0mRyzpBznzsv7S3l19TIjwmrvMEhrAAg1MFwYvMNut99hlk1m6AwY2qSjb5YV1IK/x0XFtFAm/khIl8kunN0zhTYmUUYeT0lKSQTiebw7/y2QypJlylFt6Xit4IJeQSaZA2a8f9hzQ2UeshJWX1G8vrgZBoSboi3R70kyOxbYagKa6gUlK1t9bfe1243g/VhByr2fU34m6dZ/S7zf0Nr6I35jsoyLsVeA5PVGm9/3SOX6yl6dVEOENUUSDO2cXTz5uwFKXw7JbHnO6XSeD78rIzzvgIVBWoMENFpPdTofEZJ8CEWppyMoQXsG5hHs3dkRJAVQd26wms31QFzPh1S7PcCFyCWMTCBdR3AgUG1evdbQrXF9f0RIVURJ6CtcZN3U1MRQwupo6JYAoWw+3b0DjA7xxbJly3RyKl60aJV7svuaAA9shnerr6MEQhqDcc4wbFC4C3gcHi+j5F89EBZwoFYH7xjNClE0uGyoPXG3Q6L8Uu8k7wwjX2PPMEirG4RScuHz8aNTZyQzPQsrK1d6nJ45IC29Qbo+2/bOgnYRP6gvrNvgfhQkLkKnm2XzNmda2qfN0dGBjrNNoNOtUYjYAAem/3xNgSP0PQ3NzR8fyH5D4UgeAe+wESTU6UyQ6yiuVKC6etaR9BlQijM3NzcWVx6E7Fe61IWhr1OTk89VmHSpINoTsP+BzmocziQzCTNhd50UCMEP7+iNQCDwADDoJlUNmBRJOocK3SUFl0LJGDONWUnUVK/3+YWlpZ8aqmJ4GKQVBhgt02oy/xOI4yQYGj92290vjxs3rqS3WR4kFS/zfihc5LluMtXgWLvAL9SbJcZQUkK7RziJCX+P5Zy+wEeNqpEJrQLVAVNkoaMq2qbWuHPcp3OrnlgCbhwoL6moKBsONqp9hRAJfYG+aa78/F9Lsnw1CTqNYqwutLfDK6MX20wWN0hUD3o8nkWSqjYIxkamJo84GwgFXUxMRKM46bLH+8IF18mJiRdgeyFh3jlmvwYJ685ttbUPdmxDMDDeZTObcVH430JliKGM4mqHX3kdDnSuDbs+9ECHQVqdAI1a8rpcx0IDPg1+joSGeBGTyFEp8Un3wb7He5O4MJIlNPjHpOCIG05N8IOKUVNcWvo9dI7NEmW3hY4LZ6zFcL+50JozmGAYvG9l+47SJfpC68f7X9MDEyFi/xwGpjKF82Mol06C54vhbYITKJQUSIQ9CVSzUnB5K5DVGNg6lgRJZQOhYkHnWcTEmMQsaCdnk/CDVBNc43EQk5/sPOihA+8Ut7cc9p/aaeE5UxkLt2bVADFIqwtAoplAKUMP8NTQJrScZ0OjurXA7sZp6fd7u0ZbW1sVNZtf4ER3xNxz7R4lU2XOMRP0s3DcF0xRZjLGH4bfY7q7nsBIo1QzfHkGEe2Os/C+58PPNynlF4dmFHFiBAiD5nWeMhFC+8/mbdsWdL4Wk3UpLLXzdh1UfNvi8z0I9wvvysII+uihSrubtOB9t4Aa+VN/6nUgwCCtEHDxLTTgyRLjuJC4Y2YXtA9JenwpiV4HIv383qIyoF0FpKinQWg7BBo+ivkdm38s1egMuFd5aWlpCdz3I7fbfQYX5D9Aj2NIV4mrlQryKrTiLwahmgY6ICQ1b0M3EPj7EbyTgyXKr4E3cDANrhVtfxdtQFgf7Coru6lG0/awF06aNCkp1hZ9Slg5WZBdPk2dBoS1pbsy0GDcf6nTtk8Nv63uYZAWCRKW2+4+mDM9OkMwkzGI9dB6PhNElBJBY4B8sjDsblNTU0R2I/SJKnC57kAfLBL01elwQzJGJuyfoKKcBx1nC9x/odfpvAC0ixvhPpiSq1012AT3f9mnBu5EyWDwajz4qJ9hj5ds8hSVqJIWoIvjiypqkBR2TR8fy6Jjs3AxIFNbfooqXDrkZsZC5IXSzoK0tLSSUaNG/R/oeefB+4Z3Qf3QDt7ya9otyzsRFiLKEjUlGPEiDKh4kVdUrOjx5oKa4fyOauUGv6Y+OoDq7PcwSIvoU9VuxsXd+JUEbRfoOvCQT9WexOiiaOdyT5qUrJpMLX1ZclFSUVHicXmeZ5Rc1XUvPdYsm56c6in41uFwvFpcXv4ljPQ1XPCjGRN23Xirad81tLR8gwHvBq2y/cCqK7NNI1KsvwFST2ry+d9ILuwqaTKrcgo8uRmcSAqTxA+bCu3oaLtViYr7o6D0MsqJotGo12qLsu9PKFxej2TGbbEnM0aPAAlzq/CJD1v9LSuTZldv2gdV3I2NGzc2wft+JT8/v8QsKX8XRNvlV7WXQfIJG6yRczKRhMuSJDAOv3inlPSSlkgTGwin+DyteI6gYi7cP+IMQgciDmjSQjICNc4uMfYSkMhBJKgONIIqMHvztm2Pr1u3rhX2jwIpaJLa2roSGm63Yn444JS1x+N5ghGG0lZno7wcCmNzrML54XAoek/jIuefQF1UMNX7L7GotqiIsdNIrvR6UXWgsJsp9pQE8yjOpKegE0ZZZFNu2UVshuuxPXP/CdqyAPou2gJHUUZPjlJ4/bbL8i63pMorGOG4SDyaUrGztpa0SSCVAZkVEqaHzwGpUjRRhZ5vUSxrQWI7KmZu5c6GW9xZxKRli0BgGVdYg9pAfDtNDS1ji/b0v9obCBnal48bN26WzWbTunOjCFV8RJeIpwhKSkSbT5eyPHZPPlA5htvJBGL6xKcF3gGJfRW6pqzfuun99JEjcRIgX1XJO/XN9d/CdiMtWQ84YEkL/bBAJTySUYoSVjthIbbs2LXrWVxGg2ojENqJlLJHJIttsdflugEa8vy+SFttbW0/cpPpNUropDB+WSjVmTRCd8djD81u7XXXhS3XO23meJo1TXE4qGDp02Y71zUWeT+IKuzq2EglietLZAg1g9R4YVaG80vY/GHHY6JnLl7ZdJt7PiUMJx841Pd0y0hldVtDy32WWBuGcDG1kca3x9+/vK1ulvOQEGFFw3UXC5U+CZLYX+GcCahiwvaPqZnCu5EeIgpvAoZaKUXTtUlawuKdhXHvvHE6qzr11cjXAfYXkbxnkIywrl0tWkJbvq2xcVtWVla0pNBLYMtfib6GnvxK4dI/5JiYh3Nzc59dv349rqh4avBLv//igCQtfR0aEBbn9E4StGHtbnTQQWITY2N/Dce8R/SwTLSaYFZmSvKhQz6UnJAwJzMz85We1gZ2BI7SBe6Casr05T4jwxyyVqjinsGoVyRoKHJOoDI/NTpOmigEmUwxxhfVZ8uahaz9C1TB2UgsHc8RASqIsjv7tA0e30U7inIqEguX7KEyAfnt6mCQtlDGLjZFW7YL9D8ipE71y3o0C84YqlT6rKoQ1N+q+j5RKFsN0txdmtB0VVjzt73BFNNtcNUEuPMIoHcrSnCyQo88ImvSGQRXHQ4BUCF2Amd1TliL7WYnkl5OTo6MUR1ocCBql8jGAHsVRVls6Q6H427D6N43HJCkBQ0lBwjrMUL04Hh7jJIUwyQzXgSk1lBaWbqgtbV1odVkegqkrZth9zj4Ozc1JUUAqb3Y7syJHtOYMw82bmloaCgL45GO3tedI4gGoMcuApWgEFSCT/dGPZGc116dYY62JaRu4IENOYVVPipLF4K0dAHstlEi6kDKe5gFQ7RkACmflJQc8xp8r9rjmch6DLBmoJht6IWPHuWKZP0tqJbPFxZ2VCmFht2YBGNSgQRCUoG4ZqNaCdsXbFtCfChSUibqQseAoEvyLLLyCih8j7f5AqftqG1eiXFoOJGgbVKd2DSivQmfeRKR3oZTDjNbzYfA5jdxn25vS7BmQr1yNTXQvFOjPxQvqWj4JSQxvWxCLIPniQNYR78q4DIaBc8fSarRm5f3MFFMaC/9VYdjrLhO1STLitvtLiwtLY0ogKI3zzuaKeQSSqkKauYjQHib+xJ9Yn/AAUlaMuWomozpZjeqbA4u0YeAuC4DQvnOKpu3wdbWUPr1VBgl73c7HChl6PGlUmJT0jgjr+JMU1xUzGxorHd38k7Hxtv+rNFesRZEuvfa1MBtmEhisOu3oIhJTuIaUz/biY6pfwOWOGa04HfXFmU/IEuWJwSRJkCj/x2UN0B84r9CJgnw+2yon4UzrXNMeMIEQc/xClWo94M0dB/a/6C+Z18l2efD9p/9iYK2nVog449B0jiKBiXLBD34sRDq5Ner/dqrSG2sHCTPEhJco4nuJHbo+FA2dgVIeYvxUjAAoNuBHHxe9BvQwuqACqgeBZkKvYy1N7tGpaXGYJLUS+FjZsBzSUJbcpzdOWNJUd5HSNKD/Ww7o8Xn+8ZmtqDU13EGEYRCMsXlco0vKytbrhI5XhLEFsYtwoyRUaWgNP9wb/fCPAQel+fPQHY34G+FSX/yOJ2zsrOz3wmF8jkgcECSlibUeTCSnwQiQRvIBdGhuEqdm1QOENe9sVGx84Cw0Bm0Y8TRGM6lW6BRrsAlNQEeaOBURm/1XEzm6bbbf4DvX7Yf7FN9X5iIfDt01Ilwzx+FSr+qa6r732CvEQRSijEx2xS37DwYRJijoEoYlVOPhwPlusSk2H6421fx+TTZ9RrXMwUJC4zaB4NUEAobJdY1NZGVXViLUpcgIp5QPkEPOhx8VFM4oZhm7Mn2w1C6gA61RdXIw4zrriJzg6qnDn395qYirzVaov8nVPV+yjkSDkarwPtL0Cdx6ZM+3U+5HvwP0QplT6VU/BZtavg7oJLqhpm5ieYoy3XwG6RG0Qqlmg/ltIMknAOluymNc7Sj7SEx7g2A+r95qqfgDajnLZ12FQAb3+RxuUoZZcfBw8nrECUIogAAIABJREFUJkkSqI/sN1D3RyKYYKC0o4mBkvGM8jvioqPTQdq/N1KTxXDHAUlapRUVX7rz3acISQRAArBB45pFsWF1hZ0FU0u1PycUw5FosPM4QWI7FRrbPWPGjKlPSkj4EBonHpvAuHy1d9KkquKlQZ8ktFlAo7ovJiYGp8Ybe5yNGgCAlKaAinc/DXri6+qoQNuP0J6G+l0jBMvLXUK+EHaBSTBqoNVPhgOuokEJCcQhmhttpZc3FnkfaDfIo4rZONvtxvj2QFKYkabdc9sEHeYKkGieb5doaCjBq2Caqjaoz/FoeWIoxjrGwtdnxKxSYARj0j9A57u4VWv7u5kpc2B3exILPYPRG6czfoLd3Z61GVRrMROuExf8Kf5LmtVVxGbG9GxnBusp3vW1+K83WeTrQcZBqcuucHkKXGcJqolFUIlps51T/UTUJMyIPEFupAgQ7Rku2P9BHfM7bDZDrU9ngp6s1yF8/g29Qoxo1ZHMiEI7k0HanNqJ/EDyZ9elJiejtPds/2sxfHBAkhY0EOxAu0dhkJj+DMP8dSDSo29RZ0fB9mckNE09d/vOna8lx8f/DpSUQmhAI7KyshSQmFq9Lm8x4bqxFaR9chi1Rp8MfeXZ9pX6oVFwL4+EbAQ06PFQ1FXwF2cqD8KY9KDWvUhb/S/Xq+pW7MQ7bsxZLsXacDo+j+iqFi0NZYoGiYbMFLJohbLfpjuHFuYeFIwtD8do4gPoYc3AblfAvcbA9pzRsnLjpqJJj0RxJYtz+Vd4X07ZoTUmf0Wq0G41U4z1Lv4IhKj7drW1aM2yjaQBuVxjotLd0FW/Z8FVA6hC6oH2jstxZoekXxDwyOcgRa2Eco5Gu5hoFP8OmFpVM4k+jQRnXdvgvLfiZy1a1zjHsxiuhZ1fhv/zHKlZ+O7UabPys6BMr4A4JzXOdT0CIt+7jTt3rkq/Z3AkE5C210IbulCCAYMEJcf2NsPDJLPF8m0VmngW6vQT/AhAed7q7R64iD85IamQBjMs7QlK4oC4bi9wuX5aWFY2f4DVGfI4IEmrM9B7HTPPmCWlgTJ6I9mdU3BPMMFUjNO0evXqNz35niWc+ttQxdMTtLpcUR0C/MXjOjZ3djbGJ//FPMCpRndBIXcBEbyKM1acElyMLXEqnWa9tWR2e16zxNuXNDTf5qmAM06Ej08Q9XFQUcYxkDiJHiKanls/y/X2tqK8dWbJhISCBLhIBNQnVaJFSYp8ZfsjAenybBsz/wCSHJIf2vmsIEUlJzZJcsKdZRvqilx3yAoD0hN6mJdH5apt1xLX5/B8fkMJPxj+4rWhuGIZ0PuTqD7GyAzVTiSkFkG0D6Jmlj7QsZ5Nt9lH/By7nagBf3AhOQ2qj8F3QAUUwK9/9wfgWSgEDWIjoZxFCmdnxickv9ZQ5Hw5urC8Z4/1CIDkjqsagLiuhBELCJ3+HwnrcCp80ES+g3o+sWX71nciVeegbY6wyCZcXnRuD4clMs4vyM3Nraqurt7Rz6oMCxikFQIuk8nLy3vIqqBUT/8JDaTzbB/0MXqt0+lcH0q9tbR9h9vtjoYO8XvSYZU/dMbJmtWKYU9+uWUrLc2lxGa5P6CJ9zmSB6eY7gokJfpbIKCnkgurNrcfCqN7scQ4dhogaOYAaeVJ6FTnAQ2NIThLysgd8CxeDCV/QDMXkARPhRM22zT6Fkg0skbEFlAEi0mLtrApQHyWGFEDpC/7AmTNyLsrm7U7gb2LKiob5rguA9G2DqdQcbYRynKVhctHMc4OB4JNgWst1XzkrZWbykqy0l0etL8Fy0Xa4P5WnFj4deHPExttgktmPbu1zkmaCLTpS5ygzB1TpW3csSlRxQ1mif+WBNO0qSC1fYyRG6CcNzCZp20pct40onDgkyEhifp7r9dbw4l4Xwj6G0qFE0qVAvesJ6gCUvoRSOsLysvLf4o0jFB2dnZMXFQMtsczSdfEuR3BBaF5NpMNfQ4N0hqucDgc6Qrn+dAZt7f4/RW92ZJwITSQ0oNwjgriNkpcnbNE50NHf8jjdN4AxDUPp5qB6KIsJtM06DEnho5B8X8jdMY3WR0bFPsJTuuPTLEeqgneUqaWFXfswB0RNXfp5sWFuXe9TqoDV7flxYpo5dtgGiyRbZXNR0OZX263nVCiAaFxfB7AGfQ8ihMEmpgBHb+I4HQ8ERubfG1fmTlbR4mktLa2rqiWl247soioKy7Pmo5SzI5Na1TPE0EDeygw/u74T1ooH3Pofos6lhPIc9Ubp7OfDs1xvyiRVtZMmgLtnu6NRZNWatT2LBDLaUBeCfBckxzEgULi7igJJqpC/TkSVQq6d3KzbAaJLk5WuFuvGhHbkJRdj5X56+Y4x8pUQr8udGStgsLcSTAQINUnTX5j4RQnEgZtBre4uHgTLgPKyMh4OyUlBZ5bC5dlWYNBwQ8DY2uk7gkovbtz3enxMbH/IsEBsbuIq+gAi5FUMTLrWtWv1Q5SVYYs9lvScue77WaTch98/TV8akFWxxnAhb2dh6F0gYgetZgsmEkHl6V0VBVhEzpk8scK3O4bvQ7vMqvZ/Cfo9JiKXn+W0CmqidBmlJSVfTQYQfk2zsxNHDki5i/Qya5m0PGc1IWe1R+HO7Z94W+IL3Y2z/F+QxjGiqJJQEIn1Rbmfg19YV3wOI4jMqpm0OjFFri+pUQrf9ql5ZcxJpnunlm+OLSsR5fOkDXwQYbISJ/1RClGe6x/9Qr5Ue2Ou95OcqEF1XeUXeT91+ik5qTWlsZdnW1PzT6tyaqQr0MBFGVQf8/gsh4ZdBK6nUDlPmgJiIpVFzF5QobzOAwHRIL2sR9B6jkIXqyT6BvILh4qQ31RXgJlyhTBiL/88/L5v/6i/++uQxz+ftnMcCUGqJpeUO9xADmKdN9Pl0FbexUIsUwQHqcStbK0qnRVP4s9bLBfkhaoazmyiaM9B535UIdIhJeKfaxX0kKgxAVi/mMY3YEEiatzeqoM+O9e6Cg4Y4Nxvq3BHaIMWuv04pKSAecHxFm72lud+bFR1mkgKR4DPWwZ3LdJYvT3ISmv19kmn9AWKoShFIH5An8ry6bYnbPsqHr9pAbYYiqpN4Cc4iMaWdGobq4MSXDL8dzC6QOtwcDgeqwYJ0vCer0/QqqbryXul+F5YxQG9Oi/hgSdVXGAWRBoVR8YOaty27ZC50h4VkjyyLmgPdFDgAgKSNBtoBE6+ztN9WKNCoTFZNN0kLz+gklePUc77wUSeymmsGqfSC0eh+MoStkcEjTqh82pKYT2fUCIG1l5+XfFmnZArVXc70jLO8mbJNkY+v+gMXd35mbggBOys7P/G6lvFIj5dSBx3WM1WdJACsGG3/lZpYQ+CDQkL4RGdF1ZWdl3A63DtqK8qIZZrrMoo5fBdTMEFff5W5qfZlwe6ZelmkgXDEsqSB+chDzWqY1SkUdFMJt1XGHx2qIi9nRREc6KaqInY8lQA9rFQBL7ZkK6dj7j7AqQng4DwmkAHex5f4vv7gfmLN6AKwFMEjkLDkeSwvfzOkpZcByqkBsCQr16R0P9J2PvXNXUPNt9ChAaZsLBwWkEZWwOV0y/qZ/rvnXrlsaqCQ/+MtmgMVdivC3mZJB0cXlZON9BRBuIjP+F13hreWVJRK4S+xv2O9KiVu0EUN8wkuQeNgAgnjPio2M3eXNzHyxdunRzJC8bw+F6PJ5/csEwntZJna8ZAqod36gBcmlJRcmgODNaiOQARXQmCc6gQV8kFpVbLbLC7qVCu/ON09k7kSxTYZK+eBkjU2yCayyEz3NxNy9erc0M7sfOX1g4GCX+5RGSxL4NffbA1ao9nkXJMxhF0tfb+Fq/qt4uc34TfHfDi7e0+MWCsbet0tdCCsrWUyHQ/WIJDG4YwhoTi5wkUXLYyNSYJ3YWOlAJXrM36wMD5Oi4qJiL4b1jCrrw8bn0tHDikcbm5jv39xnCnrBfkZYe393twYbadbpZd/YjlzGrdVR+fj6m81oeyTVLSkpqvE7nnUySJ9A9I5oicAT/GmSVa4orSgfN+1oldYslEX8NoQxGf/or6Hx/VGSB5BPDKLvuuBzn0iWn561Kz2HpMYWVq7u7Tm3t1s/jEpJ/bAuIuodI+WYkqXbC2p9Rb9JoPNXXLba37zKmkiaQOlHKQvEl2kYYrnDQDfC2mcVf7Lgxp9gSa0WXD1QxcUBYA0eOhGP/ymT2LRlk0tKX5DgcB8PIMpkJwUCiPx4GRtQOwrVdRK3QxCO+TRtur964MWwKswMF+xVpYWwsGrQDdIdoKsgZZlmZONXtvnRheXlpJBJXaWVlidfhfoBw+kSHzbie5WsYwa+tqKgoG0i50X616+rc+BYutemuApq2q4ixV6cVun5iMsYvp6OBuGaEDo+iMj02I5+rjMr/apjjmBY9syIsAYcM2EvRoDNMBap+Ib2wqnb7TblFVovlJ5BcgPhFI1N0B9mM0CG1O2p2rm73W0OP+WtmO46BF3qavjpAkCoQbx+G14LPvJloqk5u+J4GSx0DVTCBMulpuF+qngco6M4Q3mtekG0a0W6ra2x4evkBTliI/Yq0oDJHk24Ml7sRjGlVIBj/yOt0/ys3N/c5ELV7DHOC09RAiJ8qPz8uDRrSh1pby6VlVVXh07VHgE1F6dYoacQRDbOdN1DKfhVNRBN8/7iuyDU79zRSFV1Y8n3zHM9N0JQfgcNjoRyv+APaHElio7U26uMWchgn8h+gM91xINo2ekIoAuod8GzQPkTqCl1TuKIvJTLBgyrLfObnWFnX3urG5Vc3A3Okwd96jYr7WltbvwbS+zMQ2f9Wrq8sw5GwcY7rxaa5nuLaxu3PZ8xdM6Dw1zZAKNNPZ3/AzvhRpeLa0tKyd413HMR+RVpCCJxh206DxIV+PdgIwzYKXN4Co/BNURaL3ePxzAU1cFG443YfTymqDVro82mLv+3KygEQVs2548xJ41MvoHooZoqZXNSgsZyeIslk3PGTXdcVFbEvryJj3paVhJNwfR4M9McpjD6jNgX+J0fLGF4mSlBy0IbCNFQpek3bfiCivaPXXpm9hKdEPQHi0m867sdJD5usXAequCO06dMWX9trC5YvaTnW7ryWNAZ+xCitNUXjzMlK4rHwvo5PjErM2Dgzd27anP7blbZt27Z1ZErKZ/Bef9fdMVDw5VQV00srSvtEWHoeRpstLqCqTJjNzbjio7/lHIrYr0hr286dnyTHx5+qwvAKP5uZEDmUsEuBoXCgDCd6W0HC+RM8hNFut/sen8/3XncOqNBoVgFzzRKCmrWA7zk4rt8pnjA+elJWImY6vgh+blEFuZAJLYYyhva4CRTTqkv05mt99i13k8ol07T4pzijHj2WlcTv49EcPd2nQLNeoGr09RVkoy9cdEEDPyPpwZUNOwsdt8gKeU5oomVJUZ4yWlL+ZlVMuIYRXdAwyiF6+N+BMfBPDZ6G4XN0tbB+tuvvJOhVLwHBXRxrs0QPxJseQ3mnpKQ8wINBKMd23g8MtRrE+VtIBXk/EsJCz3nAb6HhH5aSmIRJWKJkDOUjqG+K17sa2u3nbf62eUM9QUok2K9IKxQed3deOmhsxQ6H4xOJSxfDy8R1W+H6NpLZVJnxZ2ST5RWQuu7CcDOwTcvIyDBh48JGg06nXsbmtObmUlAn/f0V1VHCSj4oEU1MuDhbD7UihLbho0XlLx6X5yhjnKMHNMaZOpzK0vkX7bLPaLX5vrLKpo9BCsM6ZEFLRBX39iZf26Pos7RnID4D4RB6XyhxBO2PQFqC0QRGKAYU1MPnUE17aqe6qyqmw3m4hKh+tvN3jFL0XGNABpWYyxDexUnRipRdN8txa4W2aEF3qxR6Kg+0zy9gsCzihM6GTekddmPauBdKy0rf7MmDXk+4MnlyJlOUP8bFxF4UCluDbYMHh2ga+kePQLcdi2z6Cdr3TaqqzsP23JfyDiXsV6TVGSHPZMymcws0jlJ4k7g0B72hO7su4CtGR9ILJMK8BS7XC6qgrUB0o1MSUuYDWX2BDnyD4cS3Q0lUkygphUb5Ntzvj7BpFKf09qPtk/8QNaPs26ZZnhmU0xcx4gKMk6dZbNr9MYVVq5vnetbA+FsLI+aHmkYfnVdVWoJuDweSgX0wgeF0GmbmPqNFWRQgLlS1R2N8tSZi2z0AYNKPa7gLY5Ohq8QY+PyIDp2NTS3FMVbLIhDAHpYl6VJnQx4SYZ8lGFwxAW3zJa/bDZyip68bEdoVAHLc0RNhYbRcr8NxOmXSZdBWsE2z7o4N7cPZ80mSYE9zid5ht9sfH65S135NWu1A8sKEnB67Zw2TxJXAUbgIuLupZSeI/3k8GOJEAkXzV1peHqpjywajLCH/opfqi/I+4pLiB5XwbzBqu0zENLOuyHXjfUUVX147y42zSjNx5ORMRtVhNdDv19Cw1wi18ZPowqXbTx2MwhzgiJ5TvaOmaNw98VJCqURpAQg/3y9ZUq3mhPZfTXLHMa6TiTu0qQXaxcRYq/kINaC9xRReB+d8uqq2qrGnKeueECKuF7wuVwBjnsEmjMnVSDTRbfSJvLy8hNSUlMugPVxBgkllIweGsRF0mlmWa+E6z+2t2G57EwcEaSFw1ILGUQHq4lUSk0pAzr+VUD2wXDhb1+7nAtwVTahiHezyxBVV72y41YEzWzgNjzaVP8sSrbnggrT7ifC/CkW4ANcMqkTTR9B71LJvcSVad2m+DPQPmYWrMXbYvBWXZ325IWGl+qfXibZzlCuOx4rJkmLGpWC4bhG90BdAQ0HP+5kCJOM20rqWCfIPX6DpczTUo92rsJDQ/qjqobb5Ckg/3yqMIf9tag74SsId6500KckWFTUD2gZ68EeFOyYElNKaYOhtg0aM7flnvzVKkqnGppuo6Wv4tbSHawxJHDCkhWi3a0ADeRjUxRWckFuIEN4wqb12nwLq2NJWtWXAMZe6KUt10xznbBDxRxFc/MvYlVEZqZspE+0hZFYEQiFw9M5g6IJ7BaF30YYJNRaTPEWW2PmE6bHFcOYZl309p/rIPZJCPwBG+qDJF3hhRGEVGuDfxiirx9xmz2+41T0Z9imNsz0/rlhf9n3nvJARlAFtYj+RjjH3OwETZXhcHlyihhM23UV9QOzARLGaqr7a0NJSbjabM0xchkFaDxoQVCMZGcNNelTZa/pSzqGAA4q02hEygn7mdDq3yIzjS8NlP11tAgJX6Wsv4gLqvVWWks8rv3If47qHUYqx0UdwhtRE6+CDjqG3bybq1s7xcQzsPUwuqvbXzcr/LycSqGkUtfBNIGW9ESCBVi6kB5m/7Y0QYelOqdNm2Y/nhM8knHi47mpDa7LSnYVFRezFwZ4gcdvthzOqJ/HoibBWBIR2T1tb28vt7RaKuQPIbh4LrsfdnTUI1MTjs7OzZwx2roK9jWFJWl6v10oC5HAukckgAzcHAoGvKysrl/UlFEzISF8JxHWtwuVWSsl5pDNxUfHxth21eyW9VzswBMqmIu+LMYo4Ede7wU1Hg3hXpVFyQf32rV/l3FMz7GwOwxmhAW3F5jl510Zp0k4STLbxJ07YbT6/Ol+WzQfXz7DPxyzYehhnJk0jwUXZ2HZQukrnnF1+LXGUEgxTNEjAuG02swWzVIeNqkuCcdwq1YC4sS3Q9mUnWxX2c7R97UF2AtREm82GM45rBqucvwSGFWmh3cDhcNhlLj1CJXIwbkMvUi7JbR63Z57H47m7rKzsm764I2AKLyDBQq47ou529MNF0BWqr+2GvmST7i9GFZW2NMxxYfaeI4lG3m8INF0+snDp9phezzSwNxBqP1sWFLFpbsW1gRF6taxwjBiBpPSssEmL62e7MBMOhjtqzya0XQjtCoKZgwi9i3I+o+k2+zTb9Motg1Emm2w+Htqkp5uFPgDxbUCIC0rKS7rYqFwu1ySQ0DDBxh6rRYIOEV1i2A95DCvSck+ePI5zCRc7dw7ub4JW83tG2UR4Qainf92X627dunXXyOQRi0I6P2K5IFph6eLFNX0t45LTmTI6z3lwm0pWJhaWbuz9jCCETy0mMp/e0tLy5sjZS3+5EM0GugX6Xm0rynvYKistlNI/axr9kAS0N5nE/obqIBHaC/DmpNBcjgLHWNQAXQEaAMOEFapvcOJcoT9Wgcvjhdt0F/2hQlPVa0rKyroQFgzIMg8GqbSHOU/lbW3DLtLpsCEtEI8Vq9mMSRZwpq07n5QJEmU3A3GdAxIX+mdJzhznWCazEQES2Aoq5CotzOyb7p2ckPAhlyRMW+UTmnbfrsbGr/saeRQdEb1211Uw2v7dzFSceXopkvNCKokeZsVYXza0gN7xtUXZz8mSZV5toL4mQYo/ijFyNojizxAhNhHKcNIEo8BGC4FBBrWvgMTWwlt8415SvWsw5k5yc3NjoFGMpuH76xogzltLKipKO+/ACKgel+dCSrux2RKysXTp0ogyWw8lDBvSUhRlPIjdaF/ozf1gqsKkCwrcBcsK3O7roQFhSF7Kiax63Z6lUzyeR32q+kHHzM4h0vhuzJgxGIubYMadvpIHEpZbcp9Pgtl/0RfmxLo5zu9iZ5bvng0KrV+LX1ZDtof8tXbDIKuhi4RCPXtzfSxjtGF2AsYoSwE13uoP+FfLClsF7RJJCwQtMSFA2W1ke6vrk/XVdYWvDo4hXpZlCw2vxtWDSnp/cVnZ+50dUXFZj8flugjUQugDYX0SBZz7+mCEBP+lMWxIS2IMVbdJERyKGaNnwMtien6WDqD68hhqN0nSGyA2Ty8uLt694DkkgfXbfmWXXaPgZpg6KjjZx9ipMqGmhjnuG6JnluoprhJILEajnJOVQZ4vu4jNCzctvuzyjMQozrwkQFaNfmTdjwaZDR3gu6if7fpBYnwFZfQMWTZjWrKOGYA2sWatPuqeqtrBdP4FNvRplLSGEZV+bPX7/6N1WqmBzqfxMbE4QYBLxcJPPguxTvPTXvMtDkUMC9JKS0uzZYxK1zMV42+NiI+ERt5iVKQAN2HigsPInk6i3dcr6JN1BieixTtp0o3tWaAHCuZva9AkEzaCBUzQ89EPBvPfweCs1BU5bokvWlReN33SDqJwGQj11nHJkzBo4B42s9WXpKXEmE24xu1UYSLfrbsiHZePrByM8hkYHKxX/ZWjKXsY3uE9QCbXd9i1ThPitVKlfOevB/mejY2N9XHRsVtJcJXG7nYuBNlSWbmnod+T5xlvNZtvgCP/HCZRbPuJPrjMszsatq8Z5KL+IhjypIUzhl63+7RgKqwQNO2hVp/vs9D+p02yfBoj7NpQVuJu51c6XhYOO43aoivg/McGQ0RGD/fFhbnP4iz3aO5YAOW5De4xFQpznCTLaUBcN4MsV83RO4aKXNlkOpR0IC0kLJNZuSWUkBPVgeM1Rv9DDNIaUsA1i6uuzH5uZHJ0HeUaZtrOAFl4KVXJbWs137d9XTgdCdCPaqrXuxDaE6ZC2y05QRsZP8XtPoS2ti5vBRUSNIgTJDPDxCU5hHbry6XBwP2xTw0890vMjO8NDHnScjgcadDNzw0tRdDBGL/LajLdD0r8F2VlZWgzetTlcvkkwnCdWHczLJ2Bcd9PhvPeI50knv6gPX1X6Oe39UWec7ksZqARFEZkJ5fYi4wIjNnlgOb2lq8t8HX7MIjEvO6q0biWDKXJ9vhfLUHnVgNDDePv150xXyli7LXTTsuVJr8ejPqR08t5W6532qJipSnNgdYlHRPnRoK2QOAjE5f/Dv3gqN0bKSbq4G8Tq+1HM8HIrPqg3Rt+0AL+f5aVl6/py/2HEoY0aeHsh9fhPgFeDsZm7yhB5VDK7oHCl7id7ndIa/NrDNNNYSdvnxYOpiBvIEESCzvbCBfMJhgMcBBIqzNiCktWNRU5bwR1EInnHLhbHNXDoIj3Aj7/zPhZi9a1H7v60pGjgdhwWvrngIWC+DQx/IykBxJC60B92quRHW+NFekgaL9gk00v75runR13W3FdpPfCiaOpTu+dRNKzCbU7mGKf6JgVqtfLBIR2QwlcLNL7DkUMadLy5uVlEkZPJ8Hga52Bi0WP4AxeotV6niC6rSr48kBcV3GlnuZbBsx3EOfSX2HbcWF0fKsUjD/UZ5RdxOTEkWO4v1YW9W0x2uqdpRoutu1oOLcVlm/dUuQsilYw+S+5QAhR2eJXb0wpqvpJ6zAXLsvKYV3qSEkKJ/ygN05n8yPJvGNg6AOkookwOGE03Qslq/Y9/H2zL+cvrCz9wutyzSSU/ZMGPdwjMYUgWqH9fwZj4K1lYVwjhhuGLGlhthKv0/1HGhSHe3o50bA7r8MBQhDx6Y5dO94L6exVcKl33W73CZzQh0gwLlI7Nmsqa+hr2ZbfmBM9IcP5BGh1U7QRpBFa4YqJo52L6u2iqn6ue53mZ1vbmgI7MEnF2qszGrTEpM8ZodlAaVckFZav6EhYZRelW1Ns0p9p1yoqlNFzPSkj55G9IAka+GWBg1x2puu80M9oymnRkqK899BGFuk1cJYQ2vLjQFw/CsquoUEn6+5SVuLgWQ9/fhQqeXDzjq2v1tTU7BfmhiFLWrm5uRJ02iNJ5KNJOzC3ybHJiYnrHA7HKxUVFetR+vEy9olwuZ4MZe5FgOBDykWr6JNtAZEWbUmFxoNLfqJZsHR5QLOngA4aYERsELJYqsRJqxpnudYKSqIC/sC7qkT/cZ9aWdPZ2TCRaSlM0MywtaTEqyjyjHVXj/mMBMiS1dVrlw8kXbuBfYcJ6a6pQfOAjlp4t5j6DaX8Pq0tDU0afZyfn19tVswnQ7OZSqkYj2GMYLsMTNVCBdkKaugSIKtSlYkvS8pKVkVybbStZmdnJ0ZFRSWVlpauGqo+XEOWtKqrq1WPy/U/RtnRAlfaa+Rthp7HjBxBMMpoz5gIDeQWs6T8forX+4rWSF6DbXVAZ7tVN/iyA0jr09LlpX1exrBla+PakSNiHoXLXQ6NxRK8nE47uKQjE6QTOdM4AAAgAElEQVTDTBJMw+6DjesJp++gk2lY7+jWzZuJefQKOK9zTkUEznKewxk5GZr3pvGOjC82XDX6/aqV67877gNtWK3MP5BRX2QfJykKLnZGE0AA2vKTfiH+k6mYrq2b7Xw39qa+25gWLVq0DkjmEXe2+xVm8idqRLZpXI+9htJYY319/baVK1c29MXPz5uXN5bIprnQZid63e5X4Tp3DUXiGrKkhR6+eXl5d8qy/Cz8bAKJqcFN3Myf7x9nkpQrgBhOBFkptYdYWOhkejiQ18E8itwqXJ4l9Of1V+gUOL+hpfHtcMt6egPOHq26MvuWkSNsiyjlczUhntH84gMmszkgeWG2F/SgRvuZCW718adVlYu6czbMfEZrXX9l5gNMon/s5hCcusYwvCNASswnnJyfl535ZM1lqXdlPrS5z1Kigb0PXPkQT6LRdkWYxI+WFBn9udBlh+EqU9AgpiiE4YL/Q2XGDwdSO7+npLvdIdR2t4c+A4KePNbpPBbaL0b1hfGYprkd7hrY/J+h5uA8ZEkLEQqvsaHDJjRIL4cHeZXH4XiZMuk0+H3SHj5cXaGH5QCSO7zDtgY1IO5fsmRJn+1Z7QgR1xsjU6LjQKq+EqjTRoNr0GpgJH2IMnIOyFqaz992Z2+GdK2tdQWTrNgAe4rzTUL7ozGLj2wyp6y5OmP2mHvXRZQp28Avh2QeO4lw6TEgKDP0f/SEaO9nG0C6r4K22AosIFNMskGJizGOg2mfSWuwgLP0brfbDgM8+ggGDRWUJHEQDAomTUKbar9Tpe0NDGnS6g6hZQvfgiRWaVWUtwXl/4CGgOsGI4rmAg3nv6WVpf/r63233mQfkTq3amv7yIPEtePGnH+boq2TGCNXBiPakGk/1m18bmx02gpKtdqEoup1Wg+rZtFAO8IyGh0CeyOsn0Fx/SX9i8KkpHVXjbkp84G1ZUNtNDyQIQj1UUEsIK/khTb5Yfz6DBStB1XNVy37WVsb5RIzaQkgaU3WNFa1L8oZCvWU6XW5zgTCwmAEng67YRP1CrMZ3YK+2xfl6w7DkrTagZEZ4cF/NXHixDKbyfYsl8i0UA47VM26SzG+kQr1/p4ynXQGZmW5TnKdFWVVrm4sckyH3x+1R6VMvH1Jw44i91yLTLIJZW4itNpxthHOUq3soyOLiNobmaSaRh8HjePsyGu9G1i/33BG09ddmfnPmnPZB6hq9uM6BgYZ29VdPybzhFdJcCG1JjTt/qaWwIMdB7yQ780GaEvVRUXQan6hUNoYshmkpxifyTQOyOovlDI0SySTcEmNKRknCD8Izvm+P2aUvYVhTVqIUCNANe8LkLy+sZgs54Be/jcSzKBi7nS4CiPe6w0tLX1Sqa4ITB4lFHoZsMQkwdlF1xJXK6iG34Q8o0lyUfmmultdt3BOcHbyJaqwzybXTTxF03pWPzG++CGjMg+GxtHfXKuY324ylOspKW500YqL0l+c8Nh6IxbXPgYmy4CB7Fmzgh7qNMNH/Y+nzF60RZvd9Vgc/Ar3MmGhROXOzk4UNlsWENUUGCRPUAhFm1pPiTH0cONCE60kONE0ZDDsSasj0AYGL+gpl8v1FRf0z4TRK2jH8LSCrIf/P+6rLUuWqA3EfZymXkgpLSCSeGzkiJi3mmY7XrxbXVSNoxCQWFlaavR8aKTjhRD/Tb5zWaN2e8/X9driogkV6XrknIEA00IRWhRlk1wbrxrzOfB42eiH1i02VMZ9BwwAufUm+y1RZin1gVsWbyicsXfvh/HmzJL5ME5JapsIfIYe9Bii2Ww253qd7iMpox5oDDjAYf6OyNobFQtJgAw508N+RVqIkNq31Ol03iFTvgYkn0dIUPTFB1+8q6Ghz/r5ik1VP44dPflc6jNRWSLjoQHcSim5knD55GuZ892dN+c/tCFh5aY0zf6xRvm2Fn/bc5G8aB4TjYQVSbidSBAD1zoTmuOJjNNtNZePvu1JL3vp/OLBiZ45GMARf+1pGebW1F0wwismDPPka25otql1zWOfI32OYTbUkTJbj8CwpfCmvX8vRVGO5pQ+BQOY2UTktVO9BTU2kwWzVqcQpnvPW2hffB4F2aUR8WrpYj3b+pDCfkda7cC033a7/QOLYvoCfh4Hn12gGv5n+XI9oFtEwE62pTDXNn6kK5Vp2ggqEzsl4hCUpkiQCLOBFK83WUx/dxH3Pc3NzS8lza5+v2eZOwhUDQ9OG42RHhz9q2FY4AiKay3jOad3/+7wzFHrz898NP3Jmn0eUhfrW3P16EOYRuZaWQLWW+9A1pg4HwjDn667XJsJz3vR/kZcvxQkxk4iep4DHRgJwtGtVTeIngmMim8bm5pf7ovt95fCfktaCKWqqlZzOO6ljP1ECVvb3Nb2SV/OB8IaYVNMN+EyISL4BExVH/Zdw3Zgi+ssZtPGIsZe6UNCVVxW0US6z7DSf1B9dL2OxbDEJZel3pLz0Oa9lgatNyD5/3hlei4ndBaM+oeQPR+iAj+PoxKrXXVhOuYaNELx9ANU0MYeaAiJZ50QZCEM3EsZoXZCKc62dzdj3RYQ4t6BuATtTQxp0kKHN8cEx0gWxQ5iQqQIaPwg8TRqgcBPpVVVvS4zKA6u1fo8KyvrBxCfW/uaAtwsmf4GzQGjP7IwDQJHrK1CiGLY9bVQ1WKqBZYWwfZI7Krou7X+kszPiZnOp8FMKXsDSIYXxZoscesuT7sn8+HNSwYiySw4ikmp45Is3CZiFW7GePyHCEFTVaK+tXDDhgUd/dFAslIOTkufCF8tay5N80uMY8x8JKxwHYUTQU8xW3nuhmljPiOq9nbd5vVlOa9qRvq0CKEJdSELZxrVyBrBxH2qEN+oqrqurq6uPikh6Qp4Cd23OSE+qq2t/XYvFndAGFKkFfIbiYVC5VLK/1Dg9mD4YowR9DNpME64wjXYVzvFUzBfCPU/QGYLS0tLt4YTZUNTtRGrhHuWhxwNb3AHOgiToNqFraIFWsgzvoD/ie21rcsrNq8MdIzu0Jep6/RHajZsnDYW8yriovDuFr4OFGjLOJspppPWXzX6rbVXpP/bR8WK+rZNu957gqhnnw2STlycmdPotvR7ul9Qq890OjOBk9EfTVeN8ZlQeDJUItLvpqSPvuKnixK/kpToLCaTsw5Jz0TPalRTqKRwfDac9KSS6L5nxIWxx4jEp8WNylyy4eoxj/gD/o9rqjdt2B/XXKKP3rjRrjSmahM4Z3no5getdZXqI4s+W1G2vi/RPajP9w0xWyrIz+aGNiG0pwNE/LO8tHx7e/t0u91ZjJIzSffvYi2cd1fHAIHoJoFT8cXa0LCPDinScrlcEznFGT/d0W1ED4diBxgBHeZ0UPuA2OjHbof7eW+6d17x+uLmwSoP1bR/CcqArDRGKTscCAzj1I8ECk3jEhudkhDdfGyCi3xZqBsr+9epBJAhDeMjM7hA1k2C/y+QZPlUoJBPrbbMJf+4UrRRRuM0IhKZoFtrLkt9KNzSICQsd1oG5vdDcg0XwncMF3QutcZ8CyM42qtySd8Xuu8uK8F2SUk+ENgDICF/O94++rlll2d8MPHBdUPKM3sgWHVltmlCuvNv0LDOApL2EhwIoOacEZVL5Ktj8113Lihin0UaCRUafa1FkMeAkDAgAJoGVgmNPV9atnCPbDsy5ZiZJz/sRYTwCUrQJWi3syv0yVyv2/176AuN8P0tzHLV70oPEoYMaXkd3jwus39RTFga6ZQsglLs8L/nnLpIKnkaRoVbB8uYa51ZttsGtq0o7z0rM71JJT1V+umcSR6iiA1IbXZf7p9h29rur9RT+bGskTvDDwISkOz1W4dCVLBg2s6AbDLb10/LXADf19OAWONra1q6s3Rn8yGHZ1wOzxlH53CLuoOgZDLraX//gOtKcbp+crQsnbLxyjFPraxa+9Fwl7pQo2iY5byAMnYLCQahREA7EBgUEKQr+iuJEYtDceAAUhHJNdH0YbfbXzPLMqjUzKMS7fPaXbWLOh4z1emcQCQZM1qFHVAEpT8JNfBauy0rmNXafDUcfhZoOK2yJqIyMzPv29chboYEacHDHMtl+Q4SHMn704PxJWTA/9NhVJCcTuftOHs4mGXE/HfQ2L5Ye3XGd7HxCc9xzu+G2x4mhNgoArTfJKlR8g0Lrq8cO4jFjQgBUJxboYu0wadZJdLaBnpCa4Ae40kmaqyJ+E08Zn3qr2LQMH4M2XvqayRIBqI9SUg0e3x+Or6H+cN5lrFhjuso0CYw6gMSFrwFsQ6o6tZWNfCJ5verVpvlbWhbHq4Rb1ERW9S++qI3VFZW7oRn80JGRsYr69at83U0l3jT0608Ne0aEgzLHA4atOIXi0NBAqEP2ayK8lsox9EkyBNRIPmenZyc/Dp8jyjUzd7CPiet7OxsU2x07GksKGENVOQwQWO4ROF8M1z3KUwI0J+L4LKdy0l+mkli42H0iWVUBAIq2bqhMK16ZGENqp8/7Jrtvkzh9BFQrd5uaq7bHmlg+s6oX7/uf7GjMhdAp9zrpOWDJrytlZDtME7ugL8bgdZX1xNS06AvjiOuJMKOzyTmqPa4GVQPpTLY0lN/gSputsbZuUvPTSohwVUQww6oap/gcGMstnb3hDLVF7g8urDiezTq6VLYHNdmTMyKGatzl/RNzQ5NTu0hiYYiOPwmlEG9Oy3G16b6X3I4HKlT3O5JJi5jGf/UoZzYHrKgf6H6f2CTVlRUVCIj2onwaDsvuekvQP1h50VHR38N3xf1enQnoHH0mtHOE6DBnAcvCD2IseP6JU62xvKRX9fPdj177y0VpX+5PGtZaoL5PLW1aVNPBuzegDNk664Y8zCXKS49GphnfBg0+AmproVWtgv010YgKqDcbfDZAXTeAk07HpTrI2DsPQKaZj6M+8ovqqn2HaDKnhQVbfuq7KL0l1yPrR80++UvhUNz0jBcUTLOT8DfJhgrXi4ji0ra047tnGM/AtrdVP0HZSnenDEYmmhAvlKg5oGkytGEkdbDYZLCZXQ5wYgoaJNEx9TOhImJREH6Is8PpDwDxT4nLSCHJBSFO21GcbgR/iujmigjOKNBRS0RIiAIUzDfITTfMfBwJwtMchGc2u+YMimPU3qCl7GlfZ3xGD/a4WSUYVhmFKM7vjSMPz9R4uzQqwud18QVlX8Oo9qyflS5C7b51laOkDLLoD7egVwH9SU/NO8mIKPybYR8t5mQZTsJ2eUD9S8QlLT0aIVQK5AiyTHQH87IlsjYJCuJSc8iyqg8oE1OWkreJmrdpsGo2t5ALPScO0bY5LM3XpM5T2jinS0t2qr3ntjY2gf/uH2Gb5dsbDvOPnIroygNURVeRYO+sL4QvhQ5J8iKdDPRZ11FDdStonjJGn9mr1ftGSZZPhLuA4JBj5qMhJmjwmzHZ0pDHwz9MHGAxRkw9j1paXQc4bRj2m4kmc+EGihcWFZW3Nv5MIqk2kymy2EMwFhAqaHNEhX01IDd/mDoehGDE3oB/ElHWxUQKorBbXpcJEHjiB6RlOZLCr2/odCO6uzWvly7O3ieIIH1V4lyuHa/SEsVQVVvZR3orUBU38BnVzeKcUyUjRTkTySnHZxFpnqcxJIxmfC4UUBSG0lLxQdAWG/C9yEfWzCe6j5f7BAgsFtSrfyT86/MfKTmXPbZUI90gW4M9bM9LzNOMZlJAbStMxpmObCdzWe4akOIr6FulX5KX4i/uax8oLY7u90eb1FM6IgTPnFreGC8rxoqyAaMGgztfgTVy0qoEOSrgZRnMLDvSYvpgdI6btqsEvFwcQSEhaiqqtqck5Nze7TNZoaHfNnuSKaU5EitEpJh3wzylOESnVYYdR5pbPI9RWUNz4+yKTxNEPlwSsSFGNhNyLpu/3afrt0DNEJr+6qZIVn9VE/I/4Bjvt8SlKpae1AksrKyyFl/PZMce+xvSNII0BSERgJbV5Om7/5NWhd/QvzrFw+oDvsIGOnieEHJWBYz+q/we8hnm4m5qaS06TbnjZRId0BbchDGzmKMLQCCwkFwt6efNn1g90H7WIHLdTZ8jUQ6QnLcKIj4Tgj6PyrUHxpaW5cvXbq0FgSDFJNs+h0TKvUL8fHASjVw7HPSEpTu7LRJ5qTb7LhhgVO0Uzye1UA4KF+0m5EtAXMAnRv7FKpFYLwtPTEszTHJfkts4RJc9IpLYDbvmj7+Rzk6fjLsnwQq5IS+XLcn4Gi6cdqYtX1xbWoE+fHlFYQs2AhDYXNwBrA7SJJEjjzySHLxxRejZAoaICfC10Sav3+FtCx8nQR2rCXCP6QFlF6BRnrGxJlFjJUPBzXxbl/lgmmS8y9AtrHw8HdiG6gvykuAd3UI0SOZwnBO1OVb6+vnjb1tVcT5ETvC4XDkwGX+3sthaLSvFkJ7TajqR9CsNlRWVu7o5KiNfeCZ/pRhb2CfkxY8nNWc8i0ogoY2oYp36xS3W2sNBH6A/Tu7W36je+pmu2OFTdhBIceU4XtMyzM/67NoTYX2DLxonGU5U1Zsf2q+zbsCRp91oMu3KDFxKJ44glLy4Kb1UlXyI5dIryGXUe1DonpheZCsekNUVBS58MILybnnnkssFgvRGraTlmVfkqb5TxH/1lU6S+8nAG2eXXrOFaM+XH1JWpUmb64bf//QTf4RcmPQ11k2zMxNbJrrKZIUM0jxPztVUxAiR8TE1TT8P3vXAR9FtfXPvTOzu+mkQGghgEFq6ibIUxREfKI+xYb4bKioD1FEwIaiMRCKKCqKiooidhQVPwtWRKRDGqETSjqQQHqyZWbud85sggEpuyEhgPn/CLs7OztzZ+be/z3n3FOmJjy0Mxd+ipu73m1TR2z7WB9Te+k++PvqL5HRQXzwefjkf9Gdzo9TNm7cciYGRh8PzU5aDofjoGz2WoYz5fC/trJIxqQFXibpV3yy2/tarcT0ZcAkB6CIimRl0oXw6xsXH4I3PgJ7Ky2+dDziwAJKHczhsQd1ZZm+2qcVe5kz9hC4/Gh6MVeebzgsCQnIrKmu+cMTI8HJwLmOs6lE4UbHKkxroBAV1fe2AiwrcBnWTwY/Pz+4++67YdSoUSCh7uzYmwLVK94H25alKGmdEyXwjoZZ5vL7spe0GiBsZ9748E04DWTmFebu6rdQPyMvuDKpZ4jk60NB+VRhvHbCEjRJ70I1zYZ9u5fExdSIjjqNAbdMJgQ5RG6LGgF5vteNcdIWaOFoDWhiuZPpK9PS0grPRn+3ZietzZs3l1ljrN9KEiMHxuDDX7h8hG4yniLj+BBZNW7TavPlyUgq5CJhOl6OIMHEOovF4rHOEzozrar8qehXmZ+yC8f5gzh/XwB/nUMTOqzTQHvudWXr/sZMOKmrvFIyAS3ZHZO08rDLvb4JYEUhBcce+Z23l4CI83TYuOkvjwlFUWDo0KFw1113AdccUJP6DVStXADOfTvOJenq72A0ebFhJA1j3zkkJMgJ6xC+LX98l+91u/gh7PW9R5sjmg1kcyqbEncNtpVUuDoJexuS1WuaLtZK4HQIriwgFwRJ4v0XDeep7sYj2oRtv7cwvycY28yFnodz3Bau8T0FRQW78/LOPleR+mh20iKxNCEi4jsIDOyHEsz9xywJ5trmfvl6AVUofC9ISU9xi7RcDn3RvXVdStBV/euAaRkl2EE+HdzV+i3307sxCuAGZsZZe+tB7VDm+7C33F0vZbehQwES4u+MG5klj7DpEWHN3ugyth9NWG1a6zD5aRt06qDDiAe8oaiYG1HM8fHxcO+994K/RYKKJS9CzdqFoDvO6r7qKahod7BRPp4ZpeOukyxQWDC+y/9pquPjCl3b1pzpeggliTEBnMMl4JqoyFn2fadDe+mXLem5MW27ye3a+l+IEwyqi9j/hd6mX8cwGgNuSYy19RM+69at2xc7d+4kojtpvYKzBc1OWoT1WVllVqt1usykNtjJboRTc7J0Yif9Co/wu7vJ+IsTu/kJXRolcfY/rkg9qqckLB0SFZdTo9pyWz+VSd7X9GeQGz34pkjpTRJAztgur6MqgG1mg2vLkSm0Grho17EJy89PwEP3O2DARSrMfdcExcWuydpsNsMtt9wCHUP8oWrpm1D15/wmaPFZBboxXnhfu2LfeERSTCMCBMzPHtvxo72ZBZnNFctYDZIc4KpjgEK9WFbtsD8VmrS56sCU2AgzY9cwEA/gDNTOcEIVbO+7L+faE2e5f/xjecefCzgjSIuQkpJScEF09EShmKuZq2CkdwMPtVpo6ksp6eluOxtxxS8Q1U1K6UEOdhPwrtyG8kqOj2LOqZ6esFloYqfOtaqK5NgLK5OiF/gmZjSKU+nR6DR7z5YtD7Wd6Ge2fI6sfR/25f/uLAX5p9y/ExYhLlqDoVc54adfZXj3Q/PhlJQdO3aEgRdfCLbUxVC95pOmaOrZjkB8zmMUWUk4L6otlW9rFl+Pki22cv9o8w5siwME6+htsoysSI5rg5JyfyQqKz5/MpviY2Xrhar+fjasip4OnDGkRVifmbmnd+/eE7zN5uWMcQruJGmjLnfTiUDjtYYc3xyac3RGRka2J6KwJIQJZzQKH1QN5yWg2Q064DaqWGJjEquRQKYOo4EiU2hQk5AWgVQWlOhW5IwNswFnkd/uhZjyY6ydklf7qLsdUI0a3xeLTVBV/dctGnDJJWAu2QNlKz4AvbpBqcT+CSAV/ELGzHdljeVPN8dKY6+FmQ6cBD9lJuVSfJ79sO9NxZ5O7TK5urxwYJ/ezJzqhJdgY1ZDJXyKPQSXtHlOqIhnFGnV3lDKZz4/rmvXr+WgkNs5g8FISRRMTGJyQD2bl4bbKwSD/fi6BX/9VbXdvph0eU/Py2vUcvBRftEF+xWfbJFrRdJIPkjkRfFaZHNg5CWvA2tyOwjdBzK6tvLutHzNfkZL1n97TuEddYiPVeH3P2XYuZsfYVvvGxcN1WsXglq8t6mberZDYhzus7Awckj9tDkaQFJ76TPxd5i8RbJwxbpSSBpNnvvwmf5ZU83mhCSnF54CYbG+VutQDaAr9qtf8XPmmVTDsCE4o0irPlJ37y7Flzndu3f/wN/LP0JS4DwkDYr/I18sBjpUI40cFBrfI8oPbSK7WEPP5Tct88D+xN6TigAcvRIzHRSJf2W3yBDdWz5P0lkPnKOsSJY3I5EVo57WZFJWfdAq0a3/6rIDpSySAP72nPrGq5RZFQ4UcygrO9K1q52/GexrfjsdzTwX4M8Zn7j7gU4bIt7Ky2oOSaTVlA3ZhUkd/+clhXZTGLQWjDlUqMnOcG7NH5DssrdR5pGR5WFmT4PzY2JicNJl8ySAIIlJP0VGRo4Al7PoWYszlrTqUFs9J7X2r07UhcacLWo7amXr2s9EGHgaCqmgv9XFid0WmWSfdMElZ6qaumfA8Q/VqFi3Xzdrgh9TNT4/QjdURFsN+bod+Z3Iz0C1sPR0NPFcQXeLlzQvZ0zYszhhrTjaraBuAaYpG/A2FNiSni04XI2IzkmvifiO0tNMiI69jgWzew8m9RoZnLilwN3j4nHIxOLKnMRgkJfJ/F5CQsKb5eXly3fu3ElagzjbVMYznrSORmOLttQ5SiZHx0hMDtdB2KrUqg3e4KWWJ8eNZSDkaqfj+aDETCLOd2j/00VYrsYpYaBpx3T1IFcH6moKfivhU1TrrRHtTV9xOB1mC9yCCQf0JVzmP/6rY9ji3HGd32MFYoXNV/U1+fDI3Ic7ReSP63Swqkb/o6kqeD8mx706YSrbXz4lbg1I0KoiOS4UBGuLUn4YRIve5GRK7bTIXrS6/pq7x+W6bqYczrUge9lVErCrWvkHHEyIi/8DR9RP8fHxmZqm7ZOL5P2Nma68qXDWkVZjY9MwUBRJJnvG+fhonQEmr//pQsriRpoO1sZHMXmXJ8UtTIP0VHfzdTcWmGtx4JjuH9XVzDDVtgvVIShQwIGivwSy1VtyIL7baWrkuQULA36LxEVf0YG/4i1M0YLDLYwyJAhJ9fGW3sp9uPO0sFf3ui3puA0Og7D/9eSyROYAIkanq2QdWFx1VQzoKHj7e3JYIcQxV2Jq/dduwBNfjx3soCTzzRAqNvS1Wrfij7appaWba000Zxz+8aTVoRfp/NAZHy/NMN/bdHWtyZjVOHUYP2B8jGyCi2McMZSyxq183Y2BTbd3DvThPLjsOCunWbtcs2efXhp07aIhaf31KH/fXQGXo67b47gBQS04MVgXZmhm4MfqnJoZyPj+TiazrSidv9HYKpUQsBKPvw/l59eFJmySLN2OW6+p9/htutC/1p2qRwsGNlXN9pEVI43acXZxFT0hJYKzSxhI5OS6TwkOoSrV2/CcaajaZDGVlQhZMFkIk13Xy1VV3etpSb7GwllBWtHR0aGKogyUcNYoKilZvbsRZwDZJJG47RQ6e7m60DYz9I3NVWVTYrsB+c6AWCeAUWeNk02cSmfd3VjnPRE2jmjvE9TG/HjfUBjyS+6x9/lpqQJjHnAYUtZtwxyweYsEFZWufnmgSoep6wHu7w2QEIpTdaPnQz3nwWqL3R4NP8bEf7PHdspYNJyv9qTE18lgK68ez30tnUwcBjCZj8O+1wmbQeocGd5XIEnMqqnYvyJ0ZoFHqZY2b968H6Wn1Xgs8q53ajrcIrgol4En4zX+FaImoMro80L4II2djxspi8mlnHGV00q9bPiLGf/MsqSZJfn32NjY+9PS0holp5wnOCNIi4zrvXv3lvEGO4+ewXB7sK+Xz4tGVkUGNSFBIeNx93dPVqjVXTAjQ6lQgYuiLa032+j8pUkJ6cykvaw54Hsu8T6SxN5hDUzQ1xCoXvscHMLzzBZvG+c1vscy4+Xmc/htmQzXDHHC4IEq/LrMCd987xIK6AaisAUzUS78TzjAtZ1RjWzM6O5/MChHOucw/4KOnZ4uGB+2o1qz7Y6YXXTKznAmiZmwr43A41PVI3K1qQue3iUc2gzsFelzffbVeOr6QP05If595HEAACAASURBVDb2GS4powRj+QcO7v+Rqun0i+9LaZOpnKErZIyJRbjvUs6l81Hqi8AL7Y1joydAbXm7vyVeZkPNskyey5839JobimYjLTKAG3UOBbu6rzWebo43zgil/RISftIKC39eXxvU6WPxuQAJi+zfdNu8uYAH+sbGmhKio39xp8r0yYAPKIsx5sMEGxlvipPLk6x/ctlZomnSD5LE47lkJP2zAHJZI1y2W4ibqztzHm7/gx7UY7iSs/tiu/3YUvjCL03QL14FX1+Uqu5ywLbtEmzP+kusOmRz5dzagS2/pjNAv7YtUldjADtihATsVRTC93gz30m46ZT9S/ZXqpUhJudHkiwvY8DDUaKLxX55lRF6ZJJnSSDlPiqsWyunxX+f8mvqKk9Cj1IyMpZHRkam45hzHC7/xcR+/I9S3bhIS4id61JSPqgrmKwwdpng0lwGx1/T0QU77RWkCM1CWr169fJLsFofwlnlvsPGRpfsSeL2DbxNuyS8yfNI8uobFx9pBI3WGSM5pduQpkkm6TFa/bBarS+iiLq5oeRV7oTP/RUxFjtIJJ5hCqqBlai2a5ScCVtE8glSAtgFg88a6fLdwvw5+7J7vvjIzj+35PVH0jqmPSITVcLFKF2NuNUO4R0FPDzKDlNftEDBvr/8tlQUu9YecBW3SGgDcA9ODx18aGY/bZdyroLyXrURTMRnXc1XRHx/ah71rdt4t1e49BxOosUMRJn4a1x4M6NqNItBoqGSXv+2Do6mNDYr3D12ba6sI7Jb6EJUcsbqqbc8hDSeHj16+EhCIsKkiJQTWUUF00Wz+Hs1C2l5eXl1pVJfcHQOLNdKWWt8YD0dDoclLCyM6qm3Y0dmfqABTETiizx2u8KkwfGx8eNRv/6/htQ6bJe4vrg0OeZek6Qk1xbYCGXsCI6glZwPhbPqtJIWxZll7dixd+HX39orKiqPWamoGufMzxcrENlHhd7ddbionwaj7nHArDlmKCv/6xrIW56q8izNB0jFq7kGVcaBeOcj/CnHz2m7pHMROLWxR8w9Ou2BU1STFMHxabC+2PWMuoTHtpozSsUUhk+Nal24TVoE5CMZx5OSm5trc5lgOElZh00x+CYqPjZ2OO53I9LRkNoJ+3gQlPHECdoyT9rQWGgW0pJVtUKYpBz2d9Kim5jFdfWnnTt3VnXr1o1u3MkCp9tyDjNMuqQncP6Vp9V3CL9mblw1pFfk/5hi+jfOnBdxw6bANCHEHnw4Pwut6lffxK1N4p9zInSNiMjq16+fY9GiRcctr7Y3m8O8BWZ4MbnGcDK9YrATDmBL35xnBk3/e9enzKef7HRljbgM7/4VnVBc8DrGgVvgLtqiqjhx5+jwld3eyG5wyXiNaTYJOElDRFpl2PeyGFUtF1CgM3GACVaBFBmEauP5mq7v8OTYCRERAQkxMbeBJMUGBQW9gZvSuEuKOjz+kSwvZkyi0mXeJ4n0JQPrah20aenp6cdZJmpaNAtpOSVJV/5eJQdJApY4dXVKaVkZeQZrqEYKFIk1N3KnhzGZjdNjY8lrfqen7aldBdr2RxLPsjpiP3WYHMYwlsFS/eOWlJLGXCXyBJqmbRw+fHjNd99952+zHT812Mo1Msz/yARj7rfDwUMM7hjuNEJ7Pv3SdITT6eHjClflntxK/O0+gOu6AFzSHiVg2ZMs9S2oh54+Fn5PEudTG5qJQVSJA8JPrGI6ZKmqPh2fUrHkhCqb6qzOLt1mo4pNO8Z0M7UPMgVmFVoOxnlwbObv34dJMtU0DDLLSs9+8X2/xXF1s2Gr/QsKnLg2gxE1gnrl+3ZdfTkjIyO3uVI0NwtpSUZSPehXb9MBFFhnFB8qfnP37t2HR+e2bdtqEmKtRcCPyJ2eZ5Q1AiPBG4rKxo0me9gFMudD8f2L7rajKCnS10eSu6mM07GylwHsGjA1pYi+K0uKiWMy3DAkKia7NNm6rrioclvE7IZVrG4ozGbzltLS0syhQ4eGfv755+QoeMz9iJgWfGIy3B9uvNYJpWUMHkQC8/cX8OFnJiivODYVUa6uzIOuv56BACN64IMJdBVwZS3s5QnM2ANHjnw4bOOi4fy7hkxy/tOMkvaj6H39FXRSNcj3Qp+LnT6po6QqPp3C2hlDxP06b5x3BaOWooGLsK0XeTA9UVty8f/fNCbmpKSlpDd3wHWzkBZn0sVQt5QqhAPv38KyivJ36xMWgYzrF8THoyhsVOyhZ+fEm/eSKrSFTNN6yoopCeghHAajgpRuk5a3SfkXUuhLeBPaC108e00hvA0u8ReYYsQ+tOOMP2ICtqlNkB+Vpzqt5cCp8zqdzk/+c/XVg1esWAH5+cfXPig1zbsfmiAAieqyASoSF8B/b3SCN8qMc942QY3txJ10K97h5A0AF4QCXIoKykXtzvxq02cYOiHp3GVtH7oGGhiQTM/7j0FcrpwW248LqRtV6mGUqkaQpwWTApS2bQVjw7xlaRaS4yvukqMQzNwAEdooKYY//lUX+ufFJSVLjx6fzYVmIS3G/6rDhg+hXNfUpbWB0X+Hw/EHihwp+KvL8VM+sv36lA0pBdhB9vWNi78eHwblvHKlkhdGIn+3oesGNbXCB/ORztXdPTpZX6yemhCCx6QcoMs1p2OqZFJak6Mdlz0ra9ZYQBXxt8ioqJ0DBw7stnDhQpSqjr9IWriPw8tvmKFVgIC+cRoU4Fx88/UOUBQd3pxngZKyE/fcylpjfXqxKxf9TecBdG9Fk0xjX9U5CbpLF0tMIptUg0gra2x3c/zlsQ8x4PdhZw5FpiIzBQdWW+GZzP6u5PfnDejVm75zL00S9yx7qRBGrdAF4NQ+r9YdmZs3by45k4Kqm8tP67Dplxkhduy47Vi/aVNet27dhrXy9e2rOVl+6qbUHeRLYo2MPB9/HAn1S24x8PckIh+1ThutouDefzidUjaXRRV2i3vwse1mQmxHWiPfrE14sA6CHFCbAVu2bCmMiYl5884775yRkpJiQpX5hPsXFHJ4/FkvmPxUDVz0Lw12ZnFUGVU4r0sNvPqWGTI3S3Ai4Z400IN4VyhbKhEXSVzDI1CM8HXZvFpwAjAIloX8QOooPpp87Tz5KfXb8uRY1BSM5Je0EET9jXwVSZoStX/0BALAU9OjBmVuJDAn/63dOBZ+cGrOebIs71mfkeLxotbpQHN1w0P13hPRXBMdHf1nRkbG32aoWgIiKezXum04iLtLsvIkjrB+RxlfSj2aETiV9hEcD/Efi4n7oxhMK5qqUbAVeIUsm67H/kGSnFMAbxY9Pi4uzulwOJYgcd9w11139Z80adIJpS1CSSmDabMsMOoeOwy9WoVduzmcf54G056pgVdQElu2UgHnSbojkRdJXj/lAKQWAfw7zGWsp3jGFjeJE4Cx29p4hS3Cdz958rM9iZ3N2OfimVH3kxyZ2ULsyxkoVVWhqEXZdLF7Cj/ktluF0DP+2LK55iY3j43TOI43icjvWNRF0loqTsq/6nb7JymbNu0+k6SqY6FZSAvvyEZ8OLfUfkSdnV3rpZg1q9U6Oy0tbeOxbho5viGxhZsk6SqLYrq5lrCOTtuS6Uk7mIoSnsyItCgx2i2cGaPRgp9RFRUXgyGNG1LhKgFqsxkfN23atAuJ+vMrr7wy6ueff/ZfunTpSX9TuJ+jZGWB/UUOGD3SDrv2ctLFYdJjNujYUcCixcrhWMWToQjn4IVZACtR8uqPMsB1XVEUaGgG/3MfFuxHY/eMa5fe5eVCt9VEP1BMjOkuZVyHn2sqqh4LnrGlov4+RsmxxOjfweE46ImxX+hSOXZtqgHapt5mVeiwDAfah07duQYFht2NFRrX1GgW0tKEWCYDK0Ih15V3z1XjkBxFr0yIi9/cNyFhI5LSPlQb7RTAiVJO+75WK5UKPw8Zj1JzeAP72/oWPkTxtUftkJgsudRL0uEdrvzwNPPQsQ0GU1zHRUmLSc1GWiRtlZWVfejn5zdk9OjRV2VnZ8OuXbtO+jtyf6CCFxWV3JC6qqoE7NwjwYP32iGqlwavvGmC3PwTq4t1IMrei0Mov8pV4XpIJ5f0RT5eLZLXESAL1EUmsNz8xyD+prvhNvvAZAvX2X7sdbrOwFnjrFLrTB2UtZQSAe4Y000GRQoSkpcfbitxt4ydLumHOPAt4CItEggyUFqbUl5dtXz79u0lZ1N1aULz+Gk5ndskxfwFMyrOHDZw0ytVxW2LT/2yw2ofY7UKfN3nYx8Tn0QqkuEPHjVE1w6AJKdoGiDZObbrwG3UCJUzRWLcz0jCxsRgEs2dTnuzllgPCAgoRbUwMSoqqu/IkSNDZs6cCaWlJw+HpFZ/9qUC+3DOHz3SAT26aZCaIUF0pAZvzLLB7LlmWLVWgsoq96Qup+7y73oHh8CPOS5jfV8cCh18KeH6qV7lOQN/vBW3de7T/hdwswgKpfmumhq3DO8iSvzsP0HBrSeXTQ3JrJoWp02QYy0smnkJYEGcifuR1H4f7+j5CBjl7U+OioqKfa38/L5hjFP9R3JYeHldyoavGn55zYtmIS0URUv6Wa3z8QFdgCREfnKn2t0PCF17y+5w5Hjyo/1F1eltgizjXk7K3Hs8p8CipMjfFN0UmrvfcrD1sXY4jVi5cmV6//7956Ka+ASqjMqnn356XN+t+iD71W9/KIbKOG60HaL7aLB1u2QstyY+WQOLvzXB+5+YoOigZ48ht7bq9VKUky9u75K+As0NvbpzDAxiJFm6EIWkHe46nBarpWtClFZTkFwex8l6rESpadhhnzlRO3kzzoVvjcni9p1GacoeGxuLTxgKBRdmp67/2JBLOlPQbOtB69LS0vBGTlBA+ghc8VYNJa5qoYvXaxyOhZ4mJat1Ft2deFS+DxLLN9wPcnirHhZvfx8qaHFBl072J4Gc7JoRAwYMUO12+3yUuuLHjh17RWpqKjvZamIdaNhs2iJB4nQL3D/CDlddrkLWXg7LVypw/TVOiIlSYfLzFshC9VHzQFlw4L4ZON/vKANYlg9wQ1eAC9sC+Cr/eAdVM0pMVw4bBh+BYXo4OcITd9tSRyV8fl6YugpUEchBsqD4StOSQ2i6xiQuSRweBF3s18HhUTqc2rxXC09HvvumRrORVq0e/UdCTMxQSTIlAzecRD1JJUu2gj0CxCvr01LebOiDoIdYmhjZBSQpWALJVlmu7i6ZGtNNAX4vfj0E/7rSWpqJSfOgmUmLYDabd6OaOK11SEj41KlTe06YMAH27t3r9u/z8rmRCSJzixP+d7cDglqpsPRPCXp212H+GzXwwWcm+OZ7kso8Y5wafBqbDrmySXRDyevmCAAriqYhln+0n9eFtsD2NMbcIq3aYhYq9mUKwN5znN3uof8amh7tbCcsQrN73qRs3JgWFRV1v5nJQ1HwpTAccjHwPcFPSNTegdPPT5qqf8k2pq45lQdRPjmqP+fycyjnkZG/3DdQ+g5fY1FMoCRpdYa0KiGkM8ZYuXjx4lXXXmJ9t3fPHs+PGjVKmjVrFhQVFbn9e8p0QwVeyRl11EgHDOyvwpr1MuzNBrh1mAPiolV4410TbNwkGVZhT0APgvJ3vZAGEN/G5SbRHyWvVv9ItZGFeiuGg+hJi0VQ2bpDkyP7K5LUZ+Pj7d+P8jBD6YlAZNijRw9fH4eDp+zeXX62E1ezk1btDczDGzs3Li7uOwmgJ8Ul6oLHMg7hDIQ3avMqJS0TOmwToK9WhdhYVVW1+7he9G7i4JO9/LwCfMiZ71JwrfrQf90Mn+PaEr/4rwBbOFevdHrkTtGUuOmmm7SiF6/5zefSe8TVV18NOTk5MG/ePHAcXUvsJFi1Tob8fQzuud0BV/9bNQz0XyxWUHV0wvREG3z4qQkW/6AYRTQ8hR0pflWhK67xlxyA4d1c+byUf9ZKo+QtdFqxO6nBfEhEbBCXONUhuDYisP2g6unxNBnvVIW+q7JEyy/Pg8oOvZT2iok/rgnxkd9TKavcbURCnz4dmdmcDN6+HaOifMm9J+8UrqnZ0eykVYdadTGb/pDAfsZXbrVamc3mCpqzWCwiJS1Fd+3aODOF4uUdigciYsSZkGUgRVWSARXf+yFZfQ66Ot9pt2+XTOajU+g0O2pyUx36khIR4N0KHn7oQThw4ABJYCd1PK0PsnPt2StB0gwvWJ/ihAljbNC1sw4fLVSM1cWxD9iNVM4vvWGGHTslcHjoH214BSOPbkAhcCMO295BAMPOA4gOAQgw/TNsXtwkY3+CrSfbTzU5vEzC3JZiDFGL6IeT54WcS74mPEJQiAxBIUzU2eIlxkh1dJu0VEXprlAaZwayRVKeuyAyclKpw1FCBnpPrycyMtLXbDaHCSGKU1JS3BfvGxFnDGnVR20UeZP7RXGHKGMmsGFf2I3S2xQuRA3j7FHmSvhfqDGlj2KR78KHfQXz1WkW/KKp2+QuSktFdrBcUFj52xud/K9vA+PHjYOSkhJYtmwZxSt6dCziuW9/dNmx7hthh4fud8BX3yrw8eccLr5Qg1em18AHKHUt+VWGouKGiUoOfJppxS6bF6mMA/EvtvU/Qm1s585OOB8ckoUgT/rlgol1Qui+MnByer+Okv+59jI4qwwHyKHjH+nvkECipIGusc7ZrczsFRpo9krtG9+3gIFexDRWqsmAPOYolWW5AvtRxbGCoxM4V7yt1jHYigcEY1StelxmZqZ78Y+NiDOStE4XfsxKO3RljLUESSpKZuyVWodSEucpvmuMxOovOPPecAaRVtSCgqr88eGLHNnp4yt/fhVaD3sexo4dC2VlZYAzoFuuEPVBu29Ik41UzTde44SRd9ohbaNkkFefnho8eJ8dLohX4fV5ZtiyreG5mom8fstzhQb1QslraBdXZolz1cdLMH2jO/u1TsyszBrb/b1y+05jso4Ii7kUux6lXK4/S5AD9UcO1fGLJwEJnB2RbNMLezVlQ/kPHhglLV6GLFCBT7TSy2QmAqpuExxS3S8hoVToUIxnL8bOsZ/prFSKi0Mlnz0M5E8JcIuPLL+A7z1KSNgY+EeTFoVCVE+PrwBXND2VN3UalXmAka2MxBXqQORv6gPslH3JGh2CORcKnQ+zbfmto/TTLNbzqicgMTERnnjiCQq09vx4SFz5BRzmzjfDdlQHHx5lg4f/Z4cFKGVRCuf/3uSEN1+qhnfeN8P3PytG3i4PudF1Hvw7ZHeFBWWg9BUV7HJS7RlU6yrh+SHPTAjYUHJQWx3m5u7p+3aql3SLDPHxUiagxD8SXNlFibSoX+7SBJtRXaotCp2Z6amRPvw420nObQNHhvfUAmmKG2NAM2o3SDQWjJJmdbKxr84Y1QxtIa3TDsEO4Cg5iB1siQCxB5go1gWjDOvlOlOrZcHbCuCXCl2cPODvNEMr2bdRbtXxaZwRk6s3fNWJ+7WGXhffBVOmTIHHHnsMUMRv0HHJnv/z7zLs3usF99zhgHtHOGDZnxK8jWT2r76qseJ48b9U+GChyVh19FAbPQwiL8pdT9lTye51EcoVV+Hw6hUI4G8+F8hLvPf1h/tqohacfE9aFBoSFXc5ktVYvO7+8FcJsSoB7FNwaLMPQVnWXp+9aqinrQDo1IB7yWv/lOM9CMZYsyTq/seTlkPX5ylcX1FZCt+Fzvx7YYwkzvno0b2/pJqIA5qjgSdA+Hu6LWss/9xLCh8kHNV3Vq36iHPfYIiKux4ef/xxeOGFF9yKUTwWSILauVuC51+xwM5dTqPiz/nn2eGTRQqkbzLBbcOc8NxEG3z6hWJUBDpUcmoUQ6uNv+e7HFXjQlyJCPu3P6vVRlVjsMtdb3hzgPcwDoySWh7laM1KQRN/AJdCQ+Tg21vrreiBvuNRS9jfajE0Fpoldc0/nrRaTUrJSErimYkzj925ajtd5ZlGWHWImK3b88d13obTnlOvKDJX/vQycJ9AuHTgICOmPDk5GXJzG+4TSyrgR5+bYMt2bqiKY0fZDefTqS+aYcjlKoy+1wEXXaDBnHfMRkkzDxYv/waSvCiX129IXuuLXK+kNp4fQFWNzzrJS+JCdztxpEOH1WYQ7wJn0VRXEVwGfDLA+zGJTQOX1BMiGKd4RrdJi7Kj9I2L73jUzaszfbDa4zJoyO0Voszj3zQC/vGkRXA3Wv5Mhaqp3yhceQDfhmuVB6Fs0dPQ6pYXYPCggeDj42NIXQUFBQ0+PqmLpAaSAf7+u+1GHvq4aA3mvmeGDSkSjLjNCW/Prob3PjLBt0sUw5h/KneUHFqoahAZ7CkRIRnqKTzo/FaoNprOGvJigrHYRcP5j+6kkdmbm5oVN1d/ru4zZTEN8Zfbyl4KKsxyD84Y+RJexYB5ZEOKjo6mTLz17fa7BIjF2DicFnSvWmM/aZwBhquPAFcFLGY4eNN7smEd65bbQdPcCthubLSQ1jmA8FfztqG09S3j7CH6rFeXQvn3zxsxUf+64BKYOHEivPzyyw22cdWBCmS8OpfURQnuvMUBSU/XwIKPTTDrNTNc1E+FW1FlTIhT4dNFJli2Qgan89TphdTG5ci3mw66yIsCs+n1bKiUjcTQtnXRyTn2wKTo0B6drP+rSo770mdS6mbaVhsXm137t7zyaevXzEf8CU7uUb1DWZaPUA01EK+uX7/+1frbSBqL6dzZn/sEhXCFB3FJBAJVltZZELYepTvRHsmSqsBTloi6aJUcnJlO6unfFGghrXMEOHt+hx2LpC1jOKsHdkHF9zPA3+ILl19+OUiSBNOmTYO8vFNzhiap6/ufFNi8lcN9dzpg7AMO+H2FZhjpKexn9L12SJpog29+UOBNlMTKyxtHLqLVRkoBve6AyzmVUkCTwf4MjmusAqH/fuky0E4mdPp4m25EcpjIJOmKimkJX3Pd+dMGbePWAYl/5eLynZpSlMT5XE9LlDGddTeyzNdCCLHu6H1q/SJLa/+OABV5jYmJ8cXXEBn4U3gkKvCi4A9SnLJccfT+pwMtpHWO4ECN9meoN3uGMTYOaJYUgjn374LSBaMh8O634IrLL4PQ0FBDVSSJy1M/rvogu1XWbgkmTvaCDekOuP8uJ7z7ejXMnG2GRyd5wb8HqXDbMAdccpFqFI1dvko+bhkzT6DX5q9fSmpjgSsge1iEq/yZ35mlNuo4i/yka3y1O9Ebrky5QDUIL5SoUIukzIiXrAeqpiWkori2AQQ7hK+DJiTFzsd9vnG3EYY9y2q9ov6dkVzuD2vcvhBXNlOD0PrGxr7EJaU9Ho7rQn+jIRXdGwMtpOUGKHPkg86eobLF0kaqseX5Td3cLLr8iRA3N696+71t5vgE+Kgc4BmgDL4IrarEsHH5Xf0kxEb1h8mTJ8Pzzz8PmZmZp0RcBJqfv/rWZBDYHaguPjXeDkt+0eDjL8hwb4ER/3XAk+Nt0C9BNrJHkFp5iqc8DHJSXb3ftdo4sIMrDXQckphfs9RMOhpiK3LVO+/Nyd2X+OqJ96TV6cemWskfC8lBoDzJyP5Ei6btkByuxs9XuziHkck8HTwgrcjIyNZIeH2OWItk7J6EqKgN6zdu9HhZeV1a2qa4uLhbnE4nx/7jkVd+Y6KFtE6AQ8/EdTCb+aWPmawXgQm64yZF8/Gaia/fNnfbjoXu8w5UZD/U9kNu8roPO6pf3XaSuMpRVQxgE6FvwkXw1FNPwYwZMygZ4ykTF/loked8XoEFduxywJ3DnRDZ2wZz3zPBc9MtcDlKXSPvsBupb8jW9cPPMlTXNJ5MVK26Mqiu2+9yTr2xq8tlQmq+wGy8JdoLRbb839xR5Wif6ukJC3QhluCz2MQEtOUSJcZk8fg1RWH8lfGEMY+IAvnQcIU7cisbIJktb14QH/+pzelcTAk5PTlmamrqydPlNjFaSAuRndTVEiIF3SQ4dBYaZEgSlSZjl1q8JaqjSJ2GVlAk7AFLqp3aWr+THK850eWNA/tzHwn/k7k8/F0QOqj7s6Bk4ePQavhMiI/9F7z44otAlX3Wr1/vcazisVBUzOC9D8yQkirDg/fbIPEJG3z3kwKfIFGtWC0bGVMfHWND1VGCF161QHYuBw+TUhwXpIAV21wqY1oRShhIXjecB9AnyCV5nebAbJ3pfIsnJcS2ZcPHSAdAvyGp/i6t8ycBtmCzkO3+spdpIIpdj+Ju7TTQ3VbrCEhIRQl9+tzJzOZZAvsz3gYysFNfvowxfpHZZB6F0thlzRE/eCpoIS2gJ+njDZwN5lSVRzZ8WMixiWYpqmpdt051EAd3YmiikQHyjAXZUArGh6fiPHvPEV+gRKVXFEPZFxNRVXwCuvS5wnA+JXVx+fLlYG+EFPiUBWJ9mgTjJnrDPbfb4br/qBAbqRmuEJQx9ZILVbjlRge882o1fPKFglKXAvmFvNFURjoMlT0jtZE87EllHNzRFSYUaDltNi/dIYTb0gjlujrwVM8Qi3dct+qp8d6Pcqtd45Dz8+6UvTct1MuQxD6eoETvEULqpqpV7qWprWuIy56WGx4ePqJNcJtBjLNrORO98E4QeTm5DuscDk9zdzQ/WkgLjORClZ0EbMcZWcWn/IGmw0KJiZtcpcWoNiJoOOTntno2fYM+qblbe3Loggceb1VNK9sPFUtmYZe1QXvr9YaqGBAQAN9++22jEBeB6i6Ss+mmrTLcdasdnn7MBou+UWDhlybI3OJlJBq84xanEQr03kdm+GOl58kGTwYqwLGstlo2SV6XdXS5S5ib2FUCCTiz1Ja31519tyRFmsqSo6+QmHQ3fowUjPkwDjYclHuvirb+fDDJ+kFiok4Oditq/xqE7OxsKsT6fdeuXX9rExDQSQM5EBneWV5TntWQ9DTNjRbSAlcllPKk+C+YAhU11TVfmi3enVCnIJsCmXUF4hfVKeYVPhrtXTHFeonKRE7ws+lbztQMkCi7tD6RXKGV5EP5d8+DcNRAp3/davhxEXF9+OGHHicSPB7sdga/LpNh4yYOd/7XYYT99I3TjCwRxl9IqgAAIABJREFUc95GolohwwMjyT2iBvdTjKDsvALe4DjGY4EeTgkOyT8LkbwO4qjNcTmpxgQ3WQ77ChDaDHdUQyPNd1L0pRKTZ4NrRa/+PNMFH98FFoVdWPJM1IOBUzY2Sprv2nQzpz3AubHRQlq18E/ckIUvc7SkyCDGxRjGWGztV/uRtBYJSffzDZBuZJw/JQl9U35i+yvBjTS6zQHBIexk41GvKYPyH14AvaoEAvqPgCeffAICAwNhwYIFHqVuPhGIgKgCENmwNqDaOPIOB8x5oRo++MwMX6Hkdf9Yb7j1ZgcMG4pSF6qO8z82wS+/Nzxn1/FQl4yQjPUbDrgkr2G1fl6tvRrN16uc6hVUVth+dmdnqigtc2kUEEG5QMS0r/Y9FX7qgH3wGrOXUpo6io/0xEZ2rqOFtI4C4+arsRNf99cWYcZZ8TZFsAdwZo4EigdjvK+PPZgMmmccaWWN7dDaW1bOd2dfgSpi5YoFSGDl4Dv4QRgxYgQEBQXB66+/Dvn5+Y3WJlpDW7pcgV17JLjuaoeR4sYa4/Kc//AzE6xPleGGaxxGXOMF8ZqR8nldioRSX+OLQiQbk5tEVrnL1kV2LwrODji6Vrl7ILF0mRD6LsH4Fs1uW0QruO780FIVIIlWLLw2WdtWTRePgqpnGZ84dJEk6VokrVuwr13ZvV0ceaO7lZfrn4AW0qqHiqd7B0s+3o/BEcVOGPnQkKqYIgTMYAJ8BBfBVeaDdk9KB50ueEkmKg7idlS/sFVA9brPQa88CAE3z4Drr78eOnToAOPHj4eDBxvPHY2M7XtzOLw1n2xdEoy53w7PPGaD2CgZ3kCV8UWUxv5cpcK4Bxzw/HM18OOvCrzyJlXHbhrzeRUZ7Pe5cthTjOP1KO9c2M7j8KC1QtUer4GaPeX2gzWeSENO7ZDOoHWRocbrMN/v6ZT6hYZ3HEyyZlpMcB4DdhEoOk2WLaRVixbSqgfdZPaTmBE8Sl7AuagWLmeaWOioLlvVanrWERHtDS3h1JTYM65TlJlJlBY6wJPfkcRVs/EHVBUPgf81T8HFF10IH3zwgbGymJaW1mh2LkKNjRk2LJKuyCD/3xvJc16Dt94zGQVlbxkpw/AbnHDjtQ4YNECFd97H7SilHShqWMLBk8KoYC6gAYfeg91kcofZuRkNOe2Bqlw1MLj1GqSsCwWDw5a8/Y/H+viZHP4mby8rtozS1OhM88w/61xHC2nVwyuQnvMYxK3Enny+pmpP7MxP/5lmz7oQefKheaimh0+Ns0rtOMtYkTljkH0Pt5gCOv0HR2B0gw6AjODYvRbKFieB7+UPQ4/uFxqkNXfuXGNl0ZOCGe6grJwZDqibt7q86R972A59rRp8tNAE8z9WYM16yfCop1jGSy9RjfQ4a9fLYGuktS5vb29IiI+HgTER0F9NA5/izSBUN8lZwC4d9Glr8vN/v6kB5y5/Kjqwa8fYKKfT/q6smKuQOMvJMF+YGN3ar5V0LwjvBIkJsql2wnP9otaoHvlnnetoIa16oBQ1xVP7PGEWStAhvSyjTtyn2c8nQLpighJ7E1d4awt4q1XT49dqDucH/okZp5Y6oZHAfNu2Y5xRMGvDrDNAPqg6OPamQPlXieB//bPQrfsAI1bRz88PPv7440ZxQj3ifChikOPpzl0chgx2GhlSe/fU4Kv/M8HCrxRIftECF/ZV4a5bHTDlKRv8+ocMCz4xQU5ew327KMdYZGQk3HPPPWC1WqFtaBsQB/eCfctSqFrxvuEScgLo2OpVui6mHrDl/uZOypmjQRPfo75xt0mMjdSZ6eYdOezl4HYHJVqJLk1KsADT/42NvMS1til2aLr2rP80z7zWz3W0kNZRCHl60056Ja93Y1l6clycb6CchCrEQNxCQhcpFFRgYJBkMl1TOS1+3Cxn6ormzMm1/d42fr5+PhRv2OOUD4ZsoBbvgbKFT4DvZQ9AG+uN8Oijj0J4eDi8/fbbRqmyUw39qQ8yjNMK44efmeH3PxUY96AN7r/LDldS7cWXLPDHKhmWrURV8iYn3Hy9EwZdUm24R1DeLsqW6u5d9/X1he7du8Ptt98OAwYMAH9/f2PVUK8uA2fRbrBt+Q20ihPa8JzY1I9A1Z9997W8Ak+zLdRhHER2xtNej28jJc7Htw2untQ+cbdx4tmQkjdGxNxmEvq12Mk6471Zmaupab0acqJzGC2kdQKUJsadJ0vsdXx7Qa3fE/m5pOLITsfPUUYubwaPPAIxZCRtlpisHaM6hvgGeFNe8VuhEZ2+tfIDULHkZdBKCsBn4P3GYG/Xrh3MmTOnQUUzTgZVcxnqn0rygssvVWHY9Q54Y1Y1fP2dDIu/Mxle9ZQtYjhuv+c2Bwzor8LCRQosR0mtqur4l01kFRUVBVdddRVQYVvyRyPoFQfAtmst1GT8gFLWbyC0E6q/Atl18cFDjjFUBSlx9ilcqCwoOSBJ5xSBeVsrH2+1eFLvaSHJmwtrJz5atn2zbvcWwvo7WkjrOCApq3KqlcolXVC7qUYXYho4nYtSIDMrXol7GFWN/ih1DdAVlQSz005a1MacsWH/xjaMhr+qpDQadFs5qkwfGD5dvpePhSuuuAJCQkKM8J8NGzY0qsRVh6pqZuTiytgsGQR17ZVO6Jegw/sfK7DkVwVefM0Mq9bKrqwSE2xwMZLWW/NNkJ175LIfqYF9+/aFG264Afr162esiOL9Mhxq7Tv+hJq1C8GRk274qZ0UAvKRU18lwjrV60tTN2VHqjHPyWZoL0vSWpxm7vLy9vLZ/3jsmGPVKGjB39FCWsdBcWIUrdwMqf14QNO0Jw5ppZ+t37LXeWVkTA8cFPfXftdsOTTJDpI3IdwLB5WlqQLrhGqHmg1fg166D3yHjAdrbIyRIeKNN96AJUuWQHV147uqERfu2cth9psulXH0vTZ44hE7XHqxarhMLF8tIal5wRWDnHAXSl1RfVwZVH/6XcE5xRu6desGt912GwwcONDwO5OIrGyVYNuzHqpXfgDOvI1IVrQY7C7pig2C1TSsQkg9VCXH9Y43xd0mgH3AmE6uKXZswZ846cT7BvJXDz0T9+xrcnrh2Z7+u6nRQlrHAZeMiHhKzOZEiWLu/qKqTwO9LKYh0bFXYydLxO2GAyfqDaukStZsUfK6UFdxZqJl937QRDHBQnOCbftyUA/lgv+Vj0KX3oMhKSnJIId58+ZBcXFxU5zWcI9Yu0GC9ExvuGO4y3P+7dk18P4nipE9lfJ2kc1r7AN2GH2fgISE84BbHoDBgy83bFauIPEisGenQ/X6L8C+9Xfc5DEfELGsK64uKu50CtdCOd/btfGfhg9oECgQzRjviw3crwu2kDOhMeBvWbxE5wkQM+dQUvffghK3l5/C6c5ptJDWcVBSVZkT6h9IS0ltkQs6tG3jew0HPqi2Om9d/c180Pj7aebMiuaq1rMuv3BHv/bhn6AwEQNUPbgJoRbtgbJvpoBvZTF4xV1nrMAFBwcbxLVjx44mURcJFMf4/idmowL2TUMdMOoeB8THarDwKxOsXmeCpSsvhogeF8LgK/tDq8BI4zfCXmWsCNo2/gD2XetAr27QAhytDv6gac5vTjWMpm2odwJqrBfhW198vcq1lflKTDyBt60a+5WCOu0gDlIfi+z/Cn45/VTOdy6jhbSOgy7Ts8oqkq2vSRKfh51sOM6M1+DmIHDdMxqdRboQkyu0fd91gG7elVOt/7Zx28qQiZsLT2c7adk9647WH3i38bkLB4G1qc+nle2Dih9fBq04B3wGPQDXXnstdO3aFWbNmgVr1qyBBi6qnRTkJpaeKcGebAus2aDCIw84YexoL7j5lkcgKuZ6w9YmyzIIpx0cu9dA9aqPwJGdBnolkdVJyZSs8GQApyCHVrXb9qm6NoNz9uX81woLT8n4bkAaCC6fZCLCOomYStL2qA3crtvWWmdiMLSQ1nHRQlonQKqW9km8ZEUlRdyPfYpCY8pQmiCxPQXVspf8nk7fvg/F/rZt/O7Azv2mN1gWVk2PfsRnYsYJnX0aGxEfFpXnjus8EaWtTxjlh29iULWfyuXvgiM3AwJunAIx0ZHw7rvvGgb6xYsXw6FDTefArQt/2H+wB2TuHg6XXz4YIr39jSSHFIZkz90Elb+/Cc69KR5LfbqAT5xC+8zM+ONIH2YdxAudZ+etJ7th4qxTbzcXsEEwmIXHW4cc2opLLBgb2QYnQyJJ+iMpWcV25Duc4q1TP+O5ixbSOgGoGgrn/IucxN6/+kvmLoyD4nSqeZmwcd+lSaAdsvUObx/qPww7+YPgSuI9VBcKxZB9eLrbWlSjrgz1ll/CZkzAlgSfjnM69qRA6WePgS9KXOaeA2HChAmGnev9999vdHXRYrFAQkICXHbZZTBkyBBo06aNsZ1iJ22kBm76GRw7/gTd1iDzopMxUd7lpZyNi4bzER0hzNRvYXaN3ghkVQfvpzf8iC8/Nt4R/7loIa2ToDZn1qHaPwOXolhVnhxzHZLUGCQICqauy8DsJTFGauRpJy0qbJF3X/hbzE+EY7soqVyDPePdhwBn/mYo/yYZfIp2g/fA+42A6x49esDMmTNh9erVp3wGcl2g440cOdJwYSBfMSqHRquazixUA9d8CnaUrPTKQ+D+auDfsEYTTqNgRK2Xe6OFaJUnRXeVTabbgFyu8HYJpq9Xa/TvApMz9p6p+djOdLSQlpvY93T31t4mny4o5wdWTI37HwN2BbhEejLi5ODfEk0Xa0GFrc3Vxo7vZB/KHt9+hiLMcUimCaflpChNaWWFUPHzbHAWbAW/IeMhqk9vwyXinXfegS+++MJYXfRU6qLK2OTBfuutt8KgQYPqebCXgj0nHar+QPU0Ox3JywYexvSQZ3shHioLX9M0XV1Wekj7PebDfdX6Sx418YQgH7ryxLiLkbA+BmMxBySKzGbAb5G9+MSK5LiZ+x+PfafFN8tztJCWm/Dz9b0FiepFqhsMfzlyHsAR87OuiXmztLQ/k5JAlCb2DiydGBFwdFaI04Xwlwr25o/vNJuBRNLeaSvpQB7lto0/1nrQ3wcBvS6DMWPGQJ8+fWD+/PlGAQ13QIHM0dHRMHjwYMMx9LAHe+VBsGWthprMJWDf8ruRmaIBsKG084MQ9hlhs/dtqJN0yJVBX9CQwx0f+Yndg5kJnsK35O9X9xzolbvKg7HpvoFcypsQ/saZFnx/pqOFtNyEECwDVRVKv1zbAfVVAvhs4aha6p+0/eD+xN4+Eyabb+YSu1aYzCWV0+Ln5zjtayiV8+lvLe8Gp5Gw6kA+UORlrv3fVNBIXbz4bsOLvkuXLoZbxA8//AA1Nccfnz179jTChS666CLDg91QA5Gc7DtXQs3azwzJisirgbChQDbfrmsvR7y6L6upVTMfxZtiDA0+xL9UoYsf8JHIjOsX4/Ppi9ssKHX9LzAoOAXfL2vKtpxraCEtN7Ejh63u0Qk+Qyq4SRdimep03Pvrls35ZAPREwGKpyf4oQxGZEF5kNphhx0SLptmLBrO5zQkG0BDkDqKKyHe7fvKTLnzdJzveNBKC6DitzfBQeriZQ/C+RHd4JlnnjHCad566y3Ys2fP4YwRJpPJIKgbb7wRrrvuOsPALkvc5We1YwNUr1yARJhhZFdteEItUYDU8UhlRfWPPd8rrjwttiSmB+PwIheHbFUTDxwoqtikBDmZBSR/b8l/iCTxZPyuKzDpsqyx3VdHzD77Ckw0F1pIy03EzV3vPJQU87hFUQq5gF8yYHPBZdF92ldNj+thd+hbQxLT83C3ieXJcV/JkrQEya0tY3zyoF5RX4JR8KfpEezdobfMJPIo6nw6znciCEc12DJ+ALVgG/hc+j/wi77SICYiLgq6pkKxiqLAJZdcAjfffDOEhZG/rnCpgXtSoCb1G7Bv+rkhHux/bwuIxWVO+5Je8w5U6vNO/drcAdelHOCUKBBMLz+bmlYvKwTptR9UTI0PlTibiewZHuorU23NFtJyEy2k5QGCkJj+SOJPLEORf7wW1U0yydNxSr3IJAMVFnuH9snTnBmdOc8g72b86K8oMiVzOy2kJTl4JZhYFbDTrxoeDyqqiRXfTgV1/07w6fdf6NC+Mzz77LOwa9cug7QiIiJcaqDmBAepgeu+APvutbWrgY0DvB2hfsKiNNoBj4M/BnHZOjh2KAfuuyM39ZPzO8YmMonH9x7meh5FD0X6btmy2RZ9QVcfk1+r9rQNJ8Bqm0ltKVrhAVpIy0OQ7xaF7FRPjUeiYtfi2zKN6yl134fWaGYkjs51n5mmn7Z7fEDNzQ41h6/HEfIvcJU/OyNAql31yg8Np0+fQaPAq+elhoHegKaCWrgNqv6cD/btf4JWvv8U1MDjgV2scpUq3DRZMj1X7jXrDZzBDPxYFBHWZ4X/M2nL8xOt68k8UDHV2s2ng2VeQgerl3A9m65guNGIta/BzsrEpmrYOYgW0mogBGNBjJaxhfhFVFQaGQAOPtnLzxLgRYUxutbuVu6QYN3pahPFx+U80gXVUyO3VrvTdd6/QxQgUVD6h85Q28fIr4rCapzvI2n1HQ5esf/BkS6jGrgYatL+D4S9SQsbtTHJ0g34OqOpTlCeGNuTSzAeXCXBwmRmmlWSGPNcYFJKBtk8napWI5k4ZQSJYQZpiWwh2Eslh4o+S5zVktXBE7SQVkOhiw9BYleh4N+e+3qHVCTFhlr8vW9DVeTB2j007IoLlm5M39eQPOINhaapxZIkO5tRP6zShcBhqGXIkkzG5n71vyR/rep1C8GW6XIOp1xdTVOx4khQSXhoQtJiMidfLJooSNWj4OdrFJMUXJ4c9zRKYX/qup5XNj3uIVnnNzMO56lCX9RqUvqiFgdTz9FCWg2E0MRGHAi52DkTZG7+VJhAQaKIwK/IqErD8GehqnMG9ermUzkt/hYOrL/Qte+qNecPrRMzmyyVjZlLtMxuaarjHxPIQ0hG6Yyx7njdK1Wn+uUhZ2FBqKXTfCT2/aDDUhyoU8F1b1wpY6pPe87ELhtHtPdpjER+x4Kqaqlc4rdwoXdFAsNrZVQ1uj8+9zllyTHPZ43tvihi9vb07KSu20IgwD8P1NIWwmoYWkirgUiBtB1WETseJasncbBGMMPhVDiws+4WOnyBg3R2QFLGvoqpMcNwn0fxJx2ZxIf7SJYNFcnxj76kpa5tzGRvuQ92DuSy3olJ0j34MbCxjnsckL8CBY4bjrZ4EdOrKqpf1Sq4UC1cGN7lOCAXDefv9gJ4n34Q0KFTEN4ncrZsqK2N/N0kaHjSRa3atq9R1TByMTk/OLaVanJ4KSDbsjV7Sp+kzWvKJkVtkLzkybjL1Tip9ZGAv9W+re9/KpMTfguGwFU+iSlbWtIoNxwtpNVA1AZTf1maGJcum7T+mpBCmC5KcVSktXo2NZX2oYG7//HY7738HbskyTQKiY38p/6FhDZ2nNabUiQ3yhLZnjFh55vN0mOCSf8GV6FWd+vKu1QZz5BLXuWoHq8WnOmMif4aOD6uX1m5zru81j/NcMjKezj8S6YwytZJeb/qJIyTabE7wBUHGCGE/gEDTuFJF5zkN8cEZQjtt1BvNM/z0okJAed3ihvOGbvcBJZ2lNAvTPDVRVOivqXsHwcmRT/s623aDEyMcxX8ZbdwCYZqHJ7Fnzd+kv1/EFpI6xRQK95n1f4ZQOlCKpsSd5fERd/ipNiXQ2em0cBLQYnrRYkrZAwO4pRXHoyUJI1CWmZFHo6D+U7mfpA0xd99jSP5//C1D2ficirUAScmsFLcfwle8wdVlTUr60hq9+j2P0XM3Vd0sri9Aw5tV1uFf4S3rIAx/Ufk1daMwW14XjJcH4u8ioRT+x9OBuXMJCKqnPpvfgq7n7lyhnnYb0UVE9o7nv3mxJB8xf1IWBPhsFTLUBNmV3kxPrQ8Of6RNskZKYVJHV/0V9qWI7EnuohLFGuM/dyY7fgnooW0GhG07F022XqHxOFV7KRVZkl//69vpb9UEwb+jJbOGgGpCVxpe0knJBzmLmER0f5u17RJEa/mZe25C8zVpvazfL2Vu7Bdz9d+X4X/FTABfwihrcDPOx01eqFDdZQc7VHe9Y2CA/obJz+pkYViePgbWoeKd/eml1Z2iATJJNrOl2TlMcY4ZUHwr9fCg6oG4zrPyfuDzoX3NY1ec0Z1eZ97607OOC12WISgVC/a97qu7WHMdCXnRoogMoiTKrlLaOItwUSaEKxtdaXdveBHN3AoKaYjThSPgBEwL7JBMDPeu7r03BfJEnuzfJp1dIekgpTyiaEfgw+PQYK+Bme5KbPV9E0t7g2nhhbSakSUPBITwLl4iNLo4sefJE3Npu3ZSV0trZXg/4IrcyWxwg6nwgyDMBEdTcMNraMXfEGbYPy5J/mzKoQuvur6Ss5O/RXjM3lo2zaO4G+GhHSKxMY5BOjLi0srv41+79DfrOWn4lHecaERGFwT7vpI2UL3ZN/Dx8utOu1ACWpSbR6wUl3ozxfbta/ryPFwYPPcPZQV9sXcMe2XcEVurZbmrQl/Tzcip5OQ2O57JMwbGB+PZLZYBfuTXV7dl90Uxm5FluKQhCiSe42qO+7lqllhij4Cyfc63GaEcknARpcm9n40BdJK4iD2bbzahbm687eWohWnjhbSakTYZOH0ZUCOplbKcKqCYjOMtWHR5BoxAlzZIZxIDN/ZnQdK9ifFtqmYEjdMZ2BOSuKvNKRDc7OFCsh6slp4AIS+9OiNUQv0KmzrA5YSUHst1B0dPG1IA0Gkk/1Q288Us+UyJN8rSG112J0L4uYWHNdxK+y1gs1HbyPSz3sk/BMuwY26EO+Gv1ywtzFTzRwBxuqIMJwzpU8KpHzf2dk5KRj8lzBFnsgYG4J/l4Ji6jrgKX0D7reKdm4xvjcOWkirEUG5kcqmxn2qcOle7LSXKQq7t3snazD2cSpXTzxAnX2lcOqfMoePj68vfwcljP4SPodHTbGscFTHN9uhGuXJOctsZSVBllbup8ERsHJ1Yd7uY/mOxc3Vm9TD83jo8saB/bljO6UyjmTv1F8mlbMhx1lTmLu1X/uwh4tqtJVhJ9/dbfyRxOVIiD1fAlEdkJi6V3PaMySThSQ8JC0+K16K8yosqvh61msbVzySGPOAbJJWMeRWlMZaN2IzWlCLFtJqZORvTF/VOTruU5yNh6Kq8kK9mgXkIrAMtZUnfRNTtoqp1msZcFIKX0SVsg++fzKgU9uupUmRzwclbc51V63p83pJSf64LktwwFN00Ymq8dAg22Z3qtNOV9YJd0HXmv9Il7VC6N07vpqd2dDj1F7XTx0bsW1FSZG+VsX6EGfiCRAsFdXQy1Gqy6meHv8RPteHcJfzmMQ+aBfqt3jClLjPcFIoYiAOkeGdA2uGtETnPlpIq5FBqtWhpJgnzSY5hQmWIJhhYN4PGqTUSNVfhzztqtZTqe7/1RvabJRk7qU67G/JJvM7SHT3mEzmkJLEmCdwl73unrNGd3zqzZX+OFCuh2M/U0rvuchp12ad90b+Tv21RrnURkWFpq6l1YnmbsfR8DbJnfFlmLH6xyB0QmIk1bvcpjqcr0ompQdKylQ5R0HJehhK1FchsdGqKi0GrNIdWm5ztv1cRQtpNQEoG0TqqIQ55wfrrRwmzSwBr/xlS3pFfQmn6pCP7N+GJ+KsvKPcob/dysRIfbTg4LhRMUnmg0m9Rgcnbilw53wRs/OLdj0UNsGsyGs5B5r9Ox+1yz6npr8XPic3XZ/TeNfZmOjxWu7BrLH8l+Zux9GwO6pzTIrvZCSn2yg0h5uUMRVP9372ZdPWvRMcvceDbH6McUaLLFQOzAeJzYfSOePn13NB3dtix2p8tJBWE4Hyb+FLUd1nsiEdSuruL8DXF2pqKtqH+s7BQTAMlcA5/r5etI5H2SzpN+SYmiKBElKeHN3OZoO8NsknL0l23pzcXFRdXr57TLslsmyiVMtx9b5etXVn3qrwxr3ERkfEbL3Zc0qRn91VMT3bOISXSa4S1XZTVdVrk3Z+e8398EP38Nh5KAzexX3NZaMdvaf5JmZuwf3vvbxXzGxF4UMFsDBg4oBeoy4ISN64o6Erl7SibG1v9QI/8FYtak1aWkse+fpoIa3ThC3DuSk8Ou4JVCPMuuK1GhgPBwHLOWNjwOUUqgoQPwqH/cl9hxy72ob6TSDnRV8f+L+SZ6KeDJyy8aSqRq3bxJbccZ2flDgjPyKKhSzUNX36kO+bnxDOBlzapYc3CK/xJsb6gS8U+EPb7EentC3ECYZWXVOQlP4Dgo/1VswF2Uld56H0TLbC9Nq/w9AnN7wN0dHRrSWZPYwqfReuy4UJcQlrwQ4rUram7GuJV2whrdOGsMg4K2NwHwix1OnUt0sSGy8xcSFj/FJwWepXg0N7yj9p89b8xPZejPmRxzz5e11rtph+xdf57p7rvdk5v902puNWLw4dkBgPdXwlZ2cTXdY5g7wJ4cYixoGqXNv5fjE7OOP34mPpbyyhcApFEhUCOPmZ+TEqYS/g6RBTkIITyqLfdmwqaKzFDZKyEmKtd2I/eQyJ0sQ4CElAsfCCNfEx8eT8u7IxznM2o4W0ThMEx04tpAKkp/4mWRqggboLuExhIPQM9mg1+r0ByRlGwYWKqdYODNh1tT/VBBO2quS4W3HwMK1S+6HVjMwTZgiolbjy4DRlTD2bkUUVwlv7xAcGt57EmNgq+WuT7BW2zywB3l0YiIcNO5URpM1aMVclaBconTawqWZv81NXxljH4pZPTqUdsbGxPibOo+Ot8SOZYfg/HJJFeWhb47arGRd9E2ITRqRkpPyiN9AZ+VxAC2mdADjr8bi4uO4yYz3os1PXN6Wlpe1qSIfxfyp1Xekz8UMVb3iOcfacDAoNBprd8zVdf8RvcsrOeipFt9o/oDAR5hRbwSTNxA+DZD9pYfGUPsnYtG0tqkLDkYQ3cFxiZOf2oX43oUTo4FLUAAAgAElEQVRDiRtDhBD5vKo1C56ZVlGWFDdTVngISlUocRn+dXvBCEQXFPxMxXkp2wQ59ebqQuw+lbZYrdb2ikThTHAHktPxohtwymKhkgzJqD5S7vkdp3LOsxktpHUCYGcaiD1lCr41cgMrXFqbEBs7E/v70oYQV6spG7KLJ/V+ysvbq4C5bFkUyvKZTXUs9au/IwMKnyFbicUIbJZZCHORGA4UcZOZmXMKH42mBHstBtoGgKSrRyfHDAWJ34M3m1JTU1QBcgK/wjtAIzev7QGJqaUl06KmmIU5HJ/HZUIXC/HeLwXGA5HcKM1OR3w23lyIX3JVe2pDVwkviIoKU8yW5/AtRUy4k3ang0mSqD+2kFYL/kLXrl0trYOChkqMk+xzfr2vLmNcIjKjmW5XQ44dkry58OCTvWag+lGAcv9zAsSeuqSA5MjorSh3ciaNhDr1QBco6HGSBCi5nyoELNWYusw3ULqmanr8FboKXzn1ij9CknZWtEhe7qFdqN8DSDpP49sDQtVHoUjdHlWvaUhbHTlXxi0azh8kG1XwpE15pZNjJskS90fp+BbNof5fdZW++s/sDFtCr86KpSpAovcNtWdFREQEBAcGkmpJ6bGPJix6lnRcVv87QW55jJ32DIpnElpIqx66d+9u9vX1jWwTFEIZDyj0JuCoXcj5sT/XdSKyBpEWIXjGlgocGHOv6B71K5eYUWzBsK2E+o3GwfQMGIHVohj7awXj/ElwdVqS7BYX7q+4nWrkVU9PeBE/j0B14XYu/JYcTO5DGQ5anBmPAZoMzDILFSDp+aqa31kyl+MdlXRNzHI4qpbLFpNFEuZIZqT3EcOHRMX9jOrjYpKmUareUDYldorE2VuSSVnlq4jvB/v0frBVYmYOHbuhqbRdBvfYwThx3Q1Hxo6qyExbgInNSFA5zJACGa0CU0gQ5ZZfWF5e/o82xreQFrgkq+Dg4PhA/4Ah2GGuQcKKhOMnqPPCnhxIne5UJJva2Xl73eegIC8yttJKIq0Ylghgz6OU5YczPElZpL6kCs05mQjLlTUisA2qKvRTlMRYf0XjnaGFtI5A6qgE5bww9WIvxTKU0zMV4Oyk8Lc1p7ZCkuRilK5qzGbvAXgfB0JdKmhgAZzDmAmJkZSoz7Ab7k+K3eBn4vvANYlt5HZecbxzuouwsDALMIlS8gTV22zHqelzTXO8krJx4/+3dyXgbVVX+i5PT/JuR94SEhJCVjuOLT3JCRO2YSnpwrDMTKGdwsyUzgwd6LQFhha6BEOBr6U00JSyFVLSmZYtpA37kkxblpA4lmU7juMkmITsiZ14XyS9d+ccLUGWn2Q5iYNl3x8UyU/33fve073/Pefcc8+ph7bRfBA00oNKOlVRFOvAwMC25ubmCb09aEKTFpJVfm7ueQWT8m8koYiaZwBVWYc5zQChvXdUVLFPowdso3pgPWEKJmLAWfgwNLtis17fhBsM89VJS3A5noSIdQeomD/u6aQeHHVBia0wa2YgMNDzMGncO5FDocybRi6DZ/hL+DgNJJReeFoWRug0H2dfoYb4vkHJJ0Rn7QrH+GeYglDUguRTCp/PYRb1m0er5v5o0rLmzn0H6o7NPdP5jMA4Xf6el7LvazrpVGQwSWI2p8/FHF6v+/urquvrB0nxYefSZmICt9udzwW9ThDD1zsw8ExDw+jlHxgrmFCkhauBZWVlOTBjnckpv6rQno/bL84m0fYEQTDZKQ70LNNKBNkjmDh4qq9tBanffxtxrgIeQtVzrmD8SVBX0KvdJwR5Yned79kLnjMCh39YXpSRof4HCW3V6RCG/vDr9d4XluRNsfbe6/rXKcXZ6EZxtqra9P8m2saen7ge6eP0L3/21hweaxulRxOHbndkZOXx5RgZFcjm6aNd/ttUqz8nS03/b4XRRZl3bn4iUhae2wOg+H8p4At8W1Et3w6GxabkWzY1S+sE1dD5mPEmFDulG6BAPD4ft/wcPyAw8KLxVixhDQcuRAlMdt+khJ2VYbOdu6is7LsbGxpOef8cSxiXpDV9+vS0wpycKW3d3ftaWlr6UZUDsjrL5XCdy5g4HzrypSRk2P4UIrhit5FgFh1DzAC1DKWvoeGHqWiGsgdO9TWjRARS0pqi4owuJtjNwXhMIUnqIyCvX5c81+CrqmLsNpvzUkqC4ZFRLPuABMQLwWSg92v/AMceIKEl81YixDpQMedQTp8G3fLNz5drbx+7b+HLeXcO71k/HpCRwXDXUj5+hoc4055p+TchVB3UrCYg+rrosh2BnqeyLRl+C+e63wg8Y2HKdXgcJgsbD0VBPeVglM4ZdAA3iwuQBEdodgia3ThDO4FCBF1KbLb1UMdv4fi4zVo97kgLVb6igqLrGSX/XGi17l3sqtzhdrowHjkuE8+F3oFhcQfZq4Qw1hkGfRwkqE0ej2efu0JD46hZxAHo9LS+q6tzVGYytFfB26udVeVNXLHcCMSJElV7+h3VQZL8LinNhd6OCUEx3rhOAuTBjGW1hzuqnDMsKkcJCwhLDMBgq+r09zybrWTcDNyGq/FXwv1fYhEW48Vr2JMTQeLyBUSHTWUR1fhCeA7n0WAUCdoDo7y5517nbRk/8PwZv8xkaYuAMAYEIyUKUXABphue4Qu6YTy23KirGY3wyOFkv9GwUcqcDocDifaI6UkmoIwVkUicekpyQb39ItTxx5HUkWoYd6Rlt9vzYMpaGvK/AWGEkgANEVB8HxhKX2prb3sZpTK3250DPer8OOUPgnzzl+bm5lHdx5e9rK5l763Tl+XlFrwpqAhgELpy7jwHVL5bScj2hr3++eZ95K8vo/SlaugAiWqugP9eNHr6/qCq6VYgPSyLrhNHQfV4ZMDft3YiEBYi/17v/s57na8xQtGdIPr3zwQpViOU3weTw9fwWTPK8uBZVWGcfSCrnYYwruvyH3r7jKr9faPoRhKI+Rs57Euc0uqpU6c+tnfv8MEg3bNm5fDc3ItIdMo4g5QzP8NwSJK0UgVtbW3HCiZN+gA6JsY5Qtt0rIqHhMOij8PsdAec0wZS2p8KsrOLiaJeFKf6jzq6uz8cnSsfjKkPBuOpr8PPZVVzs1VOcaXpiyTUuft1avzO9URt4Mgyx2Q4sISE7qfVIHT1mzub2peWY955EjHW/9Xf2fGg/f6dyUc4TXEEt0NVOe4RCk8HQnKAWmgFScsfjqeP/eJszrkL3lv0Xv+rPMsyC/rBbMMfqMpaVrsdCxijmIGCCrrXZH06Ewj0rinFU3DF8H9qa2sTRnClOTmXCsquptGaAzVaiJV+JhFoTxfGHWmhtLRo4cJnidVaCb8gpuyKVvO2gyr4qEEp5ySYODSy3HwGiOZ3A3Gh1DUHesAUk6oFzMDrm5qaWkf9JmKAjqPH7i5/3MKUdOieX8ZjzBBBKaDzJ06Mw5UfvsQmw08alpRMscJN/z0J2bcMEjCeyJ1AhBXBL0jdzpsDjm9ZGC2jRDCmUJ8wjCsYY+i8ywjjwYkr+766Y4dudzyQbvHZfqE2HTsd2XJ0Krzc3Ksmi1Fyl8otDrfD/TSpq3mvOsY+hQtKroqK8+GG0Pk5P+qrAOjDb/X397eN5rV/1hh3pIXYWF+/x11Wdiu3pk2FfoFxqpC4QMwXW7t6e5+CGXbAZrMd5OgLFSIo7D2zgbgeIfFz/w34dX3VabqFQQin0fLu/n7Zt3PTyaOM8LNA1mrC70KpyAQP3gLM3sdI294cnj+bUIZRTIFpyYbdhm/dRAxGh4sbu6tmHi1Q7JcKwmYDYb0Bv3ExPBVFELrX7xfHQztjfH946zld6b2AWOoybGm4od0sOjRGkriWK/Rzwun0uN3utdAH6qjff4xarWdWOp2Xw+97OQm6cgzCAUapt6GhYVz7cY1L0kJUNzR84l7o/hpT6XLoABeTkIPm5CxVnbLR620GEnhe0zQCxHUfCa0kInHFT8UlxMsgrn98eq5+KMK2FfQP2hB+BeFnxieccOz886BATg7JKASJDKOXooaDvkkPlywb3504EX5btct32732PSBp3UAZQ/W6HQhrqy7IfZOqPA2jqQImgq2xsctwaL9njN5GzBd9cGwWAskuhRlpKWfwrzVslqPx/J7FTp+um/pzjSeMW9JC1GypaQGJ6ztCVa8BKeQc+FHfNbq7gyt/uCQ8c+bM1aAS5kLHWE4SZ1fu0w3yh5G0jRthqcV2JYjrHX7D/8Zw9okTxQq/98BtqvNpKihuQ1msqraH4T6XhL/e6Pf3bxyNdlMFGKano8r5jKIGJdF5xNDfDehka84yj9e487O7LlT5XC7XGkroFTS4qn3SGDAE3eD1esd9OKJxTVph6aQFyGm5Pc3+23420NWwc+dxj+HgauHUqStZcfFFQFxXx69J1AREwBv/+8FAm4Nb07C+u2EKDajUciVIdVVAXPWnejUq4t81OT+jjXB2C6X0MpiKg3vZQDXc1EF6DucOV8k4B0Zs2FpV9ugZxJKeV+XtGCsby48ePeqFSXMV9Jg7BzmanhgO0IBvVWTrz3jGuCatCJCcCBnsEFpeXp5nsVjsoqiogwm6Pe5OQ5CyhBCr6+rqRuKUKYhOOkD3RPeCSSABXWWh/AKXw3HXorKyF6obGw+dyoET9u96u4qxdbf8cOFslqbcADP4FwQj6XkkA9Xi/lPVVqoirCL7Pit10AxhF5uHmBEMdYMq/XBbyEwhgs7Exq1o9jjFlzgmMSFIKxYgCClup+tO4KlzBRMtMMAvjl9a7BQ6+8tIZjAkJOiMr/BQBMovhA9PYoz/TFitSyoqKp6aO3fuX0+1v1c4Ymnzi9ewO5YuXLAS/vAtr2psXzaGBqrEYFRXV/dCX7g7NysrjRL2VXQQHWEVe4khft7e3fXqqFzgGMSEJK0ZM2YoQFjXQAeZBoS1OEFRnQi6oaO3Y+tI26ipqTkGxNgdYzNFr+d/sHDmzsvKWesqLX1oc2Pj7pHWPRzCDqTB1cVlPzjVtUs4nc4ZFs5Rmp0vBOkHSadG+PrX1mzZ0nIiEjRMXp2lpaU/zkhLq2eE4Y4HDPgYX/b/FLXQQe/p9/W/PdoOz2MJE5K0du3aFSiw29+ETvcvJNEzEKRL1/1Pj7RDBP1oHI7LoNctMvma41446JI3K+kZV1e6XE/rQvy+trZ251ixtUiYA/cFOhyOc1TOVwCnLCT4W6JnpxD/SFTbHW5Ne9vlcj3e1dXl3bFjZEEZGxsb26D+35SXl69RFOU6btC/J5iYJLQKrBIRJDE//IuuGfuEMF7sHRh4Cs5rn2jx4ickaaGqp2nacgvlc6ET4EpbvMzGG2vq6qpHWr97wYIzCOU3Qt1nJiiGz/5MRtkyaPxyV0XFHaBS/hnUhXG70TXVMXv27CyF4s4E6iCDvNApbpUqgEnwq/D95TlZOaugf60Ggts0kpyF0C9RQsZV5genTp36aGFh4RyFsXmU0iKoGzdSdhA90Cw6+ZbqnZsnnLNwBBOStBDQmZpdFa57KCcrTJechdADRKwc6SwGsyV3O53XQp24FchMxO8mBtkGNIltZpFQthWNcmWFIOIOOH9tuPNKjDHkWq05oN4Pp7qhR/uNRNDLGLe85V648BcjDTeDCO89HJJPUWICkxYSAxDE/2kV2n8xTv+XDt4OgdJ4I/w74n2G2kKtBGbG7xFiuoSNxvw1A4Z/mVUo5aBcYCboYMRMaH8OJ3RZpcOBq5SbT+CWJEYZhtXax4k4EtyIn5i4cIfOLCgwnVttF1Vqlb8W/T0v1jQ1HZpoqtxoYMKSFiK8IviW2+n+T86DW3pmkHBnhH9a/X7/iFwFtLlagSWbY7TReJ71zT498FP0rAdV8BAMgFegpWvD32G75SCqfaOsrKx+vG/FSEXU1NS0gRT9LExK55GhW2jMgA7L8xgjy0V6+lfcDseD5eXl6+vq6k468ulExoQmrQgwLE2h3Y4mVXQOmE/CKpuN88nw+VAydWBSjLzM7Ovh43lxivh1Qzzk8Xgag222tRkFdvuxodM1vcpms/2QoO+NxJgCGtYdDsd6VaEvQAf5L5L8+OEUQyVR/qhNZS8ucjpXVnu9HmkGODFI0iIhJz+Qbv6YQdUWYuX3ESEuIJR+7Cck6VRNWWlZDsLo14l5mGZUJ/7kC/hWRw7Y7XYrEbTSRMkoZLp+FpGkdVqAK73hjyKZ1T40rLvLyh7m1rRr4beLjgaCah8lidRGzBQtyA2EK5eAxPbk9OnTf7Vnz55+uWo8MkjSCiOsjm2GPrzUMXu2nfX09HiSCMSG0DRQCxWOHlGmwRSgR243AuLnEbUAnVtdTtd1FKS5OOWP28OATFXALEWIDMPg/cwYaPdxfhTqGp3kGhMAwcWSsrLpgvOz3Q6tXFBRyCitBmn5TfSZGu583IxfqVWuZJSEvOAwvrsQyykjM8PJX2eQeOQVWmnEiCI/nVxQdHVxfv7PQXp7t7e3tyObsax+RemUpoHEkKQVg7ChdERRH4PL4JRclqCIHzp1Js7qM2bMUCsrtM9D+Vvjlg0Ejm8ZSlPVz8MYq4KPhZyTDiFsu1QitgNRNrhcrvdB3dwxEfabnSosKisrrnS4rhSMXAsiFrouZNMQvxzOyczEMNuvJVOPQY1XGGHfJRg9hJJ+4vetPNDefqCgoOBvoD9g5uqlZHB6sFhgs4sp5Y+rlL6jZuXsoVRMsRHye+gmr0uDfXxI0jpJVDqdlYwHQ8HEjRIBQ2KeYmG/hLLvwUwL6iNF3zCzOEqYTMHbL8RxOxpIAFcQNNCHMBmks3lw/mWckE6oq6XS6frA6XT+xuv11knJKzGQsIg17Ufwg/wTHZqIF9X69GTrgknoE0FJI9TjJrhn0GJx7969+zn4vA4kpwYrtxyDdjDG/3BjzA5S1zWhjzSYx2zatGnr4Y++pG9sgkGS1gkAs/3YbDZLXkbGNMb5PXDorDhFseNhxAV8ziWEMiCchHYPTKjwq8bGxk/VUkrfIKG06dGbafF8HHQO+FSucuU6UHN+DDP0CjlDmwO92V0O1yJQ6XCxJHNoCVEjAmxLsvVhxBtB+fswiSBppVFKL4U2VpeWltpsqg0ladxzOrLxJYQPzmsuLCyUknMCSNIaIUAtm1JUUPQ1KsRZQQdRQZYE0z8NRRtMmr8BHQC38mCsdnzW8TzvEbhV6PmBwMCb0cTT09//TobNtjksnZkB68ymjN40e/bslQQlMBKy2yxcuHCyoihFPBDobu/rOzDSrSXjDOg7hck/TAgriA5DMYa1Z0VQV1fX59a0OvhdcGJKg3foGtrl8GM44TfH/YNJS21BhAjrNWNg4PfV9fVyV0QCSNIaITjhbuiY3wa9LbRyZC4z6UQYa7v7+h5I42mFXGXfxfC5JF4C2FAo6Nf7/f6fwWAY5GJh67PpRKVtCekuVEWTqqrH/cpc5eXzmaLcQwSdSSxqT66iHgRVst5V7nrZ0+DxTrTldiB0zoiRH2/eEIQWcoOjDWp/MvXhxLJI0z4mlO9DR1I4NA9Y8UF4RzcZW1RRP0jPNVgvDUlfNrP6oAP8SR8QyzxbtrSM5L4mIiRpjRBGwNjLVHoQ2Mos+UUEzcRPnmxqajoKn4+CynCLTbGt5Qq5BUhkAXRyXEFCukM1oFUY4rleX/9DoBYOdTq0knRByYI4+iQQj1in6+RRgxrvR1adwqoQShWfg1aCMz4MGJSwvqCo7D/cTtc6d4X7pzX1NY2pqE7i/eE+wOz0dCTmK+HZ/C08154AMR7weDxvmxGy3+/H7XvxJg38Mc6mipgBH5NXERn7mAmyG85F0kIyijYThPYRGuIeX2dgNc2iPpXz58OJgiMAYVxg+OdVfkO/y1PvSdrFZiJDktYIAVKKx1nu/JmiBLM5m3lFY2d9c1OD58MoVQyjpb4Cg+2NioqKuZzz2aBe2uB1oLu/fwuSWzy1TVgEbguKZzPbY+j07mrPpvejD2Jd0E6NjanoF4bZe9AeRsPvxUBg/8QtNK+srAwTk6aUdzYm49UqtPM5J/+OCxIkou5RlIJZOaho35g7d+7rsZE5cnJyOEg8uXHDqxMyCX6ARXBu0mFeampq9ldq2g5oHLOBR4twaJN8Ww/476+pq9sUjK9WWjqZpGWcFSWZ44SzQTfIirb21rXhQJUSSUCS1ggRzozzosvhgPmefx86YRmJzVhNRI8ZCYVdExrDr6SgKOxLsfUfb0eQba3trTVm32GscLfD/QhT6JJgKJzYU8VgPQmlF+d855k8jWhCsExKjWNGgO7zCd8+kODaRtOtAncTZGRkTIZrKGYGy2JC6Log7YePHW7avXv38VU0INlMu93+dVDD0NVgRmw9cJ92+O4XOZmZ2dOnT38h+tyBgQGmpltiVwxjz784T1EeIiH74rDAZ7JYc9fDk0SSitjKDCGMX/sN4xFPbe2uSNmAqmZxirYvgm6sR0D0fc4IBB4HUtuaitLuZwlJWieA8GbrF2BWb2aC3g6zN6Zzwg6J5MIxiQYMsEkw2I+eTDuOKY4M6xmWS+N9D429lXCGVkgHlIl1VNyBg2ogEHgdru94eJPy8vIZQJAvAeOeQRlmY2Z+biF9acLaV6m5Bha7K5EA/EbI8xsZzw/k3EoNuiHg73/Hs2XLR/BcknKKRIKE9tItlFZQzq/Kzc45J7xh3QYVKzCgMSeav7igqHax0/mjD8Nbn9JVVQMd7yZiQlhRmAGTyf3F9kIrkOGqiNQEpMhM3BwGQxCNpKXNIVHZjoaDbgQ8nFlQks4M17HHaG//iWfn4DyTdXV1LdBfvg7SdSlQcnVnb299Mo6sEkMhSesEEZY8PDAAv6pVVFwBKt+NQCMYtiQbSKwdjifK7pMULJMtFxDzxLGIAZ8RWJPofGYYpYTxvKhD/oAwrt+8efOQ6BUKpZhteeGQldAYGY8N+origa8oqq3H7XSugUF5f21tbdNwK5QOh2OphbJlBNPTx/bBqPbgOU4GsRYJJEhaQvAAfI3xqQwSfyUWL2sKEO+vcrOyMkpKSp7aunVrl8/nY2kWNS9++q1gg6oQ9F/ht/sw2VXWto6OugJ7fivUWhxuvUDk5paDlDvAhL+9ur5+O9YV2ZwffkmcBCRpnSTCov2a0tLSv6alpVVQSotpIFAXuwo4UqDLQqXTdSGJu0QvvInSRYUzAuG2ouiY41t1XW8wPWGAvyfSxWoY8Sg1mq5wxQUlGcB1XwOWnuKuqEBP/4QxoCyU3wbnmEV1jQauqHp1Qo6nQDvUdshTnJ//A7i5C2gw9j4ti39NVKWCLsvKyLCB1PtEhmFQYrEm8lCP3MvFCxcuxIihSaXiQkl3sRtUREIXhA+lK4StgpEFY0v9BCRKlJSTDgQoMTwkaZ0iYLhceFt3quoDaaSIUFEOg8FUYhMGwUQGcW0h8+fPz6OhiBXHnVJBnXtDqVNMVbiappqDFRUVVSpXCuDPC03bJOQDaoidhAWJLW9IAUrOI0z5Txio30sUfsWgYgMT5NzwPrx4GPDp+reAmGsjB8I2qtdB7VsPUlQtTBDoYnBG3BooyQUSvj3dZiuAa2+A51GYoL3QKYIU2biKdsTHhit7/H6E2MqiJThKpuMbJs9FW1qy9UgkB0laYxQWSueH1c2hEOSITsU7iQy46enp08NxzCPoEDpdV21sMnVcDIdd2QWVb4PzLjQpcsAg4j5MomCz2f6RC/q4Sa4+C0oqFosFpY53411ba1vbT+y59nrOCWb3Ptv0FgXZDoS12UxNQzsVXOsrVgWvk36DJO7HQN70OyRE8NHlIm4RfFDp0D1hnsq1uDqYoN7BZw1FN7DZA9u2bUtq071E8pCkNQYRVO2czrNpfHuWh/t8iVRDWqlp6DsUtWooGnRq7ErUrnLkiBDFxQY1sfsAc7RRXT+CvmAul2sbSElYV2lsObTtKJTmD6kgCqhS4ZaXSs2FNi1cCRwqTVLRl8iuhCFi4B5fBi36KvizKFF7JGT/ipZ4UPV8GR1K4XrPIbGkQ4nDYrAleI3Drew5nc5cC1cuiTncBlf+hE6MRyeaE+/pgCStMYhp06ZZScg3yzR5pxCi8WBHR9x4W7Nnz1ZhpC1mJLzEHhRcSIPP5zsQ7xzEMZvNYqc0XsLQTmoYkRUxlNbirRQyuL5h01/hYF7kcr1HCbsJSGIIadEktsF0dHe/l5eZ0wJ0NBxpxUIXhvGAIHw+ZUEVOlbVLQD564tlZWXvkGH82FTGNKDAkk9pT+yDe7tzIBB4WUYoHR1I0hqDyM3NRUN4PCkrAJLQoWgfpFiA+pYOKtHnog61A49sbmxsTGgQzsnJUeE80/TsFOvQ9SBpcSEy4cBQm1YIR0BUS2qw6n2kXkkPkp9Jm4mlNQS6DCx2Vb5AQtJSNIaL4S4CfX27hM22XyV8OwntDx3UOPx/Nai5GMM/oZ3SZxjbLIzVo0c9tPp/eoAsr6kPrqDKTc+jBElaYxDQ4SkV6K9k+jUeVVGFjKe6gHiF0STmHz8gyEGDGKb2oWjoum7llGczk3ZBVOv2KUoXunJUOjQkCTMDONZfC/VsT9ROBJ4mzz5QEbuImVFfiEkYAHG4gHgDuv+PVsWC8cYiW3TQ9wlXHPEZYPgf06fYrevdO7zeLrdDew+kLYzUEGswz+KU3b1I02h/IACX0XDY7Pl5PJ59JSUlGCI72+v1HpTq4OhDktYYhKIoA6BgHYgjKnBCxXxN03D53lRFNCzWL7NPByGqhjuPHj06LJFAuzY61LgeBByfozLlGzDI7YRR3P5jtqoJA5v8DgZvQjX0+HWiR7nbvQ9qH5ofklIOkg6uZO5LVEdHR8eBgkn5GCJmaeSQEMYKuOdcRtgv46WZ9/v9vuD2Gof7Nc4IxkMbohYH7V2Mr7Sp7Dl43piwxPR5ox8YvHUlvluJUwVJWmMQGEq5UtM8ILe0mw86jIopLgKp56VoNSRowK+o0BhXrogqLEBqey2ZvW1CCElFNo8AAAe0SURBVBVEvKw4wkkZZRRjhyFZxfpxoQSyF+SQ21uPtb4ykm0p0OYeUHdj1TsEVULSXELS2rVrl78wz74WSA73IeKF51PC5rd3dTycm5k9CQ7cD9+lDToJGsUs4/gxQAIbObGg17yZLQ/rmwoq8wU84H+CyLj9YwKStMYgUAIA1Wh9hi0NDcG4OsZjikwCcetJkHomu93ud5nP126oqgpEVwYUhTkXZ0SVbe/o7XoxmXYpDm5Bs+OopXgN8aIkdIJWdMvm2trVI4/XxT6J943gHP2dNiU6G9Uxl8u1CXTpnfAnuoikwQWcm5mZ+T8HWw8/MbmgCAkWn8mnKigwc4TsfT6fzcotRpx7xnvZTwzxuwNHj+4xLSFx2iFJa4yioaHhYKXT+RDlSplpBuxQ4L8HgEk+JhbrXi6CscrnwGuw17cgLzY1NSUlIXAhrKBUmhOTID1QN+6xQwN5LIlmMca/5XI4/CDsvToSIzQV4mCcrTWUC2oakjoWuq7v4ZzVQjVBvzZ4d3JDzNmzZ89fCgsLnwKCx2eCKmB6+F6O+6plpqX9nYm/GQJtY+8BJ67s8/neSLTwIXF6IUlrDGOz17sBiOAWyvjjxDymPKpqc4JkZY6PdF08nWx7lDGUSsxISweR449EDzwM0s/1jNCbY75H+9n5jPIpbqcTCe2lZNsUhjhEzSz/Qed0Y3oydYA63VrpdHng49+RkOo6mTDlwtLS0g+qq6tb3fPdD7BMYgc174ZQzZ+6a2Dmo5jWDwoi1oAo9opP1+sB+yZwtNcxCUlaYxhoG8LMLA6H4zKF8jtBgriYhBwph/ODQptSk64HflhT592cbHswUPOAIM38o/pAo9r0ocdTDUTQkpWegfajod76mApesJsWlZd7N9bVJRWBM8DEwTg7y4Fj6KxEq6QR4PeLXa4NwJ24AID+bQx48AbQmJ+H0zFbUSuo29/JUG3+8CLCcZeMzR7PYzAx7AHCzgsYxvbW1tZakNAGZLiYsQtJWmMc4Vl+a0lJyTezbbZLCOdXwHiuICHSiCUYLHtQCPKuMMijNV7vu8kuwYc3aA9dxQvVGgDpIxhmJ70jvU+kkY0RVWwIKCkR3Ipbc5IiLW4YbYTFapuRqkg+7qGEj23D1dPR07M1JzMLU79FAiZOVRh/GO7pbW2BtqZhS8OO8vLyu6wW6yHc/Rg5L6zK/imZa5UYG5CklSIIL6uvcc+atZ5lZ58FUsiZlFL0RZosKLVSIbqJEC26wbb7DF/jSKNMaJrGDCIWMDMhjhJDGCy4+tif1x9II9ad5rk8giiAr4pxK1EyahVcN+7NQ+Iw6Ys0J92CeyiHJ63m5uajbs3VGX318PkSYNzzFRv1uRl7pM4wDoHE9XMMUzNcfRJjF5K0UgzVoeByGPrFCwPx9bYZM7iu69Rmsxk7duwInIhzI6pg2oIFZzKrLV7CWeBHIygONTY2+rUKbSfhcWNacSZEoRbqW8NmldEVRVdC5cz64hlUEZfOmjXro50xQfVir7/S6cSQx7Gbr4E06QHQe1tqQsSICxzdw12TxNiGJK0URrVh4GA/qXRTqBZq5do5XAn6YMUL82LDMDeY7xFIsW+x2w1EANIPJQVmhYEpCg5Pm5YUaQEb2kn8RLdZjLLv2XMnzQNJ8Afxoi5oU6bYDMquZINdPdButQFEuSd7B3xvSGP6+IEkrQkObebMTK4QDO9yfoJiNkLZ9YX2wq3o0FpZUXFIcL6LEmpKWpSSJfn5+bjauSNR286ZM3NVez6GjUkcWoaSf+GEYtbl35kVqNm/36dNnrxeEDoPyFUF1mwAinqf+vvf21hfL/2rxhkkaU1w9Lek9aU5xW5GKapN2XGKoanoLM7JFysqKt7q7O//JDs9sxaOYmiZoSoipYsslD3rcrluMgvtHIGSl38dEMzlw6yForrbSYX4KF4BNKaXlZW9CoT6gc0waJeu92zbtq1HrgCOT0jSmuBoMBp87qlTf6oXTW7hjH4ZlLvisFd8hsBYhCFHUtzmcgjYY73f7+/dunWrr9JZ+QfGyHlQbr5JtaDu0TmKMDDeVlzSAnSEX7gKGqEuJBo0zoN6J/ZjSB1diFWe2tqEySbCG6tPKsS1RGpAkpYEqd67F0niGZBWVquqeiYXopBxnifQQz7UR3qFrn/c1tHR1NLSEnTM3Ozd/L7L4fgxo/x7RIiFg0InB1O80zd1H3s7Ubt+w7/aqij9BDPUUJopBNUpFceoQY4YxNgv/P7mfiH2DBfpQWJiQZKWxHGEV9a2hl8JAZoXbtlZXVpaut5qtU5iuj7JYMxKKfUJItq6OjsPD5ciC6OPwtvzwdVLkOhqQn5mujSaSySCJC2JE0aYXI6GXydTD6qE0v4kkRQkaUlISKQUJGlJSEikFCRpSUhIpBQkaUlISKQUJGlJSEikFCRpSUhIpBQkaUlISKQUJGlJSEikFCRpSUhIpBQkaUlISKQUJGlJSEikFCRpSUhIpBQkaUlISKQUJGlJSEikFCRpSUhIpBQkaUlISKQUJGlJSEikFCRpSUhIpBQkaUlISKQUJGlJSEikFCRpSUhIpBQkaUlISKQUJGlJSEikFCRpSUhIpBQkaUlISKQUJGlJSEikFCRpSUhIpBT+HwZV3tY2gnfFAAAAAElFTkSuQmCC" alt="Logo" />
            </div>
        </div>
        
        <!-- Título del reporte -->
        <div class="title">
            <h1>REPORTE DE ACTAS DE FISCALIZACIÓN</h1>
            <p>Fecha de generación: ${fechaActual}</p>
            <p>Total de actas: ${todasLasActas.length}</p>
        </div>
        
        <!-- Tabla de actas -->
        <table>
            <thead>
                <tr>
                    <th style="width: 12%;">N° Acta</th>
                    <th style="width: 10%;">Placa</th>
                    <th style="width: 20%;">Conductor</th>
                    <th style="width: 12%;">RUC/DNI</th>
                    <th style="width: 10%;">Estado</th>
                    <th style="width: 12%;">Fecha</th>
                    <th style="width: 12%;">Código Inf.</th>
                    <th style="width: 12%;">Lugar</th>
                </tr>
            </thead>
            <tbody>
                ${todasLasActas.map(acta => `
                    <tr>
                        <td class="numero">${acta.numero_acta || 'N/A'}</td>
                        <td style="text-align: center;">${acta.placa || acta.placa_vehiculo || 'N/A'}</td>
                        <td>${acta.conductor_nombre || acta.nombre_conductor || 'N/A'}</td>
                        <td style="text-align: center;">${acta.ruc_dni || 'N/A'}</td>
                        <td class="estado-${(acta.estado || 'pendiente').toLowerCase()}" style="text-align: center;">${getEstadoDisplayName(acta.estado)}</td>
                        <td style="text-align: center;">${acta.fecha_intervencion || 'N/A'}</td>
                        <td style="text-align: center;">${acta.codigo_infraccion || 'N/A'}</td>
                        <td style="font-size: 7pt;">${(acta.lugar_intervencion || 'N/A').substring(0, 30)}</td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
        
        <!-- Pie de página -->
        <div class="footer">
            <p><strong>Sistema de Gestión de Actas - DRTC Apurímac</strong></p>
            <p>Generado automáticamente el ${fechaActual} a las ${new Date().toLocaleTimeString('es-PE')}</p>
        </div>
    </body>
    </html>`;
}

function generarHTMLParaImpresionDetallada() {
    const fechaActual = new Date().toLocaleDateString('es-PE', {year: 'numeric', month: '2-digit', day: '2-digit'});

    return `
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Reporte Detallado de Actas de Fiscalización</title>
        <style>
            * { box-sizing: border-box; }
            body { font-family: Arial, sans-serif; margin: 0; padding: 15px; font-size: 10pt; }
            .header-logos {
                width: 100%;
                margin-bottom: 15px;
                display: table;
                border-bottom: 2px solid #000;
                padding-bottom: 10px;
            }
            .logo-left, .logo-center, .logo-right {
                display: table-cell;
                vertical-align: middle;
            }
            .logo-left { width: 15%; text-align: left; }
            .logo-center { width: 70%; text-align: center; }
            .logo-right { width: 15%; text-align: right; }
            .logo-left img, .logo-right img {
                width: 50px;
                height: auto;
            }
            .logo-center div {
                font-size: 7pt;
                line-height: 1.3;
                font-weight: bold;
            }
            .title {
                text-align: center;
                margin: 15px 0;
            }
            .title h1 {
                margin: 5px 0;
                font-size: 14pt;
                color: #000;
            }
            .title p {
                margin: 3px 0;
                font-size: 9pt;
                color: #666;
            }
            .acta-detalle {
                border: 1px solid #000;
                margin-bottom: 15px;
                padding: 10px;
                page-break-inside: avoid;
            }
            .acta-header {
                background-color: #f0f0f0;
                padding: 5px;
                margin-bottom: 10px;
                border-bottom: 1px solid #000;
            }
            .acta-header h3 {
                margin: 0;
                font-size: 12pt;
                color: #000;
            }
            .acta-content {
                display: table;
                width: 100%;
            }
            .acta-row {
                display: table-row;
            }
            .acta-cell {
                display: table-cell;
                padding: 3px;
                border-bottom: 1px solid #eee;
                font-size: 9pt;
            }
            .acta-label {
                font-weight: bold;
                width: 30%;
                background-color: #f9f9f9;
            }
            .acta-value {
                width: 70%;
            }
            .estado-pendiente { color: #ffc107; font-weight: bold; }
            .estado-aprobado { color: #28a745; font-weight: bold; }
            .estado-anulado { color: #dc3545; font-weight: bold; }
            .footer {
                margin-top: 20px;
                text-align: center;
                font-size: 7pt;
                color: #666;
                border-top: 1px solid #ccc;
                padding-top: 10px;
            }
            @media print {
                body { margin: 0; padding: 10px; }
                @page { margin: 1cm; }
                .acta-detalle { page-break-inside: avoid; }
            }
        </style>
    </head>
    <body>
        <!-- Encabezado con logos -->
        <div class="header-logos">
            <div class="logo-left">
                <img src=""data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAUAAAAFACAMAAAD6TlWYAAADAFBMVEVHcEwAAAABAQACAgAAAQAGAwABAQAAAAAAAAABAAAEAgAAAQAAAAAFAQABAgADAgABBAEAAQBsJSQiCwRbGwdMOAEDFAh+dgDtHCT///8AvPIAAADtGyPvHCTqolYIAwDxHCS9ExlvAADlGiGsDxTCFBvHFRy3ERfpGiKhCg7+/v6nDBGxERbrGiNoAAD29/ZzAAB4AAF/AQH/8zThGSGWBgkRAQEeFAGEAQKbCAwGDAkAvvXOoQj7/PuKAgQ/MgDQFh7MFh0ZdT/t2BTdGCD/92n/80Lu8fAkAwOQBAYOCQEWEAEaAgKjiQH/93cISiHo6uk4BQQuBAMhgUb/945CBQVXBQcjHAIuJAJxCw/TFx797wXWFx//94T/9V3ZGB9lVwLCwsPf4uFcTgIGQhx7bQIPYTHX3NpGPAEMVCgui0qWfQCTEBUFJhD03wATbDh9DRHTuwc3LAFSSAGJDhMENhj/9VBjCw1/TwJfAQFOJwCGcwFzRAL24x6Jq0GfExaJhDfR09O/qwNMCQkDGQ1fm0vJy8ttZANSlkxoOgJ3XgJLRyDjwQHszwBOAgFSPQG1pQJsoEf76yuMXgJdLwDJazg+HwGmmQKMgQEuFgN2bzL/+J1DkUrdxgvbtgjUrAnClgflzwy2iAaaawTKsgaZjgSzmAECWnKoeQWARR6sujelo6R8oj2Crox9fX12o02Xu6CRVCOYl5eoXCi3uLhnZmWwr60AtOlpnnaWiYifoDm3FxxPUU4kIx2aszo9PzxnPyCtczw4MRpgWiy50cKox7MWFhOAqGsuLy7YlE4Ee5zzHCRSkWm7gEQAm8ieMDEAqdoDjLPhnFMZNhbI29C/yjeUbDlQLhrNi0mOWFlojzQkXSl7VjI9gFonSB08fDxUdSvdxC4HbIaJLhOtJSSZdHK/tljXz07ysWFSiDuUQUQvbzI/YiQDIy0JO0cGSVz/8yCqnmF3ek7IuzPs5YfrTTaYh1Lr3UIuZ2vvOS7a03S3rIfPPy3jNyvvZ0jPxbJJESI8AAAAGHRSTlMA+VRBIXnnCRMwjc7xZp6vv9v+3cmn9uGkg/8yAAByKklEQVR42uzBMQEAAAjAID/XP7ExfIABAAAAAHix1bFnBpyNbF8AbzKZdDqTJDN2u8VBP8afC0sBfxgEXQaW/zV2wsmougBB3WgBDUiQorRA6Dco9v8RBsgwCUA0H+KdM9lmd/e9175MVniaXyO5pkb4zTn3nHtyYLrVqls3bKdaq3quXa7wlVLNq5YAalW6YO1E/bU9w3YdxyFPRKkKDK+81VIoAj84ZmVn689UGjUAoZEd5bZQMSgA9PNSK0ko7dX3d8J+4cBlZ5JRqEiU/4xEVL4f8RKFihhVsn82uMNwALQfEawqik9WxFLwv5ZXIq14Ee8M/oLhAejwJGZYVKs9usoZPSEwQqWjHjFNZKtNqNoui1dU6o0qgEhZTIvE9a6mESazcbc7niUi3xaZaDqhS5NMTfv9/hw/NI2dOma/7gAhUtJyNWVr81QhkrA0jVAr/ztRmCaoQWeT7uMkVVXT2ttRaZTy6jHtPnZniU6yLFHyJ2knPxBxjSG92Wy2oFWjsvPnAgBKmU4mi0Tn5ZfrRZz/8cLHaMpZTdDb03NhhmO+y3cP3rg/qwmQN3eC60Tuxg+5fjyXEAUAOptTZnfHueIwjvklBQhuddw3HoM2gJYEAmAUh2FIanSSkjBiQiUEmONjzNI0kgo1qFY7JxXAPWHIBt9uPTZqoCPCFysxYV52/TRNEw3f0LjaFUnvvJ8z1Zp9xw3LathvNA73myCikJCQ9PpM72lVdvnItkzs72UklKgBqcFhZjrKr5mWA17533XsKu//rgBU7CDGbOlkkgqBKopXwnyF8umqt2Tqa8jRCRKJVi0mKTsgqsbGT3OLG0H99NDY/y3HDxGzglAnSwQwQq2cjVINIllMxuPJLNOC0BoZLYCIR+32KGo6oIRT2Uyf0SxvUeCX++vNFZarANhmMNeWm9FaMLmz8SxDWCJyYVynV+SFZ8SBWasJiWBulFOHR6fbFHh5fdcZbKiwXhUIyRXFWspq5M9qeHBq2KQYPH4namxQxq1n4kgKSPsPD3MNWipwrOKpcDi4uf/fVgV27m+D22Gjskn8CYWweOizAKTzRuuEoY/Ic+36wX4eGGazYewd2M2mXTeaQGA6J2cP/d40lb4SYkH9YQJKSl0qF03ew+FtMLi53KrA4OY+CILLevGc8UBJFHMqHAmgL+m40e/nYnyH5K34YWnZHITUEi4WiwyFlgTqRfdxISh8EeyCT/L8JgguroPP2xXIXxkMzQ0aGBKAOH6cJPlJRAkSQwfcRDjG395UdkvAHPNL+IzS2XgsecYKbrEdxRwGwS2Fw1ZT+HMQUNAHA7t4AyPY2uJxhugzCDkl78We2Ko7JXhGhkykdZbSpw9esQ3FHgS8IW1f4O1956JR/AiHEUEBp75pAGiYpll+TYJVtl1vKVFzgx2fhFqHcRxHUCs2WGhcB9d3wZYFnnfoG88GQ69uFctgF/w8akDG+dhFAUD9n95ccSAHT1pMKLn2RFCsiljNu7NBQJzXtziDst8P7zrB54/D80ahjcfyxDJqVCsnEgJFfa0RBGo2OGJyjxFAEQNl9/R8SOHQuf76eyPQPHjZgGG/v/x0F9x+NYptgXhCRDhlAW1fo5SivNYMQkqFILJ5r99rMwqKtNKVd/c3l+c3g6NDk2r/C6ydaf/9/2u/XlvGOxL4qchIk3IQW0T81COeFNdhVTLWqeGgJKEBF/0+zw+nGor0MeaXTnB22uS28yUqprO3Jh+HF0evKbQphQG89ScLptDYJlr97sM8E9onVPVgrSkERgwCKeSDcwYA3prPkjvL/1x0hq712o9eR5ene2vy5WzQOXv/B+tmwJpIksVxEtOqRjW5zcw2VDcZTzUO0YM7HG/TwG4UATmERiVyuoEsnNtsB3t172xkOUjg7DADwHECkAFcYIABgMB8gHyEAwYQABRigAPIdT7EvdcVeyNn99gZ/4YMQzRU/fKq3r9evbYfka9WK/VLJOD3Ohz3pqJqwzfD4Ru4YxOIJmMaVpwcxTwhIshtFBAU7ozjHBGEsMeZmYdAzv7xm08UcrzMlz/94/IHxwB/BKf8dt0+GUeSvXqqQtbCG84G7gYPPcUywD3Om1b1BL/DTYDmn7wGAM+uMAAVNeByepdf65VbHftPrX//7S+X754AEJzyZdA2DHYO062jaK5PyKajka9jBtDh3DYy/HAV1XQweZTLTSbDN/CaEHJ/disQ3BUFxkEQM26S7RRq9UaMsQ+Ty2/fv/vxG6cA//oO/PnbkNcukFon6Xgl8SJTzxJ30OMMYHMyOMN1R4TpEDDoQsjjuBgxucCywoio+ohe7WkhR7epxXOuUdlvRHZsNw/mX+CznQP077z/6f3f/h62+d1MqpDOlM95Nn1eJGt+rwOAIki/utcUYtTyL6aK0wSABMnoHm6fxpoqaga/phJ20sp0FOf5WCXXiHftgn/9i+/grHf5xROqPMzm5bevGY/1Ck4U0lytx7IsHz8iZHk/43ouy5IsTcD9aeQWOhFuNMI8rZ8Gr9gnhp0RQUpoWfjbZLd3wrN8p585Tuz7bUh/98v3r798FvQ9ye7+5e2frSPL181lOL57FEWCJ71d8DNLJxHMoJKkqk11hO0vEIlPqMh7t4iqEkAHofdgavzLfZAJkFIryXJsst9JnyT2drzWkfr6x7f+jadWjn2bJLtrFVnB80MAmCrmWBaH0gU/w3iWtDFN2rgGU5fp9vWUEGQgbQBArGWJUhtekhZcavsLr5FyCgfNHlfOuUIu0nFZR+qffsAQeqrCpF7btYisZ3uHUY49LMZ4gyCLfmbJEnVIyxsWBKeORqYJIMIexyVZzEWaJqHyKNntW8697NYOeRbEXxcTCPCAsYrUbAUH9jkAW9FOdmFkbezEG7B6o+UOzxriD2vL+pkgkY2DCL0SJiqC2PY6D0A8wNB70Sr8RfLqEnP1BNG9ZOiY+VY5w2UO47Edz+JILca6BPfVzwDYfcGdFxdFlquVOE5C6NVqHEvFZzpL+hmfW5tiEaFtOGi6/wfWnR5nBFEGSUYsn1ZPq+0l6llev+FecMWgev00F23EU7/ZWBip/cS/O58HMES6kKoSRwsii9lPnADAF51yhqWCYS3nZ7AYMIID7NAoKCiyBFLIlsPjTECQjGaaUxT+OdQt75LuZcYvetThuehxYq+7viBSd+sFnl8FQJYv1HfNyDJNTApcDPxsH7MIFbe0nwkRQR/QOlReboMgIzvwQR6v17NOBNrAALUYo51Ld7s83k+6F6TCztQogotNQho+8P9/pBp5eiUAMce2SoH5yNro7OUMgPFSyhwR+pn6Mn4mTIgyBgOI86YJGRu0NpdaxXjXubm5tUkEuvNBrxF8De60LWbLb798Z+7lQS+uSxE4BkAW2TeNjBmp5Wuc+OcD7Dzk2OvyfISsdyO5DA7lpNgyAVLWn67PeMICITdnZ/rd4GJSrcIGJqlIcBkftOF30yYG9H5tiGD9bKx/OBvckDVNY2xrL2sPVGbiW8UGy6YzuUiq45uP1N1eA2eFAP0rAAjiG/N+xv8yAjbQ2EfqJkCT9VrYZw+wqQjC7ZlyfzYWjf2riZlYAR/k/bQJUfC9VFOI4NHZ/Wg81lWw5XY2Zh3dC6ViRmC9DD4C03CqG5x3L90oBc3XVwWQ4w0/M5ufdycGLobDH9T6SXZO4GdslzECxBK0rGtwDlGwHDoV0A5jr8YnCLq24Z2mJlDPH2m3YlMUgaaohjz2tZcojyM2le6jgzDS8MGzx+6lEsM8vQqAfgRo5thYxaz8+Tqp+HGSBnmlwJoyWdsvx7AiUk1vsBwK60+VUKJAGHt+RGz/qimgV5oSeBl8iW6XXe3FpGIqU8bFwyUhDZubIHUvmKdXC5Dm2ER/5meC3VTihAI8L5lp+BHrom2Z1U+aEpWsTDABEK1t5BIVjKTXMvX6sIxqKi9jI5JsNvA/Z2zWvUnlkQ7p9o1pOFb3zSI1W6c2e0UA5zY4w88YkeU/2AMXQ49DpWvzPXOsbbIqeBDR7KFEDKoi0pYrgaxtMS6fz7Xu8s5dAkLm3dwWNKk9NFzLcIiPPAC/h87gqqj5PdbLl7qXefERaiAwDce+Dj5EavE8/Rh0b4UAUelWCY8l3p19cDEUYK50Pv8ekzXBPdOqIi808zDtU7wXR4lint5OKpBeAu5AAB4Q9vvMnpjn+ACEkWkmtAVJb7cNH00fgZhOFKstA6GAezGpmMK1A1FJ03Ds1TMaqUeR+UDtEWaFADkIrUgZlrGvHqMuxvAxnQUAqZ/BZWzdXDkxDPCwLaEwtEAjBTip8IUd+W7/BvVvqjhTUx/fTkaaSlvhqriB3sH/A1sum+VbNqnMJeFO8QT+oWn45Y4XDx+947m5rBggl0TnfNzbdW99FaMuxvAxPXNw88v4umy9jF2h7bWHZr8L3M9Q2Gd+IzQRE4Wl4cc9IUGUTMlGc68myiJErTy5gyZWAa9W7ZYvUlmkHrqYhzS8Xw+G1kroXlYMsPeYXzSNYKLdbPEVAEQXY/iYoyS7UHyjZ7eMXf4A9j8PBrqM6UCW9IFxw/GIlgae3L8m5k21RRUjVJQx8xrfmuCoyZbHuvRHPd0CJft9mM8sDX8VIBVaJFwlQOYRQC6aySSpVT7/50HKsIE0Uc35mDk/A6wJCfusCyqapgKnmXR9AvuclDclQU5ZU05NVZE0Ssb6Cwi/5RXCWJrnefcyp0zFWF40De+/qvRz84FKo2N1ANOFQiFJuVz/7iW1gSi+WzpcDJB6R0IsH9APaVL1V0kPS1duIxYqmRCiX1C9mVbz7bbxJU1h6VNd6AqeXywq9wSoWPBjD0t40J+l4YOfE4sW+goBJo8bJ1GKrPX7/b3ESXqWzLIR3pIgj9fGAf+GVVF58mbwAV8fwA22Z8pPhxdU2D89gDbowd1UF8GyDFGnsjaCdtbx+AqfUBTcjNey7wA9nYX4SJbaB8wikdjL38YW5cJVAszljinAZP1rAFjgeJ7j0twLOhAr8XhtTCySpC8cUIDEFZC4hb7nZnVICbXFyRTyCzx6MyE38LTDDQQnStbvrga6QgztCoJAiDvkW1x53iaEejoL8bHs9QxgA9LwH7qGz5h/f7pGgp8HsGb+wmgCEi8to/38an8vnohf/+fjx/9+/Hie7doA5IxrY+Jet7o8DW1tP9cABUqbjK8G04lIneHNze3VWBgpgiqbkvSmQEwFNhmfpfkzPZ0FwFYpXjhu/I+WcwFNI8/jOGmyTdu8NgWWMxOZBqP1kSCgW+mEXCLjaG9cxRS7uiE+QzBggWq7U0qYvdNkA3iAWXksBQQEsRyCgBiDxGx4HCEEqAmvw7vlgF48uPSS8D44Xvf7j+d0NFEW4n55t/kn//n4/f++v/9/nFnyaCXoTFX1228kKCflLdvlbgKclHEAH0y9+d2rf1RLxc2A32az+X1lKMbthXrHZ1iHs/47fb1Dw/XvPsNDwB8/fqL1LXz/eQPjHoIQ7uBQJzN6+/bAyGdot9Ju+XI3ftsLwu+gWiuVSrVaFUygUn35Rg89tUE73WWA03xoyVRS7TiXGl//nA2oRSKlqK48UO6oB5O4Odz5lt0dtOTgvTBCVNxTwZzgvslHKIs/fI/0A+TGwN2+vjv9bb/NP4rrsPWWs5er5S03y12BUu335av3X7yZFMs9S4auAhziAUKhkKomOIDy13mTSCBlcV3Scabi+QU6mIzRkMYdb0ITf/vX9w3B7uxbMB+990dvGMfqz4dAsfzup5++++u/N0Z7O/2i4UHzSjT86HnnT1WyXmxcwKzJFijk/vJCLJnXLzY1tfIbA1znARqkqocGBHCxCvyE8i0YOi5hzzq+wjKsxdkz0tsBYd8IAW+TAET1Z9Mx2vnyD//8r5UhM8tmINjTA0ZEr1T4gt8kX9+8jGJOC6uJOr+CWO0gw4KvcQEIYGQzf18i0Uv1kl8N4ISCAyjezauVTQ6MvNWLO/CTvMa9JOVyJ0Je8+AwIGx/7QNfAKG/Q2xg9Nxa9D+Ui7KCGDbq1WG3R+5xGurt6/Rl0gHM7A1prK5ERvdE2mkRL76N8Fdgsvkjm77yPASlrBngsxsDbDh62jOhmOIAVnwAUCj/3v2l9nk3/QJ/mXK7jKybYTNxenBkqK89QghlFCcOb4bUAL4ESxlZxgUI0yvhwYF7EBod1H/33kAPvZphjYzLbk1YzAvzQLBdsqn2/DxAtc0f2PTldiWyh1JtC8Bb3QTogfnIz/wtDrQdlmq78nYEn+NzIcbFhIIphjKSmbi5Z3T4Vl//tQT64DGaQfNqMMQarS5Kk7IkKWM6zbpd4N+UZdkxODA8dPdOG3pDI7cxetVCaoxsyggE2TV63dBmVmLJafnQxi9hBDCyma1IpJMTTeVI0j2A8qUJxSQC6KnZWhxo2s+rsxXPtSYUT37lTBtdTDKOx9MJymokt2JOvGd05N6tu339DYz9/Xfu9g7dg9cE6lbXoqQGiCF8c44QZQw6YiFwoQu8GAouO+jbA8OfCcbCYNQHAXgMdyyDcRlNMhYPMXY7Rcbw19DbXbfD1J8V8vumTw40IYCFM77OdwvgLewZD3DxoWJyCd07z7cCVO/nRMrN6q5cfDWApU90GY3LnVomLjacQZJBFS35ozdsxuHEdGBkeBiq2vDwyOfwekVaNxezhICe1Q6GSwbnCEKHAMKKjqWRJRHDZCa26qB74M2MI2goGv05vKkRg8hZRsZ1G0lLGMPiSSAIfxV/NX6t/Wp+Ua6s5gHOcgB9VZlUNeVpBtjTLYCSeQRw+jfi00IrwNnyNvJhtqKfbuW3CA1Mwoq88G7n5Jx+Ga17KZFKW2LLc06d2UzTZrPOGY7HgpkQmTAiesApveIkLk7ecQDxPQLTLWdSCTdiyCTIUGbNuwpjaRyJpnXOOe9aJgn0KDD4SxqDAd6U225nkqvXbDPHZWdFYLddVoqaY9hXmpIqppaaAC7cGKBEAFC1OA0lMNIKEOYyiz7HQAmtY+FS0X4NDYyVYlfwy52xmeN3hM4b5bxkdRsTLJkMpaOgdChJsgkjQ1ldHL2kZVm3cXmyM1MHSBSL5QMUy2lwJxpLMZoEmQqltzIWi+XHTDQNwzUMRbk1XMezlwuUCDxGUna7MR1uvmMDq3exUvI3PnUBQBTD2edSxeSi0AXargEcl+jrAA3VwBWAuX11fR6R6oTw9E3+DWpgrGyQvjieGQOdXNS9xFAuu91ltVJuTpQV0ME/ABk2ZPE6ifMP78dmxhoAfSL1Zu6QwMxQH1PIpC4QGsswRoZh3BSMR/TSwTka8EWUInUOw9dYyu7SbDme6iGKeX6weiOzjbrzCaCaA1j8E9T5+a4C7FloANTqHz4GgGJZyX8V4J6t0VCVTgVx/ByPJ92onzgHfkgzO0cXBB2OZZJgGeDmqguhZIwJMrm1FncQ55cnxzPw8wKAKOqL23s4Roe9lnQK2RVR50RRyM2p6FpchxH7eT83N9M2RgdZKAfwxwVRLPZUimq+dWgGCH1MoTqlUOnlzQB7uwlwflp8mr0KMH8Q4KdSrGg/nReFQ0bU0Z6/5/jVEZ5cbqCKv5ZJJ1MkyyZYliXJZDoTjMXf0kDv6BjMB2oGCJr1F7fBh5jZiQpmOgTDSTKVSqa3LCsvnTRGHG4XTI0NuqmMmS0Jqx3Zn49isb7KN8+Bg7ygE6v3Mb7c71UtAJ/eGKCWBzj1GH75eKWAACpNfl8xXwLls0VfjvDxU1H6zqCrrgewY0vjMkadGydARKDjo8tzAsPNjvBqfBkUXw2/1dEYwPtwwtNrAdhw+GZ++/AAbY0hOpxhkNNhxjGM2CvnCjalsDktY7pMwoWaGRpFMSp/uzUb//+bB1mR2mSygUxq5f9TJP9KoZL9WgBlU48Vern2bBPOsHyls8qubH7JY/As6qW7r/CiwI+Bqmwc+C09QxYwhsLE0VizgNHxydG7i/PzDQJp4/z84vLD0XuAh5Zue4AgJVxrpFjaLu8f7h0g7R3ul7fzhZbmHkXaPuaIalyomYEoBn7y05JgE18kqrXq2VkFjjTPqrV8IYIAZu8rFE17OcMNAfa2ApzW1wKBbPU5tJtizmewLsQPpI/yIoFstVO5GAJ4jbVCI4F9GLsqjtTO8fF7pOPjnR2eXWeAgs4NLbpIBI4kTbM8O7XQhJF9LAw9PGpmnuyKxYZKcVZYdh5NaSXy6XGY/rjEoD+tVLNQBH/+UiHtLsCnDYCGugNP84XqCxW6Jdd0eybX3FlnK4tcAMOnj73bGWsvoCYE90sB2v7M9U3/o+VMYJpM9/3/3/d1sl0PQgcFBGQRZHVSlrq0ApqWDptR6NCUdpAEpQaB1hr1MC2QUFCEYriLGdHA9LXt6SidjNairRVvcYTUwuXQI9FMonccvJLETHIW7/dpX/q2M+3MeOl5Zty39tPf+v393od8CTE8tytYKPrsQUzx8X2kmKkVlJT+8ychv/V6Rcn64hlObPrhtBP/9MUnNwpSQprhhsr/tVGAe4IBJm395qsPstdHckGf043UUAM5Y2NpL9fv3S2KQQH4bz2RARo50k1hDtvyqX1T0PnkNmlJfMWM4NvGH9WuvJCmN7kBSfKD61/VpcSHAORFC2C6H2DDBwtZ+1PIZlZoy41CMOR8eb/teDspoFEARh8g39tkCf4HpUYpm/Aztn5qZH4SLL+8HSNGS0KKmRuZP+qeBAFXZUabdTc+KAwBeDjKAEsTq7IK9pORXHqoOv4gVGM98wDxey8qCBQwv57Xb6ZILPxlgKmOZk6Tnc9n9LSxZiMb/IScJmr997i9FEkV92NEu+uJMlP+u2AnSeU+YOYQzGhz/0lBbnywmgCA/31DAP8Xr2FdzUpKgAWezigoyA1sZjHzmdshDtJ4Bwl4Lz52FDC/Ch0OauxrP9xavfbolwFmWjTCJovD62Z+Yozj2GTXjAlbjbSBOYSfUgTUF/dJU0eKmfs3QzVMZhLGbBjtL+gv+KsBTCQAj+0gAOnNLGZodOL+Z8Hy1o0y9FE7r+bvevhr+JFkPPdw7vXd2bgX89MPHv4iQOmgobnVSzV5uesAnd0cr9ug724WWqS+nzBiIuVjmfq7XWWoBlDMyG9/Fhyk74fOYsmGEfY7ijK2bAuWYw5tDCCeZuEdDgYYn5FdUFQY2MxiRL+yL4PCy3V86PUooGOe/Srze7x6b9njWb47ExcX9+LFTN8z1INhAXJJyYtv7SqVhuN1wY0DAFXNrRaVarDZ5XKQH1PN2ALx/yr7q12kqUMxU3xHGhSkyxiZJrBhBIBZnaEAK6ILkHgwARhaxWDwyxSC5DOHhI8C+ucLmKC+5PsFz6xnwQZ+857l2RnPrWePwgK0dxuclJ27yTFpHWz2apqbqABAa3frd5NWw5jRS5FkotE0c4SN65/nLgSUI6SYucUPKgOZfRRmw6iwKLuTaHZMgbZxgIfWBWkC8AQB6KtiQnXKQ0whiLgNCR/F668sYKZely/PzM+aFhZggdMLtcszcS+mX0+FA5hptE4OjrnsjsknqjGNXsMhsEhyTaUmDcKnTyb1FrcQfg3QcGYXnXaJNFMDUZcUM0wqvs5sRAWn4aKCzpZQgP8ligBzU+qyCgr2B8pA5uQwheBntyHht1+Wx/zaAubxtbumxcVpmw0A5+d9Xy3+EAYgDpeyqrqFHJf+icritA4KKRibgwC0q6xjT588GZS6ha0OOLQBz+FRQTE5phZNHYqZ/Ots2k1u0OVFSBomWWTLsSBBMLZ0wwDxQTEWmNuZnQ2AzGYWM8BfLwSldyDh16OAfo8C5uFq3/Kd24tx9JmZtT37KCxAENQPjnGafq8yuJ2wQscmoz9VZFKqwaeTVuMmezPHZddbVYOaZndwViNNHVFm7n+RSk8hgrZCmQ0jAMzKKE0OAfifNgTwv4QAzO4syC7IZcpAJg2ffMCnNRDEm3oo+O/Bj0hcc49+mKHxTb9d++Fx+CzM5nKN3QD46as1vvOJVeN2N9N1C35euKThb3JoQHByUmXQBFfabEgzMrQke0/13vZnO/4DsnYWmoYPkzRckN0ZH2SBSdEDeAgAM7b4AQZXMfSS5f0v2esFTD3miZELwEiF4Nxi3Pz09Oz08g9ElQkPkI8ihW83UsY3F94iHhoaLWNCB81ISnE4doS/sVYEQ6tKYw8pvRvvxKC1RDko8hcz0vshm/HMnmpRQcb2YIB5GwZYGksDxKMAnVkF2UW5zIJ+8PbQB99+xs68XiY5VY8CmhSA73kev42LW7SNll+bIn80AkCNy045nU7K+LbjworVaR8cbHYHZC4Lx5VpH+wWvnzyxKqn2D8Stx6QORMpB++cwejhBuv0TwDSWWTLieToAfxPIQCLqrNJFcPslwebekvDN99+cb/86vl6dHCkgnnPM3Ud/js7kb86FbmQ5hrGXJQeRcyY8JWod8VpVOldtJjlkLItY612t97QTAA6+Y3EUu381CBpphfzmX85UHZD+u03x/Lw2HW4NIwgWJfDvK/EjQNMYgBmkRAIgEFiFqNa1MVuLv3mLkl3xIVjnr23cuCaRwA0L3z/ODLATOeg0GJ36vUa3CIt7rhkmNQbM418n7zgIO2IRapXjS2Bn4PSoJbGZRTG9bTLvgEn3nf+as3tG9+Uxh4j9W34NJydURJNgHk0wK0ASEJg2CoGfKvIFlJ6YjUL6Y6Mgd/biT+aI1lk2rZrLrILs52qsVYH105ZXC7KeeHzlUkn3+HrOOwcCxftiNetsj59qbJY8Dj8WDPaOY6dKe9lx/ftPFuT90FSeuzm6lAtJvC4SEpuQUFGWmwQwP+/UYCJNMCS+O2d2QDIiFkhpw7K62bM/OugwNXXX9ZGLmMinUezALg4UfYwEkAco3WQ4+VzG91uPjvzrVk96JZ6Wymizwg5Dsqq4didqldrFr2VFDIA6HUz+hopT4+38RKSY8m8/OTmnwIkzRwA7mgJbrGiCLCws4AAJFVMMk56emws81G1kGBJRunnYnpP7W2/UvveSuqjt6SXM5Vf+xmADiuaXqobW/sui+Pd58trXErDsbDdbOMYx0tZ9UKHZUW0ZCVpmDQjFD8gvjwoJ+WpOC8B0xGiEYRKCbHkpNNpOLtu618H4LGMApyiEyee43Tgy+m0pJx0/LL/mc00/yyutLL8wM6D54+yYt7LiaEprMXhTI++/hk1Rqqa1LRSDhVphjneS6bP/2hQNbukXqmju7nJgnbEsda75lThDHZrLPZAJkYAlJzae/5o2bHN9KsFSJrR5uQ9h5ISExOTSkqTSBpGECwNAPw6D/fZbATg/6d77s0AWLcje8vJoQ6Fblw3LhnXjYyMjCs6nn+9h9Z9WmiUp1ltfwsxH078+P3S8I154sNd309FBpjpnDSgR5M6X3aPIcLdM1/SWzUuqsloH9S0eiEprF34XMpGAjY64OTBI7jiy/XtV2rOIc4w/oLvJB/6GsZwQUHOhQ7l82MFOBkM3a9Z0QOY1rljSDI+MD6ilsjVMpFcrpboQFHXcfrQZpJFTq7nk5P4tI/sGyiPQUP2PgB9vci8DdzDAWTb7VI+22G1jln4Rst338Fl3W/MvUuTgy5vq0tqGISY6lzq/fyN22jRjHldFqObyw7s35Vd3Xdwt7iCXlmNrfMPGxvSnnfoxkfGx3Vq8h/ezrik5+SOrBOMBbL+50YA4r5nPIxC5/jT/UPjAwq5DPDaRDKxvFiOI9MpxoGwITa2jt6igRPzaq60Hzwlel8T/MHXDZt+i+wTBmCqcUllsFDdk90WLrVEakEvdfNz88oTPdoRb6MvP//xlbnXYFD5tAQ8D+ZdT8FfxIh3H9x3texYYPMZOST28OkOBdCpRSKRTK6GUajlCt3IwIgyY0tguSJtwwBZAYDHZCMSSU1Nm6RYLYcNiuRikVpcrJCpxQqdMg2XFh0K9HXII0faz+a/pwle8wGcvh8BIBGz9GOcl3pLqv3lkh7ycyvnnlmtp6jBZqHUCJnG6TFdWpp8gk54cKyZ0+o10oo1HwaI2kpW2RAYVrZ8mJPQIVFIRDq5WiFRqBU6kU4kkUtkIgSn3nPrWuGHUQRYWicSFYtrZbJx+bh6XKSTiEQ6hUQ+IgdQtaj3+de8QPnUIKi5Un8EJrg69T5Z5NksAbhYMxcWIOZAlAq5o/U7C5uCIv30KURpp8m0xsWPhG60IE97zaaVySdIwaSG8Rr59NwYBqiFAR4oQ5SmqztefOlztU4ilqgH1IhKOoluRDEgwfuSycRi+YjoWNQs8H8GACb2yOQyUbFarJOJZXBhBEGdaFx9RT2Ob/HDHqYyiG1hSc4faR8oR1EciZbv/AjgtE+NufswDMDURi4RVAcNGCe9+iNSSbNLyHGn8j1mD58CVjfXoHplNntuWpb0KHKeWhxcgo/tNmZuyrzFOoAIqBXsCby+ytNKiVitUI/ojiPujeh0unE1AOquqNVqOfx5aM/6rGLjANPoJJIglpO90JqyMtb6yS+XFY+IBsQDEhFcuoyZEzZU1v7tXrzkmNdh0CFd/IZMkR4+nHs8BYgMQE8cOQvhAG4ydju4mLV1wzc/vbcELdDVzHE0Nr4xm95RkypNY6pzyWQWGSyap/BdlxAFIMFnabWkbvpkV9vlve1Xy5kbluoq8HrFxOp0xyU6tai4tra2vDa/pg2moRPDBrXbYqMNMKdFLVbnl7HyeJWCqqp+cqoEvApWWbm8eEChkKjlxYIcRt3CZ/7xvgMsJo0wE5Brr1dfr97p61u4e/v71WtzAYYfPfQDtD0LBxBNrsWRuamRcglbf/9qcrLb1S2kKMs7k/mNcdJq4Z5ZuWRa0fvrZ3RwjT6Xb+Zw3JtSr8dIziOnVTYwewA1MpFCPTAiGZeL88tZrLyKCh6PV1GRx2LF1NTCi2UnogcwwQ9wz3NJTRmLJwC76upOnAycLRmd/ZV5rNo2iU6hlvBKgm5kkZ86svdva1FMh6zBPH52yzM7Ozs9/4Kc+cXp5T4fQz9Akw/gcliAbNhZM8UFFqnFcmnpicEJI/Ry7PBhu2qSemcyTaxYIcOoAFDYSiyQb4G1WtjQTsvP1tcfr2EEwEM8uVgyohhR1+Sz8iqr+n3vp7raZxOVFayYWpHsZLIf4HYWbhOICsBSZTGLx/P9Yxk+dlt24GTt2LElAwzLayXq8TamCd98Mv94/ZHzvYwPf/To4aOpuVsY/YaeuEXP3dWHQBgA6LkWDiBpQro5PpWPal27p3dS1m7hWCtFfNipv2Qym96eMToNBgPqP0oolIL4IHKxG0U0q3j3wZ1HeUmBEB2fjxJWIitnVQiq8Ua2ZOAEMYRbFQ8dpruCmCgBjE3IzxPg8+nM2JKVXVS0f3+Rr61D35OdlbUjo1NQXjsuZoJMbFre0X0ft18tgw/T/O7YHqzem4kLc+an771G6bIOcPpahDqQ9MF2pBJX69O3r1bWrPrv9ELvH02mN85X4PeG2+iweL3IHlxXs9C+ya0nU05kkhvwYMTjwFOToCKTSOSEnt8IcGAH5BCO1f28yory4cTYqAD8r/8zZjt9DRwLoa9KUAWABftzUxLStm1LSCkszM3dD4oF23JOC2raghSOHAHx4cttgTx8bfrF8sJ8KDnmh9N35n4zdQ1ZGGc2nAvj8J0qTStmlfYxYdNNT4dIr39pHePcNJk7TGbwczi7/RIg5dZohA62E7W2Bamb+6BsoL3+Sg2jQMfWieQsQUsawGUThRgHFkHeSUEWgpIAb7SClRAlgP8vhvzDxCd5VbzKqnOC/h0QZApTErYllh5qOIz7VlIIxm24qehYBROm8SJr/nbvkfOiGNqcHt+Km12YjYt4Zu+8fg37jAwQx23oFsIEKdSCDqrPtNJNGmOHx2y+aJavqFS+6q+1ydXowNWYRqnqidWCVIIcjIpg39HKQ0G7ZGWVJxo+LAU3YCskbyY+npgDxNQdGTASIKQfwv9wwy5MA0SDW1lZIag6d07QmV20v3A7AXg4Z7NPxU1L2B5PbC+x2l8z0nrH1XaSh0ktTTz40nwtDDDymfFM+3/5RSSAIGiB5IKCT8NxGJv6TPfWIL+434Cfx+hUkfJ5zMWBOkipDGMOo0pF8X1Caoz41MFT4iAPjq84SaSEUqBLwRtJ2pNzuDRxW8L2wv1F2Tv6BecQBQW8Y36ALdGywPSTlRW8ispz1V1VnUW5hdvT4hNLGnzCPmTIxPhEkrRic3KCdy57z3+893g+CYJEbl5eLF8O8d4IZz4SwFQ2m+/wNj1Fzh0Uuo2cT5XmjhVr97u3JqQR9h/la5g12R1CaPhOzOQcrqfNlN3NJ0KWYifqgaD4HH8amioAJqSlbcN0AjVuQ0lifJoPYLXgXL9AUMGjAcZGA+CH/hjIA0CY4NDQcEZuCgAmleyhJyP490u2Ejcn/zN5WLb7yJHdMhIECUCPp9zDwItMEhYYtpBGBehEG9L6hzU9qmgp1dy0MmGeWDF6lIh/7D8qPY2YLrGhKxj5qknV024V+c2oAvkkBLZfZXIwkTt8L7U0PrGUSOsAuKckCQBTcos6h7uIAfLyKlrWAf7faADEaanA4QnOdQ31dJ0sRNRIOrQnmea1OTkn+adXo+FJy49RyDyjAU7XkhwRiR3zcy884QFynZiX48472QqCG98JIfqPE2aTh3z1xz8qle+MDkAm6QP1zsor6yQKQg6G62fu43XslBCpKPSkH8YboF8+FFUSBHM7u4aGzlVUVuTl8dKiBrDFB3BzCv7WCsSG4aEe5dDJFF8IpMNKmOtWyKCT5w+C/kpwbpYBGPHMz5P9tuUIYoLbiSCHNkSun6T4Bj2WhwDPPDFhmvB0KP/oIKMRR/dgs92+tKJ9ZTA4LRaixtyMKd6995S2LvYnAJkLTtJzDpEgmFs91DPUJRDk4a0K0BTQAP/dRgH6k0hKJQsEKyu6ALBDOXSaARjh5AgUOz+uv1KOLEKK5NnZ/GAXng9jifMzM/MAeOdRhCTCdRspi4W6JF8y8pE+XJmZrh6zuWNCqZx4K3UBYCo2ZzSNv5NpX73UQFCVcjOxhRUjOoUQGOaZzc0MS5JF4luqe5Q9Q8MVlXkIVtXJdE+6UYD/dx3g9n4ARMN4bnRI2XGht+dEKR0CI56T4lNHDl6u9WeRuelLu5gkAlDhEvHiIlbbFkE8giJt9JV6T7Wy63wnhOhMN8flMV00IxaeMZJtLC42tuDY8iVS0Qg5HAiqqSSH1J8dRQiMfHxZ5MRQh3J4qOscEkglDyEw6gA7K1gVlSgFy7uGQfDz3mOJcIHIB/82Oqgjp7S7fAY15f3hWV/A5GzT4dIJmmRIqh4EzbAAsZIw6XPiT7XKt+iMXVyK4/3s7cTFi+hCnIPNRtLtvbyk1K5gqAm5FRUNpKw7MQf27TtQhfog8kEQjD+mvHChp7wrv6qyslJQUZX0N9EAiBMAmLYDsUEAMSG/a7RLe6H3qE6ZkPyzl+2kjfqziC+kQcCaWl1cNzVsUTLc1jnOL09fXIxbvBNpM4FrVFn1ACgUfqecuKeftDQaoOLzl80XJ97xDaoxO4aeS/Ie5Vd2B3F1F4WhXCqS8Nn2872/cDPQ1oQexdELPUPlwzAPAUSmLSXRA3jCDzA+u5rHElSeGx4dzh9uE9coRkYUz/HPRH5lJaMD9R/vO0oLMkTvM80QWATgsHmRaUJomIs28/T8TN/DiA8bsqUOTNvsbqn07cSEXO+ECI19hGXICJlSlbWbm2l5pe0RX3fYG9nsTG4m0R2QhKFqnNIei+zBZDjyvPfAUa2ytit/eHRoqEqQV7Wf7lvITfDRAZietD+jH3XM0PDwaM1wcXGN5OjA2ZGO0zmxkbNIFTTB9hHWtXWAc3enF+f9zBYWzIszNDjPMg0SP7fY9+znH7ThOiwur8vVNzHRp8EaR7MXFjjxblOjymrM/GNfT98rPbxXQ0nXd4pII1d/uQ05JNKJTU7rGB8YUeRra4drulDHjPJ4GYUNUQdYUpjVWcXLG+rqGh6tLa5tE0lGBs4O6J4nRrxgLr1agjR8tux1QNC6bTPP0tDKFkwm/w9mbHR2nl4wzy8TfpEBprotJI1g3nbPZOr70glZtREAz2Bty3DT09Oz/AXcnEiCXgc90SRVTPvx0fgIAPHSS58rYAoSUU1bsbZ4SDs0VMHrz0rIiTrAwwkFGdX9lbzRoa7a4baaYrFINw6EI70tke7cia0Tnz+y90p5AODDheFhs98El2N+O2ozmaYXF2dttnkaqm165gEyTmSAmQ6DiqQRlz1TugwFFYoWXJhYYKalb0Jpe4sncaVGg56kYMo/UsI86VT7gOBQJO/dc7pj5OzAiE5WXFss6+npgXnw+jOK4pOjA5C5TD+5YVtuFrQyQV6XFp9UjbYNg3Xx+MDZs+MdaVtjw6dhNHPohlenaANcnbCx6Pxr+/7Rs+9HF1AF22rWNZrlicVZ1DwRAbLtTuukVa9xGe1GiqJsJnPPyqA7EwC/vemxmSa0l5wG7HM47JQGUnST10EIEikhchJO/1qpAz5M5eSYc2u1SqU2j1fVmVVYmh59gIkpBRDLIOHna5XaNnGtvE3UhnEg/FjxPDHcPa//2NJ2+eDBvx2lAU6tLpgmymwmX/ZYgFk+frb6/e3bv6U1GriyeWb59VRkgJlGMjJ3OiD8kalv0x8mzLZ7jYiB5p4Jk3mi76lfkBFa3FIj1t+MRMsidfT5nYqqrWG9t4R47wAmw3KJGCNhrVbbNSqoqs4oSNgT8KKoATyclLCfFvBHZSJtr0zWJiqWyBAJfX4c5tqi2Jbi3QBYS++6PEPUM+XbTMQE5zG6BJ8pPG+9avNvlk/bJmYX6L3WSFmYK3VL3QQjWTzguNZQQS9/hTrQfPHixLeNbiM2Lw2o/zhQpdnYW/UD7D2/7+hPAcJ7c4j3IvrpsGkxrpAfFYu1bb6ZRVZu/Nbgq/SjBXBbYUE2BPyM6qqKYvFRLCZgok6mSSPEj5Xw4zCVNFoRGuBcH7KGadQPcJZeHsSMEwBJZV0+ujBto201YgxMRawbnCSDozEYWCb3KwgJQwB40az8Vuq2uxvtVLcGxsmh6JUE/0Ru3wFBThjvfU68Z0RB3oR8ZESMOy/KBf2dGZ1bCnA1RBQB1gUAphAJtyiLTOJqjirkal2bugYbHjpYIfHjpFA/RvzQopejLXDulokI7zUTJhLybMy8+HXtDNlpmzDPzt5mxvARL51oJIMjp5GwwpLqWg/UVADsuec0oHpxGfl8VNEOKbOXBYA72w8wUjnjvRK86HG4b7GkdgTbCaIDslEyYtqxJWt/WslfCSD075TCoqwtnf2CURm2IYp1NSKZTCLHvhvx49N7Qv247l9ZOxeQttIsjrMDw2NheMhru2qyanzrZvqMVjRKO9WJTlfLtAmltVfDxQtbHs0dWkqmO0aGXFZhK2VLYqBAwVSMU3e1RjFNm9dsUamphmoyELeDO+50C21ZgUV2gdn/ubnGXB2DaE4F7hjIyI/zfed85/ufc40EcIAAzoUQcgOB4YGxMeK16YCUXWfHIwr/MBLrOKimByhNeMH6/FZKZ37Xdxc2HHr+B5crEZ+3ifOncRS+/uCcvK1LWr39NqPJxHBOQ7/dNsjfbsLu9zGW2JHqvIvJJX9ggL+UAUQhVYV+vPybF843GYR+zqYniRHHs4LZRuv4cYmsC5tBTfqeHgtzwfdoDC0ggWiUHDAWTOloDX+tic368etIcLFmDwBJZfTy4TeUzqDn66+OMSxgf2fjy4fIXmj/ew4BQ9IoCrPbagk5UHLT6oWoB8kfh+WrRxrDnjiH5XsYl0tVdeV59UmANzIMsLgwjwjiHpOc0NRjIE2YVsuaGWdiHdfDCZMaMhYAKY2pCccDAZQK4s/GAhGcdudkkIKRAAKwYh6g9wSw8SHtg3TeeKk+NPklAMbVNJUHskA4Je1/KTaCIPLJtYHHuVvB48rVXjH1azZwgslu4536fqe+Cdtf/uG647hZOk4AczIPsBAA8dVSL8VvyQk1gt7Mm41arYBEgAFBHO5UyWBScN5+RgRIV0ootMTi73yBWcWsV54sz0UVy8PgiirM3gDSPoj7j7910mq1+lGM+a424Zld39I5WD67QQmA9waSklNURb509mC18KzGwNmhLgJATnMOwTcfPUTlxarqVIA5GQAI4WnSA8sLTx8DQWrJy6esukknGPsFIwd5DsdgU7TR4a5I3KVJYYSzcHtPM6V8bpx8l98PBeYBChmMDKBfEaf0OhTeI0CYVCqA/UQA1zaDdJl8CAoBPsncOXvfdDVXcj9K/WCsmTJZIwnbOKa5Be6H7Y9aiIrySrcB/DDDACtBEN0UiWV8Hnf4ZogFWcZg4jkzyzpTKgwN5/qvf369XzdUI6p3AzOLXhRSI95tud5qHDEEjrmxx6kdcvsp6+7df0zv/nnXM/7W2VOcdMdWoKLg4TTaeR5bj9MMgFwz1DEXsP1VfQp+DXhbswzghUwDPIb6d30hXQFKTtii42xQd2Ij1CIv1NqxPSecMDdvYLCdqjFYmqSd9K8u+CKJW1+ZQY/Qt4xiIBxzHwAn/wiAI7t/3jqDbP6W8cZW3aCftZuwaBhkDuw1xqTE7ofsBeGjFPxOF6QChB0YIN5GkCMDmCM21haDoOSETS0aPWPjGQ5HO8EgaG2iExbk5n4FRYBUD4RyjQ4fYPU6JAdFzhl7v6wIvFvYlwf+bxhZ4MTun6t9hvtn7/TSJNiE+wl2juWwY7N4FAQNgu8FWr4IHxXgV1lCAAsvZs4DP5R7YBEAUk8U3iNGy5ickNRMBt7spHgsmATezDttcMKG31BJ//OjLFWka8IhRXSoBrBe++UeWAPNR2AIAMF1PwDHAdBv3f3zWrf4FW3HxN2vx2Y2sjj4MjbWPHhNq0PlgNyPlm95RWE9bslKGmQAKzMAsK0hJ5eKCVsAxaaoPHEZH0nEkhNKxBCSu3OMEV7IUjiuoEslqGPEOxGs3vjbKdwOv16Wp8sI0Ip1SFPX4YD7AThNALsO7W6jup52ZNINj+F+TqeZMwo8yXkh7+Z0CB6i+9HyhUYB/ESAqiRApGIHB+hbewFsYhsUABZs3sI0FIpOSAQRS4BQL9idDNSxjIEV7HYc0jXQdlw+dftrQjP1fjYAjQIFE588i1kMKNwQRz+f2hfAsrVhyqPTABxRfnb9k0HNjd5+pH52ljIG8yBrE0zZJ6TdrwrLF+H3EkFLAkz0MF168eTgAL1qy/h3Lx4XUj+8igCSUSgRnVAi2NYGkSWH8CbQn8gZORQ5DFAIJmsJQ/OzmiHgcrwOrMqzmJjCPRSL4Jf7Arg+lt4DUdM3H8VfwTMoWxlZgWcEJ9sjGJXKFkhTE+5HSp8GEvoQwHoVASzJzS259PjFmtWX/dGBASLjUndOrA1/UVqtaihIvYzOKwZBMRjDsJA1GpaHRpsx8Vok2Pqedsqj32/qn/uAcgrNNLJEugY+ubERiWzsD2Cteyz9HogRTyTOus0j6vImwaxFi5pRIKl3G/EjrSMt3/pNmUrlRQJ46VjRD2/WJjtr1RkCSPMOrdNZdaqGktTL1HrySiJIqmlKaZR6PaU0HM9hr6EgDI3qEOGi3Y/WMFRaCpppkrSplUgEABXr4T0OYJSb2guAUUThNGHYRGFYa+QF3sT28ALLKEWl/J+k5I90UuL2lwKw4s24RWyTyAhAtVTMxOsi5ANaISmhyELnOtJNi3m10mSyM2aON7AcR0F481aTWjH7hgAMLhhYTQnCbqTQc/5YxL26GJYE+3sHWKb2PoIHpskDURFsHmzHOHnBwGgZ5j7Lwfuoz0DiR+4nLt8tgNWqq9Ko1UwA/EgCWAaAE6Nf1Jdsk0QUoUke+WA+CaVpMyTNPrZohGIDc+cyupUkYcKCF4cNX8IFFY45GvopWnhdEfGtzsQjsbhjfQU69EQLzl49sFUEOH0ojY3oPjvT3qNhTKyAtgYTLV40Gojhl5rHU1VmMAyyrAa/2gwC9CU9cGI8Sw6Q0kOqzhz+GKGsSkLYpNQbGWSrGoohp7RvF2gBrzz/e3B5NuZbnAr7YopIyPt8ZWVlYwUWmn8d9/zXEZyPxJb//c+/rKzOoQUHjQ9788BWH5Xz3ekAWp5ROcHA8E672ahTiqkLlox0+CX3q0z1iSsV1VkTSYCNGQMoeuBIvKhyx6SL4nKc6rAXH09ItMWtUCNwLMWQ9kGKITXhfy39uORxhGbng+++7w7Nz8ZCnh8lczliMYdryaD3RzUO/PeSy+NyfbNCw6T3ALAjCIBj7trd+YGBqG8zsv0M8NG5l8rO6NMAPxzayP3kAD+NWzIPEF/VCYCjX8nL9omDcXUd1kJ5KR3uNhHq9Az2bpIHrtbUhF+5lsi6ceT1c66nDgSSEHUFiuZZVsyHuruDmmZT91LSPK9WwwvpAcL/Rmf8dCMSHG9NQ9CNTfCoWctzOmqqEfs0ICZH/idOENr++oTTxVXuri2AM9kfZMgD1QRwgl55sR0gXLCuuqK4QpVESNFEJ55Dek+Gf734Cj5F9rQv7jdk97k8QXhbdsglWTeq0YFul8fz1JVqHserxfQArb4o/A82FvVa06bSZ64/QEeXeOWWT10GdAo9QuNHIPHdDlCVNdLVmFGAjVsAOzpH3pCWYxvA4zTMqDAvFWGbUkyjtW/nNr73bFpfNr32o9vTp9Hpmvs8knWHArPLQc/PmPddOoAd5H6S+X0WddmuFS3jrfZ7hhPSuY0yPzqF1tEgzh0AK/OyRju6GqXvKjs4wA+ypfGy6k7LxGRHa1knXnmxRVACWF1cf/qiDOF5zWA7VVOfOLphHvqBOUIhh/joSP4a/xx6vVKDR+nnP9ITTL8TYHIGd4cvyQ/25xmve3R8ZMKyc9C/2gt90S2+ZQtfRTEiHxxwB0CMDpqenEwCpGrYLzIFsJYAWltR9bW8ubL1f9wEeKWyQIawTXsfWaA5OySCIFxb5kh9cIhc+0L0lPKh42cA1orz+0cxBd43Eww+id5NtUdjw/5oNBgM+ojkpKU15WpT+eDM0d+33EziKyzCHLDjOwFW/rDW1WjNOMBWaYKcpWPSKn6z5UXlNoClNPk2FWFVU++dy2hQ04UcB7FnSYBlrRPTbl8wGI36Iah5hO6kXeyRSJI4jnZIHDpO9t7BAIKbSXwXr9TTDZmKTsAp7kfve8ObSyatnerMAywjgBPWRvF5Ovm+NrG4WpoA+CtCeKmBGgbKq9CnRElMc9+BrDkBcKTTgvexNA8AHuhJ/9Ka3x8dUJ70jXSSWZ+Y7rf/n7kz4GwkiOK4Kij9AHfalUbSdG2ygrRHSiSraRJtyzb0rolULjln0CCt1aIqtmVxuDuFA4ccAiiqQqwCBA4BsBxwCeRT3HvT7ewmW01r98i/0EYt85udzJuZ9+a/kfwaB3xQG1JMB8S0nwIMMYCoNBgOClH1zgIoyPeuAc6wN5BU7lQACJKHZc4G0NypNitGyilAWMCFcOzI51ILHxCgr3sPBhjonuR7ldAkg2pr+zuk+eYZPmpzuRYfA7jYx6+KB4DWVsTMrEcAMZKuqOaTVbQMYbcv041W/MtECGUrV5iUcHG561rNzczxrnsdX2dauUYISjN48cFoNUgBlgKW15LeSfwXgPdVE2BV08jjk1VDp34YJsCgZO9JOFjAE83NmBcCmyUvlIE833p69XHZEZAiJkDLKa1DGxclFVVOeAdwlgGEOEa1uibbNnp9OEUI4FlJPCLxo2VemKAf25giXcAYtnnQ8qmVONSrUYAcJwZKOjqloRJEswEkXgEERWWi2o/9ExBTdIx+CgGuFMe8CWiNzafk9OgIxnDeypARwaYxHqYA03oPHG3uZNYsjdgAdr0DmJBJVbYBFCjVdo0CLI0ALOIIhpuWzmoTpExS/XkVXqocpJo3TxQEyLx+1xAgLxltu80bRhtWGCiQ7rxrgF3CwtgqezJT4ra2EqZRjCV6IgxbgbnyEjdBS88K/2HkxyH20fP6rWxf2scwGhwF8Q0spwx5fIeC2JqpeghQiNrNi6wOu1HCZhRj1Td8vDiEe0YKb6ZFcEfuZ5yH2cWzOIsAQCk0JOMtypJq1A5wziXA+a7KHFCd63XcaL0p2KIYlJT/kYFl3I6fmxqC/NVJ87SZtMYwj0aX/shQdR6iwGr6rfcAkRXNiHIChNOmSGAkufwE7n1qJiGd4pWiQ9L8FeXhK1iD+9uurbJrnEXC4ZqhOZuUkGXrQ81LgIJjAJvhYaU96KV47kHY23AakmltN7jF1zVy1d+oNfQiPqkUVOqKnvYOYWoH18OsYgk9tsCCSgNWzhZlBQvguluAc/PMq0Zw8jMBqrI2GPb9y3q/r0sQRbdOD9lVVS8Tgtf33/nAKje3X9eVPBr7+/bqRdYtLiW+P/h5Cmlu6CGX5kVxdfnvn1uNPAUwapuTvQC4bgJ0iMXXGsnCZKLBRppKwPLwDDbz2VVVEwVJj7ClEwqlIrmtb+e/WrtfDmDZi76RYBa5kG+ElEK9IYmw6HlhRzxNm2vAGI6d7+k9YzAYgpdh5x9z5wLSVtbufW7fdw7fgXMZbgeWsmvVjmPb6XTqZM8MtqlUHWevRKpU97atNjaEBhTRRDQkEW9QMVEiKrFxMIkmShhBQGxBHfkgARk4wMv9ZbjLmVEot3YsFXq4fP9n7xgTM8YOZl6+x6rRGLv97ee2nrXWszba27MBkty+nb6u4X//AwAOP/0u9SU1HEYlC+W30sKP43d9u6VR7cegzE2oErVwJneshMNGj8wEA4MYmtH9+fw7cb24qLu/e7u47D+zf/hhnWmgLyquPbtRri30vz8MgN/9EcDyk78uLwDbcwGkAcozClspGd6Z6sN0ZnPZx/GraNEJil6vSIC22DWJt65JI5c6VJYjVsYkWZY5E5qvFJ73m3pHm9AX0IoeX/1F2c/3qza8pnLBPyqOPH+aCTDbVW389QAxQMm4jPJN1KL7phBCPsriivsFq7EyFKpcMXHBvPBIk2VJjnaRLFiZvFIZXumwCmhEkgvfpbJ+xSTN17Ql+JjLHLhamG3DRtgwZnhSzptMB7ByyYZArfQvdh7Gtxu5AX6XOQtx4ydY8LTn/BBCfeOriroly8SkJlNWZju8pcr0GLNoKHtEpimjS6obfALTPON3Pe7tj384kA/b9nfe1tQceA3dp3e4PlZtWGlPM53/HwDiTt7PsIPnsGAsBGg5X/uqe9EkTXA9etSVfFs0sfhBmyoHsrynsbQwc5cqY0xobNG2k2Xze9ikSEN9e7bopCWBV3sZ542nnTDicBtsOO3Oa2OOXPIqDwCFcwDeyPQj64jBtUacDH8ev9ImxgSJm+cW3xzLgJkl3tVDWut/Z1sayn1JuafpIud22dBdnU0QUPu9B/GxiZDeE/O9mFw0SUN7h4lmVJ6z4zDZcOpQ6/vnAmQXBIhm+jh7ObcN4yIyLRir4hsqzrPfqgYmmowrRotVmYfaaXIQF97+AHnwwwdpvrWe3t7F+V4tyZGNh0fsbPThHxDs1C3XjsmR2DjXB8xLET5UX//Wa+hEX7GsXFpuT7MdRL/cssr+4y8HmJlfP9tZ6as5PwmkfvH6kcnJCfzrkOP79Ul5K8Tf/UDyLu7VSD5ISPs1bfTmlaJdC3bWn92mq8TAPCGjKPoD6I4m+fRizz2LJIlS810QzBgPY25pLW1+Gcbz1wNkfwZg+Sp1OzSeW0fAKQm2hUfwbHh/1KPn0DuNG1TwgSoJIknyu7DfSvLOyxcoqBhKL2Vm1SDjTYhuh03X3PmkrKIbi407ZqTE4d7WbnPGGF2raf10I23M8Q8BmLV8MRfNl+aB8y0Y0iJN3XqDGKG+79l2/ydJ8Hc2X0+46hM77zSS89IHje2OcoQfNrOG7qJM71bRMLRn5RJrwXYCSHWnLiDP19TU1wzVPcw8v5K2vu5gbJ9jdJ93gP/xpwDe/7mjlmJwYe78pbC6MdEGqzx+3+fxDxqufSHeVlODf96dd/WqF0zsaqr4YddLPzvEJKZryQglqD9WLtl1qbOkyloEMXovik0N6BpYkN4T0k6LZFYzrSenrP+jAbbLUezKwNksucwXA65RYf5DKwFrVd9bSfFU+SCLB32Qo7hyVEPSFo+/o5948Jb93gp5K7ksImtOV/HiUcHv5P3JDAcfi7vFiJkzSH9xxmBEpkUyGaeZnw/wny8McP1PWPAmmq6fk0UXbI/qJC7yXe9bTbU0+5QRivva2o7iwiEZ9oBiO+rrq+2rPeAJDW1CUM38fyTj5JTMutOHuf6E17vTVFR8TPDSNZ0sSF6LxSp0FmYUBaf6brmw2OdjpfwfDfD2T6ZpGgfnaiw4aJAtKyMjKy4733kPP6fJe7Z8SxUzm3oEWeDWaXCcvjXHtpKp9Y7K+62E5+cEnEiTFtF/PDra4kp/Si2fNDDlR7zoyNtYfdoJjuBow4+WTfYv+QN4vscoJxfY1iP2FubInw22aJcmUZPkPU5gPuxaBx49evPozRhbJoAjgvmRKibpsJZki823Qh68l6Ch01bWeynlBguv6iMx8/xBIjX+vt7PVyZ7RkaiL3Tp3uRJnZmaMa5/vAZeHOA//xmAcIEjbecMQ1qkH2tTsqXIP8Jw8daX4D0qsBfsBcGdY2Pql4uydUAdpFiF/foavM3LB/CMQ6zxWqrAhaih4+a9aUt3YYqUMmPV+6wyRwfT9HxHWcRy6Zcfr4Fr+QC4+fFRf10hF1iXwwUWN3gREVJvH+IyKRRMd05Y7iJZZhb6ZNE4AuQLFeSUEIdPhHi9ZPZvBaZrLk2F9eLOBnMsoqS0rbpBUoYOjo724yw9pb/UQsulLRjNnZ/Z5g3gv6QDPI/gSx9tDz97Nqnw0uO69z+ky4e4bTGpaqauSU31JiFmNtdF1UE7FJPSbXvSR+7Jv6uh5FtJYjjaLFXMutIgBnrLTlSNTw0MYEFHVG4qzhgOz/X1ze08y83vdp4Brp1k7rkBYluKZfrsLJC2jPc2CYn3b/c/1KdkXxwjQEBkU+tac8w0ieqVXhqhr0a4HVSBVVA0zlN8X0sXFZfEmipOxiR3S6qQx6SihX7JZdfbrBYrzCEziqCt/jkFutu3/zKA5xQvnu7QpqCzaqnotl9Hy/wkSZK9Q1pogLzgU10kL/jIBGRFMFFZX5Z7aKBsEZaJI+qqrkeqjMXVcs1bZp80MWF0MFUvSJ8LwUlIXJTtJrNNEjIOIX1MUWTRtnlOdSQD4D/lD+Btbex4bgwpKjyrfsWtnkjMaEQGw7h5RENyz2bW6qm8QwUomYPB4IqkD+KLkKL00HMdTO7RKtTKUFsfZAj9dA4lxgTtFMfsCRC2u98GebvbUJHhgfX3zosiUJOUod1eY/+eP4CYjPkutwpSJeGWRRuAZklJnWhaqqysDAUnJrsWXihMGlvUwoY4ReSCiimoorOHgsEORhgnOgQLPRW1MYs2XbKs1VmnzWy/pibO7VzoxMgwq8D1ELUyr3f+cGB6uakiY9mYvIAoh60vuQuc5SmnfmGA/5QO8LtzCpBr+ntnVRIKB+vshA8SnOh69Kb2YH6XWaOTkB6bZwKwgnYb5kZCK9yKjxZmCYaCIR9foac8DIpIMmE1JSMOZdUJOboiG0ZbrpVlEqSWYaLZ4rHK1pVwoLQw/Ql16+bP93POUfyFAFG9P68UgyD8R+OQwYCrUpMQFBAAa+r3vUyvuj2LUhkELpdIhMOiHh9NQgcwLnG7qpSiAOsmmZNHEG8wEmHzVLkRFx4tM8ZQRsjAd/1uk7Q8cGt6+jAuusVrmYO5lT4qyOScJbufV4D/zl6eAMxZA0cQxr7IRaW/MFv/rgU6KpMCC3705lZfTesP7xLMWglyKyLRChn5TDgcXpKVpXDYJxFMj6Q+YWfWoMov6DPDjCEmiUYw8+JB7ZGi1QyKKa3WljAUNddxSfRStP7gFXRF6QCvYgPVOWGYFhf9RQBv5Aao1bJ6xO5sgHcbPMf8QkFVAdvqH6CEMC/Yw1A6my8crgyHFd/S0lJMkWNLMZu8BJaKDc9WdkjiSlAVozw1SRLlXhq+zMtIhxJ6D0c4bhrtrL5bMlhUXFxiEF1TKxaZD/X1tR3yjCiMOq56uMh6rkmyvwggBACf5rDh8mc7c33IYjqz+/63KOETBQRAUkAAfACCJkAz88gSxCdGYpGIwmdiEVmJLS3NSB5SSUSQkCqVVvPEBCWJY9LIG8gYOcL3tok5kUEEOtBMh7MY2Ng0zHfPK9FYJsOhIN/WUbXDtpZrPJ+2YLU87xqIX547i0GThKw5bTrHrTLDA0IBW+HEWusx0eGORByCLxaJxRzS+MzMjF9wRGZEfSwWs4oxYPUwX6UmLhnhGsY8Ipu7HkHsXhrUyQtvlgWmKBLTxOZT5j/QFEBCMk5MmDM98mMD3HTOPAZb2jIA/lt+AQ7nsuFX2BbTZ0yvf6TyWtWCYx67z+QamSQFrCcFbK2vObBJ4w6HyB1A5xAVh8PhZ7MzDq6PJClGRDkWJoFFe+AwISY+0gVZtCVQVtjnmHPyCsbgssxEG2fMMhm1elWCcej9qZBWXWcdqJ82/3T77HHcs3SAP+UF4PG+J5rLz2XD6zJNKOmyajE42QkAl3yajnDriz1NAR/U17TV/silWbef+R3EDg/dTjx2cyfOViDljFglz5ImPls4pAWbsaQnXMZA5pDv//DD/rcv3ryZ4qaJKT2zHdUexL1Han3CHZEzx5UVjci0ciSCcIHDz08A3gZAQMgjwOe5bHhTwZx6xx8AHGSmyrCVpQQ2llTAvto3Fib5nQbBiTbiswLOAnAycXycO4GT1NIt+WKaeEQtkIf1SnSCxMijsOMoAD7AJCjVuJRFzE+xtxgqcwsG2AtyYNawnQ0QRen7Z2eBz9MAlucB4L9lAGyHCp6dR9vuaQCzTViPWMrSJP6WANa09d1602WRmKRjkhPsRExu+CXJ6eT+8XHNoOWZiCozsl1TRJPUEYRMBM3KIpLCEa5mNDQJ+s67heKElKA5PT5CabfEW2DBmQDpGnfOBni/Pb8aCIA/Hf+67wAQ3erPcoK31+ji/sgHPqyTwy6WKYkPmgJiaQy8FwQBtKGR6RobdSwwaoAqSk63e5a7ZzTxK5EYyQw3J2OyVv+a44dUmd0hr/c+QRPxNPt5KHkA2UqTTwWnfKAK8OnZyx038gsQpxGkPC4Wk7S3Y0HOmdVAaGDfnJRdjLneL8QiAssULEeAAhLArqhdVU9BEOgjWNbp6kb9Eqw64Hdo4uQOTRFFyg1JXNLcJOSFvEBVLnUu7zB+gKgiwrInbfixsA0zJwWZ+YABva1zAIQLfDX8lwEkDdw424bLyYTbpqTsPBCn9Np97LTsbrXV3sIfTwHVaJVYhkiNdQDo5IgqJE7JN0PiCCBl1MRmC1FSY7JBDR/N8R7Mp9zTR9+8uadQBQypJZT1dGm38IrOnBMgrVQeTpVMygHwX/OpgU8BMLWuOFvWEERw/EV2PRV1uKR+CTpdipTAh6bfqPzwBweNZvmUjgrO2UBgXBWnpGmiOyC5Y5o4BAsNoEN6O17dtczVmSrr8iN8sICrR3A7nLqSwixvTOuPXWf5QFhwO3p2nGhg/gFiIfmZNryJNAat37MnNaGBgiEQCAi6gN/vl1lKaEkq8aNxbjDcYdLLXFJBk+j8ozr/LIlfCoy73Ti0LCCoihjBuyiHQ8jLV0QXXj1p4VG1WOPCR7uJyjmC0ym1lGUlVNRMZNpz1tQw1v1uAGBeNfBfMwGuQgWzlnUmuw+volaEwyAbK7L3GDADwivX1fmdEM5SosxpCkgAERfCS5EZBwxWVBk2NCIoNzubA8huxiGzouB0JMUveMIkHq6Ok81ilG6CWhizmilWM9HQkhFBtHKWRFswzJQH0kVnW/Dw6urTEw28kVeAGOU8ew6A6ZteM2abNjiubtpjyFoOXt2oawQ6yRAggD4RlmxIqpk01tOV4geAGHrA0wGhZsWNOE7WAH6aHlJc1sTJbDFVbGoRsVJR6FNY9uCxzYdfoReat7MPKyprwmCp9Z6eEgu66GwLfr7+Ku8Af36aDpA2g3yXBVD9L4d3O9ST6bPC8F2DIeCc9XHRj2TPrAiqIsrHSvhi4UQBl2IR6KAD9urnmh3rmIDXOqG/jDvHNZn1C8mg7JA8lajkxCQftBG5JgpiMRGYx3lzcXahGhdiVY84WVOvGQCz9q21r2/QKaepAh01Mr9oI+k6bUcZKTgAkgqeSgXLtWkElLOwxwsLE/oLsmKI5IQR2tSPZt6IT5BjgoKtIzSRqYBuPK0qoaGpX8doJY3ADHUSPCIJnpFmHaoERDUom5gaW3wSsm43ovesrK3LyvbFlmlqB7SaMfWRvlf41Wb7CcDbq7sXB/i/WCPa+WitTwjg+qv2VOuEzMkSxCw6wgZO8Em2D1RgdyZRNrsdZtjxuNvsdpsFlkJoWSEFDJ8o4LjKifVXFFxrxmG1daO9VVVNTAoE/AE4yGNbdgo+bZQiOWYgXCSmQkCUtCnjLOmX6MDUDtq9hms+TfA2LHh183kK4NPfBhupB+1Fm2cZqgpKfxlOAny1nq2CBFCtMaxRv6dbLiEreyitE/Qej8fKuN4qB/xacucRyUQNXGIQ0WoxQpVSCgiAFG0MpegG+/DKlSfqDsQWeE5Q12IKxCnIDvCemWV+t9tBJYnxcb9OEHSj21nstO7+tkU6t5yGwrjkTIDQSbLgzeH72vdvb7yuumug7ncXbdshbBcWPnlNS5qoh+Dm6gZU8PapiUBtuXa7RMcPoOdwViKzjWkyReYiwYJLc3jwZpGBgtyaXhuHSIre53ZANAUkvydoh7IVFiZ3hVWVDF4bJYCq+AWq3pDDk4govnQ6A42D2yWlxWfsl6MsEC19ERaxwvw0QEz5DG9sricB3tj8tfhSkY767lx0qxLrhDupeD2sAmxfX38FFfwuEyDWuyO3weIs5PnIErLj8H8NCgbSPD0TBBRbVINziUx0qg9NWsBQtYuLMHHV00FElu5PCwjlJVgyTNlPpoyXQ2YDgn8W6KCdkqG59OzNnVjwMaIej7CuTh1lbsCno3JgwWurz0C2HPy2C0Cc0Y7/C69va6F49hh9kW4QQFUFkclkAqR+FCjgyj1t1G2iO+vmdzM/WLm56EyOyiIRM5Jqp1t9PHZ6EMJBEdLAsk4VLSh82FJHpAWmBRVQBjlJqGvp7O4tOntPIlpj6cz36pFn0Q5ozE4AVGYOiPZq62uvVIDlq+BHV41tIhcvCKojysLtVeqp0r66BhVshwpmAtRqDBtSxy00TvXV3T199c3Mj/KoTwIxHzcbIaYAgsAsvtkRiRjj7LQI3E82jJuXva3mbkkndmZTfBZF0dDUbBAMTb13aVIu167Y6wghaAe0AAumhIIAZk4mwcNvrrXT96m/mhb8sDTm4omgttSg+PX9ctqdji59rzJVkHasPMP4ROtXWk8q2JIdh/2OyJJdBLGYxepNmGzieMSDk4rVItVSQmWWqYc6EDQM/tcfqVQhSdl2d39/S2dpcdmVoqqU18ulgHa6OKO0Sv2Jhp9lAqRyO7RjDWN9OMHf1O081Y1UjLl4HiNcK6QLKP3tBnVw28xSQdo0pTXlKd/ETYYKmhE9C06ZMJ9ZClvEcQcmisJbsuh3x5aMCKL4Et8wMTVNtmUgDDj9dVeLSqtVYmXF2uH8GYv98UTqw3lSjCVvdG1WxODy+ygrZe5TAr/nsGDsYoc3av+1QE0edBdPAykMHy9S3N58BoDrqgpmjOcIoBZYnu34cJfRrC9jiwukqo7pR0L7cXdkKbwUDtlg0OGlUEIxInvGGMLGhFkHSHqkDIAoP+h0jd3V17Guv6n/Go3NPl6yQ/CAah2b8HfDr1bb05tLUINOuKfNtdVh7N4dfl2RPKFeawB64a02ybJaQelrNMhcXdtch6ymCre3ab74WdKq10gFWwdcutNVwe1GZnt/YJGNwVBlcIWLMzFw29PLywcHC1GzwMTxmTCqK1BFASFWZBC/n+MwbT1no3B3VGftLk5a758FCfu526ggwNVAAZ9C3TZWMwBCJcEPurG2MfxseBUtOgu0A5Vou3UeosjxkngcJvvfn6DR5t8/aent7PzbL5twhhubv0AoNCdV0LpY86Bm0Xp6LFVYZBAVRZHkof1DI0zVHg2GJqJWUdrx2mWe+FZ0qwC3vhWcsPKYSQJIzM1BPY02KCOGblaBtZRVb1MPhT/fzqOsXzLeam29ZdQUcHV19Tm2/FOfMbWEsPrbL7+8JFlf/+01LTrU8u58xBAaDbPj8kDB49KSb76+/OX31PnsScVD9OnYRge3iivXOj95ufqUNHJT6phufVA7Io9mbky9dM0wOxOJuST2LecmfLJv7b9QRLfbqijetwcc0+lUqz/cDWiq6BKAzR2jtQxjyQcWDDEaDQFFlpo0F0v6SE34PkI6VQNuW7DRKOT+q/XNl7+8/tuvv/76Gj0o0IeupOTqZ99/0Tza391ZcoWqYFr1lWoxeXGCqRppNXWNvnlTO4GoWLUn4vqk6sqndz755TlVFP6vHO170DptpHJmQXoY4eOxypDHqdh8HZULZKoS3uETQwt7R3OSZF0Bo9AI1wBW7nkZc6rcehTmdCzRt+IMlo7qgYs3JIfbpS3N3doxbLkELAzWxbYH9QOeb1ehgM83f/p7d1FVBa7+esWT6mI0kfn02tWvvr58+Rt0WL1bnFokd9GBXGqhfiqvK6b+t19SF/qrOEPnGCsaQpYWffZZ539T0WFj13yvBtdq0fUWZPYOcsRCc/5xWCVYbGH0MK4XmNwR2ttbGEGhRjLhQdDEZIcGcIixWQAkpRRmNYDzCDX0ZHBZ6C2kuzdooIrNYMF5DvBKoxKFa761wl8i8g6/7L/8dUkVQKUaaalny3zx+c2rn+JwguvHexjz4QK1TJAdh4Qy/Ff4n7RzJK5Un1wBAH6PYwFfb9woX5Nc0/Vw1ybdYDrAKoO5MjgUIFcHBFvCOCp4FoFx09D7MRlptk/YmXfBzwmemPoTLwRJPxPTFM89s6TZMh93qIa+01hRvb1dUic7fTYpa+oja+zSJE/dan3Q12P7eRgd+z65+fWXd4qqjkGhDZR6ztuXn39+hwA+KTieP6GVRXmy4dHilBMkG1ZPUSQjTp3CUVp0lVqDP/71t+fDP/MVXG7botkwmLFES1o+2hc9qqtbGGMO+uRlJDKHtUY8NonvCIxRoA6Fgj7Rqa1FWLAwPWltMKow7gZAYiq0NOgEHSVDYTLonPwej4pz8Ms198y7r8qf/9JLfa6/Kj25/TDgqzgWBYd6fFUCgBXH18suXstKDYdTyxQrcCAanMUXiCPpRlwN06bTJa6UPf71b511ZDAP+hbsRLDgZOHyt4ktWVkBnokVLkc0O+V+/3hMYXpHpDIUPfywE3ByPr9/OGKGy/RYKcmZ8/kF8+HB3oiVaeqJH5QZXIBPCbiXANaFGnhufkYyiQGL8PfV3/5W+hn43Sy5UlV8YsB0vNbNzz//Uj0dqCy1UJ3aPuXLhlsKTmz4M5wCdGzEad++cxMEq64jLg7qrAt9RNBs6MTQNeWJRnVQMAV5jEvkRAImzUiJQrDNSAxgQnvWcVQZFCXO+TjMdmT5/ZZZHnf4pN24VQYyQV6Bp+wxcxP3w5na9QAY2t/NtTnvbhPxg1PukEbv4vCfEvD74k4Rbn7KehBAcLjW55fxJ51YcCfLlwWTDSOMpNnwV19evpyKxBCKw2jmexMEr9F1FfQK9kUiuGgy9J7E4sLi0qvdugCXJEaV5DBWq9q53xFTQ64vGgLQqVn6sudwX8HKItKuox4ZiJc67MquNI6xIBfn37/QC57gGAf4qNKBVx2KnWf2kyksbVBWiN+0kTfcxYjwU5x+8sXNq6UpSyUD/oZO5fn8puqWKo7Pps1LDE7tdWC9KRtWz7CBEatuEAqfsoM7l4kg3cGybsFMBNvuWXj/k7Szff/risHpMJtdy7IrClXy+GflCCnRnp2Z9w8WRnzjWuo3JTGrMRTc24v6yD1W4uH+zvgSgo8kCJLiogcdAJ8wBRGTkbSfwa/gap1+5BbxmxOpB21Z6Vfgd/kOLPU41l7RTqvFCS0wIM0rkbmUCBmlrAsXFFjj4+PWj9B4UsFMN6ipIBH8lAgWtwjmhb5WOB6jjKw3pYQocCgIrcG9IWxWmtcLTresHwHJOVkUZa9JFhykgcEePS01dW0hPguCbYbcZehQ9sDoD+JO2Da+/JHPAaA5cbQwpbQUnLU3vttgXqhtJf0T64rA74rK78urCBVpGQydTJZUwOPQUtbMtCw6b2FEU0EihTBCKngZZzrRLQOvYxX86gsQ1Fr6gqCvR730EWtd50mxqbBF8KhxdFliEN9MRFESQ2bRPxPrMI7EmW+lcg/hQuBOmLpsE8RZJ3fRCyZcWDZNGBU1t1kcU6JkwvGhMbm/4o/bGcHpiq57MATcRrFR5fcN8bv5zadFyftepp6zCgd4rIBVZce1LypG50/+T0oFtXuGoH85I5BovuQOYYUfpLNUuwV9FMbTWrvoEZuvpJTwbiNf3oNp2g2orzQpscqerS2XiPyYDDkhcOvQkJnHExgQx4zROTwR8ykUi19wSzgcmnAxZobKLi7zoYO9HjvNvWv9dbLxFXfW2VYGasiRINOBGRSXgh+u8HsoWvFxBph0gAjBUMBUCIEC5i+EaB2gGOtOpZ1Qwe9hxCCoHolQkYpmn31Npv3VNYot13t1inb9Ayv6ut7qJMLC7UbJO2+Sm65dL/vPIoM9enS0YKVQTGppDYz7bDbr7wdjNPZAkKZxXdhoSiRQfqAyxFhg1MBs8/N2icVRlO0fHNzWfvFpKSwoGhU9C7V0BxdMUhPKIdWffqXyuwNF0276dfgdzQGmFLBYU8BrOpaaTspbJlNXVViguV3NbaQIVlWkVJMIotZQUlqhOnAOC4IZ1y665KbBpB0XPuzFoF0rqCBcywmcE6rMEMDgnORHOtizdzSF2gw5ui2M/BCsFw72ZQb5f7ydDWsq2RnHd5d2d9vdLi2vAUe8ycRxgs6SxNlJLJA4aMT4EnDoGEfmRkECggJqRGlFhAhcEEhkgAYBdzcgfojrR9gP0G9wyzuFfoE+z3lmTifL9rJA7APkAmbOnPmd//Nyjjfz/DlXisnhztQwzA4eEO7Lb/7H+yp3dCvbnI9v/gIxZNUMQSJT7BjsQdFDouTAzGmAH1IFgKd+AYpTECAcxLyyBIeKKzVat0OXoJf6KUEz3z47iIAbB8NmqNF1riAIOav6yXQCu3dShwKPSBpoG6GQFsqNfvzhe8gGAcjJkHDXf9eaEO9+WK56QJbOGKyJNbQmNdwCy6oqvzyi/rn6dMvotVIov0GqeK31RdjERy+J30FkV5ddflRBI78j6nEoeoevgU+phnnVKKjFXAnS3hvnwzXIVxQJwjShEWxaCaYtrVIEEcKDjO/r16W+rryQjIDnO7t6O5t/9/797YmmBZpPHz68O2lvpo35Pz88NUOPP+K27vs7U30DNw/+mnc6qrFhttdaxi9w4br1nLEvyHYkQS5zRsWrj98x8js8S7AULNDLCEuvLkCQICRiE474KAqCs5ITUyYBgoJHkM80AliViZFrrBxwJUC4uu1ly21JDL6AGATTO6VsIVvexMxQKJ/LTpSgGLX+8bdhJxs4f//h309NAw4Afw08IR3plE4ei88O4LvKpIqV0LCKL86/pOW+5PwUP789lAHt4ugc63UjIK8FAx2vZAlT8CCC7HiVCFJZynzl+DKKCJND7XrE5PD2xlk+1XuGaU12a+B/aDxmqbpkgzrVTbs/qdIbZVUVDuKtAkS+UGk3+HFwYIJY3fTLpUVzdJ/K4O1A9Hc5Y4LHvbSoOCXOr+bnd0wOrLolTCGAf1/z2vblH+HobRNknGpuh3AiyE4HwWPxE8wkhBb9GBHKIKte6zl+AY58kRkv56O73qJkwtnvZBPWqzVVFGUFgLHmA29++unNGz8bIdm2rHY6+JIWwRcURRbVtK2HN5O+VTaNxXm9tUo5N1egPhB8PV+wkmkJ9+64okd0iMlUJmMpQfzAjsmBbYU5cBo2cZ9DDfjq9tnXgUDJ3hFoO+IjSD3yw7Y3NSqtXPeOSTUbMsV5a+kMrkAX0KFm/Lx6Gs3umr3KYlEqlUxzWi4Ph5bV6U/2N5KtQop5CYySBf4rq1Vpsz9p9zuWNSyXp1PTLJUWi0qv2Zi11t2Uk0F6uFD3sxOtvIHSihyCF60Kc6Ek8Tti/I4YP8+BhU4gAN9mbsG+gFLGe/2h6rVYJ4LMjXeTotspjXKM6zWsaULfCFVm9ylg+BaeD/++wRmnnpfd1Xz9VGy1RqPbWb1x1zzvAVJzOuzs2z9PsUFRmnSGU8TVO28+Nur12e1o1CoW1/P77vI5NXYygwsYnWRevMtr0wntMg69CYL8RAo0+m6Elp/4fUcOTPfZ1wKBr2AXvAX7DZwphNo7Xgp7QZAdLWAWo5avXJ/e3CN9U8ufj+bP4zg9J74jEN7NdjMYZMDijuOMxylgupoXR+8eK8ZwowR924pae7roNWaj4nwFtFLjMVwQj8OVgwG8Ew9GxCFxZZxUt1iv5ArldoTFEg8feq9KbdR5AEJ8e8TPy8BB3QgEvqYMsh0ndl9tp9hAMEoEKRAyEVZl3jGS1viQyRAfINoeGlqu0hitu89jJ54ZwHPj+2bJECgAQKZAEyTUWAx1ECHxkyclaO+Nl9FV/BK6CK8awGUYG26b17kC5Ck2N7o7IyTZIg1G7osfMn7HLr8aBUB1Cm72jT+DvHYxGIBTIR4GcZagMy7CAxChQgixWxXNk3+aSPSHZlbLnfQeQUrr+xW6HqjJkxMxRTLohvNHYyJQ8KsOF6Olc0MfIS2UbZyplmS77N6vi6N6s5LXNGNqXSa+o7XDhfU38ffLb4/z8yUQ2Qpsy4F5Jg6YNiMoEkGaKjE6RYS66GtiT3Olz9kvwPcR3z5A0afhOwSvKxWMZ3eN+gwCWguZetHsLRRxo0VHZmeiZvPeuUD/zLDIuQJarRaGTYibjxA4K9cn+ZymYfflPXYLuiMXH+9Br6g++YEdevzIc3BruaUM7C+nA+X0Die4DwRJZRxhhCUTkiFGQ87Qwwh2xHroPkAKNYxstgCmacg0f83y6Xw5zlxdjNcVS8TTh/pz5uomnuquW7NGs3Kdz2tkhUI2axiQxx+o7zIbmN+J6IWphzV5b/Wl/CA2kv/axC840aCE/u0n27TPPsdUrHKCEdbN1QN05KoQEXKGtOgoC/oljyTaEf3Ey85YEy9rOAV95iv1dSpz49z3LDFmzFKDG6c7ap5ohWxpWh5CvdNvty9PcUQ+iH9wGBCjLipLZfQIn434uPyQ8GXiBb/9wlYDINkfPvURrLJOpBhxjnkkBBaQTiTyGq/fph52SZ8eM0O1oJ0CuMvvoGvofiziNr5UxJo0GRZyj+vxIH7fmxq3qUF8eXutmZ0YhDJZZT1kI5EY/ncMuPUZH5QMbk9uiYPh/XlMJn9x5Yd99d1mrzWF+MWyAdoDb9d+TwQpk9hSOBLD5zg75X5MzxDdpaLLg4iNX9Hlo9GDg8RBIgE/wKLRfUAH7CTck8huAoKtsBnS8rNlHDR4m8qM171QqFQTBMGFodaquhTeRYzIEeyALMpWQkrWVB88CsccH3kvyQ/blaqCj99Xv/tk2/YFpuJAucYIKqqObgyOwf2YI8Qe+lyHaIICGGvVpC5JYdckSderdi2NuznP5e2aCCfq5l4p1Fw5TjcVhwNl7cHUNjsq+02qRnATV7OrOgwm+cdKqzSW766qDQ7g5TuOD+SH7lul6h/8F/n9CRLI1u3Lb5DgNEl1LrgGR3j8AiFjiPUXh0jG9rCyLMrMFEXwuTrAxa7TgqUdnR09hCpzKG+eZ6HsX4/2tKGAWQAw/dc3BRyKGx/Krz2ih3HmzFPfIQbqBOIL62mF+AmTQoAS8P+NIJ6Q0MaoKtEU/QgxL4AMgSGEaQpHHzNZrKGUwZWT6FHVbAl3qmXtev2v50bI+BYCVimbZJscCT2XR7iPGOrdPzU/PvTeSJgX1ztyX0N+fAeydYIYB+HYjhHE6kra/SWELkOYLgb1pJ0WX6qEPyVkRwpeelrGcNQOdSL4xRUQLDZCJeSX6LB3QgukeIylWKL8IkXBPaShSR14syJ8/2nn7EEbx7Y4LutbtmRJ4yagNXqJnWSGyZjJw6OdbYINLpRdNRJI4CWQHly4MBi9xoV7dxlwX235mDLNbrlVyvTYfZci4Jh3z7lWFI9x3CV57P0VM+BhONy/ru75vDpF+T6CsccDptrsuvD+8txLoaAn8frNbGirsylhriEV8Rc84dFftFtA+2jlw1Fh9MKrHVGL/f2jY1JGaZDkD/SDoKQexbWnj+vDB/ifEKyAA+p0moROh56LmcYo3ldUL9991BjEh1mi3R479Px7ORRJx4MQeubrEubuBMAYDw5EoiLICEICZO3nGIDAHl2t6DFhIC6kW+tAGTQN4yCEfAEO0753Xc1s5Z4ri1x+QRdMoC4ZDOC/onib8tGtTo+/euQQSgL3ovA2WM175liqo+vCoAY1pECsCzrmnBLwV3qa5/JRukSqWvv4w9kkTpK08RnKivut/YDe+0IJ27DHcs8FrFs5xbi9kQFZMci8CnNy+aqdvucQLI17YQQL7LrJ4dNgFdcFGq7enO3QvC/bfO2nvvrCHz/sgYBf40mSZgK2Owne+Nk8NDCKbzxDbgwCv1w+UPAkdgiFssq9OCK6EifoX1bzqAH8Xn52f4W99jTPyjM3zD+Iese4ojzmxqbi7B4F/LwmYHOEbiQDw7v81FhlwWu6PcZUZ2d5tgMV78ft93sXt59sKNwroBi2A4S9/HvOmPxmGoKI52fkqAMwcaOHYe5RYPNdrkXb1WptHCyIgK3jj+lkTcDLaP07MD9tmkIzp0ieKua5Igl+8tyYON9e5ACWwL0SGt2EbjxCf5xrCOkqrKyOB/oT4KwH7VC8Q8i5YEW5eu/bvbE7vL/fu6gPGnECAqbQtKgftprkbKzX1kcRSOIMGuamwDOfI1/QZ6FBmi2uhY4wO1OPXYegF0Xu1VD4krOSsJMvjWYVebp6Us84IZDF4M6DuPDTj+olvm278/t5Nwxc14vCyA8CKFvFw17rX3PPiweHGxp2Lh4tUS+cAxaP6SG7lh2DfGOXbj9e4V4TUZKphDh2sJeD6WqW+e5TaM6K2jVBvLU393AwDmTTEM3pfBj4URT5nuNaZRn0g5ph2Ft8s8t2sKkhliqIIWrn+BG0CFHihrHOKKbyyZLKvTZaWXaQYFi/3KtuZgW1JolvLy8vLsgfneaqD/yEKkz+9GNPtyRN4TT5WxgljTRNJnFUETmhAgXTSZLE/vCdrqm8KUNl6xKvKK0bwvJCq53RgjIF1Bb21qi+P+pHVD69jMHL25HQjbr19ZXtAK79XpCBAt/VS0UBN4PhuOPBGZTHSM3ahJEIchBiFPzv8GdXIseGJlm6Gw17h829DVN5eeETyRp/2rT3qdUbB85KPkHh3gha0S44CF0ZiLhbO9KgrffHvutQ9RDFdML59fXxCcx/pWQHcsUEgmCovc9DOvVINXS8kJgiI0y5rV0Ga0fkkHUo8huSDxANU3cosLLBCUnVq8DGOgAScx+O+gnxFE5BNiWqHiLa7s3dw0MTU7nPscGJlYPVzbLmw82v2eUr0NCUiako6eMgGGHHwzoadePAoRRKRY17a9BtkeEGYdLt1aHu1KzhCwWnYeeyRQdZQp9GsFaRF5X1GwHB4u4e40Ay05JURCk+XcWBe/d3U114atAol3QwFY27vZOjCxISkfGa6jp78LDqgyE8rBWyyavcmwS2BXmXc1yP+NMwHlNiiEq81ToKRDxD23yLJH9BdmCtcwgzVWloVlKc4iRxYPV+eftjz1YViEWdmvLD8bA76JEBm2vw9/vXJ/XRoA9dKmoTKdimAY/szaKIvGSW9IKznYJuW2VD2LIMTY5iQhhhwzIO4gaBjh7dzKYSt4kqGGVLzi26roe4rvuD4XzDv3FUjZfKZsmW9cLjugoFXZZLllmUeO25RShWECE+7FYvisn+QUASXdv63ASjaJaIwS3PTC6VJRDv/whFFTVN4HkD4XlB0ER19woUK2oAEAeGkefBKYCbkRDZ4i6LvCEVTdMqlWykRB5ZmTwzQVS5fwiqPe71vpxDAzJN4sCNwpjWFEhhZSKDgLtRFFVVRQL5S+H+YfCF2QJaleBDGkkQgXoN2soY9XeOLjNUa0rDmDaEMWkQp2k2hv37YjHdMbvMUIoQRy8uLlp4KTQFJ5yFMXv3y//uGP1hSIXpu/HUR8ATF4IQDsAh0J3dXN0+27tliLKzql5BHVAn3tMom5aOQQwQTF1b47bCEPRgklBgetCgqlYwiJkAoffMPXKGaLnDQf9rH1ooEMVUcLdJMUQx0Kfvd/tDd/sAH8NwxnPg+voELzylIJZQaRzQBvJofvP9+y3zI9uRnNkdAMUEvKA15klc08A7FGQW4+Fuufz7r19NbgsMrfSf2Wz27t1wmABpIzUVY0LayAMssiwWV1d///Ub24Hb0aRiWXYDwIM02K9oJg71A+THnyuVyq4hAhbJhMQFgxOOQ9/T+ZKXVxMinRd3VFQYfGGIw0GfoR4TepLtx9CTS2FUI/VYDLOTcjSfkw45fowknQRFGZtypzAGNxqxXHgnojwjTviheUnKMWREVebtycHqsmBrsfhj1xf9GBLpKWVNpfMDv6yUo2y8jRQTrqbMBT+PWrq9QwHft46/JD7xuKIVHWRdueXyz12fhWUu5AYFhEkZ2cS5H7Eow2cWWijg1W88tx2GYoZ3d4v5bBhXTBiUoWiSJY+7H1sPy+XylrmR5xBL09tKZbPpqWhG0ap8e/fH9z+ZG3kOVTO2NoxVUZDKlm0K3KvAYDAYDAaDwWAwGAwGg8FgMBj/A/uLuFO+OuVYAAAAAElFTkSuQmCC" alt="Escudo Perú" />
            </div>
            <div class="logo-center">
                <div>PERÚ</div>
                <div>GOBIERNO REGIONAL DE APURÍMAC</div>
                <div>DIRECCIÓN REGIONAL DE TRANSPORTES Y COMUNICACIONES</div>
                <div>DIRECCIÓN DE CIRCULACIÓN TERRESTRE Y SEGURIDAD VIAL</div>
            </div>
            <div class="logo-right">
                <img src=""data:image/png;base64,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" alt="Logo" />
            </div>
        </div>

        <!-- Título del reporte -->
        <div class="title">
            <h1>REPORTE DETALLADO DE ACTAS DE FISCALIZACIÓN</h1>
            <p>Fecha de generación: ${fechaActual}</p>
            <p>Total de actas: ${todasLasActas.length}</p>
        </div>

        <!-- Detalles de cada acta -->
        ${todasLasActas.map(acta => `
            <div class="acta-detalle">
                <div class="acta-header">
                    <h3>Acta N° ${acta.numero_acta || 'N/A'} - ${acta.placa || acta.placa_vehiculo || 'N/A'}</h3>
                </div>
                <div class="acta-content">
                    <div class="acta-row">
                        <div class="acta-cell acta-label">Número de Acta:</div>
                        <div class="acta-cell acta-value">${acta.numero_acta || 'N/A'}</div>
                    </div>
                    <div class="acta-row">
                        <div class="acta-cell acta-label">Fecha de Intervención:</div>
                        <div class="acta-cell acta-value">${acta.fecha_intervencion || 'N/A'}</div>
                    </div>
                    <div class="acta-row">
                        <div class="acta-cell acta-label">Hora de Intervención:</div>
                        <div class="acta-cell acta-value">${acta.hora_intervencion || 'N/A'}</div>
                    </div>
                    <div class="acta-row">
                        <div class="acta-cell acta-label">Placa del Vehículo:</div>
                        <div class="acta-cell acta-value">${acta.placa || acta.placa_vehiculo || 'N/A'}</div>
                    </div>
                    <div class="acta-row">
                        <div class="acta-cell acta-label">Conductor:</div>
                        <div class="acta-cell acta-value">${acta.nombre_conductor || 'N/A'}</div>
                    </div>
                    <div class="acta-row">
                        <div class="acta-cell acta-label">RUC/DNI:</div>
                        <div class="acta-cell acta-value">${acta.ruc_dni || 'N/A'}</div>
                    </div>
                    <div class="acta-row">
                        <div class="acta-cell acta-label">Razón Social:</div>
                        <div class="acta-cell acta-value">${acta.razon_social || 'N/A'}</div>
                    </div>
                    <div class="acta-row">
                        <div class="acta-cell acta-label">Tipo de Agente:</div>
                        <div class="acta-cell acta-value">${acta.tipo_agente || 'N/A'}</div>
                    </div>
                    <div class="acta-row">
                        <div class="acta-cell acta-label">Tipo de Servicio:</div>
                        <div class="acta-cell acta-value">${acta.tipo_servicio || 'N/A'}</div>
                    </div>
                    <div class="acta-row">
                        <div class="acta-cell acta-label">Licencia del Conductor:</div>
                        <div class="acta-cell acta-value">${acta.licencia || 'N/A'}</div>
                    </div>
                    <div class="acta-row">
                        <div class="acta-cell acta-label">Código de Infracción:</div>
                        <div class="acta-cell acta-value">${acta.codigo_infraccion || 'N/A'}</div>
                    </div>
                    <div class="acta-row">
                        <div class="acta-cell acta-label">Lugar de Intervención:</div>
                        <div class="acta-cell acta-value">${acta.lugar_intervencion || 'N/A'}</div>
                    </div>
                    <div class="acta-row">
                        <div class="acta-cell acta-label">Inspector Responsable:</div>
                        <div class="acta-cell acta-value">${acta.inspector_responsable || 'N/A'}</div>
                    </div>
                    <div class="acta-row">
                        <div class="acta-cell acta-label">Estado:</div>
                        <div class="acta-cell acta-value estado-${(acta.estado || 'pendiente').toLowerCase()}">${getEstadoDisplayName(acta.estado)}</div>
                    </div>
                    <div class="acta-row">
                        <div class="acta-cell acta-label">Fecha de Registro:</div>
                        <div class="acta-cell acta-value">${acta.created_at ? formatDate(acta.created_at) : 'N/A'}</div>
                    </div>
                    ${acta.descripcion_infraccion ? `
                    <div class="acta-row">
                        <div class="acta-cell acta-label">Descripción de la Infracción:</div>
                        <div class="acta-cell acta-value">${acta.descripcion_infraccion}</div>
                    </div>
                    ` : ''}
                    ${acta.motivo_anulacion ? `
                    <div class="acta-row">
                        <div class="acta-cell acta-label">Motivo de Anulación:</div>
                        <div class="acta-cell acta-value">${acta.motivo_anulacion}</div>
                    </div>
                    ` : ''}
                </div>
            </div>
        `).join('')}

        <!-- Pie de página -->
        <div class="footer">
            <p><strong>Sistema de Gestión de Actas - DRTC Apurímac</strong></p>
            <p>Reporte detallado generado automáticamente el ${fechaActual} a las ${new Date().toLocaleTimeString('es-PE')}</p>
        </div>
    </body>
    </html>`;
}

// ================================
// FUNCIONES PARA HISTORIAL DEL FISCALIZADOR
// ================================



// Función para mostrar las actas del fiscalizador en la tabla
function mostrarMisActasEnTabla(actas) {
    const tbody = document.getElementById('misActasTableBody');
    if (!tbody) return;

    if (!actas || actas.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center">
                    <i class="fas fa-inbox text-muted" style="font-size: 2rem;"></i>
                    <p class="mt-2 text-muted">No has creado actas aún</p>
                </td>
            </tr>
        `;
        return;
    }

    // Debug: ver qué datos llegan
    console.log('📋 Mostrando actas:', actas.length);
    if (actas.length > 0) {
        console.log('📋 Primera acta completa:', actas[0]);
        console.log('📋 Campos de conductor:', {
            nombre_conductor: actas[0].nombre_conductor,
            conductor_nombre: actas[0].conductor_nombre,
            apellidos_conductor: actas[0].apellidos_conductor,
            nombres_conductor: actas[0].nombres_conductor,
            apellidos: actas[0].apellidos,
            nombres: actas[0].nombres
        });
    }
    
    tbody.innerHTML = actas.map(acta => {
        const placa = acta.placa || acta.placa_vehiculo || 'N/A';
        
        // Construir nombre del conductor
        let conductor = 'N/A';
        
        // Prioridad: nombre_conductor completo, luego apellidos + nombres separados
        if (acta.nombre_conductor && acta.nombre_conductor.trim() !== '') {
            conductor = acta.nombre_conductor.trim();
        } else if (acta.conductor_nombre && acta.conductor_nombre.trim() !== '') {
            conductor = acta.conductor_nombre.trim();
        } else {
            // Intentar construir desde apellidos y nombres separados
            const apellidos = acta.apellidos_conductor || acta.apellidos || '';
            const nombres = acta.nombres_conductor || acta.nombres || '';
            
            if (apellidos && nombres) {
                conductor = `${apellidos.trim()}, ${nombres.trim()}`;
            } else if (apellidos) {
                conductor = apellidos.trim();
            } else if (nombres) {
                conductor = nombres.trim();
            }
        }
        
        const fecha = acta.fecha_intervencion || (acta.created_at ? formatDate(acta.created_at) : 'N/A');

        return `
            <tr>
                <td><strong>${acta.numero_acta || 'N/A'}</strong></td>
                <td>${fecha}</td>
                <td><span class="badge bg-info">${placa}</span></td>
                <td>${conductor}</td>
                <td><span class="badge ${getEstadoBadgeClass(acta.estado)}">${getEstadoDisplayName(acta.estado)}</span></td>
                <td><strong>${acta.codigo_infraccion || acta.codigo_base || acta.subcategoria || 'N/A'}</strong></td>
                <td class="text-center">
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-outline-primary" onclick="verActa(${acta.id})" title="Ver">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="exportarActaIndividualPDF(${acta.id})" title="Exportar PDF">
                            <i class="fas fa-file-pdf"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="anularActa(${acta.id}, '${acta.numero_acta}')" title="Anular Acta">
                            <i class="fas fa-ban"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

// Función para actualizar estadísticas del fiscalizador
function actualizarEstadisticasFiscalizador(actas) {
    const total = actas.length;
    const pendientes = actas.filter(a => {
        const estado = (a.estado || '').toLowerCase();
        return estado === 'pendiente' || estado === '';
    }).length;
    const pagadas = actas.filter(a => {
        const estado = (a.estado || '').toLowerCase();
        return estado === 'pagada' || estado === 'aprobado';
    }).length;
    const anuladas = actas.filter(a => {
        const estado = (a.estado || '').toLowerCase();
        return estado === 'anulada' || estado === 'anulado';
    }).length;

    const totalEl = document.getElementById('totalActasFisca');
    const pendientesEl = document.getElementById('actasPendientesFisca');
    const pagadasEl = document.getElementById('actasPagadasFisca');
    const anuladasEl = document.getElementById('actasAnuladasFisca');

    if (totalEl) totalEl.textContent = total;
    if (pendientesEl) pendientesEl.textContent = pendientes;
    if (pagadasEl) pagadasEl.textContent = pagadas;
    if (anuladasEl) anuladasEl.textContent = anuladas;
}

// Función para filtrar las actas del fiscalizador
function filtrarMisActas() {
    const searchTerm = document.getElementById('searchMisActas').value.toLowerCase();
    const estadoFilter = document.getElementById('filterEstadoMisActas').value;
    const fechaDesde = document.getElementById('filterFechaDesdeMisActas').value;
    const fechaHasta = document.getElementById('filterFechaHastaMisActas').value;

    if (!window.misActasFiscalizador) return;

    let actasFiltradas = window.misActasFiscalizador.filter(acta => {
        // Filtro de búsqueda
        const searchMatch = !searchTerm || 
            (acta.numero_acta && acta.numero_acta.toString().toLowerCase().includes(searchTerm)) ||
            (acta.placa_vehiculo && acta.placa_vehiculo.toLowerCase().includes(searchTerm)) ||
            (acta.conductor_nombre && acta.conductor_nombre.toLowerCase().includes(searchTerm));

        // Filtro de estado
        const estadoMatch = !estadoFilter || acta.estado === estadoFilter;

        // Filtro de fecha
        let fechaMatch = true;
        if (fechaDesde || fechaHasta) {
            const fechaActa = new Date(acta.fecha_creacion);
            if (fechaDesde) {
                fechaMatch = fechaMatch && fechaActa >= new Date(fechaDesde);
            }
            if (fechaHasta) {
                fechaMatch = fechaMatch && fechaActa <= new Date(fechaHasta + 'T23:59:59');
            }
        }

        return searchMatch && estadoMatch && fechaMatch;
    });

    mostrarMisActasEnTabla(actasFiltradas);
}

// Función para limpiar filtros del historial
function limpiarFiltrosMisActas() {
    document.getElementById('searchMisActas').value = '';
    document.getElementById('filterEstadoMisActas').value = '';
    document.getElementById('filterFechaDesdeMisActas').value = '';
    document.getElementById('filterFechaHastaMisActas').value = '';
    
    if (window.misActasFiscalizador) {
        mostrarMisActasEnTabla(window.misActasFiscalizador);
    }
}

// Función para ver detalle de acta del fiscalizador
function verDetalleActaFiscalizador(actaId) {
    const acta = window.misActasFiscalizador.find(a => a.id == actaId);
    if (!acta) {
        mostrarErrorActas('Acta no encontrada en tu historial');
        return;
    }

    // Mostrar modal con detalles de la propia acta
    const modalHtml = `
        <div class="modal fade" id="modalDetalleActaFiscalizador" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-file-alt"></i> Detalle de Mi Acta #${acta.numero_acta}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="fas fa-info-circle"></i> Información General</h6>
                                <table class="table table-sm">
                                    <tr><td><strong>Número:</strong></td><td>${acta.numero_acta}</td></tr>
                                    <tr><td><strong>Fecha:</strong></td><td>${new Date(acta.fecha_creacion).toLocaleString('es-PE')}</td></tr>
                                    <tr><td><strong>Estado:</strong></td><td><span class="badge bg-${acta.estado === 'pagada' ? 'success' : acta.estado === 'anulada' ? 'danger' : 'warning'}">${acta.estado}</span></td></tr>
                                    <tr><td><strong>Monto:</strong></td><td><strong>S/ ${parseFloat(acta.monto_multa || 0).toFixed(2)}</strong></td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fas fa-user"></i> Datos del Conductor</h6>
                                <table class="table table-sm">
                                    <tr><td><strong>Nombre:</strong></td><td>${acta.conductor_nombre}</td></tr>
                                    <tr><td><strong>DNI:</strong></td><td>${acta.conductor_dni}</td></tr>
                                    <tr><td><strong>Licencia:</strong></td><td>${acta.conductor_licencia || 'No especificada'}</td></tr>
                                </table>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <h6><i class="fas fa-car"></i> Datos del Vehículo</h6>
                                <table class="table table-sm">
                                    <tr><td><strong>Placa:</strong></td><td>${acta.placa_vehiculo}</td></tr>
                                    <tr><td><strong>Marca:</strong></td><td>${acta.vehiculo_marca || 'No especificada'}</td></tr>
                                    <tr><td><strong>Modelo:</strong></td><td>${acta.vehiculo_modelo || 'No especificado'}</td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fas fa-exclamation-triangle"></i> Infracción</h6>
                                <table class="table table-sm">
                                    <tr><td><strong>Tipo:</strong></td><td>${acta.tipo_infraccion}</td></tr>
                                    <tr><td><strong>Lugar:</strong></td><td>${acta.lugar_infraccion}</td></tr>
                                </table>
                            </div>
                        </div>
                        ${acta.descripcion_hechos ? `
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6><i class="fas fa-file-text"></i> Descripción de los Hechos</h6>
                                    <div class="alert alert-light">${acta.descripcion_hechos}</div>
                                </div>
                            </div>
                        ` : ''}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="exportarActaIndividual(${acta.id})">
                            <i class="fas fa-download"></i> Exportar
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('modalDetalleActaFiscalizador'));
    modal.show();

    // Limpiar modal después de cerrar
    document.getElementById('modalDetalleActaFiscalizador').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

// Función para exportar acta individual
function exportarActaIndividual(actaId) {
    const acta = window.misActasFiscalizador ? window.misActasFiscalizador.find(a => a.id == actaId) : 
                  window.actasData ? window.actasData.find(a => a.id == actaId) : null;
    
    if (!acta) {
        mostrarErrorActas('Acta no encontrada');
        return;
    }

    // Crear CSV con datos del acta
    const datos = [
        ['Campo', 'Valor'],
        ['Número de Acta', acta.numero_acta || ''],
        ['Fecha', acta.fecha_creacion ? new Date(acta.fecha_creacion).toLocaleDateString('es-PE') : ''],
        ['Placa Vehículo', acta.placa_vehiculo || ''],
        ['Conductor', acta.conductor_nombre || ''],
        ['DNI Conductor', acta.conductor_dni || ''],
        ['Licencia', acta.conductor_licencia || ''],
        ['Estado', acta.estado || ''],
        ['Monto Multa', acta.monto_multa ? `S/ ${parseFloat(acta.monto_multa).toFixed(2)}` : ''],
        ['Tipo Infracción', acta.tipo_infraccion || ''],
        ['Lugar Infracción', acta.lugar_infraccion || ''],
        ['Descripción', acta.descripcion_hechos || '']
    ];

    const csvContent = datos
        .map(row => row.map(field => `"${field}"`).join(','))
        .join('\n');

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `Acta_${acta.numero_acta || actaId}_${new Date().toLocaleDateString('es-PE').replace(/\//g, '-')}.csv`;
    link.click();

    mostrarExitoActas('Acta exportada correctamente');
}

// Función para exportar acta individual como PDF
async function exportarActaIndividualPDF(actaId) {
    try {
        const response = await fetchWithTimeout(`${window.location.origin}${window.location.pathname}?api=acta-details&id=${actaId}`, {
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json'
            }
        });

        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (err) {
            throw { status: response.status, text };
        }

        if (!data.acta) {
            mostrarErrorActas('Acta no encontrada');
            return;
        }

        const acta = data.acta;
        
        // Verificar si html2pdf está disponible, si no, cargarlo dinámicamente
        if (typeof html2pdf === 'undefined') {
            console.log('📚 Cargando html2pdf.js...');
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';
            script.onload = () => {
                console.log('✅ html2pdf.js cargado, generando PDF individual...');
                generarPDFIndividual(acta);
            };
            script.onerror = () => {
                mostrarErrorActas('Error al cargar la librería de PDF. Intente nuevamente.');
            };
            document.head.appendChild(script);
        } else {
            generarPDFIndividual(acta);
        }

    } catch (error) {
        if (error.text) {
            mostrarErrorActas('Respuesta inesperada del servidor');
        } else {
            mostrarErrorActas('Error al cargar acta: ' + error.message);
        }
    }
}

// Función para generar PDF individual
function generarPDFIndividual(acta) {
    try {
        const aniActual = new Date().getFullYear();
        
        const contenidoHTML = `
            <div style="padding: 15px; font-family: Arial, sans-serif; font-size: 9pt; max-width: 800px; margin: 0 auto;">
                <!-- Encabezado con logos -->
                <table style="width: 100%; margin-bottom: 10px; border-collapse: collapse;">
                    <tr>
                        <td style="width: 15%; text-align: left; vertical-align: top;">
                            <div style="width: 60px; height: 60px; background: #ccc; display: flex; align-items: center; justify-content: center; font-size: 8pt;">LOGO</div>
                        </td>
                        <td style="width: 70%; text-align: center; vertical-align: middle;">
                            <div style="font-size: 7pt; line-height: 1.2;">
                                <strong>PERÚ</strong><br>
                                <strong>GOBIERNO REGIONAL</strong><br>
                                <strong>DE APURÍMAC</strong><br>
                                <strong>DIRECCIÓN REGIONAL DE</strong><br>
                                <strong>TRANSPORTES Y COMUNICACIONES</strong><br>
                                <strong>DIRECCIÓN DE CIRCULACIÓN</strong><br>
                                <strong>TERRESTRE Y SEGURIDAD VIAL</strong>
                            </div>
                        </td>
                        <td style="width: 15%; text-align: right; vertical-align: top;">
                            <div style="width: 60px; height: 60px; background: #ccc; display: flex; align-items: center; justify-content: center; font-size: 8pt;">LOGO</div>
                        </td>
                    </tr>
                </table>

                <!-- Título del acta -->
                <div style="text-align: center; margin: 10px 0;">
                    <h3 style="margin: 5px 0; font-size: 11pt;">ACTA DE CONTROL N° ${acta.numero_acta || '000000'} -${aniActual}</h3>
                    <p style="margin: 3px 0; font-size: 9pt;"><strong>D.S. N° 017-2009-MTC</strong></p>
                </div>

                <!-- Tabla principal de datos -->
                <table style="width: 100%; border-collapse: collapse; font-size: 8pt; margin-bottom: 10px;">
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Placa:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.placa || acta.placa_vehiculo || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>RUC /DNI:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.ruc_dni || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Nombre de Conductor:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.nombre_conductor || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Fecha y Hora:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.fecha_intervencion || ''} ${acta.hora_intervencion || ''}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Lugar:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.lugar_intervencion || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Inspector:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.inspector_responsable || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Código Infracción:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.codigo_infraccion || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Estado:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${getEstadoDisplayName(acta.estado)}</td>
                    </tr>
                </table>

                <!-- Descripción de hechos -->
                <div style="border: 1px solid #000; padding: 5px; margin-bottom: 10px;">
                    <p style="margin: 0; font-size: 8pt;"><strong>Descripción de los hechos:</strong></p>
                    <p style="margin: 5px 0; font-size: 8pt; min-height: 60px;">${acta.descripcion_infraccion || ''}</p>
                </div>
            </div>
        `;

        // Configuración para html2pdf
        const opciones = {
            margin: 0.5,
            filename: `Acta_${acta.numero_acta || acta.id}_${new Date().toISOString().slice(0,10)}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: {
                scale: 2,
                useCORS: true,
                letterRendering: true
            },
            jsPDF: {
                unit: 'in',
                format: 'a4',
                orientation: 'portrait'
            }
        };

        // Crear elemento temporal con el contenido
        const elementoTemporal = document.createElement('div');
        elementoTemporal.innerHTML = contenidoHTML;
        elementoTemporal.style.position = 'absolute';
        elementoTemporal.style.left = '-9999px';
        elementoTemporal.style.top = '-9999px';
        document.body.appendChild(elementoTemporal);

        // Generar y descargar el PDF
        html2pdf()
            .set(opciones)
            .from(elementoTemporal)
            .save()
            .then(() => {
                console.log('✅ PDF individual generado y descargado exitosamente');
                mostrarExitoActas('PDF descargado exitosamente');
                // Limpiar elemento temporal
                document.body.removeChild(elementoTemporal);
            })
            .catch(error => {
                console.error('❌ Error al generar PDF individual:', error);
                mostrarErrorActas('Error al generar el PDF: ' + error.message);
                document.body.removeChild(elementoTemporal);
            });

    } catch (error) {
        console.error('❌ Error en generarPDFIndividual:', error);
        mostrarErrorActas('Error al preparar el PDF: ' + error.message);
    }
}

// Función para exportar historial del fiscalizador
function exportarMisActas(formato = 'excel') {
    if (!window.misActasFiscalizador || window.misActasFiscalizador.length === 0) {
        mostrarErrorActas('No hay actas en tu historial para exportar');
        return;
    }

    const usuario = getCurrentUserData();
    const fechaHoy = new Date().toLocaleDateString('es-PE');

    if (formato === 'excel') {
        // Preparar datos para CSV
        const headers = ['Número', 'Fecha', 'Placa', 'Conductor', 'DNI', 'Estado', 'Monto', 'Tipo Infracción', 'Lugar'];
        const datos = window.misActasFiscalizador.map(acta => [
            acta.numero_acta || '',
            acta.fecha_creacion ? new Date(acta.fecha_creacion).toLocaleDateString('es-PE') : '',
            acta.placa_vehiculo || '',
            acta.conductor_nombre || '',
            acta.conductor_dni || '',
            acta.estado || '',
            `S/ ${parseFloat(acta.monto_multa || 0).toFixed(2)}`,
            acta.tipo_infraccion || '',
            acta.lugar_infraccion || ''
        ]);

        // Crear CSV
        const csvContent = [headers, ...datos]
            .map(row => row.map(field => `"${field}"`).join(','))
            .join('\n');

        // Descargar
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `Mi_Historial_Actas_${usuario.nombre}_${fechaHoy.replace(/\//g, '-')}.csv`;
        link.click();

        mostrarExitoActas('Historial exportado correctamente');
    }
}

// Función para imprimir historial del fiscalizador
function imprimirMisActas() {
    if (!window.misActasFiscalizador || window.misActasFiscalizador.length === 0) {
        mostrarErrorActas('No hay actas en tu historial para imprimir');
        return;
    }

    const usuario = getCurrentUserData();
    const fechaHoy = new Date().toLocaleDateString('es-PE');

    // Generar HTML para impresión
    const htmlImpresion = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Mi Historial de Actas</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .info { margin-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                .footer { margin-top: 30px; text-align: center; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Mi Historial de Actas de Fiscalización</h1>
                <p>Fiscalizador: ${usuario.nombre}</p>
                <p>Fecha de reporte: ${fechaHoy}</p>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Fecha</th>
                        <th>Placa</th>
                        <th>Conductor</th>
                        <th>Estado</th>
                        <th>Monto</th>
                        <th>Tipo Infracción</th>
                    </tr>
                </thead>
                <tbody>
                    ${window.misActasFiscalizador.map(acta => `
                        <tr>
                            <td>${acta.numero_acta || ''}</td>
                            <td>${acta.fecha_creacion ? new Date(acta.fecha_creacion).toLocaleDateString('es-PE') : ''}</td>
                            <td>${acta.placa_vehiculo || ''}</td>
                            <td>${acta.conductor_nombre || ''}</td>
                            <td>${acta.estado || ''}</td>
                            <td>S/ ${parseFloat(acta.monto_multa || 0).toFixed(2)}</td>
                            <td>${acta.tipo_infraccion || ''}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>

            <div class="footer">
                <p>Total de actas: ${window.misActasFiscalizador.length}</p>
                <p>Generado desde el Sistema de Fiscalización Municipal</p>
            </div>
        </body>
        </html>
    `;

    // Abrir ventana de impresión
    const ventanaImpresion = window.open('', '_blank');
    ventanaImpresion.document.write(htmlImpresion);
    ventanaImpresion.document.close();
    ventanaImpresion.focus();
    ventanaImpresion.print();
}

// Función para mostrar animación de carga con camioncito
function mostrarAnimacionCargaCamion() {
    const tbody = document.getElementById('misActasTableBody');
    if (!tbody) return;

    // Crear animación de camioncito cargando
    tbody.innerHTML = `
        <tr>
            <td colspan="7" class="text-center" style="padding: 50px;">
                <div class="camion-loading-container" style="position: relative; height: 120px;">
                    <!-- Carretera -->
                    <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 10px; background: linear-gradient(90deg, #666 0%, #999 50%, #666 100%); border-radius: 5px;"></div>

                    <!-- Camioncito animado -->
                    <div class="camioncito" style="
                        position: absolute;
                        bottom: 10px;
                        left: 50%;
                        transform: translateX(-50%);
                        font-size: 40px;
                        animation: moverCamion 2s ease-in-out infinite;
                    ">
                        🚛
                    </div>

                    <!-- Nube de polvo -->
                    <div class="polvo" style="
                        position: absolute;
                        bottom: 5px;
                        left: 50%;
                        transform: translateX(-50%);
                        font-size: 20px;
                        opacity: 0.7;
                        animation: polvo 2s ease-in-out infinite;
                    ">
                        💨
                    </div>

                    <!-- Texto de carga -->
                    <div style="
                        position: absolute;
                        top: 0;
                        left: 50%;
                        transform: translateX(-50%);
                        text-align: center;
                        color: #666;
                        font-weight: bold;
                    ">
                        <div style="font-size: 16px; margin-bottom: 5px;">Cargando tu historial...</div>
                        <div style="font-size: 12px; color: #999;">Por favor espera un momento</div>
                    </div>
                </div>

                <style>
                    @keyframes moverCamion {
                        0% { transform: translateX(-100px); }
                        50% { transform: translateX(0px); }
                        100% { transform: translateX(100px); }
                    }

                    @keyframes polvo {
                        0% { opacity: 0; transform: translateX(-100px) scale(0.5); }
                        50% { opacity: 0.7; transform: translateX(0px) scale(1); }
                        100% { opacity: 0; transform: translateX(100px) scale(0.5); }
                    }

                    .camion-loading-container {
                        overflow: hidden;
                    }
                </style>
            </td>
        </tr>
    `;

    // Intentar recargar después de 5 segundos
    setTimeout(() => {
        console.log('⏰ Reintentando carga automática después de animación...');
        cargarMisActasDesdeAPI();
    }, 5000);
}

// ================================
// EXPORTAR FUNCIONES GLOBALMENTE
// ================================

// Hacer funciones disponibles globalmente
window.loadActas = loadActas;
window.loadGestionActas = loadGestionActas;
window.loadCrearActa = loadCrearActa;
window.loadMisActas = loadMisActas;
window.cargarActasDesdeAPI = cargarActasDesdeAPI;
window.cargarMisActasDesdeAPI = cargarMisActasDesdeAPI;
window.showCrearActaModal = showCrearActaModal;
window.guardarNuevaActa = guardarNuevaActa;

// Cargar automáticamente la lista de actas al cargar el panel
document.addEventListener('DOMContentLoaded', () => {
    const tabla = document.getElementById('actasTableBody');
    const contenedor = document.getElementById('contentContainer');
    if (tabla || contenedor) {
        try { cargarActasDesdeAPI(); } catch (e) { /* noop */ }
    }
});
window.verActa = verActa;
window.editarActa = editarActa;
window.anularActa = anularActa;
window.imprimirActa = imprimirActa;
window.exportarActaPDF = exportarActaPDF;

window.filtrarActas = filtrarActas;
window.limpiarFiltros = limpiarFiltros;
window.limpiarTodosLosModales = limpiarTodosLosModales;
window.cancelarAccion = cancelarAccion;
window.validarElemento = validarElemento;
window.generarHTMLGestionActas = generarHTMLGestionActas;
window.renderizarActasEnTabla = renderizarActasEnTabla;

// Funciones de exportación e impresión
window.exportarActas = exportarActas;
window.exportarExcel = exportarExcel;
window.exportarPDF = exportarPDF;
window.generarPDFDescarga = generarPDFDescarga;
window.exportarPDFDetallado = exportarPDFDetallado;
window.generarPDFDetalladoDescarga = generarPDFDetalladoDescarga;
window.imprimirActas = imprimirActas;
window.convertirACSVMejorado = convertirACSVMejorado;

// Nuevas funciones para modal
window.configurarValidacionDinamica = configurarValidacionDinamica;
window.exportarActaActual = exportarActaActual;
window.exportarActaTemporal = exportarActaTemporal;
window.cargarDistritos = cargarDistritos;
window.onSubcategoriaCheckboxChange = onSubcategoriaCheckboxChange;

// Función para limpiar el formulario de acta
function limpiarFormularioActa() {
    const form = document.getElementById('formCrearActa');
    if (!form) {
        mostrarErrorActas('Formulario no encontrado');
        return;
    }
    
    // Resetear todos los campos del formulario
    form.reset();
    
    // Limpiar campos específicos que no se resetean automáticamente
    const camposALimpiar = [
        'ruc_dni', 'razon_social', 'placa', 'tipo_agente', 'tipo_servicio',
        'apellidos_conductor', 'nombres_conductor', 'licencia_conductor',
        'provincia', 'distrito', 'lugar_intervencion', 'codigo_base',
        'descripcion_infraccion', 'codigo_infraccion'
    ];
    
    camposALimpiar.forEach(id => {
        const campo = document.getElementById(id);
        if (campo) {
            campo.value = '';
        }
    });
    
    // Resetear el select de distrito
    const distritoSelect = document.getElementById('distrito');
    if (distritoSelect) {
        distritoSelect.innerHTML = '<option value="">Primero seleccione provincia</option>';
        distritoSelect.disabled = true;
    }
    
    // Resetear el contenedor de subcategorías
    const subcategoriaContainer = document.getElementById('subcategoria-container');
    if (subcategoriaContainer) {
        subcategoriaContainer.innerHTML = '<small class="text-muted">Primero seleccione código base</small>';
    }
    
    // Resetear el badge de gravedad
    const badgeGravedad = document.getElementById('badge_gravedad');
    if (badgeGravedad) {
        badgeGravedad.textContent = 'Sin seleccionar';
        badgeGravedad.className = 'badge bg-secondary';
    }
    
    // Reconfigurar timestamp automático
    configurarTimestampAutomatico();
    
    // Ocultar botones de acción
    const botonesAccion = document.getElementById('botonesAccion');
    if (botonesAccion) {
        botonesAccion.classList.remove('activo');
    }
    
    // Actualizar mensaje de validación
    const estadoValidacion = document.getElementById('estadoValidacion');
    if (estadoValidacion) {
        estadoValidacion.innerHTML = '<i class="fas fa-exclamation-circle"></i> Complete los 8 campos obligatorios para ver las opciones';
        estadoValidacion.className = 'text-warning text-center fw-bold';
    }
    
    mostrarExitoActas('Formulario limpiado correctamente');
}

// Funciones para historial del fiscalizador
window.cargarMisActasDesdeAPI = cargarMisActasDesdeAPI;
window.mostrarMisActasEnTabla = mostrarMisActasEnTabla;
window.actualizarEstadisticasFiscalizador = actualizarEstadisticasFiscalizador;
window.filtrarMisActas = filtrarMisActas;
window.limpiarFiltrosMisActas = limpiarFiltrosMisActas;
window.verDetalleActaFiscalizador = verDetalleActaFiscalizador;
window.exportarMisActas = exportarMisActas;
window.imprimirMisActas = imprimirMisActas;
window.exportarActaIndividual = exportarActaIndividual;
window.exportarActaIndividualPDF = exportarActaIndividualPDF;
window.generarPDFIndividual = generarPDFIndividual;
window.generarHTMLHistorialActas = generarHTMLHistorialActas;
window.mostrarAnimacionCargaCamion = mostrarAnimacionCargaCamion;
window.actualizarEstadoActaEnTabla = actualizarEstadoActaEnTabla;
window.limpiarFormularioActa = limpiarFormularioActa;

console.log('✅ Fiscalizador Actas JS cargado correctamente con exportación mejorada');
