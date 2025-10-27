-- SCRIPT TODO-EN-UNO: Agregar campo puesto de forma segura
-- Para BD existente a0020819_votocde en DonWeb
-- Este script es completamente seguro para bases de datos con datos

USE a0020819_votocde;

-- Mostrar información inicial
SELECT 'INICIO DE MIGRACIÓN - Agregar campo puesto' as estado;

-- Verificar si la columna ya existe
SET @column_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'a0020819_votocde' 
      AND TABLE_NAME = 'users' 
      AND COLUMN_NAME = 'puesto'
);

-- Mostrar si existe o no
SELECT 
    CASE 
        WHEN @column_exists > 0 THEN 'La columna puesto YA EXISTE' 
        ELSE 'La columna puesto NO EXISTE - se agregará' 
    END as estado_columna;

-- Agregar columna solo si no existe (usando condicional)
SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE users ADD COLUMN puesto VARCHAR(50) NULL AFTER last_name',
    'SELECT "Columna puesto ya existe - no se modificó nada" as mensaje'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Verificar si el índice ya existe
SET @index_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = 'a0020819_votocde' 
      AND TABLE_NAME = 'users' 
      AND INDEX_NAME = 'idx_users_puesto'
);

-- Agregar índice solo si no existe
SET @sql_index = IF(@index_exists = 0, 
    'ALTER TABLE users ADD INDEX idx_users_puesto (puesto)',
    'SELECT "Índice ya existe - no se modificó nada" as mensaje'
);

PREPARE stmt_index FROM @sql_index;
EXECUTE stmt_index;
DEALLOCATE PREPARE stmt_index;

-- Mostrar estructura final de la tabla
SELECT 'ESTRUCTURA FINAL DE LA TABLA USERS:' as info;
DESCRIBE users;

-- Mostrar usuarios existentes
SELECT 'USUARIOS EXISTENTES:' as info;
SELECT id, username, first_name, last_name, puesto, 
       CASE 
           WHEN puesto IS NULL THEN 'Sin puesto asignado' 
           ELSE puesto 
       END as estado_puesto
FROM users;

-- Resultado final
SELECT 'MIGRACIÓN COMPLETADA EXITOSAMENTE' as resultado_final;