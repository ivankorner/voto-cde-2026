-- PASO 2: Agregar índice para el campo puesto (OPCIONAL)
-- Para BD existente a0020819_votocde

USE a0020819_votocde;

-- Crear índice para el campo puesto (mejora rendimiento)
ALTER TABLE users ADD INDEX idx_users_puesto (puesto);

-- Verificar índices creados
SHOW INDEX FROM users;

SELECT 'Índice para campo puesto creado exitosamente' as resultado;