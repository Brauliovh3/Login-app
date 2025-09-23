<?php $__env->startSection('title', 'Nueva Atención'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0">
                <i class="fas fa-user-plus me-2" style="color: #ff8c00;"></i>
                Nueva Atención al Usuario
            </h2>
        </div>
    </div>

    <div class="card">
        <div class="card-header" style="background-color: #ff8c00; color: white;">
            <h5 class="mb-0">
                <i class="fas fa-clipboard me-2"></i>Formulario de Atención
            </h5>
        </div>
        <div class="card-body">
            <form id="nuevaAtencionForm">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tipo_atencion" class="form-label">Tipo de Atención *</label>
                            <select class="form-select" id="tipo_atencion" required>
                                <option value="">Seleccionar...</option>
                                <option value="consulta">Consulta General</option>
                                <option value="tramite">Nuevo Trámite</option>
                                <option value="pago">Pago de Multa</option>
                                <option value="reclamo">Reclamo</option>
                                <option value="certificado">Certificado</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="dni_usuario" class="form-label">DNI del Usuario *</label>
                            <input type="text" class="form-control" id="dni_usuario" maxlength="8" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nombres" class="form-label">Nombres *</label>
                            <input type="text" class="form-control" id="nombres" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="apellidos" class="form-label">Apellidos *</label>
                            <input type="text" class="form-control" id="apellidos" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email">
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="asunto" class="form-label">Asunto de la Consulta *</label>
                    <input type="text" class="form-control" id="asunto" required>
                </div>
                
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción Detallada *</label>
                    <textarea class="form-control" id="descripcion" rows="4" required></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="prioridad" class="form-label">Prioridad</label>
                            <select class="form-select" id="prioridad">
                                <option value="normal">Normal</option>
                                <option value="alta">Alta</option>
                                <option value="urgente">Urgente</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="documentos" class="form-label">Documentos Adjuntos</label>
                            <input type="file" class="form-control" id="documentos" multiple>
                        </div>
                    </div>
                </div>
                
                <div class="text-end">
                    <button type="button" class="btn btn-secondary me-2">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" onclick="guardarAtencion()">
                        <i class="fas fa-save me-2"></i>Registrar Atención
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function guardarAtencion() {
    // Validar campos obligatorios
    const tipo = document.getElementById('tipo_atencion').value;
    const dni = document.getElementById('dni_usuario').value;
    const nombres = document.getElementById('nombres').value;
    const apellidos = document.getElementById('apellidos').value;
    const asunto = document.getElementById('asunto').value;
    const descripcion = document.getElementById('descripcion').value;
    
    if (!tipo || !dni || !nombres || !apellidos || !asunto || !descripcion) {
        showError('Por favor complete todos los campos obligatorios');
        return;
    }
    
    // Simular guardado
    showSuccess('Atención registrada exitosamente');
    
    // Limpiar formulario
    document.getElementById('nuevaAtencionForm').reset();
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Login-app\resources\views/ventanilla/nueva-atencion.blade.php ENDPATH**/ ?>