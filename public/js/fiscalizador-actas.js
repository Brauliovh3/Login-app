/**
 * ================================
 * GESTIÓN DE ACTAS - FISCALIZADOR
 * Sistema de Gestión - JavaScript
 * ================================
 */

// Variable global para almacenar actas
let todasLasActas = [];

// ================================
// FUNCIONES PRINCIPALES - ACTAS
// ================================

// Función principal llamada desde el menú
function loadActas(event) {
    console.log('🔄 Cargando sistema de gestión de actas...');
    
    // Obtener la sección específica del data-section
    const clickedElement = event?.target?.closest('a');
    const section = clickedElement?.getAttribute('data-section') || 'actas-contra';
    
    console.log('🎯 Sección solicitada:', section);
    
    switch(section) {
        case 'crear-acta':
            loadCrearActa();
            break;
        case 'mis-actas':
            loadMisActas();
            break;
        case 'actas-contra':
        default:
            loadGestionActas();
            // Cargar las actas automáticamente
            setTimeout(() => {
                cargarActasDesdeAPI();
            }, 500);
            break;
    }
}

// Función específica para crear acta
function loadCrearActa() {
    console.log('📝 Cargando formulario de nueva acta...');
    loadGestionActas();
    // Mostrar mensaje y abrir modal después de cargar la interfaz
    setTimeout(() => {
        try {
            showCrearActaModal();
        } catch (error) {
            console.error('Error al abrir modal:', error);
            mostrarNotificacion('Función de crear acta cargada. Click en "Nueva Acta" para continuar.', 'info');
        }
    }, 500);
}

// Función específica para mis actas (filtradas por usuario)
function loadMisActas() {
    console.log('👤 Cargando mis actas...');
    loadGestionActas();
    // Cargar solo las actas del usuario actual
    setTimeout(() => {
        cargarMisActasDesdeAPI();
    }, 500);
}

