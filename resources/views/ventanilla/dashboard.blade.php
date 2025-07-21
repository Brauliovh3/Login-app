@extends('layouts.dashboard')

@section('title', 'Dashboard - Ventanilla')

@section('content')
<!-- Header de Ventanilla -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="mb-0"><i class="fas fa-window-maximize me-2"></i>Panel de Ventanilla</h2>
                        <p class="mb-0">Bienvenido, {{ Auth::user()->name }}</p>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Estadísticas de Ventanilla -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Atenciones Hoy</h5>
                            <h2 class="mb-0">24</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-check fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Trámites Completados</h5>
                            <h2 class="mb-0">18</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
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
                            <h5 class="card-title">En Proceso</h5>
                            <h2 class="mb-0">4</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
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
                            <h5 class="card-title">Cola de Espera</h5>
                            <h2 class="mb-0">2</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Herramientas de Ventanilla -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-tools me-2"></i>Herramientas de Atención</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-primary">
                                <div class="card-body text-center">
                                    <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
                                    <h5 class="card-title">Nueva Atención</h5>
                                    <p class="card-text">Registrar nueva atención al público</p>
                                    <a href="#" class="btn btn-primary">Iniciar</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-success">
                                <div class="card-body text-center">
                                    <i class="fas fa-file-alt fa-3x text-success mb-3"></i>
                                    <h5 class="card-title">Trámites</h5>
                                    <p class="card-text">Gestionar solicitudes y documentos</p>
                                    <a href="#" class="btn btn-success">Gestionar</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-search fa-3x text-info mb-3"></i>
                                    <h5 class="card-title">Consultar Estado</h5>
                                    <p class="card-text">Verificar estado de solicitudes</p>
                                    <a href="#" class="btn btn-info">Consultar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Atenciones Recientes y Cola -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-history me-2"></i>Atenciones Recientes</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Ticket</th>
                                    <th>Cliente</th>
                                    <th>Trámite</th>
                                    <th>Estado</th>
                                    <th>Hora</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>#V001</strong></td>
                                    <td>María García</td>
                                    <td>Certificado de Salud</td>
                                    <td><span class="badge bg-success">Completado</span></td>
                                    <td>{{ now()->format('H:i') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-print"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>#V002</strong></td>
                                    <td>Carlos Mendoza</td>
                                    <td>Licencia Comercial</td>
                                    <td><span class="badge bg-warning">En Proceso</span></td>
                                    <td>{{ now()->subMinutes(15)->format('H:i') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>#V003</strong></td>
                                    <td>Ana López</td>
                                    <td>Consulta General</td>
                                    <td><span class="badge bg-success">Completado</span></td>
                                    <td>{{ now()->subMinutes(30)->format('H:i') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-print"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>#V004</strong></td>
                                    <td>Luis Rodríguez</td>
                                    <td>Permiso Sanitario</td>
                                    <td><span class="badge bg-info">Documentos</span></td>
                                    <td>{{ now()->subMinutes(45)->format('H:i') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-file"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <!-- Cola de Espera -->
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0"><i class="fas fa-users me-2"></i>Cola de Espera</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info text-center mb-3">
                        <h3 class="mb-0">Próximo Turno</h3>
                        <h1 class="display-4 text-primary">#A005</h1>
                        <button class="btn btn-primary btn-lg">
                            <i class="fas fa-bell me-2"></i>Llamar
                        </button>
                    </div>
                    
                    <div class="list-group">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>#A006</strong><br>
                                <small class="text-muted">Trámite general</small>
                            </div>
                            <span class="badge bg-warning rounded-pill">Esperando</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>#A007</strong><br>
                                <small class="text-muted">Consulta</small>
                            </div>
                            <span class="badge bg-warning rounded-pill">Esperando</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notificaciones -->
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
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Turno completado</strong><br>
                            Se ha completado exitosamente el trámite #V001.
                            <small class="d-block mt-1 text-muted">Hace 5 minutos</small>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Sistema actualizado</strong><br>
                            Nuevas funcionalidades disponibles en el sistema.
                            <small class="d-block mt-1 text-muted">Hace 1 hora</small>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Accesos Rápidos -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-rocket me-2"></i>Accesos Rápidos</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <button class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="min-height: 80px;">
                                <i class="fas fa-id-card fa-2x mb-2"></i>
                                <span>Cédulas</span>
                            </button>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <button class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="min-height: 80px;">
                                <i class="fas fa-certificate fa-2x mb-2"></i>
                                <span>Certificados</span>
                            </button>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <button class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="min-height: 80px;">
                                <i class="fas fa-store fa-2x mb-2"></i>
                                <span>Licencias</span>
                            </button>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <button class="btn btn-outline-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="min-height: 80px;">
                                <i class="fas fa-file-medical fa-2x mb-2"></i>
                                <span>Sanitarios</span>
                            </button>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <button class="btn btn-outline-danger w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="min-height: 80px;">
                                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                <span>Multas</span>
                            </button>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <button class="btn btn-outline-secondary w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="min-height: 80px;">
                                <i class="fas fa-question-circle fa-2x mb-2"></i>
                                <span>Otros</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
