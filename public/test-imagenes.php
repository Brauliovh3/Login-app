<?php
// Script de prueba para verificar rutas de imágenes

echo "<h2>Verificación de Rutas de Imágenes</h2>";

$baseDir = dirname(__DIR__) . '/public';
$escudoPath1 = $baseDir . '/images/escudo_peru.png';
$logoPath1 = $baseDir . '/images/logo.png';

$escudoPath2 = __DIR__ . '/images/escudo_peru.png';
$logoPath2 = __DIR__ . '/images/logo.png';

echo "<h3>Opción 1 (desde public):</h3>";
echo "Escudo: " . $escudoPath1 . " - " . (file_exists($escudoPath1) ? "✅ EXISTE" : "❌ NO EXISTE") . "<br>";
echo "Logo: " . $logoPath1 . " - " . (file_exists($logoPath1) ? "✅ EXISTE" : "❌ NO EXISTE") . "<br>";

echo "<h3>Opción 2 (desde __DIR__):</h3>";
echo "Escudo: " . $escudoPath2 . " - " . (file_exists($escudoPath2) ? "✅ EXISTE" : "❌ NO EXISTE") . "<br>";
echo "Logo: " . $logoPath2 . " - " . (file_exists($logoPath2) ? "✅ EXISTE" : "❌ NO EXISTE") . "<br>";

echo "<h3>Directorio actual:</h3>";
echo "__DIR__: " . __DIR__ . "<br>";
echo "dirname(__DIR__): " . dirname(__DIR__) . "<br>";

echo "<h3>Vista previa de imágenes:</h3>";
if (file_exists($escudoPath2)) {
    echo "<img src='images/escudo_peru.png' width='100' alt='Escudo'><br>";
}
if (file_exists($logoPath2)) {
    echo "<img src='images/logo.png' width='100' alt='Logo'><br>";
}
