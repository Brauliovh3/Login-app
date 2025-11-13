/**
 * SISTEMA SIMPLE DE GESTIÓN DE ACTAS - FISCALIZADOR
 * Versión simplificada para garantizar funcionamiento
 */

console.log('🔄 Cargando sistema simple de actas...');

// Función principal para gestión de actas
function loadActas(event) {
    console.log('📋 Cargando gestión de actas...');
    
    const contentContainer = document.getElementById('contentContainer');
    if (!contentContainer) {
        console.error('❌ ContentContainer no encontrado');
        alert('Error: Contenedor principal no encontrado');
        return;
    }
    
    // Obtener sección específica
    let section = 'actas-contra'; // default
    if (event && event.target) {
        const clickedElement = event.target.closest('a');
        if (clickedElement) {
            section = clickedElement.getAttribute('data-section') || 'actas-contra';
        }
    }
    
    console.log('🎯 Cargando sección:', section);
    
    // Limpiar contenido previo
    contentContainer.innerHTML = '';
    
    // Crear interfaz según la sección
    switch(section) {
        case 'crear-acta':
            mostrarFormularioCrearActa();
            break;
        case 'mis-actas':
            mostrarMisActas();
            break;
        case 'actas-contra':
        default:
            mostrarGestionActas();
            break;
    }
}

