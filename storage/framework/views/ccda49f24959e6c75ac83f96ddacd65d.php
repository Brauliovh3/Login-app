<?php $__env->startSection('title', 'Cola de Espera'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .border-left-primary {
        border-left: 0.25rem solid #ff8c00 !important;
    }
    
    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }
    
    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
    
    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(255, 140, 0, 0.1);
    }
    
    .btn-group-toggle .btn {
        transition: all 0.3s ease;
    }
    
    .card {
        transition: transform 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 2rem rgba(0, 0, 0, 0.15) !important;
    }
    
    .badge {
        font-size: 0.75rem;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .alert {
        animation: fadeIn 0.5s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .btn-lg {
        padding: 0.75rem 1.25rem;
        font-size: 1rem;
        font-weight: 500;
    }
    
    .text-xs {
        font-size: 0.7rem;
    }
    
    .shadow {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
    }
</style>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-users me-2" style="color: #ff8c00;"></i>
                    Cola de Espera - Sistema de Turnos
                </h2>
                <button class="btn btn-success" onclick="generarTurno()">
                    <i class="fas fa-plus me-2"></i>Generar Turno
                </button>
            </div>
        </div>
    </div>

    <!-- Panel de control -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Turno Actual
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="turnoActual">A-001</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x" style="color: #ff8c00;"></i>
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
                                En Espera
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="enEspera">8</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-warning"></i>
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
                                Atendidos Hoy
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="atendidos">25</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check fa-2x text-success"></i>
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
                                Tiempo Promedio
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="tiempoPromedio">15 min</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-stopwatch fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Controles de turno -->
    <div class="card mb-4 shadow">
        <div class="card-header" style="background: linear-gradient(87deg, #ff8c00 0, #e67c00 100%); color: white;">
            <h5 class="mb-0">
                <i class="fas fa-cog me-2"></i>Control de Turnos
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-lg-3 col-md-6">
                    <div class="d-grid">
                        <button class="btn btn-success btn-lg" onclick="siguienteTurno()">
                            <i class="fas fa-forward me-2"></i>Siguiente Turno
                        </button>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="d-grid">
                        <button class="btn btn-warning btn-lg" onclick="llamarTurno()">
                            <i class="fas fa-volume-up me-2"></i>Llamar Turno
                        </button>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="d-grid">
                        <button class="btn btn-info btn-lg" onclick="pausarTurno()">
                            <i class="fas fa-pause me-2"></i>Pausar Atención
                        </button>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="d-grid">
                        <button class="btn btn-danger btn-lg" onclick="cancelarTurno()">
                            <i class="fas fa-times me-2"></i>Cancelar Turno
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Estado del sistema -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="alert alert-light border d-flex align-items-center" role="alert">
                        <i class="fas fa-info-circle me-2" style="color: #ff8c00;"></i>
                        <div>
                            <strong>Estado del Sistema:</strong> 
                            <span id="estadoSistema" class="text-success">Activo</span> | 
                            <strong>Última Actualización:</strong> 
                            <span id="ultimaActualizacion"><?php echo e(date('H:i:s')); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de turnos en espera -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3" style="background-color: #fff3e0; border-color: #ff8c00;">
                    <h6 class="m-0 font-weight-bold" style="color: #ff8c00;">
                        <i class="fas fa-list me-2"></i>Turnos en Espera
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                            <thead style="background-color: #ff8c00; color: white;">
                                <tr>
                                    <th>Turno</th>
                                    <th>Hora</th>
                                    <th>Usuario</th>
                                    <th>Tipo de Atención</th>
                                    <th>Tiempo</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="listaTurnos">
                                <tr>
                                    <td><span class="badge bg-primary fs-6">A-002</span></td>
                                    <td>09:15</td>
                                    <td>Juan Pérez Gómez</td>
                                    <td><span class="badge bg-info">Consulta General</span></td>
                                    <td><span class="text-warning">5 min</span></td>
                                    <td><span class="badge bg-warning">En Espera</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-success me-1" onclick="atenderTurno('A-002')" title="Atender">
                                            <i class="fas fa-play"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="cancelarTurno('A-002')" title="Cancelar">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-primary fs-6">A-003</span></td>
                                    <td>09:20</td>
                                    <td>María López Silva</td>
                                    <td><span class="badge bg-success">Pago de Multa</span></td>
                                    <td><span class="text-warning">2 min</span></td>
                                    <td><span class="badge bg-warning">En Espera</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-success me-1" onclick="atenderTurno('A-003')" title="Atender">
                                            <i class="fas fa-play"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="cancelarTurno('A-003')" title="Cancelar">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-primary fs-6">A-004</span></td>
                                    <td>09:22</td>
                                    <td>Carlos Ruiz Mendoza</td>
                                    <td><span class="badge bg-warning">Trámite de Licencia</span></td>
                                    <td><span class="text-success">1 min</span></td>
                                    <td><span class="badge bg-warning">En Espera</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-success me-1" onclick="atenderTurno('A-004')" title="Atender">
                                            <i class="fas fa-play"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="cancelarTurno('A-004')" title="Cancelar">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel lateral con estadísticas -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3" style="background-color: #fff3e0; border-color: #ff8c00;">
                    <h6 class="m-0 font-weight-bold" style="color: #ff8c00;">
                        <i class="fas fa-chart-pie me-2"></i>Estadísticas del Día
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="myPieChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Atendidos
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-warning"></i> En Espera
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-danger"></i> Cancelados
                        </span>
                    </div>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header py-3" style="background-color: #fff3e0; border-color: #ff8c00;">
                    <h6 class="m-0 font-weight-bold" style="color: #ff8c00;">
                        <i class="fas fa-clock me-2"></i>Tiempos de Atención
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tiempo Mínimo:</span>
                        <span class="text-success fw-bold">5 min</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tiempo Máximo:</span>
                        <span class="text-danger fw-bold">25 min</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tiempo Promedio:</span>
                        <span class="text-primary fw-bold">15 min</span>
                    </div>
                    <hr>
                    <div class="text-center">
                        <small class="text-muted">Última actualización: <?php echo e(date('H:i:s')); ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let contadorTurnos = 5;
let turnoActual = 'A-001';
let sistemaPausado = false;

function generarTurno() {
    const nuevoTurno = 'A-' + String(contadorTurnos).padStart(3, '0');
    contadorTurnos++;
    
    showSuccess(`Turno ${nuevoTurno} generado exitosamente`);
    
    // Actualizar contador de espera
    const enEspera = document.getElementById('enEspera');
    enEspera.textContent = parseInt(enEspera.textContent) + 1;
    
    actualizarHora();
}

function siguienteTurno() {
    if (sistemaPausado) {
        showWarning('El sistema está pausado. Reactive la atención para continuar.');
        return;
    }
    
    // Actualizar turno actual
    const siguiente = 'A-' + String(parseInt(turnoActual.split('-')[1]) + 1).padStart(3, '0');
    document.getElementById('turnoActual').textContent = siguiente;
    turnoActual = siguiente;
    
    // Actualizar contadores
    const enEspera = document.getElementById('enEspera');
    const atendidos = document.getElementById('atendidos');
    
    if (parseInt(enEspera.textContent) > 0) {
        enEspera.textContent = parseInt(enEspera.textContent) - 1;
        atendidos.textContent = parseInt(atendidos.textContent) + 1;
        
        showSuccess(`Pasando al turno ${siguiente}`);
    } else {
        showWarning('No hay turnos en espera');
    }
    
    actualizarHora();
}

function llamarTurno() {
    if (sistemaPausado) {
        showWarning('El sistema está pausado. Reactive la atención para continuar.');
        return;
    }
    
    showInfo(`🔊 Llamando al turno ${turnoActual}. Por favor acérquese a la ventanilla.`);
    
    // Simular llamada por altavoz
    setTimeout(() => {
        showInfo(`Turno ${turnoActual} llamado por segunda vez`);
    }, 3000);
}

function pausarTurno() {
    sistemaPausado = !sistemaPausado;
    const estadoElement = document.getElementById('estadoSistema');
    
    if (sistemaPausado) {
        estadoElement.textContent = 'Pausado';
        estadoElement.className = 'text-danger';
        showWarning('⏸️ Atención pausada. Los turnos no avanzarán.');
    } else {
        estadoElement.textContent = 'Activo';
        estadoElement.className = 'text-success';
        showSuccess('▶️ Atención reactivada. El sistema está funcionando normalmente.');
    }
    
    actualizarHora();
}

function cancelarTurno(turno = null) {
    const turnoACancelar = turno || turnoActual;
    
    showError(`❌ Turno ${turnoACancelar} cancelado`);
    
    // Si se cancela un turno específico de la lista
    if (turno) {
        // Buscar y remover la fila de la tabla
        const filas = document.querySelectorAll('#listaTurnos tr');
        filas.forEach(function(fila) {
            const turnoEnFila = fila.cells[0].textContent.trim();
            if (turnoEnFila.includes(turno)) {
                fila.remove();
                
                // Actualizar contador
                const enEspera = document.getElementById('enEspera');
                enEspera.textContent = parseInt(enEspera.textContent) - 1;
            }
        });
    }
    
    actualizarHora();
}

function atenderTurno(turno) {
    if (sistemaPausado) {
        showWarning('El sistema está pausado. Reactive la atención para continuar.');
        return;
    }
    
    showSuccess(`✅ Atendiendo turno ${turno}`);
    
    // Remover el turno de la tabla
    const filas = document.querySelectorAll('#listaTurnos tr');
    filas.forEach(function(fila) {
        const turnoEnFila = fila.cells[0].textContent.trim();
        if (turnoEnFila.includes(turno)) {
            fila.style.transition = 'all 0.5s ease';
            fila.style.backgroundColor = '#d4edda';
            
            setTimeout(() => {
                fila.remove();
                
                // Actualizar contadores
                const enEspera = document.getElementById('enEspera');
                const atendidos = document.getElementById('atendidos');
                
                enEspera.textContent = parseInt(enEspera.textContent) - 1;
                atendidos.textContent = parseInt(atendidos.textContent) + 1;
            }, 500);
        }
    });
    
    actualizarHora();
}

function actualizarHora() {
    const ahora = new Date();
    const horaFormateada = ahora.toLocaleTimeString('es-ES');
    document.getElementById('ultimaActualizacion').textContent = horaFormateada;
}

// Actualizar tiempos cada minuto
setInterval(function() {
    if (!sistemaPausado) {
        // Simular actualización de tiempos de espera
        const filas = document.querySelectorAll('#listaTurnos tr');
        filas.forEach(function(fila) {
            const tiempoCell = fila.cells[4];
            const tiempoSpan = tiempoCell.querySelector('span');
            let tiempo = parseInt(tiempoSpan.textContent);
            tiempo += 1;
            tiempoSpan.textContent = tiempo + ' min';
            
            // Cambiar color según el tiempo
            if (tiempo > 15) {
                tiempoSpan.className = 'text-danger';
            } else if (tiempo > 10) {
                tiempoSpan.className = 'text-warning';
            } else {
                tiempoSpan.className = 'text-success';
            }
        });
        
        actualizarHora();
    }
}, 60000);

// Actualizar hora cada segundo
setInterval(actualizarHora, 1000);

// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    actualizarHora();
    
    // Simular datos dinámicos
    setTimeout(() => {
        showInfo('Sistema de turnos iniciado correctamente');
    }, 1000);
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Login-app\resources\views/ventanilla/cola-espera.blade.php ENDPATH**/ ?>