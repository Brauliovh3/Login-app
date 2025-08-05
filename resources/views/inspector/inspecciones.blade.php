@extends('layouts.dashboard')

@section('title', 'Mis Inspecciones')

@section('content')
<div class="container-fluid">
    <!-- Encabezado -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-list-check text-orange mr-2"></i>
            Mis Inspecciones
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('inspector.nueva-inspeccion') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i>
                Nueva Inspección
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label for="filtro_fecha" class="form-label">Filtrar por Fecha</label>
                    <input type="date" class="form-control" id="filtro_fecha" name="filtro_fecha">
                </div>
                <div class="col-md-3">
                    <label for="filtro_estado" class="form-label">Estado</label>
                    <select class="form-select" id="filtro_estado" name="filtro_estado">
                        <option value="">Todos los estados</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="completada">Completada</option>
                        <option value="observada">Con Observaciones</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtro_placa" class="form-label">Placa</label>
                    <input type="text" class="form-control" id="filtro_placa" name="filtro_placa" 
                        placeholder="Buscar por placa">
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
                                Total Inspecciones
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">47</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
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
                                Completadas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">32</div>
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
                                Pendientes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">8</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                                Con Observaciones
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">7</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Inspecciones -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-table mr-2"></i>
                Historial de Inspecciones
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Placa</th>
                            <th>Conductor</th>
                            <th>Ubicación</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Multa</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-inspecciones">
                        <!-- Datos de ejemplo -->
                        <tr>
                            <td>03/08/2025 14:30</td>
                            <td>ABC-123</td>
                            <td>Juan Pérez Gómez</td>
                            <td>Av. Los Chankas 123</td>
                            <td>Rutinaria</td>
                            <td><span class="badge bg-success">Completada</span></td>
                            <td>S/ 150.00</td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="verDetalle(1)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning" onclick="editarInspeccion(1)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-info" onclick="imprimirActa(1)">
                                    <i class="fas fa-print"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>03/08/2025 11:15</td>
                            <td>XYZ-789</td>
                            <td>María López Silva</td>
                            <td>Jr. Grau 456</td>
                            <td>Operativo</td>
                            <td><span class="badge bg-warning">Pendiente</span></td>
                            <td>-</td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="verDetalle(2)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning" onclick="editarInspeccion(2)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-success" onclick="completarInspeccion(2)">
                                    <i class="fas fa-check"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>02/08/2025 16:45</td>
                            <td>DEF-456</td>
                            <td>Carlos Mendoza Ruiz</td>
                            <td>Plaza de Armas</td>
                            <td>Denuncia</td>
                            <td><span class="badge bg-info">Con Observaciones</span></td>
                            <td>S/ 75.00</td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="verDetalle(3)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning" onclick="editarInspeccion(3)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-info" onclick="imprimirActa(3)">
                                    <i class="fas fa-print"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>02/08/2025 09:30</td>
                            <td>GHI-012</td>
                            <td>Ana Torres Vega</td>
                            <td>Mercado Central</td>
                            <td>Rutinaria</td>
                            <td><span class="badge bg-success">Completada</span></td>
                            <td>S/ 200.00</td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="verDetalle(4)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning" onclick="editarInspeccion(4)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-info" onclick="imprimirActa(4)">
                                    <i class="fas fa-print"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>01/08/2025 13:20</td>
                            <td>JKL-345</td>
                            <td>Roberto Flores Castro</td>
                            <td>Terminal Terrestre</td>
                            <td>Post-Accidente</td>
                            <td><span class="badge bg-warning">Pendiente</span></td>
                            <td>-</td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="verDetalle(5)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning" onclick="editarInspeccion(5)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-success" onclick="completarInspeccion(5)">
                                    <i class="fas fa-check"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detalle Inspección -->
<div class="modal fade" id="modalDetalle" tabindex="-1" aria-labelledby="modalDetalleLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetalleLabel">
                    <i class="fas fa-eye mr-2"></i>
                    Detalle de Inspección
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="contenido-detalle">
                <!-- Contenido se carga dinámicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="imprimirDetalle()">
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
        "order": [[ 0, "desc" ]],
        "pageLength": 10
    });
});

function aplicarFiltros() {
    // Implementar filtros
    const fecha = $('#filtro_fecha').val();
    const estado = $('#filtro_estado').val();
    const placa = $('#filtro_placa').val();
    
    // Aquí iría la lógica de filtrado real
    showInfo('Filtros aplicados correctamente');
}

function limpiarFiltros() {
    $('#filtro_fecha').val('');
    $('#filtro_estado').val('');
    $('#filtro_placa').val('');
    showInfo('Filtros limpiados');
}

function verDetalle(id) {
    // Cargar detalle de la inspección
    const contenidoDetalle = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-orange">Información del Vehículo</h6>
                <p><strong>Placa:</strong> ABC-123</p>
                <p><strong>Tipo:</strong> Automóvil</p>
                <p><strong>Marca:</strong> Toyota</p>
                <p><strong>Modelo:</strong> Corolla</p>
                <p><strong>Color:</strong> Blanco</p>
                <p><strong>Año:</strong> 2020</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-orange">Información del Conductor</h6>
                <p><strong>DNI:</strong> 12345678</p>
                <p><strong>Nombre:</strong> Juan Pérez Gómez</p>
                <p><strong>Licencia:</strong> Q12345678</p>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <h6 class="text-orange">Datos de la Inspección</h6>
                <p><strong>Fecha:</strong> 03/08/2025 14:30</p>
                <p><strong>Ubicación:</strong> Av. Los Chankas 123</p>
                <p><strong>Tipo:</strong> Inspección Rutinaria</p>
                <p><strong>Inspector:</strong> {{ auth()->user()->name }}</p>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <h6 class="text-orange">Infracciones Detectadas</h6>
                <ul>
                    <li>Exceso de Velocidad</li>
                    <li>SOAT Vencido</li>
                </ul>
                <h6 class="text-orange">Observaciones</h6>
                <p>Vehículo circulaba a 80 km/h en zona de 50 km/h. SOAT vencido desde hace 2 meses.</p>
                <h6 class="text-orange">Estado y Multa</h6>
                <p><strong>Estado:</strong> <span class="badge bg-success">Completada</span></p>
                <p><strong>Multa:</strong> S/ 150.00</p>
            </div>
        </div>
    `;
    
    $('#contenido-detalle').html(contenidoDetalle);
    $('#modalDetalle').modal('show');
}

function editarInspeccion(id) {
    Swal.fire({
        title: 'Editar Inspección',
        text: '¿Desea editar esta inspección?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ff8c00',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, editar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirigir a formulario de edición
            window.location.href = '{{ route("inspector.nueva-inspeccion") }}?edit=' + id;
        }
    });
}

function completarInspeccion(id) {
    Swal.fire({
        title: 'Completar Inspección',
        text: '¿Está seguro de marcar esta inspección como completada?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, completar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            showSuccess('Inspección marcada como completada');
            // Recargar la tabla
            location.reload();
        }
    });
}

function imprimirActa(id) {
    Swal.fire({
        title: 'Imprimir Acta',
        text: '¿Desea generar e imprimir el acta de esta inspección?',
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#17a2b8',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, imprimir',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            showInfo('Generando acta para impresión...');
            // Simular generación de PDF
            setTimeout(() => {
                showSuccess('Acta generada correctamente');
            }, 2000);
        }
    });
}

function imprimirDetalle() {
    showInfo('Imprimiendo detalle de inspección...');
    $('#modalDetalle').modal('hide');
}
</script>

<!-- DataTables CSS y JS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>
@endpush
@endsection
