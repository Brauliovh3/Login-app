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
                            <div class="small text-drtc-orange opacity-75">Trámites y Consultas</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Estadísticas de Ventanilla DRTC -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-drtc-orange shadow-lg border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title text-uppercase mb-1">Atenciones Hoy</h6>
                            <h2 class="mb-0 fw-bold">24</h2>
                            <small class="opacity-75">Usuarios atendidos</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-check fa-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-success shadow-lg border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title text-uppercase mb-1">Trámites Completados</h6>
                            <h2 class="mb-0 fw-bold">18</h2>
                            <small class="opacity-75">Finalizados</small>
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
                    <div class="row mt-3">
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-clipboard-check fa-3x text-warning mb-3"></i>
                                    <h5 class="card-title">Nueva Inspección</h5>
                                    <p class="card-text">Registrar inspección de establecimiento</p>
                                    <a href="{{ route('inspecciones.create') }}" class="btn btn-warning">Crear</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-secondary">
                                <div class="card-body text-center">
                                    <i class="fas fa-list fa-3x text-secondary mb-3"></i>
                                    <h5 class="card-title">Mis Inspecciones</h5>
                                    <p class="card-text">Ver inspecciones realizadas</p>
                                    <a href="{{ route('inspecciones.index') }}" class="btn btn-secondary">Ver Lista</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Atenciones Recientes -->
    <div class="row">
        <div class="col-12">
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
