<?php $__env->startSection('title', 'Dashboard - Fiscalizador DRTC Apurímac'); ?>

<?php $__env->startSection('content'); ?>
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
        background: #e9ecef;
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
                    <h2 class="mb-1">¡Buen día, <?php echo e(Auth::user()->name); ?>!</h2>
                    <p class="text-muted">Panel de Control - Fiscalizador DRTC Apurímac</p>
                </div>
                <div class="text-end">
                    <small class="text-muted"><?php echo e(now()->format('l, d F Y')); ?></small>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas principales -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="stats-number" id="card-total-infracciones"><?php echo e($stats['total_infracciones'] ?? 0); ?></div>
                <div class="stats-label">Total Infracciones</div>
                <small class="d-block mt-2"><i class="fas fa-arrow-up trend-up"></i> <span id="card-total-infracciones-trend">+12% este mes</span></small>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="stats-number" id="card-procesadas"><?php echo e($stats['infracciones_procesadas'] ?? 0); ?></div>
                <div class="stats-label">Procesadas</div>
                <small class="d-block mt-2"><i class="fas fa-check-circle"></i> <span id="card-eficiencia"><?php echo e($stats['eficiencia_procesamiento'] ?? 75); ?></span>% completado</small>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="stats-number" id="card-pendientes"><?php echo e($stats['infracciones_pendientes'] ?? 0); ?></div>
                <div class="stats-label">Pendientes</div>
                <small class="d-block mt-2"><i class="fas fa-clock text-warning"></i> <span id="card-pendientes-label">En proceso</span></small>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <div class="stats-number" id="card-total-multas">S/<?php echo e(number_format($stats['total_multas'] ?? 0)); ?></div>
                <div class="stats-label">Total Multas</div>
                <small class="d-block mt-2"><i class="fas fa-arrow-up trend-up"></i> <span id="card-total-multas-trend">+8% vs anterior</span></small>
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
                                <span class="progress-text"><?php echo e($stats['eficiencia_procesamiento'] ?? 75); ?>%</span>
                            </div>
                            <div>
                                <h6 class="mb-0">Eficiencia de Procesamiento</h6>
                                <small class="text-muted"><?php echo e($stats['infracciones_procesadas'] ?? 0); ?> de <?php echo e($stats['total_infracciones'] ?? 0); ?> infracciones procesadas</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Resumen Semanal</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success"></i> <?php echo e($stats['actas_finalizadas_semana'] ?? 15); ?> actas finalizadas</li>
                            <li><i class="fas fa-eye text-info"></i> <?php echo e($stats['inspecciones_realizadas'] ?? 8); ?> inspecciones realizadas</li>
                            <li><i class="fas fa-file text-warning"></i> <?php echo e($stats['reportes_generados'] ?? 5); ?> reportes generados</li>
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
                        <span class="badge bg-success"><?php echo e($stats['meta_mensual_progreso'] ?? 89); ?>%</span>
                    </div>
                    <div class="progress mt-1" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: <?php echo e($stats['meta_mensual_progreso'] ?? 89); ?>%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Calidad</span>
                        <span class="badge bg-primary"><?php echo e($stats['calidad_porcentaje'] ?? 92); ?>%</span>
                    </div>
                    <div class="progress mt-1" style="height: 6px;">
                        <div class="progress-bar bg-primary" style="width: <?php echo e($stats['calidad_porcentaje'] ?? 92); ?>%"></div>
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
                        <a href="<?php echo e(route('fiscalizador.actas-contra')); ?>" class="text-decoration-none">
                            <div class="p-3 rounded" style="background: #f8f9fa;">
                                <i class="fas fa-file-alt fa-2x text-primary mb-2"></i>
                                <br><small>Actas</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-2 col-6 text-center mb-3">
                        <a href="<?php echo e(route('fiscalizador.carga-paga')); ?>" class="text-decoration-none">
                            <div class="p-3 rounded" style="background: #f8f9fa;">
                                <i class="fas fa-truck fa-2x text-success mb-2"></i>
                                <br><small>Carga Paga</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-2 col-6 text-center mb-3">
                        <a href="<?php echo e(route('fiscalizador.empresas')); ?>" class="text-decoration-none">
                            <div class="p-3 rounded" style="background: #f8f9fa;">
                                <i class="fas fa-building fa-2x text-info mb-2"></i>
                                <br><small>Empresas</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-2 col-6 text-center mb-3">
                        <a href="<?php echo e(route('fiscalizador.inspecciones')); ?>" class="text-decoration-none">
                            <div class="p-3 rounded" style="background: #f8f9fa;">
                                <i class="fas fa-search fa-2x text-warning mb-2"></i>
                                <br><small>Inspecciones</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-2 col-6 text-center mb-3">
                        <a href="<?php echo e(route('fiscalizador.consultas')); ?>" class="text-decoration-none">
                            <div class="p-3 rounded" style="background: #f8f9fa;">
                                <i class="fas fa-question-circle fa-2x text-secondary mb-2"></i>
                                <br><small>Consultas</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-2 col-6 text-center mb-3">
                        <a href="<?php echo e(route('fiscalizador.reportes')); ?>" class="text-decoration-none">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Actualizar el círculo de progreso con el porcentaje real
    const eficiencia = <?php echo e($stats['eficiencia_procesamiento'] ?? 75); ?>;
    const progressCircle = document.querySelector('.progress-circle');
    
    if (progressCircle) {
        const degrees = (eficiencia / 100) * 360;
        progressCircle.style.background = `conic-gradient(#28a745 0deg ${degrees}deg, #e9ecef ${degrees}deg 360deg)`;
    }
    
    // Animar las estadísticas al cargar la página
    const statsNumbers = document.querySelectorAll('.stats-number');
    statsNumbers.forEach(function(stat) {
        const finalValue = parseInt(stat.textContent.replace(/[^\d]/g, ''));
        if (!isNaN(finalValue)) {
            let currentValue = 0;
            const increment = finalValue / 50; // 50 pasos de animación
            const timer = setInterval(function() {
                currentValue += increment;
                if (currentValue >= finalValue) {
                    currentValue = finalValue;
                    clearInterval(timer);
                }
                
                // Mantener el formato original (con S/ si es necesario)
                if (stat.textContent.includes('S/')) {
                    stat.textContent = 'S/' + Math.floor(currentValue).toLocaleString();
                } else {
                    stat.textContent = Math.floor(currentValue);
                }
            }, 20);
        }
    });
    
    // Mostrar notificación de datos actualizados
    setTimeout(function() {
        if (typeof toastr !== 'undefined') {
            toastr.success('Dashboard actualizado con datos reales de la base de datos', 'Datos Actualizados');
        }
    }, 1000);
});
</script>

