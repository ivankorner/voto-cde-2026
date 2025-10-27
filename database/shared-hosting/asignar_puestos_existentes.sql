-- PASO 3: Actualizar usuarios existentes con puestos (OPCIONAL)
-- Para BD existente a0020819_votocde
-- Solo ejecutar si quieres asignar puestos a usuarios existentes

USE a0020819_votocde;

-- Ver usuarios actuales
SELECT 'Usuarios actuales:' as info;
SELECT id, username, first_name, last_name, puesto FROM users;

-- EJEMPLOS de cómo asignar puestos (descomentar y modificar según necesites):

-- Ejemplo 1: Asignar puesto de Presidente al usuario con ID 1
-- UPDATE users SET puesto = 'Presidente' WHERE id = 1;

-- Ejemplo 2: Asignar puesto de Secretario al usuario 'admin'
-- UPDATE users SET puesto = 'Secretario' WHERE username = 'admin';

-- Ejemplo 3: Asignar puesto de Concejal a un usuario específico
-- UPDATE users SET puesto = 'Concejal' WHERE email = 'usuario@ejemplo.com';

-- Ver resultado después de las actualizaciones
SELECT 'Estado después de actualizaciones:' as resultado;
SELECT id, username, first_name, last_name, puesto FROM users;