/**
 * Gestión de Infracciones CRUD con Modales
 * Maneja todas las interacciones AJAX y validaciones
 */

document.addEventListener('DOMContentLoaded', function() {
    // Variables globales
    let contadorDetalles = 0;
    let contadorDetallesEditar = 0;
    let infraccionIdActual = null;

    // URLs de las rutas
    const ROUTES = {
        store: '/infracciones',
        show: '/infracciones/',
        update: '/infracciones/',
        destroy: '/infracciones/',
        datatables: '/infracciones-datatables'
    };

    // ================================
    // INICIALIZACIÓN
    // ================================
    
    // Inicializar eventos cuando el DOM esté listo
    initializeEvents();
    
    function initializeEvents() {
        // Eventos de modales
        setupModalEvents();
        
        // Eventos de formularios
        setupFormEvents();
        
        // Eventos de filtros
        setupFilterEvents();
        
        // Agregar primer detalle al modal de crear
        agregarDetalle();
    }

    // ================================
    // CONFIGURACIÓN DE EVENTOS
    // ================================
    
    function setupModalEvents() {
        // Modal crear - limpiar al abrir
        $('#crearInfraccionModal').on('show.bs.modal', function() {
            limpiarFormulario('#formCrearInfraccion');
            $('#contenedorDetalles').html('');
            contadorDetalles = 0;
            agregarDetalle();
        });

        // Modal editar - botón desde ver
        $('#btnEditarDesdeVer').on('click', function() {
            $('#verInfraccionModal').modal('hide');
            setTimeout(() => {
                editarInfraccion(infraccionIdActual);
            }, 300);
        });

        // Checkbox de confirmación de eliminación
        $('#confirmarEliminacion').on('change', function() {
            $('#btnConfirmarEliminacion').prop('disabled', !this.checked);
        });
    }
    
    function setupFormEvents() {
        // Botones para agregar detalles
        $('#btnAgregarDetalle').on('click', agregarDetalle);
        $('#btnAgregarDetalleEditar').on('click', agregarDetalleEditar);
        
        // Eliminar detalles (delegación de eventos)
        $(document).on('click', '.btn-eliminar-detalle', function() {
            eliminarDetalle(this);
        });
        
        // Botones de guardado
        $('#btnGuardarInfraccion').on('click', guardarInfraccion);
        $('#btnActualizarInfraccion').on('click', actualizarInfraccion);
        $('#btnConfirmarEliminacion').on('click', confirmarEliminacion);
    }
    
    function setupFilterEvents() {
        $('#btnFiltrar').on('click', aplicarFiltros);
        
        // Filtros en tiempo real
        $('#busqueda').on('keyup', debounce(aplicarFiltros, 500));
        $('#filtroGravedad, #filtroAplicaSobre').on('change', aplicarFiltros);
    }

    // ================================
    // GESTIÓN DE DETALLES
    // ================================
    
    function agregarDetalle() {
        const template = document.getElementById('templateDetalle');
        const contenedor = document.getElementById('contenedorDetalles');
        
        const nuevoDetalle = template.content.cloneNode(true);
        
        // Reemplazar INDEX con el contador actual
        const elementos = nuevoDetalle.querySelectorAll('[name*="INDEX"]');
        elementos.forEach(elemento => {
            elemento.name = elemento.name.replace('INDEX', contadorDetalles);
        });
        
        contenedor.appendChild(nuevoDetalle);
        contadorDetalles++;
        
        // Mínimo un detalle
        actualizarBotonesEliminar('#contenedorDetalles');
    }
    
    function agregarDetalleEditar() {
        const template = document.getElementById('templateDetalleEditar');
        const contenedor = document.getElementById('contenedorDetallesEditar');
        
        const nuevoDetalle = template.content.cloneNode(true);
        
        // Reemplazar INDEX con el contador actual
        const elementos = nuevoDetalle.querySelectorAll('[name*="INDEX"]');
        elementos.forEach(elemento => {
            elemento.name = elemento.name.replace('INDEX', contadorDetallesEditar);
        });
        
        contenedor.appendChild(nuevoDetalle);
        contadorDetallesEditar++;
        
        actualizarBotonesEliminar('#contenedorDetallesEditar');
    }
    
    function eliminarDetalle(boton) {
        const detalleItem = boton.closest('.detalle-item');
        const contenedor = detalleItem.parentElement;
        
        // No permitir eliminar si es el único detalle
        const totalDetalles = contenedor.querySelectorAll('.detalle-item').length;
        if (totalDetalles <= 1) {
            mostrarAlerta('Debe mantener al menos un detalle por infracción', 'warning');
            return;
        }
        
        detalleItem.remove();
        actualizarBotonesEliminar('#' + contenedor.id);
    }
    
    function actualizarBotonesEliminar(contenedorSelector) {
        const contenedor = document.querySelector(contenedorSelector);
        const detalles = contenedor.querySelectorAll('.detalle-item');
        const botones = contenedor.querySelectorAll('.btn-eliminar-detalle');
        
        // Deshabilitar botón si solo hay un detalle
        botones.forEach(boton => {
            boton.disabled = detalles.length <= 1;
        });
    }

    // ================================
    // OPERACIONES CRUD
    // ================================
    
    function guardarInfraccion() {
        if (!validarFormulario('#formCrearInfraccion')) {
            return;
        }
        
        const formData = new FormData(document.getElementById('formCrearInfraccion'));
        const data = serializarFormulario(formData);
        
        mostrarCargando('#btnGuardarInfraccion');
        
        fetch(ROUTES.store, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            ocultarCargando('#btnGuardarInfraccion');
            
            if (data.success) {
                $('#crearInfraccionModal').modal('hide');
                mostrarAlerta('Infracción creada exitosamente', 'success');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                mostrarErrores(data.errors || {}, '#formCrearInfraccion');
                mostrarAlerta(data.message || 'Error al crear la infracción', 'error');
            }
        })
        .catch(error => {
            ocultarCargando('#btnGuardarInfraccion');
            console.error('Error:', error);
            mostrarAlerta('Error de conexión. Por favor, intente nuevamente.', 'error');
        });
    }
    
    function verInfraccion(id) {
        infraccionIdActual = id;
        
        $('#verInfraccionModal').modal('show');
        $('#contenidoVerInfraccion').hide();
        $('#loadingVerInfraccion').show();
        
        fetch(ROUTES.show + id)
        .then(response => response.json())
        .then(data => {
            $('#loadingVerInfraccion').hide();
            
            if (data.success) {
                cargarDatosVer(data.data);
                $('#contenidoVerInfraccion').show();
            } else {
                mostrarAlerta('Error al cargar la información', 'error');
                $('#verInfraccionModal').modal('hide');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            $('#loadingVerInfraccion').hide();
            mostrarAlerta('Error de conexión', 'error');
            $('#verInfraccionModal').modal('hide');
        });
    }
    
    function editarInfraccion(id) {
        infraccionIdActual = id;
        
        $('#editarInfraccionModal').modal('show');
        $('#formEditarInfraccion').hide();
        $('#loadingEditarInfraccion').show();
        
        fetch(ROUTES.show + id)
        .then(response => response.json())
        .then(data => {
            $('#loadingEditarInfraccion').hide();
            
            if (data.success) {
                cargarDatosEditar(data.data);
                $('#formEditarInfraccion').show();
            } else {
                mostrarAlerta('Error al cargar la información', 'error');
                $('#editarInfraccionModal').modal('hide');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            $('#loadingEditarInfraccion').hide();
            mostrarAlerta('Error de conexión', 'error');
            $('#editarInfraccionModal').modal('hide');
        });
    }
    
    function actualizarInfraccion() {
        if (!validarFormulario('#formEditarInfraccion')) {
            return;
        }
        
        const formData = new FormData(document.getElementById('formEditarInfraccion'));
        const data = serializarFormulario(formData);
        
        mostrarCargando('#btnActualizarInfraccion');
        
        fetch(ROUTES.update + infraccionIdActual, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            ocultarCargando('#btnActualizarInfraccion');
            
            if (data.success) {
                $('#editarInfraccionModal').modal('hide');
                mostrarAlerta('Infracción actualizada exitosamente', 'success');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                mostrarErrores(data.errors || {}, '#formEditarInfraccion');
                mostrarAlerta(data.message || 'Error al actualizar la infracción', 'error');
            }
        })
        .catch(error => {
            ocultarCargando('#btnActualizarInfraccion');
            console.error('Error:', error);
            mostrarAlerta('Error de conexión. Por favor, intente nuevamente.', 'error');
        });
    }
    
    function eliminarInfraccion(id, codigo) {
        // Buscar datos de la infracción en la tabla
        const fila = document.querySelector(`button[onclick="eliminarInfraccion(${id}, '${codigo}')"]`).closest('tr');
        const aplicaSobre = fila.children[1].textContent.trim();
        const sancion = fila.children[4].textContent.trim();
        const gravedad = fila.children[5].textContent.trim();
        const detalles = fila.children[6].textContent.trim();
        
        // Cargar datos en el modal
        document.getElementById('idInfraccionEliminar').value = id;
        document.getElementById('codigoEliminar').textContent = codigo;
        document.getElementById('aplicaSobreEliminar').textContent = aplicaSobre;
        document.getElementById('sancionEliminar').textContent = sancion;
        document.getElementById('gravedadEliminar').textContent = gravedad;
        document.getElementById('detallesEliminar').textContent = detalles;
        
        // Resetear checkbox
        document.getElementById('confirmarEliminacion').checked = false;
        document.getElementById('btnConfirmarEliminacion').disabled = true;
        
        $('#eliminarInfraccionModal').modal('show');
    }
    
    function confirmarEliminacion() {
        const id = document.getElementById('idInfraccionEliminar').value;
        
        mostrarCargando('#btnConfirmarEliminacion');
        
        fetch(ROUTES.destroy + id, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            ocultarCargando('#btnConfirmarEliminacion');
            
            if (data.success) {
                $('#eliminarInfraccionModal').modal('hide');
                $('#eliminacionExitosaModal').modal('show');
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                mostrarAlerta(data.message || 'Error al eliminar la infracción', 'error');
            }
        })
        .catch(error => {
            ocultarCargando('#btnConfirmarEliminacion');
            console.error('Error:', error);
            mostrarAlerta('Error de conexión. Por favor, intente nuevamente.', 'error');
        });
    }

    // ================================
    // FUNCIONES DE CARGA DE DATOS
    // ================================
    
    function cargarDatosVer(infraccion) {
        // Cargar datos básicos
        document.getElementById('ver_codigo').textContent = infraccion.codigo;
        document.getElementById('ver_aplica_sobre').textContent = infraccion.aplica_sobre;
        document.getElementById('ver_reglamento').textContent = infraccion.reglamento;
        document.getElementById('ver_norma_modificatoria').textContent = infraccion.norma_modificatoria;
        document.getElementById('ver_clase_pago').textContent = infraccion.clase_pago;
        document.getElementById('ver_sancion').textContent = infraccion.sancion;
        document.getElementById('ver_tipo').textContent = infraccion.tipo;
        document.getElementById('ver_medida_preventiva').textContent = infraccion.medida_preventiva;
        document.getElementById('ver_otros_responsables').textContent = infraccion.otros_responsables__otros_beneficios || 'N/A';
        
        // Gravedad con formato
        const gravedadElement = document.getElementById('ver_gravedad');
        let gravedadTexto = '';
        let badgeClass = '';
        
        switch(infraccion.gravedad) {
            case 'muy_grave':
                gravedadTexto = 'Muy Grave';
                badgeClass = 'bg-danger';
                break;
            case 'grave':
                gravedadTexto = 'Grave';
                badgeClass = 'bg-warning';
                break;
            case 'leve':
                gravedadTexto = 'Leve';
                badgeClass = 'bg-info';
                break;
        }
        
        gravedadElement.innerHTML = `<span class="badge ${badgeClass}">${gravedadTexto}</span>`;
        
        // Fechas
        document.getElementById('ver_created_at').textContent = formatearFecha(infraccion.created_at);
        document.getElementById('ver_updated_at').textContent = formatearFecha(infraccion.updated_at);
        
        // Cargar detalles
        cargarDetallesVer(infraccion.detalles);
    }
    
    function cargarDetallesVer(detalles) {
        const contenedor = document.getElementById('contenedorVerDetalles');
        const template = document.getElementById('templateVerDetalle');
        const cantidadBadge = document.getElementById('cantidadDetalles');
        
        contenedor.innerHTML = '';
        cantidadBadge.textContent = `${detalles.length} detalle(s)`;
        
        detalles.forEach(detalle => {
            const elemento = template.content.cloneNode(true);
            
            elemento.querySelector('.detalle-descripcion').textContent = detalle.descripcion;
            elemento.querySelector('.detalle-subcategoria').textContent = detalle.subcategoria || 'General';
            elemento.querySelector('.detalle-descripcion-detallada').textContent = detalle.descripcion_detallada;
            elemento.querySelector('.detalle-condiciones-especiales').textContent = detalle.condiciones_especiales || 'N/A';
            
            contenedor.appendChild(elemento);
        });
    }
    
    function cargarDatosEditar(infraccion) {
        // Cargar datos básicos
        document.getElementById('editar_infraccion_id').value = infraccion.id;
        document.getElementById('editar_codigo').value = infraccion.codigo;
        document.getElementById('editar_aplica_sobre').value = infraccion.aplica_sobre;
        document.getElementById('editar_reglamento').value = infraccion.reglamento;
        document.getElementById('editar_norma_modificatoria').value = infraccion.norma_modificatoria;
        document.getElementById('editar_clase_pago').value = infraccion.clase_pago;
        document.getElementById('editar_sancion').value = infraccion.sancion;
        document.getElementById('editar_tipo').value = infraccion.tipo;
        document.getElementById('editar_medida_preventiva').value = infraccion.medida_preventiva;
        document.getElementById('editar_gravedad').value = infraccion.gravedad;
        document.getElementById('editar_otros_responsables').value = infraccion.otros_responsables__otros_beneficios || '';
        
        // Cargar detalles
        cargarDetallesEditar(infraccion.detalles);
    }
    
    function cargarDetallesEditar(detalles) {
        const contenedor = document.getElementById('contenedorDetallesEditar');
        contenedor.innerHTML = '';
        contadorDetallesEditar = 0;
        
        detalles.forEach(detalle => {
            agregarDetalleEditar();
            
            const ultimoDetalle = contenedor.lastElementChild;
            ultimoDetalle.querySelector('[name*="descripcion"]').value = detalle.descripcion;
            ultimoDetalle.querySelector('[name*="subcategoria"]').value = detalle.subcategoria || '';
            ultimoDetalle.querySelector('[name*="descripcion_detallada"]').value = detalle.descripcion_detallada;
            ultimoDetalle.querySelector('[name*="condiciones_especiales"]').value = detalle.condiciones_especiales || '';
            ultimoDetalle.querySelector('.detalle-id').value = detalle.id;
        });
        
        actualizarBotonesEliminar('#contenedorDetallesEditar');
    }

    // ================================
    // FUNCIONES DE UTILIDAD
    // ================================
    
    function serializarFormulario(formData) {
        const data = {};
        const detalles = [];
        
        // Procesar campos normales
        for (let [key, value] of formData.entries()) {
            if (key.startsWith('detalles[')) {
                // Procesar detalles por separado
                continue;
            }
            data[key] = value;
        }
        
        // Procesar detalles
        const detalleIndices = new Set();
        for (let [key, value] of formData.entries()) {
            if (key.startsWith('detalles[')) {
                const match = key.match(/detalles\[(\d+)\]\[(\w+)\]/);
                if (match) {
                    detalleIndices.add(match[1]);
                }
            }
        }
        
        detalleIndices.forEach(index => {
            const detalle = {};
            for (let [key, value] of formData.entries()) {
                if (key.startsWith(`detalles[${index}]`)) {
                    const campo = key.match(/detalles\[\d+\]\[(\w+)\]/)[1];
                    detalle[campo] = value;
                }
            }
            if (detalle.descripcion && detalle.descripcion_detallada) {
                detalles.push(detalle);
            }
        });
        
        data.detalles = detalles;
        return data;
    }
    
    function validarFormulario(selector) {
        const form = document.querySelector(selector);
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        let valido = true;
        
        // Limpiar errores previos
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.classList.add('is-invalid');
                const feedback = input.parentElement.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.textContent = 'Este campo es obligatorio';
                }
                valido = false;
            }
        });
        
        // Validar que hay al menos un detalle
        const contenedorDetalles = form.querySelector('[id*="contenedorDetalles"]');
        if (contenedorDetalles && contenedorDetalles.children.length === 0) {
            mostrarAlerta('Debe agregar al menos un detalle', 'warning');
            valido = false;
        }
        
        return valido;
    }
    
    function mostrarErrores(errores, formularioSelector) {
        Object.keys(errores).forEach(campo => {
            const input = document.querySelector(`${formularioSelector} [name="${campo}"]`);
            if (input) {
                input.classList.add('is-invalid');
                const feedback = input.parentElement.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.textContent = errores[campo][0];
                }
            }
        });
    }
    
    function limpiarFormulario(selector) {
        const form = document.querySelector(selector);
        form.reset();
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    }
    
    function mostrarCargando(selector) {
        const boton = document.querySelector(selector);
        boton.disabled = true;
        boton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
    }
    
    function ocultarCargando(selector) {
        const boton = document.querySelector(selector);
        boton.disabled = false;
        
        // Restaurar texto original basado en el botón
        if (selector.includes('Guardar')) {
            boton.innerHTML = '<i class="fas fa-save"></i> Guardar Infracción';
        } else if (selector.includes('Actualizar')) {
            boton.innerHTML = '<i class="fas fa-save"></i> Actualizar Infracción';
        } else if (selector.includes('Confirmar')) {
            boton.innerHTML = '<i class="fas fa-trash"></i> Sí, Eliminar';
        }
    }
    
    function mostrarAlerta(mensaje, tipo) {
        // Implementar sistema de alertas (puedes usar SweetAlert2 o similar)
        let icono = '';
        let clase = '';
        
        switch(tipo) {
            case 'success':
                icono = 'fa-check-circle';
                clase = 'alert-success';
                break;
            case 'error':
                icono = 'fa-exclamation-circle';
                clase = 'alert-danger';
                break;
            case 'warning':
                icono = 'fa-exclamation-triangle';
                clase = 'alert-warning';
                break;
            default:
                icono = 'fa-info-circle';
                clase = 'alert-info';
        }
        
        // Crear y mostrar alerta
        const alerta = document.createElement('div');
        alerta.className = `alert ${clase} alert-dismissible fade show position-fixed`;
        alerta.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alerta.innerHTML = `
            <i class="fas ${icono}"></i> ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alerta);
        
        // Auto-remover después de 5 segundos
        setTimeout(() => {
            if (alerta.parentElement) {
                alerta.remove();
            }
        }, 5000);
    }
    
    function formatearFecha(fecha) {
        return new Date(fecha).toLocaleString('es-PE', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
    
    function aplicarFiltros() {
        // Implementar filtros AJAX si es necesario
        console.log('Aplicando filtros...');
    }
    
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // ================================
    // FUNCIONES GLOBALES
    // ================================
    
    // Hacer funciones disponibles globalmente para los onclick
    window.verInfraccion = verInfraccion;
    window.editarInfraccion = editarInfraccion;
    window.eliminarInfraccion = eliminarInfraccion;
});