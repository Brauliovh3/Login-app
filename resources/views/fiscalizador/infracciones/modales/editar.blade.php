<!-- Modal Editar Infracción -->
<div class="modal fade" id="editarInfraccionModal" tabindex="-1" aria-labelledby="editarInfraccionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="editarInfraccionModalLabel">
                    <i class="fas fa-edit"></i> Editar Infracción
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form id="formEditarInfraccion">
                    @csrf
                    @method('PUT')
                    
                    <input type="hidden" id="editar_infraccion_id" name="infraccion_id">
                    
                    <!-- Información General -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-warning border-bottom pb-2 mb-3">
                                <i class="fas fa-info-circle"></i> Información General
                            </h6>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="editar_codigo" class="form-label required">Código</label>
                                <input type="text" class="form-control" id="editar_codigo" name="codigo" 
                                       placeholder="Ej: F.1, S.1, I.1" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="editar_aplica_sobre" class="form-label required">Aplica Sobre</label>
                                <select class="form-select" id="editar_aplica_sobre" name="aplica_sobre" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="Transportista">Transportista</option>
                                    <option value="Conductor">Conductor</option>
                                    <option value="Generador de carga">Generador de carga</option>
                                    <option value="Operadores de terminales terrestres y estaciones de ruta">Operadores de terminales terrestres</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label for="editar_clase_pago" class="form-label required">Clase de Pago</label>
                                <select class="form-select" id="editar_clase_pago" name="clase_pago" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="Pecuniaria">Pecuniaria</option>
                                    <option value="No pecuniaria">No pecuniaria</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="editar_reglamento" class="form-label required">Reglamento</label>
                                <textarea class="form-control" id="editar_reglamento" name="reglamento" 
                                          rows="2" required></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editar_norma_modificatoria" class="form-label required">Norma Modificatoria</label>
                                <input type="text" class="form-control" id="editar_norma_modificatoria" name="norma_modificatoria" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="editar_tipo" class="form-label required">Tipo</label>
                                <input type="text" class="form-control" id="editar_tipo" name="tipo" 
                                       value="Infracción" readonly required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="editar_gravedad" class="form-label required">Gravedad</label>
                                <select class="form-select" id="editar_gravedad" name="gravedad" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="leve">Leve</option>
                                    <option value="grave">Grave</option>
                                    <option value="muy_grave">Muy Grave</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editar_sancion" class="form-label required">Sanción</label>
                                <input type="text" class="form-control" id="editar_sancion" name="sancion" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editar_medida_preventiva" class="form-label required">Medida Preventiva</label>
                                <textarea class="form-control" id="editar_medida_preventiva" name="medida_preventiva" 
                                          rows="2" required></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="editar_otros_responsables" class="form-label">Otros Responsables / Otros Beneficios</label>
                                <textarea class="form-control" id="editar_otros_responsables" name="otros_responsables__otros_beneficios" rows="2"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Detalles de la Infracción -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-warning border-bottom pb-2 mb-3">
                                <i class="fas fa-list-ul"></i> Detalles de la Infracción
                                <button type="button" class="btn btn-sm btn-outline-success float-end" id="btnAgregarDetalleEditar">
                                    <i class="fas fa-plus"></i> Agregar Detalle
                                </button>
                            </h6>
                        </div>
                    </div>
                    
                    <div id="contenedorDetallesEditar">
                        <!-- Los detalles se cargarán dinámicamente aquí -->
                    </div>
                </form>
                
                <!-- Loading state -->
                <div id="loadingEditarInfraccion" class="text-center py-5" style="display: none;">
                    <div class="spinner-border text-warning" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando información...</p>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-warning" id="btnActualizarInfraccion">
                    <i class="fas fa-save"></i> Actualizar Infracción
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Template para detalles de edición -->
<template id="templateDetalleEditar">
    <div class="detalle-item border rounded p-3 mb-3 bg-light">
        <div class="row">
            <div class="col-md-8">
                <div class="mb-3">
                    <label class="form-label required">Descripción General</label>
                    <input type="text" class="form-control" name="detalles[INDEX][descripcion]" required>
                    <input type="hidden" name="detalles[INDEX][id]" class="detalle-id">
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label">Subcategoría</label>
                    <input type="text" class="form-control" name="detalles[INDEX][subcategoria]" 
                           placeholder="a), b), c)...">
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            
            <div class="col-md-1">
                <div class="mb-3">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-outline-danger btn-sm w-100 btn-eliminar-detalle" 
                            title="Eliminar detalle">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label required">Descripción Detallada</label>
                    <textarea class="form-control" name="detalles[INDEX][descripcion_detallada]" 
                              rows="3" required></textarea>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">Condiciones Especiales</label>
                    <textarea class="form-control" name="detalles[INDEX][condiciones_especiales]" rows="2"></textarea>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
        </div>
    </div>
</template>