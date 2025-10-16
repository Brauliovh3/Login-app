<?php
// Script para crear tablas de infracciones si no existen

require_once __DIR__ . '/config/database.php';

try {
    // Verificar si la tabla infracciones existe
    $checkTable = $pdo->query("SHOW TABLES LIKE 'infracciones'")->fetch();
    
    if (!$checkTable) {
        echo "Creando tabla 'infracciones'...\n";
        
        $sql = "CREATE TABLE `infracciones` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `codigo_infraccion` varchar(255) NOT NULL,
            `aplica_sobre` varchar(255) NOT NULL,
            `reglamento` varchar(255) NOT NULL,
            `norma_modificatoria` varchar(255) NOT NULL,
            `infraccion` text NOT NULL,
            `clase_pago` enum('Pecuniaria','No pecuniaria') NOT NULL,
            `sancion` varchar(255) NOT NULL,
            `tipo` enum('Infracción') NOT NULL DEFAULT 'Infracción',
            `medida_preventiva` text,
            `gravedad` enum('Leve','Grave','Muy grave') NOT NULL,
            `otros_responsables_otros_beneficios` text,
            `estado` varchar(255) NOT NULL DEFAULT 'activo',
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `infracciones_codigo_infraccion_unique` (`codigo_infraccion`),
            KEY `infracciones_codigo_infraccion_index` (`codigo_infraccion`),
            KEY `infracciones_aplica_sobre_index` (`aplica_sobre`),
            KEY `infracciones_gravedad_index` (`gravedad`),
            KEY `infracciones_clase_pago_index` (`clase_pago`),
            KEY `infracciones_estado_index` (`estado`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $pdo->exec($sql);
        echo "✓ Tabla 'infracciones' creada exitosamente\n";
    } else {
        echo "✓ Tabla 'infracciones' ya existe\n";
    }
    
    // Verificar si la tabla detalle_infraccion existe
    $checkTable2 = $pdo->query("SHOW TABLES LIKE 'detalle_infraccion'")->fetch();
    
    if (!$checkTable2) {
        echo "Creando tabla 'detalle_infraccion'...\n";
        
        $sql = "CREATE TABLE `detalle_infraccion` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `infraccion_id` bigint(20) UNSIGNED NOT NULL,
            `subcategoria` varchar(10) NOT NULL,
            `descripcion_detallada` text NOT NULL,
            `condiciones_especiales` text,
            `observaciones` text,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `detalle_infraccion_infraccion_id_index` (`infraccion_id`),
            KEY `detalle_infraccion_subcategoria_index` (`subcategoria`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $pdo->exec($sql);
        echo "✓ Tabla 'detalle_infraccion' creada exitosamente\n";
    } else {
        echo "✓ Tabla 'detalle_infraccion' ya existe\n";
    }
    
    // Contar registros
    $count = $pdo->query("SELECT COUNT(*) FROM infracciones")->fetchColumn();
    echo "\n📊 Total de infracciones en BD: " . $count . "\n";
    
    if ($count == 0) {
        echo "\n⚠️ Las tablas están vacías. Ejecuta los seeders:\n";
        echo "   php artisan db:seed --class=InfraccionesSeeder\n";
        echo "   php artisan db:seed --class=DetalleInfraccionSeeder\n";
    } else {
        echo "\n✅ Todo listo! Las infracciones están cargadas.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
