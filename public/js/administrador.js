/**
 * SISTEMA DE GESTIÓN - MÓDULO ADMINISTRADOR
 * Funcionalidades específicas para el rol administrador
 * Última actualización: 2025-10-10 11:20:00
 */

console.log('🚀 Cargando módulo administrador v2.0...');

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
                            <i class="fas fa-user"></i> ${(usuario.name && usuario.name.trim()) ? usuario.name : (usuario.username || 'Usuario')}
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
            // Evitar múltiples envíos y mostrar estado
            try {
                const confirmBtnEl = document.querySelector('.confirm-modal-confirm');
                if (confirmBtnEl) {
                    confirmBtnEl.disabled = true;
                    confirmBtnEl.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Procesando...';
                }

                const fetchWithTimeout = (url, options, timeout = 10000) => {
                    return Promise.race([
                        fetch(url, options),
                        new Promise((_, reject) => setTimeout(() => reject(new Error('Timeout de conexión')), timeout))
                    ]);
                };

                const formData = new FormData();
                formData.append('user_id', userId);

                const urlObj = new URL(window.location.href);
                urlObj.searchParams.set('api', 'approve-user');
                const url = urlObj.toString();

                const response = await fetchWithTimeout(url, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                }, 12000);

                if (!response.ok) {
                    let text = '';
                    try { text = await response.text(); } catch(e) { text = ''; }
                    throw new Error(`Error en servidor: ${response.status} ${response.statusText} ${text ? '- ' + text : ''}`);
                }

                let result;
                try { result = await response.json(); } catch(e) { throw new Error('Respuesta inválida del servidor (no JSON)'); }

                if (result.success) {
                    showToast('Usuario aprobado correctamente', 'success');
                    cargarUsuariosPendientes(); // Recargar lista
                } else {
                    showToast('Error al aprobar usuario: ' + (result.message || 'Error desconocido'), 'error');
                }
            } catch (error) {
                console.error('Error al aprobar usuario:', error);
                showToast('No se pudo conectar con el servidor para aprobar el usuario. (' + error.message + ')', 'error');
            } finally {
                const confirmBtnEl = document.querySelector('.confirm-modal-confirm');
                if (confirmBtnEl) {
                    confirmBtnEl.disabled = false;
                    confirmBtnEl.innerHTML = 'Aprobar';
                }
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
        
        // Prevenir múltiples envíos
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Procesando...';

        // Helper para fetch con timeout
        const fetchWithTimeout = (url, options, timeout = 10000) => {
            return Promise.race([
                fetch(url, options),
                new Promise((_, reject) => setTimeout(() => reject(new Error('Timeout de conexión')), timeout))
            ]);
        };

        try {
            const formData = new FormData();
            formData.append('user_id', userId);
            formData.append('reason', razon);

            // Construir URL robusta usando la API URL para evitar problemas de ruta
            const urlObj = new URL(window.location.href);
            urlObj.searchParams.set('api', 'reject-user');
            const url = urlObj.toString();

            const response = await fetchWithTimeout(url, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            }, 12000);

            if (!response.ok) {
                // Intentar leer texto de error
                let text = '';
                try { text = await response.text(); } catch(e) { text = ''; }
                throw new Error(`Error en servidor: ${response.status} ${response.statusText} ${text ? '- ' + text : ''}`);
            }

            // Intentar parsear JSON, manejo si no es JSON válido
            let result;
            try {
                result = await response.json();
            } catch (parseError) {
                throw new Error('Respuesta inválida del servidor (no JSON)');
            }

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
            // Mensaje más claro para el usuario final
            showToast('No se pudo conectar con el servidor para rechazar el usuario. Verifica tu conexión o intenta más tarde. (' + error.message + ')', 'error');
        } finally {
            // Restaurar estado del botón
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = '<i class="fas fa-user-times me-1"></i>Rechazar Usuario';
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

// Función para formatear fechas en formato corto
function formatDate(fecha) {
    if (!fecha) return 'N/A';
    try {
        const date = new Date(fecha);
        return date.toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    } catch {
        return fecha;
    }
}

// ==================== FUNCIONES GESTIÓN DE ACTAS ====================

function loadActasList() {
    console.log('🔄 Cargando lista completa de actas para administrador...');
    
    document.getElementById('contentContainer').innerHTML = `
        <div class="content-section active">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="card-title">
                                    <i class="fas fa-file-invoice"></i> Lista Completa de Actas
                                </h3>
                                <p class="text-muted mb-0">Supervisión de todas las actas del sistema</p>
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-success" onclick="exportarActasExcel()">
                                    <i class="fas fa-file-excel"></i> Exportar Excel
                                </button>
                                <button class="btn btn-primary" onclick="loadCrearActa()">
                                    <i class="fas fa-plus"></i> Nueva Acta
                                </button>
                            </div>
                        </div>
                        
                        <!-- Estadísticas rápidas -->
                        <div class="card-body border-bottom">
                            <div class="row text-center">
                                <div class="col-md-2">
                                    <div class="info-box bg-info">
                                        <span class="info-box-icon"><i class="fas fa-file-alt"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Actas</span>
                                            <span class="info-box-number" id="totalActasAdmin">0</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="info-box bg-warning">
                                        <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Pendientes</span>
                                            <span class="info-box-number" id="actasPendientes">0</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="info-box bg-primary">
                                        <span class="info-box-icon"><i class="fas fa-cogs"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Procesadas</span>
                                            <span class="info-box-number" id="actasProcesadas">0</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="info-box bg-success">
                                        <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Aprobadas</span>
                                            <span class="info-box-number" id="actasAprobadas">0</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="info-box bg-secondary">
                                        <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Fiscalizadores</span>
                                            <span class="info-box-number" id="totalFiscalizadores">0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tabla de actas -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Informe</th>
                                            <th>Conductor</th>
                                            <th>Licencia</th>
                                            <th>Estado</th>
                                            <th>Fecha</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="actas-admin-list">
                                        <tr>
                                            <td colspan="7" class="text-center">
                                                <div class="spinner-border" role="status">
                                                    <span class="sr-only">Cargando actas...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Cargar datos
    cargarActasAdmin();
}

function loadCrearActa() {
    console.log('🔄 Cargando formulario crear acta para administrador (usando formulario de fiscalizador)...');
    
    // Usar el mismo modal que el fiscalizador
    showCrearActaModal();
}

async function cargarActasAdmin() {
    try {
        console.log('📊 Cargando datos completos de actas para administrador...');
        console.log('🌐 URL API:', 'dashboard.php?api=actas-admin');
        
        const response = await fetch('dashboard.php?api=actas-admin', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        });
        console.log('📡 Respuesta del servidor:', response.status, response.statusText);
        
        const data = await response.json();
        console.log('📦 Datos recibidos:', data);
        
        if (data.success) {
            const actas = data.actas || [];
            const stats = data.stats || {};
            
            console.log('✅ Actas encontradas:', actas.length);
            console.log('📊 Estadísticas:', stats);
            
            // Actualizar estadísticas
            const totalActasEl = document.getElementById('totalActasAdmin');
            if (totalActasEl) totalActasEl.textContent = stats.total_actas || 0;
            
            const actasPendientesEl = document.getElementById('actasPendientes');
            if (actasPendientesEl) actasPendientesEl.textContent = '0';
            
            const actasProcesadasEl = document.getElementById('actasProcesadas');
            if (actasProcesadasEl) actasProcesadasEl.textContent = '0';
            
            const actasAprobadasEl = document.getElementById('actasAprobadas');
            if (actasAprobadasEl) actasAprobadasEl.textContent = '0';
            
            // Poblar tabla
            const tbody = document.getElementById('actas-admin-list');
            if (tbody) {
                if (actas.length > 0) {
                    tbody.innerHTML = actas.map(acta => `
                        <tr>
                            <td><strong>#${acta.id || 'N/A'}</strong></td>
                            <td><span class="badge bg-info">${acta.numero_acta || 'N/A'}</span></td>
                            <td>${acta.nombre_conductor || 'N/A'}</td>
                            <td><small class="text-muted">${acta.ruc_dni || 'N/A'}</small></td>
                            <td><span class="badge ${getEstadoBadgeClass(acta.estado)}">${acta.estado || 'pendiente'}</span></td>
                            <td><small class="text-muted">${formatDate(acta.fecha_acta || acta.created_at)}</small></td>
                            <td>
                                <button class="btn btn-sm btn-info me-1" onclick="verDetalleActa(${acta.id})" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="eliminarActaAdmin(${acta.id})" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center">No hay actas registradas en el sistema</td></tr>';
                }
            }
            
        } else {
            console.error('❌ Error en la API:', data.message);
            showErrorToast('Error al cargar actas: ' + (data.message || 'Error desconocido'));
            const tbody = document.getElementById('actas-admin-list');
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error: ' + (data.message || 'Error desconocido') + '</td></tr>';
        }
    } catch (error) {
        console.error('💥 Error completo al cargar actas de administrador:', error);
        console.error('💥 Detalles del error:', error.message);
        showErrorToast('Error al cargar las actas: ' + error.message);
        const tbody = document.getElementById('actas-admin-list');
        if (tbody) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error de conexión: ' + error.message + '</td></tr>';
        }
    }
}

