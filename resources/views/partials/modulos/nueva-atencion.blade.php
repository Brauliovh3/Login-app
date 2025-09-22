<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-plus-circle"></i> Nueva Atención</h4>
                <button class="btn btn-secondary" onclick="hideModules()">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </button>
            </div>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h6><i class="fas fa-user"></i> Datos del Ciudadano</h6>
                </div>
                <div class="card-body">
                    <form>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tipo de Documento</label>
                                <select class="form-select">
                                    <option>DNI</option>
                                    <option>Carnet de Extranjería</option>
                                    <option>Pasaporte</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Número de Documento</label>
                                <input type="text" class="form-control">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombres</label>
                                <input type="text" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Apellidos</label>
                                <input type="text" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipo de Trámite</label>
                            <select class="form-select">
                                <option>Solicitud de licencia</option>
                                <option>Renovación de licencia</option>
                                <option>Duplicado de licencia</option>
                                <option>Reconsideración</option>
                                <option>Consulta general</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-success" onclick="iniciarAtencion()">
                            <i class="fas fa-play"></i> Iniciar Atención
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6><i class="fas fa-clock"></i> Cola de Espera</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h3 class="text-primary">8</h3>
                        <p>Personas en espera</p>
                    </div>
                    <div class="list-group">
                        <div class="list-group-item d-flex justify-content-between">
                            <span>Ticket #001</span>
                            <small>10 min</small>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span>Ticket #002</span>
                            <small>8 min</small>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span>Ticket #003</span>
                            <small>5 min</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function iniciarAtencion() { alert('Iniciando nueva atención al ciudadano'); }
</script>