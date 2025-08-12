<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

$dni = $_GET['dni'] ?? '';

if (empty($dni)) {
    echo json_encode(['error' => 'DNI requerido']);
    exit;
}

// Validar formato DNI
if (!preg_match('/^\d{8}$/', $dni)) {
    echo json_encode(['error' => 'DNI debe tener 8 dígitos']);
    exit;
}

try {
    // Hacer la petición a APISPERU sin token
    $url = "https://dniruc.apisperu.com/api/v1/dni/{$dni}";
    
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => [
                'Content-Type: application/json',
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ],
            'timeout' => 10
        ]
    ]);
    
    $response = file_get_contents($url, false, $context);
    
    if ($response === false) {
        throw new Exception('Error al conectar con APISPERU');
    }
    
    $data = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Error al decodificar respuesta JSON');
    }
    
    // Verificar si la respuesta tiene los datos esperados
    if (isset($data['dni']) && isset($data['nombres'])) {
        $resultado = [
            'success' => true,
            'dni' => $data['dni'],
            'nombres' => $data['nombres'],
            'apellidoPaterno' => $data['apellidoPaterno'] ?? '',
            'apellidoMaterno' => $data['apellidoMaterno'] ?? '',
            'nombre_completo' => trim($data['nombres'] . ' ' . ($data['apellidoPaterno'] ?? '') . ' ' . ($data['apellidoMaterno'] ?? '')),
            'codVerifica' => $data['codVerifica'] ?? '',
            'fuente' => 'APISPERU.com (Oficial)',
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        // Guardar en archivo temporal
        $tempDir = __DIR__ . '/temp_consultas';
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }
        
        $filename = $tempDir . "/dni_apisperu_{$dni}_" . date('Y-m-d_H-i-s') . ".json";
        file_put_contents($filename, json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        echo json_encode($resultado);
    } else {
        // Si no hay datos válidos
        echo json_encode([
            'success' => false,
            'error' => 'DNI no encontrado en APISPERU',
            'raw_response' => $data
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
