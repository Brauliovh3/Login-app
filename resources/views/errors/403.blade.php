@extends('layouts.app')

@section('title', 'Acceso Denegado')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center">
                    <div class="error-page">
                        <h1 class="display-1 text-danger">403</h1>
                        <h2 class="text-danger">Acceso Denegado</h2>
                        <p class="lead">{{ $exception->getMessage() ?? 'No tienes permisos para acceder a esta sección.' }}</p>
                        
                        <div class="mt-4">
                            <i class="fas fa-exclamation-triangle fa-5x text-warning mb-4"></i>
                        </div>
                        
                        <p class="text-muted">
                            Has intentado acceder a una sección que requiere permisos específicos. 
                            Tu rol actual no te permite ver esta página.
                        </p>

                        <div class="mt-4">
                            @auth
                                @if(auth()->user()->role == 'administrador')
                                    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                                        <i class="fas fa-home me-2"></i>Volver al Dashboard
                                    </a>
                                @elseif(auth()->user()->role == 'fiscalizador')
                                    <a href="{{ route('fiscalizador.dashboard') }}" class="btn btn-info">
                                        <i class="fas fa-home me-2"></i>Volver al Dashboard
                                    </a>
                                @elseif(auth()->user()->role == 'ventanilla')
                                    <a href="{{ route('ventanilla.dashboard') }}" class="btn btn-warning">
                                        <i class="fas fa-home me-2"></i>Volver al Dashboard
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .error-page {
        padding: 3rem 0;
    }
    
    .display-1 {
        font-size: 8rem;
        font-weight: 700;
    }
    
    @media (max-width: 768px) {
        .display-1 {
            font-size: 5rem;
        }
    }
</style>
@endsection
