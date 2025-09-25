<div class="text-center">
    <h4><i class="fas fa-clipboard-check"></i> Inspecciones Asignadas</h4>
    <p class="text-muted">Gestionar inspecciones vehiculares y reportes</p>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-body text-center">
                <i class="fas fa-plus-circle fa-2x text-primary mb-3"></i>
                <h5>Nueva Inspección</h5>
                <p class="text-muted">Iniciar nueva inspección vehicular</p>
                <button class="btn btn-primary" onclick="nuevaInspeccion()">Iniciar</button>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-warning">
            <div class="card-body text-center">
                <i class="fas fa-clock fa-2x text-warning mb-3"></i>
                <h5>Pendientes</h5>
                <p class="text-muted">Inspecciones programadas</p>
                <button class="btn btn-warning" onclick="mostrarPendientes()">Ver</button>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-success">
            <div class="card-body text-center">
                <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
                <h5>Completadas</h5>
                <p class="text-muted">Inspecciones finalizadas</p>
                <button class="btn btn-success" onclick="mostrarCompletadas()">Ver</button>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-info">
            <div class="card-body text-center">
                <i class="fas fa-chart-bar fa-2x text-info mb-3"></i>
                <h5>Reportes</h5>
                <p class="text-muted">Generar reportes</p>
                <button class="btn btn-info" onclick="mostrarReportes()">Generar</button>
            </div>
        </div>
    </div>
</div>

<!-- Panel de Filtros -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" id="searchInspeccion" placeholder="Placa, conductor...">
                </div>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="estadoInspeccion">
                    <option value="">Todos los estados</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="en_proceso">En Proceso</option>
                    <option value="completada">Completada</option>
                    <option value="observada">Observada</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" id="fechaInspeccion">
            </div>
            <div class="col-md-2">
                <select class="form-select" id="tipoInspeccion">
                    <option value="">Tipo de inspección</option>
                    <option value="rutinaria">Rutinaria</option>
                    <option value="operativo">Operativo</option>
                    <option value="seguimiento">Seguimiento</option>
                    <option value="denuncia">Por Denuncia</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" onclick="buscarInspecciones()">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>
            <div class="col-md-1">
                <button class="btn btn-outline-secondary w-100" onclick="limpiarFiltros()">
                    <i class="fas fa-broom"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Inspecciones -->
<div class="card shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Placa</th>
                        <th>Conductor</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Inspector</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="inspeccionesTableBody">
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            <i class="fas fa-clipboard-check"></i>
                            <p class="mt-2">Busca inspecciones o carga la lista completa</p>
                            <button class="btn btn-outline-primary" onclick="cargarTodasInspecciones()">
                                <i class="fas fa-list"></i> Cargar Todas las Inspecciones
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Nueva Inspección -->
<div class="modal fade" id="nuevaInspeccionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Nueva Inspección Vehicular</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="nuevaInspeccionForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Placa del Vehículo *</label>
                                <input type="text" class="form-control" id="placaVehiculo" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tipo de Inspección *</label>
                                <select class="form-select" id="tipoInspeccionModal" required>
                                    <option value="">Seleccione tipo</option>
                                    <option value="rutinaria">Rutinaria</option>
                                    <option value="operativo">Operativo</option>
                                    <option value="seguimiento">Seguimiento</option>
                                    <option value="denuncia">Por Denuncia</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Fecha de Inspección *</label>
                                <input type="date" class="form-control" id="fechaInspeccionModal" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Hora de Inspección *</label>
                                <input type="time" class="form-control" id="horaInspeccionModal" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">DNI del Conductor</label>
                                <input type="text" class="form-control" id="dniConductorModal" maxlength="8">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nombre del Conductor</label>
                                <input type="text" class="form-control" id="nombreConductorModal">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ubicación *</label>
                        <input type="text" class="form-control" id="ubicacionInspeccionModal" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observacionesInspeccionModal" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarNuevaInspeccion()">
                    <i class="fas fa-save"></i> Crear Inspección
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detalles Inspección -->
<div class="modal fade" id="detallesInspeccionModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Detalles de la Inspección</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detallesInspeccionContent">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="editarInspeccion()">
                    <i class="fas fa-edit"></i> Editar
                </button>
                <button type="button" class="btn btn-success" onclick="completarInspeccion()">
                    <i class="fas fa-check"></i> Completar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
