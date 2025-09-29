/**
 * ================================
 * DASHBOARD CORE FUNCTIONS
 * Sistema de Gestión - JavaScript Principal
 * ================================
 */

// Variables globales
let currentSection = 'dashboard';
let todosLosUsuarios = [];
let userName = '';
let userRole = '';

// ================================
// FUNCIONES DE INICIALIZACIÓN
// ================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Dashboard cargado correctamente');
    
    // Obtener datos del usuario desde variables PHP
    userName = window.dashboardUserName || '';
    userRole = window.dashboardUserRole || '';
    
    initializeApp();
    bindSidebarEvents();
});

function initializeApp() {
    console.log('🚀 Inicializando aplicación...');
    console.log('👤 Usuario logueado:', userRole);
    
    // Cargar contenido inicial del dashboard
    loadDashboardContent();
    
    // Cargar estadísticas específicas según el rol
    if (userRole === 'administrador' || userRole === 'admin') {
        // Las estadísticas se cargan desde administrador.js
        console.log('📊 Estadísticas de administrador serán cargadas por módulo específico');
    } else if (userRole === 'fiscalizador') {
        // Las estadísticas se cargan desde fiscalizador.js
        console.log('📊 Estadísticas de fiscalizador serán cargadas por módulo específico');
    } else {
        // Cargar estadísticas genéricas para otros roles
        loadDashboardStats();
    }
    
    loadNotifications();
    
    // Auto-refresh notificaciones cada 30 segundos
    setInterval(loadNotifications, 30000);
    
    console.log('✅ Aplicación inicializada');
}

// ================================
// FUNCIONES DE NAVEGACIÓN
// ================================

function loadSection(sectionId) {
    console.log('🔄 Cargando sección:', sectionId);
    
    // Actualizar el estado activo en el menú
    updateActiveMenuItem(sectionId);
    
    // Ocultar todas las secciones existentes
    hideAllSections();
    
    // Mostrar loading
    showLoading();
    
    // Cargar contenido según la sección
    switch(sectionId) {
        case 'dashboard':
            showDashboardSection();
            loadDashboardContent();
            loadDashboardStats(); // Cargar estadísticas solo en dashboard principal
            break;
        case 'listar-usuarios':
            loadUsuariosList();
            break;
        case 'aprobar-usuarios':
            loadAprobarUsuarios();
            break;
        case 'crear-usuario':
            showCrearUsuarioModal();
            return;
        case 'roles-permisos':
            loadRolesPermisos();
            break;
        case 'crear-acta':
            loadCrearActa();
            break;
        case 'mis-actas':
            loadMisActas();
            break;
        case 'actas-contra':
            loadActasContra();
            break;
        case 'reportes':
            loadReportes();
            break;
        case 'configuracion':
            loadConfiguracion();
            break;
        case 'perfil':
            loadPerfilMejorado();
            break;
        default:
            loadDefaultSection(sectionId);
    }
    
    currentSection = sectionId;
    
    // Ocultar loading
    setTimeout(hideLoading, 300);
}

function hideAllSections() {
    const dashboardSection = document.getElementById('dashboard-section');
    if (dashboardSection) {
        dashboardSection.style.display = 'none';
    }
    
    const contentContainer = document.getElementById('contentContainer');
    if (contentContainer) {
        contentContainer.innerHTML = '';
    }
}

function showDashboardSection() {
    const dashboardSection = document.getElementById('dashboard-section');
    if (dashboardSection) {
        dashboardSection.style.display = 'block';
        dashboardSection.classList.add('active');
    }
}

function updateActiveMenuItem(sectionId) {
    // Remover clases activas existentes
    document.querySelectorAll('.sidebar-link, .sidebar-sublink').forEach(link => {
        link.classList.remove('active');
    });
    
    const activeLink = document.querySelector(`[data-section="${sectionId}"]`);
    if (activeLink) {
        activeLink.classList.add('active');
    }
}

// ================================
// FUNCIONES DE SIDEBAR
// ================================

