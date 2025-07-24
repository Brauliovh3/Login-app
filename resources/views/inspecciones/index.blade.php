@extends('layouts.dashboard')

@section('title', 'Inspecciones')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="mb-0"><i class="fas fa-search me-2"></i>Gestión de Inspecciones</h2>
                        <p class="mb-0">Administrar inspecciones y controles de establecimientos</p>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-check fa-3x opacity-50"></i>
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
                <h4 class="mb-0"><i class="fas fa-list me-2"></i>Lista de Inspecciones</h4>
                @if(Auth::user()->role === 'fiscalizador' || Auth::user()->role === 'ventanilla' || Auth::user()->role === 'administrador')
                    <a href="{{ route('inspecciones.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-1"></i>Nueva Inspección
                    </a>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Establecimiento</th>
                                <th>Tipo</th>
                                <th>Fecha</th>
                                <th>Inspector</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Datos de ejemplo -->
                            <tr>
                                <td>#001</td>
                                <td>Restaurant El Buen Sabor</td>
                                <td><span class="badge bg-primary">Rutinaria</span></td>
                                <td>{{ now()->format('d/m/Y') }}</td>
                                <td>Juan Pérez</td>
                                <td><span class="badge bg-success">Conforme</span></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if(Auth::user()->role === 'fiscalizador' || Auth::user()->role === 'administrador')
                                            <button class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif
                                        @if(Auth::user()->role === 'administrador')
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>#002</td>
                                <td>Farmacia Central</td>
                                <td><span class="badge bg-warning">Seguimiento</span></td>
                                <td>{{ now()->subDay()->format('d/m/Y') }}</td>
                                <td>María García</td>
                                <td><span class="badge bg-warning">No Conforme</span></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if(Auth::user()->role === 'fiscalizador' || Auth::user()->role === 'administrador')
                                            <button class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif
                                        @if(Auth::user()->role === 'administrador')
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
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
