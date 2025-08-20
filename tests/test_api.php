<?php
// Simular una petición POST al endpoint de actas
$url = 'http://127.0.0.1:8000/api/actas';

$data = [
    'placa_1' => 'ABC-999',
    'nombre_conductor_1' => 'Juan Pérez de Prueba',
    'licencia_conductor_1' => 'L999888777',
    'razon_social' => 'Empresa de Prueba S.A.C.',
    'ruc_dni' => '20999888777',
    'lugar_intervencion' => 'Terminal de Prueba, Abancay',
    'origen_viaje' => 'Abancay',
    'destino_viaje' => 'Lima',
    'tipo_servicio' => 'Interprovincial',
    'descripcion_hechos' => 'Prueba de registro de acta desde script',
    'monto_multa' => 500.00
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

echo "=== PROBANDO ENDPOINT API DE ACTAS ===\n\n";
echo "URL: $url\n";
echo "Datos enviados:\n";
print_r($data);
echo "\n";

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "Código HTTP: $httpCode\n";
echo "Respuesta:\n";
echo $response . "\n";

if (curl_error($ch)) {
    echo "Error cURL: " . curl_error($ch) . "\n";
}

curl_close($ch);

echo "\n=== FIN DE PRUEBA ===\n";
