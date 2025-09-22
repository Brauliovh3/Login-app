<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-file-alt"></i> Gestión de Actas de Infracción</h4>
                <button class="btn btn-secondary" onclick="hideModules()">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </button>
            </div>
            <hr>
        </div>
    </div>

    <!-- Formulario de nueva acta -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-plus"></i> Nueva Acta de Infracción</h6>
                </div>
                <div class="card-body">
                    <form id="nueva-acta" class="row g-3" method="POST" action="/actas">
                        @csrf
                        
                        <!-- Primera fila -->
                        <div class="col-md-3">
                            <label for="tipo-documento" class="form-label">Tipo de Documento</label>
                            <select class="form-select" id="tipo-documento" name="tipo_documento" required>
                                <option value="">Seleccionar</option>
                                <option value="DNI">DNI</option>
                                <option value="RUC">RUC</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="numero-documento" class="form-label">Número de Documento</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="numero-documento" name="ruc_dni" required>
                                <button type="button" class="btn btn-info" onclick="consultarDocumento()">
                                    <i class="fas fa-search"></i> Consultar
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="licencia" class="form-label">Licencia</label>
                            <input type="text" class="form-control" id="licencia" name="licencia">
                        </div>

                        <!-- Segunda fila -->
                        <div class="col-md-4">
                            <label for="nombres" class="form-label">Nombres y Apellidos</label>
                            <input type="text" class="form-control" id="nombres" name="nombres" required>
                        </div>
                        <div class="col-md-4">
                            <label for="razon-social" class="form-label">Razón Social</label>
                            <input type="text" class="form-control" id="razon-social" name="razon_social">
                        </div>
                        <div class="col-md-4">
                            <label for="placa" class="form-label">Placa del Vehículo</label>
                            <input type="text" class="form-control" id="placa" name="placa" required style="text-transform: uppercase;">
                        </div>

                        <!-- Tercera fila -->
                        <div class="col-md-6">
                            <label for="infraccion" class="form-label">Código de Infracción</label>
                            <select class="form-select" id="infraccion" name="codigo_ds" required>
                                <option value="">Seleccionar infracción</option>
                                <option value="L01">L01 - Exceso de velocidad</option>
                                <option value="G05">G05 - Transporte sin autorización</option>
                                <option value="MG12">MG12 - Conducir en estado de ebriedad</option>
                                <option value="L03">L03 - No respetar señales de tránsito</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="calificacion" class="form-label">Calificación</label>
                            <select class="form-select" id="calificacion" name="calificacion" required>
                                <option value="">Seleccionar</option>
                                <option value="Leve">Leve</option>
                                <option value="Grave">Grave</option>
                                <option value="Muy Grave">Muy Grave</option>
                            </select>
                        </div>

                        <!-- Cuarta fila -->
                        <div class="col-md-6">
                            <label for="fecha-intervencion" class="form-label">Fecha de Intervención</label>
                            <input type="date" class="form-control" id="fecha-intervencion" name="fecha_intervencion" required>
                        </div>
                        <div class="col-md-6">
                            <label for="hora-intervencion" class="form-label">Hora de Intervención</label>
                            <input type="time" class="form-control" id="hora-intervencion" name="hora_intervencion" required>
                        </div>

                        <!-- Descripción -->
                        <div class="col-12">
                            <label for="descripcion" class="form-label">Descripción de los Hechos</label>
                            <textarea class="form-control" id="descripcion" name="descripcion_hechos" rows="3" required placeholder="Detalle los hechos de la infracción..."></textarea>
                        </div>

                        <!-- Botones -->
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-save"></i> Guardar Acta
                            </button>
                            <button type="reset" class="btn btn-secondary me-2">
                                <i class="fas fa-refresh"></i> Limpiar
                            </button>
                            <button type="button" class="btn btn-info" onclick="mostrarConsultas()">
                                <i class="fas fa-eye"></i> Ver Actas
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros de búsqueda -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-search"></i> Filtros de Búsqueda</h6>
                </div>
                <div class="card-body">
                    <form id="filtros-actas" class="row g-3">
                        <div class="col-md-3">
                            <label for="numero-acta" class="form-label">Número de Acta</label>
                            <input type="text" class="form-control" id="numero-acta" placeholder="Ej: DRTC-APU-2025-000001">
                        </div>
                        <div class="col-md-3">
                            <label for="dni-conductor" class="form-label">DNI Conductor</label>
                            <input type="text" class="form-control" id="dni-conductor" placeholder="12345678">
                        </div>
                        <div class="col-md-3">
                            <label for="fecha-desde" class="form-label">Fecha Desde</label>
                            <input type="date" class="form-control" id="fecha-desde">
                        </div>
                        <div class="col-md-3">
                            <label for="fecha-hasta" class="form-label">Fecha Hasta</label>
                            <input type="date" class="form-control" id="fecha-hasta">
                        </div>
                        <div class="col-12">
                            <button type="button" class="btn btn-primary me-2" onclick="buscarActas()">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-refresh"></i> Limpiar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de actas existentes -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-table"></i> Actas Registradas</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Número Acta</th>
                                    <th>Fecha</th>
                                    <th>Conductor</th>
                                    <th>Placa</th>
                                    <th>Infracción</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-actas">
                                <tr>
                                    <td>DRTC-APU-2025-000001</td>
                                    <td>18/09/2025</td>
                                    <td>Juan Pérez</td>
                                    <td>ABC-123</td>
                                    <td>Exceso de velocidad</td>
                                    <td><span class="badge bg-warning">Pendiente</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="verActa('DRTC-APU-2025-000001')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="editarActa('DRTC-APU-2025-000001')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-success" onclick="imprimirActa('DRTC-APU-2025-000001')">
                                            <i class="fas fa-print"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>DRTC-APU-2025-000002</td>
                                    <td>17/09/2025</td>
                                    <td>María García</td>
                                    <td>XYZ-789</td>
                                    <td>Transporte ilegal</td>
                                    <td><span class="badge bg-success">Procesada</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="verActa('DRTC-APU-2025-000002')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-success" onclick="imprimirActa('DRTC-APU-2025-000002')">
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
    </div>
