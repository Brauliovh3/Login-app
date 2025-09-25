<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-folder-open"></i> Gestión de Trámites</h4>
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
                    <h6><i class="fas fa-list"></i> Trámites en Proceso</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Expediente</th>
                                    <th>Solicitante</th>
                                    <th>Tipo Trámite</th>
                                    <th>Fecha Ingreso</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>EXP-2024-001</td>
                                    <td>Juan Pérez López</td>
                                    <td>Solicitud de licencia</td>
                                    <td>10/08/2024</td>
                                    <td><span class="badge bg-warning">En proceso</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="verTramite('EXP-2024-001')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-success" onclick="aprobarTramite('EXP-2024-001')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>EXP-2024-002</td>
                                    <td>María García</td>
                                    <td>Renovación</td>
                                    <td>11/08/2024</td>
                                    <td><span class="badge bg-info">Revisión</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="verTramite('EXP-2024-002')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-success" onclick="aprobarTramite('EXP-2024-002')">
                                            <i class="fas fa-check"></i>
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
function verTramite(expediente) { alert(`Viendo detalles del trámite: ${expediente}`); }
function aprobarTramite(expediente) { alert(`Aprobando trámite: ${expediente}`); }
</script><?php /**PATH C:\xampp\htdocs\Login-app\resources\views\partials\modulos\tramites.blade.php ENDPATH**/ ?>