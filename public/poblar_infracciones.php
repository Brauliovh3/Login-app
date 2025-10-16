<?php
// Script para poblar las tablas de infracciones con los datos de los seeders
session_start();
require_once '../config/database.php';

try {
    $dsn = "mysql:host={$config['host']};dbname={$config['name']}";
    $pdo = new PDO($dsn, $config['user'], $config['pass'], $config['options']);
    
    echo "<h2>Poblando Base de Datos de Infracciones</h2>";
    echo "<pre>";
    
    // Verificar si ya hay datos
    $count = $pdo->query("SELECT COUNT(*) FROM infracciones")->fetchColumn();
    
    if ($count > 0) {
        echo "⚠️ La tabla ya tiene $count registros.\n";
        echo "¿Deseas continuar de todas formas? Los datos duplicados serán ignorados.\n\n";
    }
    
    // Datos de infracciones (from InfraccionesSeeder)
    $infracciones = [
        ['codigo_infraccion' => 'F.1', 'aplica_sobre' => 'Sobre quien realiza la actividad de transporte sin autorización', 'reglamento' => 'Reglamento Nacional de Administración de Transportes - RENAT', 'norma_modificatoria' => 'D.S. N° 017-2009-MTC D.S. N° 063-2010-MTC / D.S. N° 005-2016-MTC', 'infraccion' => 'Prestar el servicio de transporte de personas, de mercancías o mixto, sin contar con autorización otorgada por la autoridad competente o en una modalidad o ámbito diferente al autorizado', 'clase_pago' => 'Pecuniaria', 'sancion' => '1 UIT', 'tipo' => 'Infracción', 'medida_preventiva' => 'Retención de la licencia de conducir / Internamiento preventivo del vehículo', 'gravedad' => 'Muy grave', 'otros_responsables_otros_beneficios' => 'Responsabilidad solidaria del propietario del vehículo / Aplica el descuento de 50% (Hasta 05 días hábiles de levantada el acta de control o de notificado el Inicio del Procedimiento Administrativo Sancionador) y 30% (15 días de Notificada la Resolución de Sanción de Multa pecuniaria)'],
        ['codigo_infraccion' => 'F.2', 'aplica_sobre' => 'Transportista', 'reglamento' => 'Reglamento Nacional de Administración de Transportes - RENAT', 'norma_modificatoria' => 'D.S. N° 017-2009-MTC', 'infraccion' => 'Permitir la utilización o utilizar, intencionalmente, los vehículos destinados a la prestación del servicio, en acciones de bloqueo, interrupción u otras que impidan el libre tránsito por las calles, carreteras, puentes, vías férreas y otras vías públicas terrestres.', 'clase_pago' => 'No pecuniaria', 'sancion' => 'Inhabilitación por 1 año para prestar el servicio de transporte.', 'tipo' => 'Infracción', 'medida_preventiva' => 'En forma sucesiva: Remoción del vehículo. Internamiento del vehículo.', 'gravedad' => 'Muy grave', 'otros_responsables_otros_beneficios' => 'Aplica el descuento de 50% (Hasta 05 días hábiles de levantada el acta de control o de notificado el Inicio del Procedimiento Administrativo Sancionador) y 30% (15 días de Notificada la Resolución de Sanción de Multa pecuniaria)'],
        ['codigo_infraccion' => 'F.4', 'aplica_sobre' => 'Transportista', 'reglamento' => 'Reglamento Nacional de Administración de Transportes - RENAT', 'norma_modificatoria' => 'D.S. N° 017-2009-MTC D.S N° 063-2010-MTC', 'infraccion' => 'a) Negarse a entregar la información o documentación correspondiente al vehículo, a su habilitación como conductor, al servicio que presta o actividad de transporte que realiza al ser requerido para ello. b) Brindar intencionalmente información no conforme, a la autoridad competente, durante la fiscalización con el propósito de hacerla incurrir en error respecto de la autorización para prestar el servicio, de la habilitación del vehículo o la del conductor c) Incurrir en actos de simulación, suplantación u otras conductas destinadas a hacer incurrir en error a la autoridad competente respecto de la autorización para prestar el servicio, o respecto de la habilitación del vehículo o la del conductor.', 'clase_pago' => 'No pecuniaria', 'sancion' => 'Transportista: Suspensión 90 días de la autorización para prestar servicio en la ruta o rutas en que ocurrió la infracción, o en el servicio tratándose del transporte de mercancías o del servicio de transporte especial de personas.', 'tipo' => 'Infracción', 'medida_preventiva' => null, 'gravedad' => 'Muy grave', 'otros_responsables_otros_beneficios' => 'Aplica el descuento de 50% (Hasta 05 días hábiles de levantada el acta de control o de notificado el Inicio del Procedimiento Administrativo Sancionador) y 30% (15 días de Notificada la Resolución de Sanción de Multa pecuniaria)'],
        ['codigo_infraccion' => 'F.5', 'aplica_sobre' => 'Generador de carga', 'reglamento' => 'Reglamento Nacional de Administración de Transportes - RENAT', 'norma_modificatoria' => 'D.S. N° 017-2009-MTC D.S N° 063-2010-MTC', 'infraccion' => 'a) Contratar el servicio de transporte con un transportista que no se encuentra autorizado, o cuya autorización es para realizar servicio de transporte privado de mercancías. b) Permitir o utilizar la vía pública como lugar habitual o constante para la carga y/o descarga de mercancías. c) Exigir que el transportista cuente con la autorización especial de la autoridad de la autoridad vial que corresponda cuando transporte bienes cuyas dimensiones o peso superen los máximos establecidos por el RNV.', 'clase_pago' => 'Pecuniaria', 'sancion' => '0.5 UIT', 'tipo' => 'Infracción', 'medida_preventiva' => null, 'gravedad' => 'Muy grave', 'otros_responsables_otros_beneficios' => 'Aplica el descuento de 50% (Hasta 05 días hábiles de levantada el acta de control o de notificado el Inicio del Procedimiento Administrativo Sancionador) y 30% (15 días de Notificada la Resolución de Sanción de Multa pecuniaria)'],
        ['codigo_infraccion' => 'F.6', 'aplica_sobre' => 'Conductor', 'reglamento' => 'Reglamento Nacional de Administración de Transportes - RENAT', 'norma_modificatoria' => 'D.S. N° 017-2009-MTC / D.S. N° 063-2010-MTC D.S 005-2016-MTC', 'infraccion' => 'a) negarse a entregar la información o documentación correspondiente al vehículo, a su habilitación como conductor, al servicio que presta o actividad de transporte que realiza, al ser requerido para ello. b) brindar intencionalmente información no conforme, a la autoridad competente, durante la fiscalización con el propósito de hacerla incurrir en error respecto de la autorización para prestar el servicio, de la habilitación del vehículo o la del conductor. c) Realizar maniobras evasivas con el vehículo para evitar la fiscalización. d) Incurrir en actos de simulación, suplantación u otras conductas destinadas a hacer incurrir en error a la autoridad competente respecto de la autorización para prestar el servicio, o respecto de la habilitación del vehículo o la del conductor.', 'clase_pago' => 'Pecuniaria', 'sancion' => 'Suspensión de la licencia de conducir por 90 días calendario + Multa 0.5 UIT', 'tipo' => 'Infracción', 'medida_preventiva' => 'Retención de la licencia de conducir', 'gravedad' => 'Muy grave', 'otros_responsables_otros_beneficios' => 'Aplica el descuento de 50% (Hasta 05 días hábiles de levantada el acta de control o de notificado el Inicio del Procedimiento Administrativo Sancionador) y 30% (15 días de Notificada la Resolución de Sanción de Multa pecuniaria)'],
        ['codigo_infraccion' => 'I.1', 'aplica_sobre' => 'Transportista', 'reglamento' => 'Reglamento Nacional de Administración de Transporte - RENAT', 'norma_modificatoria' => 'D.S N° 009-2004-MTC (D) / D.S N° 017-2009-MTC', 'infraccion' => 'No portar durante la prestación del servicio de transporte, según corresponda: a) el manifiesto de usuarios, en el transporte de personas, cuando este no sea electrónico. b) la hoja de ruta manual o electrónica, según corresponda c) en el servicio de transporte de mercancías la guía de remisión del transportista y, de ser el caso manifiesto de carga. d) el documento de habilitación del vehículo e) el certificado de Inspección Técnica Vehicular. f) el certificado del seguro obligatorio de accidente de tránsito o CAT cuando corresponda.', 'clase_pago' => 'Pecuniaria', 'sancion' => '0.1 UIT', 'tipo' => 'Infracción', 'medida_preventiva' => 'En forma sucesiva: interrupción de viaje / Retención del vehículo / internamiento del vehículo', 'gravedad' => 'Grave', 'otros_responsables_otros_beneficios' => 'Aplica el descuento de 50% (Hasta 05 días hábiles de levantada el acta de control o de notificado el Inicio del Procedimiento Administrativo Sancionador) y 30% (15 días de Notificada la Resolución de Sanción de Multa pecuniaria)'],
        ['codigo_infraccion' => 'I.2', 'aplica_sobre' => 'Conductor', 'reglamento' => 'Reglamento Nacional de Administración de Transporte - RENAT', 'norma_modificatoria' => 'D.S. N° 009-2004-MTC (D) / D.S. N° 017-2009-MTC', 'infraccion' => 'a) No exhibir en cada vehículo habilitado al servicio de transporte público de personas, la modalidad del servicio, según corresponda, la razón social y el nombre comercial si lo tuviera. b) En el servicio de transporte provincial de personas, no colocar en lugar visible para el usuario, la información sobre las tarifas vigentes y la ruta autorizada.', 'clase_pago' => 'Pecuniaria', 'sancion' => '0.1 UIT', 'tipo' => 'Infracción', 'medida_preventiva' => null, 'gravedad' => 'Grave', 'otros_responsables_otros_beneficios' => 'Aplica el descuento de 50% (Hasta 05 días hábiles de levantada el acta de control o de notificado el Inicio del Procedimiento Administrativo Sancionador) y 30% (15 días de Notificada la Resolución de Sanción de Multa pecuniaria)'],
    ];
    
    $insertados = 0;
    $duplicados = 0;
    
    foreach ($infracciones as $inf) {
        try {
            $stmt = $pdo->prepare("INSERT IGNORE INTO infracciones (codigo_infraccion, aplica_sobre, reglamento, norma_modificatoria, infraccion, clase_pago, sancion, tipo, medida_preventiva, gravedad, otros_responsables_otros_beneficios, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
            $result = $stmt->execute([
                $inf['codigo_infraccion'], $inf['aplica_sobre'], $inf['reglamento'], 
                $inf['norma_modificatoria'], $inf['infraccion'], $inf['clase_pago'], 
                $inf['sancion'], $inf['tipo'], $inf['medida_preventiva'], 
                $inf['gravedad'], $inf['otros_responsables_otros_beneficios']
            ]);
            
            if ($stmt->rowCount() > 0) {
                echo "✓ Insertado: {$inf['codigo_infraccion']}\n";
                $insertados++;
            } else {
                echo "- Ya existe: {$inf['codigo_infraccion']}\n";
                $duplicados++;
            }
        } catch (Exception $e) {
            echo "✗ Error en {$inf['codigo_infraccion']}: {$e->getMessage()}\n";
        }
    }
    
    echo "\n📊 Resumen:\n";
    echo "   Insertados: $insertados\n";
    echo "   Duplicados: $duplicados\n";
    
    // Poblar detalles solo si se insertaron infracciones
    if ($insertados > 0 || $count == 0) {
        echo "\n📋 Poblando detalles de infracciones...\n\n";
        
        // Datos de detalles (subcategorías principales)
        $detalles = [
            ['F.4', 'a)', 'Negarse a entregar la información o documentación correspondiente'],
            ['F.4', 'b)', 'Brindar intencionalmente información no conforme'],
            ['F.4', 'c)', 'Incurrir en actos de simulación o suplantación'],
            ['F.5', 'a)', 'Contratar transportista no autorizado'],
            ['F.5', 'b)', 'Permitir uso de vía pública para carga/descarga'],
            ['F.5', 'c)', 'No exigir autorización especial para carga excedida'],
            ['F.6', 'a)', 'Negarse a entregar información (conductor)'],
            ['F.6', 'b)', 'Brindar información falsa (conductor)'],
            ['F.6', 'c)', 'Realizar maniobras evasivas'],
            ['F.6', 'd)', 'Actos de simulación (conductor)'],
            ['I.1', 'a)', 'No portar manifiesto de usuarios'],
            ['I.1', 'b)', 'No portar hoja de ruta'],
            ['I.1', 'c)', 'No portar guía de remisión'],
            ['I.1', 'd)', 'No portar documento de habilitación'],
            ['I.1', 'e)', 'No portar certificado ITV'],
            ['I.1', 'f)', 'No portar seguro SOAT'],
            ['I.2', 'a)', 'No exhibir modalidad y razón social'],
            ['I.2', 'b)', 'No colocar tarifas y ruta visible'],
        ];
        
        $detallesInsertados = 0;
        foreach ($detalles as $det) {
            try {
                $stmtId = $pdo->prepare("SELECT id FROM infracciones WHERE codigo_infraccion = ?");
                $stmtId->execute([$det[0]]);
                $infId = $stmtId->fetchColumn();
                
                if ($infId) {
                    $stmt = $pdo->prepare("INSERT IGNORE INTO detalle_infraccion (infraccion_id, subcategoria, descripcion_detallada, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
                    $stmt->execute([$infId, $det[1], $det[2]]);
                    
                    if ($stmt->rowCount() > 0) {
                        echo "✓ Detalle: {$det[0]}-{$det[1]}\n";
                        $detallesInsertados++;
                    }
                }
            } catch (Exception $e) {
                echo "✗ Error en detalle {$det[0]}-{$det[1]}\n";
            }
        }
        
        echo "\n✅ Detalles insertados: $detallesInsertados\n";
    }
    
    echo "\n✅ Proceso completado!\n";
    echo "</pre>";
    echo "<p><a href='dashboard.php'>← Volver al Dashboard</a></p>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
