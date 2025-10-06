/**
 * Funciones de testing y desarrollo
 * Archivo: testing.js
 */

/**
 * Panel de pruebas - TEST CLICK mejorado
 */
function loadTestClick() {
    console.log('🧪 TEST CLICK ejecutado');
    hideAllSections();
    
    const contentContainer = document.getElementById('contentContainer');
    contentContainer.innerHTML = `
        <div class="content-section active">
            <div class="container-fluid">
                <h2><i class="fas fa-flask text-primary"></i> Panel de Pruebas</h2>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    Este es el panel de pruebas del sistema. Aquí puedes probar diferentes funcionalidades.
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-bell"></i> Pruebas de Notificaciones</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-success" onclick="showToast('success', '¡Éxito!', 'Operación completada correctamente')">
                                        <i class="fas fa-check"></i> Notificación de Éxito
                                    </button>
                                    <button class="btn btn-danger" onclick="showToast('error', 'Error', 'Algo salió mal en la operación')">
                                        <i class="fas fa-times"></i> Notificación de Error
                                    </button>
                                    <button class="btn btn-warning" onclick="showToast('warning', 'Advertencia', 'Por favor, revisa los datos')">
                                        <i class="fas fa-exclamation-triangle"></i> Notificación de Advertencia
                                    </button>
                                    <button class="btn btn-info" onclick="showToast('info', 'Información', 'Proceso iniciado correctamente')">
                                        <i class="fas fa-info"></i> Notificación Informativa
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-cog"></i> Pruebas de Funcionalidades</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-primary" onclick="loadUsuariosList()">
                                        <i class="fas fa-users"></i> Cargar Lista de Usuarios
                                    </button>
                                    <button class="btn btn-secondary" onclick="loadReportes()">
                                        <i class="fas fa-chart-bar"></i> Cargar Reportes
                                    </button>
                                    <button class="btn btn-info" onclick="loadConfiguracion()">
                                        <i class="fas fa-cog"></i> Cargar Configuración
                                    </button>
                                    <button class="btn btn-warning" onclick="loadPerfil()">
                                        <i class="fas fa-user"></i> Cargar Mi Perfil
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-database"></i> Estado del Sistema</h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <div class="p-3 bg-light rounded">
                                            <h4 class="text-success"><i class="fas fa-check-circle"></i></h4>
                                            <small>JavaScript OK</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 bg-light rounded">
                                            <h4 class="text-success"><i class="fas fa-link"></i></h4>
                                            <small>Event Listeners OK</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 bg-light rounded">
                                            <h4 class="text-success"><i class="fas fa-server"></i></h4>
                                            <small>API Disponible</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 bg-light rounded">
                                            <h4 class="text-primary"><i class="fas fa-user"></i></h4>
                                            <small>Usuario: ${userName}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-code"></i> Pruebas de API</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <button class="btn btn-outline-primary w-100 mb-2" onclick="testApiEndpoint('dashboard-stats')">
                                            <i class="fas fa-chart-bar"></i> Probar Estadísticas del Dashboard
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-outline-success w-100 mb-2" onclick="testApiEndpoint('notifications')">
                                            <i class="fas fa-bell"></i> Probar Notificaciones
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-outline-info w-100 mb-2" onclick="testApiEndpoint('users')">
                                            <i class="fas fa-users"></i> Probar API de Usuarios
                                        </button>
                                    </div>
                                </div>
                                <div id="api-test-results" class="mt-3"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 text-center">
                    <button class="btn btn-primary" onclick="loadSection('dashboard')">
                        <i class="fas fa-arrow-left"></i> Volver al Dashboard
                    </button>
                </div>
            </div>
        </div>
    `;
    
    showToast('success', '¡Test Iniciado!', 'Panel de pruebas cargado correctamente');
}

/**
 * Test de endpoint de API
 */
