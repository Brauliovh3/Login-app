@extends('layouts.dashboard')

@section('title', 'Mantenimiento de Fiscales')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-user-tie me-2" style="color: #ff8c00;"></i>
                    Mantenimiento de Fiscales
                </h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoFiscalModal">
                    <i class="fas fa-plus me-2"></i>Nuevo Fiscal
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
                    <label for="filtro_dni" class="form-label">DNI</label>
                    <input type="text" class="form-control" id="filtro_dni" placeholder="12345678">
                </div>
                <div class="col-md-3">
                    <label for="filtro_nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="filtro_nombre" placeholder="Nombre del fiscal">
                </div>
                <div class="col-md-3">
                    <label for="filtro_estado" class="form-label">Estado</label>
                    <select class="form-select" id="filtro_estado">
                        <option value="">Todos</option>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                        <option value="licencia">En Licencia</option>
                        <option value="vacaciones">Vacaciones</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtro_zona" class="form-label">Zona Asignada</label>
                    <input type="text" class="form-control" id="filtro_zona" placeholder="Zona">
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

    <!-- Tabla de fiscales -->
    <div class="card">
        <div class="card-header" style="background-color: #fff3e0; border-color: #ff8c00;">
            <h5 class="mb-0" style="color: #ff8c00;">
                <i class="fas fa-list me-2"></i>Lista de Fiscales
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead style="background-color: #ff8c00; color: white;">
                        <tr>
                            <th>Código</th>
                            <th>DNI</th>
                            <th>Nombres y Apellidos</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Zona Asignada</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>FISC-001</strong></td>
                            <td>12345678</td>
                            <td>Juan Carlos Pérez Mendoza</td>
                            <td>+51 987654321</td>
                            <td>juan.perez@drtc.gob.pe</td>
                            <td>Zona Norte</td>
                            <td><span class="badge bg-success">Activo</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" title="Ver perfil">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning" title="Cambiar estado">
                                    <i class="fas fa-toggle-on"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>FISC-002</strong></td>
                            <td>87654321</td>
                            <td>María Elena López García</td>
                            <td>+51 912345678</td>
                            <td>maria.lopez@drtc.gob.pe</td>
                            <td>Zona Sur</td>
                            <td><span class="badge bg-warning">Licencia</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" title="Ver perfil">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning" title="Cambiar estado">
                                    <i class="fas fa-toggle-off"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>FISC-003</strong></td>
                            <td>11223344</td>
                            <td>Roberto Carlos Quispe Huamán</td>
                            <td>+51 923456789</td>
                            <td>roberto.quispe@drtc.gob.pe</td>
                            <td>Zona Centro</td>
                            <td><span class="badge bg-info">Vacaciones</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" title="Ver perfil">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning" title="Cambiar estado">
                                    <i class="fas fa-toggle-on"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nuevo Fiscal -->
<div class="modal fade" id="nuevoFiscalModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #ff8c00; color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Nuevo Fiscal
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="nuevoFiscalForm">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="dni" class="form-label">DNI *</label>
                            <input type="text" class="form-control" id="dni" maxlength="8" required>
                        </div>
                        <div class="col-md-6">
                            <label for="codigo_fiscal" class="form-label">Código de Fiscal *</label>
                            <input type="text" class="form-control" id="codigo_fiscal" required>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="nombres" class="form-label">Nombres *</label>
                            <input type="text" class="form-control" id="nombres" required>
                        </div>
                        <div class="col-md-6">
                            <label for="apellidos" class="form-label">Apellidos *</label>
                            <input type="text" class="form-control" id="apellidos" required>
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
                        <div class="col-md-6">
                            <label for="fecha_ingreso" class="form-label">Fecha de Ingreso *</label>
                            <input type="date" class="form-control" id="fecha_ingreso" required>
                        </div>
                        <div class="col-md-6">
                            <label for="zona_asignada" class="form-label">Zona Asignada</label>
                            <input type="text" class="form-control" id="zona_asignada">
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
                <button type="button" class="btn btn-primary" onclick="guardarFiscal()">
                    <i class="fas fa-save me-2"></i>Guardar Fiscal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function guardarFiscal() {
    // Validar formulario
    const dni = document.getElementById('dni').value;
    const codigo = document.getElementById('codigo_fiscal').value;
    const nombres = document.getElementById('nombres').value;
    const apellidos = document.getElementById('apellidos').value;
    
    if (!dni || !codigo || !nombres || !apellidos) {
        showError('Por favor complete todos los campos obligatorios');
        return;
    }
    
    // Simular guardado
    showSuccess('Fiscal guardado exitosamente');
    
    // Cerrar modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('nuevoFiscalModal'));
    modal.hide();
    
    // Limpiar formulario
    document.getElementById('nuevoFiscalForm').reset();
}
</script>
@endsection
