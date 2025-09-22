@extends('layouts.dashboard')

@section('title', 'Gestión de Vehículos')

@section('content')
<div class="container-fluid">
    <!-- Encabezado -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-car text-orange mr-2"></i>
            Gestión de Vehículos
        </h1>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoVehiculo">
                <i class="fas fa-plus mr-2"></i>
                Registrar Vehículo
            </button>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label for="filtro_placa" class="form-label">Placa</label>
                    <input type="text" class="form-control" id="filtro_placa" name="filtro_placa" 
                           placeholder="Buscar por placa">
                </div>
                <div class="col-md-3">
                    <label for="filtro_tipo" class="form-label">Tipo de Vehículo</label>
                    <select class="form-select" id="filtro_tipo" name="filtro_tipo">
                        <option value="">Todos los tipos</option>
                        <option value="automovil">Automóvil</option>
                        <option value="camioneta">Camioneta</option>
                        <option value="bus">Bus</option>
                        <option value="camion">Camión</option>
                        <option value="motocicleta">Motocicleta</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtro_estado" class="form-label">Estado de Documentos</label>
                    <select class="form-select" id="filtro_estado" name="filtro_estado">
                        <option value="">Todos</option>
                        <option value="vigente">Documentos Vigentes</option>
                        <option value="por_vencer">Por Vencer</option>
                        <option value="vencido">Documentos Vencidos</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-primary me-2" onclick="aplicarFiltros()">
                        <i class="fas fa-search mr-2"></i>
                        Buscar
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="limpiarFiltros()">
                        <i class="fas fa-times mr-2"></i>
                        Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2" style="border-left: 0.25rem solid #ff8c00 !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: #ff8c00;">
                                Total Vehículos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">1,247</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-car fa-2x text-gray-300"></i>
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
                                Documentos Vigentes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">1,089</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                Por Vencer (30 días)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">87</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Documentos Vencidos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">71</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Vehículos -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-table mr-2"></i>
                Registro de Vehículos
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Placa</th>
                            <th>Tipo</th>
                            <th>Marca/Modelo</th>
                            <th>Propietario</th>
                            <th>SOAT</th>
                            <th>Rev. Técnica</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Datos de ejemplo -->
                        <tr>
                            <td><strong>ABC-123</strong></td>
                            <td>Automóvil</td>
                            <td>Toyota Corolla 2020</td>
                            <td>Juan Pérez Gómez</td>
                            <td><span class="badge bg-success">15/12/2025</span></td>
                            <td><span class="badge bg-success">20/11/2025</span></td>
                            <td><span class="badge bg-success">Vigente</span></td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="verDetalleVehiculo('ABC-123')">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning" onclick="editarVehiculo('ABC-123')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-info" onclick="historialInspecciones('ABC-123')">
                                    <i class="fas fa-history"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>XYZ-789</strong></td>
                            <td>Bus</td>
                            <td>Mercedes Benz 2018</td>
                            <td>Transportes Apurímac S.A.C.</td>
                            <td><span class="badge bg-warning">05/09/2025</span></td>
                            <td><span class="badge bg-success">30/10/2025</span></td>
                            <td><span class="badge bg-warning">Por Vencer</span></td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="verDetalleVehiculo('XYZ-789')">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning" onclick="editarVehiculo('XYZ-789')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-info" onclick="historialInspecciones('XYZ-789')">
                                    <i class="fas fa-history"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>DEF-456</strong></td>
                            <td>Camión</td>
                            <td>Volvo 2017</td>
                            <td>Carlos Mendoza Ruiz</td>
                            <td><span class="badge bg-danger">15/07/2025</span></td>
                            <td><span class="badge bg-danger">10/06/2025</span></td>
                            <td><span class="badge bg-danger">Vencido</span></td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="verDetalleVehiculo('DEF-456')">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning" onclick="editarVehiculo('DEF-456')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-info" onclick="historialInspecciones('DEF-456')">
                                    <i class="fas fa-history"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>GHI-012</strong></td>
                            <td>Motocicleta</td>
                            <td>Honda CB 2021</td>
                            <td>Ana Torres Vega</td>
                            <td><span class="badge bg-success">22/12/2025</span></td>
                            <td><span class="badge bg-success">18/09/2025</span></td>
                            <td><span class="badge bg-success">Vigente</span></td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="verDetalleVehiculo('GHI-012')">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning" onclick="editarVehiculo('GHI-012')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-info" onclick="historialInspecciones('GHI-012')">
                                    <i class="fas fa-history"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>JKL-345</strong></td>
                            <td>Camioneta</td>
                            <td>Ford Ranger 2019</td>
                            <td>Roberto Flores Castro</td>
                            <td><span class="badge bg-warning">25/08/2025</span></td>
                            <td><span class="badge bg-success">14/11/2025</span></td>
                            <td><span class="badge bg-warning">Por Vencer</span></td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="verDetalleVehiculo('JKL-345')">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning" onclick="editarVehiculo('JKL-345')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-info" onclick="historialInspecciones('JKL-345')">
                                    <i class="fas fa-history"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nuevo Vehículo -->
