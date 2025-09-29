/**
 * ================================
 * GESTIÓN DE USUARIOS
 * Sistema de Gestión - JavaScript
 * ================================
 */

// Variable global para almacenar usuarios
let todosLosUsuarios = [];

// ================================
// FUNCIONES PRINCIPALES
// ================================

function loadUsuariosList() {
    const contentContainer = document.getElementById('contentContainer');
    contentContainer.innerHTML = `
        <div class="content-section active">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-users"></i> Lista de Usuarios</h2>
                <button class="btn btn-primary" onclick="showCrearUsuarioModal()">
                    <i class="fas fa-user-plus"></i> Nuevo Usuario
                </button>
            </div>
            
            <!-- Filtros y Búsqueda -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">Buscar Usuario</label>
                            <input type="text" class="form-control" id="searchUsuarios" placeholder="Nombre, email o username..." onkeyup="filtrarUsuarios()">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Filtrar por Rol</label>
                            <select class="form-select" id="filterRol" onchange="filtrarUsuarios()">
                                <option value="">Todos los roles</option>
                                <option value="administrador">Administrador</option>
                                <option value="fiscalizador">Fiscalizador</option>
                                <option value="inspector">Inspector</option>
                                <option value="ventanilla">Ventanilla</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Filtrar por Estado</label>
                            <select class="form-select" id="filterStatus" onchange="filtrarUsuarios()">
                                <option value="">Todos los estados</option>
                                <option value="approved">Aprobado</option>
                                <option value="pending">Pendiente</option>
                                <option value="rejected">Rechazado</option>
                                <option value="suspended">Suspendido</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button class="btn btn-secondary w-100" onclick="limpiarFiltros()">
                                <i class="fas fa-times"></i> Limpiar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Usuarios -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="usuariosTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Usuario</th>
                                    <th>Información</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th>Último Acceso</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="usuariosTableBody">
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <div class="spinner-border" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                        <p class="mt-2">Cargando usuarios...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Cargar usuarios desde la API
    cargarUsuariosDesdeAPI();
}

// ================================
// CARGAR USUARIOS DESDE API
// ================================

async function cargarUsuariosDesdeAPI() {
    try {
        const response = await fetch('/Login-app/public/dashboard_api.php?action=get_users');
        const data = await response.json();
        
        if (data.success) {
            todosLosUsuarios = data.users;
            mostrarUsuarios(todosLosUsuarios);
        } else {
            mostrarError('Error al cargar usuarios: ' + data.message);
        }
    } catch (error) {
        mostrarError('Error de conexión: ' + error.message);
    }
}

function mostrarUsuarios(usuarios) {
    const tbody = document.getElementById('usuariosTableBody');
    
    if (usuarios.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center">
                    <i class="fas fa-users text-muted" style="font-size: 2rem;"></i>
                    <p class="mt-2 text-muted">No se encontraron usuarios</p>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = usuarios.map(user => `
        <tr>
            <td>${user.id}</td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-sm me-2">
                        <div class="avatar-initial rounded-circle bg-primary">
                            ${user.name ? user.name.charAt(0).toUpperCase() : 'U'}
                        </div>
                    </div>
                    <div>
                        <div class="fw-bold">${user.name || 'N/A'}</div>
                        <small class="text-muted">@${user.username}</small>
                    </div>
                </div>
            </td>
            <td>
                <div class="text-start">
                    <div><i class="fas fa-envelope text-muted me-1"></i> ${user.email}</div>
                    ${user.phone ? `<div><i class="fas fa-phone text-muted me-1"></i> ${user.phone}</div>` : ''}
                </div>
            </td>
            <td>
                <span class="badge ${getRoleBadgeClass(user.role)}">${getRoleDisplayName(user.role)}</span>
            </td>
            <td>
                <span class="badge ${getStatusBadgeClass(user.status)}">${getStatusDisplayName(user.status)}</span>
            </td>
            <td>
                <small class="text-muted">
                    ${user.last_login ? formatDate(user.last_login) : 'Nunca'}
                </small>
            </td>
            <td class="text-center">
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-primary" onclick="editarUsuario(${user.id})" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-warning" onclick="cambiarContrasena(${user.id})" title="Cambiar Contraseña">
                        <i class="fas fa-key"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-info" onclick="toggleEstadoUsuario(${user.id}, '${user.status}')" title="Cambiar Estado">
                        <i class="fas fa-toggle-on"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="eliminarUsuario(${user.id}, '${user.name}')" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

// ================================
// FUNCIONES DE ACCIÓN
// ================================

function editarUsuario(userId) {
    const user = todosLosUsuarios.find(u => u.id == userId);
    if (!user) {
        mostrarError('Usuario no encontrado');
        return;
    }

    // Crear modal de edición
    const modalHtml = `
        <div class="modal fade" id="editUsuarioModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editUsuarioForm">
                            <input type="hidden" id="editUserId" value="${user.id}">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Nombre Completo</label>
                                        <input type="text" class="form-control" id="editName" value="${user.name || ''}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Username</label>
                                        <input type="text" class="form-control" id="editUsername" value="${user.username}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" id="editEmail" value="${user.email}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Teléfono</label>
                                        <input type="text" class="form-control" id="editPhone" value="${user.phone || ''}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Rol</label>
                                        <select class="form-select" id="editRole" required>
                                            <option value="administrador" ${user.role === 'administrador' ? 'selected' : ''}>Administrador</option>
                                            <option value="fiscalizador" ${user.role === 'fiscalizador' ? 'selected' : ''}>Fiscalizador</option>
                                            <option value="inspector" ${user.role === 'inspector' ? 'selected' : ''}>Inspector</option>
                                            <option value="ventanilla" ${user.role === 'ventanilla' ? 'selected' : ''}>Ventanilla</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Estado</label>
                                        <select class="form-select" id="editStatus" required>
                                            <option value="approved" ${user.status === 'approved' ? 'selected' : ''}>Aprobado</option>
                                            <option value="pending" ${user.status === 'pending' ? 'selected' : ''}>Pendiente</option>
                                            <option value="suspended" ${user.status === 'suspended' ? 'selected' : ''}>Suspendido</option>
                                            <option value="rejected" ${user.status === 'rejected' ? 'selected' : ''}>Rechazado</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="guardarEdicionUsuario()">Guardar Cambios</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Agregar modal al DOM y mostrar
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('editUsuarioModal'));
    modal.show();

    // Limpiar modal cuando se cierre
    document.getElementById('editUsuarioModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

async function guardarEdicionUsuario() {
    const form = document.getElementById('editUsuarioForm');
    const formData = new FormData();
    
    formData.append('action', 'update_user');
    formData.append('user_id', document.getElementById('editUserId').value);
    formData.append('name', document.getElementById('editName').value);
    formData.append('username', document.getElementById('editUsername').value);
    formData.append('email', document.getElementById('editEmail').value);
    formData.append('phone', document.getElementById('editPhone').value);
    formData.append('role', document.getElementById('editRole').value);
    formData.append('status', document.getElementById('editStatus').value);

    try {
        const response = await fetch('/Login-app/public/dashboard_api.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.success) {
            mostrarExito('Usuario actualizado correctamente');
            bootstrap.Modal.getInstance(document.getElementById('editUsuarioModal')).hide();
            cargarUsuariosDesdeAPI(); // Recargar la lista
        } else {
            mostrarError('Error al actualizar usuario: ' + data.message);
        }
    } catch (error) {
        mostrarError('Error de conexión: ' + error.message);
    }
}

function cambiarContrasena(userId) {
    const user = todosLosUsuarios.find(u => u.id == userId);
    if (!user) {
        mostrarError('Usuario no encontrado');
        return;
    }

    const modalHtml = `
        <div class="modal fade" id="cambiarPasswordModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cambiar Contraseña - ${user.username}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="cambiarPasswordForm">
                            <input type="hidden" id="passwordUserId" value="${userId}">
                            <div class="mb-3">
                                <label class="form-label">Nueva Contraseña</label>
                                <input type="password" class="form-control" id="newPassword" required minlength="6">
                                <div class="form-text">Mínimo 6 caracteres</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirmar Contraseña</label>
                                <input type="password" class="form-control" id="confirmPassword" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="guardarNuevaContrasena()">Cambiar Contraseña</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('cambiarPasswordModal'));
    modal.show();

    document.getElementById('cambiarPasswordModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

async function guardarNuevaContrasena() {
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const userId = document.getElementById('passwordUserId').value;

    if (newPassword !== confirmPassword) {
        mostrarError('Las contraseñas no coinciden');
        return;
    }

    if (newPassword.length < 6) {
        mostrarError('La contraseña debe tener al menos 6 caracteres');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'change_password');
    formData.append('user_id', userId);
    formData.append('new_password', newPassword);

    try {
        const response = await fetch('/Login-app/public/dashboard_api.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.success) {
            mostrarExito('Contraseña cambiada correctamente');
            bootstrap.Modal.getInstance(document.getElementById('cambiarPasswordModal')).hide();
        } else {
            mostrarError('Error al cambiar contraseña: ' + data.message);
        }
    } catch (error) {
        mostrarError('Error de conexión: ' + error.message);
    }
}

async function toggleEstadoUsuario(userId, currentStatus) {
    let newStatus;
    switch (currentStatus) {
        case 'approved':
            newStatus = 'suspended';
            break;
        case 'suspended':
            newStatus = 'approved';
            break;
        case 'pending':
            newStatus = 'approved';
            break;
        default:
            newStatus = 'approved';
    }

    const user = todosLosUsuarios.find(u => u.id == userId);
    const confirmMessage = `¿Cambiar el estado de ${user.username} a "${getStatusDisplayName(newStatus)}"?`;
    
    if (!confirm(confirmMessage)) return;

    const formData = new FormData();
    formData.append('action', 'update_user_status');
    formData.append('user_id', userId);
    formData.append('status', newStatus);

    try {
        const response = await fetch('/Login-app/public/dashboard_api.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.success) {
            mostrarExito(`Estado del usuario cambiado a ${getStatusDisplayName(newStatus)}`);
            cargarUsuariosDesdeAPI(); // Recargar la lista
        } else {
            mostrarError('Error al cambiar estado: ' + data.message);
        }
    } catch (error) {
        mostrarError('Error de conexión: ' + error.message);
    }
}

async function eliminarUsuario(userId, userName) {
    if (!confirm(`¿Estás seguro de que quieres eliminar al usuario "${userName}"?\n\nEsta acción no se puede deshacer.`)) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'delete_user');
    formData.append('user_id', userId);

    try {
        const response = await fetch('/Login-app/public/dashboard_api.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.success) {
            mostrarExito('Usuario eliminado correctamente');
            cargarUsuariosDesdeAPI(); // Recargar la lista
        } else {
            mostrarError('Error al eliminar usuario: ' + data.message);
        }
    } catch (error) {
        mostrarError('Error de conexión: ' + error.message);
    }
}

// ================================
// APROBAR USUARIOS (REGISTRO PENDIENTE)
// ================================

function loadAprobarUsuarios() {
    const contentContainer = document.getElementById('contentContainer');
    contentContainer.innerHTML = `
        <div class="content-section active">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-user-check"></i> Aprobar Usuarios</h2>
                <div class="d-flex gap-2">
                    <button class="btn btn-success" onclick="aprobarTodosPendientes()">
                        <i class="fas fa-check-double"></i> Aprobar Todos
                    </button>
                    <button class="btn btn-outline-secondary" onclick="cargarUsuariosPendientes()">
                        <i class="fas fa-refresh"></i> Actualizar
                    </button>
                </div>
            </div>

            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Información:</strong> Aquí se muestran los usuarios que se han registrado pero necesitan aprobación del administrador para poder iniciar sesión.
            </div>

            <!-- Lista de Usuarios Pendientes -->
            <div class="card">
                <div class="card-body">
                    <div id="usuariosPendientesContainer">
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="mt-2">Cargando usuarios pendientes...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    cargarUsuariosPendientes();
}

async function cargarUsuariosPendientes() {
    try {
        const response = await fetch('/Login-app/public/dashboard_api.php?action=get_pending_users');
        const data = await response.json();
        
        const container = document.getElementById('usuariosPendientesContainer');
        
        if (data.success && data.users.length > 0) {
            container.innerHTML = data.users.map(user => `
                <div class="border rounded p-3 mb-3" id="pending-user-${user.id}">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <div class="avatar-lg">
                                <div class="avatar-initial rounded-circle bg-warning">
                                    ${user.name ? user.name.charAt(0).toUpperCase() : 'U'}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-1">${user.name || 'Sin nombre'}</h6>
                            <p class="text-muted mb-1">@${user.username}</p>
                            <p class="text-muted mb-1"><i class="fas fa-envelope me-1"></i> ${user.email}</p>
                            <span class="badge bg-info">${getRoleDisplayName(user.role)}</span>
                            <span class="badge bg-warning">Pendiente de Aprobación</span>
                        </div>
                        <div class="col-md-2">
                            <small class="text-muted">
                                Registrado: ${formatDate(user.created_at)}
                            </small>
                        </div>
                        <div class="col-md-2">
                            <div class="d-grid gap-2">
                                <button class="btn btn-success btn-sm" onclick="aprobarUsuario(${user.id})">
                                    <i class="fas fa-check"></i> Aprobar
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="rechazarUsuario(${user.id})">
                                    <i class="fas fa-times"></i> Rechazar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        } else {
            container.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-user-check text-muted" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 text-muted">No hay usuarios pendientes de aprobación</h5>
                    <p class="text-muted">Todos los usuarios registrados han sido procesados.</p>
                </div>
            `;
        }
    } catch (error) {
        document.getElementById('usuariosPendientesContainer').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                Error al cargar usuarios pendientes: ${error.message}
            </div>
        `;
    }
}

