<div class="row mb-3">
    <div class="col-md-8">
        <h4><i class="fas fa-users-cog"></i> Gestión de Usuarios</h4>
        <p class="text-muted">Administrar usuarios del sistema</p>
    </div>
    <div class="col-md-4 text-end">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="fas fa-plus"></i> Crear Usuario
        </button>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-4">
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" class="form-control" id="userSearch" placeholder="Buscar usuarios...">
        </div>
    </div>
    <div class="col-md-3">
        <select class="form-select" id="roleFilter">
            <option value="">Todos los roles</option>
            <option value="administrador">Administrador</option>
            <option value="fiscalizador">Fiscalizador</option>
            <option value="ventanilla">Ventanilla</option>
            <option value="inspector">Inspector</option>
            <option value="superadmin">Super Admin</option>
        </select>
    </div>
    <div class="col-md-3">
        <select class="form-select" id="statusFilter">
            <option value="">Todos los estados</option>
            <option value="approved">Aprobado</option>
            <option value="pending">Pendiente</option>
            <option value="rejected">Rechazado</option>
        </select>
    </div>
    <div class="col-md-2">
        <button class="btn btn-primary w-100" onclick="loadUsers()">
            <i class="fas fa-sync-alt"></i> Filtrar
        </button>
    </div>
</div>

<div class="card shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    <tr>
                        <td colspan="8" class="text-center">
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

<!-- Modal Crear Usuario -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">➕ Crear Nuevo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createUserForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="newUserUsername" class="form-label">Usuario *</label>
                        <input type="text" class="form-control" id="newUserUsername" required>
                    </div>
                    <div class="mb-3">
                        <label for="newUserName" class="form-label">Nombre Completo *</label>
                        <input type="text" class="form-control" id="newUserName" required>
                    </div>
                    <div class="mb-3">
                        <label for="newUserEmail" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="newUserEmail" required>
                    </div>
                    <div class="mb-3">
                        <label for="newUserPassword" class="form-label">Contraseña *</label>
                        <input type="password" class="form-control" id="newUserPassword" required>
                    </div>
                    <div class="mb-3">
                        <label for="newUserRole" class="form-label">Rol *</label>
                        <select class="form-select" id="newUserRole" required>
                            <option value="">Seleccionar rol</option>
                            <option value="fiscalizador">Fiscalizador</option>
                            <option value="administrador">Administrador</option>
                            <option value="ventanilla">Ventanilla</option>
                            <option value="inspector">Inspector</option>
                            @if(auth()->user()->isSuperAdmin())
                            <option value="superadmin">Super Admin</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Crear Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Usuario -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">✏️ Editar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUserForm">
                <div class="modal-body">
                    <input type="hidden" id="editUserId">
                    <div class="mb-3">
                        <label for="editUserName" class="form-label">Nombre Completo</label>
                        <input type="text" class="form-control" id="editUserName">
                    </div>
                    <div class="mb-3">
                        <label for="editUserEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editUserEmail">
                    </div>
                    <div class="mb-3">
                        <label for="editUserRole" class="form-label">Rol</label>
                        <select class="form-select" id="editUserRole">
                            <option value="fiscalizador">Fiscalizador</option>
                            <option value="administrador">Administrador</option>
                            <option value="ventanilla">Ventanilla</option>
                            <option value="inspector">Inspector</option>
                            @if(auth()->user()->isSuperAdmin())
                            <option value="superadmin">Super Admin</option>
                            @endif
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editUserPassword" class="form-label">Nueva Contraseña (opcional)</label>
                        <input type="password" class="form-control" id="editUserPassword" placeholder="Dejar vacío para mantener actual">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
window.init_admin_usuarios = function() {
    loadUsers();
    
    // Event listeners
    document.getElementById('createUserForm').addEventListener('submit', handleCreateUser);
    document.getElementById('editUserForm').addEventListener('submit', handleEditUser);
    
    // Búsqueda en tiempo real
    document.getElementById('userSearch').addEventListener('input', function() {
        setTimeout(loadUsers, 300);
    });
};

