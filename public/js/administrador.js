/**
 * SISTEMA DE GESTIÓN - MÓDULO ADMINISTRADOR
 * Funcionalidades específicas para el rol administrador
 */

console.log(' Cargando módulo administrador...');

// Variable global para verificar que el usuario es administrador
let isAdministrador = false;

// ==================== SISTEMA DE DATOS TEMPORAL ====================
// Simulamos una base de datos en memoria para que funcione el CRUD
let usuariosData = [
    {
        id: 1,
        nombre: 'Juan Pérez',
        username: 'jperez',
        email: 'juan@ejemplo.com',
        rol: 'fiscalizador',
        estado: 'activo',
        created_at: '2025-09-30',
        ultimo_acceso: '2025-10-01 08:30:00',
        direccion: 'Av. Principal 123, Lima',
        telefono: '+51 987654321',
        dni: '12345678'
    },
    {
        id: 2,
        nombre: 'María García',
        username: 'mgarcia',
        email: 'maria@ejemplo.com',
        rol: 'inspector',
        estado: 'activo',
        created_at: '2025-09-25',
        ultimo_acceso: '2025-09-30 15:45:00',
        direccion: 'Jr. Los Olivos 456, Lima',
        telefono: '+51 987654322',
        dni: '87654321'
    },
    {
        id: 3,
        nombre: 'Carlos López',
        username: 'clopez',
        email: 'carlos@ejemplo.com',
        rol: 'ventanilla',
        estado: 'inactivo',
        created_at: '2025-09-20',
        ultimo_acceso: '2025-09-28 10:15:00',
        direccion: 'Calle Las Flores 789, Lima',
        telefono: '+51 987654323',
        dni: '11223344'
    }
];

// Contador para IDs únicos
let nextUserId = 4;

// ==================== SISTEMA DE NOTIFICACIONES ELEGANTES ====================
function showToast(message, type = 'success', duration = 4000) {
    const toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) return;

    // Crear ID único para el toast
    const toastId = 'toast-' + Date.now();
    
    // Definir iconos según el tipo
    const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-times-circle',
        warning: 'fas fa-exclamation-triangle',
        info: 'fas fa-info-circle'
    };

    // Crear el toast
    const toastHTML = `
        <div id="${toastId}" class="toast custom-toast toast-${type} fade-in" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-body d-flex align-items-center">
                <i class="${icons[type]} toast-icon"></i>
                <div class="flex-grow-1">${message}</div>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;

    // Agregar al contenedor
    toastContainer.insertAdjacentHTML('beforeend', toastHTML);

    // Obtener el elemento del toast
    const toastElement = document.getElementById(toastId);
    
    // Inicializar el toast de Bootstrap
    const toast = new bootstrap.Toast(toastElement, {
        autohide: true,
        delay: duration
    });

    // Mostrar el toast
    toast.show();

    // Remover del DOM después de que se oculte
    toastElement.addEventListener('hidden.bs.toast', function() {
        toastElement.remove();
    });

    return toast;
}

// Funciones específicas para cada tipo de notificación
function showSuccessToast(message, duration = 4000) {
    return showToast(message, 'success', duration);
}

function showErrorToast(message, duration = 5000) {
    return showToast(message, 'error', duration);
}

function showWarningToast(message, duration = 4500) {
    return showToast(message, 'warning', duration);
}

function showInfoToast(message, duration = 4000) {
    return showToast(message, 'info', duration);
}

// ==================== MODALES DE CONFIRMACIÓN ELEGANTES ====================
// ==================== MODALES DE CONFIRMACIÓN ELEGANTES ====================
function showConfirmModal(options) {
    const {
        title = '¿Estás seguro?',
        message = '¿Deseas continuar con esta acción?',
        type = 'warning', // warning, danger, info, success
        confirmText = 'Confirmar',
        cancelText = 'Cancelar',
        onConfirm = () => {},
        onCancel = () => {},
        showIcon = true
    } = options;

    // Crear ID único para el modal
    const modalId = 'confirmModal-' + Date.now();
    
    // Definir colores y iconos según el tipo
    const typeConfig = {
        warning: {
            bgClass: 'bg-warning text-dark',
            icon: 'fas fa-exclamation-triangle',
            btnClass: 'btn-warning'
        },
        danger: {
            bgClass: 'bg-danger text-white',
            icon: 'fas fa-exclamation-circle',
            btnClass: 'btn-danger'
        },
        info: {
            bgClass: 'bg-info text-white',
            icon: 'fas fa-info-circle',
            btnClass: 'btn-info'
        },
        success: {
            bgClass: 'bg-success text-white',
            icon: 'fas fa-check-circle',
            btnClass: 'btn-success'
        }
    };

    const config = typeConfig[type] || typeConfig.warning;

    // Crear el modal HTML
    const modalHTML = `
        <div class="modal fade" id="${modalId}" tabindex="-1" aria-labelledby="${modalId}Label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border: none; border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
                    <div class="modal-header ${config.bgClass}" style="border-radius: 15px 15px 0 0; border: none;">
                        <h5 class="modal-title" id="${modalId}Label">
                            ${showIcon ? `<i class="${config.icon} me-2"></i>` : ''}
                            ${title}
                        </h5>
                        <button type="button" class="btn-close ${type === 'warning' ? '' : 'btn-close-white'}" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="padding: 2rem;">
                        <div class="text-center">
                            ${showIcon ? `<i class="${config.icon}" style="font-size: 4rem; color: var(--bs-${type}); margin-bottom: 1rem;"></i>` : ''}
                            <p style="font-size: 1.1rem; color: #555; margin-bottom: 0;">${message}</p>
                        </div>
                    </div>
                    <div class="modal-footer" style="border: none; padding: 1rem 2rem 2rem;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; padding: 0.5rem 1.5rem;">
                            <i class="fas fa-times me-1"></i> ${cancelText}
                        </button>
                        <button type="button" class="btn ${config.btnClass}" id="${modalId}-confirm" style="border-radius: 8px; padding: 0.5rem 1.5rem;">
                            <i class="fas fa-check me-1"></i> ${confirmText}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Agregar modal al body
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Obtener elementos del modal
    const modalElement = document.getElementById(modalId);
    const confirmBtn = document.getElementById(modalId + '-confirm');

    // Configurar eventos
    confirmBtn.addEventListener('click', function() {
        const modal = bootstrap.Modal.getInstance(modalElement);
        modal.hide();
        onConfirm();
    });

    modalElement.addEventListener('hidden.bs.modal', function() {
        modalElement.remove();
    });

    // Agregar evento para el botón cancelar
    modalElement.querySelector('.btn-secondary').addEventListener('click', function() {
        onCancel();
    });

    // Mostrar modal
    const modal = new bootstrap.Modal(modalElement);
    modal.show();

    // Remover del DOM cuando se cierre
    modalElement.addEventListener('hidden.bs.modal', function() {
        modalElement.remove();
    });

    return modal;
}

