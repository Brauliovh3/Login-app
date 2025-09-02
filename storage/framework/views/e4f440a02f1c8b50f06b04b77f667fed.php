<?php $__env->startSection('title', 'Iniciar Sesión'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-12 col-sm-10 col-md-6 col-lg-5 col-xl-4">
            <div class="auth-container">
                <div class="auth-header">
                    <div class="logo-container">
                        <div class="logo-circle">
                            <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Logo DRTC Apurímac" class="logo-oficial">
                        </div>
                    </div>
                    <h2>SISTEMA DRTC APURÍMAC</h2>
                    <p class="text-muted">Dirección Regional de Transportes y Comunicaciones</p>
                    <h5 class="text-muted">Iniciar Sesión</h5>
                </div>

                <?php if(session('status')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-left: 4px solid #28a745; background: linear-gradient(135deg, #d4edda, #c3e6cb); border-radius: 8px;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle me-2" style="color: #28a745; font-size: 18px;"></i>
                        <div>
                            <strong>¡Registro Exitoso!</strong><br>
                            <small><?php echo e(session('status')); ?></small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <?php if($errors->any()): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div><i class="fas fa-exclamation-circle me-2"></i><?php echo e($error); ?></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('login')); ?>">
                    <?php echo csrf_field(); ?>

                    <div class="form-group">
                        <label for="login" class="form-label">Usuario o Email</label>
                        <input id="login"
                            type="text"
                            class="form-control <?php $__errorArgs = ['login'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            name="login"
                            value="<?php echo e(old('login')); ?>"
                            required
                            autocomplete="login"
                            placeholder="Ingresa tu usuario o email"
                            autofocus>
                        <?php $__errorArgs = ['login'];
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
                                autocomplete="current-password"
                                placeholder="Ingresa tu contraseña">
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
                        <div class="form-check d-flex align-items-center">
                            <input class="form-check-input me-2"
                                type="checkbox"
                                name="remember"
                                id="remember"
                                <?php echo e(old('remember') ? 'checked' : ''); ?>>
                            <label class="form-check-label text-muted" for="remember">
                                <i class="fas fa-clock me-1"></i>Recordarme en este dispositivo
                            </label>
                        </div>
                        <small class="text-muted">
                            Si no marcas esta opción, tu sesión expirará al cerrar el navegador.
                        </small>
                    </div>

                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                        </button>
                    </div>
                </form>

                <div class="auth-links">
                    <p class="mb-2">¿No tienes cuenta?</p>
                    <a href="<?php echo e(route('register')); ?>" class="fw-bold">
                        <i class="fas fa-user-plus me-1"></i>Crear cuenta nueva
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Login-app\resources\views/auth/login.blade.php ENDPATH**/ ?>