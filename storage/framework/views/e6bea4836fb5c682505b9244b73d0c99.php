<?php $__env->startSection('title', 'Mantenimiento de Conductores'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-id-card me-2" style="color: #ff8c00;"></i>
                    Mantenimiento de Conductores
                </h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoConductorModal">
                    <i class="fas fa-plus me-2"></i>Nuevo Conductor
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
                    <input type="text" class="form-control" id="filtro_nombre" placeholder="Nombre del conductor">
                </div>
                <div class="col-md-3">
                    <label for="filtro_licencia" class="form-label">N° Licencia</label>
                    <input type="text" class="form-control" id="filtro_licencia" placeholder="Número de licencia">
                </div>
                <div class="col-md-3">
                    <label for="filtro_estado" class="form-label">Estado</label>
                    <select class="form-select" id="filtro_estado">
                        <option value="">Todos</option>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                        <option value="suspendido">Suspendido</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <button class="btn btn-primary me-2" onclick="buscarConductores()">
                        <i class="fas fa-search me-2"></i>Buscar
                    </button>
                    <button class="btn btn-outline-secondary" onclick="limpiarFiltros()">
                        <i class="fas fa-times me-2"></i>Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de conductores -->
    <div class="card">
        <div class="card-header" style="background-color: #fff3e0; border-color: #ff8c00;">
            <h5 class="mb-0" style="color: #ff8c00;">
                <i class="fas fa-list me-2"></i>Lista de Conductores
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead style="background-color: #ff8c00; color: white;">
                        <tr>
                            <th>DNI</th>
                            <th>Nombres y Apellidos</th>
                            <th>N° Licencia</th>
                            <th>Clase/Categoría</th>
                            <th>Vencimiento</th>
                            <th>Empresa</th>
                            <th>Puntos</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="conductoresTableBody">
                        <?php $__empty_1 = true; $__currentLoopData = $conductores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conductor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><strong><?php echo e($conductor->dni); ?></strong></td>
                            <td><?php echo e($conductor->nombre_completo); ?></td>
                            <td><?php echo e($conductor->numero_licencia); ?></td>
                            <td><span class="badge bg-info"><?php echo e($conductor->clase_categoria); ?></span></td>
                            <td class="<?php echo e($conductor->fecha_vencimiento && \Carbon\Carbon::parse($conductor->fecha_vencimiento)->lt(now()) ? 'text-danger' : ''); ?>">
                                <strong>
                                    <?php if($conductor->fecha_vencimiento): ?>
                                        <?php echo e(\Carbon\Carbon::parse($conductor->fecha_vencimiento)->format('d/m/Y')); ?>

                                    <?php else: ?>
                                        No especificada
                                    <?php endif; ?>
                                </strong>
                            </td>
                            <td><?php echo e($conductor->empresa ? $conductor->empresa->razon_social : 'Independiente'); ?></td>
                            <td>
                                <span class="badge bg-<?php echo e($conductor->puntos_acumulados > 10 ? 'danger' : ($conductor->puntos_acumulados > 5 ? 'warning' : 'success')); ?>">
                                    <?php echo e($conductor->puntos_acumulados ?? 0); ?>

                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo e($conductor->estado === 'activo' ? 'success' : 'danger'); ?>">
                                    <?php echo e(ucfirst($conductor->estado)); ?>

                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" title="Ver perfil" onclick="verConductor(<?php echo e($conductor->id); ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="Editar" onclick="editarConductor(<?php echo e($conductor->id); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-<?php echo e($conductor->estado === 'activo' ? 'danger' : 'success'); ?>" 
                                        title="<?php echo e($conductor->estado === 'activo' ? 'Suspender' : 'Activar'); ?>" 
                                        onclick="toggleEstadoConductor(<?php echo e($conductor->id); ?>, '<?php echo e($conductor->estado); ?>')">
                                    <i class="fas fa-<?php echo e($conductor->estado === 'activo' ? 'ban' : 'check'); ?>"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="text-center">No hay conductores registrados</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nuevo Conductor -->
<div class="modal fade" id="nuevoConductorModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #ff8c00; color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Nuevo Conductor
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="nuevoConductorForm">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Datos Personales</h6>
                            <hr>
                            <div class="mb-3">
                                <label for="dni" class="form-label">DNI *</label>
                                <input type="text" class="form-control" id="dni" maxlength="8" required>
                            </div>
                            <div class="mb-3">
                                <label for="nombres" class="form-label">Nombres *</label>
                                <input type="text" class="form-control" id="nombres" required>
                            </div>
                            <div class="mb-3">
                                <label for="apellidos" class="form-label">Apellidos *</label>
                                <input type="text" class="form-control" id="apellidos" required>
                            </div>
                            <div class="mb-3">
                                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento *</label>
                                <input type="date" class="form-control" id="fecha_nacimiento" required>
                            </div>
                            <div class="mb-3">
                                <label for="direccion" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="direccion">
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="distrito" class="form-label">Distrito</label>
                                    <input type="text" class="form-control" id="distrito">
                                </div>
                                <div class="col-md-4">
                                    <label for="provincia" class="form-label">Provincia</label>
                                    <input type="text" class="form-control" id="provincia">
                                </div>
                                <div class="col-md-4">
                                    <label for="departamento" class="form-label">Departamento</label>
                                    <input type="text" class="form-control" id="departamento">
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
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="text-primary">Datos de Licencia</h6>
                            <hr>
                            <div class="mb-3">
                                <label for="numero_licencia" class="form-label">Número de Licencia *</label>
                                <input type="text" class="form-control" id="numero_licencia" required>
                            </div>
                            <div class="mb-3">
                                <label for="clase_categoria" class="form-label">Clase/Categoría *</label>
                                <select class="form-select" id="clase_categoria" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="A-I">A-I</option>
                                    <option value="A-IIa">A-IIa</option>
                                    <option value="A-IIb">A-IIb</option>
                                    <option value="A-IIIa">A-IIIa</option>
                                    <option value="A-IIIb">A-IIIb</option>
                                    <option value="A-IIIc">A-IIIc</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="fecha_expedicion" class="form-label">Fecha de Expedición *</label>
                                <input type="date" class="form-control" id="fecha_expedicion" required>
                            </div>
                            <div class="mb-3">
                                <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento *</label>
                                <input type="date" class="form-control" id="fecha_vencimiento" required>
                            </div>
                            <div class="mb-3">
                                <label for="empresa_id" class="form-label">Empresa</label>
                                <select class="form-select" id="empresa_id">
                                    <option value="">Independiente</option>
                                    <?php $__currentLoopData = $empresas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $empresa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($empresa->id); ?>"><?php echo e($empresa->razon_social); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="estado_licencia" class="form-label">Estado de Licencia</label>
                                <select class="form-select" id="estado_licencia">
                                    <option value="vigente">Vigente</option>
                                    <option value="vencida">Vencida</option>
                                    <option value="suspendida">Suspendida</option>
                                    <option value="cancelada">Cancelada</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="puntos_acumulados" class="form-label">Puntos Acumulados</label>
                                <input type="number" class="form-control" id="puntos_acumulados" value="0" min="0" max="20">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarConductor()">
                    <i class="fas fa-save me-2"></i>Guardar Conductor
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function guardarConductor() {
    // Validar formulario
    const dni = document.getElementById('dni').value;
    const nombres = document.getElementById('nombres').value;
    const apellidos = document.getElementById('apellidos').value;
    const numero_licencia = document.getElementById('numero_licencia').value;
    const fecha_nacimiento = document.getElementById('fecha_nacimiento').value;
    const clase_categoria = document.getElementById('clase_categoria').value;
    const fecha_expedicion = document.getElementById('fecha_expedicion').value;
    const fecha_vencimiento = document.getElementById('fecha_vencimiento').value;
    
    if (!dni || !nombres || !apellidos || !numero_licencia || !fecha_nacimiento || 
        !clase_categoria || !fecha_expedicion || !fecha_vencimiento) {
        showError('Por favor complete todos los campos obligatorios');
        return;
    }
    
    const formData = {
        dni: dni,
        nombres: nombres,
        apellidos: apellidos,
        fecha_nacimiento: fecha_nacimiento,
        direccion: document.getElementById('direccion').value,
        distrito: document.getElementById('distrito').value,
        provincia: document.getElementById('provincia').value,
        departamento: document.getElementById('departamento').value,
        telefono: document.getElementById('telefono').value,
        email: document.getElementById('email').value,
        numero_licencia: numero_licencia,
        clase_categoria: clase_categoria,
        fecha_expedicion: fecha_expedicion,
        fecha_vencimiento: fecha_vencimiento,
        empresa_id: document.getElementById('empresa_id').value,
        estado_licencia: document.getElementById('estado_licencia').value,
        puntos_acumulados: document.getElementById('puntos_acumulados').value,
        estado: 'activo'
    };

    fetch('/admin/conductores', {
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
            showSuccess('Conductor guardado exitosamente');
            const modal = bootstrap.Modal.getInstance(document.getElementById('nuevoConductorModal'));
            modal.hide();
            document.getElementById('nuevoConductorForm').reset();
            location.reload(); // Recargar la página para mostrar el nuevo conductor
        } else {
            if (data.errors) {
                let errorMessage = 'Errores de validación:\n';
                Object.keys(data.errors).forEach(key => {
                    errorMessage += `${key}: ${data.errors[key].join(', ')}\n`;
                });
                showError(errorMessage);
            } else {
                showError(data.message || 'Error al guardar el conductor');
            }
        }
    })
    .catch(error => {
        showError('Error al guardar el conductor');
        console.error('Error:', error);
    });
}

// Funciones de utilidad para mostrar mensajes
function showSuccess(message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Éxito',
            text: message,
            icon: 'success',
            confirmButtonText: 'OK'
        });
    } else {
        alert(message);
    }
}

