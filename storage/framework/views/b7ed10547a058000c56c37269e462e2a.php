

<?php $__env->startSection('title', 'DRTC Apurímac - Sistema Unificado'); ?>

<?php $__env->startSection('content'); ?>
<style>
    :root {
        --drtc-orange: #ff8c00;
        --drtc-dark-orange: #e67c00;
        --drtc-light-orange: #ffb84d;
        --drtc-orange-bg: #fff4e6;
        --drtc-navy: #1e3a8a;
        --admin-primary: #dc3545;
        --fiscalizador-primary: #28a745;
        --ventanilla-primary: #ffc107;
    }
    
    .bg-drtc-orange { background-color: var(--drtc-orange) !important; }
    .bg-drtc-dark { background-color: var(--drtc-dark-orange) !important; }
    .bg-drtc-light { background-color: var(--drtc-light-orange) !important; }
    .bg-drtc-soft { background-color: var(--drtc-orange-bg) !important; }
    .bg-drtc-navy { background-color: var(--drtc-navy) !important; }
    .text-drtc-orange { color: var(--drtc-orange) !important; }
    .text-drtc-navy { color: var(--drtc-navy) !important; }
    .border-drtc-orange { border-color: var(--drtc-orange) !important; }
    
    .admin-theme { --primary-color: var(--admin-primary); }
    .fiscalizador-theme { --primary-color: var(--fiscalizador-primary); }
    .ventanilla-theme { --primary-color: var(--ventanilla-primary); }
    
    .drtc-logo {
        background: linear-gradient(135deg, var(--drtc-orange), var(--drtc-dark-orange));
        border-radius: 50%;
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        font-weight: bold;
        box-shadow: 0 4px 15px rgba(255, 140, 0, 0.3);
    }
    
    .role-badge {
        font-size: 0.8rem;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
    }
    
    .admin-badge { background: var(--admin-primary); }
    .fiscalizador-badge { background: var(--fiscalizador-primary); }
    .ventanilla-badge { background: var(--ventanilla-primary); }
    .inspector-badge { background: #6c757d; }
    
    .card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
    }
    
    .module-content {
        display: none;
    }

    .btn-back {
        position: fixed;
        top: 80px;
        right: 20px;
        z-index: 1000;
        border-radius: 50px;
        padding: 10px 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
</style>

<div class="container-fluid">
    <?php 
        $user = auth()->user();
        $role = $user->role ?? 'fiscalizador';
    ?>

    <!-- Header con información del usuario -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-drtc-orange text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="drtc-logo">
                                DRTC
                            </div>
                        </div>
                        <div class="col">
                            <h2 class="mb-1">Bienvenido, <?php echo e($user->username); ?></h2>
                            <p class="mb-0">
                                <span class="role-badge <?php echo e($role); ?>-badge text-white">
                                    <?php echo e(ucfirst($role)); ?>

                                </span>
                                - Dirección Regional de Transportes y Comunicaciones - Apurímac
                            </p>
                        </div>
                        <div class="col-auto">
                            <div class="text-end">
                                <small><?php echo e(now()->format('d/m/Y H:i')); ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido específico por rol -->
    <?php if($role === 'administrador'): ?>
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-2x mb-2"></i>
                        <h3><?php echo e($stats['usuarios'] ?? 0); ?></h3>
                        <p>Usuarios Totales</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <i class="fas fa-file-alt fa-2x mb-2"></i>
                        <h3><?php echo e($stats['actas'] ?? 0); ?></h3>
                        <p>Actas Registradas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <h3><?php echo e($stats['procesadas'] ?? 0); ?></h3>
                        <p>Actas Procesadas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-2x mb-2"></i>
                        <h3><?php echo e($stats['pendientes'] ?? 0); ?></h3>
                        <p>Actas Pendientes</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Módulos de Administrador -->
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card h-100 border-primary">
                    <div class="card-body text-center">
                        <i class="fas fa-users-cog fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">Gestionar Usuarios</h5>
                        <p class="card-text">Administrar usuarios del sistema</p>
                        <button onclick="showModule('gestionar-usuarios')" class="btn btn-primary">Gestionar</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card h-100 border-warning">
                    <div class="card-body text-center">
                        <i class="fas fa-user-check fa-3x text-warning mb-3"></i>
                        <h5 class="card-title">Aprobar Usuarios</h5>
                        <p class="card-text">Aprobar solicitudes de registro</p>
                        <button onclick="showModule('aprobar-usuarios')" class="btn btn-warning">Aprobar</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card h-100 border-success">
                    <div class="card-body text-center">
                        <i class="fas fa-exclamation-triangle fa-3x text-success mb-3"></i>
                        <h5 class="card-title">Infracciones</h5>
                        <p class="card-text">Gestionar tipos de infracciones</p>
                        <button onclick="showModule('infracciones')" class="btn btn-success">Ver Infracciones</button>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card h-100 border-info">
                    <div class="card-body text-center">
                        <i class="fas fa-users-cog fa-3x text-info mb-3"></i>
                        <h5 class="card-title">Mantenimientos</h5>
                        <p class="card-text">Gestionar conductores e inspectores</p>
                        <div class="btn-group w-100" role="group">
                            <button onclick="showModule('mantenimiento-conductores')" class="btn btn-outline-info">Conductores</button>
                            <button onclick="showModule('mantenimiento-inspectores')" class="btn btn-outline-info">Inspectores</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Botón de Super Admin eliminado por seguridad -->
        </div>

    <?php elseif($role === 'fiscalizador'): ?>
        <?php echo $__env->make('fiscalizador.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <?php elseif($role === 'ventanilla'): ?>
        <?php echo $__env->make('ventanilla.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <?php elseif($role === 'inspector'): ?>
        <?php echo $__env->make('inspector.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <?php else: ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center">
                        <h5>Rol no reconocido</h5>
                        <p>Contacte al administrador del sistema.</p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Contenedor de módulos -->
<div id="modules-container" style="display: none;">
    <button class="btn btn-secondary btn-back" onclick="hideModules()">
        <i class="fas fa-arrow-left"></i> Volver al Dashboard
    </button>

    <!-- Módulo Gestionar Usuarios -->
    <div id="module-gestionar-usuarios" class="module-content">
        <div class="container-fluid">
            <h4><i class="fas fa-users-cog"></i> Gestionar Usuarios</h4>
            <hr>
            <?php echo $__env->make('partials.modulos.gestionar-usuarios', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>

    <!-- Módulo Aprobar Usuarios -->
    <div id="module-aprobar-usuarios" class="module-content">
        <div class="container-fluid">
            <h4><i class="fas fa-user-check"></i> Aprobar Usuarios</h4>
            <hr>
            <?php echo $__env->make('partials.modulos.aprobar-usuarios', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>

    <!-- Módulo Infracciones -->
    <div id="module-infracciones" class="module-content">
        <div class="container-fluid">
            <h4><i class="fas fa-exclamation-triangle"></i> Gestión de Infracciones</h4>
            <hr>
            <?php echo $__env->make('partials.modulos.infracciones', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>

    <!-- Módulo Mantenimiento Conductores -->
    <div id="module-mantenimiento-conductores" class="module-content">
        <div class="container-fluid">
            <h4><i class="fas fa-user-tie"></i> Mantenimiento de Conductores</h4>
            <hr>
            <?php echo $__env->make('partials.modulos.mantenimiento-conductores', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>

    <!-- Módulo Mantenimiento Inspectores -->
    <div id="module-mantenimiento-inspectores" class="module-content">
        <div class="container-fluid">
            <h4><i class="fas fa-user-shield"></i> Mantenimiento de Inspectores</h4>
            <hr>
            <?php echo $__env->make('partials.modulos.mantenimiento-inspectores', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>
</div>

<script>
function showModule(moduleId) {
    // Ocultar el dashboard principal
    document.querySelector('.container-fluid > .row').style.display = 'none';
    document.querySelectorAll('.container-fluid > .row').forEach(row => {
        row.style.display = 'none';
    });
    
    // Mostrar el contenedor de módulos
    const modulesContainer = document.getElementById('modules-container');
    modulesContainer.style.display = 'block';
    
    // Ocultar todos los módulos
    document.querySelectorAll('.module-content').forEach(module => {
        module.style.display = 'none';
    });
    
    // Mostrar el módulo específico
    const targetModule = document.getElementById('module-' + moduleId);
    if (targetModule) {
        targetModule.style.display = 'block';
        
        // Scroll to top
        window.scrollTo(0, 0);
        
        // Update browser history
        history.pushState({module: moduleId}, '', '/dashboard?module=' + moduleId);
    }
}

function hideModules() {
    // Ocultar el contenedor de módulos
    document.getElementById('modules-container').style.display = 'none';
    
    // Mostrar el dashboard principal
    document.querySelectorAll('.container-fluid > .row').forEach(row => {
        row.style.display = '';
    });
    
    // Update browser history
    history.pushState({}, '', '/dashboard');
    
    // Scroll to top
    window.scrollTo(0, 0);
}

// Handle browser back button
window.addEventListener('popstate', function(event) {
    if (event.state && event.state.module) {
        showModule(event.state.module);
    } else {
        hideModules();
    }
});

// Handle direct module access via URL
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const module = urlParams.get('module');
    if (module) {
        showModule(module);
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Login-app\resources\views/dashboard.blade.php ENDPATH**/ ?>