@extends('layouts.dashboard')

@section('title', 'Dashboard - Administrador DRTC Apurímac')

@section('content')
<style>
    :root {
        --drtc-orange: #ff8c00;
        --drtc-dark-orange:                         <div class="col-md-6 col-lg-4">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-body text-center">
                                    <i class="fas fa-clipboard-list fa-3x text-primary mb-3"></i>
                                    <h5 class="card-title">Actas</h5>
                                    <p class="card-text">Supervisar actas de contravencion</p>
                                    <a href="{{ route('fiscalizador.actas-contra') }}" class="btn btn-primary">Ver Actas</a>
                                </div>
                            </div>
                        </div>       --drtc-light-orange: #ffb84d;
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

<!-- Header del Administrador DRTC -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-drtc-navy text-white shadow-lg border-0">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="drtc-logo">
                            <div class="text-center">
                                <i class="fas fa-user-shield"></i>
                                <div style="font-size: 10px; line-height: 1;">ADMIN</div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <h2 class="mb-1 fw-bold">PANEL DE ADMINISTRACIÓN DRTC</h2>
                        <h4 class="mb-2 opacity-90">Dirección Regional de Transportes y Comunicaciones - Apurímac</h4>
                        <div class="d-flex align-items-center text-warning">
                            <i class="fas fa-user-shield me-2"></i>
                            <span class="me-3">Administrador: {{ Auth::user()->name }}</span>
                            <i class="fas fa-calendar me-2"></i>
                            <span class="me-3">{{ date('d/m/Y') }}</span>
                            <i class="fas fa-clock me-2"></i>
                            <span id="hora-header"></span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="text-center">
                            <i class="fas fa-cogs fa-4x opacity-75 mb-2"></i>
                            <div class="h5 mb-0">SISTEMA DRTC</div>
                            <div class="small opacity-75">Gestión Administrativa</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Estadísticas DRTC -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-drtc-orange shadow-lg border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title text-uppercase mb-1">Usuarios Totales</h6>
                            <h2 class="mb-0 fw-bold">{{ $stats['total_usuarios'] ?? 0 }}</h2>
                            <small class="opacity-75">Sistema DRTC</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-3x opacity-75"></i>
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
                            <h6 class="card-title text-uppercase mb-1">Usuarios Activos</h6>
                            <h2 class="mb-0 fw-bold">{{ $stats['usuarios_activos'] ?? 0 }}</h2>
                            <small class="opacity-75">{{ round((($stats['usuarios_activos'] ?? 0) / max(1, $stats['total_usuarios'] ?? 1)) * 100) }}% del total</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-check fa-2x"></i>
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
                            <h6 class="card-title text-uppercase mb-1">Pendientes</h6>
                            <h2 class="mb-0 fw-bold">{{ $stats['usuarios_pendientes'] ?? 0 }}</h2>
                            <small class="opacity-75">Por activar</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-clock fa-2x"></i>
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
                            <h6 class="card-title text-uppercase mb-1">Roles Sistema</h6>
                            <h2 class="mb-0 fw-bold">{{ $stats['total_roles'] ?? 0 }}</h2>
                            <small class="opacity-75">Tipos de usuario</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-cog fa-2x"></i>
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
                    <h4 class="mb-0"><i class="fas fa-tools me-2"></i>Herramientas de Administración.</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-drtc-orange">
                                <div class="card-body text-center">
                                    <i class="fas fa-user-plus fa-3x text-drtc-orange mb-3"></i>
                                    <h5 class="card-title">Gestionar Usuarios</h5>
                                    <p class="card-text">Crear, editar y eliminar usuarios del sistema</p>
                                    <a href="{{ route('users.index') }}" class="btn bg-drtc-orange text-white">Gestionar</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-user-check fa-3x text-warning mb-3"></i>
                                    <h5 class="card-title">Aprobar Usuarios</h5>
                                    <p class="card-text">Revisar y aprobar solicitudes de registro</p>
                                    @if(($stats['usuarios_pendientes'] ?? 0) > 0)
                                        <span class="badge bg-danger mb-2">{{ $stats['usuarios_pendientes'] }} pendientes</span>
                                    @endif
                                    <br>
                                    <a href="{{ route('admin.users.approval') }}" class="btn btn-warning">
                                        <i class="fas fa-user-check me-1"></i>Revisar
                                    </a>
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
                    <div class="row">
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
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-users-cog fa-3x text-info mb-3"></i>
                                    <h5 class="card-title">Mantenimientos</h5>
                                    <p class="card-text">Gestionar conductores e inspectores</p>
                                    <div class="btn-group w-100" role="group">
                                        <a href="{{ route('admin.mantenimiento.conductor') }}" class="btn btn-outline-info btn-sm">Conductores</a>
                                        <a href="{{ route('admin.mantenimiento.fiscal') }}" class="btn btn-outline-info btn-sm">Inspectores</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-secondary">
                                <div class="card-body text-center">
                                    <i class="fas fa-hashtag fa-3x text-secondary mb-3"></i>
                                    <h5 class="card-title">Numeración Actas</h5>
                                    <p class="card-text">Reiniciar la secuencia de números de actas (AUTO_INCREMENT).</p>
                                    <button id="btn-reset-actas" class="btn btn-outline-secondary btn-sm">Reiniciar números</button>
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
        <div class="col-12">
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
                                            @case('inspector')
                                                <span class="badge bg-success">Inspector</span>
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
<!-- Modal y JS para reiniciar AUTO_INCREMENT de actas -->
<div class="modal fade" id="modalResetActas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reiniciar numeración de actas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p>Esta operación puede ser destructiva si la tabla contiene registros. Si la tabla está vacía, el sistema establecerá el siguiente AUTO_INCREMENT a 1.</p>
                <p>Si desea forzar el reinicio (TRUNCATE), escriba <strong>CONFIRMAR</strong> en el campo y pulse <em>Forzar reinicio</em> (eliminirá todos los registros).</p>
                <input id="confirmResetInput" type="text" class="form-control" placeholder="Escriba CONFIRMAR para forzar">
                <div id="resetFeedback" class="mt-2"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button id="btnForceReset" type="button" class="btn btn-danger">Forzar reinicio</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('btn-reset-actas');
        if (!btn) return;

        // Create bootstrap modal instance
        const modalEl = document.getElementById('modalResetActas');
        let bsModal = null;
        if (modalEl) bsModal = new bootstrap.Modal(modalEl);

        btn.addEventListener('click', function() {
                if (bsModal) bsModal.show();
        });

        const btnForce = document.getElementById('btnForceReset');
        const inputConfirm = document.getElementById('confirmResetInput');
        const feedback = document.getElementById('resetFeedback');

        async function callReset(force) {
                feedback.textContent = 'Procesando...';
                try {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                        const res = await fetch('{{ route('admin.actas.reset-autoincrement') }}', {
                                method: 'POST',
                                headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': token,
                                        'Accept': 'application/json'
                                },
                                body: JSON.stringify({ force: force })
                        });

                        const data = await res.json();
                        if (res.ok) {
                                feedback.innerHTML = '<div class="alert alert-success p-2">' + (data.message || 'Hecho') + '</div>';
                        } else {
                                feedback.innerHTML = '<div class="alert alert-danger p-2">' + (data.message || 'Error') + '</div>';
                        }
                } catch (err) {
                        feedback.innerHTML = '<div class="alert alert-danger p-2">Error de red</div>';
                }
        }

        btnForce.addEventListener('click', function() {
                const value = (inputConfirm.value || '').trim();
                if (value !== 'CONFIRMAR') {
                        // If not confirmed, try non-destructive reset (works only if table empty)
                        if (!confirm('No ha escrito CONFIRMAR. ¿Desea intentar un reinicio no destructivo (solo si la tabla está vacía)?')) return;
                        callReset(false);
                        return;
                }

                if (!confirm('ATENCIÓN: Forzar el reinicio eliminará todos los registros de la tabla actas. ¿Continuar?')) return;
                callReset(true);
        });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animar las estadísticas al cargar la página
    const statsNumbers = document.querySelectorAll('.h2.mb-0.fw-bold, .h2.mb-0');
    statsNumbers.forEach(function(stat) {
        const finalValue = parseInt(stat.textContent.replace(/[^\d]/g, ''));
        if (!isNaN(finalValue) && finalValue > 0) {
            let currentValue = 0;
            const increment = finalValue / 30; // 30 pasos de animación
            const timer = setInterval(function() {
                currentValue += increment;
                if (currentValue >= finalValue) {
                    currentValue = finalValue;
                    clearInterval(timer);
                }
                stat.textContent = Math.floor(currentValue);
            }, 50);
        }
    });
    
    // Actualizar reloj
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('es-PE');
        const timeElement = document.getElementById('hora-header');
        if (timeElement) {
            timeElement.textContent = timeString;
        }
    }
    
    updateTime();
    setInterval(updateTime, 1000);
    
    // Mostrar notificación de datos actualizados
    setTimeout(function() {
        if (typeof toastr !== 'undefined') {
            toastr.info('Dashboard actualizado con datos reales de la base de datos', 'Datos Actualizados');
        } else {
            // Crear notificación básica si toastr no está disponible
            const notification = document.createElement('div');
            notification.className = 'alert alert-info alert-dismissible fade show position-fixed';
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                <strong>Datos Actualizados:</strong> Dashboard conectado a la base de datos real.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(notification);
            
            // Auto-dismiss después de 5 segundos
            setTimeout(() => notification.remove(), 5000);
        }
    }, 1500);
});
</script>

@endsection
