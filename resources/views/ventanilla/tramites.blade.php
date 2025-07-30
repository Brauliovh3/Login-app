@extends('layouts.dashboard')

@section('title', 'Gestión de Trámites')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0">
                <i class="fas fa-folder-open me-2" style="color: #ff8c00;"></i>
                Gestión de Trámites
            </h2>
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
                    <label for="filtro_numero" class="form-label">N° de Trámite</label>
                    <input type="text" class="form-control" id="filtro_numero" placeholder="TRA-2025-001">
                </div>
                <div class="col-md-3">
                    <label for="filtro_tipo" class="form-label">Tipo de Trámite</label>
                    <select class="form-select" id="filtro_tipo">
                        <option value="">Todos</option>
                        <option value="licencia">Licencia de Conducir</option>
                        <option value="permiso">Permiso de Operación</option>
                        <option value="certificado">Certificado</option>
                        <option value="renovacion">Renovación</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtro_estado" class="form-label">Estado</label>
                    <select class="form-select" id="filtro_estado">
                        <option value="">Todos</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="proceso">En Proceso</option>
                        <option value="aprobado">Aprobado</option>
                        <option value="rechazado">Rechazado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtro_fecha" class="form-label">Fecha</label>
                    <input type="date" class="form-control" id="filtro_fecha">
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

    <!-- Tabla de trámites -->
    <div class="card">
        <div class="card-header" style="background-color: #fff3e0; border-color: #ff8c00;">
            <h5 class="mb-0" style="color: #ff8c00;">
                <i class="fas fa-list me-2"></i>Lista de Trámites
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead style="background-color: #ff8c00; color: white;">
                        <tr>
                            <th>N° Trámite</th>
                            <th>Fecha</th>
                            <th>Solicitante</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Días Transcurridos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>TRA-2025-001</strong></td>
                            <td>30/07/2025</td>
                            <td>Juan Pérez Gómez</td>
                            <td><span class="badge bg-primary">Licencia de Conducir</span></td>
                            <td><span class="badge bg-warning">En Proceso</span></td>
                            <td>3 días</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" title="Ver detalle">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="Actualizar estado">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" title="Imprimir">
                                    <i class="fas fa-print"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>TRA-2025-002</strong></td>
                            <td>29/07/2025</td>
                            <td>María López Silva</td>
                            <td><span class="badge bg-success">Certificado</span></td>
                            <td><span class="badge bg-success">Aprobado</span></td>
                            <td>4 días</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" title="Ver detalle">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" title="Imprimir">
                                    <i class="fas fa-print"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" title="Entregar">
                                    <i class="fas fa-hand-paper"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>TRA-2025-003</strong></td>
                            <td>28/07/2025</td>
                            <td>Carlos Ruiz Mendoza</td>
                            <td><span class="badge bg-warning">Renovación</span></td>
                            <td><span class="badge bg-info">Pendiente</span></td>
                            <td>5 días</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" title="Ver detalle">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="Procesar">
                                    <i class="fas fa-play"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="Rechazar">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