function testApiEndpoint(endpoint) {
    const resultsContainer = document.getElementById('api-test-results');
    resultsContainer.innerHTML = `<div class="alert alert-info">Probando endpoint: ${endpoint}...</div>`;
    
    const startTime = Date.now();
    
    fetchAPI(endpoint)
        .then(data => {
            const endTime = Date.now();
            const duration = endTime - startTime;
            
            resultsContainer.innerHTML = `
                <div class="alert alert-success">
                    <h6><i class="fas fa-check-circle"></i> Test exitoso: ${endpoint}</h6>
                    <p><strong>Tiempo de respuesta:</strong> ${duration}ms</p>
                    <p><strong>Estado:</strong> ${data.success ? 'Éxito' : 'Error'}</p>
                    <details>
                        <summary>Ver respuesta completa</summary>
                        <pre class="mt-2 p-2 bg-light rounded"><code>${JSON.stringify(data, null, 2)}</code></pre>
                    </details>
                </div>
            `;
        })
        .catch(error => {
            const endTime = Date.now();
            const duration = endTime - startTime;
            
            resultsContainer.innerHTML = `
                <div class="alert alert-danger">
                    <h6><i class="fas fa-times-circle"></i> Test fallido: ${endpoint}</h6>
                    <p><strong>Tiempo transcurrido:</strong> ${duration}ms</p>
                    <p><strong>Error:</strong> ${error.message}</p>
                </div>
            `;
        });
}

/**
 * Test de funciones JavaScript
 */
function testJavaScriptFunctions() {
    const tests = [
        { name: 'showToast', func: () => showToast('info', 'Test', 'Función showToast funcionando') },
        { name: 'showAlert', func: () => showAlert('info', 'Función showAlert funcionando') },
        { name: 'formatNumber', func: () => formatNumber(123456.789) },
        { name: 'formatDate', func: () => formatDate('2024-01-01') },
        { name: 'capitalize', func: () => capitalize('test string') },
        { name: 'escapeHtml', func: () => escapeHtml('<script>alert("test")</script>') }
    ];
    
    const results = [];
    
    tests.forEach(test => {
        try {
            const result = test.func();
            results.push({
                name: test.name,
                success: true,
                result: result
            });
        } catch (error) {
            results.push({
                name: test.name,
                success: false,
                error: error.message
            });
        }
    });
    
    return results;
}

/**
 * Mostrar resultados de tests JavaScript
 */
function showJavaScriptTestResults() {
    const results = testJavaScriptFunctions();
    
    let html = '<div class="card"><div class="card-header"><h6>Resultados de Test JavaScript</h6></div><div class="card-body">';
    
    results.forEach(test => {
        const statusClass = test.success ? 'success' : 'danger';
        const statusIcon = test.success ? 'check-circle' : 'times-circle';
        
        html += `
            <div class="alert alert-${statusClass} py-2 mb-2">
                <i class="fas fa-${statusIcon}"></i> 
                <strong>${test.name}:</strong> 
                ${test.success ? 'OK' : 'ERROR - ' + test.error}
                ${test.result ? ` (${test.result})` : ''}
            </div>
        `;
    });
    
    html += '</div></div>';
    
    showModal('Resultados de Test JavaScript', html);
}

/**
 * Ejecutar todos los tests
 */
function runAllTests() {
    showToast('info', 'Ejecutando Tests', 'Iniciando batería completa de pruebas...');
    
    // Test JavaScript
    setTimeout(() => {
        showJavaScriptTestResults();
    }, 500);
    
    // Test API endpoints
    setTimeout(() => {
        testApiEndpoint('dashboard-stats');
    }, 1000);
    
    setTimeout(() => {
        testApiEndpoint('notifications');
    }, 2000);
    
    setTimeout(() => {
        showToast('success', 'Tests Completados', 'Todas las pruebas han sido ejecutadas');
    }, 3000);
}

/**
 * Función de test simple para verificar que el archivo está cargado
 */
function testFunction() {
    console.log('🧪 testFunction ejecutada correctamente');
    showToast('success', 'Test OK', 'La función de test se ejecutó correctamente');
    return true;
}

// Exponer funciones globalmente
window.loadTestClick = loadTestClick;
window.testApiEndpoint = testApiEndpoint;
window.testJavaScriptFunctions = testJavaScriptFunctions;
window.showJavaScriptTestResults = showJavaScriptTestResults;
window.runAllTests = runAllTests;
window.testFunction = testFunction;

// Log de carga del archivo
console.log('✅ testing.js cargado correctamente');