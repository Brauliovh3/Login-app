<?php $__env->startSection('title', 'Aprobar Usuarios'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-user-check me-2" style="color: #ff8c00;"></i>
                    Aprobar Usuarios
                </h2>
                <div class="badge bg-warning text-dark fs-6">
                    <i class="fas fa-clock me-1"></i>
                    <?php echo e($usuarios_pendientes ?? 0); ?> pendientes
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros de Búsqueda -->
    <div class="card mb-4" style="border-left: 4px solid #ff8c00;">
        <div class="card-header" style="background-color: #ff8c00; color: white;">
            <i class="fas fa-filter me-2"></i>Filtros
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <label for="buscar_pendiente" class="form-label">Email/Usuario</label>
                    <input type="text" class="form-control" id="buscar_pendiente" placeholder="Buscar usuario pendiente">
                </div>
                <div class="col-md-4">
                    <label for="fecha_filtro" class="form-label">Fecha de Registro</label>
                    <input type="date" class="form-control" id="fecha_filtro">
                </div>
                <div class="col-md-4 d-flex align-items-end">
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

    <!-- Acciones Rápidas -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Aprobar Seleccionados</h5>
                            <p class="card-text">Aprobar múltiples usuarios a la vez</p>
                        </div>
                        <div class="align-self-center">
                            <button class="btn btn-light" id="aprobar-seleccionados" disabled>
                                <i class="fas fa-check me-1"></i>Aprobar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Rechazar Seleccionados</h5>
                            <p class="card-text">Rechazar múltiples solicitudes</p>
                        </div>
                        <div class="align-self-center">
                            <button class="btn btn-light" id="rechazar-seleccionados" disabled>
                                <i class="fas fa-times me-1"></i>Rechazar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Usuarios Pendientes -->
    <div class="card">
        <div class="card-header" style="background-color: #ff8c00; color: white;">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Usuarios Pendientes de Aprobación
                </h5>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="seleccionar-todos">
                    <label class="form-check-label text-white" for="seleccionar-todos">
                        Seleccionar todos
                    </label>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background-color: #ff8c00; color: white;">
                        <tr>
                            <th width="50">
                                <input type="checkbox" class="form-check-input" id="check-all">
                            </th>
                            <th>Email</th>
                            <th>Nombres</th>
                            <th>Teléfono</th>
                            <th>Fecha Registro</th>
                            <th>IP Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="pendientesTableBody">
                        <?php $__empty_1 = true; $__currentLoopData = $usuarios_pendientes_lista ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $usuario): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input user-checkbox" value="<?php echo e($usuario->id); ?>">
                            </td>
                            <td><strong><?php echo e($usuario->email); ?></strong></td>
                            <td><?php echo e($usuario->name ?? 'No especificado'); ?></td>
                            <td><?php echo e('No especificado'); ?></td>
                            <td><?php echo e($usuario->created_at->format('d/m/Y H:i')); ?></td>
                            <td><?php echo e('No registrada'); ?></td>
                            <td>
                                <button class="btn btn-sm btn-success me-1" title="Aprobar usuario" onclick="aprobarUsuario(<?php echo e($usuario->id); ?>)">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-sm btn-danger me-1" title="Rechazar usuario" onclick="rechazarUsuario(<?php echo e($usuario->id); ?>)">
                                    <i class="fas fa-times"></i>
                                </button>
                                <button class="btn btn-sm btn-info" title="Ver detalles" onclick="verDetalles(<?php echo e($usuario->id); ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-user-check fa-3x text-success mb-3"></i>
                                <p class="text-muted">¡Excelente! No hay usuarios pendientes de aprobación</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="confirmacionModal" tabindex="-1" aria-labelledby="confirmacionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" id="modal-header">
                <h5 class="modal-title" id="confirmacionModalLabel">Confirmar Acción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center mb-3">
                    <i id="modal-icon" class="fas fa-question-circle fa-2x me-3"></i>
                    <div>
                        <p class="mb-1" id="modal-mensaje">¿Está seguro de realizar esta acción?</p>
                        <small class="text-muted" id="modal-detalle">Esta acción no se puede deshacer.</small>
                    </div>
                </div>
                <div class="alert alert-warning" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Advertencia:</strong> Una vez realizada esta acción, será permanente.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn" id="confirmar-accion">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<script>
let accionPendiente = null;
let usuarioIdPendiente = null;

// Seleccionar todos los checkboxes
document.getElementById('seleccionar-todos').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.user-checkbox');
    checkboxes.forEach(checkbox => checkbox.checked = this.checked);
    toggleBotonesAccion();
});

// Habilitar/deshabilitar botones de acción
function toggleBotonesAccion() {
    const checkboxes = document.querySelectorAll('.user-checkbox:checked');
    const aprobarBtn = document.getElementById('aprobar-seleccionados');
    const rechazarBtn = document.getElementById('rechazar-seleccionados');
    
    if (checkboxes.length > 0) {
        aprobarBtn.disabled = false;
        rechazarBtn.disabled = false;
    } else {
        aprobarBtn.disabled = true;
        rechazarBtn.disabled = true;
    }
}

