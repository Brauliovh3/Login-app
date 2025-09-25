<?php $__env->startSection('title', 'Consultar Información'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0">
                <i class="fas fa-search me-2" style="color: #ff8c00;"></i>
                Consultar Información
            </h2>
        </div>
    </div>

    <!-- Opciones de consulta -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100" style="border-color: #ff8c00;">
                <div class="card-header" style="background-color: #ff8c00; color: white;">
                    <h5><i class="fas fa-file-alt me-2"></i>Consulta de Multas</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="consulta_placa_multa" class="form-label">Placa del Vehículo</label>
                        <input type="text" class="form-control" id="consulta_placa_multa" placeholder="ABC-123">
                    </div>
                    <div class="mb-3">
                        <label for="consulta_dni_multa" class="form-label">DNI del Conductor</label>
                        <input type="text" class="form-control" id="consulta_dni_multa" placeholder="12345678">
                    </div>
                    <button class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Buscar Multas
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100" style="border-color: #ff8c00;">
                <div class="card-header" style="background-color: #ff8c00; color: white;">
                    <h5><i class="fas fa-id-card me-2"></i>Estado de Licencia</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="consulta_dni_licencia" class="form-label">DNI del Conductor</label>
                        <input type="text" class="form-control" id="consulta_dni_licencia" placeholder="12345678">
                    </div>
                    <div class="mb-3">
                        <label for="consulta_numero_licencia" class="form-label">Número de Licencia</label>
                        <input type="text" class="form-control" id="consulta_numero_licencia" placeholder="A-IIIa-123456">
                    </div>
                    <button class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Consultar Licencia
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100" style="border-color: #ff8c00;">
                <div class="card-header" style="background-color: #ff8c00; color: white;">
                    <h5><i class="fas fa-car me-2"></i>Información de Vehículo</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="consulta_placa_vehiculo" class="form-label">Placa del Vehículo</label>
                        <input type="text" class="form-control" id="consulta_placa_vehiculo" placeholder="ABC-123">
                    </div>
                    <div class="mb-3">
                        <label for="consulta_tarjeta" class="form-label">N° Tarjeta de Propiedad</label>
                        <input type="text" class="form-control" id="consulta_tarjeta" placeholder="Número de tarjeta">
                    </div>
                    <button class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Consultar Vehículo
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100" style="border-color: #ff8c00;">
                <div class="card-header" style="background-color: #ff8c00; color: white;">
                    <h5><i class="fas fa-folder me-2"></i>Estado de Trámite</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="consulta_numero_tramite" class="form-label">Número de Trámite</label>
                        <input type="text" class="form-control" id="consulta_numero_tramite" placeholder="TRA-2025-001">
                    </div>
                    <div class="mb-3">
                        <label for="consulta_dni_tramite" class="form-label">DNI del Solicitante</label>
                        <input type="text" class="form-control" id="consulta_dni_tramite" placeholder="12345678">
                    </div>
                    <button class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Consultar Trámite
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

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Login-app\resources\views\ventanilla\consultar.blade.php ENDPATH**/ ?>