function mostrarGestionActas() {
    console.log('📊 Mostrando gestión de actas...');
    
    const contentContainer = document.getElementById('contentContainer');
    contentContainer.innerHTML = `
        <div class="content-section active">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="text-primary">
                            <i class="fas fa-file-alt"></i> Gestión de Actas
                        </h2>
                        <div class="btn-group">
                            <button class="btn btn-primary" onclick="abrirModalCrearActa()">
                                <i class="fas fa-plus"></i> Nueva Acta
                            </button>
                            <button class="btn btn-outline-secondary" onclick="cargarListaActas()">
                                <i class="fas fa-refresh"></i> Actualizar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-filter"></i> Filtros de Búsqueda
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Buscar:</label>
                            <input type="text" class="form-control" id="buscarActa" 
                                   placeholder="Número, placa, conductor..." 
                                   onkeyup="filtrarTablaActas()">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Estado:</label>
                            <select class="form-select" id="filtroEstado" onchange="filtrarTablaActas()">
                                <option value="">Todos</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="pagada">Pagada</option>
                                <option value="anulada">Anulada</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Fecha Desde:</label>
                            <input type="date" class="form-control" id="fechaDesde" onchange="filtrarTablaActas()">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Fecha Hasta:</label>
                            <input type="date" class="form-control" id="fechaHasta" onchange="filtrarTablaActas()">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button class="btn btn-outline-secondary" onclick="limpiarFiltrosActas()">
                                    <i class="fas fa-times"></i> Limpiar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Estadísticas rápidas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body text-center">
                            <h4 id="countPendientes">0</h4>
                            <p class="mb-0">Pendientes</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body text-center">
                            <h4 id="countPagadas">0</h4>
                            <p class="mb-0">Pagadas</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-danger">
                        <div class="card-body text-center">
                            <h4 id="countCobranza">0</h4>
                            <p class="mb-0">En Cobranza</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-secondary">
                        <div class="card-body text-center">
                            <h4 id="countAnuladas">0</h4>
                            <p class="mb-0">Anuladas</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabla de actas -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-list"></i> Lista de Actas de Fiscalización
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tablaActas">
                            <thead class="table-dark">
                                <tr>
                                    <th>N° Acta</th>
                                    <th>Fecha</th>
                                    <th>Placa</th>
                                    <th>Conductor</th>
                                    <th>Estado</th>
                                    <th>Monto</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaActasBody">
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                        <p class="mt-2 text-muted">Cargando actas...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Cargar datos automáticamente
    setTimeout(() => {
        cargarListaActas();
    }, 500);
}

function mostrarFormularioCrearActa() {
    console.log('📝 Mostrando formulario crear acta...');
    
    const contentContainer = document.getElementById('contentContainer');
    contentContainer.innerHTML = `
        <div class="content-section active">
            <div class="row mb-4">
                <div class="col-12">
                    <h2 class="text-success">
                        <i class="fas fa-plus-circle"></i> Crear Nueva Acta
                    </h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="#" onclick="loadActas()" class="text-decoration-none">Gestión de Actas</a>
                            </li>
                            <li class="breadcrumb-item active">Nueva Acta</li>
                        </ol>
                    </nav>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-file-plus"></i> Formulario de Nueva Acta
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Complete todos los campos obligatorios marcados con (*) para registrar la nueva acta.
                    </div>
                    
                    <button class="btn btn-primary btn-lg" onclick="abrirModalCrearActa()">
                        <i class="fas fa-plus-circle"></i> Abrir Formulario Completo
                    </button>
                </div>
            </div>
        </div>
    `;
}

function mostrarMisActas() {
    console.log('👤 Mostrando mis actas...');
    
    mostrarGestionActas();
    
    // Cambiar título para indicar que son mis actas
    setTimeout(() => {
        const titulo = document.querySelector('.content-section h2');
        if (titulo) {
            titulo.innerHTML = '<i class="fas fa-user-edit"></i> Mis Actas';
        }
        cargarMisActasEspecificas();
    }, 100);
}

async function cargarListaActas() {
    console.log('📡 Cargando lista de actas desde API...');
    
    const tbody = document.getElementById('tablaActasBody');
    if (!tbody) {
        console.error('❌ Tabla no encontrada');
        return;
    }
    
    // Mostrar loading
    tbody.innerHTML = `
        <tr>
            <td colspan="7" class="text-center py-4">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2 text-muted">Cargando actas...</p>
            </td>
        </tr>
    `;
    
    try {
        const baseUrl = window.location.origin + window.location.pathname;
        const apiUrl = `${baseUrl}?api=actas`;
        console.log('🌐 URL API:', apiUrl);
        
        const response = await fetch(apiUrl, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        
        console.log('📡 Response status:', response.status);
        console.log('📡 Response headers:', response.headers);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('❌ Error response:', errorText);
            throw new Error(`HTTP ${response.status}: ${response.statusText}\n${errorText}`);
        }
        
        const data = await response.json();
        console.log('📊 Datos recibidos:', data);
        
        if (data.success && data.actas) {
            mostrarActasEnTabla(data.actas);
            actualizarEstadisticas(data.actas);
        } else if (data.actas) {
            // Formato directo sin wrapper success
            mostrarActasEnTabla(data.actas);
            actualizarEstadisticas(data.actas);
        } else if (Array.isArray(data)) {
            // Respuesta directa como array
            mostrarActasEnTabla(data);
            actualizarEstadisticas(data);
        } else {
            console.warn('⚠️ Formato de respuesta inesperado:', data);
            mostrarErrorEnTabla('Formato de respuesta inesperado. Revisa la consola para más detalles.');
        }
        
    } catch (error) {
        console.error('❌ Error completo al cargar actas:', error);
        mostrarErrorEnTabla(`Error al cargar actas: ${error.message}`);
        
        // Mostrar información adicional de depuración
        console.group('🔍 Información de depuración');
        console.log('Current URL:', window.location.href);
        console.log('Origin:', window.location.origin);
        console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]')?.content);
        console.groupEnd();
    }
}

function mostrarActasEnTabla(actas) {
    const tbody = document.getElementById('tablaActasBody');
    
    if (!actas || actas.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4">
                    <i class="fas fa-inbox text-muted" style="font-size: 3rem;"></i>
                    <p class="mt-2 text-muted">No hay actas registradas</p>
                    <button class="btn btn-primary" onclick="abrirModalCrearActa()">
                        <i class="fas fa-plus"></i> Crear Primera Acta
                    </button>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = actas.map(acta => {
        // Construir nombre completo del conductor
        let nombreConductor = 'N/A';
        if (acta.nombres_conductor || acta.apellidos_conductor) {
            nombreConductor = `${acta.nombres_conductor || ''} ${acta.apellidos_conductor || ''}`.trim();
        } else if (acta.nombre_conductor) {
            nombreConductor = acta.nombre_conductor;
        } else if (acta.conductor_nombre) {
            nombreConductor = acta.conductor_nombre;
        }
        
        return `
        <tr>
            <td><strong>${acta.numero_acta || 'N/A'}</strong></td>
            <td><small>${formatearFecha(acta.fecha_intervencion || acta.created_at)}</small></td>
            <td><span class="badge bg-dark">${acta.placa || acta.placa_vehiculo || 'N/A'}</span></td>
            <td>${nombreConductor}</td>
            <td><span class="badge ${getBadgeColor(acta.estado)}">${getEstadoTexto(acta.estado)}</span></td>
            <td><strong>S/ ${parseFloat(acta.monto_multa || 0).toFixed(2)}</strong></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" onclick="verDetalleActa(${acta.id})" title="Ver">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-success" onclick="editarActaSimple(${acta.id})" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-info" onclick="imprimirActaSimple(${acta.id})" title="Imprimir">
                        <i class="fas fa-print"></i>
                    </button>
                    <button class="btn btn-outline-danger" onclick="exportarActaPDF(${acta.id})" title="Exportar PDF">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                    <button class="btn btn-outline-danger" onclick="eliminarActaSimple(${acta.id}, '${acta.numero_acta}')" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
        `;
    }).join('');
}

function mostrarErrorEnTabla(mensaje) {
    const tbody = document.getElementById('tablaActasBody');
    tbody.innerHTML = `
        <tr>
            <td colspan="7" class="text-center py-4 text-danger">
                <i class="fas fa-exclamation-triangle" style="font-size: 3rem;"></i>
                <p class="mt-2">${mensaje}</p>
                <button class="btn btn-outline-primary" onclick="cargarListaActas()">
                    <i class="fas fa-refresh"></i> Reintentar
                </button>
            </td>
        </tr>
    `;
}

function actualizarEstadisticas(actas) {
    const pendientes = actas.filter(a => a.estado === 'pendiente').length;
    const pagadas = actas.filter(a => a.estado === 'pagada').length;
    const anuladas = actas.filter(a => a.estado === 'anulada').length;
    const cobranza = actas.filter(a => a.estado === 'en_cobranza' || a.estado === 'cobranza').length;
    
    document.getElementById('countPendientes').textContent = pendientes;
    document.getElementById('countPagadas').textContent = pagadas;
    document.getElementById('countAnuladas').textContent = anuladas;
    document.getElementById('countCobranza').textContent = cobranza;
}

function getBadgeColor(estado) {
    switch(estado) {
        case 'pendiente': return 'bg-warning text-dark';
        case 'pagada': return 'bg-success';
        case 'anulada': return 'bg-danger';
        case 'en_cobranza':
        case 'cobranza': return 'bg-info';
        default: return 'bg-secondary';
    }
}

function formatearFecha(fecha) {
    if (!fecha) return 'N/A';
    try {
        return new Date(fecha).toLocaleDateString('es-ES');
    } catch {
        return fecha;
    }
}

// Funciones de acción (implementación real con API)
function abrirModalCrearActa() {
    console.log('📝 Abriendo modal crear acta...');
    // Crear modal simple usando Bootstrap
    const modalHTML = `
        <div class="modal fade" id="modalCrearActa" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-plus-circle"></i> Nueva Acta de Fiscalización
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formNuevaActa">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Número de Acta *</label>
                                    <input type="text" class="form-control" name="numero_acta" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Fecha de Intervención *</label>
                                    <input type="date" class="form-control" name="fecha_intervencion" required value="${new Date().toISOString().split('T')[0]}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Placa del Vehículo *</label>
                                    <input type="text" class="form-control" name="placa" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Conductor</label>
                                    <input type="text" class="form-control" name="nombre_conductor">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Descripción de Hechos *</label>
                                    <textarea class="form-control" name="descripcion_hechos" rows="3" required></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Monto de Multa</label>
                                    <input type="number" class="form-control" name="monto_multa" step="0.01">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Estado</label>
                                    <select class="form-select" name="estado">
                                        <option value="pendiente">Pendiente</option>
                                        <option value="pagada">Pagada</option>
                                        <option value="anulada">Anulada</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="guardarNuevaActa()">
                            <i class="fas fa-save"></i> Guardar Acta
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remover modal existente si existe
    document.getElementById('modalCrearActa')?.remove();
    
    // Agregar modal al DOM
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('modalCrearActa'));
    modal.show();
}

