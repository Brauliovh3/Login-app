<?php $__env->startSection('title', 'Reportes'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0">
                <i class="fas fa-chart-bar me-2" style="color: #ff8c00;"></i>
                Reportes y Estadísticas
            </h2>
        </div>
    </div>

    <!-- Opciones de reportes -->
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100" style="border-color: #ff8c00;">
                <div class="card-header text-center" style="background-color: #ff8c00; color: white;">
                    <h5><i class="fas fa-file-pdf me-2"></i>Reporte de Actas</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="fecha_inicio_actas" class="form-label">Fecha Inicio</label>
                        <input type="date" class="form-control" id="fecha_inicio_actas">
                    </div>
                    <div class="mb-3">
                        <label for="fecha_fin_actas" class="form-label">Fecha Fin</label>
                        <input type="date" class="form-control" id="fecha_fin_actas">
                    </div>
                    <button class="btn btn-danger btn-block w-100">
                        <i class="fas fa-file-pdf me-2"></i>Generar PDF
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100" style="border-color: #ff8c00;">
                <div class="card-header text-center" style="background-color: #ff8c00; color: white;">
                    <h5><i class="fas fa-file-excel me-2"></i>Reporte de Multas</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="estado_multas" class="form-label">Estado</label>
                        <select class="form-select" id="estado_multas">
                            <option value="">Todos</option>
                            <option value="pendiente">Pendientes</option>
                            <option value="pagada">Pagadas</option>
                            <option value="vencida">Vencidas</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="mes_multas" class="form-label">Mes</label>
                        <select class="form-select" id="mes_multas">
                            <option value="">Todos</option>
                            <option value="01">Enero</option>
                            <option value="02">Febrero</option>
                            <option value="03">Marzo</option>
                            <option value="07">Julio</option>
                        </select>
                    </div>
                    <button class="btn btn-success btn-block w-100">
                        <i class="fas fa-file-excel me-2"></i>Generar Excel
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100" style="border-color: #ff8c00;">
                <div class="card-header text-center" style="background-color: #ff8c00; color: white;">
                    <h5><i class="fas fa-chart-pie me-2"></i>Estadísticas</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="tipo_estadistica" class="form-label">Tipo de Estadística</label>
                        <select class="form-select" id="tipo_estadistica">
                            <option value="infracciones">Por Infracciones</option>
                            <option value="empresas">Por Empresas</option>
                            <option value="conductores">Por Conductores</option>
                            <option value="vehiculos">Por Vehículos</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="periodo" class="form-label">Período</label>
                        <select class="form-select" id="periodo">
                            <option value="mes">Este Mes</option>
                            <option value="trimestre">Trimestre</option>
                            <option value="semestre">Semestre</option>
                            <option value="año">Este Año</option>
                        </select>
                    </div>
                    <button class="btn btn-info btn-block w-100">
                        <i class="fas fa-chart-bar me-2"></i>Ver Gráficos
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos de ejemplo -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header" style="background-color: #fff3e0; border-color: #ff8c00;">
                    <h5 class="mb-0" style="color: #ff8c00;">
                        <i class="fas fa-chart-pie me-2"></i>Infracciones por Tipo
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="chartInfracciones" width="400" height="200"></canvas>
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-square text-warning me-1"></i>Graves: 45%
                            <i class="fas fa-square text-danger me-1 ms-3"></i>Muy Graves: 30%
                            <i class="fas fa-square text-info me-1 ms-3"></i>Leves: 25%
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header" style="background-color: #fff3e0; border-color: #ff8c00;">
                    <h5 class="mb-0" style="color: #ff8c00;">
                        <i class="fas fa-chart-line me-2"></i>Multas por Mes
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="chartMultas" width="400" height="200"></canvas>
                    <div class="mt-3">
                        <small class="text-muted">
                            Evolución mensual de multas emitidas en 2025
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Aquí irían los gráficos con Chart.js
document.addEventListener('DOMContentLoaded', function() {
    // Simulación de gráficos
    const ctx1 = document.getElementById('chartInfracciones').getContext('2d');
    ctx1.fillStyle = '#ff8c00';
    ctx1.fillRect(50, 50, 100, 100);
    ctx1.fillStyle = '#fff';
    ctx1.font = '16px Arial';
    ctx1.fillText('Gráfico de', 80, 90);
    ctx1.fillText('Infracciones', 75, 110);
    
    const ctx2 = document.getElementById('chartMultas').getContext('2d');
    ctx2.fillStyle = '#ff8c00';
    ctx2.fillRect(50, 50, 100, 100);
    ctx2.fillStyle = '#fff';
    ctx2.font = '16px Arial';
    ctx2.fillText('Gráfico de', 80, 90);
    ctx2.fillText('Multas', 85, 110);
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Login-app\resources\views\backup-roles-sep-2025\fiscalizador\reportes.blade.php ENDPATH**/ ?>