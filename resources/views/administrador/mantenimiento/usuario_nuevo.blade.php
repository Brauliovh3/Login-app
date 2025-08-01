@extends('layouts.dashboard')

@section('title', 'Mantenimiento de Usuarios')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-users me-2" style="color: #ff8c00;"></i>
                    Mantenimiento de Usuarios
                </h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoUsuarioModal">
                    <i class="fas fa-plus me-2"></i>Nuevo Usuario
                </button>
            </div>
        </div>
    </div>

    <!-- Filtros de búsqueda -->
    <div class="card mb-4" style="border-color: #ff8c00;">
        <div class="card-header" style="background-color: #ff8c00; color: white;">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label for="filtro_username" class="form-label">Nombre de Usuario</label>
                    <input type="text" class="form-control" id="filtro_username" placeholder="usuario123">
                </div>
                <div class="col-md-3">
                    <label for="filtro_email" class="form-label">Email</label>
                    <input type="text" class="form-control" id="filtro_email" placeholder="usuario@drtc.gob.pe">
                </div>
                <div class="col-md-3">
                    <label for="filtro_rol" class="form-label">Rol</label>
                    <select class="form-select" id="filtro_rol">
                        <option value="">Todos</option>
                        <option value="administrador">Administrador</option>
                        <option value="fiscalizador">Fiscalizador</option>
                        <option value="ventanilla">Ventanilla</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtro_estado" class="form-label">Estado</label>
                    <select class="form-select" id="filtro_estado">
                        <option value="">Todos</option>
                        <option value="active">Activo</option>
                        <option value="blocked">Bloqueado</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <button class="btn btn-primary me-2" onclick="buscarUsuarios()">
                        <i class="fas fa-search me-2"></i>Buscar
                    </button>
                    <button class="btn btn-outline-secondary" onclick="limpiarFiltros()">
                        <i class="fas fa-times me-2"></i>Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de usuarios -->
    <div class="card">
        <div class="card-header" style="background-color: #fff3e0; border-color: #ff8c00;">
            <h5 class="mb-0" style="color: #ff8c00;">
                <i class="fas fa-list me-2"></i>Lista de Usuarios del Sistema
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead style="background-color: #ff8c00; color: white;">
                        <tr>
                            <th>ID</th>
                            <th>Nombre de Usuario</th>
                            <th>Nombre Completo</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Último Acceso</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="usuariosTableBody">
                        @forelse($users as $user)
                        <tr>
                            <td><strong>{{ $user->id }}</strong></td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge bg-{{ $user->role === 'administrador' ? 'danger' : ($user->role === 'fiscalizador' ? 'info' : 'primary') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td>{{ $user->updated_at ? $user->updated_at->format('d/m/Y H:i') : 'Nunca' }}</td>
                            <td>
                                <span class="badge bg-{{ $user->blocked_at ? 'danger' : 'success' }}">
                                    {{ $user->blocked_at ? 'Bloqueado' : 'Activo' }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" title="Ver perfil" onclick="verUsuario({{ $user->id }})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="Editar" onclick="editarUsuario({{ $user->id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning" title="Cambiar contraseña" onclick="cambiarPassword({{ $user->id }})">
                                    <i class="fas fa-key"></i>
                                </button>
                                @if($user->id !== auth()->id())
                                <button class="btn btn-sm btn-outline-{{ $user->blocked_at ? 'success' : 'danger' }}" 
                                        title="{{ $user->blocked_at ? 'Desbloquear' : 'Bloquear' }}" 
                                        onclick="toggleEstadoUsuario({{ $user->id }}, {{ $user->blocked_at ? 'true' : 'false' }})">
                                    <i class="fas fa-{{ $user->blocked_at ? 'unlock' : 'ban' }}"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No hay usuarios registrados</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nuevo Usuario -->
<div class="modal fade" id="nuevoUsuarioModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #ff8c00; color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Nuevo Usuario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="nuevoUsuarioForm">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nombre Completo *</label>
                            <input type="text" class="form-control" id="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="username" class="form-label">Nombre de Usuario *</label>
                            <input type="text" class="form-control" id="username" required>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                        <div class="col-md-6">
                            <label for="role" class="form-label">Rol *</label>
                            <select class="form-select" id="role" required>
                                <option value="">Seleccionar rol...</option>
                                <option value="administrador">Administrador</option>
                                <option value="fiscalizador">Fiscalizador</option>
                                <option value="ventanilla">Ventanilla</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label">Contraseña *</label>
                            <input type="password" class="form-control" id="password" required>
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Confirmar Contraseña *</label>
                            <input type="password" class="form-control" id="password_confirmation" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarUsuario()">
                    <i class="fas fa-save me-2"></i>Guardar Usuario
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ver Usuario -->
<div class="modal fade" id="verUsuarioModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #ff8c00; color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-eye me-2"></i>Datos del Usuario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="verUsuarioContent">
                <!-- Contenido se carga dinámicamente -->
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Usuario -->
<div class="modal fade" id="editarUsuarioModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #ff8c00; color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Editar Usuario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editarUsuarioForm">
                    <input type="hidden" id="edit_user_id">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="edit_name" class="form-label">Nombre Completo *</label>
                            <input type="text" class="form-control" id="edit_name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_username" class="form-label">Nombre de Usuario *</label>
                            <input type="text" class="form-control" id="edit_username" required>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="edit_email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="edit_email" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_role" class="form-label">Rol *</label>
                            <select class="form-select" id="edit_role" required>
                                <option value="administrador">Administrador</option>
                                <option value="fiscalizador">Fiscalizador</option>
                                <option value="ventanilla">Ventanilla</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="actualizarUsuario()">
                    <i class="fas fa-save me-2"></i>Actualizar Usuario
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cambiar Contraseña -->
<div class="modal fade" id="cambiarPasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #ff8c00; color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-key me-2"></i>Cambiar Contraseña
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="cambiarPasswordForm">
                    <input type="hidden" id="password_user_id">
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Nueva Contraseña *</label>
                        <input type="password" class="form-control" id="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Confirmar Contraseña *</label>
                        <input type="password" class="form-control" id="new_password_confirmation" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" onclick="actualizarPassword()">
                    <i class="fas fa-key me-2"></i>Cambiar Contraseña
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Función para guardar usuario
function guardarUsuario() {
    const formData = {
        name: document.getElementById('name').value,
        username: document.getElementById('username').value,
        email: document.getElementById('email').value,
        role: document.getElementById('role').value,
        password: document.getElementById('password').value,
        password_confirmation: document.getElementById('password_confirmation').value
    };

    if (!formData.name || !formData.username || !formData.email || !formData.role || !formData.password) {
        showError('Por favor complete todos los campos obligatorios');
        return;
    }

    if (formData.password !== formData.password_confirmation) {
        showError('Las contraseñas no coinciden');
        return;
    }

    fetch('/admin/users', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess('Usuario creado exitosamente');
            bootstrap.Modal.getInstance(document.getElementById('nuevoUsuarioModal')).hide();
            document.getElementById('nuevoUsuarioForm').reset();
            location.reload();
        } else {
            if (data.errors) {
                let errorMessage = 'Errores de validación:\n';
                Object.keys(data.errors).forEach(key => {
                    errorMessage += `${key}: ${data.errors[key].join(', ')}\n`;
                });
                showError(errorMessage);
            } else {
                showError(data.message || 'Error al crear el usuario');
            }
        }
    });
}

// Función para ver usuario
function verUsuario(id) {
    fetch(`/admin/users/${id}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const user = data.user;
            const content = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Información Personal</h6>
                        <p><strong>ID:</strong> ${user.id}</p>
                        <p><strong>Nombre:</strong> ${user.name}</p>
                        <p><strong>Usuario:</strong> ${user.username}</p>
                        <p><strong>Email:</strong> ${user.email}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Información del Sistema</h6>
                        <p><strong>Rol:</strong> <span class="badge bg-${user.role === 'administrador' ? 'danger' : (user.role === 'fiscalizador' ? 'info' : 'primary')}">${user.role}</span></p>
                        <p><strong>Estado:</strong> <span class="badge bg-${user.blocked_at ? 'danger' : 'success'}">${user.blocked_at ? 'Bloqueado' : 'Activo'}</span></p>
                        <p><strong>Creado:</strong> ${new Date(user.created_at).toLocaleDateString()}</p>
                        <p><strong>Última actualización:</strong> ${user.updated_at ? new Date(user.updated_at).toLocaleDateString() : 'Nunca'}</p>
                    </div>
                </div>
            `;
            document.getElementById('verUsuarioContent').innerHTML = content;
            new bootstrap.Modal(document.getElementById('verUsuarioModal')).show();
        }
    });
}

