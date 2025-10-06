<?php
$config = require 'config/database.php';

try {
    $dsn = "mysql:host={$config['host']};dbname={$config['name']}";
    $pdo = new PDO($dsn, $config['user'], $config['pass'], $config['options']);
    echo "✅ Conexión a base de datos exitosa\n";
    echo "📊 Base de datos: {$config['name']}\n";
    echo "🏠 Host: {$config['host']}\n";
    echo "👤 Usuario: {$config['user']}\n";
} catch(PDOException $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "\n";
}
