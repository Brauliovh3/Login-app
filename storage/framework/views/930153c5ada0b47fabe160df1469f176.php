<?php $__env->startSection('title', 'Configuración'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Configuración</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <!-- Configuración general -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cog me-2"></i>Configuración General
                    </h6>
                </div>
                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo e(route('user.configuracion.update')); ?>">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <!-- Notificaciones -->
                        <h6 class="font-weight-bold text-secondary mb-3">
                            <i class="fas fa-bell me-2"></i>Notificaciones
                        </h6>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="notifications_enabled" 
                                           name="notifications_enabled" value="1" 
                                           <?php echo e(session('user_config.notifications_enabled', true) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="notifications_enabled">
                                        Habilitar notificaciones del sistema
                                    </label>
                                </div>
                                <small class="text-muted">Recibe notificaciones de eventos importantes del sistema</small>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="email_notifications" 
                                           name="email_notifications" value="1" 
                                           <?php echo e(session('user_config.email_notifications', true) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="email_notifications">
                                        Notificaciones por correo electrónico
                                    </label>
                                </div>
                                <small class="text-muted">Recibe notificaciones importantes en tu correo</small>
                            </div>
                        </div>

                        <hr>

                        <!-- Apariencia -->
                        <h6 class="font-weight-bold text-secondary mb-3">
                            <i class="fas fa-palette me-2"></i>Apariencia
                        </h6>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="theme" class="form-label">Tema de la interfaz</label>
                                <select class="form-select <?php $__errorArgs = ['theme'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="theme" name="theme">
                                    <option value="light" <?php echo e(session('user_config.theme', 'light') == 'light' ? 'selected' : ''); ?>>
                                        <i class="fas fa-sun"></i> Claro
                                    </option>
                                    <option value="dark" <?php echo e(session('user_config.theme') == 'dark' ? 'selected' : ''); ?>>
                                        <i class="fas fa-moon"></i> Oscuro
                                    </option>
                                    <option value="auto" <?php echo e(session('user_config.theme') == 'auto' ? 'selected' : ''); ?>>
                                        <i class="fas fa-adjust"></i> Automático
                                    </option>
                                </select>
                                <?php $__errorArgs = ['theme'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="col-md-6">
                                <label for="language" class="form-label">Idioma</label>
                                <select class="form-select <?php $__errorArgs = ['language'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="language" name="language">
                                    <option value="es" <?php echo e(session('user_config.language', 'es') == 'es' ? 'selected' : ''); ?>>
                                        🇪🇸 Español
                                    </option>
                                    <option value="en" <?php echo e(session('user_config.language') == 'en' ? 'selected' : ''); ?>>
                                        🇺🇸 English
                                    </option>
                                </select>
                                <?php $__errorArgs = ['language'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <hr>

                        <!-- Zona horaria -->
                        <h6 class="font-weight-bold text-secondary mb-3">
                            <i class="fas fa-clock me-2"></i>Zona Horaria
                        </h6>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="timezone" class="form-label">Zona Horaria</label>
                                <select class="form-select <?php $__errorArgs = ['timezone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="timezone" name="timezone">
                                    <option value="America/Lima" <?php echo e(session('user_config.timezone', 'America/Lima') == 'America/Lima' ? 'selected' : ''); ?>>
                                        Lima, Perú (GMT-5)
                                    </option>
                                    <option value="America/Bogota" <?php echo e(session('user_config.timezone') == 'America/Bogota' ? 'selected' : ''); ?>>
                                        Bogotá, Colombia (GMT-5)
                                    </option>
                                    <option value="America/New_York" <?php echo e(session('user_config.timezone') == 'America/New_York' ? 'selected' : ''); ?>>
                                        Nueva York, EE.UU. (GMT-4)
                                    </option>
                                    <option value="Europe/Madrid" <?php echo e(session('user_config.timezone') == 'Europe/Madrid' ? 'selected' : ''); ?>>
                                        Madrid, España (GMT+2)
                                    </option>
                                </select>
                                <?php $__errorArgs = ['timezone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Configuración
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Información del sistema -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>Información del Sistema
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-5">
                            <p class="mb-0 small"><strong>Versión:</strong></p>
                        </div>
                        <div class="col-sm-7">
                            <p class="text-muted mb-1 small">DRTC v1.0</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-5">
                            <p class="mb-0 small"><strong>Laravel:</strong></p>
                        </div>
                        <div class="col-sm-7">
                            <p class="text-muted mb-1 small"><?php echo e(app()->version()); ?></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-5">
                            <p class="mb-0 small"><strong>PHP:</strong></p>
                        </div>
                        <div class="col-sm-7">
                            <p class="text-muted mb-1 small"><?php echo e(PHP_VERSION); ?></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-5">
                            <p class="mb-0 small"><strong>Hora actual:</strong></p>
                        </div>
                        <div class="col-sm-7">
                            <p class="text-muted mb-1 small" id="current-time"><?php echo e(now()->format('H:i:s')); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuración avanzada -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-tools me-2"></i>Configuración Avanzada
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Opciones avanzadas del sistema</p>
                    
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-secondary btn-sm" onclick="clearCache()">
                            <i class="fas fa-trash-alt me-2"></i>Limpiar Caché
                        </button>
                        
                        <button class="btn btn-outline-info btn-sm" onclick="exportData()">
                            <i class="fas fa-download me-2"></i>Exportar Datos
                        </button>
                        
                        <button class="btn btn-outline-warning btn-sm" onclick="resetPreferences()">
                            <i class="fas fa-undo me-2"></i>Restaurar Preferencias
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-left: 0.25rem solid #4e73df;
}

.form-switch .form-check-input {
    width: 2em;
    margin-left: -2.5em;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba%280,0,0,.25%29'/%3e%3c/svg%3e");
    background-position: left center;
    background-repeat: no-repeat;
    background-size: contain;
    border-radius: 2em;
}

.form-switch .form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='white'/%3e%3c/svg%3e");
    background-position: right center;
}
</style>

<script>
// Actualizar la hora cada segundo
function updateTime() {
    const now = new Date();
    document.getElementById('current-time').textContent = now.toLocaleTimeString('es-ES');
}

setInterval(updateTime, 1000);

// Funciones para configuración avanzada
function clearCache() {
    if (confirm('¿Estás seguro de que deseas limpiar el caché?')) {
        // Aquí podrías hacer una llamada AJAX para limpiar el caché
        alert('Caché limpiado exitosamente');
    }
}

function exportData() {
    alert('Función de exportación próximamente disponible');
}

function resetPreferences() {
    if (confirm('¿Estás seguro de que deseas restaurar todas las preferencias por defecto?')) {
        // Resetear el formulario a valores por defecto
        document.getElementById('notifications_enabled').checked = true;
        document.getElementById('email_notifications').checked = true;
        document.getElementById('theme').value = 'light';
        document.getElementById('language').value = 'es';
        document.getElementById('timezone').value = 'America/Lima';
        alert('Preferencias restauradas. No olvides guardar los cambios.');
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Login-app\resources\views\user\configuracion.blade.php ENDPATH**/ ?>