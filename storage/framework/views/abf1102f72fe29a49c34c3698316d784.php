<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-clipboard-check"></i> Gestión de Inspecciones</h4>
                <button class="btn btn-secondary" onclick="hideModules()">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </button>
            </div>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6><i class="fas fa-plus"></i> Nueva Inspección</h6>
                </div>
                <div class="card-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Tipo de Inspección</label>
                            <select class="form-select">
                                <option>Rutinaria</option>
                                <option>Por denuncia</option>
                                <option>Seguimiento</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Empresa/Vehículo</label>
                            <input type="text" class="form-control" placeholder="RUC o Placa">
                        </div>
                        <button type="button" class="btn btn-primary" onclick="programarInspeccion()">
                            <i class="fas fa-calendar"></i> Programar
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6><i class="fas fa-calendar"></i> Inspecciones Programadas</h6>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <div class="list-group-item">
                            <strong>Empresa ABC SAC</strong><br>
                            <small>Hoy 14:00 - Inspección rutinaria</small>
                        </div>
                        <div class="list-group-item">
                            <strong>Vehículo ABC-123</strong><br>
                            <small>Mañana 09:00 - Por denuncia</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function programarInspeccion() { alert('Inspección programada correctamente'); }
</script><?php /**PATH C:\xampp\htdocs\Login-app\resources\views\partials\modulos\inspecciones.blade.php ENDPATH**/ ?>