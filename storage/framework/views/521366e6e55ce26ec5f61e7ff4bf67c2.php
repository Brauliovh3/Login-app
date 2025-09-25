<div class="text-center">
    <h4><i class="fas fa-gavel"></i> Gestionar Infracciones</h4>
    <p class="text-muted">Administrar tipos de infracciones y sanciones</p>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" id="searchInfraccion" placeholder="Buscar por código o descripción...">
                    <button class="btn btn-primary" onclick="buscarInfracciones()">Buscar</button>
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="tipoInfraccion">
                    <option value="">Todos los tipos</option>
                    <option value="Leve">Leve</option>
                    <option value="Grave">Grave</option>
                    <option value="Muy Grave">Muy Grave</option>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-success" onclick="nuevaInfraccion()">
                    <i class="fas fa-plus"></i> Nueva Infracción
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Tipo</th>
                        <th>Multa (S/)</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="infraccionesTableBody">
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            <i class="fas fa-gavel"></i>
                            <p class="mt-2">Busca infracciones o carga la lista completa</p>
                            <button class="btn btn-outline-primary" onclick="cargarTodasInfracciones()">
                                <i class="fas fa-list"></i> Cargar Todas las Infracciones
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Nueva/Editar Infracción -->
<div class="modal fade" id="infraccionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="infraccionModalTitle">Nueva Infracción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="infraccionForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Código de Infracción *</label>
                                <input type="text" class="form-control" id="codigoInfraccion" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tipo de Infracción *</label>
                                <select class="form-select" id="tipoInfraccionForm" required>
                                    <option value="">Seleccione tipo</option>
                                    <option value="Leve">Leve</option>
                                    <option value="Grave">Grave</option>
                                    <option value="Muy Grave">Muy Grave</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción *</label>
                        <textarea class="form-control" id="descripcionInfraccion" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Multa en Soles *</label>
                                <div class="input-group">
                                    <span class="input-group-text">S/</span>
                                    <input type="number" class="form-control" id="multaSoles" step="0.01" min="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Estado</label>
                                <select class="form-select" id="estadoInfraccion">
                                    <option value="activa">Activa</option>
                                    <option value="inactiva">Inactiva</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observacionesInfraccion" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarInfraccion()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
window.init_admin_infracciones = function() {
    // Cargar automáticamente las infracciones
    cargarTodasInfracciones();
};

function buscarInfracciones() {
    const searchTerm = document.getElementById('searchInfraccion').value.trim();
    const tipo = document.getElementById('tipoInfraccion').value;
    
    if (!searchTerm && !tipo) {
        showNotification('Ingresa un término de búsqueda o selecciona un tipo', 'warning');
        return;
    }
    
    cargarInfracciones({ search: searchTerm, tipo: tipo });
}

function cargarTodasInfracciones() {
    cargarInfracciones({ todos: true });
}