function loadUsers() {
    const search = document.getElementById('userSearch').value;
    const roleFilter = document.getElementById('roleFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    
    const params = new URLSearchParams({
        search: search,
        role: roleFilter,
        status: statusFilter
    });
    
    fetch(`/api/admin/users?${params}`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('usersTableBody');
            
            if (data.ok && data.users && data.users.length > 0) {
                tbody.innerHTML = data.users.map(user => `
                    <tr>
                        <td>${user.id}</td>
                        <td><strong>${user.username}</strong></td>
                        <td>${user.name}</td>
                        <td>${user.email || '-'}</td>
                        <td><span class="badge bg-primary">${user.role}</span></td>
                        <td>
                            <span class="badge bg-${getStatusColor(user.status)}">${user.status}</span>
                        </td>
                        <td>${formatDate(user.created_at)}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" onclick="editUser(${user.id})" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-outline-warning" onclick="toggleUserStatus(${user.id})" title="Cambiar Estado">
                                    <i class="fas fa-toggle-on"></i>
                                </button>
                                ${user.id !== {{ auth()->user()->id }} ? `
                                <button class="btn btn-outline-danger" onclick="deleteUser(${user.id})" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                                ` : ''}
                            </div>
                        </td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            <i class="fas fa-users"></i>
                            <p class="mt-2">No se encontraron usuarios</p>
                        </td>
                    </tr>
                `;
            }
        })
        .catch(error => {
            document.getElementById('usersTableBody').innerHTML = `
                <tr>
                    <td colspan="8" class="text-center text-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p class="mt-2">Error cargando usuarios</p>
                    </td>
                </tr>
            `;
        });
}

function handleCreateUser(e) {
    e.preventDefault();
    
    const userData = {
        username: document.getElementById('newUserUsername').value,
        name: document.getElementById('newUserName').value,
        email: document.getElementById('newUserEmail').value,
        password: document.getElementById('newUserPassword').value,
        role: document.getElementById('newUserRole').value
    };
    
    fetch('/api/admin/users', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(userData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.ok) {
            showNotification('Usuario creado exitosamente', 'success');
            bootstrap.Modal.getInstance(document.getElementById('createUserModal')).hide();
            document.getElementById('createUserForm').reset();
            loadUsers();
        } else {
            showNotification(data.message || 'Error creando usuario', 'error');
        }
    })
    .catch(error => {
        showNotification('Error de conexión', 'error');
    });
}

function editUser(userId) {
    fetch(`/api/admin/users/${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.ok && data.user) {
                document.getElementById('editUserId').value = data.user.id;
                document.getElementById('editUserName').value = data.user.name;
                document.getElementById('editUserEmail').value = data.user.email || '';
                document.getElementById('editUserRole').value = data.user.role;
                
                new bootstrap.Modal(document.getElementById('editUserModal')).show();
            } else {
                showNotification('Error cargando datos del usuario', 'error');
            }
        })
        .catch(error => {
            showNotification('Error de conexión', 'error');
        });
}

function handleEditUser(e) {
    e.preventDefault();
    
    const userId = document.getElementById('editUserId').value;
    const userData = {
        name: document.getElementById('editUserName').value,
        email: document.getElementById('editUserEmail').value,
        role: document.getElementById('editUserRole').value
    };
    
    const password = document.getElementById('editUserPassword').value;
    if (password) {
        userData.password = password;
    }
    
    fetch(`/api/admin/users/${userId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(userData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.ok) {
            showNotification('Usuario actualizado exitosamente', 'success');
            bootstrap.Modal.getInstance(document.getElementById('editUserModal')).hide();
            loadUsers();
        } else {
            showNotification(data.message || 'Error actualizando usuario', 'error');
        }
    })
    .catch(error => {
        showNotification('Error de conexión', 'error');
    });
}

function toggleUserStatus(userId) {
    if (confirm('¿Cambiar estado del usuario?')) {
        fetch(`/api/admin/users/${userId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.ok) {
                showNotification('Estado del usuario actualizado', 'success');
                loadUsers();
            } else {
                showNotification(data.message || 'Error cambiando estado', 'error');
            }
        })
        .catch(error => {
            showNotification('Error de conexión', 'error');
        });
    }
}

function deleteUser(userId) {
    if (confirm('¿Estás seguro de eliminar este usuario?\n\nEsta acción no se puede deshacer.')) {
        fetch(`/api/admin/users/${userId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.ok) {
                showNotification('Usuario eliminado', 'success');
                loadUsers();
            } else {
                showNotification(data.message || 'Error eliminando usuario', 'error');
            }
        })
        .catch(error => {
            showNotification('Error de conexión', 'error');
        });
    }
}

function getStatusColor(status) {
    switch(status) {
        case 'approved': return 'success';
        case 'pending': return 'warning';
        case 'rejected': return 'danger';
        default: return 'secondary';
    }
}

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES');
}
</script>