function loadGestionActas() {
    const contentContainer = document.getElementById('contentContainer');
    contentContainer.innerHTML = `
        <div class="content-section active">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-file-alt"></i> Gestión de Actas</h2>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary" onclick="showCrearActaModal()">
                        <i class="fas fa-plus"></i> Nueva Acta
                    </button>
                    <button class="btn btn-outline-secondary" onclick="cargarActasDesdeAPI()">
                        <i class="fas fa-refresh"></i> Actualizar
                    </button>
                </div>
            </div>
            
            <!-- Filtros y Búsqueda -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Buscar Acta</label>
                            <input type="text" class="form-control" id="searchActas" placeholder="Número, placa, conductor..." onkeyup="filtrarActas()">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Estado</label>
                            <select class="form-select" id="filterEstado" onchange="filtrarActas()">
                                <option value="">Todos</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="pagada">Pagada</option>
                                <option value="anulada">Anulada</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Fecha Desde</label>
                            <input type="date" class="form-control" id="filterFechaDesde" onchange="filtrarActas()">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Fecha Hasta</label>
                            <input type="date" class="form-control" id="filterFechaHasta" onchange="filtrarActas()">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button class="btn btn-secondary w-100" onclick="limpiarFiltrosActas()">
                                <i class="fas fa-times"></i> Limpiar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Actas -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="actasTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>Número</th>
                                    <th>Placa</th>
                                    <th>Conductor</th>
                                    <th>RUC/DNI</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="actasTableBody">
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <div class="spinner-border" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                        <p class="mt-2">Cargando actas...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Cargar actas desde la API
    cargarActasDesdeAPI();
}

// ================================
// CARGAR ACTAS DESDE API
// ================================

async function cargarActasDesdeAPI() {
    try {
        const response = await fetch('/api/actas', {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json'
            }
        });

        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (err) {
            throw { status: response.status, text };
        }
        
        if (data.success) {
            todasLasActas = data.actas || [];
            mostrarActas(todasLasActas);
        } else {
            mostrarErrorActas('Error al cargar actas: ' + (data.message || 'Error desconocido'));
        }
    } catch (error) {
        if (error.text) {
            mostrarErrorActas('Respuesta inesperada del servidor');
        } else {
            mostrarErrorActas('Error de conexión: ' + error.message);
        }
    }
}

// Función para cargar solo las actas del usuario actual
async function cargarMisActasDesdeAPI() {
    try {
        const response = await fetch('/api/actas', {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json'
            }
        });

        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (err) {
            throw { status: response.status, text };
        }
        
        if (data.success) {
            const todasActas = data.actas || [];
            // Filtrar solo las actas del usuario actual
            const userName = window.dashboardUserName;
            const misActas = todasActas.filter(acta => 
                acta.inspector_responsable === userName || 
                acta.inspector === userName ||
                acta.user_name === userName
            );
            
            todasLasActas = misActas;
            mostrarActas(misActas);
            
            // Actualizar el título para indicar que son "Mis Actas"
            const titleElement = document.querySelector('.content-section h2');
            if (titleElement) {
                titleElement.innerHTML = '<i class="fas fa-user-edit"></i> Mis Actas';
            }
        } else {
            mostrarErrorActas('Error al cargar mis actas: ' + (data.message || 'Error desconocido'));
        }
    } catch (error) {
        if (error.text) {
            mostrarErrorActas('Respuesta inesperada del servidor');
        } else {
            mostrarErrorActas('Error de conexión: ' + error.message);
        }
    }
}

function mostrarActas(actas) {
    const tbody = document.getElementById('actasTableBody');
    
    if (actas.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center">
                    <i class="fas fa-file-alt text-muted" style="font-size: 2rem;"></i>
                    <p class="mt-2 text-muted">No se encontraron actas</p>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = actas.map(acta => `
        <tr>
            <td>
                <strong>${acta.numero_acta || 'N/A'}</strong>
            </td>
            <td>
                <span class="badge bg-info">${acta.placa || acta.placa_vehiculo || 'N/A'}</span>
            </td>
            <td>
                ${acta.conductor_nombre || acta.nombre_conductor || 'N/A'}
            </td>
            <td>
                <small class="text-muted">${acta.ruc_dni || 'N/A'}</small>
            </td>
            <td>
                <span class="badge ${getEstadoBadgeClass(acta.estado)}">${getEstadoDisplayName(acta.estado)}</span>
            </td>
            <td>
                <small class="text-muted">
                    ${acta.fecha_acta ? formatDate(acta.fecha_acta) : formatDate(acta.created_at)}
                </small>
            </td>
            <td class="text-center">
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-primary" onclick="verActa(${acta.id})" title="Ver">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-success" onclick="editarActa(${acta.id})" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-info" onclick="imprimirActa(${acta.id})" title="Imprimir">
                        <i class="fas fa-print"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="eliminarActa(${acta.id}, '${acta.numero_acta}')" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

// ================================
// FUNCIONES DE MODAL - ACTAS
// ================================

function showCrearActaModal() {
    console.log('🆕 Abriendo modal para crear nueva acta...');
    
    const modalTitle = document.getElementById('modalTitle');
    const modalBody = document.getElementById('modalBody');
    const modalFooter = document.getElementById('modalFooter');
    
    if (!modalTitle || !modalBody || !modalFooter) {
        console.error('❌ Modal no encontrado');
        mostrarErrorActas('Error: Modal no encontrado en el sistema');
        return;
    }
    
    // Configurar título del modal
    modalTitle.innerHTML = '<i class="fas fa-plus-circle"></i> Crear Nueva Acta';
    
    // Configurar contenido del modal
    modalBody.innerHTML = `
        <form id="formCrearActa" class="row g-3">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Complete los datos para registrar una nueva acta de fiscalización
                </div>
            </div>
            
            <!-- Datos del Operador/Empresa -->
            <div class="col-12">
                <h6 class="text-primary border-bottom pb-2">
                    <i class="fas fa-building"></i> Datos del Operador/Empresa
                </h6>
            </div>
            
            <div class="col-md-6">
                <label class="form-label">RUC/DNI *</label>
                <input type="text" class="form-control" name="ruc_dni" id="ruc_dni" required 
                       placeholder="11 dígitos para RUC, 8 para DNI">
            </div>
            
            <div class="col-md-6">
                <label class="form-label">Razón Social / Nombre</label>
                <input type="text" class="form-control" name="razon_social" id="razon_social" 
                       placeholder="Nombre o razón social">
            </div>
            
            <!-- Datos del Vehículo -->
            <div class="col-12 mt-4">
                <h6 class="text-warning border-bottom pb-2">
                    <i class="fas fa-car"></i> Datos del Vehículo
                </h6>
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Placa del Vehículo *</label>
                <input type="text" class="form-control" name="placa" id="placa" required 
                       placeholder="ABC-123" style="text-transform: uppercase;">
            </div>
            
            <div class="col-md-8">
                <label class="form-label">Tipo de Servicio *</label>
                <select class="form-select" name="tipo_servicio" id="tipo_servicio" required>
                    <option value="">Seleccione...</option>
                    <option value="Interprovincial">Interprovincial</option>
                    <option value="Interdistrital">Interdistrital</option>
                    <option value="Urbano">Urbano</option>
                    <option value="Turístico">Turístico</option>
                    <option value="Carga">Transporte de Carga</option>
                    <option value="Especial">Servicio Especial</option>
                </select>
            </div>
            
            <!-- Datos del Conductor -->
            <div class="col-12 mt-4">
                <h6 class="text-success border-bottom pb-2">
                    <i class="fas fa-user"></i> Datos del Conductor
                </h6>
            </div>
            
            <div class="col-md-6">
                <label class="form-label">Nombre del Conductor *</label>
                <input type="text" class="form-control" name="nombre_conductor" id="nombre_conductor" required
                       placeholder="Apellidos y Nombres completos">
            </div>
            
            <div class="col-md-3">
                <label class="form-label">DNI Conductor</label>
                <input type="text" class="form-control" name="dni_conductor" id="dni_conductor" 
                       placeholder="12345678" maxlength="8">
            </div>
            
            <div class="col-md-3">
                <label class="form-label">N° Licencia</label>
                <input type="text" class="form-control" name="licencia" id="licencia" 
                       placeholder="Número de licencia">
            </div>
            
            <!-- Datos de la Intervención -->
            <div class="col-12 mt-4">
                <h6 class="text-danger border-bottom pb-2">
                    <i class="fas fa-map-marker-alt"></i> Datos de la Intervención
                </h6>
            </div>
            
            <div class="col-md-6">
                <label class="form-label">Lugar de Intervención *</label>
                <input type="text" class="form-control" name="lugar_intervencion" id="lugar_intervencion" required
                       placeholder="Ubicación donde se realizó la intervención">
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Fecha</label>
                <input type="date" class="form-control" name="fecha_intervencion" id="fecha_intervencion" 
                       value="${new Date().toISOString().split('T')[0]}">
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Hora</label>
                <input type="time" class="form-control" name="hora_intervencion" id="hora_intervencion" 
                       value="${new Date().toTimeString().slice(0,5)}">
            </div>
            
            <div class="col-12">
                <label class="form-label">Descripción de los Hechos *</label>
                <textarea class="form-control" name="descripcion_hechos" id="descripcion_hechos" rows="3" required
                          placeholder="Describa detalladamente la infracción o situación encontrada..."></textarea>
            </div>
            
            <!-- Datos de la Sanción -->
            <div class="col-12 mt-4">
                <h6 class="text-secondary border-bottom pb-2">
                    <i class="fas fa-gavel"></i> Datos de la Sanción
                </h6>
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Monto de Multa (S/)</label>
                <input type="number" class="form-control" name="monto_multa" id="monto_multa" 
                       min="0" step="0.01" placeholder="0.00">
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Estado</label>
                <select class="form-select" name="estado" id="estado">
                    <option value="pendiente">Pendiente</option>
                    <option value="pagada">Pagada</option>
                    <option value="anulada">Anulada</option>
                </select>
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Inspector Responsable</label>
                <input type="text" class="form-control" name="inspector_responsable" id="inspector_responsable" 
                       value="${window.dashboardUserName || ''}" readonly>
            </div>
        </form>
    `;
    
    // Configurar botones del modal
    modalFooter.innerHTML = `
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times"></i> Cancelar
        </button>
        <button type="button" class="btn btn-primary" onclick="guardarNuevaActa()">
            <i class="fas fa-save"></i> Guardar Acta
        </button>
    `;
    
    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('generalModal'));
    modal.show();
}

