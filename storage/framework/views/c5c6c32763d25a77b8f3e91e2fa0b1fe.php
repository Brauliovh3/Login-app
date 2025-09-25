

<?php $__env->startSection('title', 'Dashboard Unificado - DRTC'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 20px;
        color: white;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
    }
    
    .stats-number {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 5px;
    }
    
    .stats-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    
    .content-section {
        min-height: 500px;
        padding: 20px;
        animation: fadeIn 0.3s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .analysis-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border-left: 4px solid #ff6b35;
    }
    
    .progress-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    
    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #ff8c00 0%, #e67e22 100%);
        border: none;
    }
    
    .nav-pills .nav-link {
        color: #666;
        border-radius: 8px;
        margin-right: 5px;
        transition: all 0.3s ease;
    }
    
    .nav-pills .nav-link:hover {
        background: linear-gradient(135deg, rgba(255,140,0,0.1) 0%, rgba(230,126,34,0.1) 100%);
        color: #ff8c00;
    }
    
    .section-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: none;
    }
</style>

<div class="container-fluid">
    <!-- Loader Global para transiciones -->
    <div id="globalLoader" class="d-none">
        <div class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(255,255,255,0.9); z-index: 9999;">
            <div class="text-center">
                <div class="spinner-border text-warning" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <div class="mt-2">Cargando contenido...</div>
            </div>
        </div>
    </div>

    <!-- Header con información del usuario -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card" style="background: linear-gradient(135deg, #ff8c00, #e67e22); color: white;">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="mb-1">👋 Bienvenido, <?php echo e(auth()->user()->name); ?></h3>
                            <p class="mb-0">
                                <i class="fas fa-user-shield"></i> 
                                <?php echo e(ucfirst(auth()->user()->role)); ?> | 
                                <i class="fas fa-calendar"></i> 
                                <?php echo e(now()->format('d/m/Y')); ?>

                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <?php if(auth()->user()->isSuperAdmin()): ?>
                                    <a href="<?php echo e(route('admin.super.index')); ?>" class="btn btn-light btn-sm">
                                        <i class="fas fa-shield-alt"></i> Super Admin
                                    </a>
                                <?php endif; ?>
                                <button class="btn btn-light btn-sm" onclick="showSection('profile')">
                                    <i class="fas fa-user"></i> Mi Perfil
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menú de navegación horizontal -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-3">
                    <ul class="nav nav-pills nav-fill flex-wrap" id="mainNavigation">
                        <!-- Dashboard siempre visible -->
                        <li class="nav-item">
                            <button class="nav-link active" data-section="dashboard" onclick="showSection('dashboard')">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </button>
                        </li>

                        <!-- Super Admin -->
                        <?php if(auth()->user()->isSuperAdmin()): ?>
                        <li class="nav-item">
                            <button class="nav-link" data-section="superadmin" onclick="showSection('superadmin')">
                                <i class="fas fa-shield-alt text-danger"></i> Super Admin
                            </button>
                        </li>
                        <?php endif; ?>

                        <!-- Administrador -->
                        <?php if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()): ?>
                        <li class="nav-item dropdown">
                            <button class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-users-cog"></i> Administración
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="showSection('admin-usuarios')">👥 Gestionar Usuarios</a></li>
                                <li><a class="dropdown-item" href="#" onclick="showSection('admin-aprobar')">✅ Aprobar Usuarios</a></li>
                                <li><a class="dropdown-item" href="#" onclick="showSection('admin-conductores')">🚗 Conductores</a></li>
                                <li><a class="dropdown-item" href="#" onclick="showSection('admin-inspectores')">🔍 Inspectores</a></li>
                                <li><a class="dropdown-item" href="#" onclick="showSection('admin-infracciones')">⚖️ Infracciones</a></li>
                            </ul>
                        </li>
                        <?php endif; ?>

                        <!-- Fiscalizador -->
                        <?php if(auth()->user()->isFiscalizador() || auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()): ?>
                        <li class="nav-item dropdown">
                            <button class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-clipboard-list"></i> Fiscalización
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="showSection('fiscal-actas')">📋 Generar Acta</a></li>
                                <li><a class="dropdown-item" href="#" onclick="showSection('fiscal-consultar')">🔍 Consultar Actas</a></li>
                                <li><a class="dropdown-item" href="#" onclick="showSection('fiscal-carga')">🚛 Carga y Pasajeros</a></li>
                                <li><a class="dropdown-item" href="#" onclick="showSection('fiscal-empresas')">🏢 Empresas</a></li>
                                <li><a class="dropdown-item" href="#" onclick="showSection('fiscal-calendario')">📅 Calendario</a></li>
                                <li><a class="dropdown-item" href="#" onclick="showSection('fiscal-reportes')">📊 Reportes</a></li>
                            </ul>
                        </li>
                        <?php endif; ?>

                        <!-- Ventanilla -->
                        <?php if(auth()->user()->isVentanilla() || auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()): ?>
                        <li class="nav-item dropdown">
                            <button class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-desk"></i> Ventanilla
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="showSection('ventanilla-atencion')">🎫 Nueva Atención</a></li>
                                <li><a class="dropdown-item" href="#" onclick="showSection('ventanilla-tramites')">📄 Trámites</a></li>
                                <li><a class="dropdown-item" href="#" onclick="showSection('ventanilla-consultar')">🔍 Consultar</a></li>
                                <li><a class="dropdown-item" href="#" onclick="showSection('ventanilla-cola')">⏱️ Cola de Espera</a></li>
                                <li><a class="dropdown-item" href="#" onclick="showSection('ventanilla-inspecciones')">🔍 Inspecciones</a></li>
                            </ul>
                        </li>
                        <?php endif; ?>

                        <!-- Inspector -->
                        <?php if(auth()->user()->isInspector() || auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()): ?>
                        <li class="nav-item dropdown">
                            <button class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-search"></i> Inspector
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="showSection('inspector-acta')">📋 Generar Acta</a></li>
                                <li><a class="dropdown-item" href="#" onclick="showSection('inspector-inspecciones')">🔍 Mis Inspecciones</a></li>
                                <li><a class="dropdown-item" href="#" onclick="showSection('inspector-vehiculos')">🚗 Vehículos</a></li>
                                <li><a class="dropdown-item" href="#" onclick="showSection('inspector-reportes')">📊 Reportes</a></li>
                            </ul>
                        </li>
                        <?php endif; ?>

                        <!-- Perfil siempre visible -->
                        <li class="nav-item">
                            <button class="nav-link" data-section="profile" onclick="showSection('profile')">
                                <i class="fas fa-user"></i> Perfil
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido dinámico -->
    <div id="dynamicContent">
        <!-- Dashboard por defecto -->
        <div id="section-dashboard" class="content-section">
            <?php echo $__env->make('dashboard.sections.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>

        <!-- Super Admin -->
        <?php if(auth()->user()->isSuperAdmin()): ?>
        <div id="section-superadmin" class="content-section d-none">
            <?php echo $__env->make('dashboard.sections.superadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <?php endif; ?>

        <!-- Administrador -->
        <?php if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()): ?>
        <div id="section-admin-usuarios" class="content-section d-none">
            <?php echo $__env->make('dashboard.sections.admin.usuarios', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <div id="section-admin-aprobar" class="content-section d-none">
            <?php echo $__env->make('dashboard.sections.admin.aprobar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <div id="section-admin-conductores" class="content-section d-none">
            <?php echo $__env->make('dashboard.sections.admin.conductores', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <div id="section-admin-inspectores" class="content-section d-none">
            <div class="text-center">
                <h4><i class="fas fa-user-tie"></i> Gestionar Inspectores</h4>
                <p class="text-muted">Administrar inspectores del sistema</p>
                <p class="text-info">Funcionalidad disponible próximamente</p>
            </div>
        </div>
        <div id="section-admin-infracciones" class="content-section d-none">
            <?php echo $__env->make('dashboard.sections.admin.infracciones', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <?php endif; ?>

        <!-- Fiscalizador -->
        <?php if(auth()->user()->isFiscalizador() || auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()): ?>
        <div id="section-fiscal-actas" class="content-section d-none">
            <?php echo $__env->make('dashboard.sections.fiscalizador.actas', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <div id="section-fiscal-consultar" class="content-section d-none">
            <?php echo $__env->make('dashboard.sections.fiscalizador.consultar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <div id="section-fiscal-carga" class="content-section d-none">
            <div class="text-center">
                <h4><i class="fas fa-truck"></i> Carga y Pasajeros</h4>
                <p class="text-muted">Gestión de vehículos de carga y pasajeros</p>
                <p class="text-info">Funcionalidad disponible próximamente</p>
            </div>
        </div>
        <div id="section-fiscal-empresas" class="content-section d-none">
            <div class="text-center">
                <h4><i class="fas fa-building"></i> Empresas</h4>
                <p class="text-muted">Gestión de empresas de transporte</p>
                <p class="text-info">Funcionalidad disponible próximamente</p>
            </div>
        </div>
        <div id="section-fiscal-calendario" class="content-section d-none">
            <div class="text-center">
                <h4><i class="fas fa-calendar-alt"></i> Calendario</h4>
                <p class="text-muted">Programación de inspecciones</p>
                <p class="text-info">Funcionalidad disponible próximamente</p>
            </div>
        </div>
        <div id="section-fiscal-reportes" class="content-section d-none">
            <div class="text-center">
                <h4><i class="fas fa-chart-bar"></i> Reportes</h4>
                <p class="text-muted">Generar reportes de infracciones</p>
                <p class="text-info">Funcionalidad disponible próximamente</p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Ventanilla -->
        <?php if(auth()->user()->isVentanilla() || auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()): ?>
        <div id="section-ventanilla-atencion" class="content-section d-none">
            <?php echo $__env->make('dashboard.sections.ventanilla', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <div id="section-ventanilla-tramites" class="content-section d-none">
            <?php echo $__env->make('dashboard.sections.ventanilla', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <div id="section-ventanilla-consultar" class="content-section d-none">
            <?php echo $__env->make('dashboard.sections.ventanilla', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <div id="section-ventanilla-cola" class="content-section d-none">
            <?php echo $__env->make('dashboard.sections.ventanilla', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <div id="section-ventanilla-inspecciones" class="content-section d-none">
            <?php echo $__env->make('dashboard.sections.ventanilla', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <?php endif; ?>

        <!-- Inspector -->
        <?php if(auth()->user()->isInspector() || auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()): ?>
        <div id="section-inspector-acta" class="content-section d-none">
            <div class="text-center">
                <h4><i class="fas fa-file-alt"></i> Generar Acta</h4>
                <p class="text-muted">Crear acta de infracción</p>
                <p class="text-info">Funcionalidad disponible próximamente</p>
            </div>
        </div>
        <div id="section-inspector-inspecciones" class="content-section d-none">
            <?php echo $__env->make('dashboard.sections.inspector', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <div id="section-inspector-vehiculos" class="content-section d-none">
            <div class="text-center">
                <h4><i class="fas fa-car"></i> Vehículos</h4>
                <p class="text-muted">Gestión de vehículos</p>
                <p class="text-info">Funcionalidad disponible próximamente</p>
            </div>
        </div>
        <div id="section-inspector-reportes" class="content-section d-none">
            <div class="text-center">
                <h4><i class="fas fa-chart-line"></i> Reportes</h4>
                <p class="text-muted">Reportes de inspección</p>
                <p class="text-info">Funcionalidad disponible próximamente</p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Perfil -->
        <div id="section-profile" class="content-section d-none">
            <?php echo $__env->make('dashboard.sections.profile', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
// Sistema de navegación unificado
document.addEventListener('DOMContentLoaded', function() {
    let currentSection = 'dashboard';
    
    // Mostrar loader
    function showLoader() {
        document.getElementById('globalLoader').classList.remove('d-none');
    }
    
    // Ocultar loader
    function hideLoader() {
        document.getElementById('globalLoader').classList.add('d-none');
    }
    
    // Función global para cambiar secciones
    window.showSection = function(sectionName) {
        showLoader();
        
        // Remover clase activa de todos los botones
        document.querySelectorAll('#mainNavigation .nav-link').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Ocultar todas las secciones
        document.querySelectorAll('.content-section').forEach(section => {
            section.classList.add('d-none');
        });
        
        // Mostrar sección seleccionada
        const targetSection = document.getElementById('section-' + sectionName);
        if (targetSection) {
            targetSection.classList.remove('d-none');
            currentSection = sectionName;
            
            // Activar botón correspondiente
            const targetBtn = document.querySelector(`[data-section="${sectionName}"]`);
            if (targetBtn) {
                targetBtn.classList.add('active');
            }
            
            // Ejecutar inicialización específica de la sección si existe
            if (typeof window['init_' + sectionName.replace('-', '_')] === 'function') {
                setTimeout(() => {
                    window['init_' + sectionName.replace('-', '_')]();
                }, 100);
            }
        }
        
        // Ocultar loader después de una pequeña animación
        setTimeout(() => {
            hideLoader();
        }, 300);
    };
    
    // Función para mostrar notificaciones
    window.showNotification = function(message, type = 'info') {
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        };
        
        const alertHTML = `
            <div class="alert ${alertClass[type]} alert-dismissible fade show" role="alert">
                <strong>${type.charAt(0).toUpperCase() + type.slice(1)}:</strong> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // Crear contenedor de notificaciones si no existe
        let notificationContainer = document.getElementById('notification-container');
        if (!notificationContainer) {
            notificationContainer = document.createElement('div');
            notificationContainer.id = 'notification-container';
            notificationContainer.className = 'position-fixed top-0 end-0 p-3';
            notificationContainer.style.zIndex = '9999';
            document.body.appendChild(notificationContainer);
        }
        
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = alertHTML;
        notificationContainer.appendChild(tempDiv.firstElementChild);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            const alert = notificationContainer.querySelector('.alert');
            if (alert) {
                alert.classList.remove('show');
                setTimeout(() => alert.remove(), 150);
            }
        }, 5000);
    };
    
    // Token CSRF global
    window.csrfToken = '<?php echo e(csrf_token()); ?>';
    
    console.log('Dashboard unificado inicializado');
    console.log('Rol del usuario:', '<?php echo e(auth()->user()->role); ?>');
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Login-app\resources\views\dashboard\unified.blade.php ENDPATH**/ ?>