/**
 * ================================
 * GESTIÓN DE USUARIOS - FUNCIONES UTILITARIAS
 * Sistema de Gestión - JavaScript
 * ================================
 * NOTA: Las funciones específicas del administrador están en administrador.js
 */

console.log('📋 Cargando funciones utilitarias de usuarios...');

// ================================
// FUNCIONES UTILITARIAS GENERALES
// ================================

function getStatusBadgeColorGeneral(status) {
    switch(status) {
        case 'approved': return 'bg-success';
        case 'pending': return 'bg-warning text-dark';
        case 'rejected': return 'bg-danger';
        case 'suspended': return 'bg-secondary';
        default: return 'bg-light text-dark';
    }
}

function formatearFechaGeneral(fecha) {
    if (!fecha) return 'N/A';
    try {
        return new Date(fecha).toLocaleDateString('es-ES');
    } catch {
        return fecha;
    }
}

// Función para mostrar alertas de usuario
function mostrarAlertaUsuario(mensaje, tipo = 'info') {
    const alertClass = tipo === 'success' ? 'alert-success' : 
                      tipo === 'error' ? 'alert-danger' : 
                      tipo === 'warning' ? 'alert-warning' : 'alert-info';
    
    const alertHTML = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Buscar contenedor para mostrar la alerta
    const container = document.getElementById('contentContainer');
    if (container) {
        container.insertAdjacentHTML('afterbegin', alertHTML);
        
        // Auto-remover después de 5 segundos
        setTimeout(() => {
            const alert = container.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }
}

// Función para validar email
function validarEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Función para validar teléfono
function validarTelefono(telefono) {
    const re = /^[0-9+\-\s()]{9,15}$/;
    return re.test(telefono);
}

// Exportar funciones utilitarias
window.getStatusBadgeColorGeneral = getStatusBadgeColorGeneral;
window.formatearFechaGeneral = formatearFechaGeneral;
window.mostrarAlertaUsuario = mostrarAlertaUsuario;
window.validarEmail = validarEmail;
window.validarTelefono = validarTelefono;

console.log('✅ Funciones utilitarias de usuarios cargadas');