async function guardarNuevaActa() {
    console.log('💾 Guardando nueva acta...');
    
    const form = document.getElementById('formNuevaActa');
    const formData = new FormData(form);
    
    try {
        const response = await fetch(`${window.location.origin}${window.location.pathname}?api=save-acta`, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('✅ Acta guardada correctamente');
            bootstrap.Modal.getInstance(document.getElementById('modalCrearActa')).hide();
            cargarListaActas();
        } else {
            alert('❌ Error al guardar: ' + (result.message || 'Error desconocido'));
        }
    } catch (error) {
        console.error('Error al guardar acta:', error);
        alert('❌ Error al guardar la acta: ' + error.message);
    }
}

async function verDetalleActa(id) {
    console.log(`🔍 Cargando detalles del acta ID: ${id}`);
    
    try {
        const response = await fetch(`${window.location.origin}${window.location.pathname}?api=acta-details&id=${id}`);
        const result = await response.json();
        
        if (result.success && result.acta) {
            mostrarModalDetalleActa(result.acta);
        } else {
            alert('❌ Error al cargar detalles: ' + (result.message || 'Acta no encontrada'));
        }
    } catch (error) {
        console.error('Error al cargar detalles:', error);
        alert('❌ Error al cargar detalles: ' + error.message);
    }
}

