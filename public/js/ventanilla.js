/**
 * SISTEMA DE GESTIÓN - MÓDULO VENTANILLA
 * Funcionalidades específicas para el rol ventanilla
 */

console.log('🏢 Cargando módulo ventanilla...');

// Variable global para verificar que el usuario es ventanilla
let isVentanilla = false;

// Inicialización del módulo ventanilla
document.addEventListener('DOMContentLoaded', function() {
    if (window.dashboardUserRole === 'ventanilla') {
        isVentanilla = true;
        console.log('✅ Módulo ventanilla habilitado para:', window.dashboardUserName);
        initializeVentanillaModule();
    }
});

function initializeVentanillaModule() {
    console.log('🚀 Inicializando módulo ventanilla...');
    
    // Cargar estadísticas del dashboard al inicio
    loadDashboardStatsVentanilla();
}

// ==================== DASHBOARD STATS VENTANILLA ====================
async function loadDashboardStatsVentanilla() {
    console.log('📊 Cargando estadísticas de ventanilla...');
    
    try {
        const response = await fetch(`${window.location.origin}${window.location.pathname}?api=dashboard-stats`);
        const result = await response.json();
        
        if (result.success && result.stats) {
            updateDashboardStatsVentanilla(result.stats);
        } else {
            console.error('❌ Error al cargar estadísticas:', result.message);
        }
    } catch (error) {
        console.error('❌ Error al cargar estadísticas de ventanilla:', error);
    }
}

function updateDashboardStatsVentanilla(stats) {
    console.log('📈 Actualizando estadísticas de ventanilla:', stats);
    
    // Actualizar contadores específicos para ventanilla
    if (document.getElementById('total-actas')) {
        document.getElementById('total-actas').textContent = stats.total_tramites || 0;
    }
    
    if (document.getElementById('total-conductores')) {
        document.getElementById('total-conductores').textContent = stats.clientes_atendidos || 0;
    }
    
    if (document.getElementById('total-vehiculos')) {
        document.getElementById('total-vehiculos').textContent = stats.consultas_publicas || 0;
    }
    
    if (document.getElementById('total-notifications')) {
        document.getElementById('total-notifications').textContent = stats.cola_espera || 0;
    }
}

// ==================== FUNCIONES VENTANILLA ====================
function loadNuevaAtencion() {
    console.log('🆕 Cargando nueva atención...');
    alert('🚧 Nueva Atención - Funcionalidad en desarrollo');
}

function loadColaEspera() {
    console.log('⏳ Cargando cola de espera...');
    alert('🚧 Cola de Espera - Funcionalidad en desarrollo');
}

function loadConsultas() {
    console.log('❓ Cargando consultas...');
    alert('🚧 Consultas Públicas - Funcionalidad en desarrollo');
}

function loadTramites() {
    console.log('📁 Cargando trámites...');
    alert('🚧 Trámites - Funcionalidad en desarrollo');
}

// ==================== EXPORTAR FUNCIONES ====================
// Hacer las funciones disponibles globalmente para ventanilla
window.loadNuevaAtencion = loadNuevaAtencion;
window.loadColaEspera = loadColaEspera;
window.loadConsultas = loadConsultas;
window.loadTramites = loadTramites;
window.loadDashboardStatsVentanilla = loadDashboardStatsVentanilla;

console.log('✅ Módulo ventanilla cargado completamente');