<?php $__env->startSection('title', 'Ver Acta'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h3 class="mb-4">Ver Acta</h3>
    <div id="acta-container">Cargando acta...</div>
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
                if (!json.acta) return document.getElementById('acta-container').innerText = 'Acta no encontrada';
                const a = json.acta;
                const html = `
                    <div class="card p-3">
                        <h4>${a.numero_acta || 'N/A'}</h4>
                        <p><strong>Placa:</strong> ${a.placa ?? a.placa_vehiculo ?? 'N/A'}</p>
                        <p><strong>Conductor:</strong> ${a.conductor_nombre ?? a.nombre_conductor ?? 'N/A'}</p>
                        <p><strong>RUC/DNI:</strong> ${a.ruc_dni ?? 'N/A'}</p>
                        <p><strong>Estado:</strong> ${a.estado ?? 'N/A'}</p>
                        <pre style="white-space:pre-wrap;background:#f8f9fa;padding:12px;border-radius:6px">${a.descripcion ?? a.descripcion_hechos ?? ''}</pre>
                        <a href="/actas/${id}/imprimir" class="btn btn-sm btn-outline-primary mt-2">Imprimir</a>
                        <a href="/actas/${id}/editar" class="btn btn-sm btn-outline-success mt-2">Editar</a>
                    </div>
                `;
                document.getElementById('acta-container').innerHTML = html;
            })
            .catch(e=>{
                if (e && e.text) {
                    // Server returned HTML/text instead of JSON
                    document.getElementById('acta-container').innerHTML = '<div class="alert alert-warning">Respuesta inesperada del servidor:<pre style="white-space:pre-wrap">' + e.text + '</pre></div>';
                } else {
                    document.getElementById('acta-container').innerText = 'Error cargando acta: ' + (e.message || e);
                }
            });
    })();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Login-app\resources\views\fiscalizador\actas\show.blade.php ENDPATH**/ ?>