/**
 * SISTEMA DE GESTIÓN - MÓDULO INSPECTOR
 * Funcionalidades específicas para el rol inspector
 */

console.log('🔍 Cargando módulo inspector...');

// Variable global para verificar que el usuario es inspector
let isInspector = false;

// Inicialización del módulo inspector
document.addEventListener('DOMContentLoaded', function() {
    if (window.dashboardUserRole === 'inspector') {
        isInspector = true;
        console.log('✅ Módulo inspector habilitado para:', window.dashboardUserName);
        initializeInspectorModule();
    }
});

function initializeInspectorModule() {
    console.log('🚀 Inicializando módulo inspector...');
    
    // Cargar estadísticas del dashboard al inicio
    loadDashboardStatsInspector();
}

// ==================== DASHBOARD STATS INSPECTOR ====================
async function loadDashboardStatsInspector() {
    console.log('📊 Cargando estadísticas de inspector...');
    
    try {
        const response = await fetch(`${window.location.origin}${window.location.pathname}?api=dashboard-stats`);
        const result = await response.json();
        
        if (result.success && result.stats) {
            updateDashboardStatsInspector(result.stats);
        } else {
            console.error('❌ Error al cargar estadísticas:', result.message);
        }
    } catch (error) {
        console.error('❌ Error al cargar estadísticas de inspector:', error);
    }
}

function updateDashboardStatsInspector(stats) {
    console.log('📈 Actualizando estadísticas de inspector:', stats);
    
    // Actualizar contadores específicos para inspector
    if (document.getElementById('total-actas')) {
        document.getElementById('total-actas').textContent = stats.total_actas || 0;
    }
    
    if (document.getElementById('total-conductores')) {
        document.getElementById('total-conductores').textContent = stats.inspecciones_realizadas || 0;
    }
    
    if (document.getElementById('total-vehiculos')) {
        document.getElementById('total-vehiculos').textContent = stats.vehiculos_inspeccionados || 0;
    }
    
    if (document.getElementById('total-notifications')) {
        document.getElementById('total-notifications').textContent = stats.inspecciones_pendientes || 0;
    }
}

// ==================== FUNCIONES INSPECTOR ====================
function loadMisInspecciones() {
    console.log('🔍 Cargando mis inspecciones...');
    alert('🚧 Mis Inspecciones - Funcionalidad en desarrollo');
}

function loadVehiculos() {
    console.log('🚗 Cargando vehículos...');
    alert('🚧 Vehículos - Funcionalidad en desarrollo');
}

// ==================== EXPORTAR FUNCIONES ====================
// Hacer las funciones disponibles globalmente para inspector
window.loadMisInspecciones = loadMisInspecciones;
window.loadVehiculos = loadVehiculos;
window.loadDashboardStatsInspector = loadDashboardStatsInspector;

console.log('✅ Módulo inspector cargado completamente');