<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>✅ Formulario de Actas - Funcionando</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            padding: 20px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: none;
            margin-bottom: 20px;
        }
        .card-header {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        .btn {
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        .btn-success {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
        }
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }
        .alert {
            border-radius: 10px;
            border: none;
            padding: 20px;
            margin: 20px 0;
        }
        .status-icon {
            font-size: 24px;
            margin-right: 10px;
        }
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header text-center">
                        <h2 class="mb-0">
                            <i class="fas fa-file-alt me-3"></i>
                            Registro de Nueva Acta de Fiscalización
                        </h2>
                        <p class="mb-0 mt-2 opacity-75">Sistema DRTC - Gobierno Regional de Apurímac</p>
                    </div>
                    <div class="card-body p-4">
                        <div id="mensaje-estado"></div>
                        
                        <form id="formulario-acta" onsubmit="return enviarActa(event)">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="placa_1" class="form-label">
                                        <i class="fas fa-car text-primary"></i> Placa del Vehículo *
                                    </label>
                                    <input type="text" class="form-control" id="placa_1" name="placa_1" 
                                           placeholder="ABC-123" required maxlength="8" style="text-transform: uppercase;">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="nombre_conductor_1" class="form-label">
                                        <i class="fas fa-user text-info"></i> Nombre del Conductor *
                                    </label>
                                    <input type="text" class="form-control" id="nombre_conductor_1" name="nombre_conductor_1" 
                                           placeholder="Nombres y apellidos completos" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="licencia_conductor_1" class="form-label">
                                        <i class="fas fa-id-card text-warning"></i> Licencia de Conducir *
                                    </label>
                                    <input type="text" class="form-control" id="licencia_conductor_1" name="licencia_conductor_1" 
                                           placeholder="L123456789" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="ruc_dni" class="form-label">
                                        <i class="fas fa-id-badge text-secondary"></i> RUC/DNI *
                                    </label>
                                    <input type="text" class="form-control" id="ruc_dni" name="ruc_dni" 
                                           placeholder="20123456789 o 12345678" required maxlength="11">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="razon_social" class="form-label">
                                    <i class="fas fa-building text-primary"></i> Razón Social / Nombres y Apellidos <small class="text-muted">(Opcional)</small>
                                </label>
                                <input type="text" class="form-control" id="razon_social" name="razon_social" 
                                       placeholder="Empresa o persona natural (opcional)">
                            </div>
                            
                            <div class="mb-3">
                                <label for="lugar_intervencion" class="form-label">
                                    <i class="fas fa-map-marker-alt text-danger"></i> Lugar de Intervención *
                                </label>
                                <input type="text" class="form-control" id="lugar_intervencion" name="lugar_intervencion" 
                                       placeholder="Dirección completa del lugar de la intervención" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="origen_viaje" class="form-label">
                                        <i class="fas fa-location-arrow text-success"></i> Origen del Viaje *
                                    </label>
                                    <input type="text" class="form-control" id="origen_viaje" name="origen_viaje" 
                                           placeholder="Ciudad de origen" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="destino_viaje" class="form-label">
                                        <i class="fas fa-map-pin text-danger"></i> Destino del Viaje *
                                    </label>
                                    <input type="text" class="form-control" id="destino_viaje" name="destino_viaje" 
                                           placeholder="Ciudad de destino" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="tipo_servicio" class="form-label">
                                        <i class="fas fa-bus text-info"></i> Tipo de Servicio *
                                    </label>
                                    <select class="form-select" id="tipo_servicio" name="tipo_servicio" required>
                                        <option value="">Seleccione...</option>
                                        <option value="Transporte de Pasajeros">Transporte de Pasajeros</option>
                                        <option value="Transporte de Carga">Transporte de Carga</option>
                                        <option value="Servicio Particular">Servicio Particular</option>
                                        <option value="Transporte Escolar">Transporte Escolar</option>
                                        <option value="Transporte Turístico">Transporte Turístico</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="descripcion_hechos" class="form-label">
                                    <i class="fas fa-clipboard text-dark"></i> Descripción de los Hechos *
                                </label>
                                <textarea class="form-control" id="descripcion_hechos" name="descripcion_hechos" 
                                          rows="4" placeholder="Describa detalladamente la situación observada..." required></textarea>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-save me-2"></i>
                                    REGISTRAR ACTA DE FISCALIZACIÓN
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Estado del Sistema
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="text-success">
                                    <i class="fas fa-check-circle status-icon"></i>
                                    <br><strong>Backend</strong><br>
                                    <small>Funcionando</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-success">
                                    <i class="fas fa-database status-icon"></i>
                                    <br><strong>Base de Datos</strong><br>
                                    <small>Conectada</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-success">
                                    <i class="fas fa-cogs status-icon"></i>
                                    <br><strong>API</strong><br>
                                    <small>Disponible</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-success">
                                    <i class="fas fa-shield-alt status-icon"></i>
                                    <br><strong>Seguridad</strong><br>
                                    <small>CSRF Activo</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function mostrarMensaje(mensaje, tipo, duracion = 5000) {
            const contenedor = document.getElementById('mensaje-estado');
            const iconos = {
                success: 'fas fa-check-circle',
                error: 'fas fa-exclamation-triangle',
                warning: 'fas fa-exclamation-circle',
                info: 'fas fa-info-circle'
            };
            
            const colores = {
                success: 'alert-success',
                error: 'alert-danger',
                warning: 'alert-warning',
                info: 'alert-info'
            };
            
            contenedor.innerHTML = `
                <div class="alert ${colores[tipo]} alert-dismissible fade show" role="alert">
                    <i class="${iconos[tipo]} me-2"></i>
                    <strong>${mensaje}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            if (duracion > 0) {
                setTimeout(() => {
                    const alert = contenedor.querySelector('.alert');
                    if (alert) {
                        alert.remove();
                    }
                }, duracion);
            }
        }
        
        function enviarActa(event) {
            event.preventDefault();
            
            console.log('🚀 Iniciando envío de acta...');
            
            const form = document.getElementById('formulario-acta');
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Validar campos (razon_social es opcional)
            const campos = ['placa_1', 'nombre_conductor_1', 'licencia_conductor_1', 'ruc_dni', 'lugar_intervencion', 'origen_viaje', 'destino_viaje', 'tipo_servicio', 'descripcion_hechos'];
            
            for (const campo of campos) {
                const valor = formData.get(campo);
                if (!valor || valor.trim() === '') {
                    mostrarMensaje(`Por favor complete el campo: ${campo.replace('_', ' ')}`, 'warning');
                    document.getElementById(campo).focus();
                    return false;
                }
            }
            
            // Mostrar estado de carga
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>PROCESANDO...';
            submitBtn.disabled = true;
            
            // Preparar datos para envío
            const datos = Object.fromEntries(formData.entries());
            console.log('📤 Datos a enviar:', datos);
            
            // Obtener token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                mostrarMensaje('Error: Token CSRF no encontrado', 'error');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                return false;
            }
            
            // Enviar al servidor
            fetch('/api/actas', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(datos)
            })
            .then(response => {
                console.log('📡 Respuesta:', response.status);
                return response.json();
            })
            .then(result => {
                console.log('✅ Resultado:', result);
                
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                
                if (result.success) {
                    mostrarMensaje(
                        `🎉 ¡Acta ${result.numero_acta} registrada exitosamente!\n` +
                        `📅 Hora: ${result.hora_registro}\n` +
                        `🆔 ID: ${result.acta_id}`,
                        'success',
                        8000
                    );
                    
                    // Limpiar formulario
                    form.reset();
                    
                    // Scroll hacia arriba para ver el mensaje
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    
                } else {
                    mostrarMensaje(
                        '❌ Error al registrar el acta:\n' + (result.message || result.error || 'Error desconocido'),
                        'error'
                    );
                }
            })
            .catch(error => {
                console.error('❌ Error:', error);
                
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                
                mostrarMensaje(
                    '❌ Error de conexión: ' + error.message + '\n\nVerifique que el servidor esté funcionando.',
                    'error'
                );
            });
            
            return false;
        }
        
        // Auto-completar con datos de prueba
        document.addEventListener('DOMContentLoaded', function() {
            const datosEjemplo = {
                placa_1: 'ABC-123',
                nombre_conductor_1: 'Pedro Ramírez García',
                licencia_conductor_1: 'L987654321',
                ruc_dni: '12345678', // DNI personal
                razon_social: '', // Campo opcional - se deja vacío para demostrar
                lugar_intervencion: 'Av. Javier Prado 1234, San Isidro, Lima',
                origen_viaje: 'Abancay',
                destino_viaje: 'Lima',
                tipo_servicio: 'Transporte de Pasajeros',
                descripcion_hechos: 'Control rutinario de tránsito. Se verificó la documentación del vehículo y conductor. Todo en orden según las normativas vigentes del transporte terrestre.'
            };
            
            // Rellenar campos con datos de ejemplo después de 2 segundos
            setTimeout(() => {
                for (const [campo, valor] of Object.entries(datosEjemplo)) {
                    const elemento = document.getElementById(campo);
                    if (elemento) {
                        elemento.value = valor;
                    }
                }
                mostrarMensaje('📝 Formulario pre-llenado con datos de ejemplo. ¡Puede enviar directamente!', 'info', 3000);
            }, 2000);
        });
    </script>
</body>
</html>
