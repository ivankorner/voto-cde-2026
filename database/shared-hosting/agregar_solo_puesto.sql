-- PASO 1: Agregar campo puesto SOLAMENTE (SEGURO)
-- Para BD existente a0020819_votocde
-- Este script NO afecta datos existentes

USE a0020819_votocde;

-- Agregar la columna puesto de forma segura
-- Si la columna ya existe, el comando fallará pero no causará daños
ALTER TABLE users ADD COLUMN puesto VARCHAR(50) NULL AFTER last_name;

-- Mensaje de confirmación
SELECT 'Campo puesto agregado exitosamente' as resultado;

-- Verificar que se agregó
DESCRIBE users;