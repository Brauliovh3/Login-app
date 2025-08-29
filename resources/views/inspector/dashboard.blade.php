@extends('layouts.dashboard')

@section('title', 'Dashboard Inspector')

@section('content')
<div class="container-fluid">
    <!-- Header con gradiente naranja -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0" style="color: #ff8c00; font-weight: 600;">
            <i class="fas fa-search me-2"></i>Dashboard Inspector
        </h1>
        <small class="text-muted">Panel de control para inspecciones</small>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="row">
        <!-- Mis Inspecciones -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2" style="border-left: 4px solid #ff8c00 !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: #ff8c00;">
                                Total Infracciones
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total_infracciones'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x" style="color: #ff8c00;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Infracciones Resueltas -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2" style="border-left: 4px solid #27ae60 !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: #27ae60;">
                                Infracciones Resueltas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['infracciones_resueltas'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x" style="color: #27ae60;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Infracciones Pendientes -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2" style="border-left: 4px solid #f39c12 !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: #f39c12;">
                                Infracciones Pendientes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['infracciones_pendientes'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x" style="color: #f39c12;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Actas -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2" style="border-left: 4px solid #3498db !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: #3498db;">
                                Total Actas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total_actas'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x" style="color: #3498db;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila adicional -->
    <div class="row">
        <!-- Actas Procesadas -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2" style="border-left: 4px solid #2ecc71 !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: #2ecc71;">
                                Actas Procesadas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['actas_procesadas'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-double fa-2x" style="color: #2ecc71;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actas Pendientes -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2" style="border-left: 4px solid #e67e22 !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: #e67e22;">
                                Actas Pendientes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['actas_pendientes'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hourglass-half fa-2x" style="color: #e67e22;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notificaciones -->
        <div class="col-xl-4 col-md-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3" style="background: linear-gradient(135deg, #ff8c00, #e67e22); color: white;">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-bell me-2"></i>Notificaciones Recientes
                    </h6>
                </div>
                <div class="card-body">
                    @if($notifications && $notifications->count() > 0)
                        @foreach($notifications->take(3) as $notification)
                            <div class="d-flex align-items-center mb-3">
                                <div class="mr-3">
                                    <div class="icon-circle" style="background-color: rgba(255, 140, 0, 0.1);">
                                        <i class="fas fa-info text-orange"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="small text-gray-500">{{ $notification->created_at->diffForHumans() }}</div>
                                    <div class="font-weight-bold">{{ $notification->title }}</div>
                                    <div class="text-gray-800">{{ Str::limit($notification->message, 60) }}</div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-inbox fa-2x mb-2" style="color: #ccc;"></i>
                            <p>No hay notificaciones recientes</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3" style="background: linear-gradient(135deg, #ff8c00, #e67e22); color: white;">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-lightning-bolt me-2"></i>Acciones Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('inspector.nueva-inspeccion') }}" class="btn btn-primary btn-lg w-100" style="background: linear-gradient(135deg, #ff8c00, #e67e22); border: none;">
                                <i class="fas fa-plus-circle mb-1"></i><br>
                                <small>Nueva Inspección</small>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('inspector.inspecciones') }}" class="btn btn-outline-primary btn-lg w-100" style="border-color: #ff8c00; color: #ff8c00;">
                                <i class="fas fa-list mb-1"></i><br>
                                <small>Mis Inspecciones</small>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('inspector.vehiculos') }}" class="btn btn-outline-primary btn-lg w-100" style="border-color: #ff8c00; color: #ff8c00;">
                                <i class="fas fa-car mb-1"></i><br>
                                <small>Vehículos</small>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('inspector.reportes') }}" class="btn btn-outline-primary btn-lg w-100" style="border-color: #ff8c00; color: #ff8c00;">
                                <i class="fas fa-chart-bar mb-1"></i><br>
                                <small>Reportes</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .icon-circle {
        height: 2rem;
        width: 2rem;
        border-radius: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .text-orange {
        color: #ff8c00 !important;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animar las estadísticas al cargar la página
    const statsNumbers = document.querySelectorAll('.h5.mb-0.font-weight-bold');
    statsNumbers.forEach(function(stat) {
        const finalValue = parseInt(stat.textContent.replace(/[^\d]/g, ''));
        if (!isNaN(finalValue) && finalValue > 0) {
            let currentValue = 0;
            const increment = finalValue / 20; // 20 pasos de animación
            const timer = setInterval(function() {
                currentValue += increment;
                if (currentValue >= finalValue) {
                    currentValue = finalValue;
                    clearInterval(timer);
                }
                stat.textContent = Math.floor(currentValue);
            }, 75);
        }
    });
    
    // Efecto hover mejorado para las tarjetas
    const cards = document.querySelectorAll('.card');
    cards.forEach(function(card) {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.transition = 'all 0.3s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Mostrar notificación de datos actualizados
    setTimeout(function() {
        if (typeof toastr !== 'undefined') {
            toastr.info('Dashboard de inspector actualizado con datos reales', 'Datos Sincronizados');
        } else {
            // Crear notificación básica
            const notification = document.createElement('div');
            notification.className = 'alert alert-info alert-dismissible fade show position-fixed';
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                <strong>Datos Actualizados:</strong> Estadísticas de inspección sincronizadas.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 4500);
        }
    }, 1000);
});
</script>

@endsection