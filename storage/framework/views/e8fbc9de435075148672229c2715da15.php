

<?php $__env->startSection('title', 'Gestión de Infracciones'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-exclamation-triangle me-2" style="color: #ff8c00;"></i>
                    Gestión de Infracciones
                </h2>
                <button class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nueva Infracción
                </button>
            </div>
        </div>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Total Infracciones</h5>
                            <h3><?php echo e($total_infracciones ?? 0); ?></h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-ban fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Pendientes</h5>
                            <h3><?php echo e($pendientes ?? 0); ?></h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Resueltas</h5>
                            <h3><?php echo e($resueltas ?? 0); ?></h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Este Mes</h5>
                            <h3><?php echo e($este_mes ?? 0); ?></h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros de Búsqueda -->
    <div class="card mb-4" style="border-left: 4px solid #ff8c00;">
        <div class="card-header" style="background-color: #ff8c00; color: white;">
            <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label for="numero_acta" class="form-label">N° Acta</label>
                    <input type="text" class="form-control" id="numero_acta" placeholder="Número de acta">
                </div>
                <div class="col-md-3">
                    <label for="dni_conductor" class="form-label">DNI Conductor</label>
                    <input type="text" class="form-control" id="dni_conductor" placeholder="DNI del conductor">
                </div>
                <div class="col-md-3">
                    <label for="estado_infraccion" class="form-label">Estado</label>
                    <select class="form-select" id="estado_infraccion">
                        <option value="">Todos</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="en_proceso">En Proceso</option>
                        <option value="resuelta">Resuelta</option>
                        <option value="anulada">Anulada</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-warning me-2">
                        <i class="fas fa-search me-1"></i>Buscar
                    </button>
                    <button class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Infracciones -->
    <div class="card">
        <div class="card-header" style="background-color: #ff8c00; color: white;">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Lista de Infracciones
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background-color: #ff8c00; color: white;">
                        <tr>
                            <th>N° Acta</th>
                            <th>Fecha</th>
                            <th>Conductor</th>
                            <th>Placa</th>
                            <th>Infracción</th>
                            <th>Estado</th>
                            <th>Inspector</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="infraccionesTableBody">
                        <?php $__empty_1 = true; $__currentLoopData = $infracciones ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $infraccion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><strong><?php echo e($infraccion->numero_acta ?? 'N/A'); ?></strong></td>
                            <td><?php echo e($infraccion->fecha ? $infraccion->fecha->format('d/m/Y') : 'N/A'); ?></td>
                            <td><?php echo e($infraccion->conductor_nombre ?? 'No especificado'); ?></td>
                            <td><?php echo e($infraccion->placa ?? 'N/A'); ?></td>
                            <td><?php echo e($infraccion->tipo_infraccion ?? 'No especificada'); ?></td>
                            <td>
                                <span class="badge bg-<?php echo e(($infraccion->estado ?? 'pendiente') === 'resuelta' ? 'success' : 
                                    (($infraccion->estado ?? 'pendiente') === 'en_proceso' ? 'warning' : 'danger')); ?>">
                                    <?php echo e(ucfirst($infraccion->estado ?? 'Pendiente')); ?>

                                </span>
                            </td>
                            <td><?php echo e($infraccion->inspector_nombre ?? 'No asignado'); ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" title="Imprimir">
                                    <i class="fas fa-print"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay infracciones registradas</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.table th {
    border-top: none;
    font-weight: 600;
}

.badge {
    font-size: 0.75em;
}

.btn-outline-primary:hover {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.btn-outline-success:hover {
    background-color: #198754;
    border-color: #198754;
}

.btn-outline-info:hover {
    background-color: #0dcaf0;
    border-color: #0dcaf0;
    color: #000;
}

.card-body .fas {
    opacity: 0.7;
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Login-app\resources\views\administrador\infracciones.blade.php ENDPATH**/ ?>