// Sistema de gestión de perfil de usuario
console.log('📋 Cargando módulo de perfil...');

// Función para cargar el perfil del usuario
function loadPerfil() {
    fetch('dashboard.php?api=perfil')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarPerfil(data.user);
            } else {
                mostrarError('Error al cargar el perfil');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarError('Error de conexión');
        });
}

// Función para mostrar el perfil
function mostrarPerfil(user) {
    const iniciales = user.name ? user.name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2) : 'U';
    const imagenPerfil = user.profile_image || '';
    const esAdministrador = user.role === 'administrador' || user.role === 'admin';
    
    const perfilHTML = `
        <div class="content-section active" id="perfil-section">
            <div class="row">
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <div class="position-relative d-inline-block mb-3">
                                ${imagenPerfil ? 
                                    `<img src="${imagenPerfil}" alt="Foto de perfil" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover;" id="profileImage">` :
                                    `<div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 120px; height: 120px; font-size: 3rem; font-weight: bold;" id="profileImage">${iniciales}</div>`
                                }
                                <button class="btn btn-sm btn-primary rounded-circle position-absolute" style="bottom: 0; right: 0; width: 35px; height: 35px;" onclick="document.getElementById('fileInput').click()">
                                    <i class="fas fa-camera"></i>
                                </button>
                                <input type="file" id="fileInput" accept="image/*" style="display: none;" onchange="cambiarImagenPerfil(event)">
                            </div>
                            <h4 class="mb-1">${user.name || 'Usuario'}</h4>
                            <p class="text-muted mb-2">${user.username || ''}</p>
                            <span class="badge bg-primary">${user.role || 'usuario'}</span>
                        </div>
                    </div>
                    
                    <div class="card shadow-sm mt-3">
                        <div class="card-body">
                            <h6 class="card-title mb-3"><i class="fas fa-info-circle me-2"></i>Información Personal</h6>
                            <div class="mb-2">
                                <small class="text-muted">Nombre Completo</small>
                                <p class="mb-0">${user.name || 'N/A'}</p>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Email</small>
                                <p class="mb-0">${user.email || 'N/A'}</p>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Teléfono</small>
                                <p class="mb-0">${user.phone || '+51 999 999 999'}</p>
                            </div>
                            <div class="mb-0">
                                <small class="text-muted">Rol</small>
                                <p class="mb-0">${user.role || 'usuario'}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Editar Perfil</h5>
                        </div>
                        <div class="card-body">
                            <form id="formEditarPerfil" onsubmit="guardarPerfil(event)">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nombre Completo</label>
                                        <input type="text" class="form-control" name="name" value="${user.name || ''}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Usuario</label>
                                        <input type="text" class="form-control" name="username" value="${user.username || ''}" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" value="${user.email || ''}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Teléfono</label>
                                        <input type="tel" class="form-control" name="phone" value="${user.phone || ''}">
                                    </div>
                                </div>
                                
                                ${esAdministrador ? `
                                <hr class="my-4">
                                <h6 class="mb-3"><i class="fas fa-lock me-2"></i>Cambiar Contraseña (Opcional)</h6>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nueva Contraseña</label>
                                        <input type="password" class="form-control" name="password" minlength="8">
                                        <small class="text-muted">Dejar en blanco para no cambiar</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Confirmar Contraseña</label>
                                        <input type="password" class="form-control" name="password_confirmation">
                                    </div>
                                </div>
                                ` : ''}
                                
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Guardar Cambios
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    const container = document.getElementById('contentContainer');
    if (container) {
        container.innerHTML = perfilHTML;
    }
}

// Función para cambiar la imagen de perfil
function cambiarImagenPerfil(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    // Validar tipo de archivo
    if (!file.type.startsWith('image/')) {
        mostrarAlerta('Por favor selecciona una imagen válida', 'error');
        return;
    }
    
    // Validar tamaño (máximo 5MB)
    if (file.size > 5 * 1024 * 1024) {
        mostrarAlerta('La imagen no debe superar los 5MB', 'error');
        return;
    }
    
    const formData = new FormData();
    formData.append('profile_image', file);
    
    // Mostrar preview inmediato
    const reader = new FileReader();
    reader.onload = function(e) {
        const profileImage = document.getElementById('profileImage');
        if (profileImage) {
            if (profileImage.tagName === 'IMG') {
                profileImage.src = e.target.result;
            } else {
                profileImage.outerHTML = `<img src="${e.target.result}" alt="Foto de perfil" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover;" id="profileImage">`;
            }
        }
    };
    reader.readAsDataURL(file);
    
    // Subir imagen al servidor
    fetch('dashboard.php?api=upload-profile-image', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarAlerta('Imagen de perfil actualizada correctamente', 'success');
            // Actualizar imagen en el navbar
            actualizarImagenNavbar(data.image_url);
        } else {
            mostrarAlerta(data.message || 'Error al subir la imagen', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarAlerta('Error al subir la imagen', 'error');
    });
}

// Función para actualizar la imagen en el navbar
function actualizarImagenNavbar(imageUrl) {
    // Actualizar en el dropdown del usuario
    const userDropdown = document.getElementById('userDropdown');
    if (userDropdown) {
        const icon = userDropdown.querySelector('i.fa-user');
        if (icon && imageUrl) {
            icon.outerHTML = `<img src="${imageUrl}" alt="Perfil" class="rounded-circle me-2" style="width: 24px; height: 24px; object-fit: cover;">`;
        }
    }
    
    // Actualizar en el sidebar
    const sidebarHeader = document.querySelector('.sidebar-header');
    if (sidebarHeader && imageUrl) {
        const existingImage = sidebarHeader.querySelector('img');
        if (existingImage) {
            existingImage.src = imageUrl;
        } else {
            const avatar = sidebarHeader.querySelector('.avatar-title');
            if (avatar) {
                avatar.outerHTML = `<img src="${imageUrl}" alt="Perfil" class="rounded-circle mb-2" style="width: 64px; height: 64px; object-fit: cover;">`;
            }
        }
    }
}

// Función para guardar cambios del perfil
function guardarPerfil(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    // Validar contraseñas si se proporcionaron
    const password = formData.get('password');
    const passwordConfirmation = formData.get('password_confirmation');
    
    if (password && password !== passwordConfirmation) {
        mostrarAlerta('Las contraseñas no coinciden', 'error');
        return;
    }
    
    // Convertir FormData a objeto
    const data = {};
    formData.forEach((value, key) => {
        if (value) data[key] = value;
    });
    
    fetch('dashboard.php?api=update-perfil', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarAlerta('Perfil actualizado correctamente', 'success');
            // Recargar perfil
            setTimeout(() => loadPerfil(), 1500);
        } else {
            mostrarAlerta(data.message || 'Error al actualizar el perfil', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarAlerta('Error al actualizar el perfil', 'error');
    });
}

// Función auxiliar para mostrar alertas
function mostrarAlerta(mensaje, tipo) {
    const alertClass = tipo === 'success' ? 'alert-success' : 'alert-danger';
    const icon = tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    
    const alertHTML = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="fas ${icon} me-2"></i>${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    const container = document.getElementById('contentContainer');
    if (container) {
        container.insertAdjacentHTML('afterbegin', alertHTML);
        setTimeout(() => {
            const alert = container.querySelector('.alert');
            if (alert) alert.remove();
        }, 5000);
    }
}

// Exportar funciones
window.loadPerfil = loadPerfil;
window.cambiarImagenPerfil = cambiarImagenPerfil;
window.guardarPerfil = guardarPerfil;

console.log('✅ Módulo de perfil cargado correctamente');
