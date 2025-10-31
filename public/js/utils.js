/**
 * Funciones de utilidades y componentes comunes
 * Archivo: utils.js
 */

/**
 * Mostrar alertas/mensajes al usuario
 */
function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 
                     type === 'error' ? 'alert-danger' : 
                     type === 'info' ? 'alert-info' : 'alert-warning';
    const icon = type === 'success' ? 'fas fa-check-circle' : 
                type === 'error' ? 'fas fa-exclamation-circle' : 
                type === 'info' ? 'fas fa-info-circle' : 'fas fa-exclamation-triangle';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="${icon}"></i> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    const container = document.querySelector('.main-content');
    if (container) {
        // Remover alertas anteriores
        const existingAlerts = container.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());
        
        container.insertAdjacentHTML('afterbegin', alertHtml);
        
        // Auto-remove después de 5 segundos
        setTimeout(() => {
            const alert = container.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }
}

/**
 * Mostrar notificaciones toast
 */
function showToast(type, title, message) {
    const toastContainer = document.getElementById('toastContainer') || (() => {
        const container = document.createElement('div');
        container.id = 'toastContainer';
        container.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;';
        document.body.appendChild(container);
        return container;
    })();

    const toastId = 'toast-' + Date.now();
    const bgClass = type === 'success' ? 'bg-success' : 
                   type === 'error' ? 'bg-danger' : 
                   type === 'warning' ? 'bg-warning' : 'bg-primary';
    
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `toast align-items-center text-white ${bgClass} border-0 mb-2`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <strong>${title}</strong><br>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="document.getElementById('${toastId}').remove()"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    // Auto-remove después de 5 segundos
    setTimeout(() => {
        if (document.getElementById(toastId)) {
            document.getElementById(toastId).remove();
        }
    }, 5000);
}

/**
 * Mostrar modales dinámicos
 */
function showModal(title, body, footer = '') {
    let modal = document.getElementById('dynamicModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.id = 'dynamicModal';
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="dynamicModalTitle">${title}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="dynamicModalBody">${body}</div>
                    <div class="modal-footer" id="dynamicModalFooter">
                        ${footer || '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>'}
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    } else {
        document.getElementById('dynamicModalTitle').innerHTML = title;
        document.getElementById('dynamicModalBody').innerHTML = body;
        document.getElementById('dynamicModalFooter').innerHTML = footer || '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>';
    }
    
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    return bsModal;
}

/**
 * Realizar peticiones AJAX de forma simplificada
 */
function fetchAPI(endpoint, options = {}) {
    const defaultOptions = {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    };
    
    const finalOptions = { ...defaultOptions, ...options };
    
    return fetch(`dashboard.php?api=${endpoint}`, finalOptions)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        });
}

/**
 * Realizar peticiones POST de forma simplificada
 */
function postAPI(endpoint, data) {
    return fetchAPI(endpoint, {
        method: 'POST',
        body: JSON.stringify(data)
    });
}

/**
 * Confirmar acción antes de ejecutar
 */
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

/**
 * Validar formulario básico
 */
function validateForm(formElement) {
    let isValid = true;
    const requiredFields = formElement.querySelectorAll('[required]');
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}

/**
 * Limpiar formulario
 */
function clearForm(formElement) {
    formElement.reset();
    const invalidFields = formElement.querySelectorAll('.is-invalid');
    invalidFields.forEach(field => field.classList.remove('is-invalid'));
}

/**
 * Debounce para optimizar búsquedas
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Convertir datos a CSV
 */
function convertToCSV(data) {
    if (!data || data.length === 0) return '';
    
    const headers = Object.keys(data[0]);
    const csvHeaders = headers.join(',');
    
    const csvRows = data.map(row => 
        headers.map(header => {
            const value = row[header];
            return `"${String(value).replace(/"/g, '""')}"`;
        }).join(',')
    );
    
    return [csvHeaders, ...csvRows].join('\n');
}

/**
 * Descargar archivo
 */
function downloadFile(content, filename, contentType = 'text/plain') {
    const blob = new Blob([content], { type: contentType });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
}

/**
 * Capitalizar primera letra
 */
function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
}

/**
 * Truncar texto
 */
function truncateText(text, maxLength = 100) {
    if (text.length <= maxLength) return text;
    return text.substring(0, maxLength) + '...';
}

/**
 * Escape HTML para prevenir XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Copiar texto al portapapeles
 */
async function copyToClipboard(text) {
    try {
        await navigator.clipboard.writeText(text);
        showToast('success', 'Copiado', 'Texto copiado al portapapeles');
        return true;
    } catch (err) {
        console.error('Error al copiar:', err);
        showToast('error', 'Error', 'No se pudo copiar el texto');
        return false;
    }
}

/**
 * Formatear fecha para mostrar en español
 */
function formatDate(dateString) {
    if (!dateString) return 'No disponible';
    
    const date = new Date(dateString);
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    
    return date.toLocaleDateString('es-ES', options);
}



/**
 * Formatear moneda en soles
 */
function formatCurrency(amount) {
    if (!amount && amount !== 0) return 'S/ 0.00';
    return new Intl.NumberFormat('es-PE', {
        style: 'currency',
        currency: 'PEN'
    }).format(amount);
}

// Exponer funciones globalmente
window.showAlert = showAlert;
window.showToast = showToast;
window.showModal = showModal;
window.fetchAPI = fetchAPI;
window.postAPI = postAPI;
window.confirmAction = confirmAction;
window.validateForm = validateForm;
window.clearForm = clearForm;
window.debounce = debounce;
window.convertToCSV = convertToCSV;
window.downloadFile = downloadFile;
window.capitalize = capitalize;
window.truncateText = truncateText;
window.escapeHtml = escapeHtml;
window.copyToClipboard = copyToClipboard;
window.formatDate = formatDate;
window.getRoleBadge = getRoleBadge;
window.getStatusBadge = getStatusBadge;
window.formatCurrency = formatCurrency;