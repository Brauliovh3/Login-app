<?php $__env->startSection('title', 'Dashboard Unificado'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <h2 class="mb-3">Dashboard Unificado</h2>

    <?php $role = auth()->user()->role ?? 'fiscalizador'; ?>

    <?php if($role === 'administrador'): ?>
        <?php if(view()->exists('admin.dashboard')): ?>
            <?php echo $__env->make('admin.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php else: ?>
            <p>Panel de administrador no encontrado. <a href="<?php echo e(route('admin.dashboard')); ?>">Ir al panel admin</a></p>
        <?php endif; ?>

    <?php elseif($role === 'fiscalizador'): ?>
        <?php if(view()->exists('fiscalizador.dashboard')): ?>
            <?php echo $__env->make('fiscalizador.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php else: ?>
            <p>Panel de fiscalizador no encontrado. <a href="<?php echo e(route('fiscalizador.dashboard')); ?>">Ir al panel fiscalizador</a></p>
        <?php endif; ?>

    <?php elseif($role === 'ventanilla'): ?>
        <?php if(view()->exists('ventanilla.dashboard')): ?>
            <?php echo $__env->make('ventanilla.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php else: ?>
            <p>Panel de ventanilla no encontrado. <a href="<?php echo e(route('ventanilla.dashboard')); ?>">Ir al panel ventanilla</a></p>
        <?php endif; ?>

    <?php else: ?>
        <p>Rol no reconocido. Contacte al administrador.</p>
    <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Login-app\resources\views\dashboard-original-backup.blade.php ENDPATH**/ ?>