<div class="modal fade" id="modalNuevoVehiculo" tabindex="-1" aria-labelledby="modalNuevoVehiculoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNuevoVehiculoLabel">
                    <i class="fas fa-plus mr-2"></i>
                    Registrar Nuevo Vehículo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formNuevoVehiculo">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nueva_placa" class="form-label">Placa <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nueva_placa" name="nueva_placa" required>
                        </div>
                        <div class="col-md-6">
                            <label for="nuevo_tipo" class="form-label">Tipo de Vehículo <span class="text-danger">*</span></label>
                            <select class="form-select" id="nuevo_tipo" name="nuevo_tipo" required>
                                <option value="">Seleccionar...</option>
                                <option value="automovil">Automóvil</option>
                                <option value="camioneta">Camioneta</option>
                                <option value="bus">Bus</option>
                                <option value="camion">Camión</option>
                                <option value="motocicleta">Motocicleta</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="nueva_marca" class="form-label">Marca <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nueva_marca" name="nueva_marca" required>
                        </div>
                        <div class="col-md-4">
                            <label for="nuevo_modelo" class="form-label">Modelo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nuevo_modelo" name="nuevo_modelo" required>
                        </div>
                        <div class="col-md-4">
                            <label for="nuevo_anio" class="form-label">Año</label>
                            <input type="number" class="form-control" id="nuevo_anio" name="nuevo_anio" min="1990" max="2025">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nuevo_color" class="form-label">Color</label>
                            <input type="text" class="form-control" id="nuevo_color" name="nuevo_color">
                        </div>
                        <div class="col-md-6">
                            <label for="nuevo_motor" class="form-label">Número de Motor</label>
                            <input type="text" class="form-control" id="nuevo_motor" name="nuevo_motor">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="nuevo_propietario" class="form-label">Propietario <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nuevo_propietario" name="nuevo_propietario" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="fecha_soat" class="form-label">Fecha Vencimiento SOAT</label>
                            <input type="date" class="form-control" id="fecha_soat" name="fecha_soat">
                        </div>
                        <div class="col-md-6">
                            <label for="fecha_revision" class="form-label">Fecha Vencimiento Rev. Técnica</label>
                            <input type="date" class="form-control" id="fecha_revision" name="fecha_revision">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarVehiculo()">
                    <i class="fas fa-save mr-2"></i>
                    Guardar Vehículo
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detalle Vehículo -->
<div class="modal fade" id="modalDetalleVehiculo" tabindex="-1" aria-labelledby="modalDetalleVehiculoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetalleVehiculoLabel">
                    <i class="fas fa-car mr-2"></i>
                    Detalle del Vehículo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="contenido-detalle-vehiculo">
                <!-- Contenido se carga dinámicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="imprimirDetalleVehiculo()">
                    <i class="fas fa-print mr-2"></i>
                    Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Configurar DataTable
    $('#dataTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "order": [[ 0, "asc" ]],
        "pageLength": 10
    });

    // Formatear placa en mayúsculas
    $('#nueva_placa').on('input', function() {
        this.value = this.value.toUpperCase();
    });
});

