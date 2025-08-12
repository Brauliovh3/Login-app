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

// Base de datos simulada de RUC para pruebas
$rucDatabase = [
    '10460278975' => [
        'ruc' => '10460278975',
        'razon_social' => 'CASTILLO TERRONES JOSE PEDRO',
        'tipo_contribuyente' => 'PERSONA NATURAL CON NEGOCIO',
        'estado' => 'ACTIVO',
        'condicion' => 'HABIDO',
        'direccion' => 'CASERIO PUÑA - TACABAMBA - CHOTA - CAJAMARCA',
        'departamento' => 'CAJAMARCA',
        'provincia' => 'CHOTA',
        'distrito' => 'TACABAMBA',
        'fecha_inscripcion' => '2010-05-15'
    ],
    '20123456789' => [
        'ruc' => '20123456789',
        'razon_social' => 'TRANSPORTES LIMA SOCIEDAD ANONIMA CERRADA',
        'tipo_contribuyente' => 'SOCIEDAD ANONIMA CERRADA',
        'estado' => 'ACTIVO',
        'condicion' => 'HABIDO',
        'direccion' => 'AV. ANGAMOS ESTE 1234 - MIRAFLORES - LIMA - LIMA',
        'departamento' => 'LIMA',
        'provincia' => 'LIMA',
        'distrito' => 'MIRAFLORES',
        'fecha_inscripcion' => '2015-03-10'
    ],
    '20987654321' => [
        'ruc' => '20987654321',
        'razon_social' => 'EMPRESA DE TRANSPORTES CUSCO EXPRESS S.A.C.',
        'tipo_contribuyente' => 'SOCIEDAD ANONIMA CERRADA',
        'estado' => 'ACTIVO',
        'condicion' => 'HABIDO',
        'direccion' => 'AV. CULTURA 567 - WANCHAQ - CUSCO - CUSCO',
        'departamento' => 'CUSCO',
        'provincia' => 'CUSCO',
        'distrito' => 'WANCHAQ',
        'fecha_inscripcion' => '2018-08-22'
    ],
    '20555444333' => [
        'ruc' => '20555444333',
        'razon_social' => 'TRANSPORTES APURIMAC S.R.L.',
        'tipo_contribuyente' => 'SOCIEDAD DE RESPONSABILIDAD LIMITADA',
        'estado' => 'ACTIVO',
        'condicion' => 'HABIDO',
        'direccion' => 'AV. NUÑEZ 890 - ABANCAY - ABANCAY - APURIMAC',
        'departamento' => 'APURIMAC',
        'provincia' => 'ABANCAY',
        'distrito' => 'ABANCAY',
        'fecha_inscripcion' => '2020-01-15'
    ]
];

try {
    // Buscar en la base de datos local
    if (isset($rucDatabase[$ruc])) {
        $empresa = $rucDatabase[$ruc];
        
        $resultado = [
            'success' => true,
            'ruc' => $ruc,
            'razon_social' => $empresa['razon_social'],
            'tipo_contribuyente' => $empresa['tipo_contribuyente'],
            'estado' => $empresa['estado'],
            'condicion' => $empresa['condicion'],
            'direccion' => $empresa['direccion'],
            'departamento' => $empresa['departamento'],
            'provincia' => $empresa['provincia'],
            'distrito' => $empresa['distrito'],
            'fecha_inscripcion' => $empresa['fecha_inscripcion'],
            'fuente' => 'Base de Datos Local SUNAT-DRTC',
            'timestamp' => date('Y-m-d H:i:s')
        ];
    } else {
        // Generar datos aleatorios para RUC no encontrado en base local
        $empresas = [
            'TRANSPORTES', 'SERVICIOS', 'COMERCIAL', 'INDUSTRIAL', 'LOGISTICA',
            'EXPRESS', 'RAPIDO', 'SEGURO', 'CONFIABLE', 'PERU'
        ];
        $tipos = [
            'S.A.C.', 'S.R.L.', 'E.I.R.L.', 'S.A.', 'PERSONA NATURAL CON NEGOCIO'
        ];
        $departamentos = ['APURIMAC', 'LIMA', 'CUSCO', 'AREQUIPA', 'AYACUCHO'];
        
        $nombreEmpresa = $empresas[array_rand($empresas)] . ' ' . $empresas[array_rand($empresas)] . ' ' . $tipos[array_rand($tipos)];
        $departamento = $departamentos[array_rand($departamentos)];
        
        $resultado = [
            'success' => true,
            'ruc' => $ruc,
            'razon_social' => $nombreEmpresa,
            'tipo_contribuyente' => $tipos[array_rand($tipos)],
            'estado' => 'ACTIVO',
            'condicion' => 'HABIDO',
            'direccion' => 'AV. PRINCIPAL S/N - ' . $departamento,
            'departamento' => $departamento,
            'provincia' => $departamento,
            'distrito' => $departamento,
            'fecha_inscripcion' => '2020-01-01',
            'fuente' => 'Generado Automáticamente - SUNAT-DRTC',
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
    
    // Guardar en archivo JSON temporal
    $tempDir = __DIR__ . '/temp_consultas';
    if (!file_exists($tempDir)) {
        mkdir($tempDir, 0777, true);
    }
    
    $filename = $tempDir . "/ruc_{$ruc}_" . date('Y-m-d_H-i-s') . ".json";
    file_put_contents($filename, json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    echo json_encode($resultado);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
