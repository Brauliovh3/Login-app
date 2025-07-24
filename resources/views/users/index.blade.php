<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creando usuarios</title>
</head>
<body>
@extends('layouts.app') {{-- Usa tu layout principal si lo tienes --}}

@section('content')
<div class="container mt-4">
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Gestión de Usuarios</h4>
            @if(auth()->user()->role === 'administrador')
                <a href="{{ route('users.create') }}" class="btn btn-light btn-sm">+ Crear Usuario</a>
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
                        <td class="text-center">
                            @if(auth()->user()->role === 'administrador')
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-warning">
                                    Editar
                                </a>
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este usuario?')">
                                        Eliminar
                                    </button>
                                </form>
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
@endsection

</body>
</html>