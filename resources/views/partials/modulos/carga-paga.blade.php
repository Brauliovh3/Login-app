<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-money-bill"></i> Carga y Pagos</h4>
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
                    <h6><i class="fas fa-upload"></i> Cargar Pagos</h6>
                </div>
                <div class="card-body">
                    <button class="btn btn-primary mb-2" onclick="cargarPagos()">
                        <i class="fas fa-plus"></i> Nuevo Pago
                    </button>
                    <button class="btn btn-info" onclick="importarPagos()">
                        <i class="fas fa-file-excel"></i> Importar Excel
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6><i class="fas fa-search"></i> Consultar Pagos</h6>
                </div>
                <div class="card-body">
                    <input type="text" class="form-control mb-2" placeholder="Número de acta">
                    <button class="btn btn-success" onclick="consultarPago()">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function cargarPagos() { alert('Módulo de carga de pagos'); }
function importarPagos() { alert('Importar pagos desde Excel'); }
function consultarPago() { alert('Consultar estado de pago'); }
</script>