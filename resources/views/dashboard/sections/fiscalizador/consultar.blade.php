<div class="text-center">
    <h4><i class="fas fa-search"></i> Consultar Actas</h4>
    <p class="text-muted">Buscar y consultar actas registradas en el sistema</p>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" id="searchTerm" placeholder="Número de acta, placa, DNI/RUC...">
                    <button class="btn btn-primary" onclick="buscarActas()">Buscar</button>
                </div>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="searchType">
                    <option value="all">Todos los campos</option>
                    <option value="numero_acta">Número de acta</option>
                    <option value="placa">Placa</option>
                    <option value="ruc_dni">DNI/RUC</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="statusFilter">
                    <option value="">Todos los estados</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="procesada">Procesada</option>
                    <option value="pagada">Pagada</option>
                    <option value="anulada">Anulada</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" id="dateFilter">
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary w-100" onclick="limpiarFiltros()">
                    <i class="fas fa-broom"></i> Limpiar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Número</th>
                        <th>Fecha</th>
                        <th>Placa</th>
                        <th>Razón Social</th>
                        <th>Infracción</th>
                        <th>Estado</th>
                        <th>Monto</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="actasTableBody">
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            <i class="fas fa-search"></i>
                            <p class="mt-2">Ingresa un término de búsqueda para consultar actas</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
window.init_fiscal_consultar = function() {
    document.getElementById('searchTerm').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            buscarActas();
        }
    });
};

function buscarActas() {
    const searchTerm = document.getElementById('searchTerm').value.trim();
    
    if (!searchTerm) {
        showNotification('Ingresa un término de búsqueda', 'warning');
        return;
    }
    
    const params = new URLSearchParams({
        search: searchTerm,
        type: document.getElementById('searchType').value,
        status: document.getElementById('statusFilter').value,
        date: document.getElementById('dateFilter').value
    });
    
    const tbody = document.getElementById('actasTableBody');
    tbody.innerHTML = `
        <tr>
            <td colspan="8" class="text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Buscando...</span>
                </div>
                <p class="mt-2">Buscando actas...</p>
            </td>
        </tr>
    `;
    
    fetch(`/api/actas/buscar?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.ok && data.actas && data.actas.length > 0) {
                tbody.innerHTML = data.actas.map(acta => `
                    <tr>
                        <td><strong>${acta.numero_acta || '-'}</strong></td>
                        <td>${formatDate(acta.fecha_intervencion)}</td>
                        <td>${acta.placa || '-'}</td>
                        <td>${acta.razon_social || '-'}</td>
                        <td><small>${acta.descripcion_hechos ? acta.descripcion_hechos.substring(0, 50) + '...' : '-'}</small></td>
                        <td><span class="badge bg-${getEstadoColor(acta.estado)}">${acta.estado}</span></td>
                        <td>${acta.monto_multa ? 'S/ ' + acta.monto_multa : '-'}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" onclick="verActa(${acta.id})" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-outline-info" onclick="editarActa(${acta.id})" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-outline-secondary" onclick="imprimirActa(${acta.id})" title="Imprimir">
                                    <i class="fas fa-print"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `).join('');
                
                showNotification(`Se encontraron ${data.actas.length} actas`, 'success');
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            <i class="fas fa-search-minus"></i>
                            <p class="mt-2">No se encontraron actas con los criterios de búsqueda</p>
                        </td>
                    </tr>
                `;
                showNotification('No se encontraron resultados', 'info');
            }
        })
        .catch(error => {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center text-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p class="mt-2">Error en la búsqueda</p>
                    </td>
                </tr>
            `;
            showNotification('Error realizando búsqueda', 'error');
        });
}

function limpiarFiltros() {
    document.getElementById('searchTerm').value = '';
    document.getElementById('searchType').value = 'all';
    document.getElementById('statusFilter').value = '';
    document.getElementById('dateFilter').value = '';
    
    document.getElementById('actasTableBody').innerHTML = `
        <tr>
            <td colspan="8" class="text-center text-muted">
                <i class="fas fa-search"></i>
                <p class="mt-2">Ingresa un término de búsqueda para consultar actas</p>
            </td>
        </tr>
    `;
}

function verActa(id) {
    // Abrir modal o nueva ventana con detalles del acta
    showNotification('Abriendo detalles del acta...', 'info');
}

function editarActa(id) {
    // Redirigir a formulario de edición
    showNotification('Función de edición disponible próximamente', 'info');
}

function imprimirActa(id) {
    // Generar PDF o vista de impresión
    window.open(`/actas/${id}/imprimir`, '_blank');
}

function getEstadoColor(estado) {
    switch(estado) {
        case 'pendiente': return 'warning';
        case 'procesada': return 'info';
        case 'pagada': return 'success';
        case 'anulada': return 'danger';
        default: return 'secondary';
    }
}

function formatDate(dateString) {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('es-ES');
}
</script>