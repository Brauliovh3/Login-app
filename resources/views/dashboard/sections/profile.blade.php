<div class="row">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">👤 Mi Perfil</h6>
            </div>
            <div class="card-body">
                <form id="profile-form">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="profile-name" class="form-label">Nombre Completo</label>
                                <input type="text" class="form-control" id="profile-name" value="{{ auth()->user()->name }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="profile-username" class="form-label">Usuario</label>
                                <input type="text" class="form-control" id="profile-username" value="{{ auth()->user()->username }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="profile-email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="profile-email" value="{{ auth()->user()->email }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="profile-role" class="form-label">Rol</label>
                                <input type="text" class="form-control" id="profile-role" value="{{ ucfirst(auth()->user()->role) }}" readonly>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h6 class="text-primary">🔒 Cambiar Contraseña</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="current-password" class="form-label">Contraseña Actual</label>
                                <input type="password" class="form-control" id="current-password">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="new-password" class="form-label">Nueva Contraseña</label>
                                <input type="password" class="form-control" id="new-password">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="confirm-password" class="form-label">Confirmar Contraseña</label>
                                <input type="password" class="form-control" id="confirm-password">
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="resetProfileForm()">
                            <i class="fas fa-undo"></i> Restablecer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">ℹ️ Información de la Cuenta</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Estado:</strong>
                    <span class="badge bg-{{ auth()->user()->status === 'approved' ? 'success' : 'warning' }}">
                        {{ ucfirst(auth()->user()->status) }}
                    </span>
                </div>
                <div class="mb-3">
                    <strong>Fecha de Registro:</strong><br>
                    <small class="text-muted">{{ auth()->user()->created_at->format('d/m/Y H:i') }}</small>
                </div>
                @if(auth()->user()->approved_at)
                <div class="mb-3">
                    <strong>Fecha de Aprobación:</strong><br>
                    <small class="text-muted">{{ auth()->user()->approved_at->format('d/m/Y H:i') }}</small>
                </div>
                @endif
                <div class="mb-3">
                    <strong>Último Acceso:</strong><br>
                    <small class="text-muted">{{ now()->format('d/m/Y H:i') }}</small>
                </div>
            </div>
        </div>
        
        <div class="card shadow mt-3">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">⚙️ Configuración</h6>
            </div>
            <div class="card-body">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="notifications-enabled" checked>
                    <label class="form-check-label" for="notifications-enabled">
                        Recibir Notificaciones
                    </label>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="dark-mode">
                    <label class="form-check-label" for="dark-mode">
                        Modo Oscuro
                    </label>
                </div>
                <button class="btn btn-outline-primary btn-sm w-100">
                    <i class="fas fa-cog"></i> Configuración Avanzada
                </button>
            </div>
        </div>
    </div>
</div>

<script>
window.init_profile = function() {
    // Inicializar formulario de perfil
    document.getElementById('profile-form').addEventListener('submit', function(e) {
        e.preventDefault();
        updateProfile();
    });
};

function updateProfile() {
    const formData = {
        name: document.getElementById('profile-name').value,
        email: document.getElementById('profile-email').value,
        current_password: document.getElementById('current-password').value,
        new_password: document.getElementById('new-password').value,
        confirm_password: document.getElementById('confirm-password').value
    };
    
    // Validaciones básicas
    if (!formData.name || !formData.email) {
        showNotification('Por favor completa todos los campos obligatorios', 'error');
        return;
    }
    
    if (formData.new_password && formData.new_password !== formData.confirm_password) {
        showNotification('Las contraseñas no coinciden', 'error');
        return;
    }
    
    if (formData.new_password && !formData.current_password) {
        showNotification('Ingresa tu contraseña actual para cambiarla', 'error');
        return;
    }
    
    // Enviar datos
    fetch('/api/update-profile', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.ok) {
            showNotification('Perfil actualizado correctamente', 'success');
            // Limpiar campos de contraseña
            document.getElementById('current-password').value = '';
            document.getElementById('new-password').value = '';
            document.getElementById('confirm-password').value = '';
        } else {
            showNotification(data.message || 'Error actualizando perfil', 'error');
        }
    })
    .catch(error => {
        showNotification('Error de conexión', 'error');
        console.error('Error:', error);
    });
}

function resetProfileForm() {
    document.getElementById('profile-name').value = '{{ auth()->user()->name }}';
    document.getElementById('profile-email').value = '{{ auth()->user()->email }}';
    document.getElementById('current-password').value = '';
    document.getElementById('new-password').value = '';
    document.getElementById('confirm-password').value = '';
}
</script>