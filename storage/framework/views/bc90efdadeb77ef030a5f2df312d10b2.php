<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-user-check"></i> Aprobación de Usuarios</h4>
                <button class="btn btn-secondary" onclick="hideModules()">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </button>
            </div>
            <hr>
        </div>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Pendientes</h6>
                            <h2 class="mb-0">5</h2>
                        </div>
                        <div>
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Aprobados Hoy</h6>
                            <h2 class="mb-0">12</h2>
                        </div>
                        <div>
                            <i class="fas fa-check fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Rechazados Hoy</h6>
                            <h2 class="mb-0">2</h2>
                        </div>
                        <div>
                            <i class="fas fa-times fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Usuarios</h6>
                            <h2 class="mb-0">47</h2>
                        </div>
                        <div>
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones masivas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-tools"></i> Acciones Masivas</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-success" onclick="aprobarTodosPendientes()">
                            <i class="fas fa-check-double"></i> Aprobar Todos Pendientes
                        </button>
                        <button class="btn btn-warning" onclick="notificarPendientes()">
                            <i class="fas fa-bell"></i> Notificar Pendientes
                        </button>
                        <button class="btn btn-info" onclick="exportarReporte()">
                            <i class="fas fa-file-excel"></i> Exportar Reporte
                        </button>
                        <button class="btn btn-primary" onclick="actualizarEstados()">
                            <i class="fas fa-sync"></i> Actualizar Estados
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Solicitudes pendientes -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-list"></i> Solicitudes Pendientes de Aprobación</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>
                                        <input type="checkbox" id="select-all" onchange="toggleSelectAll()">
                                    </th>
                                    <th>Fecha Solicitud</th>
                                    <th>Nombre Completo</th>
                                    <th>Email</th>
                                    <th>DNI</th>
                                    <th>Rol Solicitado</th>
                                    <th>Teléfono</th>
                                    <th>Documentos</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="usuario-check" value="101">
                                    </td>
                                    <td>12/08/2024 08:30</td>
                                    <td>
                                        <strong>Luis Alberto Mendoza</strong>
                                        <br><small class="text-muted">Nuevo usuario</small>
                                    </td>
                                    <td>
                                        luis.mendoza@email.com
                                        <br><small class="text-success">✓ Verificado</small>
                                    </td>
                                    <td>46027897</td>
                                    <td>
                                        <span class="badge bg-warning">Fiscalizador</span>
                                    </td>
                                    <td>987654321</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="verDocumentos(101)">
                                            <i class="fas fa-file-pdf"></i> Ver (3)
                                        </button>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-success" onclick="aprobarUsuario(101, 'Luis Alberto Mendoza')">
                                                <i class="fas fa-check"></i> Aprobar
                                            </button>
                                            <button class="btn btn-danger" onclick="rechazarUsuario(101, 'Luis Alberto Mendoza')">
                                                <i class="fas fa-times"></i> Rechazar
                                            </button>
                                            <button class="btn btn-info" onclick="verDetalles(101)">
                                                <i class="fas fa-eye"></i> Detalles
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="usuario-check" value="102">
                                    </td>
                                    <td>12/08/2024 10:15</td>
                                    <td>
                                        <strong>Carmen Rosa Quispe</strong>
                                        <br><small class="text-muted">Nuevo usuario</small>
                                    </td>
                                    <td>
                                        carmen.quispe@email.com
                                        <br><small class="text-warning">⚠ Pendiente verificación</small>
                                    </td>
                                    <td>60015091</td>
                                    <td>
                                        <span class="badge bg-info">Ventanilla</span>
                                    </td>
                                    <td>999888777</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="verDocumentos(102)">
                                            <i class="fas fa-file-pdf"></i> Ver (2)
                                        </button>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-success" onclick="aprobarUsuario(102, 'Carmen Rosa Quispe')">
                                                <i class="fas fa-check"></i> Aprobar
                                            </button>
                                            <button class="btn btn-danger" onclick="rechazarUsuario(102, 'Carmen Rosa Quispe')">
                                                <i class="fas fa-times"></i> Rechazar
                                            </button>
                                            <button class="btn btn-info" onclick="verDetalles(102)">
                                                <i class="fas fa-eye"></i> Detalles
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="usuario-check" value="103">
                                    </td>
                                    <td>12/08/2024 14:20</td>
                                    <td>
                                        <strong>Roberto Carlos Silva</strong>
                                        <br><small class="text-muted">Cambio de rol</small>
                                    </td>
                                    <td>
                                        roberto.silva@drtc.gob.pe
                                        <br><small class="text-success">✓ Usuario existente</small>
                                    </td>
                                    <td>60998016</td>
                                    <td>
                                        <span class="badge bg-secondary">Inspector → Fiscalizador</span>
                                    </td>
                                    <td>988777666</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="verDocumentos(103)">
                                            <i class="fas fa-file-pdf"></i> Ver (1)
                                        </button>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-success" onclick="aprobarUsuario(103, 'Roberto Carlos Silva')">
                                                <i class="fas fa-check"></i> Aprobar
                                            </button>
                                            <button class="btn btn-danger" onclick="rechazarUsuario(103, 'Roberto Carlos Silva')">
                                                <i class="fas fa-times"></i> Rechazar
                                            </button>
                                            <button class="btn btn-info" onclick="verDetalles(103)">
                                                <i class="fas fa-eye"></i> Detalles
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones seleccionadas -->
    <div class="row mt-3" id="acciones-seleccionadas" style="display: none;">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-tasks"></i> Acciones para Usuarios Seleccionados</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <button class="btn btn-success" onclick="aprobarSeleccionados()">
                            <i class="fas fa-check"></i> Aprobar Seleccionados
                        </button>
                        <button class="btn btn-danger" onclick="rechazarSeleccionados()">
                            <i class="fas fa-times"></i> Rechazar Seleccionados
                        </button>
                        <button class="btn btn-warning" onclick="notificarSeleccionados()">
                            <i class="fas fa-envelope"></i> Enviar Notificación
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver detalles del usuario -->
<div class="modal fade" id="modalDetallesUsuario" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user"></i> Detalles del Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalles-usuario-contenido">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="aprobarDesdeModal()">
                    <i class="fas fa-check"></i> Aprobar
                </button>
                <button type="button" class="btn btn-danger" onclick="rechazarDesdeModal()">
                    <i class="fas fa-times"></i> Rechazar
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
let usuarioActualModal = null;
let usuariosSeleccionados = [];

