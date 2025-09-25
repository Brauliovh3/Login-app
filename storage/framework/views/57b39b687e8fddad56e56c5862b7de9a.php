<?php $__env->startSection('title', 'Consultas'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0">
                <i class="fas fa-search me-2" style="color: #ff8c00;"></i>
                Consultas del Sistema
            </h2>
        </div>
    </div>

    <!-- Opciones de consulta -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100" style="border-color: #ff8c00;">
                <div class="card-header" style="background-color: #ff8c00; color: white;">
                    <h5><i class="fas fa-car me-2"></i>Consulta de Vehículos</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="consulta_placa" class="form-label">Placa del Vehículo</label>
                        <input type="text" class="form-control" id="consulta_placa" placeholder="ABC-123">
                    </div>
                    <button class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Buscar Vehículo
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100" style="border-color: #ff8c00;">
                <div class="card-header" style="background-color: #ff8c00; color: white;">
                    <h5><i class="fas fa-id-card me-2"></i>Consulta de Conductores</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="consulta_dni" class="form-label">DNI del Conductor</label>
                        <input type="text" class="form-control" id="consulta_dni" placeholder="12345678">
                    </div>
                    <button class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Buscar Conductor
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100" style="border-color: #ff8c00;">
                <div class="card-header" style="background-color: #ff8c00; color: white;">
                    <h5><i class="fas fa-file-alt me-2"></i>Consulta de Actas</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="consulta_acta" class="form-label">Número de Acta</label>
                        <input type="text" class="form-control" id="consulta_acta" placeholder="ACT-2025-001">
                    </div>
                    <button class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Buscar Acta
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100" style="border-color: #ff8c00;">
                <div class="card-header" style="background-color: #ff8c00; color: white;">
                    <h5><i class="fas fa-building me-2"></i>Consulta de Empresas</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="consulta_ruc" class="form-label">RUC de la Empresa</label>
                        <input type="text" class="form-control" id="consulta_ruc" placeholder="20123456789">
                    </div>
                    <button class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Buscar Empresa
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Área de resultados -->
    <div class="card mt-4">
        <div class="card-header" style="background-color: #fff3e0; border-color: #ff8c00;">
            <h5 class="mb-0" style="color: #ff8c00;">
                <i class="fas fa-list me-2"></i>Resultados de la Consulta
            </h5>
        </div>
        <div class="card-body">
            <div class="text-center py-5 text-muted">
                <i class="fas fa-search fa-3x mb-3"></i>
                <p>Seleccione un tipo de consulta y complete los datos para ver los resultados</p>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Login-app\resources\views\fiscalizador\consultas.blade.php ENDPATH**/ ?>