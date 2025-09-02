<?php


$url = "http://127.0.0.1:8002/buscar-acta-editar/60015091";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "=== TEST BUSCAR ACTA PARA EDITAR ===\n";
echo "URL: $url\n";
echo "HTTP Code: $httpCode\n";

if ($error) {
    echo "Error cURL: $error\n";
} else {
    echo "Response:\n";
    echo $response . "\n";
    
    
    $data = json_decode($response, true);
    if ($data) {
        echo "\n=== DATOS DECODIFICADOS ===\n";
        print_r($data);
    }
}