// Funciones principales
function aprobarUsuario(id, nombre) {
    if (confirm(`¿Aprobar la solicitud de ${nombre}?`)) {
        console.log(`Aprobando usuario ID: ${id}`);
        alert(`Usuario ${nombre} aprobado exitosamente`);
        // Aquí se haría la aprobación con AJAX
        actualizarEstadisticas();
    }
}

function rechazarUsuario(id, nombre) {
    const motivo = prompt(`¿Motivo del rechazo para ${nombre}?`);
    if (motivo) {
        console.log(`Rechazando usuario ID: ${id}, motivo: ${motivo}`);
        alert(`Usuario ${nombre} rechazado: ${motivo}`);
        // Aquí se haría el rechazo con AJAX
        actualizarEstadisticas();
    }
}

function verDetalles(id) {
    usuarioActualModal = id;
    
    // Simular carga de datos del usuario
    const detalles = `
        <div class="row">
            <div class="col-md-6">
                <h6>Información Personal</h6>
                <p><strong>ID:</strong> ${id}</p>
                <p><strong>Nombre:</strong> Usuario ${id}</p>
                <p><strong>Email:</strong> usuario${id}@email.com</p>
                <p><strong>DNI:</strong> 12345678</p>
                <p><strong>Teléfono:</strong> 987654321</p>
                <p><strong>Fecha de nacimiento:</strong> 15/05/1985</p>
            </div>
            <div class="col-md-6">
                <h6>Información de la Solicitud</h6>
                <p><strong>Fecha solicitud:</strong> 12/08/2024</p>
                <p><strong>Rol solicitado:</strong> Fiscalizador</p>
                <p><strong>Estado:</strong> Pendiente</p>
                <p><strong>IP de registro:</strong> 192.168.1.100</p>
                <p><strong>Navegador:</strong> Chrome 115.0</p>
            </div>
        </div>
        <hr>
        <h6>Documentos Adjuntos</h6>
        <div class="list-group">
            <a href="#" class="list-group-item list-group-item-action">
                <i class="fas fa-file-pdf text-danger"></i> Copia de DNI
            </a>
            <a href="#" class="list-group-item list-group-item-action">
                <i class="fas fa-file-pdf text-danger"></i> Certificado de estudios
            </a>
            <a href="#" class="list-group-item list-group-item-action">
                <i class="fas fa-file-pdf text-danger"></i> Carta de presentación
            </a>
        </div>
    `;
    
    document.getElementById('detalles-usuario-contenido').innerHTML = detalles;
    
    // Abrir modal (Bootstrap 5)
    const modal = new bootstrap.Modal(document.getElementById('modalDetallesUsuario'));
    modal.show();
}

