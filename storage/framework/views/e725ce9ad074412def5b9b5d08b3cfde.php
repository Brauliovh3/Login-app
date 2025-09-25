<?php $__env->startSection('title', 'Panel Superadministrador'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Loader Global -->
    <div id="globalLoader" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.9); z-index: 9999; display: none;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
            <div class="spinner-border text-warning" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <div class="mt-2">Procesando...</div>
        </div>
    </div>

    <!-- Header Simple -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card" style="background: linear-gradient(135deg, #ff8c00, #e67e22); color: white;">
                <div class="card-body text-center">
                    <h2 class="mb-0">🛡️ Panel Superadministrador DRTC</h2>
                    <p class="mb-0">Sistema de Administración Total</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Navegación Complete -->
    <div class="row mb-4">
        <div class="col-12">
            <ul class="nav nav-pills nav-fill">
                <li class="nav-item">
                    <button class="nav-link active" data-section="dashboard">📊 Dashboard</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-section="users">👥 Usuarios</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-section="actas">📋 Actas</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-section="system">⚙️ Sistema</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-section="database">🗄️ Base de Datos</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-section="maintenance">🛠️ Mantenimiento</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-section="logs">📋 Logs</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-section="advanced">⚡ Avanzado</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link text-danger" data-section="danger">⚠️ Zona Peligrosa</button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Sección Dashboard -->
    <div id="section-dashboard" class="section-content">
        <div class="row">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5>Usuarios Totales</h5>
                        <h2 id="statUsuarios">-</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5>Actas Totales</h5>
                        <h2 id="statActas">-</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5>Actas Recientes</h5>
                        <h2 id="statRecent">-</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Información del Sistema</h5>
                    </div>
                    <div class="card-body">
                        <button id="btnAppInfo" class="btn btn-outline-primary">Ver Información de App</button>
                        <pre id="appInfo" class="mt-3" style="display: none; max-height: 300px; overflow-y: auto;"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección Usuarios -->
    <div id="section-users" class="section-content" style="display: none;">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h5>Gestión de Usuarios</h5>
                        <button id="btnLoadUsers" class="btn btn-primary btn-sm">🔄 Cargar Usuarios</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="usersTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Usuario</th>
                                        <th>Email</th>
                                        <th>Rol</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            Haz clic en "Cargar Usuarios" para ver la lista
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección Actas -->
    <div id="section-actas" class="section-content" style="display: none;">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h5>Gestión de Actas</h5>
                        <button id="btnLoadActas" class="btn btn-primary btn-sm">🔄 Cargar Actas</button>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h6>Total Actas</h6>
                                        <h4 id="actasTotal">-</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h6>Pendientes</h6>
                                        <h4 id="actasPendientes">-</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h6>Procesadas</h6>
                                        <h4 id="actasProcesadas">-</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <h6>Anuladas</h6>
                                        <h4 id="actasAnuladas">-</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="actasTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Número</th>
                                        <th>Placa</th>
                                        <th>Razón Social</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            Haz clic en "Cargar Actas" para ver la lista
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección Sistema -->
    <div id="section-system" class="section-content" style="display: none;">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>⚙️ Herramientas del Sistema Laravel</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Comandos de Optimización</h6>
                                <div class="d-grid gap-2 mb-3">
                                    <button class="btn btn-outline-primary system-action" data-action="optimize">🚀 Optimizar Sistema</button>
                                    <button class="btn btn-outline-warning system-action" data-action="migrate">🔄 Ejecutar Migraciones</button>
                                    <button class="btn btn-outline-info system-action" data-action="storage_link">🔗 Crear Storage Link</button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6>Resultado</h6>
                                <pre id="systemOutput" class="bg-light p-2 rounded" style="min-height: 200px;">Selecciona una acción para ver el resultado...</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección Base de Datos -->
    <div id="section-database" class="section-content" style="display: none;">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>🗄️ Mantenimiento de Base de Datos</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Operaciones de BD</h6>
                                <div class="d-grid gap-2 mb-3">
                                    <button class="btn btn-outline-primary db-action" data-action="vacuum">🗜️ Optimizar Tablas</button>
                                    <button class="btn btn-outline-warning db-action" data-action="cleanup_sessions">🧹 Limpiar Sesiones</button>
                                    <button class="btn btn-outline-info db-action" data-action="cleanup_notifications">🔔 Limpiar Notificaciones</button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6>Resultado</h6>
                                <pre id="databaseOutput" class="bg-light p-2 rounded" style="min-height: 200px;">Selecciona una operación para ver el resultado...</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección Mantenimiento -->
    <div id="section-maintenance" class="section-content" style="display: none;">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h5>🛠️ Comandos Artisan de Mantenimiento</h5>
                        <button id="btnRunAll" class="btn btn-success btn-sm">⚡ Ejecutar Todos</button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Comandos Individuales</h6>
                                <div class="d-grid gap-2 mb-3">
                                    <button class="btn btn-outline-primary run-cmd" data-cmd="cache:clear">🧹 Limpiar Caché</button>
                                    <button class="btn btn-outline-success run-cmd" data-cmd="config:cache">⚙️ Cachear Configuración</button>
                                    <button class="btn btn-outline-warning run-cmd" data-cmd="route:clear">🛣️ Limpiar Rutas</button>
                                    <button class="btn btn-outline-info run-cmd" data-cmd="view:clear">👁️ Limpiar Vistas</button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6>Log de Comandos</h6>
                                <pre id="cmdOutput" class="bg-dark text-light p-2 rounded" style="min-height: 300px; overflow-y: auto;">Listo para ejecutar comandos...</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección Logs del Sistema -->
    <div id="section-logs" class="section-content" style="display: none;">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h5>📋 Logs del Sistema</h5>
                        <div>
                            <select id="logType" class="form-select form-select-sm d-inline-block" style="width: auto;">
                                <option value="all">Todos</option>
                                <option value="error">Errores</option>
                                <option value="warning">Advertencias</option>
                                <option value="info">Información</option>
                            </select>
                            <button id="btnLoadLogs" class="btn btn-primary btn-sm">🔄 Cargar Logs</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <pre id="systemLogs" class="bg-dark text-light p-3 rounded" style="max-height: 500px; overflow-y: auto;">Selecciona un tipo de log y haz clic en "Cargar Logs"...</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección Avanzada -->
    <div id="section-advanced" class="section-content" style="display: none;">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>⚡ Herramientas Avanzadas</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2 mb-3">
                            <button id="btnSystemInfo" class="btn btn-outline-info">📊 Información Completa del Sistema</button>
                            <button id="btnHealthCheck" class="btn btn-outline-success">🏥 Verificación de Salud</button>
                            <button id="btnActivityReport" class="btn btn-outline-warning">📈 Reporte de Actividad</button>
                            <button id="btnCleanupTemp" class="btn btn-outline-danger">🧹 Limpiar Archivos Temporales</button>
                            <button id="btnResetAllIncrements" class="btn btn-outline-dark">🔄 Reiniciar Todos los AUTO_INCREMENT</button>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">
                        <h5>👥 Crear Usuario</h5>
                    </div>
                    <div class="card-body">
                        <form id="createUserForm">
                            <div class="mb-2">
                                <input type="text" class="form-control form-control-sm" id="newUsername" placeholder="Usuario" required>
                            </div>
                            <div class="mb-2">
                                <input type="text" class="form-control form-control-sm" id="newName" placeholder="Nombre completo" required>
                            </div>
                            <div class="mb-2">
                                <input type="email" class="form-control form-control-sm" id="newEmail" placeholder="Email" required>
                            </div>
                            <div class="mb-2">
                                <input type="password" class="form-control form-control-sm" id="newPassword" placeholder="Contraseña" required>
                            </div>
                            <div class="mb-2">
                                <select class="form-select form-select-sm" id="newRole" required>
                                    <option value="">Seleccionar rol</option>
                                    <option value="fiscalizador">Fiscalizador</option>
                                    <option value="administrador">Administrador</option>
                                    <option value="ventanilla">Ventanilla</option>
                                    <option value="superadmin">Super Admin</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success btn-sm w-100">➕ Crear Usuario</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>📊 Resultado de Operaciones</h5>
                    </div>
                    <div class="card-body">
                        <pre id="advancedOutput" class="bg-light p-2 rounded" style="min-height: 400px; overflow-y: auto;">Los resultados aparecerán aquí...</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección Zona Peligrosa -->
    <div id="section-danger" class="section-content" style="display: none;">
        <div class="row">
            <div class="col-12">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5>⚠️ Zona Peligrosa - Operaciones Críticas</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger">
                            <strong>ADVERTENCIA:</strong> Las operaciones de esta sección pueden afectar gravemente el sistema.
                        </div>

                        <h6>Reset de Actas</h6>
                        <div class="mb-3">
                            <button id="btnResetActasPreview" class="btn btn-warning btn-sm">👀 Vista Previa</button>
                            <button id="btnResetActasSuper" class="btn btn-danger btn-sm">💀 Reset Destructivo</button>
                        </div>
                        
                        <div class="mb-3">
                            <label for="superConfirm" class="form-label">Para reset destructivo, escribe "CONFIRMAR":</label>
                            <input type="text" id="superConfirm" class="form-control" placeholder="Escribe CONFIRMAR">
                        </div>
                        
                        <div id="resetActasFeedback"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
// Panel Superadmin - Versión Simplificada
document.addEventListener('DOMContentLoaded', function() {
    const token = '<?php echo e(csrf_token()); ?>';
    let currentSection = 'dashboard';

    // Funciones básicas de UI
    function showLoader() {
        document.getElementById('globalLoader').style.display = 'block';
    }

    function hideLoader() {
        document.getElementById('globalLoader').style.display = 'none';
    }

    function showMessage(message, type = 'info') {
        // Sin notificaciones - solo console log
        console.log(`${type.toUpperCase()}: ${message}`);
    }

    // Navegación entre secciones
    function showSection(sectionName) {
        // Ocultar todas las secciones
        document.querySelectorAll('.section-content').forEach(section => {
            section.style.display = 'none';
        });
        
        // Remover clase activa
        document.querySelectorAll('[data-section]').forEach(link => {
            link.classList.remove('active');
        });
        
        // Mostrar sección y activar botón
        const sectionEl = document.getElementById(`section-${sectionName}`);
        const btnEl = document.querySelector(`[data-section="${sectionName}"]`);
        
        if (sectionEl && btnEl) {
            sectionEl.style.display = 'block';
            btnEl.classList.add('active');
            currentSection = sectionName;
            
            // Cargar datos si es necesario
            if (sectionName === 'dashboard') {
                loadStats();
            }
        }
    }

    // Event listeners para navegación
    document.querySelectorAll('[data-section]').forEach(btn => {
        btn.addEventListener('click', function() {
            const section = this.dataset.section;
            showSection(section);
        });
    });

    // Función para cargar estadísticas
    async function loadStats() {
        try {
            showLoader();
            console.log('Cargando estadísticas...');
            
            const response = await fetch('<?php echo e(route('admin.super.stats')); ?>');
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            console.log('Data received:', data);
            
            hideLoader();
            
            if (data && data.ok && data.stats) {
                document.getElementById('statUsuarios').textContent = data.stats.total_usuarios || '0';
                document.getElementById('statActas').textContent = data.stats.total_actas || '0';
                document.getElementById('statRecent').textContent = (data.stats.actas_recientes || []).length || '0';
                showMessage('Estadísticas cargadas correctamente', 'success');
            } else {
                throw new Error('Datos no válidos recibidos del servidor');
            }
            
        } catch (error) {
            console.error('Error cargando estadísticas:', error);
            hideLoader();
            
            // Mostrar error en lugar de loader infinito
            document.getElementById('statUsuarios').textContent = 'Error';
            document.getElementById('statActas').textContent = 'Error';
            document.getElementById('statRecent').textContent = 'Error';
            
            showMessage(`Error al cargar estadísticas: ${error.message}`, 'error');
        }
    }

    // Botón de información de app
    document.getElementById('btnAppInfo')?.addEventListener('click', async function() {
        try {
            showLoader();
            const response = await fetch('<?php echo e(route('admin.super.app-info')); ?>');
            const data = await response.json();
            hideLoader();
            
            const pre = document.getElementById('appInfo');
            pre.textContent = JSON.stringify(data.info || data, null, 2);
            pre.style.display = 'block';
            
            showMessage('Información del sistema cargada', 'success');
        } catch (error) {
            hideLoader();
            showMessage(`Error: ${error.message}`, 'error');
        }
    });

    // Cargar usuarios
    document.getElementById('btnLoadUsers')?.addEventListener('click', async function() {
        try {
            showLoader();
            const response = await fetch('<?php echo e(route('admin.super.users')); ?>');
            const data = await response.json();
            hideLoader();
            
            if (data.ok && data.users) {
                const tbody = document.querySelector('#usersTable tbody');
                tbody.innerHTML = '';
                
                data.users.forEach(user => {
                    const row = `
                        <tr>
                            <td>${user.id}</td>
                            <td>${user.username}</td>
                            <td>${user.email || '-'}</td>
                            <td><span class="badge bg-primary">${user.role}</span></td>
                            <td><span class="badge bg-${user.status === 'active' ? 'success' : 'warning'}">${user.status}</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-success" onclick="alert('Aprobar usuario ${user.id}')">✅</button>
                                <button class="btn btn-sm btn-outline-warning" onclick="alert('Toggle usuario ${user.id}')">🔄</button>
                            </td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', row);
                });
                
                showMessage(`${data.users.length} usuarios cargados`, 'success');
            }
        } catch (error) {
            hideLoader();
            showMessage(`Error cargando usuarios: ${error.message}`, 'error');
        }
    });

    // Cargar actas
    document.getElementById('btnLoadActas')?.addEventListener('click', async function() {
        try {
            showLoader();
            const response = await fetch('<?php echo e(route('admin.super.actas')); ?>');
            const data = await response.json();
            hideLoader();
            
            if (data.ok) {
                // Actualizar estadísticas
                document.getElementById('actasTotal').textContent = data.stats.total || '0';
                document.getElementById('actasPendientes').textContent = data.stats.pendientes || '0';
                document.getElementById('actasProcesadas').textContent = data.stats.procesadas || '0';
                document.getElementById('actasAnuladas').textContent = data.stats.anuladas || '0';
                
                // Actualizar tabla
                const tbody = document.querySelector('#actasTable tbody');
                tbody.innerHTML = '';
                
                if (data.actas && data.actas.length > 0) {
                    data.actas.forEach(acta => {
                        const row = `
                            <tr>
                                <td>${acta.id}</td>
                                <td>${acta.numero_acta || '-'}</td>
                                <td>${acta.placa || '-'}</td>
                                <td>${acta.razon_social || '-'}</td>
                                <td><span class="badge bg-${acta.estado === 'pendiente' ? 'warning' : acta.estado === 'procesada' ? 'success' : 'danger'}">${acta.estado}</span></td>
                                <td>${new Date(acta.created_at).toLocaleDateString('es-ES')}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-danger" onclick="alert('Eliminar acta ${acta.id}')">🗑️</button>
                                </td>
                            </tr>
                        `;
                        tbody.insertAdjacentHTML('beforeend', row);
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No hay actas disponibles</td></tr>';
                }
                
                showMessage(`${data.actas.length} actas cargadas`, 'success');
            }
        } catch (error) {
            hideLoader();
            showMessage(`Error cargando actas: ${error.message}`, 'error');
        }
    });

    // Herramientas del Sistema
    document.querySelectorAll('.system-action')?.forEach(btn => {
        btn.addEventListener('click', async function() {
            const action = this.dataset.action;
            const actionNames = {
                optimize: 'Optimizar Sistema',
                migrate: 'Ejecutar Migraciones',
                storage_link: 'Crear Storage Link'
            };
            
            if (confirm(`¿Ejecutar: ${actionNames[action]}?`)) {
                try {
                    showLoader();
                    const response = await fetch('<?php echo e(route('admin.super.system')); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({ action })
                    });
                    
                    const data = await response.json();
                    hideLoader();
                    
                    document.getElementById('systemOutput').textContent = JSON.stringify(data, null, 2);
                    showMessage(`${actionNames[action]} completado`, 'success');
                } catch (error) {
                    hideLoader();
                    document.getElementById('systemOutput').textContent = `Error: ${error.message}`;
                    showMessage(`Error en ${actionNames[action]}`, 'error');
                }
            }
        });
    });

    // Operaciones de Base de Datos
    document.querySelectorAll('.db-action')?.forEach(btn => {
        btn.addEventListener('click', async function() {
            const action = this.dataset.action;
            const actionNames = {
                vacuum: 'Optimizar Tablas',
                cleanup_sessions: 'Limpiar Sesiones',
                cleanup_notifications: 'Limpiar Notificaciones'
            };
            
            if (confirm(`¿Ejecutar: ${actionNames[action]}?`)) {
                try {
                    showLoader();
                    const response = await fetch('<?php echo e(route('admin.super.database')); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({ action })
                    });
                    
                    const data = await response.json();
                    hideLoader();
                    
                    document.getElementById('databaseOutput').textContent = JSON.stringify(data, null, 2);
                    showMessage(`${actionNames[action]} completado`, 'success');
                } catch (error) {
                    hideLoader();
                    document.getElementById('databaseOutput').textContent = `Error: ${error.message}`;
                    showMessage(`Error en ${actionNames[action]}`, 'error');
                }
            }
        });
    });

    // Comandos Artisan individuales
    document.querySelectorAll('.run-cmd')?.forEach(btn => {
        btn.addEventListener('click', async function() {
            const cmd = this.dataset.cmd;
            
            if (confirm(`¿Ejecutar comando: ${cmd}?`)) {
                try {
                    showLoader();
                    const response = await fetch('<?php echo e(route('admin.super.run-command')); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({ command: cmd })
                    });
                    
                    const data = await response.json();
                    hideLoader();
                    
                    const output = document.getElementById('cmdOutput');
                    output.textContent += `\n=== COMANDO: ${cmd} ===\n` + JSON.stringify(data, null, 2) + '\n';
                    output.scrollTop = output.scrollHeight;
                    
                    showMessage(`Comando ${cmd} ejecutado`, 'success');
                } catch (error) {
                    hideLoader();
                    const output = document.getElementById('cmdOutput');
                    output.textContent += `\n=== ERROR EN ${cmd} ===\n` + error.message + '\n';
                    showMessage(`Error ejecutando ${cmd}`, 'error');
                }
            }
        });
    });

    // Cargar logs del sistema
    document.getElementById('btnLoadLogs')?.addEventListener('click', async function() {
        const logType = document.getElementById('logType').value;
        
        try {
            showLoader();
            const response = await fetch(`<?php echo e(route('admin.super.logs')); ?>?type=${logType}&limit=200`);
            const data = await response.json();
            hideLoader();
            
            if (data.ok && data.logs) {
                const logsOutput = document.getElementById('systemLogs');
                if (data.logs.logs && data.logs.logs.length > 0) {
                    logsOutput.textContent = data.logs.logs.join('\n');
                } else {
                    logsOutput.textContent = 'No se encontraron logs del tipo seleccionado.';
                }
                showMessage(`${data.logs.shown || 0} logs cargados`, 'success');
            } else {
                document.getElementById('systemLogs').textContent = 'Error cargando logs: ' + (data.logs?.error || 'Error desconocido');
            }
        } catch (error) {
            hideLoader();
            document.getElementById('systemLogs').textContent = `Error: ${error.message}`;
            showMessage('Error cargando logs', 'error');
        }
    });

    // Información completa del sistema
    document.getElementById('btnSystemInfo')?.addEventListener('click', async function() {
        try {
            showLoader();
            const response = await fetch('<?php echo e(route('admin.super.full-system-info')); ?>');
            const data = await response.json();
            hideLoader();
            
            document.getElementById('advancedOutput').textContent = JSON.stringify(data.info || data, null, 2);
            showMessage('Información del sistema cargada', 'success');
        } catch (error) {
            hideLoader();
            document.getElementById('advancedOutput').textContent = `Error: ${error.message}`;
            showMessage('Error obteniendo información del sistema', 'error');
        }
    });

    // Verificación de salud
    document.getElementById('btnHealthCheck')?.addEventListener('click', async function() {
        try {
            showLoader();
            const response = await fetch('<?php echo e(route('admin.super.health-check')); ?>');
            const data = await response.json();
            hideLoader();
            
            document.getElementById('advancedOutput').textContent = JSON.stringify(data.health || data, null, 2);
            showMessage('Verificación de salud completada', 'success');
        } catch (error) {
            hideLoader();
            document.getElementById('advancedOutput').textContent = `Error: ${error.message}`;
            showMessage('Error en verificación de salud', 'error');
        }
    });

    // Reporte de actividad
    document.getElementById('btnActivityReport')?.addEventListener('click', async function() {
        try {
            showLoader();
            const response = await fetch('<?php echo e(route('admin.super.activity-report')); ?>');
            const data = await response.json();
            hideLoader();
            
            document.getElementById('advancedOutput').textContent = JSON.stringify(data.report || data, null, 2);
            showMessage('Reporte de actividad generado', 'success');
        } catch (error) {
            hideLoader();
            document.getElementById('advancedOutput').textContent = `Error: ${error.message}`;
            showMessage('Error generando reporte', 'error');
        }
    });

    // Limpiar archivos temporales
    document.getElementById('btnCleanupTemp')?.addEventListener('click', async function() {
        if (confirm('¿Limpiar archivos temporales del sistema?')) {
            try {
                showLoader();
                const response = await fetch('<?php echo e(route('admin.super.cleanup-temp')); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    }
                });
                
                const data = await response.json();
                hideLoader();
                
                document.getElementById('advancedOutput').textContent = JSON.stringify(data.results || data, null, 2);
                showMessage('Limpieza de archivos temporales completada', 'success');
            } catch (error) {
                hideLoader();
                document.getElementById('advancedOutput').textContent = `Error: ${error.message}`;
                showMessage('Error en limpieza', 'error');
            }
        }
    });

    // Reiniciar todos los AUTO_INCREMENT
    document.getElementById('btnResetAllIncrements')?.addEventListener('click', function() {
        const force = confirm('¿Reiniciar AUTO_INCREMENT de todas las tablas?\n\nPresiona OK para operación segura (solo tablas vacías)\nPresiona Cancelar y luego OK en el siguiente diálogo para FORZAR (peligroso)');
        
        if (force || confirm('¿FORZAR reinicio de AUTO_INCREMENT en todas las tablas? (PELIGROSO)')) {
            performResetAllIncrements(!force); // invertir lógica: force=true cuando se confirma el segundo diálogo
        }
    });

    async function performResetAllIncrements(force) {
        try {
            showLoader();
            const response = await fetch('<?php echo e(route('admin.super.reset-all-increments')); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ force: force })
            });
            
            const data = await response.json();
            hideLoader();
            
            document.getElementById('advancedOutput').textContent = JSON.stringify(data.results || data, null, 2);
            showMessage('Reset de AUTO_INCREMENT completado', force ? 'warning' : 'success');
        } catch (error) {
            hideLoader();
            document.getElementById('advancedOutput').textContent = `Error: ${error.message}`;
            showMessage('Error en reset de AUTO_INCREMENT', 'error');
        }
    }

    // Crear usuario
    document.getElementById('createUserForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const userData = {
            username: document.getElementById('newUsername').value,
            name: document.getElementById('newName').value,
            email: document.getElementById('newEmail').value,
            password: document.getElementById('newPassword').value,
            role: document.getElementById('newRole').value
        };
        
        try {
            showLoader();
            const response = await fetch('<?php echo e(route('admin.super.create-user')); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify(userData)
            });
            
            const data = await response.json();
            hideLoader();
            
            if (data.ok) {
                document.getElementById('advancedOutput').textContent = JSON.stringify(data, null, 2);
                document.getElementById('createUserForm').reset();
                showMessage('Usuario creado exitosamente', 'success');
            } else {
                document.getElementById('advancedOutput').textContent = `Error creando usuario: ${JSON.stringify(data, null, 2)}`;
                showMessage('Error creando usuario', 'error');
            }
        } catch (error) {
            hideLoader();
            document.getElementById('advancedOutput').textContent = `Error: ${error.message}`;
            showMessage('Error en creación de usuario', 'error');
        }
    });

    // Ejecutar todos los comandos en secuencia
    document.getElementById('btnRunAll')?.addEventListener('click', async function() {
        const commands = ['cache:clear', 'config:cache', 'route:clear', 'view:clear'];
        
        if (confirm('¿Ejecutar todos los comandos de mantenimiento en secuencia?')) {
            const output = document.getElementById('cmdOutput');
            output.textContent = '=== INICIANDO SECUENCIA DE COMANDOS ===\n';
            
            for (const cmd of commands) {
                try {
                    showLoader();
                    const response = await fetch('<?php echo e(route('admin.super.run-command')); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({ command: cmd })
                    });
                    
                    const data = await response.json();
                    output.textContent += `\n=== ${cmd.toUpperCase()} ===\n` + JSON.stringify(data, null, 2) + '\n';
                    output.scrollTop = output.scrollHeight;
                    
                    // Pequeña pausa entre comandos
                    await new Promise(resolve => setTimeout(resolve, 500));
                } catch (error) {
                    output.textContent += `\n=== ERROR EN ${cmd.toUpperCase()} ===\n` + error.message + '\n';
                }
            }
            
            hideLoader();
            output.textContent += '\n=== SECUENCIA COMPLETADA ===\n';
            showMessage('Secuencia de comandos completada', 'success');
        }
    });

    // Reset de actas - Vista previa
    document.getElementById('btnResetActasPreview')?.addEventListener('click', async function() {
        try {
            showLoader();
            const response = await fetch('<?php echo e(route('admin.super.reset-actas')); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ force: false })
            });
            
            const data = await response.json();
            hideLoader();
            
            document.getElementById('resetActasFeedback').innerHTML = `
                <div class="alert alert-info">
                    <strong>Vista previa:</strong> ${data.message || 'Operación completada'}
                </div>
            `;
        } catch (error) {
            hideLoader();
            showMessage(`Error en vista previa: ${error.message}`, 'error');
        }
    });

    // Reset de actas - Destructivo
    document.getElementById('btnResetActasSuper')?.addEventListener('click', function() {
        const confirmation = document.getElementById('superConfirm').value.trim();
        const isDestructive = confirmation === 'CONFIRMAR';
        
        if (!isDestructive && confirmation) {
            showMessage('Debes escribir exactamente "CONFIRMAR" para operaciones destructivas', 'error');
            return;
        }
        
        const message = isDestructive ? 
            '¿Estás seguro de ELIMINAR TODAS las actas?' : 
            '¿Intentar reset no destructivo?';
            
        if (confirm(message)) {
            performReset(isDestructive);
        }
    });

    async function performReset(force) {
        try {
            showLoader();
            const response = await fetch('<?php echo e(route('admin.super.reset-actas')); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ force: force })
            });
            
            const data = await response.json();
            hideLoader();
            
            const alertType = data.ok ? 'success' : 'danger';
            document.getElementById('resetActasFeedback').innerHTML = `
                <div class="alert alert-${alertType}">
                    <strong>${force ? 'Reset destructivo' : 'Reset'}:</strong> ${data.message || 'Operación completada'}
                </div>
            `;
            
            if (currentSection === 'dashboard') {
                loadStats(); // Recargar estadísticas
            }
            
        } catch (error) {
            hideLoader();
            showMessage(`Error en reset: ${error.message}`, 'error');
        }
    }

    // Inicialización
    console.log('Superadmin panel initialized');
    
    // Cargar estadísticas iniciales con timeout
    setTimeout(() => {
        if (currentSection === 'dashboard') {
            loadStats();
        }
    }, 1000);
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Login-app\resources\views\administrador\super\index.blade.php ENDPATH**/ ?>