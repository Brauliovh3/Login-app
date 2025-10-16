<?php
// Script para limpiar tablas innecesarias y ajustar la tabla actas
session_start();
require_once '../config/database.php';

try {
    $dsn = "mysql:host={$config['host']};dbname={$config['name']}";
    $pdo = new PDO($dsn, $config['user'], $config['pass'], $config['options']);
    
    echo "<h2>Limpiando y Ajustando Base de Datos</h2>";
    echo "<pre>";
    
    // 1. Eliminar tablas de infracciones (no las necesitamos)
    echo "1. Eliminando tablas innecesarias...\n";
    $pdo->exec("DROP TABLE IF EXISTS detalle_infraccion");
    echo "   ✓ Tabla detalle_infraccion eliminada\n";
    
    $pdo->exec("DROP TABLE IF EXISTS infracciones");
    echo "   ✓ Tabla infracciones eliminada\n";
    
    // 2. Verificar y ajustar tabla actas
    echo "\n2. Ajustando tabla actas...\n";
    
    // Verificar si la columna codigo_infraccion existe
    $checkColumn = $pdo->query("SHOW COLUMNS FROM actas LIKE 'codigo_infraccion'")->fetch();
    
    if (!$checkColumn) {
        $pdo->exec("ALTER TABLE actas ADD COLUMN codigo_infraccion VARCHAR(50) NULL AFTER lugar_intervencion");
        echo "   ✓ Columna codigo_infraccion agregada\n";
    } else {
        echo "   - Columna codigo_infraccion ya existe\n";
    }
    
    // 3. Limpiar campos innecesarios de la tabla actas
    echo "\n3. Limpiando campos innecesarios...\n";
    
    $columnasEliminar = [
        'descripcion_hechos',
        'observaciones_adicionales',
        'tipo_infraccion'
    ];
    
    foreach ($columnasEliminar as $col) {
        try {
            $checkCol = $pdo->query("SHOW COLUMNS FROM actas LIKE '$col'")->fetch();
            if ($checkCol) {
                $pdo->exec("ALTER TABLE actas DROP COLUMN $col");
                echo "   ✓ Columna $col eliminada\n";
            }
        } catch (Exception $e) {
            // Columna no existe, continuar
        }
    }
    
    // 4. Reiniciar autoincrement
    echo "\n4. Reiniciando autoincrement...\n";
    
    $maxId = $pdo->query("SELECT IFNULL(MAX(id), 0) FROM actas")->fetchColumn();
    $newAuto = $maxId + 1;
    $pdo->exec("ALTER TABLE actas AUTO_INCREMENT = $newAuto");
    echo "   ✓ Autoincrement ajustado a $newAuto\n";
    
    // 5. Mostrar estructura final
    echo "\n5. Estructura final de la tabla actas:\n";
    $columns = $pdo->query("SHOW COLUMNS FROM actas")->fetchAll();
    echo "\n";
    foreach ($columns as $col) {
        echo "   - {$col['Field']} ({$col['Type']})" . ($col['Null'] == 'NO' ? ' NOT NULL' : '') . "\n";
    }
    
    // 6. Contar registros
    $count = $pdo->query("SELECT COUNT(*) FROM actas")->fetchColumn();
    echo "\n📊 Total de actas en la base de datos: $count\n";
    
    echo "\n✅ Proceso completado exitosamente!\n";
    echo "</pre>";
    echo "<p><a href='dashboard.php'>← Volver al Dashboard</a></p>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
