<!-- Modal Ver Infracción -->
<div class="modal fade" id="verInfraccionModal" tabindex="-1" aria-labelledby="verInfraccionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="verInfraccionModalLabel">
                    <i class="fas fa-eye"></i> Detalles de la Infracción
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <div id="contenidoVerInfraccion">
                    <!-- Información General -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-info border-bottom pb-2 mb-3">
                                <i class="fas fa-info-circle"></i> Información General
                            </h6>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Código:</label>
                                <p id="ver_codigo" class="form-control-plaintext border rounded px-3 py-2 bg-light"></p>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Aplica Sobre:</label>
                                <p id="ver_aplica_sobre" class="form-control-plaintext border rounded px-3 py-2 bg-light"></p>
                            </div>
                        </div>
                        
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Clase de Pago:</label>
                                <p id="ver_clase_pago" class="form-control-plaintext border rounded px-3 py-2 bg-light"></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Reglamento:</label>
                                <p id="ver_reglamento" class="form-control-plaintext border rounded px-3 py-2 bg-light"></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Norma Modificatoria:</label>
                                <p id="ver_norma_modificatoria" class="form-control-plaintext border rounded px-3 py-2 bg-light"></p>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tipo:</label>
                                <p id="ver_tipo" class="form-control-plaintext border rounded px-3 py-2 bg-light"></p>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Gravedad:</label>
                                <p id="ver_gravedad" class="form-control-plaintext border rounded px-3 py-2 bg-light"></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Sanción:</label>
                                <p id="ver_sancion" class="form-control-plaintext border rounded px-3 py-2 bg-light"></p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Medida Preventiva:</label>
                                <p id="ver_medida_preventiva" class="form-control-plaintext border rounded px-3 py-2 bg-light"></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Otros Responsables / Otros Beneficios:</label>
                                <p id="ver_otros_responsables" class="form-control-plaintext border rounded px-3 py-2 bg-light"></p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Detalles de la Infracción -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-info border-bottom pb-2 mb-3">
                                <i class="fas fa-list-ul"></i> Detalles de la Infracción
                                <span id="cantidadDetalles" class="badge bg-secondary ms-2"></span>
                            </h6>
                        </div>
                    </div>
                    
                    <div id="contenedorVerDetalles">
                        <!-- Los detalles se cargarán dinámicamente aquí -->
                    </div>
                    
                    <!-- Información de Auditoría -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="text-info border-bottom pb-2 mb-3">
                                <i class="fas fa-clock"></i> Información de Auditoría
                            </h6>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Fecha de Creación:</label>
                                <p id="ver_created_at" class="form-control-plaintext border rounded px-3 py-2 bg-light"></p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Última Modificación:</label>
                                <p id="ver_updated_at" class="form-control-plaintext border rounded px-3 py-2 bg-light"></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Loading state -->
                <div id="loadingVerInfraccion" class="text-center py-5" style="display: none;">
                    <div class="spinner-border text-info" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando información...</p>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cerrar
                </button>
                <button type="button" class="btn btn-warning" id="btnEditarDesdeVer">
                    <i class="fas fa-edit"></i> Editar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Template para mostrar detalles -->
<template id="templateVerDetalle">
    <div class="card mb-3">
        <div class="card-header bg-light">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h6 class="mb-0 fw-bold text-primary detalle-descripcion"></h6>
                </div>
                <div class="col-md-4 text-end">
                    <span class="badge bg-primary detalle-subcategoria"></span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <label class="form-label fw-bold">Descripción Detallada:</label>
                    <p class="detalle-descripcion-detallada text-muted"></p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label class="form-label fw-bold">Condiciones Especiales:</label>
                    <p class="detalle-condiciones-especiales text-muted"></p>
                </div>
            </div>
        </div>
    </div>
</template>