@extends('layouts.dashboard')

@section('title', 'Dashboard - Fiscalizador')

@section('content')
<!-- Header del Fiscalizador -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="mb-0"><i class="fas fa-search me-2"></i>Panel de Fiscalizador</h2>
                        <p class="mb-0">Bienvenido, {{ Auth::user()->name }}</p>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-check fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Estadísticas del Fiscalizador -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Inspecciones Hoy</h5>
                            <h2 class="mb-0">12</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-eye fa-2x"></i>
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
                            <h5 class="card-title">Aprobadas</h5>
                            <h2 class="mb-0">8</h2>
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
                            <h5 class="card-title">Pendientes</h5>
                            <h2 class="mb-0">3</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
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
                            <h5 class="card-title">Rechazadas</h5>
                            <h2 class="mb-0">1</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Herramientas del Fiscalizador -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-tools me-2"></i>Herramientas de Fiscalización</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-search-plus fa-3x text-info mb-3"></i>
                                    <h5 class="card-title">Nueva Inspección</h5>
                                    <p class="card-text">Iniciar un nuevo proceso de fiscalización</p>
                                    <a href="#" class="btn btn-info">Iniciar</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-list-alt fa-3x text-warning mb-3"></i>
                                    <h5 class="card-title">Mis Inspecciones</h5>
                                    <p class="card-text">Ver todas mis inspecciones realizadas</p>
                                    <a href="#" class="btn btn-warning">Ver Lista</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-success">
                                <div class="card-body text-center">
                                    <i class="fas fa-file-alt fa-3x text-success mb-3"></i>
                                    <h5 class="card-title">Generar Reporte</h5>
                                    <p class="card-text">Crear reportes de fiscalizaciones</p>
                                    <a href="#" class="btn btn-success">Generar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inspecciones Recientes y Notificaciones -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-history me-2"></i>Inspecciones Recientes</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Establecimiento</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>#001</td>
                                    <td>Restaurant El Buen Sabor</td>
                                    <td>Sanitaria</td>
                                    <td><span class="badge bg-success">Aprobada</span></td>
                                    <td>{{ now()->format('d/m/Y') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#002</td>
                                    <td>Farmacia Central</td>
                                    <td>Medicamentos</td>
                                    <td><span class="badge bg-warning">Pendiente</span></td>
                                    <td>{{ now()->subDay()->format('d/m/Y') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#003</td>
                                    <td>Supermercado Los Andes</td>
                                    <td>Alimentos</td>
                                    <td><span class="badge bg-success">Aprobada</span></td>
                                    <td>{{ now()->subDays(2)->format('d/m/Y') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
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
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Nueva asignación</strong><br>
                            Se te ha asignado la inspección #004 para mañana.
                            <small class="d-block mt-1 text-muted">Hace 2 horas</small>
                        </div>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Revisión pendiente</strong><br>
                            La inspección #002 requiere tu atención.
                            <small class="d-block mt-1 text-muted">Hace 4 horas</small>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Calendario de Inspecciones -->
            <div class="card mt-3">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Próximas Inspecciones</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                        <div>
                            <strong>Hotel Plaza</strong><br>
                            <small class="text-muted">Inspección turística</small>
                        </div>
                        <div class="text-end">
                            <small class="text-info">Mañana</small><br>
                            <small class="text-muted">09:00</small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                        <div>
                            <strong>Clínica Santa María</strong><br>
                            <small class="text-muted">Inspección sanitaria</small>
                        </div>
                        <div class="text-end">
                            <small class="text-warning">Viernes</small><br>
                            <small class="text-muted">14:30</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
