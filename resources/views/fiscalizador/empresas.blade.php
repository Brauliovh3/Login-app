@extends('layouts.dashboard')

@section('title', 'Gestión de Empresas')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-building me-2" style="color: #ff8c00;"></i>
                    Gestión de Empresas de Transporte
                </h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevaEmpresaModal">
                    <i class="fas fa-plus me-2"></i>Nueva Empresa
                </button>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4" style="border-color: #ff8c00;">
        <div class="card-header" style="background-color: #ff8c00; color: white;">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filtros de Búsqueda</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label for="filtro_ruc" class="form-label">RUC</label>
                    <input type="text" class="form-control" id="filtro_ruc" placeholder="20123456789">
                </div>
                <div class="col-md-3">
                    <label for="filtro_razon" class="form-label">Razón Social</label>
                    <input type="text" class="form-control" id="filtro_razon" placeholder="Nombre de la empresa">
                </div>
                <div class="col-md-3">
                    <label for="filtro_tipo" class="form-label">Tipo de Servicio</label>
                    <select class="form-select" id="filtro_tipo">
                        <option value="">Todos</option>
                        <option value="publico_regular">Público Regular</option>
                        <option value="publico_especial">Público Especial</option>
                        <option value="turistico">Turístico</option>
                        <option value="carga">Carga</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtro_estado" class="form-label">Estado</label>
                    <select class="form-select" id="filtro_estado">
                        <option value="">Todos</option>
                        <option value="activo">Activo</option>
                        <option value="suspendido">Suspendido</option>
                        <option value="cancelado">Cancelado</option>
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

    <!-- Tabla de empresas -->
    <div class="card">
        <div class="card-header" style="background-color: #fff3e0; border-color: #ff8c00;">
            <h5 class="mb-0" style="color: #ff8c00;">
                <i class="fas fa-list me-2"></i>Lista de Empresas Registradas
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead style="background-color: #ff8c00; color: white;">
                        <tr>
                            <th>RUC</th>
                            <th>Razón Social</th>
                            <th>Tipo de Servicio</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Vehículos</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>20123456789</strong></td>
                            <td>Transportes del Sur SAC</td>
                            <td><span class="badge bg-info">Público Regular</span></td>
                            <td>+51 987654321</td>
                            <td>contacto@transportessur.com</td>
                            <td><span class="badge bg-primary">25</span></td>
                            <td><span class="badge bg-success">Activo</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" title="Ver detalle">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" title="Documentos">
                                    <i class="fas fa-folder"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>20987654321</strong></td>
                            <td>Empresa Andina EIRL</td>
                            <td><span class="badge bg-warning">Turístico</span></td>
                            <td>+51 912345678</td>
                            <td>info@andina.pe</td>
                            <td><span class="badge bg-primary">12</span></td>
                            <td><span class="badge bg-success">Activo</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" title="Ver detalle">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" title="Documentos">
                                    <i class="fas fa-folder"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>20456789123</strong></td>
                            <td>Cargo Express SA</td>
                            <td><span class="badge bg-dark">Carga</span></td>
                            <td>+51 923456789</td>
                            <td>cargo@express.com</td>
                            <td><span class="badge bg-primary">45</span></td>
                            <td><span class="badge bg-warning">Suspendido</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" title="Ver detalle">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning" title="Reactivar">
                                    <i class="fas fa-play"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva Empresa -->
<div class="modal fade" id="nuevaEmpresaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #ff8c00; color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Nueva Empresa de Transporte
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="nuevaEmpresaForm">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="ruc" class="form-label">RUC *</label>
                            <input type="text" class="form-control" id="ruc" maxlength="11" required>
                        </div>
                        <div class="col-md-6">
                            <label for="tipo_servicio" class="form-label">Tipo de Servicio *</label>
                            <select class="form-select" id="tipo_servicio" required>
                                <option value="">Seleccionar...</option>
                                <option value="publico_regular">Público Regular</option>
                                <option value="publico_especial">Público Especial</option>
                                <option value="turistico">Turístico</option>
                                <option value="carga">Carga</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <label for="razon_social" class="form-label">Razón Social *</label>
                            <input type="text" class="form-control" id="razon_social" required>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarEmpresa()">
                    <i class="fas fa-save me-2"></i>Guardar Empresa
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function guardarEmpresa() {
    const ruc = document.getElementById('ruc').value;
    const razon_social = document.getElementById('razon_social').value;
    const tipo_servicio = document.getElementById('tipo_servicio').value;
    
    if (!ruc || !razon_social || !tipo_servicio) {
        showError('Por favor complete todos los campos obligatorios');
        return;
    }
    
    showSuccess('Empresa guardada exitosamente');
    
    const modal = bootstrap.Modal.getInstance(document.getElementById('nuevaEmpresaModal'));
    modal.hide();
    
    document.getElementById('nuevaEmpresaForm').reset();
}
</script>
@endsection