window.init_inspector = function() {
    // Establecer fecha de hoy por defecto
    document.getElementById('fechaInspeccion').value = new Date().toISOString().split('T')[0];
    cargarTodasInspecciones();
};

function buscarInspecciones() {
    const searchTerm = document.getElementById('searchInspeccion').value.trim();
    const estado = document.getElementById('estadoInspeccion').value;
    const fecha = document.getElementById('fechaInspeccion').value;
    const tipo = document.getElementById('tipoInspeccion').value;
    
    cargarInspecciones({ search: searchTerm, estado: estado, fecha: fecha, tipo: tipo });
}

function cargarTodasInspecciones() {
    cargarInspecciones({ todos: true });
}

function cargarInspecciones(params = {}) {
    const tbody = document.getElementById('inspeccionesTableBody');
    tbody.innerHTML = `
        <tr>
            <td colspan="8" class="text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2">Cargando inspecciones...</p>
            </td>
        </tr>
    `;
    
    const queryParams = new URLSearchParams(params);
    
    fetch(`/api/inspector/inspecciones?${queryParams}`)
        .then(response => response.json())
        .then(data => {
            if (data.ok && data.inspecciones && data.inspecciones.length > 0) {
                tbody.innerHTML = data.inspecciones.map(inspeccion => `
                    <tr>
                        <td><strong>#${inspeccion.id}</strong></td>
                        <td>${formatDate(inspeccion.fecha_inspeccion)}</td>
                        <td><strong>${inspeccion.placa}</strong></td>
                        <td>${inspeccion.conductor_nombre || '-'}</td>
                        <td><span class="badge bg-secondary">${inspeccion.tipo_inspeccion}</span></td>
                        <td><span class="badge bg-${getEstadoInspeccionColor(inspeccion.estado)}">${inspeccion.estado}</span></td>
                        <td>${inspeccion.inspector_nombre || 'Sin asignar'}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" onclick="verDetallesInspeccion(${inspeccion.id})" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </button>
                                ${inspeccion.estado !== 'completada' ? `
                                    <button class="btn btn-outline-info" onclick="continuarInspeccion(${inspeccion.id})" title="Continuar">
                                        <i class="fas fa-play"></i>
                                    </button>
                                ` : `
                                    <button class="btn btn-outline-success" onclick="verReporte(${inspeccion.id})" title="Ver reporte">
                                        <i class="fas fa-file-alt"></i>
                                    </button>
                                `}
                            </div>
                        </td>
                    </tr>
                `).join('');
                
                showNotification(`Se encontraron ${data.inspecciones.length} inspecciones`, 'success');
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            <i class="fas fa-clipboard-check"></i>
                            <p class="mt-2">No se encontraron inspecciones</p>
                        </td>
                    </tr>
                `;
            }
        })
        .catch(error => {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center text-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p class="mt-2">Error cargando inspecciones</p>
                    </td>
                </tr>
            `;
            showNotification('Error cargando inspecciones', 'error');
        });
}

function nuevaInspeccion() {
    // Establecer fecha y hora actual por defecto
    const now = new Date();
    document.getElementById('fechaInspeccionModal').value = now.toISOString().split('T')[0];
    document.getElementById('horaInspeccionModal').value = now.toTimeString().slice(0, 5);
    
    document.getElementById('nuevaInspeccionForm').reset();
    const modal = new bootstrap.Modal(document.getElementById('nuevaInspeccionModal'));
    modal.show();
}

function guardarNuevaInspeccion() {
    const formData = {
        placa: document.getElementById('placaVehiculo').value.trim().toUpperCase(),
        tipo_inspeccion: document.getElementById('tipoInspeccionModal').value,
        fecha_inspeccion: document.getElementById('fechaInspeccionModal').value,
        hora_inspeccion: document.getElementById('horaInspeccionModal').value,
        conductor_dni: document.getElementById('dniConductorModal').value.trim(),
        conductor_nombre: document.getElementById('nombreConductorModal').value.trim(),
        ubicacion: document.getElementById('ubicacionInspeccionModal').value.trim(),
        observaciones: document.getElementById('observacionesInspeccionModal').value.trim(),
        estado: 'pendiente'
    };
    
    if (!formData.placa || !formData.tipo_inspeccion || !formData.fecha_inspeccion || !formData.hora_inspeccion || !formData.ubicacion) {
        showNotification('Complete todos los campos obligatorios', 'warning');
        return;
    }
    
    fetch('/api/inspector/inspecciones', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.ok) {
            showNotification('Inspección creada exitosamente', 'success');
            bootstrap.Modal.getInstance(document.getElementById('nuevaInspeccionModal')).hide();
            cargarTodasInspecciones();
        } else {
            showNotification(data.message || 'Error creando inspección', 'error');
        }
    })
    .catch(error => {
        showNotification('Error creando inspección', 'error');
    });
}