function mostrarModalDetalleActa(acta) {
    const modalHTML = `
        <div class="modal fade" id="modalDetalleActa" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-eye"></i> Detalles del Acta ${acta.numero_acta}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <strong>Número de Acta:</strong><br>
                                ${acta.numero_acta || 'N/A'}
                            </div>
                            <div class="col-md-6">
                                <strong>Fecha:</strong><br>
                                ${formatearFecha(acta.fecha_intervencion || acta.created_at)}
                            </div>
                            <div class="col-md-6">
                                <strong>Placa:</strong><br>
                                <span class="badge bg-dark fs-6">${acta.placa || 'N/A'}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Estado:</strong><br>
                                <span class="badge ${getBadgeColor(acta.estado)} fs-6">${acta.estado || 'Pendiente'}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Conductor:</strong><br>
                                ${acta.nombre_conductor || acta.conductor_nombres + ' ' + acta.conductor_apellidos || 'N/A'}
                            </div>
                            <div class="col-md-6">
                                <strong>Monto:</strong><br>
                                <strong class="text-primary">S/ ${parseFloat(acta.monto_multa || 0).toFixed(2)}</strong>
                            </div>
                            <div class="col-12">
                                <strong>Descripción de Hechos:</strong><br>
                                <div class="bg-light p-3 rounded">${acta.descripcion_hechos || 'Sin descripción'}</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-primary" onclick="editarActaSimple(${acta.id})">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button type="button" class="btn btn-info" onclick="imprimirActaSimple(${acta.id})">
                            <i class="fas fa-print"></i> Imprimir
                        </button>
                        <button type="button" class="btn btn-danger" onclick="exportarActaPDF(${acta.id})">
                            <i class="fas fa-file-pdf"></i> Exportar PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remover modal existente si existe
    document.getElementById('modalDetalleActa')?.remove();
    
    // Agregar modal al DOM
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('modalDetalleActa'));
    modal.show();
}

function editarActaSimple(id) {
    alert(`✏️ Editar acta ID: ${id}\n\nFuncionalidad en desarrollo.`);
}



async function exportarActaPDF(id) {
    console.log('📄 Exportando PDF para acta ID:', id);
    try {
        const response = await fetch(`${window.location.origin}${window.location.pathname}?api=acta-details&id=${id}`);
        const result = await response.json();
        
        if (!result.success || !result.acta) return;
        
        const acta = result.acta;
        const aniActual = new Date().getFullYear();
        
        // Convertir imágenes a base64
        const escudoBase64 = await imagenABase64('images/escudo_peru.png');
        const logoBase64 = await imagenABase64('images/logo.png');
        
        const printContent = `
            <div style="padding: 15px; font-family: Arial, sans-serif; font-size: 9pt; max-width: 800px; margin: 0 auto;">
                <table style="width: 100%; margin-bottom: 10px; border-collapse: collapse;">
                    <tr>
                        <td style="width: 15%; text-align: left; vertical-align: top;">
                            <img src="${escudoBase64}" style="width: 60px; height: auto;" />
                        </td>
                        <td style="width: 70%; text-align: center; vertical-align: middle;">
                            <div style="font-size: 7pt; line-height: 1.2;">
                                <strong>PERÚ</strong><br>
                                <strong>GOBIERNO REGIONAL</strong><br>
                                <strong>DE APURÍMAC</strong><br>
                                <strong>DIRECCIÓN REGIONAL DE</strong><br>
                                <strong>TRANSPORTES Y COMUNICACIONES</strong><br>
                                <strong>DIRECCIÓN DE CIRCULACIÓN</strong><br>
                                <strong>TERRESTRE Y SEGURIDAD VIAL</strong>
                            </div>
                        </td>
                        <td style="width: 15%; text-align: right; vertical-align: top;">
                            <img src="${logoBase64}" style="width: 60px; height: auto;" />
                        </td>
                    </tr>
                </table>
                <div style="text-align: center; margin: 10px 0;">
                    <h3 style="margin: 5px 0; font-size: 11pt;">ACTA DE CONTROL N° ${acta.numero_acta || '000000'} -${aniActual}</h3>
                    <p style="margin: 3px 0; font-size: 9pt;"><strong>D.S. N° 017-2009-MTC</strong></p>
                    <p style="margin: 3px 0; font-size: 8pt;">Código de infracciones y/o incumplimiento<br>Tipo infractor</p>
                </div>
                <p style="font-size: 7pt; text-align: justify; margin: 10px 0;">
                    Quienes suscriben la presente acta nos identificamos como Inspectores acreditados de la DRTC AP, informamos el objeto y el 
                    contenido de la acción de fiscalización, cumpliendo de acuerdo a lo señalado en la normativa vigente:
                </p>
                <table style="width: 100%; border-collapse: collapse; font-size: 8pt; margin-bottom: 10px;">
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px; width: 25%;"><strong>Agente Infractor:</strong></td>
                        <td style="border: 1px solid #000; padding: 3px; width: 25%;">☐ Transportista</td>
                        <td style="border: 1px solid #000; padding: 3px; width: 25%;">☐ Operador de Ruta</td>
                        <td style="border: 1px solid #000; padding: 3px; width: 25%;">☑ Conductor</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Placa:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.placa || acta.placa_vehiculo || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Razón Social/Nombre:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.razon_social || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>RUC /DNI:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.ruc_dni || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Fecha y Hora Inicio:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.fecha_intervencion || ''} ${acta.hora_intervencion || ''}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Fecha y Hora de fin:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Nombre de Conductor:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.nombre_conductor || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>N° Licencia DNI del conductor:</strong></td>
                        <td style="border: 1px solid #000; padding: 3px;">N°: ${acta.licencia_conductor || acta.licencia || 'N/A'}</td>
                        <td colspan="2" style="border: 1px solid #000; padding: 3px;">Clase y Categoría:</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Dirección:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>N° Km. De la red Vial Nacional Prov. /Dpto.</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.lugar_intervencion || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Origen del viaje (Depto./Prov./Distrito)</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Destino Viaje: (Depto./Prov./Distrito)</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Tipo de Servicio que presta:</strong></td>
                        <td style="border: 1px solid #000; padding: 3px;">☐ Personas</td>
                        <td style="border: 1px solid #000; padding: 3px;">☐ mercancía</td>
                        <td style="border: 1px solid #000; padding: 3px;">☐ mixto</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Inspector:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.inspector_responsable || 'N/A'}</td>
                    </tr>
                </table>
                <div style="border: 1px solid #000; padding: 5px; margin-bottom: 10px;">
                    <p style="margin: 0; font-size: 8pt;"><strong>Descripción de los hechos:</strong></p>
                    <p style="margin: 5px 0; font-size: 8pt; min-height: 60px;">${acta.descripcion_infraccion || acta.descripcion_hechos || ''}</p>
                </div>
                <table style="width: 100%; border-collapse: collapse; font-size: 8pt; margin-bottom: 10px;">
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px; width: 50%;"><strong>Medios probatorios:</strong></td>
                        <td style="border: 1px solid #000; padding: 3px; width: 50%;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Calificación de la Infracción:</strong></td>
                        <td style="border: 1px solid #000; padding: 3px;">${acta.codigo_infraccion || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Medida(s) Administrativa(s):</strong></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Sanción:</strong></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Observaciones del intervenido:</strong></td>
                        <td style="border: 1px solid #000; padding: 3px; min-height: 40px;"></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border: 1px solid #000; padding: 3px; min-height: 40px;"><strong>Observaciones del inspector:</strong></td>
                    </tr>
                </table>
                <p style="font-size: 6pt; text-align: justify; margin: 10px 0;">
                    La medida administrativa impuesta deberá ser cumplida estrictamente, bajo apercibimiento expreso de ser denunciado 
                    penalmente por desobediencia o resistencia a la autoridad, ante su incumplimiento.
                </p>
                <table style="width: 100%; margin-top: 20px; font-size: 8pt;">
                    <tr>
                        <td style="width: 33%; text-align: center; vertical-align: bottom;">
                            <div style="border-top: 1px solid #000; padding-top: 3px; margin: 0 10px;">
                                <p style="margin: 2px 0;"><strong>Firma del Intervenido</strong></p>
                                <p style="margin: 2px 0;">Nom Ap.:</p>
                                <p style="margin: 2px 0;">DNI:</p>
                            </div>
                        </td>
                        <td style="width: 33%; text-align: center; vertical-align: bottom;">
                            <div style="border-top: 1px solid #000; padding-top: 3px; margin: 0 10px;">
                                <p style="margin: 2px 0;"><strong>Firma del Representante PNP</strong></p>
                                <p style="margin: 2px 0;">Nom Ap.:</p>
                                <p style="margin: 2px 0;">CIP:</p>
                            </div>
                        </td>
                        <td style="width: 33%; text-align: center; vertical-align: bottom;">
                            <div style="border-top: 1px solid #000; padding-top: 3px; margin: 0 10px;">
                                <p style="margin: 2px 0;"><strong>Firma del Inspector</strong></p>
                                <p style="margin: 2px 0;">Nombre Ap.:</p>
                                <p style="margin: 2px 0;">DNI:</p>
                            </div>
                        </td>
                    </tr>
                </table>
                <p style="font-size: 6pt; text-align: justify; margin-top: 15px;">
                    De conceder la presentación de algún descargo puede realizarlo en la sede de la DRTC. As. (h) Para lo cual dispone de cinco (5) días 
                    hábiles, a partir de la imposición del presente informe de control o del certificado de presente documento de acuerdo a lo dispuesto en el Reglamento del Procedimiento 
                    Administrativo Sancionador Especial de la Dirección General Caminos y Servicios de Transporte y tránsito terrestre, y sus servicios complementarios, 
                    aprobado mediante Decreto Supremo N° 009-2004 MTC, tal como si de acuerdo a la Ley N° 27867 Ley Orgánica de Gobiernos Regionales y su Reglamento de Organización y Funciones, aprobado mediante
                    Ordenanza Regional N°...
                </p>
            </div>
        `;

        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Acta ${acta.numero_acta}</title>
                    <style>
                        body { margin: 0; padding: 20px; font-family: Arial, sans-serif; }
                        @media print { body { margin: 0; padding: 10px; } @page { size: A4; margin: 1cm; } }
                    </style>
                </head>
                <body>
                    ${printContent}
                    <script>window.onload = function() { setTimeout(function() { window.print(); }, 250); };</script>
                </body>
            </html>
        `);
        printWindow.document.close();
        
    } catch (error) {
        console.error('Error al generar PDF:', error);
        alert('Error al generar PDF: ' + error.message);
    }
}

