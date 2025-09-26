/**
 * Funciones de configuración del sistema
 * Archivo: config.js
 */

/**
 * Cargar configuración del sistema
 */
function loadConfiguracion() {
    console.log('⚙️ Cargando Configuración...');
    hideAllSections();
    
    const contentContainer = document.getElementById('contentContainer');
    contentContainer.innerHTML = `
        <div class="content-section active">
            <h2><i class="fas fa-cog"></i> Configuración del Sistema</h2>
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Configuración General</h5>
                        </div>
                        <div class="card-body">
                            <form id="configForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Nombre del Sistema</label>
                                            <input type="text" class="form-control" value="Sistema de Gestión" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Versión</label>
                                            <input type="text" class="form-control" value="1.0.0" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Email Administrador</label>
                                            <input type="email" class="form-control" value="admin@sistema.com">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Zona Horaria</label>
                                            <select class="form-select">
                                                <option value="America/Lima" selected>Lima, Peru (GMT-5)</option>
                                                <option value="America/Bogota">Bogotá, Colombia (GMT-5)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="maintMode">
                                        <label class="form-check-label" for="maintMode">
                                            Modo Mantenimiento
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="debugMode">
                                        <label class="form-check-label" for="debugMode">
                                            Modo Debug
                                        </label>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary" onclick="saveConfiguration()">
                                    <i class="fas fa-save"></i> Guardar Configuración
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Configuración de Tema -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Personalización de Tema</h5>
                        </div>
                        <div class="card-body">
                            <form id="themeForm">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Tema Principal</label>
                                            <select class="form-select" id="mainTheme">
                                                <option value="default" selected>Por Defecto</option>
                                                <option value="dark">Oscuro</option>
                                                <option value="light">Claro</option>
                                                <option value="blue">Azul</option>
                                                <option value="green">Verde</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Color Primario</label>
                                            <input type="color" class="form-control form-control-color" id="primaryColor" value="#2c3e50">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Color Secundario</label>
                                            <input type="color" class="form-control form-control-color" id="secondaryColor" value="#3498db">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="darkMode">
                                                <label class="form-check-label" for="darkMode">
                                                    Modo Oscuro
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="compactMode">
                                                <label class="form-check-label" for="compactMode">
                                                    Modo Compacto
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-info" onclick="applyTheme()">
                                    <i class="fas fa-palette"></i> Aplicar Tema
                                </button>
                                <button type="button" class="btn btn-outline-secondary ms-2" onclick="resetTheme()">
                                    <i class="fas fa-undo"></i> Restaurar Por Defecto
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Información del Sistema</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr><td><strong>Servidor:</strong></td><td>Apache/PHP</td></tr>
                                <tr><td><strong>Base de Datos:</strong></td><td>MySQL</td></tr>
                                <tr><td><strong>PHP Version:</strong></td><td>8.x</td></tr>
                                <tr><td><strong>Usuarios Activos:</strong></td><td id="activeUsers">-</td></tr>
                                <tr><td><strong>Último Backup:</strong></td><td>Hoy</td></tr>
                                <tr><td><strong>Espacio Usado:</strong></td><td>250 MB</td></tr>
                                <tr><td><strong>Estado:</strong></td><td><span class="badge bg-success">Operativo</span></td></tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Acciones del Sistema</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button class="btn btn-warning" onclick="performSystemBackup()">
                                    <i class="fas fa-database"></i> Crear Backup
                                </button>
                                <button class="btn btn-info" onclick="clearSystemCache()">
                                    <i class="fas fa-broom"></i> Limpiar Cache
                                </button>
                                <button class="btn btn-success" onclick="runSystemDiagnostic()">
                                    <i class="fas fa-stethoscope"></i> Diagnóstico
                                </button>
                                <button class="btn btn-secondary" onclick="viewSystemLogs()">
                                    <i class="fas fa-file-alt"></i> Ver Logs
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Cargar configuración actual
    loadCurrentConfiguration();
}

/**
 * Cargar configuración actual
 */
function loadCurrentConfiguration() {
    fetchAPI('system-config')
        .then(data => {
            if (data.success) {
                updateConfigurationForm(data.config);
            }
        })
        .catch(error => {
            console.error('Error al cargar configuración:', error);
        });
}

/**
 * Actualizar formulario de configuración
 */
function updateConfigurationForm(config) {
    // Actualizar campos del formulario con la configuración actual
    if (config.mantenimiento) {
        document.getElementById('maintMode').checked = true;
    }
    
    if (config.modo_oscuro) {
        document.getElementById('darkMode').checked = true;
    }
    
    // Actualizar información del sistema
    document.getElementById('activeUsers').textContent = config.usuarios_activos || '-';
}

/**
 * Guardar configuración
 */
function saveConfiguration() {
    const formData = new FormData(document.getElementById('configForm'));
    const config = Object.fromEntries(formData.entries());
    
    // Agregar checkboxes
    config.mantenimiento = document.getElementById('maintMode').checked;
    config.debug = document.getElementById('debugMode').checked;
    
    postAPI('update-config', { type: 'system', ...config })
        .then(data => {
            if (data.success) {
                showToast('success', 'Configuración Guardada', 'La configuración se ha actualizado correctamente');
            } else {
                showToast('error', 'Error', data.message || 'Error al guardar configuración');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Error', 'Error de conexión');
        });
}

/**
 * Aplicar tema
 */
function applyTheme() {
    const theme = document.getElementById('mainTheme').value;
    const primaryColor = document.getElementById('primaryColor').value;
    const secondaryColor = document.getElementById('secondaryColor').value;
    const darkMode = document.getElementById('darkMode').checked;
    const compactMode = document.getElementById('compactMode').checked;
    
    const themeConfig = {
        type: 'theme',
        tema_principal: theme,
        color_primario: primaryColor,
        color_secundario: secondaryColor,
        modo_oscuro: darkMode,
        modo_compacto: compactMode
    };
    
    postAPI('update-config', themeConfig)
        .then(data => {
            if (data.success) {
                applyThemeToDocument(themeConfig);
                showToast('success', 'Tema Aplicado', 'El tema se ha actualizado correctamente');
            } else {
                showToast('error', 'Error', data.message || 'Error al aplicar tema');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Error', 'Error de conexión');
        });
}

/**
 * Aplicar tema al documento
 */
function applyThemeToDocument(themeConfig) {
    const root = document.documentElement;
    
    // Aplicar colores personalizados
    root.style.setProperty('--primary-color', themeConfig.color_primario);
    root.style.setProperty('--secondary-color', themeConfig.color_secundario);
    
    // Aplicar modo oscuro
    if (themeConfig.modo_oscuro) {
        document.body.classList.add('dark-theme');
    } else {
        document.body.classList.remove('dark-theme');
    }
    
    // Aplicar modo compacto
    if (themeConfig.modo_compacto) {
        document.body.classList.add('compact-theme');
    } else {
        document.body.classList.remove('compact-theme');
    }
}

/**
 * Restaurar tema por defecto
 */
function resetTheme() {
    document.getElementById('mainTheme').value = 'default';
    document.getElementById('primaryColor').value = '#2c3e50';
    document.getElementById('secondaryColor').value = '#3498db';
    document.getElementById('darkMode').checked = false;
    document.getElementById('compactMode').checked = false;
    
    applyTheme();
    showToast('info', 'Tema Restaurado', 'El tema por defecto ha sido restaurado');
}

/**
 * Realizar backup del sistema
 */
function performSystemBackup() {
    showToast('info', 'Backup Iniciado', 'Creando copia de seguridad del sistema...');
    
    // Simular proceso de backup
    setTimeout(() => {
        showToast('success', 'Backup Completado', 'La copia de seguridad se ha creado exitosamente');
    }, 3000);
}

/**
 * Limpiar cache del sistema
 */
function clearSystemCache() {
    showToast('info', 'Limpiando Cache', 'Eliminando archivos temporales...');
    
    // Simular limpieza de cache
    setTimeout(() => {
        showToast('success', 'Cache Limpio', 'Los archivos de cache han sido eliminados');
    }, 2000);
}

/**
 * Ejecutar diagnóstico del sistema
 */
function runSystemDiagnostic() {
    showToast('info', 'Diagnóstico Iniciado', 'Verificando el estado del sistema...');
    
    // Simular diagnóstico
    setTimeout(() => {
        const diagnosticResults = `
            <div class="alert alert-success">
                <h6><i class="fas fa-check-circle"></i> Sistema Operativo</h6>
                <ul class="mb-0">
                    <li>Base de datos: Conectada ✓</li>
                    <li>Archivos del sistema: OK ✓</li>
                    <li>Memoria disponible: 75% ✓</li>
                    <li>Espacio en disco: 80% libre ✓</li>
                    <li>Conexión a internet: Activa ✓</li>
                </ul>
            </div>
        `;
        
        showModal('Resultados del Diagnóstico', diagnosticResults);
    }, 3000);
}

/**
 * Ver logs del sistema
 */
function viewSystemLogs() {
    const logsContent = `
        <div class="font-monospace small" style="max-height: 400px; overflow-y: auto;">
            <div class="text-success">[2024-01-15 10:30:25] INFO: Sistema iniciado correctamente</div>
            <div class="text-info">[2024-01-15 10:30:26] INFO: Base de datos conectada</div>
            <div class="text-primary">[2024-01-15 10:30:27] INFO: Usuario admin autenticado</div>
            <div class="text-warning">[2024-01-15 10:35:15] WARN: Intento de login fallido para usuario: test</div>
            <div class="text-info">[2024-01-15 10:40:12] INFO: Nueva acta creada: ACT000123</div>
            <div class="text-success">[2024-01-15 10:45:30] INFO: Backup automático completado</div>
            <div class="text-primary">[2024-01-15 10:50:18] INFO: Estadísticas actualizadas</div>
            <div class="text-info">[2024-01-15 11:00:00] INFO: Limpieza de cache programada</div>
        </div>
    `;
    
    showModal('Logs del Sistema', logsContent);
}

// Exponer funciones globalmente
window.loadConfiguracion = loadConfiguracion;
window.saveConfiguration = saveConfiguration;
window.applyTheme = applyTheme;
window.resetTheme = resetTheme;
window.performSystemBackup = performSystemBackup;
window.clearSystemCache = clearSystemCache;
window.runSystemDiagnostic = runSystemDiagnostic;
window.viewSystemLogs = viewSystemLogs;

// Log de carga del archivo
console.log('✅ config.js cargado correctamente');