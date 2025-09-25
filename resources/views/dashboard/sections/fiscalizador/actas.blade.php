<div class="row mb-3">
    <div class="col-12">
        <h4><i class="fas fa-clipboard-list"></i> Generar Acta de Infracción</h4>
        <p class="text-muted">Registrar nueva acta de infracción de transporte</p>
    </div>
</div>

<form id="actaForm">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">📋 Datos del Acta</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="numero_acta" class="form-label">Número de Acta *</label>
                                <input type="text" class="form-control" id="numero_acta" required>
                                <div class="form-text">Se generará automáticamente si se deja vacío</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_intervencion" class="form-label">Fecha de Intervención *</label>
                                <input type="date" class="form-control" id="fecha_intervencion" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="hora_intervencion" class="form-label">Hora de Intervención *</label>
                                <input type="time" class="form-control" id="hora_intervencion" value="{{ date('H:i') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="lugar_intervencion" class="form-label">Lugar de Intervención</label>
                                <input type="text" class="form-control" id="lugar_intervencion" placeholder="Ej: Av. Principal Km 15">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tipo_agente" class="form-label">Tipo de Agente *</label>
                                <select class="form-select" id="tipo_agente" required>
                                    <option value="">Seleccionar</option>
                                    <option value="Inspector">Inspector</option>
                                    <option value="Fiscalizador">Fiscalizador</option>
                                    <option value="Supervisor">Supervisor</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="inspector_responsable" class="form-label">Inspector Responsable</label>
                                <select class="form-select" id="inspector_responsable">
                                    <option value="">Seleccionar inspector</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-3">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">🚗 Datos del Vehículo</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="placa" class="form-label">Placa del Vehículo *</label>
                                <div class="input-group">
                                    <input type="text" class="form-control text-uppercase" id="placa" placeholder="ABC-123" required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="buscarVehiculo()">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tipo_servicio" class="form-label">Tipo de Servicio</label>
                                <select class="form-select" id="tipo_servicio">
                                    <option value="">Seleccionar</option>
                                    <option value="Transporte de Pasajeros">Transporte de Pasajeros</option>
                                    <option value="Transporte de Carga">Transporte de Carga</option>
                                    <option value="Servicio Particular">Servicio Particular</option>
                                    <option value="Taxi">Taxi</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-3">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">👤 Datos del Conductor/Empresa</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ruc_dni" class="form-label">RUC/DNI *</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="ruc_dni" placeholder="20123456789 / 12345678" required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="consultarDocumento()">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="razon_social" class="form-label">Razón Social/Nombre *</label>
                                <input type="text" class="form-control" id="razon_social" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre_conductor" class="form-label">Nombre del Conductor</label>
                                <input type="text" class="form-control" id="nombre_conductor">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="licencia" class="form-label">Licencia de Conducir</label>
                                <input type="text" class="form-control" id="licencia">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">⚖️ Infracción</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="infraccion_id" class="form-label">Tipo de Infracción *</label>
                        <select class="form-select" id="infraccion_id" required onchange="actualizarMulta()">
                            <option value="">Seleccionar infracción</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="calificacion" class="form-label">Calificación *</label>
                        <select class="form-select" id="calificacion" required>
                            <option value="">Seleccionar</option>
                            <option value="Leve">Leve</option>
                            <option value="Grave">Grave</option>
                            <option value="Muy Grave">Muy Grave</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="monto_multa" class="form-label">Monto de Multa (S/)</label>
                        <input type="number" class="form-control" id="monto_multa" step="0.01" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="medida_administrativa" class="form-label">Medida Administrativa</label>
                        <select class="form-select" id="medida_administrativa">
                            <option value="">Ninguna</option>
                            <option value="Internamiento">Internamiento</option>
                            <option value="Retención de Licencia">Retención de Licencia</option>
                            <option value="Retención de Placa">Retención de Placa</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-3">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">📝 Descripción</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="descripcion_hechos" class="form-label">Descripción de los Hechos *</label>
                        <textarea class="form-control" id="descripcion_hechos" rows="4" required placeholder="Describir detalladamente los hechos que constituyen la infracción..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observaciones" rows="3" placeholder="Observaciones adicionales..."></textarea>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-3">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-save"></i> Guardar Acta
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="limpiarFormulario()">
                            <i class="fas fa-broom"></i> Limpiar
                        </button>
                        <button type="button" class="btn btn-outline-primary" onclick="previsualizarActa()">
                            <i class="fas fa-eye"></i> Vista Previa
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
window.init_fiscal_actas = function() {
    cargarInspectores();
    cargarInfracciones();
    
    document.getElementById('actaForm').addEventListener('submit', handleActaSubmit);
    
    // Auto-completar campos
    document.getElementById('placa').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
};

