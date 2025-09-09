<?php $__env->startSection('title', 'Mantenimiento de Fiscales'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-user-tie me-2" style="color: #ff8c00;"></i>
                    Mantenimiento de Fiscales
                </h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoFiscalModal">
                    <i class="fas fa-plus me-2"></i>Nuevo Fiscal
                </button>
            </div>
        </div>
    </div>

    <!-- Filtros de búsqueda -->
    <div class="card mb-4" style="border-color: #ff8c00;">
        <div class="card-header" style="background-color: #ff8c00; color: white;">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label for="filtro_dni" class="form-label">DNI</label>
                    <input type="text" class="form-control" id="filtro_dni" placeholder="12345678">
                </div>
                <div class="col-md-3">
                    <label for="filtro_nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="filtro_nombre" placeholder="Nombre del fiscal">
                </div>
                <div class="col-md-3">
                    <label for="filtro_estado" class="form-label">Estado</label>
                    <select class="form-select" id="filtro_estado">
                        <option value="">Todos</option>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                        <option value="licencia">En Licencia</option>
                        <option value="vacaciones">Vacaciones</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtro_zona" class="form-label">Zona Asignada</label>
                    <input type="text" class="form-control" id="filtro_zona" placeholder="Zona">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <button class="btn btn-primary me-2" onclick="buscarInspectores()">
                        <i class="fas fa-search me-2"></i>Buscar
                    </button>
                    <button class="btn btn-outline-secondary" onclick="limpiarFiltros()">
                        <i class="fas fa-times me-2"></i>Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de fiscales -->
    <div class="card">
        <div class="card-header" style="background-color: #fff3e0; border-color: #ff8c00;">
            <h5 class="mb-0" style="color: #ff8c00;">
                <i class="fas fa-list me-2"></i>Lista de Fiscales
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead style="background-color: #ff8c00; color: white;">
                        <tr>
                            <th>Código</th>
                            <th>DNI</th>
                            <th>Nombres y Apellidos</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Zona Asignada</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="inspectoresTableBody">
                        <?php $__empty_1 = true; $__currentLoopData = $inspectores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inspector): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><strong><?php echo e($inspector->codigo_inspector); ?></strong></td>
                            <td><?php echo e($inspector->dni); ?></td>
                            <td><?php echo e($inspector->nombre_completo); ?></td>
                            <td><?php echo e($inspector->telefono); ?></td>
                            <td><?php echo e($inspector->email); ?></td>
                            <td><?php echo e($inspector->zona_asignada); ?></td>
                            <td>
                                <span class="badge bg-<?php echo e($inspector->estado === 'activo' ? 'success' : ($inspector->estado === 'licencia' ? 'warning' : 'info')); ?>">
                                    <?php echo e(ucfirst($inspector->estado)); ?>

                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" title="Ver perfil" onclick="verInspector(<?php echo e($inspector->id); ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="Editar" onclick="editarInspector(<?php echo e($inspector->id); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-<?php echo e($inspector->estado === 'activo' ? 'warning' : 'success'); ?>" 
                                        title="<?php echo e($inspector->estado === 'activo' ? 'Desactivar' : 'Activar'); ?>" 
                                        onclick="toggleEstadoInspector(<?php echo e($inspector->id); ?>, '<?php echo e($inspector->estado); ?>')">
                                    <i class="fas fa-toggle-<?php echo e($inspector->estado === 'activo' ? 'on' : 'off'); ?>"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center">No hay inspectores registrados</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nuevo Fiscal -->
<div class="modal fade" id="nuevoFiscalModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #ff8c00; color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Nuevo Fiscal
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="nuevoFiscalForm">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="dni" class="form-label">DNI *</label>
                            <input type="text" class="form-control" id="dni" maxlength="8" required>
                        </div>
                        <div class="col-md-6">
                            <label for="codigo_inspector" class="form-label">Código de Inspector *</label>
                            <input type="text" class="form-control" id="codigo_inspector" required>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="nombres" class="form-label">Nombres *</label>
                            <input type="text" class="form-control" id="nombres" required>
                        </div>
                        <div class="col-md-6">
                            <label for="apellidos" class="form-label">Apellidos *</label>
                            <input type="text" class="form-control" id="apellidos" required>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="fecha_ingreso" class="form-label">Fecha de Ingreso *</label>
                            <input type="date" class="form-control" id="fecha_ingreso" required>
                        </div>
                        <div class="col-md-6">
                            <label for="zona_asignada" class="form-label">Zona Asignada</label>
                            <input type="text" class="form-control" id="zona_asignada">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <label for="observaciones" class="form-label">Observaciones</label>
                            <textarea class="form-control" id="observaciones" rows="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarFiscal()">
                    <i class="fas fa-save me-2"></i>Guardar Fiscal
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ver Inspector -->
<div class="modal fade" id="verInspectorModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #ff8c00; color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-eye me-2"></i>Datos del Inspector
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="verInspectorContent">
                <!-- Contenido se carga dinámicamente -->
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Inspector -->
<div class="modal fade" id="editarInspectorModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #ff8c00; color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Editar Inspector
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editarInspectorForm">
                    <input type="hidden" id="edit_inspector_id">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="edit_dni" class="form-label">DNI *</label>
                            <input type="text" class="form-control" id="edit_dni" maxlength="8" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_codigo_inspector" class="form-label">Código de Inspector *</label>
                            <input type="text" class="form-control" id="edit_codigo_inspector" required>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="edit_nombres" class="form-label">Nombres *</label>
                            <input type="text" class="form-control" id="edit_nombres" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_apellidos" class="form-label">Apellidos *</label>
                            <input type="text" class="form-control" id="edit_apellidos" required>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="edit_telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="edit_telefono">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="edit_fecha_ingreso" class="form-label">Fecha de Ingreso *</label>
                            <input type="date" class="form-control" id="edit_fecha_ingreso" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_zona_asignada" class="form-label">Zona Asignada</label>
                            <input type="text" class="form-control" id="edit_zona_asignada">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <label for="edit_observaciones" class="form-label">Observaciones</label>
                            <textarea class="form-control" id="edit_observaciones" rows="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="actualizarInspector()">
                    <i class="fas fa-save me-2"></i>Actualizar Inspector
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function guardarFiscal() {
    // Validar formulario
    const dni = document.getElementById('dni').value;
    const codigo = document.getElementById('codigo_inspector').value;
    const nombres = document.getElementById('nombres').value;
    const apellidos = document.getElementById('apellidos').value;
    const fecha_ingreso = document.getElementById('fecha_ingreso').value;
    
    if (!dni || !codigo || !nombres || !apellidos || !fecha_ingreso) {
        showError('Por favor complete todos los campos obligatorios');
        return;
    }
    
    const formData = {
        dni: dni,
        codigo_inspector: codigo,
        nombres: nombres,
        apellidos: apellidos,
        telefono: document.getElementById('telefono').value,
        email: document.getElementById('email').value,
        fecha_ingreso: fecha_ingreso,
        zona_asignada: document.getElementById('zona_asignada').value,
        observaciones: document.getElementById('observaciones').value,
        estado: 'activo'
    };

    fetch('/admin/inspectores', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess('Inspector guardado exitosamente');
            const modal = bootstrap.Modal.getInstance(document.getElementById('nuevoFiscalModal'));
            modal.hide();
            document.getElementById('nuevoFiscalForm').reset();
            location.reload();
        } else {
            if (data.errors) {
                let errorMessage = 'Errores de validación:\n';
                Object.keys(data.errors).forEach(key => {
                    errorMessage += `${key}: ${data.errors[key].join(', ')}\n`;
                });
                showError(errorMessage);
            } else {
                showError(data.message || 'Error al guardar el inspector');
            }
        }
    })
    .catch(error => {
        showError('Error al guardar el inspector');
    });
}

