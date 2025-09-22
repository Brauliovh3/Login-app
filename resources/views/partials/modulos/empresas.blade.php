<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-building"></i> Gestión de Empresas</h4>
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
                    <h6><i class="fas fa-list"></i> Lista de Empresas de Transporte</h6>
                </div>
                <div class="card-body">
                    <button class="btn btn-success mb-3" onclick="nuevaEmpresa()">
                        <i class="fas fa-plus"></i> Nueva Empresa
                    </button>
                    
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>RUC</th>
                                    <th>Razón Social</th>
                                    <th>Tipo Servicio</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>20123456789</td>
                                    <td>Transportes El Rápido SAC</td>
                                    <td>Interprovincial</td>
                                    <td><span class="badge bg-success">Activa</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="editarEmpresa('20123456789')">
                                            <i class="fas fa-edit"></i>
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
function nuevaEmpresa() { alert('Registrar nueva empresa de transporte'); }
function editarEmpresa(ruc) { alert(`Editando empresa RUC: ${ruc}`); }
</script>