function getEstadoBadgeClassActa(estado) {
    switch(estado) {
        case 'aprobado': return 'success';
        case 'procesado': return 'primary';
        case 'pendiente': return 'warning';
        case 'rechazado': return 'danger';
        default: return 'secondary';
    }
}

// Función para obtener la clase del badge según el estado del acta
function getEstadoBadgeClass(estado) {
    const estadoLower = (estado || '').toLowerCase().trim();
    switch(estadoLower) {
        case 'pendiente': return 'bg-warning text-dark';
        case 'procesada': 
        case 'procesado': return 'bg-primary';
        case 'aprobada':
        case 'aprobado': return 'bg-success';
        case 'anulada':
        case 'anulado': return 'bg-danger';
        case 'pagada':
        case 'pagado': return 'bg-info';
        default: return 'bg-secondary';
    }
}

// Funciones de acciones administrativas
function verDetalleActaAdmin(id) {
    showInfoToast('Función ver detalle de acta en desarrollo');
}

function duplicarActaAdmin(id) {
    showInfoToast('Función duplicar acta en desarrollo');
}

function editarActaAdmin(id) {
    showInfoToast('Función editar acta en desarrollo');
}

function aprobarActaAdmin(id) {
    showInfoToast('Función aprobar acta en desarrollo');
}

function rechazarActaAdmin(id) {
    showInfoToast('Función rechazar acta en desarrollo');
}

function limpiarFiltrosActasAdmin() {
    document.getElementById('buscarActaAdmin').value = '';
    document.getElementById('filtroEstadoAdmin').value = '';
    document.getElementById('filtroFiscalizadorAdmin').value = '';
    document.getElementById('fechaDesdeAdmin').value = '';
    cargarActasAdmin();
}

async function eliminarActaAdmin(actaId) {
    showConfirmModal({
        title: 'Eliminar Acta',
        message: '¿Estás seguro de que deseas eliminar esta acta? Esta acción no se puede deshacer.',
        type: 'danger',
        confirmText: 'Sí, Eliminar',
        cancelText: 'Cancelar',
        onConfirm: async () => {
            try {
                const response = await fetch(`${window.location.origin}${window.location.pathname}?api=eliminar_acta`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: actaId }),
                    credentials: 'same-origin'
                });
                
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    showSuccessToast('Acta eliminada correctamente: ' + (data.acta_numero || ''));
                    // Recargar la lista de actas
                    cargarActasAdmin();
                } else {
                    showErrorToast('Error al eliminar: ' + (data.message || 'Error desconocido'));
                }
            } catch (error) {
                console.error('Error al eliminar acta:', error);
                showErrorToast('Error de conexión al eliminar acta: ' + error.message);
            }
        }
    });
}

