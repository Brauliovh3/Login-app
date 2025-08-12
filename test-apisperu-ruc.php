<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

$ruc = $_GET['ruc'] ?? '';

if (empty($ruc)) {
    echo json_encode(['error' => 'RUC requerido']);
    exit;
}

// Validar formato RUC
if (!preg_match('/^\d{11}$/', $ruc)) {
    echo json_encode(['error' => 'RUC debe tener 11 dígitos']);
    exit;
}

try {
    // Hacer la petición a APISPERU sin token
    $url = "https://dniruc.apisperu.com/api/v1/ruc/{$ruc}";
    
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
    if (isset($data['ruc']) && isset($data['razonSocial'])) {
        $resultado = [
            'success' => true,
            'ruc' => $data['ruc'],
            'razon_social' => $data['razonSocial'],
            'nombre_comercial' => $data['nombreComercial'] ?? '',
            'telefonos' => $data['telefonos'] ?? [],
            'estado' => $data['estado'] ?? '',
            'condicion' => $data['condicion'] ?? '',
            'direccion' => $data['direccion'] ?? '',
            'departamento' => $data['departamento'] ?? '',
            'provincia' => $data['provincia'] ?? '',
            'distrito' => $data['distrito'] ?? '',
            'ubigeo' => $data['ubigeo'] ?? '',
            'capital' => $data['capital'] ?? '',
            'fuente' => 'APISPERU.com (Oficial)',
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        // Guardar en archivo temporal
        $tempDir = __DIR__ . '/temp_consultas';
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }
        
        $filename = $tempDir . "/ruc_apisperu_{$ruc}_" . date('Y-m-d_H-i-s') . ".json";
        file_put_contents($filename, json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        echo json_encode($resultado);
    } else {
        // Si no hay datos válidos
        echo json_encode([
            'success' => false,
            'error' => 'RUC no encontrado en APISPERU',
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
