@extends('layouts.dashboard')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="container-fluid py-4">
    <!-- Header con breadcrumb -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #ff8c00, #e67e22); border-radius: 12px;">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-2" style="background: none; padding: 0;">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" style="color: rgba(255,255,255,0.8); text-decoration: none;">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page" style="color: white;">Gestión de Usuarios</li>
                                </ol>
                            </nav>
                            <h2 class="h3 mb-0" style="color: white;">
                                <i class="fas fa-users me-2"></i>Gestión de Usuarios
                            </h2>
                        </div>
                        @if(auth()->user()->role === 'administrador')
                        <button type="button" class="btn btn-light btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#createUserModal" style="background: white; color: #ff8c00; border: none; font-weight: 600;">
                            <i class="fas fa-plus me-2"></i>Crear Usuario
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y búsqueda -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header border-0" style="background: linear-gradient(135deg, #ff8c00, #e67e22); color: white;">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
                    </h6>
                </div>
                <div class="card-body" style="background: linear-gradient(135deg, #fff4e6, #ffe4cc); border-radius: 0 0 12px 12px;">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label for="searchInput" class="form-label fw-semibold text-dark">Buscar usuarios</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Buscar por nombre, usuario, email...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="roleFilter" class="form-label fw-semibold text-dark">Filtrar por rol</label>
                            <select class="form-select" id="roleFilter">
                                <option value="">Todos los roles</option>
                                <option value="administrador">👑 Administrador</option>
                                <option value="fiscalizador">📋 Fiscalizador</option>
                                <option value="ventanilla">🏪 Ventanilla</option>
                                <option value="inspector">🔍 Inspector</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-warning w-100" id="clearFilters" style="border-color: #ff8c00; color: #ff8c00;">
                                <i class="fas fa-times me-1"></i>Limpiar Filtros
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de usuarios -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header border-0" style="background: linear-gradient(135deg, #ff8c00, #e67e22); color: white;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-table me-2"></i>Lista de Usuarios
                        </h5>
                        <small style="color: rgba(255, 255, 255, 0.9);">
                            <i class="fas fa-info-circle me-1"></i>Total: {{ $users->total() }} usuarios
                        </small>
                    </div>
                </div>
                <div class="card-body p-0" style="background: linear-gradient(135deg, #fff4e6, #ffe4cc); border-radius: 0 0 12px 12px;">
                    <div class="table-responsive">
                        <div id="usersTable">
                            @include('users.partials.table', ['users' => $users])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear Usuario -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #ff8c00, #e67e22);">
                <h5 class="modal-title" id="createUserModalLabel">
                    <i class="fas fa-user-plus me-2"></i>Crear Nuevo Usuario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createUserForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="create_name" class="form-label fw-semibold">
                                <i class="fas fa-user me-1 text-muted"></i>Nombre completo <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg" id="create_name" name="name" required placeholder="Ingrese el nombre completo">
                            <div class="invalid-feedback" id="create_name_error"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="create_username" class="form-label fw-semibold">
                                <i class="fas fa-at me-1 text-muted"></i>Nombre de usuario <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg" id="create_username" name="username" required placeholder="Ingrese el usuario">
                            <div class="invalid-feedback" id="create_username_error"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="create_email" class="form-label fw-semibold">
                                <i class="fas fa-envelope me-1 text-muted"></i>Correo electrónico <span class="text-danger">*</span>
                            </label>
                            <input type="email" class="form-control form-control-lg" id="create_email" name="email" required placeholder="usuario@dominio.com">
                            <div class="invalid-feedback" id="create_email_error"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="create_role" class="form-label fw-semibold">
                                <i class="fas fa-user-tag me-1 text-muted"></i>Rol del sistema <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-lg" id="create_role" name="role" required>
                                <option value="">Seleccionar rol...</option>
                                <option value="administrador">👑 Administrador</option>
                                <option value="fiscalizador">📋 Fiscalizador</option>
                                <option value="ventanilla">🏪 Ventanilla</option>
                                <option value="inspector">🔍 Inspector</option>
                            </select>
                            <div class="invalid-feedback" id="create_role_error"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="create_password" class="form-label fw-semibold">
                                <i class="fas fa-lock me-1 text-muted"></i>Contraseña <span class="text-danger">*</span>
                            </label>
                            <input type="password" class="form-control form-control-lg" id="create_password" name="password" required placeholder="Mínimo 8 caracteres">
                            <div class="invalid-feedback" id="create_password_error"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="create_password_confirmation" class="form-label fw-semibold">
                                <i class="fas fa-lock me-1 text-muted"></i>Confirmar contraseña <span class="text-danger">*</span>
                            </label>
                            <input type="password" class="form-control form-control-lg" id="create_password_confirmation" name="password_confirmation" required placeholder="Repetir contraseña">
                            <div class="invalid-feedback" id="create_password_confirmation_error"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-1"></i>Crear Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Usuario -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #ff8c00, #e67e22);">
                <h5 class="modal-title" id="editUserModalLabel">
                    <i class="fas fa-user-edit me-2"></i>Editar Usuario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_user_id" name="user_id">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_name" class="form-label fw-semibold">
                                <i class="fas fa-user me-1 text-muted"></i>Nombre completo <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg" id="edit_name" name="name" required>
                            <div class="invalid-feedback" id="edit_name_error"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_username" class="form-label fw-semibold">
                                <i class="fas fa-at me-1 text-muted"></i>Nombre de usuario <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg" id="edit_username" name="username" required>
                            <div class="invalid-feedback" id="edit_username_error"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_email" class="form-label fw-semibold">
                                <i class="fas fa-envelope me-1 text-muted"></i>Correo electrónico <span class="text-danger">*</span>
                            </label>
                            <input type="email" class="form-control form-control-lg" id="edit_email" name="email" required>
                            <div class="invalid-feedback" id="edit_email_error"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_role" class="form-label fw-semibold">
                                <i class="fas fa-user-tag me-1 text-muted"></i>Rol del sistema <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-lg" id="edit_role" name="role" required>
                                <option value="administrador">👑 Administrador</option>
                                <option value="fiscalizador">📋 Fiscalizador</option>
                                <option value="ventanilla">🏪 Ventanilla</option>
                                <option value="inspector">🔍 Inspector</option>
                            </select>
                            <div class="invalid-feedback" id="edit_role_error"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_password" class="form-label fw-semibold">
                                <i class="fas fa-lock me-1 text-muted"></i>Nueva contraseña
                            </label>
                            <input type="password" class="form-control form-control-lg" id="edit_password" name="password" placeholder="Dejar vacío para mantener actual">
                            <small class="text-muted">Dejar vacío para no cambiar la contraseña</small>
                            <div class="invalid-feedback" id="edit_password_error"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_password_confirmation" class="form-label fw-semibold">
                                <i class="fas fa-lock me-1 text-muted"></i>Confirmar nueva contraseña
                            </label>
                            <input type="password" class="form-control form-control-lg" id="edit_password_confirmation" name="password_confirmation" placeholder="Repetir nueva contraseña">
                            <div class="invalid-feedback" id="edit_password_confirmation_error"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-lg text-white" style="background: linear-gradient(135deg, #ff8c00, #e67e22);">
                        <i class="fas fa-save me-1"></i>Actualizar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Confirmar Eliminación -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteUserModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="fas fa-user-times fa-3x text-danger mb-3"></i>
                </div>
                <h6 class="mb-2">¿Estás seguro de eliminar este usuario?</h6>
                <p class="mb-0">Usuario: <strong id="deleteUserName" class="text-danger"></strong></p>
                <p class="text-muted mt-2 mb-0">
                    <i class="fas fa-exclamation-triangle me-1"></i>Esta acción no se puede deshacer.
                </p>
            </div>
            <div class="modal-footer bg-light justify-content-center">
                <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-danger btn-lg" id="confirmDeleteUser">
                    <i class="fas fa-trash me-1"></i>Sí, Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ver Detalles del Usuario -->
<div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #ff8c00, #e67e22);">
                <h5 class="modal-title" id="viewUserModalLabel">
                    <i class="fas fa-user me-2"></i>Detalles del Usuario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="info-group">
                            <label class="form-label fw-bold text-muted">
                                <i class="fas fa-user me-1"></i>Nombre Completo
                            </label>
                            <p class="form-control-plaintext border rounded p-2 bg-light" id="view_name">-</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-group">
                            <label class="form-label fw-bold text-muted">
                                <i class="fas fa-at me-1"></i>Nombre de Usuario
                            </label>
                            <p class="form-control-plaintext border rounded p-2 bg-light" id="view_username">-</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-group">
                            <label class="form-label fw-bold text-muted">
                                <i class="fas fa-envelope me-1"></i>Correo Electrónico
                            </label>
                            <p class="form-control-plaintext border rounded p-2 bg-light" id="view_email">-</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-group">
                            <label class="form-label fw-bold text-muted">
                                <i class="fas fa-user-tag me-1"></i>Rol del Sistema
                            </label>
                            <p class="form-control-plaintext border rounded p-2 bg-light" id="view_role">-</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-group">
                            <label class="form-label fw-bold text-muted">
                                <i class="fas fa-calendar-plus me-1"></i>Fecha de Registro
                            </label>
                            <p class="form-control-plaintext border rounded p-2 bg-light" id="view_created_at">-</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-group">
                            <label class="form-label fw-bold text-muted">
                                <i class="fas fa-clock me-1"></i>Última Actualización
                            </label>
                            <p class="form-control-plaintext border rounded p-2 bg-light" id="view_updated_at">-</p>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="info-group">
                            <label class="form-label fw-bold text-muted">
                                <i class="fas fa-shield-alt me-1"></i>Estado del Usuario
                            </label>
                            <p class="form-control-plaintext border rounded p-2 bg-light" id="view_status">-</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cambiar Contraseña -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="changePasswordModalLabel">
                    <i class="fas fa-key me-2"></i>Cambiar Contraseña
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="changePasswordForm">
                @csrf
                <input type="hidden" id="change_password_user_id" name="user_id">
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-circle fa-3x text-warning mb-2"></i>
                        <h6 class="mb-0">Usuario: <strong id="changePasswordUserName" class="text-dark"></strong></h6>
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="change_new_password" class="form-label fw-semibold">
                                <i class="fas fa-lock me-1 text-muted"></i>Nueva Contraseña <span class="text-danger">*</span>
                            </label>
                            <input type="password" class="form-control form-control-lg" id="change_new_password" name="password" required placeholder="Mínimo 8 caracteres">
                            <div class="invalid-feedback" id="change_password_error"></div>
                        </div>
                        <div class="col-12">
                            <label for="change_password_confirmation" class="form-label fw-semibold">
                                <i class="fas fa-lock me-1 text-muted"></i>Confirmar Nueva Contraseña <span class="text-danger">*</span>
                            </label>
                            <input type="password" class="form-control form-control-lg" id="change_password_confirmation" name="password_confirmation" required placeholder="Repetir la nueva contraseña">
                            <div class="invalid-feedback" id="change_password_confirmation_error"></div>
                        </div>
                    </div>
                    <div class="alert alert-info mt-3" role="alert">
                        <i class="fas fa-info-circle me-1"></i>
                        <strong>Nota:</strong> La nueva contraseña debe tener al menos 8 caracteres.
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning btn-lg text-dark">
                        <i class="fas fa-key me-1"></i>Cambiar Contraseña
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Cambiar Estado Usuario -->
<div class="modal fade" id="toggleStatusModal" tabindex="-1" aria-labelledby="toggleStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" id="toggleStatusHeader">
                <h5 class="modal-title" id="toggleStatusModalLabel">
                    <i class="fas fa-user-lock me-2"></i>Cambiar Estado
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i id="toggleStatusIcon" class="fa-3x mb-3"></i>
                </div>
                <h6 class="mb-2" id="toggleStatusQuestion">¿Confirmar acción?</h6>
                <p class="mb-0">Usuario: <strong id="toggleStatusUserName" class="text-primary"></strong></p>
                <div class="alert mt-3" role="alert" id="toggleStatusAlert">
                    <i class="fas fa-info-circle me-1"></i>
                    <span id="toggleStatusMessage">Esta acción cambiará el estado del usuario.</span>
                </div>
            </div>
            <div class="modal-footer bg-light justify-content-center">
                <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-lg" id="confirmToggleStatus">
                    <i class="fas fa-check me-1"></i>Confirmar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let deleteUserId = null;
    let searchTimeout = null;

    // Búsqueda en tiempo real
    $('#searchInput').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadUsers();
        }, 500);
    });

    // Filtro por rol
    $('#roleFilter').on('change', function() {
        loadUsers();
    });

    // Limpiar filtros
    $('#clearFilters').on('click', function() {
        $('#searchInput').val('');
        $('#roleFilter').val('');
        loadUsers();
    });

    // Función para cargar usuarios
    function loadUsers() {
        const search = $('#searchInput').val();
        const roleFilter = $('#roleFilter').val();
        
        $.ajax({
            url: '{{ route("users.index") }}',
            type: 'GET',
            data: {
                search: search,
                role_filter: roleFilter
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                $('#usersTable').html(response);
            },
            error: function() {
                showAlert('Error al cargar los usuarios', 'error');
            }
        });
    }

    // Crear usuario
    $('#createUserForm').on('submit', function(e) {
        e.preventDefault();
        clearErrors('create');
        
        $.ajax({
            url: '{{ route("users.store") }}',
            type: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    $('#createUserModal').modal('hide');
                    $('#createUserForm')[0].reset();
                    loadUsers();
                    showAlert(response.message, 'success');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    showErrors(xhr.responseJSON.errors, 'create');
                } else {
                    showAlert('Error al crear el usuario', 'error');
                }
            }
        });
    });

    // Editar usuario
    $(document).on('click', '.edit-user', function() {
        const userId = $(this).data('id');
        
        $.ajax({
            url: `/users/${userId}/edit`,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    const user = response.user;
                    $('#edit_user_id').val(user.id);
                    $('#edit_name').val(user.name);
                    $('#edit_username').val(user.username);
                    $('#edit_email').val(user.email);
                    $('#edit_role').val(user.role);
                    $('#editUserModal').modal('show');
                }
            },
            error: function() {
                showAlert('Error al cargar los datos del usuario', 'error');
            }
        });
    });

    // Actualizar usuario
    $('#editUserForm').on('submit', function(e) {
        e.preventDefault();
        clearErrors('edit');
        
        const userId = $('#edit_user_id').val();
        
        $.ajax({
            url: `/users/${userId}`,
            type: 'PUT',
            data: $(this).serialize(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    $('#editUserModal').modal('hide');
                    loadUsers();
                    showAlert(response.message, 'success');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    showErrors(xhr.responseJSON.errors, 'edit');
                } else {
                    showAlert('Error al actualizar el usuario', 'error');
                }
            }
        });
    });

    // Confirmar eliminación
    $(document).on('click', '.delete-user', function() {
        deleteUserId = $(this).data('id');
        const userName = $(this).data('name');
        $('#deleteUserName').text(userName);
        $('#deleteUserModal').modal('show');
    });

    // Eliminar usuario
    $('#confirmDeleteUser').on('click', function() {
        if (deleteUserId) {
            $.ajax({
                url: `/users/${deleteUserId}`,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.success) {
                        $('#deleteUserModal').modal('hide');
                        loadUsers();
                        showAlert(response.message, 'success');
                    }
                },
                error: function(xhr) {
                    $('#deleteUserModal').modal('hide');
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        showAlert(xhr.responseJSON.message, 'error');
                    } else {
                        showAlert('Error al eliminar el usuario', 'error');
                    }
                }
            });
        }
    });

    // Función para mostrar errores
    function showErrors(errors, prefix) {
        Object.keys(errors).forEach(function(field) {
            const errorElement = $(`#${prefix}_${field}_error`);
            const inputElement = $(`#${prefix}_${field}`);
            
            errorElement.text(errors[field][0]);
            inputElement.addClass('is-invalid');
        });
    }

    // Función para limpiar errores
    function clearErrors(prefix) {
        $(`.invalid-feedback[id^="${prefix}_"]`).text('');
        $(`.form-control[id^="${prefix}_"], .form-select[id^="${prefix}_"]`).removeClass('is-invalid');
    }

    // Función para mostrar alertas
    function showAlert(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
        
        const alert = `
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
                <i class="${icon} me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        $('body').append(alert);
        
        // Auto-remove after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }

    // Limpiar modales al cerrar
    $('.modal').on('hidden.bs.modal', function() {
        $(this).find('form')[0].reset();
        clearErrors('create');
        clearErrors('edit');
        clearErrors('change');
    });

    // ===== NUEVAS FUNCIONALIDADES =====

    // Ver detalles del usuario
    $(document).on('click', '.view-user', function() {
        const userId = $(this).data('id');
        
        $.ajax({
            url: `/users/${userId}/edit`,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    const user = response.user;
                    
                    $('#view_name').text(user.name);
                    $('#view_username').text(user.username);
                    $('#view_email').text(user.email);
                    
                    // Mostrar rol con icono
                    let roleText = '';
                    switch(user.role) {
                        case 'administrador':
                            roleText = '👑 Administrador';
                            break;
                        case 'fiscalizador':
                            roleText = '📋 Fiscalizador';
                            break;
                        case 'ventanilla':
                            roleText = '🏪 Ventanilla';
                            break;
                        case 'inspector':
                            roleText = '🔍 Inspector';
                            break;
                        default:
                            roleText = user.role;
                    }
                    $('#view_role').text(roleText);
                    
                    $('#view_created_at').text(new Date(user.created_at).toLocaleString());
                    $('#view_updated_at').text(new Date(user.updated_at).toLocaleString());
                    $('#view_status').html('<span class="badge bg-success">Activo</span>');
                    
                    $('#viewUserModal').modal('show');
                }
            },
            error: function(xhr) {
                showAlert('Error al cargar los detalles del usuario', 'error');
            }
        });
    });

    // Cambiar contraseña
    $(document).on('click', '.change-password', function() {
        const userId = $(this).data('id');
        const userName = $(this).data('name');
        
        $('#change_password_user_id').val(userId);
        $('#changePasswordUserName').text(userName);
        $('#changePasswordModal').modal('show');
    });

    // Enviar formulario de cambio de contraseña
    $('#changePasswordForm').on('submit', function(e) {
        e.preventDefault();
        clearErrors('change');
        
        const userId = $('#change_password_user_id').val();
        const formData = {
            password: $('#change_new_password').val(),
            password_confirmation: $('#change_password_confirmation').val(),
            _token: '{{ csrf_token() }}'
        };
        
        $.ajax({
            url: `/users/${userId}/change-password`,
            type: 'PUT',
            data: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    $('#changePasswordModal').modal('hide');
                    showAlert(response.message, 'success');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    showErrors(xhr.responseJSON.errors, 'change');
                } else {
                    showAlert('Error al cambiar la contraseña', 'error');
                }
            }
        });
    });

    // Cambiar estado del usuario (bloquear/desbloquear)
    let toggleUserId = null;
    let toggleAction = null;
    
    $(document).on('click', '.toggle-status', function() {
        toggleUserId = $(this).data('id');
        const userName = $(this).data('name');
        const currentStatus = $(this).data('status');
        
        $('#toggleStatusUserName').text(userName);
        
        if (currentStatus === 'blocked') {
            // Usuario está bloqueado, mostrar opción para desbloquear
            toggleAction = 'unblock';
            $('#toggleStatusHeader').removeClass('bg-warning').addClass('bg-success text-white');
            $('#toggleStatusIcon').removeClass().addClass('fas fa-unlock text-success');
            $('#toggleStatusQuestion').text('¿Desbloquear este usuario?');
            $('#toggleStatusMessage').text('El usuario podrá acceder nuevamente al sistema.');
            $('#toggleStatusAlert').removeClass('alert-warning').addClass('alert-success');
            $('#confirmToggleStatus').removeClass('btn-warning').addClass('btn-success').html('<i class="fas fa-unlock me-1"></i>Desbloquear');
        } else {
            // Usuario está activo, mostrar opción para bloquear
            toggleAction = 'block';
            $('#toggleStatusHeader').removeClass('bg-success').addClass('bg-warning text-dark');
            $('#toggleStatusIcon').removeClass().addClass('fas fa-user-lock text-warning');
            $('#toggleStatusQuestion').text('¿Bloquear este usuario?');
            $('#toggleStatusMessage').text('El usuario no podrá acceder al sistema hasta ser desbloqueado.');
            $('#toggleStatusAlert').removeClass('alert-success').addClass('alert-warning');
            $('#confirmToggleStatus').removeClass('btn-success').addClass('btn-warning text-dark').html('<i class="fas fa-user-lock me-1"></i>Bloquear');
        }
        
        $('#toggleStatusModal').modal('show');
    });

    // Confirmar cambio de estado
    $('#confirmToggleStatus').on('click', function() {
        if (toggleUserId && toggleAction) {
            $.ajax({
                url: `/users/${toggleUserId}/toggle-status`,
                type: 'PUT',
                data: {
                    action: toggleAction,
                    _token: '{{ csrf_token() }}'
                },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.success) {
                        $('#toggleStatusModal').modal('hide');
                        loadUsers();
                        showAlert(response.message, 'success');
                    }
                },
                error: function(xhr) {
                    $('#toggleStatusModal').modal('hide');
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        showAlert(xhr.responseJSON.message, 'error');
                    } else {
                        showAlert('Error al cambiar el estado del usuario', 'error');
                    }
                }
            });
        }
    });
});
</script>

<style>
/* Estilos adicionales para la gestión de usuarios con tema DRTC naranja */
.btn-group .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    transition: all 0.2s ease;
}

.btn[style*="border-color: #ff8c00"]:hover {
    background: linear-gradient(135deg, #ff8c00, #e67e22) !important;
    color: white !important;
    border-color: #ff8c00 !important;
}

.btn[style*="border-color: #17a2b8"]:hover {
    background-color: #17a2b8 !important;
    color: white !important;
}

.btn[style*="border-color: #ffc107"]:hover {
    background-color: #ffc107 !important;
    color: #212529 !important;
}

.btn-outline-warning:hover {
    background: linear-gradient(135deg, #ff8c00, #e67e22) !important;
    border-color: #ff8c00 !important;
    color: white !important;
}

/* Mejorar la tabla */
.table tbody tr:hover {
    background-color: rgba(255, 140, 0, 0.05) !important;
    transform: scale(1.001);
    transition: all 0.2s ease;
}

/* Avatar hover effect */
.avatar-md:hover {
    transform: scale(1.1);
    transition: transform 0.2s ease;
}

/* Card hover effects */
.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(255, 140, 0, 0.15);
}

/* Input focus con tema naranja */
.form-control:focus, .form-select:focus {
    border-color: #ff8c00;
    box-shadow: 0 0 0 0.2rem rgba(255, 140, 0, 0.25);
}
</style>
@endpush