async function aprobarUsuario(userId) {
    if (!confirm('¿Aprobar este usuario para que pueda iniciar sesión?')) return;

    const formData = new FormData();
    formData.append('action', 'approve_user');
    formData.append('user_id', userId);

    try {
        const response = await fetch('/Login-app/public/dashboard_api.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.success) {
            mostrarExito('Usuario aprobado correctamente');
            document.getElementById(`pending-user-${userId}`).remove();
            // Verificar si quedan usuarios pendientes
            if (document.querySelectorAll('[id^="pending-user-"]').length === 0) {
                cargarUsuariosPendientes();
            }
        } else {
            mostrarError('Error al aprobar usuario: ' + data.message);
        }
    } catch (error) {
        mostrarError('Error de conexión: ' + error.message);
    }
}

async function rechazarUsuario(userId) {
    if (!confirm('¿Rechazar este usuario? Será marcado como rechazado y no podrá iniciar sesión.')) return;

    const formData = new FormData();
    formData.append('action', 'reject_user');
    formData.append('user_id', userId);

    try {
        const response = await fetch('/Login-app/public/dashboard_api.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.success) {
            mostrarExito('Usuario rechazado');
            document.getElementById(`pending-user-${userId}`).remove();
            if (document.querySelectorAll('[id^="pending-user-"]').length === 0) {
                cargarUsuariosPendientes();
            }
        } else {
            mostrarError('Error al rechazar usuario: ' + data.message);
        }
    } catch (error) {
        mostrarError('Error de conexión: ' + error.message);
    }
}

