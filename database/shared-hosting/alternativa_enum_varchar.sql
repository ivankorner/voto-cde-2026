-- VERSIÓN ALTERNATIVA: Si necesitas restricciones de valores
-- Agregar campo puesto con CHECK constraint (si el servidor lo soporta)

USE a0020819_votocde;

-- Opción 1: Campo VARCHAR sin restricciones (MÁS COMPATIBLE)
-- ALTER TABLE users ADD COLUMN puesto VARCHAR(50) NULL AFTER last_name;

-- Opción 2: Campo ENUM con valores específicos (si es soportado)
-- ALTER TABLE users ADD COLUMN puesto ENUM('Presidente', 'Vice Presidente', 'Concejal', 'Secretario', 'Pro Secretario') NULL AFTER last_name;

-- Si la opción 2 falla, usar opción 1 y validar en PHP
-- El controlador UserController.php ya tiene la validación:
-- $puestosValidos = ['Presidente', 'Vice Presidente', 'Concejal', 'Secretario', 'Pro Secretario'];

-- Verificar cuál opción funcionó
SELECT 'Verifica la estructura de la tabla:' as mensaje;
DESCRIBE users;