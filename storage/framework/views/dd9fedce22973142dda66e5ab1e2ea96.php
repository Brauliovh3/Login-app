<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-user-shield"></i> Mantenimiento de Inspectores</h4>
                <button class="btn btn-secondary" onclick="hideModules()">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </button>
            </div>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6><i class="fas fa-database"></i> Registro de Inspectores</h6>
                </div>
                <div class="card-body">
                    <button class="btn btn-success mb-3" onclick="nuevoInspector()">
                        <i class="fas fa-plus"></i> Nuevo Inspector
                    </button>
                    
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>DNI</th>
                                    <th>Nombres</th>
                                    <th>Código Inspector</th>
                                    <th>Zona Asignada</th>
                                    <th>Estado</th>
                                    <th>Último Reporte</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>11223344</td>
                                    <td>Carlos Inspector Uno</td>
                                    <td>INS-001</td>
                                    <td>Centro Histórico</td>
                                    <td><span class="badge bg-success">Activo</span></td>
                                    <td>Hoy 14:30</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="editarInspector('11223344')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-info" onclick="verReportes('11223344')">
                                            <i class="fas fa-file-alt"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="asignarZona('11223344')">
                                            <i class="fas fa-map"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>55667788</td>
                                    <td>Ana Inspector Dos</td>
                                    <td>INS-002</td>
                                    <td>Avenida Principal</td>
                                    <td><span class="badge bg-warning">En campo</span></td>
                                    <td>Hoy 12:15</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="editarInspector('55667788')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-info" onclick="verReportes('55667788')">
                                            <i class="fas fa-file-alt"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="asignarZona('55667788')">
                                            <i class="fas fa-map"></i>
                                        </button>
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
function nuevoInspector() { alert('Registrando nuevo inspector'); }
function editarInspector(dni) { alert(`Editando inspector DNI: ${dni}`); }
function verReportes(dni) { alert(`Viendo reportes del inspector DNI: ${dni}`); }
function asignarZona(dni) { alert(`Asignando zona al inspector DNI: ${dni}`); }
</script><?php /**PATH C:\xampp\htdocs\Login-app\resources\views\partials\modulos\mantenimiento-inspectores.blade.php ENDPATH**/ ?>