// ================================
// FUNCIONES DE FILTRADO
// ================================

function filtrarUsuarios() {
    const search = document.getElementById('searchUsuarios').value.toLowerCase();
    const rolFilter = document.getElementById('filterRol').value;
    const statusFilter = document.getElementById('filterStatus').value;

    let usuariosFiltrados = todosLosUsuarios.filter(user => {
        const matchSearch = !search || 
            user.name?.toLowerCase().includes(search) ||
            user.username.toLowerCase().includes(search) ||
            user.email.toLowerCase().includes(search);
        
        const matchRol = !rolFilter || user.role === rolFilter;
        const matchStatus = !statusFilter || user.status === statusFilter;

        return matchSearch && matchRol && matchStatus;
    });

    mostrarUsuarios(usuariosFiltrados);
}

function limpiarFiltros() {
    document.getElementById('searchUsuarios').value = '';
    document.getElementById('filterRol').value = '';
    document.getElementById('filterStatus').value = '';
    mostrarUsuarios(todosLosUsuarios);
}

// ================================
// FUNCIONES AUXILIARES
// ================================

function getRoleBadgeClass(role) {
    switch (role) {
        case 'administrador': return 'bg-danger';
        case 'fiscalizador': return 'bg-primary';
        case 'inspector': return 'bg-success';
        case 'ventanilla': return 'bg-info';
        default: return 'bg-secondary';
    }
}

function getRoleDisplayName(role) {
    switch (role) {
        case 'administrador': return 'Administrador';
        case 'fiscalizador': return 'Fiscalizador';
        case 'inspector': return 'Inspector';
        case 'ventanilla': return 'Ventanilla';
        default: return role;
    }
}

function getStatusBadgeClass(status) {
    switch (status) {
        case 'approved': return 'bg-success';
        case 'pending': return 'bg-warning';
        case 'rejected': return 'bg-danger';
        case 'suspended': return 'bg-secondary';
        default: return 'bg-secondary';
    }
}

function getStatusDisplayName(status) {
    switch (status) {
        case 'approved': return 'Aprobado';
        case 'pending': return 'Pendiente';
        case 'rejected': return 'Rechazado';
        case 'suspended': return 'Suspendido';
        default: return status;
    }
}

function mostrarExito(mensaje) {
    // Implementar notificación de éxito (puedes usar toast, alert, etc.)
    alert('✅ ' + mensaje);
}

function mostrarError(mensaje) {
    // Implementar notificación de error
    alert('❌ ' + mensaje);
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}