function exportarActasExcel() {
    showInfoToast('Preparando exportación a Excel...', 'info');
    
    // Obtener las actas actuales desde la tabla
    const tbody = document.getElementById('actas-admin-list');
    if (!tbody) {
        showErrorToast('No se encontró la tabla de actas');
        return;
    }
    
    // Obtener todas las filas de la tabla
    const rows = tbody.querySelectorAll('tr');
    if (rows.length === 0 || (rows.length === 1 && rows[0].cells.length === 1)) {
        showErrorToast('No hay actas para exportar');
        return;
    }
    
    // Crear el contenido CSV
    let csvContent = 'ID,Informe,Conductor,Licencia,Estado,Fecha\n';
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length > 1) {
            const rowData = [
                cells[0].textContent.trim(), // ID
                cells[1].textContent.trim(), // Informe
                cells[2].textContent.trim(), // Conductor
                cells[3].textContent.trim(), // Licencia
                cells[4].textContent.trim(), // Estado
                cells[5].textContent.trim()  // Fecha
            ];
            csvContent += rowData.map(cell => `"${cell}"`).join(',') + '\n';
        }
    });
    
    // Crear el archivo y descargarlo
    const blob = new Blob(['\ufeff' + csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', `actas_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showSuccessToast('Archivo Excel exportado correctamente');
}

function loadGestionarInfracciones() {
    console.log('🔄 Cargando gestión de infracciones para administrador...');
    
    const contentContainer = document.getElementById('contentContainer');
    if (contentContainer) {
        contentContainer.innerHTML = `
            <div class="content-section active">
                <div class="content-header d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4><i class="fas fa-exclamation-triangle"></i> Gestión de Infracciones</h4>
                        <p class="text-muted mb-0">Administración de tipos de infracciones y sanciones</p>
                    </div>
                    <button class="btn btn-primary" onclick="mostrarModalNuevaInfraccion()">
                        <i class="fas fa-plus"></i> Nueva Infracción
                    </button>
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-list"></i> Infracciones Registradas</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Código</th>
                                                <th>Descripción</th>
                                                <th>Gravedad</th>
                                                <th>Monto Base</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="infraccionesTableBody">
                                            <tr>
                                                <td colspan="6" class="text-center">
                                                    <div class="spinner-border text-primary" role="status">
                                                        <span class="visually-hidden">Cargando...</span>
                                                    </div>
                                                    <p class="mt-2">Cargando infracciones...</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-chart-pie"></i> Estadísticas</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Total Infracciones:</span>
                                    <span class="badge bg-primary" id="totalInfracciones">0</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Activas:</span>
                                    <span class="badge bg-success" id="infraccionesActivas">0</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Inactivas:</span>
                                    <span class="badge bg-secondary" id="infraccionesInactivas">0</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5><i class="fas fa-info-circle"></i> Información</h5>
                            </div>
                            <div class="card-body">
                                <p class="small text-muted">
                                    Las infracciones son la base para la generación de actas. 
                                    Cada infracción tiene un código único y monto base establecido.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Cargar datos de infracciones
        cargarInfraccionesAdmin();
    }
}

// Funciones auxiliares para gestión de actas - función movida arriba para evitar duplicados

async function cargarInfraccionesAdmin() {
    try {
        const response = await fetch('dashboard.php?api=infracciones', {
            method: 'GET',
            credentials: 'same-origin'
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
            mostrarInfraccionesEnTabla(data.infracciones);
            actualizarEstadisticasInfracciones(data.infracciones);
        } else {
            showErrorToast('Error al cargar infracciones: ' + (data.message || 'Error desconocido'));
        }
    } catch (error) {
        console.error('Error al cargar infracciones:', error);
        showErrorToast('Error de conexión al cargar infracciones');
    }
}

function mostrarInfraccionesEnTabla(infracciones) {
    const tbody = document.getElementById('infraccionesTableBody');
    if (!tbody) return;
    
    if (!infracciones || infracciones.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center">
                    <i class="fas fa-inbox text-muted"></i>
                    <p class="mt-2 text-muted">No hay infracciones registradas</p>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = infracciones.map(infraccion => {
        const gravedadClase = {
            'leve': 'success',
            'grave': 'warning', 
            'muy_grave': 'danger'
        }[infraccion.gravedad] || 'secondary';
        
        return `
            <tr>
                <td><strong>${infraccion.codigo_infraccion}</strong></td>
                <td>${infraccion.descripcion}</td>
                <td><span class="badge bg-${gravedadClase}">${infraccion.gravedad || 'N/A'}</span></td>
                <td>S/ ${parseFloat(infraccion.monto_base || 0).toFixed(2)}</td>
                <td><span class="badge bg-success">Activa</span></td>
                <td>
                    <button class="btn btn-sm btn-outline-warning me-1" onclick="editarInfraccion(${infraccion.id})" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="eliminarInfraccion(${infraccion.id})" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

function actualizarEstadisticasInfracciones(infracciones) {
    const total = infracciones.length;
    const activas = infracciones.filter(i => i.estado === 'activa').length;
    const inactivas = total - activas;
    
    document.getElementById('totalInfracciones').textContent = total;
    document.getElementById('infraccionesActivas').textContent = activas;
    document.getElementById('infraccionesInactivas').textContent = inactivas;
}

// ==================== GESTIÓN DE CARGA Y PASAJEROS ====================

function loadCargaPasajerosList() {
    document.getElementById('contentContainer').innerHTML = `
        <div class="content-section active">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="card-title mb-0">
                                    <i class="fas fa-truck"></i> Lista de Registros de Carga y Pasajeros
                                </h3>
                                <p class="text-muted mb-0">Gestión de registros de transporte</p>
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-success" onclick="exportarCargaPasajerosExcel()">
                                    <i class="fas fa-file-excel"></i> Exportar
                                </button>
                                <button class="btn btn-primary" onclick="loadCrearCargaPasajero()">
                                    <i class="fas fa-plus"></i> Nuevo Registro
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Información:</strong> Los registros se ordenan por fecha de creación (más recientes primero). 
                                La fecha se asigna automáticamente al crear cada registro.
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Tipo</th>
                                            <th>Descripción</th>
                                            <th>Peso/Cantidad</th>
                                            <th>Origen</th>
                                            <th>Destino</th>
                                            <th>Fecha Creación</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="carga-pasajeros-list">
                                        <tr>
                                            <td colspan="9" class="text-center py-4">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Cargando...</span>
                                                </div>
                                                <p class="mt-2 text-muted">Cargando registros...</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Cargar datos después de un breve delay
    setTimeout(() => {
        cargarDatosCargaPasajeros();
    }, 500);
}

function loadCrearCargaPasajero() {
    document.getElementById('contentContainer').innerHTML = `
        <div class="content-section active">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">
                                <i class="fas fa-plus"></i> Nuevo Registro de Carga/Pasajero
                            </h3>
                            <button class="btn btn-secondary" onclick="loadCargaPasajerosList()">
                                <i class="fas fa-arrow-left"></i> Volver
                            </button>
                        </div>
                        <div class="card-body">

                            
                            <form id="crear-carga-form">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="tipo" class="form-label">Tipo *</label>
                                            <select class="form-select" id="tipo" required>
                                                <option value="">Seleccionar tipo</option>
                                                <option value="carga">Carga</option>
                                                <option value="pasajero">Pasajero</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="descripcion" class="form-label">Descripción *</label>
                                            <input type="text" class="form-control" id="descripcion" required 
                                                   placeholder="Ej: Transporte de mercancías varias">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="peso_cantidad" class="form-label">Peso/Cantidad</label>
                                            <input type="text" class="form-control" id="peso_cantidad" 
                                                   placeholder="Ej: 2.5 toneladas o 45 pasajeros">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="origen" class="form-label">Origen *</label>
                                            <input type="text" class="form-control" id="origen" required 
                                                   placeholder="Ej: Lima">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="destino" class="form-label">Destino *</label>
                                            <input type="text" class="form-control" id="destino" required 
                                                   placeholder="Ej: Abancay">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="estado" class="form-label">Estado</label>
                                            <select class="form-select" id="estado">
                                                <option value="pendiente" selected>Pendiente</option>
                                                <option value="en_transito">En Tránsito</option>
                                                <option value="entregado">Entregado</option>
                                                <option value="cancelado">Cancelado</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="observaciones" class="form-label">Observaciones</label>
                                            <textarea class="form-control" id="observaciones" rows="2" 
                                                      placeholder="Observaciones adicionales (opcional)"></textarea>
                                        </div>
                                    </div>
                                </div>
                                

                                
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-secondary" onclick="loadCargaPasajerosList()">
                                        <i class="fas fa-times"></i> Cancelar
                                    </button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save"></i> Guardar Registro
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Configurar el evento de envío del formulario
    setTimeout(() => {
        const form = document.getElementById('crear-carga-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                guardarNuevoCargaPasajero();
            });
        }
    }, 100);
}

function loadEstadisticasCarga() {
    document.getElementById('contentContainer').innerHTML = `
        <div class="content-wrapper">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-bar"></i> Estadísticas de Carga y Pasajeros
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-3 col-6">
                                    <div class="small-box bg-info">
                                        <div class="inner">
                                            <h3 id="totalRegistros">0</h3>
                                            <p>Total Registros</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-truck"></i>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-lg-3 col-6">
                                    <div class="small-box bg-success">
                                        <div class="inner">
                                            <h3 id="registrosCarga">0</h3>
                                            <p>Registros de Carga</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-boxes"></i>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-lg-3 col-6">
                                    <div class="small-box bg-warning">
                                        <div class="inner">
                                            <h3 id="registrosPasajeros">0</h3>
                                            <p>Registros de Pasajeros</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-users"></i>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-lg-3 col-6">
                                    <div class="small-box bg-danger">
                                        <div class="inner">
                                            <h3 id="registrosTransito">0</h3>
                                            <p>En Tránsito</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-route"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4>Distribución por Estado</h4>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="estadosChart" width="400" height="200"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    cargarEstadisticasCarga();
}

// --- Sección actualizada: normalización de estados y badges ---
// Normaliza estados y calcula estadísticas
async function cargarEstadisticasCarga() {
  try {
    const response = await fetch('dashboard.php?api=carga-pasajeros', {
      method: 'GET',
      headers: { 'Accept': 'application/json' },
      credentials: 'same-origin'
    });
    const data = await response.json();

    if (data.success && data.carga_pasajeros) {
      const registros = data.carga_pasajeros;

      // Normalizar una sola vez cada estado por registro
      const registrosNormalizados = registros.map(r => ({
        ...r,
        estado_norm: (r.estado || '').toLowerCase().trim()
      }));

      const total = registrosNormalizados.length;
      const pendientes = registrosNormalizados.filter(r => r.estado_norm === 'pendiente').length;
      const procesados = registrosNormalizados.filter(r => ['procesado', 'procesada'].includes(r.estado_norm)).length;
      const aprobados = registrosNormalizados.filter(r => ['aprobado', 'aprobada'].includes(r.estado_norm)).length;

      if (document.getElementById('totalRegistros')) document.getElementById('totalRegistros').textContent = total;
      if (document.getElementById('registrosPendientes')) document.getElementById('registrosPendientes').textContent = pendientes;
      if (document.getElementById('registrosProcesados')) document.getElementById('registrosProcesados').textContent = procesados;
      if (document.getElementById('registrosAprobados')) document.getElementById('registrosAprobados').textContent = aprobados;
    }
  } catch (error) {
    console.error('Error al cargar estadísticas:', error);
  }
}

// Función que devuelve la clase del badge (devuelve la parte final: 'warning','info','success','secondary')
function getEstadoBadgeClass(estadoRaw) {
  const estado = (estadoRaw || '').toLowerCase().trim();

  if (estado === 'pendiente') return 'warning';
  if (estado === 'procesado' || estado === 'procesada') return 'info';
  if (estado === 'aprobado' || estado === 'aprobada') return 'success';
  if (estado === 'en_transito' || estado === 'en tránsito' || estado === 'en-transito') return 'secondary';

  // fallback
  return 'secondary';
}

// Renderizado/lectura de la lista (reemplaza la función antigua cargarDatosCargaPasajeros si existe)
async function cargarDatosCargaPasajeros() {
  try {
    const response = await fetch('dashboard.php?api=carga-pasajeros', {
      method: 'GET',
      headers: { 'Accept': 'application/json' },
      credentials: 'same-origin'
    });
    const data = await response.json();

    if (data.success && data.carga_pasajeros && data.carga_pasajeros.length > 0) {
      const registros = data.carga_pasajeros;
      const tbody = document.getElementById('carga-pasajeros-list');
      if (!tbody) return;

      tbody.innerHTML = registros.map((registro, index) => {
        const fechaFormateada = registro.fecha ? new Date(registro.fecha).toLocaleDateString('es-ES') : 
                                (registro.created_at ? new Date(registro.created_at).toLocaleDateString('es-ES') : 'N/A');
        
        return `
          <tr>
            <td><strong>${String(index + 1).padStart(2, '0')}</strong></td>
            <td><span class="badge ${registro.tipo === 'carga' ? 'bg-primary' : 'bg-info'}">${registro.tipo || 'N/A'}</span></td>
            <td>${registro.descripcion || 'N/A'}</td>
            <td><small class="text-muted">${registro.peso_cantidad || 'N/A'}</small></td>
            <td>${registro.origen || 'N/A'}</td>
            <td>${registro.destino || 'N/A'}</td>
            <td><small class="text-muted">${fechaFormateada}</small></td>
            <td><span class="badge bg-${getEstadoBadgeClass(registro.estado || 'pendiente')}">${registro.estado || 'Pendiente'}</span></td>
            <td>
              <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-info" onclick="verDetalleCarga('${btoa(registro.id)}')" title="Ver detalle">
                  <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-outline-warning" onclick="editarCarga('${btoa(registro.id)}')" title="Editar">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-outline-danger" onclick="eliminarCarga('${btoa(registro.id)}')" title="Eliminar">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        `;
      }).join('');
    } else {
      const tbody = document.getElementById('carga-pasajeros-list');
      if (tbody) {
        tbody.innerHTML = `
          <tr>
            <td colspan="9" class="text-center py-4">
              <i class="fas fa-inbox text-muted" style="font-size: 3rem;"></i>
              <p class="mt-2 text-muted">No hay registros disponibles</p>
              <button class="btn btn-primary" onclick="loadCrearCargaPasajero()">
                <i class="fas fa-plus"></i> Crear Primer Registro
              </button>
            </td>
          </tr>
        `;
      }
    }
  } catch (error) {
    console.error('Error al cargar datos de carga/pasajeros:', error);
    const tbody = document.getElementById('carga-pasajeros-list');
    if (tbody) {
      tbody.innerHTML = `
        <tr>
          <td colspan="9" class="text-center py-4 text-danger">
            <i class="fas fa-exclamation-triangle" style="font-size: 3rem;"></i>
            <p class="mt-2">Error al cargar los datos</p>
            <button class="btn btn-outline-primary" onclick="cargarDatosCargaPasajeros()">
              <i class="fas fa-refresh"></i> Reintentar
            </button>
          </td>
        </tr>
      `;
    }
  }
}

function verDetalleCarga(encodedId) {
    try {
        const id = atob(encodedId);
        showInfoToast('Cargando detalles del registro...');
        // Aquí puedes agregar la lógica para mostrar el detalle
        console.log('Ver detalle del registro ID:', id); // Solo para debug
    } catch (error) {
        showErrorToast('Error al procesar la solicitud');
    }
}

function editarCarga(encodedId) {
    try {
        const id = atob(encodedId);
        showInfoToast('Cargando formulario de edición...');
        // Aquí puedes agregar la lógica para editar
        console.log('Editar registro ID:', id); // Solo para debug
    } catch (error) {
        showErrorToast('Error al procesar la solicitud');
    }
}

async function eliminarCarga(encodedId) {
    try {
        const id = atob(encodedId);
        
        showConfirmModal({
            title: 'Eliminar Registro',
            message: '¿Estás seguro de que deseas eliminar este registro? Esta acción no se puede deshacer.',
            type: 'danger',
            confirmText: 'Sí, Eliminar',
            cancelText: 'Cancelar',
            onConfirm: async () => {
                try {
                    const response = await fetch(`${window.location.origin}${window.location.pathname}?api=eliminar-carga-pasajero`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id: id }),
                        credentials: 'same-origin'
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        showSuccessToast('Registro eliminado correctamente');
                        // Recargar la lista
                        cargarDatosCargaPasajeros();
                    } else {
                        showErrorToast('Error al eliminar: ' + (data.message || 'Error desconocido'));
                    }
                } catch (error) {
                    console.error('Error al eliminar registro:', error);
                    showErrorToast('Error de conexión al eliminar registro');
                }
            }
        });
    } catch (error) {
        showErrorToast('Error al procesar la solicitud');
    }
}

// Función para exportar a Excel
function exportarCargaPasajerosExcel() {
    showInfoToast('Preparando exportación a Excel...', 'info');
    
    const tbody = document.getElementById('carga-pasajeros-list');
    if (!tbody) {
        showErrorToast('No se encontró la tabla de registros');
        return;
    }
    
    const rows = tbody.querySelectorAll('tr');
    if (rows.length === 0 || (rows.length === 1 && rows[0].cells.length === 1)) {
        showErrorToast('No hay registros para exportar');
        return;
    }
    
    let csvContent = 'Número,Tipo,Descripción,Peso/Cantidad,Origen,Destino,Fecha,Estado\n';
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length > 1) {
            const rowData = [
                cells[0].textContent.trim(),
                cells[1].textContent.trim(),
                cells[2].textContent.trim(),
                cells[3].textContent.trim(),
                cells[4].textContent.trim(),
                cells[5].textContent.trim(),
                cells[6].textContent.trim(),
                cells[7].textContent.trim()
            ];
            csvContent += rowData.map(cell => `"${cell}"`).join(',') + '\n';
        }
    });
    
    const blob = new Blob(['\ufeff' + csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', `carga_pasajeros_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showSuccessToast('Archivo Excel exportado correctamente');
}

// Función para guardar nuevo registro de carga/pasajero
async function guardarNuevoCargaPasajero() {
    const tipo = document.getElementById('tipo').value;
    const descripcion = document.getElementById('descripcion').value;
    const peso_cantidad = document.getElementById('peso_cantidad').value;
    const origen = document.getElementById('origen').value;
    const destino = document.getElementById('destino').value;
    const estado = document.getElementById('estado').value;
    const observaciones = document.getElementById('observaciones').value;
    
    // Validaciones
    if (!tipo || !descripcion || !origen || !destino) {
        showErrorToast('Por favor completa todos los campos obligatorios');
        return;
    }
    
    // Mostrar loading
    const btnGuardar = document.querySelector('#crear-carga-form button[type="submit"]');
    const originalText = btnGuardar.innerHTML;
    btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    btnGuardar.disabled = true;
    
    try {
        const response = await fetch(`${window.location.origin}${window.location.pathname}?api=carga-pasajeros`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                tipo: tipo,
                descripcion: descripcion,
                peso_cantidad: peso_cantidad,
                origen: origen,
                destino: destino,
                estado: estado,
                observaciones: observaciones
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showSuccessToast(`Registro de ${tipo} creado exitosamente`);
            showInfoToast(`ID: ${data.id} | ${descripcion} | ${origen} → ${destino}`);
            
            // Limpiar formulario
            document.getElementById('crear-carga-form').reset();
            
            // Volver a la lista después de 2 segundos
            setTimeout(() => {
                loadCargaPasajerosList();
            }, 2000);
        } else {
            showErrorToast('Error al crear registro: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        showErrorToast('Error de conexión al crear registro');
    } finally {
        // Restaurar botón
        btnGuardar.innerHTML = originalText;
        btnGuardar.disabled = false;
    }
}

// Exportar funciones globalmente
window.loadUsuariosList = loadUsuariosList;
window.loadAprobarUsuarios = loadAprobarUsuarios;
window.loadActasList = loadActasList;
window.loadCrearActa = loadCrearActa;
window.loadGestionarInfracciones = loadGestionarInfracciones;
window.loadCargaPasajerosList = loadCargaPasajerosList;
window.loadCrearCargaPasajero = loadCrearCargaPasajero;
window.loadEstadisticasCarga = loadEstadisticasCarga;
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
window.guardarNuevoCargaPasajero = guardarNuevoCargaPasajero;
window.cargarDatosCargaPasajeros = cargarDatosCargaPasajeros;
window.verDetalleCarga = verDetalleCarga;
window.editarCarga = editarCarga;
window.eliminarCarga = eliminarCarga;
window.exportarCargaPasajerosExcel = exportarCargaPasajerosExcel;
window.showToast = showToast;
window.showSuccessToast = showSuccessToast;
window.showErrorToast = showErrorToast;
window.showWarningToast = showWarningToast;
window.showInfoToast = showInfoToast;
window.getEstadoBadgeClass = getEstadoBadgeClass;
window.formatDate = formatDate;
window.formatearFecha = formatearFecha;

// Debug: Verificar que las funciones están disponibles
console.log('🔍 Verificando funciones exportadas del administrador:');
console.log('- loadUsuariosList:', typeof window.loadUsuariosList);
console.log('- loadAprobarUsuarios:', typeof window.loadAprobarUsuarios);
console.log('- loadCargaPasajerosList:', typeof window.loadCargaPasajerosList);
console.log('- loadCrearCargaPasajero:', typeof window.loadCrearCargaPasajero);
console.log('- loadEstadisticasCarga:', typeof window.loadEstadisticasCarga);





console.log(' Módulo administrador cargado completamente');

// ================================
// FUNCIONES DEL FORMULARIO DE ACTAS (COPIADAS DE FISCALIZADOR)
// ================================

// Función para mostrar el modal de crear acta (igual que fiscalizador)
function showCrearActaModal() {
    console.log('🆕 Abriendo modal para crear nueva acta...');
    
    const modalTitle = document.getElementById('modalTitle');
    const modalBody = document.getElementById('modalBody');
    const modalFooter = document.getElementById('modalFooter');
    
    if (!modalTitle || !modalBody || !modalFooter) {
        console.error('❌ Elementos del modal no encontrados');
        return;
    }
    
    // Configurar título del modal
    modalTitle.innerHTML = `
        <i class="fas fa-plus-circle me-2"></i> Crear Nueva Acta
    `;
    
    // Configurar contenido del modal (mismo formulario que fiscalizador)
    modalBody.innerHTML = `
        <style>
            #generalModal .modal-dialog { max-width: 1400px; margin: 1rem auto; width: 95%; }
            #generalModal .modal-content { min-height: 600px; }
            @media (min-width: 1200px) {
                #generalModal .modal-dialog { max-width: 1300px; }
            }
        </style>
        <form id="formCrearActa" class="row g-4">
            
            <!-- Datos del Operador/Empresa -->
            <div class="col-12">
                <h6 class="text-primary border-bottom pb-2">
                    <i class="fas fa-building"></i> Datos del Operador/Empresa
                </h6>
            </div>
            
            <div class="col-md-6">
                <label class="form-label">RUC/DNI *</label>
                <input type="text" class="form-control" name="ruc_dni" id="ruc_dni" required 
                       placeholder="11 dígitos para RUC, 8 para DNI">
            </div>
            
            <div class="col-md-6">
                <label class="form-label">Razón Social / Nombre</label>
                <input type="text" class="form-control" name="razon_social" id="razon_social" 
                       placeholder="Nombre o razón social">
            </div>
            
            <!-- Datos del Vehículo -->
            <div class="col-12 mt-4">
                <h6 class="text-warning border-bottom pb-2">
                    <i class="fas fa-car"></i> Datos del Vehículo
                </h6>
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Placa del Vehículo *</label>
                <input type="text" class="form-control" name="placa" id="placa" required 
                       placeholder="ABC-123" style="text-transform: uppercase;">
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Tipo de Agente *</label>
                <select class="form-select" name="tipo_agente" id="tipo_agente" required>
                    <option value="">Seleccione...</option>
                    <option value="Transportista">Transportista</option>
                    <option value="Operador de Ruta">Operador de Ruta</option>
                    <option value="Conductor" selected>Conductor</option>
                    <option value="Inspector">Inspector</option>
                </select>
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Tipo de Servicio *</label>
                <select class="form-select" name="tipo_servicio" id="tipo_servicio" required>
                    <option value="">Seleccione...</option>
                    <option value="Interprovincial">Interprovincial</option>
                    <option value="Interdistrital">Interdistrital</option>
                    <option value="Urbano">Urbano</option>
                    <option value="Turístico">Turístico</option>
                    <option value="Carga">Transporte de Carga</option>
                    <option value="Especial">Servicio Especial</option>
                </select>
            </div>
            
            <!-- Datos del Conductor -->
            <div class="col-12 mt-4">
                <h6 class="text-success border-bottom pb-2">
                    <i class="fas fa-user"></i> Datos del Conductor
                </h6>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Apellidos del Conductor *</label>
                <input type="text" class="form-control" name="apellidos_conductor" id="apellidos_conductor" required
                       placeholder="Ej: García López">
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Nombres del Conductor *</label>
                <input type="text" class="form-control" name="nombres_conductor" id="nombres_conductor" required
                       placeholder="Ej: Juan Carlos">
            </div>
            
            <div class="col-md-4">
                <label class="form-label">N° Licencia</label>
                <input type="text" class="form-control" name="licencia_conductor" id="licencia_conductor" 
                       placeholder="Ej: A12345678">
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Provincia *</label>
                <select class="form-select" name="provincia" id="provincia" required onchange="cargarDistritos()">
                    <option value="">Seleccione provincia...</option>
                    <option value="Abancay">Abancay</option>
                    <option value="Andahuaylas">Andahuaylas</option>
                    <option value="Antabamba">Antabamba</option>
                    <option value="Aymaraes">Aymaraes</option>
                    <option value="Cotabambas">Cotabambas</option>
                    <option value="Chincheros">Chincheros</option>
                    <option value="Grau">Grau</option>
                </select>
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Distrito *</label>
                <select class="form-select" name="distrito" id="distrito" required>
                    <option value="">Primero seleccione provincia</option>
                </select>
            </div>
            
            <!-- Datos de la Intervención -->
            <div class="col-12 mt-4">
                <h6 class="text-danger border-bottom pb-2">
                    <i class="fas fa-map-marker-alt"></i> Datos de la Intervención
                </h6>
            </div>

            <div class="col-md-12">
                <label class="form-label">Lugar de Intervención *</label>
                <input type="text" class="form-control" name="lugar_intervencion" id="lugar_intervencion" required
                       placeholder="Ej: Av. Núñez - Abancay, Apurímac">
            </div>
            
            <!-- Campos ocultos automáticos -->
            <input type="hidden" name="fecha_intervencion" id="fecha_intervencion" 
                   value="${new Date().toISOString().split('T')[0]}">
            <input type="hidden" name="hora_intervencion" id="hora_intervencion">
            <input type="hidden" name="inspector_responsable" id="inspector_responsable" 
                   value="${window.dashboardUserName || ''}">
            
            <!-- Código de Infracción -->
            <div class="col-12 mt-4">
                <h6 class="text-danger border-bottom pb-2">
                    <i class="fas fa-gavel"></i> Infracción Detectada
                </h6>
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Código Base *</label>
                <select class="form-select" name="codigo_base" id="codigo_base" required>
                    <option value="">Seleccione código...</option>
                </select>
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Subcategoría *</label>
                <select class="form-select" name="subcategoria" id="subcategoria" required disabled>
                    <option value="">Primero seleccione código</option>
                </select>
                <input type="hidden" name="codigo_infraccion" id="codigo_infraccion">
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Gravedad</label>
                <div class="mt-2">
                    <span id="badge_gravedad" class="badge bg-secondary">Sin seleccionar</span>
                </div>
            </div>
            
            <div class="col-12">
                <label class="form-label">Descripción de la Infracción</label>
                <textarea class="form-control" name="descripcion_infraccion" id="descripcion_infraccion" 
                          rows="3" readonly style="background-color: #f8f9fa;"></textarea>
            </div>
        </form>
    `;
    
    // Configurar botones del modal
    modalFooter.innerHTML = `
        <style>
            #botonesAccion { display: none !important; }
            #botonesAccion.activo { display: flex !important; }
        </style>
        <div class="d-flex justify-content-center align-items-center w-100 flex-column gap-3">
            <div id="botonesAccion" class="d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-success" onclick="exportarActaActual('excel')">
                    <i class="fas fa-file-excel"></i> Exportar Excel
                </button>
                <button type="button" class="btn btn-info" onclick="exportarActaActual('pdf')">
                    <i class="fas fa-file-pdf"></i> Exportar PDF
                </button>
                <button type="button" class="btn btn-primary" onclick="guardarNuevaActa()">
                    <i class="fas fa-save"></i> Guardar Acta
                </button>
            </div>
            <small id="estadoValidacion" class="text-warning text-center fw-bold">
                <i class="fas fa-exclamation-circle"></i> Complete los 8 campos obligatorios para ver las opciones
            </small>
        </div>
    `;
    
    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('generalModal'));
    modal.show();

    // Agregar event listener para limpiar backdrop cuando se cierre el modal
    document.getElementById('generalModal').addEventListener('hidden.bs.modal', function() {
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(backdrop => backdrop.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    });

    // Configurar funcionalidades del formulario
    setTimeout(() => {
        configurarTimestampAutomatico();
        configurarValidacionDinamica();
        cargarCodigosInfracciones();
        console.log('🎯 Modal de acta configurado completamente');
    }, 500);
}

// Funciones de soporte copiadas del fiscalizador
function configurarTimestampAutomatico() {
    const horaIntervencionInput = document.getElementById('hora_intervencion');
    const ahora = new Date();
    const horaFormateada = ahora.toTimeString().slice(0,5);
    if (horaIntervencionInput) {
        horaIntervencionInput.value = horaFormateada;
    }
}

function cargarDistritos() {
    const provinciaSelect = document.getElementById('provincia');
    const distritoSelect = document.getElementById('distrito');
    
    if (!provinciaSelect || !distritoSelect) return;
    
    const provincia = provinciaSelect.value;
    distritoSelect.innerHTML = '<option value="">Seleccione distrito...</option>';
    
    const distritosPorProvincia = {
        'Abancay': ['Abancay', 'Chacoche', 'Circa', 'Curahuasi', 'Huanipaca', 'Lambrama', 'Pichirhua', 'San Pedro de Cachora', 'Tamburco'],
        'Andahuaylas': ['Andahuaylas', 'Andarapa', 'Chiara', 'Huancarama', 'Huancaray', 'Huayana', 'Kishuara', 'Pacobamba', 'Pacucha', 'Pampachiri', 'Pomacocha', 'San Antonio de Cachi', 'San Jerónimo', 'San Miguel de Chaccrampa', 'Santa María de Chicmo', 'Talavera', 'Tumay Huaraca', 'Turpo', 'Kaquiabamba', 'José María Arguedas'],
        'Antabamba': ['Antabamba', 'El Oro', 'Huaquirca', 'Juan Espinoza Medrano', 'Oropesa', 'Pachaconas', 'Sabaino'],
        'Aymaraes': ['Chalhuanca', 'Capaya', 'Caraybamba', 'Chapimarca', 'Colcabamba', 'Cotaruse', 'Ihuayllo', 'Justo Apu Sahuaraura', 'Lucre', 'Pocohuanca', 'San Juan de Chacña', 'Sañayca', 'Soraya', 'Tapairihua', 'Tintay', 'Toraya', 'Yanaca'],
        'Cotabambas': ['Tambobamba', 'Cotabambas', 'Coyllurqui', 'Haquira', 'Mara', 'Challhuahuacho'],
        'Chincheros': ['Chincheros', 'Anco Huallo', 'Cocharcas', 'Huaccana', 'Ocobamba', 'Ongoy', 'Uranmarca', 'Ranracancha', 'Rocchacc', 'El Porvenir', 'Los Chankas'],
        'Grau': ['Chuquibambilla', 'Curpahuasi', 'Gamarra', 'Huayllati', 'Mamara', 'Micaela Bastidas', 'Pataypampa', 'Progreso', 'San Antonio', 'Santa Rosa', 'Turpay', 'Vilcabamba', 'Virundo', 'Curasco']
    };
    
    if (provincia && distritosPorProvincia[provincia]) {
        distritosPorProvincia[provincia].forEach(distrito => {
            const option = document.createElement('option');
            option.value = distrito;
            option.textContent = distrito;
            distritoSelect.appendChild(option);
        });
        distritoSelect.disabled = false;
    } else {
        distritoSelect.disabled = true;
        distritoSelect.innerHTML = '<option value="">Primero seleccione provincia</option>';
    }
}

// Funciones placeholder para evitar errores
function configurarValidacionDinamica() {
    console.log('Configurando validación dinámica...');
}

function cargarCodigosInfracciones() {
    console.log('Cargando códigos de infracciones...');
}

function exportarActaActual(formato) {
    console.log('Exportando acta actual en formato:', formato);
}

function guardarNuevaActa() {
    console.log('Guardando nueva acta...');
}

async function limpiarActasErroneas() {
    showConfirmModal({
        title: 'Limpiar Actas Erróneas',
        message: '¿Estás seguro de eliminar todas las actas con datos vacíos o inválidos? Esta acción no se puede deshacer.',
        type: 'danger',
        confirmText: 'Sí, Limpiar',
        cancelText: 'Cancelar',
        onConfirm: async () => {
            try {
                const response = await fetch('limpiar_actas.php', {
                    method: 'POST',
                    credentials: 'same-origin'
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showSuccessToast(`Se eliminaron ${data.eliminadas} actas erróneas`);
                    cargarActasAdmin();
                } else {
                    showErrorToast('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                showErrorToast('Error de conexión: ' + error.message);
            }
        }
    });
}

// Exportar la nueva función
window.showCrearActaModal = showCrearActaModal;
window.cargarDistritos = cargarDistritos;
window.limpiarActasErroneas = limpiarActasErroneas;