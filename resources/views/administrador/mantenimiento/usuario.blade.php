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
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                        <option value="bloqueado">Bloqueado</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <button class="btn btn-primary me-2">
                        <i class="fas fa-search me-2"></i>Buscar
                    </button>
                    <button class="btn btn-outline-secondary">
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
                    <tbody>
                        <tr>
                            <td><strong>1</strong></td>
                            <td>admin</td>
                            <td>Administrador Sistema</td>
                            <td>admin@drtc.gob.pe</td>
                            <td><span class="badge bg-danger">Administrador</span></td>
                            <td>30/07/2025 10:30</td>
                            <td><span class="badge bg-success">Activo</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" title="Ver perfil">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning" title="Cambiar contraseña">
                                    <i class="fas fa-key"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>2</strong></td>
                            <td>jperez</td>
                            <td>Juan Carlos Pérez</td>
                            <td>juan.perez@drtc.gob.pe</td>
                            <td><span class="badge bg-info">Fiscalizador</span></td>
                            <td>30/07/2025 09:15</td>
                            <td><span class="badge bg-success">Activo</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" title="Ver perfil">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning" title="Cambiar contraseña">
                                    <i class="fas fa-key"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="Bloquear">
                                    <i class="fas fa-ban"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>3</strong></td>
                            <td>mlopez</td>
                            <td>María Elena López</td>
                            <td>maria.lopez@drtc.gob.pe</td>
                            <td><span class="badge bg-primary">Ventanilla</span></td>
                            <td>29/07/2025 16:45</td>
                            <td><span class="badge bg-success">Activo</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" title="Ver perfil">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning" title="Cambiar contraseña">
                                    <i class="fas fa-key"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="Bloquear">
                                    <i class="fas fa-ban"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>4</strong></td>
                            <td>rquispe</td>
                            <td>Roberto Carlos Quispe</td>
                            <td>roberto.quispe@drtc.gob.pe</td>
                            <td><span class="badge bg-info">Fiscalizador</span></td>
                            <td>28/07/2025 14:20</td>
                            <td><span class="badge bg-warning">Bloqueado</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" title="Ver perfil">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" title="Desbloquear">
                                    <i class="fas fa-unlock"></i>
                                </button>
                            </td>
                        </tr>
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
                    <i class="fas fa-plus me-2"></i>Nuevo Usuario del Sistema
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="nuevoUsuarioForm">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="username" class="form-label">Nombre de Usuario *</label>
                            <input type="text" class="form-control" id="username" required>
                            <small class="text-muted">Solo letras, números y guiones bajos</small>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nombre Completo *</label>
                            <input type="text" class="form-control" id="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="role" class="form-label">Rol del Usuario *</label>
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
                            <small class="text-muted">Mínimo 8 caracteres</small>
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Confirmar Contraseña *</label>
                            <input type="password" class="form-control" id="password_confirmation" required>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono">
                        </div>
                        <div class="col-md-6">
                            <label for="dni" class="form-label">DNI</label>
                            <input type="text" class="form-control" id="dni" maxlength="8">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="enviar_credenciales" checked>
                                <label class="form-check-label" for="enviar_credenciales">
                                    Enviar credenciales por email al usuario
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarUsuario()">
                    <i class="fas fa-save me-2"></i>Crear Usuario
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function guardarUsuario() {
    // Validar formulario
    const username = document.getElementById('username').value;
    const email = document.getElementById('email').value;
    const name = document.getElementById('name').value;
    const role = document.getElementById('role').value;
    const password = document.getElementById('password').value;
    const password_confirmation = document.getElementById('password_confirmation').value;
    
    if (!username || !email || !name || !role || !password || !password_confirmation) {
        showError('Por favor complete todos los campos obligatorios');
        return;
    }
    
    if (password !== password_confirmation) {
        showError('Las contraseñas no coinciden');
        return;
    }
    
    if (password.length < 8) {
        showError('La contraseña debe tener al menos 8 caracteres');
        return;
    }
    
    // Simular guardado
    showSuccess('Usuario creado exitosamente');
    
    // Cerrar modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('nuevoUsuarioModal'));
    modal.hide();
    
    // Limpiar formulario
    document.getElementById('nuevoUsuarioForm').reset();
}
</script>
@endsection