function aplicarFiltros() {
    const placa = $('#filtro_placa').val();
    const tipo = $('#filtro_tipo').val();
    const estado = $('#filtro_estado').val();
    
    showInfo('Filtros aplicados correctamente');
}

function limpiarFiltros() {
    $('#filtro_placa').val('');
    $('#filtro_tipo').val('');
    $('#filtro_estado').val('');
    showInfo('Filtros limpiados');
}

function verDetalleVehiculo(placa) {
    const contenidoDetalle = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-orange">Información del Vehículo</h6>
                <p><strong>Placa:</strong> ${placa}</p>
                <p><strong>Tipo:</strong> Automóvil</p>
                <p><strong>Marca:</strong> Toyota</p>
                <p><strong>Modelo:</strong> Corolla</p>
                <p><strong>Año:</strong> 2020</p>
                <p><strong>Color:</strong> Blanco</p>
                <p><strong>Número de Motor:</strong> 4A-FE123456</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-orange">Propietario y Documentos</h6>
                <p><strong>Propietario:</strong> Juan Pérez Gómez</p>
                <p><strong>DNI:</strong> 12345678</p>
                <p><strong>SOAT:</strong> <span class="badge bg-success">Vigente hasta 15/12/2025</span></p>
                <p><strong>Rev. Técnica:</strong> <span class="badge bg-success">Vigente hasta 20/11/2025</span></p>
                <p><strong>Estado General:</strong> <span class="badge bg-success">Vigente</span></p>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <h6 class="text-orange">Historial de Inspecciones (Últimas 5)</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Inspector</th>
                                <th>Resultado</th>
                                <th>Multa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>03/08/2025</td>
                                <td>{{ auth()->user()->name }}</td>
                                <td><span class="badge bg-success">Sin infracciones</span></td>
                                <td>S/ 0.00</td>
                            </tr>
                            <tr>
                                <td>15/07/2025</td>
                                <td>Inspector García</td>
                                <td><span class="badge bg-warning">Con observaciones</span></td>
                                <td>S/ 150.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `;
    
    $('#contenido-detalle-vehiculo').html(contenidoDetalle);
    $('#modalDetalleVehiculo').modal('show');
}

function editarVehiculo(placa) {
    Swal.fire({
        title: 'Editar Vehículo',
        text: `¿Desea editar la información del vehículo ${placa}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ff8c00',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, editar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Cargar datos en el modal de edición
            $('#modalNuevoVehiculo').modal('show');
            showInfo('Cargando datos para edición...');
        }
    });
}

function historialInspecciones(placa) {
    Swal.fire({
        title: 'Historial de Inspecciones',
        text: `Ver historial completo de inspecciones para ${placa}`,
        icon: 'info',
        confirmButtonColor: '#ff8c00',
        confirmButtonText: 'Ver Historial'
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirigir a página de historial o mostrar modal
            window.location.href = '{{ route("inspector.inspecciones") }}?vehiculo=' + placa;
        }
    });
}

function guardarVehiculo() {
    const placa = $('#nueva_placa').val();
    const tipo = $('#nuevo_tipo').val();
    const marca = $('#nueva_marca').val();
    const modelo = $('#nuevo_modelo').val();
    const propietario = $('#nuevo_propietario').val();

    if (!placa || !tipo || !marca || !modelo || !propietario) {
        showError('Por favor complete todos los campos obligatorios');
        return;
    }

    Swal.fire({
        title: 'Guardar Vehículo',
        text: '¿Está seguro de que desea registrar este vehículo?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ff8c00',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, guardar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            showSuccess('Vehículo registrado correctamente');
            $('#modalNuevoVehiculo').modal('hide');
            $('#formNuevoVehiculo')[0].reset();
            // Recargar la tabla
            setTimeout(() => {
                location.reload();
            }, 2000);
        }
    });
}

function imprimirDetalleVehiculo() {
    showInfo('Imprimiendo detalle del vehículo...');
    $('#modalDetalleVehiculo').modal('hide');
}
</script>

<!-- DataTables CSS y JS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>
@endpush
@endsection
