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

echo "=== PRUEBA DE DASHBOARDS CON DATOS REALES ===\n";

try {
    // Obtener estadísticas de actas
    $totalActas = $capsule->getConnection()->select("SELECT COUNT(*) as total FROM actas")[0]->total;
    echo "📊 Total de actas: $totalActas\n";
    
    // Estadísticas por estado (0=pendiente, 1=procesada)
    $procesadas = $capsule->getConnection()->select("SELECT COUNT(*) as total FROM actas WHERE estado = 1")[0]->total;
    $pendientes = $capsule->getConnection()->select("SELECT COUNT(*) as total FROM actas WHERE estado = 0")[0]->total;
    
    echo "✅ Actas procesadas (estado=1): $procesadas\n";
    echo "⏳ Actas pendientes (estado=0): $pendientes\n";
    
    // Total multas
    $totalMultas = $capsule->getConnection()->select("SELECT SUM(monto_multa) as total FROM actas WHERE monto_multa IS NOT NULL")[0]->total ?? 0;
    echo "💰 Total multas: S/ " . number_format($totalMultas, 2) . "\n";
    
    // Usuarios
    $totalUsuarios = $capsule->getConnection()->select("SELECT COUNT(*) as total FROM usuarios")[0]->total;
    echo "👤 Total usuarios: $totalUsuarios\n";
    
    // Usuarios por estado
    $usuariosAprobados = $capsule->getConnection()->select("SELECT COUNT(*) as total FROM usuarios WHERE status = 'approved'")[0]->total;
    $usuariosPendientes = $capsule->getConnection()->select("SELECT COUNT(*) as total FROM usuarios WHERE status = 'pending'")[0]->total;
    
    echo "✅ Usuarios aprobados: $usuariosAprobados\n";
    echo "⏳ Usuarios pendientes: $usuariosPendientes\n";
    
    // Usuarios por rol
    echo "\n=== USUARIOS POR ROL ===\n";
    $rolesSql = $capsule->getConnection()->select("SELECT role, COUNT(*) as total FROM usuarios GROUP BY role");
    foreach ($rolesSql as $rol) {
        echo "👥 {$rol->role}: {$rol->total} usuarios\n";
    }
    
    // Actas del día de hoy
    echo "\n=== ESTADÍSTICAS DEL DÍA ===\n";
    $actasHoy = $capsule->getConnection()->select("SELECT COUNT(*) as total FROM actas WHERE DATE(created_at) = CURDATE()")[0]->total;
    $procesadasHoy = $capsule->getConnection()->select("SELECT COUNT(*) as total FROM actas WHERE DATE(created_at) = CURDATE() AND estado = 1")[0]->total;
    $pendientesHoy = $capsule->getConnection()->select("SELECT COUNT(*) as total FROM actas WHERE DATE(created_at) = CURDATE() AND estado = 0")[0]->total;
    
    echo "📅 Actas creadas hoy: $actasHoy\n";
    echo "✅ Procesadas hoy: $procesadasHoy\n";
    echo "⏳ Pendientes hoy: $pendientesHoy\n";
    
    // Calcular porcentajes
    if ($totalActas > 0) {
        $porcentajeProcesadas = round(($procesadas / $totalActas) * 100, 2);
        $porcentajePendientes = round(($pendientes / $totalActas) * 100, 2);
        
        echo "\n=== PORCENTAJES ===\n";
        echo "📊 % Procesadas: {$porcentajeProcesadas}%\n";
        echo "📊 % Pendientes: {$porcentajePendientes}%\n";
    }
    
    // Algunas placas de ejemplo
    echo "\n=== PLACAS DE VEHÍCULOS (PRIMERAS 5) ===\n";
    $placasSql = $capsule->getConnection()->select("SELECT DISTINCT placa FROM actas WHERE placa IS NOT NULL AND placa != '' LIMIT 5");
    foreach ($placasSql as $p) {
        echo "🚗 Placa: {$p->placa}\n";
    }
    
    echo "\n✅ PRUEBA COMPLETADA - LOS DASHBOARDS TIENEN DATOS REALES PARA MOSTRAR\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DE LA PRUEBA ===\n";
