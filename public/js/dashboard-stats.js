/**
 * ================================
 * DASHBOARD STATS & CONTENT
 * Sistema de Gestión - JavaScript
 * ================================
 */

// ================================
// FUNCIONES DE ESTADÍSTICAS
// ================================

function loadDashboardStats() {
    console.log('📊 Cargando estadísticas del dashboard...');
    
    fetch('dashboard.php?api=dashboard-stats')
        .then(response => response.json())
        .then(data => {
            console.log('Estadísticas recibidas:', data);
            if (data.success && data.stats) {
                renderDashboardStats(data.stats);
            } else {
                console.error('Error al cargar estadísticas:', data.message);
                showStatsError();
            }
        })
        .catch(error => {
            console.error('Error cargando estadísticas:', error);
            showStatsError();
        });
}

function renderDashboardStats(stats) {
    const statsContainer = document.getElementById('dashboardStats');
    if (!statsContainer) return;
    
    // Limpiar contenido existente
    statsContainer.innerHTML = '';
    
    // Generar cards según el rol del usuario
    const cards = generateStatsCards(stats);
    
    cards.forEach(card => {
        const cardElement = createStatsCard(card);
        statsContainer.appendChild(cardElement);
    });
    
    console.log('✅ Estadísticas renderizadas');
}

function generateStatsCards(stats) {
    const cards = [];
    
    // Cards específicos según el rol
    if (userRole === 'administrador' || userRole === 'superadmin') {
        cards.push(
            { title: 'Total Usuarios', value: stats.total_usuarios || 0, icon: 'fas fa-users', color: 'primary', trend: '+12%' },
            { title: 'Usuarios Activos', value: stats.usuarios_activos || 0, icon: 'fas fa-user-check', color: 'success', trend: '+8%' },
            { title: 'Usuarios Pendientes', value: stats.usuarios_pendientes || 0, icon: 'fas fa-user-clock', color: 'warning', trend: '-2%' },
            { title: 'Total Conductores', value: stats.total_conductores || 0, icon: 'fas fa-id-card', color: 'info', trend: '+5%' }
        );
    } else if (userRole === 'fiscalizador' || userRole === 'inspector') {
        cards.push(
            { title: 'Total Infracciones', value: stats.total_infracciones || 0, icon: 'fas fa-file-alt', color: 'primary', trend: '+15%' },
            { title: 'Infracciones Procesadas', value: stats.infracciones_procesadas || 0, icon: 'fas fa-check-circle', color: 'success', trend: '+20%' },
            { title: 'Infracciones Pendientes', value: stats.infracciones_pendientes || 0, icon: 'fas fa-clock', color: 'warning', trend: '-5%' },
            { title: 'Total Multas (S/)', value: formatCurrency(stats.total_multas || 0), icon: 'fas fa-money-bill', color: 'info', trend: '+18%' }
        );
    } else if (userRole === 'ventanilla') {
        cards.push(
            { title: 'Atenciones Hoy', value: stats.atenciones_hoy || 0, icon: 'fas fa-users', color: 'primary', trend: '+10%' },
            { title: 'Cola de Espera', value: stats.cola_espera || 0, icon: 'fas fa-hourglass-half', color: 'warning', trend: 'Real time' },
            { title: 'Trámites Completados', value: stats.tramites_completados || 0, icon: 'fas fa-check-circle', color: 'success', trend: '+25%' },
            { title: 'Tiempo Promedio (min)', value: stats.tiempo_promedio || 0, icon: 'fas fa-clock', color: 'info', trend: '-3 min' }
        );
    } else {
        // Cards genéricos para otros roles
        cards.push(
            { title: 'Notificaciones', value: stats.notificaciones || 0, icon: 'fas fa-bell', color: 'primary', trend: 'Nuevas' },
            { title: 'Tareas Pendientes', value: stats.tareas_pendientes || 0, icon: 'fas fa-tasks', color: 'warning', trend: 'Hoy' },
            { title: 'Documentos', value: stats.documentos || 0, icon: 'fas fa-file', color: 'info', trend: 'Totales' },
            { title: 'Estado Sistema', value: 'Activo', icon: 'fas fa-server', color: 'success', trend: '99.9%' }
        );
    }
    
    return cards;
}

function createStatsCard(card) {
    const cardDiv = document.createElement('div');
    cardDiv.className = 'col-lg-3 col-md-6 mb-4';
    
    cardDiv.innerHTML = `
        <div class="card border-left-${card.color} shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-${card.color} text-uppercase mb-1">
                            ${card.title}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            ${card.value}
                        </div>
                        ${card.trend ? `<div class="text-xs text-muted">${card.trend}</div>` : ''}
                    </div>
                    <div class="col-auto">
                        <i class="${card.icon} fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    return cardDiv;
}

function showStatsError() {
    const statsContainer = document.getElementById('dashboardStats');
    if (!statsContainer) return;
    
    statsContainer.innerHTML = `
        <div class="col-12">
            <div class="alert alert-warning" role="alert">
                <i class="fas fa-exclamation-triangle"></i>
                No se pudieron cargar las estadísticas en este momento.
                <button class="btn btn-sm btn-outline-warning ml-2" onclick="loadDashboardStats()">
                    <i class="fas fa-sync-alt"></i> Reintentar
                </button>
            </div>
        </div>
    `;
}

// ================================
// FUNCIONES AUXILIARES
// ================================

function formatCurrency(amount) {
    return new Intl.NumberFormat('es-PE', {
        style: 'currency',
        currency: 'PEN',
        minimumFractionDigits: 2
    }).format(amount);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-PE', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function showLoading(message = 'Cargando...') {
    return `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">${message}</span>
            </div>
            <p class="mt-2 text-muted">${message}</p>
        </div>
    `;
}

// Exponer funciones globalmente
window.loadDashboardStats = loadDashboardStats;

console.log('📊 Dashboard Stats JS cargado correctamente');