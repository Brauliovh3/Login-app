<?php $__env->startSection('title', 'Registro'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-4">
            <div class="auth-container">
                <div class="auth-header">
                    <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
                    <h2>Crear Cuenta</h2>
                    <p class="text-muted">Completa los datos para registrarte</p>
                </div>

                <?php if($errors->any()): ?>
                    <div class="alert alert-danger">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div><?php echo e($error); ?></div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('register')); ?>">
                    <?php echo csrf_field(); ?>
                    
                    <div class="form-group">
                        <label for="username" class="form-label">Nombre de Usuario</label>
                        <input id="username" 
                               type="text" 
                               class="form-control <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               name="username" 
                               value="<?php echo e(old('username')); ?>" 
                               required 
                               autocomplete="username" 
                               placeholder="Elige un nombre de usuario único"
                               autofocus>
                        <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback">
                                <strong><?php echo e($message); ?></strong>
                            </div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input id="email" 
                               type="email" 
                               class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               name="email" 
                               value="<?php echo e(old('email')); ?>" 
                               required 
                               autocomplete="email"
                               placeholder="tu@ejemplo.com">
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback">
                                <strong><?php echo e($message); ?></strong>
                            </div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="position-relative">
                            <input id="password" 
                                   type="password" 
                                   class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   name="password" 
                                   required 
                                   autocomplete="new-password"
                                   placeholder="Mínimo 8 caracteres">
                            <button type="button" 
                                    class="password-toggle" 
                                    onclick="togglePassword('password', 'password-icon')">
                                <i id="password-icon" class="fas fa-eye"></i>
                            </button>
                        </div>
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback">
                                <strong><?php echo e($message); ?></strong>
                            </div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="form-group">
                        <label for="password-confirm" class="form-label">Confirmar Contraseña</label>
                        <div class="position-relative">
                            <input id="password-confirm" 
                                   type="password" 
                                   class="form-control" 
                                   name="password_confirmation" 
                                   required 
                                   autocomplete="new-password"
                                   placeholder="Repite la contraseña">
                            <button type="button" 
                                    class="password-toggle" 
                                    onclick="togglePassword('password-confirm', 'password-confirm-icon')">
                                <i id="password-confirm-icon" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="role" class="form-label">Rol</label>
                        <select id="role" 
                                class="form-control <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                name="role" 
                                required>
                            <option value="">Selecciona tu rol</option>
                            <option value="administrador" <?php echo e(old('role') == 'administrador' ? 'selected' : ''); ?>>
                                👑 Administrador
                            </option>
                            <option value="fiscalizador" <?php echo e(old('role') == 'fiscalizador' ? 'selected' : ''); ?>>
                                📋 Fiscalizador
                            </option>
                            <option value="ventanilla" <?php echo e(old('role') == 'ventanilla' ? 'selected' : ''); ?>>
                                🏪 Ventanilla
                            </option>
                            <option value="inspector" <?php echo e(old('role') == 'inspector' ? 'selected' : ''); ?>>
                                🔍 Inspector
                            </option>
                        </select>
                        <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback">
                                <strong><?php echo e($message); ?></strong>
                            </div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-user-plus me-2"></i>Crear Cuenta
                        </button>
                    </div>
                </form>

                <div class="auth-links">
                    <p class="mb-2">¿Ya tienes cuenta?</p>
                    <a href="<?php echo e(route('login')); ?>" class="fw-bold">
                        <i class="fas fa-sign-in-alt me-1"></i>Iniciar sesión
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Login-app\resources\views/auth/register.blade.php ENDPATH**/ ?>