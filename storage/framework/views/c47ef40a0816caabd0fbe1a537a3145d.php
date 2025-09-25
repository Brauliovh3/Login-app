<?php $__env->startSection('title', 'Acceso Denegado'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center">
                    <div class="error-page">
                        <h1 class="display-1 text-danger">403</h1>
                        <h2 class="text-danger">Acceso Denegado</h2>
                        <p class="lead"><?php echo e($exception->getMessage() ?? 'No tienes permisos para acceder a esta sección.'); ?></p>
                        
                        <div class="mt-4">
                            <i class="fas fa-exclamation-triangle fa-5x text-warning mb-4"></i>
                        </div>
                        
                        <p class="text-muted">
                            Has intentado acceder a una sección que requiere permisos específicos. 
                            Tu rol actual no te permite ver esta página.
                        </p>

                        <div class="mt-4">
                            <?php if(auth()->guard()->check()): ?>
                                <?php if(auth()->user()->role == 'administrador'): ?>
                                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-primary">
                                        <i class="fas fa-home me-2"></i>Volver al Dashboard
                                    </a>
                                <?php elseif(auth()->user()->role == 'fiscalizador'): ?>
                                    <a href="<?php echo e(route('fiscalizador.dashboard')); ?>" class="btn btn-info">
                                        <i class="fas fa-home me-2"></i>Volver al Dashboard
                                    </a>
                                <?php elseif(auth()->user()->role == 'ventanilla'): ?>
                                    <a href="<?php echo e(route('ventanilla.dashboard')); ?>" class="btn btn-warning">
                                        <i class="fas fa-home me-2"></i>Volver al Dashboard
                                    </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="<?php echo e(route('login')); ?>" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .error-page {
        padding: 3rem 0;
    }
    
    .display-1 {
        font-size: 8rem;
        font-weight: 700;
    }
    
    @media (max-width: 768px) {
        .display-1 {
            font-size: 5rem;
        }
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Login-app\resources\views\errors\403.blade.php ENDPATH**/ ?>