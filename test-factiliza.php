<?php
// Prueba directa de la API Factiliza
$dni = '46027897'; // DNI de prueba
$token = 'apis-token-1.aTSI1U7KEuT-6bbbCguH-4Y8TI6KS73N';

echo "<h2>Prueba API Factiliza DNI: $dni</h2>";

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.factiliza.com/v1/dni/info/$dni",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 2,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    ),
));

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

if (curl_errno($curl)) {
    echo "<p style='color: red;'>Error cURL: " . curl_error($curl) . "</p>";
} else {
    echo "<p><strong>HTTP Code:</strong> $httpCode</p>";
    echo "<p><strong>Respuesta:</strong></p>";
    echo "<pre>";
    
    $data = json_decode($response, true);
    if ($data) {
        print_r($data);
        
        // Procesar datos si están disponibles
        if (isset($data['data']) && isset($data['data']['nombres'])) {
            $personaData = $data['data'];
            $nombreCompleto = trim($personaData['nombres'] . ' ' . 
                                 ($personaData['apellido_paterno'] ?? '') . ' ' . 
                                 ($personaData['apellido_materno'] ?? ''));
            
            echo "\n\n=== DATOS PROCESADOS ===\n";
            echo "Nombre completo: $nombreCompleto\n";
            echo "Nombres: " . ($personaData['nombres'] ?? 'N/A') . "\n";
            echo "Apellido Paterno: " . ($personaData['apellido_paterno'] ?? 'N/A') . "\n";
            echo "Apellido Materno: " . ($personaData['apellido_materno'] ?? 'N/A') . "\n";
        } else {
            echo "\n\n=== ESTRUCTURA NO RECONOCIDA ===\n";
            echo "La respuesta no contiene la estructura esperada para datos de DNI.\n";
        }
    } else {
        echo "Respuesta no es JSON válido:\n";
        echo htmlspecialchars($response);
    }
    
    echo "</pre>";
}

curl_close($curl);

echo "<hr>";
echo "<p><a href='/test-api-dni.php?dni=$dni'>Probar API proxy local</a></p>";
echo "<p><a href='/fiscalizador/actas-contra'>Volver al formulario</a></p>";
?>