function verDetallesInspeccion(id) {
    fetch(`/api/inspector/inspecciones/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.ok && data.inspeccion) {
                const i = data.inspeccion;
                document.getElementById('detallesInspeccionContent').innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Información General</h6>
                            <p><strong>ID:</strong> #${i.id}</p>
                            <p><strong>Placa:</strong> ${i.placa}</p>
                            <p><strong>Tipo:</strong> <span class="badge bg-secondary">${i.tipo_inspeccion}</span></p>
                            <p><strong>Estado:</strong> <span class="badge bg-${getEstadoInspeccionColor(i.estado)}">${i.estado}</span></p>
                            <p><strong>Fecha:</strong> ${formatDate(i.fecha_inspeccion)}</p>
                            <p><strong>Hora:</strong> ${i.hora_inspeccion}</p>
                            <p><strong>Ubicación:</strong> ${i.ubicacion}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Conductor</h6>
                            <p><strong>DNI:</strong> ${i.conductor_dni || 'No especificado'}</p>
                            <p><strong>Nombre:</strong> ${i.conductor_nombre || 'No especificado'}</p>
                            <h6>Inspector</h6>
                            <p><strong>Asignado:</strong> ${i.inspector_nombre || 'Sin asignar'}</p>
                            <p><strong>Fecha Asignación:</strong> ${i.fecha_asignacion ? formatDate(i.fecha_asignacion) : '-'}</p>
                        </div>
                    </div>
                    ${i.observaciones ? `
                        <hr>
                        <h6>Observaciones</h6>
                        <p>${i.observaciones}</p>
                    ` : ''}
                    ${i.resultado_inspeccion ? `
                        <hr>
                        <h6>Resultado de Inspección</h6>
                        <p>${i.resultado_inspeccion}</p>
                    ` : ''}
                `;
                
                const modal = new bootstrap.Modal(document.getElementById('detallesInspeccionModal'));
                modal.show();
            } else {
                showNotification('No se pudo cargar la información de la inspección', 'error');
            }
        })
        .catch(error => {
            showNotification('Error cargando detalles de inspección', 'error');
        });
}

function mostrarPendientes() {
    document.getElementById('estadoInspeccion').value = 'pendiente';
    buscarInspecciones();
}

function mostrarCompletadas() {
    document.getElementById('estadoInspeccion').value = 'completada';
    buscarInspecciones();
}

function mostrarReportes() {
    showNotification('Funcionalidad de reportes disponible próximamente', 'info');
}

function continuarInspeccion(id) {
    showNotification('Continuando inspección...', 'info');
    // Aquí se podría abrir un modal de inspección detallada
}

function completarInspeccion() {
    showNotification('Completando inspección...', 'info');
    // Lógica para completar inspección
}

function editarInspeccion() {
    showNotification('Editando inspección...', 'info');
    // Lógica para editar inspección
}

function verReporte(id) {
    window.open(`/inspector/inspecciones/${id}/reporte`, '_blank');
}

function limpiarFiltros() {
    document.getElementById('searchInspeccion').value = '';
    document.getElementById('estadoInspeccion').value = '';
    document.getElementById('fechaInspeccion').value = '';
    document.getElementById('tipoInspeccion').value = '';
    
    cargarTodasInspecciones();
}

function getEstadoInspeccionColor(estado) {
    switch(estado) {
        case 'pendiente': return 'warning';
        case 'en_proceso': return 'info';
        case 'completada': return 'success';
        case 'observada': return 'danger';
        default: return 'secondary';
    }
}

function formatDate(dateString) {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('es-ES');
}
</script><?php /**PATH C:\xampp\htdocs\Login-app\resources\views\dashboard\sections\inspector.blade.php ENDPATH**/ ?>