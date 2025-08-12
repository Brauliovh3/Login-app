<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Permitir preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Obtener RUC de parámetros
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
    // Datos para API Decolecta SUNAT
    $token = 'apis-token-1.aTSI1U7KEuT-6bbbCguH-4Y8TI6KS73N';
    
    // Iniciar llamada a API
    $curl = curl_init();
    
    // Configurar cURL para Decolecta SUNAT
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.decolecta.com/v1/sunat/ruc?numero=' . $ruc,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Referer: http://apis.net.pe/api-ruc',
            'Authorization: Bearer ' . $token,
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ),
    ));
    
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
    if (curl_errno($curl)) {
        throw new Exception('Error cURL: ' . curl_error($curl));
    }
    
    curl_close($curl);
    
    // Procesar respuesta
    $data = json_decode($response, true);
    
    if ($httpCode === 200 && $data) {
        // Verificar estructura de respuesta Decolecta SUNAT
        if (isset($data['data'])) {
            $empresaData = $data['data'];
            
            // Extraer datos principales
            $razonSocial = $empresaData['razon_social'] ?? 
                          $empresaData['nombre_comercial'] ?? 
                          $empresaData['nombre'] ?? '';
            
            $direccion = $empresaData['direccion'] ?? 
                        $empresaData['direccion_completa'] ?? '';
            
            $estado = $empresaData['estado'] ?? 
                     $empresaData['condicion'] ?? '';
            
            echo json_encode([
                'success' => true,
                'ruc' => $ruc,
                'razon_social' => $razonSocial,
                'direccion' => $direccion,
                'estado' => $estado,
                'departamento' => $empresaData['departamento'] ?? '',
                'provincia' => $empresaData['provincia'] ?? '',
                'distrito' => $empresaData['distrito'] ?? '',
                'tipo_contribuyente' => $empresaData['tipo'] ?? '',
                'fecha_inscripcion' => $empresaData['fecha_inscripcion'] ?? '',
                'fuente' => 'Decolecta SUNAT API',
                'timestamp' => date('Y-m-d H:i:s'),
                'raw_data' => $empresaData
            ]);
            
            // Guardar en archivo JSON temporal
            $tempDir = __DIR__ . '/temp_consultas';
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0777, true);
            }
            
            $datosCompletos = [
                'success' => true,
                'ruc' => $ruc,
                'razon_social' => $razonSocial,
                'direccion' => $direccion,
                'estado' => $estado,
                'departamento' => $empresaData['departamento'] ?? '',
                'provincia' => $empresaData['provincia'] ?? '',
                'distrito' => $empresaData['distrito'] ?? '',
                'tipo_contribuyente' => $empresaData['tipo'] ?? '',
                'fecha_inscripcion' => $empresaData['fecha_inscripcion'] ?? '',
                'fuente' => 'Decolecta SUNAT API',
                'timestamp' => date('Y-m-d H:i:s'),
                'raw_data' => $empresaData
            ];
            
            $filename = $tempDir . "/ruc_{$ruc}_" . date('Y-m-d_H-i-s') . ".json";
            file_put_contents($filename, json_encode($datosCompletos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else if (isset($data['razon_social'])) {
            // Formato directo
            echo json_encode([
                'success' => true,
                'ruc' => $ruc,
                'razon_social' => $data['razon_social'],
                'direccion' => $data['direccion'] ?? '',
                'estado' => $data['estado'] ?? '',
                'fuente' => 'Decolecta SUNAT API (directo)',
                'raw_data' => $data
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'RUC no encontrado en SUNAT',
                'raw_data' => $data
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Error en API SUNAT: HTTP ' . $httpCode,
            'response' => $response
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
