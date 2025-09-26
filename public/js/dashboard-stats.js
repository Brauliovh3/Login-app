/**
 * Funciones para manejo de estadísticas del dashboard
 * Archivo: dashboard-stats.js
 */

/**
 * Cargar estadísticas del dashboard
 */
function loadDashboardStats() {
    console.log('📊 Cargando estadísticas...');
    
    fetch('dashboard.php?api=dashboard-stats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateStatsDisplay(data.stats);
                console.log('✅ Estadísticas cargadas');
            } else {
                console.error('❌ Error al cargar estadísticas:', data.message);
            }
        })
        .catch(error => {
            console.error('❌ Error de conexión:', error);
        });
}

/**
 * Actualizar display de estadísticas
 */
function updateStatsDisplay(stats) {
    // Mapear estadísticas según el rol del usuario
    const statsMapping = {
        'total_usuarios': { selector: '#totalUsuarios', icon: 'fas fa-users' },
        'usuarios_activos': { selector: '#usuariosActivos', icon: 'fas fa-user-check' },
        'usuarios_pendientes': { selector: '#usuariosPendientes', icon: 'fas fa-user-clock' },
        'total_conductores': { selector: '#totalConductores', icon: 'fas fa-id-card' },
        'total_vehiculos': { selector: '#totalVehiculos', icon: 'fas fa-car' },
        'total_infracciones': { selector: '#totalInfracciones', icon: 'fas fa-file-alt' },
        'infracciones_procesadas': { selector: '#infraccionesProcesadas', icon: 'fas fa-check-circle' },
        'infracciones_pendientes': { selector: '#infraccionesPendientes', icon: 'fas fa-clock' },
        'total_multas': { selector: '#totalMultas', icon: 'fas fa-dollar-sign' },
        'atenciones_hoy': { selector: '#atencionesHoy', icon: 'fas fa-calendar-day' },
        'cola_espera': { selector: '#colaEspera', icon: 'fas fa-users-clock' },
        'tramites_completados': { selector: '#tramitesCompletados', icon: 'fas fa-check-double' },
        'tiempo_promedio': { selector: '#tiempoPromedio', icon: 'fas fa-stopwatch' }
    };
    
    // Actualizar cada estadística
    Object.keys(stats).forEach(key => {
        const mapping = statsMapping[key];
        if (mapping) {
            const element = document.querySelector(mapping.selector);
            if (element) {
                const numberElement = element.querySelector('.stats-number');
                if (numberElement) {
                    const value = key === 'total_multas' ? `S/ ${formatMoney(stats[key])}` :
                                key === 'tiempo_promedio' ? `${stats[key]} min` :
                                formatNumber(stats[key]);
                    numberElement.textContent = value;
                }
            }
        }
    });
}

/**
 * Cargar contenido principal del dashboard
 */
function loadDashboardContent() {
    console.log('📊 Cargando contenido del dashboard...');
    
    const contentContainer = document.getElementById('contentContainer');
    if (!contentContainer) return;
    
    // Mostrar las estadísticas del dashboard
    const dashboardSection = document.getElementById('dashboard-section');
    if (dashboardSection) {
        dashboardSection.style.display = 'block';
        dashboardSection.classList.add('active');
    }
    
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
        
        <div class="row">
            <div class="col-12">
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <strong>Sistema Operativo</strong> - Todos los servicios están funcionando correctamente.
                </div>
            </div>
        </div>
    `;
    
    // Recargar las estadísticas
    loadDashboardStats();
}

/**
 * Formatear números
 */
function formatNumber(num) {
    return new Intl.NumberFormat('es-PE').format(num);
}

/**
 * Formatear moneda
 */
function formatMoney(amount) {
    return new Intl.NumberFormat('es-PE', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(amount);
}

/**
 * Formatear fechas
 */
function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES');
}

/**
 * Formatear fecha y hora
 */
function formatDateTime(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES') + ' ' + date.toLocaleTimeString('es-ES');
}

// Exponer funciones globalmente
window.loadDashboardStats = loadDashboardStats;
window.loadDashboardContent = loadDashboardContent;
window.updateStatsDisplay = updateStatsDisplay;
window.formatNumber = formatNumber;
window.formatMoney = formatMoney;
window.formatDate = formatDate;
window.formatDateTime = formatDateTime;