<!-- Toast Container - Positioned fixed for floating notifications -->
<div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
    <!-- Los toasts se insertarán aquí dinámicamente -->
</div>

<script>
class ToastNotification {
    constructor() {
        this.container = document.getElementById('toastContainer');
        this.init();
    }

    init() {
        // Escuchar eventos de notificación personalizados
        document.addEventListener('showToast', (e) => {
            this.show(e.detail);
        });

        // Verificar notificaciones no leídas cada 30 segundos
        setInterval(() => {
            this.checkForNewNotifications();
        }, 30000);

        // Verificar notificaciones al cargar la página
        this.checkForNewNotifications();
    }

    show(options = {}) {
        const {
            title = 'Notificación',
            message = '',
            type = 'info', // success, error, warning, info
            duration = 5000,
            persist = false
        } = options;

        const toastId = 'toast_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        
        const iconMap = {
            success: 'fas fa-check-circle',
            error: 'fas fa-exclamation-circle',
            warning: 'fas fa-exclamation-triangle',
            info: 'fas fa-info-circle'
        };

        const bgMap = {
            success: 'bg-success',
            error: 'bg-danger',
            warning: 'bg-warning',
            info: 'bg-primary'
        };

        const toastHTML = `
            <div id="${toastId}" class="toast align-items-center text-white ${bgMap[type]} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <div class="d-flex align-items-start">
                            <i class="${iconMap[type]} me-2 mt-1"></i>
                            <div>
                                <strong class="d-block">${title}</strong>
                                ${message ? `<div class="small">${message}</div>` : ''}
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        this.container.insertAdjacentHTML('beforeend', toastHTML);
        
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, {
            autohide: !persist,
            delay: duration
        });

        // Mostrar el toast
        toast.show();

        // Remover del DOM después de que se oculte
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });

        return toast;
    }

    async checkForNewNotifications() {
        try {
            // Sistema de notificaciones deshabilitado
            return;
            
            /*const response = await fetch('/notifications/unread-count');
            const data = await response.json();
            
            // Actualizar contador de notificaciones en la interfaz si existe
            const badge = document.querySelector('.notification-badge');
            if (badge && data.count > 0) {
                badge.textContent = data.count;
                badge.style.display = 'inline';
            } else if (badge) {
                badge.style.display = 'none';
            }

            // Si hay notificaciones nuevas, mostrar una de ellas como toast
            if (data.count > 0 && data.latest) {
                this.showLatestNotification(data.latest);
            }*/
            }
        } catch (error) {
            console.error('Error checking notifications:', error);
        }
    }

    showLatestNotification(notification) {
        // Solo mostrar si la notificación es muy reciente (menos de 1 minuto)
        const notificationTime = new Date(notification.created_at);
        const now = new Date();
        const diffMinutes = (now - notificationTime) / (1000 * 60);

        if (diffMinutes <= 1) {
            const typeMap = {
                success: 'success',
                danger: 'error',
                warning: 'warning',
                info: 'info'
            };

            this.show({
                title: notification.title,
                message: notification.message,
                type: typeMap[notification.type] || 'info',
                duration: 8000
            });
        }
    }

    // Métodos de conveniencia para mostrar diferentes tipos de notificaciones
    success(title, message, duration = 5000) {
        return this.show({ title, message, type: 'success', duration });
    }

    error(title, message, duration = 8000) {
        return this.show({ title, message, type: 'error', duration });
    }

    warning(title, message, duration = 6000) {
        return this.show({ title, message, type: 'warning', duration });
    }

    info(title, message, duration = 5000) {
        return this.show({ title, message, type: 'info', duration });
    }
}

// Inicializar el sistema de notificaciones cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.toastNotification = new ToastNotification();
});

// Función global para mostrar toasts desde otros scripts
function showToast(title, message, type = 'info', duration = 5000) {
    if (window.toastNotification) {
        window.toastNotification.show({ title, message, type, duration });
    }
}
</script>

<style>
.toast-container {
    max-width: 400px;
}

.toast {
    min-width: 300px;
    margin-bottom: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

.toast-body {
    padding: 12px;
}

.toast .btn-close {
    padding: 0.5rem;
}

/* Animación de entrada para los toasts */
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.toast.show {
    animation: slideInRight 0.3s ease-out;
}

/* Estilos para diferentes tipos de notificación */
.toast.bg-success {
    background-color: #28a745 !important;
}

.toast.bg-danger {
    background-color: #dc3545 !important;
}

.toast.bg-warning {
    background-color: #ffc107 !important;
    color: #212529 !important;
}

.toast.bg-warning .btn-close {
    filter: invert(1) grayscale(100%) brightness(200%);
}

.toast.bg-info {
    background-color: #17a2b8 !important;
}
</style>
<?php /**PATH C:\xampp\htdocs\Login-app\resources\views/components/toast-notifications.blade.php ENDPATH**/ ?>