function bindSidebarEvents() {
    const sidebarMenu = document.getElementById('sidebarMenu');
    if (!sidebarMenu) {
        console.warn('⚠️ sidebarMenu no encontrado, reintentando en 300ms');
        setTimeout(bindSidebarEvents, 300);
        return;
    }

    sidebarMenu.addEventListener('click', function(e) {
        const a = e.target.closest('a');
        if (!a || !sidebarMenu.contains(a)) return;

        const href = a.getAttribute('href') || '';
        if (href && !href.startsWith('javascript') && href !== '#') {
            return; // Permitir navegación normal
        }

        e.preventDefault();

        const section = a.getAttribute('data-section');
        if (section) {
            console.log('➡️ Sidebar click (data-section):', section);
            loadSection(section);
            return;
        }

        // Manejar onclick attributes
        const onclick = a.getAttribute('onclick') || '';
        if (onclick) {
            console.log('➡️ Sidebar click (onclick):', onclick);
            
            if (onclick.includes('loadUsuariosList')) {
                loadUsuariosList();
                return;
            }
            
            if (onclick.includes('toggleSubmenu')) {
                const match = onclick.match(/toggleSubmenu\((?:'|")?(\w+)(?:'|")?\s*,\s*event\)/);
                if (match) {
                    toggleSubmenu(match[1], e);
                }
                return;
            }
            
            if (onclick.includes('showCrearUsuarioModal')) {
                showCrearUsuarioModal();
                return;
            }
        }

        console.log('ℹ️ Click en sidebar sin acción mapeada:', a);
    });

    console.log('✅ Event listeners del sidebar configurados');
}

function toggleSubmenu(menuId, event) {
    console.log('🔄 [DEBUG] toggleSubmenu llamado con menuId:', menuId);
    
    if (event) {
        event.preventDefault();
        console.log('✅ [DEBUG] preventDefault ejecutado');
    }
    
    const submenu = document.getElementById('submenu-' + menuId);
    console.log('🔍 [DEBUG] Buscando elemento:', 'submenu-' + menuId);
    console.log('📍 [DEBUG] Elemento encontrado:', !!submenu);
    
    if (!submenu) {
        console.error('❌ [DEBUG] Submenu no encontrado:', 'submenu-' + menuId);
        return;
    }
    
    // Obtener el estado actual
    const currentDisplay = submenu.style.display;
    const isHidden = currentDisplay === 'none' || currentDisplay === '';
    
    console.log('📊 [DEBUG] Estado actual - display:', currentDisplay, 'isHidden:', isHidden);
    
    if (isHidden) {
        // Mostrar - forzar con !important
        submenu.style.setProperty('display', 'block', 'important');
        submenu.classList.add('show');
        console.log('✅ [DEBUG] Submenu mostrado con !important');
    } else {
        // Ocultar - forzar con !important
        submenu.style.setProperty('display', 'none', 'important');
        submenu.classList.remove('show');
        console.log('✅ [DEBUG] Submenu ocultado con !important');
    }
    
    console.log('🏁 [DEBUG] toggleSubmenu completado');
}

// Función alternativa para toggle submenu con método más directo
function toggleSubmenuAlt(menuId, event) {
    console.log('🔄 [ALT] toggleSubmenuAlt llamado con menuId:', menuId);
    
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    const submenu = document.getElementById('submenu-' + menuId);
    console.log('🔍 [ALT] Elemento encontrado:', !!submenu);
    
    if (!submenu) {
        console.error('❌ [ALT] Submenu no encontrado');
        return;
    }
    
    // Método más directo - alternar visibilidad
    if (submenu.style.visibility === 'hidden' || submenu.style.display === 'none' || !submenu.style.display) {
        // Mostrar
        submenu.style.display = 'block';
        submenu.style.visibility = 'visible';
        submenu.style.opacity = '1';
        submenu.style.height = 'auto';
        console.log('✅ [ALT] Submenu mostrado');
    } else {
        // Ocultar
        submenu.style.display = 'none';
        submenu.style.visibility = 'hidden';
        submenu.style.opacity = '0';
        console.log('✅ [ALT] Submenu ocultado');
    }
}

// Función de emergencia - forzar mostrar submenu
function forceShowSubmenu(menuId) {
    console.log('💪 [FORCE] Forzando mostrar submenu:', menuId);
    
    const submenu = document.getElementById('submenu-' + menuId);
    if (!submenu) {
        console.error('❌ [FORCE] Submenu no encontrado');
        return;
    }
    
    console.log('🔍 [FORCE] Estado inicial:');
    console.log('- innerHTML:', submenu.innerHTML.substring(0, 100) + '...');
    console.log('- style.display:', submenu.style.display);
    console.log('- computed display:', window.getComputedStyle(submenu).display);
    console.log('- classList:', submenu.classList.toString());
    console.log('- offsetHeight:', submenu.offsetHeight);
    console.log('- clientHeight:', submenu.clientHeight);
    
    // Remover todos los estilos que podrían ocultarlo
    submenu.removeAttribute('style');
    submenu.className = 'sidebar-submenu show';
    
    // Agregar estilos inline forzados
    submenu.style.cssText = 'display: block !important; visibility: visible !important; opacity: 1 !important; height: auto !important; max-height: none !important; overflow: visible !important;';
    
    console.log('🔍 [FORCE] Estado después del forzado:');
    console.log('- style.display:', submenu.style.display);
    console.log('- computed display:', window.getComputedStyle(submenu).display);
    console.log('- classList:', submenu.classList.toString());
    console.log('- offsetHeight:', submenu.offsetHeight);
    console.log('- clientHeight:', submenu.clientHeight);
    
    // Verificar si el padre también está visible
    const parent = submenu.parentElement;
    if (parent) {
        console.log('🔍 [FORCE] Estado del padre:');
        console.log('- parent tagName:', parent.tagName);
        console.log('- parent computed display:', window.getComputedStyle(parent).display);
        console.log('- parent offsetHeight:', parent.offsetHeight);
    }
    
    console.log('✅ [FORCE] Submenu forzado a mostrar');
}

// Función de diagnóstico CSS completo
window.diagnosticarSubmenu = function(menuId) {
    console.log('🔬 [DIAGNÓSTICO] Análisis completo del submenu:', menuId);
    
    const submenu = document.getElementById('submenu-' + menuId);
    if (!submenu) {
        console.error('❌ [DIAGNÓSTICO] Submenu no encontrado');
        return;
    }
    
    const computed = window.getComputedStyle(submenu);
    const rect = submenu.getBoundingClientRect();
    
    console.log('📋 [DIAGNÓSTICO] Propiedades del elemento:');
    console.log('- ID:', submenu.id);
    console.log('- Tag:', submenu.tagName);
    console.log('- Classes:', submenu.className);
    console.log('- Style attribute:', submenu.getAttribute('style'));
    
    console.log('📏 [DIAGNÓSTICO] Dimensiones y posición:');
    console.log('- offsetWidth:', submenu.offsetWidth);
    console.log('- offsetHeight:', submenu.offsetHeight);
    console.log('- clientWidth:', submenu.clientWidth);
    console.log('- clientHeight:', submenu.clientHeight);
    console.log('- scrollWidth:', submenu.scrollWidth);
    console.log('- scrollHeight:', submenu.scrollHeight);
    console.log('- getBoundingClientRect:', rect);
    
    console.log('🎨 [DIAGNÓSTICO] Estilos computados relevantes:');
    console.log('- display:', computed.display);
    console.log('- visibility:', computed.visibility);
    console.log('- opacity:', computed.opacity);
    console.log('- height:', computed.height);
    console.log('- max-height:', computed.maxHeight);
    console.log('- overflow:', computed.overflow);
    console.log('- position:', computed.position);
    console.log('- z-index:', computed.zIndex);
    console.log('- transform:', computed.transform);
    
    console.log('👨‍👩‍👧‍👦 [DIAGNÓSTICO] Jerarquía de padres:');
    let parent = submenu.parentElement;
    let level = 1;
    while (parent && level <= 5) {
        const parentComputed = window.getComputedStyle(parent);
        console.log(`- Nivel ${level} (${parent.tagName}):`, {
            id: parent.id,
            classes: parent.className,
            display: parentComputed.display,
            visibility: parentComputed.visibility,
            overflow: parentComputed.overflow,
            height: parentComputed.height
        });
        parent = parent.parentElement;
        level++;
    }
    
    console.log('📜 [DIAGNÓSTICO] Reglas CSS aplicadas:');
    try {
        const sheets = document.styleSheets;
        const matchingRules = [];
        
        for (let sheet of sheets) {
            try {
                for (let rule of sheet.cssRules || sheet.rules || []) {
                    if (rule.selectorText && submenu.matches(rule.selectorText)) {
                        matchingRules.push({
                            selector: rule.selectorText,
                            cssText: rule.cssText,
                            href: sheet.href
                        });
                    }
                }
            } catch (e) {
                console.log('- No se pudo acceder a reglas de:', sheet.href || 'inline');
            }
        }
        
        console.log('- Reglas que coinciden:', matchingRules.length);
        matchingRules.forEach((rule, index) => {
            console.log(`  ${index + 1}.`, rule.selector, '→', rule.cssText.substring(0, 100) + '...');
        });
    } catch (e) {
        console.log('- Error al analizar reglas CSS:', e.message);
    }
    
    console.log('🔬 [DIAGNÓSTICO] Análisis completado');
};

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar) {
        sidebar.classList.toggle('show');
    }
}

// ================================
// FUNCIONES DE LOADING
// ================================

function showLoading() {
    const loading = document.getElementById('loading');
    if (loading) {
        loading.style.display = 'block';
    }
}

function hideLoading() {
    const loading = document.getElementById('loading');
    if (loading) {
        loading.style.display = 'none';
    }
}

// ================================
// FUNCIONES DE ALERTAS
// ================================

function showAlert(type, message, title = '') {
    const alertId = 'alert-' + Date.now();
    const alertClass = type === 'error' ? 'danger' : type;
    
    const alertHTML = `
        <div id="${alertId}" class="alert alert-${alertClass} alert-dismissible fade show custom-alert" 
             style="position: fixed; top: 90px; right: 20px; z-index: 9999; min-width: 300px; max-width: 500px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);" 
             role="alert">
            ${title ? `<h6 class="alert-heading">${title}</h6>` : ''}
            <div class="d-flex align-items-center">
                <i class="fas fa-${getAlertIcon(type)} me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close" onclick="closeAlert('${alertId}')"></button>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', alertHTML);
    setTimeout(() => closeAlert(alertId), 5000);
}

function getAlertIcon(type) {
    const icons = {
        'success': 'check-circle',
        'error': 'exclamation-triangle',
        'warning': 'exclamation-triangle',
        'info': 'info-circle'
    };
    return icons[type] || 'info-circle';
}

function closeAlert(alertId) {
    const alert = document.getElementById(alertId);
    if (alert) {
        alert.classList.add('fade');
        setTimeout(() => alert.remove(), 150);
    }
}

// ================================
// FUNCIONES DE UTILIDAD
// ================================

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function getStatusBadge(status) {
    const badges = {
        'approved': '<span class="badge bg-success">Aprobado</span>',
        'pending': '<span class="badge bg-warning">Pendiente</span>',
        'rejected': '<span class="badge bg-danger">Rechazado</span>',
        'suspended': '<span class="badge bg-secondary">Suspendido</span>'
    };
    return badges[status] || '<span class="badge bg-dark">Desconocido</span>';
}

function getRoleBadge(role) {
    const badges = {
        'administrador': '<span class="badge bg-primary">Administrador</span>',
        'fiscalizador': '<span class="badge bg-info">Fiscalizador</span>',
        'inspector': '<span class="badge bg-success">Inspector</span>',
        'ventanilla': '<span class="badge bg-warning">Ventanilla</span>'
    };
    return badges[role] || '<span class="badge bg-dark">' + role + '</span>';
}

// ================================
// FUNCIONES DE CONTENIDO
// ================================

function loadDashboardContent() {
    console.log('📊 Cargando contenido del dashboard...');
    
    const contentContainer = document.getElementById('contentContainer');
    if (!contentContainer) return;
    
    // Limpiar el contenedor y mostrar mensaje de bienvenida
    contentContainer.innerHTML = `
        <div class="dashboard-welcome">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3><i class="fas fa-tachometer-alt text-primary"></i> Bienvenido, ${userName}!</h3>
                    <p class="text-muted mb-0">Panel de control del sistema - Rol: <strong>${userRole.toUpperCase()}</strong></p>
                    <small class="text-muted">Último acceso: ${new Date().toLocaleDateString('es-ES')}</small>
                </div>
                <div class="col-md-4 text-end">
                    <div class="d-flex gap-2 justify-content-end">
                        <button class="btn btn-outline-primary btn-sm" onclick="loadDashboardStats()">
                            <i class="fas fa-refresh"></i> Actualizar
                        </button>
                        <button class="btn btn-outline-info btn-sm" onclick="loadNotifications()">
                            <i class="fas fa-bell"></i> Notificaciones
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    console.log('✅ Contenido del dashboard cargado');
}

function loadNotifications() {
    console.log('🔔 Cargando notificaciones...');
    
    fetch('dashboard.php?api=notifications')
        .then(response => response.json())
        .then(data => {
            console.log('Notificaciones recibidas:', data);
            updateNotificationBadge(data.count || 0);
        })
        .catch(error => {
            console.error('Error cargando notificaciones:', error);
        });
}

function updateNotificationBadge(count) {
    const badge = document.getElementById('notificationBadge');
    if (badge) {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'inline' : 'none';
    }
}

// NOTA: Esta función fue movida al módulo administrador.js para evitar conflictos
// function loadAprobarUsuarios() {
//     console.log('👥 Función movida a administrador.js');
//     if (window.loadAprobarUsuarios && typeof window.loadAprobarUsuarios === 'function') {
//         window.loadAprobarUsuarios();
//     } else {
//         console.warn('⚠️ Función loadAprobarUsuarios no disponible en módulo específico');
//     }
// }

function loadRolesPermisos() {
    console.log('🔐 Cargando roles y permisos...');
    
    const contentContainer = document.getElementById('contentContainer');
    if (contentContainer) {
        contentContainer.innerHTML = `
            <div class="content-section active">
                <h2><i class="fas fa-user-shield"></i> Roles y Permisos</h2>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    Gestión de roles y permisos del sistema
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Roles Disponibles</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <div class="list-group-item">
                                        <strong>Administrador</strong> - Control total del sistema
                                    </div>
                                    <div class="list-group-item">
                                        <strong>Fiscalizador</strong> - Gestión de infracciones
                                    </div>
                                    <div class="list-group-item">
                                        <strong>Inspector</strong> - Creación de actas
                                    </div>
                                    <div class="list-group-item">
                                        <strong>Ventanilla</strong> - Atención al público
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Permisos por Rol</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning">
                                    <i class="fas fa-construction"></i>
                                    Configuración de permisos en desarrollo
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
}

function loadCrearActa() {
    console.log('📝 Cargando formulario de crear acta...');
    
    const contentContainer = document.getElementById('contentContainer');
    if (contentContainer) {
        contentContainer.innerHTML = `
            <div class="content-section active">
                <h2><i class="fas fa-plus-circle"></i> Crear Nueva Acta</h2>
                <div class="card">
                    <div class="card-body">
                        <form id="crearActaForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Número de Placa</label>
                                    <input type="text" class="form-control" name="placa" required 
                                           placeholder="ABC-123">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tipo de Infracción</label>
                                    <select class="form-select" name="tipo_infraccion" required>
                                        <option value="">Seleccionar infracción</option>
                                        <option value="exceso_velocidad">Exceso de velocidad</option>
                                        <option value="estacionamiento_prohibido">Estacionamiento prohibido</option>
                                        <option value="semaforo_rojo">Semáforo en rojo</option>
                                        <option value="sin_licencia">Sin licencia de conducir</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">DNI del Conductor</label>
                                    <input type="text" class="form-control" name="dni_conductor" 
                                           placeholder="12345678" maxlength="8">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nombre del Conductor</label>
                                    <input type="text" class="form-control" name="nombre_conductor" 
                                           placeholder="Nombre completo">
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Observaciones</label>
                                    <textarea class="form-control" name="observaciones" rows="3" 
                                              placeholder="Describa los detalles de la infracción..."></textarea>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Crear Acta
                                </button>
                                <button type="reset" class="btn btn-secondary">
                                    <i class="fas fa-eraser"></i> Limpiar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;
        
        // Agregar event listener al formulario
        document.getElementById('crearActaForm').addEventListener('submit', function(e) {
            e.preventDefault();
            showAlert('info', 'Funcionalidad de crear acta en desarrollo');
        });
    }
}

function loadMisActas() {
    console.log('📋 Cargando mis actas...');
    
    const contentContainer = document.getElementById('contentContainer');
    if (contentContainer) {
        contentContainer.innerHTML = `
            <div class="content-section active">
                <h2><i class="fas fa-file-alt"></i> Mis Actas</h2>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    Actas creadas por el usuario actual
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="text-center py-4">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aún no has creado ninguna acta</p>
                            <button onclick="loadSection('crear-acta')" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Crear Primera Acta
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
}

function loadActasContra() {
    console.log('⚖️ Cargando actas contraventor...');
    
    const contentContainer = document.getElementById('contentContainer');
    if (contentContainer) {
        contentContainer.innerHTML = `
            <div class="content-section active">
                <h2><i class="fas fa-gavel"></i> Actas por Contraventor</h2>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    Búsqueda de actas por DNI del contraventor
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h6>Buscar por DNI</h6>
                                <div class="mb-3">
                                    <input type="text" class="form-control" id="dniSearch" 
                                           placeholder="Ingrese DNI" maxlength="8">
                                </div>
                                <button onclick="buscarActasPorDNI()" class="btn btn-primary btn-block">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <div id="resultadosActas" class="text-center text-muted">
                                    <i class="fas fa-search fa-2x mb-2"></i>
                                    <p>Ingrese un DNI para buscar actas</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
}

function loadReportes() {
    console.log('📊 Cargando reportes...');
    
    const contentContainer = document.getElementById('contentContainer');
    if (contentContainer) {
        contentContainer.innerHTML = `
            <div class="content-section active">
                <h2><i class="fas fa-chart-bar"></i> Reportes</h2>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-users fa-2x text-primary mb-2"></i>
                                <h6>Reporte de Usuarios</h6>
                                <p class="text-muted">Estadísticas de usuarios del sistema</p>
                                <button class="btn btn-primary btn-sm" onclick="generarReporte('usuarios')">
                                    <i class="fas fa-download"></i> Generar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-file-alt fa-2x text-success mb-2"></i>
                                <h6>Reporte de Actas</h6>
                                <p class="text-muted">Actas creadas por período</p>
                                <button class="btn btn-success btn-sm" onclick="generarReporte('actas')">
                                    <i class="fas fa-download"></i> Generar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-money-bill fa-2x text-warning mb-2"></i>
                                <h6>Reporte de Multas</h6>
                                <p class="text-muted">Ingresos por multas</p>
                                <button class="btn btn-warning btn-sm" onclick="generarReporte('multas')">
                                    <i class="fas fa-download"></i> Generar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
}

function loadConfiguracion() {
    console.log('⚙️ Cargando configuración...');
    
    const contentContainer = document.getElementById('contentContainer');
    if (contentContainer) {
        contentContainer.innerHTML = `
            <div class="content-section active">
                <h2><i class="fas fa-cog"></i> Configuración</h2>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6>Configuración General</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Nombre del Sistema</label>
                                    <input type="text" class="form-control" value="Sistema de Gestión" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Versión</label>
                                    <input type="text" class="form-control" value="1.0.0" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Zona Horaria</label>
                                    <select class="form-select">
                                        <option selected>America/Lima</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6>Configuración de Notificaciones</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="emailNotif" checked>
                                    <label class="form-check-label" for="emailNotif">
                                        Notificaciones por email
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="systemNotif" checked>
                                    <label class="form-check-label" for="systemNotif">
                                        Notificaciones del sistema
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="soundNotif">
                                    <label class="form-check-label" for="soundNotif">
                                        Sonidos de notificación
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
}

function loadPerfilMejorado() {
    console.log('👤 Cargando perfil mejorado...');
    
    const contentContainer = document.getElementById('contentContainer');
    if (contentContainer) {
        contentContainer.innerHTML = `
            <div class="content-section active">
                <h2><i class="fas fa-user"></i> Mi Perfil</h2>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="avatar-lg mx-auto mb-3">
                                    <div class="avatar-title bg-primary text-white rounded-circle">
                                        ${userName ? userName.charAt(0).toUpperCase() : 'U'}
                                    </div>
                                </div>
                                <h5>${userName || 'Usuario'}</h5>
                                <p class="text-muted">${userRole || 'Rol no definido'}</p>
                                <button class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-camera"></i> Cambiar Foto
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h6>Información Personal</h6>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Nombre Completo</label>
                                            <input type="text" class="form-control" value="${userName || ''}" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" placeholder="usuario@example.com" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Teléfono</label>
                                            <input type="text" class="form-control" placeholder="+51 999 999 999">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Rol</label>
                                            <input type="text" class="form-control" value="${userRole || ''}" readonly>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Guardar Cambios
                                        </button>
                                        <button type="button" class="btn btn-outline-warning">
                                            <i class="fas fa-key"></i> Cambiar Contraseña
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
}

// Funciones auxiliares para reportes y búsquedas
function generarReporte(tipo) {
    showAlert('info', `Generando reporte de ${tipo}...`, 'Reporte');
    // Aquí iría la lógica para generar el reporte
}

function buscarActasPorDNI() {
    const dni = document.getElementById('dniSearch').value;
    if (!dni) {
        showAlert('warning', 'Por favor ingrese un DNI', 'Búsqueda');
        return;
    }
    
    showAlert('info', `Buscando actas para DNI: ${dni}...`, 'Búsqueda');
    // Aquí iría la lógica para buscar actas
}

function renderPendingUsers(users) {
    if (!users || users.length === 0) {
        return '<p class="text-muted">No hay usuarios pendientes de aprobación</p>';
    }
    
    return users.map(user => `
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h6>${user.name}</h6>
                        <p class="text-muted mb-1">@${user.username} - ${user.email}</p>
                        <small class="text-muted">Rol solicitado: ${user.role}</small>
                    </div>
                    <div class="col-md-4 text-end">
                        <button class="btn btn-success btn-sm me-1" onclick="approveUser(${user.id})">
                            <i class="fas fa-check"></i> Aprobar
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="rejectUser(${user.id})">
                            <i class="fas fa-times"></i> Rechazar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

function approveUser(userId) {
    if (confirm('¿Está seguro de aprobar este usuario?')) {
        showAlert('success', 'Usuario aprobado correctamente');
        loadAprobarUsuarios(); // Recargar la lista
    }
}

function rejectUser(userId) {
    if (confirm('¿Está seguro de rechazar este usuario?')) {
        showAlert('warning', 'Usuario rechazado');
        loadAprobarUsuarios(); // Recargar la lista
    }
}

// ================================
// FUNCIONES DE FALLBACK
// ================================

function loadDefaultSection(sectionId) {
    const contentContainer = document.getElementById('contentContainer');
    if (contentContainer) {
        contentContainer.innerHTML = `
            <div class="content-section active">
                <h2><i class="fas fa-info-circle"></i> ${sectionId.toUpperCase()}</h2>
                <div class="alert alert-info">
                    <i class="fas fa-construction"></i> 
                    Esta sección está en desarrollo.
                </div>
                <p>La funcionalidad para <strong>${sectionId}</strong> será implementada próximamente.</p>
                <button onclick="loadSection('dashboard')" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </button>
            </div>
        `;
    }
}

// ================================
// EXPOSICIÓN DE FUNCIONES GLOBALES
// ================================

window.loadSection = loadSection;
window.loadDashboardContent = loadDashboardContent;
window.loadNotifications = loadNotifications;
// window.loadAprobarUsuarios = loadAprobarUsuarios; // Movido a administrador.js
window.loadRolesPermisos = loadRolesPermisos;
window.loadCrearActa = loadCrearActa;
window.loadMisActas = loadMisActas;
window.loadActasContra = loadActasContra;
window.loadReportes = loadReportes;
window.loadConfiguracion = loadConfiguracion;
window.loadPerfilMejorado = loadPerfilMejorado;
window.hideAllSections = hideAllSections;
window.toggleSubmenu = toggleSubmenu;
window.toggleSubmenuAlt = toggleSubmenuAlt;
window.forceShowSubmenu = forceShowSubmenu;
window.toggleSidebar = toggleSidebar;
window.showLoading = showLoading;
window.hideLoading = hideLoading;
window.showAlert = showAlert;
window.closeAlert = closeAlert;
window.formatDate = formatDate;
window.getStatusBadge = getStatusBadge;
window.getRoleBadge = getRoleBadge;
window.generarReporte = generarReporte;
window.buscarActasPorDNI = buscarActasPorDNI;
window.approveUser = approveUser;
window.rejectUser = rejectUser;

// ================================
// FUNCIONES PRINCIPALES DE SECCIONES
// ================================

// Función para cargar actas
function loadActas() {
    console.log('📋 Cargando sección de Actas...');
    hideAllSections();
    
    // Usar la nueva función de gestión de actas
    if (typeof loadGestionActas === 'function') {
        loadGestionActas();
    } else {
        // Fallback si no está cargado el archivo fiscalizador-actas.js
        const contentContainer = document.getElementById('contentContainer');
        contentContainer.innerHTML = `
            <div class="content-section active">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Módulo no disponible:</strong> No se pudo cargar el módulo de gestión de actas.
                    <br>Asegúrate de que el archivo <code>fiscalizador-actas.js</code> esté cargado.
                </div>
            </div>
        `;
    }
}

// Función para cargar conductores
function loadConductores() {
    console.log('🚗 Cargando sección de Conductores...');
    hideAllSections();
    
    const contentContainer = document.getElementById('contentContainer');
    contentContainer.innerHTML = `
        <div class="content-section active">
            <div class="content-header d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4><i class="fas fa-user-tie"></i> Gestión de Conductores</h4>
                    <p class="text-muted mb-0">Administrar información de conductores</p>
                </div>
                <button class="btn btn-primary" onclick="showCrearConductorModal()">
                    <i class="fas fa-plus"></i> Nuevo Conductor
                </button>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombres</th>
                                            <th>Apellidos</th>
                                            <th>DNI</th>
                                            <th>Licencia</th>
                                            <th>Clase</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="conductores-table-body">
                                        <tr><td colspan="7" class="text-center">Cargando conductores...</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Cargar datos de conductores
    loadConductoresData();
}

// Función para cargar vehículos
function loadVehiculos() {
    console.log('🚙 Cargando sección de Vehículos...');
    hideAllSections();
    
    const contentContainer = document.getElementById('contentContainer');
    contentContainer.innerHTML = `
        <div class="content-section active">
            <div class="content-header d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4><i class="fas fa-car"></i> Gestión de Vehículos</h4>
                    <p class="text-muted mb-0">Administrar información de vehículos</p>
                </div>
                <button class="btn btn-primary" onclick="showCrearVehiculoModal()">
                    <i class="fas fa-plus"></i> Nuevo Vehículo
                </button>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Placa</th>
                                            <th>Marca</th>
                                            <th>Modelo</th>
                                            <th>Año</th>
                                            <th>Propietario</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="vehiculos-table-body">
                                        <tr><td colspan="7" class="text-center">Cargando vehículos...</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Cargar datos de vehículos
    loadVehiculosData();
}

// Función para cargar infracciones
function loadInfracciones() {
    console.log('⚠️ Cargando sección de Infracciones...');
    hideAllSections();
    
    const contentContainer = document.getElementById('contentContainer');
    contentContainer.innerHTML = `
        <div class="content-section active">
            <div class="content-header d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4><i class="fas fa-exclamation-triangle"></i> Tipos de Infracciones</h4>
                    <p class="text-muted mb-0">Gestionar tipos y categorías de infracciones</p>
                </div>
                <button class="btn btn-primary" onclick="showCrearInfraccionModal()">
                    <i class="fas fa-plus"></i> Nueva Infracción
                </button>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Descripción</th>
                                            <th>Categoría</th>
                                            <th>Multa (S/)</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="infracciones-table-body">
                                        <tr><td colspan="6" class="text-center">Cargando infracciones...</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Cargar datos de infracciones
    loadInfraccionesData();
}

// Función para cargar perfil
function loadPerfil() {
    console.log('👤 Cargando sección de Perfil...');
    hideAllSections();
    
    const contentContainer = document.getElementById('contentContainer');
    contentContainer.innerHTML = `
        <div class="content-section active">
            <div class="content-header mb-4">
                <h4><i class="fas fa-user-circle"></i> Mi Perfil</h4>
                <p class="text-muted mb-0">Gestionar información personal y configuración</p>
            </div>
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-edit"></i> Información Personal</h5>
                        </div>
                        <div class="card-body">
                            <form id="profileForm">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nombre completo</label>
                                        <input type="text" class="form-control" id="profile-name" value="${userName}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Rol</label>
                                        <input type="text" class="form-control" value="${userRole}" readonly>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Usuario</label>
                                        <input type="text" class="form-control" id="profile-username" placeholder="Usuario">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" id="profile-email" placeholder="Email">
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary" onclick="updateProfile()">
                                    <i class="fas fa-save"></i> Guardar Cambios
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-key"></i> Cambiar Contraseña</h5>
                        </div>
                        <div class="card-body">
                            <form id="passwordForm">
                                <div class="mb-3">
                                    <label class="form-label">Contraseña actual</label>
                                    <input type="password" class="form-control" id="current-password">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nueva contraseña</label>
                                    <input type="password" class="form-control" id="new-password">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Confirmar contraseña</label>
                                    <input type="password" class="form-control" id="confirm-password">
                                </div>
                                <button type="button" class="btn btn-warning" onclick="updatePassword()">
                                    <i class="fas fa-key"></i> Cambiar Contraseña
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Cargar datos del perfil
    loadProfileData();
}

// Función para cargar configuración (solo para admins)
function loadConfiguracion() {
    if (!['administrador', 'superadmin'].includes(userRole)) {
        showAlert('warning', 'No tienes permisos para acceder a esta sección');
        return;
    }
    
    console.log('⚙️ Cargando sección de Configuración...');
    hideAllSections();
    
    const contentContainer = document.getElementById('contentContainer');
    contentContainer.innerHTML = `
        <div class="content-section active">
            <div class="content-header mb-4">
                <h4><i class="fas fa-cogs"></i> Configuración del Sistema</h4>
                <p class="text-muted mb-0">Gestionar configuración general del sistema</p>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-server"></i> Configuración General</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Configuración del Sistema:</strong> Próximamente disponible.
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><i class="fas fa-database"></i> Base de Datos</h6>
                                    <p class="text-muted">Estado: <span class="badge bg-success">Conectado</span></p>
                                </div>
                                <div class="col-md-6">
                                    <h6><i class="fas fa-users"></i> Usuarios Registrados</h6>
                                    <p class="text-muted">Total: <span id="total-users">Cargando...</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Funciones de carga de datos (placeholder - implementar según API)
function loadActasData() {
    // Implementar carga de datos de actas
    console.log('Cargando datos de actas...');
}

function loadConductoresData() {
    // Implementar carga de datos de conductores
    console.log('Cargando datos de conductores...');
}

function loadVehiculosData() {
    // Implementar carga de datos de vehículos
    console.log('Cargando datos de vehículos...');
}

function loadInfraccionesData() {
    // Implementar carga de datos de infracciones
    console.log('Cargando datos de infracciones...');
}

function loadProfileData() {
    // Implementar carga de datos del perfil
    console.log('Cargando datos del perfil...');
}

// Exponer las nuevas funciones globalmente
window.loadActas = loadActas;
window.loadConductores = loadConductores;
window.loadVehiculos = loadVehiculos;
window.loadInfracciones = loadInfracciones;
window.loadPerfil = loadPerfil;

console.log('✅ Dashboard Core cargado correctamente');