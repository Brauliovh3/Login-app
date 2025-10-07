# Script para servir la aplicación con la URL deseada
# Ejecuta: .\serve-dashboard.ps1

Write-Host "🚀 Iniciando servidor para Dashboard..." -ForegroundColor Green
Write-Host "📍 URL disponible en: http://localhost/Login-app/public/dashboard.php" -ForegroundColor Yellow
Write-Host "📍 URL alternativa: http://127.0.0.1:8000" -ForegroundColor Yellow
Write-Host ""
Write-Host "⚠️  Asegúrate de que XAMPP esté ejecutándose (Apache)" -ForegroundColor Cyan
Write-Host "⚠️  Para detener el servidor presiona Ctrl+C" -ForegroundColor Cyan
Write-Host ""

# Verificar si XAMPP está corriendo
$xamppRunning = Get-Process -Name "httpd" -ErrorAction SilentlyContinue
if ($xamppRunning) {
    Write-Host "✅ XAMPP Apache detectado corriendo" -ForegroundColor Green
    Write-Host "🌐 Dashboard disponible en: http://localhost/Login-app/public/dashboard.php" -ForegroundColor Green
} else {
    Write-Host "⚠️  XAMPP Apache no detectado. Iniciando servidor PHP..." -ForegroundColor Yellow
}

# Cambiar al directorio del proyecto
Set-Location (Split-Path $PSScriptRoot -Parent)

# Iniciar el servidor PHP
Write-Host "🔄 Iniciando php artisan serve..." -ForegroundColor Blue
php artisan serve --host=127.0.0.1 --port=8000