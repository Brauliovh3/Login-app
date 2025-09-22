<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-user-tie"></i> Mantenimiento de Conductores</h4>
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
                    <h6><i class="fas fa-database"></i> Registro de Conductores</h6>
                </div>
                <div class="card-body">
                    <button class="btn btn-success mb-3" onclick="nuevoConductor()">
                        <i class="fas fa-plus"></i> Nuevo Conductor
                    </button>
                    
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>DNI</th>
                                    <th>Nombres</th>
                                    <th>Licencia</th>
                                    <th>Categoría</th>
                                    <th>Vencimiento</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>12345678</td>
                                    <td>Juan Pérez López</td>
                                    <td>L123456789</td>
                                    <td>A-IIa</td>
                                    <td>15/12/2025</td>
                                    <td><span class="badge bg-success">Vigente</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="editarConductor('12345678')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-info" onclick="verHistorial('12345678')">
                                            <i class="fas fa-history"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>87654321</td>
                                    <td>María García Ruiz</td>
                                    <td>L987654321</td>
                                    <td>A-I</td>
                                    <td>20/03/2024</td>
                                    <td><span class="badge bg-danger">Vencida</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="editarConductor('87654321')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-info" onclick="verHistorial('87654321')">
                                            <i class="fas fa-history"></i>
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
function nuevoConductor() { alert('Registrando nuevo conductor'); }
function editarConductor(dni) { alert(`Editando conductor DNI: ${dni}`); }
function verHistorial(dni) { alert(`Viendo historial del conductor DNI: ${dni}`); }
</script><?php /**PATH C:\xampp\htdocs\Login-app\resources\views/partials/modulos/mantenimiento-conductores.blade.php ENDPATH**/ ?>