function showError(message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Error',
            text: message,
            icon: 'error',
            confirmButtonText: 'OK'
        });
    } else {
        alert(message);
    }
}

// Función para ver conductor
function verConductor(id) {
    fetch(`/admin/conductores/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const conductor = data.conductor;
                const content = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Datos Personales</h6>
                            <hr>
                            <p><strong>DNI:</strong> ${conductor.dni}</p>
                            <p><strong>Nombres:</strong> ${conductor.nombres}</p>
                            <p><strong>Apellidos:</strong> ${conductor.apellidos}</p>
                            <p><strong>Fecha de Nacimiento:</strong> ${conductor.fecha_nacimiento}</p>
                            <p><strong>Dirección:</strong> ${conductor.direccion || 'No especificada'}</p>
                            <p><strong>Distrito:</strong> ${conductor.distrito || 'No especificado'}</p>
                            <p><strong>Provincia:</strong> ${conductor.provincia || 'No especificada'}</p>
                            <p><strong>Departamento:</strong> ${conductor.departamento || 'No especificado'}</p>
                            <p><strong>Teléfono:</strong> ${conductor.telefono || 'No especificado'}</p>
                            <p><strong>Email:</strong> ${conductor.email || 'No especificado'}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">Datos de Licencia</h6>
                            <hr>
                            <p><strong>Número de Licencia:</strong> ${conductor.numero_licencia}</p>
                            <p><strong>Clase/Categoría:</strong> ${conductor.clase_categoria}</p>
                            <p><strong>Fecha de Expedición:</strong> ${conductor.fecha_expedicion}</p>
                            <p><strong>Fecha de Vencimiento:</strong> ${conductor.fecha_vencimiento}</p>
                            <p><strong>Estado de Licencia:</strong> ${conductor.estado_licencia}</p>
                            <p><strong>Empresa:</strong> ${conductor.empresa ? conductor.empresa.razon_social : 'Independiente'}</p>
                            <p><strong>Puntos Acumulados:</strong> ${conductor.puntos_acumulados || 0}</p>
                            <p><strong>Estado:</strong> 
                                <span class="badge bg-${conductor.estado === 'activo' ? 'success' : 'danger'}">
                                    ${conductor.estado}
                                </span>
                            </p>
                        </div>
                    </div>
                `;
                document.getElementById('verConductorContent').innerHTML = content;
                new bootstrap.Modal(document.getElementById('verConductorModal')).show();
            } else {
                showError('Error al cargar los datos del conductor');
            }
        })
        .catch(error => {
            showError('Error al cargar los datos del conductor');
        });
}

