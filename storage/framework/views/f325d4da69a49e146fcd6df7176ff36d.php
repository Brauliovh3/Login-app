<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead style="background: linear-gradient(135deg, #ff8c00, #e67e22); color: white;">
            <tr>
                <th class="text-center" style="width: 60px; border: none;">
                    <i class="fas fa-hashtag"></i>
                </th>
                <th style="width: 200px; border: none;">
                    <i class="fas fa-user me-1"></i>Usuario
                </th>
                <th style="width: 150px; border: none;">
                    <i class="fas fa-at me-1"></i>Usuario/Login
                </th>
                <th style="border: none;">
                    <i class="fas fa-envelope me-1"></i>Email
                </th>
                <th class="text-center" style="width: 130px; border: none;">
                    <i class="fas fa-user-tag me-1"></i>Rol
                </th>
                <th class="text-center" style="width: 120px; border: none;">
                    <i class="fas fa-calendar me-1"></i>Registro
                </th>
                <th class="text-center" style="width: 120px; border: none;">
                    <i class="fas fa-cogs me-1"></i>Acciones
                </th>
            </tr>
        </thead>
        <tbody style="background: linear-gradient(135deg, #fff4e6, #ffe4cc);"
            <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="border-bottom">
                <td class="text-center">
                    <!--  Cambie esta linea por esto -->
                    <!-- <span class="badge bg-light text-dark">#<?php echo e($user->id); ?></span> -->

                    <!--  POR ESTA: ASi el IDE no esta expuesto numeracion correlativa en ves de ID -->
                    <span class="badge bg-light text-dark">#<?php echo e(($users->currentPage() - 1) * $users->perPage() + $loop->iteration); ?></span>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-md rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="background: linear-gradient(135deg, #ff8c00, #e67e22); width: 45px; height: 45px;">
                            <span class="text-white fw-bold fs-5"><?php echo e(strtoupper(substr($user->name, 0, 1))); ?></span>
                        </div>
                        <div>
                            <div class="fw-bold text-dark"><?php echo e($user->name); ?></div>
                            <!--  CAMBIAR ESTA LÍNEA -->
                            <!-- <small class="text-muted">ID: <?php echo e($user->id); ?></small> -->
                            
                            <!-- ✅ POR ESTA: -->
                            <small class="text-muted">Reg: <?php echo e($user->created_at->format('d/m/Y')); ?></small>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge bg-secondary bg-opacity-10 text-dark border px-3 py-2">
                        <i class="fas fa-user me-1"></i><?php echo e($user->username); ?>

                    </span>
                </td>
                <td>
                    <div class="text-dark"><?php echo e($user->email); ?></div>
                    <small class="text-muted">
                        <i class="fas fa-envelope me-1"></i>Correo principal
                    </small>
                </td>
                <td class="text-center">
                    <?php switch($user->role):
                        case ('administrador'): ?>
                            <span class="badge bg-danger bg-opacity-90 px-3 py-2">
                                <i class="fas fa-crown me-1"></i>Administrador
                            </span>
                            <?php break; ?>
                        <?php case ('fiscalizador'): ?>
                            <span class="badge bg-warning text-dark px-3 py-2">
                                <i class="fas fa-clipboard-check me-1"></i>Fiscalizador
                            </span>
                            <?php break; ?>
                        <?php case ('ventanilla'): ?>
                            <span class="badge bg-success px-3 py-2">
                                <i class="fas fa-window-maximize me-1"></i>Ventanilla
                            </span>
                            <?php break; ?>
                        <?php case ('inspector'): ?>
                            <span class="badge bg-info px-3 py-2">
                                <i class="fas fa-search me-1"></i>Inspector
                            </span>
                            <?php break; ?>
                        <?php default: ?>
                            <span class="badge bg-secondary px-3 py-2"><?php echo e(ucfirst($user->role)); ?></span>
                    <?php endswitch; ?>
                </td>
                <td class="text-center">
                    <div class="text-dark fw-semibold"><?php echo e($user->created_at->format('d/m/Y')); ?></div>
                    <small class="text-muted"><?php echo e($user->created_at->format('H:i')); ?></small>
                </td>
                <td class="text-center">
                    <?php if(auth()->user()->role === 'administrador'): ?>
                    <div class="btn-group shadow-sm" role="group" aria-label="Acciones de usuario">
                        <!-- Botón Ver Detalles -->
                        <button type="button" 
                                class="btn btn-sm view-user" 
                                style="border-color: #17a2b8; color: #17a2b8; background: transparent;"
                                data-id="<?php echo e($user->id); ?>"
                                data-bs-toggle="tooltip"
                                title="Ver detalles del usuario">
                            <i class="fas fa-eye"></i>
                        </button>
                        
                        <!-- Botón Editar -->
                        <button type="button" 
                                class="btn btn-sm edit-user" 
                                style="border-color: #ff8c00; color: #ff8c00; background: transparent;"
                                data-id="<?php echo e($user->id); ?>"
                                data-bs-toggle="tooltip"
                                title="Editar usuario">
                            <i class="fas fa-edit"></i>
                        </button>
                        
                        <!-- Botón Cambiar Contraseña -->
                        <button type="button" 
                                class="btn btn-sm change-password" 
                                style="border-color: #ffc107; color: #ffc107; background: transparent;"
                                data-id="<?php echo e($user->id); ?>"
                                data-name="<?php echo e($user->name); ?>"
                                data-bs-toggle="tooltip"
                                title="Cambiar contraseña">
                            <i class="fas fa-key"></i>
                        </button>
                        
                        <?php if(auth()->user()->id !== $user->id): ?>
                        <!-- Botón Bloquear/Desbloquear -->
                        <button type="button" 
                                class="btn btn-outline-<?php echo e(isset($user->blocked_at) ? 'success' : 'secondary'); ?> btn-sm toggle-status" 
                                data-id="<?php echo e($user->id); ?>"
                                data-name="<?php echo e($user->name); ?>"
                                data-status="<?php echo e(isset($user->blocked_at) ? 'blocked' : 'active'); ?>"
                                data-bs-toggle="tooltip"
                                title="<?php echo e(isset($user->blocked_at) ? 'Desbloquear usuario' : 'Bloquear usuario'); ?>">
                            <i class="fas fa-<?php echo e(isset($user->blocked_at) ? 'unlock' : 'user-lock'); ?>"></i>
                        </button>
                        
                        <!-- Botón Eliminar -->
                        <button type="button" 
                                class="btn btn-outline-danger btn-sm delete-user" 
                                data-id="<?php echo e($user->id); ?>"
                                data-name="<?php echo e($user->name); ?>"
                                data-bs-toggle="tooltip"
                                title="Eliminar usuario">
                            <i class="fas fa-trash"></i>
                        </button>
                        <?php else: ?>
                        <button type="button" 
                                class="btn btn-outline-secondary btn-sm" 
                                disabled
                                data-bs-toggle="tooltip"
                                title="No puedes realizar acciones sobre tu propia cuenta">
                            <i class="fas fa-lock"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    <span class="text-muted d-flex align-items-center justify-content-center">
                        <i class="fas fa-lock me-1"></i>
                        <small>Sin permisos</small>
                    </span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="7" class="text-center py-5">
                    <div class="d-flex flex-column align-items-center">
                        <div class="mb-3">
                            <i class="fas fa-users fa-4x text-muted opacity-50"></i>
                        </div>
                        <h5 class="text-muted mb-2">No hay usuarios registrados</h5>
                        <p class="text-muted mb-3">Los usuarios aparecerán aquí una vez que sean creados.</p>
                        <?php if(auth()->user()->role === 'administrador'): ?>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                            <i class="fas fa-plus me-1"></i>Crear Primer Usuario
                        </button>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if($users->hasPages()): ?>
<div class="d-flex justify-content-center mt-4 px-3">
    <nav aria-label="Navegación de usuarios">
        <?php echo e($users->appends(request()->query())->links('pagination::bootstrap-4')); ?>

    </nav>
</div>
<?php endif; ?>

<style>
.avatar-md {
    width: 48px;
    height: 48px;
    font-size: 1rem;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.table tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

.btn-group .btn {
    margin: 0 1px;
    border-radius: 6px !important;
}

.badge {
    font-size: 0.75rem;
    font-weight: 600;
}

.table th {
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}

.border-bottom {
    border-bottom: 1px solid #f8f9fa !important;
}
</style>
<?php /**PATH C:\xampp\htdocs\Login-app\resources\views/users/partials/table.blade.php ENDPATH**/ ?>