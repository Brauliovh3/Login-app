<?php
// Test para verificar estadísticas del dashboard

require 'vendor/autoload.php';

// Bootstrap Laravel mínimo
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Configurar la aplicación
$app->instance('request', Illuminate\Http\Request::create('/', 'GET'));

try {
    echo "=== ESTADÍSTICAS DEL DASHBOARD ===\n\n";
    
    // Verificar conexión a la base de datos
    $totalActas = DB::table('actas')->count();
    echo "✅ Total de actas en BD: $totalActas\n";
    
    if ($totalActas > 0) {
        // Obtener estadísticas reales
        if (Schema::hasColumn('actas', 'estado')) {
            $procesadas = DB::table('actas')->whereIn('estado', ['pagada', 'en_proceso'])->count();
            $pendientes = DB::table('actas')->where('estado', 'pendiente')->count();
            echo "✅ Actas procesadas: $procesadas\n";
            echo "✅ Actas pendientes: $pendientes\n";
        }
        
        if (Schema::hasColumn('actas', 'monto_multa')) {
            $totalMultas = DB::table('actas')->sum('monto_multa');
            echo "✅ Total multas: S/" . number_format($totalMultas) . "\n";
        }
        
        $eficiencia = $totalActas > 0 ? round(($procesadas / $totalActas) * 100, 2) : 0;
        echo "✅ Eficiencia: $eficiencia%\n";
        
        echo "\n🎉 El dashboard mostrará datos REALES de la base de datos\n";
    } else {
        echo "⚠️  No hay actas en la base de datos, se mostrarán valores por defecto\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
