<div class="text-center">
    <h4><i class="fas fa-file-invoice"></i> Ventanilla de Pagos</h4>
    <p class="text-muted">Gestión de pagos y consultas de multas</p>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-primary">
            <div class="card-body text-center">
                <i class="fas fa-search fa-2x text-primary mb-3"></i>
                <h5>Consultar Multa</h5>
                <p class="text-muted">Buscar multa por número de acta o DNI/RUC</p>
                <button class="btn btn-primary" onclick="mostrarConsulta()">Consultar</button>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-success">
            <div class="card-body text-center">
                <i class="fas fa-credit-card fa-2x text-success mb-3"></i>
                <h5>Registrar Pago</h5>
                <p class="text-muted">Procesar pago de multa</p>
                <button class="btn btn-success" onclick="mostrarPago()">Pagar</button>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-info">
            <div class="card-body text-center">
                <i class="fas fa-history fa-2x text-info mb-3"></i>
                <h5>Historial</h5>
                <p class="text-muted">Consultar historial de pagos</p>
                <button class="btn btn-info" onclick="mostrarHistorial()">Ver Historial</button>
            </div>
        </div>
    </div>
</div>

<!-- Panel de Consulta -->
<div id="panelConsulta" class="card shadow mb-4" style="display: none;">
    <div class="card-header bg-primary text-white">
        <h5><i class="fas fa-search"></i> Consultar Multa</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-file-alt"></i>
                    </span>
                    <input type="text" class="form-control" id="numeroActa" placeholder="Número de acta">
                    <button class="btn btn-primary" onclick="consultarPorActa()">Buscar</button>
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-id-card"></i>
                    </span>
                    <input type="text" class="form-control" id="dniRucConsulta" placeholder="DNI o RUC del infractor">
                    <button class="btn btn-primary" onclick="consultarPorDni()">Buscar</button>
                </div>
            </div>
        </div>
        <div id="resultadoConsulta" class="mt-3"></div>
    </div>
</div>

<!-- Panel de Pago -->
<div id="panelPago" class="card shadow mb-4" style="display: none;">
    <div class="card-header bg-success text-white">
        <h5><i class="fas fa-credit-card"></i> Procesar Pago</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <div class="input-group mb-3">
                    <span class="input-group-text">Número de Acta</span>
                    <input type="text" class="form-control" id="actaPago" placeholder="Ingrese número de acta">
                    <button class="btn btn-outline-secondary" onclick="cargarActaPago()">Cargar</button>
                </div>
                <div id="infoActaPago"></div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6>Resumen de Pago</h6>
                        <div id="resumenPago">
                            <p class="text-muted">Cargue un acta para ver el resumen</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Panel de Historial -->
<div id="panelHistorial" class="card shadow mb-4" style="display: none;">
    <div class="card-header bg-info text-white">
        <h5><i class="fas fa-history"></i> Historial de Pagos</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="date" class="form-control" id="fechaDesde" placeholder="Desde">
            </div>
            <div class="col-md-4">
                <input type="date" class="form-control" id="fechaHasta" placeholder="Hasta">
            </div>
            <div class="col-md-4">
                <button class="btn btn-info w-100" onclick="cargarHistorial()">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Fecha Pago</th>
                        <th>N° Acta</th>
                        <th>DNI/RUC</th>
                        <th>Infractor</th>
                        <th>Monto</th>
                        <th>Método</th>
                        <th>Recibo</th>
                    </tr>
                </thead>
                <tbody id="historialTableBody">
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            <i class="fas fa-calendar-alt"></i>
                            <p class="mt-2">Selecciona un rango de fechas para ver el historial</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de Pago -->
<div class="modal fade" id="modalPago" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Confirmar Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="detallesPago"></div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Método de Pago *</label>
                            <select class="form-select" id="metodoPago" required>
                                <option value="">Seleccione método</option>
                                <option value="efectivo">Efectivo</option>
                                <option value="tarjeta">Tarjeta</option>
                                <option value="transferencia">Transferencia</option>
                                <option value="deposito">Depósito Bancario</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Número de Recibo</label>
                            <input type="text" class="form-control" id="numeroRecibo" placeholder="Opcional">
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Observaciones</label>
                    <textarea class="form-control" id="observacionesPago" rows="2" placeholder="Observaciones adicionales"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="confirmarPago()">
                    <i class="fas fa-check"></i> Confirmar Pago
                </button>
            </div>
        </div>
    </div>
</div>

<script>
window.init_ventanilla = function() {
    // Mostrar panel de consulta por defecto
    mostrarConsulta();
};

function mostrarConsulta() {
    ocultarTodosPaneles();
    document.getElementById('panelConsulta').style.display = 'block';
}