// Inicialización del módulo administrador
document.addEventListener('DOMContentLoaded', function() {
    if (window.dashboardUserRole === 'administrador' || window.dashboardUserRole === 'admin') {
        isAdministrador = true;
        console.log(' Módulo administrador habilitado para:', window.dashboardUserName);
        initializeAdministradorModule();
    }
});

function initializeAdministradorModule() {
    console.log(' Inicializando módulo administrador...');
    
    // Cargar estadísticas del dashboard al inicio
    loadDashboardStatsAdmin();
    
    // Configurar eventos específicos del administrador
    setupAdministradorEvents();
}

function setupAdministradorEvents() {
    // Configurar eventos específicos para administrador
    console.log(' Configurando eventos del administrador...');
}

// ==================== DASHBOARD STATS ADMIN ====================
async function loadDashboardStatsAdmin() {
    console.log(' Cargando estadísticas del administrador...');
    
    try {
        const response = await fetch(`${window.location.origin}${window.location.pathname}?api=dashboard-stats`);
        const result = await response.json();
        
        if (result.success && result.stats) {
            updateDashboardStatsAdmin(result.stats);
        } else {
            console.error(' Error al cargar estadísticas:', result.message);
        }
    } catch (error) {
        console.error(' Error al cargar estadísticas del admin:', error);
    }
}

function updateDashboardStatsAdmin(stats) {
    console.log(' Actualizando estadísticas del admin:', stats);
    
    // Actualizar contadores específicos para administrador
    if (document.getElementById('total-actas')) {
        document.getElementById('total-actas').textContent = stats.total_infracciones || 0;
    }
    
    if (document.getElementById('total-conductores')) {
        document.getElementById('total-conductores').textContent = stats.total_conductores || 0;
    }
    
    if (document.getElementById('total-vehiculos')) {
        document.getElementById('total-vehiculos').textContent = stats.total_vehiculos || 0;
    }
    
    if (document.getElementById('total-notifications')) {
        document.getElementById('total-notifications').textContent = stats.usuarios_pendientes || 0;
    }
    
    // Crear cards adicionales específicas para administrador
    createAdminSpecificCards(stats);
}

