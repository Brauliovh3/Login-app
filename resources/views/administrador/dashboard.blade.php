@extends('layouts.dashboard')

@section('title', 'Dashboard - Administrador')

@section('content')
<!-- Header del Administrador -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="mb-0"><i class="fas fa-user-shield me-2"></i>Panel de Administrador</h2>
                        <p class="mb-0">Bienvenido, {{ Auth::user()->name }}</p>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-cogs fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Usuarios Totales</h5>
                            <h2 class="mb-0">{{ \App\Models\User::count() }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Fiscalizadores</h5>
                            <h2 class="mb-0">{{ \App\Models\User::where('role', 'fiscalizador')->count() }}</h2>
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
                    <h4 class="mb-0"><i class="fas fa-tools me-2"></i>Herramientas de Administración</h4>
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
                </div>
            </div>
        </div>
    </div>

    <!-- Usuarios Recientes -->
    <div class="row">
        <div class="col-md-8">
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
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-bell me-2"></i>Notificaciones</h4>
                </div>
                <div class="card-body">
                    @forelse(\App\Models\Notification::where('user_id', Auth::id())->latest()->take(5)->get() as $notification)
                        <div class="alert alert-{{ $notification->type == 'success' ? 'success' : ($notification->type == 'warning' ? 'warning' : 'info') }} alert-dismissible fade show">
                            <strong>{{ $notification->title }}</strong><br>
                            {{ $notification->message }}
                            <small class="d-block mt-1 text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                        </div>
                    @empty
                        <div class="text-center text-muted">
                            <i class="fas fa-bell-slash fa-2x mb-2"></i>
                            <p>No hay notificaciones nuevas</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