// Función para mostrar modal de confirmación
function mostrarModalConfirmacion(tipo, mensaje, detalle, callback) {
    const modal = new bootstrap.Modal(document.getElementById('confirmacionModal'));
    const modalHeader = document.getElementById('modal-header');
    const modalIcon = document.getElementById('modal-icon');
    const modalMensaje = document.getElementById('modal-mensaje');
    const modalDetalle = document.getElementById('modal-detalle');
    const confirmarBtn = document.getElementById('confirmar-accion');
    
    // Configurar colores según el tipo
    if (tipo === 'aprobar') {
        modalHeader.className = 'modal-header bg-success text-white';
        modalIcon.className = 'fas fa-check-circle fa-2x me-3 text-success';
        confirmarBtn.className = 'btn btn-success';
        confirmarBtn.textContent = 'Aprobar';
    } else if (tipo === 'rechazar') {
        modalHeader.className = 'modal-header bg-danger text-white';
        modalIcon.className = 'fas fa-times-circle fa-2x me-3 text-danger';
        confirmarBtn.className = 'btn btn-danger';
        confirmarBtn.textContent = 'Rechazar';
    }
    
    modalMensaje.textContent = mensaje;
    modalDetalle.textContent = detalle;
    
    // Limpiar eventos anteriores y agregar nuevo
    confirmarBtn.replaceWith(confirmarBtn.cloneNode(true));
    const nuevoConfirmarBtn = document.getElementById('confirmar-accion');
    nuevoConfirmarBtn.addEventListener('click', function() {
        callback();
        modal.hide();
    });
    
    modal.show();
}

// Aprobar usuario individual
function aprobarUsuario(id) {
    mostrarModalConfirmacion(
        'aprobar',
        '¿Está seguro de aprobar este usuario?',
        'El usuario podrá acceder al sistema inmediatamente.',
        () => {
            // Aquí iría la llamada AJAX para aprobar
            console.log('Aprobando usuario:', id);
            // Simular éxito y recargar
            setTimeout(() => {
                location.reload();
            }, 500);
        }
    );
}

// Rechazar usuario individual
function rechazarUsuario(id) {
    mostrarModalConfirmacion(
        'rechazar',
        '¿Está seguro de rechazar este usuario?',
        'El usuario será eliminado del sistema permanentemente.',
        () => {
            // Aquí iría la llamada AJAX para rechazar
            console.log('Rechazando usuario:', id);
            // Simular éxito y recargar
            setTimeout(() => {
                location.reload();
            }, 500);
        }
    );
}

// Aprobar múltiples usuarios
document.getElementById('aprobar-seleccionados').addEventListener('click', function() {
    const checkboxes = document.querySelectorAll('.user-checkbox:checked');
    const cantidad = checkboxes.length;
    
    if (cantidad === 0) return;
    
    mostrarModalConfirmacion(
        'aprobar',
        `¿Está seguro de aprobar ${cantidad} usuario(s)?`,
        'Todos los usuarios seleccionados podrán acceder al sistema.',
        () => {
            // Aquí iría la llamada AJAX para aprobar múltiples
            const ids = Array.from(checkboxes).map(cb => cb.value);
            console.log('Aprobando usuarios:', ids);
            // Simular éxito y recargar
            setTimeout(() => {
                location.reload();
            }, 500);
        }
    );
});

// Rechazar múltiples usuarios
document.getElementById('rechazar-seleccionados').addEventListener('click', function() {
    const checkboxes = document.querySelectorAll('.user-checkbox:checked');
    const cantidad = checkboxes.length;
    
    if (cantidad === 0) return;
    
    mostrarModalConfirmacion(
        'rechazar',
        `¿Está seguro de rechazar ${cantidad} usuario(s)?`,
        'Todos los usuarios seleccionados serán eliminados permanentemente.',
        () => {
            // Aquí iría la llamada AJAX para rechazar múltiples
            const ids = Array.from(checkboxes).map(cb => cb.value);
            console.log('Rechazando usuarios:', ids);
            // Simular éxito y recargar
            setTimeout(() => {
                location.reload();
            }, 500);
        }
    );
});

// Agregar event listeners a los checkboxes
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.user-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', toggleBotonesAccion);
    });
});

function verDetalles(id) {
    // Aquí iría la lógica para mostrar detalles del usuario
    console.log('Ver detalles del usuario:', id);
}
</script>

<style>
.table th {
    border-top: none;
    font-weight: 600;
}

.badge {
    font-size: 0.75em;
}

.btn-success:hover {
    background-color: #157347;
    border-color: #146c43;
}

.btn-danger:hover {
    background-color: #bb2d3b;
    border-color: #b02a37;
}

.btn-info:hover {
    background-color: #31708f;
    border-color: #2e6da4;
}

.form-check-input:checked {
    background-color: #ff8c00;
    border-color: #ff8c00;
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Login-app\resources\views/administrador/aprobar-usuarios.blade.php ENDPATH**/ ?>