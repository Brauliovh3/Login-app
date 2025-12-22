<?php $__env->startSection('title', 'Consultar Información'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0">
                <i class="fas fa-search me-2" style="color: #ff8c00;"></i>
                Consultar Información
            </h2>
        </div>
    </div>

    <!-- Opciones de consulta -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100" style="border-color: #ff8c00;">
                <div class="card-header" style="background-color: #ff8c00; color: white;">
                    <h5><i class="fas fa-file-alt me-2"></i>Consulta de Multas</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="consulta_placa_multa" class="form-label">Placa del Vehículo</label>
                        <input type="text" class="form-control" id="consulta_placa_multa" placeholder="ABC-123">
                    </div>
                    <div class="mb-3">
                        <label for="consulta_dni_multa" class="form-label">DNI del Conductor</label>
                        <input type="text" class="form-control" id="consulta_dni_multa" placeholder="12345678">
                    </div>
                    <button class="btn btn-primary w-100" onclick="consultarMultas()">
                        <i class="fas fa-search me-2"></i>Buscar Multas
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100" style="border-color: #ff8c00;">
                <div class="card-header" style="background-color: #ff8c00; color: white;">
                    <h5><i class="fas fa-id-card me-2"></i>Estado de Licencia</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="consulta_dni_licencia" class="form-label">DNI del Conductor</label>
                        <input type="text" class="form-control" id="consulta_dni_licencia" placeholder="12345678">
                    </div>
                    <div class="mb-3">
                        <label for="consulta_numero_licencia" class="form-label">Número de Licencia</label>
                        <input type="text" class="form-control" id="consulta_numero_licencia" placeholder="A-IIIa-123456">
                    </div>
                    <button class="btn btn-primary w-100" onclick="consultarLicencia()">
                        <i class="fas fa-search me-2"></i>Consultar Licencia
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100" style="border-color: #ff8c00;">
                <div class="card-header" style="background-color: #ff8c00; color: white;">
                    <h5><i class="fas fa-car me-2"></i>Información de Vehículo</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="consulta_placa_vehiculo" class="form-label">Placa del Vehículo</label>
                        <input type="text" class="form-control" id="consulta_placa_vehiculo" placeholder="ABC-123">
                    </div>
                    <div class="mb-3">
                        <label for="consulta_tarjeta" class="form-label">N° Tarjeta de Propiedad</label>
                        <input type="text" class="form-control" id="consulta_tarjeta" placeholder="Número de tarjeta">
                    </div>
                    <button class="btn btn-primary w-100" onclick="consultarVehiculo()">
                        <i class="fas fa-search me-2"></i>Consultar Vehículo
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100" style="border-color: #ff8c00;">
                <div class="card-header" style="background-color: #ff8c00; color: white;">
                    <h5><i class="fas fa-folder me-2"></i>Estado de Trámite</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="consulta_numero_tramite" class="form-label">Número de Trámite</label>
                        <input type="text" class="form-control" id="consulta_numero_tramite" placeholder="TRA-2025-001">
                    </div>
                    <div class="mb-3">
                        <label for="consulta_dni_tramite" class="form-label">DNI del Solicitante</label>
                        <input type="text" class="form-control" id="consulta_dni_tramite" placeholder="12345678">
                    </div>
                    <button class="btn btn-primary w-100" onclick="consultarTramite()">
                        <i class="fas fa-search me-2"></i>Consultar Trámite
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Nueva fila para Consulta de Actas -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card" style="border-color: #ff8c00;">
                <div class="card-header" style="background-color: #ff8c00; color: white;">
                    <h5><i class="fas fa-file-contract me-2"></i>Consulta de Actas</h5>
                </div>
                <div class="card-body">
                    <form id="form-consulta-actas">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="documento" class="form-label">Documento (DNI/RUC)</label>
                                <input type="text" class="form-control" id="documento" placeholder="12345678 o 20123456789">
                            </div>
                            <div class="col-md-3">
                                <label for="placa" class="form-label">Placa</label>
                                <input type="text" class="form-control" id="placa" placeholder="ABC-123" style="text-transform: uppercase;">
                            </div>
                            <div class="col-md-3">
                                <label for="numero_acta" class="form-label">Número de Acta</label>
                                <input type="text" class="form-control" id="numero_acta" placeholder="DRTC-APU-2025-001">
                            </div>
                            <div class="col-md-3">
                                <label for="estado" class="form-label">Estado</label>
                                <select class="form-select" id="estado">
                                    <option value="">Todos</option>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="procesada">Procesada</option>
                                    <option value="pagada">Pagada</option>
                                    <option value="anulada">Anulada</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="fecha_desde" class="form-label">Fecha Desde</label>
                                <input type="date" class="form-control" id="fecha_desde">
                            </div>
                            <div class="col-md-3">
                                <label for="fecha_hasta" class="form-label">Fecha Hasta</label>
                                <input type="date" class="form-control" id="fecha_hasta">
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary w-100" onclick="consultarActas()">
                                    <i class="fas fa-search me-2"></i>Buscar Actas
                                </button>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-secondary w-100" onclick="limpiarConsultaActas()">
                                    <i class="fas fa-broom me-2"></i>Limpiar
                                </button>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-success w-100" onclick="descargarActasWord()" id="btnDescargarWord" style="display: none;">
                                    <i class="fas fa-file-word me-2"></i>Word
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Área de resultados -->
    <div class="card mt-4 shadow-sm">
        <div class="card-header" style="background: linear-gradient(135deg, #ff8c00, #ff6b35); color: white; border: none;">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Resultados de la Consulta de Actas
                </h5>
                <div id="resultados-info" class="text-white-50 small">
                    <!-- Información de resultados se mostrará aquí -->
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered" id="tabla-resultados">
                    <thead style="background-color: #f8f9fa;">
                        <tr>
                            <th class="text-center" style="width: 80px;"><i class="fas fa-hashtag text-muted"></i> N° Acta</th>
                            <th class="text-center" style="width: 100px;"><i class="fas fa-calendar text-muted"></i> Fecha</th>
                            <th class="text-center" style="width: 90px;"><i class="fas fa-car text-muted"></i> Placa</th>
                            <th style="min-width: 150px;"><i class="fas fa-user text-muted"></i> Conductor</th>
                            <th style="min-width: 150px;"><i class="fas fa-building text-muted"></i> Razón Social</th>
                            <th style="min-width: 200px;"><i class="fas fa-file-alt text-muted"></i> Descripción</th>
                            <th class="text-center" style="width: 100px;"><i class="fas fa-info-circle text-muted"></i> Estado</th>
                            <th class="text-center" style="width: 100px;"><i class="fas fa-money-bill text-muted"></i> Monto</th>
                            <th class="text-center" style="width: 120px;"><i class="fas fa-cogs text-muted"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="resultados-actas">
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Realice una búsqueda</h5>
                                    <p class="text-muted mb-0">Utilice los filtros de arriba para buscar actas</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Información adicional -->
            <div class="row mt-3" id="resultados-stats" style="display: none;">
                <div class="col-md-3">
                    <div class="card border-warning">
                        <div class="card-body text-center py-2">
                            <h6 class="text-warning mb-1"><i class="fas fa-clock"></i></h6>
                            <small class="text-muted">Pendientes</small>
                            <h5 id="count-pendientes" class="mb-0">0</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-info">
                        <div class="card-body text-center py-2">
                            <h6 class="text-info mb-1"><i class="fas fa-play-circle"></i></h6>
                            <small class="text-muted">Procesadas</small>
                            <h5 id="count-procesadas" class="mb-0">0</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-success">
                        <div class="card-body text-center py-2">
                            <h6 class="text-success mb-1"><i class="fas fa-check-circle"></i></h6>
                            <small class="text-muted">Pagadas</small>
                            <h5 id="count-pagadas" class="mb-0">0</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-danger">
                        <div class="card-body text-center py-2">
                            <h6 class="text-danger mb-1"><i class="fas fa-times-circle"></i></h6>
                            <small class="text-muted">Anuladas</small>
                            <h5 id="count-anuladas" class="mb-0">0</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadConsultas();
});

