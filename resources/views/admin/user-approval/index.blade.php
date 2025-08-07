@extends('layouts.dashboard')

@section('title', 'Gestión de Usuarios - Administrador DRTC')

@section('content')
<style>
    :root {
        --drtc-orange: #ff8c00;
        --drtc-dark-orange: #e67c00;
        --drtc-navy: #1e3a8a;
    }
    
    .approval-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .approval-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }
    
    .user-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--drtc-orange), var(--drtc-dark-orange));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 1.5rem;
    }
    
    .status-badge {
        border-radius: 25px;
        padding: 8px 16px;
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .btn-approve {
        background: linear-gradient(135deg, #28a745, #20c997);
        border: none;
        border-radius: 25px;
        padding: 8px 20px;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-approve:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
    }
    
    .btn-reject {
        background: linear-gradient(135deg, #dc3545, #e74c3c);
        border: none;
        border-radius: 25px;
        padding: 8px 20px;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-reject:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
    }
    
    /* Estilos adicionales para modales flotantes */
    .floating-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.8);
        z-index: 10000;
        display: none;
        overflow-y: auto;
        padding: 20px;
        box-sizing: border-box;
    }

    .floating-modal.show {
        display: block;
        animation: fadeInModal 0.3s ease-out;
    }

    .floating-modal .modal-content-wrapper {
        background: white;
        border-radius: 15px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: slideInModal 0.3s ease-out;
        position: relative;
    }

    .floating-modal .modal-header-custom {
        padding: 20px 30px;
        border-radius: 15px 15px 0 0;
        position: relative;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .floating-modal .modal-body-custom {
        padding: 30px;
    }

    .floating-modal .close-modal {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        font-size: 20px;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .floating-modal .close-modal:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: rotate(90deg);
    }

    @keyframes fadeInModal {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideInModal {
        from {
            opacity: 0;
            transform: translateY(50px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* Pulse animation for loading */
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    .btn-loading {
        animation: pulse 1s infinite;
    }
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1" style="color: var(--drtc-navy);">
                        <i class="fas fa-users-cog me-2" style="color: var(--drtc-orange);"></i>
                        Gestión de Usuarios
                    </h2>
                    <p class="text-muted mb-0">Revisa y aprueba las solicitudes de registro de nuevos usuarios</p>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-warning fs-6">{{ $pendingUsers->count() }} Pendientes</span>
                    <span class="badge bg-danger fs-6">{{ $rejectedUsers->count() }} Rechazados</span>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Usuarios Pendientes -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card approval-card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>
                        Solicitudes Pendientes de Aprobación ({{ $pendingUsers->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    @if($pendingUsers->count() > 0)
                        <div class="row">
                            @foreach($pendingUsers as $user)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 border">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="user-avatar me-3">
                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 fw-bold">{{ $user->name }}</h6>
                                                    <small class="text-muted">@{{ $user->username }}</small>
                                                </div>
                                                <span class="status-badge bg-warning text-dark">Pendiente</span>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <p class="mb-1"><i class="fas fa-envelope me-2 text-muted"></i>{{ $user->email }}</p>
                                                <p class="mb-1"><i class="fas fa-user-tag me-2 text-muted"></i>{{ ucfirst($user->role) }}</p>
                                                <p class="mb-0"><i class="fas fa-calendar me-2 text-muted"></i>{{ $user->created_at->format('d/m/Y H:i') }}</p>
                                            </div>
                                            
                                            <div class="d-flex gap-2">
                                                <button type="button" 
                                                        class="btn btn-approve flex-fill" 
                                                        onclick="approveUser({{ $user->id }})"
                                                        data-user-id="{{ $user->id }}"
                                                        data-user-name="{{ $user->name }}"
                                                        data-user-email="{{ $user->email }}"
                                                        data-user-role="{{ ucfirst($user->role) }}"
                                                        data-user-initials="{{ strtoupper(substr($user->name, 0, 2)) }}">
                                                    <i class="fas fa-check me-1"></i>Aprobar
                                                </button>
                                                <button type="button" class="btn btn-reject flex-fill" onclick="showRejectModal({{ $user->id }}, '{{ $user->name }}')">
                                                    <i class="fas fa-times me-1"></i>Rechazar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                            <h5>¡No hay solicitudes pendientes!</h5>
                            <p class="text-muted">Todas las solicitudes de registro han sido procesadas.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Usuarios Rechazados Recientes -->
    @if($rejectedUsers->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card approval-card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-times-circle me-2"></i>
                            Solicitudes Rechazadas Recientes ({{ $rejectedUsers->count() }})
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Email</th>
                                        <th>Rol Solicitado</th>
                                        <th>Motivo de Rechazo</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rejectedUsers as $user)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="user-avatar me-2" style="width: 40px; height: 40px; font-size: 1rem;">
                                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">{{ $user->name }}</div>
                                                        <small class="text-muted">@{{ $user->username }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $user->email }}</td>
                                            <td><span class="badge bg-secondary">{{ ucfirst($user->role) }}</span></td>
                                            <td>
                                                <small class="text-muted">{{ Str::limit($user->rejection_reason, 50) }}</small>
                                            </td>
                                            <td>{{ $user->updated_at->format('d/m/Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Modal para Rechazar Usuario -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-times-circle me-2"></i>
                    Rechazar Solicitud
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas rechazar la solicitud de <strong id="rejectUserName"></strong>?</p>
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Motivo del rechazo <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required 
                                  placeholder="Explica brevemente por qué se rechaza esta solicitud..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-1"></i>Rechazar Solicitud
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Flotante para Aprobación de Usuario -->
<div class="floating-modal" id="approvalModal">
    <div class="modal-content-wrapper" style="max-width: 500px; margin: 100px auto;">
        <div class="modal-header-custom" style="background: linear-gradient(135deg, #28a745, #20c997);">
            <div class="d-flex align-items-center">
                <i class="fas fa-user-check fa-2x me-3"></i>
                <div>
                    <h4 class="mb-0">Confirmar Aprobación</h4>
                    <small class="opacity-75">Gestión de Usuarios DRTC</small>
                </div>
            </div>
            <button class="close-modal" onclick="cerrarModalAprobacion()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body-custom">
            <div class="text-center mb-4">
                <div class="user-avatar mx-auto mb-3" id="approvalUserAvatar" style="width: 80px; height: 80px; font-size: 2rem;">
                    
                </div>
                <h5 class="fw-bold" id="approvalUserName">Usuario</h5>
                <p class="text-muted mb-0" id="approvalUserEmail">email@example.com</p>
                <span class="badge bg-primary" id="approvalUserRole">Rol</span>
            </div>
            
            <div class="alert alert-info d-flex align-items-center">
                <i class="fas fa-info-circle me-3 fa-lg"></i>
                <div>
                    <strong>¿Confirmas la aprobación?</strong><br>
                    <small>El usuario podrá acceder al sistema inmediatamente y recibirá una notificación por email.</small>
                </div>
            </div>

            <div class="row g-3 mt-3">
                <div class="col-6">
                    <button type="button" class="btn btn-secondary w-100" onclick="cerrarModalAprobacion()">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                </div>
                <div class="col-6">
                    <button type="button" class="btn btn-success w-100" onclick="confirmarAprobacion()" id="btnConfirmarAprobacion">
                        <i class="fas fa-check me-2"></i>Aprobar Usuario
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Flotante de Confirmación Exitosa -->
<div class="floating-modal" id="successModal">
    <div class="modal-content-wrapper" style="max-width: 450px; margin: 150px auto;">
        <div class="modal-header-custom" style="background: linear-gradient(135deg, #28a745, #20c997);">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle fa-2x me-3"></i>
                <div>
                    <h4 class="mb-0">¡Usuario Aprobado!</h4>
                    <small class="opacity-75">Operación completada exitosamente</small>
                </div>
            </div>
            <button class="close-modal" onclick="cerrarModalExito()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body-custom text-center">
            <div class="mb-4">
                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                <h5>¡Aprobación Exitosa!</h5>
                <p class="text-muted mb-0">
                    El usuario <strong id="successUserName">Usuario</strong> ha sido aprobado exitosamente.
                </p>
            </div>
            
            <div class="alert alert-success d-flex align-items-center">
                <i class="fas fa-paper-plane me-3"></i>
                <div class="text-start">
                    <strong>Notificación enviada</strong><br>
                    <small>Se ha enviado un email de confirmación al usuario informándole que su cuenta está activa.</small>
                </div>
            </div>

            <button type="button" class="btn btn-primary w-100" onclick="cerrarModalExito()">
                <i class="fas fa-thumbs-up me-2"></i>Entendido
            </button>
        </div>
    </div>
</div>

<script>
let currentUserId = null;
let currentUserData = {};
let isProcessing = false; // Variable para prevenir múltiples envíos

function approveUser(userId) {
    // Prevenir múltiples clicks
    if (isProcessing) return;
    
    // Buscar el botón para obtener los datos del usuario
    const approveButton = document.querySelector(`button[data-user-id="${userId}"]`);
    
    const userName = approveButton.dataset.userName;
    const userEmail = approveButton.dataset.userEmail;
    const userRole = approveButton.dataset.userRole;
    const userInitials = approveButton.dataset.userInitials;
    
    // Guardar datos del usuario actual
    currentUserId = userId;
    currentUserData = {
        name: userName,
        email: userEmail,
        role: userRole,
        initials: userInitials
    };
    
    // Llenar el modal con los datos del usuario
    document.getElementById('approvalUserAvatar').textContent = userInitials;
    document.getElementById('approvalUserName').textContent = userName;
    document.getElementById('approvalUserEmail').textContent = userEmail;
    document.getElementById('approvalUserRole').textContent = userRole;
    
    // Mostrar el modal de aprobación
    abrirModal('approvalModal');
}

function confirmarAprobacion() {
    if (!currentUserId || isProcessing) return;
    
    // Marcar como procesando
    isProcessing = true;
    
    // Deshabilitar botón para evitar doble click
    const btnConfirmar = document.getElementById('btnConfirmarAprobacion');
    btnConfirmar.disabled = true;
    btnConfirmar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
    
    // Crear formulario para enviar aprobación
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/users/${currentUserId}/approve`;
    form.style.display = 'none';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    
    form.appendChild(csrfToken);
    document.body.appendChild(form);
    
    // Mostrar modal de éxito antes de enviar el formulario
    setTimeout(() => {
        // Cerrar modal de confirmación
        cerrarModalAprobacion();
        
        // Mostrar modal de éxito
        setTimeout(() => {
            document.getElementById('successUserName').textContent = currentUserData.name;
            abrirModal('successModal');
            
            // Enviar formulario después de un momento
            setTimeout(() => {
                form.submit();
            }, 800);
        }, 300);
    }, 800);
}

function cerrarModalAprobacion() {
    cerrarModal('approvalModal');
    // Resetear botón
    const btnConfirmar = document.getElementById('btnConfirmarAprobacion');
    btnConfirmar.disabled = false;
    btnConfirmar.innerHTML = '<i class="fas fa-check me-2"></i>Aprobar Usuario';
}

function cerrarModalExito() {
    cerrarModal('successModal');
    currentUserId = null;
    currentUserData = {};
    isProcessing = false; // Resetear el estado de procesamiento
}

function showRejectModal(userId, userName) {
    document.getElementById('rejectUserName').textContent = userName;
    document.getElementById('rejectForm').action = `/admin/users/${userId}/reject`;
    
    // Agregar event listener para mostrar notificación cuando se rechace
    document.getElementById('rejectForm').onsubmit = function() {
        if (window.showWarning) {
            setTimeout(() => {
                window.showWarning('La solicitud de registro ha sido rechazada.');
            }, 500);
        }
    };
    
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}

// Funciones para modales flotantes (ya definidas en dashboard.blade.php)
function abrirModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
}

function cerrarModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
    }
}

// Cerrar modales al hacer click fuera
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('floating-modal')) {
        const modalId = e.target.id;
        if (modalId === 'approvalModal') {
            cerrarModalAprobacion();
        } else if (modalId === 'successModal') {
            cerrarModalExito();
        }
    }
});

// Cerrar modales con tecla ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (document.getElementById('approvalModal').classList.contains('show')) {
            cerrarModalAprobacion();
        }
        if (document.getElementById('successModal').classList.contains('show')) {
            cerrarModalExito();
        }
    }
});
</script>
@endsection
