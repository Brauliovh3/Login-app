@extends('layouts.dashboard')

@section('title', 'Reportes de Inspección')

@section('content')
<div class="container-fluid">
    <!-- Encabezado -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-bar text-orange mr-2"></i>
            Reportes de Inspección
        </h1>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" onclick="exportarPDF()">
                <i class="fas fa-file-pdf mr-2"></i>
                Exportar PDF
            </button>
            <button class="btn btn-success" onclick="exportarExcel()">
                <i class="fas fa-file-excel mr-2"></i>
                Exportar Excel
            </button>
        </div>
    </div>

    <!-- Filtros de Reporte -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-filter mr-2"></i>
                Configurar Reporte
            </h6>
        </div>
        <div class="card-body">
            <form id="formReporte">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="fecha_inicio" class="form-label">Fecha Inicio <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                               value="{{ date('Y-m-01') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label for="fecha_fin" class="form-label">Fecha Fin <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                               value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label for="tipo_reporte" class="form-label">Tipo de Reporte <span class="text-danger">*</span></label>
                        <select class="form-select" id="tipo_reporte" name="tipo_reporte" required>
                            <option value="">Seleccionar...</option>
                            <option value="inspecciones">Inspecciones Realizadas</option>
                            <option value="infracciones">Infracciones Detectadas</option>
                            <option value="vehiculos">Vehículos Inspeccionados</option>
                            <option value="multas">Multas Aplicadas</option>
                            <option value="estadisticas">Estadísticas Generales</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="button" class="btn btn-primary w-100" onclick="generarReporte()">
                            <i class="fas fa-chart-line mr-2"></i>
                            Generar Reporte
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <label for="filtro_estado_reporte" class="form-label">Estado de Inspección</label>
                        <select class="form-select" id="filtro_estado_reporte" name="filtro_estado_reporte">
                            <option value="">Todos los estados</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="procesada">Completada</option>
                            <option value="observada">Con Observaciones</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="filtro_tipo_vehiculo" class="form-label">Tipo de Vehículo</label>
                        <select class="form-select" id="filtro_tipo_vehiculo" name="filtro_tipo_vehiculo">
                            <option value="">Todos los tipos</option>
                            <option value="automovil">Automóvil</option>
                            <option value="camioneta">Camioneta</option>
                            <option value="bus">Bus</option>
                            <option value="camion">Camión</option>
                            <option value="motocicleta">Motocicleta</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="filtro_ubicacion" class="form-label">Ubicación</label>
                        <input type="text" class="form-control" id="filtro_ubicacion" name="filtro_ubicacion" 
                               placeholder="Filtrar por ubicación">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Resumen Estadístico -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2" style="border-left: 0.25rem solid #ff8c00 !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: #ff8c00;">
                                Inspecciones (Mes Actual)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">47</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Infracciones Detectadas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">23</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Multas Aplicadas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">S/ 3,450</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Vehículos Únicos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">42</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-car fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Área de Resultados -->
    <div class="row">
        <!-- Gráfico de Inspecciones por Día -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-white">Inspecciones por Día (Último Mes)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="graficoInspecciones" width="100%" height="40"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribución de Infracciones -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-white">Tipos de Infracciones</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="graficoInfracciones" width="100%" height="50"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Exceso Velocidad
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> SOAT Vencido
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Rev. Técnica
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Resultados Detallados -->
    <div class="card shadow mb-4" id="contenedor-tabla-reporte" style="display: none;">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-table mr-2"></i>
                Resultados Detallados
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tablaReporte" width="100%" cellspacing="0">
                    <thead id="tabla-header">
                        <!-- Headers dinámicos según tipo de reporte -->
                    </thead>
                    <tbody id="tabla-body">
                        <!-- Datos dinámicos -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Reportes Predefinidos -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-download mr-2"></i>
                Reportes Predefinidos
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card h-100" style="border-left: 4px solid #ff8c00;">
                        <div class="card-body">
                            <h6 class="card-title text-orange">Reporte Diario</h6>
                            <p class="card-text">Inspecciones realizadas hoy</p>
                            <button class="btn btn-sm btn-primary" onclick="reporteDiario()">
                                <i class="fas fa-download mr-1"></i> Generar
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card h-100" style="border-left: 4px solid #28a745;">
                        <div class="card-body">
                            <h6 class="card-title text-success">Reporte Semanal</h6>
                            <p class="card-text">Resumen de la semana actual</p>
                            <button class="btn btn-sm btn-success" onclick="reporteSemanal()">
                                <i class="fas fa-download mr-1"></i> Generar
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card h-100" style="border-left: 4px solid #17a2b8;">
                        <div class="card-body">
                            <h6 class="card-title text-info">Reporte Mensual</h6>
                            <p class="card-text">Estadísticas del mes completo</p>
                            <button class="btn btn-sm btn-info" onclick="reporteMensual()">
                                <i class="fas fa-download mr-1"></i> Generar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function() {
    // Inicializar gráficos
    inicializarGraficos();
});

