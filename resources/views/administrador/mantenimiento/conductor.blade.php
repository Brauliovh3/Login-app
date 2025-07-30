@extends('layouts.dashboard')

@section('title', 'Mantenimiento de Conductores')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-id-card me-2" style="color: #ff8c00;"></i>
                    Mantenimiento de Conductores
                </h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoConductorModal">
                    <i class="fas fa-plus me-2"></i>Nuevo Conductor
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
                    <input type="text" class="form-control" id="filtro_nombre" placeholder="Nombre del conductor">
                </div>
                <div class="col-md-3">
                    <label for="filtro_licencia" class="form-label">N° Licencia</label>
                    <input type="text" class="form-control" id="filtro_licencia" placeholder="Número de licencia">
                </div>
                <div class="col-md-3">
                    <label for="filtro_estado" class="form-label">Estado</label>
                    <select class="form-select" id="filtro_estado">
                        <option value="">Todos</option>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                        <option value="suspendido">Suspendido</option>
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

    <!-- Tabla de conductores -->
    <div class="card">
        <div class="card-header" style="background-color: #fff3e0; border-color: #ff8c00;">
            <h5 class="mb-0" style="color: #ff8c00;">
                <i class="fas fa-list me-2"></i>Lista de Conductores
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead style="background-color: #ff8c00; color: white;">
                        <tr>
                            <th>DNI</th>
                            <th>Nombres y Apellidos</th>
                            <th>N° Licencia</th>
                            <th>Clase/Categoría</th>
                            <th>Vencimiento</th>
                            <th>Empresa</th>
                            <th>Puntos</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>12345678</strong></td>
                            <td>Juan Carlos Pérez Mendoza</td>
                            <td>A-IIIa-123456</td>
                            <td><span class="badge bg-info">A-IIIa</span></td>
                            <td>15/12/2025</td>
                            <td>Transportes del Sur SAC</td>
                            <td><span class="badge bg-success">0</span></td>
                            <td><span class="badge bg-success">Activo</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" title="Ver perfil">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" title="Historial">
                                    <i class="fas fa-history"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>87654321</strong></td>
                            <td>María Elena López García</td>
                            <td>A-IIb-789012</td>
                            <td><span class="badge bg-warning">A-IIb</span></td>
                            <td>08/03/2026</td>
                            <td>Empresa Andina EIRL</td>
                            <td><span class="badge bg-warning">5</span></td>
                            <td><span class="badge bg-success">Activo</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" title="Ver perfil">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" title="Historial">
                                    <i class="fas fa-history"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>11223344</strong></td>
                            <td>Roberto Carlos Quispe Huamán</td>
                            <td>A-I-345678</td>
                            <td><span class="badge bg-primary">A-I</span></td>
                            <td class="text-danger"><strong>30/06/2025</strong></td>
                            <td>Independiente</td>
                            <td><span class="badge bg-danger">12</span></td>
                            <td><span class="badge bg-danger">Suspendido</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" title="Ver perfil">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" title="Historial">
                                    <i class="fas fa-history"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nuevo Conductor -->
<div class="modal fade" id="nuevoConductorModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #ff8c00; color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Nuevo Conductor
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="nuevoConductorForm">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Datos Personales</h6>
                            <hr>
                            <div class="mb-3">
                                <label for="dni" class="form-label">DNI *</label>
                                <input type="text" class="form-control" id="dni" maxlength="8" required>
                            </div>
                            <div class="mb-3">
                                <label for="nombres" class="form-label">Nombres *</label>
                                <input type="text" class="form-control" id="nombres" required>
                            </div>
                            <div class="mb-3">
                                <label for="apellidos" class="form-label">Apellidos *</label>
                                <input type="text" class="form-control" id="apellidos" required>
                            </div>
                            <div class="mb-3">
                                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento *</label>
                                <input type="date" class="form-control" id="fecha_nacimiento" required>
                            </div>
                            <div class="mb-3">
                                <label for="direccion" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="direccion">
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="distrito" class="form-label">Distrito</label>
                                    <input type="text" class="form-control" id="distrito">
                                </div>
                                <div class="col-md-4">
                                    <label for="provincia" class="form-label">Provincia</label>
                                    <input type="text" class="form-control" id="provincia">
                                </div>
                                <div class="col-md-4">
                                    <label for="departamento" class="form-label">Departamento</label>
                                    <input type="text" class="form-control" id="departamento">
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
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="text-primary">Datos de Licencia</h6>
                            <hr>
                            <div class="mb-3">
                                <label for="numero_licencia" class="form-label">Número de Licencia *</label>
                                <input type="text" class="form-control" id="numero_licencia" required>
                            </div>
                            <div class="mb-3">
                                <label for="clase_categoria" class="form-label">Clase/Categoría *</label>
                                <select class="form-select" id="clase_categoria" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="A-I">A-I</option>
                                    <option value="A-IIa">A-IIa</option>
                                    <option value="A-IIb">A-IIb</option>
                                    <option value="A-IIIa">A-IIIa</option>
                                    <option value="A-IIIb">A-IIIb</option>
                                    <option value="A-IIIc">A-IIIc</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="fecha_expedicion" class="form-label">Fecha de Expedición *</label>
                                <input type="date" class="form-control" id="fecha_expedicion" required>
                            </div>
                            <div class="mb-3">
                                <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento *</label>
                                <input type="date" class="form-control" id="fecha_vencimiento" required>
                            </div>
                            <div class="mb-3">
                                <label for="empresa_id" class="form-label">Empresa</label>
                                <select class="form-select" id="empresa_id">
                                    <option value="">Independiente</option>
                                    <option value="1">Transportes del Sur SAC</option>
                                    <option value="2">Empresa Andina EIRL</option>
                                    <option value="3">Movil Tours SA</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="estado_licencia" class="form-label">Estado de Licencia</label>
                                <select class="form-select" id="estado_licencia">
                                    <option value="vigente">Vigente</option>
                                    <option value="vencida">Vencida</option>
                                    <option value="suspendida">Suspendida</option>
                                    <option value="cancelada">Cancelada</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="puntos_acumulados" class="form-label">Puntos Acumulados</label>
                                <input type="number" class="form-control" id="puntos_acumulados" value="0" min="0" max="20">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarConductor()">
                    <i class="fas fa-save me-2"></i>Guardar Conductor
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function guardarConductor() {
    // Validar formulario
    const dni = document.getElementById('dni').value;
    const nombres = document.getElementById('nombres').value;
    const apellidos = document.getElementById('apellidos').value;
    const numero_licencia = document.getElementById('numero_licencia').value;
    
    if (!dni || !nombres || !apellidos || !numero_licencia) {
        showError('Por favor complete todos los campos obligatorios');
        return;
    }
    
    // Simular guardado
    showSuccess('Conductor guardado exitosamente');
    
    // Cerrar modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('nuevoConductorModal'));
    modal.hide();
    
    // Limpiar formulario
    document.getElementById('nuevoConductorForm').reset();
}
</script>
@endsection
