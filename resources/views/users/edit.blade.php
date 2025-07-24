<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar usuario</title>
</head>
<body>
    @extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-warning text-dark">
            <h4>Editar Usuario</h4>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Nombre</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label">Usuario</label>
                    <input type="text" name="username" class="form-control" value="{{ old('username', $user->username) }}" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña (opcional)</label>
                    <input type="password" name="password" class="form-control">
                    <small class="text-muted">Déjelo vacío si no desea cambiar la contraseña.</small>
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Rol</label>
                    <select name="role" class="form-select" required>
                        <option value="administrador" {{ $user->role === 'administrador' ? 'selected' : '' }}>Administrador</option>
                        <option value="ventanilla" {{ $user->role === 'ventanilla' ? 'selected' : '' }}>Ventanilla</option>
                        <option value="fiscalizador" {{ $user->role === 'fiscalizador' ? 'selected' : '' }}>Fiscalizador</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-warning text-dark">Actualizar Usuario</button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection

</body>
</html>