function inicializarGraficos() {
    // Gráfico de Inspecciones por Día
    const ctxInspecciones = document.getElementById('graficoInspecciones').getContext('2d');
    new Chart(ctxInspecciones, {
        type: 'line',
        data: {
            labels: ['1 Ago', '2 Ago', '3 Ago', '4 Ago', '5 Ago', '6 Ago', '7 Ago'],
            datasets: [{
                label: 'Inspecciones',
                data: [3, 7, 4, 8, 5, 6, 9],
                borderColor: '#ff8c00',
                backgroundColor: 'rgba(255, 140, 0, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Gráfico de Tipos de Infracciones
    const ctxInfracciones = document.getElementById('graficoInfracciones').getContext('2d');
    new Chart(ctxInfracciones, {
        type: 'doughnut',
        data: {
            labels: ['Exceso Velocidad', 'SOAT Vencido', 'Rev. Técnica', 'Documentos', 'Otros'],
            datasets: [{
                data: [35, 25, 20, 12, 8],
                backgroundColor: [
                    '#ff8c00',
                    '#28a745',
                    '#17a2b8',
                    '#ffc107',
                    '#6c757d'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

function generarReporte() {
    const fechaInicio = $('#fecha_inicio').val();
    const fechaFin = $('#fecha_fin').val();
    const tipoReporte = $('#tipo_reporte').val();

    if (!fechaInicio || !fechaFin || !tipoReporte) {
        showError('Por favor complete todos los campos obligatorios');
        return;
    }

    if (new Date(fechaInicio) > new Date(fechaFin)) {
        showError('La fecha de inicio no puede ser mayor que la fecha fin');
        return;
    }

    showInfo('Generando reporte...');

    // Simular generación de reporte
    setTimeout(() => {
        mostrarTablaReporte(tipoReporte);
        showSuccess('Reporte generado correctamente');
    }, 2000);
}

function mostrarTablaReporte(tipo) {
    let headers = '';
    let datos = '';

    switch(tipo) {
        case 'inspecciones':
            headers = `
                <tr>
                    <th>Fecha</th>
                    <th>Placa</th>
                    <th>Conductor</th>
                    <th>Ubicación</th>
                    <th>Estado</th>
                    <th>Multa</th>
                </tr>
            `;
            datos = `
                <tr>
                    <td>03/08/2025</td>
                    <td>ABC-123</td>
                    <td>Juan Pérez</td>
                    <td>Av. Los Chankas</td>
                    <td><span class="badge bg-success">Completada</span></td>
                    <td>S/ 150.00</td>
                </tr>
                <tr>
                    <td>02/08/2025</td>
                    <td>XYZ-789</td>
                    <td>María López</td>
                    <td>Jr. Grau</td>
                    <td><span class="badge bg-warning">Pendiente</span></td>
                    <td>-</td>
                </tr>
            `;
            break;
        case 'infracciones':
            headers = `
                <tr>
                    <th>Tipo Infracción</th>
                    <th>Cantidad</th>
                    <th>Multa Promedio</th>
                    <th>Total Recaudado</th>
                </tr>
            `;
            datos = `
                <tr>
                    <td>Exceso de Velocidad</td>
                    <td>15</td>
                    <td>S/ 180.00</td>
                    <td>S/ 2,700.00</td>
                </tr>
                <tr>
                    <td>SOAT Vencido</td>
                    <td>8</td>
                    <td>S/ 120.00</td>
                    <td>S/ 960.00</td>
                </tr>
            `;
            break;
        case 'vehiculos':
            headers = `
                <tr>
                    <th>Placa</th>
                    <th>Tipo</th>
                    <th>Inspecciones</th>
                    <th>Infracciones</th>
                    <th>Total Multas</th>
                </tr>
            `;
            datos = `
                <tr>
                    <td>ABC-123</td>
                    <td>Automóvil</td>
                    <td>3</td>
                    <td>2</td>
                    <td>S/ 300.00</td>
                </tr>
                <tr>
                    <td>XYZ-789</td>
                    <td>Bus</td>
                    <td>2</td>
                    <td>1</td>
                    <td>S/ 150.00</td>
                </tr>
            `;
            break;
        default:
            headers = '<tr><th>Dato</th><th>Valor</th></tr>';
            datos = '<tr><td>Total Inspecciones</td><td>47</td></tr>';
    }

    $('#tabla-header').html(headers);
    $('#tabla-body').html(datos);
    $('#contenedor-tabla-reporte').show();

    // Inicializar DataTable si no existe
    if (!$.fn.DataTable.isDataTable('#tablaReporte')) {
        $('#tablaReporte').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
            },
            "order": [[ 0, "desc" ]],
            "pageLength": 10
        });
    }
}

function exportarPDF() {
    // use shared helper: export visible table in the modal
    exportTableToPDF('#tabla-resultados', 'actas-consulta.pdf');
}

function exportarExcel() {
    // use shared helper: export visible table in the modal
    exportTableToCSV('#tabla-resultados', 'actas-consulta.csv');
}

function reporteDiario() {
    showInfo('Generando reporte diario...');
    setTimeout(() => {
        showSuccess('Reporte diario generado');
    }, 1500);
}

function reporteSemanal() {
    showInfo('Generando reporte semanal...');
    setTimeout(() => {
        showSuccess('Reporte semanal generado');
    }, 1500);
}

function reporteMensual() {
    showInfo('Generando reporte mensual...');
    setTimeout(() => {
        showSuccess('Reporte mensual generado');
    }, 1500);
}
</script>

<!-- DataTables CSS y JS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>
@endpush
@endsection

@push('scripts')
    @include('partials.export-actas-scripts')
@endpush