function imagenABase64(url) {
    return new Promise((resolve) => {
        const img = new Image();
        img.crossOrigin = 'Anonymous';
        img.onload = function() {
            const canvas = document.createElement('canvas');
            canvas.width = img.width;
            canvas.height = img.height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0);
            resolve(canvas.toDataURL('image/png'));
        };
        img.onerror = () => resolve(url);
        img.src = url;
    });
}

function imprimirActaSimple(id) {
    try {
        fetch(`${window.location.origin}${window.location.pathname}?api=acta-details&id=${id}`)
            .then(r => r.json())
            .then(result => {
                if (!result.success || !result.acta) return;
                const acta = result.acta;
                const aniActual = new Date().getFullYear();
                const printContent = `
            <div style="padding: 15px; font-family: Arial, sans-serif; font-size: 9pt; max-width: 800px; margin: 0 auto;">
                <table style="width: 100%; margin-bottom: 10px; border-collapse: collapse;">
                    <tr>
                        <td style="width: 15%; text-align: left; vertical-align: top;">
                            <img src="images/escudo_peru.png" style="width: 60px; height: auto;" />
                        </td>
                        <td style="width: 70%; text-align: center; vertical-align: middle;">
                            <div style="font-size: 7pt; line-height: 1.2;">
                                <strong>PERÚ</strong><br>
                                <strong>GOBIERNO REGIONAL</strong><br>
                                <strong>DE APURÍMAC</strong><br>
                                <strong>DIRECCIÓN REGIONAL DE</strong><br>
                                <strong>TRANSPORTES Y COMUNICACIONES</strong><br>
                                <strong>DIRECCIÓN DE CIRCULACIÓN</strong><br>
                                <strong>TERRESTRE Y SEGURIDAD VIAL</strong>
                            </div>
                        </td>
                        <td style="width: 15%; text-align: right; vertical-align: top;">
                            <img src="images/logo.png" style="width: 60px; height: auto;" />
                        </td>
                    </tr>
                </table>
                <div style="text-align: center; margin: 10px 0;">
                    <h3 style="margin: 5px 0; font-size: 11pt;">ACTA DE CONTROL N° ${acta.numero_acta || '000000'} -${aniActual}</h3>
                    <p style="margin: 3px 0; font-size: 9pt;"><strong>D.S. N° 017-2009-MTC</strong></p>
                    <p style="margin: 3px 0; font-size: 8pt;">Código de infracciones y/o incumplimiento<br>Tipo infractor</p>
                </div>
                <p style="font-size: 7pt; text-align: justify; margin: 10px 0;">
                    Quienes suscriben la presente acta nos identificamos como Inspectores acreditados de la DRTC AP, informamos el objeto y el 
                    contenido de la acción de fiscalización, cumpliendo de acuerdo a lo señalado en la normativa vigente:
                </p>
                <table style="width: 100%; border-collapse: collapse; font-size: 8pt; margin-bottom: 10px;">
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px; width: 25%;"><strong>Agente Infractor:</strong></td>
                        <td style="border: 1px solid #000; padding: 3px; width: 25%;">☐ Transportista</td>
                        <td style="border: 1px solid #000; padding: 3px; width: 25%;">☐ Operador de Ruta</td>
                        <td style="border: 1px solid #000; padding: 3px; width: 25%;">☑ Conductor</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Placa:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.placa || acta.placa_vehiculo || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Razón Social/Nombre:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.razon_social || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>RUC /DNI:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.ruc_dni || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Fecha y Hora Inicio:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.fecha_intervencion || ''} ${acta.hora_intervencion || ''}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Fecha y Hora de fin:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Nombre de Conductor:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.nombre_conductor || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>N° Licencia DNI del conductor:</strong></td>
                        <td style="border: 1px solid #000; padding: 3px;">N°: ${acta.licencia_conductor || acta.licencia || 'N/A'}</td>
                        <td colspan="2" style="border: 1px solid #000; padding: 3px;">Clase y Categoría:</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Dirección:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>N° Km. De la red Vial Nacional Prov. /Dpto.</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.lugar_intervencion || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Origen del viaje (Depto./Prov./Distrito)</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Destino Viaje: (Depto./Prov./Distrito)</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Tipo de Servicio que presta:</strong></td>
                        <td style="border: 1px solid #000; padding: 3px;">☐ Personas</td>
                        <td style="border: 1px solid #000; padding: 3px;">☐ mercancía</td>
                        <td style="border: 1px solid #000; padding: 3px;">☐ mixto</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Inspector:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.inspector_responsable || 'N/A'}</td>
                    </tr>
                </table>
                <div style="border: 1px solid #000; padding: 5px; margin-bottom: 10px;">
                    <p style="margin: 0; font-size: 8pt;"><strong>Descripción de los hechos:</strong></p>
                    <p style="margin: 5px 0; font-size: 8pt; min-height: 60px;">${acta.descripcion_infraccion || acta.descripcion_hechos || ''}</p>
                </div>
                <table style="width: 100%; border-collapse: collapse; font-size: 8pt; margin-bottom: 10px;">
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px; width: 50%;"><strong>Medios probatorios:</strong></td>
                        <td style="border: 1px solid #000; padding: 3px; width: 50%;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Calificación de la Infracción:</strong></td>
                        <td style="border: 1px solid #000; padding: 3px;">${acta.codigo_infraccion || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Medida(s) Administrativa(s):</strong></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Sanción:</strong></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Observaciones del intervenido:</strong></td>
                        <td style="border: 1px solid #000; padding: 3px; min-height: 40px;"></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border: 1px solid #000; padding: 3px; min-height: 40px;"><strong>Observaciones del inspector:</strong></td>
                    </tr>
                </table>
                <p style="font-size: 6pt; text-align: justify; margin: 10px 0;">
                    La medida administrativa impuesta deberá ser cumplida estrictamente, bajo apercibimiento expreso de ser denunciado 
                    penalmente por desobediencia o resistencia a la autoridad, ante su incumplimiento.
                </p>
                <table style="width: 100%; margin-top: 20px; font-size: 8pt;">
                    <tr>
                        <td style="width: 33%; text-align: center; vertical-align: bottom;">
                            <div style="border-top: 1px solid #000; padding-top: 3px; margin: 0 10px;">
                                <p style="margin: 2px 0;"><strong>Firma del Intervenido</strong></p>
                                <p style="margin: 2px 0;">Nom Ap.:</p>
                                <p style="margin: 2px 0;">DNI:</p>
                            </div>
                        </td>
                        <td style="width: 33%; text-align: center; vertical-align: bottom;">
                            <div style="border-top: 1px solid #000; padding-top: 3px; margin: 0 10px;">
                                <p style="margin: 2px 0;"><strong>Firma del Representante PNP</strong></p>
                                <p style="margin: 2px 0;">Nom Ap.:</p>
                                <p style="margin: 2px 0;">CIP:</p>
                            </div>
                        </td>
                        <td style="width: 33%; text-align: center; vertical-align: bottom;">
                            <div style="border-top: 1px solid #000; padding-top: 3px; margin: 0 10px;">
                                <p style="margin: 2px 0;"><strong>Firma del Inspector</strong></p>
                                <p style="margin: 2px 0;">Nombre Ap.:</p>
                                <p style="margin: 2px 0;">DNI:</p>
                            </div>
                        </td>
                    </tr>
                </table>
                <p style="font-size: 6pt; text-align: justify; margin-top: 15px;">
                    De conceder la presentación de algún descargo puede realizarlo en la sede de la DRTC. As. (h) Para lo cual dispone de cinco (5) días 
                    hábiles, a partir de la imposición del presente informe de control o del certificado de presente documento de acuerdo a lo dispuesto en el Reglamento del Procedimiento 
                    Administrativo Sancionador Especial de la Dirección General Caminos y Servicios de Transporte y tránsito terrestre, y sus servicios complementarios, 
                    aprobado mediante Decreto Supremo N° 009-2004 MTC, tal como si de acuerdo a la Ley N° 27867 Ley Orgánica de Gobiernos Regionales y su Reglamento de Organización y Funciones, aprobado mediante
                    Ordenanza Regional N°...
                </p>
            </div>
        `;

        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Acta ${acta.numero_acta}</title>
                    <style>
                        body { margin: 0; padding: 20px; font-family: Arial, sans-serif; }
                        @media print { body { margin: 0; padding: 10px; } @page { size: A4; margin: 1cm; } }
                    </style>
                </head>
                <body>
                    ${printContent}
                    <script>window.onload = function() { setTimeout(function() { window.print(); }, 250); };</script>
                </body>
            </html>
        `);
        printWindow.document.close();
        
    } catch (error) {}
}

function getEstadoTexto(estado) {
    const estados = {
        'pendiente': 'Pendiente',
        'pagada': 'Pagada',
        'anulada': 'Anulada',
        'en_cobranza': 'En Cobranza',
        'cobranza': 'En Cobranza',
        '0': 'Pendiente',
        '1': 'Procesada',
        '2': 'Anulada',
        '3': 'Pagada'
    };
    return estados[estado] || 'Pendiente';
}

async function eliminarActaSimple(id, numero) {
    if (!confirm(`⚠️ ¿Está seguro de eliminar el acta ${numero}?\n\nEsta acción no se puede deshacer.`)) {
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('acta_id', id);
        
        const response = await fetch(`${window.location.origin}${window.location.pathname}?api=delete-acta`, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('✅ Acta eliminada correctamente');
            cargarListaActas();
        } else {
            alert('❌ Error al eliminar: ' + (result.message || 'Error desconocido'));
        }
    } catch (error) {
        console.error('Error al eliminar acta:', error);
        alert('❌ Error al eliminar la acta: ' + error.message);
    }
}

async function cargarMisActasEspecificas() {
    // Similar a cargarListaActas pero filtrado por usuario
    console.log('👤 Cargando solo mis actas...');
    // Por ahora usa la misma función
    cargarListaActas();
}

function filtrarTablaActas() {
    console.log('🔍 Aplicando filtros...');
    // Implementar filtrado local
}

function limpiarFiltrosActas() {
    console.log('🧹 Limpiando filtros...');
    document.getElementById('buscarActa').value = '';
    document.getElementById('filtroEstado').value = '';
    document.getElementById('fechaDesde').value = '';
    document.getElementById('fechaHasta').value = '';
    filtrarTablaActas();
}

// Exportar funciones globalmente
window.loadActas = loadActas;
window.mostrarGestionActas = mostrarGestionActas;
window.cargarListaActas = cargarListaActas;
window.abrirModalCrearActa = abrirModalCrearActa;
window.guardarNuevaActa = guardarNuevaActa;
window.verDetalleActa = verDetalleActa;
window.mostrarModalDetalleActa = mostrarModalDetalleActa;
window.editarActaSimple = editarActaSimple;
window.exportarActaPDF = exportarActaPDF;
window.imprimirActaSimple = imprimirActaSimple;
window.eliminarActaSimple = eliminarActaSimple;
window.filtrarTablaActas = filtrarTablaActas;
window.limpiarFiltrosActas = limpiarFiltrosActas;

console.log('✅ Sistema simple de actas cargado correctamente');