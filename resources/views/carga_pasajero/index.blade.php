@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h2>Gestión de Carga y Pasajero</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalCrear">Nuevo Control</button>

    <table class="table mt-3">
        <thead>
            <tr>
                <th>Informe</th>
                <th>Resolución</th>
                <th>Conductor</th>
                <th>Licencia</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($registros as $registro)
            <tr>
                <td>{{ $registro->informe }}</td>
                <td>{{ $registro->resolucion }}</td>
                <td>{{ $registro->conductor }}</td>
                <td>{{ $registro->licencia_conductor }}</td>
                <td>{{ $registro->estado }}</td>
                <td>
                    {{-- Solo mostrar editar/eliminar si está pendiente --}}
                    @if($registro->estado === 'pendiente')
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $registro->id }}">Editar</button>
                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalEliminar{{ $registro->id }}">Eliminar</button>
                    @endif
                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalVer{{ $registro->id }}">Ver</button>
                </td>
            </tr>

            {{-- Modal Ver --}}
            <div class="modal fade" id="modalVer{{ $registro->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header"><h5>Detalle</h5></div>
                        <div class="modal-body">
                            <p><strong>Informe:</strong> {{ $registro->informe }}</p>
                            <p><strong>Resolución:</strong> {{ $registro->resolucion }}</p>
                            <p><strong>Conductor:</strong> {{ $registro->conductor }}</p>
                            <p><strong>Licencia:</strong> {{ $registro->licencia_conductor }}</p>
                            <p><strong>Estado:</strong> {{ $registro->estado }}</p>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Editar --}}
            @if($registro->estado === 'pendiente')
            <div class="modal fade" id="modalEditar{{ $registro->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('carga-pasajero.update', $registro->id) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="modal-header"><h5>Editar Registro</h5></div>
                            <div class="modal-body">
                                <input type="text" name="informe" value="{{ $registro->informe }}" class="form-control mb-2" required>
                                <input type="text" name="resolucion" value="{{ $registro->resolucion }}" class="form-control mb-2" required>
                                <input type="text" name="conductor" value="{{ $registro->conductor }}" class="form-control mb-2" required>
                                <input type="text" name="licencia_conductor" value="{{ $registro->licencia_conductor }}" class="form-control mb-2" required>
                                <select name="estado" class="form-control mb-2" required>
                                    <option value="pendiente" @if($registro->estado === 'pendiente') selected @endif>Pendiente</option>
                                    <option value="aprobado" @if($registro->estado === 'aprobado') selected @endif>Aprobado</option>
                                    <option value="procesado" @if($registro->estado === 'procesado') selected @endif>Procesado</option>
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Guardar</button>
                                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            {{-- Modal Eliminar --}}
            @if($registro->estado === 'pendiente')
            <div class="modal fade" id="modalEliminar{{ $registro->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('carga-pasajero.destroy', $registro->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <div class="modal-header">
                                <h5 class="modal-title text-danger">Confirmar Eliminación</h5>
                            </div>
                            <div class="modal-body">
                                <p>¿Estás seguro de que deseas eliminar este registro?</p>
                                <ul>
                                    <li><strong>Informe:</strong> {{ $registro->informe }}</li>
                                    <li><strong>Conductor:</strong> {{ $registro->conductor }}</li>
                                    <li><strong>Licencia:</strong> {{ $registro->licencia_conductor }}</li>
                                </ul>
                                <div class="alert alert-warning">
                                    <strong>Advertencia:</strong> Esta acción no se puede deshacer.
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-danger">Eliminar</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            @endforeach
        </tbody>
    </table>

    {{-- Modal Crear --}}
    <div class="modal fade" id="modalCrear" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('carga-pasajero.store') }}" method="POST">
                    @csrf
                    <div class="modal-header"><h5>Nuevo Control</h5></div>
                    <div class="modal-body">
                        <input type="text" name="informe" class="form-control mb-2" placeholder="Informe" required>
                        <input type="text" name="resolucion" class="form-control mb-2" placeholder="Resolución" required>
                        <input type="text" name="conductor" class="form-control mb-2" placeholder="Conductor" required>
                        <input type="text" name="licencia_conductor" class="form-control mb-2" placeholder="Licencia" required>
                        <select name="estado" class="form-control mb-2" required>
                            <option value="pendiente">Pendiente</option>
                            <option value="aprobado">Aprobado</option>
                            <option value="procesado">Procesado</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Crear</button>
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
