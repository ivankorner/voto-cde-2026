-- Datos de prueba para verificar el hemiciclo con puestos
-- Este script asigna puestos específicos a usuarios para probar la visualización

-- Actualizar algunos usuarios con puestos específicos (ajustar IDs según tu base de datos)
UPDATE users SET puesto = 'Presidente' WHERE id = 1;
UPDATE users SET puesto = 'Vice Presidente' WHERE id = 2;
UPDATE users SET puesto = 'Concejal' WHERE id = 3;
UPDATE users SET puesto = 'Secretario' WHERE id = 4;
UPDATE users SET puesto = 'Pro Secretario' WHERE id = 5;

-- Verificar los cambios
SELECT id, username, first_name, last_name, puesto, status FROM users WHERE puesto IS NOT NULL;

-- Mostrar todos los usuarios para referencia
SELECT id, username, first_name, last_name, puesto FROM users ORDER BY id;