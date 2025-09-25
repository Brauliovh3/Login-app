<?php $__env->startSection('title', 'Registro Exitoso'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6">
            <div class="text-center">
                <div class="success-animation mb-4">
                    <i class="fas fa-check-circle fa-5x text-success"></i>
                </div>
                
                <h2 class="mb-3">¡Registro Exitoso!</h2>
                <p class="lead text-muted mb-4">
                    Tu solicitud de registro ha sido enviada correctamente.
                </p>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>¿Qué sigue?</strong><br>
                    Un administrador revisará tu solicitud y te notificará cuando sea aprobada.
                    Recibirás una notificación en tu email cuando puedas acceder al sistema.
                </div>
                
                <div class="d-flex gap-3 justify-content-center">
                    <a href="<?php echo e(route('login')); ?>" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Ir al Login
                    </a>
                    <a href="<?php echo e(route('register')); ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-plus me-2"></i>
                        Registrar Otro Usuario
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notifications Component -->
<?php echo $__env->make('components.toast-notifications', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<style>
.success-animation {
    animation: bounceIn 1s ease-out;
}

@keyframes bounceIn {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.1);
        opacity: 1;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.auth-container {
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    padding: 2rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mostrar notificación de éxito
    if (window.toastNotification) {
        window.toastNotification.success(
            '¡Registro Completado!', 
            'Tu solicitud está siendo revisada por un administrador.',
            6000
        );
        
        // Mostrar notificación informativa después de 3 segundos
        setTimeout(() => {
            window.toastNotification.info(
                'Próximos Pasos',
                'Recibirás un email cuando tu cuenta sea aprobada.',
                5000
            );
        }, 3000);
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Login-app\resources\views\auth\register-success.blade.php ENDPATH**/ ?>