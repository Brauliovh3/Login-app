@extends('layouts.dashboard')

@section('title', 'Infracciones de Tránsito')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Infracciones de Tránsito</h2>
                        <p class="mb-0">Gestión de infracciones y multas de tránsito</p>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-list me-2"></i>Lista de Infracciones</h4>
                @if(Auth::user()->role === 'fiscalizador' || Auth::user()->role === 'administrador')
                    <a href="{{ route('infracciones.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-1"></i>Nueva Infracción
                    </a>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Placa</th>
                                <th>Conductor</th>
                                <th>Fecha</th>
                                <th>Tipo Servicio</th>
                                <th>Inspector</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($infracciones as $infraccion)
                                <tr>
                                    <td>{{ $infraccion->id }}</td>
                                    <td><strong>{{ $infraccion->placa }}</strong></td>
                                    <td>{{ $infraccion->nombre_conductor1 }}</td>
                                    <td>{{ $infraccion->fecha_inicio->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $infraccion->tipo_servicio == 'personas' ? 'info' : 'warning' }}">
                                            {{ ucfirst($infraccion->tipo_servicio) }}
                                        </span>
                                    </td>
                                    <td>{{ $infraccion->inspector }}</td>
                                    <td>
                                        <span class="badge bg-{{ $infraccion->calificacion_infraccion == 'muy_grave' ? 'danger' : ($infraccion->calificacion_infraccion == 'grave' ? 'warning' : 'success') }}">
                                            {{ ucfirst(str_replace('_', ' ', $infraccion->calificacion_infraccion)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('infracciones.show', $infraccion->id) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(Auth::user()->role === 'fiscalizador' || Auth::user()->role === 'administrador')
                                                <a href="{{ route('infracciones.edit', $infraccion->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                            @if(Auth::user()->role === 'administrador')
                                                <form action="{{ route('infracciones.destroy', $infraccion->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar esta infracción?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">
                                        <i class="fas fa-info-circle me-2"></i>No hay infracciones registradas
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($infracciones->hasPages())
                    <div class="d-flex justify-content-center">
                        {{ $infracciones->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div class="toast show" role="alert">
            <div class="toast-header">
                <i class="fas fa-check-circle text-success me-2"></i>
                <strong class="me-auto">Éxito</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                {{ session('success') }}
            </div>
        </div>
    </div>
@endif
@endsection
