<?php
echo "=== VERIFICANDO ESTRUCTURA DE LA TABLA ACTAS ===\n\n";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=login_app;charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "🔍 Consultando estructura de la tabla 'actas'...\n\n";
    
    $stmt = $pdo->query("DESCRIBE actas");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "📋 COLUMNAS DISPONIBLES EN LA TABLA 'actas':\n";
    echo str_repeat("-", 60) . "\n";
    
    foreach ($columns as $column) {
        echo sprintf("   %-25s | %-15s | %s\n", 
            $column['Field'], 
            $column['Type'], 
            $column['Null'] == 'NO' ? 'OBLIGATORIO' : 'Opcional'
        );
    }
    
    echo str_repeat("-", 60) . "\n\n";
    
    // Verificar qué columnas necesitamos usar
    $necesarias = [
        'numero_acta', 'placa', 'nombre_conductor', 'licencia',
        'razon_social', 'ruc_dni', 'lugar_intervencion', 'origen', 
        'destino', 'tipo_servicio', 'descripcion_hechos', 'estado'
    ];
    
    $columnasReales = array_column($columns, 'Field');
    
    echo "🔧 MAPEO DE CAMPOS NECESARIOS:\n";
    foreach ($necesarias as $campo) {
        $existe = in_array($campo, $columnasReales);
        echo "   " . ($existe ? "✅" : "❌") . " $campo" . ($existe ? "" : " - NO EXISTE") . "\n";
    }
    
    echo "\n📝 CAMPOS RELACIONADOS CON CONDUCTOR:\n";
    foreach ($columnasReales as $col) {
        if (stripos($col, 'conductor') !== false || stripos($col, 'nombre') !== false) {
            echo "   ✅ $col\n";
        }
    }
    
    echo "\n📝 CAMPOS RELACIONADOS CON PLACA/VEHICULO:\n";
    foreach ($columnasReales as $col) {
        if (stripos($col, 'placa') !== false || stripos($col, 'vehiculo') !== false) {
            echo "   ✅ $col\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
