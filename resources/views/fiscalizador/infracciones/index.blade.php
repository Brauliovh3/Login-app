@extends('layouts.app')

@section('title', 'Gestión de Infracciones')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        Gestión de Infracciones
                    </h3>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearInfraccionModal">
                        <i class="fas fa-plus"></i> Nueva Infracción
                    </button>
                </div>

                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="filtroGravedad" class="form-label">Filtrar por Gravedad:</label>
                            <select id="filtroGravedad" class="form-select">
                                <option value="">Todas las gravedades</option>
                                <option value="leve">Leve</option>
                                <option value="grave">Grave</option>
                                <option value="muy_grave">Muy Grave</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filtroAplicaSobre" class="form-label">Aplica sobre:</label>
                            <select id="filtroAplicaSobre" class="form-select">
                                <option value="">Todos</option>
                                <option value="Transportista">Transportista</option>
                                <option value="Conductor">Conductor</option>
                                <option value="Generador de carga">Generador de carga</option>
                                <option value="Operadores de terminales terrestres">Operadores de terminales</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="busqueda" class="form-label">Buscar:</label>
                            <input type="text" id="busqueda" class="form-control" placeholder="Buscar por código, sanción...">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" id="btnFiltrar" class="btn btn-outline-primary">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                        </div>
                    </div>

                    <!-- Tabla de infracciones -->
                    <div class="table-responsive">
                        <table id="tablaInfracciones" class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Código</th>
                                    <th>Aplica Sobre</th>
                                    <th>Reglamento</th>
                                    <th>Clase de Pago</th>
                                    <th>Sanción</th>
                                    <th>Gravedad</th>
                                    <th>Detalles</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($infracciones as $infraccion)
                                <tr>
                                    <td>
                                        <span class="badge badge-codigo">{{ $infraccion->codigo }}</span>
                                    </td>
                                    <td>{{ $infraccion->aplica_sobre }}</td>
                                    <td class="text-truncate" style="max-width: 200px;" title="{{ $infraccion->reglamento }}">
                                        {{ Str::limit($infraccion->reglamento, 50) }}
                                    </td>
                                    <td>
                                        <span class="badge {{ $infraccion->clase_pago === 'Pecuniaria' ? 'bg-success' : 'bg-warning' }}">
                                            {{ $infraccion->clase_pago }}
                                        </span>
                                    </td>
                                    <td>{{ $infraccion->sancion }}</td>
                                    <td>
                                        @if($infraccion->gravedad === 'muy_grave')
                                            <span class="badge bg-danger">Muy Grave</span>
                                        @elseif($infraccion->gravedad === 'grave')
                                            <span class="badge bg-warning">Grave</span>
                                        @else
                                            <span class="badge bg-info">Leve</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $infraccion->detalles->count() }} detalle(s)</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-info" 
                                                    onclick="verInfraccion({{ $infraccion->id }})" 
                                                    title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-warning" 
                                                    onclick="editarInfraccion({{ $infraccion->id }})" 
                                                    title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="eliminarInfraccion({{ $infraccion->id }}, '{{ $infraccion->codigo }}')" 
                                                    title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-center">
                        {{ $infracciones->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Incluir modales -->
@include('fiscalizador.infracciones.modales.crear')
@include('fiscalizador.infracciones.modales.ver')
@include('fiscalizador.infracciones.modales.editar')
@include('fiscalizador.infracciones.modales.eliminar')

@endsection

@push('styles')
<style>
    .badge-codigo {
        background-color: #6c757d;
        font-weight: bold;
        font-size: 0.9em;
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
    }
    
    .btn-group .btn {
        margin-right: 2px;
    }
    
    .text-truncate {
        max-width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/infracciones.js') }}"></script>
@endpush