// Función para ver inspector
function verInspector(id) {
    fetch(`/admin/inspectores/${id}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const inspector = data.inspector;
            const content = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Información Personal</h6>
                        <p><strong>DNI:</strong> ${inspector.dni}</p>
                        <p><strong>Nombre:</strong> ${inspector.nombres} ${inspector.apellidos}</p>
                        <p><strong>Código:</strong> ${inspector.codigo_inspector}</p>
                        <p><strong>Teléfono:</strong> ${inspector.telefono || 'No especificado'}</p>
                        <p><strong>Email:</strong> ${inspector.email || 'No especificado'}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Información Laboral</h6>
                        <p><strong>Fecha de Ingreso:</strong> ${new Date(inspector.fecha_ingreso).toLocaleDateString()}</p>
                        <p><strong>Zona Asignada:</strong> ${inspector.zona_asignada || 'No asignada'}</p>
                        <p><strong>Estado:</strong> <span class="badge bg-${inspector.estado === 'activo' ? 'success' : 'warning'}">${inspector.estado}</span></p>
                        <p><strong>Observaciones:</strong> ${inspector.observaciones || 'Ninguna'}</p>
                    </div>
                </div>
            `;
            document.getElementById('verInspectorContent').innerHTML = content;
            new bootstrap.Modal(document.getElementById('verInspectorModal')).show();
        }
    });
}

