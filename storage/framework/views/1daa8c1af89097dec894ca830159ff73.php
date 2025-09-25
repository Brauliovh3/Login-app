<?php $__env->startSection('content'); ?>
<div class="container">
    <h1>Infracciones</h1>

    <?php if($infracciones->isEmpty()): ?>
        <p>No hay infracciones registradas.</p>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Multa (S/)</th>
                    <th>Tipo</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $infracciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inf): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($inf->id); ?></td>
                    <td><?php echo e($inf->codigo); ?></td>
                    <td><?php echo e($inf->descripcion); ?></td>
                    <td><?php echo e($inf->multa_soles); ?></td>
                    <td><?php echo e($inf->tipo_infraccion); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Login-app\resources\views\infracciones\index.blade.php ENDPATH**/ ?>