</div>

<script>
// Funciones JavaScript simples y funcionales
function consultarDocumento() {
    const tipo = document.getElementById('tipo-documento').value;
    const numero = document.getElementById('numero-documento').value;
    
    if (!tipo || !numero) {
        alert('Seleccione el tipo de documento e ingrese el número');
        return;
    }
    
    // Simulación de consulta básica
    if (tipo === 'DNI') {
        if (numero === '12345678') {
            document.getElementById('nombres').value = 'Juan Pérez García';
        } else if (numero === '87654321') {
            document.getElementById('nombres').value = 'María González López';
        } else {
            alert('DNI no encontrado. Complete los datos manualmente.');
        }
    } else if (tipo === 'RUC') {
        if (numero === '20123456789') {
            document.getElementById('razon-social').value = 'Transportes Lima SAC';
            document.getElementById('nombres').value = 'Carlos Ruiz (Representante)';
        } else {
            alert('RUC no encontrado. Complete los datos manualmente.');
        }
    }
}

function buscarActas() {
    const numeroActa = document.getElementById('numero-acta').value;
    const dniConductor = document.getElementById('dni-conductor').value;
    const fechaDesde = document.getElementById('fecha-desde').value;
    const fechaHasta = document.getElementById('fecha-hasta').value;
    
    console.log('Ejecutando búsqueda con filtros:', {
        numeroActa, dniConductor, fechaDesde, fechaHasta
    });
    
    alert('Búsqueda ejecutada. Resultados mostrados en la tabla.');
}

function mostrarConsultas() {
    alert('Mostrando historial de consultas y actas registradas en la tabla inferior.');
}

function verActa(numeroActa) {
    alert(`Visualizando detalles del acta: ${numeroActa}`);
}

function editarActa(numeroActa) {
    alert(`Cargando acta para edición: ${numeroActa}`);
}

function imprimirActa(numeroActa) {
    alert(`Generando PDF para impresión del acta: ${numeroActa}`);
}

