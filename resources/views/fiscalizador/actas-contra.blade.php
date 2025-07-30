@extends('layouts.dashboard')

@section('title', 'Actas de Contra')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-file-alt me-2" style="color: #ff8c00;"></i>
                    Actas de Contra
                </h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevaActaModal">
                    <i class="fas fa-plus me-2"></i>Nueva Acta
                </button>
            </div>
        </div>
    </div>

    <!-- Filtros de búsqueda -->
    <div class="card mb-4" style="border-color: #ff8c00;">
        <div class="card-header" style="background-color: #ff8c00; color: white;">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label for="filtro_numero" class="form-label">N° de Acta</label>
                    <input type="text" class="form-control" id="filtro_numero" placeholder="ACT-2025-001">
                </div>
                <div class="col-md-3">
                    <label for="filtro_placa" class="form-label">Placa</label>
                    <input type="text" class="form-control" id="filtro_placa" placeholder="ABC-123">
                </div>
                <div class="col-md-3">
                    <label for="filtro_fecha" class="form-label">Fecha</label>
                    <input type="date" class="form-control" id="filtro_fecha">
                </div>
                <div class="col-md-3">
                    <label for="filtro_estado" class="form-label">Estado</label>
                    <select class="form-select" id="filtro_estado">
                        <option value="">Todos</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="pagada">Pagada</option>
                        <option value="anulada">Anulada</option>
                        <option value="en_cobranza">En Cobranza</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <button class="btn btn-primary me-2">
                        <i class="fas fa-search me-2"></i>Buscar
                    </button>
                    <button class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>25</h4>
                            <p class="mb-0">Pendientes</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>18</h4>
                            <p class="mb-0">Pagadas</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>8</h4>
                            <p class="mb-0">En Cobranza</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-secondary">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>2</h4>
                            <p class="mb-0">Anuladas</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de actas -->
    <div class="card">
        <div class="card-header" style="background-color: #fff3e0; border-color: #ff8c00;">
            <h5 class="mb-0" style="color: #ff8c00;">
                <i class="fas fa-list me-2"></i>Lista de Actas de Contra
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead style="background-color: #ff8c00; color: white;">
                        <tr>
                            <th>N° Acta</th>
                            <th>Fecha/Hora</th>
                            <th>Placa</th>
                            <th>Conductor</th>
                            <th>Infracción</th>
                            <th>Monto</th>
                            <th>Vencimiento</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>ACT-2025-001</strong></td>
                            <td>30/07/2025 08:30</td>
                            <td><span class="badge bg-dark">ABC-123</span></td>
                            <td>Juan Pérez Gómez</td>
                            <td>G.01 - Exceso de velocidad</td>
                            <td><strong>S/ 462.00</strong></td>
                            <td>15/08/2025</td>
                            <td><span class="badge bg-warning">Pendiente</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" title="Ver detalle">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" title="Imprimir">
                                    <i class="fas fa-print"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="Anular">
                                    <i class="fas fa-ban"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>ACT-2025-002</strong></td>
                            <td>30/07/2025 09:15</td>
                            <td><span class="badge bg-dark">XYZ-789</span></td>
                            <td>María López Silva</td>
                            <td>L.02 - Documentos vencidos</td>
                            <td><strong>S/ 231.00</strong></td>
                            <td>14/08/2025</td>
                            <td><span class="badge bg-success">Pagada</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" title="Ver detalle">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" title="Ver comprobante">
                                    <i class="fas fa-receipt"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" title="Imprimir">
                                    <i class="fas fa-print"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>ACT-2025-003</strong></td>
                            <td>29/07/2025 16:45</td>
                            <td><span class="badge bg-dark">DEF-456</span></td>
                            <td>Carlos Ruiz Mendoza</td>
                            <td>MG.03 - Transporte ilegal</td>
                            <td><strong>S/ 4,620.00</strong></td>
                            <td class="text-danger"><strong>13/08/2025</strong></td>
                            <td><span class="badge bg-danger">En Cobranza</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" title="Ver detalle">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning" title="Gestionar cobranza">
                                    <i class="fas fa-dollar-sign"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" title="Imprimir">
                                    <i class="fas fa-print"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva Acta -->
