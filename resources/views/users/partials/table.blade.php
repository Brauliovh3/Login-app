<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-dark">
            <tr>
                <th class="text-center" style="width: 60px;">
                    <i class="fas fa-hashtag"></i>
                </th>
                <th style="width: 200px;">
                    <i class="fas fa-user me-1"></i>Usuario
                </th>
                <th style="width: 150px;">
                    <i class="fas fa-at me-1"></i>Usuario/Login
                </th>
                <th>
                    <i class="fas fa-envelope me-1"></i>Email
                </th>
                <th class="text-center" style="width: 130px;">
                    <i class="fas fa-user-tag me-1"></i>Rol
                </th>
                <th class="text-center" style="width: 120px;">
                    <i class="fas fa-calendar me-1"></i>Registro
                </th>
                <th class="text-center" style="width: 120px;">
                    <i class="fas fa-cogs me-1"></i>Acciones
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr class="border-bottom">
                <td class="text-center">
                    <span class="badge bg-light text-dark">#{{ $user->id }}</span>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-md bg-gradient-primary rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm">
                            <span class="text-white fw-bold fs-5">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">{{ $user->name }}</div>
                            <small class="text-muted">ID: {{ $user->id }}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge bg-secondary bg-opacity-10 text-dark border px-3 py-2">
                        <i class="fas fa-user me-1"></i>{{ $user->username }}
                    </span>
                </td>
                <td>
                    <div class="text-dark">{{ $user->email }}</div>
                    <small class="text-muted">
                        <i class="fas fa-envelope me-1"></i>Correo principal
                    </small>
                </td>
                <td class="text-center">
                    @switch($user->role)
                        @case('administrador')
                            <span class="badge bg-danger bg-opacity-90 px-3 py-2">
                                <i class="fas fa-crown me-1"></i>Administrador
                            </span>
                            @break
                        @case('fiscalizador')
                            <span class="badge bg-warning text-dark px-3 py-2">
                                <i class="fas fa-clipboard-check me-1"></i>Fiscalizador
                            </span>
                            @break
                        @case('ventanilla')
                            <span class="badge bg-success px-3 py-2">
                                <i class="fas fa-window-maximize me-1"></i>Ventanilla
                            </span>
                            @break
                        @case('inspector')
                            <span class="badge bg-info px-3 py-2">
                                <i class="fas fa-search me-1"></i>Inspector
                            </span>
                            @break
                        @default
                            <span class="badge bg-secondary px-3 py-2">{{ ucfirst($user->role) }}</span>
                    @endswitch
                </td>
                <td class="text-center">
                    <div class="text-dark fw-semibold">{{ $user->created_at->format('d/m/Y') }}</div>
                    <small class="text-muted">{{ $user->created_at->format('H:i') }}</small>
                </td>
                <td class="text-center">
                    @if(auth()->user()->role === 'administrador')
                    <div class="btn-group shadow-sm" role="group" aria-label="Acciones de usuario">
                        <!-- Botón Ver Detalles -->
                        <button type="button" 
                                class="btn btn-outline-info btn-sm view-user" 
                                data-id="{{ $user->id }}"
                                data-bs-toggle="tooltip"
                                title="Ver detalles del usuario">
                            <i class="fas fa-eye"></i>
                        </button>
                        
                        <!-- Botón Editar -->
                        <button type="button" 
                                class="btn btn-outline-primary btn-sm edit-user" 
                                data-id="{{ $user->id }}"
                                data-bs-toggle="tooltip"
                                title="Editar usuario">
                            <i class="fas fa-edit"></i>
                        </button>
                        
                        <!-- Botón Cambiar Contraseña -->
                        <button type="button" 
                                class="btn btn-outline-warning btn-sm change-password" 
                                data-id="{{ $user->id }}"
                                data-name="{{ $user->name }}"
                                data-bs-toggle="tooltip"
                                title="Cambiar contraseña">
                            <i class="fas fa-key"></i>
                        </button>
                        
                        @if(auth()->user()->id !== $user->id)
                        <!-- Botón Bloquear/Desbloquear -->
                        <button type="button" 
                                class="btn btn-outline-{{ isset($user->blocked_at) ? 'success' : 'secondary' }} btn-sm toggle-status" 
                                data-id="{{ $user->id }}"
                                data-name="{{ $user->name }}"
                                data-status="{{ isset($user->blocked_at) ? 'blocked' : 'active' }}"
                                data-bs-toggle="tooltip"
                                title="{{ isset($user->blocked_at) ? 'Desbloquear usuario' : 'Bloquear usuario' }}">
                            <i class="fas fa-{{ isset($user->blocked_at) ? 'unlock' : 'user-lock' }}"></i>
                        </button>
                        
                        <!-- Botón Eliminar -->
                        <button type="button" 
                                class="btn btn-outline-danger btn-sm delete-user" 
                                data-id="{{ $user->id }}"
                                data-name="{{ $user->name }}"
                                data-bs-toggle="tooltip"
                                title="Eliminar usuario">
                            <i class="fas fa-trash"></i>
                        </button>
                        @else
                        <button type="button" 
                                class="btn btn-outline-secondary btn-sm" 
                                disabled
                                data-bs-toggle="tooltip"
                                title="No puedes realizar acciones sobre tu propia cuenta">
                            <i class="fas fa-lock"></i>
                        </button>
                        @endif
                    </div>
                    @else
                    <span class="text-muted d-flex align-items-center justify-content-center">
                        <i class="fas fa-lock me-1"></i>
                        <small>Sin permisos</small>
                    </span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-5">
                    <div class="d-flex flex-column align-items-center">
                        <div class="mb-3">
                            <i class="fas fa-users fa-4x text-muted opacity-50"></i>
                        </div>
                        <h5 class="text-muted mb-2">No hay usuarios registrados</h5>
                        <p class="text-muted mb-3">Los usuarios aparecerán aquí una vez que sean creados.</p>
                        @if(auth()->user()->role === 'administrador')
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                            <i class="fas fa-plus me-1"></i>Crear Primer Usuario
                        </button>
                        @endif
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($users->hasPages())
<div class="d-flex justify-content-center mt-4 px-3">
    <nav aria-label="Navegación de usuarios">
        {{ $users->appends(request()->query())->links('pagination::bootstrap-4') }}
    </nav>
</div>
@endif

<style>
.avatar-md {
    width: 48px;
    height: 48px;
    font-size: 1rem;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.table tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

.btn-group .btn {
    margin: 0 1px;
    border-radius: 6px !important;
}

.badge {
    font-size: 0.75rem;
    font-weight: 600;
}

.table th {
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}

.border-bottom {
    border-bottom: 1px solid #f8f9fa !important;
}
</style>
