# Instalación en Servidor Compartido DonWeb

## 📊 **Datos de la base de datos:**
- **Usuario:** a0020819_votocde
- **Clave:** revu06weRI  
- **Base de datos:** a0020819_votocde
- **Servidor:** localhost

## 🚀 **Pasos de instalación:**

### 1. **Conectarse a la base de datos**
Desde el panel de DonWeb o phpMyAdmin:
- Servidor: localhost
- Usuario: a0020819_votocde
- Contraseña: revu06weRI
- Base de datos: a0020819_votocde

### 2. **Crear las tablas iniciales**
Ejecutar en este orden:

```sql
-- 1. Primero ejecutar el schema principal
-- Copiar y pegar el contenido de: database/schema.sql
```

### 3. **Agregar el campo puesto (si es necesario)**
Si ya tienes usuarios creados, ejecutar:

```sql
-- 2. Después ejecutar la migración del puesto
-- Copiar y pegar el contenido de: database/migrations/add_puesto_safe.sql
```

### 4. **Verificar instalación**
```sql
-- Verificar que las tablas se crearon correctamente
SHOW TABLES;

-- Verificar estructura de usuarios
DESCRIBE users;

-- Ver usuarios creados
SELECT username, email, first_name, last_name, puesto FROM users;
```

### 5. **Acceso inicial al sistema**
- **URL:** https://tu-dominio.com/
- **Usuario:** admin
- **Contraseña:** admin123

⚠️ **IMPORTANTE:** Cambiar la contraseña del admin inmediatamente después del primer acceso.

## 🔒 **Seguridad post-instalación:**

1. **Cambiar contraseña del admin**
2. **Actualizar emails de los usuarios**  
3. **Configurar la URL correcta en config/config.php**
4. **Eliminar usuarios de ejemplo si no son necesarios**

## 🛠️ **Archivos ya configurados para DonWeb:**
- ✅ `database/schema.sql` - Configurado para a0020819_votocde
- ✅ `database/migrations/add_puesto_safe.sql` - Configurado para DonWeb
- ✅ `database/migrations/add_puesto_to_users.sql` - Configurado para DonWeb
- ✅ `config/config.php` - Configurado con tus credenciales de BD

## 📱 **Usuarios por defecto creados:**
1. **admin** (Administrador) - Contraseña: admin123
2. **editor1** (Editor) - Contraseña: admin123  
3. **viewer1** (Viewer) - Contraseña: admin123

## 🎯 **Próximos pasos:**
1. Ejecutar el script SQL principal
2. Verificar que todo funciona
3. Personalizar usuarios y configuración
4. ¡Comenzar a usar el sistema de votaciones!