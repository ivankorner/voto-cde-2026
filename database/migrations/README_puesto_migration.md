# Migración: Campo Puesto para Usuarios

## Descripción
Esta migración agrega el campo "puesto" a la tabla `users` para almacenar el cargo o posición del usuario en el consejo municipal.

## Puestos disponibles
- Presidente
- Vice Presidente
- Concejal
- Secretario
- Pro Secretario

## Instrucciones para aplicar la migración

### Opción 1: Migración segura (recomendada)
```bash
cd /Applications/XAMPP/xamppfiles/htdocs/voto-cde
mysql -u root -p voto_db < database/migrations/add_puesto_safe.sql
```

### Opción 2: Migración simple
```bash
cd /Applications/XAMPP/xamppfiles/htdocs/voto-cde
mysql -u root -p voto_db < database/migrations/add_puesto_to_users.sql
```

## Verificación
Después de ejecutar la migración, puedes verificar que el campo se agregó correctamente:

```sql
USE voto_db;
DESCRIBE users;
```

Deberías ver la columna `puesto` con tipo `enum('Presidente','Vice Presidente','Concejal','Secretario','Pro Secretario')`.

## Cambios incluidos en el código

### Frontend
- ✅ Formulario de crear usuario incluye selector de puesto
- ✅ Formulario de editar usuario incluye selector de puesto  
- ✅ Lista de usuarios muestra la columna puesto con badges
- ✅ Validación frontend para opciones válidas

### Backend
- ✅ Controlador actualizado para manejar el campo puesto
- ✅ Validación backend para puestos válidos
- ✅ Modelo User compatible con el nuevo campo

### Base de datos
- ✅ Esquema principal actualizado
- ✅ Migración para agregar el campo a bases existentes
- ✅ Índice para optimizar consultas por puesto

## Notas importantes
- El campo puesto es **opcional** (puede ser NULL)
- Los puestos están definidos como ENUM para garantizar integridad
- La migración es **no destructiva** (no afecta datos existentes)
- Compatible con usuarios existentes (campo será NULL inicialmente)