function cargarInfracciones(params = {}) {
    const tbody = document.getElementById('infraccionesTableBody');
    tbody.innerHTML = `
        <tr>
            <td colspan="6" class="text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2">Cargando infracciones...</p>
            </td>
        </tr>
    `;
    
    const queryParams = new URLSearchParams(params);
    
    fetch(`/api/infracciones?${queryParams}`)
        .then(response => response.json())
        .then(data => {
            if (data.infracciones && data.infracciones.length > 0) {
                tbody.innerHTML = data.infracciones.map(infraccion => `
                    <tr>
                        <td><strong>${infraccion.codigo}</strong></td>
                        <td>${infraccion.descripcion}</td>
                        <td><span class="badge bg-${getTipoInfraccionColor(infraccion.tipo_infraccion)}">${infraccion.tipo_infraccion}</span></td>
                        <td class="text-success"><strong>S/ ${infraccion.multa_soles}</strong></td>
                        <td><span class="badge bg-${infraccion.estado === 'activa' ? 'success' : 'secondary'}">${infraccion.estado || 'activa'}</span></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" onclick="verInfraccion(${infraccion.id})" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-outline-info" onclick="editarInfraccion(${infraccion.id})" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-outline-danger" onclick="eliminarInfraccion(${infraccion.id})" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `).join('');
                
                showNotification(`Se encontraron ${data.infracciones.length} infracciones`, 'success');
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            <i class="fas fa-gavel"></i>
                            <p class="mt-2">No se encontraron infracciones</p>
                        </td>
                    </tr>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p class="mt-2">Error cargando infracciones</p>
                    </td>
                </tr>
            `;
            showNotification('Error cargando infracciones', 'error');
        });
}

function nuevaInfraccion() {
    document.getElementById('infraccionModalTitle').textContent = 'Nueva Infracción';
    document.getElementById('infraccionForm').reset();
    
    const modal = new bootstrap.Modal(document.getElementById('infraccionModal'));
    modal.show();
}

function editarInfraccion(id) {
    fetch(`/api/infracciones/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.ok && data.infraccion) {
                const i = data.infraccion;
                document.getElementById('infraccionModalTitle').textContent = 'Editar Infracción';
                document.getElementById('codigoInfraccion').value = i.codigo || '';
                document.getElementById('tipoInfraccionForm').value = i.tipo_infraccion || '';
                document.getElementById('descripcionInfraccion').value = i.descripcion || '';
                document.getElementById('multaSoles').value = i.multa_soles || '';
                document.getElementById('estadoInfraccion').value = i.estado || 'activa';
                document.getElementById('observacionesInfraccion').value = i.observaciones || '';
                
                const modal = new bootstrap.Modal(document.getElementById('infraccionModal'));
                modal.show();
            } else {
                showNotification('No se pudo cargar la información de la infracción', 'error');
            }
        })
        .catch(error => {
            showNotification('Error cargando infracción', 'error');
        });
}

function guardarInfraccion() {
    const formData = {
        codigo_infraccion: document.getElementById('codigoInfraccion').value.trim(),
        tipo_infraccion: document.getElementById('tipoInfraccionForm').value,
        descripcion: document.getElementById('descripcionInfraccion').value.trim(),
        multa_soles: document.getElementById('multaSoles').value,
        estado: document.getElementById('estadoInfraccion').value,
        observaciones: document.getElementById('observacionesInfraccion').value.trim()
    };
    
    if (!formData.codigo_infraccion || !formData.tipo_infraccion || !formData.descripcion || !formData.multa_soles) {
        showNotification('Complete todos los campos obligatorios', 'warning');
        return;
    }
    
    fetch('/api/infracciones/guardar', {
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
            showNotification('Infracción guardada exitosamente', 'success');
            bootstrap.Modal.getInstance(document.getElementById('infraccionModal')).hide();
            cargarTodasInfracciones();
        } else {
            showNotification(data.message || 'Error guardando infracción', 'error');
        }
    })
    .catch(error => {
        showNotification('Error guardando infracción', 'error');
    });
}

function verInfraccion(id) {
    showNotification('Abriendo detalles de la infracción...', 'info');
}

function eliminarInfraccion(id) {
    if (confirm('¿Está seguro de que desea eliminar esta infracción?')) {
        fetch(`/api/infracciones/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.ok) {
                showNotification('Infracción eliminada exitosamente', 'success');
                cargarTodasInfracciones();
            } else {
                showNotification(data.message || 'Error eliminando infracción', 'error');
            }
        })
        .catch(error => {
            showNotification('Error eliminando infracción', 'error');
        });
    }
}

function getTipoInfraccionColor(tipo) {
    switch(tipo) {
        case 'Leve': return 'success';
        case 'Grave': return 'warning';
        case 'Muy Grave': return 'danger';
        default: return 'secondary';
    }
}
</script><?php /**PATH C:\xampp\htdocs\Login-app\resources\views\dashboard\sections\admin\infracciones.blade.php ENDPATH**/ ?>