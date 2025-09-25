<?php $__env->startSection('title', 'Editar Acta'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h3 class="mb-4">Editar Acta</h3>
    <div id="acta-edit-container">Cargando acta...</div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    (function(){
        const id = '<?php echo e($id); ?>';
        fetch(`/api/actas/${id}`, { 
            credentials: 'same-origin', 
            headers:{ 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' }
        })
            .then(async r=>{
                const text = await r.text();
                try { return JSON.parse(text); } catch(err) { throw { status: r.status, text }; }
            })
            .then(json=>{
                if (!json.acta) return document.getElementById('acta-edit-container').innerText = 'Acta no encontrada';
                const a = json.acta;
                const html = `
                    <form id="form-edit-acta">
                        <div class="mb-3">
                            <label class="form-label">Número</label>
                            <input class="form-control" name="numero_acta" value="${a.numero_acta || ''}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Estado</label>
                            <select name="estado" class="form-select">
                                <option value="pendiente" ${a.estado==='pendiente'?'selected':''}>Pendiente</option>
                                <option value="pagada" ${a.estado==='pagada'?'selected':''}>Pagada</option>
                                <option value="anulada" ${a.estado==='anulada'?'selected':''}>Anulada</option>
                            </select>
                        </div>
                        <button class="btn btn-primary" type="submit">Guardar</button>
                    </form>
                `;
                document.getElementById('acta-edit-container').innerHTML = html;

                document.getElementById('form-edit-acta').addEventListener('submit', function(e){
                    e.preventDefault();
                    const form = e.target;
                    const data = { estado: form.estado.value };
                    fetch(`/api/actas/${id}/status`, { method: 'PUT', headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }, body: JSON.stringify(data)})
                    .then(r=>r.json()).then(j=>{
                        if (j.success) alert('Estado actualizado');
                        else alert('Error: ' + (j.message||'')); 
                    }).catch(err=>alert('Error: '+err.message));
                });
            })
            .catch(e=>{
                if (e && e.text) {
                    document.getElementById('acta-edit-container').innerHTML = '<div class="alert alert-warning">Respuesta inesperada del servidor:<pre style="white-space:pre-wrap">' + e.text + '</pre></div>';
                } else {
                    document.getElementById('acta-edit-container').innerText = 'Error cargando acta: ' + (e.message || e);
                }
            });
    })();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Login-app\resources\views\fiscalizador\actas\edit.blade.php ENDPATH**/ ?>