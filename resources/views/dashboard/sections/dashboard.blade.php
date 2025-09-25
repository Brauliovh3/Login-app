<div class="row">
    <!-- Estadísticas principales -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Actas Totales</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-actas">-</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Usuarios Activos</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-usuarios">-</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Actas Hoy</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="actas-hoy">-</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Pendientes</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="actas-pendientes">-</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Accesos rápidos según rol -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">🚀 Accesos Rápidos</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @if(auth()->user()->isFiscalizador() || auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                    <div class="col-md-4 mb-3">
                        <button class="btn btn-outline-primary w-100" onclick="showSection('fiscal-actas')">
                            <i class="fas fa-plus-circle"></i><br>
                            <small>Nueva Acta</small>
                        </button>
                    </div>
                    <div class="col-md-4 mb-3">
                        <button class="btn btn-outline-info w-100" onclick="showSection('fiscal-consultar')">
                            <i class="fas fa-search"></i><br>
                            <small>Consultar Actas</small>
                        </button>
                    </div>
                    @endif
                    
                    @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                    <div class="col-md-4 mb-3">
                        <button class="btn btn-outline-success w-100" onclick="showSection('admin-usuarios')">
                            <i class="fas fa-users-cog"></i><br>
                            <small>Gestionar Usuarios</small>
                        </button>
                    </div>
                    <div class="col-md-4 mb-3">
                        <button class="btn btn-outline-warning w-100" onclick="showSection('admin-aprobar')">
                            <i class="fas fa-user-check"></i><br>
                            <small>Aprobar Usuarios</small>
                        </button>
                    </div>
                    @endif
                    
                    @if(auth()->user()->isVentanilla() || auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                    <div class="col-md-4 mb-3">
                        <button class="btn btn-outline-secondary w-100" onclick="showSection('ventanilla-atencion')">
                            <i class="fas fa-ticket-alt"></i><br>
                            <small>Nueva Atención</small>
                        </button>
                    </div>
                    @endif
                    
                    @if(auth()->user()->role === 'inspector' || auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                    <div class="col-md-4 mb-3">
                        <button class="btn btn-outline-dark w-100" onclick="showSection('inspector-acta')">
                            <i class="fas fa-clipboard-check"></i><br>
                            <small>Generar Acta</small>
                        </button>
                    </div>
                    @endif
                    
                    @if(auth()->user()->isSuperAdmin())
                    <div class="col-md-4 mb-3">
                        <button class="btn btn-outline-danger w-100" onclick="showSection('superadmin')">
                            <i class="fas fa-shield-alt"></i><br>
                            <small>Super Admin</small>
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Notificaciones y actividad reciente -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">📋 Actividad Reciente</h6>
            </div>
            <div class="card-body">
                <div id="recent-activity">
                    <div class="text-center text-muted">
                        <div class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2 mb-0">Cargando actividad...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Gráfico de actividad (placeholder) -->
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">📊 Resumen de Actividad</h6>
            </div>
            <div class="card-body">
                <div class="text-center text-muted py-4">
                    <i class="fas fa-chart-line fa-3x mb-3"></i>
                    <p>Gráficos de actividad disponibles próximamente</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Inicializar dashboard
window.init_dashboard = function() {
    loadDashboardStats();
    loadRecentActivity();
};

function loadDashboardStats() {
    fetch('/api/dashboard-stats')
        .then(response => response.json())
        .then(data => {
            if (data.ok) {
                document.getElementById('total-actas').textContent = data.stats.total_actas || '0';
                document.getElementById('total-usuarios').textContent = data.stats.total_usuarios || '0';
                document.getElementById('actas-hoy').textContent = data.stats.actas_hoy || '0';
                document.getElementById('actas-pendientes').textContent = data.stats.actas_pendientes || '0';
            }
        })
        .catch(error => {
            console.error('Error loading dashboard stats:', error);
        });
}

function loadRecentActivity() {
    fetch('/api/recent-activity')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('recent-activity');
            if (data.ok && data.activity && data.activity.length > 0) {
                container.innerHTML = data.activity.map(item => `
                    <div class="d-flex align-items-center mb-2">
                        <div class="flex-shrink-0">
                            <i class="${item.icon} text-${item.type}"></i>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <small class="d-block">${item.description}</small>
                            <small class="text-muted">${item.time}</small>
                        </div>
                    </div>
                `).join('');
            } else {
                container.innerHTML = `
                    <div class="text-center text-muted">
                        <i class="fas fa-info-circle"></i>
                        <p class="mb-0">No hay actividad reciente</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            document.getElementById('recent-activity').innerHTML = `
                <div class="text-center text-muted">
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                    <p class="mb-0">Error cargando actividad</p>
                </div>
            `;
        });
}
</script>