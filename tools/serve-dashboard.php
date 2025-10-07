#!/usr/bin/env php
<?php

/**
 * Script personalizado para servir el dashboard
 * Uso: php serve-dashboard.php
 */

echo "🚀 Iniciando Dashboard Sistema de Gestión...\n";
echo "📍 URL principal: http://localhost/Login-app/public/dashboard.php\n";
echo "📍 URL servidor: http://127.0.0.1:8000\n";
echo "⚠️  Para detener presiona Ctrl+C\n\n";

// Verificar si XAMPP está corriendo
$xamppRunning = false;
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    // Windows
    exec('tasklist /FI "IMAGENAME eq httpd.exe" 2>NUL', $output, $return);
    $xamppRunning = ($return === 0 && count($output) > 3);
} else {
    // Linux/Mac
    exec('pgrep httpd', $output, $return);
    $xamppRunning = ($return === 0);
}

if ($xamppRunning) {
    echo "✅ XAMPP Apache detectado\n";
    echo "🌐 Dashboard disponible en: http://localhost/Login-app/public/dashboard.php\n\n";
} else {
    echo "⚠️  XAMPP no detectado, usando servidor PHP integrado...\n\n";
}

// Cambiar al directorio public
chdir(dirname(__DIR__) . '/public');

// Iniciar servidor PHP
$host = '127.0.0.1';
$port = 8000;

echo "🔄 Iniciando servidor en {$host}:{$port}...\n";
echo "🌐 Accede a: http://{$host}:{$port}\n";
echo "   (Se redirigirá automáticamente a dashboard.php)\n\n";

// Ejecutar el servidor
passthru("php -S {$host}:{$port} -t . 2>&1");