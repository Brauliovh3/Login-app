@extends('layouts.dashboard')

@section('title', 'Nueva Inspección')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Acta de Inspección</h2>
                        <p class="mb-0">Complete todos los campos requeridos para registrar la inspección</p>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-alt fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('inspecciones.store') }}" method="POST">
    @csrf
    
    <!-- Campos ocultos para fecha/hora automática -->
    <input type="hidden" id="fecha_inspeccion_hidden" name="fecha_inspeccion">
    <input type="hidden" id="hora_inicio_hidden" name="hora_inicio">
    <input type="hidden" name="inspector_principal" value="{{ Auth::user()->name }}">

    <!-- Información del Agente Infractor -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-user-shield me-2"></i>Agente infractor</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label"><strong>Transportista</strong></label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipo_agente" id="transportista" value="transportista">
                        <label class="form-check-label" for="transportista">☑️</label>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label"><strong>Operador de Ruta</strong></label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipo_agente" id="operador_ruta" value="operador_ruta">
                        <label class="form-check-label" for="operador_ruta">☑️</label>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label"><strong>Conductor</strong></label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipo_agente" id="conductor" value="conductor">
                        <label class="form-check-label" for="conductor">☑️</label>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label"><strong>Placa 1:</strong></label>
                    <input type="text" class="form-control" name="placa_1" placeholder="ABC-123">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label"><strong>Razón Social/Nombre:</strong></label>
                    <input type="text" class="form-control" name="razon_social" placeholder="Nombre o razón social">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><strong>RUC/DNI:</strong></label>
                    <input type="text" class="form-control" name="ruc_dni" placeholder="20123456789">
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label"><strong>Fecha:</strong></label>
                    <div class="form-control bg-light" id="fecha-actual"></div>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label"><strong>Hora inicio:</strong></label>
                    <div class="form-control bg-light" id="hora-actual"></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><strong>Fecha y Hora de fin:</strong></label>
                    <input type="datetime-local" class="form-control" name="fecha_hora_fin">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label"><strong>Nombre de Conductor 1:</strong></label>
                    <input type="text" class="form-control" name="nombre_conductor_1" placeholder="Nombres y apellidos">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label"><strong>N° Licencia/DNI del conductor 1:</strong></label>
                    <input type="text" class="form-control" name="licencia_conductor_1" placeholder="N°">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label"><strong>Clase y Categoría</strong></label>
                    <select class="form-select" name="clase_categoria">
                        <option value="">Seleccione...</option>
                        <option value="A-I">A-I</option>
                        <option value="A-IIa">A-IIa</option>
                        <option value="A-IIb">A-IIb</option>
                        <option value="A-IIIa">A-IIIa</option>
                        <option value="A-IIIb">A-IIIb</option>
                        <option value="A-IIIc">A-IIIc</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label"><strong>Lugar de la intervención:</strong></label>
                    <input type="text" class="form-control" name="lugar_intervencion" placeholder="Dirección exacta del lugar">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label"><strong>N° Km. De la red Vial Nacional Prov./Dpto.</strong></label>
                    <input type="text" class="form-control" name="km_red_vial" placeholder="Kilómetro">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><strong>Origen de viaje (Depto./Prov./Distrito)</strong></label>
                    <input type="text" class="form-control" name="origen_viaje" placeholder="Lugar de origen">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label"><strong>Destino Viaje: (Depto/Prov/Distrito)</strong></label>
                    <input type="text" class="form-control" name="destino_viaje" placeholder="Lugar de destino">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><strong>Tipo de Servicio que presta</strong></label>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipo_servicio" id="personas" value="personas">
                                <label class="form-check-label" for="personas">Personas</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipo_servicio" id="mercancia" value="mercancia">
                                <label class="form-check-label" for="mercancia">Mercancía</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label"><strong>Inspector:</strong></label>
                    <div class="form-control bg-light">{{ Auth::user()->name }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Descripción de los hechos -->
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-file-text me-2"></i>Descripción de los hechos</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <textarea class="form-control" name="descripcion_hechos" rows="8" placeholder="Describa detalladamente los hechos observados durante la inspección..." required></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Medidas y Observaciones -->
    <div class="card mb-4">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-gavel me-2"></i>Medidas y Observaciones</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label"><strong>Medios probatorios:</strong></label>
                    <textarea class="form-control" name="medios_probatorios" rows="3" placeholder="Especifique los medios probatorios"></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><strong>Calificación de la Infracción:</strong></label>
                    <select class="form-select" name="calificacion_infraccion">
                        <option value="">Seleccione...</option>
                        <option value="leve">🟡 Leve</option>
                        <option value="grave">🟠 Grave</option>
                        <option value="muy_grave">🔴 Muy Grave</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label"><strong>Medida(s) Administrativa(s):</strong></label>
                    <textarea class="form-control" name="medidas_administrativas" rows="3" placeholder="Especifique las medidas administrativas aplicadas"></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><strong>Sanción:</strong></label>
                    <textarea class="form-control" name="sancion" rows="3" placeholder="Detalle la sanción aplicada"></textarea>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label"><strong>Observaciones del intervenido:</strong></label>
                    <textarea class="form-control" name="observaciones_intervenido" rows="4" placeholder="Comentarios del intervenido"></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><strong>Observaciones del inspector:</strong></label>
                    <textarea class="form-control" name="observaciones_inspector" rows="4" placeholder="Comentarios adicionales del inspector"></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <button type="submit" class="btn btn-success btn-lg me-3">
                        <i class="fas fa-save me-2"></i>Guardar Acta de Inspección
                    </button>
                    <a href="{{ route('inspecciones.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function actualizarFechaHora() {
    const ahora = new Date();
    
    // Formatear fecha (DD/MM/YYYY)
    const fecha = ahora.toLocaleDateString('es-PE', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
    
    // Formatear hora (HH:MM:SS)
    const hora = ahora.toLocaleTimeString('es-PE', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
    });
    
    // Actualizar elementos en la página
    document.getElementById('fecha-actual').textContent = fecha;
    document.getElementById('hora-actual').textContent = hora;
    
    // Actualizar campos ocultos para envío del formulario
    document.getElementById('fecha_inspeccion_hidden').value = ahora.toISOString().split('T')[0];
    document.getElementById('hora_inicio_hidden').value = ahora.toTimeString().split(' ')[0].substring(0, 5);
}

// Actualizar cada segundo
setInterval(actualizarFechaHora, 1000);

// Ejecutar inmediatamente al cargar
actualizarFechaHora();
</script>
@endsection
