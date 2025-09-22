@extends('layouts.dashboard')

@section('title', 'Gestionar Usuarios')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-users-cog me-2" style="color: #ff8c00;"></i>
                    Gestionar Usuarios
                </h2>
                <button class="btn btn-primary">
                    <i class="fas fa-user-plus me-2"></i>Nuevo Usuario
                </button>
            </div>
        </div>
    </div>

    <!-- Filtros de Búsqueda -->
    <div class="card mb-4" style="border-left: 4px solid #ff8c00;">
        <div class="card-header" style="background-color: #ff8c00; color: white;">
            <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label for="buscar_usuario" class="form-label">Usuario/Email</label>
                    <input type="text" class="form-control" id="buscar_usuario" placeholder="Buscar por usuario o email">
                </div>
                <div class="col-md-3">
                    <label for="rol_filtro" class="form-label">Rol</label>
                    <select class="form-select" id="rol_filtro">
                        <option value="">Todos los roles</option>
                        <option value="administrador">Administrador</option>
                        <option value="fiscalizador">Fiscalizador</option>
                        <option value="inspector">Inspector</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="estado_filtro" class="form-label">Estado</label>
                    <select class="form-select" id="estado_filtro">
                        <option value="">Todos</option>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                        <option value="bloqueado">Bloqueado</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-warning me-2">
                        <i class="fas fa-search me-1"></i>Buscar
                    </button>
                    <button class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Usuarios -->
    <div class="card">
        <div class="card-header" style="background-color: #ff8c00; color: white;">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Lista de Usuarios
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background-color: #ff8c00; color: white;">
                        <tr>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Nombres</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Último Acceso</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="usuariosTableBody">
                        @forelse($usuarios ?? [] as $usuario)
                        <tr>
                            <td><strong>{{ $usuario->username ?? $usuario->email }}</strong></td>
                            <td>{{ $usuario->email }}</td>
                            <td>{{ $usuario->name ?? 'No especificado' }}</td>
                            <td>
                                <span class="badge bg-info">{{ ucfirst($usuario->role ?? 'Sin rol') }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $usuario->status === 'active' ? 'success' : 'danger' }}">
                                    {{ $usuario->status === 'active' ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>{{ $usuario->updated_at ? $usuario->updated_at->format('d/m/Y H:i') : 'Nunca' }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" title="Ver perfil">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="Bloquear/Desbloquear">
                                    <i class="fas fa-{{ $usuario->status === 'active' ? 'lock' : 'unlock' }}"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay usuarios registrados</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.table th {
    border-top: none;
    font-weight: 600;
}

.badge {
    font-size: 0.75em;
}

.btn-outline-primary:hover {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.btn-outline-success:hover {
    background-color: #198754;
    border-color: #198754;
}

.btn-outline-danger:hover {
    background-color: #dc3545;
    border-color: #dc3545;
}
</style>
@endsection