<?php $__env->startSection('title', 'Calendario de Inspecciones'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-calendar-alt me-2" style="color: #ff8c00;"></i>
                    Calendario de Inspecciones
                </h2>
                <div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevaCitaModal">
                        <i class="fas fa-plus me-2"></i>Nueva Cita
                    </button>
                    <button class="btn btn-outline-secondary" onclick="hoy()">
                        <i class="fas fa-home me-2"></i>Hoy
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Controles del calendario -->
    <div class="card mb-4" style="border-color: #ff8c00;">
        <div class="card-header" style="background-color: #ff8c00; color: white;">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar me-2"></i>Julio 2025
                    </h5>
                </div>
                <div class="col-md-4 text-center">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-light" onclick="mesAnterior()">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button type="button" class="btn btn-outline-light" onclick="mesSiguiente()">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="vista" id="vistaMes" checked>
                        <label class="btn btn-outline-light" for="vistaMes">Mes</label>
                        
                        <input type="radio" class="btn-check" name="vista" id="vistaSemana">
                        <label class="btn btn-outline-light" for="vistaSemana">Semana</label>
                        
                        <input type="radio" class="btn-check" name="vista" id="vistaDia">
                        <label class="btn btn-outline-light" for="vistaDia">Día</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendario principal -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0" id="calendario">
                    <thead style="background-color: #fff3e0;">
                        <tr>
                            <th class="text-center py-3" style="color: #ff8c00; width: 14.28%;">Domingo</th>
                            <th class="text-center py-3" style="color: #ff8c00; width: 14.28%;">Lunes</th>
                            <th class="text-center py-3" style="color: #ff8c00; width: 14.28%;">Martes</th>
                            <th class="text-center py-3" style="color: #ff8c00; width: 14.28%;">Miércoles</th>
                            <th class="text-center py-3" style="color: #ff8c00; width: 14.28%;">Jueves</th>
                            <th class="text-center py-3" style="color: #ff8c00; width: 14.28%;">Viernes</th>
                            <th class="text-center py-3" style="color: #ff8c00; width: 14.28%;">Sábado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="height: 120px;">
                            <td class="p-2 align-top text-muted">29</td>
                            <td class="p-2 align-top text-muted">30</td>
                            <td class="p-2 align-top">
                                <div class="fw-bold">1</div>
                                <div class="mt-1">
                                    <small class="badge bg-primary">09:00 - Inspección ABC-123</small>
                                </div>
                            </td>
                            <td class="p-2 align-top">
                                <div class="fw-bold">2</div>
                            </td>
                            <td class="p-2 align-top">
                                <div class="fw-bold">3</div>
                                <div class="mt-1">
                                    <small class="badge bg-warning">14:30 - Operativo Centro</small>
                                </div>
                            </td>
                            <td class="p-2 align-top">
                                <div class="fw-bold">4</div>
                            </td>
                            <td class="p-2 align-top">
                                <div class="fw-bold">5</div>
                            </td>
                        </tr>
                        <tr style="height: 120px;">
                            <td class="p-2 align-top">
                                <div class="fw-bold">6</div>
                            </td>
                            <td class="p-2 align-top">
                                <div class="fw-bold">7</div>
                                <div class="mt-1">
                                    <small class="badge bg-success">08:00 - Control XYZ-789</small>
                                </div>
                            </td>
                            <td class="p-2 align-top">
                                <div class="fw-bold">8</div>
                            </td>
                            <td class="p-2 align-top">
                                <div class="fw-bold">9</div>
                            </td>
                            <td class="p-2 align-top">
                                <div class="fw-bold">10</div>
                                <div class="mt-1">
                                    <small class="badge bg-info">10:00 - Reunión Equipo</small>
                                </div>
                            </td>
                            <td class="p-2 align-top">
                                <div class="fw-bold">11</div>
                            </td>
                            <td class="p-2 align-top">
                                <div class="fw-bold">12</div>
                            </td>
                        </tr>
                        <tr style="height: 120px;">
                            <td class="p-2 align-top">
                                <div class="fw-bold">13</div>
                            </td>
                            <td class="p-2 align-top">
                                <div class="fw-bold">14</div>
                            </td>
                            <td class="p-2 align-top">
                                <div class="fw-bold">15</div>
                                <div class="mt-1">
                                    <small class="badge bg-danger">16:00 - Operativo Urgente</small>
                                </div>
                            </td>
                            <td class="p-2 align-top">
                                <div class="fw-bold">16</div>
                            </td>
                            <td class="p-2 align-top">
                                <div class="fw-bold">17</div>
                            </td>
                            <td class="p-2 align-top">
                                <div class="fw-bold">18</div>
                            </td>
                            <td class="p-2 align-top">
                                <div class="fw-bold">19</div>
                            </td>
                        </tr>
                        <tr style="height: 120px;">
                            <td class="p-2 align-top">
                                <div class="fw-bold">20</div>
                            </td>
                            <td class="p-2 align-top">
                                <div class="fw-bold">21</div>
                                <div class="mt-1">
                                    <small class="badge bg-primary">11:30 - Control DEF-456</small>
                                </div>
                            </td>
                            <td class="p-2 align-top">
                                <div class="fw-bold">22</div>
                            </td>
                            <td class="p-2 align-top">
                                <div class="fw-bold">23</div>
                            </td>
                            <td class="p-2 align-top">
                                <div class="fw-bold">24</div>
                            </td>
                            <td class="p-2 align-top">
                                <div class="fw-bold">25</div>
                            </td>
                            <td class="p-2 align-top">
                                <div class="fw-bold">26</div>
                            </td>
                        </tr>
                        <tr style="height: 120px;">
                            <td class="p-2 align-top">
                                <div class="fw-bold">27</div>
                            </td>
                            <td class="p-2 align-top">
                                <div class="fw-bold">28</div>
                            </td>
                            <td class="p-2 align-top">
                                <div class="fw-bold">29</div>
                            </td>
                            <td class="p-2 align-top" style="background-color: #fff3e0;">
                                <div class="fw-bold" style="color: #ff8c00;">30</div>
                                <div class="mt-1">
                                    <small class="badge bg-primary">Hoy</small>
                                </div>
                            </td>
                            <td class="p-2 align-top">
                                <div class="fw-bold">31</div>
                                <div class="mt-1">
                                    <small class="badge bg-success">15:00 - Inspección Final</small>
                                </div>
                            </td>
                            <td class="p-2 align-top text-muted">1</td>
                            <td class="p-2 align-top text-muted">2</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Próximas inspecciones -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header" style="background-color: #fff3e0; border-color: #ff8c00;">
                    <h5 class="mb-0" style="color: #ff8c00;">
                        <i class="fas fa-clock me-2"></i>Próximas Inspecciones
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Inspección Vehicular</h6>
                                <p class="mb-1">Placa: ABC-123 - Juan Pérez</p>
                                <small>31/07/2025 - 09:00 AM</small>
                            </div>
                            <span class="badge bg-primary rounded-pill">Pendiente</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Control de Carga</h6>
                                <p class="mb-1">Placa: DEF-456 - María López</p>
                                <small>31/07/2025 - 15:00 PM</small>
                            </div>
                            <span class="badge bg-warning rounded-pill">Programado</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header" style="background-color: #fff3e0; border-color: #ff8c00;">
                    <h5 class="mb-0" style="color: #ff8c00;">
                        <i class="fas fa-chart-pie me-2"></i>Resumen del Mes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-primary">45</h4>
                            <small>Inspecciones Realizadas</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success">12</h4>
                            <small>Inspecciones Programadas</small>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-warning">8</h4>
                            <small>Actas Generadas</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-info">95%</h4>
                            <small>Cumplimiento</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva Cita -->
<div class="modal fade" id="nuevaCitaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #ff8c00; color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Nueva Cita de Inspección
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="nuevaCitaForm">
                    <div class="mb-3">
                        <label for="fecha_cita" class="form-label">Fecha y Hora *</label>
                        <input type="datetime-local" class="form-control" id="fecha_cita" required>
                    </div>
                    <div class="mb-3">
                        <label for="tipo_inspeccion" class="form-label">Tipo de Inspección *</label>
                        <select class="form-select" id="tipo_inspeccion" required>
                            <option value="">Seleccionar...</option>
                            <option value="vehicular">Inspección Vehicular</option>
                            <option value="carga">Control de Carga</option>
                            <option value="operativo">Operativo</option>
                            <option value="seguimiento">Seguimiento</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="placa_cita" class="form-label">Placa del Vehículo</label>
                        <input type="text" class="form-control" id="placa_cita">
                    </div>
                    <div class="mb-3">
                        <label for="conductor_cita" class="form-label">Conductor</label>
                        <input type="text" class="form-control" id="conductor_cita">
                    </div>
                    <div class="mb-3">
                        <label for="ubicacion_cita" class="form-label">Ubicación</label>
                        <input type="text" class="form-control" id="ubicacion_cita">
                    </div>
                    <div class="mb-3">
                        <label for="observaciones_cita" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observaciones_cita" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarCita()">
                    <i class="fas fa-save me-2"></i>Programar Cita
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function mesAnterior() {
    showInfo('Navegando al mes anterior');
}

function mesSiguiente() {
    showInfo('Navegando al mes siguiente');
}

function hoy() {
    showInfo('Navegando a la fecha actual');
}

function guardarCita() {
    const fecha = document.getElementById('fecha_cita').value;
    const tipo = document.getElementById('tipo_inspeccion').value;
    
    if (!fecha || !tipo) {
        showError('Por favor complete todos los campos obligatorios');
        return;
    }
    
    showSuccess('Cita programada exitosamente');
    
    const modal = bootstrap.Modal.getInstance(document.getElementById('nuevaCitaModal'));
    modal.hide();
    
    document.getElementById('nuevaCitaForm').reset();
}

// Agregar eventos de clic a las celdas del calendario
document.addEventListener('DOMContentLoaded', function() {
    const celdas = document.querySelectorAll('#calendario td');
    celdas.forEach(function(celda) {
        celda.style.cursor = 'pointer';
        celda.addEventListener('click', function() {
            if (!celda.classList.contains('text-muted')) {
                showInfo('Seleccionó el día ' + celda.querySelector('.fw-bold').textContent);
            }
        });
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Login-app\resources\views\fiscalizador\calendario.blade.php ENDPATH**/ ?>