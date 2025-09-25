<div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-warning">
            <i class="fas fa-shield-alt"></i> 
            <strong>Panel Super Admin</strong> - Vista simplificada. Para acceso completo ve a 
            <a href="<?php echo e(route('admin.super.index')); ?>" class="alert-link" target="_blank">Panel Completo</a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Estadísticas del sistema -->
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h5>Usuarios Totales</h5>
                <h2 id="super-total-usuarios">-</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h5>Actas Totales</h5>
                <h2 id="super-total-actas">-</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h5>Sesiones Activas</h5>
                <h2 id="super-sesiones">-</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h5>Sistema</h5>
                <h2 id="super-status">OK</h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-danger">🛠️ Herramientas Rápidas</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary" onclick="clearCache()">
                        <i class="fas fa-broom"></i> Limpiar Caché
                    </button>
                    <button class="btn btn-outline-warning" onclick="resetAutoIncrement()">
                        <i class="fas fa-redo"></i> Reset AUTO_INCREMENT Actas
                    </button>
                    <button class="btn btn-outline-info" onclick="viewSystemInfo()">
                        <i class="fas fa-info-circle"></i> Información del Sistema
                    </button>
                    <a href="<?php echo e(route('admin.super.index')); ?>" class="btn btn-danger" target="_blank">
                        <i class="fas fa-external-link-alt"></i> Panel Completo
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-danger">📊 Estado del Sistema</h6>
            </div>
            <div class="card-body">
                <div id="system-status">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2">Verificando estado del sistema...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-danger">⚡ Acciones Rápidas</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <button class="btn btn-outline-success w-100" onclick="showSection('admin-usuarios')">
                            <i class="fas fa-users"></i><br>
                            <small>Gestionar Usuarios</small>
                        </button>
                    </div>
                    <div class="col-md-4 mb-3">
                        <button class="btn btn-outline-primary w-100" onclick="viewLogs()">
                            <i class="fas fa-file-alt"></i><br>
                            <small>Ver Logs</small>
                        </button>
                    </div>
                    <div class="col-md-4 mb-3">
                        <button class="btn btn-outline-warning w-100" onclick="databaseMaintenance()">
                            <i class="fas fa-database"></i><br>
                            <small>Mantenimiento DB</small>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para resultados -->
<div class="modal fade" id="superAdminModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="superAdminModalTitle">Resultado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <pre id="superAdminModalContent" class="bg-light p-3 rounded" style="max-height: 400px; overflow-y: auto;"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
window.init_superadmin = function() {
    loadSuperAdminStats();
    checkSystemHealth();
};

function loadSuperAdminStats() {
    fetch('/admin/super/stats')
        .then(response => response.json())
        .then(data => {
            if (data.ok && data.stats) {
                document.getElementById('super-total-usuarios').textContent = data.stats.total_usuarios || '0';
                document.getElementById('super-total-actas').textContent = data.stats.total_actas || '0';
                document.getElementById('super-sesiones').textContent = Math.floor(Math.random() * 10) + 1; // Placeholder
            }
        })
        .catch(error => {
            console.error('Error loading super admin stats:', error);
        });
}

function checkSystemHealth() {
    fetch('/admin/super/health-check')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('system-status');
            if (data.ok && data.health) {
                let html = '';
                Object.entries(data.health).forEach(([key, value]) => {
                    const statusClass = value.status === 'ok' ? 'success' : 
                                      value.status === 'warning' ? 'warning' : 'danger';
                    html += `
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>${key.replace('_', ' ').toUpperCase()}:</span>
                            <span class="badge bg-${statusClass}">${value.message}</span>
                        </div>
                    `;
                });
                container.innerHTML = html;
            } else {
                container.innerHTML = '<p class="text-muted">Error verificando estado del sistema</p>';
            }
        })
        .catch(error => {
            document.getElementById('system-status').innerHTML = '<p class="text-danger">Error de conexión</p>';
        });
}

function clearCache() {
    if (confirm('¿Limpiar caché del sistema?')) {
        fetch('/admin/super/cache-clear', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            showModal('Limpiar Caché', JSON.stringify(data, null, 2));
            showNotification(data.message || 'Caché limpiado', 'success');
        })
        .catch(error => {
            showNotification('Error limpiando caché', 'error');
        });
    }
}

function resetAutoIncrement() {
    if (confirm('¿Reiniciar AUTO_INCREMENT de la tabla actas?\n\nEsto ajustará el contador al siguiente ID disponible.')) {
        fetch('/admin/super/reset-auto-increment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            showModal('Reset AUTO_INCREMENT', JSON.stringify(data, null, 2));
            showNotification(data.message || 'AUTO_INCREMENT reiniciado', 'success');
        })
        .catch(error => {
            showNotification('Error reiniciando AUTO_INCREMENT', 'error');
        });
    }
}

function viewSystemInfo() {
    fetch('/admin/super/app-info')
        .then(response => response.json())
        .then(data => {
            showModal('Información del Sistema', JSON.stringify(data.info || data, null, 2));
        })
        .catch(error => {
            showModal('Error', 'Error obteniendo información del sistema');
        });
}

function viewLogs() {
    fetch('/admin/super/logs?type=all&limit=50')
        .then(response => response.json())
        .then(data => {
            if (data.ok && data.logs && data.logs.logs) {
                showModal('Logs del Sistema (últimos 50)', data.logs.logs.join('\n'));
            } else {
                showModal('Logs', 'No se encontraron logs o error cargando');
            }
        })
        .catch(error => {
            showModal('Error', 'Error cargando logs del sistema');
        });
}

function databaseMaintenance() {
    if (confirm('¿Ejecutar optimización de base de datos?')) {
        fetch('/admin/super/database', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ action: 'vacuum' })
        })
        .then(response => response.json())
        .then(data => {
            showModal('Mantenimiento de Base de Datos', JSON.stringify(data, null, 2));
            showNotification(data.message || 'Mantenimiento completado', 'success');
        })
        .catch(error => {
            showNotification('Error en mantenimiento de BD', 'error');
        });
    }
}

function showModal(title, content) {
    document.getElementById('superAdminModalTitle').textContent = title;
    document.getElementById('superAdminModalContent').textContent = content;
    new bootstrap.Modal(document.getElementById('superAdminModal')).show();
}
</script><?php /**PATH C:\xampp\htdocs\Login-app\resources\views\dashboard\sections\superadmin.blade.php ENDPATH**/ ?>