function verDocumentos(id) {
    alert(`Abriendo documentos del usuario ID: ${id}`);
    // Aquí se abriría una ventana o modal con los documentos
}

// Funciones de selección masiva
function toggleSelectAll() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('input[name="usuario-check"]');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    actualizarSeleccionados();
}

function actualizarSeleccionados() {
    const checkboxes = document.querySelectorAll('input[name="usuario-check"]:checked');
    usuariosSeleccionados = Array.from(checkboxes).map(cb => cb.value);
    
    const accionesDiv = document.getElementById('acciones-seleccionadas');
    if (usuariosSeleccionados.length > 0) {
        accionesDiv.style.display = 'block';
    } else {
        accionesDiv.style.display = 'none';
    }
}

// Event listeners para checkboxes individuales
document.addEventListener('change', function(e) {
    if (e.target.name === 'usuario-check') {
        actualizarSeleccionados();
    }
});

// Funciones masivas
function aprobarSeleccionados() {
    if (usuariosSeleccionados.length === 0) {
        alert('Seleccione al menos un usuario');
        return;
    }
    
    if (confirm(`¿Aprobar ${usuariosSeleccionados.length} usuarios seleccionados?`)) {
        console.log('Aprobando usuarios:', usuariosSeleccionados);
        alert(`${usuariosSeleccionados.length} usuarios aprobados`);
        usuariosSeleccionados = [];
        actualizarSeleccionados();
        actualizarEstadisticas();
    }
}

function rechazarSeleccionados() {
    if (usuariosSeleccionados.length === 0) {
        alert('Seleccione al menos un usuario');
        return;
    }
    
    const motivo = prompt(`¿Motivo del rechazo para ${usuariosSeleccionados.length} usuarios?`);
    if (motivo) {
        console.log('Rechazando usuarios:', usuariosSeleccionados, 'Motivo:', motivo);
        alert(`${usuariosSeleccionados.length} usuarios rechazados`);
        usuariosSeleccionados = [];
        actualizarSeleccionados();
        actualizarEstadisticas();
    }
}

function aprobarTodosPendientes() {
    if (confirm('¿Aprobar TODOS los usuarios pendientes?')) {
        alert('Todos los usuarios pendientes han sido aprobados');
        actualizarEstadisticas();
    }
}

function notificarPendientes() {
    alert('Enviando notificaciones a usuarios pendientes...');
}

function notificarSeleccionados() {
    if (usuariosSeleccionados.length === 0) {
        alert('Seleccione al menos un usuario');
        return;
    }
    
    alert(`Enviando notificaciones a ${usuariosSeleccionados.length} usuarios seleccionados`);
}

function exportarReporte() {
    alert('Generando reporte de aprobaciones...');
}

function actualizarEstados() {
    alert('Actualizando estados de usuarios...');
}

function actualizarEstadisticas() {
    // Aquí se actualizarían las estadísticas en tiempo real
    console.log('Actualizando estadísticas...');
}

// Funciones del modal
function aprobarDesdeModal() {
    if (usuarioActualModal) {
        aprobarUsuario(usuarioActualModal, `Usuario ${usuarioActualModal}`);
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalDetallesUsuario'));
        modal.hide();
    }
}

function rechazarDesdeModal() {
    if (usuarioActualModal) {
        rechazarUsuario(usuarioActualModal, `Usuario ${usuarioActualModal}`);
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalDetallesUsuario'));
        modal.hide();
    }
}
</script><?php /**PATH C:\xampp\htdocs\Login-app\resources\views\partials\modulos\aprobar-usuarios.blade.php ENDPATH**/ ?>