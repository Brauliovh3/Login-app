<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-search"></i> Consultas del Sistema</h4>
                <button class="btn btn-secondary" onclick="hideModules()">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </button>
            </div>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h6><i class="fas fa-id-card"></i> Consulta DNI</h6>
                </div>
                <div class="card-body">
                    <input type="text" class="form-control mb-2" placeholder="Número de DNI">
                    <button class="btn btn-primary" onclick="consultarDNI()">
                        <i class="fas fa-search"></i> Consultar
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h6><i class="fas fa-building"></i> Consulta RUC</h6>
                </div>
                <div class="card-body">
                    <input type="text" class="form-control mb-2" placeholder="Número de RUC">
                    <button class="btn btn-primary" onclick="consultarRUC()">
                        <i class="fas fa-search"></i> Consultar
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h6><i class="fas fa-car"></i> Consulta Vehículo</h6>
                </div>
                <div class="card-body">
                    <input type="text" class="form-control mb-2" placeholder="Número de placa">
                    <button class="btn btn-primary" onclick="consultarVehiculo()">
                        <i class="fas fa-search"></i> Consultar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function consultarDNI() { alert('Consultando DNI en RENIEC'); }
function consultarRUC() { alert('Consultando RUC en SUNAT'); }
function consultarVehiculo() { alert('Consultando vehículo en base de datos'); }
</script>