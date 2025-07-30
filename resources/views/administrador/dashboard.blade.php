@extends('layouts.dashboard')

@section('title', 'Dashboard - Administrador DRTC Apurímac')

@section('content')
<style>
    :root {
        --drtc-orange: #ff8c00;
        --drtc-dark-orange: #e67c00;
        --drtc-light-orange: #ffb84d;
        --drtc-orange-bg: #fff4e6;
        --drtc-navy: #1e3a8a;
    }
    
    .bg-drtc-orange { background-color: var(--drtc-orange) !important; }
    .bg-drtc-dark { background-color: var(--drtc-dark-orange) !important; }
    .bg-drtc-light { background-color: var(--drtc-light-orange) !important; }
    .bg-drtc-soft { background-color: var(--drtc-orange-bg) !important; }
    .bg-drtc-navy { background-color: var(--drtc-navy) !important; }
    .text-drtc-orange { color: var(--drtc-orange) !important; }
    .text-drtc-navy { color: var(--drtc-navy) !important; }
    .border-drtc-orange { border-color: var(--drtc-orange) !important; }
    
    .drtc-logo {
        background: linear-gradient(135deg, var(--drtc-orange), var(--drtc-dark-orange));
        border-radius: 50%;
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        font-weight: bold;
        box-shadow: 0 4px 15px rgba(255, 140, 0, 0.3);
    }
</style>

<!-- Header del Administrador DRTC -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-drtc-navy text-white shadow-lg border-0">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="drtc-logo">
                            <div class="text-center">
                                <i class="fas fa-user-shield"></i>
                                <div style="font-size: 10px; line-height: 1;">ADMIN</div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <h2 class="mb-1 fw-bold">PANEL DE ADMINISTRACIÓN DRTC</h2>
                        <h4 class="mb-2 opacity-90">Dirección Regional de Transportes y Comunicaciones - Apurímac</h4>
                        <div class="d-flex align-items-center text-warning">
                            <i class="fas fa-user-shield me-2"></i>
                            <span class="me-3">Administrador: {{ Auth::user()->name }}</span>
                            <i class="fas fa-calendar me-2"></i>
                            <span class="me-3">{{ date('d/m/Y') }}</span>
                            <i class="fas fa-clock me-2"></i>
                            <span id="hora-header"></span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="text-center">
                            <i class="fas fa-cogs fa-4x opacity-75 mb-2"></i>
                            <div class="h5 mb-0">SISTEMA DRTC</div>
                            <div class="small opacity-75">Gestión Administrativa</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Estadísticas DRTC -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-drtc-orange shadow-lg border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title text-uppercase mb-1">Usuarios Totales</h6>
                            <h2 class="mb-0 fw-bold">{{ \App\Models\User::count() }}</h2>
                            <small class="opacity-75">Sistema DRTC</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-info shadow-lg border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title text-uppercase mb-1">Fiscalizadores</h6>
                            <h2 class="mb-0 fw-bold">{{ \App\Models\User::where('role', 'fiscalizador')->count() }}</h2>
                            <small class="opacity-75">Activos</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-search fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Ventanillas</h5>
                            <h2 class="mb-0">{{ \App\Models\User::where('role', 'ventanilla')->count() }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-window-maximize fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Notificaciones</h5>
                            <h2 class="mb-0">{{ \App\Models\Notification::where('user_id', Auth::id())->where('read', false)->count() }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-bell fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Opciones de Administrador -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-tools me-2"></i>Herramientas de Administración.</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-primary">
                                <div class="card-body text-center">
                                    <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
                                    <h5 class="card-title">Gestionar Usuarios</h5>
                                    <p class="card-text">Crear, editar y eliminar usuarios del sistema</p>
                                    <a href="#" class="btn btn-primary">Gestionar</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-bar fa-3x text-info mb-3"></i>
                                    <h5 class="card-title">Reportes</h5>
                                    <p class="card-text">Ver estadísticas y generar reportes del sistema</p>
                                    <a href="#" class="btn btn-info">Ver Reportes</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-success">
                                <div class="card-body text-center">
                                    <i class="fas fa-cog fa-3x text-success mb-3"></i>
                                    <h5 class="card-title">Configuración</h5>
                                    <p class="card-text">Configurar parámetros generales del sistema</p>
                                    <a href="#" class="btn btn-success">Configurar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-clipboard-list fa-3x text-warning mb-3"></i>
                                    <h5 class="card-title">Auditoría del Sistema</h5>
                                    <p class="card-text">Revisar logs y actividades del sistema</p>
                                    <a href="#" class="btn btn-warning">Ver Auditoría</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-secondary">
                                <div class="card-body text-center">
                                    <i class="fas fa-shield-alt fa-3x text-secondary mb-3"></i>
                                    <h5 class="card-title">Seguridad</h5>
                                    <p class="card-text">Gestionar permisos y seguridad del sistema</p>
                                    <a href="#" class="btn btn-secondary">Seguridad</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-danger">
                                <div class="card-body text-center">
                                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                                    <h5 class="card-title">Infracciones</h5>
                                    <p class="card-text">Gestionar infracciones de tránsito</p>
                                    <a href="{{ route('infracciones.index') }}" class="btn btn-danger">Ver Infracciones</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-dark">
                                <div class="card-body text-center">
                                    <i class="fas fa-database fa-3x text-dark mb-3"></i>
                                    <h5 class="card-title">Base de Datos</h5>
                                    <p class="card-text">Administrar y mantener la base de datos</p>
                                    <a href="#" class="btn btn-dark">Gestionar BD</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Usuarios Recientes -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-users me-2"></i>Usuarios Recientes</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Registrado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(\App\Models\User::latest()->take(5)->get() as $user)
                                <tr>
                                    <td>
                                        <strong>{{ $user->name }}</strong><br>
                                        <small class="text-muted">{{ $user->username }}</small>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @switch($user->role)
                                            @case('administrador')
                                                <span class="badge bg-primary">Administrador</span>
                                                @break
                                            @case('fiscalizador')
                                                <span class="badge bg-info">Fiscalizador</span>
                                                @break
                                            @case('ventanilla')
                                                <span class="badge bg-warning">Ventanilla</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para actualizar fecha y hora en tiempo real
    function actualizarFechaHora() {
        const ahora = new Date();
        const hora = ahora.toLocaleTimeString('es-PE', { 
            hour12: false,
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        
        // Actualizar header
        const horaHeader = document.getElementById('hora-header');
        if (horaHeader) horaHeader.textContent = hora;
    }

    // Actualizar cada segundo
    actualizarFechaHora();
    setInterval(actualizarFechaHora, 1000);
});
</script>
@endsection