<div class="modal fade" id="nuevaActaModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #ff8c00; color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Nueva Acta de Contra
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="nuevaActaForm">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Datos del Vehículo y Conductor</h6>
                            <hr>
                            <div class="mb-3">
                                <label for="placa_vehiculo" class="form-label">Placa del Vehículo *</label>
                                <input type="text" class="form-control" id="placa_vehiculo" required>
                            </div>
                            <div class="mb-3">
                                <label for="dni_conductor" class="form-label">DNI del Conductor *</label>
                                <input type="text" class="form-control" id="dni_conductor" maxlength="8" required>
                            </div>
                            <div class="mb-3">
                                <label for="nombre_conductor" class="form-label">Nombre del Conductor</label>
                                <input type="text" class="form-control" id="nombre_conductor" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="licencia_conductor" class="form-label">N° Licencia</label>
                                <input type="text" class="form-control" id="licencia_conductor" readonly>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="text-primary">Datos de la Infracción</h6>
                            <hr>
                            <div class="mb-3">
                                <label for="fecha_infraccion" class="form-label">Fecha y Hora *</label>
                                <input type="datetime-local" class="form-control" id="fecha_infraccion" required>
                            </div>
                            <div class="mb-3">
                                <label for="lugar_infraccion" class="form-label">Lugar de la Infracción *</label>
                                <input type="text" class="form-control" id="lugar_infraccion" required>
                            </div>
                            <div class="mb-3">
                                <label for="infraccion_id" class="form-label">Tipo de Infracción *</label>
                                <select class="form-select" id="infraccion_id" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="1">G.01 - Exceso de velocidad (S/ 462.00)</option>
                                    <option value="2">L.02 - Documentos vencidos (S/ 231.00)</option>
                                    <option value="3">MG.03 - Transporte ilegal (S/ 4,620.00)</option>
                                    <option value="4">G.05 - No usar cinturón (S/ 462.00)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="monto_multa" class="form-label">Monto de la Multa</label>
                                <input type="number" class="form-control" id="monto_multa" step="0.01" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary">Observaciones y Evidencias</h6>
                            <hr>
                            <div class="mb-3">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="observaciones" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="evidencias" class="form-label">Evidencias (Fotos, Videos)</label>
                                <input type="file" class="form-control" id="evidencias" multiple accept="image/*,video/*">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarActa()">
                    <i class="fas fa-save me-2"></i>Generar Acta
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function guardarActa() {
    // Validar formulario
    const placa = document.getElementById('placa_vehiculo').value;
    const dni = document.getElementById('dni_conductor').value;
    const fecha = document.getElementById('fecha_infraccion').value;
    const lugar = document.getElementById('lugar_infraccion').value;
    const infraccion = document.getElementById('infraccion_id').value;
    
    if (!placa || !dni || !fecha || !lugar || !infraccion) {
        showError('Por favor complete todos los campos obligatorios');
        return;
    }
    
    // Simular guardado
    showSuccess('Acta de contra generada exitosamente');
    
    // Cerrar modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('nuevaActaModal'));
    modal.hide();
    
    // Limpiar formulario
    document.getElementById('nuevaActaForm').reset();
}

// Actualizar monto al seleccionar infracción
document.getElementById('infraccion_id').addEventListener('change', function() {
    const select = this;
    const montoInput = document.getElementById('monto_multa');
    
    if (select.value === '1') montoInput.value = '462.00';
    else if (select.value === '2') montoInput.value = '231.00';
    else if (select.value === '3') montoInput.value = '4620.00';
    else if (select.value === '4') montoInput.value = '462.00';
    else montoInput.value = '';
});
</script>
@endsection
