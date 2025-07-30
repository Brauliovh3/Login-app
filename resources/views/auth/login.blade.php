@extends('layouts.app')

@section('title', 'Iniciar Sesión')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-4">
            <div class="auth-container">
                <div class="auth-header">
                    <div style="background: linear-gradient(135deg, var(--drtc-orange), var(--drtc-dark-orange)); border-radius: 50%; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; font-weight: bold; margin: 0 auto 20px; box-shadow: 0 4px 15px rgba(255, 140, 0, 0.3);">
                        <div class="text-center">
                            <i class="fas fa-road"></i>
                            <div style="font-size: 10px; line-height: 1;">DRTC</div>
                        </div>
                    </div>
                    <h2>SISTEMA DRTC APURÍMAC</h2>
                    <p class="text-muted">Dirección Regional de Transportes y Comunicaciones</p>
                    <h5 class="text-muted">Iniciar Sesión</h5>
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
                        <div class="form-check d-flex align-items-center">
                            <input class="form-check-input me-2" 
                                   type="checkbox" 
                                   name="remember" 
                                   id="remember" 
                                   {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label text-muted" for="remember">
                                <i class="fas fa-clock me-1"></i>Recordarme en este dispositivo
                            </label>
                        </div>
                        <small class="text-muted">
                            Si no marcas esta opción, tu sesión expirará al cerrar el navegador.
                        </small>
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
