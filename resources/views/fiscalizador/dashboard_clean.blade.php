@extends('layouts.dashboard')

@section('title', 'Dashboard - Fiscalizador DRTC Apurímac')

@section('content')
<style>
    :root {
        --drtc-orange: #ff8c00;
        --drtc-dark-orange: #e67c00;
        --drtc-navy: #1e3a8a;
        --drtc-success: #28a745;
        --drtc-warning: #ffc107;
        --drtc-danger: #dc3545;
        --drtc-info: #17a2b8;
    }
    
    .stats-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        overflow: hidden;
        background: linear-gradient(135deg, #fff, #f8f9fa);
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }
    
    .stats-number {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
    }
    
    .progress-custom {
        height: 8px;
        border-radius: 10px;
        background-color: rgba(255, 255, 255, 0.3);
    }
    
    .progress-bar-custom {
        border-radius: 10px;
        transition: width 0.6s ease;
    }
    
    .welcome-banner {
        background: linear-gradient(135deg, var(--drtc-orange), var(--drtc-dark-orange));
        border-radius: 20px;
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .welcome-banner::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 200px;
        height: 200%;
        background: rgba(255, 255, 255, 0.1);
        transform: rotate(15deg);
    }
    
    .chart-container {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        padding: 25px;
    }
</style>

<!-- Banner de Bienvenida -->
<div class="row mb-4">
    <div class="col-12">
        <div class="welcome-banner p-4">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="p-3 bg-white bg-opacity-20 rounded-circle">
                        <i class="fas fa-clipboard-check fa-2x"></i>
                    </div>
                </div>
                <div class="col">
                    <h2 class="mb-2 fw-bold">Sistema de Fiscalización DRTC - Apurímac</h2>
                    <p class="mb-1 fs-5">
                        <i class="fas fa-user me-2"></i>Bienvenido, <strong>{{ Auth::user()->name ?? 'Inspector' }}</strong>
                        <span class="ms-4">
                            <i class="fas fa-calendar-alt me-2"></i>{{ date('d/m/Y') }}
                        </span>
                    </p>
                    <small class="opacity-75">Use el menú lateral "Actas Contra" para acceder a todas las funcionalidades de fiscalización</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Solo mostrar lo esencial -->
<div class="row">
    <div class="col-md-12">
        <div class="chart-container text-center">
            <h4 class="mb-4 fw-bold text-dark">
                <i class="fas fa-file-alt me-2 text-primary"></i>
                Resumen de Actas Registradas
            </h4>
            
            <div class="row justify-content-center">
                <div class="col-md-4">
                    <h2 class="display-3 fw-bold mb-2" style="color: var(--drtc-orange);">{{ $stats['totalActas'] ?? 0 }}</h2>
                    <p class="text-muted fs-5">Total del mes</p>
                </div>
                
                <div class="col-md-4">
                    <h2 class="display-3 fw-bold text-success mb-2">{{ $stats['actasHoy'] ?? 0 }}</h2>
                    <p class="text-muted fs-5">Registradas hoy</p>
                </div>
            </div>
            
            <div class="mt-4 pt-4 border-top">
                <p class="text-muted">
                    <i class="fas fa-info-circle me-2"></i>
                    Use el menú "Actas Contra" para acceder a todas las funcionalidades de fiscalización
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Actividad Reciente -->
<div class="row">
    <div class="col-12">
        <div class="chart-container">
            <h5 class="mb-4 fw-bold text-dark">
                <i class="fas fa-history me-2 text-info"></i>
                Actividad Reciente
            </h5>
            <div class="row">
                @if(isset($stats['actas_recientes']) && $stats['actas_recientes']->count() > 0)
                    @foreach($stats['actas_recientes'] as $index => $acta)
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <div class="flex-shrink-0 me-3">
                                    @if($index == 0)
                                        <i class="fas fa-plus-circle fa-2x text-success"></i>
                                    @elseif($acta->estado == 'procesada')
                                        <i class="fas fa-check fa-2x text-primary"></i>
                                    @else
                                        <i class="fas fa-edit fa-2x text-warning"></i>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-1">
                                        @if($index == 0)
                                            Nueva Acta Registrada
                                        @elseif($acta->estado == 'procesada')
                                            Acta Procesada
                                        @else
                                            Acta Actualizada
                                        @endif
                                    </h6>
                                    <small class="text-muted">{{ $acta->numero_acta ?? 'DRTC-APU-' . date('Y') . '-' . str_pad($acta->id ?? 1, 3, '0', STR_PAD_LEFT) }}</small>
                                    <div class="text-muted small">{{ \Carbon\Carbon::parse($acta->created_at)->diffForHumans() }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center p-3 bg-light rounded">
                            <div class="flex-shrink-0 me-3">
                                <i class="fas fa-info-circle fa-2x text-info"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Sin Actividad Reciente</h6>
                                <small class="text-muted">No hay actas registradas aún</small>
                                <div class="text-muted small">Comience registrando una nueva acta</div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
