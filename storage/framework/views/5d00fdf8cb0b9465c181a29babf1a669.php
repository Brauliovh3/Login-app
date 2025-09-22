<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-exclamation-triangle"></i> Gestión de Infracciones</h4>
                <button class="btn btn-secondary" onclick="hideModules()">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </button>
            </div>
            <hr>
        </div>
    </div>

    <!-- Botones de acción -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-tools"></i> Acciones</h6>
                </div>
                <div class="card-body">
                    <button class="btn btn-success me-2 mb-2" onclick="nuevaInfraccion()">
                        <i class="fas fa-plus"></i> Nueva Infracción
                    </button>
                    <button class="btn btn-info me-2 mb-2" onclick="importarInfracciones()">
                        <i class="fas fa-upload"></i> Importar
                    </button>
                    <button class="btn btn-warning me-2 mb-2" onclick="exportarInfracciones()">
                        <i class="fas fa-download"></i> Exportar
                    </button>
                    <button class="btn btn-primary mb-2" onclick="sincronizarInfracciones()">
                        <i class="fas fa-sync"></i> Sincronizar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Catálogo de infracciones -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-list"></i> Catálogo de Infracciones</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Código</th>
                                    <th>Descripción</th>
                                    <th>Categoría</th>
                                    <th>Multa (S/)</th>
                                    <th>Puntos</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>A01</strong></td>
                                    <td>Exceso de velocidad en zona urbana</td>
                                    <td><span class="badge bg-danger">Grave</span></td>
                                    <td>150.00</td>
                                    <td>5</td>
                                    <td><span class="badge bg-success">Activa</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="editarInfraccion('A01')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="eliminarInfraccion('A01')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>A02</strong></td>
                                    <td>No respetar semáforo en rojo</td>
                                    <td><span class="badge bg-danger">Grave</span></td>
                                    <td>200.00</td>
                                    <td>8</td>
                                    <td><span class="badge bg-success">Activa</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="editarInfraccion('A02')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="eliminarInfraccion('A02')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>B01</strong></td>
                                    <td>Conducir sin licencia de conducir</td>
                                    <td><span class="badge bg-warning">Muy Grave</span></td>
                                    <td>300.00</td>
                                    <td>12</td>
                                    <td><span class="badge bg-success">Activa</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="editarInfraccion('B01')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="eliminarInfraccion('B01')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>C01</strong></td>
                                    <td>Estacionar en lugar prohibido</td>
                                    <td><span class="badge bg-info">Leve</span></td>
                                    <td>50.00</td>
                                    <td>2</td>
                                    <td><span class="badge bg-success">Activa</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="editarInfraccion('C01')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="eliminarInfraccion('C01')">
                                            <i class="fas fa-trash"></i>
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
function nuevaInfraccion() {
    alert('Abriendo formulario para nueva infracción');
}

function editarInfraccion(codigo) {
    alert(`Editando infracción: ${codigo}`);
}

function eliminarInfraccion(codigo) {
    if (confirm(`¿Eliminar la infracción ${codigo}?`)) {
        alert(`Infracción ${codigo} eliminada`);
    }
}

function importarInfracciones() {
    alert('Importando infracciones desde archivo');
}

function exportarInfracciones() {
    alert('Exportando catálogo de infracciones');
}

function sincronizarInfracciones() {
    alert('Sincronizando con sistema central');
}
</script><?php /**PATH C:\xampp\htdocs\Login-app\resources\views/partials/modulos/infracciones.blade.php ENDPATH**/ ?>