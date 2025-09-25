

<?php $__env->startSection('title', 'Panel de Control - DRTC Apurímac'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- El contenido se carga dinámicamente aquí -->
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<style>
    .cursor-pointer {
        cursor: pointer;
    }
    
    .btn-circle {
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .btn-circle.btn-lg {
        width: 3rem;
        height: 3rem;
    }
    
    .blinking-cursor {
        animation: blink 1s infinite;
    }
    
    @keyframes blink {
        0%, 50% { opacity: 1; }
        51%, 100% { opacity: 0; }
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Login-app\resources\views/dashboard/unified-dynamic.blade.php ENDPATH**/ ?>