-- Agregar columna puesto a la tabla users si no existe
-- Este script permite identificar jerarquías democráticas en el hemiciclo

-- Verificar si la columna puesto ya existe
SET @column_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'users'
    AND COLUMN_NAME = 'puesto'
);

-- Agregar columna puesto si no existe
SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE users ADD COLUMN puesto VARCHAR(50) NULL AFTER last_name',
    'SELECT "La columna puesto ya existe" as mensaje'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Mostrar estructura actualizada
DESCRIBE users;