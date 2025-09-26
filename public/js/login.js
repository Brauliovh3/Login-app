/**
 * Funciones para el formulario de login
 * Archivo: login.js
 */

/**
 * Alternar visibilidad de la contraseña
 */
function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('togglePassword');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// Exponer función globalmente
window.togglePasswordVisibility = togglePasswordVisibility;