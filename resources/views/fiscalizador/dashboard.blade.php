@extends('layouts.dashboard')

@section('title', 'Dashboard - Fiscalizador DRTC Apurímac')

@section('content')
<style>
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 20px;
        color: white;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
    }
    
    .stats-number {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 5px;
    }
    
    .stats-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    
    .analysis-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border-left: 4px solid #ff6b35;
    }
    
    .progress-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: conic-gradient(#28a745 0deg 252deg, #e9ecef 252deg 360deg);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    
    .progress-circle::before {
        content: '';
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: white;
        position: absolute;
    }
    
    .progress-text {
        position: relative;
        z-index: 1;
        font-weight: bold;
        color: #333;
    }
    
    .trend-up {
        color: #28a745;
    }
    
    .trend-down {
        color: #dc3545;
    }
</style>

<div class="container-fluid">
    <!-- Header con saludo personalizado -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">¡Buen día, {{ Auth::user()->name }}!</h2>
                    <p class="text-muted">Panel de Control - Fiscalizador DRTC Apurímac</p>
                </div>
                <div class="text-end">
                    <small class="text-muted">{{ now()->format('l, d F Y') }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas principales -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="stats-number">{{ $stats['total_infracciones'] ?? 89 }}</div>
                <div class="stats-label">Total Infracciones</div>
                <small class="d-block mt-2"><i class="fas fa-arrow-up trend-up"></i> +12% este mes</small>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="stats-number">{{ $stats['infracciones_procesadas'] ?? 67 }}</div>
                <div class="stats-label">Procesadas</div>
                <small class="d-block mt-2"><i class="fas fa-check-circle"></i> 75% completado</small>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="stats-number">{{ $stats['infracciones_pendientes'] ?? 22 }}</div>
                <div class="stats-label">Pendientes</div>
                <small class="d-block mt-2"><i class="fas fa-clock text-warning"></i> En proceso</small>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <div class="stats-number">S/{{ number_format($stats['total_multas'] ?? 45000) }}</div>
                <div class="stats-label">Total Multas</div>
                <small class="d-block mt-2"><i class="fas fa-arrow-up trend-up"></i> +8% vs anterior</small>
            </div>
        </div>
    </div>

    <!-- Análisis de resultados -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="analysis-card">
                <h5 class="mb-3"><i class="fas fa-chart-line text-primary"></i> Análisis de Rendimiento</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="progress-circle me-3">
                                <span class="progress-text">75%</span>
                            </div>
                            <div>
                                <h6 class="mb-0">Eficiencia de Procesamiento</h6>
                                <small class="text-muted">67 de 89 infracciones procesadas</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Resumen Semanal</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success"></i> 15 actas finalizadas</li>
                            <li><i class="fas fa-eye text-info"></i> 8 inspecciones realizadas</li>
                            <li><i class="fas fa-file text-warning"></i> 5 reportes generados</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="analysis-card">
                <h6 class="mb-3"><i class="fas fa-trophy text-warning"></i> Logros Recientes</h6>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Meta Mensual</span>
                        <span class="badge bg-success">89%</span>
                    </div>
                    <div class="progress mt-1" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: 89%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Calidad</span>
                        <span class="badge bg-primary">92%</span>
                    </div>
                    <div class="progress mt-1" style="height: 6px;">
                        <div class="progress-bar bg-primary" style="width: 92%"></div>
                    </div>
                </div>
                <small class="text-muted">
                    <i class="fas fa-medal text-warning"></i> 
                    Excelente trabajo esta semana
                </small>
            </div>
        </div>
    </div>

    <!-- Acciones rápidas -->
    <div class="row">
        <div class="col-12">
            <div class="analysis-card">
                <h5 class="mb-3"><i class="fas fa-bolt text-warning"></i> Acciones Rápidas</h5>
                <div class="row">
                    <div class="col-md-2 col-6 text-center mb-3">
                        <a href="{{ route('fiscalizador.actas-contra') }}" class="text-decoration-none">
                            <div class="p-3 rounded" style="background: #f8f9fa;">
                                <i class="fas fa-file-alt fa-2x text-primary mb-2"></i>
                                <br><small>Actas</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-2 col-6 text-center mb-3">
                        <a href="{{ route('fiscalizador.carga-paga') }}" class="text-decoration-none">
                            <div class="p-3 rounded" style="background: #f8f9fa;">
                                <i class="fas fa-truck fa-2x text-success mb-2"></i>
                                <br><small>Carga Paga</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-2 col-6 text-center mb-3">
                        <a href="{{ route('fiscalizador.empresas') }}" class="text-decoration-none">
                            <div class="p-3 rounded" style="background: #f8f9fa;">
                                <i class="fas fa-building fa-2x text-info mb-2"></i>
                                <br><small>Empresas</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-2 col-6 text-center mb-3">
                        <a href="{{ route('fiscalizador.inspecciones') }}" class="text-decoration-none">
                            <div class="p-3 rounded" style="background: #f8f9fa;">
                                <i class="fas fa-search fa-2x text-warning mb-2"></i>
                                <br><small>Inspecciones</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-2 col-6 text-center mb-3">
                        <a href="{{ route('fiscalizador.consultas') }}" class="text-decoration-none">
                            <div class="p-3 rounded" style="background: #f8f9fa;">
                                <i class="fas fa-question-circle fa-2x text-secondary mb-2"></i>
                                <br><small>Consultas</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-2 col-6 text-center mb-3">
                        <a href="{{ route('fiscalizador.reportes') }}" class="text-decoration-none">
                            <div class="p-3 rounded" style="background: #f8f9fa;">
                                <i class="fas fa-chart-bar fa-2x text-danger mb-2"></i>
                                <br><small>Reportes</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection