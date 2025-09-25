<div class="row mb-3">
    <div class="col-12">
        <h4><i class="fas fa-user-check"></i> Aprobar Usuarios</h4>
        <p class="text-muted">Revisar y aprobar solicitudes de registro de usuarios</p>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-8">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5>Usuarios Pendientes de Aprobación</h5>
                        <h2 id="pendingUsersCount">-</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-clock fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <button class="btn btn-success w-100 h-100" onclick="loadPendingUsers()">
            <i class="fas fa-sync-alt"></i><br>
            Actualizar Lista
        </button>
    </div>
</div>

<div class="card shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Usuario</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol Solicitado</th>
                        <th>Fecha Registro</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="pendingUsersTable">
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="mt-2">Cargando usuarios pendientes...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de detalle de usuario -->
<div class="modal fade" id="userDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">👤 Detalle del Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="userDetailContent">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-danger" id="rejectUserBtn">
                    <i class="fas fa-times"></i> Rechazar
                </button>
                <button type="button" class="btn btn-success" id="approveUserBtn">
                    <i class="fas fa-check"></i> Aprobar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
window.init_admin_aprobar = function() {
    loadPendingUsers();
};

function loadPendingUsers() {
    fetch('/api/admin/pending-users')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('pendingUsersTable');
            const countElement = document.getElementById('pendingUsersCount');
            
            if (data.ok && data.users) {
                countElement.textContent = data.users.length;
                
                if (data.users.length > 0) {
                    tbody.innerHTML = data.users.map(user => `
                        <tr>
                            <td><strong>${user.username}</strong></td>
                            <td>${user.name}</td>
                            <td>${user.email || '-'}</td>
                            <td><span class="badge bg-primary">${user.role}</span></td>
                            <td>${formatDate(user.created_at)}</td>
                            <td>
                                <span class="badge bg-${getStatusColor(user.status)}">${user.status}</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-info" onclick="viewUserDetail(${user.id})" title="Ver Detalle">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-success" onclick="approveUser(${user.id})" title="Aprobar">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" onclick="rejectUser(${user.id})" title="Rechazar">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                <i class="fas fa-user-check"></i>
                                <p class="mt-2">No hay usuarios pendientes de aprobación</p>
                            </td>
                        </tr>
                    `;
                }
            } else {
                countElement.textContent = '0';
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center text-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p class="mt-2">Error cargando usuarios pendientes</p>
                        </td>
                    </tr>
                `;
            }
        })
        .catch(error => {
            document.getElementById('pendingUsersCount').textContent = 'Error';
            document.getElementById('pendingUsersTable').innerHTML = `
                <tr>
                    <td colspan="7" class="text-center text-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p class="mt-2">Error de conexión</p>
                    </td>
                </tr>
            `;
        });
}

function viewUserDetail(userId) {
    fetch(`/api/admin/users/${userId}/detail`)
        .then(response => response.json())
        .then(data => {
            if (data.ok && data.user) {
                const user = data.user;
                document.getElementById('userDetailContent').innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Información Básica</h6>
                            <table class="table table-sm">
                                <tr><td><strong>Usuario:</strong></td><td>${user.username}</td></tr>
                                <tr><td><strong>Nombre:</strong></td><td>${user.name}</td></tr>
                                <tr><td><strong>Email:</strong></td><td>${user.email || 'No especificado'}</td></tr>
                                <tr><td><strong>Rol:</strong></td><td><span class="badge bg-primary">${user.role}</span></td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Estado de Cuenta</h6>
                            <table class="table table-sm">
                                <tr><td><strong>Estado:</strong></td><td><span class="badge bg-${getStatusColor(user.status)}">${user.status}</span></td></tr>
                                <tr><td><strong>Registro:</strong></td><td>${formatDateTime(user.created_at)}</td></tr>
                                <tr><td><strong>Última actualización:</strong></td><td>${formatDateTime(user.updated_at)}</td></tr>
                            </table>
                        </div>
                    </div>
                    ${user.rejection_reason ? `
                    <div class="alert alert-warning mt-3">
                        <strong>Razón de rechazo anterior:</strong><br>
                        ${user.rejection_reason}
                    </div>
                    ` : ''}
                `;
                
                // Configurar botones del modal
                document.getElementById('approveUserBtn').onclick = () => approveUser(userId);
                document.getElementById('rejectUserBtn').onclick = () => rejectUser(userId);
                
                new bootstrap.Modal(document.getElementById('userDetailModal')).show();
            }
        })
        .catch(error => {
            showNotification('Error cargando detalle del usuario', 'error');
        });
}

function approveUser(userId) {
    if (confirm('¿Aprobar este usuario?')) {
        fetch(`/api/admin/users/${userId}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.ok) {
                showNotification('Usuario aprobado exitosamente', 'success');
                loadPendingUsers();
                
                // Cerrar modal si está abierto
                const modal = bootstrap.Modal.getInstance(document.getElementById('userDetailModal'));
                if (modal) modal.hide();
            } else {
                showNotification(data.message || 'Error aprobando usuario', 'error');
            }
        })
        .catch(error => {
            showNotification('Error de conexión', 'error');
        });
    }
}

function rejectUser(userId) {
    const reason = prompt('¿Razón del rechazo? (opcional)');
    
    if (reason !== null) { // null = cancelado, string vacío = sin razón
        fetch(`/api/admin/users/${userId}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.ok) {
                showNotification('Usuario rechazado', 'success');
                loadPendingUsers();
                
                // Cerrar modal si está abierto
                const modal = bootstrap.Modal.getInstance(document.getElementById('userDetailModal'));
                if (modal) modal.hide();
            } else {
                showNotification(data.message || 'Error rechazando usuario', 'error');
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
    return new Date(dateString).toLocaleDateString('es-ES');
}

function formatDateTime(dateString) {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleString('es-ES');
}
</script>