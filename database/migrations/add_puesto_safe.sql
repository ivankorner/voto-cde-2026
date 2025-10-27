-- Script para aplicar la migración del campo puesto de forma segura
-- Para servidor compartido DonWeb
-- Este script verifica si la columna ya existe antes de agregarla

USE a0020819_votocde;

-- Verificar si la columna puesto ya existe
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'a0020819_votocde' 
  AND TABLE_NAME = 'users' 
  AND COLUMN_NAME = 'puesto';

-- Solo agregar la columna si no existe
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE users ADD COLUMN puesto ENUM(''Presidente'', ''Vice Presidente'', ''Concejal'', ''Secretario'', ''Pro Secretario'') NULL AFTER last_name',
    'SELECT "La columna puesto ya existe" AS mensaje'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Verificar si el índice ya existe
SET @index_exists = 0;
SELECT COUNT(*) INTO @index_exists 
FROM INFORMATION_SCHEMA.STATISTICS 
WHERE TABLE_SCHEMA = 'a0020819_votocde' 
  AND TABLE_NAME = 'users' 
  AND INDEX_NAME = 'idx_users_puesto';

-- Solo agregar el índice si no existe
SET @sql_index = IF(@index_exists = 0, 
    'CREATE INDEX idx_users_puesto ON users(puesto)',
    'SELECT "El índice idx_users_puesto ya existe" AS mensaje'
);

PREPARE stmt_index FROM @sql_index;
EXECUTE stmt_index;
DEALLOCATE PREPARE stmt_index;

-- Mostrar información final
SELECT 
    'Migración completada' AS estado,
    IF(@col_exists = 0, 'Columna puesto agregada', 'Columna puesto ya existía') AS columna,
    IF(@index_exists = 0, 'Índice agregado', 'Índice ya existía') AS indice;