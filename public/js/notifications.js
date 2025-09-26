/**
 * Sistema de notificaciones del dashboard
 * Archivo: notifications.js
 */

/**
 * Cargar notificaciones del usuario
 */
function loadNotifications() {
    console.log('🔔 Cargando notificaciones...');
    
    fetch('dashboard.php?api=notifications')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateNotificationDisplay(data.notifications);
                updateNotificationBadge(data.notifications);
                console.log('✅ Notificaciones cargadas:', data.notifications.length);
            } else {
                console.error('❌ Error al cargar notificaciones:', data.message);
            }
        })
        .catch(error => {
            console.error('❌ Error de conexión en notificaciones:', error);
        });
}

/**
 * Actualizar badge de notificaciones
 */
function updateNotificationBadge(notifications) {
    const badge = document.querySelector('.notification-badge');
    if (!badge) return;
    
    const unreadCount = notifications.filter(n => !n.is_read).length;
    
    if (unreadCount > 0) {
        badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
        badge.style.display = 'block';
    } else {
        badge.style.display = 'none';
    }
}

/**
 * Actualizar display de notificaciones en el dropdown
 */
function updateNotificationDisplay(notifications) {
    const container = document.getElementById('notificationsDropdown');
    if (!container) return;
    
    if (notifications.length === 0) {
        container.innerHTML = `
            <div class="dropdown-item text-center py-3">
                <i class="fas fa-bell-slash text-muted"></i>
                <p class="mb-0 small text-muted">No hay notificaciones</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    
    notifications.slice(0, 10).forEach(notification => {
        const isRead = notification.is_read;
        const bgClass = isRead ? '' : 'fw-bold bg-light';
        const timeAgo = formatTimeAgo(notification.created_at);
        
        html += `
            <div class="dropdown-item ${bgClass}" onclick="markNotificationRead(${notification.id})">
                <div class="d-flex align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="mb-1 ${isRead ? 'text-muted' : ''}">${notification.title}</h6>
                        <p class="mb-1 small ${isRead ? 'text-muted' : ''}">${notification.message}</p>
                        <small class="text-muted">${timeAgo}</small>
                    </div>
                    ${!isRead ? '<div class="ms-2"><i class="fas fa-circle text-primary" style="font-size: 8px;"></i></div>' : ''}
                </div>
            </div>
        `;
    });
    
    // Agregar enlace para ver todas
    if (notifications.length > 10) {
        html += `
            <div class="dropdown-item text-center border-top">
                <small><a href="#" onclick="loadAllNotifications()" class="text-decoration-none">Ver todas las notificaciones</a></small>
            </div>
        `;
    }
    
    // Agregar opción para marcar todas como leídas
    const unreadCount = notifications.filter(n => !n.is_read).length;
    if (unreadCount > 0) {
        html += `
            <div class="dropdown-item text-center border-top">
                <button class="btn btn-sm btn-outline-primary" onclick="markAllNotificationsRead()">
                    <i class="fas fa-check-double"></i> Marcar todas como leídas
                </button>
            </div>
        `;
    }
    
    container.innerHTML = html;
}

/**
 * Marcar notificación como leída
 */
function markNotificationRead(notificationId) {
    fetch('dashboard.php?api=mark_notification_read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `notification_id=${notificationId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Recargar notificaciones para actualizar el display
            loadNotifications();
        }
    })
    .catch(error => {
        console.error('Error al marcar notificación:', error);
    });
}

/**
 * Marcar todas las notificaciones como leídas
 */
function markAllNotificationsRead() {
    fetch('dashboard.php?api=mark-all-notifications-read', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Notificaciones', 'Todas las notificaciones han sido marcadas como leídas');
            loadNotifications();
        } else {
            showToast('error', 'Error', 'No se pudieron marcar las notificaciones');
        }
    })
    .catch(error => {
        console.error('Error al marcar todas las notificaciones:', error);
        showToast('error', 'Error', 'Error de conexión');
    });
}

/**
 * Cargar todas las notificaciones
 */
function loadAllNotifications() {
    console.log('📄 Cargando todas las notificaciones...');
    hideAllSections();
    
    const contentContainer = document.getElementById('contentContainer');
    contentContainer.innerHTML = `
        <div class="content-section active">
            <div class="content-header">
                <h4><i class="fas fa-bell"></i> Todas las Notificaciones</h4>
                <p>Historial completo de notificaciones</p>
            </div>
            
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Notificaciones</h5>
                    <button class="btn btn-outline-primary btn-sm" onclick="loadNotifications()">
                        <i class="fas fa-refresh"></i> Actualizar
                    </button>
                </div>
                <div class="card-body">
                    <div id="all-notifications-container">
                        <div class="text-center p-3">
                            <i class="fas fa-spinner fa-spin"></i> Cargando notificaciones...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Cargar las notificaciones completas
    fetch('dashboard.php?api=notifications')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayAllNotifications(data.notifications);
            }
        });
}

/**
 * Mostrar todas las notificaciones
 */
function displayAllNotifications(notifications) {
    const container = document.getElementById('all-notifications-container');
    if (!container) return;
    
    if (notifications.length === 0) {
        container.innerHTML = `
            <div class="text-center p-4">
                <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                <h5>No hay notificaciones</h5>
                <p class="text-muted">Cuando recibas notificaciones, aparecerán aquí.</p>
            </div>
        `;
        return;
    }
    
    let html = '<div class="list-group">';
    
    notifications.forEach(notification => {
        const isRead = notification.is_read;
        const bgClass = isRead ? 'list-group-item-light' : 'list-group-item-primary';
        const timeAgo = formatTimeAgo(notification.created_at);
        
        html += `
            <div class="list-group-item ${bgClass}">
                <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1">${notification.title}</h6>
                    <small>${timeAgo}</small>
                </div>
                <p class="mb-1">${notification.message}</p>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">Estado: ${isRead ? 'Leída' : 'No leída'}</small>
                    ${!isRead ? `<button class="btn btn-sm btn-outline-primary" onclick="markNotificationRead(${notification.id})">Marcar como leída</button>` : ''}
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    container.innerHTML = html;
}

/**
 * Formatear tiempo transcurrido
 */
function formatTimeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now - date;
    const diffMinutes = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMinutes / 60);
    const diffDays = Math.floor(diffHours / 24);
    
    if (diffMinutes < 1) return 'Ahora mismo';
    if (diffMinutes < 60) return `Hace ${diffMinutes} min`;
    if (diffHours < 24) return `Hace ${diffHours}h`;
    if (diffDays < 7) return `Hace ${diffDays}d`;
    
    return date.toLocaleDateString('es-ES');
}

// Exponer funciones globalmente
window.loadNotifications = loadNotifications;
window.markNotificationRead = markNotificationRead;
window.markAllNotificationsRead = markAllNotificationsRead;
window.loadAllNotifications = loadAllNotifications;
window.formatTimeAgo = formatTimeAgo;