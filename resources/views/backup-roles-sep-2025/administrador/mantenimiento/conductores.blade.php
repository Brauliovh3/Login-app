@php
    /**
     * Vista mejorada de mantenimiento de conductores
     * Variables esperadas: $conductores (colección)
     */
@endphp

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mantenimiento - Conductores</title>
    <!-- Bootswatch Lumen theme (opción 3 solicitada) -->
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.0/dist/lumen/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-header-toolbar { display:flex; gap:.5rem; align-items:center; }
        .table-fixed thead th { position: sticky; top: 0; background: #fff; }
    </style>
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0">Mantenimiento — Conductores</h4>
            </div>
            <div class="card-header-toolbar">
                <a href="/admin/dashboard" class="btn btn-outline-secondary btn-sm">Volver</a>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCrear">Nuevo conductor</button>
            </div>
        </div>
        <div class="card-body">
            @if(isset($conductores) && count($conductores) > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-fixed align-middle">
                        <thead class="table-light">
                        <tr>
                            <th>Nombre</th>
                            <th>DNI</th>
                            <th>Licencia</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($conductores as $c)
                            <tr>
                                <td class="fw-semibold">{{ $c->nombre_completo ?? trim(($c->nombres ?? '') . ' ' . ($c->apellidos ?? '')) }}</td>
                                <td>{{ $c->dni ?? ($c->documento ?? '-') }}</td>
                                <td>{{ $c->licencia ?? ($c->numero_licencia ?? '-') }}</td>
                                <td>
                                    <span class="badge bg-{{ ($c->estado ?? ($c->estado_licencia ?? 'inactivo')) === 'activo' ? 'success' : 'secondary' }}">{{ $c->estado ?? ($c->estado_licencia ?? 'inactivo') }}</span>
                                </td>
                                <td class="text-end">
                                    <!-- No se muestra el id como columna visible; se puede usar para acciones JS/data-attrs si es necesario -->
                                    <button class="btn btn-sm btn-outline-primary btn-editar" data-id="{{ $c->id }}" data-nombres="{{ $c->nombres ?? '' }}" data-apellidos="{{ $c->apellidos ?? '' }}" data-dni="{{ $c->dni ?? '' }}" data-licencia="{{ $c->licencia ?? '' }}">Editar</button>
                                    <button class="btn btn-sm btn-outline-danger btn-eliminar" data-id="{{ $c->id }}">Eliminar</button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">No hay conductores para mostrar.</div>
            @endif
        </div>
        <div class="card-footer text-muted small d-flex justify-content-between align-items-center">
            <div>Mostrando {{ $conductores->total() ?? (isset($conductores) ? count($conductores) : 0) }} conductores</div>
            <div>
                {{ $conductores->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear -->
<div class="modal fade" id="modalCrear" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo Conductor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formCrear">
                    <div id="crearErrors" class="alert alert-danger d-none"></div>
                    <div class="mb-2">
                        <label class="form-label">Nombres</label>
                        <input name="nombres" class="form-control" required />
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Apellidos</label>
                        <input name="apellidos" class="form-control" />
                    </div>
                    <div class="mb-2">
                        <label class="form-label">DNI</label>
                        <input name="dni" class="form-control" />
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Licencia</label>
                        <input name="licencia" class="form-control" />
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarCrear">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Conductor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formEditar">
                    <div id="editarErrors" class="alert alert-danger d-none"></div>
                    <input type="hidden" name="id" />
                    <div class="mb-2">
                        <label class="form-label">Nombres</label>
                        <input name="nombres" class="form-control" required />
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Apellidos</label>
                        <input name="apellidos" class="form-control" />
                    </div>
                    <div class="mb-2">
                        <label class="form-label">DNI</label>
                        <input name="dni" class="form-control" />
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Licencia</label>
                        <input name="licencia" class="form-control" />
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarEditar">Guardar cambios</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
        // Crear conductor (POST simple, ajuste URL según rutas)
        document.getElementById('btnGuardarCrear').addEventListener('click', function(){
                const btn = this;
                const form = document.getElementById('formCrear');
                const data = Object.fromEntries(new FormData(form).entries());
                const errContainer = document.getElementById('crearErrors');
                errContainer.classList.add('d-none'); errContainer.innerHTML = '';
                btn.disabled = true; const originalText = btn.textContent; btn.textContent = 'Guardando...';
        fetch('/admin/conductores', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
            body: JSON.stringify(data)
        })
                .then(async r => {
                    const ct = (r.headers.get('content-type') || '');
                    if (r.ok) return r.json();
                    // manejo JSON
                    if (ct.includes('application/json')) {
                        const json = await r.json();
                        // CSRF específico
                        if (json.message && json.message.toLowerCase().includes('csrf')) {
                            errContainer.classList.remove('d-none'); errContainer.textContent = 'CSRF token mismatch. Refresca la página e intenta de nuevo.';
                            throw new Error('csrf');
                        }
                        if (r.status === 422 && json.errors) {
                            errContainer.classList.remove('d-none');
                            Object.values(json.errors).forEach(arr => arr.forEach(msg => {
                                const p = document.createElement('div'); p.textContent = msg; errContainer.appendChild(p);
                            }));
                            throw new Error('validation');
                        }
                        // otros mensajes de error
                        errContainer.classList.remove('d-none');
                        errContainer.textContent = json.message || json.error || JSON.stringify(json);
                        throw new Error('server');
                    }
                    const text = await r.text();
                    throw new Error(text || 'Error interno');
                })
                .then(res => { 
                    // cerrar modal y recargar
                    var modalEl = document.getElementById('modalCrear');
                    var modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                    location.reload();
                })
                .catch(e=> {
                    if (e.message === 'validation' || e.message === 'csrf' || e.message === 'server') {
                        // ya mostrado en errContainer
                    } else {
                        errContainer.classList.remove('d-none'); errContainer.textContent = e.message || 'Error de red';
                    }
                })
                .finally(()=>{ btn.disabled = false; btn.textContent = originalText; });
        });

        // Abrir modal editar y rellenar campos
        document.querySelectorAll('.btn-editar').forEach(btn => {
                btn.addEventListener('click', function(){
                        const id = this.dataset.id;
                        document.querySelector('#modalEditar input[name="id"]').value = id;
                        document.querySelector('#modalEditar input[name="nombres"]').value = this.dataset.nombres || '';
                        document.querySelector('#modalEditar input[name="apellidos"]').value = this.dataset.apellidos || '';
                        document.querySelector('#modalEditar input[name="dni"]').value = this.dataset.dni || '';
                        document.querySelector('#modalEditar input[name="licencia"]').value = this.dataset.licencia || '';
                        var modal = new bootstrap.Modal(document.getElementById('modalEditar'));
                        modal.show();
                });
        });

        // Guardar edición (PUT)
        document.getElementById('btnGuardarEditar').addEventListener('click', function(){
                const form = document.getElementById('formEditar');
                const data = Object.fromEntries(new FormData(form).entries());
                const id = data.id;
                const btnE = document.getElementById('btnGuardarEditar');
                const errContainerE = document.getElementById('editarErrors'); errContainerE.classList.add('d-none'); errContainerE.innerHTML = '';
                btnE.disabled = true; const origE = btnE.textContent; btnE.textContent = 'Guardando...';
        fetch('/admin/conductores/' + id, {
            method: 'PUT',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
            body: JSON.stringify(data)
        })
                .then(async r => {
                    const ct = (r.headers.get('content-type') || '');
                    if (r.ok) return r.json();
                    if (ct.includes('application/json')) {
                        const json = await r.json();
                        if (json.message && json.message.toLowerCase().includes('csrf')) {
                            errContainerE.classList.remove('d-none'); errContainerE.textContent = 'CSRF token mismatch. Refresca la página e intenta de nuevo.'; throw new Error('csrf');
                        }
                        if (r.status === 422 && json.errors) {
                            errContainerE.classList.remove('d-none');
                            Object.values(json.errors).forEach(arr => arr.forEach(msg => { const p = document.createElement('div'); p.textContent = msg; errContainerE.appendChild(p); }));
                            throw new Error('validation');
                        }
                        errContainerE.classList.remove('d-none'); errContainerE.textContent = json.message || json.error || JSON.stringify(json); throw new Error('server');
                    }
                    const text = await r.text(); throw new Error(text || 'Error interno');
                })
                .then(res => { var modalEl = document.getElementById('modalEditar'); var modal = bootstrap.Modal.getInstance(modalEl); if (modal) modal.hide(); location.reload(); })
                .catch(e=> {
                    if (!(e.message === 'validation' || e.message === 'csrf' || e.message === 'server')) {
                        errContainerE.classList.remove('d-none'); errContainerE.textContent = e.message || 'Error de red';
                    }
                })
                .finally(()=>{ btnE.disabled = false; btnE.textContent = origE; });
        });

        // Eliminar (DELETE)
        document.querySelectorAll('.btn-eliminar').forEach(btn => {
                btn.addEventListener('click', function(){
                        if(!confirm('¿Eliminar este conductor?')) return;
                        const id = this.dataset.id;
            fetch('/admin/conductores/' + id, {
                method: 'DELETE',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' }
            })
                        .then(async r => {
                            if (r.ok) return r.json();
                            const ct = (r.headers.get('content-type') || '');
                            if (ct.includes('application/json')) {
                                const json = await r.json(); throw new Error(json.message || json.error || JSON.stringify(json));
                            }
                            const text = await r.text(); throw new Error(text || 'Error');
                        })
                        .then(res => { location.reload(); })
                        .catch(e=> { alert(e.message || 'Error'); });
                });
        });
});
</script>
</html>