// Función para editar usuario
function editarUsuario(id) {
    fetch(`/admin/users/${id}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const user = data.user;
            document.getElementById('edit_user_id').value = user.id;
            document.getElementById('edit_name').value = user.name;
            document.getElementById('edit_username').value = user.username;
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_role').value = user.role;
            
            new bootstrap.Modal(document.getElementById('editarUsuarioModal')).show();
        }
    });
}

// Función para actualizar usuario
function actualizarUsuario() {
    const id = document.getElementById('edit_user_id').value;
    const formData = {
        name: document.getElementById('edit_name').value,
        username: document.getElementById('edit_username').value,
        email: document.getElementById('edit_email').value,
        role: document.getElementById('edit_role').value
    };

    fetch(`/admin/users/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess('Usuario actualizado exitosamente');
            bootstrap.Modal.getInstance(document.getElementById('editarUsuarioModal')).hide();
            location.reload();
        } else {
            showError(data.message || 'Error al actualizar el usuario');
        }
    });
}

// Función para cambiar contraseña
function cambiarPassword(id) {
    document.getElementById('password_user_id').value = id;
    new bootstrap.Modal(document.getElementById('cambiarPasswordModal')).show();
}

// Función para actualizar contraseña
function actualizarPassword() {
    const id = document.getElementById('password_user_id').value;
    const password = document.getElementById('new_password').value;
    const confirmation = document.getElementById('new_password_confirmation').value;

    if (password !== confirmation) {
        showError('Las contraseñas no coinciden');
        return;
    }

    fetch(`/admin/users/${id}/change-password`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            password: password,
            password_confirmation: confirmation
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess('Contraseña actualizada exitosamente');
            bootstrap.Modal.getInstance(document.getElementById('cambiarPasswordModal')).hide();
            document.getElementById('cambiarPasswordForm').reset();
        } else {
            showError(data.message || 'Error al cambiar la contraseña');
        }
    });
}

