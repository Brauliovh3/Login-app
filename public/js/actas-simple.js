/**
 * SISTEMA SIMPLE DE GESTIÃ“N DE ACTAS - FISCALIZADOR
 * VersiÃ³n simplificada para garantizar funcionamiento
 */

console.log('ðŸ”„ Cargando sistema simple de actas...');

// FunciÃ³n principal para gestiÃ³n de actas
function loadActas(event) {
    console.log('ðŸ“‹ Cargando gestiÃ³n de actas...');
    
    const contentContainer = document.getElementById('contentContainer');
    if (!contentContainer) {
        console.error('âŒ ContentContainer no encontrado');
        alert('Error: Contenedor principal no encontrado');
        return;
    }
    
    // Obtener secciÃ³n especÃ­fica
    let section = 'actas-contra'; // default
    if (event && event.target) {
        const clickedElement = event.target.closest('a');
        if (clickedElement) {
            section = clickedElement.getAttribute('data-section') || 'actas-contra';
        }
    }
    
    console.log('ðŸŽ¯ Cargando secciÃ³n:', section);
    
    // Limpiar contenido previo
    contentContainer.innerHTML = '';
    
    // Crear interfaz segÃºn la secciÃ³n
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
    console.log('ðŸ“Š Mostrando gestiÃ³n de actas...');
    
    const contentContainer = document.getElementById('contentContainer');
    contentContainer.innerHTML = `
        <div class="content-section active">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="text-primary">
                            <i class="fas fa-file-alt"></i> GestiÃ³n de Actas
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
                        <i class="fas fa-filter"></i> Filtros de BÃºsqueda
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Buscar:</label>
                            <input type="text" class="form-control" id="buscarActa" 
                                   placeholder="NÃºmero, placa, conductor..." 
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
            
            <!-- EstadÃ­sticas rÃ¡pidas -->
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
                        <i class="fas fa-list"></i> Lista de Actas de FiscalizaciÃ³n
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tablaActas">
                            <thead class="table-dark">
                                <tr>
                                    <th>NÂ° Acta</th>
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
    
    // Cargar datos automÃ¡ticamente
    setTimeout(() => {
        cargarListaActas();
    }, 500);
}

function mostrarFormularioCrearActa() {
    console.log('ðŸ“ Mostrando formulario crear acta...');
    
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
                                <a href="#" onclick="loadActas()" class="text-decoration-none">GestiÃ³n de Actas</a>
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
    console.log('ðŸ‘¤ Mostrando mis actas...');
    
    mostrarGestionActas();
    
    // Cambiar tÃ­tulo para indicar que son mis actas
    setTimeout(() => {
        const titulo = document.querySelector('.content-section h2');
        if (titulo) {
            titulo.innerHTML = '<i class="fas fa-user-edit"></i> Mis Actas';
        }
        cargarMisActasEspecificas();
    }, 100);
}

async function cargarListaActas() {
    console.log('ðŸ“¡ Cargando lista de actas desde API...');
    
    const tbody = document.getElementById('tablaActasBody');
    if (!tbody) {
        console.error('âŒ Tabla no encontrada');
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
        console.log('ðŸŒ URL API:', apiUrl);
        
        const response = await fetch(apiUrl, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        
        console.log('ðŸ“¡ Response status:', response.status);
        console.log('ðŸ“¡ Response headers:', response.headers);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('âŒ Error response:', errorText);
            throw new Error(`HTTP ${response.status}: ${response.statusText}\n${errorText}`);
        }
        
        const data = await response.json();
        console.log('ðŸ“Š Datos recibidos:', data);
        
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
            console.warn('âš ï¸ Formato de respuesta inesperado:', data);
            mostrarErrorEnTabla('Formato de respuesta inesperado. Revisa la consola para mÃ¡s detalles.');
        }
        
    } catch (error) {
        console.error('âŒ Error completo al cargar actas:', error);
        mostrarErrorEnTabla(`Error al cargar actas: ${error.message}`);
        
        // Mostrar informaciÃ³n adicional de depuraciÃ³n
        console.group('ðŸ” InformaciÃ³n de depuraciÃ³n');
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

// Funciones de acciÃ³n (implementaciÃ³n real con API)
function abrirModalCrearActa() {
    console.log('ðŸ“ Abriendo modal crear acta...');
    // Crear modal simple usando Bootstrap
    const modalHTML = `
        <div class="modal fade" id="modalCrearActa" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-plus-circle"></i> Nueva Acta de FiscalizaciÃ³n
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formNuevaActa">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">NÃºmero de Acta *</label>
                                    <input type="text" class="form-control" name="numero_acta" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Fecha de IntervenciÃ³n *</label>
                                    <input type="date" class="form-control" name="fecha_intervencion" required value="${new Date().toISOString().split('T')[0]}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Placa del VehÃ­culo *</label>
                                    <input type="text" class="form-control" name="placa" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Conductor</label>
                                    <input type="text" class="form-control" name="nombre_conductor">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">DescripciÃ³n de Hechos *</label>
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
    console.log('ðŸ’¾ Guardando nueva acta...');
    
    const form = document.getElementById('formNuevaActa');
    const formData = new FormData(form);
    
    try {
        const response = await fetch(`${window.location.origin}${window.location.pathname}?api=save-acta`, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('âœ… Acta guardada correctamente');
            bootstrap.Modal.getInstance(document.getElementById('modalCrearActa')).hide();
            cargarListaActas();
        } else {
            alert('âŒ Error al guardar: ' + (result.message || 'Error desconocido'));
        }
    } catch (error) {
        console.error('Error al guardar acta:', error);
        alert('âŒ Error al guardar la acta: ' + error.message);
    }
}

