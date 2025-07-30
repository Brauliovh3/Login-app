@extends('layouts.dashboard')

@section('title', 'Dashboard - Fiscalizador DRTC Apurímac')

@section('content')
<style>
    :root {
        --drtc-orange: #ff8c00;
        --drtc-dark-orange: #e67c00;
        --drtc-light-orange: #ffb84d;
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
        color: white;
        font-weight: bold;
    }
    .btn-drtc-orange:hover { 
        background-color: var(--drtc-dark-orange); 
        border-color: var(--drtc-dark-orange); 
        color: white;
    }
    
    .nav-tabs .nav-link.active {
        background-color: var(--drtc-orange) !important;
        border-color: var(--drtc-orange) !important;
        color: white !important;
    }
    .nav-tabs .nav-link {
        color: var(--drtc-orange) !important;
        border-color: transparent;
        font-weight: bold;
    }
    .nav-tabs .nav-link:hover {
        background-color: var(--drtc-orange-bg);
        border-color: var(--drtc-light-orange);
    }
    
    .drtc-logo {
        background: linear-gradient(135deg, var(--drtc-orange), var(--drtc-dark-orange));
        border-radius: 50%;
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        font-weight: bold;
        box-shadow: 0 4px 15px rgba(255, 140, 0, 0.3);
    }
    
    .drtc-header {
        background: linear-gradient(135deg, var(--drtc-orange), var(--drtc-dark-orange));
        position: relative;
        overflow: hidden;
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
</style>

<!-- Header Principal DRTC Apurímac -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card drtc-header text-white shadow-lg border-0">
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
                                <h1 class="mb-1 fw-bold">DIRECCIÓN REGIONAL DE TRANSPORTES</h1>
                                <h2 class="mb-1">Y COMUNICACIONES - APURÍMAC</h2>
                                <h4 class="mb-2 opacity-90">Sistema de Fiscalización de Transporte Terrestre</h4>
                                <div class="d-flex align-items-center text-warning">
                                    <i class="fas fa-user-shield me-2"></i>
                                    <span class="me-3">Fiscalizador: {{ Auth::user()->name }}</span>
                                    <i class="fas fa-calendar me-2"></i>
                                    <span class="me-3">{{ date('d/m/Y') }}</span>
                                    <i class="fas fa-clock me-2"></i>
                                    <span id="hora-header"></span>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="text-center">
                                    <i class="fas fa-clipboard-list fa-4x opacity-75 mb-2"></i>
                                    <div class="h5 mb-0">SISTEMA DE ACTAS</div>
                                    <div class="small opacity-75">Versión 2.0</div>
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
                <h3 class="mb-1 text-drtc-orange fw-bold">SISTEMA DE GESTIÓN DE ACTAS DRTC</h3>
                <p class="mb-0 text-muted">Dirección Regional de Transportes y Comunicaciones - Apurímac</p>
            </div>
        </div>
        
        <!-- Navegación de Pestañas -->
        <nav class="mt-4">
            <div class="nav nav-tabs border-0" id="nav-tab" role="tablist">
                <button class="nav-link active" id="nav-nueva-tab" data-bs-toggle="tab" data-bs-target="#nav-nueva" type="button" role="tab">
                    <i class="fas fa-plus-circle me-2"></i>NUEVA ACTA
                </button>
                <button class="nav-link" id="nav-editar-tab" data-bs-toggle="tab" data-bs-target="#nav-editar" type="button" role="tab">
                    <i class="fas fa-edit me-2"></i>EDITAR ACTA
                </button>
                <button class="nav-link" id="nav-eliminar-tab" data-bs-toggle="tab" data-bs-target="#nav-eliminar" type="button" role="tab">
                    <i class="fas fa-trash-alt me-2"></i>ELIMINAR ACTA
                </button>
                <button class="nav-link" id="nav-consulta-tab" data-bs-toggle="tab" data-bs-target="#nav-consulta" type="button" role="tab">
                    <i class="fas fa-search me-2"></i>CONSULTA DE ACTAS
                </button>
            </div>
        </nav>
    </div>
    
    <div class="card-body bg-light p-4">
        <div class="tab-content" id="nav-tabContent">
            
            <!-- PESTAÑA: NUEVA ACTA -->
            <div class="tab-pane fade show active" id="nav-nueva" role="tabpanel">
                <div class="card border-0 shadow">
                    <div class="card-header bg-drtc-orange text-white">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-file-plus me-2"></i>REGISTRO DE NUEVA ACTA DE FISCALIZACIÓN DRTC</h5>
                    </div>
                    <div class="card-body">
                        <form id="form-nueva-acta" action="{{ route('inspecciones.store') }}" method="POST">
                            @csrf
                            
                            <!-- Campos automáticos ocultos -->
                            <input type="hidden" id="fecha_inspeccion_hidden" name="fecha_inspeccion">
                            <input type="hidden" id="hora_inicio_hidden" name="hora_inicio">
                            <input type="hidden" name="inspector_principal" value="{{ Auth::user()->name }}">

                            <!-- SECCIÓN 1: INFORMACIÓN DEL OPERADOR -->
                            <div class="card mb-4 border-drtc-orange">
                                <div class="card-header bg-drtc-light text-dark">
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
                            <div class="card mb-4 border-drtc-orange">
                                <div class="card-header bg-drtc-light text-dark">
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
                            <div class="card mb-4 border-drtc-orange">
                                <div class="card-header bg-warning text-dark">
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
                            <div class="card mb-4 border-drtc-orange">
                                <div class="card-header bg-danger text-white">
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
                <div class="card border-0 shadow">
                    <div class="card-header bg-warning text-dark">
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
                <div class="card border-0 shadow">
                    <div class="card-header bg-danger text-white">
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

            <!-- PESTAÑA: CONSULTA DE ACTAS -->
            <div class="tab-pane fade" id="nav-consulta" role="tabpanel">
                <div class="card border-0 shadow">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-search me-2"></i>CONSULTA Y BÚSQUEDA DE ACTAS DRTC APURÍMAC</h5>
                    </div>
                    <div class="card-body">
                        <!-- Filtros de búsqueda -->
                        <div class="card mb-4 border-info">
                            <div class="card-header bg-light border-info">
                                <h6 class="mb-0 text-info fw-bold"><i class="fas fa-filter me-2"></i>FILTROS DE BÚSQUEDA AVANZADA DRTC</h6>
                            </div>
                            <div class="card-body bg-light">
                                <form id="form-buscar-actas">
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
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-info btn-lg px-5">
                                            <i class="fas fa-search me-2"></i>EJECUTAR BÚSQUEDA DRTC
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Resultados -->
                        <div class="card border-drtc-orange">
                            <div class="card-header bg-drtc-orange text-white">
                                <h6 class="mb-0 fw-bold"><i class="fas fa-list me-2"></i>RESULTADOS DE CONSULTA - ACTAS DRTC APURÍMAC</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="bg-drtc-dark text-white">
                                            <tr>
                                                <th class="fw-bold">N° ACTA DRTC</th>
                                                <th class="fw-bold">FECHA</th>
                                                <th class="fw-bold">OPERADOR</th>
                                                <th class="fw-bold">PLACA</th>
                                                <th class="fw-bold">LUGAR</th>
                                                <th class="fw-bold">ESTADO</th>
                                                <th class="fw-bold">ACCIONES</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tabla-resultados">
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-5">
                                                    <div class="drtc-logo mx-auto mb-3" style="width: 80px; height: 80px;">
                                                        <div class="text-center">
                                                            <i class="fas fa-search"></i>
                                                            <div style="font-size: 10px; line-height: 1;">DRTC</div>
                                                        </div>
                                                    </div>
                                                    <strong>Utilice los filtros de búsqueda para consultar las actas DRTC</strong><br>
                                                    <small>Sistema de Fiscalización - Dirección Regional de Transportes Apurímac</small>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para actualizar fecha y hora en tiempo real
    function actualizarFechaHora() {
        const ahora = new Date();
        const fecha = ahora.toLocaleDateString('es-PE', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        const hora = ahora.toLocaleTimeString('es-PE', { 
            hour12: false,
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        
        // Actualizar header
        const horaHeader = document.getElementById('hora-header');
        if (horaHeader) horaHeader.textContent = hora;
        
        // Actualizar formulario nueva acta
        const fechaInput = document.getElementById('fecha-actual-nueva');
        const horaInput = document.getElementById('hora-actual-nueva');
        
        if (fechaInput) fechaInput.textContent = ahora.toLocaleDateString('es-PE');
        if (horaInput) horaInput.textContent = hora;
        
        // También actualizar los campos hidden
        const fechaHidden = document.getElementById('fecha_inspeccion_hidden');
        const horaHidden = document.getElementById('hora_inicio_hidden');
        
        if (fechaHidden) fechaHidden.value = ahora.toISOString().split('T')[0];
        if (horaHidden) horaHidden.value = hora;
    }

    // Actualizar cada segundo
    actualizarFechaHora();
    setInterval(actualizarFechaHora, 1000);

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

    // Búsqueda de actas con filtros
    document.getElementById('form-buscar-actas')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Simular resultados de búsqueda del DRTC
        const tbody = document.getElementById('tabla-resultados');
        tbody.innerHTML = `
            <tr>
                <td><strong>DRTC-APU-2024-001</strong></td>
                <td>15/01/2024</td>
                <td>TRANSPORTES ANDINOS APURÍMAC S.A.C.</td>
                <td>APU-123</td>
                <td>CARRETERA ABANCAY-CUSCO</td>
                <td><span class="badge bg-success">✅ Activa</span></td>
                <td>
                    <button class="btn btn-sm btn-info me-1" title="Ver Detalle">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-drtc-orange me-1" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
            <tr>
                <td><strong>DRTC-APU-2024-002</strong></td>
                <td>16/01/2024</td>
                <td>JUAN TORRES MAMANI</td>
                <td>APU-456</td>
                <td>AV. DÍAZ BÁRCENAS, ABANCAY</td>
                <td><span class="badge bg-warning">🔄 Procesada</span></td>
                <td>
                    <button class="btn btn-sm btn-info me-1" title="Ver Detalle">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-drtc-orange me-1" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
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
