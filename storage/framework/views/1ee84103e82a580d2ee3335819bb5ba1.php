<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-search"></i> Consultar Trámites</h4>
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
                    <h6><i class="fas fa-search"></i> Buscar Trámite</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Número de Expediente</label>
                        <input type="text" class="form-control" placeholder="EXP-2024-001">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">DNI del Solicitante</label>
                        <input type="text" class="form-control" placeholder="12345678">
                    </div>
                    <button class="btn btn-primary" onclick="buscarTramite()">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6><i class="fas fa-info-circle"></i> Resultado de Búsqueda</h6>
                </div>
                <div class="card-body">
                    <div id="resultado-busqueda" style="display: none;">
                        <p><strong>Expediente:</strong> EXP-2024-001</p>
                        <p><strong>Solicitante:</strong> Juan Pérez López</p>
                        <p><strong>Trámite:</strong> Solicitud de licencia</p>
                        <p><strong>Estado:</strong> <span class="badge bg-warning">En proceso</span></p>
                        <p><strong>Observaciones:</strong> Pendiente revisión médica</p>
                    </div>
                    <div id="sin-resultados">
                        <p class="text-muted">Ingrese los datos para buscar un trámite</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function buscarTramite() { 
    document.getElementById('sin-resultados').style.display = 'none';
    document.getElementById('resultado-busqueda').style.display = 'block';
    alert('Trámite encontrado');
}
</script><?php /**PATH C:\xampp\htdocs\Login-app\resources\views\partials\modulos\consultar.blade.php ENDPATH**/ ?>