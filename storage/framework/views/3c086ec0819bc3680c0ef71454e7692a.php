<?php $__env->startSection('title', 'Imprimir Acta'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h3 class="mb-4">Imprimir Acta</h3>
    <div id="acta-print-container">Generando vista imprimible...</div>
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
                if (!json.acta) return document.getElementById('acta-print-container').innerText = 'Acta no encontrada';
                const a = json.acta;
                const content = `\
                    <div style="padding:20px; font-family:Arial;">\
                        <h2>${a.numero_acta || ''}</h2>\
                        <p><strong>Placa:</strong> ${a.placa ?? a.placa_vehiculo ?? ''}</p>\
                        <p><strong>Conductor:</strong> ${a.conductor_nombre ?? a.nombre_conductor ?? ''}</p>\
                        <p><strong>Descripción:</strong></p>\
                        <pre style="white-space:pre-wrap; background:#f8f9fa; padding:12px">${a.descripcion ?? a.descripcion_hechos ?? ''}</pre>\
                    </div>`;
                const w = window.open('', '_blank');
                w.document.write(content);
                w.document.close();
                setTimeout(()=>w.print(), 400);
                document.getElementById('acta-print-container').innerText = 'Impresión iniciada';
            })
            .catch(e=>{
                if (e && e.text) {
                    document.getElementById('acta-print-container').innerHTML = '<div class="alert alert-warning">Respuesta inesperada del servidor:<pre style="white-space:pre-wrap">' + e.text + '</pre></div>';
                } else {
                    document.getElementById('acta-print-container').innerText = 'Error generando impresión: ' + (e.message || e);
                }
            });
    })();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Login-app\resources\views\backup-roles-sep-2025\fiscalizador\actas\imprimir.blade.php ENDPATH**/ ?>