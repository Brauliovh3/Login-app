<!-- Modal Eliminar Infracción -->
<div class="modal fade" id="eliminarInfraccionModal" tabindex="-1" aria-labelledby="eliminarInfraccionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="eliminarInfraccionModalLabel">
                    <i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle text-danger" style="font-size: 4rem;"></i>
                    <h4 class="mt-3 mb-3">¿Estás seguro?</h4>
                    <p class="mb-3">
                        Estás a punto de eliminar la infracción con código:
                    </p>
                    <div class="alert alert-danger">
                        <strong id="codigoEliminar" class="fs-5"></strong>
                    </div>
                    <p class="text-muted">
                        Esta acción <strong>no se puede deshacer</strong>. 
                        Se eliminarán todos los detalles asociados a esta infracción.
                    </p>
                    
                    <!-- Información adicional de la infracción -->
                    <div class="border rounded p-3 bg-light text-start">
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">Aplica sobre:</small>
                                <div id="aplicaSobreEliminar" class="fw-bold"></div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Gravedad:</small>
                                <div id="gravedadEliminar" class="fw-bold"></div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <small class="text-muted">Sanción:</small>
                                <div id="sancionEliminar" class="fw-bold"></div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <small class="text-muted">Número de detalles:</small>
                                <div id="detallesEliminar" class="fw-bold"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Confirmación adicional -->
                <div class="mt-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="confirmarEliminacion">
                        <label class="form-check-label" for="confirmarEliminacion">
                            Confirmo que deseo eliminar esta infracción y todos sus detalles
                        </label>
                    </div>
                </div>
                
                <input type="hidden" id="idInfraccionEliminar">
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="btnConfirmarEliminacion" disabled>
                    <i class="fas fa-trash"></i> Sí, Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación de eliminación exitosa -->
<div class="modal fade" id="eliminacionExitosaModal" tabindex="-1" aria-labelledby="eliminacionExitosaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h6 class="modal-title" id="eliminacionExitosaModalLabel">
                    <i class="fas fa-check-circle"></i> Eliminación Exitosa
                </h6>
            </div>
            
            <div class="modal-body text-center">
                <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                <h5 class="mt-3 mb-3">¡Eliminado!</h5>
                <p class="mb-0">La infracción ha sido eliminada exitosamente.</p>
            </div>
            
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">
                    <i class="fas fa-check"></i> Entendido
                </button>
            </div>
        </div>
    </div>
</div>