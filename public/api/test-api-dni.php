<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Permitir preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Obtener DNI de parámetros
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
    // Token para API Factiliza (necesitarás obtener uno válido)
    $token = 'apis-token-1.aTSI1U7KEuT-6bbbCguH-4Y8TI6KS73N'; // Usar el mismo token por ahora
    
    // Lista de APIs a probar en orden
    $apis = [
        // API Factiliza (principal)
        [
            'url' => "https://api.factiliza.com/v1/dni/info/{$dni}",
            'headers' => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ]
        ],
        // API Decolecta (respaldo)
        [
            'url' => "https://api.decolecta.com/v1/reniec/dni?numero={$dni}",
            'headers' => [
                'Referer: https://apis.net.pe/consulta-dni-api',
                'Authorization: Bearer ' . $token,
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ]
        ],
        // API RENIEC alternativa
        [
            'url' => "https://dniruc.apisperu.com/api/v1/dni/{$dni}?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6InRlc3RAdGVzdC5jb20ifQ.bb2doqtI_pKcqT3TsCtm9-lFfwHJUkkrOkF_a1r7jW4",
            'headers' => [
                'Content-Type: application/json',
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ]
        ]
    ];
    
    $resultado = null;
    $apiUsada = '';
    
    foreach ($apis as $index => $api) {
        try {
            $curl = curl_init();
            
            curl_setopt_array($curl, array(
                CURLOPT_URL => $api['url'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 2,
                CURLOPT_TIMEOUT => 15,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => $api['headers'],
            ));
            
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            
            if (curl_errno($curl)) {
                curl_close($curl);
                continue;
            }
            
            curl_close($curl);
            
            if ($httpCode === 200 && $response) {
                $data = json_decode($response, true);
                
                // Procesar según el tipo de API
                if ($index === 0) {
                    // API Factiliza - estructura exacta que proporcionaste
                    if (isset($data['success']) && $data['success'] && isset($data['data'])) {
                        $personaData = $data['data'];
                        $nombreCompleto = $personaData['nombre_completo'] ?? '';
                        
                        // Si no hay nombre_completo, construirlo
                        if (empty($nombreCompleto)) {
                            $nombres = $personaData['nombres'] ?? '';
                            $apellidoPaterno = $personaData['apellido_paterno'] ?? '';
                            $apellidoMaterno = $personaData['apellido_materno'] ?? '';
                            $nombreCompleto = trim("$nombres $apellidoPaterno $apellidoMaterno");
                        }
                        
                        $resultado = [
                            'success' => true,
                            'dni' => $dni,
                            'nombre_completo' => $nombreCompleto,
                            'nombres' => $personaData['nombres'] ?? '',
                            'apellido_paterno' => $personaData['apellido_paterno'] ?? '',
                            'apellido_materno' => $personaData['apellido_materno'] ?? '',
                            'departamento' => $personaData['departamento'] ?? '',
                            'provincia' => $personaData['provincia'] ?? '',
                            'distrito' => $personaData['distrito'] ?? '',
                            'direccion' => $personaData['direccion'] ?? '',
                            'direccion_completa' => $personaData['direccion_completa'] ?? '',
                            'ubigeo_reniec' => $personaData['ubigeo_reniec'] ?? '',
                            'fecha_nacimiento' => $personaData['fecha_nacimiento'] ?? '',
                            'sexo' => $personaData['sexo'] ?? '',
                            'fuente' => 'Factiliza API',
                            'timestamp' => date('Y-m-d H:i:s'),
                            'raw_data' => $data
                        ];
                        $apiUsada = 'Factiliza';
                        
                        // Guardar en archivo JSON temporal
                        $tempDir = __DIR__ . '/temp_consultas';
                        if (!file_exists($tempDir)) {
                            mkdir($tempDir, 0777, true);
                        }
                        
                        $filename = $tempDir . "/dni_{$dni}_" . date('Y-m-d_H-i-s') . ".json";
                        file_put_contents($filename, json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                        
                        break;
                    }
                } else if ($index === 1) {
                    // API Decolecta
                    if (isset($data['data'])) {
                        $personaData = $data['data'];
                        $nombres = $personaData['nombres'] ?? '';
                        $apellidoPaterno = $personaData['apellido_paterno'] ?? '';
                        $apellidoMaterno = $personaData['apellido_materno'] ?? '';
                        $nombreCompleto = trim("$nombres $apellidoPaterno $apellidoMaterno");
                        
                        if (isset($personaData['nombre_completo']) && !empty($personaData['nombre_completo'])) {
                            $nombreCompleto = $personaData['nombre_completo'];
                        }
                        
                        $resultado = [
                            'success' => true,
                            'dni' => $dni,
                            'nombre_completo' => $nombreCompleto,
                            'nombres' => $nombres,
                            'apellido_paterno' => $apellidoPaterno,
                            'apellido_materno' => $apellidoMaterno,
                            'fuente' => 'Decolecta RENIEC API',
                            'raw_data' => $data
                        ];
                        $apiUsada = 'Decolecta';
                        break;
                    }
                } else {
                    // API APISPERU
                    if (isset($data['success']) && $data['success'] && isset($data['nombres'])) {
                        $nombreCompleto = trim($data['nombres'] . ' ' . 
                                             ($data['apellidoPaterno'] ?? '') . ' ' . 
                                             ($data['apellidoMaterno'] ?? ''));
                        
                        $resultado = [
                            'success' => true,
                            'dni' => $dni,
                            'nombre_completo' => $nombreCompleto,
                            'nombres' => $data['nombres'],
                            'apellido_paterno' => $data['apellidoPaterno'] ?? '',
                            'apellido_materno' => $data['apellidoMaterno'] ?? '',
                            'fuente' => 'APISPERU RENIEC API',
                            'raw_data' => $data
                        ];
                        $apiUsada = 'APISPERU';
                        break;
                    }
                }
            }
        } catch (Exception $e) {
            continue;
        }
    }
    
    if ($resultado) {
        echo json_encode($resultado);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'DNI no encontrado en ninguna fuente disponible',
            'dni' => $dni
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