// Función para editar inspector
function editarInspector(id) {
    fetch(`/admin/inspectores/${id}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const inspector = data.inspector;
            document.getElementById('edit_inspector_id').value = inspector.id;
            document.getElementById('edit_dni').value = inspector.dni;
            document.getElementById('edit_codigo_inspector').value = inspector.codigo_inspector;
            document.getElementById('edit_nombres').value = inspector.nombres;
            document.getElementById('edit_apellidos').value = inspector.apellidos;
            document.getElementById('edit_telefono').value = inspector.telefono || '';
            document.getElementById('edit_email').value = inspector.email || '';
            document.getElementById('edit_fecha_ingreso').value = inspector.fecha_ingreso;
            document.getElementById('edit_zona_asignada').value = inspector.zona_asignada || '';
            document.getElementById('edit_observaciones').value = inspector.observaciones || '';
            
            new bootstrap.Modal(document.getElementById('editarInspectorModal')).show();
        }
    });
}

// Función para actualizar inspector
function actualizarInspector() {
    const id = document.getElementById('edit_inspector_id').value;
    const formData = {
        dni: document.getElementById('edit_dni').value,
        codigo_inspector: document.getElementById('edit_codigo_inspector').value,
        nombres: document.getElementById('edit_nombres').value,
        apellidos: document.getElementById('edit_apellidos').value,
        telefono: document.getElementById('edit_telefono').value,
        email: document.getElementById('edit_email').value,
        fecha_ingreso: document.getElementById('edit_fecha_ingreso').value,
        zona_asignada: document.getElementById('edit_zona_asignada').value,
        observaciones: document.getElementById('edit_observaciones').value
    };

    fetch(`/admin/inspectores/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess('Inspector actualizado exitosamente');
            bootstrap.Modal.getInstance(document.getElementById('editarInspectorModal')).hide();
            location.reload();
        } else {
            showError(data.message || 'Error al actualizar el inspector');
        }
    });
}

// Función para toggle estado
function toggleEstadoInspector(id, estadoActual) {
    const nuevoEstado = estadoActual === 'activo' ? 'inactivo' : 'activo';
    const accion = nuevoEstado === 'activo' ? 'activar' : 'desactivar';
    
    if (confirm(`¿Está seguro que desea ${accion} este inspector?`)) {
        fetch(`/admin/inspectores/${id}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess('Estado actualizado exitosamente');
                location.reload();
            } else {
                showError(data.message || 'Error al cambiar el estado');
            }
        });
    }
}

// Función para buscar inspectores
function buscarInspectores() {
    const filtros = {
        dni: document.getElementById('filtro_dni').value,
        nombre: document.getElementById('filtro_nombre').value,
        estado: document.getElementById('filtro_estado').value,
        zona: document.getElementById('filtro_zona').value
    };

    const params = new URLSearchParams();
    Object.keys(filtros).forEach(key => {
        if (filtros[key]) params.append(key, filtros[key]);
    });

    fetch(`/admin/inspectores/search?${params.toString()}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            actualizarTablaInspectores(data.inspectores);
        }
    });
}

// Función para limpiar filtros
function limpiarFiltros() {
    document.getElementById('filtro_dni').value = '';
    document.getElementById('filtro_nombre').value = '';
    document.getElementById('filtro_estado').value = '';
    document.getElementById('filtro_zona').value = '';
    location.reload();
}

// Función para actualizar tabla
function actualizarTablaInspectores(inspectores) {
    const tbody = document.getElementById('inspectoresTableBody');
    tbody.innerHTML = '';
    
    if (inspectores.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center">No se encontraron inspectores</td></tr>';
        return;
    }
    
    inspectores.forEach(inspector => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><strong>${inspector.codigo_inspector}</strong></td>
            <td>${inspector.dni}</td>
            <td>${inspector.nombres} ${inspector.apellidos}</td>
            <td>${inspector.telefono || ''}</td>
            <td>${inspector.email || ''}</td>
            <td>${inspector.zona_asignada || ''}</td>
            <td><span class="badge bg-${inspector.estado === 'activo' ? 'success' : 'warning'}">${inspector.estado}</span></td>
            <td>
                <button class="btn btn-sm btn-outline-primary" title="Ver perfil" onclick="verInspector(${inspector.id})">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-sm btn-outline-success" title="Editar" onclick="editarInspector(${inspector.id})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-outline-${inspector.estado === 'activo' ? 'warning' : 'success'}" 
                        title="${inspector.estado === 'activo' ? 'Desactivar' : 'Activar'}" 
                        onclick="toggleEstadoInspector(${inspector.id}, '${inspector.estado}')">
                    <i class="fas fa-toggle-${inspector.estado === 'activo' ? 'on' : 'off'}"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Login-app\resources\views/administrador/mantenimiento/fiscal.blade.php ENDPATH**/ ?>