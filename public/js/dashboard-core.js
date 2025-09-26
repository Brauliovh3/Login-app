/**
 * Funciones principales del Dashboard
 * Archivo: dashboard-core.js
 */

// Variables globales del dashboard
let userName = '';
let userRole = '';
let currentNotifications = [];

/**
 * Inicialización del dashboard
 */
function initDashboard() {
    console.log('🚀 Inicializando Dashboard...');
    
    // Cargar datos del usuario desde las variables PHP
    userName = window.dashboardUserName || '';
    userRole = window.dashboardUserRole || '';
    
    // Cargar estadísticas iniciales
    loadDashboardStats();
    
    // Configurar notificaciones
    loadNotifications();
    
    // Configurar eventos de la sidebar
    setupSidebarEvents();
    
    // Mostrar sección dashboard por defecto
    loadSection('dashboard');
    
    console.log('✅ Dashboard inicializado');
}

/**
 * Ocultar todas las secciones
 */
function hideAllSections() {
    const sections = document.querySelectorAll('.content-section');
    sections.forEach(section => {
        section.style.display = 'none';
        section.classList.remove('active');
    });
    
    // También ocultar el contenido del container principal
    const contentContainer = document.getElementById('contentContainer');
    if (contentContainer) {
        contentContainer.innerHTML = '';
    }
}

/**
 * Mostrar sección específica
 */
function showSection(sectionId) {
    console.log('📄 Mostrando sección:', sectionId);
    const section = document.getElementById(sectionId);
    if (section) {
        section.style.display = 'block';
        section.classList.add('active');
    }
}

/**
 * Cargar sección principal
 */
function loadSection(sectionId) {
    console.log('🔄 Cargando sección:', sectionId);
    
    // Actualizar enlaces activos de la sidebar
    updateActiveMenuItem(sectionId);
    
    // Ocultar todas las secciones primero
    hideAllSections();
    
    // Cargar la sección correspondiente
    switch(sectionId) {
        case 'dashboard':
            loadDashboardContent();
            break;
        case 'actas':
            loadActas();
            break;
        case 'actas-contra':
            loadActasContravencion();
            break;
        case 'usuarios':
            loadUsuariosList();
            break;
        case 'reportes':
            loadReportes();
            break;
        case 'configuracion':
            loadConfiguracion();
            break;
        case 'perfil':
            loadPerfil();
            break;
        default:
            loadDefaultSection(sectionId);
    }
}

/**
 * Cargar contenido por defecto para secciones no definidas
 */
function loadDefaultSection(sectionId) {
    const contentContainer = document.getElementById('contentContainer');
    contentContainer.innerHTML = `
        <div class="content-section active">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Sección: ${sectionId}</strong><br>
                Esta funcionalidad estará disponible próximamente.
            </div>
        </div>
    `;
}

/**
 * Actualizar elemento activo del menú
 */
function updateActiveMenuItem(sectionId) {
    // Remover clases activas existentes
    document.querySelectorAll('.sidebar-link.active, .sidebar-sublink.active').forEach(link => {
        link.classList.remove('active');
    });
    
    // Agregar clase activa al elemento correspondiente
    const activeLink = document.querySelector(`[onclick*="${sectionId}"], [href*="${sectionId}"]`);
    if (activeLink) {
        activeLink.classList.add('active');
    }
}

/**
 * Toggle submenu
 */
function toggleSubmenu(menuId, event) {
    if (event) event.preventDefault();
    console.log('🔄 toggleSubmenu ejecutándose:', menuId);
    
    // Si es el TEST CLICK (submenu usuarios), ejecutar loadTestClick
    if (menuId === 'usuarios') {
        hideAllSections();
        loadTestClick();
        return;
    }
    
    const submenu = document.getElementById(`submenu-${menuId}`);
    const toggle = event ? event.currentTarget : null;
    
    if (!submenu) {
        console.error('❌ No se encontró el submenu:', `submenu-${menuId}`);
        return;
    }
    
    if (submenu.classList.contains('show')) {
        submenu.classList.remove('show');
        if (toggle) toggle.classList.remove('expanded');
    } else {
        // Cerrar otros submenus
        document.querySelectorAll('.sidebar-submenu.show').forEach(sub => {
            sub.classList.remove('show');
        });
        document.querySelectorAll('.sidebar-toggle.expanded').forEach(tog => {
            tog.classList.remove('expanded');
        });
        
        submenu.classList.add('show');
        if (toggle) toggle.classList.add('expanded');
    }
}

/**
 * Configurar eventos de la sidebar
 */
function setupSidebarEvents() {
    // Configurar toggles de submenu
    document.querySelectorAll('.sidebar-toggle').forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            const menuId = this.getAttribute('data-menu');
            if (menuId) {
                toggleSubmenu(menuId, e);
            }
        });
    });
}

// Exponer funciones globalmente
window.initDashboard = initDashboard;
window.loadSection = loadSection;
window.hideAllSections = hideAllSections;
window.showSection = showSection;
window.toggleSubmenu = toggleSubmenu;
window.updateActiveMenuItem = updateActiveMenuItem;

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Pequeño delay para asegurar que todos los scripts estén cargados
    setTimeout(initDashboard, 100);
});