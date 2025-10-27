-- PASO 5: Verificación de la instalación
-- Para servidor compartido DonWeb
-- Ejecutar al final para verificar que todo está correcto

USE a0020819_votocde;

-- Mostrar tablas creadas
SHOW TABLES;

-- Verificar estructura de usuarios
DESCRIBE users;

-- Verificar roles creados
SELECT * FROM roles;

-- Verificar usuarios creados
SELECT username, email, first_name, last_name, puesto, r.name as rol 
FROM users u 
LEFT JOIN roles r ON u.role_id = r.id;

-- Contar registros
SELECT 
    (SELECT COUNT(*) FROM roles) as total_roles,
    (SELECT COUNT(*) FROM users) as total_usuarios;