function createAdminSpecificCards(stats) {
    const dashboardContent = document.getElementById('dashboardContent');
    if (!dashboardContent) return;
    
    // Agregar cards específicas para administrador
    const adminCardsHTML = `
        <div class="col-md-3 mb-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title">Total Usuarios</h5>
                            <h3>${stats.total_usuarios || 0}</h3>
                        </div>
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title">Usuarios Activos</h5>
                            <h3>${stats.usuarios_activos || 0}</h3>
                        </div>
                        <i class="fas fa-user-check fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title">Usuarios Pendientes</h5>
                            <h3>${stats.usuarios_pendientes || 0}</h3>
                        </div>
                        <i class="fas fa-user-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title">Total Infracciones</h5>
                            <h3>${stats.total_infracciones || 0}</h3>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Agregar las cards adicionales
    dashboardContent.insertAdjacentHTML('beforeend', adminCardsHTML);
}

// ==================== GESTIÓN DE USUARIOS ====================
function loadUsuariosList() {
    console.log(' [ADMIN] Cargando lista de usuarios...');
    
    if (!isAdministrador) {
        showToast('Acceso denegado. Solo administradores pueden ver esta sección.', 'error');
        return;
    }
    
    const contentContainer = document.getElementById('contentContainer');
    if (!contentContainer) {
        console.error(' ContentContainer no encontrado');
        return;
    }
    
    // Mostrar loading
    contentContainer.innerHTML = `
        <div class="content-section active">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-users"></i> Gestión de Usuarios</h2>
                <button class="btn btn-primary" onclick="mostrarModalCrearUsuario()">
                    <i class="fas fa-plus"></i> Nuevo Usuario
                </button>
            </div>
            
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-list"></i> Lista de Usuarios del Sistema
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tablaUsuarios">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaUsuariosBody">
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                        <p class="mt-2 text-muted">Cargando usuarios...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Cargar datos de usuarios
    setTimeout(() => {
        cargarDatosUsuarios();
    }, 500);
}

async function cargarDatosUsuarios() {
    console.log(' Cargando datos de usuarios desde la base de datos...');
    
    try {
        const response = await fetch(`${window.location.origin}${window.location.pathname}?api=users`);
        const result = await response.json();
        
        if (result.success && result.users) {
            console.log(' Usuarios cargados desde la base de datos:', result.users.length);
            console.log(' Datos mapeados:', result.users[0]); // Debug primer usuario
            // Actualizar datos locales para uso en modales
            usuariosData = result.users.map(user => ({
                id: user.id,
                nombre: user.name,
                username: user.username,
                email: user.email,
                rol: user.role,
                estado: user.status === 'approved' ? 'activo' : user.status === 'pending' ? 'pendiente' : 'inactivo',
                created_at: user.created_at,
                ultimo_acceso: 'Por definir',
                direccion: 'Por definir',
                telefono: 'Por definir',
                dni: 'Por definir'
            }));
            console.log(' Usuario mapeado:', usuariosData[0]); // Debug primer usuario mapeado
            mostrarUsuariosEnTabla(usuariosData);
        } else {
            console.log(' Error desde API:', result.message);
            console.log(' Usando datos locales como respaldo');
            mostrarUsuariosEnTabla(usuariosData);
        }
    } catch (error) {
        console.error(' Error de conexión:', error);
        console.log(' Usando datos locales como respaldo');
        mostrarUsuariosEnTabla(usuariosData);
    }
}

