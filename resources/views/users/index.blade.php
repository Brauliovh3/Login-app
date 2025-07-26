@extends('layouts.app')

@section('content')
<a href="{{ route('dashboard') }}" class="btn btn-secondary mb-3">
    <i class="fas fa-arrow-left"></i> Regresar al panel
</a>

<div class="container mt-4">
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Gestión de Usuarios</h4>
            @if(auth()->user()->role === 'administrador')
            <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#createUserModal">
                + Crear Usuario
            </button>
            @endif
        </div>

        <div class="card-body">
            <table class="table table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->role === 'administrador')
                            <span class="badge bg-primary">Administrador</span>
                            @elseif($user->role === 'ventanilla')
                            <span class="badge bg-success">Ventanilla</span>
                            @else
                            <span class="badge bg-warning text-dark">Fiscalizador</span>
                            @endif
                        </td>
                        {{--Boton y modal de confirmacion para eliminar usuario con clase Bootstrap--}}
                        <td class="text-center">
                            @if(auth()->user()->role === 'administrador')
                            <!-- Botón para abrir el modal de editar -->
                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal{{$user->id}}">
                                Editar
                            </button>

                            <!-- Modal de Editar Usuario -->
                            <div class="modal fade" id="editUserModal{{$user->id}}" tabindex="-1" aria-labelledby="editUserModalLabel{{$user->id}}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning text-dark">
                                            <h5 class="modal-title" id="editUserModalLabel{{$user->id}}">Editar Usuario</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                        </div>
                                        <form action="{{ route('users.update', $user->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="name{{$user->id}}" class="form-label">Nombre</label>
                                                    <input type="text" class="form-control" name="name" value="{{ $user->name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="email{{$user->id}}" class="form-label">Correo electrónico</label>
                                                    <input type="email" class="form-control" name="email" value="{{ $user->email }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="role{{$user->id}}" class="form-label">Rol</label>
                                                    <select class="form-select" name="role" required>
                                                        <option value="administrador" @if($user->role == 'administrador') selected @endif>Administrador</option>
                                                        <option value="ventanilla" @if($user->role == 'ventanilla') selected @endif>Ventanilla</option>
                                                        <option value="fiscalizador" @if($user->role == 'fiscalizador') selected @endif>Fiscalizador</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="username{{$user->id}}" class="form-label">Nombre de usuario</label>
                                                    <input type="text" class="form-control" name="username" value="{{ $user->username }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="password{{$user->id}}" class="form-label">Contraseña (dejar en blanco para no cambiar)</label>
                                                    <input type="password" class="form-control" name="password">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="password_confirmation{{$user->id}}" class="form-label">Confirmar contraseña</label>
                                                    <input type="password" class="form-control" name="password_confirmation">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-warning text-dark">Actualizar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Botón para abrir el modal -->
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{$user->id}}">
                                Eliminar
                            </button>

                            <!-- Modal de confirmación -->
                            <div class="modal fade" id="deleteModal{{$user->id}}" tabindex="-1" aria-labelledby="deleteModalLabel{{$user->id}}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title" id="deleteModalLabel{{$user->id}}">Confirmar eliminación</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                        </div>
                                        <div class="modal-body">
                                            ¿Estás seguro de que deseas eliminar al usuario <strong>{{ $user->name }}</strong>?
                                        </div>
                                        <div class="modal-footer">
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-danger">Eliminar</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @else
                            <span class="text-muted">Sin permisos</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="d-flex justify-content-center mt-3">
                {{ $users->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

<!-- Modal de Crear Usuario -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="createUserModalLabel">Crear Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo electrónico</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Rol</label>
                        <select class="form-select" name="role" required>
                            <option value="administrador">Administrador</option>
                            <option value="ventanilla">Ventanilla</option>
                            <option value="fiscalizador">Fiscalizador</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Nombre de usuario</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
                        <input type="password" class="form-control" name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection