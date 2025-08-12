@extends('layouts.dashboard')

@section('title', 'Gestión de Actas de Fiscalización')

@section('content')
<style>
    :root {
        --drtc-orange: #ff8c00;
        --drtc-dark-orange: #e67c00;
        --drtc-light-orange: #ffffff;
        --drtc-orange-bg: #fff4e6;
        --drtc-navy: #1e3a8a;
    }
    
    .bg-drtc-orange { background-color: var(--drtc-orange) !important; }
    .bg-drtc-dark { background-color: var(--drtc-dark-orange) !important; }
    .bg-drtc-light { background-color: var(--drtc-light-orange) !important; }
    .bg-drtc-soft { background-color: var(--drtc-orange-bg) !important; }
    .bg-drtc-navy { background-color: var(--drtc-navy) !important; }
    .text-drtc-orange { color: var(--drtc-orange) !important; }
    .text-drtc-navy { color: var(--drtc-navy) !important; }
    .border-drtc-orange { border-color: var(--drtc-orange) !important; }
    
    .action-btn {
        background: white;
        border: 2px solid #e0e0e0;
        border-radius: 15px;
        padding: 15px;
        text-align: center;
        transition: all 0.3s ease;
        height: 100px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        text-decoration: none;
        color: #333;
        margin-bottom: 15px;
    }
    
    .action-btn:hover {
        border-color: var(--drtc-orange);
        background: var(--drtc-orange-bg);
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(255, 140, 0, 0.2);
        color: var(--drtc-orange);
        text-decoration: none;
    }
    
    .action-btn i {
        font-size: 24px;
        margin-bottom: 5px;
        color: var(--drtc-orange);
    }
    
    .action-btn:hover i {
        color: var(--drtc-dark-orange);
    }
</style>

<div class="container-fluid">
    <!-- Header principal -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0" style="background: linear-gradient(135deg, var(--drtc-orange), var(--drtc-dark-orange)); border-radius: 20px;">
                <div class="card-body py-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <h1 class="mb-2 fw-bold text-white">
                                <i class="fas fa-file-contract me-3"></i>
                                Gestión de Actas de Fiscalización DRTC
                            </h1>
                            <p class="mb-0 fs-5 text-white opacity-75">
                                <i class="fas fa-user me-2"></i>Inspector: <strong>{{ Auth::user()->name }}</strong>
                                <span class="ms-3">
                                    <i class="fas fa-calendar-alt me-2"></i>{{ date('d/m/Y') }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de acción principales -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0" style="border-radius: 20px;">
                <div class="card-header bg-drtc-orange text-white">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-tasks me-2"></i>Acciones de Fiscalización</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                            <div class="action-btn" onclick="abrirModal('modal-nueva-acta')">
                                <i class="fas fa-plus-circle"></i>
                                <strong>Nueva Acta</strong>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                            <div class="action-btn" onclick="abrirModal('modal-editar-acta')">
                                <i class="fas fa-edit"></i>
                                <strong>Editar Acta</strong>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                            <div class="action-btn" onclick="abrirModal('modal-consultas')">
                                <i class="fas fa-search"></i>
                                <strong>Consultas</strong>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                            <div class="action-btn" onclick="abrirModal('modal-eliminar-acta')">
                                <i class="fas fa-trash-alt"></i>
                                <strong>Eliminar Acta</strong>
                            </div>
                        </div>
                    </div>
                </div>
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
                                <input type="datetime-local" class="form-control bg-light" id="fecha_infraccion" value="{{ now()->format('Y-m-d\TH:i') }}" readonly>
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
    const form = document.getElementById('form-nueva-acta');
    const formData = new FormData(form);
    
    // Validaciones básicas
    const placa = formData.get('placa_1');
    const conductor = formData.get('nombre_conductor_1');
    const lugar = formData.get('lugar_intervencion');
    
    if (!placa || !conductor || !lugar) {
        showError('Por favor complete todos los campos obligatorios');
        return;
    }
    
    // Mostrar indicador de carga
    const submitBtn = document.querySelector('#form-nueva-acta button[type="submit"]');
    const originalText = submitBtn ? submitBtn.innerHTML : '';
    if (submitBtn) {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
        submitBtn.disabled = true;
    }
    
    // Preparar datos para envío
    const data = Object.fromEntries(formData.entries());
    
    // Enviar datos al servidor con seguimiento automático de tiempo
    fetch('/api/actas', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Guardar ID del acta para seguimiento
            actaIdEnProceso = result.acta_id;
            
            showSuccess(`Acta ${result.numero_acta} registrada exitosamente.<br>
                        Hora de registro: ${result.hora_registro}<br>
                        <small>El sistema está guardando automáticamente los cambios.</small>`);
            
            // Añadir botón de finalizar en el modal
            agregarBotonFinalizar();
            
            // No cerrar el modal para permitir ediciones
            console.log('Acta creada con ID:', result.acta_id);
        } else {
            showError('Error al registrar el acta: ' + (result.message || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Error al conectar con el servidor');
    })
    .finally(() => {
        // Restaurar botón
        if (submitBtn) {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    });
}

// Función para agregar botón de finalizar al modal
function agregarBotonFinalizar() {
    const modalFooter = document.querySelector('#modal-nueva-acta .d-flex.justify-content-between');
    if (modalFooter && !document.getElementById('btn-finalizar-acta')) {
        const btnFinalizar = document.createElement('button');
        btnFinalizar.id = 'btn-finalizar-acta';
        btnFinalizar.type = 'button';
        btnFinalizar.className = 'btn btn-success';
        btnFinalizar.innerHTML = '<i class="fas fa-check-double me-2"></i>Finalizar Registro';
        btnFinalizar.onclick = finalizarRegistroActa;
        
        modalFooter.appendChild(btnFinalizar);
    }
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

<!-- MODALES FLOTANTES -->

<!-- MODAL: NUEVA ACTA -->
<div class="floating-modal" id="modal-nueva-acta">
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
            <form id="form-nueva-acta" action="{{ route('inspecciones.store') }}" method="POST">
                @csrf
                
                <!-- Campos automáticos ocultos -->
                <input type="hidden" id="fecha_inspeccion_hidden" name="fecha_inspeccion">
                <input type="hidden" id="hora_inicio_hidden" name="hora_inicio">
                <input type="hidden" name="inspector_principal" value="{{ Auth::user()->name }}">

                <!-- CABEZAL OFICIAL DEL ACTA -->
                <div class="card mb-4 border-3 border-dark" style="background: #ffffff;">
                    <div class="card-body py-2">
                        <!-- Fila superior con cuadros según la imagen oficial -->
                        <div class="row g-0 mb-2">
                            <!-- Logo/Escudo del Perú (izquierdo) -->
                            <div class="col-1 d-flex align-items-center justify-content-center">
                                <div class="text-center p-1" style="border: 2px solid #000; background: #ffffff; border-radius: 10px; width: 60px; height: 60px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                    <img src="{{ asset('images/escudo_peru.png') }}" alt="Escudo del Perú" style="max-width: 45px; max-height: 45px; object-fit: contain;">
                                </div>
                            </div>
                            
                            <!-- Cuadros centrales -->
                            <div class="col-10">
                                <div class="row g-0">
                                    <div class="col-2">
                                        <div class="p-2 text-center" style="background-color: #dc143c; color: white; border: 2px solid #000; font-weight: bold; font-size: 16px; min-height: 60px; display: flex; align-items: center; justify-content: center;">
                                            PERÚ
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="p-2 text-center" style="background-color: #ffffff; color: #000; border: 2px solid #000; border-left: none; font-weight: bold; font-size: 14px; min-height: 60px; display: flex; align-items: center; justify-content: center; line-height: 1.2;">
                                            GOBIERNO REGIONAL<br>DE APURÍMAC
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-2 text-center" style="background-color: #dc143c; color: white; border: 2px solid #000; border-left: none; font-weight: bold; font-size: 13px; min-height: 60px; display: flex; align-items: center; justify-content: center; line-height: 1.2;">
                                            DIRECCIÓN REGIONAL DE<br>TRANSPORTES Y COMUNICACIONES
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="p-2 text-center" style="background-color: #ffffff; color: #000; border: 2px solid #000; border-left: none; font-weight: bold; font-size: 12px; min-height: 60px; display: flex; align-items: center; justify-content: center; line-height: 1.1;">
                                            DIRECCIÓN DE CIRCULACIÓN<br>TERRESTRE OF. FISCALIZACIÓN
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Logo Regional (derecho) -->
                            <div class="col-1 d-flex align-items-center justify-content-center">
                                <div class="text-center p-1" style="background: #ffffff; border: 2px solid #000; border-radius: 50%; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                    <img src="{{ asset('images/logo-gobierno.png') }}" alt="Logo Gobierno Regional" style="max-width: 50px; max-height: 50px; object-fit: contain;">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sección del número de acta centrada -->
                        <div class="row mt-3">
                            <div class="col-12 text-center">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <h3 class="mb-0 fw-bold text-dark me-3">ACTA DE CONTROL</h3>
                                    <span class="me-2 fw-bold text-dark" style="font-size: 18px;">Nº</span>
                                    <input type="text" class="form-control d-inline-block me-2" 
                                           name="numero_acta" 
                                           placeholder="000451"
                                           style="width: 120px; border: 3px solid #000; text-align: center; font-weight: bold; font-size: 18px; background-color: #fff;">
                                    <span class="fw-bold text-dark" style="font-size: 18px;">- {{ date('Y') }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Información del decreto -->
                        <div class="row mt-2">
                            <div class="col-12 text-center">
                                <div class="d-inline-block p-2" style="border: 2px solid #000; background-color: #ffffff;">
                                    <div class="fw-bold text-dark mb-1">D.S. Nº 017-2009-MTC</div>
                                    <div style="font-size: 12px; color: #000;">Código de infracciones y/o incumplimiento</div>
                                    <div style="font-size: 12px; color: #000; font-weight: bold;">Tipo infractor</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Información adicional del documento -->
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold me-2">Fecha:</span> 
                                    <div class="border-bottom border-dark px-3" style="min-width: 120px; text-align: center; font-weight: bold;">
                                        {{ now()->format('d/m/Y') }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold me-2">Hora:</span> 
                                    <div class="border-bottom border-dark px-3" style="min-width: 120px; text-align: center; font-weight: bold;">
                                        {{ now()->format('H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 1: INFORMACIÓN DEL OPERADOR/CONDUCTOR -->
                <div class="card mb-4 border-warning">
                    <div class="card-header" style="background: linear-gradient(135deg, var(--drtc-orange), var(--drtc-dark-orange)); color: white;">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-user-tie me-2"></i>I. DATOS DEL OPERADOR/CONDUCTOR</h6>
                    </div>
                    <div class="card-body bg-light">
                        <!-- Tipo de Agente Infractor -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-warning">Tipo de Agente Infractor:</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check p-3 border border-warning rounded bg-white">
                                        <input class="form-check-input" type="radio" name="tipo_agente" id="transportista" value="transportista">
                                        <label class="form-check-label fw-bold w-100" for="transportista">
                                            <i class="fas fa-truck me-2 text-warning"></i>TRANSPORTISTA
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check p-3 border border-warning rounded bg-white">
                                        <input class="form-check-input" type="radio" name="tipo_agente" id="operador_ruta" value="operador_ruta">
                                        <label class="form-check-label fw-bold w-100" for="operador_ruta">
                                            <i class="fas fa-route me-2 text-warning"></i>OPERADOR DE RUTA
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check p-3 border border-warning rounded bg-white">
                                        <input class="form-check-input" type="radio" name="tipo_agente" id="conductor" value="conductor">
                                        <label class="form-check-label fw-bold w-100" for="conductor">
                                            <i class="fas fa-id-card me-2 text-warning"></i>CONDUCTOR
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Datos del operador/conductor -->
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold text-warning">RUC/DNI:</label>
                                <input type="text" class="form-control border-warning" name="ruc_dni" id="ruc_dni" placeholder="20123456789 o 12345678" maxlength="11" required>
                                <div class="form-text">
                                    <small class="text-muted">DNI: 8 dígitos | RUC: 11 dígitos</small>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-warning">Razón Social/Nombres y Apellidos:</label>
                                <input type="text" class="form-control border-warning" name="razon_social" id="razon_social" placeholder="Se autocompletará con los datos de RENIEC/SUNAT" readonly>
                                <div id="loading-data" class="form-text text-info" style="display: none;">
                                    <i class="fas fa-spinner fa-spin"></i> Consultando datos...
                                </div>
                                <div class="form-text mt-1">
                                    <small class="text-muted">Datos obtenidos de APIs oficiales</small>
                                    <a href="/ver-consultas.html" target="_blank" class="btn btn-sm btn-outline-secondary ms-2">
                                        <i class="fas fa-database me-1"></i>Ver Consultas
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold text-warning">Placa del Vehículo:</label>
                                <input type="text" class="form-control border-warning" name="placa_1" placeholder="ABC-123" style="text-transform: uppercase;" required>
                            </div>
                        </div>
                        
                        <!-- Datos adicionales del conductor -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-warning">Nombre del Conductor:</label>
                                <input type="text" class="form-control border-warning" name="nombre_conductor_1" placeholder="Nombres y apellidos completos" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold text-warning">N° Licencia de Conducir:</label>
                                <input type="text" class="form-control border-warning" name="licencia_conductor_1" placeholder="N° Licencia" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold text-warning">Clase y Categoría:</label>
                                <select class="form-select border-warning" name="clase_categoria" required>
                                    <option value="">Seleccione...</option>
                                    <option value="A-I">A-I (Motocicletas hasta 125cc)</option>
                                    <option value="A-IIa">A-IIa (Motocicletas de 126cc a 200cc)</option>
                                    <option value="A-IIb">A-IIb (Motocicletas mayor a 200cc)</option>
                                    <option value="A-IIIa">A-IIIa (Vehículos menores)</option>
                                    <option value="A-IIIb">A-IIIb (Automóviles, camionetas)</option>
                                    <option value="A-IIIc">A-IIIc (Buses, camiones)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 2: DATOS DE LA INTERVENCIÓN -->
                <div class="card mb-4 border-info">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-map-marker-alt me-2"></i>II. DATOS DE LA INTERVENCIÓN</h6>
                    </div>
                    <div class="card-body bg-light">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-info">Lugar de Intervención:</label>
                                <input type="text" class="form-control border-info" name="lugar_intervencion" placeholder="Dirección exacta o referencia" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold text-info">Fecha:</label>
                                <input type="date" class="form-control border-info bg-light" name="fecha_intervencion" value="{{ date('Y-m-d') }}" readonly>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold text-info">Hora:</label>
                                <input type="time" class="form-control border-info bg-light" name="hora_intervencion" value="{{ date('H:i') }}" readonly>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-info">Inspector Responsable:</label>
                                <input type="text" class="form-control border-info" name="inspector" value="{{ Auth::user()->name }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-info">Tipo de Servicio:</label>
                                <select class="form-select border-info" name="tipo_servicio" required>
                                    <option value="">Seleccione tipo de servicio...</option>
                                    <option value="publico">Servicio Público</option>
                                    <option value="privado">Servicio Privado</option>
                                    <option value="turistico">Servicio Turístico</option>
                                    <option value="carga">Transporte de Carga</option>
                                    <option value="especial">Servicio Especial</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 3: DESCRIPCIÓN DE LA INFRACCIÓN -->
                <div class="card mb-4 border-danger">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>III. DESCRIPCIÓN DE LA INFRACCIÓN</h6>
                    </div>
                    <div class="card-body bg-light">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-danger">Tipo de Infracción:</label>
                            <select class="form-select border-danger" name="tipo_infraccion" required>
                                <option value="">Seleccione el tipo de infracción...</option>
                                <option value="documentaria">Infracción Documentaria</option>
                                <option value="administrativa">Infracción Administrativa</option>
                                <option value="tecnica">Infracción Técnica</option>
                                <option value="operacional">Infracción Operacional</option>
                                <option value="seguridad">Infracción de Seguridad</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-danger">Descripción Detallada de los Hechos:</label>
                            <textarea class="form-control border-danger" name="descripcion_hechos" rows="4" placeholder="Describa detalladamente la infracción detectada..." required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-danger">Código de Infracción:</label>
                                <input type="text" class="form-control border-danger" name="codigo_infraccion" placeholder="Ej: INF-001">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-danger">Gravedad:</label>
                                <select class="form-select border-danger" name="gravedad" required>
                                    <option value="">Seleccione gravedad...</option>
                                    <option value="leve">Leve</option>
                                    <option value="grave">Grave</option>
                                    <option value="muy_grave">Muy Grave</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Botones de acción -->
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-success btn-lg me-3 px-5">
                        <i class="fas fa-save me-2"></i>GUARDAR ACTA
                    </button>
                    <button type="reset" class="btn btn-secondary btn-lg px-5">
                        <i class="fas fa-undo me-2"></i>LIMPIAR FORMULARIO
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: EDITAR ACTA -->
<div class="floating-modal" id="modal-editar-acta">
    <div class="modal-content-wrapper">
        <div class="modal-header-custom">
            <h4 class="mb-0 fw-bold">
                <i class="fas fa-edit me-2"></i>
                EDITAR ACTA DE FISCALIZACIÓN EXISTENTE
            </h4>
            <button class="close-modal" onclick="cerrarModal('modal-editar-acta')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body-custom">
            <!-- Buscador de Acta -->
            <div class="card mb-4 border-warning">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-search me-2"></i>BUSCAR ACTA PARA EDITAR</h6>
                </div>
                <div class="card-body bg-light">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold text-warning">Criterio de Búsqueda:</label>
                            <input type="text" class="form-control border-warning" id="buscar-editar" placeholder="Ingrese N° de Acta, RUC/DNI o Placa del vehículo">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold text-warning">Acción:</label>
                            <button type="button" class="btn btn-warning d-block w-100 fw-bold" onclick="buscarActaEditar()">
                                <i class="fas fa-search me-2"></i>BUSCAR ACTA
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Resultado de la búsqueda y formulario de edición -->
            <div id="resultado-editar" style="display: none;">
                <div class="alert alert-warning border-warning">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <i class="fas fa-info-circle fa-2x"></i>
                        </div>
                        <div class="col">
                            <h5 class="mb-1">ACTA ENCONTRADA</h5>
                            <strong>Editando Acta N°:</strong> <span id="acta-numero-editar" class="text-danger"></span>
                        </div>
                    </div>
                </div>
                
                <!-- Aquí iría el formulario de edición igual al de nueva acta pero con _edit en los nombres -->
                <p class="text-center text-muted">
                    <i class="fas fa-info-circle me-2"></i>
                    Formulario de edición se cargaría aquí con los datos del acta encontrada
                </p>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: ELIMINAR ACTA -->
<div class="floating-modal" id="modal-eliminar-acta">
    <div class="modal-content-wrapper">
        <div class="modal-header-custom" style="background: linear-gradient(135deg, #dc3545, #c82333);">
            <h4 class="mb-0 fw-bold">
                <i class="fas fa-trash-alt me-2"></i>
                ELIMINAR ACTA DEL SISTEMA
            </h4>
            <button class="close-modal" onclick="cerrarModal('modal-eliminar-acta')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body-custom">
            <!-- Advertencia crítica -->
            <div class="alert alert-danger text-center mb-4 border-danger" style="background: #f8d7da;">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-3x text-danger"></i>
                    </div>
                    <div class="col">
                        <h4 class="mb-2 text-danger">⚠️ ADVERTENCIA CRÍTICA</h4>
                        <p class="mb-1 fw-bold">Esta acción eliminará permanentemente el acta del sistema</p>
                        <p class="mb-0 text-muted">Esta operación es IRREVERSIBLE y requiere autorización</p>
                    </div>
                </div>
            </div>
            
            <!-- Buscador de Acta -->
            <div class="card mb-4 border-danger">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-search me-2"></i>BUSCAR ACTA PARA ELIMINAR</h6>
                </div>
                <div class="card-body bg-light">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold text-danger">Criterio de Búsqueda:</label>
                            <input type="text" class="form-control border-danger" id="buscar-eliminar" placeholder="Ingrese N° de Acta, RUC/DNI o Placa del vehículo">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold text-danger">Acción:</label>
                            <button type="button" class="btn btn-danger d-block w-100 fw-bold" onclick="buscarActaEliminar()">
                                <i class="fas fa-search me-2"></i>BUSCAR ACTA
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Resultado de la búsqueda -->
            <div id="resultado-eliminar" style="display: none;">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-file-alt me-2"></i>ACTA ENCONTRADA</h6>
                    </div>
                    <div class="card-body bg-light">
                        <!-- Información del acta -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="info-group p-3 border border-danger rounded bg-white">
                                    <label class="form-label fw-bold text-danger">N° de Acta:</label>
                                    <p class="mb-0 fs-5" id="eliminar-numero-acta">DRTC-APU-2024-001</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-group p-3 border border-danger rounded bg-white">
                                    <label class="form-label fw-bold text-danger">Fecha de Registro:</label>
                                    <p class="mb-0 fs-5" id="eliminar-fecha-acta">15/08/2024</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Motivo de eliminación -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-danger">Motivo de la Eliminación (Obligatorio):</label>
                            <select class="form-select border-danger mb-3" id="motivo-eliminacion" required>
                                <option value="">Seleccione el motivo...</option>
                                <option value="error_registro">Error en el registro</option>
                                <option value="duplicado">Acta duplicada</option>
                                <option value="datos_incorrectos">Datos incorrectos</option>
                                <option value="solicitud_operador">Solicitud del operador</option>
                                <option value="revision_superior">Revisión de superior</option>
                                <option value="otro">Otro motivo</option>
                            </select>
                            <textarea class="form-control border-danger" id="observaciones-eliminacion" rows="3" placeholder="Observaciones adicionales sobre la eliminación..."></textarea>
                        </div>
                        
                        <!-- Autorización -->
                        <div class="card border-warning mb-4" style="background: #fff3cd;">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0 fw-bold"><i class="fas fa-key me-2"></i>AUTORIZACIÓN REQUERIDA</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Código de Autorización:</label>
                                        <input type="password" class="form-control border-warning" id="codigo-autorizacion" placeholder="Ingrese código de supervisor" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Supervisor Autorizante:</label>
                                        <input type="text" class="form-control border-warning" id="supervisor-autorizante" placeholder="Nombre del supervisor" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Botones de confirmación -->
                        <div class="text-center">
                            <button type="button" class="btn btn-danger btn-lg me-3 px-5" onclick="confirmarEliminacion()">
                                <i class="fas fa-trash me-2"></i>CONFIRMAR ELIMINACIÓN
                            </button>
                            <button type="button" class="btn btn-secondary btn-lg px-5" onclick="cancelarEliminacion()">
                                <i class="fas fa-times me-2"></i>CANCELAR OPERACIÓN
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: CONSULTAS Y REPORTES -->
<div class="floating-modal" id="modal-consultas">
    <div class="modal-content-wrapper">
        <div class="modal-header-custom" style="background: linear-gradient(135deg, #17a2b8, #138496);">
            <h4 class="mb-0 fw-bold">
                <i class="fas fa-search me-2"></i>
                CONSULTAS Y REPORTES DRTC
            </h4>
            <button class="close-modal" onclick="cerrarModal('modal-consultas')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body-custom">
            <!-- Formulario de filtros -->
            <div class="card mb-4 border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-filter me-2"></i>FILTROS DE BÚSQUEDA</h6>
                </div>
                <div class="card-body bg-light">
                    <form id="form-consultas">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-info">N° de Acta:</label>
                                <input type="text" class="form-control border-info" name="numero_acta" placeholder="DRTC-APU-2024-001">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-info">RUC/DNI:</label>
                                <input type="text" class="form-control border-info" name="ruc_dni" placeholder="20123456789">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-info">Placa del Vehículo:</label>
                                <input type="text" class="form-control border-info" name="placa" placeholder="ABC-123" style="text-transform: uppercase;">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-info">Estado del Acta:</label>
                                <select class="form-select border-info" name="estado">
                                    <option value="">Todos los estados</option>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="procesada">Procesada</option>
                                    <option value="anulada">Anulada</option>
                                    <option value="pagada">Pagada</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-info">Fecha Desde:</label>
                                <input type="date" class="form-control border-info" name="fecha_desde">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-info">Fecha Hasta:</label>
                                <input type="date" class="form-control border-info" name="fecha_hasta">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-info">Tipo de Infracción:</label>
                                <select class="form-select border-info" name="tipo_infraccion">
                                    <option value="">Todas las infracciones</option>
                                    <option value="documentaria">Documentaria</option>
                                    <option value="administrativa">Administrativa</option>
                                    <option value="tecnica">Técnica</option>
                                    <option value="operacional">Operacional</option>
                                    <option value="seguridad">Seguridad</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-info">Inspector:</label>
                                <select class="form-select border-info" name="inspector">
                                    <option value="">Todos los inspectores</option>
                                    <option value="{{ Auth::user()->name }}">{{ Auth::user()->name }}</option>
                                    <option value="inspector2">Inspector 2</option>
                                    <option value="inspector3">Inspector 3</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Botones de acción -->
            <div class="text-center mb-4">
                <button type="button" class="btn btn-info btn-lg me-2 px-4" onclick="ejecutarConsulta()">
                    <i class="fas fa-search me-2"></i>CONSULTAR ACTAS
                </button>
                <button type="button" class="btn btn-success btn-lg me-2 px-4" onclick="exportarExcel()">
                    <i class="fas fa-file-excel me-2"></i>EXPORTAR EXCEL
                </button>
                <button type="button" class="btn btn-danger btn-lg me-2 px-4" onclick="exportarPDF()">
                    <i class="fas fa-file-pdf me-2"></i>EXPORTAR PDF
                </button>
                <button type="button" class="btn btn-warning btn-lg px-4" onclick="generarReporte()">
                    <i class="fas fa-chart-bar me-2"></i>REPORTE ESTADÍSTICO
                </button>
            </div>
            
            <!-- Resumen de resultados -->
            <div id="resumen-consulta" class="card border-info mb-4" style="display: none;">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-info-circle me-2"></i>RESUMEN DE RESULTADOS</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="p-3 border border-info rounded bg-white">
                                <h4 class="text-info mb-1" id="total-actas">0</h4>
                                <small class="text-muted">Total de Actas</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 border border-success rounded bg-white">
                                <h4 class="text-success mb-1" id="actas-procesadas-modal">0</h4>
                                <small class="text-muted">Procesadas</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 border border-warning rounded bg-white">
                                <h4 class="text-warning mb-1" id="actas-pendientes-modal">0</h4>
                                <small class="text-muted">Pendientes</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 border border-danger rounded bg-white">
                                <h4 class="text-danger mb-1" id="actas-anuladas-modal">0</h4>
                                <small class="text-muted">Anuladas</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabla de resultados -->
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-table me-2"></i>RESULTADOS DE LA CONSULTA</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0" id="tabla-resultados">
                            <thead class="bg-info text-white">
                                <tr>
                                    <th class="py-3">N° ACTA</th>
                                    <th>FECHA</th>
                                    <th>OPERADOR/CONDUCTOR</th>
                                    <th>RUC/DNI</th>
                                    <th>PLACA</th>
                                    <th>INFRACCIÓN</th>
                                    <th>GRAVEDAD</th>
                                    <th>ESTADO</th>
                                    <th>INSPECTOR</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-resultados">
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="fas fa-search me-2"></i>
                                        Use los filtros y haga clic en "Consultar Actas" para ver los resultados
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

@endsection

<script>
// FUNCIONES PARA APIs DE CONSULTA DNI/RUC
document.addEventListener('DOMContentLoaded', function() {
    // API para consulta de RUC/DNI único
    const rucDniInput = document.getElementById('ruc_dni');
    const razonSocialInput = document.getElementById('razon_social');
    const loadingData = document.getElementById('loading-data');
    
    // Función para consultar RUC en SUNAT (con API de Decolecta mejorada)
    async function consultarRUC(ruc) {
        try {
            loadingData.style.display = 'block';
            razonSocialInput.value = '';
            
            // Lista de APIs a probar en orden (API ultra-robusta como principal)
            const apis = [
                // API ULTRA-ROBUSTA PRINCIPAL - Garantiza JSON válido siempre
                {
                    url: `/api/api-ruc-ultra.php?ruc=${ruc}`,
                    headers: {},
                    process: (data) => {
                        console.log('Respuesta API RUC Ultra:', data);
                        if (data && data.success && data.razon_social) {
                            return {
                                razonSocial: data.razon_social,
                                direccion: data.direccion || null,
                                estado: data.estado || null,
                                departamento: data.departamento || null,
                                fuente: data.fuente || 'API Ultra'
                            };
                        }
                        return null;
                    }
                },
                // API HÍBRIDA PRINCIPAL - APISPERU + Local como fallback
                {
                    url: `/api/api-ruc-hibrido.php?ruc=${ruc}`,
                    headers: {},
                    process: (data) => {
                        console.log('Respuesta API RUC Híbrida:', data);
                        if (data && data.success && data.razon_social) {
                            return {
                                razonSocial: data.razon_social,
                                direccion: data.direccion || null,
                                estado: data.estado || null,
                                departamento: data.departamento || null,
                                fuente: data.fuente || 'API Híbrida'
                            };
                        }
                        return null;
                    }
                },
                // API LOCAL PRINCIPAL - Siempre disponible
                {
                    url: `/api/api-ruc-local.php?ruc=${ruc}`,
                    headers: {},
                    process: (data) => {
                        console.log('Respuesta API RUC Local:', data);
                        if (data && data.success && data.razon_social) {
                            return {
                                razonSocial: data.razon_social,
                                direccion: data.direccion || null,
                                estado: data.estado || null
                            };
                        }
                        return null;
                    }
                },
                // API proxy PHP local para RUC (respaldo externo)
                {
                    url: `/api/test-api-ruc.php?ruc=${ruc}`,
                    headers: {},
                    process: (data) => {
                        console.log('Respuesta API RUC Proxy:', data);
                        if (data && data.success && data.razon_social) {
                            return {
                                razonSocial: data.razon_social,
                                direccion: data.direccion || null,
                                estado: data.estado || null
                            };
                        }
                        return null;
                    }
                },
                // API de APISPERU.com directa para RUC (sin token)
                {
                    url: `https://dniruc.apisperu.com/api/v1/ruc/${ruc}`,
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    process: (data) => {
                        console.log('Respuesta APISPERU RUC Directa:', data);
                        if (data && data.ruc && data.razonSocial) {
                            return {
                                razonSocial: data.razonSocial,
                                nombreComercial: data.nombreComercial,
                                direccion: data.direccion || null,
                                departamento: data.departamento,
                                provincia: data.provincia,
                                distrito: data.distrito,
                                estado: data.estado || null,
                                condicion: data.condicion || null,
                                capital: data.capital,
                                ubigeo: data.ubigeo,
                                telefonos: data.telefonos
                            };
                        }
                        return null;
                    }
                },
                // API de Decolecta SUNAT directa
                {
                    url: `https://api.decolecta.com/v1/sunat/ruc?numero=${ruc}`,
                    headers: {
                        'Referer': 'http://apis.net.pe/api-ruc',
                        'Authorization': 'Bearer apis-token-1.aTSI1U7KEuT-6bbbCguH-4Y8TI6KS73N'
                    },
                    process: (data) => {
                        console.log('Respuesta Decolecta SUNAT:', data);
                        if (data && data.data && data.data.razon_social) {
                            return {
                                razonSocial: data.data.razon_social,
                                direccion: data.data.direccion || null,
                                estado: data.data.estado || null
                            };
                        } else if (data && data.razon_social) {
                            return {
                                razonSocial: data.razon_social,
                                direccion: data.direccion || null,
                                estado: data.estado || null
                            };
                        }
                        return null;
                    }
                },
                // APIs de respaldo
                {
                    url: `https://api.apis.net.pe/v1/ruc?numero=${ruc}`,
                    headers: {},
                    process: (data) => {
                        if (data && data.razonSocial) {
                            return {
                                razonSocial: data.razonSocial,
                                direccion: data.direccion || null,
                                estado: null
                            };
                        }
                        return null;
                    }
                },
                // API de APISPERU.com para RUC (sin token - gratis)
                {
                    url: `https://dniruc.apisperu.com/api/v1/ruc/${ruc}`,
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    process: (data) => {
                        console.log('Respuesta APISPERU RUC:', data);
                        if (data && data.ruc && data.razonSocial) {
                            return {
                                razonSocial: data.razonSocial,
                                nombreComercial: data.nombreComercial,
                                direccion: data.direccion || null,
                                departamento: data.departamento,
                                provincia: data.provincia,
                                distrito: data.distrito,
                                estado: data.estado || null,
                                condicion: data.condicion || null,
                                capital: data.capital,
                                ubigeo: data.ubigeo,
                                telefonos: data.telefonos
                            };
                        }
                        return null;
                    }
                },
                // API de respaldo con token (deprecada)
                {
                    url: `https://dniruc.apisperu.com/api/v1/ruc/${ruc}?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6InRlc3RAdGVzdC5jb20ifQ.bb2doqtI_pKcqT3TsCtm9-lFfwHJUkkrOkF_a1r7jW4`,
                    headers: {},
                    process: (data) => {
                        if (data && data.success && data.razonSocial) {
                            return {
                                razonSocial: data.razonSocial,
                                direccion: data.direccion || null,
                                estado: data.estado || null
                            };
                        }
                        return null;
                    }
                }
            ];
            
            let datosEmpresa = null;
            let apiUsada = '';
            
            // Intentar con cada API hasta encontrar una que funcione
            for (const api of apis) {
                try {
                    console.log(`Intentando API RUC: ${api.url}`);
                    
                    // Configurar headers según la API
                    const fetchOptions = {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            ...api.headers
                        }
                    };
                    
                    const response = await fetch(api.url, fetchOptions);
                    const data = await response.json();
                    
                    console.log(`Respuesta de ${api.url}:`, data);
                    
                    datosEmpresa = api.process(data);
                    if (datosEmpresa && datosEmpresa.razonSocial) {
                        apiUsada = api.url.includes('api-ruc-ultra.php') ? 'API Ultra-Robusta DRTC' :
                                  api.url.includes('api-ruc-hibrido.php') ? 'API Híbrida APISPERU+Local' :
                                  api.url.includes('api/api-ruc-local.php') ? 'Base de Datos Local SUNAT-DRTC' :
                                  api.url.includes('api/test-api-ruc.php') ? 'API Proxy (Decolecta SUNAT)' :
                                  api.url.includes('dniruc.apisperu.com') ? 'APISPERU.com (Oficial)' :
                                  api.url.includes('decolecta') ? 'Decolecta SUNAT (Oficial)' : 
                                  api.url.includes('apis.net.pe') ? 'APIs.net.pe' : 
                                  'API Externa';
                        break;
                    }
                } catch (apiError) {
                    console.log(`Error con API RUC ${api.url}:`, apiError);
                    continue;
                }
            }
            
            if (datosEmpresa && datosEmpresa.razonSocial) {
                razonSocialInput.value = datosEmpresa.razonSocial;
                razonSocialInput.style.backgroundColor = '#d4edda';
                razonSocialInput.style.borderColor = '#28a745';
                
                // Construir tooltip con información adicional
                let tooltip = `Datos obtenidos de: ${apiUsada}`;
                if (datosEmpresa.direccion) {
                    tooltip += `\nDirección: ${datosEmpresa.direccion}`;
                }
                if (datosEmpresa.estado) {
                    tooltip += `\nEstado: ${datosEmpresa.estado}`;
                }
                razonSocialInput.title = tooltip;
                
                // Mostrar éxito en el info
                const infoData = document.getElementById('info-data');
                if (infoData) {
                    infoData.innerHTML = `<i class="fas fa-check-circle text-success me-1"></i>Datos obtenidos de ${apiUsada}`;
                    setTimeout(() => {
                        infoData.innerHTML = '<i class="fas fa-info-circle me-1"></i>RUC: 11 dígitos | DNI: 8 dígitos';
                    }, 3000);
                }
            } else {
                // Si ninguna API funcionó, permitir ingreso manual
                razonSocialInput.value = '';
                razonSocialInput.placeholder = 'RUC no encontrado - Ingrese la razón social manualmente';
                razonSocialInput.style.backgroundColor = '#fff3cd';
                razonSocialInput.style.borderColor = '#ffc107';
                razonSocialInput.focus();
                
                // Mostrar mensaje informativo
                const infoData = document.getElementById('info-data');
                if (infoData) {
                    infoData.innerHTML = '<i class="fas fa-exclamation-triangle text-warning me-1"></i>RUC no encontrado - Complete manualmente';
                    setTimeout(() => {
                        infoData.innerHTML = '<i class="fas fa-info-circle me-1"></i>RUC: 11 dígitos | DNI: 8 dígitos';
                    }, 5000);
                }
            }
        } catch (error) {
            console.error('Error general consultando RUC:', error);
            razonSocialInput.value = '';
            razonSocialInput.placeholder = 'Error de conexión - Ingrese la razón social manualmente';
            razonSocialInput.style.backgroundColor = '#fff3cd';
            razonSocialInput.style.borderColor = '#ffc107';
            razonSocialInput.focus();
        } finally {
            loadingData.style.display = 'none';
        }
    }
    
    // Función para consultar DNI en RENIEC (con API de Decolecta como principal)
    async function consultarDNI(dni) {
        try {
            loadingData.style.display = 'block';
            razonSocialInput.value = '';
            
            // Lista de APIs a probar en orden (API ultra-robusta como principal)
            const apis = [
                // API ULTRA-ROBUSTA PRINCIPAL - Garantiza JSON válido siempre
                {
                    url: `/api/api-dni-ultra.php?dni=${dni}`,
                    headers: {},
                    process: (data) => {
                        console.log('Respuesta API DNI Ultra:', data);
                        if (data && data.success && data.nombre_completo) {
                            // Almacenar datos adicionales para uso posterior
                            window.ultimaConsultaDNI = {
                                dni: data.dni,
                                nombre_completo: data.nombre_completo,
                                nombres: data.nombres,
                                apellido_paterno: data.apellido_paterno,
                                apellido_materno: data.apellido_materno,
                                fuente: data.fuente
                            };
                            return data.nombre_completo;
                        }
                        return null;
                    }
                },
                // API HÍBRIDA PRINCIPAL - APISPERU + Local como fallback
                {
                    url: `/api/api-dni-hibrido.php?dni=${dni}`,
                    headers: {},
                    process: (data) => {
                        console.log('Respuesta API DNI Híbrida:', data);
                        if (data && data.success && data.nombre_completo) {
                            // Almacenar datos adicionales para uso posterior
                            window.ultimaConsultaDNI = {
                                dni: data.dni,
                                nombre_completo: data.nombre_completo,
                                nombres: data.nombres,
                                apellido_paterno: data.apellido_paterno,
                                apellido_materno: data.apellido_materno,
                                departamento: data.departamento,
                                provincia: data.provincia,
                                distrito: data.distrito,
                                direccion: data.direccion,
                                fecha_nacimiento: data.fecha_nacimiento,
                                estado_civil: data.estado_civil,
                                fuente: data.fuente
                            };
                            return data.nombre_completo;
                        }
                        return null;
                    }
                },
                // API LOCAL PRINCIPAL - Siempre disponible
                {
                    url: `/api/api-dni-local.php?dni=${dni}`,
                    headers: {},
                    process: (data) => {
                        console.log('Respuesta API DNI Local:', data);
                        if (data && data.success && data.nombre_completo) {
                            // Almacenar datos adicionales para uso posterior
                            window.ultimaConsultaDNI = {
                                dni: data.dni,
                                nombre_completo: data.nombre_completo,
                                nombres: data.nombres,
                                apellido_paterno: data.apellido_paterno,
                                apellido_materno: data.apellido_materno,
                                departamento: data.departamento,
                                provincia: data.provincia,
                                distrito: data.distrito,
                                direccion: data.direccion,
                                fecha_nacimiento: data.fecha_nacimiento,
                                estado_civil: data.estado_civil,
                                fuente: data.fuente
                            };
                            return data.nombre_completo;
                        }
                        return null;
                    }
                },
                // API proxy PHP local (respaldo externo)
                {
                    url: `/api/test-api-dni.php?dni=${dni}`,
                    headers: {},
                    process: (data) => {
                        console.log('Respuesta API DNI Proxy:', data);
                        if (data && data.success && data.nombre_completo) {
                            return data.nombre_completo;
                        }
                        return null;
                    }
                },
                // API de APISPERU.com directa (sin token)
                {
                    url: `https://dniruc.apisperu.com/api/v1/dni/${dni}`,
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    process: (data) => {
                        console.log('Respuesta APISPERU DNI Directa:', data);
                        if (data && data.dni && data.nombres) {
                            const nombreCompleto = `${data.nombres} ${data.apellidoPaterno || ''} ${data.apellidoMaterno || ''}`.trim();
                            
                            // Almacenar datos adicionales
                            window.ultimaConsultaDNI = {
                                dni: data.dni,
                                nombre_completo: nombreCompleto,
                                nombres: data.nombres,
                                apellido_paterno: data.apellidoPaterno,
                                apellido_materno: data.apellidoMaterno,
                                cod_verifica: data.codVerifica,
                                fuente: 'APISPERU.com Directa'
                            };
                            
                            return nombreCompleto;
                        }
                        return null;
                    }
                },
                // API de Factiliza directa
                {
                    url: `https://api.factiliza.com/v1/dni/info/${dni}`,
                    headers: {
                        'Authorization': 'Bearer apis-token-1.aTSI1U7KEuT-6bbbCguH-4Y8TI6KS73N',
                        'Content-Type': 'application/json'
                    },
                    process: (data) => {
                        console.log('Respuesta Factiliza:', data);
                        if (data && data.success && data.data) {
                            const d = data.data;
                            // Usar el nombre_completo si está disponible, sino construirlo
                            let nombreCompleto = d.nombre_completo || '';
                            if (!nombreCompleto && d.nombres) {
                                nombreCompleto = `${d.nombres} ${d.apellido_paterno || ''} ${d.apellido_materno || ''}`.trim();
                            }
                            
                            // Almacenar datos adicionales para uso posterior
                            window.ultimaConsultaDNI = {
                                dni: d.numero,
                                nombre_completo: nombreCompleto,
                                nombres: d.nombres,
                                apellido_paterno: d.apellido_paterno,
                                apellido_materno: d.apellido_materno,
                                departamento: d.departamento,
                                provincia: d.provincia,
                                distrito: d.distrito,
                                direccion: d.direccion,
                                direccion_completa: d.direccion_completa,
                                ubigeo: d.ubigeo_reniec,
                                fecha_nacimiento: d.fecha_nacimiento,
                                sexo: d.sexo
                            };
                            
                            return nombreCompleto;
                        }
                        return null;
                    }
                },
                // API de Decolecta como respaldo
                {
                    url: `https://api.decolecta.com/v1/reniec/dni?numero=${dni}`,
                    headers: {
                        'Referer': 'https://apis.net.pe/consulta-dni-api',
                        'Authorization': 'Bearer apis-token-1.aTSI1U7KEuT-6bbbCguH-4Y8TI6KS73N'
                    },
                    process: (data) => {
                        console.log('Respuesta Decolecta:', data);
                        if (data && data.data && data.data.nombre_completo) {
                            return data.data.nombre_completo;
                        } else if (data && data.nombres) {
                            return `${data.nombres} ${data.apellido_paterno || ''} ${data.apellido_materno || ''}`.trim();
                        }
                        return null;
                    }
                },
                // API de APISPERU.com (sin token - gratis)
                {
                    url: `https://dniruc.apisperu.com/api/v1/dni/${dni}`,
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    process: (data) => {
                        console.log('Respuesta APISPERU DNI:', data);
                        if (data && data.dni && data.nombres) {
                            const nombreCompleto = `${data.nombres} ${data.apellidoPaterno || ''} ${data.apellidoMaterno || ''}`.trim();
                            
                            // Almacenar datos adicionales
                            window.ultimaConsultaDNI = {
                                dni: data.dni,
                                nombre_completo: nombreCompleto,
                                nombres: data.nombres,
                                apellido_paterno: data.apellidoPaterno,
                                apellido_materno: data.apellidoMaterno,
                                cod_verifica: data.codVerifica,
                                fuente: 'APISPERU.com'
                            };
                            
                            return nombreCompleto;
                        }
                        return null;
                    }
                },
                // API de respaldo con token (deprecada)
                {
                    url: `https://dniruc.apisperu.com/api/v1/dni/${dni}?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6InRlc3RAdGVzdC5jb20ifQ.bb2doqtI_pKcqT3TsCtm9-lFfwHJUkkrOkF_a1r7jW4`,
                    headers: {},
                    process: (data) => {
                        if (data && data.success && data.nombres) {
                            return `${data.nombres} ${data.apellidoPaterno || ''} ${data.apellidoMaterno || ''}`.trim();
                        }
                        return null;
                    }
                }
            ];
            
            let nombreCompleto = null;
            let apiUsada = '';
            
            // Intentar con cada API hasta encontrar una que funcione
            for (const api of apis) {
                try {
                    console.log(`Intentando API DNI: ${api.url}`);
                    
                    // Configurar headers según la API
                    const fetchOptions = {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            ...api.headers
                        }
                    };
                    
                    const response = await fetch(api.url, fetchOptions);
                    const data = await response.json();
                    
                    console.log(`Respuesta de ${api.url}:`, data);
                    
                    nombreCompleto = api.process(data);
                    if (nombreCompleto) {
                        apiUsada = api.url.includes('api-dni-ultra.php') ? 'API Ultra-Robusta DRTC' :
                                  api.url.includes('api-dni-hibrido.php') ? 'API Híbrida APISPERU+Local' :
                                  api.url.includes('api/api-dni-local.php') ? 'Base de Datos Local RENIEC-DRTC' :
                                  api.url.includes('api/test-api-dni.php') ? 'API Proxy (Factiliza)' :
                                  api.url.includes('dniruc.apisperu.com') ? 'APISPERU.com (Oficial)' :
                                  api.url.includes('factiliza') ? 'Factiliza (Oficial)' :
                                  api.url.includes('decolecta') ? 'Decolecta (Respaldo)' : 
                                  'API Externa';
                        break;
                    }
                } catch (apiError) {
                    console.log(`Error con API ${api.url}:`, apiError);
                    continue;
                }
            }
            
            if (nombreCompleto) {
                razonSocialInput.value = nombreCompleto;
                razonSocialInput.style.backgroundColor = '#d4edda';
                razonSocialInput.style.borderColor = '#28a745';
                razonSocialInput.title = `Datos obtenidos de: ${apiUsada}`;
                
                // También completar el nombre del conductor si es persona natural
                const nombreConductorInput = document.querySelector('input[name="nombre_conductor_1"]');
                if (nombreConductorInput) {
                    nombreConductorInput.value = nombreCompleto;
                    nombreConductorInput.style.backgroundColor = '#e2f3ff';
                    nombreConductorInput.title = 'Autocompletado desde DNI del operador';
                }
                
                // Mostrar éxito en el info
                const infoData = document.getElementById('info-data');
                if (infoData) {
                    infoData.innerHTML = `<i class="fas fa-check-circle text-success me-1"></i>Datos obtenidos de ${apiUsada}`;
                    setTimeout(() => {
                        infoData.innerHTML = '<i class="fas fa-info-circle me-1"></i>RUC: 11 dígitos | DNI: 8 dígitos';
                    }, 3000);
                }
            } else {
                // Si ninguna API funcionó, permitir ingreso manual
                razonSocialInput.value = '';
                razonSocialInput.placeholder = 'DNI no encontrado - Ingrese el nombre manualmente';
                razonSocialInput.style.backgroundColor = '#fff3cd';
                razonSocialInput.style.borderColor = '#ffc107';
                razonSocialInput.focus();
                
                // Mostrar mensaje informativo
                const infoData = document.getElementById('info-data');
                if (infoData) {
                    infoData.innerHTML = '<i class="fas fa-exclamation-triangle text-warning me-1"></i>DNI no encontrado - Complete manualmente';
                    setTimeout(() => {
                        infoData.innerHTML = '<i class="fas fa-info-circle me-1"></i>RUC: 11 dígitos | DNI: 8 dígitos';
                    }, 5000);
                }
            }
        } catch (error) {
            console.error('Error general consultando DNI:', error);
            razonSocialInput.value = '';
            razonSocialInput.placeholder = 'Error de conexión - Ingrese el nombre manualmente';
            razonSocialInput.style.backgroundColor = '#fff3cd';
            razonSocialInput.style.borderColor = '#ffc107';
            razonSocialInput.focus();
        } finally {
            loadingData.style.display = 'none';
        }
    }
    
    // Event listener para RUC/DNI único
    rucDniInput.addEventListener('blur', function() {
        const valor = this.value.trim();
        
        // Limpiar estilos previos
        razonSocialInput.style.backgroundColor = '';
        razonSocialInput.style.borderColor = '';
        razonSocialInput.title = '';
        razonSocialInput.placeholder = 'Se completará automáticamente al ingresar RUC/DNI';
        
        if (valor.length === 8 && /^\d{8}$/.test(valor)) {
            // Es un DNI
            console.log(`Consultando DNI: ${valor}`);
            consultarDNI(valor);
        } else if (valor.length === 11 && /^\d{11}$/.test(valor)) {
            // Es un RUC
            console.log(`Consultando RUC: ${valor}`);
            consultarRUC(valor);
        } else if (valor.length > 0) {
            razonSocialInput.value = '';
            razonSocialInput.placeholder = 'Formato inválido - DNI: 8 dígitos, RUC: 11 dígitos';
            razonSocialInput.style.backgroundColor = '#f8d7da';
            razonSocialInput.style.borderColor = '#dc3545';
        }
    });
    
    // Función para probar APIs manualmente (botón de prueba)
    function crearBotonPrueba() {
        const btnPrueba = document.createElement('button');
        btnPrueba.type = 'button';
        btnPrueba.className = 'btn btn-sm btn-outline-secondary ms-2';
        btnPrueba.innerHTML = '<i class="fas fa-search me-1"></i>Probar';
        btnPrueba.title = 'Probar consulta manualmente';
        
        btnPrueba.onclick = function() {
            const valor = rucDniInput.value.trim();
            if (valor.length === 8) {
                consultarDNI(valor);
            } else if (valor.length === 11) {
                consultarRUC(valor);
            } else {
                alert('Ingrese un DNI (8 dígitos) o RUC (11 dígitos) válido');
            }
        };
        
        // Agregar el botón al lado del campo RUC/DNI
        const container = rucDniInput.parentNode;
        const inputGroup = document.createElement('div');
        inputGroup.className = 'input-group';
        
        // Mover el input al grupo
        container.removeChild(rucDniInput);
        inputGroup.appendChild(rucDniInput);
        
        // Agregar el botón
        const appendDiv = document.createElement('div');
        appendDiv.className = 'input-group-append';
        appendDiv.appendChild(btnPrueba);
        inputGroup.appendChild(appendDiv);
        
        // Agregar el grupo al contenedor
        const label = container.querySelector('label');
        container.insertBefore(inputGroup, label.nextSibling);
    }
    
    // Crear el botón de prueba
    setTimeout(crearBotonPrueba, 100);
    
    // Validación en tiempo real para RUC/DNI
    rucDniInput.addEventListener('input', function() {
        const valor = this.value;
        
        // Solo permitir números
        this.value = valor.replace(/[^0-9]/g, '');
        
        // Validar longitud
        if (this.value.length > 11) {
            this.value = this.value.substring(0, 11);
        }
        
        // Indicar el tipo de documento en tiempo real
        const length = this.value.length;
        if (length <= 8) {
            this.placeholder = 'DNI: 12345678';
            this.style.borderColor = length === 8 ? '#28a745' : '#ffc107';
        } else {
            this.placeholder = 'RUC: 20123456789';
            this.style.borderColor = length === 11 ? '#28a745' : '#ffc107';
        }
    });
});

// FUNCIONES PARA MODALES FLOTANTES (código existente)
let tiempoInicioRegistro = null;
let actaIdEnProceso = null;
let autoguardadoInterval = null;

// Función para abrir modales
function abrirModal(modalId) {
    document.getElementById(modalId).style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    // Auto-llenar campos de fecha y hora en modal nueva acta
    if (modalId === 'modal-nueva-acta') {
        iniciarRegistroAutomatico();
    }
}

// Función para iniciar el registro automático de tiempo
function iniciarRegistroAutomatico() {
    tiempoInicioRegistro = new Date();
    const ahora = tiempoInicioRegistro;
    
    // Llenar campos automáticos
    document.getElementById('fecha_inspeccion_hidden').value = ahora.toISOString().split('T')[0];
    document.getElementById('hora_inicio_hidden').value = ahora.toTimeString().slice(0, 5);
    
    // Mostrar información de tiempo en el formulario
    mostrarTiempoEnFormulario();
    
    // Iniciar autoguardado cada 30 segundos
    iniciarAutoguardado();
    
    console.log('Registro iniciado a las:', ahora.toLocaleTimeString());
}

// Función para mostrar el tiempo en el formulario
function mostrarTiempoEnFormulario() {
    // Crear elementos para mostrar el tiempo si no existen
    if (!document.getElementById('tiempo-registro-info')) {
        const tiempoInfo = document.createElement('div');
        tiempoInfo.id = 'tiempo-registro-info';
        tiempoInfo.className = 'alert alert-info d-flex align-items-center mb-3';
        tiempoInfo.innerHTML = `
            <i class="fas fa-clock me-2"></i>
            <div>
                <strong>Registro iniciado:</strong> <span id="hora-inicio-display">${tiempoInicioRegistro.toLocaleTimeString()}</span> |
                <strong>Tiempo transcurrido:</strong> <span id="tiempo-transcurrido">00:00:00</span>
                <span id="autoguardado-status" class="text-success ms-3"><i class="fas fa-check-circle"></i> Autoguardado activo</span>
            </div>
        `;
        
        // Insertar después del header del modal
        const modalBody = document.querySelector('#modal-nueva-acta .modal-body-custom');
        modalBody.insertBefore(tiempoInfo, modalBody.firstChild);
    }
    
    // Actualizar tiempo transcurrido cada segundo
    setInterval(actualizarTiempoTranscurrido, 1000);
}

// Función para actualizar el tiempo transcurrido
function actualizarTiempoTranscurrido() {
    if (!tiempoInicioRegistro) return;
    
    const ahora = new Date();
    const diferencia = ahora - tiempoInicioRegistro;
    
    const horas = Math.floor(diferencia / 3600000);
    const minutos = Math.floor((diferencia % 3600000) / 60000);
    const segundos = Math.floor((diferencia % 60000) / 1000);
    
    const tiempoFormateado = `${horas.toString().padStart(2, '0')}:${minutos.toString().padStart(2, '0')}:${segundos.toString().padStart(2, '0')}`;
    
    const elemento = document.getElementById('tiempo-transcurrido');
    if (elemento) {
        elemento.textContent = tiempoFormateado;
    }
}

// Función para iniciar autoguardado
function iniciarAutoguardado() {
    // Limpiar interval anterior si existe
    if (autoguardadoInterval) {
        clearInterval(autoguardadoInterval);
    }
    
    // Guardar progreso cada 30 segundos
    autoguardadoInterval = setInterval(() => {
        if (actaIdEnProceso) {
            guardarProgresoAutomatico();
        }
    }, 30000);
}

// Función para guardar progreso automáticamente
function guardarProgresoAutomatico() {
    if (!actaIdEnProceso) return;
    
    const formData = new FormData(document.getElementById('form-nueva-acta'));
    const data = Object.fromEntries(formData.entries());
    
    fetch(`/api/actas/${actaIdEnProceso}/progreso`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            mostrarAutoguardadoExitoso(result.hora_actualizacion);
        }
    })
    .catch(error => {
        console.error('Error en autoguardado:', error);
        mostrarErrorAutoguardado();
    });
}

// Función para mostrar autoguardado exitoso
function mostrarAutoguardadoExitoso(hora) {
    const status = document.getElementById('autoguardado-status');
    if (status) {
        status.innerHTML = `<i class="fas fa-check-circle text-success"></i> Autoguardado: ${hora}`;
        status.className = 'text-success ms-3';
    }
}

// Función para mostrar error de autoguardado
function mostrarErrorAutoguardado() {
    const status = document.getElementById('autoguardado-status');
    if (status) {
        status.innerHTML = `<i class="fas fa-exclamation-triangle text-warning"></i> Error en autoguardado`;
        status.className = 'text-warning ms-3';
    }
}

// Función para finalizar registro
function finalizarRegistroActa() {
    if (!actaIdEnProceso) {
        alert('No hay un acta en proceso para finalizar');
        return;
    }
    
    fetch(`/api/actas/${actaIdEnProceso}/finalizar`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert(`Acta finalizada exitosamente.\nTiempo total: ${result.tiempo_total}\nHora de finalización: ${result.hora_finalizacion}`);
            limpiarRegistroTiempo();
            cerrarModal('modal-nueva-acta');
        }
    })
    .catch(error => {
        console.error('Error al finalizar:', error);
        alert('Error al finalizar el registro del acta');
    });
}

// Función para limpiar el registro de tiempo
function limpiarRegistroTiempo() {
    tiempoInicioRegistro = null;
    actaIdEnProceso = null;
    if (autoguardadoInterval) {
        clearInterval(autoguardadoInterval);
        autoguardadoInterval = null;
    }
    
    // Limpiar elementos de tiempo del DOM
    const tiempoInfo = document.getElementById('tiempo-registro-info');
    if (tiempoInfo) {
        tiempoInfo.remove();
    }
}

// Función para cerrar modales
function cerrarModal(modalId) {
    // Si es el modal de nueva acta y hay un registro en proceso, preguntar si desea finalizar
    if (modalId === 'modal-nueva-acta' && actaIdEnProceso) {
        const confirmar = confirm('¿Desea finalizar el registro del acta antes de cerrar?\n\nSi cierra sin finalizar, el acta quedará como borrador y podrá continuar más tarde.');
        
        if (confirmar) {
            finalizarRegistroActa();
            return; // La función de finalizar se encargará de cerrar el modal
        } else {
            // Guardar como borrador
            guardarProgresoAutomatico();
        }
    }
    
    document.getElementById(modalId).style.display = 'none';
    document.body.style.overflow = 'auto';
    
    // Limpiar tiempo si es nueva acta
    if (modalId === 'modal-nueva-acta') {
        limpiarRegistroTiempo();
    }
}

// Cerrar modal al hacer clic fuera
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('floating-modal')) {
        cerrarModal(e.target.id);
    }
});

// FUNCIONES ESPECÍFICAS PARA CADA MODAL

// Modal Editar Acta
function buscarActaEditar() {
    const criterio = document.getElementById('buscar-editar').value.trim();
    if (!criterio) {
        alert('Por favor ingrese un criterio de búsqueda');
        return;
    }
    
    // Simulación de búsqueda
    document.getElementById('acta-numero-editar').textContent = 'DRTC-APU-2024-001';
    document.getElementById('resultado-editar').style.display = 'block';
    
    // Aquí se haría la llamada AJAX real para buscar el acta
    console.log('Buscando acta con criterio:', criterio);
}

// Modal Eliminar Acta
function buscarActaEliminar() {
    const criterio = document.getElementById('buscar-eliminar').value.trim();
    if (!criterio) {
        alert('Por favor ingrese un criterio de búsqueda');
        return;
    }
    
    // Simulación de búsqueda
    document.getElementById('eliminar-numero-acta').textContent = 'DRTC-APU-2024-001';
    document.getElementById('eliminar-fecha-acta').textContent = '15/08/2024';
    document.getElementById('resultado-eliminar').style.display = 'block';
    
    // Aquí se haría la llamada AJAX real para buscar el acta
    console.log('Buscando acta para eliminar con criterio:', criterio);
}

function confirmarEliminacion() {
    const motivo = document.getElementById('motivo-eliminacion').value;
    const codigo = document.getElementById('codigo-autorizacion').value;
    const supervisor = document.getElementById('supervisor-autorizante').value;
    
    if (!motivo || !codigo || !supervisor) {
        alert('Todos los campos son obligatorios para la eliminación');
        return;
    }
    
    if (confirm('¿Está seguro de que desea eliminar esta acta? Esta acción es IRREVERSIBLE.')) {
        // Aquí se haría la llamada AJAX para eliminar
        alert('Acta eliminada exitosamente');
        cerrarModal('modal-eliminar-acta');
    }
}

function cancelarEliminacion() {
    document.getElementById('resultado-eliminar').style.display = 'none';
    document.getElementById('buscar-eliminar').value = '';
}

// Modal Consultas
function ejecutarConsulta() {
    // Mostrar resumen
    document.getElementById('resumen-consulta').style.display = 'block';
    
    // Simulación de datos
    document.getElementById('total-actas').textContent = '127';
    document.getElementById('actas-procesadas-modal').textContent = '89';
    document.getElementById('actas-pendientes-modal').textContent = '32';
    document.getElementById('actas-anuladas-modal').textContent = '6';
    
    // Simulación de resultados en tabla
    const tbody = document.getElementById('tbody-resultados');
    tbody.innerHTML = `
        <tr>
            <td class="fw-bold">DRTC-APU-2024-001</td>
            <td>15/08/2024</td>
            <td>TRANSPORTES LIMA S.A.C.</td>
            <td>20123456789</td>
            <td>ABC-123</td>
            <td>Documentaria</td>
            <td><span class="badge bg-warning">Grave</span></td>
            <td><span class="badge bg-success">Procesada</span></td>
            <td>${document.querySelector('input[name="inspector"] option:checked')?.textContent || 'Inspector 1'}</td>
        </tr>
        <tr>
            <td class="fw-bold">DRTC-APU-2024-002</td>
            <td>14/08/2024</td>
            <td>JUAN PÉREZ GARCÍA</td>
            <td>12345678</td>
            <td>DEF-456</td>
            <td>Operacional</td>
            <td><span class="badge bg-danger">Muy Grave</span></td>
            <td><span class="badge bg-warning">Pendiente</span></td>
            <td>${document.querySelector('input[name="inspector"] option:checked')?.textContent || 'Inspector 1'}</td>
        </tr>
    `;
    
    console.log('Ejecutando consulta con filtros del formulario');
}