// Auto-completar fecha y hora actuales al cargar
document.addEventListener('DOMContentLoaded', function() {
    const fechaInput = document.getElementById('fecha-intervencion');
    const horaInput = document.getElementById('hora-intervencion');
    
    if (fechaInput && horaInput) {
        const ahora = new Date();
        fechaInput.value = ahora.toISOString().split('T')[0];
        horaInput.value = ahora.toTimeString().split(' ')[0].substring(0, 5);
});
</script>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de resultados -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-table"></i> Actas Registradas</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Número Acta</th>
                                    <th>Fecha</th>
                                    <th>Conductor</th>
                                    <th>Placa</th>
                                    <th>Infracción</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-actas">
                                <tr>
                                    <td>DRTC-APU-2025-000001</td>
                                    <td>18/09/2025</td>
                                    <td>Juan Pérez</td>
                                    <td>ABC-123</td>
                                    <td>Exceso de velocidad</td>
                                    <td><span class="badge bg-warning">Pendiente</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="verActa('DRTC-APU-2025-000001')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="editarActa('DRTC-APU-2025-000001')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-success" onclick="imprimirActa('DRTC-APU-2025-000001')">
                                            <i class="fas fa-print"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>DRTC-APU-2025-000002</td>
                                    <td>17/09/2025</td>
                                    <td>María García</td>
                                    <td>XYZ-789</td>
                                    <td>Transporte ilegal</td>
                                    <td><span class="badge bg-success">Procesada</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="verActa('DRTC-APU-2025-000002')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-success" onclick="imprimirActa('DRTC-APU-2025-000002')">
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
    </div>
</div>

<!-- MODAL: NUEVA ACTA -->
<div class="floating-modal" id="modal-nueva-acta" style="display: none;">
    <div class="modal-content-wrapper">
        <div class="modal-header-custom">
            <h4 class="mb-0 fw-bold">
                <i class="fas fa-plus-circle me-2"></i>
                REGISTRO DE NUEVA ACTA DE FISCALIZACIÓN DRTC
            </h4>
            <button class="close-modal" onclick="cerrarModal('modal-nueva-acta')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body-custom">
            <form id="form-nueva-acta" action="/actas" method="POST">
                @csrf
                
                <!-- Campos automáticos ocultos -->
                <input type="hidden" id="fecha_inspeccion_hidden" name="fecha_inspeccion">
                <input type="hidden" id="hora_inicio_hidden" name="hora_inicio">
                <input type="hidden" name="inspector_principal" value="{{ Auth::check() ? Auth::user()->name : 'Sistema' }}">

                <!-- DATOS DEL CONDUCTOR -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-user"></i> Datos del Conductor</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="tipo_documento" class="form-label">Tipo Documento</label>
                                <select class="form-select" id="tipo_documento" name="tipo_documento" required>
                                    <option value="">Seleccionar</option>
                                    <option value="DNI">DNI</option>
                                    <option value="RUC">RUC</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="ruc_dni" class="form-label">Número de Documento</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="ruc_dni" name="ruc_dni" required>
                                    <button type="button" class="btn btn-info" onclick="consultarDocumento()">
                                        <i class="fas fa-search"></i> Consultar
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="licencia" class="form-label">Licencia</label>
                                <input type="text" class="form-control" id="licencia" name="licencia">
                            </div>
                            <div class="col-md-6">
                                <label for="nombres" class="form-label">Nombres y Apellidos</label>
                                <input type="text" class="form-control" id="nombres" name="nombres" required>
                            </div>
                            <div class="col-md-6">
                                <label for="razon_social" class="form-label">Razón Social (Empresa)</label>
                                <input type="text" class="form-control" id="razon_social" name="razon_social">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DATOS DEL VEHÍCULO -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="fas fa-car"></i> Datos del Vehículo</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="placa" class="form-label">Placa del Vehículo</label>
                                <input type="text" class="form-control" id="placa" name="placa" required style="text-transform: uppercase;">
                            </div>
                            <div class="col-md-4">
                                <label for="marca" class="form-label">Marca</label>
                                <input type="text" class="form-control" id="marca" name="marca">
                            </div>
                            <div class="col-md-4">
                                <label for="modelo" class="form-label">Modelo</label>
                                <input type="text" class="form-control" id="modelo" name="modelo">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DATOS DE LA INFRACCIÓN -->
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Datos de la Infracción</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="fecha_intervencion" class="form-label">Fecha de Intervención</label>
                                <input type="date" class="form-control" id="fecha_intervencion" name="fecha_intervencion" required>
                            </div>
                            <div class="col-md-6">
                                <label for="hora_intervencion" class="form-label">Hora de Intervención</label>
                                <input type="time" class="form-control" id="hora_intervencion" name="hora_intervencion" required>
                            </div>
                            <div class="col-md-6">
                                <label for="codigo_ds" class="form-label">Código de Infracción</label>
                                <select class="form-select" id="codigo_ds" name="codigo_ds" required>
                                    <option value="">Seleccionar infracción</option>
                                    <option value="L01">L01 - Exceso de velocidad</option>
                                    <option value="G05">G05 - Transporte sin autorización</option>
                                    <option value="MG12">MG12 - Conducir en estado de ebriedad</option>
                                    <option value="L03">L03 - No respetar señales de tránsito</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="calificacion" class="form-label">Calificación</label>
                                <select class="form-select" id="calificacion" name="calificacion" required>
                                    <option value="">Seleccionar</option>
                                    <option value="Leve">Leve</option>
                                    <option value="Grave">Grave</option>
                                    <option value="Muy Grave">Muy Grave</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="descripcion_hechos" class="form-label">Descripción de los Hechos</label>
                                <textarea class="form-control" id="descripcion_hechos" name="descripcion_hechos" rows="4" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="monto_multa" class="form-label">Monto de la Multa (S/)</label>
                                <input type="number" class="form-control" id="monto_multa" name="monto_multa" step="0.01">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones del formulario -->
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" onclick="cerrarModal('modal-nueva-acta')">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Acta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Estilos para el modal flotante */
.floating-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content-wrapper {
    background: white;
    border-radius: 10px;
    max-width: 90%;
    max-height: 90%;
    overflow-y: auto;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.modal-header-custom {
    background: linear-gradient(135deg, #ff8c00, #e67e22);
    color: white;
    padding: 1rem;
    border-radius: 10px 10px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-body-custom {
    padding: 1.5rem;
}

.close-modal {
    background: none;
    border: none;
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background-color 0.3s;
}

.close-modal:hover {
    background-color: rgba(255, 255, 255, 0.2);
}
</style>

<script>
// Funciones principales para el módulo de actas
function abrirModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
        // Auto-completar fecha y hora actuales
        const ahora = new Date();
        const fechaInput = document.getElementById('fecha_intervencion');
        const horaInput = document.getElementById('hora_intervencion');
        
        if (fechaInput) {
            fechaInput.value = ahora.toISOString().split('T')[0];
        }
        if (horaInput) {
            horaInput.value = ahora.toTimeString().split(' ')[0].substring(0, 5);
        }
    }
}

function cerrarModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    }
}

