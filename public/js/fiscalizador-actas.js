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
    console.log('� Cargando historial de mis actas...');
    
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
                        <i class="fas fa-refresh"></i> Actualizar
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
                        <i class="fas fa-file-excel"></i> Exportar Excel
                    </button>
                    <button class="btn btn-info" onclick="exportarActas('pdf')">
                        <i class="fas fa-file-pdf"></i> Exportar PDF
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
    try {
        const response = await fetchWithTimeout(`${window.location.origin}${window.location.pathname}?api=actas`, {
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
            throw { status: response.status, text };
        }
        
        if (data.success) {
            todasLasActas = data.actas || [];
            mostrarActas(todasLasActas);
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
    try {
        const response = await fetchWithTimeout(`${window.location.origin}${window.location.pathname}?api=actas`, {
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
            throw { status: response.status, text };
        }
        
        if (data.success) {
            const todasActas = data.actas || [];
            // Filtrar solo las actas del usuario actual
            const userName = window.dashboardUserName;
            const misActas = todasActas.filter(acta => 
                acta.inspector_responsable === userName || 
                acta.inspector === userName ||
                acta.user_name === userName
            );
            
            todasLasActas = misActas;
            mostrarActas(misActas);
            
            // Actualizar el título para indicar que son "Mis Actas"
            const titleElement = document.querySelector('.content-section h2');
            if (titleElement) {
                titleElement.innerHTML = '<i class="fas fa-user-edit"></i> Mis Actas';
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
                            <i class="fas fa-file-excel"></i> Exportar Excel
                        </button>
                        <button class="btn btn-info" onclick="exportarActas('pdf')">
                            <i class="fas fa-file-pdf"></i> Exportar PDF
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

    tbody.innerHTML = actas.map(acta => `
        <tr>
            <td>
                <strong>${acta.numero_acta || 'N/A'}</strong>
            </td>
            <td>
                <span class="badge bg-info">${acta.placa || acta.placa_vehiculo || 'N/A'}</span>
            </td>
            <td>
                ${acta.conductor_nombre || acta.nombre_conductor || 'N/A'}
            </td>
            <td>
                <small class="text-muted">${acta.ruc_dni || 'N/A'}</small>
            </td>
            <td>
                <span class="badge ${getEstadoBadgeClass(acta.estado)}">${getEstadoDisplayName(acta.estado)}</span>
            </td>
            <td>
                <small class="text-muted">
                    ${acta.fecha_acta ? formatDate(acta.fecha_acta) : formatDate(acta.created_at)}
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
                    <button class="btn btn-sm btn-outline-danger" onclick="anularActa(${acta.id}, '${acta.numero_acta}')" title="Anular Acta">
                        <i class="fas fa-ban"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
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
            
            <div class="col-md-6">
                <label class="form-label">Nombre del Conductor *</label>
                <input type="text" class="form-control" name="nombre_conductor" id="nombre_conductor" required
                       placeholder="Apellidos y Nombres completos">
            </div>
            
            <div class="col-md-6">
                <label class="form-label">N° Licencia</label>
                <input type="text" class="form-control" name="licencia_conductor" id="licencia_conductor" 
                       placeholder="Número de licencia">
            </div>
            
            <!-- Datos de la Intervención -->
            <div class="col-12 mt-4">
                <h6 class="text-danger border-bottom pb-2">
                    <i class="fas fa-map-marker-alt"></i> Datos de la Intervención
                </h6>
            </div>

            <div class="col-md-4">
                <label class="form-label">Lugar de Intervención *</label>
                <input type="text" class="form-control" name="lugar_intervencion" id="lugar_intervencion" required
                       placeholder="Ubicación donde se realizó la intervención">
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Fecha</label>
                <input type="date" class="form-control" name="fecha_intervencion" id="fecha_intervencion" 
                       value="${new Date().toISOString().split('T')[0]}" readonly>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Hora de Inicio</label>
                <input type="time" class="form-control" name="hora_intervencion" id="hora_intervencion" 
                       readonly style="background-color: #f8f9fa;">
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Inspector Responsable</label>
                <input type="text" class="form-control" name="inspector_responsable" id="inspector_responsable" 
                       value="${window.dashboardUserName || ''}" readonly>
            </div>
            
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
                <label class="form-label">Subcategoría *</label>
                <select class="form-select" name="subcategoria" id="subcategoria" required disabled>
                    <option value="">Primero seleccione código</option>
                </select>
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
        const response = await fetch('dashboard.php?api=codigos-infracciones', {
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
            
            const selectSubcat = document.getElementById('subcategoria');
            if (selectSubcat) {
                selectSubcat.addEventListener('change', onSubcategoriaChange);
            }
            
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
    const selectSubcat = document.getElementById('subcategoria');
    const badgeGravedad = document.getElementById('badge_gravedad');
    const textareaDesc = document.getElementById('descripcion_infraccion');
    const hiddenCodigo = document.getElementById('codigo_infraccion');
    
    // Reset
    selectSubcat.innerHTML = '<option value="">Seleccione...</option>';
    selectSubcat.disabled = true;
    textareaDesc.value = '';
    hiddenCodigo.value = '';
    badgeGravedad.textContent = 'Sin seleccionar';
    badgeGravedad.className = 'badge bg-secondary';
    
    if (!codigoBase) return;
    
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
        return;
    }
    
    if (subcategorias.length === 1 && subcategorias[0].codigo === codigoBase) {
        // Solo hay código base, sin subcategorías
        selectSubcat.innerHTML = '<option value="general">General</option>';
        selectSubcat.value = 'general';
        selectSubcat.disabled = false;
        textareaDesc.value = subcategorias[0].descripcion;
        hiddenCodigo.value = codigoBase;
        console.log('✅ Código sin subcategorías:', codigoBase);
    } else {
        // Hay subcategorías
        subcategorias.forEach(sub => {
            if (sub.codigo !== codigoBase) { // Excluir el código base si viene solo
                const option = document.createElement('option');
                const subcategoria = sub.codigo.split('-')[1] || 'general';
                option.value = subcategoria;
                option.textContent = `${subcategoria}) ${sub.descripcion.substring(0, 50)}...`;
                option.dataset.descripcion = sub.descripcion;
                option.dataset.codigoCompleto = sub.codigo;
                selectSubcat.appendChild(option);
            }
        });
        selectSubcat.disabled = false;
        console.log(`✅ ${subcategorias.length} subcategorías cargadas para ${codigoBase}`);
    }
}

function onSubcategoriaChange(event) {
    const subcategoria = event.target.value;
    const codigoBase = document.getElementById('codigo_base').value;
    const textareaDesc = document.getElementById('descripcion_infraccion');
    const hiddenCodigo = document.getElementById('codigo_infraccion');
    
    if (!subcategoria || !codigoBase) return;
    
    const selectedOption = event.target.options[event.target.selectedIndex];
    const descripcion = selectedOption.dataset.descripcion;
    const codigoCompleto = selectedOption.dataset.codigoCompleto;
    
    if (descripcion) {
        textareaDesc.value = descripcion;
    }
    
    if (codigoCompleto) {
        hiddenCodigo.value = codigoCompleto;
    } else {
        hiddenCodigo.value = codigoBase;
    }
    
    console.log('✅ Código completo seleccionado:', hiddenCodigo.value);
}

function configurarValidacionDinamica() {
    const camposRequeridos = ['ruc_dni', 'placa', 'tipo_agente', 'tipo_servicio', 'nombre_conductor', 'lugar_intervencion', 'codigo_base', 'subcategoria'];
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
            const valor = formData.get(campo)?.trim();
            
            // Validación especial para selects
            if (elemento && elemento.tagName === 'SELECT') {
                if (valor && valor !== '' && valor !== 'Seleccione...' && valor !== 'seleccione') {
                    camposCompletos++;
                }
            } else {
                // Validación normal para inputs y textareas
                if (valor && valor.length > 0) {
                    camposCompletos++;
                }
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
                    const elemento = form.querySelector(`[name="${campo}"]`);
                    const valor = formData.get(campo)?.trim();
                    
                    let valido = false;
                    if (elemento && elemento.tagName === 'SELECT') {
                        valido = valor && valor !== '' && valor !== 'Seleccione...' && valor !== 'seleccione';
                    } else {
                        valido = valor && valor.length > 0;
                    }
                    
                    if (!valido) {
                        const label = elemento ? (elemento.closest('.col-md-6, .col-md-4, .col-md-3, .col-12')?.querySelector('label')?.textContent?.replace('*', '')?.trim() || campo) : campo;
                        camposFaltantes.push(label);
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

async function guardarNuevaActa() {
    console.log('💾 Guardando nueva acta...');
    
    const form = document.getElementById('formCrearActa');
    const formData = new FormData(form);
    
    // Validar campos requeridos
    const camposRequeridos = ['ruc_dni', 'placa', 'tipo_agente', 'tipo_servicio', 'nombre_conductor', 'codigo_infraccion', 'lugar_intervencion'];
    const camposFaltantes = [];

    camposRequeridos.forEach(campo => {
        if (!formData.get(campo)?.trim()) {
            camposFaltantes.push(campo);
        }
    });
    
    if (camposFaltantes.length > 0) {
        mostrarErrorActas('Por favor complete todos los campos obligatorios marcados con *');
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
                            <img src="images/escudo_peru.png" style="width: 60px; height: auto;" />
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
                            <img src="images/logo.png" style="width: 60px; height: auto;" />
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
                cargarActasDesdeAPI(); // Recargar la lista
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

function limpiarFiltrosActas() {
    document.getElementById('searchActas').value = '';
    document.getElementById('filterEstado').value = '';
    document.getElementById('filterFechaDesde').value = '';
    document.getElementById('filterFechaHasta').value = '';
    mostrarActas(todasLasActas);
}

// ================================
// FUNCIONES AUXILIARES - ACTAS
// ================================

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
    
    // Auto-remover después de 5 segundos
    setTimeout(() => {
        if (notificacion.parentNode) {
            notificacion.remove();
        }
    }, 5000);
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
        }
    } catch (error) {
        console.error('Error al exportar:', error);
        mostrarErrorActas('Error al exportar las actas: ' + error.message);
    }
}

function exportarExcel() {
    // Preparar datos para Excel con formato oficial
    const datosExcel = todasLasActas.map(acta => ({
        'N° Acta': acta.numero_acta || '',
        'Placa': acta.placa || acta.placa_vehiculo || '',
        'Conductor': acta.nombre_conductor || '',
        'RUC/DNI': acta.ruc_dni || '',
        'Razón Social': acta.razon_social || '',
        'Licencia': acta.licencia || '',
        'Tipo Agente': acta.tipo_agente || '',
        'Tipo Servicio': acta.tipo_servicio || '',
        'Código Infracción': acta.codigo_infraccion || '',
        'Descripción Infracción': (acta.descripcion_infraccion || '').replace(/[\n\r]/g, ' '),
        'Lugar Intervención': acta.lugar_intervencion || '',
        'Fecha Intervención': acta.fecha_intervencion || '',
        'Hora Intervención': acta.hora_intervencion || '',
        'Inspector': acta.inspector_responsable || '',
        'Estado': acta.estado || 'Pendiente',
        'Motivo Anulación': acta.motivo_anulacion || '',
        'Fecha Registro': acta.created_at ? formatDate(acta.created_at) : ''
    }));
    
    // Crear CSV y descargarlo
    const csv = '\uFEFF' + convertirACSV(datosExcel); // BOM para UTF-8
    const fechaExport = new Date().toISOString().slice(0,10);
    descargarArchivo(csv, `Reporte_Actas_${fechaExport}.csv`, 'text/csv;charset=utf-8');
    
    mostrarExitoActas(`✅ Exportadas ${datosExcel.length} actas a Excel (CSV)`);
}

function exportarPDF() {
    // Abrir ventana de impresión con formato PDF
    const ventanaImpresion = window.open('', '_blank');
    
    if (!ventanaImpresion) {
        mostrarErrorActas('No se pudo abrir la ventana de impresión. Verifique que no esté bloqueada por el navegador.');
        return;
    }
    
    const contenidoPDF = generarHTMLParaImpresion();
    
    ventanaImpresion.document.write(contenidoPDF);
    ventanaImpresion.document.close();
    
    // Esperar a que se cargue completamente antes de imprimir
    ventanaImpresion.onload = function() {
        ventanaImpresion.print();
    };
    
    mostrarInfoActas('Abriendo vista previa de PDF...');
}

function imprimirActas() {
    console.log('🖨️ Preparando impresión de actas...');
    
    if (!todasLasActas || todasLasActas.length === 0) {
        mostrarErrorActas('No hay actas para imprimir');
        return;
    }
    
    exportarPDF(); // Usar la misma función que PDF
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

function descargarArchivo(contenido, nombreArchivo, tipoMIME) {
    const blob = new Blob([contenido], { type: tipoMIME });
    const url = window.URL.createObjectURL(blob);
    
    const enlaceDescarga = document.createElement('a');
    enlaceDescarga.href = url;
    enlaceDescarga.download = nombreArchivo;
    enlaceDescarga.style.display = 'none';
    
    document.body.appendChild(enlaceDescarga);
    enlaceDescarga.click();
    document.body.removeChild(enlaceDescarga);
    
    window.URL.revokeObjectURL(url);
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
                <img src="images/escudo_peru.png" alt="Escudo Perú" />
            </div>
            <div class="logo-center">
                <div>PERÚ</div>
                <div>GOBIERNO REGIONAL DE APURÍMAC</div>
                <div>DIRECCIÓN REGIONAL DE TRANSPORTES Y COMUNICACIONES</div>
                <div>DIRECCIÓN DE CIRCULACIÓN TERRESTRE Y SEGURIDAD VIAL</div>
            </div>
            <div class="logo-right">
                <img src="images/logo.png" alt="Logo" />
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

// ================================
// FUNCIONES PARA HISTORIAL DEL FISCALIZADOR
// ================================

// Función para cargar las actas del fiscalizador desde la API
function cargarMisActasDesdeAPI() {
    const usuario = getCurrentUserData();
    if (!usuario || !usuario.id) {
        mostrarErrorActas('No se pudo obtener la información del usuario');
        return;
    }

    // Mostrar indicador de carga
    const tbody = document.getElementById('misActasTableBody');
    if (tbody) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Actualizando mi historial...</p>
                </td>
            </tr>
        `;
    }

    // Cargar actas del fiscalizador
    console.log('Enviando solicitud para fiscalizador ID:', usuario.id);
    
    fetch('dashboard.php?api=obtener_actas_fiscalizador', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            fiscalizador_id: usuario.id
        })
    })
    .then(response => {
        console.log('Respuesta HTTP status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Datos recibidos:', data);
        if (data.success) {
            window.misActasFiscalizador = data.actas || [];
            mostrarMisActasEnTabla(window.misActasFiscalizador);
            actualizarEstadisticasFiscalizador(window.misActasFiscalizador);
            console.log('Actas cargadas exitosamente:', window.misActasFiscalizador.length);
        } else {
            console.error('Error en respuesta:', data.message);
            mostrarErrorActas('Error al cargar mi historial: ' + (data.message || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('Error al cargar actas del fiscalizador:', error);
        // Mostrar animación de carga con camioncito en lugar del error
        mostrarAnimacionCargaCamion();
    });
}

// Función para mostrar las actas del fiscalizador en la tabla
function mostrarMisActasEnTabla(actas) {
    const tbody = document.getElementById('misActasTableBody');
    if (!tbody) return;

    if (!actas || actas.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center">
                    <i class="fas fa-inbox text-muted"></i>
                    <p class="mt-2 text-muted">No has creado actas aún</p>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = actas.map(acta => {
        const estado = (acta.estado || 'Pendiente').toLowerCase();
        const estadoClase = {
            'pendiente': 'warning',
            'aprobado': 'success',
            'anulado': 'danger'
        }[estado] || 'secondary';

        // Usar los nombres de campos correctos de la BD
        const placa = acta.placa || acta.placa_vehiculo || 'N/A';
        const conductor = acta.nombre_conductor || acta.conductor_nombre || 'N/A';
        const fecha = acta.fecha_intervencion || (acta.created_at ? new Date(acta.created_at).toLocaleDateString('es-PE') : 'N/A');

        return `
            <tr>
                <td><strong>${acta.numero_acta || 'N/A'}</strong></td>
                <td>${fecha}</td>
                <td><span class="badge bg-primary">${placa}</span></td>
                <td>${conductor}</td>
                <td><span class="badge bg-${estadoClase}">${getEstadoDisplayName(acta.estado)}</span></td>
                <td><strong>${acta.codigo_infraccion || 'N/A'}</strong></td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-primary me-1" onclick="verActa(${acta.id})" title="Ver detalle">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-info me-1" onclick="imprimirActa(${acta.id})" title="Imprimir">
                        <i class="fas fa-print"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

// Función para actualizar estadísticas del fiscalizador
function actualizarEstadisticasFiscalizador(actas) {
    const total = actas.length;
    const pendientes = actas.filter(a => (a.estado || '').toLowerCase() === 'pendiente').length;
    const pagadas = actas.filter(a => (a.estado || '').toLowerCase() === 'aprobado').length;
    const anuladas = actas.filter(a => (a.estado || '').toLowerCase() === 'anulado').length;

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
window.verActa = verActa;
window.editarActa = editarActa;
window.anularActa = anularActa;
window.imprimirActa = imprimirActa;
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
window.imprimirActas = imprimirActas;

// Nuevas funciones para modal
window.configurarValidacionDinamica = configurarValidacionDinamica;
window.exportarActaActual = exportarActaActual;
window.exportarActaTemporal = exportarActaTemporal;

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
window.generarHTMLHistorialActas = generarHTMLHistorialActas;
window.mostrarAnimacionCargaCamion = mostrarAnimacionCargaCamion;

console.log('✅ Fiscalizador Actas JS cargado correctamente');
