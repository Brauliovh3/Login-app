@extends('layouts.dashboard')

@section('title', 'Nueva Infracción de Tránsito')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="mb-0"><i class="fas fa-edit me-2"></i>Registro de Infracción de Tránsito</h2>
                        <p class="mb-0">Complete todos los campos requeridos</p>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-alt fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Fecha y Hora Automática -->
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Información de Registro Automática</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <label class="form-label">Fecha Actual:</label>
                <div class="form-control bg-light" id="fecha-actual"></div>
            </div>
            <div class="col-md-3">
                <label class="form-label">Hora Actual:</label>
                <div class="form-control bg-light" id="hora-actual"></div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Inspector:</label>
                <div class="form-control bg-light">{{ Auth::user()->name }} ({{ Auth::user()->username }})</div>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('infracciones.store') }}" method="POST">
    @csrf
    
    <!-- Campos ocultos para fecha/hora automática -->
    <input type="hidden" id="fecha_inicio_hidden" name="fecha_inicio">
    <input type="hidden" id="hora_inicio_hidden" name="hora_inicio">
    <input type="hidden" name="inspector" value="{{ Auth::user()->name }}">

    <!-- Información del Agente Infractor -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-user me-2"></i>Información del Agente Infractor</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="agente_infractor" class="form-label"><strong>Agente Infractor:</strong></label>
                    <select class="form-select @error('agente_infractor') is-invalid @enderror" id="agente_infractor" name="agente_infractor" required>
                        <option value="">Seleccione...</option>
                        <option value="transportista" {{ old('agente_infractor') == 'transportista' ? 'selected' : '' }}>☑️ Transportista</option>
                        <option value="operador_ruta" {{ old('agente_infractor') == 'operador_ruta' ? 'selected' : '' }}>☑️ Operador de Ruta</option>
                        <option value="conductor" {{ old('agente_infractor') == 'conductor' ? 'selected' : '' }}>☑️ Conductor</option>
                    </select>
                    @error('agente_infractor')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label for="placa" class="form-label"><strong>Placa:</strong></label>
                    <input type="text" class="form-control @error('placa') is-invalid @enderror" id="placa" name="placa" value="{{ old('placa') }}" placeholder="ABC-123" required style="text-transform: uppercase;">
                    @error('placa')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="razon_social" class="form-label"><strong>Razón Social/Nombre:</strong></label>
                    <input type="text" class="form-control @error('razon_social') is-invalid @enderror" id="razon_social" name="razon_social" value="{{ old('razon_social') }}" placeholder="Nombre completo o razón social">
                    @error('razon_social')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="ruc_dni" class="form-label"><strong>RUC/DNI:</strong></label>
                    <input type="text" class="form-control @error('ruc_dni') is-invalid @enderror" id="ruc_dni" name="ruc_dni" value="{{ old('ruc_dni') }}" placeholder="Número de documento" required>
                    @error('ruc_dni')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="fecha_fin" class="form-label">Fecha Fin (opcional):</label>
                    <input type="date" class="form-control @error('fecha_fin') is-invalid @enderror" id="fecha_fin" name="fecha_fin" value="{{ old('fecha_fin') }}">
                    @error('fecha_fin')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="hora_fin" class="form-label">Hora Fin (opcional):</label>
                    <input type="time" class="form-control @error('hora_fin') is-invalid @enderror" id="hora_fin" name="hora_fin" value="{{ old('hora_fin') }}">
                    @error('hora_fin')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Información del Conductor -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Información del Conductor</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="nombre_conductor1" class="form-label"><strong>Nombre de Conductor 1:</strong></label>
                    <input type="text" class="form-control @error('nombre_conductor1') is-invalid @enderror" id="nombre_conductor1" name="nombre_conductor1" value="{{ old('nombre_conductor1') }}" placeholder="Nombres y apellidos completos" required>
                    @error('nombre_conductor1')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="licencia_conductor1" class="form-label"><strong>N° Licencia/DNI del conductor 1:</strong></label>
                    <input type="text" class="form-control @error('licencia_conductor1') is-invalid @enderror" id="licencia_conductor1" name="licencia_conductor1" value="{{ old('licencia_conductor1') }}" placeholder="Número de licencia o DNI" required>
                    @error('licencia_conductor1')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="clase_categoria" class="form-label"><strong>Clase y Categoría:</strong></label>
                    <select class="form-select @error('clase_categoria') is-invalid @enderror" id="clase_categoria" name="clase_categoria" required>
                        <option value="">Seleccione...</option>
                        <option value="A-I" {{ old('clase_categoria') == 'A-I' ? 'selected' : '' }}>A-I (Motocicletas hasta 50cc)</option>
                        <option value="A-IIa" {{ old('clase_categoria') == 'A-IIa' ? 'selected' : '' }}>A-IIa (Motocicletas 51-125cc)</option>
                        <option value="A-IIb" {{ old('clase_categoria') == 'A-IIb' ? 'selected' : '' }}>A-IIb (Motocicletas más de 125cc)</option>
                        <option value="A-IIIa" {{ old('clase_categoria') == 'A-IIIa' ? 'selected' : '' }}>A-IIIa (Automóviles)</option>
                        <option value="A-IIIb" {{ old('clase_categoria') == 'A-IIIb' ? 'selected' : '' }}>A-IIIb (Camionetas hasta 3500kg)</option>
                        <option value="A-IIIc" {{ old('clase_categoria') == 'A-IIIc' ? 'selected' : '' }}>A-IIIc (Camiones más de 3500kg)</option>
                    </select>
                    @error('clase_categoria')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Información del Viaje -->
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-route me-2"></i>Información del Viaje y Ubicación</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="lugar_intervencion" class="form-label"><strong>Lugar de la Intervención:</strong></label>
                    <textarea class="form-control @error('lugar_intervencion') is-invalid @enderror" id="lugar_intervencion" name="lugar_intervencion" rows="2" placeholder="Descripción detallada del lugar donde ocurrió la intervención" required>{{ old('lugar_intervencion') }}</textarea>
                    @error('lugar_intervencion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="km_via_nacional" class="form-label"><strong>N° Km de la Vía Nacional/Prov./Dpto.:</strong></label>
                    <input type="text" class="form-control @error('km_via_nacional') is-invalid @enderror" id="km_via_nacional" name="km_via_nacional" value="{{ old('km_via_nacional') }}" placeholder="Ej: Km 15">
                    @error('km_via_nacional')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="origen_viaje" class="form-label"><strong>Origen de viaje (Depto./Prov./Distrito):</strong></label>
                    <input type="text" class="form-control @error('origen_viaje') is-invalid @enderror" id="origen_viaje" name="origen_viaje" value="{{ old('origen_viaje') }}" placeholder="Lugar de origen" required>
                    @error('origen_viaje')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="destino_viaje" class="form-label"><strong>Destino Viaje (Depto./Prov./Distrito):</strong></label>
                    <input type="text" class="form-control @error('destino_viaje') is-invalid @enderror" id="destino_viaje" name="destino_viaje" value="{{ old('destino_viaje') }}" placeholder="Lugar de destino" required>
                    @error('destino_viaje')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="tipo_servicio" class="form-label"><strong>Tipo de Servicio que presta:</strong></label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipo_servicio" id="personas" value="personas" {{ old('tipo_servicio') == 'personas' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="personas">
                                <strong>Personas</strong>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipo_servicio" id="mercancia" value="mercancia" {{ old('tipo_servicio') == 'mercancia' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="mercancia">
                                <strong>Mercancía</strong>
                            </label>
                        </div>
                    </div>
                    @error('tipo_servicio')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><strong>Inspector asignado:</strong></label>
                    <div class="form-control bg-light">{{ Auth::user()->name }} ({{ Auth::user()->username }})</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Descripción de los Hechos -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-file-text me-2"></i>Descripción de los Hechos</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label for="descripcion_hechos" class="form-label">Descripción de los hechos:</label>
                <textarea class="form-control @error('descripcion_hechos') is-invalid @enderror" id="descripcion_hechos" name="descripcion_hechos" rows="5" required>{{ old('descripcion_hechos') }}</textarea>
                @error('descripcion_hechos')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <!-- Información Adicional -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-clipboard me-2"></i>Información Adicional</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="medios_probatorios" class="form-label">Medios probatorios:</label>
                    <textarea class="form-control @error('medios_probatorios') is-invalid @enderror" id="medios_probatorios" name="medios_probatorios" rows="3">{{ old('medios_probatorios') }}</textarea>
                    @error('medios_probatorios')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="calificacion_infraccion" class="form-label">Calificación de la Infracción:</label>
                    <select class="form-select @error('calificacion_infraccion') is-invalid @enderror" id="calificacion_infraccion" name="calificacion_infraccion" required>
                        <option value="">Seleccione...</option>
                        <option value="muy_grave" {{ old('calificacion_infraccion') == 'muy_grave' ? 'selected' : '' }}>Muy Grave</option>
                        <option value="grave" {{ old('calificacion_infraccion') == 'grave' ? 'selected' : '' }}>Grave</option>
                        <option value="leve" {{ old('calificacion_infraccion') == 'leve' ? 'selected' : '' }}>Leve</option>
                    </select>
                    @error('calificacion_infraccion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="medidas_administrativas" class="form-label">Medida(s) Administrativa(s):</label>
                    <textarea class="form-control @error('medidas_administrativas') is-invalid @enderror" id="medidas_administrativas" name="medidas_administrativas" rows="3">{{ old('medidas_administrativas') }}</textarea>
                    @error('medidas_administrativas')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="sancion" class="form-label">Sanción:</label>
                    <textarea class="form-control @error('sancion') is-invalid @enderror" id="sancion" name="sancion" rows="3">{{ old('sancion') }}</textarea>
                    @error('sancion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="observaciones_intervenido" class="form-label">Observaciones del intervenido:</label>
                    <textarea class="form-control @error('observaciones_intervenido') is-invalid @enderror" id="observaciones_intervenido" name="observaciones_intervenido" rows="3">{{ old('observaciones_intervenido') }}</textarea>
                    @error('observaciones_intervenido')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="observaciones_inspector" class="form-label">Observaciones del inspector:</label>
                    <textarea class="form-control @error('observaciones_inspector') is-invalid @enderror" id="observaciones_inspector" name="observaciones_inspector" rows="3">{{ old('observaciones_inspector') }}</textarea>
                    @error('observaciones_inspector')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <a href="{{ route('infracciones.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-1"></i>Registrar Infracción
                </button>
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
    document.getElementById('fecha_inicio_hidden').value = ahora.toISOString().split('T')[0];
    document.getElementById('hora_inicio_hidden').value = ahora.toTimeString().split(' ')[0].substring(0, 5);
}

// Actualizar cada segundo
setInterval(actualizarFechaHora, 1000);

// Ejecutar inmediatamente al cargar
actualizarFechaHora();

// Convertir placa a mayúsculas automáticamente
document.getElementById('placa').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});
</script>
@endsection
