# SOLUCIÓN AL ERROR DE VOTACIÓN

## Problema Encontrado

El error "Error al votar" ocurre porque la tabla `historial_votacion` en la base de datos de producción **NO tiene la columna `total_presentes`**.

Error SQL exacto:
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'total_presentes' in 'field list'
```

## Solución

Ejecutar la migración que agrega la columna faltante.

### Opción 1: Ejecutar script PHP (RECOMENDADO)

1. Subir el archivo `migrate_historial.php` al servidor
2. Ejecutar desde el navegador:
   ```
   https://votocde.online/migrate_historial.php
   ```
3. Debería mostrar:
   ```
   ✓ Columna 'total_presentes' agregada exitosamente
   ✓ Migración completada con éxito!
   ```
4. **ELIMINAR el archivo migrate_historial.php** después de ejecutar por seguridad

### Opción 2: Ejecutar SQL directo en phpMyAdmin

1. Abrir phpMyAdmin en el panel de DonWeb
2. Seleccionar la base de datos `a0020819_votocde`
3. Ir a la pestaña "SQL"
4. Ejecutar este comando:

```sql
ALTER TABLE historial_votacion 
ADD COLUMN total_presentes INT(11) NOT NULL DEFAULT 0 
AFTER item_votacion_id;
```

5. Verificar con:
```sql
DESCRIBE historial_votacion;
```

## Después de la migración

Una vez agregada la columna, la votación funcionará correctamente.

El sistema ya está recibiendo los POST correctamente (se confirma en el log):
- ✓ Router recibe las peticiones
- ✓ VotacionController se ejecuta
- ✓ Datos POST llegan correctamente
- ✗ Solo falla al insertar en `historial_votacion` por columna faltante

## Archivos creados

- `migrate_historial.php` - Script de migración PHP
- `database/fix_historial_votacion.sql` - SQL de la migración
- `SOLUCION_ERROR_VOTACION.md` - Este archivo

---
**Fecha:** 29 de octubre de 2025
**Causa raíz:** Base de datos de producción desactualizada (falta columna en historial_votacion)
