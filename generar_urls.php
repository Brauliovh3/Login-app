<?php
// Script para generar URLs encriptadas - Ejecutar con: php generar_urls.php

require_once 'vendor/autoload.php';

// Configurar Laravel para usar la encriptación
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Helpers\ModuleTokenHelper;

echo "=== URLs UNIFICADAS DEL DASHBOARD ADMIN ===\n\n";

echo "🔒 URLS NORMALES (Abreviadas):\n";
echo "- Gestionar Usuarios: /dashboard/admin/usr\n";
echo "- Aprobar Usuarios: /dashboard/admin/app\n";
echo "- Infracciones: /dashboard/admin/inf\n";
echo "- Mantenimiento Conductores: /dashboard/admin/mnt-c\n";
echo "- Mantenimiento Inspectores: /dashboard/admin/mnt-i\n\n";

echo "🔐 URLS COMPLETAMENTE ENCRIPTADAS:\n";
$tokens = ModuleTokenHelper::getAllTokens();

foreach ($tokens as $module => $token) {
    echo "- " . ucwords(str_replace('-', ' ', $module)) . ":\n";
    echo "  /dashboard/panel/" . $token . "\n\n";
}

echo "=== CARACTERÍSTICAS ===\n";
echo "✅ URLs completamente ocultas\n";
echo "✅ Tokens encriptados únicos\n";
echo "✅ Mismo funcionamiento\n";
echo "✅ Redirecciones automáticas desde URLs viejas\n";
echo "✅ Seguridad mejorada\n";
echo "✅ Dashboard unificado\n\n";

echo "=== COMPATIBILIDAD ===\n";
echo "Las URLs anteriores (/admin/gestionar-usuarios, etc.) \n";
echo "automáticamente redirigen a las nuevas URLs abreviadas.\n";