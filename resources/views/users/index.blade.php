@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="container-fluid py-4">
    <!-- Header con breadcrumb -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Gestión de Usuarios</li>
                        </ol>
                    </nav>
                    <h2 class="h3 mb-0">
                        <i class="fas fa-users text-primary me-2"></i>Gestión de Usuarios
                    </h2>
                </div>
                @if(auth()->user()->role === 'administrador')
                <button type="button" class="btn btn-primary btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#createUserModal">
                    <i class="fas fa-plus me-2"></i>Crear Usuario
                </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Filtros y búsqueda -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light border-0">
                    <h6 class="card-title mb-0 text-muted">
                        <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
                    </h6>
                </div>
                <div class="card-body">
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
                                <option value="ventanilla">🪟 Ventanilla</option>
                                <option value="inspector">🔍 Inspector</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-secondary w-100" id="clearFilters">
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
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-table me-2 text-muted"></i>Lista de Usuarios
                        </h5>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>Total: {{ $users->total() }} usuarios
                        </small>
                    </div>
                </div>
                <div class="card-body p-0">
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
            <div class="modal-header bg-primary text-white">
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
                                <option value="ventanilla">🪟 Ventanilla</option>
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
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="editUserModalLabel">
                    <i class="fas fa-user-edit me-2"></i>Editar Usuario
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                <option value="ventanilla">🪟 Ventanilla</option>
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
                    <button type="submit" class="btn btn-warning btn-lg text-dark">
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
    });
});
</script>
@endpush