function consultarDocumento() {
    const tipo = document.getElementById('tipo_documento').value;
    const numero = document.getElementById('ruc_dni').value;
    
    if (!tipo || !numero) {
        alert('Seleccione el tipo de documento e ingrese el número');
        return;
    }
    
    // Simulación de consulta
    if (tipo === 'DNI' && numero === '12345678') {
        document.getElementById('nombres').value = 'Juan Pérez García';
    } else if (tipo === 'RUC' && numero === '20123456789') {
        document.getElementById('razon_social').value = 'Transportes Lima SAC';
        document.getElementById('nombres').value = 'María González (Representante)';
    } else {
        alert('Documento no encontrado. Complete los datos manualmente.');
    }
}

function ejecutarBusqueda() {
    const numeroActa = document.getElementById('numero-acta').value;
    const dniConductor = document.getElementById('dni-conductor').value;
    const fechaDesde = document.getElementById('fecha-desde').value;
    const fechaHasta = document.getElementById('fecha-hasta').value;
    
    console.log('Buscando actas con filtros:', {
        numeroActa, dniConductor, fechaDesde, fechaHasta
    });
    
    alert('Búsqueda ejecutada. Los resultados aparecerían en la tabla.');
}

function buscarActas() {
    alert('Abriendo panel de búsqueda avanzada');
}

function verConsultas() {
    alert('Abriendo historial de consultas realizadas');
}

function exportarReporte() {
    alert('Generando reporte en Excel/PDF');
}

function verActa(numeroActa) {
    alert(`Visualizando acta: ${numeroActa}`);
}

function editarActa(numeroActa) {
    alert(`Editando acta: ${numeroActa}`);
}

function imprimirActa(numeroActa) {
    alert(`Imprimiendo acta: ${numeroActa}`);
}

// Manejar envío del formulario
document.addEventListener('DOMContentLoaded', function() {
    const formNuevaActa = document.getElementById('form-nueva-acta');
    if (formNuevaActa) {
        formNuevaActa.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validar campos requeridos
            const campos = ['tipo_documento', 'ruc_dni', 'nombres', 'placa', 'fecha_intervencion', 'hora_intervencion', 'codigo_ds', 'calificacion', 'descripcion_hechos'];
            let valid = true;
            
            campos.forEach(campo => {
                const element = document.getElementById(campo);
                if (element && !element.value.trim()) {
                    element.classList.add('is-invalid');
                    valid = false;
                } else if (element) {
                    element.classList.remove('is-invalid');
                }
            });
            
            if (!valid) {
                alert('Por favor complete todos los campos requeridos');
                return;
            }
            
            // Simular guardado exitoso
            alert('Acta guardada exitosamente');
            cerrarModal('modal-nueva-acta');
            formNuevaActa.reset();
        });
    }
});

// Cerrar modal al hacer clic fuera del contenido
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('floating-modal')) {
        const modalId = e.target.id;
        cerrarModal(modalId);
    }
});
</script>