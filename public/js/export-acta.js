// Exportar acta con diseño completo (logos, formato, etc.)
function exportarActaConDiseno(actaId) {
    fetch(`dashboard.php?api=acta-details&id=${actaId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.acta) {
                generarPDFConDiseno(data.acta);
            } else {
                showAlert('Error al obtener datos del acta', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error al exportar acta', 'danger');
        });
}

async function generarPDFConDiseno(acta) {
    const html = await generarHTMLActa(acta);
    const ventana = window.open('', '_blank');
    ventana.document.write(html);
    ventana.document.close();
    
    setTimeout(() => {
        ventana.print();
    }, 500);
}

async function generarHTMLActa(acta) {
    const aniActual = new Date().getFullYear();
    const escudoBase64 = await imagenABase64('images/escudo_peru.png');
    const logoBase64 = await imagenABase64('images/logo.png');
    
    return `
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Acta ${acta.numero_acta}</title>
    <style>
        body { margin: 0; padding: 20px; font-family: Arial, sans-serif; }
        @media print { body { margin: 0; padding: 10px; } @page { size: A4; margin: 1cm; } }
    </style>
</head>
<body>
    <div style="padding: 15px; font-family: Arial, sans-serif; font-size: 9pt; max-width: 800px; margin: 0 auto;">
        <table style="width: 100%; margin-bottom: 10px; border-collapse: collapse;">
            <tr>
                <td style="width: 15%; text-align: left; vertical-align: top;">
                    <img src="${escudoBase64}" style="width: 60px; height: auto;" />
                </td>
                <td style="width: 70%; text-align: center; vertical-align: middle;">
                    <div style="font-size: 7pt; line-height: 1.2;">
                        <strong>PERÚ</strong><br>
                        <strong>GOBIERNO REGIONAL</strong><br>
                        <strong>DE APURÍMAC</strong><br>
                        <strong>DIRECCIÓN REGIONAL DE</strong><br>
                        <strong>TRANSPORTES Y COMUNICACIONES</strong><br>
                        <strong>DIRECCIÓN DE CIRCULACIÓN</strong><br>
                        <strong>TERRESTRE Y SEGURIDAD VIAL</strong>
                    </div>
                </td>
                <td style="width: 15%; text-align: right; vertical-align: top;">
                    <img src="${logoBase64}" style="width: 60px; height: auto;" />
                </td>
            </tr>
        </table>
        <div style="text-align: center; margin: 10px 0;">
            <h3 style="margin: 5px 0; font-size: 11pt;">ACTA DE CONTROL N° ${acta.numero_acta || '000000'} -${aniActual}</h3>
            <p style="margin: 3px 0; font-size: 9pt;"><strong>D.S. N° 017-2009-MTC</strong></p>
            <p style="margin: 3px 0; font-size: 8pt;">Código de infracciones y/o incumplimiento<br>Tipo infractor</p>
        </div>
        <p style="font-size: 7pt; text-align: justify; margin: 10px 0;">
            Quienes suscriben la presente acta nos identificamos como Inspectores acreditados de la DRTC AP, informamos el objeto y el 
            contenido de la acción de fiscalización, cumpliendo de acuerdo a lo señalado en la normativa vigente:
        </p>
        <table style="width: 100%; border-collapse: collapse; font-size: 8pt; margin-bottom: 10px;">
            <tr>
                <td style="border: 1px solid #000; padding: 3px; width: 25%;"><strong>Agente Infractor:</strong></td>
                <td style="border: 1px solid #000; padding: 3px; width: 25%;">☐ Transportista</td>
                <td style="border: 1px solid #000; padding: 3px; width: 25%;">☐ Operador de Ruta</td>
                <td style="border: 1px solid #000; padding: 3px; width: 25%;">☑ Conductor</td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 3px;"><strong>Placa:</strong></td>
                <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.placa || acta.placa_vehiculo || 'N/A'}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 3px;"><strong>Razón Social/Nombre:</strong></td>
                <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.razon_social || 'N/A'}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 3px;"><strong>RUC /DNI:</strong></td>
                <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.ruc_dni || 'N/A'}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 3px;"><strong>Fecha y Hora Inicio:</strong></td>
                <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.fecha_intervencion || ''} ${acta.hora_intervencion || ''}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 3px;"><strong>Fecha y Hora de fin:</strong></td>
                <td colspan="3" style="border: 1px solid #000; padding: 3px;"></td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 3px;"><strong>Nombre de Conductor:</strong></td>
                <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.nombre_conductor || 'N/A'}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 3px;"><strong>N° Licencia DNI del conductor:</strong></td>
                <td style="border: 1px solid #000; padding: 3px;">N°: ${acta.licencia_conductor || acta.licencia || 'N/A'}</td>
                <td colspan="2" style="border: 1px solid #000; padding: 3px;">Clase y Categoría:</td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 3px;"><strong>Dirección:</strong></td>
                <td colspan="3" style="border: 1px solid #000; padding: 3px;"></td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 3px;"><strong>N° Km. De la red Vial Nacional Prov. /Dpto.</strong></td>
                <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.lugar_intervencion || 'N/A'}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 3px;"><strong>Origen del viaje (Depto./Prov./Distrito)</strong></td>
                <td colspan="3" style="border: 1px solid #000; padding: 3px;"></td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 3px;"><strong>Destino Viaje: (Depto./Prov./Distrito)</strong></td>
                <td colspan="3" style="border: 1px solid #000; padding: 3px;"></td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 3px;"><strong>Tipo de Servicio que presta:</strong></td>
                <td style="border: 1px solid #000; padding: 3px;">☐ Personas</td>
                <td style="border: 1px solid #000; padding: 3px;">☐ mercancía</td>
                <td style="border: 1px solid #000; padding: 3px;">☐ mixto</td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 3px;"><strong>Inspector:</strong></td>
                <td colspan="3" style="border: 1px solid #000; padding: 3px;">${acta.inspector_responsable || 'N/A'}</td>
            </tr>
        </table>
        <div style="border: 1px solid #000; padding: 5px; margin-bottom: 10px;">
            <p style="margin: 0; font-size: 8pt;"><strong>Descripción de los hechos:</strong></p>
            <p style="margin: 5px 0; font-size: 8pt; min-height: 60px;">${acta.descripcion_infraccion || acta.descripcion_hechos || ''}</p>
        </div>
        <table style="width: 100%; border-collapse: collapse; font-size: 8pt; margin-bottom: 10px;">
            <tr>
                <td style="border: 1px solid #000; padding: 3px; width: 50%;"><strong>Medios probatorios:</strong></td>
                <td style="border: 1px solid #000; padding: 3px; width: 50%;"></td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 3px;"><strong>Calificación de la Infracción:</strong></td>
                <td style="border: 1px solid #000; padding: 3px;">${acta.codigo_infraccion || 'N/A'}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 3px;"><strong>Medida(s) Administrativa(s):</strong></td>
                <td style="border: 1px solid #000; padding: 3px;"></td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 3px;"><strong>Sanción:</strong></td>
                <td style="border: 1px solid #000; padding: 3px;"></td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 3px;"><strong>Observaciones del intervenido:</strong></td>
                <td style="border: 1px solid #000; padding: 3px; min-height: 40px;"></td>
            </tr>
            <tr>
                <td colspan="2" style="border: 1px solid #000; padding: 3px; min-height: 40px;"><strong>Observaciones del inspector:</strong></td>
            </tr>
        </table>
        <p style="font-size: 6pt; text-align: justify; margin: 10px 0;">
            La medida administrativa impuesta deberá ser cumplida estrictamente, bajo apercibimiento expreso de ser denunciado 
            penalmente por desobediencia o resistencia a la autoridad, ante su incumplimiento.
        </p>
        <table style="width: 100%; margin-top: 20px; font-size: 8pt;">
            <tr>
                <td style="width: 33%; text-align: center; vertical-align: bottom;">
                    <div style="border-top: 1px solid #000; padding-top: 3px; margin: 0 10px;">
                        <p style="margin: 2px 0;"><strong>Firma del Intervenido</strong></p>
                        <p style="margin: 2px 0;">Nom Ap.:</p>
                        <p style="margin: 2px 0;">DNI:</p>
                    </div>
                </td>
                <td style="width: 33%; text-align: center; vertical-align: bottom;">
                    <div style="border-top: 1px solid #000; padding-top: 3px; margin: 0 10px;">
                        <p style="margin: 2px 0;"><strong>Firma del Representante PNP</strong></p>
                        <p style="margin: 2px 0;">Nom Ap.:</p>
                        <p style="margin: 2px 0;">CIP:</p>
                    </div>
                </td>
                <td style="width: 33%; text-align: center; vertical-align: bottom;">
                    <div style="border-top: 1px solid #000; padding-top: 3px; margin: 0 10px;">
                        <p style="margin: 2px 0;"><strong>Firma del Inspector</strong></p>
                        <p style="margin: 2px 0;">Nombre Ap.:</p>
                        <p style="margin: 2px 0;">DNI:</p>
                    </div>
                </td>
            </tr>
        </table>
        <p style="font-size: 6pt; text-align: justify; margin-top: 15px;">
            De conceder la presentación de algún descargo puede realizarlo en la sede de la DRTC. As. (h) Para lo cual dispone de cinco (5) días 
            hábiles, a partir de la imposición del presente informe de control o del certificado de presente documento de acuerdo a lo dispuesto en el Reglamento del Procedimiento 
            Administrativo Sancionador Especial de la Dirección General Caminos y Servicios de Transporte y tránsito terrestre, y sus servicios complementarios, 
            aprobado mediante Decreto Supremo N° 009-2004 MTC, tal como si de acuerdo a la Ley N° 27867 Ley Orgánica de Gobiernos Regionales y su Reglamento de Organización y Funciones, aprobado mediante
            Ordenanza Regional N°...
        </p>
    </div>
</body>
</html>
    `;
}

// Exportar a PDF con diseño completo
function exportarActaPDF(actaId) {
    window.open(`export-acta-pdf-v2.php?id=${actaId}`, '_blank');
}

// Exportar a Word con diseño completo
function exportarActaWord(actaId) {
    window.location.href = `export-acta-word.php?id=${actaId}`;
}

// Exportar a Excel con formato
function exportarActaExcel(actaId) {
    window.location.href = `dashboard.php?api=exportar-acta&id=${actaId}&formato=excel`;
}

function imagenABase64(url) {
    return new Promise((resolve) => {
        const img = new Image();
        img.crossOrigin = 'Anonymous';
        img.onload = function() {
            const canvas = document.createElement('canvas');
            canvas.width = img.width;
            canvas.height = img.height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0);
            resolve(canvas.toDataURL('image/png'));
        };
        img.onerror = () => resolve(url);
        img.src = url;
    });
}
