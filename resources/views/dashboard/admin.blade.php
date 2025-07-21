@extends('layouts.app')

@section('title', 'Panel de Administrador')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="fas fa-crown text-warning"></i> Panel de Administrador</h1>
                <p class="text-muted">Bienvenido {{ auth()->user()->username }} - Control total del sistema</p>
            </div>
            <div>
                <button class="btn btn-primary me-2"><i class="fas fa-plus"></i> Nuevo Usuario</button>
                <button class="btn btn-success"><i class="fas fa-download"></i> Exportar Datos</button>
            </div>
        </div>
    </div>
</div>

<!-- Estadísticas principales -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card bg-primary text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $stats['total_users'] }}</h4>
                        <p class="mb-0">Total Usuarios</p>
                    </div>
                    <div>
                        <i class="fas fa-users fa-3x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="#">Ver Detalles</a>
                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-warning text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $stats['admin_users'] }}</h4>
                        <p class="mb-0">Administradores</p>
                    </div>
                    <div>
                        <i class="fas fa-user-shield fa-3x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="#">Gestionar</a>
                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-success text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $stats['fiscalizador_users'] }}</h4>
                        <p class="mb-0">Fiscalizadores</p>
                    </div>
                    <div>
                        <i class="fas fa-search fa-3x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="#">Supervisar</a>
                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-info text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $stats['ventanilla_users'] }}</h4>
                        <p class="mb-0">Ventanilla</p>
                    </div>
                    <div>
                        <i class="fas fa-desktop fa-3x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="#">Monitorear</a>
                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Panel de administración -->
<div class="row">
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-cogs"></i> Panel de Administración</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Gestión de Usuarios
                                        </div>
                                        <div class="text-gray-800">
                                            <button class="btn btn-sm btn-outline-primary me-1">Crear Usuario</button>
                                            <button class="btn btn-sm btn-outline-secondary">Lista Usuarios</button>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Configuración del Sistema
                                        </div>
                                        <div class="text-gray-800">
                                            <button class="btn btn-sm btn-outline-success me-1">Configurar</button>
                                            <button class="btn btn-sm btn-outline-secondary">Logs</button>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-cog fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Reportes y Estadísticas
                                        </div>
                                        <div class="text-gray-800">
                                            <button class="btn btn-sm btn-outline-info me-1">Ver Reportes</button>
                                            <button class="btn btn-sm btn-outline-secondary">Exportar</button>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-chart-area fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Seguridad y Permisos
                                        </div>
                                        <div class="text-gray-800">
                                            <button class="btn btn-sm btn-outline-warning me-1">Permisos</button>
                                            <button class="btn btn-sm btn-outline-secondary">Auditoría</button>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-shield-alt fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Panel de notificaciones -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-bell"></i> Actividad Reciente</h6>
                <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-primary">Ver Todas</a>
            </div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                @if($notifications->count() > 0)
                    @foreach($notifications as $notification)
                        <div class="media mb-3">
                            <div class="media-object mr-3">
                                @switch($notification->type)
                                    @case('success')
                                        <i class="fas fa-check-circle text-success fa-2x"></i>
                                        @break
                                    @case('error')
                                        <i class="fas fa-exclamation-circle text-danger fa-2x"></i>
                                        @break
                                    @case('warning')
                                        <i class="fas fa-exclamation-triangle text-warning fa-2x"></i>
                                        @break
                                    @default
                                        <i class="fas fa-info-circle text-info fa-2x"></i>
                                @endswitch
                            </div>
                            <div class="media-body">
                                <h6 class="mt-0">{{ $notification->title }}</h6>
                                <p class="text-muted small mb-1">{{ $notification->message }}</p>
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        @if(!$loop->last)<hr>@endif
                    @endforeach
                @else
                    <div class="text-center text-muted">
                        <i class="fas fa-bell-slash fa-3x mb-3"></i>
                        <p>No hay notificaciones</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
