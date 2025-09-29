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

// ==================== EXPORTAR FUNCIONES ====================
// Hacer las funciones disponibles globalmente para el fiscalizador
window.loadDashboardStatsFiscalizador = loadDashboardStatsFiscalizador;

console.log('✅ Módulo fiscalizador cargado completamente');