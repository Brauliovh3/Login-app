<?php $__env->startSection('content'); ?>
<div class="container">
    <h2>Gestión de Carga y Pasajero</h2>
    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalCrear">Nuevo Control</button>

    <table class="table table-striped table-hover mt-3">
        <thead class="table-dark">
            <tr>
                <th>Informe</th>
                <th>Resolución</th>
                <th>Conductor</th>
                <th>Licencia</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $registros; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $registro): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($registro->informe); ?></td>
                <td><?php echo e($registro->resolucion); ?></td>
                <td><?php echo e($registro->conductor); ?></td>
                <td><?php echo e($registro->licencia_conductor); ?></td>
                <td><?php echo e($registro->estado); ?></td>
                <td>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditar<?php echo e($registro->id); ?>">Editar</button>
                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalEliminar<?php echo e($registro->id); ?>">Eliminar</button>
                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalVer<?php echo e($registro->id); ?>">Ver</button>
                </td>
            </tr>

            
            <div class="modal fade" id="modalVer<?php echo e($registro->id); ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header"><h5>Detalle</h5></div>
                        <div class="modal-body">
                            <p><strong>Informe:</strong> <?php echo e($registro->informe); ?></p>
                            <p><strong>Resolución:</strong> <?php echo e($registro->resolucion); ?></p>
                            <p><strong>Conductor:</strong> <?php echo e($registro->conductor); ?></p>
                            <p><strong>Licencia:</strong> <?php echo e($registro->licencia_conductor); ?></p>
                            <p><strong>Estado:</strong> <?php echo e($registro->estado); ?></p>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="modal fade" id="modalEditar<?php echo e($registro->id); ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form action="<?php echo e(route('carga-pasajero.update', $registro->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                            <div class="modal-header bg-primary text-white"><h5>Editar Registro</h5></div>
                            <div class="modal-body">
                                <input type="text" name="informe" value="<?php echo e($registro->informe); ?>" class="form-control mb-2" required>
                                <input type="text" name="resolucion" value="<?php echo e($registro->resolucion); ?>" class="form-control mb-2" required>
                                <input type="text" name="conductor" value="<?php echo e($registro->conductor); ?>" class="form-control mb-2" required>
                                <input type="text" name="licencia_conductor" value="<?php echo e($registro->licencia_conductor); ?>" class="form-control mb-2" required>
                                <select name="estado" class="form-control mb-2" required>
                                    <option value="pendiente" <?php if($registro->estado === 'pendiente'): ?> selected <?php endif; ?>>Pendiente</option>
                                    <option value="aprobado" <?php if($registro->estado === 'aprobado'): ?> selected <?php endif; ?>>Aprobado</option>
                                    <option value="procesado" <?php if($registro->estado === 'procesado'): ?> selected <?php endif; ?>>Procesado</option>
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Guardar</button>
                                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            
            <div class="modal fade" id="modalEliminar<?php echo e($registro->id); ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="<?php echo e(route('carga-pasajero.destroy', $registro->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">Confirmar Eliminación</h5>
                            </div>
                            <div class="modal-body">
                                <p>¿Estás seguro de que deseas eliminar este registro?</p>
                                <ul>
                                    <li><strong>Informe:</strong> <?php echo e($registro->informe); ?></li>
                                    <li><strong>Conductor:</strong> <?php echo e($registro->conductor); ?></li>
                                    <li><strong>Licencia:</strong> <?php echo e($registro->licencia_conductor); ?></li>
                                </ul>
                                <div class="alert alert-warning">
                                    <strong>Advertencia:</strong> Esta acción no se puede deshacer.
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-danger">Eliminar</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    
    <div class="modal fade" id="modalCrear" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="<?php echo e(route('carga-pasajero.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header"><h5>Nuevo Control</h5></div>
                    <div class="modal-body">
                        <input type="text" name="informe" class="form-control mb-2" placeholder="Informe" required>
                        <input type="text" name="resolucion" class="form-control mb-2" placeholder="Resolución" required>
                        <input type="text" name="conductor" class="form-control mb-2" placeholder="Conductor" required>
                        <input type="text" name="licencia_conductor" class="form-control mb-2" placeholder="Licencia" required>
                        <select name="estado" class="form-control mb-2" required>
                            <option value="pendiente">Pendiente</option>
                            <option value="aprobado">Aprobado</option>
                            <option value="procesado">Procesado</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Crear</button>
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Login-app\resources\views/carga_pasajero/index.blade.php ENDPATH**/ ?>