function mostrarPago() {
    ocultarTodosPaneles();
    document.getElementById('panelPago').style.display = 'block';
}

function mostrarHistorial() {
    ocultarTodosPaneles();
    document.getElementById('panelHistorial').style.display = 'block';
}

function ocultarTodosPaneles() {
    document.getElementById('panelConsulta').style.display = 'none';
    document.getElementById('panelPago').style.display = 'none';
    document.getElementById('panelHistorial').style.display = 'none';
}

function consultarPorActa() {
    const numeroActa = document.getElementById('numeroActa').value.trim();
    if (!numeroActa) {
        showNotification('Ingrese el número de acta', 'warning');
        return;
    }
    consultarMulta({ numero_acta: numeroActa });
}

function consultarPorDni() {
    const dni = document.getElementById('dniRucConsulta').value.trim();
    if (!dni) {
        showNotification('Ingrese el DNI o RUC', 'warning');
        return;
    }
    consultarMulta({ dni_ruc: dni });
}

function consultarMulta(params) {
    const resultado = document.getElementById('resultadoConsulta');
    resultado.innerHTML = `
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Consultando...</span>
            </div>
            <p class="mt-2">Consultando multa...</p>
        </div>
    `;
    
    const queryParams = new URLSearchParams(params);
    
    fetch(`/api/ventanilla/consultar?${queryParams}`)
        .then(response => response.json())
        .then(data => {
            if (data.ok && data.multas && data.multas.length > 0) {
                resultado.innerHTML = `
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>N° Acta</th>
                                    <th>Fecha</th>
                                    <th>Infractor</th>
                                    <th>Placa</th>
                                    <th>Monto</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.multas.map(multa => `
                                    <tr>
                                        <td><strong>${multa.numero_acta}</strong></td>
                                        <td>${formatDate(multa.fecha_intervencion)}</td>
                                        <td>${multa.razon_social || '-'}</td>
                                        <td>${multa.placa || '-'}</td>
                                        <td class="text-success"><strong>S/ ${multa.monto_multa || '0.00'}</strong></td>
                                        <td><span class="badge bg-${getEstadoMultaColor(multa.estado)}">${multa.estado}</span></td>
                                        <td>
                                            ${multa.estado === 'pendiente' ? `
                                                <button class="btn btn-success btn-sm" onclick="iniciarPago('${multa.numero_acta}')">
                                                    <i class="fas fa-credit-card"></i> Pagar
                                                </button>
                                            ` : `
                                                <button class="btn btn-info btn-sm" onclick="verDetalle('${multa.id}')">
                                                    <i class="fas fa-eye"></i> Ver
                                                </button>
                                            `}
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
            } else {
                resultado.innerHTML = `
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        No se encontraron multas con los criterios de búsqueda
                    </div>
                `;
            }
        })
        .catch(error => {
            resultado.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Error realizando la consulta
                </div>
            `;
            showNotification('Error en la consulta', 'error');
        });
}

function iniciarPago(numeroActa) {
    document.getElementById('actaPago').value = numeroActa;
    mostrarPago();
    cargarActaPago();
}

function cargarActaPago() {
    const numeroActa = document.getElementById('actaPago').value.trim();
    if (!numeroActa) {
        showNotification('Ingrese el número de acta', 'warning');
        return;
    }
    
    fetch(`/api/ventanilla/acta/${numeroActa}`)
        .then(response => response.json())
        .then(data => {
            if (data.ok && data.acta) {
                const acta = data.acta;
                document.getElementById('infoActaPago').innerHTML = `
                    <div class="card">
                        <div class="card-body">
                            <h6>Información del Acta</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Número:</strong> ${acta.numero_acta}</p>
                                    <p><strong>Fecha:</strong> ${formatDate(acta.fecha_intervencion)}</p>
                                    <p><strong>Infractor:</strong> ${acta.razon_social || '-'}</p>
                                    <p><strong>DNI/RUC:</strong> ${acta.ruc_dni || '-'}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Placa:</strong> ${acta.placa || '-'}</p>
                                    <p><strong>Estado:</strong> <span class="badge bg-${getEstadoMultaColor(acta.estado)}">${acta.estado}</span></p>
                                    <p><strong>Monto:</strong> <span class="text-success fs-5"><strong>S/ ${acta.monto_multa || '0.00'}</strong></span></p>
                                </div>
                            </div>
                            <div class="d-grid">
                                <button class="btn btn-success" onclick="abrirModalPago('${acta.numero_acta}', '${acta.monto_multa}', '${acta.razon_social}')">
                                    <i class="fas fa-credit-card"></i> Procesar Pago
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                
                document.getElementById('resumenPago').innerHTML = `
                    <p><strong>Acta:</strong> ${acta.numero_acta}</p>
                    <p><strong>Monto:</strong></p>
                    <h4 class="text-success">S/ ${acta.monto_multa || '0.00'}</h4>
                    <hr>
                    <p class="text-muted small">Verifique los datos antes de procesar el pago</p>
                `;
            } else {
                document.getElementById('infoActaPago').innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        No se encontró el acta o no está pendiente de pago
                    </div>
                `;
                document.getElementById('resumenPago').innerHTML = `<p class="text-muted">Acta no encontrada</p>`;
            }
        })
        .catch(error => {
            showNotification('Error cargando acta', 'error');
        });
}

function abrirModalPago(numeroActa, monto, infractor) {
    document.getElementById('detallesPago').innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <p><strong>Número de Acta:</strong> ${numeroActa}</p>
                <p><strong>Infractor:</strong> ${infractor}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Monto a Pagar:</strong></p>
                <h4 class="text-success">S/ ${monto}</h4>
            </div>
        </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('modalPago'));
    modal.show();
}

function confirmarPago() {
    const metodoPago = document.getElementById('metodoPago').value;
    const numeroRecibo = document.getElementById('numeroRecibo').value;
    const observaciones = document.getElementById('observacionesPago').value;
    const numeroActa = document.getElementById('actaPago').value;
    
    if (!metodoPago) {
        showNotification('Seleccione el método de pago', 'warning');
        return;
    }
    
    const pagoData = {
        numero_acta: numeroActa,
        metodo_pago: metodoPago,
        numero_recibo: numeroRecibo,
        observaciones: observaciones
    };
    
    fetch('/api/ventanilla/pagar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(pagoData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.ok) {
            showNotification('Pago procesado exitosamente', 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalPago')).hide();
            
            // Limpiar formulario
            document.getElementById('actaPago').value = '';
            document.getElementById('infoActaPago').innerHTML = '';
            document.getElementById('resumenPago').innerHTML = '<p class="text-muted">Cargue un acta para ver el resumen</p>';
        } else {
            showNotification(data.message || 'Error procesando pago', 'error');
        }
    })
    .catch(error => {
        showNotification('Error procesando pago', 'error');
    });
}

function cargarHistorial() {
    const fechaDesde = document.getElementById('fechaDesde').value;
    const fechaHasta = document.getElementById('fechaHasta').value;
    
    const tbody = document.getElementById('historialTableBody');
    tbody.innerHTML = `
        <tr>
            <td colspan="7" class="text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2">Cargando historial...</p>
            </td>
        </tr>
    `;
    
    const params = new URLSearchParams();
    if (fechaDesde) params.append('fecha_desde', fechaDesde);
    if (fechaHasta) params.append('fecha_hasta', fechaHasta);
    
    fetch(`/api/ventanilla/historial?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.ok && data.pagos && data.pagos.length > 0) {
                tbody.innerHTML = data.pagos.map(pago => `
                    <tr>
                        <td>${formatDate(pago.fecha_pago)}</td>
                        <td><strong>${pago.numero_acta}</strong></td>
                        <td>${pago.ruc_dni || '-'}</td>
                        <td>${pago.razon_social || '-'}</td>
                        <td class="text-success"><strong>S/ ${pago.monto}</strong></td>
                        <td><span class="badge bg-info">${pago.metodo_pago}</span></td>
                        <td>
                            ${pago.numero_recibo ? `
                                <button class="btn btn-outline-primary btn-sm" onclick="imprimirRecibo('${pago.id}')">
                                    <i class="fas fa-print"></i>
                                </button>
                            ` : '-'}
                        </td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            <i class="fas fa-inbox"></i>
                            <p class="mt-2">No se encontraron pagos en el rango seleccionado</p>
                        </td>
                    </tr>
                `;
            }
        })
        .catch(error => {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center text-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p class="mt-2">Error cargando historial</p>
                    </td>
                </tr>
            `;
            showNotification('Error cargando historial', 'error');
        });
}

function getEstadoMultaColor(estado) {
    switch(estado) {
        case 'pendiente': return 'warning';
        case 'pagada': return 'success';
        case 'anulada': return 'danger';
        default: return 'secondary';
    }
}

function formatDate(dateString) {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('es-ES');
}

function verDetalle(id) {
    showNotification('Abriendo detalles...', 'info');
}

function imprimirRecibo(id) {
    window.open(`/ventanilla/recibo/${id}`, '_blank');
}
</script><?php /**PATH C:\xampp\htdocs\Login-app\resources\views\dashboard\sections\ventanilla.blade.php ENDPATH**/ ?>