<?php $__env->startSection('title', 'Nueva Inspección'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Encabezado -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus-circle text-orange mr-2"></i>
            Nueva Inspección
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo e(route('inspector.dashboard')); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Nueva Inspección</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-file-alt mr-2"></i>
                        Formulario de Nueva Inspección
                    </h6>
                </div>
                <div class="card-body">
                    <form id="nuevaInspeccionForm" method="POST" action="<?php echo e(route('inspector.nueva-inspeccion.store')); ?>">
                        <?php echo csrf_field(); ?>
                        
                        <!-- Información del Vehículo -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="text-orange mb-3">
                                    <i class="fas fa-car mr-2"></i>
                                    Información del Vehículo
                                </h5>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="placa" class="form-label">Placa del Vehículo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="placa" name="placa" required 
                                       placeholder="Ej: ABC-123" style="text-transform: uppercase;">
                            </div>
                            <div class="col-md-4">
                                <label for="tipo_vehiculo" class="form-label">Tipo de Vehículo <span class="text-danger">*</span></label>
                                <select class="form-select" id="tipo_vehiculo" name="tipo_vehiculo" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="automovil">Automóvil</option>
                                    <option value="camioneta">Camioneta</option>
                                    <option value="bus">Bus</option>
                                    <option value="camion">Camión</option>
                                    <option value="motocicleta">Motocicleta</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="marca" class="form-label">Marca</label>
                                <input type="text" class="form-control" id="marca" name="marca" placeholder="Ej: Toyota">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="modelo" class="form-label">Modelo</label>
                                <input type="text" class="form-control" id="modelo" name="modelo" placeholder="Ej: Corolla">
                            </div>
                            <div class="col-md-4">
                                <label for="color" class="form-label">Color</label>
                                <input type="text" class="form-control" id="color" name="color" placeholder="Ej: Blanco">
                            </div>
                            <div class="col-md-4">
                                <label for="anio" class="form-label">Año</label>
                                <input type="number" class="form-control" id="anio" name="anio" 
                                       min="1990" max="2025" placeholder="2020">
                            </div>
                        </div>

                        <!-- Información del Conductor -->
                        <div class="row mb-4 mt-4">
                            <div class="col-md-12">
                                <h5 class="text-orange mb-3">
                                    <i class="fas fa-user mr-2"></i>
                                    Información del Conductor
                                </h5>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="conductor_dni" class="form-label">DNI del Conductor <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="conductor_dni" name="conductor_dni" 
                                       required maxlength="8" placeholder="12345678">
                            </div>
                            <div class="col-md-4">
                                <label for="conductor_nombre" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="conductor_nombre" name="conductor_nombre" 
                                       required placeholder="Nombres y Apellidos">
                            </div>
                            <div class="col-md-4">
                                <label for="licencia" class="form-label">Número de Licencia</label>
                                <input type="text" class="form-control" id="licencia" name="licencia" 
                                       placeholder="Número de licencia">
                            </div>
                        </div>

                        <!-- Información de la Inspección -->
                        <div class="row mb-4 mt-4">
                            <div class="col-md-12">
                                <h5 class="text-orange mb-3">
                                    <i class="fas fa-clipboard-check mr-2"></i>
                                    Datos de la Inspección
                                </h5>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="fecha_inspeccion" class="form-label">Fecha de Inspección <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="fecha_inspeccion" name="fecha_inspeccion" 
                                       required value="<?php echo e(date('Y-m-d\TH:i')); ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="ubicacion" class="form-label">Ubicación <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="ubicacion" name="ubicacion" 
                                       required placeholder="Dirección o punto de referencia">
                            </div>
                            <div class="col-md-4">
                                <label for="tipo_inspeccion" class="form-label">Tipo de Inspección <span class="text-danger">*</span></label>
                                <select class="form-select" id="tipo_inspeccion" name="tipo_inspeccion" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="rutinaria">Inspección Rutinaria</option>
                                    <option value="operativo">Operativo Especial</option>
                                    <option value="denuncia">Por Denuncia</option>
                                    <option value="accidente">Post-Accidente</option>
                                </select>
                            </div>
                        </div>

                        <!-- Infracciones Detectadas -->
                        <div class="row mb-4 mt-4">
                            <div class="col-md-12">
                                <h5 class="text-orange mb-3">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    Infracciones Detectadas
                                </h5>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-check-container">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="exceso_velocidad" name="infracciones[]" value="exceso_velocidad">
                                                <label class="form-check-label" for="exceso_velocidad">
                                                    Exceso de Velocidad
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="documentos_vigentes" name="infracciones[]" value="documentos_vigentes">
                                                <label class="form-check-label" for="documentos_vigentes">
                                                    Documentos no vigentes
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="soat_vencido" name="infracciones[]" value="soat_vencido">
                                                <label class="form-check-label" for="soat_vencido">
                                                    SOAT Vencido
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="revision_tecnica" name="infracciones[]" value="revision_tecnica">
                                                <label class="form-check-label" for="revision_tecnica">
                                                    Revisión Técnica Vencida
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="estado_vehiculo" name="infracciones[]" value="estado_vehiculo">
                                                <label class="form-check-label" for="estado_vehiculo">
                                                    Mal estado del vehículo
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="sobrecarga" name="infracciones[]" value="sobrecarga">
                                                <label class="form-check-label" for="sobrecarga">
                                                    Sobrecarga
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="ruta_no_autorizada" name="infracciones[]" value="ruta_no_autorizada">
                                                <label class="form-check-label" for="ruta_no_autorizada">
                                                    Ruta no autorizada
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="otras" name="infracciones[]" value="otras">
                                                <label class="form-check-label" for="otras">
                                                    Otras infracciones
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Observaciones -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="observaciones" name="observaciones" 
                                          rows="4" placeholder="Describir detalles adicionales de la inspección..."></textarea>
                            </div>
                        </div>

                        <!-- Estado de la Inspección -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="estado" class="form-label">Estado de la Inspección <span class="text-danger">*</span></label>
                                <select class="form-select" id="estado" name="estado" required>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="procesada" selected>Completada</option>
                                    <option value="observada">Con Observaciones</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="multa_aplicada" class="form-label">Multa Aplicada</label>
                                <div class="input-group">
                                    <span class="input-group-text">S/</span>
                                    <input type="number" class="form-control" id="multa_aplicada" name="multa_aplicada" 
                                           step="0.01" min="0" placeholder="0.00">
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between">
                                    <a href="<?php echo e(route('inspector.dashboard')); ?>" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left mr-2"></i>
                                        Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-2"></i>
                                        Guardar Inspección
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    // Formatear placa en mayúsculas
    $('#placa').on('input', function() {
        this.value = this.value.toUpperCase();
    });

    // Validar DNI (solo números y 8 dígitos)
    $('#conductor_dni').on('input', function() {
        this.value = this.value.replace(/\D/g, '');
        if (this.value.length > 8) {
            this.value = this.value.slice(0, 8);
        }
    });

    // Envío del formulario
    $('#nuevaInspeccionForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validaciones básicas
        if (!$('#placa').val() || !$('#conductor_dni').val() || !$('#conductor_nombre').val()) {
            showError('Por favor complete todos los campos obligatorios');
            return;
        }

        // Confirmar envío
        Swal.fire({
            title: '¿Confirmar Inspección?',
            text: '¿Está seguro de que desea registrar esta inspección?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ff8c00',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, registrar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Simular envío exitoso (reemplazar con AJAX real)
                showSuccess('Inspección registrada correctamente');
                setTimeout(() => {
                    window.location.href = '<?php echo e(route("inspector.inspecciones")); ?>';
                }, 2000);
            }
        });
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Login-app\resources\views\backup-roles-sep-2025\inspector\nueva-inspeccion.blade.php ENDPATH**/ ?>