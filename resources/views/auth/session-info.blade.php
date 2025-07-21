@extends('layouts.dashboard')

@section('title', 'Información de Sesión')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información de tu Sesión</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Estado de la Sesión</h6>
                        <div class="alert {{ session('expire_on_close', true) ? 'alert-warning' : 'alert-success' }}">
                            @if(session('expire_on_close', true))
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Sesión Temporal</strong><br>
                                Tu sesión expirará automáticamente al cerrar el navegador por seguridad.
                            @else
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Sesión Persistente</strong><br>
                                Tu sesión se mantendrá activa gracias a la opción "Recordarme".
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Detalles de Usuario</h6>
                        <ul class="list-unstyled">
                            <li><strong>Usuario:</strong> {{ auth()->user()->username }}</li>
                            <li><strong>Nombre:</strong> {{ auth()->user()->name }}</li>
                            <li><strong>Email:</strong> {{ auth()->user()->email }}</li>
                            <li><strong>Rol:</strong> 
                                <span class="badge bg-{{ auth()->user()->role == 'administrador' ? 'primary' : (auth()->user()->role == 'fiscalizador' ? 'info' : 'warning') }}">
                                    {{ ucfirst(auth()->user()->role) }}
                                </span>
                            </li>
                            <li><strong>Último acceso:</strong> {{ auth()->user()->updated_at->format('d/m/Y H:i:s') }}</li>
                        </ul>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-lightbulb me-2"></i>Consejo de Seguridad</h6>
                            <p class="mb-0">
                                Para mayor seguridad, especialmente en computadoras compartidas, 
                                asegúrate de <strong>no marcar "Recordarme"</strong> al iniciar sesión. 
                                Esto hará que tu sesión se cierre automáticamente al cerrar el navegador.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-3">
                    @if(auth()->user()->role == 'administrador')
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                        </a>
                    @elseif(auth()->user()->role == 'fiscalizador')
                        <a href="{{ route('fiscalizador.dashboard') }}" class="btn btn-info">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                        </a>
                    @else
                        <a href="{{ route('ventanilla.dashboard') }}" class="btn btn-warning">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
