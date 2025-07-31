@extends('layouts.app')

@section('title', 'Panel de Administrador - DRTC Apurímac')

@section('content')
<div class="container-fluid">
    <!-- Header principal -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-crown text-warning"></i> 
                Panel de Administrador DRTC
            </h1>
            <p class="text-muted">Bienvenido {{ auth()->user()->username }} - Dirección Regional de Transportes y Comunicaciones Apurímac</p>
        </div>
        <div>
            <button class="btn btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Nueva Gestión
            </button>
        </div>
    </div>

    <!-- Estadísticas principales del sistema -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Empresas de Transporte
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_empresas'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Vehículos Registrados
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_vehiculos'] }}</div>
                            <div class="text-xs text-success">{{ $stats['vehiculos_activos'] }} activos</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bus fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Conductores Habilitados
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_conductores'] }}</div>
                            <div class="text-xs text-info">{{ $stats['conductores_activos'] }} con licencia vigente</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-id-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Inspectores Activos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['inspectores_activos'] }}</div>
                            <div class="text-xs text-warning">de {{ $stats['total_inspectores'] }} total</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel de administración principal -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cogs"></i> Panel de Control DRTC Apurímac
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Gestión de Empresas
                                            </div>
                                            <div class="text-gray-800">
                                                <small class="text-muted d-block mb-2">Administrar empresas de transporte</small>
                                                <button class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-building"></i> Gestionar
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-building fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Control de Vehículos
                                            </div>
                                            <div class="text-gray-800">
                                                <small class="text-muted d-block mb-2">Registro y supervisión vehicular</small>
                                                <button class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-bus"></i> Administrar
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-bus fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Licencias de Conducir
                                            </div>
                                            <div class="text-gray-800">
                                                <small class="text-muted d-block mb-2">Control de habilitaciones</small>
                                                <button class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-id-card"></i> Supervisar
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-id-card fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Gestión de Usuarios
                                            </div>
                                            <div class="text-gray-800">
                                                <small class="text-muted d-block mb-2">Administrar accesos al sistema</small>
                                                <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-users"></i> Gestionar
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de estadísticas de infracciones -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-exclamation-triangle"></i> Control de Infracciones
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="h4 mb-2 font-weight-bold text-danger">{{ $stats['total_infracciones'] }}</div>
                        <p class="text-muted">Tipos de infracciones registradas</p>
                    </div>
                    <hr>
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                        Personal de Fiscalización
                    </div>
                    <div class="h6 mb-2 text-gray-800">{{ $stats['inspectores_activos'] }} inspectores activos</div>
                    <div class="progress mb-3">
                        <div class="progress-bar bg-info" role="progressbar" 
                             style="width: {{ ($stats['inspectores_activos']/$stats['total_inspectores'])*100 }}%">
                        </div>
                    </div>
                    <button class="btn btn-sm btn-block btn-outline-primary">
                        <i class="fas fa-chart-bar"></i> Ver Reportes Detallados
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
