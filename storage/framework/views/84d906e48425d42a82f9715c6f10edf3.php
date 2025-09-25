<?php $__env->startSection('title', 'Panel Superadministrador'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid p-0">
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
    <div class="row mb-3">
        <div class="col-12">
            <div class="card" style="background: linear-gradient(135deg, #ff8c00, #e67e22); color: white;">
                <div class="card-body text-center py-3">
                    <h2 class="mb-1 h4">🛡️ Panel Superadministrador DRTC</h2>
                    <p class="mb-0 small">Sistema de Administración Total</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Navegación Complete -->
    <div class="row mb-3">
        <div class="col-12">
            <ul class="nav nav-pills nav-fill flex-column flex-md-row">
                <li class="nav-item mb-1 mb-md-0">
                    <button class="nav-link active w-100" data-section="dashboard">📊 Dashboard</button>
                </li>
                <li class="nav-item mb-1 mb-md-0">
                    <button class="nav-link w-100" data-section="users">👥 Usuarios</button>
                </li>
                <li class="nav-item mb-1 mb-md-0">
                    <button class="nav-link w-100" data-section="actas">📋 Actas</button>
                </li>
                <li class="nav-item mb-1 mb-md-0">
                    <button class="nav-link w-100" data-section="system">⚙️ Sistema</button>
                </li>
                <li class="nav-item mb-1 mb-md-0">
                    <button class="nav-link w-100" data-section="database">🗄️ Base de Datos</button>
                </li>
                <li class="nav-item mb-1 mb-md-0">
                    <button class="nav-link w-100" data-section="maintenance">🛠️ Mantenimiento</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link text-danger w-100" data-section="danger">⚠️ Zona Peligrosa</button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Sección Zona Peligrosa -->
    <div id="section-danger" class="section-content">
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

                        <h6>Reset de Auto Increment de la Base de Actas</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning text-dark">
                                        <h6 class="mb-0">🔄 Reset Solo Auto Increment</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="small">Reinicia el contador AUTO_INCREMENT sin borrar datos.</p>
                                        <button id="btnResetAutoIncrement" class="btn btn-warning btn-sm w-100">
                                            🔄 Reset AUTO_INCREMENT Solo
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <button id="btnResetActasPreview" class="btn btn-info btn-sm">👀 Vista Previa Estado Actual</button>
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
// JavaScript para el panel de administrador simplificado
document.addEventListener('DOMContentLoaded', function() {
    // Navigation handling
    const navButtons = document.querySelectorAll('.nav-link[data-section]');
    const sections = document.querySelectorAll('.section-content');
    
    navButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetSection = this.getAttribute('data-section');
            
            // Update active button
            navButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Show target section
            sections.forEach(section => {
                section.style.display = 'none';
            });
            
            const target = document.getElementById(`section-${targetSection}`);
            if (target) {
                target.style.display = 'block';
            }
        });
    });

    // Reset Auto Increment functionality
    const btnResetAutoIncrement = document.getElementById('btnResetAutoIncrement');
    const btnResetActasPreview = document.getElementById('btnResetActasPreview');
    const resetActasFeedback = document.getElementById('resetActasFeedback');

    if (btnResetAutoIncrement) {
        btnResetAutoIncrement.addEventListener('click', async function() {
            if (!confirm('¿Estás seguro de que quieres resetear el AUTO_INCREMENT de la tabla actas?\n\nEsta operación NO borrará datos, solo reiniciará el contador.')) {
                return;
            }

            try {
                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Procesando...';

                const response = await fetch('<?php echo e(route('admin.super.reset-auto-increment')); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    resetActasFeedback.innerHTML = `
                        <div class="alert alert-success">
                            <strong>✅ AUTO_INCREMENT reseteado exitosamente</strong><br>
                            ${data.message}
                        </div>
                    `;
                } else {
                    resetActasFeedback.innerHTML = `
                        <div class="alert alert-danger">
                            <strong>❌ Error al resetear AUTO_INCREMENT</strong><br>
                            ${data.message}
                        </div>
                    `;
                }
            } catch (error) {
                resetActasFeedback.innerHTML = `
                    <div class="alert alert-danger">
                        <strong>❌ Error de conexión</strong><br>
                        ${error.message}
                    </div>
                `;
            } finally {
                this.disabled = false;
                this.innerHTML = '🔄 Reset AUTO_INCREMENT Solo';
            }
        });
    }

    if (btnResetActasPreview) {
        btnResetActasPreview.addEventListener('click', async function() {
            try {
                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Cargando...';

                const response = await fetch('/admin/super/actas-info');
                const data = await response.json();

                if (data.success) {
                    resetActasFeedback.innerHTML = `
                        <div class="alert alert-info">
                            <h6>📊 Estado Actual de la Tabla Actas:</h6>
                            <ul class="mb-0">
                                <li><strong>Total de registros:</strong> ${data.total_actas}</li>
                                <li><strong>AUTO_INCREMENT actual:</strong> ${data.auto_increment}</li>
                                <li><strong>Último ID usado:</strong> ${data.last_id || 'Ninguno'}</li>
                            </ul>
                        </div>
                    `;
                }
            } catch (error) {
                resetActasFeedback.innerHTML = `
                    <div class="alert alert-warning">
                        <strong>⚠️ No se pudo obtener información</strong><br>
                        ${error.message}
                    </div>
                `;
            } finally {
                this.disabled = false;
                this.innerHTML = '👀 Vista Previa Estado Actual';
            }
        });
    }
});
</script>
<?php $__env->stopSection(); ?>
            <div class="col-lg-4 col-md-6">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Usuarios Totales</h5>
                        <h2 id="statUsuarios" class="display-6">-</h2>
                        <small class="opacity-75">Usuarios registrados en el sistema</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card bg-success text-white h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Actas Totales</h5>
                        <h2 id="statActas" class="display-6">-</h2>
                        <small class="opacity-75">Actas de contravención</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                <div class="card bg-info text-white h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Actas Recientes</h5>
                        <h2 id="statRecent" class="display-6">-</h2>
                        <small class="opacity-75">Últimas 5 actas creadas</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información del Sistema</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column flex-md-row gap-2">
                            <button id="btnAppInfo" class="btn btn-outline-primary">
                                <i class="fas fa-server me-1"></i>Ver Información de App
                            </button>
                        </div>
                        <pre id="appInfo" class="mt-3 bg-light p-3 rounded border" style="display: none; max-height: 300px; overflow-y: auto; font-size: 0.875rem;"></pre>
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
                    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <h5 class="mb-2 mb-md-0"><i class="fas fa-users me-2"></i>Gestión de Usuarios</h5>
                        <button id="btnLoadUsers" class="btn btn-primary btn-sm">
                            <i class="fas fa-sync-alt me-1"></i>Cargar Usuarios
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="usersTable" class="table table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-nowrap">ID</th>
                                        <th class="text-nowrap">Usuario</th>
                                        <th class="text-nowrap d-none d-md-table-cell">Email</th>
                                        <th class="text-nowrap">Rol</th>
                                        <th class="text-nowrap">Estado</th>
                                        <th class="text-nowrap">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5">
                                            <i class="fas fa-users fa-2x mb-2 text-muted"></i><br>
                                            <strong>Sin usuarios cargados</strong><br>
                                            <small>Haz clic en "Cargar Usuarios" para ver la lista</small>
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
                    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <h5 class="mb-2 mb-md-0"><i class="fas fa-file-alt me-2"></i>Gestión de Actas</h5>
                        <button id="btnLoadActas" class="btn btn-primary btn-sm">
                            <i class="fas fa-sync-alt me-1"></i>Cargar Actas
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-4">
                            <div class="col-lg-3 col-md-6">
                                <div class="card bg-info text-white h-100">
                                    <div class="card-body text-center p-3">
                                        <h6 class="card-title">Total Actas</h6>
                                        <h4 id="actasTotal" class="mb-1">-</h4>
                                        <small class="opacity-75">Todas las actas</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="card bg-warning text-white h-100">
                                    <div class="card-body text-center p-3">
                                        <h6 class="card-title">Pendientes</h6>
                                        <h4 id="actasPendientes" class="mb-1">-</h4>
                                        <small class="opacity-75">Por procesar</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="card bg-success text-white h-100">
                                    <div class="card-body text-center p-3">
                                        <h6 class="card-title">Procesadas</h6>
                                        <h4 id="actasProcesadas" class="mb-1">-</h4>
                                        <small class="opacity-75">Completadas</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="card bg-danger text-white h-100">
                                    <div class="card-body text-center p-3">
                                        <h6 class="card-title">Anuladas</h6>
                                        <h4 id="actasAnuladas" class="mb-1">-</h4>
                                        <small class="opacity-75">Canceladas</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="actasTable" class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-nowrap">ID</th>
                                        <th class="text-nowrap">Número</th>
                                        <th class="text-nowrap d-none d-md-table-cell">Placa</th>
                                        <th class="text-nowrap d-none d-lg-table-cell">Razón Social</th>
                                        <th class="text-nowrap">Estado</th>
                                        <th class="text-nowrap d-none d-md-table-cell">Fecha</th>
                                        <th class="text-nowrap">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-5">
                                            <i class="fas fa-file-alt fa-2x mb-2 text-muted"></i><br>
                                            <strong>Sin actas cargadas</strong><br>
                                            <small>Haz clic en "Cargar Actas" para ver la lista</small>
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

                        <h6>Reset de Auto Increment y Tabla Actas</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning text-dark">
                                        <h6 class="mb-0">🔄 Reset Solo Auto Increment</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="small">Reinicia el contador AUTO_INCREMENT sin borrar datos.</p>
                                        <button id="btnResetAutoIncrement" class="btn btn-warning btn-sm w-100">
                                            � Reset AUTO_INCREMENT Solo
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-danger">
                                    <div class="card-header bg-danger text-white">
                                        <h6 class="mb-0">💀 Reset Completo</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="small">Borra TODOS los datos y reinicia contador.</p>
                                        <button id="btnResetActasSuper" class="btn btn-danger btn-sm w-100">
                                            💀 Reset Completo (Destructivo)
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <button id="btnResetActasPreview" class="btn btn-info btn-sm">👀 Vista Previa Estado Actual</button>
                        </div>
                        
                        <div class="mb-3">
                            <label for="superConfirm" class="form-label">Para operaciones destructivas, escribe "CONFIRMAR":</label>
                            <input type="text" id="superConfirm" class="form-control" placeholder="Escribe CONFIRMAR para borrar datos">
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
                            <td class="text-nowrap">${user.username}</td>
                            <td class="d-none d-md-table-cell">${user.email || '-'}</td>
                            <td><span class="badge bg-primary">${user.role}</span></td>
                            <td><span class="badge bg-${user.status === 'active' ? 'success' : 'warning'}">${user.status}</span></td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button class="btn btn-outline-success" onclick="alert('Aprobar usuario ${user.id}')" title="Aprobar">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-outline-warning" onclick="alert('Toggle usuario ${user.id}')" title="Cambiar estado">
                                        <i class="fas fa-power-off"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" onclick="alert('Eliminar usuario ${user.id}')" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
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
                                <td class="text-nowrap">${acta.numero_acta || '-'}</td>
                                <td class="d-none d-lg-table-cell">${acta.placa || '-'}</td>
                                <td class="d-none d-md-table-cell">${acta.razon_social || '-'}</td>
                                <td><span class="badge bg-${acta.estado === 'pendiente' ? 'warning' : acta.estado === 'procesada' ? 'success' : 'danger'}">${acta.estado}</span></td>
                                <td class="d-none d-lg-table-cell">${new Date(acta.created_at).toLocaleDateString('es-ES')}</td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-outline-primary" onclick="alert('Ver acta ${acta.id}')" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-warning" onclick="alert('Editar acta ${acta.id}')" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" onclick="alert('Eliminar acta ${acta.id}')" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
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

    // Reset de auto increment solo
    document.getElementById('btnResetAutoIncrement')?.addEventListener('click', async function() {
        if (confirm('¿Reiniciar solo el AUTO_INCREMENT de la tabla actas sin borrar datos?')) {
            try {
                showLoader();
                const response = await fetch('<?php echo e(route('admin.super.reset-auto-increment')); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    }
                });
                
                const data = await response.json();
                hideLoader();
                
                const alertType = data.ok ? 'success' : 'danger';
                document.getElementById('resetActasFeedback').innerHTML = `
                    <div class="alert alert-${alertType}">
                        <strong>Reset AUTO_INCREMENT:</strong> ${data.message || 'Operación completada'}
                    </div>
                `;
                
                showMessage('Reset de AUTO_INCREMENT completado', 'success');
                
            } catch (error) {
                hideLoader();
                showMessage(`Error en reset auto increment: ${error.message}`, 'error');
            }
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

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Login-app\resources\views\administrador\super\index_backup.blade.php ENDPATH**/ ?>