function cargarInspectores() {
    fetch('/api/inspectores-activos')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('inspector_responsable');
            if (data.inspectores) {
                select.innerHTML = '<option value="">Seleccionar inspector</option>' +
                    data.inspectores.map(inspector => 
                        `<option value="${inspector.id}">${inspector.nombre} (${inspector.codigo_inspector})</option>`
                    ).join('');
            }
        })
        .catch(error => console.error('Error cargando inspectores:', error));
}

function cargarInfracciones() {
    fetch('/api/infracciones')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('infraccion_id');
            if (data.infracciones) {
                select.innerHTML = '<option value="">Seleccionar infracción</option>' +
                    data.infracciones.map(infraccion => 
                        `<option value="${infraccion.id}" data-multa="${infraccion.multa_soles}">
                            ${infraccion.codigo} - ${infraccion.descripcion} (S/ ${infraccion.multa_soles})
                        </option>`
                    ).join('');
            }
        })
        .catch(error => console.error('Error cargando infracciones:', error));
}

function actualizarMulta() {
    const select = document.getElementById('infraccion_id');
    const selectedOption = select.options[select.selectedIndex];
    const monto = selectedOption.getAttribute('data-multa') || '';
    
    document.getElementById('monto_multa').value = monto;
}

function buscarVehiculo() {
    const placa = document.getElementById('placa').value;
    if (!placa) {
        showNotification('Ingresa una placa para buscar', 'warning');
        return;
    }
    
    fetch(`/api/vehiculos/buscar?placa=${placa}`)
        .then(response => response.json())
        .then(data => {
            if (data.vehiculo) {
                showNotification('Vehículo encontrado', 'success');
                // Rellenar campos si existe información
            } else {
                showNotification('Vehículo no encontrado en la base de datos', 'info');
            }
        })
        .catch(error => {
            showNotification('Error buscando vehículo', 'error');
        });
}

function consultarDocumento() {
    const documento = document.getElementById('ruc_dni').value;
    if (!documento) {
        showNotification('Ingresa un documento para consultar', 'warning');
        return;
    }
    
    fetch(`/api/proxy-dni?dni=${documento}`)
        .then(response => response.json())
        .then(data => {
            if (data.nombres) {
                document.getElementById('razon_social').value = `${data.nombres} ${data.apellidos || ''}`.trim();
                showNotification('Datos encontrados', 'success');
            } else {
                showNotification('No se encontraron datos para el documento', 'info');
            }
        })
        .catch(error => {
            showNotification('Error consultando documento', 'error');
        });
}

function handleActaSubmit(e) {
    e.preventDefault();
    
    const formData = {
        numero_acta: document.getElementById('numero_acta').value || null,
        fecha_intervencion: document.getElementById('fecha_intervencion').value,
        hora_intervencion: document.getElementById('hora_intervencion').value,
        lugar_intervencion: document.getElementById('lugar_intervencion').value,
        tipo_agente: document.getElementById('tipo_agente').value,
        inspector_responsable: document.getElementById('inspector_responsable').value,
        placa: document.getElementById('placa').value,
        tipo_servicio: document.getElementById('tipo_servicio').value,
        ruc_dni: document.getElementById('ruc_dni').value,
        razon_social: document.getElementById('razon_social').value,
        nombre_conductor: document.getElementById('nombre_conductor').value,
        licencia: document.getElementById('licencia').value,
        infraccion_id: document.getElementById('infraccion_id').value,
        calificacion: document.getElementById('calificacion').value,
        monto_multa: document.getElementById('monto_multa').value,
        medida_administrativa: document.getElementById('medida_administrativa').value,
        descripcion_hechos: document.getElementById('descripcion_hechos').value,
        observaciones: document.getElementById('observaciones').value
    };
    
    fetch('/api/actas', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.ok) {
            showNotification('Acta guardada exitosamente', 'success');
            limpiarFormulario();
        } else {
            showNotification(data.message || 'Error guardando acta', 'error');
        }
    })
    .catch(error => {
        showNotification('Error de conexión', 'error');
        console.error('Error:', error);
    });
}

function limpiarFormulario() {
    document.getElementById('actaForm').reset();
    document.getElementById('fecha_intervencion').value = '{{ date('Y-m-d') }}';
    document.getElementById('hora_intervencion').value = '{{ date('H:i') }}';
}

function previsualizarActa() {
    showNotification('Vista previa disponible próximamente', 'info');
}
</script>