async function guardarNuevaActa() {
    console.log('💾 Guardando nueva acta...');
    
    const form = document.getElementById('formCrearActa');
    const formData = new FormData(form);
    
    // Validar campos requeridos
    const camposRequeridos = ['ruc_dni', 'placa', 'tipo_servicio', 'nombre_conductor', 'lugar_intervencion', 'descripcion_hechos'];
    const camposFaltantes = [];
    
    camposRequeridos.forEach(campo => {
        if (!formData.get(campo)?.trim()) {
            camposFaltantes.push(campo);
        }
    });
    
    if (camposFaltantes.length > 0) {
        mostrarErrorActas('Por favor complete todos los campos obligatorios marcados con *');
        return;
    }
    
    try {
        const response = await fetch('/api/actas', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json'
            },
            body: formData
        });
        
        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (err) {
            throw { status: response.status, text };
        }
        
        if (data.success) {
            mostrarExitoActas(`Acta ${data.numero_acta || 'nueva'} creada exitosamente`);
            
            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('generalModal'));
            modal.hide();
            
            // Recargar la lista de actas
            cargarActasDesdeAPI();
        } else {
            mostrarErrorActas('Error al crear acta: ' + (data.message || 'Error desconocido'));
        }
    } catch (error) {
        console.error('Error al guardar acta:', error);
        if (error.text) {
            mostrarErrorActas('Respuesta inesperada del servidor');
        } else {
            mostrarErrorActas('Error de conexión: ' + error.message);
        }
    }
}