// Función para toggle estado usuario
function toggleEstadoUsuario(id, isBlocked) {
    const accion = isBlocked ? 'desbloquear' : 'bloquear';
    
    if (confirm(`¿Está seguro que desea ${accion} este usuario?`)) {
        fetch(`/admin/users/${id}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess('Estado actualizado exitosamente');
                location.reload();
            } else {
                showError(data.message || 'Error al cambiar el estado');
            }
        });
    }
}

// Función para buscar usuarios
function buscarUsuarios() {
    const filtros = {
        username: document.getElementById('filtro_username').value,
        email: document.getElementById('filtro_email').value,
        role: document.getElementById('filtro_rol').value,
        status: document.getElementById('filtro_estado').value
    };

    const params = new URLSearchParams();
    Object.keys(filtros).forEach(key => {
        if (filtros[key]) params.append(key, filtros[key]);
    });

    fetch(`/admin/users/search?${params.toString()}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            actualizarTablaUsuarios(data.users);
        }
    });
}

// Función para limpiar filtros
function limpiarFiltros() {
    document.getElementById('filtro_username').value = '';
    document.getElementById('filtro_email').value = '';
    document.getElementById('filtro_rol').value = '';
    document.getElementById('filtro_estado').value = '';
    location.reload();
}

// Función para actualizar tabla
function actualizarTablaUsuarios(users) {
    const tbody = document.getElementById('usuariosTableBody');
    tbody.innerHTML = '';
    
    if (users.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center">No se encontraron usuarios</td></tr>';
        return;
    }
    
    users.forEach(user => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><strong>${user.id}</strong></td>
            <td>${user.username}</td>
            <td>${user.name}</td>
            <td>${user.email}</td>
            <td><span class="badge bg-${user.role === 'administrador' ? 'danger' : (user.role === 'fiscalizador' ? 'info' : 'primary')}">${user.role}</span></td>
            <td>${user.updated_at ? new Date(user.updated_at).toLocaleDateString() : 'Nunca'}</td>
            <td><span class="badge bg-${user.blocked_at ? 'danger' : 'success'}">${user.blocked_at ? 'Bloqueado' : 'Activo'}</span></td>
            <td>
                <button class="btn btn-sm btn-outline-primary" title="Ver perfil" onclick="verUsuario(${user.id})">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-sm btn-outline-success" title="Editar" onclick="editarUsuario(${user.id})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-outline-warning" title="Cambiar contraseña" onclick="cambiarPassword(${user.id})">
                    <i class="fas fa-key"></i>
                </button>
                ${user.id !== {{ auth()->id() }} ? `<button class="btn btn-sm btn-outline-${user.blocked_at ? 'success' : 'danger'}" 
                        title="${user.blocked_at ? 'Desbloquear' : 'Bloquear'}" 
                        onclick="toggleEstadoUsuario(${user.id}, ${user.blocked_at ? 'true' : 'false'})">
                    <i class="fas fa-${user.blocked_at ? 'unlock' : 'ban'}"></i>
                </button>` : ''}
            </td>
        `;
        tbody.appendChild(tr);
    });
}
</script>
@endsection
