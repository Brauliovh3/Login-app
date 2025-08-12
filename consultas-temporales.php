<?php
header('Content-Type: application/json; charset=utf-8');

// Función para obtener las consultas almacenadas temporalmente
function obtenerConsultasTemporales($tipo = null, $numero = null) {
    $tempDir = __DIR__ . '/temp_consultas';
    
    if (!file_exists($tempDir)) {
        return [];
    }
    
    $consultas = [];
    $archivos = glob($tempDir . '/*.json');
    
    foreach ($archivos as $archivo) {
        $contenido = file_get_contents($archivo);
        $datos = json_decode($contenido, true);
        
        if ($datos) {
            // Filtrar por tipo si se especifica
            if ($tipo && !str_contains(basename($archivo), $tipo)) {
                continue;
            }
            
            // Filtrar por número si se especifica
            if ($numero && (!isset($datos['dni']) || $datos['dni'] != $numero) && 
                          (!isset($datos['ruc']) || $datos['ruc'] != $numero)) {
                continue;
            }
            
            $datos['archivo'] = basename($archivo);
            $consultas[] = $datos;
        }
    }
    
    // Ordenar por timestamp descendente (más recientes primero)
    usort($consultas, function($a, $b) {
        return strtotime($b['timestamp']) - strtotime($a['timestamp']);
    });
    
    return $consultas;
}

// Función para limpiar consultas antiguas (más de X días)
function limpiarConsultasAntiguas($dias = 7) {
    $tempDir = __DIR__ . '/temp_consultas';
    
    if (!file_exists($tempDir)) {
        return 0;
    }
    
    $archivos = glob($tempDir . '/*.json');
    $eliminados = 0;
    $tiempoLimite = time() - ($dias * 24 * 60 * 60);
    
    foreach ($archivos as $archivo) {
        if (filemtime($archivo) < $tiempoLimite) {
            unlink($archivo);
            $eliminados++;
        }
    }
    
    return $eliminados;
}

// Manejo de la petición
$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'list':
        $tipo = $_GET['tipo'] ?? null; // 'dni' o 'ruc'
        $numero = $_GET['numero'] ?? null;
        $consultas = obtenerConsultasTemporales($tipo, $numero);
        
        echo json_encode([
            'success' => true,
            'total' => count($consultas),
            'consultas' => $consultas
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        break;
        
    case 'clean':
        $dias = $_GET['dias'] ?? 7;
        $eliminados = limpiarConsultasAntiguas($dias);
        
        echo json_encode([
            'success' => true,
            'message' => "Se eliminaron $eliminados archivos antiguos",
            'eliminados' => $eliminados
        ]);
        break;
        
    case 'delete':
        $archivo = $_GET['archivo'] ?? '';
        $tempDir = __DIR__ . '/temp_consultas';
        $rutaArchivo = $tempDir . '/' . basename($archivo);
        
        if (file_exists($rutaArchivo)) {
            unlink($rutaArchivo);
            echo json_encode([
                'success' => true,
                'message' => 'Archivo eliminado correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Archivo no encontrado'
            ]);
        }
        break;
        
    default:
        echo json_encode([
            'success' => false,
            'error' => 'Acción no válida',
            'acciones_disponibles' => ['list', 'clean', 'delete']
        ]);
}
?>
