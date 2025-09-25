@echo off
title Dashboard Sistema de Gestion
color 0A

echo.
echo ========================================
echo   DASHBOARD SISTEMA DE GESTION
echo ========================================
echo.
echo 🚀 Iniciando servidor...
echo 📍 URL: http://localhost/Login-app/public/dashboard.php
echo 📍 Servidor: http://127.0.0.1:8000
echo.
echo ⚠️  Para detener presione Ctrl+C
echo ========================================
echo.

REM Verificar si XAMPP está corriendo
tasklist /FI "IMAGENAME eq httpd.exe" 2>NUL | find /I "httpd.exe" >NUL
if %ERRORLEVEL% EQU 0 (
    echo ✅ XAMPP Apache detectado
    echo 🌐 Dashboard disponible en: http://localhost/Login-app/public/dashboard.php
    echo.
) else (
    echo ⚠️  XAMPP no detectado, usando servidor PHP integrado...
    echo.
)

REM Cambiar al directorio del proyecto
cd /d "%~dp0"

REM Verificar si PHP está disponible
php --version >NUL 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo ❌ Error: PHP no está disponible en el PATH
    echo    Asegúrate de tener PHP instalado y configurado
    pause
    exit /b 1
)

REM Iniciar el servidor
echo 🔄 Iniciando servidor PHP...
echo.
php artisan serve --host=127.0.0.1 --port=8000

pause