async function verDetalleActa(id) {
    console.log(`ðŸ” Cargando detalles del acta ID: ${id}`);
    
    try {
        const response = await fetch(`${window.location.origin}${window.location.pathname}?api=acta-details&id=${id}`);
        const result = await response.json();
        
        if (result.success && result.acta) {
            mostrarModalDetalleActa(result.acta);
        } else {
            alert('âŒ Error al cargar detalles: ' + (result.message || 'Acta no encontrada'));
        }
    } catch (error) {
        console.error('Error al cargar detalles:', error);
        alert('âŒ Error al cargar detalles: ' + error.message);
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
                                <strong>NÃºmero de Acta:</strong><br>
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
                                <strong>DescripciÃ³n de Hechos:</strong><br>
                                <div class="bg-light p-3 rounded">${acta.descripcion_hechos || 'Sin descripciÃ³n'}</div>
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
    alert(`âœï¸ Editar acta ID: ${id}\n\nFuncionalidad en desarrollo.`);
}



async function exportarActaPDF(id) {
    console.log('ðŸ“„ Exportando PDF para acta ID:', id);
    try {
        const response = await fetch(`${window.location.origin}${window.location.pathname}?api=acta-details&id=${id}`);
        const result = await response.json();
        
        if (!result.success || !result.acta) return;
        
        const acta = result.acta;

        // Método mejorado: usar jsPDF para generar PDF directamente
        console.log('ðŸ“š Generando PDF directamente con jsPDF...');
        await generarPDFDirecto(acta);

    } catch (error) {
        console.error('Error al generar PDF:', error);
        alert('Error al generar PDF: ' + error.message);
    }
}

async function generarPDFConHtml2pdf(acta) {
    try {
        // Verificar que html2pdf esté disponible
        if (typeof html2pdf === 'undefined') {
            throw new Error('html2pdf no está disponible. Recargue la página e intente nuevamente.');
        }
        const aniActual = new Date().getFullYear();

        // Convertir imágenes a base64 (igual que en impresión)
        console.log('📷 Convirtiendo imágenes a base64...');
        let escudoBase64, logoBase64;
        try {
            escudoBase64 = await imagenABase64('images/escudo_peru.png');
            logoBase64 = await imagenABase64('images/logo.png');
            console.log('✅ Imágenes convertidas correctamente');
        } catch (imgError) {
            console.warn('⚠️ Error convirtiendo imágenes, usando URLs directas:', imgError);
            escudoBase64 = `${window.location.origin}/images/escudo_peru.png`;
            logoBase64 = `${window.location.origin}/images/logo.png`;
        }

        // Crear HTML completo con estilos embebidos (igual que la impresiÃ³n)
        const htmlCompleto = `
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>Acta ${acta.numero_acta}</title>
            <style>
                * { box-sizing: border-box; }
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 20px;
                    font-size: 9pt;
                    line-height: 1.3;
                    color: #000;
                }
                @media print {
                    body { margin: 0; padding: 10px; }
                    @page { size: A4; margin: 1cm; }
                }
                .header-table {
                    width: 100%;
                    margin-bottom: 10px;
                    border-collapse: collapse;
                }
                .header-table td {
                    vertical-align: top;
                }
                .logo-left {
                    width: 15%;
                    text-align: left;
                }
                .logo-center {
                    width: 70%;
                    text-align: center;
                }
                .logo-right {
                    width: 15%;
                    text-align: right;
                }
                .logo-center div {
                    font-size: 7pt;
                    line-height: 1.2;
                    font-weight: bold;
                }
                .logo-center strong {
                    display: block;
                }
                .content-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 10px;
                    font-size: 8pt;
                }
                .content-table td {
                    border: 1px solid #000;
                    padding: 3px;
                    vertical-align: top;
                }
                .title-section {
                    text-align: center;
                    margin: 10px 0;
                }
                .title-section h3 {
                    margin: 5px 0;
                    font-size: 11pt;
                }
                .title-section p {
                    margin: 3px 0;
                    font-size: 8pt;
                }
                .description-box {
                    border: 1px solid #000;
                    padding: 5px;
                    margin-bottom: 10px;
                    min-height: 60px;
                    font-size: 8pt;
                }
                .signatures-table {
                    width: 100%;
                    margin-top: 20px;
                    font-size: 8pt;
                }
                .signatures-table td {
                    width: 33%;
                    text-align: center;
                    vertical-align: bottom;
                }
                .signature-line {
                    border-top: 1px solid #000;
                    padding-top: 3px;
                    margin: 0 10px;
                }
                .footer-text {
                    font-size: 6pt;
                    text-align: justify;
                    margin: 10px 0;
                }
                .checkbox-checked {
                    font-size: 12pt;
                    color: #000;
                }
                .checkbox-empty {
                    font-size: 12pt;
                    color: #000;
                }
            </style>
        </head>
        <body>
            <div style="max-width: 800px; margin: 0 auto;">
                <table class="header-table">
                    <tr>
                        <td class="logo-left">
                            <img src="${escudoBase64}" style="width: 60px; height: auto;" />
                        </td>
                        <td class="logo-center">
                            <div>
                                <strong>PERÚ</strong><br>
                                <strong>GOBIERNO REGIONAL</strong><br>
                                <strong>DE APURÍMAC</strong><br>
                                <strong>DIRECCIÓN REGIONAL DE</strong><br>
                                <strong>TRANSPORTES Y COMUNICACIONES</strong><br>
                                <strong>DIRECCIÓN DE CIRCULACIÓN</strong><br>
                                <strong>TERRESTRE Y SEGURIDAD VIAL</strong>
                            </div>
                        </td>
                        <td class="logo-right">
                            <img src="${logoBase64}" style="width: 60px; height: auto;" />
                        </td>
                    </tr>
                </table>

                <div class="title-section">
                    <h3>ACTA DE CONTROL NÂ° ${acta.numero_acta || '000000'} -${aniActual}</h3>
                    <p><strong>D.S. NÂ° 017-2009-MTC</strong></p>
                    <p>CÃ³digo de infracciones y/o incumplimiento<br>Tipo infractor</p>
                </div>

                <p style="font-size: 7pt; text-align: justify; margin: 10px 0;">
                    Quienes suscriben la presente acta nos identificamos como Inspectores acreditados de la DRTC AP, informamos el objeto y el contenido de la acciÃ³n de fiscalizaciÃ³n, cumpliendo de acuerdo a lo seÃ±alado en la normativa vigente:
                </p>

                <table class="content-table">
                    <tr>
                        <td style="width: 25%;"><strong>Agente Infractor:</strong></td>
                        <td style="width: 25%;">â˜ Transportista</td>
                        <td style="width: 25%;">â˜ Operador de Ruta</td>
                        <td style="width: 25%;">â˜‘ Conductor</td>
                    </tr>
                    <tr>
                        <td><strong>Placa:</strong></td>
                        <td colspan="3">${acta.placa || acta.placa_vehiculo || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td><strong>RazÃ³n Social/Nombre:</strong></td>
                        <td colspan="3">${acta.razon_social || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td><strong>RUC /DNI:</strong></td>
                        <td colspan="3">${acta.ruc_dni || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td><strong>Fecha y Hora Inicio:</strong></td>
                        <td colspan="3">${acta.fecha_intervencion || ''} ${acta.hora_intervencion || ''}</td>
                    </tr>
                    <tr>
                        <td><strong>Fecha y Hora de fin:</strong></td>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td><strong>Nombre de Conductor:</strong></td>
                        <td colspan="3">${acta.nombre_conductor || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td><strong>NÂ° Licencia DNI del conductor:</strong></td>
                        <td>NÂ°: ${acta.licencia_conductor || acta.licencia || 'N/A'}</td>
                        <td colspan="2"><strong>Clase y CategorÃ­a:</strong></td>
                    </tr>
                    <tr>
                        <td><strong>DirecciÃ³n:</strong></td>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td><strong>NÂ° Km. De la red Vial Nacional</strong></td>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td><strong>Prov. /Dpto.</strong></td>
                        <td colspan="3">${acta.lugar_intervencion || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td><strong>Origen del viaje</strong></td>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td><strong>(Depto./Prov./Distrito)</strong></td>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td><strong>Destino Viaje:</strong></td>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td><strong>(Depto./Prov./Distrito)</strong></td>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td><strong>Tipo de Servicio que presta:</strong></td>
                        <td>â˜ Personas</td>
                        <td>â˜ mercancÃ­a</td>
                        <td>â˜ mixto</td>
                    </tr>
                    <tr>
                        <td><strong>Inspector:</strong></td>
                        <td colspan="3">${acta.inspector_responsable || 'N/A'}</td>
                    </tr>
                </table>

                <div class="description-box">
                    <p style="margin: 0; font-size: 8pt;"><strong>DescripciÃ³n de los hechos:</strong></p>
                    <p style="margin: 5px 0; font-size: 8pt; min-height: 40px;">${acta.descripcion_infraccion || acta.descripcion_hechos || ''}</p>
                </div>

                <table class="content-table">
                    <tr>
                        <td style="width: 50%;"><strong>Medios probatorios:</strong></td>
                        <td style="width: 50%;"></td>
                    </tr>
                    <tr>
                        <td><strong>CalificaciÃ³n de la InfracciÃ³n:</strong></td>
                        <td>${acta.codigo_infraccion || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td><strong>Medida(s) Administrativa(s):</strong></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td><strong>SanciÃ³n:</strong></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td><strong>Observaciones del intervenido:</strong></td>
                        <td style="min-height: 40px;"></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="min-height: 40px;"><strong>Observaciones del inspector:</strong></td>
                    </tr>
                </table>

                <p class="footer-text">
                    La medida administrativa impuesta deberÃ¡ ser cumplida estrictamente, bajo apercibimiento expreso de ser denunciado penalmente por desobediencia o resistencia a la autoridad, ante su incumplimiento.
                </p>

                <table class="signatures-table">
                    <tr>
                        <td>
                            <div class="signature-line">
                                <p style="margin: 2px 0;"><strong>Firma del Intervenido</strong></p>
                                <p style="margin: 2px 0;">Nom Ap.:</p>
                                <p style="margin: 2px 0;">DNI:</p>
                            </div>
                        </td>
                        <td>
                            <div class="signature-line">
                                <p style="margin: 2px 0;"><strong>Firma del Representante PNP</strong></p>
                                <p style="margin: 2px 0;">Nom Ap.:</p>
                                <p style="margin: 2px 0;">CIP:</p>
                            </div>
                        </td>
                        <td>
                            <div class="signature-line">
                                <p style="margin: 2px 0;"><strong>Firma del Inspector</strong></p>
                                <p style="margin: 2px 0;">Nombre Ap.:</p>
                                <p style="margin: 2px 0;">DNI:</p>
                            </div>
                        </td>
                    </tr>
                </table>

                <p class="footer-text">
                    De conceder la presentaciÃ³n de algÃºn descargo puede realizarlo en la sede de la DRTC. As. (h) Para lo cual dispone de cinco (5) dÃ­as hÃ¡biles, a partir de la imposiciÃ³n del presente informe de control o del certificado de presente documento de acuerdo a lo dispuesto en el Reglamento del Procedimiento Administrativo Sancionador Especial de la DirecciÃ³n General Caminos y Servicios de Transporte y trÃ¡nsito terrestre, y sus servicios complementarios, aprobado mediante Decreto Supremo NÂ° 009-2004 MTC, tal como si de acuerdo a la Ley NÂ° 27867 Ley OrgÃ¡nica de Gobiernos Regionales y su Reglamento de OrganizaciÃ³n y Funciones, aprobado mediante Ordenanza Regional NÂ°...
                </p>
            </div>
        </body>
        </html>
        `;

        // Crear elemento temporal con el HTML completo
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = htmlCompleto;
        tempDiv.style.position = 'absolute';
        tempDiv.style.left = '-9999px';
        tempDiv.style.top = '-9999px';
        tempDiv.style.width = '210mm'; // Ancho A4
        tempDiv.style.fontFamily = 'Arial, sans-serif';
        document.body.appendChild(tempDiv);

        // Configuración simplificada de html2pdf
        const opt = {
            margin: 10,
            filename: `Acta_${acta.numero_acta || '000000'}_${new Date().getFullYear()}.pdf`,
            image: { type: 'jpeg', quality: 0.95 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        console.log('📄 Generando PDF con html2pdf...');
        console.log('📄 Elemento temporal creado con contenido HTML');
        console.log('📄 HTML length:', htmlCompleto.length);
        console.log('📄 HTML preview:', htmlCompleto.substring(0, 500));

        // Verificar que html2pdf esté disponible
        console.log('📄 html2pdf disponible:', typeof html2pdf);

        // Generar PDF con manejo de errores detallado
        try {
            const pdfInstance = html2pdf().set(opt).from(tempDiv);
            console.log('📄 PDF instance created');
            await pdfInstance.save();
            console.log('✅ PDF generado exitosamente');
        } catch (pdfError) {
            console.error('❌ Error específico en html2pdf:', pdfError);
            throw pdfError;
        }

    } catch (error) {
        console.error('âŒ Error generando PDF:', error);
        alert('Error al generar PDF: ' + error.message);
    } finally {
        // Limpiar el elemento temporal
        if (tempDiv && tempDiv.parentNode) {
            document.body.removeChild(tempDiv);
        }
    }
}

async function generarPDFDirecto(acta) {
    try {
        console.log('ðŸ“š Generando PDF directo usando HTML renderizado...');

        // Verificar que jsPDF esté disponible
        if (typeof window.jspdf === 'undefined') {
            throw new Error('jsPDF no está disponible. Recargue la página.');
        }

        // Verificar que html2canvas esté disponible
        if (typeof html2canvas === 'undefined') {
            console.log('ðŸ“š html2canvas no disponible, cargando...');
            await new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
                script.onload = resolve;
                script.onerror = reject;
                document.head.appendChild(script);
            });
        }

        const { jsPDF } = window.jspdf;

        // Crear el HTML completo igual que en la impresión
        const aniActual = new Date().getFullYear();
        const htmlCompleto = `
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <title>Acta ${acta.numero_acta}</title>
                <style>
                    * { box-sizing: border-box; }
                    body {
                        font-family: Arial, sans-serif;
                        margin: 0;
                        padding: 15px;
                        font-size: 9pt;
                        line-height: 1.3;
                        color: #000;
                        background: white;
                    }
                    .header-table {
                        width: 100%;
                        margin-bottom: 10px;
                        border-collapse: collapse;
                    }
                    .header-table td {
                        vertical-align: top;
                        padding: 0;
                    }
                    .logo-left {
                        width: 15%;
                        text-align: left;
                    }
                    .logo-center {
                        width: 70%;
                        text-align: center;
                    }
                    .logo-right {
                        width: 15%;
                        text-align: right;
                    }
                    .logo-center div {
                        font-size: 7pt;
                        line-height: 1.2;
                        font-weight: bold;
                    }
                    .title-section {
                        text-align: center;
                        margin: 10px 0;
                    }
                    .title-section h3 {
                        margin: 5px 0;
                        font-size: 11pt;
                        font-weight: bold;
                    }
                    .title-section p {
                        margin: 3px 0;
                        font-size: 8pt;
                    }
                    .intro-text {
                        font-size: 7pt;
                        text-align: justify;
                        margin: 10px 0;
                    }
                    .content-table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 10px;
                        font-size: 8pt;
                    }
                    .content-table td {
                        border: 1px solid #000;
                        padding: 3px;
                        vertical-align: top;
                    }
                    .description-box {
                        border: 1px solid #000;
                        padding: 5px;
                        margin-bottom: 10px;
                        min-height: 60px;
                        font-size: 8pt;
                    }
                    .footer-text {
                        font-size: 6pt;
                        text-align: justify;
                        margin: 10px 0;
                    }
                    .signatures-table {
                        width: 100%;
                        margin-top: 20px;
                        font-size: 8pt;
                    }
                    .signatures-table td {
                        width: 33%;
                        text-align: center;
                        vertical-align: bottom;
                    }
                    .signature-line {
                        border-top: 1px solid #000;
                        padding-top: 3px;
                        margin: 0 10px;
                    }
                </style>
            </head>
            <body>
                <div style="max-width: 800px; margin: 0 auto;">
                    <table class="header-table">
                        <tr>
                            <td class="logo-left">
                                <img src="images/escudo_peru.png" style="width: 60px; height: auto;" />
                            </td>
                            <td class="logo-center">
                                <div>
                                    <strong>PERÚ</strong><br>
                                    <strong>GOBIERNO REGIONAL</strong><br>
                                    <strong>DE APURÍMAC</strong><br>
                                    <strong>DIRECCIÓN REGIONAL DE</strong><br>
                                    <strong>TRANSPORTES Y COMUNICACIONES</strong><br>
                                    <strong>DIRECCIÓN DE CIRCULACIÓN</strong><br>
                                    <strong>TERRESTRE Y SEGURIDAD VIAL</strong>
                                </div>
                            </td>
                            <td class="logo-right">
                                <img src="images/logo.png" style="width: 60px; height: auto;" />
                            </td>
                        </tr>
                    </table>

                    <div class="title-section">
                        <h3>ACTA DE CONTROL N° ${acta.numero_acta || '000000'} -${aniActual}</h3>
                        <p><strong>D.S. N° 017-2009-MTC</strong></p>
                        <p>Código de infracciones y/o incumplimiento<br>Tipo infractor</p>
                    </div>

                    <p class="intro-text">
                        Quienes suscriben la presente acta nos identificamos como Inspectores acreditados de la DRTC AP, informamos el objeto y el contenido de la acción de fiscalización, cumpliendo de acuerdo a lo señalado en la normativa vigente:
                    </p>

                    <table class="content-table">
                        <tr>
                            <td style="width: 25%;"><strong>Agente Infractor:</strong></td>
                            <td style="width: 25%;">☐ Transportista</td>
                            <td style="width: 25%;">☐ Operador de Ruta</td>
                            <td style="width: 25%;">☑ Conductor</td>
                        </tr>
                        <tr>
                            <td><strong>Placa:</strong></td>
                            <td colspan="3">${acta.placa || acta.placa_vehiculo || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td><strong>Razón Social/Nombre:</strong></td>
                            <td colspan="3">${acta.razon_social || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td><strong>RUC /DNI:</strong></td>
                            <td colspan="3">${acta.ruc_dni || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td><strong>Fecha y Hora Inicio:</strong></td>
                            <td colspan="3">${acta.fecha_intervencion || ''} ${acta.hora_intervencion || ''}</td>
                        </tr>
                        <tr>
                            <td><strong>Fecha y Hora de fin:</strong></td>
                            <td colspan="3"></td>
                        </tr>
                        <tr>
                            <td><strong>Nombre de Conductor:</strong></td>
                            <td colspan="3">${acta.nombre_conductor || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td><strong>N° Licencia DNI del conductor:</strong></td>
                            <td>N°: ${acta.licencia_conductor || acta.licencia || 'N/A'}</td>
                            <td colspan="2"><strong>Clase y Categoría:</strong></td>
                        </tr>
                        <tr>
                            <td><strong>Dirección:</strong></td>
                            <td colspan="3"></td>
                        </tr>
                        <tr>
                            <td><strong>N° Km. De la red Vial Nacional</strong></td>
                            <td colspan="3"></td>
                        </tr>
                        <tr>
                            <td><strong>Prov. /Dpto.</strong></td>
                            <td colspan="3">${acta.lugar_intervencion || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td><strong>Origen del viaje</strong></td>
                            <td colspan="3"></td>
                        </tr>
                        <tr>
                            <td><strong>(Depto./Prov./Distrito)</strong></td>
                            <td colspan="3"></td>
                        </tr>
                        <tr>
                            <td><strong>Destino Viaje:</strong></td>
                            <td colspan="3"></td>
                        </tr>
                        <tr>
                            <td><strong>(Depto./Prov./Distrito)</strong></td>
                            <td colspan="3"></td>
                        </tr>
                        <tr>
                            <td><strong>Tipo de Servicio que presta:</strong></td>
                            <td>☐ Personas</td>
                            <td>☐ mercancía</td>
                            <td>☐ mixto</td>
                        </tr>
                        <tr>
                            <td><strong>Inspector:</strong></td>
                            <td colspan="3">${acta.inspector_responsable || 'N/A'}</td>
                        </tr>
                    </table>

                    <div class="description-box">
                        <p style="margin: 0; font-size: 8pt;"><strong>Descripción de los hechos:</strong></p>
                        <p style="margin: 5px 0; font-size: 8pt; min-height: 40px;">${acta.descripcion_infraccion || acta.descripcion_hechos || ''}</p>
                    </div>

                    <table class="content-table">
                        <tr>
                            <td style="width: 50%;"><strong>Medios probatorios:</strong></td>
                            <td style="width: 50%;"></td>
                        </tr>
                        <tr>
                            <td><strong>Calificación de la Infracción:</strong></td>
                            <td>${acta.codigo_infraccion || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td><strong>Medida(s) Administrativa(s):</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><strong>Sanción:</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><strong>Observaciones del intervenido:</strong></td>
                            <td style="min-height: 40px;"></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="min-height: 40px;"><strong>Observaciones del inspector:</strong></td>
                        </tr>
                    </table>

                    <p class="footer-text">
                        La medida administrativa impuesta deberá ser cumplida estrictamente, bajo apercibimiento expreso de ser denunciado penalmente por desobediencia o resistencia a la autoridad, ante su incumplimiento.
                    </p>

                    <table class="signatures-table">
                        <tr>
                            <td>
                                <div class="signature-line">
                                    <p style="margin: 2px 0;"><strong>Firma del Intervenido</strong></p>
                                    <p style="margin: 2px 0;">Nom Ap.:</p>
                                    <p style="margin: 2px 0;">DNI:</p>
                                </div>
                            </td>
                            <td>
                                <div class="signature-line">
                                    <p style="margin: 2px 0;"><strong>Firma del Representante PNP</strong></p>
                                    <p style="margin: 2px 0;">Nom Ap.:</p>
                                    <p style="margin: 2px 0;">CIP:</p>
                                </div>
                            </td>
                            <td>
                                <div class="signature-line">
                                    <p style="margin: 2px 0;"><strong>Firma del Inspector</strong></p>
                                    <p style="margin: 2px 0;">Nombre Ap.:</p>
                                    <p style="margin: 2px 0;">DNI:</p>
                                </div>
                            </td>
                        </tr>
                    </table>

                    <p class="footer-text">
                        De conceder la presentación de algún descargo puede realizarlo en la sede de la DRTC. As. (h) Para lo cual dispone de cinco (5) días hábiles, a partir de la imposición del presente informe de control o del certificado de presente documento de acuerdo a lo dispuesto en el Reglamento del Procedimiento Administrativo Sancionador Especial de la Dirección General Caminos y Servicios de Transporte y tránsito terrestre, y sus servicios complementarios, aprobado mediante Decreto Supremo N° 009-2004 MTC, tal como si de acuerdo a la Ley N° 27867 Ley Orgánica de Gobiernos Regionales y su Reglamento de Organización y Funciones, aprobado mediante Ordenanza Regional N°...
                    </p>
                </div>
            </body>
            </html>
        `;

        // Crear elemento temporal con el HTML
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = htmlCompleto;
        tempDiv.style.position = 'absolute';
        tempDiv.style.left = '-9999px';
        tempDiv.style.top = '-9999px';
        tempDiv.style.width = '800px';
        tempDiv.style.background = 'white';
        tempDiv.style.fontFamily = 'Arial, sans-serif';
        document.body.appendChild(tempDiv);

        console.log('📄 HTML creado, convirtiendo a canvas...');

        // Usar html2canvas para convertir el HTML a imagen
        const canvas = await html2canvas(tempDiv, {
            scale: 2,
            useCORS: true,
            allowTaint: true,
            backgroundColor: '#ffffff',
            width: 800,
            height: tempDiv.scrollHeight
        });

        console.log('📄 Canvas generado, creando PDF...');

        // Crear PDF con jsPDF
        const imgData = canvas.toDataURL('image/png');
        const pdf = new jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: 'a4'
        });

        const imgWidth = 210; // A4 width in mm
        const pageHeight = 297; // A4 height in mm
        const imgHeight = (canvas.height * imgWidth) / canvas.width;

        let heightLeft = imgHeight;
        let position = 0;

        // Agregar primera página
        pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;

        // Agregar páginas adicionales si es necesario
        while (heightLeft >= 0) {
            position = heightLeft - imgHeight;
            pdf.addPage();
            pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
        }

        // Generar nombre del archivo y guardar
        const fileName = `Acta_${acta.numero_acta || '000000'}_${new Date().getFullYear()}.pdf`;
        pdf.save(fileName);

        console.log('✅ PDF generado y guardado como:', fileName);

        // Limpiar
        document.body.removeChild(tempDiv);

        // Mostrar mensaje de éxito
        if (typeof mostrarExitoActas === 'function') {
            mostrarExitoActas('PDF generado correctamente');
        } else {
            alert('PDF generado correctamente');
        }

    } catch (error) {
        console.error('❌ Error generando PDF directo:', error);
        alert('Error al generar PDF: ' + error.message);
    }
}

