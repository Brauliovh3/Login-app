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
function cancelarAccion(mostrarMensaje = true) {
    console.log('❌ Cancelando acción... mostrarMensaje:', mostrarMensaje);
    console.trace('Llamada a cancelarAccion desde:'); // Debug para ver de dónde viene la llamada
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
                                    <th>Monto</th>
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
                    <button class="btn btn-sm btn-outline-danger" onclick="eliminarActa(${acta.id}, '${acta.numero_acta}')" title="Eliminar">
                        <i class="fas fa-trash"></i>
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
    
    // Configurar título del modal con botón X en el header
    modalTitle.innerHTML = `
        <div class="d-flex justify-content-between align-items-center w-100">
            <div class="d-flex align-items-center">
                <i class="fas fa-plus-circle me-2"></i> Crear Nueva Acta
            </div>
            <button type="button" class="btn-close" onclick="cancelarAccion()" aria-label="Close"></button>
        </div>
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
            
            <!-- Campo oculto para timestamp exacto -->
            <input type="hidden" name="timestamp_inicio" id="timestamp_inicio" value="">
            
            <div class="col-12">
                <label class="form-label">Descripción de los Hechos *</label>
                <textarea class="form-control" name="descripcion_hechos" id="descripcion_hechos" rows="3" required
                          placeholder="Describa detalladamente la infracción o situación encontrada..."></textarea>
            </div>
        </form>
    `;
    
    // Configurar botones del modal solo con acciones (cerrar está en header)
    modalFooter.innerHTML = `
        <div class="d-flex justify-content-between align-items-center w-100">
            <div id="botonesAccion" style="display: none;" class="d-flex gap-2">
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
            <small id="estadoValidacion" class="text-muted">Complete todos los campos para ver las opciones</small>
        </div>
    `;
    
    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('generalModal'));
    modal.show();
    
    // Configurar captura automática de timestamp cuando el usuario comience a escribir
    setTimeout(() => {
        configurarTimestampAutomatico();
        configurarValidacionDinamica();
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

// Función para configurar validación dinámica de campos
function configurarValidacionDinamica() {
    const camposRequeridos = ['ruc_dni', 'placa', 'tipo_agente', 'tipo_servicio', 'nombre_conductor', 'lugar_intervencion', 'descripcion_hechos'];
    const botonesAccion = document.getElementById('botonesAccion');
    
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
                botonesAccion.style.display = 'flex';
                botonesAccion.style.gap = '0.5rem';
                console.log('✅ Botones de acción mostrados');
            }
        } else {
            if (botonesAccion) {
                botonesAccion.style.display = 'none';
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
        descripcion_hechos: formData.get('descripcion_hechos') || 'N/A',
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
    let timestampCapturado = false;
    const ahora = new Date();
    
    // Configurar hora actual en el campo readonly
    const horaInput = document.getElementById('hora_intervencion');
    const timestampInput = document.getElementById('timestamp_inicio');
    
    if (horaInput && timestampInput) {
        const horaFormateada = ahora.toTimeString().slice(0,5);
        horaInput.value = horaFormateada;
        timestampInput.value = ahora.toISOString();
        timestampCapturado = true;
        
        console.log('⏰ Timestamp automático configurado:', horaFormateada);
    }
    
    // Si no se ha capturado aún, capturar cuando el usuario comience a escribir
    if (!timestampCapturado) {
        const camposFormulario = document.querySelectorAll('#formCrearActa input, #formCrearActa select, #formCrearActa textarea');
        
        function capturarHoraInicio() {
            if (!timestampCapturado) {
                const momentoInicio = new Date();
                const horaFormateada = momentoInicio.toTimeString().slice(0,5);
                
                if (horaInput) horaInput.value = horaFormateada;
                if (timestampInput) timestampInput.value = momentoInicio.toISOString();
                
                timestampCapturado = true;
                console.log('⏰ Hora de inicio capturada automáticamente:', horaFormateada);
                
                // Remover los event listeners
                camposFormulario.forEach(campo => {
                    campo.removeEventListener('focus', capturarHoraInicio);
                    campo.removeEventListener('input', capturarHoraInicio);
                });
            }
        }
        
        // Agregar event listeners para capturar cuando comience a llenar
        camposFormulario.forEach(campo => {
            campo.addEventListener('focus', capturarHoraInicio, { once: true });
            campo.addEventListener('input', capturarHoraInicio, { once: true });
        });
    }
}

async function guardarNuevaActa() {
    console.log('💾 Guardando nueva acta...');
    
    const form = document.getElementById('formCrearActa');
    const formData = new FormData(form);
    
    // Validar campos requeridos
    const camposRequeridos = ['ruc_dni', 'placa', 'tipo_agente', 'tipo_servicio', 'nombre_conductor', 'lugar_intervencion', 'descripcion_hechos'];
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
    
    // Log para debugging
    console.log('📋 Datos a enviar:', actaData);
    
    try {
        const response = await fetchWithTimeout(`${window.location.origin}${window.location.pathname}?api=actas`, {
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
            mostrarErrorActas('Error al crear acta: ' + (data.message || 'Error desconocido'));
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
                            <button type="button" class="btn btn-secondary" onclick="cancelarAccion()">Cancelar</button>
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
        const printContent = `
            <div style="padding: 20px; font-family: Arial, sans-serif;">
                <div style="text-align: center; margin-bottom: 30px;">
                    <h1>ACTA DE INFRACCIÓN</h1>
                    <h2>${acta.numero_acta || 'N/A'}</h2>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; background: #f5f5f5; width: 30%;"><strong>Placa del Vehículo:</strong></td>
                            <td style="padding: 8px; border: 1px solid #ddd;">${acta.placa || acta.placa_vehiculo || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; background: #f5f5f5;"><strong>Conductor:</strong></td>
                            <td style="padding: 8px; border: 1px solid #ddd;">${acta.conductor_nombre || acta.nombre_conductor || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; background: #f5f5f5;"><strong>RUC/DNI:</strong></td>
                            <td style="padding: 8px; border: 1px solid #ddd;">${acta.ruc_dni || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; background: #f5f5f5;"><strong>Estado:</strong></td>
                            <td style="padding: 8px; border: 1px solid #ddd;">${getEstadoDisplayName(acta.estado)}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; background: #f5f5f5;"><strong>Monto:</strong></td>
                            <td style="padding: 8px; border: 1px solid #ddd;">S/ ${acta.monto || '0.00'}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; background: #f5f5f5;"><strong>Fecha:</strong></td>
                            <td style="padding: 8px; border: 1px solid #ddd;">${acta.fecha_acta ? formatDate(acta.fecha_acta) : formatDate(acta.created_at)}</td>
                        </tr>
                    </table>
                </div>
                
                <div style="margin: 20px 0;">
                    <h3>Descripción de los Hechos:</h3>
                    <div style="border: 1px solid #ddd; padding: 15px; background: #f9f9f9;">
                        <pre style="white-space: pre-wrap; margin: 0; font-family: Arial;">${acta.descripcion || acta.descripcion_hechos || 'Sin descripción'}</pre>
                    </div>
                </div>
                
                <div style="margin-top: 50px; text-align: center;">
                    <p>_________________________</p>
                    <p>Firma del Inspector</p>
                </div>
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

async function eliminarActa(actaId, numeroActa) {
    if (!confirm(`¿Estás seguro de que quieres eliminar el acta "${numeroActa}"?\n\nEsta acción no se puede deshacer.`)) {
        return;
    }

    try {
        const response = await fetchWithTimeout(`${window.location.origin}${window.location.pathname}?api=delete-acta`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({acta_id: actaId})
        });

        const data = await response.json();

        if (data.success) {
            mostrarExitoActas('Acta eliminada correctamente');
            cargarActasDesdeAPI(); // Recargar la lista
        } else {
            mostrarErrorActas('Error al eliminar acta: ' + (data.message || 'Error desconocido'));
        }
    } catch (error) {
        mostrarErrorActas('Error de conexión: ' + error.message);
    }
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
    switch (estado) {
        case 'pendiente': return 'bg-warning';
        case 'pagada': return 'bg-success';
        case 'anulada': return 'bg-danger';
        default: return 'bg-secondary';
    }
}

function getEstadoDisplayName(estado) {
    switch (estado) {
        case 'pendiente': return 'Pendiente';
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
    // Preparar datos para Excel
    const datosExcel = todasLasActas.map(acta => ({
        'Número Acta': acta.numero_acta || 'N/A',
        'Placa': acta.placa || acta.placa_vehiculo || 'N/A',
        'Conductor': acta.conductor_nombre || acta.nombre_conductor || 'N/A',
        'RUC/DNI': acta.ruc_dni || 'N/A',
        'Razón Social': acta.razon_social || 'N/A',
        'Estado': acta.estado || 'N/A',
        'Fecha Intervención': acta.fecha_intervencion || 'N/A',
        'Hora Intervención': acta.hora_intervencion || 'N/A',
        'Lugar': acta.lugar_intervencion || 'N/A',
        'Tipo Servicio': acta.tipo_servicio || 'N/A',
        'Monto Multa': acta.monto_multa ? `S/ ${acta.monto_multa}` : 'N/A',
        'Inspector': acta.inspector_responsable || 'N/A',
        'Fecha Creación': acta.created_at ? formatDate(acta.created_at) : 'N/A'
    }));
    
    // Crear CSV y descargarlo
    const csv = convertirACSV(datosExcel);
    descargarArchivo(csv, 'actas_fiscalizacion.csv', 'text/csv');
    
    mostrarExitoActas(`Exportadas ${datosExcel.length} actas a Excel`);
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
    return `
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Reporte de Actas de Fiscalización</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
            .header h1 { margin: 0; color: #333; }
            .header h2 { margin: 5px 0; color: #666; font-weight: normal; }
            table { width: 100%; border-collapse: collapse; margin: 20px 0; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; font-weight: bold; }
            .numero { font-weight: bold; color: #0066cc; }
            .estado-pendiente { background-color: #fff3cd; color: #856404; }
            .estado-pagada { background-color: #d1ecf1; color: #0c5460; }
            .estado-anulada { background-color: #f8d7da; color: #721c24; }
            .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
            @media print { body { margin: 0; } }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>REPORTE DE ACTAS DE FISCALIZACIÓN</h1>
            <h2>Fecha de generación: ${new Date().toLocaleDateString('es-PE')}</h2>
            <h2>Total de actas: ${todasLasActas.length}</h2>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>N° Acta</th>
                    <th>Placa</th>
                    <th>Conductor</th>
                    <th>RUC/DNI</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Monto</th>
                </tr>
            </thead>
            <tbody>
                ${todasLasActas.map(acta => `
                    <tr>
                        <td class="numero">${acta.numero_acta || 'N/A'}</td>
                        <td>${acta.placa || acta.placa_vehiculo || 'N/A'}</td>
                        <td>${acta.conductor_nombre || acta.nombre_conductor || 'N/A'}</td>
                        <td>${acta.ruc_dni || 'N/A'}</td>
                        <td class="estado-${acta.estado || 'pendiente'}">${getEstadoDisplayName(acta.estado)}</td>
                        <td>${acta.fecha_intervencion || formatDate(acta.created_at)}</td>
                        <td>${acta.monto_multa ? 'S/ ' + acta.monto_multa : 'N/A'}</td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
        
        <div class="footer">
            <p>Sistema de Gestión de Actas - Generado automáticamente</p>
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
        mostrarErrorActas('Error de conexión al cargar mi historial: ' + error.message);
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
        const estado = acta.estado || 'pendiente';
        const estadoClase = {
            'pendiente': 'warning',
            'procesada': 'info',
            'pagada': 'success',
            'anulada': 'danger'
        }[estado] || 'secondary';

        const monto = acta.monto_multa ? `S/ ${parseFloat(acta.monto_multa).toFixed(2)}` : 'No especificado';

        return `
            <tr>
                <td><strong>${acta.numero_acta || 'N/A'}</strong></td>
                <td>${acta.fecha_creacion ? new Date(acta.fecha_creacion).toLocaleDateString('es-PE') : 'N/A'}</td>
                <td><span class="badge bg-primary">${acta.placa_vehiculo || 'N/A'}</span></td>
                <td>${acta.conductor_nombre || 'N/A'}</td>
                <td><span class="badge bg-${estadoClase}">${estado.charAt(0).toUpperCase() + estado.slice(1)}</span></td>
                <td><strong>${monto}</strong></td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-primary me-1" onclick="verDetalleActaFiscalizador(${acta.id})" title="Ver detalle">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-success" onclick="exportarActaIndividual(${acta.id})" title="Exportar">
                        <i class="fas fa-download"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

// Función para actualizar estadísticas del fiscalizador
function actualizarEstadisticasFiscalizador(actas) {
    const total = actas.length;
    const pendientes = actas.filter(a => a.estado === 'pendiente').length;
    const procesadas = actas.filter(a => a.estado === 'procesada').length;
    const pagadas = actas.filter(a => a.estado === 'pagada').length;
    const anuladas = actas.filter(a => a.estado === 'anulada').length;

    document.getElementById('totalActasFisca').textContent = total;
    document.getElementById('actasPendientesFisca').textContent = pendientes;
    document.getElementById('actasPagadasFisca').textContent = pagadas;
    document.getElementById('actasAnuladasFisca').textContent = anuladas;
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
window.eliminarActa = eliminarActa;
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

console.log('✅ Fiscalizador Actas JS cargado correctamente');
