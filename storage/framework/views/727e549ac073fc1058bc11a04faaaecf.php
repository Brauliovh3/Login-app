<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-users"></i> Gestión de Usuarios</h4>
                <button class="btn btn-secondary" onclick="hideModules()">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </button>
            </div>
            <hr>
        </div>
    </div>

    <!-- Botones de acción principales -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-tools"></i> Acciones Rápidas</h6>
                </div>
                <div class="card-body">
                    <button class="btn btn-success me-2 mb-2" onclick="mostrarFormularioNuevoUsuario()">
                        <i class="fas fa-user-plus"></i> Nuevo Usuario
                    </button>
                    <button class="btn btn-warning me-2 mb-2" onclick="mostrarUsuariosPendientes()">
                        <i class="fas fa-clock"></i> Usuarios Pendientes
                        <span class="badge bg-danger" id="contador-pendientes">0</span>
                    </button>
                    <button class="btn btn-info me-2 mb-2" onclick="exportarUsuarios()">
                        <i class="fas fa-download"></i> Exportar Lista
                    </button>
                    <button class="btn btn-primary mb-2" onclick="sincronizarUsuarios()">
                        <i class="fas fa-sync"></i> Sincronizar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-filter"></i> Filtros de Búsqueda</h6>
                </div>
                <div class="card-body">
                    <form class="row g-3">
                        <div class="col-md-3">
                            <label for="filtro-nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="filtro-nombre" placeholder="Buscar por nombre">
                        </div>
                        <div class="col-md-3">
                            <label for="filtro-email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="filtro-email" placeholder="Buscar por email">
                        </div>
                        <div class="col-md-2">
                            <label for="filtro-rol" class="form-label">Rol</label>
                            <select class="form-select" id="filtro-rol">
                                <option value="">Todos</option>
                                <option value="administrador">Administrador</option>
                                <option value="fiscalizador">Fiscalizador</option>
                                <option value="ventanilla">Ventanilla</option>
                                <option value="inspector">Inspector</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="filtro-estado" class="form-label">Estado</label>
                            <select class="form-select" id="filtro-estado">
                                <option value="">Todos</option>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                                <option value="pendiente">Pendiente</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-primary d-block w-100" onclick="filtrarUsuarios()">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario nuevo usuario (oculto inicialmente) -->
    <div class="row mb-4" id="formulario-nuevo-usuario" style="display: none;">
        <div class="col-12">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-user-plus"></i> Crear Nuevo Usuario</h6>
                </div>
                <div class="card-body">
                    <form id="form-nuevo-usuario" class="row g-3">
                        <div class="col-md-6">
                            <label for="nuevo-nombre" class="form-label">Nombre Completo</label>
                            <input type="text" class="form-control" id="nuevo-nombre" required>
                        </div>
                        <div class="col-md-6">
                            <label for="nuevo-email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="nuevo-email" required>
                        </div>
                        <div class="col-md-4">
                            <label for="nuevo-dni" class="form-label">DNI</label>
                            <input type="text" class="form-control" id="nuevo-dni" pattern="[0-9]{8}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="nuevo-telefono" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="nuevo-telefono">
                        </div>
                        <div class="col-md-4">
                            <label for="nuevo-rol" class="form-label">Rol</label>
                            <select class="form-select" id="nuevo-rol" required>
                                <option value="">Seleccionar rol</option>
                                <option value="administrador">Administrador</option>
                                <option value="fiscalizador">Fiscalizador</option>
                                <option value="ventanilla">Ventanilla</option>
                                <option value="inspector">Inspector</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="nuevo-password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="nuevo-password" required>
                        </div>
                        <div class="col-md-6">
                            <label for="confirmar-password" class="form-label">Confirmar Contraseña</label>
                            <input type="password" class="form-control" id="confirmar-password" required>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-success me-2">
                                <i class="fas fa-save"></i> Crear Usuario
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="ocultarFormularioNuevoUsuario()">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de usuarios -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-list"></i> Lista de Usuarios del Sistema</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>DNI</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th>Último Acceso</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-usuarios">
                                <!-- Los usuarios se cargarán dinámicamente aquí -->
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-users fa-2x mb-2"></i><br>
                                        <strong>Sin usuarios cargados</strong><br>
                                        <small>Los usuarios aparecerán aquí cuando se carguen desde la base de datos</small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Funciones específicas del módulo de gestión de usuarios
function mostrarFormularioNuevoUsuario() {
    document.getElementById('formulario-nuevo-usuario').style.display = 'block';
    document.getElementById('nuevo-nombre').focus();
}

function ocultarFormularioNuevoUsuario() {
    document.getElementById('formulario-nuevo-usuario').style.display = 'none';
    document.getElementById('form-nuevo-usuario').reset();
}

function mostrarUsuariosPendientes() {
    document.getElementById('filtro-estado').value = 'pendiente';
    filtrarUsuarios();
    alert('Mostrando usuarios pendientes de aprobación');
}

function filtrarUsuarios() {
    const nombre = document.getElementById('filtro-nombre').value;
    const email = document.getElementById('filtro-email').value;
    const rol = document.getElementById('filtro-rol').value;
    const estado = document.getElementById('filtro-estado').value;
    
    console.log('Filtrando usuarios:', { nombre, email, rol, estado });
    alert('Aplicando filtros de usuarios');
}

function editarUsuario(id) {
    alert(`Editando usuario ID: ${id}`);
    // Aquí se abriría un modal o formulario de edición
}

function cambiarEstadoUsuario(id) {
    if (confirm('¿Cambiar el estado de este usuario?')) {
        alert(`Cambiando estado del usuario ID: ${id}`);
        // Aquí se haría la actualización con AJAX
    }
}

function verDetallesUsuario(id) {
    alert(`Mostrando detalles del usuario ID: ${id}`);
    // Aquí se abriría un modal con los detalles completos
}

function aprobarUsuario(id) {
    if (confirm('¿Aprobar este usuario?')) {
        alert(`Usuario ID: ${id} aprobado`);
        // Aquí se actualizaría el estado a "activo"
    }
}

function rechazarUsuario(id) {
    if (confirm('¿Rechazar este usuario?')) {
        alert(`Usuario ID: ${id} rechazado`);
        // Aquí se rechazaría la solicitud
    }
}

function eliminarUsuario(id) {
    if (confirm('¿Está seguro de eliminar este usuario? Esta acción no se puede deshacer.')) {
        alert(`Usuario ID: ${id} eliminado`);
        // Aquí se haría la eliminación con AJAX
    }
}

function exportarUsuarios() {
    alert('Exportando lista de usuarios a Excel');
    // Aquí se generaría el archivo Excel
}

function sincronizarUsuarios() {
    alert('Sincronizando usuarios con el sistema central');
    // Aquí se haría la sincronización
}

// Validación del formulario de nuevo usuario
document.getElementById('form-nuevo-usuario').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const password = document.getElementById('nuevo-password').value;
    const confirmar = document.getElementById('confirmar-password').value;
    
    if (password !== confirmar) {
        alert('Las contraseñas no coinciden');
        return;
    }
    
    if (password.length < 6) {
        alert('La contraseña debe tener al menos 6 caracteres');
        return;
    }
    
    alert('Usuario creado exitosamente');
    ocultarFormularioNuevoUsuario();
    // Aquí se enviarían los datos con AJAX
});
</script><?php /**PATH C:\xampp\htdocs\Login-app\resources\views\partials\modulos\gestionar-usuarios.blade.php ENDPATH**/ ?>