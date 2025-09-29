/**
 * SISTEMA DE GESTIÓN - MÓDULO ADMINISTRADOR
 * Funcionalidades específicas para el rol administrador
 */

console.log('🔐 Cargando módulo administrador...');

// Variable global para verificar que el usuario es administrador
let isAdministrador = false;

// Inicialización del módulo administrador
document.addEventListener('DOMContentLoaded', function() {
    if (window.dashboardUserRole === 'administrador' || window.dashboardUserRole === 'admin') {
        isAdministrador = true;
        console.log('✅ Módulo administrador habilitado para:', window.dashboardUserName);
        initializeAdministradorModule();
    }
});

function initializeAdministradorModule() {
    console.log('🚀 Inicializando módulo administrador...');
    
    // Cargar estadísticas del dashboard al inicio
    loadDashboardStatsAdmin();
    
    // Configurar eventos específicos del administrador
    setupAdministradorEvents();
}

function setupAdministradorEvents() {
    // Configurar eventos específicos para administrador
    console.log('⚙️ Configurando eventos del administrador...');
}

// ==================== DASHBOARD STATS ADMIN ====================
async function loadDashboardStatsAdmin() {
    console.log('📊 Cargando estadísticas del administrador...');
    
    try {
        const response = await fetch(`${window.location.origin}${window.location.pathname}?api=dashboard-stats`);
        const result = await response.json();
        
        if (result.success && result.stats) {
            updateDashboardStatsAdmin(result.stats);
        } else {
            console.error('❌ Error al cargar estadísticas:', result.message);
        }
    } catch (error) {
        console.error('❌ Error al cargar estadísticas del admin:', error);
    }
}

function updateDashboardStatsAdmin(stats) {
    console.log('📈 Actualizando estadísticas del admin:', stats);
    
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
    console.log('👥 [ADMIN] Cargando lista de usuarios...');
    
    if (!isAdministrador) {
        alert('❌ Acceso denegado. Solo administradores pueden ver esta sección.');
        return;
    }
    
    const contentContainer = document.getElementById('contentContainer');
    if (!contentContainer) {
        console.error('❌ ContentContainer no encontrado');
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
                                    <th>ID</th>
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
    console.log('📡 Cargando datos de usuarios desde API...');
    
    try {
        const response = await fetch(`${window.location.origin}${window.location.pathname}?api=users`);
        const result = await response.json();
        
        if (result.success && result.users) {
            mostrarUsuariosEnTabla(result.users);
        } else {
            mostrarErrorUsuarios('No se pudieron cargar los usuarios: ' + (result.message || 'Error desconocido'));
        }
    } catch (error) {
        console.error('❌ Error al cargar usuarios:', error);
        mostrarErrorUsuarios('Error al cargar usuarios: ' + error.message);
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
    
    tbody.innerHTML = usuarios.map(usuario => `
        <tr>
            <td><strong>${usuario.id}</strong></td>
            <td>${usuario.name || 'N/A'}</td>
            <td><code>${usuario.username || 'N/A'}</code></td>
            <td>${usuario.email || 'N/A'}</td>
            <td><span class="badge bg-info">${usuario.role || 'N/A'}</span></td>
            <td><span class="badge ${getStatusBadgeColor(usuario.status)}">${usuario.status || 'N/A'}</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" onclick="verDetalleUsuario(${usuario.id})" title="Ver">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-success" onclick="editarUsuario(${usuario.id})" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    ${usuario.status === 'pending' ? 
                        `<button class="btn btn-outline-success" onclick="aprobarUsuario(${usuario.id})" title="Aprobar">
                            <i class="fas fa-check"></i>
                        </button>` : ''
                    }
                    <button class="btn btn-outline-danger" onclick="eliminarUsuario(${usuario.id}, '${usuario.name}')" title="Eliminar">
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
        case 'approved': return 'bg-success';
        case 'pending': return 'bg-warning text-dark';
        case 'rejected': return 'bg-danger';
        case 'suspended': return 'bg-secondary';
        default: return 'bg-light text-dark';
    }
}

// ==================== APROBAR USUARIOS ====================
function loadAprobarUsuarios() {
    console.log('✅ [ADMIN] Cargando sección aprobar usuarios...');
    
    if (!isAdministrador) {
        alert('❌ Acceso denegado. Solo administradores pueden ver esta sección.');
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
    console.log('⏳ Cargando usuarios pendientes...');
    
    try {
        const response = await fetch(`${window.location.origin}${window.location.pathname}?api=pending-users`);
        const result = await response.json();
        
        if (result.success && result.users) {
            mostrarUsuariosPendientes(result.users);
        } else {
            mostrarErrorPendientes('No se pudieron cargar los usuarios pendientes: ' + (result.message || 'Error desconocido'));
        }
    } catch (error) {
        console.error('❌ Error al cargar usuarios pendientes:', error);
        mostrarErrorPendientes('Error al cargar usuarios pendientes: ' + error.message);
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
    if (!confirm('¿Estás seguro de aprobar este usuario?')) return;
    
    try {
        const formData = new FormData();
        formData.append('user_id', userId);
        
        const response = await fetch(`${window.location.origin}${window.location.pathname}?api=approve-user`, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('✅ Usuario aprobado correctamente');
            cargarUsuariosPendientes(); // Recargar lista
        } else {
            alert('❌ Error al aprobar usuario: ' + (result.message || 'Error desconocido'));
        }
    } catch (error) {
        console.error('Error al aprobar usuario:', error);
        alert('❌ Error al aprobar usuario: ' + error.message);
    }
}

async function rechazarUsuario(userId) {
    const razon = prompt('Ingresa la razón del rechazo (opcional):');
    if (razon === null) return; // Usuario canceló
    
    try {
        const formData = new FormData();
        formData.append('user_id', userId);
        formData.append('reason', razon || 'Sin razón especificada');
        
        const response = await fetch(`${window.location.origin}${window.location.pathname}?api=reject-user`, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('✅ Usuario rechazado correctamente');
            cargarUsuariosPendientes(); // Recargar lista
        } else {
            alert('❌ Error al rechazar usuario: ' + (result.message || 'Error desconocido'));
        }
    } catch (error) {
        console.error('Error al rechazar usuario:', error);
        alert('❌ Error al rechazar usuario: ' + error.message);
    }
}

function verDetalleUsuario(userId) {
    alert(`🔍 Ver detalles del usuario ID: ${userId}\n\nFuncionalidad en desarrollo.`);
}

function editarUsuario(userId) {
    alert(`✏️ Editar usuario ID: ${userId}\n\nFuncionalidad en desarrollo.`);
}

async function eliminarUsuario(userId, userName) {
    if (!confirm(`⚠️ ¿Estás seguro de eliminar al usuario "${userName}"?\n\nEsta acción no se puede deshacer.`)) {
        return;
    }
    
    alert(`🗑️ Eliminar usuario ID: ${userId}\n\nFuncionalidad en desarrollo.`);
}

function mostrarModalCrearUsuario() {
    alert('➕ Crear nuevo usuario\n\nFuncionalidad en desarrollo.');
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

// Debug: Verificar que las funciones están disponibles
console.log('🔍 Verificando funciones exportadas del administrador:');
console.log('- loadUsuariosList:', typeof window.loadUsuariosList);
console.log('- loadAprobarUsuarios:', typeof window.loadAprobarUsuarios);

console.log('✅ Módulo administrador cargado completamente');