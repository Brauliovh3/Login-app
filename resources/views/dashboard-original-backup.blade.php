@extends('layouts.dashboard')

@section('title', 'Dashboard Unificado')

@section('content')
<div class="container-fluid">
    <h2 class="mb-3">Dashboard Unificado</h2>

    @php $role = auth()->user()->role ?? 'fiscalizador'; @endphp

    @if($role === 'administrador')
        @if(view()->exists('admin.dashboard'))
            @include('admin.dashboard')
        @else
            <p>Panel de administrador no encontrado. <a href="{{ route('admin.dashboard') }}">Ir al panel admin</a></p>
        @endif

    @elseif($role === 'fiscalizador')
        @if(view()->exists('fiscalizador.dashboard'))
            @include('fiscalizador.dashboard')
        @else
            <p>Panel de fiscalizador no encontrado. <a href="{{ route('fiscalizador.dashboard') }}">Ir al panel fiscalizador</a></p>
        @endif

    @elseif($role === 'ventanilla')
        @if(view()->exists('ventanilla.dashboard'))
            @include('ventanilla.dashboard')
        @else
            <p>Panel de ventanilla no encontrado. <a href="{{ route('ventanilla.dashboard') }}">Ir al panel ventanilla</a></p>
        @endif

    @else
        <p>Rol no reconocido. Contacte al administrador.</p>
    @endif

</div>
@endsection
