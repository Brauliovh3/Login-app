@extends('layouts.app')

@section('title', 'Registro')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-4">
            <div class="auth-container">
                <div class="auth-header">
                    <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
                    <h2>Crear Cuenta</h2>
                    <p class="text-muted">Completa los datos para registrarte</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    
                    <div class="form-group">
                        <label for="username" class="form-label">Nombre de Usuario</label>
                        <input id="username" 
                               type="text" 
                               class="form-control @error('username') is-invalid @enderror" 
                               name="username" 
                               value="{{ old('username') }}" 
                               required 
                               autocomplete="username" 
                               placeholder="Elige un nombre de usuario único"
                               autofocus>
                        @error('username')
                            <div class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input id="email" 
                               type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autocomplete="email"
                               placeholder="tu@ejemplo.com">
                        @error('email')
                            <div class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="position-relative">
                            <input id="password" 
                                   type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   name="password" 
                                   required 
                                   autocomplete="new-password"
                                   placeholder="Mínimo 8 caracteres">
                            <button type="button" 
                                    class="password-toggle" 
                                    onclick="togglePassword('password', 'password-icon')">
                                <i id="password-icon" class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password-confirm" class="form-label">Confirmar Contraseña</label>
                        <div class="position-relative">
                            <input id="password-confirm" 
                                   type="password" 
                                   class="form-control" 
                                   name="password_confirmation" 
                                   required 
                                   autocomplete="new-password"
                                   placeholder="Repite la contraseña">
                            <button type="button" 
                                    class="password-toggle" 
                                    onclick="togglePassword('password-confirm', 'password-confirm-icon')">
                                <i id="password-confirm-icon" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="role" class="form-label">Rol</label>
                        <select id="role" 
                                class="form-control @error('role') is-invalid @enderror" 
                                name="role" 
                                required>
                            <option value="">Selecciona tu rol</option>
                            <option value="administrador" {{ old('role') == 'administrador' ? 'selected' : '' }}>
                                <i class="fas fa-crown"></i> Administrador
                            </option>
                            <option value="fiscalizador" {{ old('role') == 'fiscalizador' ? 'selected' : '' }}>
                                <i class="fas fa-search"></i> Fiscalizador
                            </option>
                            <option value="ventanilla" {{ old('role') == 'ventanilla' ? 'selected' : '' }}>
                                <i class="fas fa-desktop"></i> Ventanilla
                            </option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>

                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-user-plus me-2"></i>Crear Cuenta
                        </button>
                    </div>
                </form>

                <div class="auth-links">
                    <p class="mb-2">¿Ya tienes cuenta?</p>
                    <a href="{{ route('login') }}" class="fw-bold">
                        <i class="fas fa-sign-in-alt me-1"></i>Iniciar sesión
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
