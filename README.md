# 🔐 Sistema de Login con Roles

Un sistema completo de autenticación desarrollado en Laravel 11 con roles específicos y dashboard personalizado.

## 📋 Características

- ✅ **Autenticación completa** (Login/Registro)
- ✅ **Sistema de roles** (Administrador, Fiscalizador, Ventanilla)
- ✅ **Dashboards específicos** por rol con menú lateral
- ✅ **Sistema de notificaciones**
- ✅ **Funcionalidad "Recordarme"**
- ✅ **Protección de rutas** por middleware de roles
- ✅ **Diseño responsive** con Bootstrap 5

## 🛠️ Tecnologías

- **Laravel 11**
- **MySQL**
- **Bootstrap 5.1.3**
- **Font Awesome 6.0**
- **PHP 8.2+**

## 🚀 Instalación

### Paso 1: Clonar el repositorio
```bash
git clone https://github.com/Brauliovh3/Login-app.git
cd Login-app
```

### Paso 2: Instalar dependencias
```bash
composer install
```

### Paso 3: Configurar entorno
```bash
# Copiar archivo de configuración
copy .env.example .env

# Generar clave de aplicación
php artisan key:generate
```

### Paso 4: Configurar base de datos
1. Crear base de datos MySQL llamada `login_app`
2. Configurar credenciales en `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=login_app
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password
```

### Paso 5: Ejecutar migraciones y seeders
```bash
# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders para crear usuarios de prueba
php artisan db:seed
```

### Paso 6: Iniciar servidor
```bash
php artisan serve
```

## 👥 Usuarios de Prueba

Después de ejecutar los seeders, tendrás estos usuarios disponibles:

| Rol | Usuario | Email | Contraseña |
|-----|---------|-------|------------|
| Administrador | admin | admin@sistema.com | 123456789 |
| Fiscalizador | fiscalizador | fiscalizador@sistema.com | 123456789 |
| Ventanilla | ventanilla | ventanilla@sistema.com | 123456789 |

## 🏗️ Estructura del Proyecto

```
├── app/
│   ├── Http/
│   │   ├── Controllers/Auth/     # Controladores de autenticación
│   │   ├── Middleware/           # Middleware personalizado
│   ├── Models/                   # Modelos Eloquent
├── database/
│   ├── migrations/               # Migraciones de base de datos
│   ├── seeders/                  # Seeders para datos de prueba
├── resources/
│   └── views/
│       ├── administrador/        # Vistas del administrador
│       ├── fiscalizador/         # Vistas del fiscalizador
│       ├── ventanilla/           # Vistas de ventanilla
│       ├── auth/                 # Vistas de autenticación
│       └── layouts/              # Layouts de plantillas
```

## 🔐 Sistema de Roles y Permisos

### URLs por Rol:

**🔧 Administrador:**
- `/admin/dashboard` - Dashboard principal
- `/admin/usuarios` - Gestión de usuarios
- `/admin/reportes` - Reportes del sistema
- `/admin/configuracion` - Configuración general

**🔍 Fiscalizador:**
- `/fiscalizador/dashboard` - Dashboard principal
- `/fiscalizador/nueva-inspeccion` - Nueva inspección
- `/fiscalizador/inspecciones` - Mis inspecciones
- `/fiscalizador/reportes` - Reportes de fiscalización

**🪟 Ventanilla:**
- `/ventanilla/dashboard` - Dashboard principal
- `/ventanilla/nueva-atencion` - Nueva atención
- `/ventanilla/tramites` - Gestión de trámites
- `/ventanilla/consultar` - Consultar estados

## 🤝 Colaboración

### Para trabajar en equipo:

1. **Clonar el repositorio:**
   ```bash
   git clone https://github.com/Brauliovh3/Login-app.git
   ```

2. **Crear rama para tu trabajo:**
   ```bash
   git checkout -b feature/nombre-feature
   ```

3. **Hacer cambios y commit:**
   ```bash
   git add .
   git commit -m "Descripción de los cambios"
   ```

4. **Subir cambios:**
   ```bash
   git push origin feature/nombre-feature
   ```

5. **Crear Pull Request** en GitHub

### Buenas prácticas:
- ✅ Siempre trabajar en ramas separadas
- ✅ Usar mensajes de commit descriptivos
- ✅ Actualizar tu rama antes de hacer cambios: `git pull origin main`
- ✅ Probar cambios antes de hacer push

## 📝 Comandos Útiles

```bash
# Limpiar cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Crear nuevo usuario desde artisan
php artisan tinker
> User::create(['name' => 'Nombre', 'username' => 'usuario', 'email' => 'email@test.com', 'password' => Hash::make('password'), 'role' => 'administrador']);

# Ejecutar migraciones específicas
php artisan migrate --path=/database/migrations/nombre_migracion.php

# Rollback migraciones
php artisan migrate:rollback
```

## 🔧 Configuración Adicional

### Sesiones "Recordarme":
- Por defecto, las sesiones expiran al cerrar el navegador
- El usuario puede marcar "Recordarme" para mantener la sesión activa
- Configuración en `.env`: `SESSION_EXPIRE_ON_CLOSE=true`

### Notificaciones:
- Sistema completo de notificaciones por usuario
- Notificaciones en tiempo real en el dashboard
- Contador de notificaciones no leídas

## 🐛 Solución de Problemas

### Error "Please provide a valid cache path"
```bash
php artisan config:cache
```

### Error de base de datos
1. Verificar que MySQL esté ejecutándose
2. Verificar credenciales en `.env`
3. Ejecutar `php artisan migrate:fresh --seed`

### Permisos en Windows/XAMPP
- Asegurar que la carpeta `storage` tenga permisos de escritura

## 👨‍💻 Desarrolladores

- **Braulio Velásquez** - [@Brauliovh3](https://github.com/Brauliovh3)

## 📄 Licencia

Este proyecto está bajo la Licencia MIT.