// Función para editar conductor
function editarConductor(id) {
    fetch(`/admin/conductores/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const conductor = data.conductor;
                
                // Llenar el formulario de edición
                document.getElementById('edit_conductor_id').value = conductor.id;
                document.getElementById('edit_dni').value = conductor.dni;
                document.getElementById('edit_nombres').value = conductor.nombres;
                document.getElementById('edit_apellidos').value = conductor.apellidos;
                document.getElementById('edit_fecha_nacimiento').value = conductor.fecha_nacimiento;
                document.getElementById('edit_direccion').value = conductor.direccion || '';
                document.getElementById('edit_distrito').value = conductor.distrito || '';
                document.getElementById('edit_provincia').value = conductor.provincia || '';
                document.getElementById('edit_departamento').value = conductor.departamento || '';
                document.getElementById('edit_telefono').value = conductor.telefono || '';
                document.getElementById('edit_email').value = conductor.email || '';
                document.getElementById('edit_numero_licencia').value = conductor.numero_licencia;
                document.getElementById('edit_clase_categoria').value = conductor.clase_categoria;
                document.getElementById('edit_fecha_expedicion').value = conductor.fecha_expedicion;
                document.getElementById('edit_fecha_vencimiento').value = conductor.fecha_vencimiento;
                document.getElementById('edit_empresa_id').value = conductor.empresa_id || '';
                document.getElementById('edit_estado_licencia').value = conductor.estado_licencia;
                document.getElementById('edit_puntos_acumulados').value = conductor.puntos_acumulados || 0;
                
                new bootstrap.Modal(document.getElementById('editarConductorModal')).show();
            } else {
                showError('Error al cargar los datos del conductor');
            }
        })
        .catch(error => {
            showError('Error al cargar los datos del conductor');
        });
}

// Función para actualizar conductor
function actualizarConductor() {
    const conductorId = document.getElementById('edit_conductor_id').value;
    
    const formData = {
        dni: document.getElementById('edit_dni').value,
        nombres: document.getElementById('edit_nombres').value,
        apellidos: document.getElementById('edit_apellidos').value,
        fecha_nacimiento: document.getElementById('edit_fecha_nacimiento').value,
        direccion: document.getElementById('edit_direccion').value,
        distrito: document.getElementById('edit_distrito').value,
        provincia: document.getElementById('edit_provincia').value,
        departamento: document.getElementById('edit_departamento').value,
        telefono: document.getElementById('edit_telefono').value,
        email: document.getElementById('edit_email').value,
        numero_licencia: document.getElementById('edit_numero_licencia').value,
        clase_categoria: document.getElementById('edit_clase_categoria').value,
        fecha_expedicion: document.getElementById('edit_fecha_expedicion').value,
        fecha_vencimiento: document.getElementById('edit_fecha_vencimiento').value,
        empresa_id: document.getElementById('edit_empresa_id').value,
        estado_licencia: document.getElementById('edit_estado_licencia').value,
        puntos_acumulados: document.getElementById('edit_puntos_acumulados').value
    };

    fetch(`/admin/conductores/${conductorId}`, {
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
            showSuccess('Conductor actualizado exitosamente');
            const modal = bootstrap.Modal.getInstance(document.getElementById('editarConductorModal'));
            modal.hide();
            location.reload(); // Recargar la página para mostrar los cambios
        } else {
            if (data.errors) {
                let errorMessage = 'Errores de validación:\n';
                Object.keys(data.errors).forEach(key => {
                    errorMessage += `${key}: ${data.errors[key].join(', ')}\n`;
                });
                showError(errorMessage);
            } else {
                showError(data.message || 'Error al actualizar el conductor');
            }
        }
    })
    .catch(error => {
        showError('Error al actualizar el conductor');
    });
}

// Función para cambiar estado del conductor
function toggleEstadoConductor(id, estadoActual) {
    const nuevoEstado = estadoActual === 'activo' ? 'suspendido' : 'activo';
    const accion = nuevoEstado === 'activo' ? 'activar' : 'suspender';
    
    Swal.fire({
        title: '¿Estás seguro?',
        text: `¿Quieres ${accion} este conductor?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: `Sí, ${accion}`,
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/conductores/${id}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess(data.message);
                    location.reload(); // Recargar la página para mostrar los cambios
                } else {
                    showError(data.message || 'Error al cambiar el estado del conductor');
                }
            })
            .catch(error => {
                showError('Error al cambiar el estado del conductor');
            });
        }
    });
}

