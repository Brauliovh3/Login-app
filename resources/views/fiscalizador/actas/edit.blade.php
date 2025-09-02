@extends('layouts.dashboard')

@section('title', 'Editar Acta')

@section('content')
<div class="container">
    <h3 class="mb-4">Editar Acta</h3>
    <div id="acta-edit-container">Cargando acta...</div>
</div>
@endsection

@section('scripts')
<script>
    (function(){
        const id = '{{ $id }}';
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
@endsection
