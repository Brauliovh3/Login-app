<script>
// Shared helpers to export HTML tables shown in the UI to CSV or PDF.
// Designed to be defensive: accepts a selector and falls back to the first .table.

function _findTableElement(selector) {
    if (selector) {
        const el = document.querySelector(selector);
        if (el && el.tagName.toLowerCase() === 'table') return el;
    }
    // fallback: first visible table on the page
    const tables = Array.from(document.querySelectorAll('table'));
    return tables.find(t => t.offsetParent !== null) || null;
}

function parseTableRowToArray(tr) {
    const cells = Array.from(tr.querySelectorAll('th, td'));
    return cells.map(c => (c.innerText || '').trim());
}

function tableToCSV(table) {
    const rows = [];
    const thead = table.querySelector('thead');
    if (thead) {
        const th = thead.querySelectorAll('th');
        if (th.length) rows.push(Array.from(th).map(h => (h.innerText || '').trim()));
    }
    const tbody = table.querySelectorAll('tbody tr');
    if (tbody.length) {
        tbody.forEach(tr => {
            // skip placeholder rows
            if (tr.querySelector('td') && tr.querySelector('td').getAttribute('colspan')) {
                return;
            }
            rows.push(parseTableRowToArray(tr));
        });
    } else {
        // fallback: plain rows
        Array.from(table.querySelectorAll('tr')).forEach(tr => rows.push(parseTableRowToArray(tr)));
    }
    // Turn into CSV
    return rows.map(r => r.map(cell => '"' + String(cell).replace(/"/g, '""') + '"').join(',')).join('\r\n');
}

function downloadCSVFromTable(selector, filename = 'export.csv') {
    const table = _findTableElement(selector);
    if (!table) {
        showError && showError('No se encontró una tabla para exportar.');
        return;
    }
    const csv = tableToCSV(table);
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', filename);
    document.body.appendChild(link);
    link.click();
    setTimeout(() => {
        URL.revokeObjectURL(url);
        link.remove();
    }, 100);
}

function exportTableToCSV(selector, filename) {
    try {
        downloadCSVFromTable(selector, filename);
        showSuccess && showSuccess('Descarga CSV iniciada');
    } catch (e) {
        console.error('Export CSV failed', e);
        showError && showError('Error al generar CSV');
    }
}

function exportTableToPDF(selector, filename = 'export.pdf') {
    const table = _findTableElement(selector);
    if (!table) {
        showError && showError('No se encontró una tabla para exportar a PDF.');
        return;
    }

    // Prefer html2pdf if present (used elsewhere in the app). If not available, open print window.
    try {
        const wrapper = document.createElement('div');
        // clone the table to avoid modifying the page
        const clone = table.cloneNode(true);
        // remove interactive elements
        clone.querySelectorAll('button,a,input,select').forEach(n => n.remove());
        wrapper.appendChild(clone);

        if (window.html2pdf) {
            const opt = {
                margin:       10,
                filename:     filename,
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(wrapper).save();
            showSuccess && showSuccess('Generando PDF...');
            return;
        }

        // Fallback: open printable window with the table
        const html = `<!doctype html><html><head><meta charset="utf-8"><title>${filename}</title>` +
                     `<style>body{margin:8px;font-family:Arial,sans-serif}table{width:100%;border-collapse:collapse;margin:0}th,td{border:1px solid #333;padding:4px 6px;text-align:left;font-size:12px}th{background:#f5f5f5;font-weight:bold}</style>` +
                     `</head><body>` + wrapper.innerHTML + `</body></html>`;
        const w = window.open('', '_blank');
        w.document.write(html);
        w.document.close();
        setTimeout(() => w.print(), 600);
        showSuccess && showSuccess('Abriendo vista de impresión...');
    } catch (e) {
        console.error('Export PDF failed', e);
        showError && showError('Error al generar PDF');
    }
}
</script>
<?php /**PATH C:\xampp\htdocs\Login-app\resources\views\partials\export-actas-scripts.blade.php ENDPATH**/ ?>