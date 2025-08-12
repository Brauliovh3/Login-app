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

// Base de datos simulada de DNI para pruebas
$dniDatabase = [
    '46027897' => [
        'dni' => '46027897',
        'nombres' => 'JOSE PEDRO',
        'apellido_paterno' => 'CASTILLO',
        'apellido_materno' => 'TERRONES',
        'nombre_completo' => 'CASTILLO TERRONES, JOSE PEDRO',
        'departamento' => 'CAJAMARCA',
        'provincia' => 'CHOTA',
        'distrito' => 'TACABAMBA',
        'direccion' => 'CASERIO PUÑA',
        'sexo' => 'M',
        'fecha_nacimiento' => '1985-03-15'
    ],
    '12345678' => [
        'dni' => '12345678',
        'nombres' => 'JUAN CARLOS',
        'apellido_paterno' => 'PEREZ',
        'apellido_materno' => 'GARCIA',
        'nombre_completo' => 'PEREZ GARCIA, JUAN CARLOS',
        'departamento' => 'APURIMAC',
        'provincia' => 'ABANCAY',
        'distrito' => 'ABANCAY',
        'direccion' => 'AV. PRINCIPAL 123',
        'sexo' => 'M',
        'fecha_nacimiento' => '1990-07-20'
    ],
    '87654321' => [
        'dni' => '87654321',
        'nombres' => 'MARIA ELENA',
        'apellido_paterno' => 'RODRIGUEZ',
        'apellido_materno' => 'LOPEZ',
        'nombre_completo' => 'RODRIGUEZ LOPEZ, MARIA ELENA',
        'departamento' => 'LIMA',
        'provincia' => 'LIMA',
        'distrito' => 'MIRAFLORES',
        'direccion' => 'JR. LAS FLORES 456',
        'sexo' => 'F',
        'fecha_nacimiento' => '1988-12-10'
    ],
    '11111111' => [
        'dni' => '11111111',
        'nombres' => 'LUIS ALBERTO',
        'apellido_paterno' => 'GONZALEZ',
        'apellido_materno' => 'MENDOZA',
        'nombre_completo' => 'GONZALEZ MENDOZA, LUIS ALBERTO',
        'departamento' => 'CUSCO',
        'provincia' => 'CUSCO',
        'distrito' => 'WANCHAQ',
        'direccion' => 'AV. CULTURA 789',
        'sexo' => 'M',
        'fecha_nacimiento' => '1982-05-25'
    ]
];

try {
    // Buscar en la base de datos local
    if (isset($dniDatabase[$dni])) {
        $persona = $dniDatabase[$dni];
        
        $resultado = [
            'success' => true,
            'dni' => $dni,
            'nombre_completo' => $persona['nombre_completo'],
            'nombres' => $persona['nombres'],
            'apellido_paterno' => $persona['apellido_paterno'],
            'apellido_materno' => $persona['apellido_materno'],
            'departamento' => $persona['departamento'],
            'provincia' => $persona['provincia'],
            'distrito' => $persona['distrito'],
            'direccion' => $persona['direccion'],
            'sexo' => $persona['sexo'],
            'fecha_nacimiento' => $persona['fecha_nacimiento'],
            'fuente' => 'Base de Datos Local DRTC',
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        // Guardar en archivo JSON temporal
        $tempDir = __DIR__ . '/temp_consultas';
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }
        
        $filename = $tempDir . "/dni_{$dni}_" . date('Y-m-d_H-i-s') . ".json";
        file_put_contents($filename, json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        echo json_encode($resultado);
    } else {
        // Generar datos aleatorios para DNI no encontrado en base local
        $nombres = ['CARLOS', 'MARIA', 'JOSE', 'ANA', 'LUIS', 'ROSA', 'MIGUEL', 'ELENA'];
        $apellidos = ['GARCIA', 'RODRIGUEZ', 'MARTINEZ', 'LOPEZ', 'GONZALEZ', 'PEREZ', 'SANCHEZ', 'RAMIREZ'];
        $departamentos = ['APURIMAC', 'LIMA', 'CUSCO', 'AREQUIPA', 'AYACUCHO', 'HUANCAVELICA'];
        
        $nombreAleatorio = $nombres[array_rand($nombres)];
        $apellido1 = $apellidos[array_rand($apellidos)];
        $apellido2 = $apellidos[array_rand($apellidos)];
        $departamento = $departamentos[array_rand($departamentos)];
        
        $resultado = [
            'success' => true,
            'dni' => $dni,
            'nombre_completo' => "$apellido1 $apellido2, $nombreAleatorio",
            'nombres' => $nombreAleatorio,
            'apellido_paterno' => $apellido1,
            'apellido_materno' => $apellido2,
            'departamento' => $departamento,
            'provincia' => $departamento,
            'distrito' => $departamento,
            'direccion' => 'AV. PRINCIPAL S/N',
            'sexo' => 'M',
            'fecha_nacimiento' => '1990-01-01',
            'fuente' => 'Generado Automáticamente - DRTC',
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        // Guardar en archivo JSON temporal
        $tempDir = __DIR__ . '/temp_consultas';
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }
        
        $filename = $tempDir . "/dni_{$dni}_" . date('Y-m-d_H-i-s') . ".json";
        file_put_contents($filename, json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        echo json_encode($resultado);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
