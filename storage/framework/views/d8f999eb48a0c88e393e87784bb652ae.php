<?php $__env->startSection('title', 'Carga y Pasajero'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0">
                <i class="fas fa-truck me-2" style="color: #ff8c00;"></i>
                Gestión de Carga y Pasajero
            </h2>
        </div>
    </div>

    <!-- Flexbox de opciones principales -->
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100" style="border-color: #ff8c00;">
                <div class="card-header text-center" style="background-color: #ff8c00; color: white;">
                    <h5><i class="fas fa-weight-hanging me-2"></i>Control de Carga</h5>
                </div>
                <div class="card-body text-center">
                    <p>Gestionar el control de peso y dimensiones de vehículos de carga</p>
                    <button class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>Nuevo Control
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100" style="border-color: #ff8c00;">
                <div class="card-header text-center" style="background-color: #ff8c00; color: white;">
                    <h5><i class="fas fa-users me-2"></i>Control de Pasajeros</h5>
                </div>
                <div class="card-body text-center">
                    <p>Gestionar registro y control de transporte de pasajeros</p>
                    <button class="btn btn-success btn-lg">
                        <i class="fas fa-user-plus me-2"></i>Nuevo Registro
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100" style="border-color: #ff8c00;">
                <div class="card-header text-center" style="background-color: #ff8c00; color: white;">
                    <h5><i class="fas fa-balance-scale me-2"></i>Verificación</h5>
                </div>
                <div class="card-body text-center">
                    <p>Verificar estado de pagos y validar documentación</p>
                    <button class="btn btn-info btn-lg">
                        <i class="fas fa-search me-2"></i>Verificar Estado
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de registros recientes -->
    <div class="card mt-4">
        <div class="card-header" style="background-color: #fff3e0; border-color: #ff8c00;">
            <h5 class="mb-0" style="color: #ff8c00;">
                <i class="fas fa-list me-2"></i>Registros Recientes
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead style="background-color: #ff8c00; color: white;">
                        <tr>
                            <th>Fecha</th>
                            <th>Placa</th>
                            <th>Tipo</th>
                            <th>Peso/Monto</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="hover-row">
                            <td>30/07/2025</td>
                            <td>ABC-123</td>
                            <td>Control de Carga</td>
                            <td>15.5 TN</td>
                            <td><span class="badge bg-success">Aprobado</span></td>
                            <td>
                                <div class="action-menu">
                                    <button class="btn btn-sm btn-outline-primary action-trigger">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <div class="action-dropdown">
                                        <button class="btn btn-sm btn-outline-warning mb-1">
                                            <i class="fas fa-tools me-1"></i>Mantenimiento
                                        </button>
                                        <button class="btn btn-sm btn-outline-info mb-1">
                                            <i class="fas fa-edit me-1"></i>Editar
                                        </button>
                                        <button class="btn btn-sm btn-outline-success mb-1">
                                            <i class="fas fa-check me-1"></i>Aprobar
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash me-1"></i>Eliminar
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr class="hover-row">
                            <td>30/07/2025</td>
                            <td>XYZ-789</td>
                            <td>Control de Pasajeros</td>
                            <td>25 personas</td>
                            <td><span class="badge bg-info">Procesado</span></td>
                            <td>
                                <div class="action-menu">
                                    <button class="btn btn-sm btn-outline-primary action-trigger">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <div class="action-dropdown">
                                        <button class="btn btn-sm btn-outline-warning mb-1">
                                            <i class="fas fa-tools me-1"></i>Mantenimiento
                                        </button>
                                        <button class="btn btn-sm btn-outline-info mb-1">
                                            <i class="fas fa-edit me-1"></i>Editar
                                        </button>
                                        <button class="btn btn-sm btn-outline-success mb-1">
                                            <i class="fas fa-check me-1"></i>Aprobar
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash me-1"></i>Eliminar
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.hover-row {
    transition: background-color 0.3s ease;
}

.hover-row:hover {
    background-color: #fff3e0 !important;
}

.action-menu {
    position: relative;
    display: inline-block;
}

.action-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    display: none;
    flex-direction: column;
    min-width: 140px;
    padding: 8px;
    background-color: white;
    border: 1px solid #ff8c00;
    border-radius: 5px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.action-menu:hover .action-dropdown {
    display: flex;
}

.action-trigger {
    cursor: pointer;
}

.action-dropdown button {
    width: 100%;
    text-align: left;
    font-size: 12px;
    padding: 4px 8px;
}

.action-dropdown button:hover {
    transform: translateX(2px);
    transition: transform 0.2s ease;
}
</style>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Login-app\resources\views\backup-roles-sep-2025\fiscalizador\carga-paga.blade.php ENDPATH**/ ?>