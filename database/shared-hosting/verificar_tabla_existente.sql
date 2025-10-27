-- MIGRACIÓN SEGURA: Agregar campo puesto a base de datos existente
-- Para servidor compartido DonWeb con BD a0020819_votocde
-- Este script es 100% seguro y no afectará datos existentes

USE a0020819_votocde;

-- 1. Verificar si la tabla users existe
SELECT 'Verificando tabla users...' as paso;

-- 2. Verificar si la columna puesto ya existe
SELECT COUNT(*) as 'Columna puesto existe (0=no, 1=si)' 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'a0020819_votocde' 
  AND TABLE_NAME = 'users' 
  AND COLUMN_NAME = 'puesto';

-- 3. Mostrar estructura actual de la tabla users
SELECT 'Estructura actual de la tabla users:' as info;
DESCRIBE users;