function mostrarUsuariosEnTabla(usuarios) {
    const tbody = document.getElementById('tablaUsuariosBody');
    if (!tbody) return;
    
    if (!usuarios || usuarios.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4">
                    <i class="fas fa-users text-muted" style="font-size: 3rem;"></i>
                    <p class="mt-2 text-muted">No hay usuarios registrados</p>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = usuarios.map((usuario, index) => `
        <tr>
            <td><strong>${index + 1}</strong></td>
            <td>${usuario.nombre || 'N/A'}</td>
            <td><code>${usuario.username || 'N/A'}</code></td>
            <td>${usuario.email || 'N/A'}</td>
            <td><span class="badge bg-info">${usuario.rol || 'N/A'}</span></td>
            <td><span class="badge ${getStatusBadgeColor(usuario.estado)}">${usuario.estado || 'N/A'}</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" onclick="verDetalleUsuario(${usuario.id})" title="Ver">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-success" onclick="editarUsuario(${usuario.id})" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    ${usuario.estado === 'pendiente' ? 
                        `<button class="btn btn-outline-success" onclick="aprobarUsuario(${usuario.id})" title="Aprobar">
                            <i class="fas fa-check"></i>
                        </button>` : ''
                    }
                    <button class="btn btn-outline-danger" onclick="eliminarUsuario(${usuario.id}, '${usuario.nombre}')" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function mostrarErrorUsuarios(mensaje) {
    const tbody = document.getElementById('tablaUsuariosBody');
    if (tbody) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4 text-danger">
                    <i class="fas fa-exclamation-triangle" style="font-size: 3rem;"></i>
                    <p class="mt-2">${mensaje}</p>
                    <button class="btn btn-outline-primary" onclick="cargarDatosUsuarios()">
                        <i class="fas fa-refresh"></i> Reintentar
                    </button>
                </td>
            </tr>
        `;
    }
}

function getStatusBadgeColor(status) {
    switch(status) {
        case 'approved': 
        case 'activo': 
            return 'bg-success';
        case 'pending': 
        case 'pendiente': 
            return 'bg-warning text-dark';
        case 'rejected': 
        case 'rechazado': 
            return 'bg-danger';
        case 'suspended': 
        case 'suspendido': 
            return 'bg-secondary';
        case 'inactivo': 
            return 'bg-danger';
        default: 
            return 'bg-light text-dark';
    }
}

// ==================== APROBAR USUARIOS ====================
function loadAprobarUsuarios() {
    console.log(' [ADMIN] Cargando sección aprobar usuarios...');
    
    if (!isAdministrador) {
        showToast('Acceso denegado. Solo administradores pueden ver esta sección.', 'error');
        return;
    }
    
    const contentContainer = document.getElementById('contentContainer');
    contentContainer.innerHTML = `
        <div class="content-section active">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-user-check"></i> Aprobar Usuarios</h2>
                <button class="btn btn-outline-secondary" onclick="cargarUsuariosPendientes()">
                    <i class="fas fa-refresh"></i> Actualizar
                </button>
            </div>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Revisa y aprueba a los usuarios que han solicitado acceso al sistema.
            </div>
            
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0">
                        <i class="fas fa-clock"></i> Usuarios Pendientes de Aprobación
                    </h6>
                </div>
                <div class="card-body">
                    <div id="usuariosPendientesContainer">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2 text-muted">Cargando usuarios pendientes...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Cargar usuarios pendientes
    setTimeout(() => {
        cargarUsuariosPendientes();
    }, 500);
}

async function cargarUsuariosPendientes() {
    console.log(' Cargando usuarios pendientes...');
    console.log('URL completa:', `${window.location.origin}${window.location.pathname}?api=pending-users`);
    
    try {
        const response = await fetch(`${window.location.origin}${window.location.pathname}?api=pending-users`, {
            method: 'GET',
            credentials: 'same-origin', // Incluir cookies de sesión
            headers: {
                'Content-Type': 'application/json',
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        
        console.log(' Respuesta de la API:', result);
        
        if (result.success && result.users) {
            console.log(' Usuarios encontrados:', result.users.length);
            mostrarUsuariosPendientes(result.users);
        } else {
            console.error(' Error al cargar usuarios pendientes:', result.message);
            // Mostrar mensaje de error en lugar de datos de ejemplo
            const container = document.getElementById('usuariosPendientesContainer');
            if (container) {
                container.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h6>No se pudieron cargar los usuarios pendientes</h6>
                        <p class="text-muted">${result.message || 'Error de conexión'}</p>
                        <button class="btn btn-outline-primary" onclick="cargarUsuariosPendientes()">
                            <i class="fas fa-refresh"></i> Reintentar
                        </button>
                    </div>
                `;
            }
        }
    } catch (error) {
        console.error(' Error al cargar usuarios pendientes:', error);
        // Mostrar mensaje de error en lugar de datos de ejemplo
        const container = document.getElementById('usuariosPendientesContainer');
        if (container) {
            container.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                    <h6>Error de conexión</h6>
                    <p class="text-muted">No se pudo conectar con el servidor</p>
                    <button class="btn btn-outline-primary" onclick="cargarUsuariosPendientes()">
                        <i class="fas fa-refresh"></i> Reintentar
                    </button>
                </div>
            `;
        }
    }
}

function mostrarUsuariosPendientes(usuarios) {
    const container = document.getElementById('usuariosPendientesContainer');
    if (!container) return;
    
    if (!usuarios || usuarios.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                <h4 class="mt-3 text-success">¡Excelente!</h4>
                <p class="text-muted">No hay usuarios pendientes de aprobación.</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = usuarios.map(usuario => `
        <div class="card mb-3 border-warning">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="card-title mb-1">
                            <i class="fas fa-user"></i> ${usuario.name || 'Sin nombre'}
                        </h5>
                        <p class="card-text mb-2">
                            <strong>Username:</strong> ${usuario.username || 'N/A'}<br>
                            <strong>Email:</strong> ${usuario.email || 'N/A'}<br>
                            <strong>Teléfono:</strong> ${usuario.phone || 'N/A'}<br>
                            <strong>Fecha de registro:</strong> ${formatearFecha(usuario.created_at)}
                        </p>
                        <span class="badge bg-warning text-dark">Pendiente de Aprobación</span>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="btn-group-vertical">
                            <button class="btn btn-success mb-2" onclick="aprobarUsuario(${usuario.id})">
                                <i class="fas fa-check"></i> Aprobar
                            </button>
                            <button class="btn btn-danger mb-2" onclick="rechazarUsuario(${usuario.id})">
                                <i class="fas fa-times"></i> Rechazar
                            </button>
                            <button class="btn btn-outline-info" onclick="verDetalleUsuario(${usuario.id})">
                                <i class="fas fa-eye"></i> Ver Detalles
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

function mostrarErrorPendientes(mensaje) {
    const container = document.getElementById('usuariosPendientesContainer');
    if (container) {
        container.innerHTML = `
            <div class="text-center py-4 text-danger">
                <i class="fas fa-exclamation-triangle" style="font-size: 3rem;"></i>
                <p class="mt-2">${mensaje}</p>
                <button class="btn btn-outline-primary" onclick="cargarUsuariosPendientes()">
                    <i class="fas fa-refresh"></i> Reintentar
                </button>
            </div>
        `;
    }
}

// ==================== ACCIONES DE USUARIOS ====================
async function aprobarUsuario(userId) {
    showConfirmModal({
        title: 'Aprobar Usuario',
        message: '¿Estás seguro de aprobar este usuario? Esta acción le permitirá acceder al sistema.',
        type: 'success',
        confirmText: 'Aprobar',
        cancelText: 'Cancelar',
        onConfirm: async () => {
            try {
                const formData = new FormData();
                formData.append('user_id', userId);
                
                const response = await fetch(`${window.location.origin}${window.location.pathname}?api=approve-user`, {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showToast('Usuario aprobado correctamente', 'success');
                    cargarUsuariosPendientes(); // Recargar lista
                } else {
                    showToast('Error al aprobar usuario: ' + (result.message || 'Error desconocido'), 'error');
                }
            } catch (error) {
                console.error('Error al aprobar usuario:', error);
                showToast('Error al aprobar usuario: ' + error.message, 'error');
            }
        }
    });
}

async function rechazarUsuario(userId) {
    // Crear modal personalizado para el rechazo
    const modalHtml = `
        <div class="modal fade" id="rejectionModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-user-times me-2"></i>Rechazar Usuario
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3">¿Estás seguro de que deseas rechazar este usuario?</p>
                        <div class="mb-3">
                            <label for="rejectionReason" class="form-label">Razón del rechazo (opcional):</label>
                            <textarea class="form-control" id="rejectionReason" rows="3" 
                                placeholder="Ingresa la razón del rechazo..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-danger" id="confirmReject">
                            <i class="fas fa-user-times me-1"></i>Rechazar Usuario
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remover modal existente si existe
    const existingModal = document.getElementById('rejectionModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Agregar modal al DOM
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Configurar eventos
    const modal = new bootstrap.Modal(document.getElementById('rejectionModal'));
    const confirmBtn = document.getElementById('confirmReject');
    const reasonTextarea = document.getElementById('rejectionReason');
    
    confirmBtn.addEventListener('click', async () => {
        const razon = reasonTextarea.value.trim() || 'Sin razón especificada';
        
        try {
            const formData = new FormData();
            formData.append('user_id', userId);
            formData.append('reason', razon);
            
            const response = await fetch(`${window.location.origin}${window.location.pathname}?api=reject-user`, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            modal.hide();
            
            if (result.success) {
                showToast('Usuario rechazado correctamente', 'success');
                cargarUsuariosPendientes(); // Recargar lista
            } else {
                showToast('Error al rechazar usuario: ' + (result.message || 'Error desconocido'), 'error');
            }
        } catch (error) {
            console.error('Error al rechazar usuario:', error);
            modal.hide();
            showToast('Error al rechazar usuario: ' + error.message, 'error');
        }
    });
    
    // Limpiar modal cuando se cierre
    document.getElementById('rejectionModal').addEventListener('hidden.bs.modal', function () {
        this.remove();
    });
    
    // Mostrar modal
    modal.show();
    
    // Focus en el textarea cuando se muestre el modal
    document.getElementById('rejectionModal').addEventListener('shown.bs.modal', function () {
        reasonTextarea.focus();
    });
}

function verDetalleUsuario(userId) {
    // Buscar usuario en los datos actuales
    const usuario = usuariosData.find(u => u.id == userId);
    
    if (!usuario) {
        showErrorToast('Usuario no encontrado');
        return;
    }
    
    const modalHTML = `
        <div class="modal fade" id="modalDetalleUsuario" tabindex="-1" aria-labelledby="modalDetalleUsuarioLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="modalDetalleUsuarioLabel">
                            <i class="fas fa-user"></i> Detalles del Usuario
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 text-center mb-4">
                                <div class="avatar-wrapper">
                                    <i class="fas fa-user-circle text-secondary" style="font-size: 5rem;"></i>
                                    <h5 class="mt-2">${usuario.nombre}</h5>
                                    <span class="badge ${usuario.estado === 'activo' ? 'bg-success' : 'bg-danger'}">${usuario.estado.toUpperCase()}</span>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <strong>ID:</strong><br>
                                        <span class="text-muted">${usuario.id}</span>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <strong>Username:</strong><br>
                                        <span class="text-muted">${usuario.username}</span>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <strong>Email:</strong><br>
                                        <span class="text-muted">${usuario.email}</span>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <strong>Rol:</strong><br>
                                        <span class="badge bg-primary">${usuario.rol}</span>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <strong>DNI:</strong><br>
                                        <span class="text-muted">${usuario.dni}</span>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <strong>Teléfono:</strong><br>
                                        <span class="text-muted">${usuario.telefono}</span>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <strong>Dirección:</strong><br>
                                        <span class="text-muted">${usuario.direccion}</span>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <strong>Fecha de Registro:</strong><br>
                                        <span class="text-muted">${formatearFecha(usuario.created_at)}</span>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <strong>Último Acceso:</strong><br>
                                        <span class="text-muted">${usuario.ultimo_acceso}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cerrar
                        </button>
                        <button type="button" class="btn btn-warning" onclick="editarUsuario(${usuario.id})">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remover modal existente si existe
    const existingModal = document.getElementById('modalDetalleUsuario');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Agregar modal al body
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('modalDetalleUsuario'));
    modal.show();
}

function editarUsuario(userId) {
    // Buscar usuario en los datos actuales
    const usuario = usuariosData.find(u => u.id == userId);
    
    if (!usuario) {
        showErrorToast('Usuario no encontrado');
        return;
    }
    
    const modalHTML = `
        <div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title" id="modalEditarUsuarioLabel">
                            <i class="fas fa-edit"></i> Editar Usuario: ${usuario.nombre}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formEditarUsuario">
                            <input type="hidden" id="editUserId" value="${usuario.id}">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editNombreUsuario" class="form-label">Nombre Completo *</label>
                                        <input type="text" class="form-control" id="editNombreUsuario" 
                                               value="${usuario.nombre}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editUsernameUsuario" class="form-label">Username *</label>
                                        <input type="text" class="form-control" id="editUsernameUsuario" 
                                               value="${usuario.username}" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editEmailUsuario" class="form-label">Email *</label>
                                        <input type="email" class="form-control" id="editEmailUsuario" 
                                               value="${usuario.email}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editRolUsuario" class="form-label">Rol *</label>
                                        <select class="form-select" id="editRolUsuario" required>
                                            <option value="administrador" ${usuario.rol === 'administrador' ? 'selected' : ''}>Administrador</option>
                                            <option value="fiscalizador" ${usuario.rol === 'fiscalizador' ? 'selected' : ''}>Fiscalizador</option>
                                            <option value="inspector" ${usuario.rol === 'inspector' ? 'selected' : ''}>Inspector</option>
                                            <option value="ventanilla" ${usuario.rol === 'ventanilla' ? 'selected' : ''}>Ventanilla</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editEstadoUsuario" class="form-label">Estado</label>
                                        <select class="form-select" id="editEstadoUsuario">
                                            <option value="activo" ${usuario.estado === 'activo' ? 'selected' : ''}>Activo</option>
                                            <option value="inactivo" ${usuario.estado === 'inactivo' ? 'selected' : ''}>Inactivo</option>
                                            <option value="pendiente" ${usuario.estado === 'pendiente' ? 'selected' : ''}>Pendiente</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editPasswordUsuario" class="form-label">Nueva Contraseña</label>
                                        <input type="password" class="form-control" id="editPasswordUsuario" 
                                               placeholder="Dejar vacío para mantener la actual">
                                        <small class="text-muted">Solo completar si deseas cambiar la contraseña</small>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-warning" onclick="actualizarUsuario()">
                            <i class="fas fa-save"></i> Actualizar Usuario
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remover modal existente si existe
    const existingModal = document.getElementById('modalEditarUsuario');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Agregar modal al body
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('modalEditarUsuario'));
    modal.show();
}

function actualizarUsuario() {
    const userId = parseInt(document.getElementById('editUserId').value);
    const nombre = document.getElementById('editNombreUsuario').value;
    const username = document.getElementById('editUsernameUsuario').value;
    const email = document.getElementById('editEmailUsuario').value;
    const rol = document.getElementById('editRolUsuario').value;
    const estado = document.getElementById('editEstadoUsuario').value;
    const password = document.getElementById('editPasswordUsuario').value;
    
    // Validaciones básicas
    if (!nombre || !username || !email || !rol) {
        showErrorToast('Por favor completa todos los campos obligatorios');
        return;
    }
    
    if (password && password.length < 8) {
        showErrorToast('La nueva contraseña debe tener al menos 8 caracteres');
        return;
    }
    
    // Mostrar loading
    const btnActualizar = document.querySelector('#modalEditarUsuario .btn-warning');
    const originalText = btnActualizar.innerHTML;
    btnActualizar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualizando...';
    btnActualizar.disabled = true;
    
    // Preparar datos
    const updateData = {
        id: userId,
        nombre: nombre,
        username: username,
        email: email,
        rol: rol,
        estado: estado
    };
    
    // Agregar password solo si se proporcionó
    if (password) {
        updateData.password = password;
    }
    
    // Enviar a la API
    fetch(`${window.location.origin}${window.location.pathname}?api=user`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(updateData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessToast(`Usuario "${nombre}" actualizado exitosamente`, 5000);
            showInfoToast(`Username: ${username} | Email: ${email} | Rol: ${rol} | Estado: ${estado}`, 4000);
            
            // Cerrar modal
            bootstrap.Modal.getInstance(document.getElementById('modalEditarUsuario')).hide();
            
            // Recargar datos desde la base de datos
            cargarDatosUsuarios();
        } else {
            showErrorToast('Error al actualizar usuario: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorToast('Error de conexión al actualizar usuario');
    })
    .finally(() => {
        // Restaurar botón
        btnActualizar.innerHTML = originalText;
        btnActualizar.disabled = false;
    });
}

async function eliminarUsuario(userId, userName) {
    const modalHTML = `
        <div class="modal fade" id="modalEliminarUsuario" tabindex="-1" aria-labelledby="modalEliminarUsuarioLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="modalEliminarUsuarioLabel">
                            <i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                            <i class="fas fa-user-times text-danger" style="font-size: 4rem;"></i>
                            <h5 class="mt-3">¿Estás seguro de eliminar este usuario?</h5>
                            <p class="text-muted">
                                <strong>Usuario:</strong> ${userName}<br>
                                <strong>ID:</strong> ${userId}
                            </p>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>¡Advertencia!</strong> Esta acción no se puede deshacer.
                                Se eliminará toda la información asociada a este usuario.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-danger" onclick="confirmarEliminacionUsuario(${userId}, '${userName}')">
                            <i class="fas fa-trash"></i> Sí, Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remover modal existente si existe
    const existingModal = document.getElementById('modalEliminarUsuario');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Agregar modal al body
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('modalEliminarUsuario'));
    modal.show();
}

function confirmarEliminacionUsuario(userId, userName) {
    // Mostrar loading
    const btnEliminar = document.querySelector('#modalEliminarUsuario .btn-danger');
    const originalText = btnEliminar.innerHTML;
    btnEliminar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Eliminando...';
    btnEliminar.disabled = true;
    
    // Enviar a la API
    fetch(`${window.location.origin}${window.location.pathname}?api=user`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            id: userId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessToast(`Usuario "${userName}" eliminado exitosamente`, 5000);
            showWarningToast(`ID eliminado: ${userId}`, 3000);
            
            // Cerrar modal
            bootstrap.Modal.getInstance(document.getElementById('modalEliminarUsuario')).hide();
            
            // Recargar datos desde la base de datos
            cargarDatosUsuarios();
        } else {
            showErrorToast('Error al eliminar usuario: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorToast('Error de conexión al eliminar usuario');
    })
    .finally(() => {
        // Restaurar botón
        btnEliminar.innerHTML = originalText;
        btnEliminar.disabled = false;
    });
}

function mostrarModalCrearUsuario() {
    const modalHTML = `
        <div class="modal fade" id="modalCrearUsuario" tabindex="-1" aria-labelledby="modalCrearUsuarioLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalCrearUsuarioLabel">
                            <i class="fas fa-user-plus"></i> Crear Nuevo Usuario
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formCrearUsuario">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nombreUsuario" class="form-label">Nombre Completo *</label>
                                        <input type="text" class="form-control" id="nombreUsuario" required 
                                               placeholder="Ej: Juan Pérez González">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="usernameUsuario" class="form-label">Username *</label>
                                        <input type="text" class="form-control" id="usernameUsuario" required 
                                               placeholder="Ej: jperez">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="emailUsuario" class="form-label">Email *</label>
                                        <input type="email" class="form-control" id="emailUsuario" required 
                                               placeholder="usuario@ejemplo.com">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="rolUsuario" class="form-label">Rol *</label>
                                        <select class="form-select" id="rolUsuario" required>
                                            <option value="">Seleccionar rol...</option>
                                            <option value="administrador">Administrador</option>
                                            <option value="fiscalizador">Fiscalizador</option>
                                            <option value="inspector">Inspector</option>
                                            <option value="ventanilla">Ventanilla</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="passwordUsuario" class="form-label">Contraseña *</label>
                                        <input type="password" class="form-control" id="passwordUsuario" required 
                                               placeholder="Mínimo 8 caracteres">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="estadoUsuario" class="form-label">Estado</label>
                                        <select class="form-select" id="estadoUsuario">
                                            <option value="activo" selected>Activo</option>
                                            <option value="inactivo">Inactivo</option>
                                            <option value="pendiente">Pendiente</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="observacionesUsuario" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="observacionesUsuario" rows="3" 
                                          placeholder="Observaciones adicionales (opcional)"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-primary" onclick="guardarNuevoUsuario()">
                            <i class="fas fa-save"></i> Crear Usuario
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Agregar modal al body si no existe
    if (!document.getElementById('modalCrearUsuario')) {
        document.body.insertAdjacentHTML('beforeend', modalHTML);
    }
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('modalCrearUsuario'));
    modal.show();
}

function guardarNuevoUsuario() {
    const nombre = document.getElementById('nombreUsuario').value;
    const username = document.getElementById('usernameUsuario').value;
    const email = document.getElementById('emailUsuario').value;
    const rol = document.getElementById('rolUsuario').value;
    const password = document.getElementById('passwordUsuario').value;
    const estado = document.getElementById('estadoUsuario').value;
    const observaciones = document.getElementById('observacionesUsuario').value;
    
    // Validaciones básicas
    if (!nombre || !username || !email || !rol || !password) {
        showErrorToast('Por favor completa todos los campos obligatorios');
        return;
    }
    
    if (password.length < 8) {
        showErrorToast('La contraseña debe tener al menos 8 caracteres');
        return;
    }
    
    // Mostrar loading
    const btnGuardar = document.querySelector('#modalCrearUsuario .btn-primary');
    const originalText = btnGuardar.innerHTML;
    btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    btnGuardar.disabled = true;
    
    // Enviar a la API
    fetch(`${window.location.origin}${window.location.pathname}?api=users`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            nombre: nombre,
            username: username,
            email: email,
            rol: rol,
            password: password,
            estado: estado,
            observaciones: observaciones
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessToast(`Usuario "${nombre}" creado exitosamente`, 5000);
            showInfoToast(`ID: ${data.user_id} | Username: ${username} | Rol: ${rol}`, 4000);
            
            // Limpiar formulario
            document.getElementById('formCrearUsuario').reset();
            
            // Cerrar modal
            bootstrap.Modal.getInstance(document.getElementById('modalCrearUsuario')).hide();
            
            // Recargar datos desde la base de datos
            cargarDatosUsuarios();
        } else {
            showErrorToast('Error al crear usuario: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorToast('Error de conexión al crear usuario');
    })
    .finally(() => {
        // Restaurar botón
        btnGuardar.innerHTML = originalText;
        btnGuardar.disabled = false;
    });
}

// ==================== FUNCIONES UTILITARIAS ====================
function formatearFecha(fecha) {
    if (!fecha) return 'N/A';
    try {
        return new Date(fecha).toLocaleDateString('es-ES');
    } catch {
        return fecha;
    }
}

// Exportar funciones globalmente
window.loadUsuariosList = loadUsuariosList;
window.loadAprobarUsuarios = loadAprobarUsuarios;
window.cargarDatosUsuarios = cargarDatosUsuarios;
window.cargarUsuariosPendientes = cargarUsuariosPendientes;
window.aprobarUsuario = aprobarUsuario;
window.rechazarUsuario = rechazarUsuario;
window.verDetalleUsuario = verDetalleUsuario;
window.editarUsuario = editarUsuario;
window.eliminarUsuario = eliminarUsuario;
window.mostrarModalCrearUsuario = mostrarModalCrearUsuario;
window.loadDashboardStatsAdmin = loadDashboardStatsAdmin;
window.guardarNuevoUsuario = guardarNuevoUsuario;
window.actualizarUsuario = actualizarUsuario;
window.confirmarEliminacionUsuario = confirmarEliminacionUsuario;
window.showToast = showToast;
window.showSuccessToast = showSuccessToast;
window.showErrorToast = showErrorToast;
window.showWarningToast = showWarningToast;
window.showInfoToast = showInfoToast;

// Debug: Verificar que las funciones están disponibles
console.log(' Verificando funciones exportadas del administrador:');
console.log('- loadUsuariosList:', typeof window.loadUsuariosList);
console.log('- loadAprobarUsuarios:', typeof window.loadAprobarUsuarios);

console.log(' Módulo administrador cargado completamente');