// ================================
// FUNCIONES DE ACCIÓN - ACTAS
// ================================

async function verActa(actaId) {
    try {
        const response = await fetch(`/api/actas/${actaId}`, {
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json'
            }
        });

        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (err) {
            throw { status: response.status, text };
        }

        if (!data.acta) {
            mostrarErrorActas('Acta no encontrada');
            return;
        }

        const acta = data.acta;
        const modalHtml = `
            <div class="modal fade" id="verActaModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Ver Acta - ${acta.numero_acta || 'N/A'}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="card p-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Número de Acta:</strong> ${acta.numero_acta || 'N/A'}</p>
                                        <p><strong>Placa:</strong> ${acta.placa || acta.placa_vehiculo || 'N/A'}</p>
                                        <p><strong>Conductor:</strong> ${acta.conductor_nombre || acta.nombre_conductor || 'N/A'}</p>
                                        <p><strong>RUC/DNI:</strong> ${acta.ruc_dni || 'N/A'}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Estado:</strong> <span class="badge ${getEstadoBadgeClass(acta.estado)}">${getEstadoDisplayName(acta.estado)}</span></p>
                                        <p><strong>Fecha:</strong> ${acta.fecha_acta ? formatDate(acta.fecha_acta) : formatDate(acta.created_at)}</p>
                                        <p><strong>Monto:</strong> S/ ${acta.monto || '0.00'}</p>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <strong>Descripción de Hechos:</strong>
                                    <div class="bg-light p-3 rounded mt-2">
                                        <pre style="white-space: pre-wrap; margin: 0;">${acta.descripcion || acta.descripcion_hechos || 'Sin descripción'}</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn btn-info" onclick="imprimirActa(${acta.id})">
                                <i class="fas fa-print"></i> Imprimir
                            </button>
                            <button type="button" class="btn btn-primary" onclick="editarActa(${acta.id}); bootstrap.Modal.getInstance(document.getElementById('verActaModal')).hide();">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modal = new bootstrap.Modal(document.getElementById('verActaModal'));
        modal.show();

        document.getElementById('verActaModal').addEventListener('hidden.bs.modal', function() {
            this.remove();
        });

    } catch (error) {
        if (error.text) {
            mostrarErrorActas('Respuesta inesperada del servidor');
        } else {
            mostrarErrorActas('Error al cargar acta: ' + error.message);
        }
    }
}

async function editarActa(actaId) {
    try {
        const response = await fetch(`/api/actas/${actaId}`, {
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json'
            }
        });

        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (err) {
            throw { status: response.status, text };
        }

        if (!data.acta) {
            mostrarErrorActas('Acta no encontrada');
            return;
        }

        const acta = data.acta;
        const modalHtml = `
            <div class="modal fade" id="editarActaModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar Acta - ${acta.numero_acta || 'N/A'}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="formEditarActa">
                                <input type="hidden" id="editActaId" value="${acta.id}">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Número de Acta</label>
                                            <input type="text" class="form-control" id="editNumeroActa" value="${acta.numero_acta || ''}" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Placa</label>
                                            <input type="text" class="form-control" id="editPlaca" value="${acta.placa || acta.placa_vehiculo || ''}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Conductor</label>
                                            <input type="text" class="form-control" id="editConductor" value="${acta.conductor_nombre || acta.nombre_conductor || ''}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">RUC/DNI</label>
                                            <input type="text" class="form-control" id="editRucDni" value="${acta.ruc_dni || ''}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Estado</label>
                                            <select class="form-select" id="editEstado" required>
                                                <option value="pendiente" ${acta.estado === 'pendiente' ? 'selected' : ''}>Pendiente</option>
                                                <option value="pagada" ${acta.estado === 'pagada' ? 'selected' : ''}>Pagada</option>
                                                <option value="anulada" ${acta.estado === 'anulada' ? 'selected' : ''}>Anulada</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Monto</label>
                                            <input type="number" step="0.01" class="form-control" id="editMonto" value="${acta.monto || ''}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Descripción de Hechos</label>
                                    <textarea class="form-control" id="editDescripcion" rows="4" required>${acta.descripcion || acta.descripcion_hechos || ''}</textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" onclick="guardarEdicionActa()">Guardar Cambios</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modal = new bootstrap.Modal(document.getElementById('editarActaModal'));
        modal.show();

        document.getElementById('editarActaModal').addEventListener('hidden.bs.modal', function() {
            this.remove();
        });

    } catch (error) {
        if (error.text) {
            mostrarErrorActas('Respuesta inesperada del servidor');
        } else {
            mostrarErrorActas('Error al cargar acta: ' + error.message);
        }
    }
}

