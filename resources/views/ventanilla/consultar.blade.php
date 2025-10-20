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
                    <button class="btn btn-primary w-100">
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
                    <button class="btn btn-primary w-100">
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
                    <button class="btn btn-primary w-100">
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
                    <button class="btn btn-primary w-100">
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
                            <div class="col-md-3">
                                <button type="button" class="btn btn-secondary w-100" onclick="limpiarConsultaActas()">
                                    <i class="fas fa-broom me-2"></i>Limpiar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Área de resultados -->
    <div class="card mt-4">
        <div class="card-header" style="background-color: #fff3e0; border-color: #ff8c00;">
            <h5 class="mb-0" style="color: #ff8c00;">
                <i class="fas fa-list me-2"></i>Resultados de la Consulta de Actas
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Número Acta</th>
                            <th>Fecha</th>
                            <th>Placa</th>
                            <th>Conductor</th>
                            <th>Razón Social</th>
                            <th>Infracción</th>
                            <th>Estado</th>
                            <th>Monto</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="resultados-actas">
                        <tr>
                            <td colspan="9" class="text-center text-muted">
                                <i class="fas fa-search fa-2x mb-2"></i>
                                <p class="mb-0">Realice una búsqueda para ver los resultados</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
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
            tbody.innerHTML = data.actas.map(acta => `
                <tr>
                    <td><strong>${acta.numero_acta || '-'}</strong></td>
                    <td>${formatDate(acta.fecha_intervencion)}</td>
                    <td>${acta.placa || '-'}</td>
                    <td>${acta.nombres || '-'}</td>
                    <td>${acta.razon_social || '-'}</td>
                    <td><small>${acta.descripcion_hechos ? acta.descripcion_hechos.substring(0, 50) + '...' : '-'}</small></td>
                    <td><span class="badge bg-${getEstadoColor(acta.estado)}">${acta.estado || 'pendiente'}</span></td>
                    <td>${acta.monto_multa ? 'S/ ' + acta.monto_multa : '-'}</td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="verDetalleActa('${acta.id}')">
                            <i class="fas fa-eye"></i> Ver
                        </button>
                    </td>
                </tr>
            `).join('');
            showNotification(`Se encontraron ${data.actas.length} actas`, 'success');
        } else {
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center text-muted">
                        <i class="fas fa-search-minus fa-2x mb-2"></i>
                        <p class="mb-0">No se encontraron actas con los criterios especificados</p>
                    </td>
                </tr>
            `;
            showNotification('No se encontraron resultados', 'info');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center text-danger">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <p class="mb-0">Error al realizar la consulta</p>
                </td>
            </tr>
        `;
        showNotification('Error al consultar las actas', 'error');
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
            <td colspan="9" class="text-center text-muted">
                <i class="fas fa-search fa-2x mb-2"></i>
                <p class="mb-0">Realice una búsqueda para ver los resultados</p>
            </td>
        </tr>
    `;
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
        case 'pendiente': return 'warning';
        case 'procesada': return 'info';
        case 'pagada': return 'success';
        case 'anulada': return 'danger';
        default: return 'secondary';
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
