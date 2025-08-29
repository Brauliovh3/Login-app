<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

// Configurar la conexión a la base de datos
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => '127.0.0.1',
    'port'      => '3306',
    'database'  => 'login_app',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "=== PRUEBA DE CONEXIÓN A BASE DE DATOS ===\n";

try {
    // Probar conexión
    $pdo = $capsule->getConnection()->getPdo();
    echo "✅ Conexión exitosa a la base de datos\n";
    
    // Verificar tabla actas
    $result = $capsule->getConnection()->select("SHOW TABLES LIKE 'actas'");
    if (!empty($result)) {
        echo "✅ Tabla 'actas' existe\n";
        
        // Contar registros
        $count = $capsule->getConnection()->select("SELECT COUNT(*) as total FROM actas")[0];
        echo "📊 Total de actas: {$count->total}\n";
        
        // Verificar columnas críticas
        $columns = $capsule->getConnection()->select("DESCRIBE actas");
        $columnNames = array_column($columns, 'Field');
        
        $requiredColumns = ['id', 'dni', 'estado', 'monto_multa', 'created_at'];
        foreach ($requiredColumns as $col) {
            if (in_array($col, $columnNames)) {
                echo "✅ Columna '$col' existe\n";
            } else {
                echo "❌ Columna '$col' NO existe\n";
            }
        }
        
        // Mostrar algunas estadísticas básicas
        echo "\n=== ESTADÍSTICAS BÁSICAS ===\n";
        
        // Estados
        $estados = $capsule->getConnection()->select("SELECT estado, COUNT(*) as total FROM actas GROUP BY estado");
        foreach ($estados as $estado) {
            echo "📊 Estado '{$estado->estado}': {$estado->total} actas\n";
        }
        
        // Total multas si existe la columna
        if (in_array('monto_multa', $columnNames)) {
            $totalMultas = $capsule->getConnection()->select("SELECT SUM(monto_multa) as total FROM actas WHERE monto_multa IS NOT NULL")[0];
            echo "💰 Total multas: S/ " . number_format($totalMultas->total ?? 0, 2) . "\n";
        }
        
        // Actas esta semana
        $actasSemana = $capsule->getConnection()->select("SELECT COUNT(*) as total FROM actas WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")[0];
        echo "📅 Actas esta semana: {$actasSemana->total}\n";
        
    } else {
        echo "❌ Tabla 'actas' NO existe\n";
    }
    
    // Verificar tabla users
    $result = $capsule->getConnection()->select("SHOW TABLES LIKE 'users'");
    if (!empty($result)) {
        echo "\n✅ Tabla 'users' existe\n";
        
        $userCount = $capsule->getConnection()->select("SELECT COUNT(*) as total FROM users")[0];
        echo "👤 Total usuarios: {$userCount->total}\n";
    } else {
        echo "\n❌ Tabla 'users' NO existe\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== PRUEBA COMPLETADA ===\n";
