@extends('layouts.app')

@section('title', 'Iniciar Sesión')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-4">
            <div class="auth-container">
                <div class="auth-header">
                    <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                    <h2>Iniciar Sesión</h2>
                    <p class="text-muted">Ingresa tus credenciales para acceder</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <div class="form-group">
                        <label for="login" class="form-label">Usuario o Email</label>
                        <input id="login" 
                               type="text" 
                               class="form-control @error('login') is-invalid @enderror" 
                               name="login" 
                               value="{{ old('login') }}" 
                               required 
                               autocomplete="login" 
                               placeholder="Ingresa tu usuario o email"
                               autofocus>
                        @error('login')
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
                                   autocomplete="current-password"
                                   placeholder="Ingresa tu contraseña">
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
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                Recordarme
                            </label>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                        </button>
                    </div>
                </form>

                <div class="auth-links">
                    <p class="mb-2">¿No tienes cuenta?</p>
                    <a href="{{ route('register') }}" class="fw-bold">
                        <i class="fas fa-user-plus me-1"></i>Crear cuenta nueva
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
