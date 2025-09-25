<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-chart-bar"></i> Reportes y Estadísticas</h4>
                <button class="btn btn-secondary" onclick="hideModules()">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </button>
            </div>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">
                    <h6><i class="fas fa-file-pdf"></i> Reportes Disponibles</h6>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action" onclick="generarReporte('actas')">
                            <i class="fas fa-file-alt"></i> Reporte de Actas
                        </a>
                        <a href="#" class="list-group-item list-group-item-action" onclick="generarReporte('pagos')">
                            <i class="fas fa-money-bill"></i> Reporte de Pagos
                        </a>
                        <a href="#" class="list-group-item list-group-item-action" onclick="generarReporte('usuarios')">
                            <i class="fas fa-users"></i> Reporte de Usuarios
                        </a>
                        <a href="#" class="list-group-item list-group-item-action" onclick="generarReporte('infracciones')">
                            <i class="fas fa-exclamation-triangle"></i> Reporte de Infracciones
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">
                    <h6><i class="fas fa-calendar"></i> Filtros de Fecha</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Fecha Inicio</label>
                        <input type="date" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha Fin</label>
                        <input type="date" class="form-control">
                    </div>
                    <button class="btn btn-success" onclick="aplicarFiltros()">
                        <i class="fas fa-filter"></i> Aplicar Filtros
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function generarReporte(tipo) { alert(`Generando reporte de ${tipo}`); }
function aplicarFiltros() { alert('Aplicando filtros de fecha'); }
</script><?php /**PATH C:\xampp\htdocs\Login-app\resources\views\partials\modulos\reportes.blade.php ENDPATH**/ ?>