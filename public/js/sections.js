/**
 * Funciones específicas para diferentes secciones del sistema
 * Archivo: sections.js
 */

/**
 * Cargar sección de usuarios
 */
function loadUsuariosList() {
    console.log('👥 Cargando Lista de Usuarios...');
    hideAllSections();
    
    const contentContainer = document.getElementById('contentContainer');
    contentContainer.innerHTML = `
        <div class="content-section active">
            <div class="content-header d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4><i class="fas fa-users"></i> Gestión de Usuarios</h4>
                    <p class="text-muted mb-0">Administrar usuarios del sistema</p>
                </div>
                <button class="btn btn-primary" onclick="showCrearUsuarioModal()">
                    <i class="fas fa-plus"></i> Nuevo Usuario
                </button>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-0">Lista de Usuarios</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Buscar usuarios..." id="searchUsers">
                                <button class="btn btn-outline-secondary" type="button" onclick="searchUsers()">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Usuario</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="usuarios-table-body">
                                <tr><td colspan="7" class="text-center">Cargando usuarios...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Cargar datos de usuarios
    loadUsersData();
}

/**
 * Cargar datos de usuarios desde la API
 */
function loadUsersData() {
    fetchAPI('users')
        .then(data => {
            if (data.success) {
                displayUsersTable(data.users);
            } else {
                showAlert('error', 'Error al cargar usuarios: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Error de conexión al cargar usuarios');
        });
}

/**
 * Mostrar tabla de usuarios
 */
function displayUsersTable(users) {
    const tbody = document.getElementById('usuarios-table-body');
    if (!tbody) return;
    
    if (users.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No se encontraron usuarios</td></tr>';
        return;
    }
    
    tbody.innerHTML = users.map(user => `
        <tr>
            <td>${user.id}</td>
            <td>${escapeHtml(user.name)}</td>
            <td>${escapeHtml(user.username)}</td>
            <td>${escapeHtml(user.email)}</td>
            <td>
                <span class="badge bg-${getRoleBadgeClass(user.role)}">${capitalize(user.role)}</span>
            </td>
            <td>
                <span class="badge bg-${getStatusBadgeClass(user.status)}">${getStatusText(user.status)}</span>
            </td>
            <td>
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-primary" onclick="editUser(${user.id})" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(${user.id})" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

/**
 * Obtener clase CSS para badge de rol
 */
function getRoleBadgeClass(role) {
    const roleClasses = {
        'superadmin': 'danger',
        'administrador': 'primary',
        'fiscalizador': 'info',
        'inspector': 'success',
        'ventanilla': 'warning'
    };
    return roleClasses[role] || 'secondary';
}

/**
 * Obtener clase CSS para badge de estado
 */
function getStatusBadgeClass(status) {
    const statusClasses = {
        'approved': 'success',
        'active': 'success',
        'pending': 'warning',
        'suspended': 'danger',
        'rejected': 'danger'
    };
    return statusClasses[status] || 'secondary';
}

/**
 * Obtener texto legible para estado
 */
function getStatusText(status) {
    const statusTexts = {
        'approved': 'Aprobado',
        'active': 'Activo',
        'pending': 'Pendiente',
        'suspended': 'Suspendido',
        'rejected': 'Rechazado'
    };
    return statusTexts[status] || status;
}

/**
 * Mostrar modal para crear usuario
 */
function showCrearUsuarioModal() {
    const modalBody = `
        <form id="crearUsuarioForm">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nombre Completo</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nombre de Usuario</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="tel" class="form-control" name="phone">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Rol</label>
                        <select class="form-select" name="role" required>
                            <option value="">Seleccionar rol...</option>
                            <option value="administrador">Administrador</option>
                            <option value="fiscalizador">Fiscalizador</option>
                            <option value="inspector">Inspector</option>
                            <option value="ventanilla">Ventanilla</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select class="form-select" name="status" required>
                            <option value="approved">Aprobado</option>
                            <option value="pending">Pendiente</option>
                            <option value="suspended">Suspendido</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" class="form-control" name="password" required>
            </div>
        </form>
    `;
    
    const modalFooter = `
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="submitCreateUser()">Crear Usuario</button>
    `;
    
    showModal('Crear Nuevo Usuario', modalBody, modalFooter);
}

/**
 * Enviar formulario de crear usuario
 */
function submitCreateUser() {
    const form = document.getElementById('crearUsuarioForm');
    if (!validateForm(form)) {
        showToast('warning', 'Validación', 'Por favor complete todos los campos requeridos');
        return;
    }
    
    const formData = new FormData(form);
    const userData = Object.fromEntries(formData.entries());
    
    postAPI('create-user', userData)
        .then(data => {
            if (data.success) {
                showToast('success', 'Usuario Creado', 'El usuario ha sido creado exitosamente');
                bootstrap.Modal.getInstance(document.getElementById('dynamicModal')).hide();
                loadUsersData(); // Recargar la tabla
            } else {
                showToast('error', 'Error', data.message || 'Error al crear usuario');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Error', 'Error de conexión');
        });
}

/**
 * Editar usuario
 */
function editUser(userId) {
    showToast('info', 'Función en desarrollo', 'La edición de usuarios estará disponible próximamente');
}

/**
 * Eliminar usuario
 */
function deleteUser(userId) {
    confirmAction('¿Está seguro de eliminar este usuario?', () => {
        postAPI('delete-user', { id: userId })
            .then(data => {
                if (data.success) {
                    showToast('success', 'Usuario Eliminado', 'El usuario ha sido eliminado');
                    loadUsersData(); // Recargar la tabla
                } else {
                    showToast('error', 'Error', data.message || 'Error al eliminar usuario');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'Error', 'Error de conexión');
            });
    });
}

/**
 * Buscar usuarios
 */
function searchUsers() {
    const searchTerm = document.getElementById('searchUsers').value.toLowerCase();
    const rows = document.querySelectorAll('#usuarios-table-body tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
}

/**
 * Cargar sección de reportes
 */
function loadReportes() {
    console.log('📊 Cargando Reportes...');
    hideAllSections();
    
    const contentContainer = document.getElementById('contentContainer');
    contentContainer.innerHTML = `
        <div class="content-section active">
            <div class="content-header">
                <h4><i class="fas fa-chart-bar"></i> Reportes del Sistema</h4>
                <p class="text-muted">Generar y visualizar reportes estadísticos</p>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-3x text-primary mb-3"></i>
                            <h5>Reporte de Usuarios</h5>
                            <p class="text-muted">Estadísticas y listado de usuarios del sistema</p>
                            <button class="btn btn-primary" onclick="generarReporte('usuarios')">
                                <i class="fas fa-file-pdf"></i> Generar PDF
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-file-alt fa-3x text-success mb-3"></i>
                            <h5>Reporte de Actas</h5>
                            <p class="text-muted">Infracciones registradas en el sistema</p>
                            <button class="btn btn-success" onclick="generarReporte('actas')">
                                <i class="fas fa-file-excel"></i> Generar Excel
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-line fa-3x text-info mb-3"></i>
                            <h5>Estadísticas Generales</h5>
                            <p class="text-muted">Resumen estadístico del sistema</p>
                            <button class="btn btn-info" onclick="generarReporte('estadisticas')">
                                <i class="fas fa-chart-bar"></i> Ver Gráficos
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-filter"></i> Filtros de Reporte</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Fecha Desde</label>
                            <input type="date" class="form-control" id="fechaDesde">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Fecha Hasta</label>
                            <input type="date" class="form-control" id="fechaHasta">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tipo</label>
                            <select class="form-select" id="tipoReporte">
                                <option value="">Todos</option>
                                <option value="usuarios">Usuarios</option>
                                <option value="actas">Actas</option>
                                <option value="multas">Multas</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-outline-primary w-100" onclick="aplicarFiltros()">
                                <i class="fas fa-filter"></i> Aplicar Filtros
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

/**
 * Generar reporte
 */
function generarReporte(tipo) {
    console.log('📊 Generando reporte:', tipo);
    showToast('info', 'Generando Reporte', `Generando reporte de ${tipo}...`);
    
    // Simular proceso de generación
    setTimeout(() => {
        const modal = showModal('Reporte Generado', `
            <div class="text-center">
                <i class="fas fa-file-pdf fa-3x text-danger mb-3"></i>
                <h4>Reporte de ${tipo.toUpperCase()}</h4>
                <p>El reporte ha sido generado exitosamente.</p>
                <div class="alert alert-info">
                    <strong>Fecha:</strong> ${new Date().toLocaleDateString('es-ES')}<br>
                    <strong>Usuario:</strong> ${userName}<br>
                    <strong>Tipo:</strong> ${tipo}
                </div>
            </div>
        `, `
            <button class="btn btn-success" onclick="descargarReporte('${tipo}')">
                <i class="fas fa-download"></i> Descargar PDF
            </button>
            <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        `);
    }, 2000);
}

/**
 * Descargar reporte
 */
function descargarReporte(tipo) {
    showToast('success', 'Descarga Iniciada', `Descargando reporte de ${tipo}...`);
    console.log('⬇️ Descargando reporte:', tipo);
}

/**
 * Aplicar filtros de reporte
 */
function aplicarFiltros() {
    const fechaDesde = document.getElementById('fechaDesde').value;
    const fechaHasta = document.getElementById('fechaHasta').value;
    const tipo = document.getElementById('tipoReporte').value;
    
    showToast('info', 'Filtros Aplicados', `Filtros: ${fechaDesde || 'Sin fecha'} - ${fechaHasta || 'Sin fecha'}, Tipo: ${tipo || 'Todos'}`);
}

/**
 * Cargar Mi Perfil
 */
function loadPerfil() {
    console.log('👤 Cargando perfil...');
    hideAllSections();
    
    const contentContainer = document.getElementById('contentContainer');
    contentContainer.innerHTML = `
        <div class="content-section active">
            <h2><i class="fas fa-user"></i> Mi Perfil</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-user-circle fa-5x text-primary"></i>
                            </div>
                            <h4>${userName}</h4>
                            <p class="text-muted">${userRole.toUpperCase()}</p>
                            <span class="badge bg-success">Usuario Activo</span>
                            <hr>
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary btn-sm" onclick="showModal('Cambiar Avatar', 'Funcionalidad de cambio de avatar en desarrollo')">
                                    <i class="fas fa-camera"></i> Cambiar Avatar
                                </button>
                                <button class="btn btn-outline-secondary btn-sm" onclick="showModal('Actividad Reciente', 'Historial de actividad del usuario')">
                                    <i class="fas fa-history"></i> Actividad Reciente
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-edit"></i> Información Personal</h5>
                        </div>
                        <div class="card-body">
                            <form id="perfilForm">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nombre Completo</label>
                                        <input type="text" class="form-control" name="name" value="${userName}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" value="${userName.toLowerCase()}@sistema.com">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Teléfono</label>
                                        <input type="tel" class="form-control" name="phone" placeholder="+51 999 999 999">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Rol</label>
                                        <input type="text" class="form-control" value="${userRole.toUpperCase()}" readonly>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Nueva Contraseña</label>
                                        <input type="password" class="form-control" name="password" placeholder="Dejar vacío si no desea cambiar">
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Confirmar Nueva Contraseña</label>
                                        <input type="password" class="form-control" name="password_confirm" placeholder="Confirmar contraseña">
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Guardar Cambios
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary ms-2" onclick="loadSection('dashboard')">
                                            <i class="fas fa-times"></i> Cancelar
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Agregar evento al formulario
    document.getElementById('perfilForm').addEventListener('submit', function(e) {
        e.preventDefault();
        showToast('success', 'Perfil Actualizado', 'Los cambios se han guardado correctamente');
    });
    
    showToast('info', 'Perfil Cargado', 'Tu información personal está disponible');
}

// Exponer funciones globalmente
window.loadUsuariosList = loadUsuariosList;
window.loadUsersData = loadUsersData;
window.displayUsersTable = displayUsersTable;
window.showCrearUsuarioModal = showCrearUsuarioModal;
window.submitCreateUser = submitCreateUser;
window.editUser = editUser;
window.deleteUser = deleteUser;
window.searchUsers = searchUsers;
window.loadReportes = loadReportes;
window.generarReporte = generarReporte;
window.descargarReporte = descargarReporte;
window.aplicarFiltros = aplicarFiltros;
window.loadPerfil = loadPerfil;