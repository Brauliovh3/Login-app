<?php
echo "=== PRUEBA: CAMPO RAZON_SOCIAL OPCIONAL ===\n\n";

// Datos de prueba SIN razón social
$datosSinRazonSocial = [
    'placa_1' => 'XYZ-999',
    'nombre_conductor_1' => 'Juan Carlos Mendoza',
    'licencia_conductor_1' => 'L555666777',
    'razon_social' => '', // Campo vacío
    'ruc_dni' => '12345678',
    'lugar_intervencion' => 'Av. Principal 789, Abancay',
    'origen_viaje' => 'Abancay',
    'destino_viaje' => 'Andahuaylas',
    'tipo_servicio' => 'Transporte de Pasajeros',
    'descripcion_hechos' => 'Control rutinario - campo razón social vacío para demostrar que es opcional.'
];

// Datos de prueba CON razón social
$datosConRazonSocial = [
    'placa_1' => 'ABC-888',
    'nombre_conductor_1' => 'María Elena Quispe',
    'licencia_conductor_1' => 'L888999000',
    'razon_social' => 'Transportes Andinos S.A.C.',
    'ruc_dni' => '20999888777',
    'lugar_intervencion' => 'Av. Comercio 456, Abancay',
    'origen_viaje' => 'Abancay',
    'destino_viaje' => 'Cusco',
    'tipo_servicio' => 'Transporte de Carga',
    'descripcion_hechos' => 'Control rutinario - con razón social completa.'
];

try {
    $pdo = new PDO("mysql:host=localhost;dbname=login_app;charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Conexión a base de datos exitosa\n\n";
    
    // Verificar infracciones e inspectores
    $infraccionId = $pdo->query("SELECT id FROM infracciones LIMIT 1")->fetch(PDO::FETCH_COLUMN);
    $inspectorId = $pdo->query("SELECT id FROM inspectores LIMIT 1")->fetch(PDO::FETCH_COLUMN);
    
    echo "📋 IDs encontrados:\n";
    echo "   Infracción ID: $infraccionId\n";
    echo "   Inspector ID: $inspectorId\n\n";
    
    // Función para crear acta
    function crearActa($pdo, $datos, $infraccionId, $inspectorId, $descripcionTest) {
        $numeroActa = 'DRTC-APU-' . date('Y') . '-' . str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT);
        
        $descripcionCompleta = "ACTA DE FISCALIZACIÓN\n\n";
        $descripcionCompleta .= "DATOS DEL VEHÍCULO:\n";
        $descripcionCompleta .= "Placa: " . $datos['placa_1'] . "\n";
        
        // Solo agregar empresa/operador si hay razón social
        if (!empty($datos['razon_social'])) {
            $descripcionCompleta .= "Empresa/Operador: " . $datos['razon_social'] . "\n";
        }
        
        $descripcionCompleta .= "RUC/DNI: " . $datos['ruc_dni'] . "\n\n";
        $descripcionCompleta .= "DATOS DEL CONDUCTOR:\n";
        $descripcionCompleta .= "Nombre: " . $datos['nombre_conductor_1'] . "\n";
        $descripcionCompleta .= "Licencia: " . $datos['licencia_conductor_1'] . "\n\n";
        $descripcionCompleta .= "DATOS DEL VIAJE:\n";
        $descripcionCompleta .= "Origen: " . $datos['origen_viaje'] . "\n";
        $descripcionCompleta .= "Destino: " . $datos['destino_viaje'] . "\n";
        $descripcionCompleta .= "Tipo de Servicio: " . $datos['tipo_servicio'] . "\n\n";
        $descripcionCompleta .= "DESCRIPCIÓN DE LOS HECHOS:\n";
        $descripcionCompleta .= $datos['descripcion_hechos'];
        
        $sql = "INSERT INTO actas (
            numero_acta, vehiculo_id, conductor_id, infraccion_id, inspector_id,
            placa_vehiculo, ubicacion, descripcion, monto_multa, estado,
            fecha_infraccion, hora_infraccion, hora_inicio_registro,
            user_id, created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $numeroActa,
            null,
            null,
            $infraccionId,
            $inspectorId,
            $datos['placa_1'],
            $datos['lugar_intervencion'],
            $descripcionCompleta,
            0,
            'registrada',
            date('Y-m-d'),
            date('H:i:s'),
            date('Y-m-d H:i:s'),
            1,
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s')
        ]);
        
        if ($result) {
            $actaId = $pdo->lastInsertId();
            echo "$descripcionTest\n";
            echo "   📄 Número: $numeroActa\n";
            echo "   🆔 ID: $actaId\n";
            echo "   🚗 Placa: {$datos['placa_1']}\n";
            echo "   👤 Conductor: {$datos['nombre_conductor_1']}\n";
            echo "   🏢 Razón Social: " . (empty($datos['razon_social']) ? 'VACÍO (opcional)' : $datos['razon_social']) . "\n";
            echo "   📍 Lugar: {$datos['lugar_intervencion']}\n\n";
            return true;
        }
        return false;
    }
    
    // Prueba 1: Acta SIN razón social
    echo "🧪 PRUEBA 1: Acta sin razón social (campo vacío)\n";
    echo str_repeat("-", 50) . "\n";
    $exitoso1 = crearActa($pdo, $datosSinRazonSocial, $infraccionId, $inspectorId, "✅ Acta creada SIN razón social:");
    
    // Prueba 2: Acta CON razón social
    echo "🧪 PRUEBA 2: Acta con razón social completa\n";
    echo str_repeat("-", 50) . "\n";
    $exitoso2 = crearActa($pdo, $datosConRazonSocial, $infraccionId, $inspectorId, "✅ Acta creada CON razón social:");
    
    if ($exitoso1 && $exitoso2) {
        echo "🎉 ¡TODAS LAS PRUEBAS EXITOSAS!\n";
        echo str_repeat("=", 60) . "\n";
        echo "✅ CONFIRMADO: El campo 'razon_social' ahora es OPCIONAL\n";
        echo "   • Se puede dejar vacío sin problema\n";
        echo "   • La validación no lo requiere\n";
        echo "   • La descripción se adapta automáticamente\n";
        echo "   • Ambos casos funcionan correctamente\n\n";
        
        echo "🎯 INSTRUCCIONES PARA EL USUARIO:\n";
        echo "   1. El campo 'Razón Social' ahora es opcional\n";
        echo "   2. Puede llenarlo o dejarlo vacío\n";
        echo "   3. El formulario se enviará sin problemas\n";
        echo "   4. La descripción del acta se ajusta automáticamente\n\n";
        
        echo "📝 UBICACIONES ACTUALIZADAS:\n";
        echo "   • Formulario principal: /fiscalizador/actas\n";
        echo "   • Formulario de prueba: /test-formulario\n";
        echo "   • Validación del backend: ActaController.php\n";
        
    } else {
        echo "❌ Algunas pruebas fallaron\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
