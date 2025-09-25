# README - Configuración del Dashboard

## 🚀 Cómo iniciar el Dashboard

### Opción 1: Con XAMPP (Recomendado)
1. Inicia XAMPP Control Panel
2. Ejecuta Apache
3. Visita: http://localhost/Login-app/public/dashboard.php

### Opción 2: Con php artisan serve
1. Ejecuta el archivo: `start-dashboard.bat`
2. O desde terminal: `php artisan serve`
3. Se redirigirá automáticamente a dashboard.php

### Opción 3: Con script personalizado
1. Ejecuta: `php serve-dashboard.php`
2. O desde PowerShell: `.\serve-dashboard.ps1`

## 📋 URLs Disponibles

- **URL Principal (XAMPP):** http://localhost/Login-app/public/dashboard.php
- **URL Servidor PHP:** http://127.0.0.1:8000 (redirige automáticamente)
- **URL Laravel:** http://127.0.0.1:8000/?keep_laravel (mantiene Laravel original)

## 🔧 Configuración

- Archivo `.htaccess` configurado para redirección automática
- Archivo `index.php` modificado para redirigir a dashboard.php
- Scripts de inicio automatizados creados

## 🎯 Credenciales de Acceso

- **Admin:** admin / admin123
- **Usuario:** user / user123

## 📁 Archivos Creados

- `start-dashboard.bat` - Script de Windows para iniciar fácilmente
- `serve-dashboard.ps1` - Script de PowerShell
- `serve-dashboard.php` - Script PHP personalizado
- `.env.serve` - Configuración para el servidor