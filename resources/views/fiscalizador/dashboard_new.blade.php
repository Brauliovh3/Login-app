@extends('layouts.dashboard')

@section('title', 'Dashboard - Fiscalizador DRTC Apurímac')

@section('content')
<style>
    :root {
        --drtc-orange: #ff8c00;
        --drtc-dark-orange: #e67c00;
        --drtc-light-orange: #ffffff;
        --drtc-orange-bg: #fff4e6;
        --drtc-navy: #1e3a8a;
    }
    
    .bg-drtc-orange { background-color: var(--drtc-orange) !important; }
    .bg-drtc-dark { background-color: var(--drtc-dark-orange) !important; }
    .bg-drtc-light { background-color: var(--drtc-light-orange) !important; }
    .bg-drtc-soft { background-color: var(--drtc-orange-bg) !important; }
    .bg-drtc-navy { background-color: var(--drtc-navy) !important; }
    .text-drtc-orange { color: var(--drtc-orange) !important; }
    .text-drtc-navy { color: var(--drtc-navy) !important; }
    .border-drtc-orange { border-color: var(--drtc-orange) !important; }
    
    .btn-drtc-orange { 
        background-color: var(--drtc-orange); 
        border-color: var(--drtc-orange); 
        color: white !important;
        font-weight: bold;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
    }
    .btn-drtc-orange:hover { 
        background-color: var(--drtc-dark-orange); 
        border-color: var(--drtc-dark-orange); 
        color: white !important;
    }
    
    .nav-tabs .nav-link.active {
        background-color: var(--drtc-orange) !important;
        border-color: var(--drtc-orange) !important;
        color: white !important;
        font-weight: bold;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        font-size: 1.1rem;
    }
    .nav-tabs .nav-link {
        color: #2c3e50 !important;
        border-color: transparent;
        font-weight: bold;
        font-size: 1.05rem;
        text-shadow: 1px 1px 2px rgba(255,255,255,0.8);
    }
    .nav-tabs .nav-link:hover {
        background-color: var(--drtc-orange-bg);
        border-color: var(--drtc-orange);
        color: var(--drtc-dark-orange) !important;
        text-shadow: 1px 1px 2px rgba(255,255,255,0.9);
    }
    
    .drtc-logo {
        background: linear-gradient(135deg, var(--drtc-orange), var(--drtc-dark-orange));
        border-radius: 50%;
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white !important;
        font-size: 24px;
        font-weight: bold;
        box-shadow: 0 4px 15px rgba(255, 140, 0, 0.3);
        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    }
    
    .drtc-header {
        background: linear-gradient(135deg, var(--drtc-orange), var(--drtc-dark-orange));
        position: relative;
        overflow: hidden;
        color: white !important;
    }
    
    .drtc-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20px;
        width: 100px;
        height: 200%;
        background: rgba(255, 255, 255, 0.1);
        transform: rotate(15deg);
    }
    
    /* Mejorar visibilidad general */
    .card-header {
        color: #333 !important;
    }
    
    .card-header.bg-drtc-orange {
        color: white !important;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
    }
    
    .card-header.bg-warning {
        color: #333 !important;
        font-weight: bold;
    }
    
    .card-header.bg-danger {
        color: white !important;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
    }
    
    .text-drtc-orange {
        color: var(--drtc-orange) !important;
        font-weight: bold;
    }
    
    .btn {
        font-weight: 600;
    }
    
    .table thead th {
        color: white !important;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
    }
    
    /* Estilos para formularios modales */
    .modal-form {
        background: rgba(255, 255, 255, 0.95);
        border: 2px solid var(--drtc-orange);
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(5px);
        margin: 20px 0;
    }
    
    .modal-header-custom {
        background: linear-gradient(135deg, var(--drtc-orange), var(--drtc-dark-orange));
        color: white;
        border-radius: 13px 13px 0 0;
        padding: 20px;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
    }
    
    .form-section {
        background: rgba(255, 248, 230, 0.8);
        border: 1px solid #e67c00;
        border-radius: 10px;
        margin: 15px 0;
        padding: 20px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }
    
    .consultas-tab {
        background: linear-gradient(135deg, #17a2b8, #138496) !important;
        color: white !important;
        border-color: #17a2b8 !important;
    }
    
    .consultas-content {
        background: linear-gradient(135deg, rgba(23, 162, 184, 0.1), rgba(19, 132, 150, 0.1));
        border: 2px solid #17a2b8;
        border-radius: 15px;
    }
    
    /* Modales Flotantes */
    .floating-modal {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1050;
        width: 90%;
        max-width: 1200px;
        max-height: 90vh;
        overflow-y: auto;
        background: rgba(255, 255, 255, 0.98);
        border-radius: 20px;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(10px);
        border: 3px solid var(--drtc-orange);
        display: none;
    }
    
    .modal-backdrop-custom {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        z-index: 1040;
        backdrop-filter: blur(5px);
        display: none;
    }
    
    .action-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin: 20px 0;
    }
    
    .action-card {
        background: linear-gradient(135deg, rgba(255, 140, 0, 0.1), rgba(230, 124, 0, 0.1));
        border: 2px solid var(--drtc-orange);
        border-radius: 15px;
        padding: 25px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(255, 140, 0, 0.3);
        border-color: var(--drtc-dark-orange);
    }
    
    .action-card.nueva { border-color: #28a745; background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(34, 126, 52, 0.1)); }
    .action-card.editar { border-color: #ffc107; background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(224, 168, 0, 0.1)); }
    .action-card.eliminar { border-color: #dc3545; background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(200, 35, 51, 0.1)); }
    .action-card.consultas { border-color: #17a2b8; background: linear-gradient(135deg, rgba(23, 162, 184, 0.1), rgba(19, 132, 150, 0.1)); }
    
    .unified-dashboard {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        min-height: 100vh;
        padding: 20px;
    }
    
    /* Sistema de Modales Flotantes */
    .modal-backdrop-custom {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(5px);
        z-index: 1040;
        display: none;
    }
    
    .floating-modal {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.8);
        background: white;
        border-radius: 15px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        z-index: 1050;
        display: none;
        max-width: 90vw;
        max-height: 90vh;
        overflow-y: auto;
        transition: all 0.3s ease;
    }
    
    .modal-header-custom {
        background: linear-gradient(135deg, var(--drtc-orange), var(--drtc-dark-orange));
        color: white;
        padding: 20px;
        border-radius: 15px 15px 0 0;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        position: relative;
    }
    
    .form-section {
        margin-bottom: 2rem;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .action-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
        margin: 30px 0;
    }
</style>
</style>

<!-- Header Principal DRTC Apurímac -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card drtc-header shadow-lg border-0" style="background: linear-gradient(135deg, var(--drtc-orange), var(--drtc-dark-orange)); color: white; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="drtc-logo">
                            <div class="text-center">
                                <i class="fas fa-road"></i>
                                <div style="font-size: 10px; line-height: 1;">DRTC</div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h1 class="mb-1 fw-bold" style="color: white; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">DIRECCIÓN REGIONAL DE TRANSPORTES</h1>
                                <h2 class="mb-1" style="color: white; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">Y COMUNICACIONES - APURÍMAC</h2>
                                <h4 class="mb-2" style="color: #fffacd; text-shadow: 1px 1px 3px rgba(0,0,0,0.4);">Sistema de Fiscalización de Transporte Terrestre</h4>
                                <div class="d-flex align-items-center" style="color: #ffff99; text-shadow: 1px 1px 2px rgba(0,0,0,0.6);">
                                    <i class="fas fa-user-shield me-2"></i>
                                    <span class="me-3">Fiscalizador: {{ Auth::user()->name }}</span>
                                    <i class="fas fa-calendar me-2"></i>
                                    <span class="me-3">{{ date('d/m/Y') }}</span>
                                    <i class="fas fa-clock me-2"></i>
                                    <span id="hora-header"></span>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="text-center" style="color: white; text-shadow: 2px 2px 4px rgba(0,0,0,0.4);">
                                    <i class="fas fa-clipboard-list fa-4x mb-2" style="opacity: 0.9;"></i>
                                    <div class="h5 mb-0" style="color: white; font-weight: bold;">SISTEMA DE ACTAS</div>
                                    <div class="small" style="color: #fffacd;">Versión 2.0</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Panel de Estadísticas DRTC -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-drtc-orange shadow-lg border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase mb-1">Actas Registradas</h6>
                        <h2 class="mb-0 fw-bold">15</h2>
                        <small class="opacity-75">Hoy</small>
                    </div>
                    <div class="text-end">
                        <i class="fas fa-file-alt fa-3x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-success shadow-lg border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase mb-1">Procesadas</h6>
                        <h2 class="mb-0 fw-bold">12</h2>
                        <small class="opacity-75">Completadas</small>
                    </div>
                    <div class="text-end">
                        <i class="fas fa-check-circle fa-3x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-warning shadow-lg border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase mb-1">Pendientes</h6>
                        <h2 class="mb-0 fw-bold">3</h2>
                        <small class="opacity-75">Por revisar</small>
                    </div>
                    <div class="text-end">
                        <i class="fas fa-clock fa-3x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-danger shadow-lg border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase mb-1">Infracciones</h6>
                        <h2 class="mb-0 fw-bold">8</h2>
                        <small class="opacity-75">Detectadas</small>
                    </div>
                    <div class="text-end">
                        <i class="fas fa-exclamation-triangle fa-3x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Panel Principal del Sistema -->
<div class="card shadow-lg border-drtc-orange">
    <div class="card-header bg-drtc-soft border-drtc-orange p-4">
        <div class="row align-items-center">
            <div class="col-auto">
                <div class="drtc-logo" style="width: 60px; height: 60px; font-size: 18px;">
                    <div class="text-center">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>
            </div>
            <div class="col">
                <h3 class="mb-1 fw-bold" style="color: #2c3e50; text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.8); font-size: 1.75rem;">SISTEMA DE GESTIÓN DE ACTAS DRTC</h3>
                <p class="mb-0" style="color: #34495e; font-weight: 600;">Dirección Regional de Transportes y Comunicaciones - Apurímac</p>
            </div>
        </div>
        
        <!-- Header simplificado -->
        <div class="text-center mb-4">
            <h2 class="text-drtc-orange fw-bold mb-2">
                <i class="fas fa-clipboard-list me-2"></i>SISTEMA UNIFICADO DE GESTIÓN
            </h2>
            <p class="text-muted mb-0">Todas las operaciones de actas en un solo lugar</p>
        </div>
    </div>
    
    <div class="card-body bg-light p-4">
        <!-- Dashboard Unificado -->
        <div class="unified-dashboard rounded-4 p-4">
            <div class="text-center mb-4">
                <h2 class="mb-3" style="color: white; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">
                    <i class="fas fa-clipboard-list me-3"></i>CENTRO DE GESTIÓN DE ACTAS DRTC
                </h2>
                <p class="lead" style="color: #f8f9fa; opacity: 0.9;">Seleccione la acción que desea realizar</p>
            </div>
            
            <!-- Tarjetas de Acción -->
            <div class="action-cards">
                <!-- Nueva Acta -->
                <div class="action-card nueva" onclick="openModal('nueva-acta')">
                    <div class="mb-3">
                        <i class="fas fa-plus-circle fa-4x text-success"></i>
                    </div>
                    <h4 class="text-success fw-bold">NUEVA ACTA</h4>
                    <p class="text-muted">Registrar una nueva acta de fiscalización DRTC</p>
                    <div class="mt-3">
                        <span class="badge bg-success px-3 py-2">Crear Registro</span>
                    </div>
                </div>
                
                <!-- Editar Acta -->
                <div class="action-card editar" onclick="openModal('editar-acta')">
                    <div class="mb-3">
                        <i class="fas fa-edit fa-4x text-warning"></i>
                    </div>
                    <h4 class="text-warning fw-bold">EDITAR ACTA</h4>
                    <p class="text-muted">Modificar información de actas existentes</p>
                    <div class="mt-3">
                        <span class="badge bg-warning px-3 py-2">Modificar</span>
                    </div>
                </div>
                
                <!-- Eliminar Acta -->
                <div class="action-card eliminar" onclick="openModal('eliminar-acta')">
                    <div class="mb-3">
                        <i class="fas fa-trash-alt fa-4x text-danger"></i>
                    </div>
                    <h4 class="text-danger fw-bold">ELIMINAR ACTA</h4>
                    <p class="text-muted">Eliminar actas del sistema (irreversible)</p>
                    <div class="mt-3">
                        <span class="badge bg-danger px-3 py-2">Eliminar</span>
                    </div>
                </div>
                
                <!-- Consultas y Reportes -->
                <div class="action-card consultas" onclick="openModal('consultas-reportes')">
                    <div class="mb-3">
                        <i class="fas fa-chart-line fa-4x text-info"></i>
                    </div>
                    <h4 class="text-info fw-bold">CONSULTAS Y REPORTES</h4>
                    <p class="text-muted">Generar reportes y consultar actas registradas</p>
                    <div class="mt-3">
                        <span class="badge bg-info px-3 py-2">Consultar</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Backdrop para modales -->
<div class="modal-backdrop-custom" id="modal-backdrop" onclick="closeAllModals()"></div>

<!-- MODAL: NUEVA ACTA -->
<div class="floating-modal" id="modal-nueva-acta">
    <div class="modal-header-custom text-center">
        <h3 class="mb-0 fw-bold">
            <i class="fas fa-plus-circle me-2"></i>NUEVA ACTA DE FISCALIZACIÓN DRTC
        </h3>
        <button type="button" class="btn btn-light btn-sm position-absolute" style="top: 15px; right: 15px;" onclick="closeModal('nueva-acta')">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="p-4">
        <form id="form-nueva-acta" action="{{ route('inspecciones.store') }}" method="POST">
                            @csrf
                            
                            <!-- Campos automáticos ocultos -->
                            <input type="hidden" id="fecha_inspeccion_hidden" name="fecha_inspeccion">
                            <input type="hidden" id="hora_inicio_hidden" name="hora_inicio">
                            <input type="hidden" name="inspector_principal" value="{{ Auth::user()->name }}">

                            <!-- SECCIÓN 1: INFORMACIÓN DEL OPERADOR -->
                            <div class="card mb-4 border-drtc-orange form-section">
                                <div class="card-header modal-header-custom">
                                    <h6 class="mb-0 fw-bold"><i class="fas fa-user-tie me-2"></i>I. DATOS DEL OPERADOR/CONDUCTOR</h6>
                                </div>
                                <div class="card-body bg-drtc-soft">
                                    <div class="row">
                                        <div class="col-md-12 mb-4">
                                            <label class="form-label fw-bold text-drtc-orange">Tipo de Agente Infractor:</label>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-check p-3 border border-drtc-orange rounded bg-white">
                                                        <input class="form-check-input" type="radio" name="tipo_agente" id="transportista" value="transportista">
                                                        <label class="form-check-label fw-bold w-100" for="transportista">
                                                            <i class="fas fa-truck me-2 text-drtc-orange"></i>TRANSPORTISTA
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-check p-3 border border-drtc-orange rounded bg-white">
                                                        <input class="form-check-input" type="radio" name="tipo_agente" id="operador_ruta" value="operador_ruta">
                                                        <label class="form-check-label fw-bold w-100" for="operador_ruta">
                                                            <i class="fas fa-route me-2 text-drtc-orange"></i>OPERADOR DE RUTA
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-check p-3 border border-drtc-orange rounded bg-white">
                                                        <input class="form-check-input" type="radio" name="tipo_agente" id="conductor" value="conductor">
                                                        <label class="form-check-label fw-bold w-100" for="conductor">
                                                            <i class="fas fa-id-card me-2 text-drtc-orange"></i>CONDUCTOR
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-drtc-orange">Razón Social/Nombres y Apellidos:</label>
                                            <input type="text" class="form-control border-drtc-orange" name="razon_social" placeholder="Ingrese razón social o nombres completos" required>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-bold text-drtc-orange">RUC/DNI:</label>
                                            <input type="text" class="form-control border-drtc-orange" name="ruc_dni" placeholder="20123456789" required>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-bold text-drtc-orange">Placa del Vehículo:</label>
                                            <input type="text" class="form-control border-drtc-orange" name="placa_1" placeholder="ABC-123" style="text-transform: uppercase;">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-drtc-orange">Nombre del Conductor:</label>
                                            <input type="text" class="form-control border-drtc-orange" name="nombre_conductor_1" placeholder="Nombres y apellidos completos" required>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-bold text-drtc-orange">N° Licencia de Conducir:</label>
                                            <input type="text" class="form-control border-drtc-orange" name="licencia_conductor_1" placeholder="N° Licencia" required>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-bold text-drtc-orange">Clase y Categoría:</label>
                                            <select class="form-select border-drtc-orange" name="clase_categoria">
                                                <option value="">Seleccione...</option>
                                                <option value="A-I">A-I (Motocicletas hasta 125cc)</option>
                                                <option value="A-IIa">A-IIa (Motocicletas de 126cc a 200cc)</option>
                                                <option value="A-IIb">A-IIb (Motocicletas mayor a 200cc)</option>
                                                <option value="A-IIIa">A-IIIa (Vehículos menores)</option>
                                                <option value="A-IIIb">A-IIIb (Automóviles, camionetas)</option>
                                                <option value="A-IIIc">A-IIIc (Buses, camiones)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SECCIÓN 2: DATOS DE LA INTERVENCIÓN -->
                            <div class="card mb-4 border-drtc-orange form-section">
                                <div class="card-header modal-header-custom">
                                    <h6 class="mb-0 fw-bold"><i class="fas fa-map-marker-alt me-2"></i>II. DATOS DE LA INTERVENCIÓN</h6>
                                </div>
                                <div class="card-body bg-drtc-soft">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label fw-bold text-drtc-orange">Fecha de Intervención:</label>
                                            <div class="form-control bg-white border-drtc-orange fw-bold text-drtc-orange" id="fecha-actual-nueva"></div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label fw-bold text-drtc-orange">Hora de Inicio:</label>
                                            <div class="form-control bg-white border-drtc-orange fw-bold text-drtc-orange" id="hora-actual-nueva"></div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label fw-bold text-drtc-orange">Fecha y Hora de Fin:</label>
                                            <input type="datetime-local" class="form-control border-drtc-orange" name="fecha_hora_fin">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label fw-bold text-drtc-orange">Lugar de la Intervención:</label>
                                            <input type="text" class="form-control border-drtc-orange" name="lugar_intervencion" placeholder="Dirección exacta, referencias, distrito, provincia - Apurímac" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-drtc-orange">Origen del Viaje:</label>
                                            <input type="text" class="form-control border-drtc-orange" name="origen_viaje" placeholder="Ciudad/localidad de origen" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-drtc-orange">Destino del Viaje:</label>
                                            <input type="text" class="form-control border-drtc-orange" name="destino_viaje" placeholder="Ciudad/localidad de destino" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-drtc-orange">Tipo de Servicio:</label>
                                            <div class="d-flex gap-3 mt-2">
                                                <div class="form-check p-3 border border-drtc-orange rounded bg-white flex-fill">
                                                    <input class="form-check-input" type="radio" name="tipo_servicio" id="personas_nueva" value="personas">
                                                    <label class="form-check-label fw-bold w-100" for="personas_nueva">
                                                        <i class="fas fa-users me-2 text-drtc-orange"></i>TRANSPORTE DE PERSONAS
                                                    </label>
                                                </div>
                                                <div class="form-check p-3 border border-drtc-orange rounded bg-white flex-fill">
                                                    <input class="form-check-input" type="radio" name="tipo_servicio" id="mercancia_nueva" value="mercancia">
                                                    <label class="form-check-label fw-bold w-100" for="mercancia_nueva">
                                                        <i class="fas fa-boxes me-2 text-drtc-orange"></i>TRANSPORTE DE MERCANCÍA
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-drtc-orange">Km. en Red Vial (opcional):</label>
                                            <input type="text" class="form-control border-drtc-orange" name="km_red_vial" placeholder="Ej: Km 45+500">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SECCIÓN 3: DESCRIPCIÓN DE LOS HECHOS -->
                            <div class="card mb-4 border-warning form-section" style="background: rgba(255, 243, 205, 0.9);">
                                <div class="card-header" style="background: linear-gradient(135deg, #ffc107, #e0a800); color: #333; border-radius: 8px 8px 0 0;">
                                    <h6 class="mb-0 fw-bold"><i class="fas fa-file-text me-2"></i>III. DESCRIPCIÓN DETALLADA DE LOS HECHOS</h6>
                                </div>
                                <div class="card-body bg-drtc-soft">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-drtc-orange">Relato detallado de la intervención:</label>
                                        <textarea class="form-control border-drtc-orange" name="descripcion_hechos" rows="6" 
                                            placeholder="Describa detalladamente: fecha, hora, lugar, circunstancias, infracciones detectadas, actitud del conductor/operador, condiciones del vehículo, documentación revisada, etc." required></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- SECCIÓN 4: MEDIDAS LEGALES Y ADMINISTRATIVAS -->
                            <div class="card mb-4 border-danger form-section" style="background: rgba(255, 235, 238, 0.9);">
                                <div class="card-header" style="background: linear-gradient(135deg, #dc3545, #c82333); color: white; border-radius: 8px 8px 0 0; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">
                                    <h6 class="mb-0 fw-bold"><i class="fas fa-gavel me-2"></i>IV. MEDIDAS LEGALES Y ADMINISTRATIVAS</h6>
                                </div>
                                <div class="card-body bg-drtc-soft">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-drtc-orange">Medios Probatorios:</label>
                                            <textarea class="form-control border-drtc-orange" name="medios_probatorios" rows="4" 
                                                placeholder="Ej: Fotografías, videos, testimonios, documentos incautados, actas adicionales, etc."></textarea>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-drtc-orange">Calificación de la Infracción:</label>
                                            <select class="form-select border-drtc-orange" name="calificacion_infraccion">
                                                <option value="">Seleccione el nivel de gravedad...</option>
                                                <option value="leve">🟡 LEVE - Sanciones menores</option>
                                                <option value="grave">🟠 GRAVE - Sanciones intermedias</option>
                                                <option value="muy_grave">🔴 MUY GRAVE - Sanciones severas</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-drtc-orange">Medida(s) Administrativa(s) Aplicada(s):</label>
                                            <textarea class="form-control border-drtc-orange" name="medidas_administrativas" rows="4" 
                                                placeholder="Ej: Retención de licencia, internamiento del vehículo, suspensión temporal de autorización, decomiso de documentos, etc."></textarea>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-drtc-orange">Sanción Aplicada:</label>
                                            <textarea class="form-control border-drtc-orange" name="sancion" rows="4" 
                                                placeholder="Detalle la sanción económica, puntos en licencia, suspensión de autorización, etc."></textarea>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-drtc-orange">Observaciones del Intervenido:</label>
                                            <textarea class="form-control border-drtc-orange" name="observaciones_intervenido" rows="4" 
                                                placeholder="Declaraciones, justificaciones o descargos del conductor/operador/transportista"></textarea>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-drtc-orange">Observaciones del Inspector DRTC:</label>
                                            <textarea class="form-control border-drtc-orange" name="observaciones_inspector" rows="4" 
                                                placeholder="Comentarios adicionales del fiscalizador, recomendaciones, seguimiento requerido, etc."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones de Acción -->
                            <div class="text-center py-4">
                                <button type="submit" class="btn btn-drtc-orange btn-lg px-5 me-3 shadow">
                                    <i class="fas fa-save me-2"></i>GUARDAR ACTA DE FISCALIZACIÓN DRTC
                                </button>
                                <button type="reset" class="btn btn-outline-secondary btn-lg px-5 shadow">
                                    <i class="fas fa-undo me-2"></i>LIMPIAR FORMULARIO
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- PESTAÑA: EDITAR ACTA -->
            <div class="tab-pane fade" id="nav-editar" role="tabpanel">
                <div class="card border-0 shadow modal-form">
                    <div class="card-header" style="background: linear-gradient(135deg, #ffc107, #e0a800); color: #333; border-radius: 13px 13px 0 0; padding: 20px;">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-edit me-2"></i>EDITAR ACTA DE FISCALIZACIÓN DRTC</h5>
                    </div>
                    <div class="card-body">
                        <!-- Buscador de acta para editar -->
                        <div class="card mb-4 border-drtc-orange">
                            <div class="card-header bg-drtc-light text-dark">
                                <h6 class="mb-0 fw-bold"><i class="fas fa-search me-2"></i>LOCALIZAR ACTA PARA MODIFICAR</h6>
                            </div>
                            <div class="card-body bg-drtc-soft">
                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label class="form-label fw-bold text-drtc-orange">Buscar por N° de Acta, RUC/DNI o Placa:</label>
                                        <input type="text" class="form-control border-drtc-orange" id="buscar-acta-editar" 
                                            placeholder="Ingrese número de acta DRTC, RUC/DNI del operador o placa del vehículo">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="button" class="btn btn-drtc-orange d-block w-100" id="btn-buscar-editar">
                                            <i class="fas fa-search me-2"></i>BUSCAR ACTA DRTC
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Resultado de edición -->
                        <div id="form-editar-container" style="display: none;">
                            <div class="alert alert-warning border-drtc-orange">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>EDITANDO ACTA DRTC N°:</strong> <span id="acta-numero-editar" class="text-drtc-orange fw-bold"></span>
                            </div>
                            
                            <div class="card border-drtc-orange">
                                <div class="card-body bg-drtc-soft">
                                    <p class="text-center text-muted">
                                        <i class="fas fa-tools fa-2x mb-3 text-drtc-orange"></i><br>
                                        <strong>Formulario de edición cargará aquí</strong><br>
                                        <small>Los campos serán precargados con los datos existentes de la base de datos</small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PESTAÑA: ELIMINAR ACTA -->
            <div class="tab-pane fade" id="nav-eliminar" role="tabpanel">
                <div class="card border-0 shadow modal-form" style="border-color: #dc3545 !important;">
                    <div class="card-header" style="background: linear-gradient(135deg, #dc3545, #c82333); color: white; border-radius: 13px 13px 0 0; padding: 20px; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-trash-alt me-2"></i>ELIMINAR ACTA DE FISCALIZACIÓN DRTC</h5>
                    </div>
                    <div class="card-body">
                        <!-- Advertencia de seguridad -->
                        <div class="alert alert-danger border-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>ADVERTENCIA CRÍTICA:</strong> La eliminación de actas es una acción IRREVERSIBLE. 
                            Solo proceda si está completamente seguro y tiene autorización administrativa.
                        </div>
                        
                        <!-- Buscador -->
                        <div class="card mb-4 border-danger">
                            <div class="card-header bg-light border-danger">
                                <h6 class="mb-0 text-danger fw-bold"><i class="fas fa-search me-2"></i>LOCALIZAR ACTA PARA ELIMINAR</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label class="form-label fw-bold text-danger">Buscar por N° de Acta, RUC/DNI o Placa:</label>
                                        <input type="text" class="form-control border-danger" id="buscar-acta-eliminar" 
                                            placeholder="Ingrese número de acta DRTC, RUC/DNI del operador o placa del vehículo">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="button" class="btn btn-danger d-block w-100" id="btn-buscar-eliminar">
                                            <i class="fas fa-search me-2"></i>BUSCAR ACTA DRTC
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Resultado para eliminar -->
                        <div id="resultado-eliminar" style="display: none;">
                            <div class="card border-danger">
                                <div class="card-body">
                                    <div id="datos-acta-eliminar" class="bg-light p-4 rounded border">
                                        <!-- Datos de la acta se cargarán aquí -->
                                    </div>
                                    <div class="text-center mt-4">
                                        <button type="button" class="btn btn-danger btn-lg px-5 me-3" id="btn-confirmar-eliminar">
                                            <i class="fas fa-trash me-2"></i>CONFIRMAR ELIMINACIÓN
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-lg px-5" id="btn-cancelar-eliminar">
                                            <i class="fas fa-times me-2"></i>CANCELAR OPERACIÓN
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PESTAÑA: CONSULTAS Y REPORTES -->
            <div class="tab-pane fade" id="nav-consulta" role="tabpanel">
                <div class="card border-0 shadow modal-form consultas-content">
                    <div class="card-header consultas-tab" style="border-radius: 13px 13px 0 0; padding: 20px;">
                        <div class="row align-items-center text-white">
                            <div class="col">
                                <h5 class="mb-0 fw-bold"><i class="fas fa-chart-line me-2"></i>CONSULTAS Y REPORTES DRTC APURÍMAC</h5>
                                <small>Sistema Integrado de Consultas y Generación de Reportes</small>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-download fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        
                        <!-- Filtros de búsqueda y criterios de reporte -->
                        <div class="card mb-4 border-info">
                            <div class="card-header bg-light border-info">
                                <h6 class="mb-0 text-info fw-bold">
                                    <i class="fas fa-filter me-2"></i>FILTROS DE CONSULTA Y CRITERIOS DE REPORTE
                                </h6>
                            </div>
                            <div class="card-body bg-light">
                                <form id="form-consulta-reportes">
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-bold text-info">N° de Acta DRTC:</label>
                                            <input type="text" class="form-control border-info" name="numero_acta" placeholder="DRTC-APU-2024-001">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-bold text-info">RUC/DNI del Operador:</label>
                                            <input type="text" class="form-control border-info" name="ruc_dni" placeholder="20123456789">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-bold text-info">Placa del Vehículo:</label>
                                            <input type="text" class="form-control border-info" name="placa" placeholder="ABC-123">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-bold text-info">Tipo de Agente:</label>
                                            <select class="form-select border-info" name="tipo_agente">
                                                <option value="">Todos</option>
                                                <option value="transportista">Transportista</option>
                                                <option value="operador_ruta">Operador de Ruta</option>
                                                <option value="conductor">Conductor</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!-- Filtros de fecha y rango -->
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-bold text-info">Fecha Desde:</label>
                                            <input type="date" class="form-control border-info" name="fecha_desde">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-bold text-info">Fecha Hasta:</label>
                                            <input type="date" class="form-control border-info" name="fecha_hasta">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-bold text-info">Calificación de Infracción:</label>
                                            <select class="form-select border-info" name="calificacion">
                                                <option value="">Todas</option>
                                                <option value="leve">🟡 Leve</option>
                                                <option value="grave">🟠 Grave</option>
                                                <option value="muy_grave">🔴 Muy Grave</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-bold text-info">Estado del Acta:</label>
                                            <select class="form-select border-info" name="estado">
                                                <option value="">Todos</option>
                                                <option value="activa">✅ Activa</option>
                                                <option value="procesada">🔄 Procesada</option>
                                                <option value="anulada">❌ Anulada</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!-- Botones de acción -->
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                                <button type="submit" class="btn btn-info btn-lg px-4">
                                                    <i class="fas fa-search me-2"></i>CONSULTAR ACTAS
                                                </button>
                                                <button type="button" class="btn btn-success btn-lg px-4" id="btn-generar-excel">
                                                    <i class="fas fa-file-excel me-2"></i>GENERAR EXCEL
                                                </button>
                                                <button type="button" class="btn btn-danger btn-lg px-4" id="btn-generar-pdf">
                                                    <i class="fas fa-file-pdf me-2"></i>GENERAR PDF
                                                </button>
                                                <button type="button" class="btn btn-warning btn-lg px-4" id="btn-reporte-completo">
                                                    <i class="fas fa-chart-bar me-2"></i>REPORTE COMPLETO
                                                </button>
                                                <button type="reset" class="btn btn-outline-secondary btn-lg px-4">
                                                    <i class="fas fa-eraser me-2"></i>LIMPIAR
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Estadísticas del filtro actual -->
                        <div class="row mb-4" id="estadisticas-filtro" style="display: none;">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white text-center">
                                    <div class="card-body py-3">
                                        <i class="fas fa-file-alt fa-2x mb-2"></i>
                                        <h4 class="mb-0" id="total-actas">0</h4>
                                        <small>Total Actas</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white text-center">
                                    <div class="card-body py-3">
                                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                                        <h4 class="mb-0" id="actas-procesadas">0</h4>
                                        <small>Procesadas</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white text-center">
                                    <div class="card-body py-3">
                                        <i class="fas fa-clock fa-2x mb-2"></i>
                                        <h4 class="mb-0" id="actas-pendientes">0</h4>
                                        <small>Pendientes</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white text-center">
                                    <div class="card-body py-3">
                                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                        <h4 class="mb-0" id="infracciones-graves">0</h4>
                                        <small>Muy Graves</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Resultados de consulta -->
                        <div class="card border-drtc-orange">
                            <div class="card-header bg-drtc-orange text-white">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="mb-0 fw-bold"><i class="fas fa-list me-2"></i>RESULTADOS DE CONSULTA - ACTAS DRTC APURÍMAC</h6>
                                    </div>
                                    <div class="col-auto">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-light btn-sm" id="btn-exportar-seleccionadas">
                                                <i class="fas fa-download me-1"></i>Exportar Seleccionadas
                                            </button>
                                            <button type="button" class="btn btn-light btn-sm" id="btn-seleccionar-todas">
                                                <i class="fas fa-check-square me-1"></i>Seleccionar Todas
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="bg-drtc-dark text-white">
                                            <tr>
                                                <th class="fw-bold">
                                                    <input type="checkbox" id="check-all" class="form-check-input">
                                                </th>
                                                <th class="fw-bold">N° ACTA DRTC</th>
                                                <th class="fw-bold">FECHA</th>
                                                <th class="fw-bold">OPERADOR</th>
                                                <th class="fw-bold">PLACA</th>
                                                <th class="fw-bold">LUGAR</th>
                                                <th class="fw-bold">INFRACCIÓN</th>
                                                <th class="fw-bold">ESTADO</th>
                                                <th class="fw-bold">ACCIONES</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tabla-resultados-consulta">
                                            <tr>
                                                <td colspan="9" class="text-center text-muted py-5">
                                                    <div class="drtc-logo mx-auto mb-3" style="width: 80px; height: 80px;">
                                                        <div class="text-center">
                                                            <i class="fas fa-search"></i>
                                                            <div style="font-size: 10px; line-height: 1;">DRTC</div>
                                                        </div>
                                                    </div>
                                                    <strong>Utilice los filtros de búsqueda para consultar las actas DRTC</strong><br>
                                                    <small>Sistema de Fiscalización - Dirección Regional de Transportes Apurímac</small><br>
                                                    <small class="text-info">💡 Tip: Puede generar reportes PDF/Excel directamente sin realizar consulta previa</small>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- Paginación -->
                                <nav aria-label="Paginación" id="paginacion-resultados" style="display: none;">
                                    <ul class="pagination justify-content-center">
                                        <li class="page-item disabled">
                                            <span class="page-link">Anterior</span>
                                        </li>
                                        <li class="page-item active">
                                            <span class="page-link">1</span>
                                        </li>
                                        <li class="page-item">
                                            <a class="page-link" href="#">2</a>
                                        </li>
                                        <li class="page-item">
                                            <a class="page-link" href="#">3</a>
                                        </li>
                                        <li class="page-item">
                                            <a class="page-link" href="#">Siguiente</a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>

                        <!-- Panel de opciones de exportación -->
                        <div class="card mt-4 border-success" id="panel-exportacion" style="display: none;">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0 fw-bold"><i class="fas fa-download me-2"></i>OPCIONES DE EXPORTACIÓN Y DESCARGA</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="card h-100 border-danger">
                                            <div class="card-body text-center">
                                                <i class="fas fa-file-pdf fa-3x text-danger mb-3"></i>
                                                <h6 class="card-title">REPORTE PDF</h6>
                                                <p class="card-text small">Generar documento PDF profesional con formato oficial DRTC</p>
                                                <div class="d-grid gap-2">
                                                    <button class="btn btn-danger btn-sm" id="pdf-individual">
                                                        <i class="fas fa-file me-1"></i>PDF Individual
                                                    </button>
                                                    <button class="btn btn-outline-danger btn-sm" id="pdf-consolidado">
                                                        <i class="fas fa-files me-1"></i>PDF Consolidado
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card h-100 border-success">
                                            <div class="card-body text-center">
                                                <i class="fas fa-file-excel fa-3x text-success mb-3"></i>
                                                <h6 class="card-title">REPORTE EXCEL</h6>
                                                <p class="card-text small">Exportar datos a hoja de cálculo para análisis estadístico</p>
                                                <div class="d-grid gap-2">
                                                    <button class="btn btn-success btn-sm" id="excel-detallado">
                                                        <i class="fas fa-table me-1"></i>Excel Detallado
                                                    </button>
                                                    <button class="btn btn-outline-success btn-sm" id="excel-resumen">
                                                        <i class="fas fa-chart-bar me-1"></i>Excel Resumen
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card h-100 border-warning">
                                            <div class="card-body text-center">
                                                <i class="fas fa-chart-line fa-3x text-warning mb-3"></i>
                                                <h6 class="card-title">REPORTES ESPECIALES</h6>
                                                <p class="card-text small">Reportes estadísticos y análisis avanzados</p>
                                                <div class="d-grid gap-2">
                                                    <button class="btn btn-warning btn-sm" id="reporte-estadistico">
                                                        <i class="fas fa-chart-pie me-1"></i>Estadístico
                                                    </button>
                                                    <button class="btn btn-outline-warning btn-sm" id="reporte-mensual">
                                                        <i class="fas fa-calendar me-1"></i>Mensual
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Backdrop para modales -->
<div class="modal-backdrop-custom" id="modal-backdrop" onclick="closeAllModals()"></div>

<!-- MODAL: NUEVA ACTA -->
<div class="floating-modal" id="modal-nueva-acta">
    <div class="modal-header-custom text-center">
        <h3 class="mb-0 fw-bold">
            <i class="fas fa-plus-circle me-2"></i>NUEVA ACTA DE FISCALIZACIÓN DRTC
        </h3>
        <button type="button" class="btn btn-light btn-sm position-absolute" style="top: 15px; right: 15px;" onclick="closeModal('nueva-acta')">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="p-4">
        <form id="form-nueva-acta" action="{{ route('inspecciones.store') }}" method="POST">
            @csrf
            
            <!-- Información Básica -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold text-drtc-orange">Razón Social/Nombres:</label>
                    <input type="text" class="form-control border-drtc-orange" name="razon_social" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-drtc-orange">RUC/DNI:</label>
                    <input type="text" class="form-control border-drtc-orange" name="ruc_dni" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-drtc-orange">Placa:</label>
                    <input type="text" class="form-control border-drtc-orange" name="placa" style="text-transform: uppercase;">
                </div>
            </div>
            
            <!-- Descripción -->
            <div class="mb-4">
                <label class="form-label fw-bold text-drtc-orange">Descripción de los Hechos:</label>
                <textarea class="form-control border-drtc-orange" name="descripcion_hechos" rows="4" required></textarea>
            </div>
            
            <!-- Botones -->
            <div class="text-center">
                <button type="submit" class="btn btn-success btn-lg px-5 me-3">
                    <i class="fas fa-save me-2"></i>GUARDAR ACTA
                </button>
                <button type="button" class="btn btn-secondary btn-lg px-5" onclick="closeModal('nueva-acta')">
                    <i class="fas fa-times me-2"></i>CANCELAR
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: EDITAR ACTA -->
<div class="floating-modal" id="modal-editar-acta">
    <div class="modal-header-custom text-center" style="background: linear-gradient(135deg, #ffc107, #e0a800);">
        <h3 class="mb-0 fw-bold text-dark">
            <i class="fas fa-edit me-2"></i>EDITAR ACTA DRTC
        </h3>
        <button type="button" class="btn btn-dark btn-sm position-absolute" style="top: 15px; right: 15px;" onclick="closeModal('editar-acta')">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="p-4">
        <!-- Buscador -->
        <div class="row mb-4">
            <div class="col-md-8">
                <label class="form-label fw-bold text-warning">Buscar Acta:</label>
                <input type="text" class="form-control border-warning" id="buscar-editar" placeholder="N° Acta, RUC/DNI o Placa">
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-warning w-100" onclick="buscarActaEditar()">
                    <i class="fas fa-search me-2"></i>BUSCAR
                </button>
            </div>
        </div>
        
        <!-- Resultado -->
        <div id="resultado-editar" style="display: none;">
            <div class="alert alert-warning">
                <strong>Acta encontrada. Formulario de edición cargará aquí.</strong>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: ELIMINAR ACTA -->
<div class="floating-modal" id="modal-eliminar-acta">
    <div class="modal-header-custom text-center" style="background: linear-gradient(135deg, #dc3545, #c82333);">
        <h3 class="mb-0 fw-bold">
            <i class="fas fa-trash-alt me-2"></i>ELIMINAR ACTA DRTC
        </h3>
        <button type="button" class="btn btn-light btn-sm position-absolute" style="top: 15px; right: 15px;" onclick="closeModal('eliminar-acta')">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="p-4">
        <!-- Advertencia -->
        <div class="alert alert-danger text-center mb-4">
            <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
            <h5>⚠️ ADVERTENCIA CRÍTICA</h5>
            <p class="mb-0">Esta acción es IRREVERSIBLE</p>
        </div>
        
        <!-- Buscador -->
        <div class="row mb-4">
            <div class="col-md-8">
                <label class="form-label fw-bold text-danger">Buscar Acta a Eliminar:</label>
                <input type="text" class="form-control border-danger" id="buscar-eliminar" placeholder="N° Acta, RUC/DNI o Placa">
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-danger w-100" onclick="buscarActaEliminar()">
                    <i class="fas fa-search me-2"></i>BUSCAR
                </button>
            </div>
        </div>
        
        <!-- Resultado -->
        <div id="resultado-eliminar" style="display: none;">
            <div class="text-center">
                <button type="button" class="btn btn-danger btn-lg px-5 me-3">
                    <i class="fas fa-trash me-2"></i>CONFIRMAR ELIMINACIÓN
                </button>
                <button type="button" class="btn btn-secondary btn-lg px-5" onclick="closeModal('eliminar-acta')">
                    <i class="fas fa-times me-2"></i>CANCELAR
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: CONSULTAS Y REPORTES -->
<div class="floating-modal" id="modal-consultas-reportes" style="max-width: 1400px;">
    <div class="modal-header-custom text-center" style="background: linear-gradient(135deg, #17a2b8, #138496);">
        <h3 class="mb-0 fw-bold">
            <i class="fas fa-chart-line me-2"></i>CONSULTAS Y REPORTES DRTC
        </h3>
        <button type="button" class="btn btn-light btn-sm position-absolute" style="top: 15px; right: 15px;" onclick="closeModal('consultas-reportes')">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="p-4">
        <!-- Filtros -->
        <div class="row mb-4">
            <div class="col-md-3">
                <label class="form-label fw-bold text-info">N° Acta:</label>
                <input type="text" class="form-control border-info" placeholder="DRTC-APU-2024-001">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold text-info">RUC/DNI:</label>
                <input type="text" class="form-control border-info" placeholder="20123456789">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold text-info">Fecha Desde:</label>
                <input type="date" class="form-control border-info">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold text-info">Fecha Hasta:</label>
                <input type="date" class="form-control border-info">
            </div>
        </div>
        
        <!-- Botones de Acción -->
        <div class="text-center mb-4">
            <button type="button" class="btn btn-info btn-lg px-4 me-2">
                <i class="fas fa-search me-2"></i>CONSULTAR
            </button>
            <button type="button" class="btn btn-success btn-lg px-4 me-2">
                <i class="fas fa-file-excel me-2"></i>EXCEL
            </button>
            <button type="button" class="btn btn-danger btn-lg px-4">
                <i class="fas fa-file-pdf me-2"></i>PDF
            </button>
        </div>
        
        <!-- Tabla de Resultados -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="bg-info text-white">
                    <tr>
                        <th>N° ACTA</th>
                        <th>FECHA</th>
                        <th>OPERADOR</th>
                        <th>PLACA</th>
                        <th>ESTADO</th>
                        <th>ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="fas fa-search fa-2x mb-2"></i><br>
                            Utilice los filtros para consultar actas
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para actualizar fecha y hora en tiempo real
    function actualizarFechaHora() {
        const ahora = new Date();
        const hora = ahora.toLocaleTimeString('es-PE', { 
            hour12: false,
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        
        // Actualizar header
        const horaHeader = document.getElementById('hora-header');
        if (horaHeader) horaHeader.textContent = hora;
    }

    // Actualizar cada segundo
    actualizarFechaHora();
    setInterval(actualizarFechaHora, 1000);
    
    // Añadir efectos de hover a las tarjetas
    document.querySelectorAll('.action-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
});

// Funciones para manejar modales
function openModal(modalType) {
    const backdrop = document.getElementById('modal-backdrop');
    const modal = document.getElementById('modal-' + modalType);
    
    backdrop.style.display = 'block';
    modal.style.display = 'block';
    
    // Animación de entrada
    setTimeout(() => {
        backdrop.style.opacity = '1';
        modal.style.opacity = '1';
        modal.style.transform = 'translate(-50%, -50%) scale(1)';
    }, 10);
    
    // Efecto de sonido (opcional)
    playNotificationSound();
}

function closeModal(modalType) {
    const backdrop = document.getElementById('modal-backdrop');
    const modal = document.getElementById('modal-' + modalType);
    
    modal.style.transform = 'translate(-50%, -50%) scale(0.9)';
    modal.style.opacity = '0';
    backdrop.style.opacity = '0';
    
    setTimeout(() => {
        backdrop.style.display = 'none';
        modal.style.display = 'none';
    }, 300);
}

function closeAllModals() {
    const modals = ['nueva-acta', 'editar-acta', 'eliminar-acta', 'consultas-reportes'];
    modals.forEach(modal => {
        const modalElement = document.getElementById('modal-' + modal);
        if (modalElement.style.display === 'block') {
            closeModal(modal);
        }
    });
}

// Funciones específicas de cada modal
function buscarActaEditar() {
    const criterio = document.getElementById('buscar-editar').value;
    if (!criterio) {
        alert('Ingrese un criterio de búsqueda');
        return;
    }
    
    // Simular búsqueda
    setTimeout(() => {
        document.getElementById('resultado-editar').style.display = 'block';
        alert('✅ Acta encontrada: DRTC-APU-2024-001');
    }, 1000);
}

function buscarActaEliminar() {
    const criterio = document.getElementById('buscar-eliminar').value;
    if (!criterio) {
        alert('Ingrese un criterio de búsqueda');
        return;
    }
    
    // Simular búsqueda
    setTimeout(() => {
        document.getElementById('resultado-eliminar').style.display = 'block';
        alert('⚠️ Acta encontrada. Proceda con precaución.');
    }, 1000);
}

function playNotificationSound() {
    // Efecto de sonido opcional usando Web Audio API
    try {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
        oscillator.frequency.setValueAtTime(600, audioContext.currentTime + 0.1);
        
        gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
        
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.3);
    } catch (e) {
        // Navegador no soporta Web Audio API
    }
}

// Manejo del formulario de nueva acta
document.getElementById('form-nueva-acta')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    alert('✅ Nueva Acta DRTC guardada correctamente\n\nN° de Acta: DRTC-APU-2024-' + String(Math.floor(Math.random() * 1000)).padStart(3, '0'));
    this.reset();
    closeModal('nueva-acta');
});

// Atajos de teclado
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAllModals();
    }
    
    // Atajos para abrir modales
    if (e.ctrlKey) {
        switch(e.key) {
            case '1':
                e.preventDefault();
                openModal('nueva-acta');
                break;
            case '2':
                e.preventDefault();
                openModal('editar-acta');
                break;
            case '3':
                e.preventDefault();
                openModal('eliminar-acta');
                break;
            case '4':
                e.preventDefault();
                openModal('consultas-reportes');
                break;
        }
    }
});
</script>

    // Función para buscar acta para editar
    document.getElementById('btn-buscar-editar')?.addEventListener('click', function() {
        const criterio = document.getElementById('buscar-acta-editar').value;
        if (!criterio) {
            alert('Por favor ingrese un criterio de búsqueda (N° de acta, RUC/DNI o placa)');
            return;
        }
        
        // Mostrar loading
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>BUSCANDO EN DRTC...';
        this.disabled = true;
        
        // Simular búsqueda
        setTimeout(() => {
            document.getElementById('form-editar-container').style.display = 'block';
            document.getElementById('acta-numero-editar').textContent = 'DRTC-APU-2024-001';
            
            // Restaurar botón
            this.innerHTML = '<i class="fas fa-search me-2"></i>BUSCAR ACTA DRTC';
            this.disabled = false;
            
            // Scroll hacia el formulario
            document.getElementById('form-editar-container').scrollIntoView({ behavior: 'smooth' });
        }, 1500);
    });

    // Función para buscar acta para eliminar
    document.getElementById('btn-buscar-eliminar')?.addEventListener('click', function() {
        const criterio = document.getElementById('buscar-acta-eliminar').value;
        if (!criterio) {
            alert('Por favor ingrese un criterio de búsqueda (N° de acta, RUC/DNI o placa)');
            return;
        }
        
        // Mostrar loading
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>BUSCANDO EN DRTC...';
        this.disabled = true;
        
        // Simular búsqueda
        setTimeout(() => {
            document.getElementById('resultado-eliminar').style.display = 'block';
            document.getElementById('datos-acta-eliminar').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <strong class="text-danger">N° ACTA DRTC:</strong> DRTC-APU-2024-002<br>
                        <strong class="text-danger">FECHA:</strong> 15/01/2024 - 14:30 hrs<br>
                        <strong class="text-danger">RUC/DNI:</strong> 10456789123<br>
                        <strong class="text-danger">PLACA:</strong> APU-456
                    </div>
                    <div class="col-md-6">
                        <strong class="text-danger">OPERADOR:</strong> JUAN TORRES MAMANI<br>
                        <strong class="text-danger">CONDUCTOR:</strong> PEDRO GARCÍA LÓPEZ<br>
                        <strong class="text-danger">LUGAR:</strong> AV. DÍAZ BÁRCENAS, ABANCAY<br>
                        <strong class="text-danger">INFRACCIÓN:</strong> Muy Grave
                    </div>
                </div>
            `;
            
            // Restaurar botón
            this.innerHTML = '<i class="fas fa-search me-2"></i>BUSCAR ACTA DRTC';
            this.disabled = false;
            
            // Scroll hacia el resultado
            document.getElementById('resultado-eliminar').scrollIntoView({ behavior: 'smooth' });
        }, 1500);
    });

    // Cancelar eliminación
    document.getElementById('btn-cancelar-eliminar')?.addEventListener('click', function() {
        document.getElementById('resultado-eliminar').style.display = 'none';
        document.getElementById('buscar-acta-eliminar').value = '';
    });

    // Confirmar eliminación
    document.getElementById('btn-confirmar-eliminar')?.addEventListener('click', function() {
        if (confirm('¿Está COMPLETAMENTE SEGURO de que desea eliminar esta acta DRTC?\n\nEsta acción es IRREVERSIBLE y eliminará permanentemente:\n- El registro del sistema DRTC\n- Los datos de fiscalización\n- El historial asociado\n\n¿Confirma la eliminación?')) {
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>ELIMINANDO...';
            this.disabled = true;
            
            setTimeout(() => {
                alert('✅ Acta DRTC eliminada correctamente del sistema');
                document.getElementById('resultado-eliminar').style.display = 'none';
                document.getElementById('buscar-acta-eliminar').value = '';
                this.innerHTML = '<i class="fas fa-trash me-2"></i>CONFIRMAR ELIMINACIÓN';
                this.disabled = false;
            }, 2000);
        }
    });

    // Búsqueda de actas con filtros y generación de reportes
    document.getElementById('form-consulta-reportes')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Mostrar estadísticas del filtro
        document.getElementById('estadisticas-filtro').style.display = 'block';
        document.getElementById('total-actas').textContent = '15';
        document.getElementById('actas-procesadas').textContent = '12';
        document.getElementById('actas-pendientes').textContent = '3';
        document.getElementById('infracciones-graves').textContent = '8';
        
        // Simular resultados de búsqueda del DRTC
        const tbody = document.getElementById('tabla-resultados-consulta');
        tbody.innerHTML = `
            <tr>
                <td><input type="checkbox" class="form-check-input row-checkbox" data-acta="DRTC-APU-2024-001"></td>
                <td><strong>DRTC-APU-2024-001</strong></td>
                <td>15/01/2024 - 14:30</td>
                <td>TRANSPORTES ANDINOS APURÍMAC S.A.C.</td>
                <td>APU-123</td>
                <td>CARRETERA ABANCAY-CUSCO KM 25</td>
                <td><span class="badge bg-danger">🔴 Muy Grave</span></td>
                <td><span class="badge bg-success">✅ Activa</span></td>
                <td>
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-info" title="Ver Detalle">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" title="Descargar PDF">
                            <i class="fas fa-file-pdf"></i>
                        </button>
                        <button class="btn btn-sm btn-success" title="Exportar Excel">
                            <i class="fas fa-file-excel"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td><input type="checkbox" class="form-check-input row-checkbox" data-acta="DRTC-APU-2024-002"></td>
                <td><strong>DRTC-APU-2024-002</strong></td>
                <td>16/01/2024 - 09:15</td>
                <td>JUAN TORRES MAMANI</td>
                <td>APU-456</td>
                <td>AV. DÍAZ BÁRCENAS, ABANCAY</td>
                <td><span class="badge bg-warning">🟠 Grave</span></td>
                <td><span class="badge bg-warning">🔄 Procesada</span></td>
                <td>
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-info" title="Ver Detalle">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" title="Descargar PDF">
                            <i class="fas fa-file-pdf"></i>
                        </button>
                        <button class="btn btn-sm btn-success" title="Exportar Excel">
                            <i class="fas fa-file-excel"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <tr>
                <td><input type="checkbox" class="form-check-input row-checkbox" data-acta="DRTC-APU-2024-003"></td>
                <td><strong>DRTC-APU-2024-003</strong></td>
                <td>17/01/2024 - 16:45</td>
                <td>EMPRESA MOLINA TRANSPORT E.I.R.L.</td>
                <td>APU-789</td>
                <td>TERMINAL TERRESTRE ABANCAY</td>
                <td><span class="badge bg-success">🟡 Leve</span></td>
                <td><span class="badge bg-success">✅ Activa</span></td>
                <td>
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-info" title="Ver Detalle">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" title="Descargar PDF">
                            <i class="fas fa-file-pdf"></i>
                        </button>
                        <button class="btn btn-sm btn-success" title="Exportar Excel">
                            <i class="fas fa-file-excel"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        
        // Mostrar paginación y panel de exportación
        document.getElementById('paginacion-resultados').style.display = 'block';
        document.getElementById('panel-exportacion').style.display = 'block';
    });

    // Generar reporte Excel directo
    document.getElementById('btn-generar-excel')?.addEventListener('click', function() {
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>GENERANDO EXCEL...';
        this.disabled = true;
        
        setTimeout(() => {
            alert('📊 Reporte Excel generado correctamente\\n\\nArchivo: DRTC_Actas_' + new Date().toISOString().split('T')[0] + '.xlsx\\nUbicación: Carpeta de Descargas\\n\\nEl archivo contiene todas las actas según los filtros aplicados.');
            this.innerHTML = '<i class="fas fa-file-excel me-2"></i>GENERAR EXCEL';
            this.disabled = false;
            
            // Simular descarga
            const link = document.createElement('a');
            link.href = '#';
            link.download = 'DRTC_Actas_' + new Date().toISOString().split('T')[0] + '.xlsx';
            link.click();
        }, 2000);
    });

    // Generar reporte PDF directo
    document.getElementById('btn-generar-pdf')?.addEventListener('click', function() {
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>GENERANDO PDF...';
        this.disabled = true;
        
        setTimeout(() => {
            alert('📄 Reporte PDF generado correctamente\\n\\nArchivo: DRTC_Actas_' + new Date().toISOString().split('T')[0] + '.pdf\\nUbicación: Carpeta de Descargas\\n\\nEl documento incluye formato oficial DRTC con todas las actas filtradas.');
            this.innerHTML = '<i class="fas fa-file-pdf me-2"></i>GENERAR PDF';
            this.disabled = false;
            
            // Simular descarga
            const link = document.createElement('a');
            link.href = '#';
            link.download = 'DRTC_Actas_' + new Date().toISOString().split('T')[0] + '.pdf';
            link.click();
        }, 2500);
    });

    // Reporte completo estadístico
    document.getElementById('btn-reporte-completo')?.addEventListener('click', function() {
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>PROCESANDO...';
        this.disabled = true;
        
        setTimeout(() => {
            alert('📈 Reporte Completo Estadístico generado\\n\\nIncluye:\\n- Análisis de infracciones por tipo\\n- Estadísticas mensuales\\n- Gráficos comparativos\\n- Resumen ejecutivo\\n\\nFormatos: PDF + Excel');
            this.innerHTML = '<i class="fas fa-chart-bar me-2"></i>REPORTE COMPLETO';
            this.disabled = false;
        }, 3000);
    });

    // Seleccionar todas las actas
    document.getElementById('btn-seleccionar-todas')?.addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.row-checkbox');
        const checkAll = document.getElementById('check-all');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        
        checkboxes.forEach(cb => cb.checked = !allChecked);
        checkAll.checked = !allChecked;
        
        this.innerHTML = allChecked ? 
            '<i class="fas fa-check-square me-1"></i>Seleccionar Todas' : 
            '<i class="fas fa-square me-1"></i>Deseleccionar Todas';
    });

    // Checkbox principal
    document.getElementById('check-all')?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    // Exportar actas seleccionadas
    document.getElementById('btn-exportar-seleccionadas')?.addEventListener('click', function() {
        const seleccionadas = document.querySelectorAll('.row-checkbox:checked');
        if (seleccionadas.length === 0) {
            alert('⚠️ Seleccione al menos una acta para exportar');
            return;
        }
        
        const actas = Array.from(seleccionadas).map(cb => cb.dataset.acta);
        alert(`📁 Exportando ${actas.length} acta(s) seleccionada(s):\\n\\n${actas.join('\\n')}\\n\\nGenerando archivos PDF y Excel...`);
    });

    // Botones de exportación específicos
    document.getElementById('pdf-individual')?.addEventListener('click', function() {
        alert('📄 Generando PDFs individuales para cada acta seleccionada\\nFormato: Acta oficial DRTC por separado');
    });

    document.getElementById('pdf-consolidado')?.addEventListener('click', function() {
        alert('📑 Generando PDF consolidado con todas las actas\\nFormato: Documento único con todas las actas');
    });

    document.getElementById('excel-detallado')?.addEventListener('click', function() {
        alert('📊 Generando Excel detallado\\nIncluye: Todos los campos, fórmulas y análisis completo');
    });

    document.getElementById('excel-resumen')?.addEventListener('click', function() {
        alert('📈 Generando Excel resumen\\nIncluye: Datos principales, estadísticas y gráficos');
    });

    document.getElementById('reporte-estadistico')?.addEventListener('click', function() {
        alert('📊 Generando reporte estadístico avanzado\\nIncluye: Análisis temporal, comparativas, tendencias');
    });

    document.getElementById('reporte-mensual')?.addEventListener('click', function() {
        alert('📅 Generando reporte mensual DRTC\\nIncluye: Resumen del mes, comparativa histórica, KPIs');
    });

    // Manejar envío del formulario de nueva acta
    document.getElementById('form-nueva-acta')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validar que todos los campos requeridos estén llenos
        const requiredFields = this.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        if (isValid) {
            // Mostrar loading
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>GUARDANDO ACTA DRTC...';
            submitBtn.disabled = true;
            
            // Simular guardado
            setTimeout(() => {
                alert('✅ Nueva Acta DRTC guardada correctamente en el sistema\n\nN° de Acta: DRTC-APU-2024-' + String(Math.floor(Math.random() * 1000)).padStart(3, '0'));
                this.reset();
                actualizarFechaHora();
                
                // Restaurar botón
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 2500);
        } else {
            alert('⚠️ Por favor complete todos los campos obligatorios marcados para registrar el acta DRTC');
        }
    });

    // Formatear automáticamente la placa en mayúsculas
    document.querySelectorAll('input[name="placa_1"], input[name="placa"]').forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    });

    // Formatear RUC/DNI (solo números)
    document.querySelectorAll('input[name="ruc_dni"]').forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });
});
</script>

@endsection
