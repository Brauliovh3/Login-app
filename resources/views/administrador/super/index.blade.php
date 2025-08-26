@extends('layouts.dashboard')

@section('title', 'Superadmin - Herramientas')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-0">Panel Superadmin</h3>
            <small class="text-muted">Acciones potentes. Solo rol <strong>superadmin</strong>.</small>
        </div>
        <div>
            <span class="badge bg-danger">Oculto</span>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">Información</div>
                <div class="card-body">
                    <p class="small text-muted">Consulta información de la aplicación y servidor.</p>
                    <button id="btnAppInfo" class="btn btn-info btn-sm">Mostrar info app/servidor</button>
                    <pre id="appInfo" class="mt-3 small p-2 bg-light rounded" style="display:none; white-space:pre-wrap;"></pre>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">Mantenimiento - Artisan</div>
                <div class="card-body">
                    <p class="small text-muted">Comandos permitidos (whitelist). Cada acción mostrará advertencia y log.</p>
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-primary btn-sm run-cmd" data-cmd="cache:clear">cache:clear</button>
                        <button class="btn btn-secondary btn-sm run-cmd" data-cmd="config:cache">config:cache</button>
                        <button class="btn btn-warning btn-sm run-cmd" data-cmd="route:clear">route:clear</button>
                        <button class="btn btn-dark btn-sm run-cmd" data-cmd="view:clear">view:clear</button>
                    </div>
                    <div class="mt-3">
                        <button id="btnRunAll" class="btn btn-outline-success btn-sm">Ejecutar todos (secuencial)</button>
                    </div>
                    <pre id="cmdOutput" class="mt-3 small p-2 bg-light rounded" style="display:none; white-space:pre-wrap; max-height:220px; overflow:auto;"></pre>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">Usuarios y Estadísticas</div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Estadísticas rápidas</h6>
                            <small class="text-muted">Actas y usuarios</small>
                        </div>
                        <div>
                            <button id="btnRefreshStats" class="btn btn-sm btn-outline-primary">Actualizar</button>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-4 text-center">
                            <h4 id="statActas">-</h4>
                            <small class="text-muted">Total actas</small>
                        </div>
                        <div class="col-4 text-center">
                            <h4 id="statUsuarios">-</h4>
                            <small class="text-muted">Total usuarios</small>
                        </div>
                        <div class="col-4 text-center">
                            <h4 id="statRecent">-</h4>
                            <small class="text-muted">Actas recientes</small>
                        </div>
                    </div>

                    <hr />
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Usuarios (últimos 50)</h6>
                        <button id="btnLoadUsers" class="btn btn-sm btn-outline-secondary">Cargar usuarios</button>
                    </div>
                    <div style="max-height:240px; overflow:auto;">
                        <table class="table table-sm table-striped" id="usersTable">
                            <thead><tr><th>ID</th><th>Usuario</th><th>Email</th><th>Rol</th><th>Estado</th><th>Acciones</th></tr></thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card shadow-sm border-danger">
                <div class="card-header bg-danger text-white">Peligroso — Reset actas</div>
                <div class="card-body">
                    <p class="small text-muted">Reinicia la numeración de la tabla <code>actas</code>. Si existen registros puede ser destructivo.</p>
                    <div class="mb-2">
                        <input id="superConfirm" class="form-control" placeholder="Escribe CONFIRMAR para forzar (opcional)" />
                    </div>
                    <div class="d-flex gap-2">
                        <button id="btnResetActasSuper" class="btn btn-danger">Reset actas</button>
                        <button id="btnResetActasPreview" class="btn btn-outline-danger">Vista previa (segura)</button>
                    </div>
                    <div id="resetActasFeedback" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toasts -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
        <div id="toastContainer"></div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Confirmar acción</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p id="confirmModalBody" class="small text-muted"></p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button id="confirmModalOk" type="button" class="btn btn-danger">Confirmar</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Global loader with a small cart animation -->
    <div id="globalLoader" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.35); z-index:1200; align-items:center; justify-content:center;">
        <div style="width:320px; max-width:90%; text-align:center; color:#fff;">
            <div id="cartProgress" style="height:90px; position:relative;">
                <div id="cartTrack" style="height:6px; background:rgba(255,255,255,0.15); border-radius:4px; position:absolute; left:10px; right:10px; top:40px;"></div>
                <div id="cart" style="position:absolute; left:10px; top:0; transform:translateX(0); transition:transform 0.2s linear;">
                    <!-- simple cart icon -->
                    <svg width="80" height="50" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M3 3h2l.4 2M7 13h10l4-8H5.4" stroke="#fff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                      <circle cx="10" cy="20" r="1.6" fill="#fff" />
                      <circle cx="18" cy="20" r="1.6" fill="#fff" />
                    </svg>
                </div>
            </div>
            <div class="mt-2 small">Procesando... <span id="loaderPercent">0%</span></div>
            <div id="loaderMsg" class="small text-light mt-1"></div>
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script>
(() => {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    function showToast(title, body, type = 'info') {
        const id = 't' + Date.now();
        const color = type === 'error' ? 'bg-danger text-white' : (type === 'success' ? 'bg-success text-white' : 'bg-primary text-white');
        const html = `
            <div id="${id}" class="toast ${color}" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
              <div class="d-flex">
                <div class="toast-body small">${title}<div class="small text-white-50">${body}</div></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
              </div>
            </div>`;
        const container = document.getElementById('toastContainer');
        container.insertAdjacentHTML('beforeend', html);
        const toastEl = document.getElementById(id);
        const bs = new bootstrap.Toast(toastEl);
        bs.show();
        toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
    }

    // Global loader controls
    const loader = document.getElementById('globalLoader');
    const cart = document.getElementById('cart');
    const percent = document.getElementById('loaderPercent');
    const loaderMsg = document.getElementById('loaderMsg');
    let loaderInterval;

    function startLoader(initialMsg = '') {
        loader.style.display = 'flex';
        loaderMsg.textContent = initialMsg;
        let p = 0;
        percent.textContent = p + '%';
        cart.style.transform = `translateX(${p}%)`;
        clearInterval(loaderInterval);
        loaderInterval = setInterval(() => {
            // simulate progress but never reach 100% until finished
            p = Math.min(95, p + Math.floor(Math.random() * 8) + 3);
            percent.textContent = p + '%';
            cart.style.transform = `translateX(${p}%)`;
        }, 400);
    }
    function finishLoader(finalMsg = '') {
        clearInterval(loaderInterval);
        percent.textContent = '100%';
        cart.style.transform = 'translateX(100%)';
        loaderMsg.textContent = finalMsg;
        setTimeout(() => { loader.style.display = 'none'; cart.style.transform = 'translateX(0)'; percent.textContent = '0%'; loaderMsg.textContent = ''; }, 800);
    }

    async function doPost(url, body = {}, opts = {}) {
        startLoader(opts.message || 'Ejecutando...');
        try {
            const res = await fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token }, body: JSON.stringify(body) });
            const data = await res.json();
            finishLoader(opts.finalMessage || 'Finalizado');
            return data;
        } catch (e) {
            finishLoader('Error');
            throw e;
        }
    }

    async function doGet(url, opts = {}) {
        startLoader(opts.message || 'Consultando...');
        try {
            const res = await fetch(url);
            const data = await res.json();
            finishLoader(opts.finalMessage || 'Finalizado');
            return data;
        } catch (e) {
            finishLoader('Error');
            throw e;
        }
    }

    // App info
    document.getElementById('btnAppInfo')?.addEventListener('click', async function() {
        const pre = document.getElementById('appInfo');
        pre.style.display = 'block';
        pre.textContent = 'Cargando...';
        try {
            const data = await doGet('{{ route('admin.super.app-info') }}', { message: 'Obteniendo info...' });
            pre.textContent = JSON.stringify(data.info || data, null, 2);
        } catch (e) {
            pre.textContent = 'Error: ' + e.message;
            showToast('Error', 'No se pudo obtener info', 'error');
        }
    });

    // Run single command
    document.querySelectorAll('.run-cmd').forEach(function(btn) {
        btn.addEventListener('click', async function() {
            const cmd = this.dataset.cmd;
            if (!confirm(`Ejecutar comando: ${cmd}? Esta acción será registrada.`)) return;
            const outPre = document.getElementById('cmdOutput');
            outPre.style.display = 'block';
            outPre.textContent = `Ejecutando ${cmd}...`;
            try {
                const data = await doPost('{{ route('admin.super.run-command') }}', { command: cmd }, { message: `Ejecutando ${cmd}...`, finalMessage: `${cmd} finalizado` });
                outPre.textContent = JSON.stringify(data, null, 2);
                showToast('Comando ejecutado', cmd, 'success');
            } catch (e) {
                outPre.textContent = 'Error: ' + e.message;
                showToast('Error', `Fallo al ejecutar ${cmd}`, 'error');
            }
        });
    });

    // Run all sequentially
    document.getElementById('btnRunAll')?.addEventListener('click', async function() {
        const cmds = ['cache:clear','config:cache','route:clear','view:clear'];
        if (!confirm('Ejecutar todos los comandos permitidos en secuencia?')) return;
        const outPre = document.getElementById('cmdOutput');
        outPre.style.display = 'block';
        outPre.textContent = '';
        for (const c of cmds) {
            outPre.textContent += `\n--- Ejecutando ${c} ---\n`;
            try {
                const data = await doPost('{{ route('admin.super.run-command') }}', { command: c }, { message: `Ejecutando ${c}...`, finalMessage: `${c} finalizado` });
                outPre.textContent += JSON.stringify(data, null, 2) + '\n';
            } catch (e) {
                outPre.textContent += `Error al ejecutar ${c}: ${e.message}\n`;
                showToast('Error', `Fallo en ${c}`, 'error');
            }
            // small pause between commands
            await new Promise(r => setTimeout(r, 400));
        }
        showToast('Secuencia', 'Todos los comandos han terminado', 'success');
    });

    // Reset actas: preview (safe) and destructive
    document.getElementById('btnResetActasPreview')?.addEventListener('click', async function() {
        try {
            const data = await doPost('{{ route('admin.super.reset-actas') }}', { force: false }, { message: 'Comprobando actas...', finalMessage: 'Comprobación terminada' });
            document.getElementById('resetActasFeedback').innerText = data.message || JSON.stringify(data);
            showToast('Vista previa', data.message || 'Revisado', 'info');
        } catch (e) {
            document.getElementById('resetActasFeedback').innerText = 'Error: ' + e.message;
            showToast('Error', 'No se pudo previsualizar reset', 'error');
        }
    });

    document.getElementById('btnResetActasSuper')?.addEventListener('click', async function() {
        const v = (document.getElementById('superConfirm').value || '').trim();
        const force = v === 'CONFIRMAR';
        const msg = force ? 'Atención: esta acción TRUNCARÁ la tabla actas. Continúe solo si está seguro.' : 'No destructivo: intentará establecer AUTO_INCREMENT a 1 si la tabla está vacía.';
        // show modal
        document.getElementById('confirmModalBody').textContent = msg;
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
        document.getElementById('confirmModalOk').onclick = async () => {
            confirmModal.hide();
            try {
                const data = await doPost('{{ route('admin.super.reset-actas') }}', { force: force }, { message: 'Procesando reset actas...', finalMessage: 'Reset finalizado' });
                document.getElementById('resetActasFeedback').innerText = data.message || JSON.stringify(data);
                showToast('Reset actas', data.message || 'Completado', 'success');
            } catch (e) {
                document.getElementById('resetActasFeedback').innerText = 'Error: ' + e.message;
                showToast('Error', 'Fallo reset actas', 'error');
            }
        };
        confirmModal.show();
    });

})();
</script>
<script>
// Users and Stats handlers
(async function(){
    const statsUrl = '{{ route('admin.super.stats') }}';
    const usersUrl = '{{ route('admin.super.users') }}';

    async function loadStats(){
        try{
            const r = await doGet(statsUrl, { message: 'Cargando estadísticas...', finalMessage: 'Estadísticas cargadas' });
            if(r.ok){
                document.getElementById('statActas').textContent = r.stats.total_actas;
                document.getElementById('statUsuarios').textContent = r.stats.total_usuarios;
                document.getElementById('statRecent').textContent = (r.stats.actas_recientes || []).length;
            }
        }catch(e){ console.error(e); }
    }

    async function loadUsers(){
        try{
            const r = await doGet(usersUrl, { message: 'Cargando usuarios...', finalMessage: 'Usuarios cargados' });
            if(r.ok){
                const tbody = document.querySelector('#usersTable tbody');
                tbody.innerHTML = '';
                r.users.forEach(u => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${u.id}</td>
                        <td>${u.username}</td>
                        <td>${u.email || ''}</td>
                        <td>${u.role}</td>
                        <td>${u.status}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary btn-approve" data-id="${u.id}">Aprobar</button>
                            <button class="btn btn-sm btn-outline-secondary btn-toggle" data-id="${u.id}">Toggle</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });

                // attach events
                document.querySelectorAll('.btn-toggle').forEach(b=> b.addEventListener('click', async ()=>{
                    const id = b.dataset.id;
                    try{
                        const res = await doPost(`{{ url('/admin/super/users') }}/${id}/toggle-status`, {}, { message: 'Cambiando estado...', finalMessage: 'Estado cambiado' });
                        showToast('Usuario', res.message || 'Actualizado', 'success');
                        loadUsers(); loadStats();
                    }catch(e){ showToast('Error','No se pudo cambiar estado','error'); }
                }));

                document.querySelectorAll('.btn-approve').forEach(b=> b.addEventListener('click', async ()=>{
                    const id = b.dataset.id;
                    if(!confirm('Aprobar usuario?')) return;
                    try{
                        const res = await doPost(`{{ url('/admin/super/users') }}/${id}/approve`, {}, { message: 'Aprobando...', finalMessage: 'Aprobado' });
                        showToast('Usuario', res.message || 'Aprobado', 'success');
                        loadUsers(); loadStats();
                    }catch(e){ showToast('Error','No se pudo aprobar','error'); }
                }));
            }
        }catch(e){ console.error(e); }
    }

    document.getElementById('btnLoadUsers')?.addEventListener('click', loadUsers);
    document.getElementById('btnRefreshStats')?.addEventListener('click', loadStats);

    // initial load
    loadStats();
})();
</script>
@endsection