async function generarPDFAlternativo(acta) {
    try {
        console.log('ðŸ“š Generando PDF alternativo para acta:', acta.numero_acta);

        // Crear el mismo HTML que se usa para imprimir
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
                    Quienes suscriben la presente acta nos identificamos como Inspectores acreditados de la DRTC AP, informamos el objeto y el contenido de la acción de fiscalización, cumpliendo de acuerdo a lo señalado en la normativa vigente:
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
                    La medida administrativa impuesta deberá ser cumplida estrictamente, bajo apercibimiento expreso de ser denunciado penalmente por desobediencia o resistencia a la autoridad, ante su incumplimiento.
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
                    De conceder la presentación de algún descargo puede realizarlo en la sede de la DRTC. As. (h) Para lo cual dispone de cinco (5) días hábiles, a partir de la imposición del presente informe de control o del certificado de presente documento de acuerdo a lo dispuesto en el Reglamento del Procedimiento Administrativo Sancionador Especial de la Dirección General Caminos y Servicios de Transporte y tránsito terrestre, y sus servicios complementarios, aprobado mediante Decreto Supremo N° 009-2004 MTC, tal como si de acuerdo a la Ley N° 27867 Ley Orgánica de Gobiernos Regionales y su Reglamento de Organización y Funciones, aprobado mediante Ordenanza Regional N°...
                </p>
            </div>
        `;

        // Crear ventana oculta con el contenido
        const printWindow = window.open('', '_blank', 'width=800,height=600');
        if (!printWindow) {
            alert('Por favor permita las ventanas emergentes para generar el PDF');
            return;
        }

        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Acta ${acta.numero_acta}</title>
                    <style>
                        body {
                            margin: 0;
                            padding: 20px;
                            font-family: Arial, sans-serif;
                            font-size: 9pt;
                        }
                        @media print {
                            body { margin: 0; padding: 10px; }
                            @page { size: A4; margin: 1cm; }
                        }
                    </style>
                </head>
                <body>
                    ${printContent}
                </body>
            </html>
        `);
        printWindow.document.close();

        // Esperar a que se cargue y luego mostrar diálogo de impresión (que permite guardar como PDF)
        printWindow.onload = function() {
            console.log('ðŸ“š Ventana de impresión lista, mostrando diálogo...');
            setTimeout(() => {
                printWindow.print();
                // Cerrar la ventana después de un tiempo
                setTimeout(() => {
                    printWindow.close();
                }, 1000);
            }, 500);
        };

        console.log('âœ… PDF alternativo generado (usando diÃ¡logo de impresiÃ³n)');

    } catch (error) {
        console.error('âŒ Error en PDF alternativo:', error);
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
                                <strong>PERÃš</strong><br>
                                <strong>GOBIERNO REGIONAL</strong><br>
                                <strong>DE APURÃMAC</strong><br>
                                <strong>DIRECCIÃ“N REGIONAL DE</strong><br>
                                <strong>TRANSPORTES Y COMUNICACIONES</strong><br>
                                <strong>DIRECCIÃ“N DE CIRCULACIÃ“N</strong><br>
                                <strong>TERRESTRE Y SEGURIDAD VIAL</strong>
                            </div>
                        </td>
                        <td style="width: 15%; text-align: right; vertical-align: top;">
                            <img src="images/logo.png" style="width: 60px; height: auto;" />
                        </td>
                    </tr>
                </table>
                <div style="text-align: center; margin: 10px 0;">
                    <h3 style="margin: 5px 0; font-size: 11pt;">ACTA DE CONTROL NÂ° ${acta.numero_acta || '000000'} -${aniActual}</h3>
                    <p style="margin: 3px 0; font-size: 9pt;"><strong>D.S. NÂ° 017-2009-MTC</strong></p>
                    <p style="margin: 3px 0; font-size: 8pt;">CÃ³digo de infracciones y/o incumplimiento<br>Tipo infractor</p>
                </div>
                <p style="font-size: 7pt; text-align: justify; margin: 10px 0;">
                    Quienes suscriben la presente acta nos identificamos como Inspectores acreditados de la DRTC AP, informamos el objeto y el 
                    contenido de la acciÃ³n de fiscalizaciÃ³n, cumpliendo de acuerdo a lo seÃ±alado en la normativa vigente:
                </p>
                <table style="width: 100%; border-collapse: collapse; font-size: 8pt; margin-bottom: 10px;">
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px; width: 25%;"><strong>Agente Infractor:</strong></td>
                        <td style="border: 1px solid #000; padding: 3px; width: 25%;">â˜ Transportista</td>
                        <td style="border: 1px solid #000; padding: 3px; width: 25%;">â˜ Operador de Ruta</td>
                        <td style="border: 1px solid #000; padding: 3px; width: 25%;">â˜‘ Conductor</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Placa:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.placa || acta.placa_vehiculo || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>RazÃ³n Social/Nombre:</strong></td>
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
                        <td style="border: 1px solid #000; padding: 3px;"><strong>NÂ° Licencia DNI del conductor:</strong></td>
                        <td style="border: 1px solid #000; padding: 3px;">NÂ°: ${acta.licencia_conductor || acta.licencia || 'N/A'}</td>
                        <td colspan="2" style="border: 1px solid #000; padding: 3px;">Clase y CategorÃ­a:</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>DirecciÃ³n:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>NÂ° Km. De la red Vial Nacional Prov. /Dpto.</strong></td>
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
                        <td style="border: 1px solid #000; padding: 3px;">â˜ Personas</td>
                        <td style="border: 1px solid #000; padding: 3px;">â˜ mercancÃ­a</td>
                        <td style="border: 1px solid #000; padding: 3px;">â˜ mixto</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Inspector:</strong></td>
                        <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.inspector_responsable || 'N/A'}</td>
                    </tr>
                </table>
                <div style="border: 1px solid #000; padding: 5px; margin-bottom: 10px;">
                    <p style="margin: 0; font-size: 8pt;"><strong>DescripciÃ³n de los hechos:</strong></p>
                    <p style="margin: 5px 0; font-size: 8pt; min-height: 60px;">${acta.descripcion_infraccion || acta.descripcion_hechos || ''}</p>
                </div>
                <table style="width: 100%; border-collapse: collapse; font-size: 8pt; margin-bottom: 10px;">
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px; width: 50%;"><strong>Medios probatorios:</strong></td>
                        <td style="border: 1px solid #000; padding: 3px; width: 50%;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>CalificaciÃ³n de la InfracciÃ³n:</strong></td>
                        <td style="border: 1px solid #000; padding: 3px;">${acta.codigo_infraccion || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>Medida(s) Administrativa(s):</strong></td>
                        <td style="border: 1px solid #000; padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px;"><strong>SanciÃ³n:</strong></td>
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
                    La medida administrativa impuesta deberÃ¡ ser cumplida estrictamente, bajo apercibimiento expreso de ser denunciado 
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
                    De conceder la presentaciÃ³n de algÃºn descargo puede realizarlo en la sede de la DRTC. As. (h) Para lo cual dispone de cinco (5) dÃ­as 
                    hÃ¡biles, a partir de la imposiciÃ³n del presente informe de control o del certificado de presente documento de acuerdo a lo dispuesto en el Reglamento del Procedimiento 
                    Administrativo Sancionador Especial de la DirecciÃ³n General Caminos y Servicios de Transporte y trÃ¡nsito terrestre, y sus servicios complementarios, 
                    aprobado mediante Decreto Supremo NÂ° 009-2004 MTC, tal como si de acuerdo a la Ley NÂ° 27867 Ley OrgÃ¡nica de Gobiernos Regionales y su Reglamento de OrganizaciÃ³n y Funciones, aprobado mediante
                    Ordenanza Regional NÂ°...
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
        })
        .catch(error => {
            console.error('Error al imprimir acta:', error);
        });
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
    if (!confirm(`âš ï¸ Â¿EstÃ¡ seguro de eliminar el acta ${numero}?\n\nEsta acciÃ³n no se puede deshacer.`)) {
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
            alert('âœ… Acta eliminada correctamente');
            cargarListaActas();
        } else {
            alert('âŒ Error al eliminar: ' + (result.message || 'Error desconocido'));
        }
    } catch (error) {
        console.error('Error al eliminar acta:', error);
        alert('âŒ Error al eliminar la acta: ' + error.message);
    }
}

async function cargarMisActasEspecificas() {
    // Similar a cargarListaActas pero filtrado por usuario
    console.log('ðŸ‘¤ Cargando solo mis actas...');
    // Por ahora usa la misma funciÃ³n
    cargarListaActas();
}

function filtrarTablaActas() {
    console.log('ðŸ” Aplicando filtros...');
    // Implementar filtrado local
}

function limpiarFiltrosActas() {
    console.log('ðŸ§¹ Limpiando filtros...');
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

console.log('âœ… Sistema simple de actas cargado correctamente');
