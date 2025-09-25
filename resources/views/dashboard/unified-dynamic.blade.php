@extends('layouts.dashboard')

@section('title', 'Panel de Control - DRTC Apurímac')

@section('content')
<div class="container-fluid">
    <!-- El contenido se carga dinámicamente aquí -->
</div>
@endsection

@section('scripts')
<style>
    .cursor-pointer {
        cursor: pointer;
    }
    
    .btn-circle {
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .btn-circle.btn-lg {
        width: 3rem;
        height: 3rem;
    }
    
    .blinking-cursor {
        animation: blink 1s infinite;
    }
    
    @keyframes blink {
        0%, 50% { opacity: 1; }
        51%, 100% { opacity: 0; }
    }
</style>
@endsection