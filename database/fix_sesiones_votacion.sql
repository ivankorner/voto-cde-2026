-- Arreglar tabla sesiones_votacion para incluir orden_dia_id
-- Este script corrige el error de integridad al crear sesiones

-- Verificar si la columna orden_dia_id ya existe
SET @column_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'sesiones_votacion'
    AND COLUMN_NAME = 'orden_dia_id'
);

-- Agregar columna orden_dia_id si no existe
SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE sesiones_votacion ADD COLUMN orden_dia_id INT NULL AFTER descripcion',
    'SELECT "La columna orden_dia_id ya existe" as mensaje'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Verificar si la columna item_id en puntos_habilitados puede ser NULL
-- (debe poder ser NULL para puntos globales como apertura y cierre)
ALTER TABLE puntos_habilitados MODIFY COLUMN item_id INT NULL;

-- Mostrar estructura actualizada
DESCRIBE sesiones_votacion;
DESCRIBE puntos_habilitados;