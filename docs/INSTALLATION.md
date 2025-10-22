# 🚀 Guía de Instalación - Sistema de Votación

## 📋 Tabla de Contenidos

1. [Requisitos del Sistema](#requisitos-del-sistema)
2. [Instalación con XAMPP](#instalación-con-xampp)
3. [Configuración de Base de Datos](#configuración-de-base-de-datos)
4. [Configuración del Sistema](#configuración-del-sistema)
5. [Verificación de la Instalación](#verificación-de-la-instalación)
6. [Configuración de Producción](#configuración-de-producción)
7. [Solución de Problemas](#solución-de-problemas)
8. [Mantenimiento](#mantenimiento)

---

## 💻 Requisitos del Sistema

### Requisitos Mínimos

#### 🖥️ Servidor Web
- **Apache** 2.4 o superior
- **Módulo mod_rewrite** habilitado
- **PHP** 7.4 o superior (recomendado 8.0+)
- **MySQL** 5.7 o superior (o MariaDB 10.2+)

#### 🔧 Extensiones PHP Requeridas
- `pdo` y `pdo_mysql` - Para conexión a base de datos
- `session` - Para manejo de sesiones
- `json` - Para procesamiento de datos JSON
- `mbstring` - Para strings multibyte
- `openssl` - Para funciones de seguridad

#### 💾 Recursos del Sistema
- **RAM**: Mínimo 512MB (recomendado 1GB+)
- **Espacio en disco**: 100MB para la aplicación
- **Procesador**: 1 GHz o superior

### Requisitos Recomendados

#### 🎯 Para Desarrollo
- **XAMPP** 8.2.4 o superior (incluye Apache, PHP, MySQL)
- **PHP** 8.2+ para mejor rendimiento
- **MySQL** 8.0+ para características avanzadas
- **Navegador** moderno (Chrome, Firefox, Safari, Edge)

#### 🏢 Para Producción
- **Linux** (Ubuntu 20.04+ / CentOS 8+)
- **Apache** con SSL configurado
- **PHP-FPM** para mejor rendimiento
- **MySQL** con configuración optimizada
- **Certificado SSL** válido

---

## 📦 Instalación con XAMPP

### Paso 1: Descargar e Instalar XAMPP

#### Para Windows
```bash
1. Ir a https://www.apachefriends.org/
2. Descargar XAMPP para Windows
3. Ejecutar el instalador como administrador
4. Seguir el asistente de instalación
5. Instalar en C:\xampp (ruta por defecto)
```

#### Para macOS
```bash
1. Ir a https://www.apachefriends.org/
2. Descargar XAMPP para macOS
3. Abrir el archivo .dmg descargado
4. Arrastrar XAMPP a Applications
5. Ejecutar desde /Applications/XAMPP/
```

#### Para Linux
```bash
1. Descargar XAMPP para Linux
2. Hacer ejecutable: chmod +x xampp-linux-*-installer.run
3. Ejecutar: sudo ./xampp-linux-*-installer.run
4. Seguir las instrucciones del instalador
```

### Paso 2: Iniciar Servicios de XAMPP

#### Panel de Control XAMPP
```
┌─────────────────────────────────────┐
│        XAMPP Control Panel          │
├─────────────────────────────────────┤
│ Apache    [Start] [Stop] [Config]   │
│ MySQL     [Start] [Stop] [Config]   │
│ FileZilla [Start] [Stop] [Config]   │
│ Mercury   [Start] [Stop] [Config]   │
├─────────────────────────────────────┤
│ ✅ Apache Running on Port 80        │
│ ✅ MySQL Running on Port 3306       │
└─────────────────────────────────────┘
```

#### Servicios Necesarios
1. **Iniciar Apache** - Servidor web
2. **Iniciar MySQL** - Base de datos
3. **Verificar puertos** - 80 (Apache) y 3306 (MySQL)

### Paso 3: Verificar Instalación XAMPP

#### Probar Apache
```bash
# Abrir navegador y ir a:
http://localhost

# Debería mostrar la página de bienvenida de XAMPP
```

#### Probar MySQL
```bash
# Abrir navegador y ir a:
http://localhost/phpmyadmin

# Debería mostrar la interfaz de phpMyAdmin
```

---

## 🗄️ Configuración de Base de Datos

### Paso 1: Crear Base de Datos

#### Usando phpMyAdmin
1. **Abrir** `http://localhost/phpmyadmin`
2. **Hacer clic** en "Nuevo" (New)
3. **Nombrar** la base de datos: `voto_db`
4. **Seleccionar** cotejamiento: `utf8mb4_unicode_ci`
5. **Hacer clic** en "Crear"

#### Usando Línea de Comandos
```sql
-- Conectar a MySQL
mysql -u root -p

-- Crear base de datos
CREATE DATABASE voto_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Verificar creación
SHOW DATABASES;
```

### Paso 2: Importar Esquema

#### Método 1: phpMyAdmin
1. **Seleccionar** base de datos `voto_db`
2. **Hacer clic** en pestaña "Importar"
3. **Seleccionar archivo** `database/schema.sql`
4. **Hacer clic** en "Continuar"

#### Método 2: Línea de Comandos
```bash
# Navegar a la carpeta del proyecto
cd /Applications/XAMPP/xamppfiles/htdocs/voto

# Importar esquema
mysql -u root -p voto_db < database/schema.sql
```

### Paso 3: Verificar Importación

#### Comprobar Tablas
```sql
-- Conectar a la base de datos
USE voto_db;

-- Listar tablas
SHOW TABLES;

-- Verificar estructura de usuarios
DESCRIBE users;

-- Verificar datos iniciales
SELECT * FROM users;
SELECT * FROM roles;
```

#### Resultado Esperado
```
Tables_in_voto_db:
- activity_logs
- roles
- user_sessions
- users

Usuarios iniciales:
- admin (Administrador)
- editor1 (Editor)
- viewer1 (Viewer)

Roles iniciales:
- admin
- editor
- viewer
```

---

## ⚙️ Configuración del Sistema

### Paso 1: Copiar Archivos del Sistema

#### Estructura de Directorio
```bash
# XAMPP Windows
C:\xampp\htdocs\voto\

# XAMPP macOS/Linux
/Applications/XAMPP/xamppfiles/htdocs/voto/
```

#### Verificar Archivos
```
voto/
├── index.php                 ✅
├── .htaccess                 ✅
├── app/                      ✅
├── assets/                   ✅
├── config/                   ✅
├── core/                     ✅
├── database/                 ✅
└── docs/                     ✅
```

### Paso 2: Configurar Base de Datos

#### Editar `config/config.php`
```php
<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');      // Host de MySQL
define('DB_NAME', 'voto_db');        // Nombre de la BD
define('DB_USER', 'root');           // Usuario MySQL
define('DB_PASS', '');               // Contraseña MySQL (vacía en XAMPP)

// URL base del sistema
define('BASE_URL', 'http://localhost/voto/');

// Nombre de la aplicación
define('APP_NAME', 'Sistema de Votación');
?>
```

#### Configuraciones Importantes
- **DB_HOST**: `localhost` para XAMPP local
- **DB_USER**: `root` por defecto en XAMPP
- **DB_PASS**: Vacío `''` por defecto en XAMPP
- **BASE_URL**: Ajustar según tu configuración

### Paso 3: Configurar .htaccess

#### Verificar .htaccess
```apache
RewriteEngine On

# Redirigir todas las solicitudes al index.php
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
```

#### Verificar mod_rewrite
```php
// Crear archivo test-rewrite.php
<?php
if (in_array('mod_rewrite', apache_get_modules())) {
    echo "mod_rewrite está habilitado ✅";
} else {
    echo "mod_rewrite NO está habilitado ❌";
}
?>
```

### Paso 4: Configurar Permisos

#### Permisos en Linux/macOS
```bash
# Navegar al directorio
cd /Applications/XAMPP/xamppfiles/htdocs/

# Establecer permisos
chmod -R 755 voto/
chmod -R 644 voto/*.php
chmod -R 644 voto/app/
chmod -R 644 voto/config/
```

#### Permisos en Windows
- **Generalmente no es necesario** ajustar permisos en Windows con XAMPP
- **Verificar** que no haya restricciones de antivirus

---

## ✅ Verificación de la Instalación

### Paso 1: Prueba Básica

#### Acceder al Sistema
```bash
# Abrir navegador web
# Ir a: http://localhost/voto/

# Debería mostrar la página de login
```

#### Verificar Login
```
Credenciales de prueba:
Usuario: admin
Contraseña: Estatanteria12022
```

### Paso 2: Pruebas Funcionales

#### Lista de Verificación
```
✅ La página de login se carga correctamente
✅ Puedo iniciar sesión con admin/Estatanteria12022
✅ El dashboard se muestra después del login
✅ Puedo ver el menú con Usuarios y Roles (como admin)
✅ Puedo acceder a la gestión de usuarios
✅ Puedo cerrar sesión correctamente
```

### Paso 3: Prueba de Roles

#### Probar Diferentes Usuarios
```bash
# Usuario Editor
Usuario: editor1
Contraseña: admin123
Verificar: Solo ve Dashboard

# Usuario Viewer
Usuario: viewer1
Contraseña: admin123
Verificar: Solo ve Dashboard
```

### Paso 4: Crear Script de Diagnóstico

#### Archivo `diagnostico.php`
```php
<?php
echo "<h1>Diagnóstico del Sistema</h1>";

// Verificar PHP
echo "<h2>PHP</h2>";
echo "Versión: " . phpversion() . "<br>";
echo "Extensiones requeridas:<br>";
$extensiones = ['pdo', 'pdo_mysql', 'session', 'json'];
foreach ($extensiones as $ext) {
    echo "- $ext: " . (extension_loaded($ext) ? '✅' : '❌') . "<br>";
}

// Verificar configuración
echo "<h2>Configuración</h2>";
require_once 'config/config.php';
echo "BASE_URL: " . BASE_URL . "<br>";
echo "DB_HOST: " . DB_HOST . "<br>";
echo "DB_NAME: " . DB_NAME . "<br>";

// Verificar base de datos
echo "<h2>Base de Datos</h2>";
try {
    require_once 'config/database.php';
    $db = Database::getInstance();
    echo "Conexión: ✅<br>";
    
    $stmt = $db->getConnection()->query("SELECT COUNT(*) as total FROM users");
    $result = $stmt->fetch();
    echo "Usuarios: " . $result['total'] . "<br>";
} catch (Exception $e) {
    echo "Error: ❌ " . $e->getMessage() . "<br>";
}
?>
```

---

## 🔐 Configuración de Producción

### Configuraciones de Seguridad

#### 1. Configurar HTTPS
```apache
# En .htaccess (producción)
RewriteEngine On

# Forzar HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Resto de configuraciones...
```

#### 2. Configurar Headers de Seguridad
```apache
# Agregar a .htaccess
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Strict-Transport-Security "max-age=31536000"
</IfModule>
```

#### 3. Configurar PHP para Producción
```php
// En config/config.php para producción
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/logs/php_errors.log');

// Configurar sesiones seguras
ini_set('session.cookie_secure', 1);      // Solo HTTPS
ini_set('session.cookie_httponly', 1);    // Solo HTTP
ini_set('session.use_strict_mode', 1);    // Modo estricto
```

### Base de Datos en Producción

#### 1. Crear Usuario Específico
```sql
-- Crear usuario específico para la aplicación
CREATE USER 'voto_app'@'localhost' IDENTIFIED BY 'contraseña_segura';

-- Otorgar permisos específicos
GRANT SELECT, INSERT, UPDATE, DELETE ON voto_db.* TO 'voto_app'@'localhost';

-- Aplicar cambios
FLUSH PRIVILEGES;
```

#### 2. Actualizar Configuración
```php
// config/config.php para producción
define('DB_USER', 'voto_app');
define('DB_PASS', 'contraseña_segura');
```

### Backup y Monitoreo

#### 1. Script de Backup
```bash
#!/bin/bash
# backup.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/path/to/backups"
DB_NAME="voto_db"

# Crear backup de base de datos
mysqldump -u voto_app -p$DB_PASS $DB_NAME > $BACKUP_DIR/voto_db_$DATE.sql

# Backup de archivos
tar -czf $BACKUP_DIR/voto_files_$DATE.tar.gz /path/to/voto/

echo "Backup completado: $DATE"
```

#### 2. Monitoreo de Logs
```bash
# Monitorear logs de Apache
tail -f /var/log/apache2/error.log

# Monitorear logs de PHP
tail -f /var/log/php_errors.log

# Monitorear logs de MySQL
tail -f /var/log/mysql/error.log
```

---

## 🛠️ Solución de Problemas

### Problemas Comunes

#### ❌ Error 500 - Internal Server Error

**Causas posibles:**
- Archivo .htaccess mal configurado
- Permisos de archivos incorrectos
- Error en código PHP

**Soluciones:**
```bash
1. Verificar logs de Apache:
   tail -f /Applications/XAMPP/xamppfiles/logs/error_log

2. Verificar .htaccess:
   Renombrar temporalmente a .htaccess.bak

3. Verificar permisos:
   chmod 755 directorio/
   chmod 644 archivo.php
```

#### ❌ No se puede conectar a la base de datos

**Causas posibles:**
- MySQL no está ejecutándose
- Credenciales incorrectas
- Base de datos no existe

**Soluciones:**
```bash
1. Verificar MySQL:
   # En XAMPP, comprobar que MySQL esté iniciado

2. Probar conexión:
   mysql -u root -p

3. Verificar configuración:
   # Revisar config/config.php
```

#### ❌ Página en blanco

**Causas posibles:**
- Error fatal en PHP
- Memoria insuficiente
- Timeout del script

**Soluciones:**
```php
1. Habilitar errores temporalmente:
   ini_set('display_errors', 1);
   error_reporting(E_ALL);

2. Verificar logs de PHP

3. Aumentar límites:
   ini_set('memory_limit', '256M');
   ini_set('max_execution_time', 300);
```

### Comandos de Diagnóstico

#### Verificar Estado de Servicios
```bash
# En Linux
systemctl status apache2
systemctl status mysql

# En macOS con XAMPP
/Applications/XAMPP/xamppfiles/bin/mysql.server status
```

#### Verificar Configuración PHP
```php
<?php
// info.php - Eliminar después de usar
phpinfo();
?>
```

#### Verificar Base de Datos
```sql
-- Verificar tablas
SHOW TABLES FROM voto_db;

-- Verificar usuarios
SELECT * FROM voto_db.users;

-- Verificar proceso de MySQL
SHOW PROCESSLIST;
```

---

## 🔄 Mantenimiento

### Tareas Regulares

#### Diarias
- [ ] Verificar logs de errores
- [ ] Comprobar disponibilidad del servicio
- [ ] Revisar conexiones activas

#### Semanales
- [ ] Backup de base de datos
- [ ] Limpiar logs antiguos
- [ ] Verificar espacio en disco

#### Mensuales
- [ ] Actualizar dependencias
- [ ] Revisar configuraciones de seguridad
- [ ] Auditar cuentas de usuario

### Actualizaciones

#### Actualizar Sistema
1. **Hacer backup** completo
2. **Descargar** nueva versión
3. **Comparar** archivos de configuración
4. **Aplicar** cambios gradualmente
5. **Verificar** funcionamiento

#### Actualizar Base de Datos
```sql
-- Ejemplo de script de migración
ALTER TABLE users ADD COLUMN phone VARCHAR(20) AFTER email;
UPDATE users SET phone = '' WHERE phone IS NULL;
```

### Seguridad

#### Auditoría Regular
- **Revisar** cuentas de usuario inactivas
- **Verificar** permisos de archivos
- **Comprobar** logs de acceso
- **Actualizar** contraseñas importantes

#### Monitoreo
```bash
# Verificar conexiones sospechosas
netstat -an | grep :80

# Revisar logs de acceso
tail -f /var/log/apache2/access.log

# Monitorear uso de recursos
top -p $(pgrep apache2)
```

---

## 📞 Soporte Post-Instalación

### Recursos de Ayuda

- **Documentación**: `/docs/README.md`
- **Guías técnicas**: `/docs/TECHNICAL.md`
- **Guías de usuario**: `/docs/guides/`

### Información del Sistema

**Versión actual**: 1.0.0  
**Fecha de instalación**: [Fecha de instalación]  
**Última actualización**: 12 de septiembre de 2025

---

*Guía de Instalación - Sistema de Votación v1.0.0*