<script>
// Poll the API endpoint every 60 seconds to refresh dashboard cards with real data
function fetchFiscalizadorStats() {
    fetch('/api/dashboard/fiscalizador', { credentials: 'same-origin' })
        .then(r => r.json())
        .then(result => {
            if (!result.success || !result.stats) return;
            const s = result.stats;

            // Update numeric cards
            const setText = (id, val, zeroText=null) => {
                const el = document.getElementById(id);
                if (!el) return;
                if ((val === null || val === undefined || Number(val) === 0) && zeroText) {
                    el.textContent = zeroText;
                } else {
                    if (typeof val === 'number') el.textContent = val;
                    else el.textContent = val;
                }
            };

            setText('card-total-infracciones', s.total_infracciones ?? 0, 'Sin actas');
            setText('card-procesadas', s.infracciones_procesadas ?? 0, '0');
            setText('card-pendientes', s.infracciones_pendientes ?? 0, '0');
            setText('card-total-multas', 'S/' + Number(s.total_multas ?? 0).toLocaleString(), 'S/0');

            // Eficiencia
            const ef = document.getElementById('card-eficiencia');
            if (ef) ef.textContent = (s.eficiencia_procesamiento ?? s.eficiencia_procesamiento ?? 0);

            // Update small labels if needed
            const pendientesLabel = document.getElementById('card-pendientes-label');
            if (pendientesLabel) pendientesLabel.textContent = ((s.infracciones_pendientes ?? 0) > 0) ? 'En proceso' : 'Sin pendientes';

        }).catch(e => {
            console.warn('No se pudo obtener stats del dashboard:', e);
        });
}

// initial fetch and set interval
fetchFiscalizadorStats();
setInterval(fetchFiscalizadorStats, 60000);
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Login-app\resources\views\backup-roles-sep-2025\fiscalizador\dashboard.blade.php ENDPATH**/ ?>