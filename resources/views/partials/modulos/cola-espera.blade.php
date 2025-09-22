<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-users"></i> Cola de Espera</h4>
                <button class="btn btn-secondary" onclick="hideModules()">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </button>
            </div>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-header bg-primary text-white">
                    <h6><i class="fas fa-ticket-alt"></i> Próximo Ticket</h6>
                </div>
                <div class="card-body">
                    <h1 class="display-4 text-primary">A001</h1>
                    <button class="btn btn-success btn-lg" onclick="llamarSiguiente()">
                        <i class="fas fa-play"></i> Llamar
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h6><i class="fas fa-list"></i> Personas en Espera</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Ticket</th>
                                    <th>Tiempo Espera</th>
                                    <th>Tipo Trámite</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-warning">
                                    <td><strong>A001</strong></td>
                                    <td>15 min</td>
                                    <td>Solicitud de licencia</td>
                                    <td><span class="badge bg-warning">Siguiente</span></td>
                                </tr>
                                <tr>
                                    <td>A002</td>
                                    <td>12 min</td>
                                    <td>Renovación</td>
                                    <td><span class="badge bg-info">En espera</span></td>
                                </tr>
                                <tr>
                                    <td>A003</td>
                                    <td>8 min</td>
                                    <td>Duplicado</td>
                                    <td><span class="badge bg-info">En espera</span></td>
                                </tr>
                                <tr>
                                    <td>A004</td>
                                    <td>5 min</td>
                                    <td>Consulta</td>
                                    <td><span class="badge bg-info">En espera</span></td>
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
function llamarSiguiente() { 
    alert('Llamando al ticket A001');
    // Aquí se actualizaría la cola
}
</script>