@extends('layouts.dashboard')

@section('title', 'Dashboard - Ventanilla DRTC Apurímac')

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

<!-- Header de Ventanilla DRTC -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-drtc-light text-dark shadow-lg border-0">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="drtc-logo">
                            <div class="text-center">
                                <i class="fas fa-window-maximize"></i>
                                <div style="font-size: 10px; line-height: 1;">VENT</div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <h2 class="mb-1 fw-bold text-drtc-navy">SISTEMA DE VENTANILLA DRTC</h2>
                        <h4 class="mb-2 text-drtc-orange">Dirección Regional de Transportes y Comunicaciones - Apurímac</h4>
                        <div class="d-flex align-items-center text-drtc-navy">
                            <i class="fas fa-user-tie me-2"></i>
                            <span class="me-3">Operador: {{ Auth::user()->name }}</span>
                            <i class="fas fa-calendar me-2"></i>
                            <span class="me-3">{{ date('d/m/Y') }}</span>
                            <i class="fas fa-clock me-2"></i>
                            <span id="hora-header"></span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="text-center">
                            <i class="fas fa-users fa-4x text-drtc-orange opacity-75 mb-2"></i>
                            <div class="h5 mb-0 text-drtc-navy">ATENCIÓN AL USUARIO</div>
                            <div class="small text-drtc-orange opacity-75">Fiscalización y Control</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Estadísticas de Fiscalización DRTC -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-drtc-orange shadow-lg border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title text-uppercase mb-1">Inspecciones Hoy</h6>
                            <h2 class="mb-0 fw-bold">
                                @php
                                    try {
                                        echo DB::table('inspecciones')->whereDate('created_at', today())->count();
                                    } catch (\Exception $e) {
                                        echo '0';
                                    }
                                @endphp
                            </h2>
                            <small class="opacity-75">Realizadas hoy</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clipboard-check fa-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-danger shadow-lg border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title text-uppercase mb-1">Infracciones</h6>
                            <h2 class="mb-0 fw-bold">
                                @php
                                    try {
                                        echo DB::table('infracciones')->count();
                                    } catch (\Exception $e) {
                                        echo '0';
                                    }
                                @endphp
                            </h2>
                            <small class="opacity-75">Total registradas</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
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
                            <h5 class="card-title">Vehículos</h5>
                            <h2 class="mb-0">
                                @php
                                    try {
                                        echo DB::table('vehiculos')->count();
                                    } catch (\Exception $e) {
                                        echo '0';
                                    }
                                @endphp
                            </h2>
                            <small class="opacity-75">Registrados</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-car fa-2x"></i>
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
                            <h5 class="card-title">Empresas</h5>
                            <h2 class="mb-0">
                                @php
                                    try {
                                        echo DB::table('empresas')->count();
                                    } catch (\Exception $e) {
                                        echo '0';
                                    }
                                @endphp
                            </h2>
                            <small class="opacity-75">Registradas</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-building fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Herramientas de Ventanilla - Fiscalización -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-tools me-2"></i>Herramientas de Ventanilla - Fiscalización</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-clipboard-check fa-3x text-warning mb-3"></i>
                                    <h5 class="card-title">Nueva Inspección</h5>
                                    <p class="card-text">Registrar inspección de vehículos y empresas</p>
                                    <a href="{{ route('inspecciones.create') }}" class="btn btn-warning">Crear Inspección</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-secondary">
                                <div class="card-body text-center">
                                    <i class="fas fa-list fa-3x text-secondary mb-3"></i>
                                    <h5 class="card-title">Mis Inspecciones</h5>
                                    <p class="card-text">Ver y gestionar inspecciones realizadas</p>
                                    <a href="{{ route('inspecciones.index') }}" class="btn btn-secondary">Ver Lista</a>
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
                            <div class="card h-100 border-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-car fa-3x text-info mb-3"></i>
                                    <h5 class="card-title">Vehículos</h5>
                                    <p class="card-text">Consultar y verificar datos de vehículos</p>
                                    <a href="#" class="btn btn-info">Consultar Vehículos</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-success">
                                <div class="card-body text-center">
                                    <i class="fas fa-building fa-3x text-success mb-3"></i>
                                    <h5 class="card-title">Empresas</h5>
                                    <p class="card-text">Consultar empresas de transporte</p>
                                    <a href="#" class="btn btn-success">Ver Empresas</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-primary">
                                <div class="card-body text-center">
                                    <i class="fas fa-id-card fa-3x text-primary mb-3"></i>
                                    <h5 class="card-title">Conductores</h5>
                                    <p class="card-text">Verificar licencias y datos de conductores</p>
                                    <a href="#" class="btn btn-primary">Consultar Conductores</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inspecciones Recientes -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Inspecciones Recientes</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Empresa/Vehículo</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    try {
                                        $inspecciones = DB::table('inspecciones')->latest()->take(5)->get();
                                    } catch (\Exception $e) {
                                        $inspecciones = collect();
                                    }
                                @endphp
                                @forelse($inspecciones as $inspeccion)
                                <tr>
                                    <td><strong>#{{ $inspeccion->id }}</strong></td>
                                    <td>{{ $inspeccion->numero_inspeccion ?? 'N/A' }}</td>
                                    <td>{{ $inspeccion->tipo_inspeccion ?? 'General' }}</td>
                                    <td>
                                        @if($inspeccion->estado == 'completada')
                                            <span class="badge bg-success">Completada</span>
                                        @elseif($inspeccion->estado == 'pendiente')
                                            <span class="badge bg-warning">Pendiente</span>
                                        @else
                                            <span class="badge bg-info">{{ $inspeccion->estado }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $inspeccion->created_at ?? 'N/A' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No hay inspecciones registradas</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Accesos Rápidos - Ventanilla -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-rocket me-2"></i>Accesos Rápidos - Ventanilla</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <a href="{{ route('inspecciones.index') }}" class="btn btn-outline-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center text-decoration-none" style="min-height: 80px;">
                                <i class="fas fa-clipboard-check fa-2x mb-2"></i>
                                <span>Inspecciones</span>
                            </a>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <a href="{{ route('infracciones.index') }}" class="btn btn-outline-danger w-100 h-100 d-flex flex-column align-items-center justify-content-center text-decoration-none" style="min-height: 80px;">
                                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                <span>Infracciones</span>
                            </a>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <button class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="min-height: 80px;">
                                <i class="fas fa-car fa-2x mb-2"></i>
                                <span>Vehículos</span>
                            </button>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <button class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="min-height: 80px;">
                                <i class="fas fa-building fa-2x mb-2"></i>
                                <span>Empresas</span>
                            </button>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <button class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="min-height: 80px;">
                                <i class="fas fa-id-card fa-2x mb-2"></i>
                                <span>Conductores</span>
                            </button>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <button class="btn btn-outline-secondary w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="min-height: 80px;">
                                <i class="fas fa-chart-bar fa-2x mb-2"></i>
                                <span>Reportes</span>
                            </button>
                        </div>
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
