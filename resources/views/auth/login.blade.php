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

                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-left: 4px solid #28a745; background: linear-gradient(135deg, #d4edda, #c3e6cb); border-radius: 8px;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle me-2" style="color: #28a745; font-size: 18px;"></i>
                            <div>
                                <strong>¡Registro Exitoso!</strong><br>
                                <small>{{ session('status') }}</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        @foreach ($errors->all() as $error)
                            <div><i class="fas fa-exclamation-circle me-2"></i>{{ $error }}</div>
                        @endforeach
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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

@if(session('toast'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    @php $toast = session('toast'); @endphp
    
    // Función para mostrar toast de registro exitoso
    function showRegistrationToast() {
        const toastContainer = document.getElementById('toast-container') || createToastContainer();
        const toastId = 'toast-registration-' + Date.now();
        
        const icons = {
            'success': 'fas fa-check-circle',
            'error': 'fas fa-exclamation-triangle', 
            'warning': 'fas fa-exclamation-circle',
            'info': 'fas fa-info-circle'
        };
        
        const colors = {
            'success': '#28a745',
            'error': '#dc3545',
            'warning': '#ffc107',
            'info': '#17a2b8'
        };
        
        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = 'toast-notification';
        toast.innerHTML = `
            <div style="
                background: white;
                border-left: 4px solid ${colors['{{ $toast["type"] ?? "success" }}']};
                border-radius: 8px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.15);
                margin-bottom: 10px;
                padding: 16px;
                display: flex;
                align-items: flex-start;
                animation: slideInRight 0.3s ease-out;
                position: relative;
                max-width: 100%;
            ">
                <div style="color: ${colors['{{ $toast["type"] ?? "success" }}']}; margin-right: 12px; margin-top: 2px;">
                    <i class="${icons['{{ $toast["type"] ?? "success" }}']} " style="font-size: 18px;"></i>
                </div>
                <div style="flex: 1; min-width: 0;">
                    <div style="font-weight: 600; color: #333; margin-bottom: 4px; font-size: 14px;">
                        {{ $toast['title'] ?? '¡Registro Exitoso!' }}
                    </div>
                    <div style="color: #666; font-size: 13px; line-height: 1.4;">
                        {{ $toast['message'] ?? 'Tu solicitud ha sido enviada correctamente.' }}
                    </div>
                </div>
                <button onclick="closeToast('${toastId}')" style="
                    background: none;
                    border: none;
                    color: #999;
                    font-size: 16px;
                    cursor: pointer;
                    padding: 0;
                    margin-left: 8px;
                    width: 20px;
                    height: 20px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                ">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        
        // Auto-cerrar después del tiempo especificado
        setTimeout(() => {
            closeToast(toastId);
        }, {{ $toast['duration'] ?? 8000 }});
    }
    
    function createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 350px;';
        document.body.appendChild(container);
        return container;
    }
    
    function closeToast(toastId) {
        const toast = document.getElementById(toastId);
        if (toast) {
            toast.style.animation = 'slideOutRight 0.3s ease-in';
            setTimeout(() => {
                toast.remove();
            }, 300);
        }
    }
    
    // Mostrar el toast de registro
    showRegistrationToast();
});
</script>

<style>
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .toast-notification:hover {
        transform: translateX(-5px);
        transition: transform 0.2s ease;
    }
</style>
@endif
@endsection
