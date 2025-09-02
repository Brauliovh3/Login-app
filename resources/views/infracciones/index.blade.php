@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Infracciones</h1>

    @if($infracciones->isEmpty())
        <p>No hay infracciones registradas.</p>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Multa (S/)</th>
                    <th>Tipo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($infracciones as $inf)
                <tr>
                    <td>{{ $inf->id }}</td>
                    <td>{{ $inf->codigo }}</td>
                    <td>{{ $inf->descripcion }}</td>
                    <td>{{ $inf->multa_soles }}</td>
                    <td>{{ $inf->tipo_infraccion }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
