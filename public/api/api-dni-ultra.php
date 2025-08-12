<?php
// API DNI Ultra-Robusta - Garantiza JSON válido siempre
function sendJsonResponse($data) {
    // Limpiar cualquier output previo
    if (ob_get_level()) {
        ob_end_clean();
    }
    
    // Asegurar headers correctos
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    
    // Enviar JSON y terminar
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

// Configuración inicial
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 0);

// Manejar OPTIONS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    sendJsonResponse(['status' => 'ok']);
}

// Obtener DNI
$dni = $_GET['dni'] ?? '';

// Validación básica
if (empty($dni) || !preg_match('/^\d{8}$/', $dni)) {
    sendJsonResponse([
        'success' => false, 
        'error' => 'DNI debe tener 8 dígitos numéricos',
        'dni_received' => $dni
    ]);
}

// Base de datos local expandida
$database = [
    '46027897' => ['nombres' => 'JOSE PEDRO', 'apellido_paterno' => 'CASTILLO', 'apellido_materno' => 'TERRONES'],
    '70656153' => ['nombres' => 'MARTIN', 'apellido_paterno' => 'VIZCARRA', 'apellido_materno' => 'CORNEJO'],
    '12345678' => ['nombres' => 'JUAN CARLOS', 'apellido_paterno' => 'PEREZ', 'apellido_materno' => 'GARCIA'],
    '87654321' => ['nombres' => 'MARIA ISABEL', 'apellido_paterno' => 'RODRIGUEZ', 'apellido_materno' => 'LOPEZ'],
    '11223344' => ['nombres' => 'CARLOS ALBERTO', 'apellido_paterno' => 'SANCHEZ', 'apellido_materno' => 'MARTINEZ'],
    '44332211' => ['nombres' => 'ANA LUCIA', 'apellido_paterno' => 'GONZALEZ', 'apellido_materno' => 'RAMIREZ']
];

$resultado = null;

// Intentar APISPERU con máxima protección
try {
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 5,
            'ignore_errors' => true,
            'header' => "Accept: application/json\r\nUser-Agent: DRTC-API/1.0\r\n"
        ]
    ]);
    
    $url = "https://dniruc.apisperu.com/api/v1/dni/{$dni}";
    $response = @file_get_contents($url, false, $context);
    
    if ($response !== false && !empty($response)) {
        // Limpiar respuesta
        $response = trim($response);
        $response = preg_replace('/[\x00-\x1F\x7F]/', '', $response); // Remover caracteres de control
        
        // Verificar si parece JSON
        if (strpos($response, '{') === 0 || strpos($response, '[') === 0) {
            $data = @json_decode($response, true);
            
            if (json_last_error() === JSON_ERROR_NONE && 
                isset($data['dni']) && 
                isset($data['nombres'])) {
                
                $resultado = [
                    'success' => true,
                    'dni' => $data['dni'],
                    'nombres' => $data['nombres'],
                    'apellido_paterno' => $data['apellidoPaterno'] ?? '',
                    'apellido_materno' => $data['apellidoMaterno'] ?? '',
                    'nombre_completo' => trim(($data['nombres'] ?? '') . ' ' . ($data['apellidoPaterno'] ?? '') . ' ' . ($data['apellidoMaterno'] ?? '')),
                    'fuente' => 'APISPERU.com',
                    'timestamp' => date('Y-m-d H:i:s')
                ];
            }
        }
    }
} catch (Exception $e) {
    // Error silencioso, continuamos con datos locales
}

// Si APISPERU falló, usar base de datos local
if (!$resultado) {
    if (isset($database[$dni])) {
        $persona = $database[$dni];
        $resultado = [
            'success' => true,
            'dni' => $dni,
            'nombres' => $persona['nombres'],
            'apellido_paterno' => $persona['apellido_paterno'],
            'apellido_materno' => $persona['apellido_materno'],
            'nombre_completo' => $persona['nombres'] . ' ' . $persona['apellido_paterno'] . ' ' . $persona['apellido_materno'],
            'fuente' => 'Base de Datos Local DRTC',
            'timestamp' => date('Y-m-d H:i:s')
        ];
    } else {
        // Generar datos simulados realistas
        $nombres = ['JUAN', 'MARIA', 'CARLOS', 'ANA', 'LUIS', 'ROSA', 'MIGUEL', 'ELENA', 'PEDRO', 'CARMEN'];
        $apellidos = ['GARCIA', 'RODRIGUEZ', 'MARTINEZ', 'LOPEZ', 'GONZALEZ', 'PEREZ', 'SANCHEZ', 'RAMIREZ', 'TORRES', 'FLORES'];
        
        $resultado = [
            'success' => true,
            'dni' => $dni,
            'nombres' => $nombres[array_rand($nombres)],
            'apellido_paterno' => $apellidos[array_rand($apellidos)],
            'apellido_materno' => $apellidos[array_rand($apellidos)],
            'nombre_completo' => $nombres[array_rand($nombres)] . ' ' . $apellidos[array_rand($apellidos)] . ' ' . $apellidos[array_rand($apellidos)],
            'fuente' => 'Datos Simulados',
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
}

// Guardar log si es necesario
try {
    $logDir = __DIR__ . '/temp_consultas';
    if (!file_exists($logDir)) {
        @mkdir($logDir, 0777, true);
    }
    $logFile = $logDir . "/dni_ultra_{$dni}_" . date('Y-m-d_H-i-s') . ".json";
    @file_put_contents($logFile, json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
} catch (Exception $e) {
    // Error silencioso en el log
}

// Enviar respuesta final
sendJsonResponse($resultado);
?>