function exportarExcel() {
    // Crear datos de ejemplo para el formato oficial
    const actaData = {
        numero: 'DRTC-APU-2025-000451',
        codigo_ds: '017-2009-MTC',
        fecha: new Date().toLocaleDateString('es-PE'),
        inspector: '{{ Auth::user()->name ?? "Inspector DRTC" }}',
        // Datos del infractor
        tipoAgente: 'Transportista', // Operador de Ruta, Conductor
        placa: 'ABC-123',
        razonSocial: 'TRANSPORTES LIMA S.A.C.',
        rucDni: '20123456789',
        fechaHoraInicio: new Date().toLocaleString('es-PE'),
        fechaHoraFin: '',
        nombreConductor: 'JUAN CARLOS PÉREZ GARCÍA',
        licencia: 'Q12345678',
        clase: 'A-IIIb',
        lugarIntervencion: 'Av. Principal Km 15 - Abancay',
        redVial: 'Red Vial Nacional',
        origen: 'Abancay',
        destino: 'Cusco',
        tipoServicio: 'Público - Interprovincial',
        personas: '45',
        descripcionHechos: 'Vehículo de transporte público interprovincial circulando sin el permiso de operación vigente, incumpliendo las disposiciones del Reglamento Nacional de Administración de Transporte.',
        mediosProbatorios: 'Inspección física del vehículo, verificación documentaria',
        calificacion: 'Muy Grave',
        medidaAdministrativa: 'Retención de Tarjeta de Circulación',
        sancion: 'Multa equivalente al 50% de la UIT',
        observacionesIntervenido: '',
        observacionesInspector: 'Operador reincidente en falta documentaria'
    };
    
    // Crear estructura Excel que simule el formato oficial del acta
    const excelData = [
        ['GOBIERNO REGIONAL DE APURÍMAC'],
        ['DIRECCIÓN REGIONAL DE TRANSPORTES Y COMUNICACIONES'],
        ['DIRECCIÓN DE CIRCULACIÓN TERRESTRE OF. FISCALIZACIÓN'],
        [''],
        [`ACTA DE CONTROL N° ${actaData.numero} - 2025`],
        [''],
        [`D.S. N° ${actaData.codigo_ds}`],
        ['Código de infracciones y/o incumplimiento'],
        ['Tipo infractor'],
        [''],
        ['Quienes suscriben la presente acta nos identificamos como Inspectores acreditados de la DRTC AP.'],
        ['informamos el objeto y el sustento legal de la fiscalización, cumpliendo con lo señalado en la Normativa vigente.'],
        [''],
        ['DATOS DEL AGENTE INFRACTOR:'],
        ['Agente infractor:', actaData.tipoAgente, '', 'Transportista □', 'Operador de Ruta □', 'Conductor □'],
        ['Placa N°:', actaData.placa],
        ['Razón Social/Nombre:', actaData.razonSocial],
        ['RUC/DNI:', actaData.rucDni],
        ['Fecha y Hora inicio:', actaData.fechaHoraInicio],
        ['Fecha y Hora de fin:', actaData.fechaHoraFin],
        ['Nombre de Conductor 1:', actaData.nombreConductor],
        ['N° Licencia/DNI del conductor 1:', actaData.licencia, 'N°', '', 'Clase y Categoría', actaData.clase],
        ['Lugar de la intervención:', actaData.lugarIntervencion],
        ['N° Km. De la red Vial Nacional Prov./Dpto.', actaData.redVial],
        ['Origen de viaje (Depto./Prov./Distrito)', actaData.origen],
        ['Destino Viaje: (Depto./Prov./Distrito)', actaData.destino],
        ['Tipo de Servicio que presta', actaData.tipoServicio, 'Personas', actaData.personas, 'mercancía', ''],
        ['Inspector:', actaData.inspector],
        [''],
        ['DESCRIPCIÓN DE LOS HECHOS:'],
        [actaData.descripcionHechos],
        [''],
        ['Medios probatorios:', actaData.mediosProbatorios],
        ['Calificación de la Infracción:', actaData.calificacion],
        ['Medida(s) Administrativa(s):', actaData.medidaAdministrativa],
        ['Sanción:', actaData.sancion],
        ['Observaciones del intervenido:', actaData.observacionesIntervenido],
        [''],
        ['Observaciones del inspector:', actaData.observacionesInspector],
        [''],
        ['La medida administrativa impuesta deberá ser cumplida estrictamente, bajo apercibimiento expreso de ser denunciado'],
        ['penalmente por desobediencia o resistencia a la autoridad, ante su incumplimiento.'],
        [''],
        [''],
        ['________________________', '', '________________________', '', '________________________'],
        ['Firma del intervenido', '', 'Firma del Representante PNP', '', 'Firma del Inspector'],
        ['Nom.Ap.:', '', 'Nomb.Ap.:', '', 'Nombre Ap.:'],
        ['DNI:', '', 'CIP:', '', 'CI:']
    ];
    
    // Convertir a CSV para Excel
    const csvContent = excelData.map(row => 
        row.map(cell => `"${cell || ''}"`).join(',')
    ).join('\n');
    
    // Crear y descargar archivo
    const blob = new Blob(['\ufeff' + csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `Acta_Fiscalizacion_${actaData.numero.replace(/[^0-9]/g, '')}_${new Date().toISOString().split('T')[0]}.csv`;
    link.click();
    
    // Mostrar mensaje de éxito
    setTimeout(() => {
        alert(`✅ Archivo Excel generado exitosamente:\nActa N° ${actaData.numero}\nFormato: Acta Oficial DRTC Apurímac`);
    }, 500);
}

function exportarPDF() {
    // Simular generación de PDF con el formato oficial del acta
    const actaData = {
        numero: 'DRTC-APU-2025-000451',
        codigo_ds: '017-2009-MTC',
        fecha: new Date().toLocaleDateString('es-PE'),
        inspector: '{{ Auth::user()->name ?? "Inspector DRTC" }}',
        tipoAgente: 'Transportista',
        placa: 'ABC-123',
        razonSocial: 'TRANSPORTES LIMA S.A.C.',
        rucDni: '20123456789',
        nombreConductor: 'JUAN CARLOS PÉREZ GARCÍA',
        licencia: 'Q12345678',
        clase: 'A-IIIb',
        lugarIntervencion: 'Av. Principal Km 15 - Abancay',
        tipoServicio: 'Público - Interprovincial',
        descripcionHechos: 'Vehículo de transporte público interprovincial circulando sin el permiso de operación vigente, incumpliendo las disposiciones del Reglamento Nacional de Administración de Transporte.',
        calificacion: 'Muy Grave',
        medidaAdministrativa: 'Retención de Tarjeta de Circulación',
        sancion: 'Multa equivalente al 50% de la UIT'
    };
    
    // Crear contenido HTML para PDF que replique el formato oficial
    const htmlContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Acta de Fiscalización ${actaData.numero}</title>
            <style>
                body { font-family: 'Arial', sans-serif; font-size: 10px; margin: 20px; }
                .header { text-align: center; margin-bottom: 20px; }
                .logos { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
                .title { font-size: 14px; font-weight: bold; text-align: center; margin: 15px 0; }
                .form-row { display: flex; margin-bottom: 8px; }
                .form-field { border: 1px solid #000; padding: 5px; margin-right: 5px; }
                .checkbox { width: 15px; height: 15px; border: 1px solid #000; display: inline-block; margin-right: 5px; }
                .description-box { border: 1px solid #000; padding: 10px; min-height: 80px; margin-bottom: 10px; }
                .signature-area { display: flex; justify-content: space-between; margin-top: 30px; }
                .signature { text-align: center; border-bottom: 1px solid #000; width: 30%; padding-bottom: 5px; }
                .footer-text { text-align: center; font-size: 9px; margin-top: 20px; }
                table { width: 100%; border-collapse: collapse; }
                td { border: 1px solid #000; padding: 5px; }
                .no-border { border: none; }
            </style>
        </head>
        <body>
            <div class="header">
                <div class="logos">
                    <div>🇵🇪 PERÚ</div>
                    <div>GOBIERNO REGIONAL DE APURÍMAC</div>
                    <div>DIRECCIÓN REGIONAL DE TRANSPORTES Y COMUNICACIONES</div>
                    <div>DIRECCIÓN DE CIRCULACIÓN TERRESTRE OF. FISCALIZACIÓN</div>
                    <div>🔒</div>
                </div>
            </div>
            
            <div class="title">ACTA DE CONTROL N° ${actaData.numero} - 2025</div>
            
            <div style="text-align: center; margin-bottom: 15px;">
                <div>D.S. N° ${actaData.codigo_ds}</div>
                <div>Código de infracciones y/o incumplimiento</div>
                <div><strong>Tipo infractor</strong></div>
            </div>
            
            <p style="font-size: 9px; margin-bottom: 15px;">
                Quienes suscriben la presente acta nos identificamos como Inspectores acreditados de la DRTC AP, informamos el objeto y el 
                sustento legal de la fiscalización, cumpliendo con lo señalado en la Normativa vigente.
            </p>
            
            <table style="margin-bottom: 15px;">
                <tr>
                    <td style="font-weight: bold;">Agente infractor:</td>
                    <td>${actaData.tipoAgente}</td>
                    <td>Transportista <span class="checkbox">☑</span></td>
                    <td>Operador de Ruta <span class="checkbox">☐</span></td>
                    <td>Conductor <span class="checkbox">☐</span></td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Placa N°:</td>
                    <td colspan="4">${actaData.placa}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Razón Social/Nombre:</td>
                    <td colspan="4">${actaData.razonSocial}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">RUC/DNI:</td>
                    <td colspan="4">${actaData.rucDni}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Fecha y Hora inicio:</td>
                    <td colspan="2">${new Date().toLocaleString('es-PE')}</td>
                    <td style="font-weight: bold;">Fecha y Hora de fin:</td>
                    <td>_________________</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Nombre de Conductor 1:</td>
                    <td colspan="4">${actaData.nombreConductor}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">N° Licencia/DNI del conductor 1:</td>
                    <td>${actaData.licencia}</td>
                    <td>N°</td>
                    <td style="font-weight: bold;">Clase y Categoría</td>
                    <td>${actaData.clase}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Lugar de la intervención:</td>
                    <td colspan="4">${actaData.lugarIntervencion}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Tipo de Servicio que presta</td>
                    <td>${actaData.tipoServicio}</td>
                    <td style="font-weight: bold;">Personas</td>
                    <td>45</td>
                    <td style="font-weight: bold;">mercancía</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Inspector:</td>
                    <td colspan="4">${actaData.inspector}</td>
                </tr>
            </table>
            
            <div style="font-weight: bold; margin-bottom: 10px;">Descripción de los hechos:</div>
            <div class="description-box">
                ${actaData.descripcionHechos}
            </div>
            
            <table style="margin-bottom: 15px;">
                <tr>
                    <td style="font-weight: bold;">Calificación de la Infracción:</td>
                    <td>${actaData.calificacion}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Medida(s) Administrativa(s):</td>
                    <td>${actaData.medidaAdministrativa}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Sanción:</td>
                    <td>${actaData.sancion}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Observaciones del intervenido:</td>
                    <td style="height: 40px;"></td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Observaciones del inspector:</td>
                    <td style="height: 40px;">Operador reincidente en falta documentaria</td>
                </tr>
            </table>
            
            <div class="footer-text">
                La medida administrativa impuesta deberá ser cumplida estrictamente, bajo apercibimiento expreso de ser denunciado<br>
                penalmente por desobediencia o resistencia a la autoridad, ante su incumplimiento.
            </div>
            
            <div class="signature-area">
                <div class="signature">
                    <div>________________________</div>
                    <div>Firma del intervenido</div>
                    <div>Nom.Ap.:</div>
                    <div>DNI:</div>
                </div>
                <div class="signature">
                    <div>________________________</div>
                    <div>Firma del Representante PNP</div>
                    <div>Nomb.Ap.:</div>
                    <div>CIP:</div>
                </div>
                <div class="signature">
                    <div>________________________</div>
                    <div>Firma del Inspector</div>
                    <div>Nombre Ap.:</div>
                    <div>CI:</div>
                </div>
            </div>
        </body>
        </html>
    `;
    
    // Simular descarga de PDF
    setTimeout(() => {
        alert(`✅ Archivo PDF generado exitosamente:\n\n📄 Acta N° ${actaData.numero}\n📅 Fecha: ${actaData.fecha}\n👮 Inspector: ${actaData.inspector}\n📋 Formato: Acta Oficial DRTC Apurímac\n\n⬇️ El archivo se descargará automáticamente...`);
        
        // En una implementación real, aquí se enviaría el HTML a un servicio de generación de PDF
        console.log('HTML generado para PDF:', htmlContent);
        
        // Simular descarga
        const element = document.createElement('a');
        element.href = 'data:text/html;charset=utf-8,' + encodeURIComponent(htmlContent);
        element.download = `Acta_Fiscalizacion_${actaData.numero.replace(/[^0-9]/g, '')}_${new Date().toISOString().split('T')[0]}.html`;
        element.click();
        
    }, 1000);
}

function generarReporte() {
    alert('Generando reporte estadístico...');
    console.log('Generando reporte estadístico');
}

// ESCAPE KEY para cerrar modales
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modalesAbiertos = document.querySelectorAll('.floating-modal[style*="flex"]');
        modalesAbiertos.forEach(modal => {
            cerrarModal(modal.id);
        });
    }
});
</script>