// Función para buscar conductores
function buscarConductores() {
    const filtros = {
        dni: document.getElementById('filtro_dni').value,
        nombre: document.getElementById('filtro_nombre').value,
        licencia: document.getElementById('filtro_licencia').value,
        estado: document.getElementById('filtro_estado').value
    };

    const queryString = new URLSearchParams(filtros).toString();
    
    fetch(`/admin/conductores/search?${queryString}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                actualizarTablaConductores(data.conductores);
            } else {
                showError('Error al buscar conductores');
            }
        })
        .catch(error => {
            showError('Error al buscar conductores');
        });
}

// Función para limpiar filtros
function limpiarFiltros() {
    document.getElementById('filtro_dni').value = '';
    document.getElementById('filtro_nombre').value = '';
    document.getElementById('filtro_licencia').value = '';
    document.getElementById('filtro_estado').value = '';
    location.reload(); // Recargar la página para mostrar todos los conductores
}

// Función para actualizar la tabla de conductores
function actualizarTablaConductores(conductores) {
    const tbody = document.getElementById('conductoresTableBody');
    
    if (conductores.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center">No se encontraron conductores</td></tr>';
        return;
    }
    
    tbody.innerHTML = conductores.map(conductor => `
        <tr>
            <td><strong>${conductor.dni}</strong></td>
            <td>${conductor.nombres} ${conductor.apellidos}</td>
            <td>${conductor.numero_licencia}</td>
            <td><span class="badge bg-info">${conductor.clase_categoria}</span></td>
            <td class="${new Date(conductor.fecha_vencimiento) < new Date() ? 'text-danger' : ''}">
                <strong>${new Date(conductor.fecha_vencimiento).toLocaleDateString('es-ES')}</strong>
            </td>
            <td>${conductor.empresa ? conductor.empresa.razon_social : 'Independiente'}</td>
            <td>
                <span class="badge bg-${conductor.puntos_acumulados > 10 ? 'danger' : (conductor.puntos_acumulados > 5 ? 'warning' : 'success')}">
                    ${conductor.puntos_acumulados || 0}
                </span>
            </td>
            <td>
                <span class="badge bg-${conductor.estado === 'activo' ? 'success' : 'danger'}">
                    ${conductor.estado.charAt(0).toUpperCase() + conductor.estado.slice(1)}
                </span>
            </td>
            <td>
                <button class="btn btn-sm btn-outline-primary" title="Ver perfil" onclick="verConductor(${conductor.id})">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-sm btn-outline-success" title="Editar" onclick="editarConductor(${conductor.id})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-outline-${conductor.estado === 'activo' ? 'danger' : 'success'}" 
                        title="${conductor.estado === 'activo' ? 'Suspender' : 'Activar'}" 
                        onclick="toggleEstadoConductor(${conductor.id}, '${conductor.estado}')">
                    <i class="fas fa-${conductor.estado === 'activo' ? 'ban' : 'check'}"></i>
                </button>
            </td>
        </tr>
    `).join('');
}
</script>

<!-- Modal Ver Conductor -->
<div class="modal fade" id="verConductorModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #ff8c00; color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-eye me-2"></i>Datos del Conductor
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="verConductorContent">
                <!-- Contenido se carga dinámicamente -->
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Conductor -->
<div class="modal fade" id="editarConductorModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #ff8c00; color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Editar Conductor
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editarConductorForm">
                    <input type="hidden" id="edit_conductor_id">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Datos Personales</h6>
                            <hr>
                            <div class="mb-3">
                                <label for="edit_dni" class="form-label">DNI *</label>
                                <input type="text" class="form-control" id="edit_dni" maxlength="8" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_nombres" class="form-label">Nombres *</label>
                                <input type="text" class="form-control" id="edit_nombres" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_apellidos" class="form-label">Apellidos *</label>
                                <input type="text" class="form-control" id="edit_apellidos" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_fecha_nacimiento" class="form-label">Fecha de Nacimiento *</label>
                                <input type="date" class="form-control" id="edit_fecha_nacimiento" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_direccion" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="edit_direccion">
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="edit_distrito" class="form-label">Distrito</label>
                                    <input type="text" class="form-control" id="edit_distrito">
                                </div>
                                <div class="col-md-4">
                                    <label for="edit_provincia" class="form-label">Provincia</label>
                                    <input type="text" class="form-control" id="edit_provincia">
                                </div>
                                <div class="col-md-4">
                                    <label for="edit_departamento" class="form-label">Departamento</label>
                                    <input type="text" class="form-control" id="edit_departamento">
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
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="text-primary">Datos de Licencia</h6>
                            <hr>
                            <div class="mb-3">
                                <label for="edit_numero_licencia" class="form-label">Número de Licencia *</label>
                                <input type="text" class="form-control" id="edit_numero_licencia" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_clase_categoria" class="form-label">Clase/Categoría *</label>
                                <select class="form-select" id="edit_clase_categoria" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="A-I">A-I</option>
                                    <option value="A-IIa">A-IIa</option>
                                    <option value="A-IIb">A-IIb</option>
                                    <option value="A-IIIa">A-IIIa</option>
                                    <option value="A-IIIb">A-IIIb</option>
                                    <option value="A-IIIc">A-IIIc</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="edit_fecha_expedicion" class="form-label">Fecha de Expedición *</label>
                                <input type="date" class="form-control" id="edit_fecha_expedicion" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_fecha_vencimiento" class="form-label">Fecha de Vencimiento *</label>
                                <input type="date" class="form-control" id="edit_fecha_vencimiento" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_empresa_id" class="form-label">Empresa</label>
                                <select class="form-select" id="edit_empresa_id">
                                    <option value="">Independiente</option>
                                    <?php $__currentLoopData = $empresas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $empresa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($empresa->id); ?>"><?php echo e($empresa->razon_social); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="edit_estado_licencia" class="form-label">Estado de Licencia</label>
                                <select class="form-select" id="edit_estado_licencia">
                                    <option value="vigente">Vigente</option>
                                    <option value="vencida">Vencida</option>
                                    <option value="suspendida">Suspendida</option>
                                    <option value="cancelada">Cancelada</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="edit_puntos_acumulados" class="form-label">Puntos Acumulados</label>
                                <input type="number" class="form-control" id="edit_puntos_acumulados" value="0" min="0" max="20">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="actualizarConductor()">
                    <i class="fas fa-save me-2"></i>Actualizar Conductor
                </button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Login-app\resources\views\administrador_restore\mantenimiento\conductor.blade.php ENDPATH**/ ?>