function loadConsultas() {
    // Inicializar la página de consultas
    console.log('Consultas Públicas inicializadas');
    // Aquí se pueden agregar inicializaciones adicionales si es necesario
}

function consultarActas() {
    const documento = document.getElementById('documento').value.trim();
    const placa = document.getElementById('placa').value.trim().toUpperCase();
    const numero_acta = document.getElementById('numero_acta').value.trim();
    const estado = document.getElementById('estado').value;
    const fecha_desde = document.getElementById('fecha_desde').value;
    const fecha_hasta = document.getElementById('fecha_hasta').value;

    // Validar que al menos un campo esté lleno
    if (!documento && !placa && !numero_acta && !estado && !fecha_desde && !fecha_hasta) {
        showNotification('Ingrese al menos un criterio de búsqueda', 'warning');
        return;
    }

    // Preparar parámetros de búsqueda
    const params = new URLSearchParams();
    if (documento) params.append('documento', documento);
    if (placa) params.append('placa', placa);
    if (numero_acta) params.append('numero_acta', numero_acta);
    if (estado) params.append('estado', estado);
    if (fecha_desde) params.append('fecha_desde', fecha_desde);
    if (fecha_hasta) params.append('fecha_hasta', fecha_hasta);

    // Mostrar loading
    const tbody = document.getElementById('resultados-actas');
    tbody.innerHTML = `
        <tr>
            <td colspan="9" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Buscando...</span>
                </div>
                <p class="mt-2">Buscando actas...</p>
            </td>
        </tr>
    `;

    // Realizar la consulta
    fetch(`/consultar-actas?${params.toString()}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.actas && data.actas.length > 0) {
            tbody.innerHTML = data.actas.map(acta => {
                const estadoClass = getEstadoColor(acta.estado);
                const estadoTexto = getEstadoTexto(acta.estado);
                const descripcionCorta = acta.descripcion_hechos ?
                    (acta.descripcion_hechos.length > 60 ? acta.descripcion_hechos.substring(0, 60) + '...' : acta.descripcion_hechos) : '-';

                return `
                <tr class="align-middle">
                    <td class="text-center fw-bold text-primary">${acta.numero_acta || '-'}</td>
                    <td class="text-center">${formatDate(acta.fecha_intervencion)}</td>
                    <td class="text-center"><span class="badge bg-dark">${acta.placa || '-'}</span></td>
                    <td>${acta.nombres || acta.nombre_conductor || '-'}</td>
                    <td>${acta.razon_social || '-'}</td>
                    <td><small class="text-muted" title="${acta.descripcion_hechos || ''}">${descripcionCorta}</small></td>
                    <td class="text-center">
                        <span class="badge bg-${estadoClass} px-2 py-1">
                            <i class="fas ${getEstadoIcon(acta.estado)} me-1"></i>${estadoTexto}
                        </span>
                    </td>
                    <td class="text-end fw-bold text-success">${acta.monto_multa ? 'S/ ' + parseFloat(acta.monto_multa).toFixed(2) : '-'}</td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-info" onclick="verDetalleActa('${acta.id}')" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-outline-primary" onclick="imprimirActa('${acta.id}')" title="Imprimir">
                                <i class="fas fa-print"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                `;
            }).join('');

            // Actualizar información de resultados
            actualizarInfoResultados(data.actas);

            showNotification(`Se encontraron ${data.actas.length} actas`, 'success');
            // Mostrar botón de descarga Word y estadísticas
            document.getElementById('btnDescargarWord').style.display = 'block';
            document.getElementById('resultados-stats').style.display = 'block';
        } else {
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-5">
                        <div class="empty-state">
                            <i class="fas fa-search-minus fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Sin resultados</h5>
                            <p class="text-muted mb-0">No se encontraron actas con los criterios especificados</p>
                        </div>
                    </td>
                </tr>
            `;
            // Ocultar información adicional
            document.getElementById('resultados-info').innerHTML = '';
            document.getElementById('resultados-stats').style.display = 'none';
            showNotification('No se encontraron resultados', 'info');
            // Ocultar botón de descarga Word
            document.getElementById('btnDescargarWord').style.display = 'none';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-5">
                <div class="empty-state">
                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                    <h5 class="text-danger">Error de conexión</h5>
                    <p class="text-muted mb-0">No se pudo realizar la consulta. Intente nuevamente.</p>
                </div>
            </td>
        </tr>
        `;
        // Ocultar elementos adicionales
        document.getElementById('resultados-info').innerHTML = '';
        document.getElementById('resultados-stats').style.display = 'none';
        showNotification('Error al consultar las actas', 'error');
        // Ocultar botón de descarga Word
        document.getElementById('btnDescargarWord').style.display = 'none';
    });
}

function limpiarConsultaActas() {
    document.getElementById('documento').value = '';
    document.getElementById('placa').value = '';
    document.getElementById('numero_acta').value = '';
    document.getElementById('estado').value = '';
    document.getElementById('fecha_desde').value = '';
    document.getElementById('fecha_hasta').value = '';

    document.getElementById('resultados-actas').innerHTML = `
        <tr>
            <td colspan="9" class="text-center py-5">
                <div class="empty-state">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Realice una búsqueda</h5>
                    <p class="text-muted mb-0">Utilice los filtros de arriba para buscar actas</p>
                </div>
            </td>
        </tr>
    `;

    // Ocultar elementos adicionales
    document.getElementById('resultados-info').innerHTML = '';
    document.getElementById('resultados-stats').style.display = 'none';
    document.getElementById('btnDescargarWord').style.display = 'none';
}

function verDetalleActa(actaId) {
    // Mostrar loading
    showNotification('Cargando detalles del acta...', 'info');

    // Realizar consulta a la API
    fetch(`/api/actas/${actaId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.acta) {
            mostrarModalDetalle(data.acta);
        } else {
            showNotification('No se pudo cargar los detalles del acta', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al consultar los detalles del acta', 'error');
    });
}

function mostrarModalDetalle(acta) {
    // Crear el contenido del modal
    const modalContent = `
        <div class="modal fade" id="modalDetalleActa" tabindex="-1" aria-labelledby="modalDetalleActaLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #ff8c00; color: white;">
                        <h5 class="modal-title" id="modalDetalleActaLabel">
                            <i class="fas fa-file-contract me-2"></i>Detalles del Acta ${acta.numero_acta || 'N/A'}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary"><i class="fas fa-info-circle me-2"></i>Información General</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Número de Acta:</strong></td>
                                        <td>${acta.numero_acta || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Fecha:</strong></td>
                                        <td>${formatDate(acta.fecha_intervencion)}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Hora:</strong></td>
                                        <td>${acta.hora_intervencion || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Estado:</strong></td>
                                        <td><span class="badge bg-${getEstadoColor(acta.estado)}">${acta.estado || 'pendiente'}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Monto Multa:</strong></td>
                                        <td>${acta.monto_multa ? 'S/ ' + acta.monto_multa : 'N/A'}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary"><i class="fas fa-car me-2"></i>Información del Vehículo</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Placa:</strong></td>
                                        <td>${acta.placa || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Modelo:</strong></td>
                                        <td>${acta.modelo || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Razón Social:</strong></td>
                                        <td>${acta.razon_social || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Empresa:</strong></td>
                                        <td>${acta.empresa_nombre || 'N/A'}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <h6 class="text-primary"><i class="fas fa-user me-2"></i>Información del Conductor</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Nombre:</strong></td>
                                        <td>${acta.conductor_nombre || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>DNI:</strong></td>
                                        <td>${acta.conductor_dni || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Licencia:</strong></td>
                                        <td>${acta.licencia || 'N/A'}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary"><i class="fas fa-user-tie me-2"></i>Información del Inspector</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Inspector:</strong></td>
                                        <td>${acta.inspector_nombre || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Código Infracción:</strong></td>
                                        <td>${acta.infraccion_codigo || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Ubicación:</strong></td>
                                        <td>${acta.lugar_intervencion || acta.ubicacion || 'N/A'}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <h6 class="text-primary"><i class="fas fa-file-alt me-2"></i>Descripción de los Hechos</h6>
                                <div class="border p-3 bg-light">
                                    ${acta.descripcion_hechos || acta.infraccion_descripcion || 'Sin descripción disponible'}
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <h6 class="text-primary"><i class="fas fa-clock me-2"></i>Información de Registro</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Fecha de Registro:</strong></td>
                                        <td>${formatDate(acta.created_at)}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Hora Inicio Registro:</strong></td>
                                        <td>${acta.hora_inicio_registro ? new Date(acta.hora_inicio_registro).toLocaleTimeString('es-ES') : 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Hora Fin Registro:</strong></td>
                                        <td>${acta.hora_fin_registro ? new Date(acta.hora_fin_registro).toLocaleTimeString('es-ES') : 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tiempo Total:</strong></td>
                                        <td>${acta.tiempo_total_registro ? acta.tiempo_total_registro + ' minutos' : 'N/A'}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cerrar
                        </button>
                        <button type="button" class="btn btn-primary" onclick="imprimirActa(${acta.id})">
                            <i class="fas fa-print me-2"></i>Imprimir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Remover modal anterior si existe
    const existingModal = document.getElementById('modalDetalleActa');
    if (existingModal) {
        existingModal.remove();
    }

    // Agregar el modal al body
    document.body.insertAdjacentHTML('beforeend', modalContent);

    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('modalDetalleActa'));
    modal.show();
}

function imprimirActa(actaId) {
    // Abrir nueva ventana para imprimir
    window.open(`/actas/${actaId}/imprimir`, '_blank');
}

function formatDate(dateString) {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('es-ES');
}

function getEstadoColor(estado) {
    switch(estado) {
        case 'pendiente':
        case '0': return 'warning';
        case 'procesada':
        case '1': return 'info';
        case 'pagada':
        case '3': return 'success';
        case 'anulada':
        case '2': return 'danger';
        default: return 'secondary';
    }
}

function getEstadoTexto(estado) {
    switch(estado) {
        case 'pendiente':
        case '0': return 'Pendiente';
        case 'procesada':
        case '1': return 'Procesada';
        case 'pagada':
        case '3': return 'Pagada';
        case 'anulada':
        case '2': return 'Anulada';
        default: return 'Pendiente';
    }
}

function getEstadoIcon(estado) {
    switch(estado) {
        case 'pendiente':
        case '0': return 'fa-clock';
        case 'procesada':
        case '1': return 'fa-play-circle';
        case 'pagada':
        case '3': return 'fa-check-circle';
        case 'anulada':
        case '2': return 'fa-times-circle';
        default: return 'fa-question-circle';
    }
}

function actualizarInfoResultados(actas) {
    // Actualizar contador en el header
    const infoElement = document.getElementById('resultados-info');
    infoElement.innerHTML = `<i class="fas fa-list-ul me-1"></i> ${actas.length} resultado(s) encontrado(s)`;

    // Actualizar estadísticas
    const pendientes = actas.filter(acta => acta.estado === 'pendiente' || acta.estado === '0').length;
    const procesadas = actas.filter(acta => acta.estado === 'procesada' || acta.estado === '1').length;
    const pagadas = actas.filter(acta => acta.estado === 'pagada' || acta.estado === '3').length;
    const anuladas = actas.filter(acta => acta.estado === 'anulada' || acta.estado === '2').length;

    document.getElementById('count-pendientes').textContent = pendientes;
    document.getElementById('count-procesadas').textContent = procesadas;
    document.getElementById('count-pagadas').textContent = pagadas;
    document.getElementById('count-anuladas').textContent = anuladas;
}

function descargarActasWord() {
    const tbody = document.getElementById('resultados-actas');
    const filas = tbody.getElementsByTagName('tr');

    // Verificar si hay resultados
    if (filas.length === 0 || filas[0].cells.length === 1) {
        showNotification('No hay resultados para descargar', 'warning');
        return;
    }

    // Crear contenido HTML para Word
    let htmlContent = `
        <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">
        <head>
            <meta charset="utf-8">
            <title>Consulta de Actas - DRTC Apurímac</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #ff8c00; padding-bottom: 10px; }
                .header h1 { color: #ff8c00; margin: 0; }
                .header p { margin: 5px 0; color: #666; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #000; padding: 8px; text-align: left; font-size: 11px; }
                th { background-color: #f8f9fa; font-weight: bold; }
                .badge { padding: 2px 6px; border-radius: 3px; font-size: 10px; color: white; }
                .badge-warning { background-color: #ffc107; }
                .badge-info { background-color: #0dcaf0; }
                .badge-success { background-color: #198754; }
                .badge-danger { background-color: #dc3545; }
                .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #666; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>GOBIERNO REGIONAL DE APURÍMAC</h1>
                <h2>DIRECCIÓN REGIONAL DE TRANSPORTES Y COMUNICACIONES</h2>
                <h3>DIRECCIÓN DE CIRCULACIÓN TERRESTRE Y SEGURIDAD VIAL</h3>
                <p><strong>CONSULTA DE ACTAS DE FISCALIZACIÓN</strong></p>
                <p>Fecha de consulta: ${new Date().toLocaleDateString('es-ES')}</p>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>N° Acta</th>
                        <th>Fecha</th>
                        <th>Placa</th>
                        <th>Conductor</th>
                        <th>Razón Social</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Monto</th>
                    </tr>
                </thead>
                <tbody>
    `;

    // Agregar filas de datos
    for (let fila of filas) {
        if (fila.cells.length > 1) { // Evitar fila de "no hay datos"
            const celdas = fila.getElementsByTagName('td');
            const numeroActa = celdas[0].textContent.trim();
            const fecha = celdas[1].textContent.trim();
            const placa = celdas[2].textContent.trim();
            const conductor = celdas[3].textContent.trim();
            const razonSocial = celdas[4].textContent.trim();
            const descripcion = celdas[5].textContent.trim();
            const estado = celdas[6].innerHTML; // Mantener el HTML del badge
            const monto = celdas[7].textContent.trim();

            htmlContent += `
                <tr>
                    <td>${numeroActa}</td>
                    <td>${fecha}</td>
                    <td>${placa}</td>
                    <td>${conductor}</td>
                    <td>${razonSocial}</td>
                    <td>${descripcion}</td>
                    <td>${estado}</td>
                    <td>${monto}</td>
                </tr>
            `;
        }
    }

    htmlContent += `
                </tbody>
            </table>

            <div class="footer">
                <p>Documento generado por el Sistema de Gestión de Actas - DRTC Apurímac</p>
                <p>Total de registros: ${filas.length}</p>
            </div>
        </body>
        </html>
    `;

    // Crear blob y descargar
    const blob = new Blob([htmlContent], { type: 'application/msword' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `Consulta_Actas_${new Date().toISOString().split('T')[0]}.doc`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);

    showNotification('Documento Word descargado correctamente', 'success');
}

// ==================== CONSULTAS ADICIONALES ====================

function consultarMultas() {
    const placa = document.getElementById('consulta_placa_multa').value.trim().toUpperCase();
    const dni = document.getElementById('consulta_dni_multa').value.trim();

    if (!placa && !dni) {
        showNotification('Ingrese placa o DNI del conductor', 'warning');
        return;
    }

    showNotification('Buscando multas...', 'info');

    // Por ahora, buscar en las actas que tienen montos de multa
    const params = new URLSearchParams();
    if (placa) params.append('placa', placa);
    if (dni) params.append('documento', dni);

    fetch(`/consultar-actas?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.actas) {
                const multas = data.actas.filter(acta => acta.monto_multa && parseFloat(acta.monto_multa) > 0);

                if (multas.length > 0) {
                    mostrarResultadoMultas(multas);
                } else {
                    showNotification('No se encontraron multas pendientes para los criterios especificados', 'info');
                }
            } else {
                showNotification('No se pudieron consultar las multas', 'error');
            }
        })
        .catch(error => {
            console.error('Error consultando multas:', error);
            showNotification('Error al consultar multas. Intente nuevamente.', 'error');
        });
}

function mostrarResultadoMultas(multas) {
    const resultadoHTML = `
        <div class="alert alert-info">
            <h6><i class="fas fa-info-circle me-2"></i>Resultado de la consulta</h6>
            <p>Se encontraron <strong>${multas.length}</strong> multa(s) asociada(s) a las actas:</p>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>N° Acta</th>
                        <th>Fecha</th>
                        <th>Placa</th>
                        <th>Conductor</th>
                        <th>Monto</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    ${multas.map(acta => `
                        <tr>
                            <td><strong>${acta.numero_acta || '-'}</strong></td>
                            <td>${formatDate(acta.fecha_intervencion)}</td>
                            <td><span class="badge bg-dark">${acta.placa || '-'}</span></td>
                            <td>${acta.nombres || 'N/A'}</td>
                            <td><strong class="text-danger">S/ ${parseFloat(acta.monto_multa).toFixed(2)}</strong></td>
                            <td><span class="badge bg-${getEstadoColor(acta.estado)}">${getEstadoTexto(acta.estado)}</span></td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="verDetalleActa('${acta.id}')">
                                    <i class="fas fa-eye"></i> Ver Acta
                                </button>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>

        <div class="alert alert-warning mt-3">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Importante:</strong> Esta consulta muestra multas asociadas a actas de fiscalización.
            Para información detallada sobre el pago de multas, acuda a las oficinas de la DRTC.
        </div>
    `;

    // Mostrar en un modal
    mostrarModalResultado('Consulta de Multas', resultadoHTML);
}

function consultarLicencia() {
    const dni = document.getElementById('consulta_dni_licencia').value.trim();
    const numeroLicencia = document.getElementById('consulta_numero_licencia').value.trim();

    if (!dni && !numeroLicencia) {
        showNotification('Ingrese DNI o número de licencia', 'warning');
        return;
    }

    showNotification('Consultando estado de licencia...', 'info');

    // Esta funcionalidad requiere una tabla de licencias que aún no existe
    // Por ahora mostrar mensaje informativo
    setTimeout(() => {
        const resultadoHTML = `
            <div class="alert alert-info">
                <h6><i class="fas fa-id-card me-2"></i>Consulta de Licencia de Conducir</h6>
            </div>

            <div class="text-center py-4">
                <i class="fas fa-tools fa-3x text-warning mb-3"></i>
                <h5>Sistema en Desarrollo</h5>
                <p class="text-muted">
                    La consulta de licencias de conducir está en proceso de implementación.
                </p>
                <p class="text-muted">
                    Para consultar el estado de su licencia, acuda personalmente a las oficinas de la DRTC
                    con su DNI y licencia original.
                </p>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <strong>DNI consultado:</strong> ${dni || 'No especificado'}
                </div>
                <div class="col-md-6">
                    <strong>N° Licencia:</strong> ${numeroLicencia || 'No especificado'}
                </div>
            </div>
        `;

        mostrarModalResultado('Estado de Licencia', resultadoHTML);
    }, 1000);
}

function consultarVehiculo() {
    const placa = document.getElementById('consulta_placa_vehiculo').value.trim().toUpperCase();
    const tarjeta = document.getElementById('consulta_tarjeta').value.trim();

    if (!placa && !tarjeta) {
        showNotification('Ingrese placa o número de tarjeta de propiedad', 'warning');
        return;
    }

    showNotification('Consultando información del vehículo...', 'info');

    // Esta funcionalidad requiere una tabla de vehículos que aún no existe
    // Por ahora mostrar mensaje informativo y buscar en actas existentes
    const params = new URLSearchParams();
    if (placa) params.append('placa', placa);

    fetch(`/consultar-actas?${params}`)
        .then(response => response.json())
        .then(data => {
            let infoVehiculo = null;
            if (data.success && data.actas && data.actas.length > 0) {
                // Tomar información del primer acta encontrada con esa placa
                infoVehiculo = data.actas.find(acta => acta.placa === placa);
            }

            const resultadoHTML = `
                <div class="alert alert-info">
                    <h6><i class="fas fa-car me-2"></i>Consulta de Vehículo</h6>
                </div>

                ${infoVehiculo ? `
                    <div class="card border-success mb-3">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-check-circle me-2"></i>Vehículo Encontrado</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Placa:</strong> <span class="badge bg-dark">${infoVehiculo.placa}</span></p>
                                    <p><strong>Razón Social:</strong> ${infoVehiculo.razon_social || 'No registrada'}</p>
                                    <p><strong>RUC/DNI:</strong> ${infoVehiculo.ruc_dni || 'No registrado'}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Última Intervención:</strong> ${formatDate(infoVehiculo.fecha_intervencion)}</p>
                                    <p><strong>Acta Asociada:</strong> ${infoVehiculo.numero_acta || 'N/A'}</p>
                                    <p><strong>Estado:</strong> <span class="badge bg-${getEstadoColor(infoVehiculo.estado)}">${getEstadoTexto(infoVehiculo.estado)}</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                ` : `
                    <div class="text-center py-4">
                        <i class="fas fa-car-slash fa-3x text-muted mb-3"></i>
                        <h5>Sin Información</h5>
                        <p class="text-muted">
                            No se encontró información del vehículo con la placa especificada.
                        </p>
                    </div>
                `}

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Nota:</strong> La información completa del vehículo requiere acudir a las oficinas de la DRTC
                    con la tarjeta de propiedad original.
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <strong>Placa consultada:</strong> ${placa || 'No especificada'}
                    </div>
                    <div class="col-md-6">
                        <strong>Tarjeta consultada:</strong> ${tarjeta || 'No especificada'}
                    </div>
                </div>
            `;

            mostrarModalResultado('Información del Vehículo', resultadoHTML);
        })
        .catch(error => {
            console.error('Error consultando vehículo:', error);
            showNotification('Error al consultar información del vehículo', 'error');
        });
}

function consultarTramite() {
    const numeroTramite = document.getElementById('consulta_numero_tramite').value.trim();
    const dniTramite = document.getElementById('consulta_dni_tramite').value.trim();

    if (!numeroTramite && !dniTramite) {
        showNotification('Ingrese número de trámite o DNI del solicitante', 'warning');
        return;
    }

    showNotification('Consultando estado del trámite...', 'info');

    // Esta funcionalidad requiere una tabla de trámites que aún no existe
    // Por ahora mostrar mensaje informativo
    setTimeout(() => {
        const resultadoHTML = `
            <div class="alert alert-info">
                <h6><i class="fas fa-folder me-2"></i>Consulta de Estado de Trámite</h6>
            </div>

            <div class="text-center py-4">
                <i class="fas fa-folder-open fa-3x text-info mb-3"></i>
                <h5>Sistema en Desarrollo</h5>
                <p class="text-muted">
                    La consulta de trámites administrativos está en proceso de implementación.
                </p>
                <p class="text-muted">
                    Para consultar el estado de su trámite, acuda personalmente a las oficinas de la DRTC
                    con su comprobante de pago y documento de identidad.
                </p>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <strong>N° Trámite:</strong> ${numeroTramite || 'No especificado'}
                </div>
                <div class="col-md-6">
                    <strong>DNI Solicitante:</strong> ${dniTramite || 'No especificado'}
                </div>
            </div>

            <div class="alert alert-info mt-3">
                <strong>Información útil:</strong>
                <ul class="mb-0 mt-2">
                    <li>Los trámites de renovación de licencias tardan aproximadamente 15 días hábiles</li>
                    <li>Los trámites de duplicado tardan aproximadamente 7 días hábiles</li>
                    <li>Puede realizar seguimiento presencial en ventanilla única</li>
                </ul>
            </div>
        `;

        mostrarModalResultado('Estado del Trámite', resultadoHTML);
    }, 1000);
}

function mostrarModalResultado(titulo, contenido) {
    const modalHTML = `
        <div class="modal fade" id="modalResultadoConsulta" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" style="background: linear-gradient(135deg, #ff8c00, #ff6b35); color: white;">
                        <h5 class="modal-title">
                            <i class="fas fa-search me-2"></i>${titulo}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        ${contenido}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cerrar
                        </button>
                        <button type="button" class="btn btn-primary" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>Imprimir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Remover modal anterior si existe
    const existingModal = document.getElementById('modalResultadoConsulta');
    if (existingModal) {
        existingModal.remove();
    }

    // Agregar el modal al body
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('modalResultadoConsulta'));
    modal.show();
}

// Función auxiliar para mostrar notificaciones
function showNotification(message, type = 'info') {
    // Crear notificación temporal
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(notification);

    // Auto-remover después de 5 segundos
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
