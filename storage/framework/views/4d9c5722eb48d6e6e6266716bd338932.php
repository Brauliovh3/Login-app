<?php $__env->startSection('title', 'Inspecciones - Fiscalizador'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-search-plus me-2" style="color: #ff8c00;"></i>
                    Inspecciones
                </h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevaInspeccionModal">
                    <i class="fas fa-plus me-2"></i>Nueva Inspección
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
                    <label for="filtro_placa" class="form-label">Placa del Vehículo</label>
                    <input type="text" class="form-control" id="filtro_placa" placeholder="Ej: ABC-123">
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
                        <option value="en_proceso">En Proceso</option>
                        <option value="procesada">Completada</option>
                        <option value="cancelada">Cancelada</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtro_inspector" class="form-label">Inspector</label>
                    <select class="form-select" id="filtro_inspector">
                        <option value="">Todos</option>
                        <option value="1">Inspector 001</option>
                        <option value="2">Inspector 002</option>
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

    <!-- Tabla de inspecciones -->
    <div class="card">
        <div class="card-header" style="background-color: #fff3e0; border-color: #ff8c00;">
            <h5 class="mb-0" style="color: #ff8c00;">
                <i class="fas fa-list me-2"></i>Lista de Inspecciones
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead style="background-color: #ff8c00; color: white;">
                        <tr>
                            <th>N° Inspección</th>
                            <th>Fecha/Hora</th>
                            <th>Placa</th>
                            <th>Conductor</th>
                            <th>Inspector</th>
                            <th>Estado</th>
                            <th>Infracciones</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>INS-2025-001</strong></td>
                            <td>30/07/2025 08:30</td>
                            <td><span class="badge bg-dark">ABC-123</span></td>
                            <td>Juan Pérez Gómez</td>
                            <td>Inspector 001</td>
                            <td><span class="badge bg-warning">En Proceso</span></td>
                            <td><span class="badge bg-danger">2</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" title="Ver detalle">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="Generar acta">
                                    <i class="fas fa-file-alt"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>INS-2025-002</strong></td>
                            <td>30/07/2025 09:15</td>
                            <td><span class="badge bg-dark">XYZ-789</span></td>
                            <td>María López Silva</td>
                            <td>Inspector 002</td>
                            <td><span class="badge bg-success">Completada</span></td>
                            <td><span class="badge bg-success">0</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" title="Ver detalle">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" title="Imprimir reporte">
                                    <i class="fas fa-print"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>INS-2025-003</strong></td>
                            <td>30/07/2025 10:45</td>
                            <td><span class="badge bg-dark">DEF-456</span></td>
                            <td>Carlos Ruiz Mendoza</td>
                            <td>Inspector 001</td>
                            <td><span class="badge bg-info">Pendiente</span></td>
                            <td><span class="badge bg-secondary">-</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-success" title="Iniciar inspección">
                                    <i class="fas fa-play"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning" title="Cancelar">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva Inspección -->
<div class="modal fade" id="nuevaInspeccionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #ff8c00; color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Nueva Inspección
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="nuevaInspeccionForm">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="placa_vehiculo" class="form-label">Placa del Vehículo *</label>
                            <input type="text" class="form-control" id="placa_vehiculo" required>
                        </div>
                        <div class="col-md-6">
                            <label for="dni_conductor" class="form-label">DNI del Conductor *</label>
                            <input type="text" class="form-control" id="dni_conductor" maxlength="8" required>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="ubicacion" class="form-label">Ubicación *</label>
                            <input type="text" class="form-control" id="ubicacion" required>
                        </div>
                        <div class="col-md-6">
                            <label for="motivo" class="form-label">Motivo de Inspección</label>
                            <select class="form-select" id="motivo">
                                <option value="rutina">Inspección de Rutina</option>
                                <option value="denuncia">Por Denuncia</option>
                                <option value="operativo">Operativo Especial</option>
                                <option value="seguimiento">Seguimiento</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <label for="observaciones" class="form-label">Observaciones</label>
                            <textarea class="form-control" id="observaciones" rows="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarInspeccion()">
                    <i class="fas fa-save me-2"></i>Crear Inspección
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function guardarInspeccion() {
    // Validar formulario
    const placa = document.getElementById('placa_vehiculo').value;
    const dni = document.getElementById('dni_conductor').value;
    const ubicacion = document.getElementById('ubicacion').value;
    
    if (!placa || !dni || !ubicacion) {
        showError('Por favor complete todos los campos obligatorios');
        return;
    }
    
    // Simular guardado
    showSuccess('Inspección creada exitosamente');
    
    // Cerrar modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('nuevaInspeccionModal'));
    modal.hide();
    
    // Limpiar formulario
    document.getElementById('nuevaInspeccionForm').reset();
    
    // Aquí iría la llamada AJAX real
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Login-app\resources\views\fiscalizador\inspecciones.blade.php ENDPATH**/ ?>