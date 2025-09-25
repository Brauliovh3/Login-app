<div class="text-center">
    <h4><i class="fas fa-users-cog"></i> Gestionar Conductores</h4>
    <p class="text-muted">Administrar información de conductores registrados</p>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" id="searchConductor" placeholder="DNI, nombre, apellidos...">
                    <button class="btn btn-primary" onclick="buscarConductores()">Buscar</button>
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="estadoConductor">
                    <option value="">Todos los estados</option>
                    <option value="activo">Activo</option>
                    <option value="suspendido">Suspendido</option>
                    <option value="inhabilitado">Inhabilitado</option>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-success" onclick="nuevoConductor()">
                    <i class="fas fa-plus"></i> Nuevo Conductor
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
                        <th>DNI</th>
                        <th>Nombres y Apellidos</th>
                        <th>Licencia</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Infracciones</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="conductoresTableBody">
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            <i class="fas fa-user-friends"></i>
                            <p class="mt-2">Busca conductores o carga la lista completa</p>
                            <button class="btn btn-outline-primary" onclick="cargarTodosConductores()">
                                <i class="fas fa-list"></i> Cargar Todos los Conductores
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Nuevo/Editar Conductor -->
<div class="modal fade" id="conductorModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="conductorModalTitle">Nuevo Conductor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="conductorForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">DNI *</label>
                                <input type="text" class="form-control" id="dni" required maxlength="8">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Número de Licencia</label>
                                <input type="text" class="form-control" id="licencia">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nombres *</label>
                                <input type="text" class="form-control" id="nombres" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Apellidos *</label>
                                <input type="text" class="form-control" id="apellidos" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="telefono">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" id="email">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Fecha de Nacimiento</label>
                                <input type="date" class="form-control" id="fecha_nacimiento">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Estado</label>
                                <select class="form-select" id="estado">
                                    <option value="activo">Activo</option>
                                    <option value="suspendido">Suspendido</option>
                                    <option value="inhabilitado">Inhabilitado</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <textarea class="form-control" id="direccion" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarConductor()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
window.init_admin_conductores = function() {
    // Inicialización automática si es necesario
};

function buscarConductores() {
    const searchTerm = document.getElementById('searchConductor').value.trim();
    const estado = document.getElementById('estadoConductor').value;
    
    if (!searchTerm && !estado) {
        showNotification('Ingresa un término de búsqueda o selecciona un estado', 'warning');
        return;
    }
    
    cargarConductores({ search: searchTerm, estado: estado });
}

function cargarTodosConductores() {
    cargarConductores({ todos: true });
}

function cargarConductores(params = {}) {
    const tbody = document.getElementById('conductoresTableBody');
    tbody.innerHTML = `
        <tr>
            <td colspan="7" class="text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2">Cargando conductores...</p>
            </td>
        </tr>
    `;
    
    const queryParams = new URLSearchParams(params);
    
    fetch(`/api/conductores?${queryParams}`)
        .then(response => response.json())
        .then(data => {
            if (data.ok && data.conductores && data.conductores.length > 0) {
                tbody.innerHTML = data.conductores.map(conductor => `
                    <tr>
                        <td><strong>${conductor.dni}</strong></td>
                        <td>${conductor.nombres} ${conductor.apellidos}</td>
                        <td>${conductor.licencia || '-'}</td>
                        <td>${conductor.telefono || '-'}</td>
                        <td><span class="badge bg-${getEstadoConductorColor(conductor.estado)}">${conductor.estado}</span></td>
                        <td>
                            <span class="badge bg-${conductor.infracciones > 0 ? 'danger' : 'success'}">
                                ${conductor.infracciones || 0}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" onclick="verConductor(${conductor.id})" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-outline-info" onclick="editarConductor(${conductor.id})" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-outline-warning" onclick="verInfracciones(${conductor.id})" title="Infracciones">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `).join('');
                
                showNotification(`Se encontraron ${data.conductores.length} conductores`, 'success');
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            <i class="fas fa-user-slash"></i>
                            <p class="mt-2">No se encontraron conductores</p>
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
                        <p class="mt-2">Error cargando conductores</p>
                    </td>
                </tr>
            `;
            showNotification('Error cargando conductores', 'error');
        });
}

function nuevoConductor() {
    document.getElementById('conductorModalTitle').textContent = 'Nuevo Conductor';
    document.getElementById('conductorForm').reset();
    
    const modal = new bootstrap.Modal(document.getElementById('conductorModal'));
    modal.show();
}

function editarConductor(id) {
    fetch(`/api/conductores/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.ok && data.conductor) {
                const c = data.conductor;
                document.getElementById('conductorModalTitle').textContent = 'Editar Conductor';
                document.getElementById('dni').value = c.dni || '';
                document.getElementById('licencia').value = c.licencia || '';
                document.getElementById('nombres').value = c.nombres || '';
                document.getElementById('apellidos').value = c.apellidos || '';
                document.getElementById('telefono').value = c.telefono || '';
                document.getElementById('email').value = c.email || '';
                document.getElementById('fecha_nacimiento').value = c.fecha_nacimiento || '';
                document.getElementById('estado').value = c.estado || 'activo';
                document.getElementById('direccion').value = c.direccion || '';
                
                const modal = new bootstrap.Modal(document.getElementById('conductorModal'));
                modal.show();
            } else {
                showNotification('No se pudo cargar la información del conductor', 'error');
            }
        })
        .catch(error => {
            showNotification('Error cargando conductor', 'error');
        });
}

function guardarConductor() {
    const formData = {
        dni: document.getElementById('dni').value.trim(),
        licencia: document.getElementById('licencia').value.trim(),
        nombres: document.getElementById('nombres').value.trim(),
        apellidos: document.getElementById('apellidos').value.trim(),
        telefono: document.getElementById('telefono').value.trim(),
        email: document.getElementById('email').value.trim(),
        fecha_nacimiento: document.getElementById('fecha_nacimiento').value,
        estado: document.getElementById('estado').value,
        direccion: document.getElementById('direccion').value.trim()
    };
    
    if (!formData.dni || !formData.nombres || !formData.apellidos) {
        showNotification('Complete los campos obligatorios', 'warning');
        return;
    }
    
    fetch('/api/conductores/guardar', {
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
            showNotification('Conductor guardado exitosamente', 'success');
            bootstrap.Modal.getInstance(document.getElementById('conductorModal')).hide();
            cargarTodosConductores();
        } else {
            showNotification(data.message || 'Error guardando conductor', 'error');
        }
    })
    .catch(error => {
        showNotification('Error guardando conductor', 'error');
    });
}

function verConductor(id) {
    showNotification('Abriendo perfil del conductor...', 'info');
}

function verInfracciones(id) {
    showNotification('Mostrando infracciones del conductor...', 'info');
}

function getEstadoConductorColor(estado) {
    switch(estado) {
        case 'activo': return 'success';
        case 'suspendido': return 'warning';
        case 'inhabilitado': return 'danger';
        default: return 'secondary';
    }
}
</script><?php /**PATH C:\xampp\htdocs\Login-app\resources\views\dashboard\sections\admin\conductores.blade.php ENDPATH**/ ?>