async function guardarEdicionActa() {
    const actaId = document.getElementById('editActaId').value;
    const formData = {
        placa: document.getElementById('editPlaca').value,
        conductor_nombre: document.getElementById('editConductor').value,
        ruc_dni: document.getElementById('editRucDni').value,
        estado: document.getElementById('editEstado').value,
        monto: document.getElementById('editMonto').value,
        descripcion: document.getElementById('editDescripcion').value
    };

    try {
        const response = await fetch(`/api/actas/${actaId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (data.success) {
            mostrarExitoActas('Acta actualizada correctamente');
            bootstrap.Modal.getInstance(document.getElementById('editarActaModal')).hide();
            cargarActasDesdeAPI(); // Recargar la lista
        } else {
            mostrarErrorActas('Error al actualizar acta: ' + (data.message || 'Error desconocido'));
        }
    } catch (error) {
        mostrarErrorActas('Error de conexión: ' + error.message);
    }
}

async function imprimirActa(actaId) {
    try {
        const response = await fetch(`/api/actas/${actaId}`, {
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json'
            }
        });

        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (err) {
            throw { status: response.status, text };
        }

        if (!data.acta) {
            mostrarErrorActas('Acta no encontrada');
            return;
        }

        const acta = data.acta;
        const printContent = `
            <div style="padding: 20px; font-family: Arial, sans-serif;">
                <div style="text-align: center; margin-bottom: 30px;">
                    <h1>ACTA DE INFRACCIÓN</h1>
                    <h2>${acta.numero_acta || 'N/A'}</h2>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; background: #f5f5f5; width: 30%;"><strong>Placa del Vehículo:</strong></td>
                            <td style="padding: 8px; border: 1px solid #ddd;">${acta.placa || acta.placa_vehiculo || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; background: #f5f5f5;"><strong>Conductor:</strong></td>
                            <td style="padding: 8px; border: 1px solid #ddd;">${acta.conductor_nombre || acta.nombre_conductor || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; background: #f5f5f5;"><strong>RUC/DNI:</strong></td>
                            <td style="padding: 8px; border: 1px solid #ddd;">${acta.ruc_dni || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; background: #f5f5f5;"><strong>Estado:</strong></td>
                            <td style="padding: 8px; border: 1px solid #ddd;">${getEstadoDisplayName(acta.estado)}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; background: #f5f5f5;"><strong>Monto:</strong></td>
                            <td style="padding: 8px; border: 1px solid #ddd;">S/ ${acta.monto || '0.00'}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; background: #f5f5f5;"><strong>Fecha:</strong></td>
                            <td style="padding: 8px; border: 1px solid #ddd;">${acta.fecha_acta ? formatDate(acta.fecha_acta) : formatDate(acta.created_at)}</td>
                        </tr>
                    </table>
                </div>
                
                <div style="margin: 20px 0;">
                    <h3>Descripción de los Hechos:</h3>
                    <div style="border: 1px solid #ddd; padding: 15px; background: #f9f9f9;">
                        <pre style="white-space: pre-wrap; margin: 0; font-family: Arial;">${acta.descripcion || acta.descripcion_hechos || 'Sin descripción'}</pre>
                    </div>
                </div>
                
                <div style="margin-top: 50px; text-align: center;">
                    <p>_________________________</p>
                    <p>Firma del Inspector</p>
                </div>
            </div>
        `;

        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Imprimir Acta - ${acta.numero_acta}</title>
                    <style>
                        body { margin: 0; padding: 0; }
                        @media print {
                            body { margin: 0; }
                        }
                    </style>
                </head>
                <body>
                    ${printContent}
                </body>
            </html>
        `);
        printWindow.document.close();
        
        setTimeout(() => {
            printWindow.print();
        }, 500);

        mostrarExitoActas('Impresión iniciada');

    } catch (error) {
        if (error.text) {
            mostrarErrorActas('Respuesta inesperada del servidor');
        } else {
            mostrarErrorActas('Error al generar impresión: ' + error.message);
        }
    }
}

async function eliminarActa(actaId, numeroActa) {
    if (!confirm(`¿Estás seguro de que quieres eliminar el acta "${numeroActa}"?\n\nEsta acción no se puede deshacer.`)) {
        return;
    }

    try {
        const response = await fetch(`/api/actas/${actaId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            mostrarExitoActas('Acta eliminada correctamente');
            cargarActasDesdeAPI(); // Recargar la lista
        } else {
            mostrarErrorActas('Error al eliminar acta: ' + (data.message || 'Error desconocido'));
        }
    } catch (error) {
        mostrarErrorActas('Error de conexión: ' + error.message);
    }
}

// ================================
// FUNCIONES DE FILTRADO - ACTAS
// ================================

function filtrarActas() {
    const search = document.getElementById('searchActas').value.toLowerCase();
    const estadoFilter = document.getElementById('filterEstado').value;
    const fechaDesde = document.getElementById('filterFechaDesde').value;
    const fechaHasta = document.getElementById('filterFechaHasta').value;

    let actasFiltradas = todasLasActas.filter(acta => {
        const matchSearch = !search || 
            (acta.numero_acta && acta.numero_acta.toLowerCase().includes(search)) ||
            (acta.placa && acta.placa.toLowerCase().includes(search)) ||
            (acta.placa_vehiculo && acta.placa_vehiculo.toLowerCase().includes(search)) ||
            (acta.conductor_nombre && acta.conductor_nombre.toLowerCase().includes(search)) ||
            (acta.nombre_conductor && acta.nombre_conductor.toLowerCase().includes(search)) ||
            (acta.ruc_dni && acta.ruc_dni.includes(search));
        
        const matchEstado = !estadoFilter || acta.estado === estadoFilter;
        
        let matchFecha = true;
        if (fechaDesde || fechaHasta) {
            const actaFecha = new Date(acta.fecha_acta || acta.created_at);
            if (fechaDesde) {
                matchFecha = matchFecha && actaFecha >= new Date(fechaDesde);
            }
            if (fechaHasta) {
                matchFecha = matchFecha && actaFecha <= new Date(fechaHasta + 'T23:59:59');
            }
        }

        return matchSearch && matchEstado && matchFecha;
    });

    mostrarActas(actasFiltradas);
}

function limpiarFiltrosActas() {
    document.getElementById('searchActas').value = '';
    document.getElementById('filterEstado').value = '';
    document.getElementById('filterFechaDesde').value = '';
    document.getElementById('filterFechaHasta').value = '';
    mostrarActas(todasLasActas);
}

// ================================
// FUNCIONES AUXILIARES - ACTAS
// ================================

function getEstadoBadgeClass(estado) {
    switch (estado) {
        case 'pendiente': return 'bg-warning';
        case 'pagada': return 'bg-success';
        case 'anulada': return 'bg-danger';
        default: return 'bg-secondary';
    }
}

function getEstadoDisplayName(estado) {
    switch (estado) {
        case 'pendiente': return 'Pendiente';
        case 'pagada': return 'Pagada';
        case 'anulada': return 'Anulada';
        default: return estado || 'N/A';
    }
}

function mostrarExitoActas(mensaje) {
    mostrarNotificacion(mensaje, 'success');
}

function mostrarErrorActas(mensaje) {
    mostrarNotificacion(mensaje, 'error');
}

function mostrarNotificacion(mensaje, tipo = 'info') {
    // Crear contenedor de notificaciones si no existe
    let container = document.getElementById('notificaciones-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'notificaciones-container';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
        `;
        document.body.appendChild(container);
    }
    
    // Crear notificación
    const notificacion = document.createElement('div');
    const iconos = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-triangle', 
        warning: 'fas fa-exclamation-circle',
        info: 'fas fa-info-circle'
    };
    
    const colores = {
        success: 'alert-success',
        error: 'alert-danger',
        warning: 'alert-warning', 
        info: 'alert-info'
    };
    
    notificacion.className = `alert ${colores[tipo]} alert-dismissible fade show shadow-sm`;
    notificacion.innerHTML = `
        <i class="${iconos[tipo]} me-2"></i>
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Agregar al contenedor
    container.appendChild(notificacion);
    
    // Auto-remover después de 5 segundos
    setTimeout(() => {
        if (notificacion.parentNode) {
            notificacion.remove();
        }
    }, 5000);
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// ================================
// EXPORTAR FUNCIONES GLOBALMENTE
// ================================

// Hacer funciones disponibles globalmente
window.loadActas = loadActas;
window.loadGestionActas = loadGestionActas;
window.loadCrearActa = loadCrearActa;
window.loadMisActas = loadMisActas;
window.cargarActasDesdeAPI = cargarActasDesdeAPI;
window.cargarMisActasDesdeAPI = cargarMisActasDesdeAPI;
window.showCrearActaModal = showCrearActaModal;
window.guardarNuevaActa = guardarNuevaActa;
window.verActa = verActa;
window.editarActa = editarActa;
window.eliminarActa = eliminarActa;
window.imprimirActa = imprimirActa;
window.filtrarActas = filtrarActas;
window.limpiarFiltros = limpiarFiltros;

console.log('✅ Fiscalizador Actas JS cargado correctamente');
